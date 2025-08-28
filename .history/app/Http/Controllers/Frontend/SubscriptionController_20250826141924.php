<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Il;
use App\Models\SubscriptionPlan;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
        $this->middleware('auth');
    }

    /**
     * Abonelik paketlerini göster
     */
    public function plans()
    {
        $plans = SubscriptionPlan::active()->ordered()->get();
        $tenant = Auth::user()->tenant;
        $currentPlan = $tenant->currentSubscription->plan ?? null;
        $onTrial = $tenant->isOnTrial();
        $remainingTrialDays = $tenant->getRemainingTrialDays();
        return view('frontend.secure.subscription.plans', compact('plans', 'tenant', 'currentPlan','onTrial',
        'remainingTrialDays'));
    }

    /**
     * Abonelik satın alma formu
     */
    public function subscribe($tenant_id, $planid)
    {
        $tenant = Auth::user()->tenant;
        $plan = SubscriptionPlan::where('id', $planid)->first();
        
        // Zaten aynı plana sahipse
        if ($tenant->currentSubscription && $tenant->currentSubscription->plan_id == $plan->id) {
            return redirect()->route('subscription.plans')
                           ->with('info', 'Zaten bu paketi kullanıyorsunuz.');
        }
        
        $countries = Il::orderBy('name', 'ASC')->get();
        return view('frontend.secure.subscription.checkout', compact('plan', 'tenant', 'countries', 'tenant_id', 'planid'));
    }

    public function processSubscription(Request $request, $tenant_id, $planid)
    {
        $plan = SubscriptionPlan::findOrFail($planid);
        $tenant = Auth::user()->tenant;

        $validated = $request->validate([
            'billing_type' => 'required|in:bireysel,kurumsal',
            'first_name' => 'required|string',
            'last_name' => 'nullable|string', // Bu alan formda yok, nullable yaptım
            'email' => 'required|email',
            'phone' => 'required|string',
            'il' => 'required|string',
            'ilce' => 'required|string',
            'neighborhood' => 'nullable|string',
            'address' => 'nullable|string',
            'identity_number' => 'nullable|string',
            'foreign' => 'nullable|boolean',
            'tax_office' => 'nullable|string',
            'tax_number' => 'nullable|string',
        ]);

        // Paket ve form bilgilerini session'a kaydet
        session([
            'subscription.plan' => $plan->toArray(),
            'subscription.billing' => $validated,
            'subscription.tenant_id' => $tenant_id,
            'subscription.planid' => $planid
        ]);

        // Ödeme sayfasına yönlendir
        return redirect()->route('subscription.payment', [$tenant_id, $planid]);
    }

    public function payment($tenant_id, $planid)
    {
        // Session'daki verileri al
        $planData = session('subscription.plan');
        $billingData = session('subscription.billing');

        // Eğer session boşsa checkout sayfasına geri yönlendir
        if (!$planData || !$billingData) {
            return redirect()->route('subscription.subscribe', [$tenant_id, $planid])
                            ->with('error', 'Ödeme adımına geçmek için önce formu doldurmalısınız.');
        }

        $tenant = Auth::user()->tenant;
        $plan = SubscriptionPlan::findOrFail($planid);

        // Blade dosyasına verileri gönder
        return view('frontend.secure.subscription.payment', [
            'planData' => $planData,
            'billingData' => $billingData,
            'tenant_id' => $tenant_id,
            'planid' => $planid,
            'tenant' => $tenant,
            'plan' => $plan
        ]);
    }

    /**
     * Abonelik satın alma işlemi
     */
    public function paymentSubscription(Request $request, $tenant_id, $planid)
    {
        $request->validate([
            'payment_method' => 'required|in:credit_card,bank_transfer',
            'card_number' => 'required_if:payment_method,credit_card',
            'card_expiry' => 'required_if:payment_method,credit_card',
            'card_cvc' => 'required_if:payment_method,credit_card',
            'card_holder' => 'required_if:payment_method,credit_card',
            'terms_accepted' => 'required|accepted'
        ]);

        $plan = SubscriptionPlan::findOrFail($planid);
        $tenant = Auth::user()->tenant;
        
        // Session'dan fatura bilgilerini al
        $billingData = session('subscription.billing');
        
        if (!$billingData) {
            return redirect()->route('subscription.subscribe', [$tenant_id, $planid])
                            ->with('error', 'Fatura bilgileri bulunamadı. Lütfen tekrar deneyin.');
        }

        try {
            // Ödeme verilerini fatura verileriyle birleştir
            $paymentData = array_merge($request->all(), $billingData);
            
            $result = $this->subscriptionService->subscribe($tenant, $plan, $paymentData);

            if ($result['success']) {
                // Session'ı temizle
                session()->forget(['subscription.plan', 'subscription.billing', 'subscription.tenant_id', 'subscription.planid']);
                
                return redirect()->route('subscription.success')
                               ->with('success', 'Aboneliğiniz başarıyla aktif edildi!')
                               ->with('subscription', $result['subscription']);
            } else {
                return redirect()->back()
                               ->with('error', $result['message'])
                               ->withInput();
            }
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Abonelik işlemi sırasında bir hata oluştu: ' . $e->getMessage())
                           ->withInput();
        }
    }

    // Diğer metodlar aynı kalıyor...
    public function success()
    {
        return view('frontend.secure.subscription.success');
    }

    public function cancel()
    {
        $tenant = Auth::user()->tenant;

        try {
            $this->subscriptionService->cancelSubscription($tenant);
            
            return redirect()->route('dashboard')
                           ->with('success', 'Aboneliğiniz iptal edildi. Mevcut dönem sonuna kadar erişiminiz devam edecek.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', $e->getMessage());
        }
    }

    public function upgrade()
    {
        $tenant = Auth::user()->tenant;
        $currentPlan = $tenant->currentSubscription->plan ?? null;
        $plans = SubscriptionPlan::active()
                                ->where('id', '!=', $currentPlan?->id)
                                ->ordered()
                                ->get();

        return view('frontend.secure.subscription.upgrade', compact('plans', 'tenant', 'currentPlan'));
    }

    public function expired()
    {
        $tenant = Auth::user()->tenant;
        $plans = SubscriptionPlan::active()->ordered()->get();
        
        return view('frontend.secure.subscription.expired', compact('tenant', 'plans'));
    }

    public function invoices()
    {
        $tenant = Auth::user()->tenant;
        $payments = $tenant->payments()
                          ->with('subscription.plan')
                          ->orderBy('created_at', 'desc')
                          ->paginate(10);
        
        return view('frontend.secure.subscription.invoices', compact('payments', 'tenant'));
    }
}
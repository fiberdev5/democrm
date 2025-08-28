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
    public function subscribe($tenant_id,$planid)
    {
        $tenant = Auth::user()->tenant;
        $plan = SubscriptionPlan::where('id', $planid)->first();
        // Zaten aynı plana sahipse
        if ($tenant->currentSubscription && $tenant->currentSubscription->plan_id == $plan->id) {
            return redirect()->route('subscription.plans')
                           ->with('info', 'Zaten bu paketi kullanıyorsunuz.');
        }
        $countries = Il::orderBy('name', 'ASC')->get();
        return view('frontend.secure.subscription.checkout', compact('plan', 'tenant','countries'));
    }

    public function processSubscription(Request $request, $tenant_id, $planid)
    {
        $plan = SubscriptionPlan::findOrFail($planid);
        $tenant = Auth::user()->tenant;

        $validated = $request->validate([
            'billing_type' => 'required|in:bireysel,kurumsal',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
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

        // Paket ve form bilgilerini session’a kaydet
        session([
            'subscription.plan' => $plan->toArray(),
            'subscription.billing' => $validated
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

        // Blade dosyasına verileri gönder
        return view('frontend.secure.subscription.payment', [
            'planData' => $planData,
            'billingData' => $billingData,
            'tenant_id' => $tenant_id,
            'planid' => $planid
        ]);
    }
    /**
     * Abonelik satın alma işlemi
     */
    public function paymentSubscription(Request $request, SubscriptionPlan $plan)
    {
        $request->validate([
            'payment_method' => 'required|in:credit_card,bank_transfer',
            'card_number' => 'required_if:payment_method,credit_card',
            'card_expiry' => 'required_if:payment_method,credit_card',
            'card_cvc' => 'required_if:payment_method,credit_card',
            'card_holder' => 'required_if:payment_method,credit_card',
            'terms_accepted' => 'required|accepted'
        ]);

        $tenant = Auth::user()->tenant;

        try {
            $result = $this->subscriptionService->subscribe($tenant, $plan, $request->all());

            if ($result['success']) {
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

    /**
     * Abonelik başarı sayfası
     */
    public function success()
    {
        return view('subscription.success');
    }

    /**
     * Abonelik iptal
     */
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

    /**
     * Abonelik yükseltme sayfası
     */
    public function upgrade()
    {
        $tenant = Auth::user()->tenant;
        $currentPlan = $tenant->currentSubscription->plan ?? null;
        $plans = SubscriptionPlan::active()
                                ->where('id', '!=', $currentPlan?->id)
                                ->ordered()
                                ->get();

        return view('subscription.upgrade', compact('plans', 'tenant', 'currentPlan'));
    }

    /**
     * Abonelik süresi dolmuş sayfası
     */
    public function expired()
    {
        $tenant = Auth::user()->tenant;
        $plans = SubscriptionPlan::active()->ordered()->get();
        
        return view('subscription.expired', compact('tenant', 'plans'));
    }

    /**
     * Faturalar sayfası
     */
    public function invoices()
    {
        $tenant = Auth::user()->tenant;
        $payments = $tenant->payments()
                          ->with('subscription.plan')
                          ->orderBy('created_at', 'desc')
                          ->paginate(10);
        
        return view('subscription.invoices', compact('payments', 'tenant'));
    }
}

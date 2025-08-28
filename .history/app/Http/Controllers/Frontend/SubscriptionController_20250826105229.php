<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
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
    public function subscribe(SubscriptionPlan $plan)
    {
        $tenant = Auth::user()->tenant;
        
        // Zaten aynı plana sahipse
        if ($tenant->currentSubscription && $tenant->currentSubscription->plan_id == $plan->id) {
            return redirect()->route('subscription.plans')
                           ->with('info', 'Zaten bu paketi kullanıyorsunuz.');
        }

        return view('subscription.checkout', compact('plan', 'tenant'));
    }

    /**
     * Abonelik satın alma işlemi
     */
    public function processSubscription(Request $request, SubscriptionPlan $plan)
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

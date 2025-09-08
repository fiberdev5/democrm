<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Il;
use App\Models\SubscriptionPayment;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\TenantSubscription;
use App\Services\PaytrService;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    protected $subscriptionService;
    protected $paytrService;

    public function __construct(SubscriptionService $subscriptionService, PaytrService $paytrService)
    {
        $this->subscriptionService = $subscriptionService;
        $this->paytrService = $paytrService;
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
            'last_name' => 'nullable|string',
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
     * Paytr ödeme işlemini başlat
     */
    public function initiatePayment(Request $request, $tenant_id, $planid)
    {
        try {
            // Session verilerini kontrol et
            $planData = session('subscription.plan');
            $billingData = session('subscription.billing');

            if (!$planData || !$billingData) {
                return redirect()->route('subscription.subscribe', [$tenant_id, $planid])
                                ->with('error', 'Ödeme bilgileri bulunamadı. Lütfen tekrar deneyin.');
            }

            $plan = SubscriptionPlan::findOrFail($planid);
            $tenant = Auth::user()->tenant;

            // Benzersiz sipariş ID oluştur
            $orderId = 'SUB_' . $tenant_id . '_' . $planid . '_' . time();
            
            // KDV dahil toplam tutarı hesapla
            $totalAmount = $plan->price * 1.20;

            // Sepet bilgilerini hazırla
            $basket = [
                [
                    'Abonelik Paketi',
                    $plan->price,
                    1
                ]
            ];

            // Paytr için sipariş verilerini hazırla
            $orderData = [
                'order_id' => $orderId,
                'email' => $billingData['email'],
                'amount' => $totalAmount,
                'user_name' => $billingData['first_name'],
                'user_address' => $billingData['address'] ?? $billingData['il'] . '/' . $billingData['ilce'],
                'user_phone' => $billingData['phone'],
                'basket' => $basket,
                'success_url' => route('subscription.payment.success', ['tenant_id' => $tenant_id, 'planid' => $planid]),
                'fail_url' => route('subscription.payment.fail', ['tenant_id' => $tenant_id, 'planid' => $planid]),
            ];

            // Ödeme kaydını pending olarak oluştur
            $payment = SubscriptionPayment::create([
                'tenant_id' => $tenant_id,
                'payment_id' => $orderId,
                'amount' => $totalAmount,
                'currency' => 'TL',
                'status' => 'pending',
                'payment_method' => 'Kredi Kartı',
                'gateway' => 'Paytr',
                'gateway_response' => json_encode(['order_data' => $orderData]),
            ]);

            // Session'a payment ID'sini kaydet
            session(['subscription.payment_id' => $payment->id]);

            // Paytr iframe token'ını al
            $paytrResponse = $this->paytrService->createPaymentIframe($orderData);

            if ($paytrResponse['success']) {
                return view('frontend.secure.subscription.paytr_payment', [
                    'iframe_url' => $paytrResponse['iframe_url'],
                    'planData' => $planData,
                    'billingData' => $billingData,
                    'totalAmount' => $totalAmount
                ]);
            } else {
                // Payment kaydını başarısız olarak güncelle
                $payment->update([
                    'status' => 'failed',
                    'failure_reason' => $paytrResponse['error']
                ]);

                return back()->with('error', 'Ödeme servisiyle bağlantı kurulamadı: ' . $paytrResponse['error']);
            }

        } catch (\Exception $e) {
            Log::error('Payment initiation error: ' . $e->getMessage());
            return back()->with('error', 'Ödeme başlatılırken bir hata oluştu. Lütfen tekrar deneyin.');
        }
    }

    /**
     * Paytr callback (ödeme bildirimi)
     */
    public function paymentCallback(Request $request)
    {
        try {
            $postData = $request->all();
            Log::info('Paytr Callback Data:', $postData);

            // Callback'i doğrula
            if (!$this->paytrService->verifyCallback($postData)) {
                Log::error('Paytr callback verification failed');
                return response('FAIL', 200);
            }

            // Sipariş ID'sinden payment kaydını bul
            $payment = SubscriptionPayment::where('payment_id', $postData['merchant_oid'])->first();

            if (!$payment) {
                Log::error('Payment not found for order ID: ' . $postData['merchant_oid']);
                return response('FAIL', 200);
            }

            // Ödeme durumunu güncelle
            if ($postData['status'] == 'success') {
                $payment->update([
                    'status' => 'completed',
                    'transaction_id' => $postData['transaction_id'] ?? null,
                    'gateway_response' => json_encode($postData),
                    'paid_at' => Carbon::now(),
                ]);

                // Abonelik işlemlerini tamamla
                $this->completeSubscription($payment);

                Log::info('Payment successful: ' . $postData['merchant_oid']);
                return response('OK', 200);
            } else {
                $payment->update([
                    'status' => 'failed',
                    'failure_reason' => $postData['failed_reason_msg'] ?? 'Bilinmeyen hata',
                    'gateway_response' => json_encode($postData),
                ]);

                Log::info('Payment failed: ' . $postData['merchant_oid']);
                return response('OK', 200);
            }

        } catch (\Exception $e) {
            Log::error('Payment callback error: ' . $e->getMessage());
            return response('FAIL', 200);
        }
    }

    /**
     * Ödeme başarı sayfası
     */
    public function paymentSuccess($tenant_id, $planid)
    {
        // Son payment kaydını kontrol et
        $paymentId = session('subscription.payment_id');
        
        if ($paymentId) {
            $payment = SubscriptionPayment::find($paymentId);
            
            if ($payment && $payment->status == 'completed') {
                session()->forget('subscription');
                return redirect()->route('secure.home', $tenant_id)
                                ->with('success', 'Ödemeniz başarıyla tamamlandı! Aboneliğiniz aktif edildi.');
            }
        }

        // Ödeme durumu belirsizse, kontrol sayfasına yönlendir
        return view('frontend.secure.subscription.payment_processing', [
            'tenant_id' => $tenant_id,
            'planid' => $planid
        ]);
    }

    /**
     * Ödeme başarısız sayfası
     */
    public function paymentFail($tenant_id, $planid)
    {
        $paymentId = session('subscription.payment_id');
        $errorMessage = 'Ödeme işlemi başarısız oldu.';
        
        if ($paymentId) {
            $payment = SubscriptionPayment::find($paymentId);
            if ($payment && $payment->failure_reason) {
                $errorMessage = $payment->failure_reason;
            }
        }

        return redirect()->route('subscription.payment', [$tenant_id, $planid])
                        ->with('error', $errorMessage);
    }

    /**
     * Abonelik işlemlerini tamamla
     */
    private function completeSubscription(SubscriptionPayment $payment)
    {
        try {
            DB::beginTransaction();

            $billingData = session('subscription.billing');
            $planData = session('subscription.plan');

            if (!$billingData || !$planData) {
                throw new \Exception('Session verisi bulunamadı');
            }

            $tenant = Tenant::findOrFail($payment->tenant_id);
            $plan = SubscriptionPlan::findOrFail($planData['id']);

            // Abonelik tarihleri
            $startDate = Carbon::now();
            $endDate = $plan->billing_cycle == 'yearly' ? $startDate->copy()->addYear() : $startDate->copy()->addMonth();

            // Tenant'ı güncelle
            $tenant->update([
                'subscription_status' => 'active',
                'status' => 1,
                'bitisTarihi' => $endDate,
                'trial_used' => 1,
                'subscription_ends_at' => $endDate,
                'tcNo' => $billingData['identity_number'] ?? null,
                'vergiDairesi' => $billingData['tax_office'] ?? null,
                'vergiNo' => $billingData['tax_number'] ?? null,
                'name' => $billingData['first_name'],
                'eposta' => $billingData['email'],
                'tel1' => $billingData['phone'],
                'il' => $billingData['il'],
                'ilce' => $billingData['ilce'],
                'adres' => $billingData['address'] ?? null,
            ]);

            // Mevcut abonelikleri iptal et
            TenantSubscription::where('tenant_id', $payment->tenant_id)->update(['status' => 'canceled']);

            // Yeni abonelik oluştur
            $subscription = TenantSubscription::create([
                'tenant_id' => $payment->tenant_id,
                'plan_id' => $plan->id,
                'status' => 'active',
                'starts_at' => $startDate,
                'ends_at' => $endDate,
                'payment_method' => 'kredi kartı',
                'subscription_data' => json_encode($billingData)
            ]);

            // Payment kaydını subscription ile ilişkilendir
            $payment->update(['subscription_id' => $subscription->id]);

            DB::commit();
            Log::info('Subscription completed successfully for tenant: ' . $payment->tenant_id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Subscription completion error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Ödeme durumu kontrol et (AJAX)
     */
    public function checkPaymentStatus(Request $request)
    {
        $paymentId = $request->input('payment_id');
        
        if (!$paymentId) {
            return response()->json(['status' => 'error', 'message' => 'Payment ID gerekli']);
        }

        $payment = SubscriptionPayment::find($paymentId);
        
        if (!$payment) {
            return response()->json(['status' => 'error', 'message' => 'Ödeme kaydı bulunamadı']);
        }

        return response()->json([
            'status' => $payment->status,
            'message' => $payment->status == 'completed' ? 'Ödeme başarılı' : 'Ödeme beklemede'
        ]);
    }

    
}
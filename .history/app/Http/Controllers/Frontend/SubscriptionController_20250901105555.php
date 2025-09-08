<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Il;
use App\Models\SubscriptionPayment;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\TenantSubscription;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
    public function completePayment(Request $request, $tenant_id, $planid)
    {
        // 1. Gerekli verileri al
        $billingData = session('subscription.billing');
        $plan = SubscriptionPlan::findOrFail($planid);
        $tenant = Tenant::findOrFail($tenant_id);

        // Session'da bilgi yoksa, kullanıcıyı geri yönlendir
        if (!$billingData) {
            return redirect()->route('subscription.subscribe', [$tenant_id, $planid])
                            ->with('error', 'Oturum süresi doldu. Lütfen bilgilerinizi tekrar girin.');
        }


        try {
            // 2. Abonelik başlangıç ve bitiş tarihlerini hesapla
            $startDate = Carbon::now();
            $endDate = null;

            if ($plan->billing_cycle == 'yearly') {
                $endDate = $startDate->copy()->addYear();
            } elseif ($plan->billing_cycle == 'monthly') {
                $endDate = $startDate->copy()->addMonth();
            } else {
                // Diğer periyotlar için varsayılan bir süre (örn: 1 yıl)
                $endDate = $startDate->copy()->addYear();
            }

            // 3. 'tenants' tablosunu güncelle
            $tenant->subscription_status = 'active';
            $tenant->status = 1; // Aktif olduğunu varsayıyorum
            $tenant->bitisTarihi = $endDate;
            $tenant->trial_used = 1; // Deneme süresi kullanıldı olarak işaretle
            $tenant->subscription_ends_at = $endDate;
            // Session'dan gelen fatura bilgilerini de güncelle
            if (isset($billingData['identity_number'])) {
                $tenant->tcNo = $billingData['identity_number'];
            }
            if (isset($billingData['tax_office'])) {
                $tenant->vergiDairesi = $billingData['tax_office'];
            }
            if (isset($billingData['tax_number'])) {
                $tenant->vergiNo = $billingData['tax_number'];
            }
            $tenant->save();

            // 4. 'tenant_subscriptions' tablosuna yeni kayıt ekle
            // Önce mevcut aktif abonelikleri pasif yap (isteğe bağlı)
            TenantSubscription::where('tenant_id', $tenant_id)->update(['status' => 'canceled']);

            $tenantSubscription = TenantSubscription::create([
                    'tenant_id' => $tenant_id,
                    'plan_id' => $planid,
                    'status' => 'active',
                    'starts_at' => $startDate,
                    'ends_at' => $endDate,
                    'payment_method' => 'kredi kartı', // payment sayfasından gelen metodu kullan
                    'subscription_data' => json_encode($billingData) // Fatura bilgilerini JSON olarak kaydet
                ]);

                // 5. 'subscription_payments' tablosuna ödeme kaydını ekle
                SubscriptionPayment::create([
                    'tenant_id' => $tenant_id,
                    'subscription_id' => $tenantSubscription->id, // Yeni oluşturulan abonelik ID'si
                    'payment_id' => 'AWE234', // QNB'den gelen sipariş ID'si
                    'amount' => $plan->price * 1.20, // KDV dahil toplam tutarı kullan, QNB'den gelen PurchAmount'u da kullanabilirsin
                    'currency' => 'TL', // veya '949' kodunu da saklayabilirsin
                    'status' => 'completed',
                    'payment_method' => 'Kredi kartı',
                    'transaction_id' => null,
                    'gateway' => 'QNB Finansbank',
                    'gateway_response' => null,
                    'paid_at' => Carbon::now(),
                    'failure_reason' => null,
                ]);

            // Başarılı işlem sonrası session verilerini temizle
            $request->session()->forget('subscription');

            // Kullanıcıyı bir başarı sayfasına veya panele yönlendir
            return redirect()->route('secure.home', $tenant_id)->with('success', 'Aboneliğiniz başarıyla başlatılmıştır!');

        } catch (\Exception $e) {
            // Bir hata oluşursa tüm işlemleri geri al
            DB::rollBack();

            // Hata günlüğüne kaydet (isteğe bağlı)
            // Log::error($e->getMessage());

            // Kullanıcıyı bir hata mesajıyla geri yönlendir
            return back()->with('error', 'Abonelik oluşturulurken bir hata oluştu. Lütfen tekrar deneyin.');
        }
    }

    public function success(Request $request)
    {
        // QNB’den gelen tüm parametreler burada gelir
        $response = $request->all();

        // success sayfasının URL'sine eklediğin hidden inputlardan al
        $planid = $request->input('planid');
        $tenant_id = $request->input('tenant_id');
        $payment_method = $request->input('payment_method', 'credit_card'); // Varsayılanı kredi kartı yap

        $transactionId = $request->input('AuthCode', $request->input('TransId', null)); 
        // AuthCode veya TransId'yi deneyebiliriz. QNB dokümanını kontrol et.
        $gatewayResponse = json_encode($response); 

        // 3D secure kontrolü
        if ($request->ProcReturnCode == '00' && $request->input('3DStatus') == '1') {
            // ✅ 3D doğrulama başarılı sayılır
            
            // 1. Gerekli verileri al
            $billingData = session('subscription.billing');
            $plan = SubscriptionPlan::findOrFail($planid);
            $tenant = Tenant::findOrFail($tenant_id);

            // Session'da fatura bilgisi yoksa (tarayıcı kapatılmış veya session bitmiş olabilir),
            // bir hata mesajı ile geri yönlendir
            if (!$billingData) {
                // Burada QNB'den gelen diğer verilerden (eğer varsa) billingData'yı tekrar oluşturabilirsin
                // ya da kullanıcıdan tekrar bilgi girmesini isteyebilirsin.
                // Şimdilik hata ile yönlendiriyoruz.
                return redirect()->route('subscription.fail')->with('error', 'Ödeme bilgileriniz bulunamadı. Lütfen tekrar deneyin.');
            }

            try {
                // 2. Abonelik başlangıç ve bitiş tarihlerini hesapla
                $startDate = Carbon::now();
                $endDate = null;

                if ($plan->billing_cycle == 'yearly') {
                    $endDate = $startDate->copy()->addYear();
                } elseif ($plan->billing_cycle == 'monthly') {
                    $endDate = $startDate->copy()->addMonth();
                } else {
                    // Diğer periyotlar için varsayılan bir süre (örn: 1 yıl)
                    $endDate = $startDate->copy()->addYear();
                }

                // 3. 'tenants' tablosunu güncelle
                $tenant->subscription_status = 'active';
                $tenant->status = 1; // Aktif olduğunu varsayıyorum
                $tenant->bitisTarihi = $endDate;
                $tenant->trial_used = 1; // Deneme süresi kullanıldı olarak işaretle

                // Session'dan gelen fatura bilgilerini de güncelle
                if (isset($billingData['identity_number'])) {
                    $tenant->tcNo = $billingData['identity_number'];
                }
                if (isset($billingData['tax_office'])) {
                    $tenant->vergiDairesi = $billingData['tax_office'];
                }
                if (isset($billingData['tax_number'])) {
                    $tenant->vergiNo = $billingData['tax_number'];
                }
                $tenant->save();

                // 4. 'tenant_subscriptions' tablosuna yeni kayıt ekle
                // Önce mevcut aktif abonelikleri pasif yap (isteğe bağlı ve mantıklı)
                TenantSubscription::where('tenant_id', $tenant_id)->update(['status' => 'canceled']);

                $tenantSubscription = TenantSubscription::create([
                    'tenant_id' => $tenant_id,
                    'plan_id' => $planid,
                    'status' => 'active',
                    'starts_at' => $startDate,
                    'ends_at' => $endDate,
                    'payment_method' => $payment_method, // payment sayfasından gelen metodu kullan
                    'subscription_data' => json_encode($billingData) // Fatura bilgilerini JSON olarak kaydet
                ]);

                // 5. 'subscription_payments' tablosuna ödeme kaydını ekle
                SubscriptionPayment::create([
                    'tenant_id' => $tenant_id,
                    'subscription_id' => $tenantSubscription->id, // Yeni oluşturulan abonelik ID'si
                    'payment_id' => $request->input('OrderId'), // QNB'den gelen sipariş ID'si
                    'amount' => $plan->price * 1.20, // KDV dahil toplam tutarı kullan, QNB'den gelen PurchAmount'u da kullanabilirsin
                    'currency' => 'TL', // veya '949' kodunu da saklayabilirsin
                    'status' => 'completed',
                    'payment_method' => $payment_method,
                    'transaction_id' => $transactionId,
                    'gateway' => 'QNB Finansbank',
                    'gateway_response' => $gatewayResponse,
                    'paid_at' => Carbon::now(),
                    'failure_reason' => null,
                ]);


                // Başarılı işlem sonrası session verilerini temizle
                $request->session()->forget('subscription');

                // Kullanıcıyı bir başarı sayfasına veya panele yönlendir
                return redirect()->route('secure.home', $tenant->id)->with('success', 'Aboneliğiniz başarıyla başlatılmıştır!');

            } catch (\Exception $e) {
                DB::rollBack(); // Bir hata oluşursa tüm işlemleri geri al
                \Log::error("Abonelik oluşturma hatası: " . $e->getMessage() . " Tenant ID: " . $tenant_id . " Plan ID: " . $planid);
                return redirect()->route('subscription.fail')->with('error', 'Abonelik oluşturulurken bir hata oluştu. Lütfen tekrar deneyin.');
            }

        } else {
            // ❌ 3D doğrulama başarısız
            \Log::warning("3D doğrulama başarısız: " . json_encode($response));
            return redirect()->route('subscription.fail')->with('error', 'Ödeme başarısız oldu. Lütfen tekrar deneyin veya farklı bir kart kullanın.');
        }
    }

    // Diğer metodlar aynı kalıyor...
     public function fail(Request $request)
    {
        // Ödeme başarısız olduğunda yönlendirilecek sayfa
        return view('frontend.secure.subscription.fail')->with('error', $request->session()->get('error', 'Ödeme işlemi başarısız.'));
    }

    public function showSuccess()
    {
        // Ödeme başarılı olduktan sonra gösterilecek genel başarı sayfası
        return view('frontend.secure.subscription.success')->with('success', session('success', 'Ödemeniz başarıyla tamamlandı.'));
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
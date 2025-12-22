<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BulkSms;
use App\Models\DeviceBrand;
use App\Models\DeviceType;
use App\Models\Il;
use App\Models\Service;
use App\Models\ServiceResource;
use App\Models\ServiceStage;
use App\Models\Tenant;
use App\Services\NetgsmService;
use App\Services\SmsFactory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BulkSmsController extends Controller
{
    public function index($tenant_id)
    {   
    $firma = Tenant::where('id', $tenant_id)->first();
        if(!$firma) {
            $notification = array(
                'message' => 'Firma bulunamadı',
                'alert-type' => 'danger'
            );
            return redirect()->route('giris')->with($notification);
        }

        // Firmanın aktif SMS entegrasyonlarını getir
        $smsIntegrations = DB::table('integration_purchases')
            ->join('integrations', 'integration_purchases.integration_id', '=', 'integrations.id')
            ->where('integration_purchases.tenant_id', $tenant_id)
            ->where('integration_purchases.status', 'completed')
            ->where('integrations.category', 'sms')
            ->where('integrations.is_active', true)
            ->select(
                'integration_purchases.id as purchase_id',
                'integrations.name',
                'integrations.slug',
                'integration_purchases.is_default'
            )
            ->get();

        if ($smsIntegrations->isEmpty()) {
            $notification = array(
                'message' => 'SMS entegrasyonu bulunamadı. Lütfen önce bir SMS entegrasyonu satın alın.',
                'alert-type' => 'warning'
            );
            return redirect()->back()->with($notification);
        }

        $cihazlar = DeviceType::where('firma_id', $tenant_id)
            ->orderBy('cihaz')
            ->get();

        $markalar = DeviceBrand::where('firma_id', $tenant_id)
            ->orderBy('marka')
            ->get();

        $servisKaynaklari = ServiceResource::where('firma_id', $tenant_id)
            ->orderBy('id')
            ->get();

        $servisAsamalari = ServiceStage::where(function ($query) use ($tenant_id) {
            $query->whereNull('firma_id')->orWhere('firma_id', $tenant_id);
        })->orderBy('id', 'desc')->get();

        $iller = Il::orderBy('name', 'asc')->get();

        return view('frontend.secure.bulk_sms.index', compact(
            'cihazlar',
            'markalar',
            'servisKaynaklari',
            'servisAsamalari',
            'iller',
            'firma',
            'smsIntegrations' 
        ));
    }

    public function list(Request $request, $tenant_id)
    {
        try {
            $firma = Tenant::where('id', $tenant_id)->first();
            if(!$firma) {
                return response()->json(['error' => 'Firma bulunamadı'], 404);
            }

            $il = $request->input('il');
            $bolgeler = $request->input('bolgeler', []);
            $cihazlar = $request->input('cihazlar', []);
            $markalar = $request->input('markalar', []);
            $kaynaklar = $request->input('kaynaklar', []);
            $durumlar = $request->input('durumlar', 0);
            
            // Tarihleri al - Y-m-d formatında gelir
            $tarih1Input = $request->input('tarih1');
            $tarih2Input = $request->input('tarih2');
            
            // Tarih parse et
            try {
                $tarih1 = Carbon::parse($tarih1Input)->startOfDay();
                $tarih2 = Carbon::parse($tarih2Input)->endOfDay();
            } catch (\Exception $e) {
                \Log::error('Tarih parse hatası', [
                    'tarih1' => $tarih1Input,
                    'tarih2' => $tarih2Input,
                    'error' => $e->getMessage()
                ]);
                return response()->json(['error' => 'Geçersiz tarih formatı'], 400);
            }
            
            // Query builder
            $query = Service::with(['musteri', 'markaCihaz', 'turCihaz'])
                ->where('services.durum', 1)
                ->where('services.firma_id', $tenant_id)
                ->whereBetween('services.kayitTarihi', [$tarih1, $tarih2])
                ->join('customers', 'services.musteri_id', '=', 'customers.id');

            // İl filtresi
            if ($il) {
                $query->where('customers.il', $il);
            }

            // İlçe filtresi
            if (!empty($bolgeler) && !in_array('0', $bolgeler)) {
                $query->whereIn('customers.ilce', $bolgeler);
            }

            // Cihaz filtresi
            if (!empty($cihazlar) && !in_array('0', $cihazlar)) {
                $query->whereIn('services.cihazTur', $cihazlar);
            }

            // Marka filtresi
            if (!empty($markalar) && !in_array('0', $markalar)) {
                $query->whereIn('services.cihazMarka', $markalar);
            }

            // Kaynak filtresi
            if (!empty($kaynaklar) && !in_array('0', $kaynaklar)) {
                $query->whereIn('services.servisKaynak', $kaynaklar);
            }

            // Durum filtresi
            if ($durumlar != 0) {
                $query->where('services.servisDurum', $durumlar);
            }

            $servisler = $query->orderBy('services.id', 'DESC')
                ->select('services.*')
                ->get();

            // Durum başlığı
            $durumBaslik = 'Arama Sonuçları';
            if ($durumlar != 0) {
                $servisDurumu = ServiceStage::find($durumlar);
                $durumBaslik = ($servisDurumu ? $servisDurumu->asama : '') . ' - Aşamasındaki Servisler';
            }

            return view('frontend.secure.bulk_sms.list', compact('servisler', 'durumBaslik','firma'))->render();

        } catch (\Exception $e) {
            \Log::error('Toplu SMS Listele Hatası: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Bir hata oluştu: ' . $e->getMessage()], 500);
        }
    }

    public function send(Request $request, $tenant_id)
    {
        try {
            $request->validate([
                'servisidler' => 'required|string',
                'mesaj' => 'required|string|max:120',
            ], [
                'servisidler.required' => 'En az 1 müşteri seçmelisiniz',
                'mesaj.required' => 'Mesaj boş olamaz',
                'mesaj.max' => 'Mesaj en fazla 120 karakter olabilir',
            ]);

            $gelenIdler = explode(', ', $request->input('servisidler'));
            $mesaj = $request->input('mesaj');

            // Firma bilgilerini al
            $firma = Tenant::find($tenant_id);
            if (!$firma) {
                return response()->json([
                    'success' => false,
                    'message' => 'Firma bulunamadı'
                ], 404);
            }

            // SMS Provider'ı al
            $smsProvider = SmsFactory::getProviderForTenant($tenant_id);
            
            if (!$smsProvider) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aktif SMS entegrasyonu bulunamadı. Lütfen önce bir SMS entegrasyonu satın alın ve yapılandırın.'
                ], 404);
            }

            // SMS karalistesi varsa ekle (opsiyonel)
            $smsKaraliste = $firma->sms_karaliste ?? '';
            $fullMessage = $smsKaraliste 
                ? $mesaj . ' SMS Iptal ' . $smsKaraliste
                : $mesaj;

            // Telefon numaralarını topla ve SMS kayıtlarını hazırla
            $telefonlar = [];
            $smsKayitlari = [];
            $activeIntegration = DB::table('integration_purchases')
                ->join('integrations', 'integration_purchases.integration_id', '=', 'integrations.id')
                ->where('integration_purchases.tenant_id', $tenant_id)
                ->where('integration_purchases.status', 'completed')
                ->where('integrations.category', 'sms')
                ->select('integrations.name')
                ->first();

            foreach ($gelenIdler as $servisId) {
                $servis = Service::with('musteri')->find($servisId);
                
                if (!$servis || !$servis->musteri || !$servis->musteri->tel1) {
                    continue;
                }

                // Telefon numarasını temizle ve formatla
                $tel = preg_replace('/[^0-9]/', '', $servis->musteri->tel1);
                
                // 0 ile başlamıyorsa ekle
                if (substr($tel, 0, 1) !== '0' && strlen($tel) == 10) {
                    $tel = '0' . $tel;
                }

                if (strlen($tel) != 11) {
                    \Log::warning('Geçersiz telefon numarası', [
                        'servis_id' => $servis->id,
                        'tel' => $tel
                    ]);
                    continue;
                }

                $telefonlar[] = $tel;

                // SMS kaydını hazırla
                $smsKayitlari[] = [
                    'firma_id' => $tenant_id,
                    'servis_id' => $servis->id,
                    'musteri_id' => $servis->musteri->id,
                    'gsmno' => $tel,
                    'mesaj' => $fullMessage,
                    'provider' => $activeIntegration->name ?? 'Unknown',
                    'durum' => 'beklemede',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (empty($telefonlar)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Geçerli telefon numarası bulunamadı'
                ], 400);
            }

            \Log::info('Toplu SMS Gönderiliyor', [
                'firma_id' => $tenant_id,
                'telefon_sayisi' => count($telefonlar),
                'provider' => $activeIntegration->name ?? 'Unknown'
            ]);

            // SMS gönder
            $sonuc = $smsProvider->sendBulkSms($telefonlar, $fullMessage);

            // SMS kayıtlarını güncelle
            if ($sonuc['success']) {
                foreach ($smsKayitlari as &$kayit) {
                    $kayit['durum'] = 'gonderildi';
                    $kayit['response_code'] = $sonuc['response_code'] ?? null;
                }
            } else {
                foreach ($smsKayitlari as &$kayit) {
                    $kayit['durum'] = 'basarisiz';
                    $kayit['hata_mesaji'] = $sonuc['message'] ?? 'Bilinmeyen hata';
                }
            }

            // Veritabanına kaydet
            BulkSms::insert($smsKayitlari);

            return response()->json($sonuc);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Toplu SMS Gönderme Hatası: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'SMS gönderilirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}

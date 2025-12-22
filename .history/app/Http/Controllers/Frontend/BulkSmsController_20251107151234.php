<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\DeviceBrand;
use App\Models\DeviceType;
use App\Models\Il;
use App\Models\Service;
use App\Models\ServiceResource;
use App\Models\ServiceStage;
use App\Models\Tenant;
use App\Services\NetgsmService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BulkSmsController extends Controller
{
    protected $netgsmService;

    public function __construct(NetgsmService $netgsmService)
    {
        $this->netgsmService = $netgsmService;
    }

    public function index($tenant_id)
    {   $firma = Tenant::where('id', $tenant_id)->first();
        if(!$firma) {
            $notification = array(
                'message' => 'Firma bulunamadı',
                'alert-type' => 'danger'
            );
            return redirect()->route('giris')->with($notification);
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
            'firma'
        ));
    }

    public function ilceGetir(Request $request)
    {
        $il = $request->input('ilceSecimId');
        $kid = tenant('id');

        // İlçeleri getir - kendi tablonuzda nasıl tutuyorsanız ona göre
        $ilceler = DB::table('ilce_secimleri')
            ->where('il', $il)
            ->where('kid', $kid)
            ->orderBy('ilce')
            ->pluck('ilce');

        return response()->json($ilceler);
    }

    public function list(Request $request, $tenant_id)
    {
        try {
            $firma = Tenant::where('id', $tenant_id)->first();
            if(!$firma) {
                $notification = array(
                    'message' => 'Firma bulunamadı',
                    'alert-type' => 'danger'
                );
                return redirect()->route('giris')->with($notification);
            }

            $il = $request->input('il');
            $bolgeler = $request->input('bolgeler', []);
            $cihazlar = $request->input('cihazlar', []);
            $markalar = $request->input('markalar', []);
            $kaynaklar = $request->input('kaynaklar', []);
            $durumlar = $request->input('durumlar', 0);
            
            // Tarihleri d-m-Y formatından al ve Y-m-d formatına çevir
        $tarih1 = $request->input('tarih1'); // d-m-Y formatında gelir
        $tarih2 = $request->input('tarih2'); // d-m-Y formatında gelir
        
        // Carbon ile parse et ve veritabanı formatına çevir
        
        
            // Query builder
            $query = Service::with(['musteri', 'markaCihaz', 'turCihaz'])
                ->where('services.durum', 1)
                ->where('services.firma_id', $tenant_id)
                ->whereBetween('services.kayitTarihi', [$tarih1, $tarih2])
                ->join('customers', 'services.musteri_id', '=', 'customers.id');

            // İl filtresi
            $query->where('customers.il', $il);

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
            \Log::error('Toplu SMS Listele Hatası: ' . $e->getMessage());
            return response()->json(['error' => 'Bir hata oluştu'], 500);
        }
    }

    public function gonder(Request $request)
    {
        
    }
}

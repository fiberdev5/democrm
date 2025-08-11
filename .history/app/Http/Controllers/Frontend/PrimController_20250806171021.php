<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PrimController extends Controller
{
     public function hesaplaPrim($data)
    {
        $user = auth()->user();
        $personelId = $data['personel_id'];
        $tarih1 = Carbon::createFromFormat('Y-m-d', $data['tarih1'])->startOfDay();
        $tarih2 = Carbon::createFromFormat('Y-m-d', $data['tarih2'])->endOfDay();
        $primDurum = $data['durum'];

        // Grup kontrolü ve prim hesaplama
        if ($user->hasAnyRole(['Operator'])) {
            return $this->operatorPrimHesapla($personelId, $tarih1, $tarih2);
        }

        if ($user->hasAnyRole(['Atölye Ustası', 'Atölye Çırak'])) {
            return $this->atolyeUstasiPrimHesapla($personelId, $tarih1, $tarih2);
        }

        // Varsayılan: Teknisyen
        if ($user->hasAnyRole(['Teknisyen'])) {
            return $this->teknisyenPrimHesapla($personelId, $tarih1, $tarih2);
        }
    }

    /**
     * Operator prim hesaplama
     */
    private function operatorPrimHesapla($personelId, $tarih1, $tarih2)
    {
        // Biten servisleri getir (gidenIslem=255)
        $bitenServisler = DB::table('service_plannings')
            ->join('services', 'services.id', '=', 'service_plannings.servisid')
            ->select('service_plannings.servisid', 'service_plannings.created_at', 
                    'service_plannings.gidenIslem', 'services.servisDurum', 
                    'services.kayitAlan', 'services.id')
            ->where('service_plannings.gidenIslem', 255)
            ->whereBetween('service_plannings.created_at', [$tarih1, $tarih2])
            ->where('services.kayitAlan', $personelId)
            ->groupBy('service_plannings.servisid')
            ->get();

        $servisIdler = [];
        
        foreach ($bitenServisler as $servis) {
            // Müşteri iptal kontrolü (gidenIslem=244)
            $iptalVar = DB::table('service_plannings')
                ->where('servisid', $servis->servisid)
                ->where('gidenIslem', 244)
                ->exists();
            
            if (!$iptalVar) {
                $servisIdler[] = $servis->id;
            }
        }

        if (empty($servisIdler)) {
            return [];
        }

        // Cevapları getir
        $cevaplar = DB::table('services')
            ->join('customers', 'services.musteri_id', '=', 'customers.id')
            ->join('device_brands', 'services.cihazMarka', '=', 'device_brands.id')
            ->join('device_types', 'services.cihazTur', '=', 'device_types.id')
            ->join('tb_user', 'services.kayitAlan', '=', 'tb_user.user_id')
            ->select('services.id', 'services.kayitAlan', 'customers.adSoyad',
                    'device_brands.marka', 'device_brands.operatorPrim as mOptPrim',
                    'device_brands.atolyePrim as mAtyPrim', 'device_types.cihaz',
                    'device_types.operatorPrim as cOptPrim', 'device_types.atolyePrim as cAtyPrim')
            ->where('services.durum', '1')
            ->whereIn('services.id', $servisIdler)
            ->get();

        return $cevaplar->toArray();
    }

    /**
     * Atölye ustası prim hesaplama
     */
    private function atolyeUstasiPrimHesapla($personelId, $tarih1, $tarih2)
    {
        // Personelin işlem yaptığı servisleri getir
        $bitenServisler = DB::table('service_plannings')
            ->join('servisler', 'servisler.id', '=', 'service_plannings.servisid')
            ->select('service_plannings.servisid')
            ->where('service_plannings.pid', $personelId)
            ->groupBy('service_plannings.servisid')
            ->get();

        $servisIdler = [];

        foreach ($bitenServisler as $servis) {
            // Müşteri iptal kontrolü
            $musteriIptal = DB::table('service_plannings')
                ->where('servisid', $servis->servisid)
                ->where('gidenIslem', 244)
                ->exists();

            if (!$musteriIptal) {
                // Cihaz tamir edilemiyor kontrolü
                $cihazTamirEdilemiyor = DB::table('service_plannings')
                    ->where('servisid', $servis->servisid)
                    ->where('gidenIslem', 246)
                    ->exists();

                if (!$cihazTamirEdilemiyor) {
                    // Servis son durum kontrolü
                    $servisSon = DB::table('service_plannings')
                        ->where('servisid', $servis->servisid)
                        ->where('gidenIslem', 255)
                        ->exists();

                    if ($servisSon) {
                        $servisIdler[] = $servis->servisid;
                    }
                }
            }
        }

        if (empty($servisIdler)) {
            return [];
        }

        // Teslimata hazır işlemini seçenler
        $planlama = DB::table('service_plannings')
            ->whereBetween('created_at', [$tarih1, $tarih2])
            ->whereIn('servisid', $servisIdler)
            ->where('pid', $personelId)
            ->where('gidenIslem', '252')
            ->groupBy('servisid')
            ->pluck('servisid')
            ->toArray();

        if (empty($planlama)) {
            return [];
        }

        return DB::table('services')
            ->join('customers', 'services.musteri_id', '=', 'customers.id')
            ->join('device_brands', 'services.cihazMarka', '=', 'device_brands.id')
            ->join('device_types', 'services.cihazTur', '=', 'device_types.id')
            ->select('services.id', 'customers.adSoyad', 'cihaz_markalari.marka',
                    'device_brands.operatorPrim as mOptPrim', 'device_brands.atolyePrim as mAtyPrim',
                    'device_types.cihaz', 'device_types.operatorPrim as cOptPrim',
                    'device_types.atolyePrim as cAtyPrim')
            ->where('services.durum', '1')
            ->whereIn('services.id', $planlama)
            ->get()
            ->toArray();
    }

    /**
     * Teknisyen prim hesaplama
     */
    private function teknisyenPrimHesapla($personelId, $tarih1, $tarih2)
    {
        // Personel ve grup bilgilerini getir
        $personel = DB::table('tb_user')->find($personelId);
        $grup = $personel->role();

        // Personelin işlem yaptığı servisleri getir
        $bitenServisler = DB::table('service_plannings')
            ->join('services', 'services.id', '=', 'service_plannings.servisid')
            ->select('service_plannings.servisid')
            ->where('service_plannings.pid', $personelId)
            ->groupBy('service_plannings.servisid')
            ->get();

        $servisIdler = [];

        foreach ($bitenServisler as $servis) {
            // Müşteri iptal kontrolü
            $musteriIptal = DB::table('service_plannings')
                ->where('servisid', $servis->servisid)
                ->where('gidenIslem', 244)
                ->exists();

            if (!$musteriIptal) {
                // Cihaz tamir edilemiyor kontrolü
                $cihazTamirEdilemiyor = DB::table('service_plannings')
                    ->where('servisid', $servis->servisid)
                    ->where('gidenIslem', 246)
                    ->exists();

                if (!$cihazTamirEdilemiyor) {
                    // Servis son durum kontrolü
                    $servisSon = DB::table('service_plannings')
                        ->where('servisid', $servis->servisid)
                        ->where('gidenIslem', 255)
                        ->exists();

                    if ($servisSon) {
                        $servisIdler[] = $servis->servisid;
                    }
                }
            }
        }

        if (empty($servisIdler)) {
            return [];
        }

        // Personelin planlamalarını getir
        $planlama = DB::table('service_plannings')
            ->whereBetween('created_at', [$tarih1, $tarih2])
            ->whereIn('servisid', $servisIdler)
            ->where('pid', $personelId)
            ->pluck('id')
            ->toArray();

        if (empty($planlama)) {
            return [];
        }

        // Teklif cevaplarını getir
        $cevaplar = DB::table('service_stage_answers')
            ->join('stage_questions', 'service_stage_answers.soruid', '=', 'stage_questions.id')
            ->whereIn('service_stage_answers.planid', $planlama)
            ->where('servis_asama_sorulari.cevapText', '[Teklif]')
            ->where('service_stage_answers.cevap', '>', 0)
            ->pluck('service_stage_answers.id')
            ->toArray();

        if (empty($cevaplar)) {
            return [];
        }

        // Tarihlere göre toplamları getir
        $cevapToplam = DB::table('service_stage_answers')
            ->select('id', 'servisid', 'planid', 'soruid', 
                    DB::raw('CAST(created_at AS DATE) as tarihDate'),
                    DB::raw('SUM(cevap) as toplamCevap'))
            ->whereIn('id', $cevaplar)
            ->groupBy(DB::raw('CAST(created_at AS DATE)'))
            ->orderBy('servisid', 'ASC')
            ->get();

        // Minimum tutarı geçen tarihleri bul
        $toplamTarihler = [];
        foreach ($cevapToplam as $cevap) {
            if ($cevap->toplamCevap > $grup->tutar) {
                $toplamTarihler[] = $cevap->tarihDate;
            }
        }

        if (empty($toplamTarihler)) {
            return [];
        }

        // Final cevapları getir
        return DB::table('service_stage_answers')
            ->whereIn('id', $cevaplar)
            ->whereIn(DB::raw('DATE(created_at)'), $toplamTarihler)
            ->orderBy('servisid', 'ASC')
            ->get()
            ->toArray();
    }

    public function index($tenant_id)
    {
        $personeller = User::select('user_id', 'name')
            ->where('tenant_id',$tenant_id)
            ->where('status', 1)
            ->orderBy('name')
            ->get();
        $firma = Tenant::where('id',$tenant_id)->first();
        return view('frontend.secure.prim.index', compact('personeller', 'firma'));
    }

    /**
     * Prim hesaplama işlemi
     */
    public function hesapla(Request $request)
    {

        try {
            $data = $request->only(['personel_id', 'tarih1', 'tarih2', 'durum']);
            $sonuclar = $this->hesaplaPrim($data);

            // Personel bilgisini getir
            $personel = DB::table('tb_user')
                ->select('name')
                ->find($request->personel_id);

            $grup = $personel->roles()->name;

            return response()->json([
                'success' => true,
                'data' => [
                    'sonuclar' => $sonuclar,
                    'personel' => $personel,
                    'grup' => $grup,
                    'tarih_araligi' => [
                        'baslangic' => $request->tarih1,
                        'bitis' => $request->tarih2
                    ],
                    'toplam_kayit' => count($sonuclar)
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Prim hesaplama hatası: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Prim hesaplama sırasında bir hata oluştu.'
            ], 500);
        }
    }

    /**
     * AJAX ile personel listesi getir
     */
    public function getPersoneller(Request $request)
    {
        $query = DB::table('tb_user')
            ->select('user_id', 'name')
            ->where('status', 1);

       

        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $personeller = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $personeller
        ]);
    }

    /**
     * Prim detayları
     */
    public function detay(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'servis_id' => 'required|exists:servisler,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $servisDetay = DB::table('servisler')
                ->join('musteriler', 'servisler.musteriid', '=', 'musteriler.id')
                ->join('cihaz_markalari', 'servisler.cihazMarka', '=', 'cihaz_markalari.id')
                ->join('cihaz_turleri', 'servisler.cihazTur', '=', 'cihaz_turleri.id')
                ->join('personeller', 'servisler.kayitAlan', '=', 'personeller.id')
                ->select(
                    'servisler.*',
                    'musteriler.adSoyad as musteri_adi',
                    'musteriler.telefon',
                    'cihaz_markalari.marka',
                    'cihaz_turleri.cihaz',
                    'personeller.adsoyad as personel_adi'
                )
                ->where('servisler.id', $request->servis_id)
                ->first();

            $servisPlanlama = DB::table('servis_planlama')
                ->join('personeller', 'servis_planlama.pid', '=', 'personeller.id')
                ->select(
                    'servis_planlama.*',
                    'personeller.adsoyad as personel_adi'
                )
                ->where('servis_planlama.servisid', $request->servis_id)
                ->orderBy('servis_planlama.tarih')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'servis' => $servisDetay,
                    'planlama' => $servisPlanlama
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Servis detay hatası: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Servis detayı getirilirken hata oluştu.'
            ], 500);
        }
    }
}

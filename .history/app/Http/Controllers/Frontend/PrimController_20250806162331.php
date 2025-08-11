<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PrimController extends Controller
{
     public function hesaplaPrim($data)
    {
        $kullaniciGrup = auth()->user()->grup_id;
        $personelId = $data['personel_id'];
        $tarih1 = Carbon::createFromFormat('d/m/Y', $data['tarih1'])->startOfDay();
        $tarih2 = Carbon::createFromFormat('d/m/Y', $data['tarih2'])->endOfDay();
        $primDurum = $data['durum'];

        // Grup kontrolü ve prim hesaplama
        switch ($kullaniciGrup) {
            case '243': // Operator
            case '256':
                return $this->operatorPrimHesapla($personelId, $tarih1, $tarih2);
                
            case '246': // Atölye Ustası
            case '247':
                return $this->atolyeUstasiPrimHesapla($personelId, $tarih1, $tarih2);
                
            default: // Teknisyen
                return $this->teknisyenPrimHesapla($personelId, $tarih1, $tarih2);
        }
    }

    /**
     * Operator prim hesaplama
     */
    private function operatorPrimHesapla($personelId, $tarih1, $tarih2)
    {
        // Biten servisleri getir (gidenIslem=255)
        $bitenServisler = DB::table('servis_planlama')
            ->join('servisler', 'servisler.id', '=', 'servis_planlama.servisid')
            ->select('servis_planlama.servisid', 'servis_planlama.tarih', 
                    'servis_planlama.gidenIslem', 'servisler.servisDurum', 
                    'servisler.kayitAlan', 'servisler.id')
            ->where('servis_planlama.gidenIslem', 255)
            ->whereBetween('servis_planlama.tarih', [$tarih1, $tarih2])
            ->where('servisler.kayitAlan', $personelId)
            ->groupBy('servis_planlama.servisid')
            ->get();

        $servisIdler = [];
        
        foreach ($bitenServisler as $servis) {
            // Müşteri iptal kontrolü (gidenIslem=244)
            $iptalVar = DB::table('servis_planlama')
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
        $cevaplar = DB::table('servisler')
            ->join('musteriler', 'servisler.musteriid', '=', 'musteriler.id')
            ->join('cihaz_markalari', 'servisler.cihazMarka', '=', 'cihaz_markalari.id')
            ->join('cihaz_turleri', 'servisler.cihazTur', '=', 'cihaz_turleri.id')
            ->join('personeller', 'servisler.kayitAlan', '=', 'personeller.id')
            ->join('gruplar', 'gruplar.id', '=', 'personeller.grup')
            ->select('servisler.id', 'servisler.kayitAlan', 'musteriler.adSoyad',
                    'cihaz_markalari.marka', 'cihaz_markalari.optPrim as mOptPrim',
                    'cihaz_markalari.atyPrim as mAtyPrim', 'cihaz_turleri.cihaz',
                    'cihaz_turleri.optPrim as cOptPrim', 'cihaz_turleri.atyPrim as cAtyPrim',
                    'personeller.adsoyad', 'personeller.prim as persPrim',
                    'personeller.grup', 'gruplar.prim as grupPrim')
            ->where('servisler.durum', '1')
            ->whereIn('servisler.id', $servisIdler)
            ->get();

        return $cevaplar->toArray();
    }

    /**
     * Atölye ustası prim hesaplama
     */
    private function atolyeUstasiPrimHesapla($personelId, $tarih1, $tarih2)
    {
        // Personelin işlem yaptığı servisleri getir
        $bitenServisler = DB::table('servis_planlama')
            ->join('servisler', 'servisler.id', '=', 'servis_planlama.servisid')
            ->select('servis_planlama.servisid')
            ->where('servis_planlama.pid', $personelId)
            ->groupBy('servis_planlama.servisid')
            ->get();

        $servisIdler = [];

        foreach ($bitenServisler as $servis) {
            // Müşteri iptal kontrolü
            $musteriIptal = DB::table('servis_planlama')
                ->where('servisid', $servis->servisid)
                ->where('gidenIslem', 244)
                ->exists();

            if (!$musteriIptal) {
                // Cihaz tamir edilemiyor kontrolü
                $cihazTamirEdilemiyor = DB::table('servis_planlama')
                    ->where('servisid', $servis->servisid)
                    ->where('gidenIslem', 246)
                    ->exists();

                if (!$cihazTamirEdilemiyor) {
                    // Servis son durum kontrolü
                    $servisSon = DB::table('servis_planlama')
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
        $planlama = DB::table('servis_planlama')
            ->whereBetween('tarih', [$tarih1, $tarih2])
            ->whereIn('servisid', $servisIdler)
            ->where('pid', $personelId)
            ->where('gidenIslem', '252')
            ->groupBy('servisid')
            ->pluck('servisid')
            ->toArray();

        if (empty($planlama)) {
            return [];
        }

        return DB::table('servisler')
            ->join('musteriler', 'servisler.musteriid', '=', 'musteriler.id')
            ->join('cihaz_markalari', 'servisler.cihazMarka', '=', 'cihaz_markalari.id')
            ->join('cihaz_turleri', 'servisler.cihazTur', '=', 'cihaz_turleri.id')
            ->select('servisler.id', 'musteriler.adSoyad', 'cihaz_markalari.marka',
                    'cihaz_markalari.optPrim as mOptPrim', 'cihaz_markalari.atyPrim as mAtyPrim',
                    'cihaz_turleri.cihaz', 'cihaz_turleri.optPrim as cOptPrim',
                    'cihaz_turleri.atyPrim as cAtyPrim')
            ->where('servisler.durum', '1')
            ->whereIn('servisler.id', $planlama)
            ->get()
            ->toArray();
    }

    /**
     * Teknisyen prim hesaplama
     */
    private function teknisyenPrimHesapla($personelId, $tarih1, $tarih2)
    {
        // Personel ve grup bilgilerini getir
        $personel = DB::table('personeller')->find($personelId);
        $grup = DB::table('gruplar')->find($personel->grup);

        // Personelin işlem yaptığı servisleri getir
        $bitenServisler = DB::table('servis_planlama')
            ->join('servisler', 'servisler.id', '=', 'servis_planlama.servisid')
            ->select('servis_planlama.servisid')
            ->where('servis_planlama.pid', $personelId)
            ->groupBy('servis_planlama.servisid')
            ->get();

        $servisIdler = [];

        foreach ($bitenServisler as $servis) {
            // Müşteri iptal kontrolü
            $musteriIptal = DB::table('servis_planlama')
                ->where('servisid', $servis->servisid)
                ->where('gidenIslem', 244)
                ->exists();

            if (!$musteriIptal) {
                // Cihaz tamir edilemiyor kontrolü
                $cihazTamirEdilemiyor = DB::table('servis_planlama')
                    ->where('servisid', $servis->servisid)
                    ->where('gidenIslem', 246)
                    ->exists();

                if (!$cihazTamirEdilemiyor) {
                    // Servis son durum kontrolü
                    $servisSon = DB::table('servis_planlama')
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
        $planlama = DB::table('servis_planlama')
            ->whereBetween('tarih', [$tarih1, $tarih2])
            ->whereIn('servisid', $servisIdler)
            ->where('pid', $personelId)
            ->pluck('id')
            ->toArray();

        if (empty($planlama)) {
            return [];
        }

        // Teklif cevaplarını getir
        $cevaplar = DB::table('servis_asama_cevaplari')
            ->join('servis_asama_sorulari', 'servis_asama_cevaplari.soruid', '=', 'servis_asama_sorulari.id')
            ->whereIn('servis_asama_cevaplari.planid', $planlama)
            ->where('servis_asama_sorulari.cevap', '[Teklif]')
            ->where('servis_asama_cevaplari.cevap', '>', 0)
            ->pluck('servis_asama_cevaplari.id')
            ->toArray();

        if (empty($cevaplar)) {
            return [];
        }

        // Tarihlere göre toplamları getir
        $cevapToplam = DB::table('servis_asama_cevaplari')
            ->select('id', 'servisid', 'planid', 'soruid', 
                    DB::raw('CAST(tarih AS DATE) as tarihDate'),
                    DB::raw('SUM(cevap) as toplamCevap'))
            ->whereIn('id', $cevaplar)
            ->groupBy(DB::raw('CAST(tarih AS DATE)'))
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
        return DB::table('servis_asama_cevaplari')
            ->whereIn('id', $cevaplar)
            ->whereIn(DB::raw('DATE(tarih)'), $toplamTarihler)
            ->orderBy('servisid', 'ASC')
            ->get()
            ->toArray();
    }

    public function index($tenant_id)
    {
        $personeller = User::where('tenant_id',$tenant_id)
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
        $validator = Validator::make($request->all(), [
            'personel_id' => 'required|exists:personeller,id',
            'tarih1' => 'required|date_format:d/m/Y',
            'tarih2' => 'required|date_format:d/m/Y|after_or_equal:tarih1',
            'durum' => 'required'
        ], [
            'personel_id.required' => 'Personel seçimi zorunludur.',
            'personel_id.exists' => 'Geçersiz personel seçimi.',
            'tarih1.required' => 'Başlangıç tarihi zorunludur.',
            'tarih1.date_format' => 'Başlangıç tarihi gg/aa/yyyy formatında olmalıdır.',
            'tarih2.required' => 'Bitiş tarihi zorunludur.',
            'tarih2.date_format' => 'Bitiş tarihi gg/aa/yyyy formatında olmalıdır.',
            'tarih2.after_or_equal' => 'Bitiş tarihi başlangıç tarihinden sonra olmalıdır.',
            'durum.required' => 'Durum seçimi zorunludur.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->only(['personel_id', 'tarih1', 'tarih2', 'durum']);
            $sonuclar = $this->primHesaplamaService->hesaplaPrim($data);

            // Personel bilgisini getir
            $personel = DB::table('personeller')
                ->select('adsoyad', 'grup')
                ->find($request->personel_id);

            $grup = DB::table('gruplar')
                ->select('grup_adi')
                ->find($personel->grup);

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
        $query = DB::table('personeller')
            ->select('id', 'adsoyad', 'grup')
            ->where('durum', 1);

        if ($request->has('grup') && !empty($request->grup)) {
            $query->where('grup', $request->grup);
        }

        if ($request->has('search') && !empty($request->search)) {
            $query->where('adsoyad', 'like', '%' . $request->search . '%');
        }

        $personeller = $query->orderBy('adsoyad')->get();

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

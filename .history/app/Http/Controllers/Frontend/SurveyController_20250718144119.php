<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Survey;
use App\Models\Service;
use App\Models\User; // User modelini ekleyin, eğer Auth::id() kullanıyorsanız
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Log sınıfını ekleyin

class SurveyController extends Controller
{
    public function SurveyCreate($tenant_id, $servisId)
    {
        $servis = Service::findOrFail($servisId);
        $mevcutAnket = Survey::where('servisid', $servisId)->first();

        return view('frontend.secure.surveys.survey_form', [
            'servis' => $servis,
            'anket' => $mevcutAnket,
            'tenant_id' => $tenant_id, 
        ]);
    }

    public function SurveyStore(Request $request, $tenant_id, $servisId)
    {
        // Validation (zorunlu alanları kontrol et)
        $request->validate([
            'soru1' => 'required|in:0,1,2',
            'soru2' => 'required|in:0,1,2',
            'soru3' => 'required|in:0,1,2',
            'soru4Text' => 'nullable|string|max:255',
            'soru5' => 'required|in:0,1,2',
            'soru1Text' => 'nullable|string|max:500',
            'soru2Text' => 'nullable|string|max:500',
            'soru3Text' => 'nullable|string|max:500',
            'soru5Text' => 'nullable|string|max:500',
        ]);

        try {
            // Mevcut anket var mı kontrol et (update için)
            $anket = Survey::where('servisid', $servisId)->first();
            $isNew = false; // Anketin yeni mi olduğunu takip etmek için

            if (!$anket) {
                // Yeni anket oluştur
                $anket = new Survey();
                $anket->servisid = $servisId;
                // 'ekleyen' ve 'personel' alanları için Auth::id() kullanımı, 
                // eğer anket formunu dolduran kişinin sistemde girişli bir kullanıcı olması bekleniyorsa doğrudur.
                // Aksi takdirde, bu alanları null bırakmalı veya farklı bir mantıkla doldurmalısınız.
                $anket->ekleyen = Auth::id(); 
                $anket->personel = Auth::id(); 
                $isNew = true; // Yeni bir anket oluşturuldu
            }

            // Verileri ata
            $anket->soru1 = $request->input('soru1');
            $anket->soru1Text = $request->input('soru1Text');
            $anket->soru2 = $request->input('soru2');
            $anket->soru2Text = $request->input('soru2Text');
            $anket->soru3 = $request->input('soru3');
            $anket->soru3Text = $request->input('soru3Text');
            $anket->soru4 = 0; 
            $anket->soru4Text = $request->input('soru4Text');
            $anket->soru5 = $request->input('soru5');
            $anket->soru5Text = $request->input('soru5Text');

            // Kaydet
            $anket->save();

            $message = $isNew ? 'Anket başarıyla kaydedildi.' : 'Anket başarıyla güncellendi.';
            
            // Başarılı durumda JSON yanıtı döndür
            return response()->json(['success' => true, 'message' => $message], 200);

        } catch (\Exception $e) {
            // Hata durumunda loglama yapabiliriz
            Log::error('Anket kaydetme hatası: ' . $e->getMessage(), ['servisId' => $servisId, 'request_data' => $request->all()]);
            // Hata durumunda JSON yanıtı döndür
            return response()->json(['success' => false, 'error' => 'Beklenmeyen bir hata oluştu. Lütfen tekrar deneyin.'], 500);
        }
    }
}
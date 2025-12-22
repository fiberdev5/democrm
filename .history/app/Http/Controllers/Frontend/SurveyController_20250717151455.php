<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Survey;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SurveyController extends Controller
{
     public function getSurveyForm(Request $request, $tenant_id, $serviceId)
    {
        $type = $request->input('tip'); // 'personel' veya 'bayi'
        $targetId = $request->input('id'); // Anket yapılacak personel veya bayi ID'si

        $service = Service::where('id', $serviceId)->first();

        // İzin kontrolü (örneğin, sadece ilgili firmanın servisleri için)
        // Eğer Service modelinizde bir `firma_id` veya `tenant_id` alanı varsa,
        // bu kontrolü burada yapmalısınız:
        // if ($service->firma_id != $tenant_id) {
        //     return "-1"; // İzin hatası
        // }

        // Giriş yapan kullanıcının ID'si ile servis kayıt edenin ID'si kontrolü
        // Eski PHP kodunuzdaki $kid kontrolüne benzer:
        if ($service->users->id != Auth::id()) {
            // Eğer giriş yapan kullanıcı servis kaydını yapan kişi değilse
            // veya başka bir yetkilendirme kontrolü gerekiyorsa
            // return "-1"; // İzin hatası
        }

        $survey = null;
        if ($type == "bayi") {
            // Bayi için anket arama (eğer Survey modelinizde 'bayi' sütunu varsa)
            $survey = Survey::where('servisid', $serviceId)
                            ->where('bayi', $targetId)
                            ->first();
        } else { // type == "personel"
            // Personel için anket arama
            $survey = Survey::where('servisid', $serviceId)
                            ->where('personel', $targetId)
                            ->first();
        }
        // Anket formunu bir Blade view'ında render edip döndürelim
        // Bu Blade dosyasını 'resources/views/surveys/form.blade.php' olarak oluşturacağız.
        return view('surveys.form', compact('survey', 'serviceId', 'type', 'targetId'));
    }

    public function saveSurvey(Request $request, $tenant_id)
    {
        // Yetkilendirme kontrolü
        // if (!Auth::user()->can('create-survey')) { // Varsayımsal bir yetkilendirme kontrolü
        //     return response()->json(['status' => 'error', 'message' => 'Yetkiniz yok.'], 403);
        // }

        $servisId = $request->input('servisid');
        $personelId = $request->input('personelId'); // Formdan gelen personel ID'si
        // Eski PHP kodunuzda bayi kontrolü vardı, eğer bayi için anket yapılıyorsa
        // buraya ilgili 'bayi' ID'si de eklenebilir. Şimdilik 'personel' üzerinden gidelim.

        $fiyat = str_replace(",", ".", $request->input('soru4Text')); // Virgülü noktaya çevir

        // Daha önce bu servis ve personel/bayi için anket yapılmış mı kontrol et
        $survey = Survey::where('servisid', $servisId)
                        ->where('personel', $personelId) // veya ->where('bayi', $bayiId)
                        ->first();

        $data = [
            'ekleyen'   => Auth::id(), // Anketi ekleyen kullanıcı
            'personel'  => $personelId, // Anket yapılan personel ID'si
            'servisid'  => $servisId,
            'soru1'     => $request->input('soru1'),
            'soru1Text' => $request->input('soru1Text'),
            'soru2'     => $request->input('soru2'),
            'soru2Text' => $request->input('soru2Text'),
            'soru3'     => $request->input('soru3'),
            'soru3Text' => $request->input('soru3Text'),
            'soru4'     => 0, 
            'soru4Text' => $fiyat,
            'soru5'     => $request->input('soru5'),
            'soru5Text' => $request->input('soru5Text'),
        ];

        if ($survey) {
            // Mevcut anket varsa güncelle
            $survey->update($data);
            return response()->json(['status' => 'success', 'message' => 'Anket başarıyla güncellendi.']);
        } else {
            // Yoksa yeni anket oluştur
            Survey::create($data);
            return response()->json(['status' => 'success', 'message' => 'Anket başarıyla kaydedildi.']);
        }
    }
    
    
}

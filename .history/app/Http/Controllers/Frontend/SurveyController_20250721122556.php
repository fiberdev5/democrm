<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Survey;
use App\Models\Service;
use App\Models\User; 
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; 
use App\Models\ServiceStageAnswer;

class SurveyController extends Controller
{
public function SurveyCreate($tenant_id, $servisId)
{
    $servis = Service::findOrFail($servisId);
    $mevcutAnket = Survey::where('servisid', $servisId)->first();

    // Servisi yapan personeli ServiceStageAnswer tablosundan bul
    $personelYapilanServis = ServiceStageAnswer::where('servisid', $servisId)
                                            ->where('soruid', 45)
                                            ->first();

    // Eğer personel bilgisi mevcutsa, ID'sini al
    $servisPersonelId = $personelYapilanServis ? $personelYapilanServis->cevap : null; // veya cevapText

    $bayiRole =Role::find(259);
    $bayiRoleId = $bayiRole->id;
    

   // Mevcut anket varsa, anketi yapılan personelin ID'sini al (eğer varsa)
        $anketYapilanPersonelId = $mevcutAnket?->personel; 
    

    // Sadece bayi rolüne sahip olmayan personelleri listele
    $personeller = User::where('tenant_id', $tenant_id)
            ->whereDoesntHave('roles', function($query) use ($bayiRoleId) {
                $query->where('id', $bayiRoleId);
            })
            ->get(); 
        

    return view('frontend.secure.surveys.survey_form', [
        'servis' => $servis,
        'anket' => $mevcutAnket,
        'tenant_id' => $tenant_id,
        'personeller' => $personeller,
        'anketYapilanPersonelId' => $anketYapilanPersonelId,
        'servisPersonelId' => $servisPersonelId,
    ]);
}

    public function SurveyStore(Request $request, $tenant_id, $servisId)
    {
        // Validation
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
            'personel' => 'nullable|integer|exists:tb_user,user_id', // Anket yapılan personel
        ]);

        try {
            // Servisi çek
            $servis = Service::findOrFail($servisId);
            // Mevcut anket var mı
            $anket = Survey::where('servisid', $servisId)->first();
            $isNew = false; // Anketin yeni mi olduğunu takip etmek için

            if (!$anket) {
                // Yeni anket oluştur
                $anket = new Survey();
                $anket->servisid = $servisId;
                $anket->ekleyen = Auth::id(); 
                $isNew = true; // Yeni bir anket oluşturuldu
            }


            // Anketi yapılan personeli belirle
            // Öncelikle ServiceStageAnswer tablosundan servisi yapan personeli bulmaya çalış
            $personelYapilanServis = ServiceStageAnswer::where('servisid', $servisId)
                                            ->where('soruid', 45)
                                            ->first();


            
            $personelIdToAssign = null;
            if ($personelYapilanServis) {
                $personelIdToAssign = $personelYapilanServis->cevap; 
            }
            // Eğer ServiceStageAnswer'dan personel bulunamazsa VEYA formdan bir personel seçilmişse,
            // request'ten gelen 'personel' değerini kullan. Bu, manuel atama esnekliği sağlar.
            if ($request->filled('personel')) {
                $personelIdToAssign = $request->input('personel');
            }
            $anket->personel = $personelIdToAssign ?? 0; 

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
            Log::error('Anket kaydetme hatası: ' . $e->getMessage(), ['servisId' => $servisId, 'request_data' => $request->all()]);
            return response()->json(['success' => false, 'error' => 'Beklenmeyen bir hata oluştu. Lütfen tekrar deneyin.'], 500);
        }
    }
}
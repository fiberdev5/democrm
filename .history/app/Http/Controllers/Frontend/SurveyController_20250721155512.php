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
    $servisPersonelId = $personelYapilanServis ? (int)$personelYapilanServis->cevap : null;



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
    
    // Sadece bayi rolüne sahip kullanıcıları listele
    $bayiler = User::where('tenant_id', $tenant_id)
                ->whereHas('roles', function($query) use ($bayiRoleId) {
                    $query->where('id', $bayiRoleId);
                })
                ->get();
        

    return view('frontend.secure.surveys.survey_form', [
        'servis' => $servis,
        'anket' => $mevcutAnket,
        'tenant_id' => $tenant_id,
        'personeller' => $personeller,
        'bayiler' => $bayiler,
        'anketYapilanPersonelId' => $anketYapilanPersonelId,
        'servisPersonelId' => $servisPersonelId,
    ]);
}

   public function SurveyStore(Request $request, $tenant_id, $servisId)
{
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
        'personel' => 'nullable|integer|exists:tb_user,user_id',
    ]);

    try {
        $servis = Service::findOrFail($servisId);
        $anket = Survey::where('servisid', $servisId)->first();
        $isNew = false;

        if (!$anket) {
            $anket = new Survey();
            $anket->servisid = $servisId;
            $anket->ekleyen = Auth::id();
            $isNew = true;
        }

        // 🔍 Aktif kullanıcı
        $user = Auth::user();

        $isBayi = $user->hasRole('Bayi') || $user->roles->pluck('id')->contains(259);

        // Anketi yapılacak kişi ID'sini belirle
        $personelIdToAssign = 0;

        if ($isBayi) {
            // Kullanıcı bayi ise: kendisi
            $personelIdToAssign = $user->user_id;
        } else {
            // Bayi değilse: önce ServiceStageAnswer'a bak, yoksa request'ten al
            $personelYapilanServis = ServiceStageAnswer::where('servisid', $servisId)
                ->where('soruid', 45)
                ->first();

            if ($personelYapilanServis && is_numeric($personelYapilanServis->cevap)) {
                $personelIdToAssign = (int) $personelYapilanServis->cevap;
            } elseif ($request->filled('personel')) {
                $personelIdToAssign = (int) $request->input('personel');
            }
        }

        // Personeli ata
        $anket->personel = $personelIdToAssign;

        // Anket sorularını ata
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

        $anket->save();

        return response()->json([
            'success' => true,
            'message' => $isNew ? 'Anket başarıyla kaydedildi.' : 'Anket başarıyla güncellendi.',
        ]);
    } catch (\Throwable $e) {
        Log::error('Anket kaydetme hatası: ' . $e->getMessage(), [
            'servisId' => $servisId,
            'request_data' => $request->all(),
        ]);

        return response()->json([
            'success' => false,
            'error' => 'Beklenmeyen bir hata oluştu. Lütfen tekrar deneyin.',
        ], 500);
    }
}

}
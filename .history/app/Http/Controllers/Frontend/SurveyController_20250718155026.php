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
    public function SurveyCreate($tenant_id, $servisId,Request $request)
    {
        $userId = $request->query('user_id');
         if (empty($userId)) {
            return redirect()->back()->with('error', 'Anket yapılacak kullanıcı ID\'si belirtilmedi.');
        }

        $servis = Service::findOrFail($servisId);
        if (!$servis || (Auth::check() && $servis->tenant_id != $tenant_id)) {
            return redirect()->back()->with('error', 'Servis bulunamadı veya yetkiniz yok.');
        }

        // Anketin yapılacağı kullanıcıyı bul (personel veya bayi)
        $targetUser = User::find($userId);

        if (!$targetUser) {
            return redirect()->back()->with('error', 'Hedef kullanıcı bulunamadı.');
        }

        // Kullanıcının rolüne göre tip belirle
        $tip = null;
        if ($targetUser->isBayi()) {
            $tip = 'bayi';
        } elseif ($targetUser->isPersonel()) {
            $tip = 'personel';
        } else {
            // Eğer kullanıcı ne bayi ne de personel ise, anket yapılamaz.
            return redirect()->back()->with('error', 'Bu kullanıcıya anket yapılamaz (rolü uygun değil).');
        }

       // Daha önce bu servis ve kullanıcı için yapılmış bir anket var mı kontrol et
        $anket = null;
        if ($tip === 'bayi') {
            $anket = Survey::where('servisid', $servisId)
                           ->where('bayi', $userId)
                           ->first();
        } else { // personel
            $anket = Survey::where('servisid', $servisId)
                           ->where('personel', $userId)
                           ->first();
        }

     return view('frontend.secure.surveys.survey_form', compact('servis', 'anket', 'tip', 'userId', 'tenant_id'));
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
            // Mevcut anket var mı
            $anket = Survey::where('servisid', $servisId)->first();
            $isNew = false; // Anketin yeni mi olduğunu takip etmek için

            if (!$anket) {
                // Yeni anket oluştur
                $anket = new Survey();
                $anket->servisid = $servisId;
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
            Log::error('Anket kaydetme hatası: ' . $e->getMessage(), ['servisId' => $servisId, 'request_data' => $request->all()]);
            return response()->json(['success' => false, 'error' => 'Beklenmeyen bir hata oluştu. Lütfen tekrar deneyin.'], 500);
        }
    }
}
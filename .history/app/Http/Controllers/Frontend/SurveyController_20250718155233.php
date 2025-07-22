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

        $servis = Service::find($servisId);
        if (!$servis || (Auth::check() && $servis->tenant_id != $tenant_id)) {
            return response()->json(['success' => false, 'error' => 'Servis bulunamadı veya yetkiniz yok.'], 403);
        }

        $userId = $request->input('user_id'); // Formdan user_id'yi alıyoruz

        if (empty($userId)) {
            return response()->json(['success' => false, 'error' => 'Anket yapılacak kullanıcı ID\'si belirtilmedi.'], 400);
        }

        $targetUser = User::find($userId);

        if (!$targetUser) {
            return response()->json(['success' => false, 'error' => 'Hedef kullanıcı bulunamadı.'], 404);
        }

          // Kullanıcının rolüne göre tip belirle
        $tip = null;
        if ($targetUser->isBayi()) {
            $tip = 'bayi';
        } elseif ($targetUser->isPersonel()) {
            $tip = 'personel';
        } else {
            return response()->json(['success' => false, 'error' => 'Bu kullanıcıya anket yapılamaz (rolü uygun değil).'], 400);
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

        // Anket verilerini hazırla
        $data = $request->except(['_token', 'user_id']); 
        $data['servisid'] = $servisId;
        $data['ekleyen'] = Auth::id(); // Anketi dolduran kullanıcının ID'si

        // Tip'e göre personel veya bayi ID'sini ata
        if ($tip === 'bayi') {
            $data['bayi'] = $userId;
            $data['personel'] = null; // Personel alanı boş bırakılır
        } else { // personel
            $data['personel'] = $userId;
            $data['bayi'] = null; // Bayi alanı boş bırakılır
        }

        


       try {
            if ($anket) {
                // Mevcut anket varsa güncelle
                $anket->update($data);
                $message = 'Anket başarıyla güncellendi!';
            } else {
                // Anket yoksa yeni oluştur
                Survey::create($data);
                $message = 'Anket başarıyla kaydedildi!';
            }
            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            Log::error('Anket kaydetme/güncelleme hatası: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Anket kaydedilirken/güncellenirken bir hata oluştu.'], 500);
        }
    }
}

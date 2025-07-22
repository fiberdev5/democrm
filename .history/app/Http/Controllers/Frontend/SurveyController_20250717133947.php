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
    //Tüm Anketleri listeleme
    public function AllSurveys(Request $request)
    {
        $surveys = Survey::with(['ekleyenUser', 'personelUser', 'servis'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('backend.surveys.all_surveys', compact('surveys'));
    }

    public function SurveyForm($service_id)
    {
        $service = Service::findOrFail($service_id);
        
        // Daha önce bu servis için anket yapılmış mı kontrol et
        $existingSurvey = Survey::where('servisid', $service_id)->first();
        
        // Aktif personelleri getir
        $personels = User::where('durum', 1)
            ->where('user_type', 'personel')
            ->get();
        
        // Aktif bayileri getir
        $dealers = User::where('durum', 1)
            ->where('user_type', 'bayi')
            ->get();

        return view('backend.surveys.survey_form', compact('service', 'existingSurvey', 'personels', 'dealers'));
    }
public function StoreSurvey(Request $request)
    {
        // Validation
        $request->validate([
            'servisid' => 'required|exists:services,id',
            'soru1' => 'required|in:0,1,2',
            'soru1Text' => 'nullable|string|max:500',
            'soru2' => 'required|in:0,1,2',
            'soru2Text' => 'nullable|string|max:500',
            'soru3' => 'required|in:0,1,2',
            'soru3Text' => 'nullable|string|max:500',
            'soru4Text' => 'nullable|string|max:500',
            'soru5' => 'required|in:0,1,2',
            'soru5Text' => 'nullable|string|max:500',
        ]);

        try {
            // Fiyat temizleme
            $fiyat = str_replace(',', '.', $request->soru4Text ?? '0');
            
            // Daha önce anket var mı kontrol et
            $existingSurvey = Survey::where('servisid', $request->servisid)->first();
            
            $data = [
                'servisid' => $request->servisid,
                'soru1' => $request->soru1,
                'soru1Text' => $request->soru1Text,
                'soru2' => $request->soru2,
                'soru2Text' => $request->soru2Text,
                'soru3' => $request->soru3,
                'soru3Text' => $request->soru3Text,
                'soru4' => 0, // Sabit değer
                'soru4Text' => $fiyat,
                'soru5' => $request->soru5,
                'soru5Text' => $request->soru5Text,
                'ekleyen' => Auth::id(),
            ];

            // Bayi mi yoksa merkez personeli mi?
            if ($request->bayi == "0") {
                $data['personel'] = $request->teknisyen;
            } else {
                $data['bayi'] = $request->bayi;
            }

            if ($existingSurvey) {
                // Güncelleme
                $existingSurvey->update($data);
                
                // Log kaydet
                Log::info("ServisID: {$request->servisid}, ServisAnketID: {$existingSurvey->id} Servis Anketi Güncellendi");
                
                return response()->json([
                    'success' => true,
                    'message' => 'Servis Anketi Güncellendi'
                ]);
            } else {
                // Yeni kayıt
                $survey = Survey::create($data);
                
                // Log kaydet
                Log::info("ServisID: {$request->servisid}, ServisAnketID: {$survey->id} Servis Anketi Eklendi");
                
                return response()->json([
                    'success' => true,
                    'message' => 'Servis Anketi Eklendi'
                ]);
            }

        } catch (\Exception $e) {
            Log::error("Servis Anketi Hata: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'HATA! Servis Anketi Kaydedilemedi'
            ], 500);
        }
    }

    /**
     * Anket düzenle
     */
    public function EditSurvey($id)
    {
        $survey = Survey::with(['servis', 'ekleyenUser', 'personelUser'])->findOrFail($id);
        
        $personels = User::where('durum', 1)
            ->where('user_type', 'personel')
            ->get();
        
        $dealers = User::where('durum', 1)
            ->where('user_type', 'bayi')
            ->get();

        return view('backend.surveys.edit_survey', compact('survey', 'personels', 'dealers'));
    }

    /**
     * Anket güncelle
     */
    public function UpdateSurvey(Request $request)
    {
        $request->validate([
            'survey_id' => 'required|exists:surveys,id',
            'soru1' => 'required|in:0,1,2',
            'soru1Text' => 'nullable|string|max:500',
            'soru2' => 'required|in:0,1,2',
            'soru2Text' => 'nullable|string|max:500',
            'soru3' => 'required|in:0,1,2',
            'soru3Text' => 'nullable|string|max:500',
            'soru4Text' => 'nullable|string|max:500',
            'soru5' => 'required|in:0,1,2',
            'soru5Text' => 'nullable|string|max:500',
        ]);

        try {
            $survey = Survey::findOrFail($request->survey_id);
            
            $fiyat = str_replace(',', '.', $request->soru4Text ?? '0');
            
            $data = [
                'soru1' => $request->soru1,
                'soru1Text' => $request->soru1Text,
                'soru2' => $request->soru2,
                'soru2Text' => $request->soru2Text,
                'soru3' => $request->soru3,
                'soru3Text' => $request->soru3Text,
                'soru4' => 0,
                'soru4Text' => $fiyat,
                'soru5' => $request->soru5,
                'soru5Text' => $request->soru5Text,
            ];

            $survey->update($data);
            
            Log::info("ServisID: {$survey->servisid}, ServisAnketID: {$survey->id} Servis Anketi Güncellendi");
            
            return redirect()->route('all.surveys')->with('success', 'Anket başarıyla güncellendi!');

        } catch (\Exception $e) {
            Log::error("Anket Güncelleme Hatası: " . $e->getMessage());
            return back()->with('error', 'Anket güncellenirken hata oluştu!');
        }
    }
    


}

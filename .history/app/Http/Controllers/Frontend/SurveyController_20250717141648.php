<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Survey;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
;

class SurveyController extends Controller
{
    //Tüm Anketleri listeleme
    public function saveSurvey(Request $request, $tenant_id)
    {
        $request->validate([
            'servisid' => 'required|integer',
            'soru1' => 'required|in:0,1,2',
            'soru2' => 'required|in:0,1,2',
            'soru3' => 'required|in:0,1,2',
            'soru4Text' => 'nullable|string',
            'soru5' => 'required|in:0,1,2',
        ]);

        // Daha önce aynı servis için anket var mı kontrol et
        $anket = Survey::where('servisid', $request->servisid);

        if ($request->tip == 'bayi') {
            $anket = $anket->where('bayi', $request->bayi);
        } else {
            $anket = $anket->where('personel', Auth::id()); // Veya Auth ile oturumdaki kullanıcı id'si
        }

        $anket = $anket->first();

        $data = [
            'servisid' => $request->servisid,
            'soru1' => $request->soru1,
            'soru1Text' => $request->input('soru1Text', ''),
            'soru2' => $request->soru2,
            'soru2Text' => $request->input('soru2Text', ''),
            'soru3' => $request->soru3,
            'soru3Text' => $request->input('soru3Text', ''),
            'soru4' => 0,
            'soru4Text' => $request->soru4Text ?? '0',
            'soru5' => $request->soru5,
            'soru5Text' => $request->input('soru5Text', ''),
            'ekleyen' => Auth::id(),
        ];

        if ($request->bayi != "0") {
            $data['bayi'] = $request->bayi;
            $data['personel'] = null;
        } else {
            $data['bayi'] = null;
            $data['personel'] = Auth::id();
        }

        if ($anket) {
            $anket->update($data);
            return back()->with('success', 'Anket güncellendi.');
        } else {
            Survey::create($data);
            return back()->with('success', 'Anket eklendi.');
        }
    }
    
}

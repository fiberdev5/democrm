<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Survey;
use App\Models\ServiceStageAnswer;


class StatisticController extends Controller
{
    public function ServiceStatistics(Request $request, $tenant_id)
    {
         return view('frontend.secure.statistics.service_statistics', [
            'tenant_id' => $tenant_id,
        ]);
        
    }
    public function SurveyStatistics($tenant_id)
    {
        return view('frontend.secure.statistics.survey_statistics', compact('tenant_id'));
    }
    public function SurveyStatisticsData(Request $request, $tenant_id)
    {
        $start = Carbon::createFromFormat('d/m/Y', $request->input('start_date'))->startOfDay();
        $end = Carbon::createFromFormat('d/m/Y', $request->input('end_date'))->endOfDay();

        $anketler = Survey::where('firma_id', $tenant_id)
            ->whereBetween('created_at', [$start, $end])
            ->get();

        $personelServisler = [];

        foreach ($anketler as $anket) {
            $personelId = $anket->personel;
            if (!$personelId) continue;

            if (!isset($personelServisler[$personelId])) {
                $personelServisler[$personelId] = [
                    'adsoyad' => optional(User::find($personelId))->adsoyad ?? 'Bilinmiyor',
                    'servisler' => [],
                ];
            }

            $personelServisler[$personelId]['servisler'][] = $anket->servisid;
        }

        $data = [];

        foreach ($personelServisler as $id => $info) {
            $data[] = [
                'adsoyad' => $info['adsoyad'],
                'toplam_servis' => count($info['servisler']),
                'servis_list' => implode(', ', $info['servisler']),
            ];
        }

        return response()->json(['data' => $data]);
    }
        



   
}

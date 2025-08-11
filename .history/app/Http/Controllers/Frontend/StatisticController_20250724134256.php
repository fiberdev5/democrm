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
        $firma = Tenant::findOrFail($tenant_id);
        
        // Cihaz türlerini getir
        $deviceTypes = DeviceType::where('tenant_id', $tenant_id)->get();
        
        // Varsayılan tarih (dün)
        $defaultDate = Carbon::yesterday()->format('d/m/Y');
        
        return view('frontend.secure.statistics.survey_statistics', compact('firma', 'deviceTypes', 'defaultDate'));
    }

    public function getData(Request $request, $tenant_id)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $deviceType = $request->input('device_type');

        // Tarihleri dönüştür
        $startDate = Carbon::createFromFormat('d/m/Y', $startDate)->startOfDay();
        $endDate = Carbon::createFromFormat('d/m/Y', $endDate)->endOfDay();

        // Ana sorgu - personellere göre anket istatistikleri
        $query = DB::table('surveys as s')
            ->join('tb_user as u', 's.personel', '=', 'u.user_id')
            ->leftJoin('services as sv', 's.servisid', '=', 'sv.id')
            ->leftJoin('device_types as dt', 'sv.device_type_id', '=', 'dt.id')
            ->where('s.firma_id', $tenant_id)
            ->whereBetween('s.created_at', [$startDate, $endDate])
            ->select([
                'u.user_id as personel_id',
                'u.name as personel_name',
                DB::raw('COUNT(s.id) as yapilan_anket_sayisi'),
                DB::raw('GROUP_CONCAT(s.servisid) as servis_ids')
            ])
            ->groupBy('u.user_id', 'u.name');

        // Cihaz türü filtresi
        if ($deviceType) {
            $query->where('dt.id', $deviceType);
        }

        $personelStats = $query->get();

        // Toplam tamamlanan servis sayısı (anket yapılmamış olanlar dahil)
        $completedServicesQuery = DB::table('services')
            ->where('tenant_id', $tenant_id)
            ->where('status', 'completed') // completed status'u varsayıyorum
            ->whereBetween('completed_at', [$startDate, $endDate]);

        if ($deviceType) {
            $completedServicesQuery->where('device_type_id', $deviceType);
        }

        $totalCompletedServices = $completedServicesQuery->count();

        // Toplam yapılan anket sayısı
        $totalSurveys = $personelStats->sum('yapilan_anket_sayisi');

        // DataTables için format
        return DataTables::of($personelStats)
            ->addColumn('action', function ($row) use ($tenant_id, $startDate, $endDate, $deviceType) {
                return '<button class="btn btn-primary btn-sm survey-details" 
                           data-personel-id="' . $row->personel_id . '"
                           data-start-date="' . $startDate->format('Y-m-d') . '"
                           data-end-date="' . $endDate->format('Y-m-d') . '"
                           data-device-type="' . $deviceType . '">
                           <i class="fas fa-eye"></i> Detayları Gör
                        </button>';
            })
            ->addColumn('progress', function ($row) use ($totalCompletedServices) {
                $percentage = $totalCompletedServices > 0 ? round(($row->yapilan_anket_sayisi / $totalCompletedServices) * 100, 2) : 0;
                return '
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: ' . $percentage . '%;" 
                             aria-valuenow="' . $percentage . '" 
                             aria-valuemin="0" aria-valuemax="100">
                            ' . $percentage . '%
                        </div>
                    </div>
                ';
            })
            ->with([
                'totalCompletedServices' => $totalCompletedServices,
                'totalSurveys' => $totalSurveys,
                'completionRate' => $totalCompletedServices > 0 ? round(($totalSurveys / $totalCompletedServices) * 100, 2) : 0
            ])
            ->rawColumns(['action', 'progress'])
            ->make(true);
    }

    public function getPersonelDetails(Request $request, $tenant_id)
    {
        $personelId = $request->input('personel_id');
        $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
        $deviceType = $request->input('device_type');

        // Personelin yaptığı anketlerin detayları
        $query = DB::table('surveys as s')
            ->join('services as sv', 's.servisid', '=', 'sv.id')
            ->leftJoin('customers as c', 'sv.customer_id', '=', 'c.id')
            ->leftJoin('device_types as dt', 'sv.device_type_id', '=', 'dt.id')
            ->where('s.firma_id', $tenant_id)
            ->where('s.personel', $personelId)
            ->whereBetween('s.created_at', [$startDate, $endDate])
            ->select([
                's.id as survey_id',
                's.servisid',
                'sv.service_number',
                'c.name as customer_name',
                'c.phone as customer_phone',
                'dt.name as device_type',
                's.soru1', 's.soru2', 's.soru3', 's.soru5',
                's.created_at as survey_date',
                'sv.completed_at as service_date'
            ]);

        if ($deviceType) {
            $query->where('dt.id', $deviceType);
        }

        $surveys = $query->orderBy('s.created_at', 'desc')->get();

        return DataTables::of($surveys)
            ->addColumn('overall_rating', function ($row) {
                $total = $row->soru1 + $row->soru2 + $row->soru3 + $row->soru5;
                $average = $total / 4;
                
                $stars = '';
                for ($i = 1; $i <= 3; $i++) {
                    if ($average >= $i) {
                        $stars .= '<i class="fas fa-star text-warning"></i>';
                    } elseif ($average >= $i - 0.5) {
                        $stars .= '<i class="fas fa-star-half-alt text-warning"></i>';
                    } else {
                        $stars .= '<i class="far fa-star text-warning"></i>';
                    }
                }
                
                return $stars . ' (' . number_format($average, 1) . ')';
            })
            ->addColumn('rating_badges', function ($row) {
                $badges = '';
                $questions = ['soru1', 'soru2', 'soru3', 'soru5'];
                $labels = ['Hizmet Kalitesi', 'Personel Davranışı', 'Zamanında Teslim', 'Genel Memnuniyet'];
                
                foreach ($questions as $index => $question) {
                    $value = $row->$question;
                    $color = $value == 2 ? 'success' : ($value == 1 ? 'warning' : 'danger');
                    $text = $value == 2 ? 'Memnun' : ($value == 1 ? 'Orta' : 'Memnun Değil');
                    
                    $badges .= '<span class="badge bg-' . $color . ' me-1" title="' . $labels[$index] . '">' . $text . '</span>';
                }
                
                return $badges;
            })
            ->editColumn('survey_date', function ($row) {
                return Carbon::parse($row->survey_date)->format('d/m/Y H:i');
            })
            ->editColumn('service_date', function ($row) {
                return $row->service_date ? Carbon::parse($row->service_date)->format('d/m/Y H:i') : '-';
            })
            ->rawColumns(['overall_rating', 'rating_badges'])
            ->make(true);
    }


    public function getChartData(Request $request, $tenant_id)
    {
        $startDate = Carbon::createFromFormat('d/m/Y', $request->input('start_date'))->startOfDay();
        $endDate = Carbon::createFromFormat('d/m/Y', $request->input('end_date'))->endOfDay();
        $deviceType = $request->input('device_type');

        // Günlük anket sayıları
        $dailyStats = DB::table('surveys')
            ->where('firma_id', $tenant_id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($deviceType, function ($query) use ($deviceType) {
                return $query->join('services', 'surveys.servisid', '=', 'services.id')
                            ->where('services.device_type_id', $deviceType);
            })
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Memnuniyet oranı
        $satisfactionStats = DB::table('surveys')
            ->where('firma_id', $tenant_id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('AVG((soru1 + soru2 + soru3 + soru5) / 4) as average_rating'),
                DB::raw('SUM(CASE WHEN (soru1 + soru2 + soru3 + soru5) / 4 >= 2 THEN 1 ELSE 0 END) as satisfied'),
                DB::raw('COUNT(*) as total')
            )
            ->first();

        return response()->json([
            'daily_stats' => $dailyStats,
            'satisfaction_stats' => $satisfactionStats
        ]);
    }

   
        



   
}

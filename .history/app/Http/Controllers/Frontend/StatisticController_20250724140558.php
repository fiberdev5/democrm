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
    

   
        



   
}

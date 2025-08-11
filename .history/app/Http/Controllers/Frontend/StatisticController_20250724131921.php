<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class StatisticController extends Controller
{
    public function ServiceStatistics(Request $request, $tenant_id)
    {
         return view('frontend.secure.statistics.service_statistics', [
            'tenant_id' => $tenant_id,
        ]);
        
    }

   
}

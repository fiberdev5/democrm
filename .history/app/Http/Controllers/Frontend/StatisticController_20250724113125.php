<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; // Oluşturduğunuz modelleri import edin
use App\Models\ServisKaynak;
use App\Models\Servis;
use App\Models\ServisPlanlama;
use App\Models\CihazMarka;
use App\Models\CihazTur;
use Carbon\Carbon; // Tarih işlemleri için

class StatisticController extends Controller
{
       public function ServiceStatistics(Request $request, $tenant_id)
    {
        
    }

   
}

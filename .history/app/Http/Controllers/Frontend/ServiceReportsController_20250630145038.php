<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;

class ServiceReportsController extends Controller
{
    public function ServiceReports($tenant_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $operators = User::role(['Operatör'])->where('tenant_id', $tenant_id)->get();
        $teknisyen = User::role(['Teknisyen'])->where('tenant_id', $tenant_id)->get();
        $yardimciTeknisyen = User::role(['Teknisyen Yardımcısı'])->where('tenant_id', $tenant_id)->get();
        return view('frontend.secure.all_services.service_reports.reports_modal', compact('firma','operators','teknisyen','yardimciTeknisyen'));
    }
}

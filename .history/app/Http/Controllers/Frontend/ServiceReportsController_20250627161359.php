<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class ServiceReportsController extends Controller
{
    public function ServiceReports($tenant_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        return view('frontend.secure.all_services.service_reports.reports_modal', compact('firma'));
    }
}

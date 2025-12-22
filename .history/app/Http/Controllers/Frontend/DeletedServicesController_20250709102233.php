<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Tenant;
use Illuminate\Http\Request;

class DeletedServicesController extends Controller
{
    public function DeletedServices($tenant_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $deleted_services = Service::where('firma_id', $tenant_id)->where('durum', '0')->get();
        return view('frontend.secure.all_services.deleted_services', compact('firma','deleted_services'));
    }
}

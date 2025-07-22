<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\DeviceBrand;
use App\Models\IncomingCall;
use App\Models\ServiceResource;
use App\Models\Tenant;
use Illuminate\Http\Request;

class IncomingCallsController extends Controller
{
    public function IncomingCalls($tenant_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        if(!$firma) {
            $notification = array(
                'message' => 'Firma bulunamadı',
                'alert-type' => 'danger'
            );
            return redirect()->route('giris')->with($notification);
        }
        $incoming_calls = IncomingCall::where('firma_id', $tenant_id)->get();
        return view('frontend.secure.incoming_calls.all_calls', compact('firma','incoming_calls'));
    }

    public function AddCall($tenant_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $service_resources = ServiceResource::where('firma_id', $tenant_id)->get();
        $device_brands = DeviceBrand::where('firma_id', $tenant_id)->get();
        return view('frontend.secure.incoming_calls.add_call', compact('firma','service_resources','device_brands'));
    }

    public function getPhone(Request $request, $tenant_id)
    {
        $brandId = $request->input('brand_id');

        // Örnek: 'tel' kolonu varsa
        $brand = DeviceBrand::find($brandId); // Model ismini senin projene göre düzenle

        if ($brand) {
            return response()->json(['phone' => $brand->aciklama]);
        } else {
            return response()->json(['phone' => ''], 404);
        }
    }
}

<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\DeviceType;
use App\Models\Tenant;
use Illuminate\Http\Request;

class DeviceTypesController extends Controller
{
    public function DeviceTypes($tenant_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        if(!$firma) {
            $notification = array(
                'message' => 'Firma bulunamadı',
                'alert-type' => 'danger'
            );
            return redirect()->route('giris')->with($notification);
        }
        $device_types = DeviceType::where('firma_id', $firma->id)->orderBy('id', 'desc')->get();
        return view('frontend.secure.device_types.all_device_types', compact('firma','device_types'));
    }

    public function AddDeviceType($tenant_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        return view('frontend.secure.device_types.add_device_type', compact('firma'));
    }

    public function StoreDeviceType($tenant_id, Request $request) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $response = DeviceType::create([
            'firma_id' => $firma->id,
            'cihaz' => $request->cihaz,
            'operatorPrim' => $request->operatorPrim,
            'atolyePrim' => $request->atolyePrim,
        ]);
        $createdDevice = DeviceType::find($response->id);
        return response()->json($createdDevice);
    }

    public function EditDeviceType($tenant_id, $id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $device_id = DeviceType::findOrFail($id);
        return view('frontend.secure.device_types.edit_device_type', compact('firma','device_id'));
    }

    public function UpdateDeviceType($tenant_id, Request $request) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $device_id = $request->id;

        DeviceType::findOrFail($device_id)->update([
            'firma_id' => $firma->id,
            'cihaz' => $request->cihaz,
            'operatorPrim' => $request->operatorPrim,
            'atolyePrim' => $request->atolyePrim,
        ]);
        $updatedDevice = DeviceType::find($device_id);
        return response()->json($updatedDevice);
    }

    public function DeleteDeviceType($tenant_id, $id) {
        $device = DeviceType::find($id);
        if($device) {
            $device->delete();
            return response()->json(['success' => true]);
        }
        else{
            return response()->json(['success' => false, 'message' => 'Cihaz türü bulunamadı.']);
        }
    }
}

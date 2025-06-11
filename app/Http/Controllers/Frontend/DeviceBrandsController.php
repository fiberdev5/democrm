<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\DeviceBrand;
use App\Models\Tenant;
use Illuminate\Http\Request;

class DeviceBrandsController extends Controller
{
    public function DeviceBrands($tenant_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        if(!$firma) {
            $notification = array(
                'message' => 'Firma bulunamadı',
                'alert-type' => 'danger'
            );
            return redirect()->route('giris')->with($notification);
        }
        $device_brands = DeviceBrand::where('firma_id', $firma->id)->orderBy('id', 'desc')->get();
        return view('frontend.secure.device_brands.all_device_brands', compact('firma','device_brands'));
    }

    public function AddDevice($tenant_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        return view('frontend.secure.device_brands.add_device_brand', compact('firma'));
    }

    public function StoreDevice($tenant_id, Request $request) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $response = DeviceBrand::create([
            'firma_id' => $firma->id,
            'marka' => $request->marka,
            'aciklama' => $request->aciklama,
            'servisUcreti' => $request->servisUcreti,
            'operatorPrim' => $request->operatorPrim,
            'atolyePrim' => $request->atolyePrim,
        ]);
        $createdBrand = DeviceBrand::find($response->id);
        return response()->json($createdBrand);
    }
    public function EditDevice($tenant_id, $id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $brand_id = DeviceBrand::findOrFail($id);
        return view('frontend.secure.device_brands.edit_device_brand', compact('brand_id','firma'));
    }

    public function UpdateDevice($tenant_id, Request $request) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $brand_id = $request->id;

        DeviceBrand::findOrFail($brand_id)->update([
            'firma_id' => $firma->id,
            'marka' => $request->marka,
            'aciklama' => $request->aciklama,
            'servisUcreti' => $request->servisUcreti,
            'operatorPrim' => $request->operatorPrim,
            'atolyePrim' => $request->atolyePrim,
        ]);
        $updatedBrand = DeviceBrand::find($brand_id);
        return response()->json($updatedBrand);
    }

    public function DeleteDevice($tenant_id, $id) {
        $brand = DeviceBrand::find($id);
        if($brand) {
            $brand->delete();
            return response()->json(['success' => true]);
        }
        else{
            return response()->json(['success' => false, 'message' => 'Marka bulunamadı.']);
        }
    }
}

<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\WarrantyPeriod;
use Illuminate\Http\Request;

class WarrantyPeriodController extends Controller
{
    public function WarrantyPeriods($tenant_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        if(!$firma) {
            $notification = array(
                'message' => 'Firma bulunamadı',
                'alert-type' => 'danger'
            );
            return redirect()->route('giris')->with($notification);
        }

        $warranties = WarrantyPeriod::where('firma_id', $firma->id)->orderBy('id','desc')->get();
        return view('frontend.secure.warranty_periods.all_warranty', compact('firma', 'warranties'));
    }

    public function AddWarrantyPeriod($tenant_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        return view('frontend.secure.warranty_periods.add_warranty', compact('firma'));
    }

    public function StoreWarrantyPeriod($tenant_id, Request $request) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $response = WarrantyPeriod::create([
            'firma_id' => $firma->id,
            'garanti' => $request->garanti,
        ]);
        $createdWarranty = WarrantyPeriod::find($response->id);
        return response()->json($createdWarranty);
    }

    public function EditWarrantyPeriod($tenant_id, $id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $warranty_id = WarrantyPeriod::findOrFail($id);
        return view('frontend.secure.warranty_periods.edit_warranty', compact('firma','warranty_id'));
    }

    public function UpdateWarrantyPeriod($tenant_id, Request $request) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $warranty_id = $request->id;

        WarrantyPeriod::findOrFail($warranty_id)->update([
            'firma_id' => $firma->id,
            'garanti' => $request->garanti,
        ]);
        $updatedWarranty = WarrantyPeriod::find($warranty_id);
        return response()->json($updatedWarranty);
    }

    public function DeleteWarrantyPeriod($tenant_id, $id) {
        $warranty = WarrantyPeriod::find($id);
        if($warranty) {
            $warranty->delete();
            return response()->json(['success' => true]);
        }
        else {
            return response()->json(['success' => false, 'message' => 'Garanti süresi bulunamadı.']);
        }
    }
}

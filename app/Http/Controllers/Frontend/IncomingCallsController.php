<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\DeviceBrand;
use App\Models\IncomingCall;
use App\Models\Service;
use App\Models\ServiceResource;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        $incoming_calls = IncomingCall::where('firma_id', $tenant_id)->with('servisKaynak','brand','kayit_alan')->orderBy('id','desc')->get();
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

    public function arizaSearch(Request $request,$tenant_id)
    {
        $term = $request->input('q');

        if (!$term) {
            return response()->json([]);
        }

        // Son 10 benzersiz arızayı getir
        $arizalar = IncomingCall::where('firma_id', $tenant_id)->where('ariza', 'like', "%{$term}%")
            ->selectRaw('DISTINCT id, ariza as ariza')
            ->orderBy('ariza')
            ->limit(10)
            ->get();

        return response()->json($arizalar);
    }

    public function StoreCall($tenant_id, Request $request) {
        $firma = Tenant::where('id', $tenant_id)->first();
        if (!$firma) {
            return redirect()->back()->with([
                'message' => 'Firma bulunamadı!',
                'alert-type' => 'danger',
            ]);
        }

        $request->validate([
            'serviceResource' => 'required',
            'deviceBrand' => 'required',
            'cihazAriza' => 'required|string',
        ]);

        $response = IncomingCall::create([
            'firma_id' => $firma->id,
            'servisKaynak' => $request->serviceResource,
            'marka' => $request->deviceBrand,
            'kayitAlan' => auth()->user()->user_id,
            'ariza' => $request->cihazAriza,
        ]);
        // Kullanıcı girişi loglarken
        Log::info( Auth::user()->name . '  yeni çağrı ekledi.', [
            'user_id' => Auth::id(),
            'firma_id' => $firma->id, // Firma ID'sini ekleyin
            'ip_address' => request()->ip(),
        ]);

        $createdCalls = IncomingCall::with(['serviskaynak', 'brand', 'kayit_alan'])->find($response->id);
        $createdCalls->formatted_created_at = $createdCalls->created_at->format('d/m/Y H:i');
        return response()->json($createdCalls);
    }

    public function EditCall($tenant_id, $call_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $call_id = IncomingCall::where('firma_id', $tenant_id)->where('id', $call_id)->first();
        $operators = User::where('tenant_id', $tenant_id)->get();
        $service_resources = ServiceResource::where('firma_id', $tenant_id)->get();
        $device_brands = DeviceBrand::where('firma_id', $tenant_id)->get();
        return view('frontend.secure.incoming_calls.edit_call', compact('firma','call_id','operators','service_resources','device_brands'));
    }

    public function UpdateCall($tenant_id, Request $request) {
        $firma = Tenant::where('id', $tenant_id)->first();
        if(!$firma) {
            $notification = array(
                'message' => 'Firma bulunamadı',
                'alert-type' => 'danger'
            );
            return redirect()->route('giris')->with($notification);
        }

        $call_id = $request->id;
        IncomingCall::findOrFail($call_id)->update([
            'firma_id' => $firma->id,
            'servisKaynak' => $request->serviceResource,
            'marka' => $request->deviceBrand,
            'kayitAlan' => $request->personel,
            'ariza' => $request->cihazAriza,
        ]);

        $updatedCall = IncomingCall::with(['serviskaynak', 'brand', 'kayit_alan'])->find($call_id);
        $updatedCall->formatted_created_at = $updatedCall->created_at->format('d/m/Y H:i');
        return response()->json($updatedCall);
    }

    public function DeleteCall($tenant_id, $id) {
        $incoming_call = IncomingCall::find($id);
        if($incoming_call) {
            $incoming_call->delete();
            return response()->json(['success' => true]);
        }
        else{
            return response()->json(['success' => false, 'message' => 'Çağrı bulunamadı.']);
        }
    }
}

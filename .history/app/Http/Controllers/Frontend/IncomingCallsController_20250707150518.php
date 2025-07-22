<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\IncomingCall;
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
        return view('frontend.secure.incoming_calls.add_call', compact('firma'));
    }
}

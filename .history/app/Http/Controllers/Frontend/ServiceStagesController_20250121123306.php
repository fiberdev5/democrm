<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ServiceStage;
use App\Models\Tenant;
use Illuminate\Http\Request;

class ServiceStagesController extends Controller
{
    public function AllServiceStage($tenant_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        if(!$firma) {
            $notification = array(
                'message' => 'Firma bulunamadı!',
                'alert-type' => 'danger'
            );
            return redirect()->route('giris')->with($notification);
        }
        $stages = ServiceStage::where(function ($query) use ($tenant_id) {
            $query->whereNull('firma_id')->orWhere('firma_id', $tenant_id);
        })->orderBy('id', 'desc')->get();
        return view('frontend.secure.service_stages.service_stages', compact('firma', 'stages'));
    }

    public function AddServiceStage($tenant_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        if(!$firma) {
            $notification = array(
                'message' => 'Firma bulunamadı!',
                'alert-type' => 'danger'
            );
            return redirect()->back()->with($notification);
        }
        $stages = ServiceStage::where(function ($query) use ($tenant_id) {
            $query->whereNull('firma_id')->orWhere('firma_id', $tenant_id);
        })->where('id', '!=', 235)->orderBy('id', 'asc')->get();
        return view('frontend.secure.service_stages.add_stage', compact('firma', 'stages'));
    }

    public function StoreServiceStage(Request $request, $tenant_id) {
        // Firma doğrulama
        $firma = Tenant::where('id', $tenant_id)->first();
        if (!$firma) {
            return redirect()->back()->with([
                'message' => 'Firma bulunamadı!',
                'alert-type' => 'danger',
            ]);
        }

        $request->validate([
            'asama' => 'required|string|max:255',
            'altAsamalar' => 'nullable|array',
            'altAsamalar.*' => 'exists:service_stages,id', 
        ]);

        $response = ServiceStage::create([
            'firma_id' => $firma->id,
            'asama' => $request->asama,
            'altAsamalar' => implode(',', $request->input('altAsamalar', []))
        ]);
        $createdStages = ServiceStage::find($response->id);
        return response()->json($createdStages);
    }

    public function EditServiceStage($tenant_id, $id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        if (!$firma) {
            return redirect()->back()->with([
                'message' => 'Firma bulunamadı!',
                'alert-type' => 'danger',
            ]);
        }
        $stage_id = ServiceStage::findOrFail($id);
        $stages = ServiceStage::where(function ($query) use ($tenant_id) {
            $query->whereNull('firma_id')->orWhere('firma_id', $tenant_id);
        })->where('id', '!=', 235)->orderBy('id', 'asc')->get();

        // Mevcut alt aşamaları al
        $existingStage = ServiceStage::findOrFail($id);
        $selectedAltAsamalar = $existingStage ? explode(',', $existingStage->altAsamalar) : [];
        return view('frontend.secure.service_stages.edit_stages', compact('firma','stage_id','stages','selectedAltAsamalar'));
    }

    public function UpdateServiceStage(Request $request, $tenant_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        if (!$firma) {
            return redirect()->back()->with([
                'message' => 'Firma bulunamadı!',
                'alert-type' => 'danger',
            ]);
        }

        $request->validate([
            'asama' => 'required|string|max:255',
            'altAsamalar' => 'nullable|array',
            'altAsamalar.*' => 'exists:service_stages,id', 
        ]);

        $stage_id = $request->id;
        $stage = ServiceStage::findOrFail($stage_id);
        $stage->asama = $request->asama;
        $stage->altAsamalar = $request->altAsamalar ? implode(',', $request->altAsamalar) : null;
        $stage->save();

        $updatedStages = ServiceStage::find($stage_id);
        return response()->json($updatedStages);
    }

    public function DeleteServiceStage($tenant_id, $id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        if (!$firma) {
            return redirect()->back()->with([
                'message' => 'Firma bulunamadı!',
                'alert-type' => 'danger',
            ]);
        }
        $stages = ServiceStage::find($id);
        if($stages) {
            $stages->delete();
            return response()->json(['success' => true]);
        }
        else{
            return response()->json(['success' => false, 'message' => 'Servis aşaması başarıyla silindi.']);
        }
    }
}

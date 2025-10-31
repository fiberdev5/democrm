<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\StockSupplier;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockSupplierController extends Controller
{
    public function AllStockSupplier($tenant_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        if(!$firma) {
            $notification = array(
                'message' => 'Firma bulunamadı',
                'alert-type' => 'danger'
            );
            return redirect()->route('giris')->with($notification);
        }
        $suppliers = StockSupplier::where('firma_id', $firma->id)->orderBy('id','desc')->get();
        return view('frontend.secure.stock_suppliers.all_suppliers', compact('firma', 'suppliers'));
    }

    public function AddStockSupplier($tenant_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        return view('frontend.secure.stock_suppliers.add_supplier', compact('firma'));
    }

    public function StoreStockSupplier($tenant_id, Request $request) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $userId = Auth::user()->user_id;
        $response = StockSupplier::create([
            'firma_id' => $firma->id,
            'kid' => $userId,
            'tedarikci' => $request->tedarikci,
        ]);
        $createdSupplier = StockSupplier::find($response->id);
        return response()->json($createdSupplier);
    }

    public function EditStockSupplier($tenant_id, $id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $supplier_id = StockSupplier::findOrFail($id);
        return view('frontend.secure.stock_suppliers.edit_supplier', compact('firma', 'supplier_id'));
    }

    public function UpdateStockSupplier($tenant_id, Request $request) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $supplier_id = $request->id;
         $userId = Auth::user()->user_id;
        StockSupplier::findOrFail($supplier_id)->update([
            'firma_id' => $firma->id,
            'kid' => $userId,
            'tedarikci' => $request->tedarikci,
        ]);
        $updatedSupplier = StockSupplier::find($supplier_id);
        return response()->json($updatedSupplier);
    }

    public function DeleteStockSupplier($tenant_id, $id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        if (!$firma) {
            return redirect()->back()->with([
                'message' => 'Firma bulunamadı!',
                'alert-type' => 'danger',
            ]);
        }
        $stock_suppliers = StockSupplier::find($id);
        if($stock_suppliers) {
            $stock_suppliers->delete();
            return response()->json(['success' => true]);
        }
        else{
            return response()->json(['success' => false, 'message' => 'Stok tedarikçisi başarıyla silindi.']);
        }
    }
}

<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Marka;
use Illuminate\Support\Facades\Storage;

class MarkaController extends Controller
{
    // Markaları listele
    public function index()
    {
        $markalar = Marka::orderBy('marka', 'ASC')->get();
        return view('frontend.secure.super_admin.ariza_kodlari.markalar.index', compact('markalar'));
    }

    // Marka ekleme formu (Modal için)
    public function create()
    {
        return view('markalar.create');
    }

    // Marka kaydet
    public function store(Request $request)
    {
        $request->validate([
            'marka' => 'required|string|max:500',
            'resim' => 'nullable|image|mimes:jpeg,jpg,png,svg|max:2048'
        ]);
        
        $data = [
            'marka' => trim($request->marka)
        ];
        
        // Resim yükleme
        if ($request->hasFile('resim')) {
            $resim = $request->file('resim');
            $resimAdi = bin2hex(random_bytes(10)) . '.' . $resim->getClientOriginalExtension();
            $resim->move(public_path('upload'), $resimAdi);
            $data['resimyol'] = $resimAdi;
        }
        
        Marka::create($data);
        
        return response()->json(['message' => 'Marka Eklendi']);
    }
     // Marka düzenleme formu (Modal için)
    public function edit($id)
    {
        $markaSec = Marka::findOrFail($id);
        return view('markalar.edit', compact('markaSec'));
    }

     // Marka güncelle
    public function update(Request $request, $id)
    {
        $request->validate([
            'marka' => 'required|string|max:500',
            'resim' => 'nullable|image|mimes:jpeg,jpg,png,svg|max:2048'
        ]);
        
        $marka = Marka::findOrFail($id);
        
        $data = [
            'marka' => trim($request->marka)
        ];
        
        // Resim güncelleme
        if ($request->hasFile('resim')) {
            // Eski resmi sil
            if ($marka->resimyol && file_exists(public_path('upload/' . $marka->resimyol))) {
                unlink(public_path('upload/' . $marka->resimyol));
            }
            
            $resim = $request->file('resim');
            $resimAdi = bin2hex(random_bytes(10)) . '.' . $resim->getClientOriginalExtension();
            $resim->move(public_path('upload'), $resimAdi);
            $data['resimyol'] = $resimAdi;
        }
        
        $marka->update($data);
        
        return response()->json(['message' => 'Marka Güncellendi']);
    }

    // Marka sil
    public function destroy($id)
    {
        $marka = Marka::findOrFail($id);
        
        // Resmi sil
        if ($marka->resimyol && file_exists(public_path('upload/' . $marka->resimyol))) {
            unlink(public_path('upload/' . $marka->resimyol));
        }
        
        $marka->delete();
        
        return response()->json(['message' => 'Marka Silindi']);
    }
    
    

}

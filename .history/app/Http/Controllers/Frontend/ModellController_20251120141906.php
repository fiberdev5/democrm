<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Modell;
use App\Models\Marka;

class ModellController extends Controller
{
    // Modelleri listele
    public function index($marka_id)
    {
        $modeller = Modell::where('mid', $marka_id)->get();
        $markaSec = Marka::findOrFail($marka_id);
        return view('modeller.index', compact('modeller', 'markaSec'));
    }

    // Model ekleme formu
    public function create($marka_id)
    {
        $markaSec = Marka::findOrFail($marka_id);
        return view('modeller.create', compact('markaSec', 'marka_id'));
    }

     // Model kaydet
    public function store(Request $request)
    {
        $request->validate([
            'mid' => 'required|exists:markalar,id',
            'model' => 'required|string|max:500',
            'resim' => 'nullable|image|mimes:jpeg,jpg,png,svg|max:2048'
        ]);
        
        $data = [
            'mid' => $request->mid,
            'model' => trim($request->model)
        ];
        
        // Resim yükleme
        if ($request->hasFile('resim')) {
            $resim = $request->file('resim');
            $resimAdi = bin2hex(random_bytes(10)) . '.' . $resim->getClientOriginalExtension();
            $resim->move(public_path('upload'), $resimAdi);
            $data['resimyol'] = $resimAdi;
        }
        
        Modell::create($data);
        
        return response()->json(['message' => 'Model Eklendi']);
    }
    
}

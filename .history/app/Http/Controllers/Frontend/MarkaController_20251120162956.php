<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Marka;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables; // Yajra kütüphanesini kullandığınızı varsayıyorum

class MarkaController extends Controller
{
  public function index(Request $request)
{
    // Eğer istek AJAX ise (Tablo yükleniyorsa) JSON verisi döndür
    if ($request->ajax()) {
        $markalar = Marka::orderBy('marka', 'ASC')->get();
        
        $data = $markalar->map(function($marka){
            // 1. Marka Sütunu HTML'i (Resim + İsim)
            $resimHtml = '';
            if($marka->resimyol && file_exists(public_path('upload/'.$marka->resimyol))){
                $resimHtml = '<img src="'.asset('upload/'.$marka->resimyol).'" style="width:40px;height:40px;object-fit:contain;border:1px solid #eee;border-radius:4px;margin-right:10px" />';
            }
            $markaIcerik = '<div class="d-flex align-items-center">'.$resimHtml.'<span style="font-weight:600; font-size:14px;">'.$marka->marka.'</span></div>';

            // 2. İşlemler Sütunu HTML'i
            $modellerUrl = route('super.admin.modeller.index', $marka->id);
            // Not: route parametrelerini kendi yapınıza göre kontrol edin
            $arizaUrl = route('super.admin.kodlar.index', ['marka_id' => $marka->id, 'model_id' => 0]);
            
            $butonlar = '<div class="d-flex gap-2">';
            $butonlar .= '<a href="'.$modellerUrl.'" class="btn btn-info btn-sm text-white"><i class="fas fa-list"></i> Modeller</a>';
            $butonlar .= '<a href="'.$arizaUrl.'" class="btn btn-warning btn-sm text-white"><i class="fas fa-exclamation-triangle"></i> Kodlar</a>';
            $butonlar .= '<a href="javascript:void(0);" data-id="'.$marka->id.'" class="btn btn-primary btn-sm markaDuzenleBtn"><i class="fas fa-edit"></i> Düzenle</a>';
            $butonlar .= '<a href="javascript:void(0);" data-id="'.$marka->id.'" class="btn btn-danger btn-sm markaSil"><i class="fas fa-trash"></i> Sil</a>';
            $butonlar .= '</div>';

            return [
                'id' => $marka->id,
                'marka_html' => $markaIcerik,
                'action_html' => $butonlar
            ];
        });

        return response()->json(['data' => $data]);
    }

    // Normal sayfa açılışı
    return view('frontend.secure.super_admin.ariza_kodlari.markalar.index');
}

    // Marka ekleme formu (Modal için)
    public function create()
    {
        return view('frontend.secure.super_admin.ariza_kodlari.markalar.create');
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
        
        if ($request->hasFile('resim')) {
            $resim = $request->file('resim');
            $resimAdi = bin2hex(random_bytes(10)) . '.' . $resim->getClientOriginalExtension();
            $resim->move(public_path('upload'), $resimAdi);
            $data['resimyol'] = $resimAdi;
        }
        
        Marka::create($data);
        
        return response()->json(['message' => 'Marka Başarıyla Eklendi']);
    }

     // Marka düzenleme formu (Modal için)
    public function edit($id)
    {
        $markaSec = Marka::findOrFail($id);
        return view('frontend.secure.super_admin.ariza_kodlari.markalar.edit', compact('markaSec'));
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
        
        if ($request->hasFile('resim')) {
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
        
        if ($marka->resimyol && file_exists(public_path('upload/' . $marka->resimyol))) {
            unlink(public_path('upload/' . $marka->resimyol));
        }
        
        $marka->delete();
        
        return response()->json(['message' => 'Marka Silindi']);
    }
}
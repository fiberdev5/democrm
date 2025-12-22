<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Marka;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables; // Yajra kütüphanesini kullandığınızı varsayıyorum

class MarkaController extends Controller
{
    // Markaları listele (Hem sayfa hem AJAX verisi)
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Marka::orderBy('marka', 'ASC');
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('marka_info', function($row){
                    // Resim ve İsim Sütunu HTML'i
                    $resimHtml = '';
                    if($row->resimyol){
                        $imgUrl = asset('upload/'.$row->resimyol);
                        $resimHtml = '<img style="width:40px;height:40px;object-fit:contain;border:1px solid #eee;border-radius:4px;margin-right:10px" src="'.$imgUrl.'" />';
                    }
                    return '<div class="d-flex align-items-center">'.$resimHtml.'<span style="font-weight:600; font-size:14px;">'.$row->marka.'</span></div>';
                })
                ->addColumn('action', function($row){
                    // Butonlar HTML'i
                    $modellerUrl = route('super.admin.modeller.index', $row->id);
                    $arizaUrl = route('super.admin.kodlar.index', ['marka_id' => $row->id, 'model_id' => 0]);
                    
                    $btn = '<div class="d-flex gap-1 justify-content-center">';
                    $btn .= '<a href="'.$modellerUrl.'" class="btn btn-info btn-sm" title="Modeller"><i class="fas fa-list"></i> Modeller</a>';
                    $btn .= '<a href="'.$arizaUrl.'" class="btn btn-warning btn-sm" title="Arıza Kodları"><i class="fas fa-exclamation-triangle"></i> Kodlar</a>';
                    $btn .= '<a href="javascript:void(0);" data-id="'.$row->id.'" class="btn btn-primary btn-sm markaDuzenleBtn" title="Düzenle"><i class="fas fa-edit"></i></a>';
                    $btn .= '<a href="javascript:void(0);" data-id="'.$row->id.'" class="btn btn-danger btn-sm markaSil" title="Sil"><i class="fas fa-trash"></i></a>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['marka_info', 'action'])
                ->make(true);
        }

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
<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Offer;
use App\Models\OfferProduct;
use App\Models\Tenant;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class OfferController extends Controller
{
    public function AllOffer(Request $request, $tenant_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        if (!$firma) {
            return redirect()->route('giris')->with([
                'message' => 'Firma bulunamadı.',
                'alert-type' => 'danger',
            ]);
        }

        $offers = Offer::where('firma_id', $firma->id)->get();
        if ($request->ajax()) {           
            $data = Offer::with('musteri');

            $data->when($request->filled('from_date') && $request->filled('to_date'), function ($query) use ($request) {
                return $query->whereDate('offers.created_at', '>=', $request->from_date)
                             ->whereDate('offers.created_at', '<=', $request->to_date);
            });

            if ($request->filled('teklifDurumu')) {
                if ($request->get('teklifDurumu') == '0') {
                    $data->where('durum', '0');
                } elseif ($request->get('teklifDurumu') == '1') {
                    $data->where('durum', '1');
                }
            }

            // Sıralama işlemi
            if ($request->has('order')) {
                $order = $request->get('order')[0];
                $columns = $request->get('columns');
                $orderColumn = $columns[$order['column']]['data'];
                $orderDir = $order['dir'];
                
                if($orderColumn == 'mid'){
                    $data->leftJoin('customers', 'offers.musteri_id', '=', 'customers.id')
                    ->addSelect(['offers.*', 'offers.musteri_id as musAdi'])
                    ->orderBy('customers.adSoyad', $orderDir);
                }
                else {
                    $data->where('firma_id', $firma->id)->orderBy($orderColumn, $orderDir);
                }
            } else {
                $data->where('firma_id',$firma->id)->orderBy('offers.created_at','desc');
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('id', function($row){
                    return '<a class="t-link editOffer address idWrap" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editOfferModal">'.$row->id.'</a>';
                })
                ->addColumn('created_at', function($row){
                    $sontarih = Carbon::parse($row->created_at)->format('d/m/Y');
                    return '<a class="t-link editOffer address" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editOfferModal"><div class="mobileTitle">Tarih:</div>'.$sontarih.'</a>';
                })
                ->addColumn('mid', function($row){
                    return '<a class="t-link editOffer" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editOfferModal"><div class="mobileTitle">Müşteri:</div>'.$row->musteri?->adSoyad.'</div></a>';
                })
                ->addColumn('genelToplam', function($row){
                  return '<a class="t-link editOffer address" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editOfferModal"><div class="mobileTitle">G. Toplam:</div>'.$row->genelToplam.' '.$row->currency?->baslik.'</div></a>';
                })
                ->addColumn('teklifDurumu', function($row){
                  if($row->durum == 0){
                    return '<a class="t-link editOffer" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editOfferModal"><div class="mobileTitle">Durum:</div><div style="color: #216dfd; display: inline-block;font-weight:700;">Beklemede</div></div></a>';
                  }else if($row->durum == 1){
                    return '<a class="t-link editOffer" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editOfferModal"><div class="mobileTitle">Durum:</div><div style="color: green; display: inline-block;font-weight:700;">Onaylandı</div></div></a>';
                  }
                })
                ->addColumn('action', function($row){
                    $deleteUrl = route('delete.offer',[$row->firma_id, $row->id]);
                    $showUrl = route('offer.pdf', [$row->firma_id, $row->id]);
                    $fileButton = '<a href="'.$showUrl.'" target="_blank" class="btn btn-primary btn-sm mobilBtn mbuton1" title="Teklifi görüntüle"><i class="far fa-eye"></i></a>';
                    $editButton = '';
                    $deleteButton = '';

                        $editButton = '<a href="javascript:void(0);" data-bs-id="'.$row->id.'" class="btn btn-warning btn-sm editOffer address mobilBtn mbuton1" data-bs-toggle="modal" data-bs-target="#editOfferModal" title="Düzenle"><i class="fas fa-edit"></i></a>';
                        $deleteButton = '<a href="'.$deleteUrl.'" class="btn btn-danger btn-sm mobilBtn" id="delete" title="Sil"><i class="fas fa-trash-alt"></i></a>';
                    return $fileButton. ' '.$editButton. ' '.$deleteButton;
                })
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->where(function($w) use($request){
                           $search = $request->get('search');
                           $w->where('id', 'LIKE', "%$search%")
                           ->orWhereHas('musteri', function($q) use($search) {
                            $q->where('adSoyad', 'LIKE', "%$search%");
                         });
                       });
                    }

                })
                ->rawColumns(['id','created_at','mid','baslik1','genelToplam','teklifDurumu','action'])
                ->make(true);                      
        }
        return view('frontend.secure.offers.all_offers', compact('offers','firma'));
    }

    //musteriyi search ederken ajax'ın çalıştığı 
    public function searchMusteri(Request $request)
    {   $user = Auth::user();
        $tenant_id = $user->tenant_id;
        $searchField = $request->input('musteriGetir');
        $musteriler = Customer::where('firma_id', $tenant_id)
                        ->where('adSoyad', 'like', '%' . $searchField . '%')
                        ->where('durum',1)
                        ->get();
        return response()->json($musteriler);
    }

    public function AddOffer($tenant_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $musteriler = Customer::where('firma_id',$tenant_id)->with(['country','state'])->orderBy('adSoyad', 'ASC')->get();
        return view('frontend.secure.offers.add_offer', compact('firma','musteriler'));
    }

    public function StoreOffer(Request $request, $tenant_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        if(!$firma) {
            $notification = array(
                'message' => 'Firma bulunamadı',
                'alert-type' => 'danger'
            );
            return redirect()->route('giris')->with($notification);
        }
        $firma_id = $firma->id;
        $staff_id = Auth::user()->user_id;
        $offer = Offer::create([
            'firma_id' => $firma_id,
            'personel_id' => $staff_id,
            'musteri_id' => $request->musteri,
            'toplam' => $request->toplam,
            'kdv' => $request->kdvTutar,
            'kdvTutar' => $request->kdv,
            'genelToplam' => $request->genelToplam,
            'toplamYazi' => $request->toplamYazi,
            'dovizKuru' => $request->dovizKuru,
            'aciklamalar' => $request->aciklamalar,
            'baslik1' => $request->baslik1,
            'baslik2' => $request->baslik2,
            'durum' => $request->durum,
            'created_at' => $request->kayitTarihi,
        ]);

        $offer_id = $offer->id;
        if($offer) {
            $aciklama = $request->aciklama;
            $miktar = $request->miktar;
            $fiyat = $request->fiyat;
            $tutar = $request->tutar;

            foreach($aciklama as $key => $val) {
                if(!empty($val)) {
                    OfferProduct::create([
                        'firma_id' => $firma_id,
                        'teklifId' => $offer_id,
                        'urun' => $val,
                        'miktar' => $miktar[$key],
                        'fiyat' => $fiyat[$key],
                        'tutar' => $tutar[$key],
                    ]);
                }
            }
        }

        $notification = array(
            'message' => 'Teklif başarıyla oluşturuldu.',
            'alert-type' => 'success'
        );
        return response()->json(['success', $notification]);
    }

    public function EditOffer($tenant_id, $id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $offer_id = Offer::findOrFail($id);
        $m_id = $offer_id->musteri_id;
        $p_id = $offer_id->personel_id;
        $musteriler = Customer::where('firma_id', $firma->id)->with(['country','state'])->orderBy('adSoyad', 'ASC')->get();
        $musteri = Customer::where('firma_id', $firma->id)->where('id', $m_id)->first();
        $personel = User::where('tenant_id', $firma->id)->where('user_id', $p_id)->first();
        $offer_products = OfferProduct::where('firma_id', $firma->id)->where('teklifId',$id)->get();
        return view('frontend.secure.offers.edit_offer', compact('firma','musteriler','musteri','personel','offer_products','offer_id'));
    }

    public function UpdateOffer(Request $request, $tenant_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        if(!$firma) {
            $notification = array(
                'message' => 'Firma bulunamadı',
                'alert-type' => 'danger'
            );
            return redirect()->route('giris')->with($notification);
        }

        $offer_id = $request->id;
        $p_id = Auth::user()->user_id;
        Offer::findOrFail($offer_id)->update([
            'musteri_id' => $request->musteri,
            'personel_id' => $p_id,
            'toplam' => $request->toplam,
            'kdvTutar' => $request->kdv,
            'kdv' => $request->kdvTutar,
            'genelToplam' => $request->genelToplam,
            'toplamYazi' => $request->toplamYazi,
            'dovizKuru' => $request->dovizKuru,
            'aciklamalar' => $request->aciklamalar,
            'baslik1' => $request->baslik1,
            'baslik2' => $request->baslik2,
            'durum' => $request->durum,
            'created_at' => $request->kayitTarihi,
        ]);

        $oldProducts = OfferProduct::where('teklifId', $offer_id)->get();
        foreach($oldProducts as $product){
            OfferProduct::findOrFail($product->id)->delete();
        }

        $aciklama = $request->aciklama;
        $miktar = $request->miktar;
        $fiyat = $request->fiyat;
        $tutar = $request->tutar;

        foreach($aciklama as $key => $val){
            if(!empty($val)){
                OfferProduct::insert([
                    'firma_id' =>$firma->id,
                    'teklifId' => $offer_id,
                    'urun' => $val,
                    'miktar' => $miktar[$key],
                    'fiyat' => $fiyat[$key],
                    'tutar' => $tutar[$key],
                ]);
            }
        }
        $notification = array(
            'message' => 'Teklif içeriği başarıyla güncellendi.',
            'alert-type' => 'success'
        );

        return response()->json(['success', $notification]);

    }

    public function DeleteOffer($tenant_id, $id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        if(!$firma) {
            $notification = array(
                'message' => 'Firma bulunamadı',
                'alert-type' => 'danger'
            );
            return redirect()->route('giris')->with($notification);
        }

        $offer = Offer::where('firma_id', $firma->id)->findOrFail($id);
        if(!empty($offer)) {
            $offer->delete();
            OfferProduct::where('teklifId', $id)->delete();

            $notification = array(
                'message' => 'Teklif başarıyla silindi.',
                'alert-type' => 'success'
            );
            return redirect()->back()->with($notification);
        }
        $notification = array(
            'message' => 'Teklif silinemedi.',
            'alert-type' => 'danger'
        );
        return redirect()->back()->with($notification);
    }

    public function OffertoPdf($tenant_id, $id) {
        $offer_id = Offer::findOrFail($id); 
        $m_id = $offer_id->musteri_id;
        $offer_products = OfferProduct::where('teklifId', $id)->get();
        $data = [
            'firma' => Tenant::where('id', $tenant_id)->with('ils','ilces')->first(),
            'customer' => Customer::where('firma_id', $tenant_id)->where('id', $m_id)->first(),
            'offers' => $offer_id,
            'offer_products' => $offer_products,
        ];

        $pdf = Pdf::loadView('frontend.secure.offers.offer_pdf',$data)->setOption('isHtml5ParserEnabled', true)
        ->setOption('isPhpEnabled', true)
        ->setOption('isHtml5ParserEnabled', true);
        return $pdf->stream();
    }
}

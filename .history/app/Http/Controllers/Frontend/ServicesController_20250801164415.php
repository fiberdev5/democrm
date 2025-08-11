<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CashTransaction;
use App\Models\Customer;
use App\Models\DeviceBrand;
use App\Models\DeviceType;
use App\Models\EmergencyService;
use App\Models\Il;
use App\Models\Ilce;
use App\Models\PaymentMethod;
use App\Models\PaymentType;
use App\Models\Service;
use App\Models\ServiceFormSetting;
use App\Models\ServiceMoneyAction;
use App\Models\ServiceOptNote;
use App\Models\ServicePhoto;
use App\Models\ServicePlanning;
use App\Models\ServiceResource;
use App\Models\ServiceStage;
use App\Models\ServiceStageAnswer;
use App\Models\StageQuestion;
use App\Models\Stock;
use App\Models\StockAction;
use App\Models\Tenant;
use App\Models\User;
use App\Models\WarrantyPeriod;
use App\Models\ServiceReceiptNote;
use App\Models\Offer;
use App\Models\PersonelStock;
use App\Models\ServiceTime;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;


class ServicesController extends Controller
{
    public function AllServices($tenant_id, Request $request) {
        $firma = Tenant::where('id', $tenant_id)->first();
        if(!$firma) {
            $notification = array(
                'message' => 'Firma bulunamadı',
                'alert-type' => 'danger'
            );
            return redirect()->route('giris')->with($notification);
        }
        $services = Service::where('firma_id', $firma->id)->get();
        $device_brands = DeviceBrand::where('firma_id', $firma->id)->orderBy('marka', 'asc')->get();
        $device_types = DeviceType::where('firma_id', $firma->id)->orderBy('cihaz', 'asc')->get();
        $service_stages = ServiceStage::where(function ($query) use ($tenant_id) {
            $query->whereNull('firma_id')->orWhere('firma_id', $tenant_id);
        })->orderBy('asama', 'asc')->get();
        $service_resources = ServiceResource::where('firma_id', $tenant_id)->orderBy('kaynak', 'asc')->get();
        $states = Il::orderBy('name', 'ASC')->get();
        //Operatör istatistikleri 
        $operator_id = $request->operator_id;
        $opeator_istatistik_tarih1 = $request->opeator_istatistik_tarih1;
        $opeator_istatistik_tarih2 = $request->opeator_istatistik_tarih2;
        //Durum istatistikleri
        $state_id = $request->state_id;
        $state_istatistik_tarih1 = $request->state_istatistik_tarih1;
        $state_istatistik_tarih2 = $request->state_istatistik_tarih2;
        //Aşama İstatistikleri
        $stage_id = $request->stage_id;
        $stage_istatistik_tarih1 = $request->stage_istatistik_tarih1;
        $stage_istatistik_tarih2 = $request->stage_istatistik_tarih2;
        //İlçe İstatistikleri
        $ilceArama= $request->ilceArama;
        $ilce_istatistik_tarih1 = $request->ilce_istatistik_tarih1;
        $ilce_istatistik_tarih2 = $request->ilce_istatistik_tarih2;
        //Anket İstatistikleri
        $personel_id= $request->personel_id;
        $personel_istatistik_tarih1 = $request->personel_istatistik_tarih1;
        $personel_istatistik_tarih2 = $request->personel_istatistik_tarih2;
    
        if ($request->ajax()) {           
            $data = Service::with(['musteri', 'markaCihaz', 'turCihaz', 'asamalar','cevaplar'])
              ->where('firma_id', $firma->id)->where('durum', 1);

              $user = auth()->user();

            if ($user->can('Kendi Servislerini Görebilir')) {
                // Sadece kendi kayıtlarını görebilir
                $servisIDleri = $this->getYetkiliServisIDleri($user, $firma->id);
                $data->whereIn('id', $servisIDleri);
            } 

            $data->when($request->filled('from_date') && $request->filled('to_date'), function ($query) use ($request) {
                return $query->whereDate('services.created_at', '>=', $request->from_date)
                             ->whereDate('services.created_at', '<=', $request->to_date);
            });
        
            //Operatör istatistiklerine göre filtre
            if ($request->filled('operator_id')) {
                $data->where('kayitAlan', $request->operator_id);
            }
            //Operatör istatistikleri tarih aralığı filtresi
            if ($request->filled('opeator_istatistik_tarih1') && $request->filled('opeator_istatistik_tarih2')) {
                $from = Carbon::createFromFormat('Y-m-d', $request->opeator_istatistik_tarih1)->startOfDay();
                $to = Carbon::createFromFormat('Y-m-d', $request->opeator_istatistik_tarih2)->endOfDay();
                $data->whereBetween('created_at', [$from, $to]);
            }
            //Durum istatistiklerine göre filtre
            if ($request->filled('state_id')) {
                $data->where('servisDurum', $request->state_id);
            }
            //Durum istatistikleri tarih aralığı filtresi
            if ($request->filled('state_istatistik_tarih1') && $request->filled('state_istatistik_tarih2')) {
                $from = Carbon::createFromFormat('Y-m-d', $request->state_istatistik_tarih1)->startOfDay();
                $to = Carbon::createFromFormat('Y-m-d', $request->state_istatistik_tarih2)->endOfDay();
                $data->whereBetween('created_at', [$from, $to]);
            }
            if ($request->get('device_brands')) {
                $data->where('cihazMarka', $request->get('device_brands'));
            }
            //Aşama istatistiklerine göre filtre (stage_id)
            if ($request->filled('stage_id')) {
                $stageId = $request->stage_id;
                
                $data->whereExists(function ($query) use ($stageId, $request) {
                $query->select(DB::raw(1))
                      ->from('service_plannings')
                      ->whereColumn('service_plannings.servisid', 'services.id')
                      ->where('service_plannings.gidenIslem', $stageId);
            // Eğer aşama istatistik tarih aralığı varsa, onu da service_plannings tablosunda kontrol et
            if ($request->filled('stage_istatistik_tarih1') && $request->filled('stage_istatistik_tarih2')) {
                $from = Carbon::createFromFormat('Y-m-d', $request->stage_istatistik_tarih1)->startOfDay();
                $to = Carbon::createFromFormat('Y-m-d', $request->stage_istatistik_tarih2)->endOfDay();
                $query->whereBetween('service_plannings.created_at', [$from, $to]);
                        }
                    });
            }
            //Aşama istatistikleri tarih aralığı filtresi (sadece tarih filtresi için)
            if ($request->filled('stage_istatistik_tarih1') && $request->filled('stage_istatistik_tarih2') && !$request->filled('stage_id')) {
                    $from = Carbon::createFromFormat('Y-m-d', $request->stage_istatistik_tarih1)->startOfDay();
                    $to = Carbon::createFromFormat('Y-m-d', $request->stage_istatistik_tarih2)->endOfDay();
                    $data->whereBetween('created_at', [$from, $to]);
            }
            //İlçe istatistiklerine göre filtre
            $ilceId = DB::table('ilces')->where('ilceName', $request->ilceArama)->value('id');
            if ($ilceId) {
                $data->whereHas('musteri', function ($query) use ($ilceId) {
                    $query->where('ilce', $ilceId);
                });
            }
            //İlçe istatistikleri tarih aralığı filtresi
            if ($request->filled('ilce_istatistik_tarih1') && $request->filled('ilce_istatistik_tarih2')) {
                $from = Carbon::createFromFormat('Y-m-d', $request->ilce_istatistik_tarih1)->startOfDay();
                $to = Carbon::createFromFormat('Y-m-d', $request->ilce_istatistik_tarih2)->endOfDay();
                $data->whereBetween('created_at', [$from, $to]);
            }

            //////////////////////Anket istatistikleri filtreleri///////////////////////////////////////////////
            // Personel filtresi
            if ($request->filled('personel_id')) {
                $data->whereHas('surveys', function ($query) use ($request) {
                    $query->where('ekleyen', $request->personel_id);
                });
            }
            // Anket yapılan servisler
            if ($request->has('anket_yapilan') && $request->anket_yapilan == '1') {
                $data->whereHas('surveys');
            }
            // Tarih aralığı filtresi
                if ($request->has('personel_istatistik_tarih1') && $request->has('personel_istatistik_tarih2')) {
                    $startDate = $request->personel_istatistik_tarih1 . ' 00:00:00';
                    $endDate = $request->personel_istatistik_tarih2 . ' 23:59:59';
                    $data->whereBetween('created_at', [$startDate, $endDate]);
            }
            //////////////////////Anket istatistikleri filtreleri///////////////////////////////////////////////
            if ($request->get('device_brands')) {
                $data->where('cihazMarka', $request->get('device_brands'));
            }
           if ($request->get('device_types') || $request->filled('deviceType')) {
                // İki parametreden biri doluysa filtreyi uygula
                $data->where('cihazTur', $request->get('device_types') ?? $request->get('deviceType'));
            }
    
            if ($request->get('stages')) {
                $data->where('servisDurum', $request->get('stages'));
            }
    
            if ($request->get('service_resource')) {
                $data->where('servisKaynak', $request->get('service_resource'));
            }

            if ($request->get('il')) {
                $data->whereRelation('musteri', 'il', $request->get('il'));
            }

            if ($request->get('ilce')) {
                $data->whereRelation('musteri', 'ilce', $request->get('ilce'));
            }

            /** Raporlama filtreleri */
            if ($request->filterType && $request->filters) {
                $filters = is_array($request->filters) ? $request->filters : json_decode($request->filters, true);
                

                switch ($request->filterType) {
                    case 'operator':
                        $tarih1 = Carbon::parse($filters['operator_tarih1'])->startOfDay();
                        $tarih2 = Carbon::parse($filters['operator_tarih2'])->endOfDay();
                        if (!empty($filters['operator_pers']) && $filters['operator_pers'] != '0') {
                            $data->where('kayitAlan', $filters['operator_pers']);
                        }

                        if (!empty($tarih1) && !empty($tarih2)) {
                            $data->whereBetween('created_at', [$tarih1, $tarih2]);
                        }
                    break; 
                    case 'teknisyen':
                        
                         // ❶ Teknisyen
                        if (!empty($filters['teknisyen']) && $filters['teknisyen'] != '0') {
                            $data->whereHas('cevaplar', function ($q) use ($filters) {
                                $q->where('soruid', 45)
                                ->where('cevap',  $filters['teknisyen']);
                            });
                        }

                        // ❷ Araç
                        if (!empty($filters['tekArac']) && $filters['tekArac'] != '0') {
                            $data->whereHas('cevaplar', function ($q) use ($filters) {
                                $q->where('soruid', 47)
                                ->where('cevap',  $filters['tekArac']);
                            });
                        }

                        // ❸ Tarih  ‑‑ cevaplardaki formatla eşleştirin
                        if (!empty($filters['tekTarih'])) {
                            // Örneğin cevaplar dd/mm/YYYY tutuluyorsa:
                            $date = Carbon::parse($filters['tekTarih'])->format('Y-m-d');
                            $data->whereHas('cevaplar', function ($q) use ($date) {
                                $q->where('soruid', 48)
                                ->where('cevap',  $date);
                            });
                            // eğer cevap kolonu gerçek DATE ise →  $q->whereDate('cevap', $filters['tekTarih']);
                        }
                    break;
                    case 'urunSatis':
                        $t1 = Carbon::parse($filters['tarih1'])->startOfDay();
                        $t2 = Carbon::parse($filters['tarih2'])->endOfDay();

                        // "Cihaz Satışı Yapıldı" aşaması = Örneğin asama_id 300
                        $data->whereHas('plans', function ($query) use ($t1, $t2) {
                            $query->where('gidenIslem', 256) // kendi cihaz satış aşama ID'nizi yazın
                                ->whereBetween('created_at', [$t1, $t2]);
                        });
                    break;
                    case 'bayiArama':
                        $t1 = Carbon::parse($filters['bayi_tarih1'])->startOfDay();
                        $t2 = Carbon::parse($filters['bayi_tarih2'])->endOfDay();

                        // "Cihaz Satışı Yapıldı" aşaması = Örneğin asama_id 300
                        $data->whereHas('plans', function ($query) use ($t1, $t2) {
                            $query->where('gidenIslem', 264) // kendi cihaz satış aşama ID'nizi yazın
                                ->whereBetween('created_at', [$t1, $t2]);
                        });
                    break;
                    case 'acilArama':
                        $t1 = Carbon::parse($filters['acil_tarih1'])->startOfDay();
                        $t2 = Carbon::parse($filters['acil_tarih2'])->endOfDay();

                        $data->where('acil', 1)
                        ->whereBetween('created_at', [$t1, $t2]);
                    break;
                    case 'yapilananketler':
                    $tarih1 = Carbon::parse($filters['yapilananket_tarih1'])->startOfDay();
                    $tarih2 = Carbon::parse($filters['yapilananket_tarih2'])->endOfDay();

                    $data->whereHas('surveys', function($query) use ($tarih1, $tarih2, $filters) {
                        $query->whereBetween('created_at', [$tarih1, $tarih2]);
                        //survey->personel
                        if (!empty($filters['anketi_yapilan_personel']) && $filters['anketi_yapilan_personel'] != '0') {
                            $query->where('personel', $filters['anketi_yapilan_personel']);
                        }
                        //survey->ekleyen
                        if (!empty($filters['anketi_yapan_personel']) && $filters['anketi_yapan_personel'] != '0') {
                            $query->where('ekleyen', $filters['anketi_yapan_personel']);
                        }
                        //survey->bayi
                        if (!empty($filters['bayiler']) && $filters['bayiler'] != '0') {
                            $query->where('bayi', $filters['bayiler']);
                        }
                    });
                    break;
                    case 'yapilmayanAnketler':
                        $tarih1 = Carbon::parse($filters['yapilmayananket_tarih1'])->startOfDay();
                        $tarih2 = Carbon::parse($filters['yapilmayananket_tarih2'])->endOfDay();
                        $data->whereBetween('created_at', [$tarih1, $tarih2]);

                        //Personel(soruid 45)
                        if (!empty($filters['yapilmayan_personel']) && $filters['yapilmayan_personel'] != '0') {
                            $personelIdToFilter = $filters['yapilmayan_personel'];
                            $data->whereHas('cevaplar', function ($q) use ($personelIdToFilter) {
                                $q->where('soruid', 45)
                                ->where('cevap', (string) $personelIdToFilter); 
                            });
                        }
                        //Bayi(soruid 3)
                        if (!empty($filters['bayiler']) && $filters['bayiler'] != '0') {
                            $bayiIdToFilter = $filters['bayiler'];
                            $data->whereHas('cevaplar', function ($q) use ($bayiIdToFilter) {
                                $q->where('soruid', 3)
                                ->where('cevap', (string) $bayiIdToFilter);
                            });
                        }           
                        // Anketleri olmayan servisleri getir
                        $data->doesntHave('surveys');
                    break;
                }
            }

    
            // Sıralama işlemi
            if ($request->has('order')) {
                $order = $request->get('order')[0];
                $columns = $request->get('columns');
                $orderColumn = $columns[$order['column']]['data'];
                $orderDir = $order['dir'];

                
                $data->orderBy($orderColumn, $orderDir);
                
            } else {
                $data->orderBy('id', 'desc');
            }
    
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('id', function($row){
                    return '<a class="t-link serBilgiDuzenle address idWrap" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editServiceDescModal">'.$row->id.'</a>';
                })
                ->addColumn('created_at', function($row){
                    $sontarih = Carbon::parse($row->created_at)->format('d/m/Y');
                    return '<a class="t-link serBilgiDuzenle address" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editServiceDescModal"><span class="mobileTitle">Tarih:</span>'.$sontarih.'</a>';
                })
                ->addColumn('m_adi', function($row){ 
                    return '<a class="t-link serBilgiDuzenle address" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editServiceDescModal"><span class="mobileTitle">Müşteri:</span><strong>'.$row->musteri->adSoyad.'</strong><br><div style="font-size:12px;">'.$row->musteri->tel1.' - '.$row->musteri->tel2.'</div><div style="font-size:12px;">'.$row->musteri->adres.'</div></a>';
                    
                })
                ->addColumn('cihaz', function($row){
                    return '<a class="t-link serBilgiDuzenle" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editServiceDescModal"><span class="mobileTitle">Cihaz:</span><strong>'.$row->markaCihaz->marka.' - '.$row->turCihaz->cihaz.'</strong></a>';
                })
                ->addColumn('asama_id', function($row){                   
                    $asamaHTML = '<a class="t-link serBilgiDuzenle address" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editServiceDescModal">';
                    $asamaHTML .= '<span class="mobileTitle">S. Durumu:</span><strong>'.$row->asamalar?->asama.'</strong><br>';
                    
                    if ($row->asamalar?->id == 235) {
                        $asamaHTML .= '<div style="font-size:12px;">('.$row->users?->name.')</div>';
                    }

                    // Sadece servisDurumu'na ait cevapları getir
                    $cevaplar = ServiceStageAnswer::where('planid', $row->planDurum)
                        ->where('servisid', $row->id)
                        ->get();
                        
                    // Aşama cevaplarını detaylı göster
                    if ($cevaplar->count()) {
                        $asamaHTML .= '<div class="spanBox" style="font-size:11px;margin-top:5px;">';
                        foreach ($cevaplar as $cevap) {
                            $soru = StageQuestion::find($cevap->soruid); // performans için eager load edilebilir
                            if (!$soru) continue;

                            if ($soru->cevapTuru == '[Fiyat]' || $soru->cevapTuru == '[Teklif]') {
                                $asamaHTML .= '<span>'.$soru->soru.': '.$cevap->cevap.' TL</span>';
                            } elseif (str_contains($soru->cevapTuru, 'Grup')) {
                                $personel = User::find($cevap->cevap);
                                $asamaHTML .= '<span>'.$soru->soru.': '.$personel?->name.'</span>';
                            } elseif (str_contains($soru->cevapTuru, '[Arac]')) {
                                $arac = Car::find($cevap->cevap);
                                $asamaHTML .= '<span>'.$soru->soru.': '.$arac?->arac.'</span>';
                            }elseif ($soru->cevapTuru == '[Tarih]') {
                                $tarih = Carbon::parse($cevap->cevap)->format('d/m/Y');
                                $asamaHTML .= '<span>'.$soru->soru.': '.$tarih.'</span>';
                            }elseif ($soru->cevapTuru == '[Bayi]') {
                                $bayi = User::find($cevap->cevap);
                                $asamaHTML .= '<span>'.$soru->soru.': '.$bayi?->name.'</span>';
                            }elseif ($soru->cevapTuru == '[Parca]') {
                                $parcalar = explode(", ", $cevap->cevap);
                                $parcaHTML = '';
                                foreach ($parcalar as $parca) {
                                    [$parcaId, $adet] = explode("---", $parca);
                                    $stok = Stock::find($parcaId);
                                    $parcaHTML .= $stok?->urunAdi." (".$adet."), ";
                                }
                                $asamaHTML .= '<span>'.$soru->soru.': '.rtrim($parcaHTML, ", ").'</span>';
                            } else {
                                $asamaHTML .= '<span>'.$soru->soru.': '.$cevap->cevap.'</span>';
                            }
                        }
                        $asamaHTML .= '</div>';
                    }

                    $asamaHTML .= '</a>';

                    return $asamaHTML;
                })
                ->addColumn('action', function($row){
                    $deleteUrl = route('delete.service', [$row->firma_id,$row->id]);
                    $editButton = '';
                    $deleteButton = '';
                        $editButton = '<a href="javascript:void(0);" data-bs-id="'.$row->id.'" class="btn btn-warning btn-sm serBilgiDuzenle mobilBtn mbuton1" data-bs-toggle="modal" data-bs-target="#editServiceDescModal" title="Düzenle" ><i class="fas fa-edit"></i><span> Düzenle</span></a>';
                        $deleteButton = '<a href="'.$deleteUrl.'" class="btn btn-danger btn-sm mobilBtn" id="delete" title="Sil" ><i class="fas fa-trash-alt"></i> <span> Sil</span></a>';
                    return $editButton. '  '.$deleteButton;
                })
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->where(function($w) use($request){
                           $search = $request->get('search');
                           $w->where('id', 'LIKE', "%$search%")
                           ->orWhereHas('musteri', function($q) use($search) {
                            $q->where('adSoyad', 'LIKE', "%$search%")
                            ->orWhere('tel1', 'LIKE', "%$search%");
                         });
                       });
                    }

                })
                ->rawColumns(['id', 'created_at', 'm_adi', 'cihaz', 'asama_id', 'action'])
                ->make(true);                      
        }
    
        return view('frontend.secure.all_services.services', compact('services', 'device_brands', 'device_types', 'service_stages','firma', 'service_resources','states','operator_id','opeator_istatistik_tarih1','opeator_istatistik_tarih2'));
    }

    //Sadece kendine atanan servisleri gören kişilerin koşullarını kontrol eden fonksiyon.(üstteki AllServices fonksiyonunda kullandım)
    private function getYetkiliServisIDleri($user, $tenant_id)
    {
        $yetkiliServisIDler = [];

        // Kullanıcının cevap verdiği servisleri, her servis için en yüksek planid ile al
        $servisler = ServiceStageAnswer::with(['question','plan'])->where('firma_id', $tenant_id)
            ->where('cevap', $user->user_id)
            ->get();

        foreach ($servisler as $servisRow) {
            $soru = StageQuestion::find($servisRow->soruid);
            
            if (!$soru) continue;

            // Grup kontrolü
            if (str_contains($soru->cevapTuru, 'Grup')) {
                $plan = ServicePlanning::find($servisRow->planid);
                if (!$plan) continue;

                $tarihDurum = $plan->tarihDurum > 0 ? "1" : "0";

                if ($tarihDurum == "1") {
                    // Tarih durumu var
                    $tarihCevap = ServiceStageAnswer::where('planid', $servisRow->planid)
                        ->where('cevapText', '[Tarih]')
                        ->first();

                    if ($tarihCevap) {
                         $bugun = now()->format('Y-m-d');
                        $tarih = $tarihCevap->cevap;

                        if ($bugun == $tarih) {
                            // Zaman kontrolü - PHP kodundaki $kid değişkenini kullanıcının kid'i ile değiştiriyoruz
                            // $kid değişkeni PHP kodunda nereden geldiğini bilmediğimiz için user_id kullanıyoruz
                            $zaman = ServiceTime::where('firma_id', $tenant_id)->first();
                            
                            if ($zaman) {
                                $ilksaat = strtotime(date("H:i"));
                                $sonsaat = strtotime(str_replace('.', ':', $zaman->zaman));
                                
                                if ($ilksaat >= $sonsaat) {
                                    // Depocu değil ise devam et
                                    if (!$user->hasRole('Depocu')) {
                                        $yetkiliServisIDler[] = $tarihCevap->servisid;
                                    }
                                }
                            }
                        }

                        // Depocu için özel durum
                        if ($user->hasRole('Depocu')) {
                            $servis = Service::find($tarihCevap->servisid);
                            if ($servis && in_array($servis->servisDurum, ['257', '263'])) {
                                $yetkiliServisIDler[] = $tarihCevap->servisid;
                            }
                        }
                    }
                } else {
                    // Tarih durumu yok
                    $servis = Service::find($servisRow->servisid);
                    if ($servis && $servis->planDurum == $servisRow->planid) {
                        $yetkiliServisIDler[] = $servisRow->servisid;
                    }

                    // Planid içerisindeki son eklenen aşama kontrolü
                    if (!$user->hasRole('Depocu')) {
                        $planSec = ServicePlanning::find($servis->planDurum);
                        if ($planSec && $planSec->pid == $user->user_id) {
                            $planTarihBugun = date("Y-m-d");
                            $planTarih = explode(" ", $planSec->tarih);
                            
                            if ($planTarih[0] == $planTarihBugun) {
                                $yetkiliServisIDler[] = $servisRow->servisid;
                            }
                        }
                    }
                }
            }

            // Bayi kontrolü
            if (str_contains($soru->cevapTuru, 'Bayi')) {
                $plan = ServicePlanning::find($servisRow->planid);
                if (!$plan) continue;

                $tarihDurum = $plan->tarihDurum > 0 ? "1" : "0";

                if ($tarihDurum == "1") {
                    // Tarih durumu var - Grup ile aynı logic
                    $tarihCevap = ServiceStageAnswer::where('planid', $servisRow->planid)
                        ->where('cevapText', '[Tarih]')
                        ->first();

                    if ($tarihCevap) {
                        $bugun = now()->format('Y-m-d');
                        $tarih = $tarihCevap->cevap;

                        if ($bugun == $tarih) {
                            // Zaman kontrolü - PHP kodundaki $kid değişkenini kullanıcının kid'i ile değiştiriyoruz
                            $zaman = ServiceTime::where('firma_id', $tenant_id)->first();
                            
                            if ($zaman) {
                               $ilksaat = strtotime(date("H:i"));
                               $sonsaat = strtotime(str_replace('.', ':', $zaman->zaman));
                                
                                if ($ilksaat >= $sonsaat) {
                                    if (!$user->hasRole('Depocu')) {
                                        $yetkiliServisIDler[] = $tarihCevap->servisid;
                                    }
                                }
                            }
                        }

                        // Depocu için özel durum
                        if ($user->hasRole('Depocu')) {
                            $servis = Service::find($tarihCevap->servisid);
                            if ($servis && in_array($servis->servisDurum, ['257', '263'])) {
                                $yetkiliServisIDler[] = $tarihCevap->servisid;
                            }
                        }
                    }
                } else {
                    // Tarih durumu yok
                    $servis = Service::find($servisRow->servisid);
                    if ($servis && $servis->planDurum == $servisRow->planid) {
                        $yetkiliServisIDler[] = $servisRow->servisid;
                    }

                    // Planid içerisindeki son eklenen aşama kontrolü
                    if (!$user->hasRole('Depocu')) {
                        $planSec = ServicePlanning::find($servis->planDurum);
                        if ($planSec && $planSec->pid == $user->user_id) {
                            $planTarihBugun = date("Y-m-d");
                            $planTarih = explode(" ", $planSec->tarih);
                            
                            if ($planTarih[0] == $planTarihBugun) {
                                $yetkiliServisIDler[] = $servisRow->servisid;
                            }
                        }
                    }
                }
            }
        }

        return array_unique($yetkiliServisIDler);
    }

    public function searchCustomer(Request $request, $firma_id)
    {
        try {
            $searchTerm = $request->input('musteriGetir');
            
            // Minimum karakter kontrolü
            if (strlen($searchTerm) < 2) {
                return response()->json([]);
            }
            
            // Müşteri arama - firma_id'ye göre filtreleme
            $customers = Customer::where('firma_id', $firma_id)
                ->where(function($query) use ($searchTerm) {
                    $query->where('adSoyad', 'LIKE', '%' . $searchTerm . '%')
                          ->orWhere('tel1', 'LIKE', '%' . $searchTerm . '%')
                          ->orWhereRaw('REPLACE(tel1, "-", "") LIKE ?', ['%' . str_replace(['-', '(', ')', ' '], '', $searchTerm) . '%'])
                          ->orWhere('tcNo', 'LIKE', '%' . $searchTerm . '%')
                          ->orWhere('vergiNo', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select([
                    'id',
                    'adSoyad', 
                    'tel1', 
                    'tel2', 
                    'adres', 
                    'il', 
                    'ilce', 
                    'musteriTipi',
                    'tcNo',
                    'vergiNo',
                    'vergiDairesi'
                ])
                ->orderBy('adSoyad')
                ->limit(10) // Maksimum 10 sonuç
                ->get();
            
            // İl ve ilçe isimlerini çek (eğer ID olarak saklanıyorsa)
            $results = $customers->map(function($customer) {
                // Eğer il ve ilçe ID olarak saklanıyorsa, isimlerini çek
                $il = DB::table('ils')->where('id', $customer->il)->value('name') ?? $customer->il;
                $ilce = DB::table('ilces')->where('id', $customer->ilce)->value('ilceName') ?? $customer->ilce;
                
                return [
                    'id' => $customer->id,
                    'adSoyad' => $customer->adSoyad,
                    'tel1' => $customer->tel1,
                    'tel2' => $customer->tel2,
                    'adres' => $customer->adres,
                    'il' => $il,
                    'ilce' => $ilce,
                    'musteriTipi' => $customer->musteriTipi,
                    'tcNo' => $customer->tcNo,
                    'vergiNo' => $customer->vergiNo,
                    'vergiDairesi' => $customer->vergiDairesi
                ];
            });
            
            return response()->json($results);
            
        } catch (\Exception $e) {
            
            return response()->json(['error' => 'Arama sırasında hata oluştu'], 500);
        }
    }

    public function AddService($tenant_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $service_resources = ServiceResource::where('firma_id', $firma->id)->orderBy('kaynak', 'asc')->get();
        $iller = DB::table('ils')->orderBy('name', 'ASC')->get();
        $device_brands = DeviceBrand::where('firma_id', $firma->id)->orderBy('marka', 'asc')->get();
        $device_types = DeviceType::where('firma_id', $firma->id)->orderBy('cihaz', 'asc')->get();
        $warranty_periods = WarrantyPeriod::orderBy('garanti', 'asc')->get();

        return view('frontend.secure.all_services.add_service', compact('firma','service_resources','iller','device_brands','device_types','warranty_periods'));
    }

    public function StoreService($tenant_id, Request $request) {        
        $firma = Tenant::where('id', $tenant_id)->first();
        
        if (!$firma) {
            return redirect()->route('giris');
        }
        
        $raw1 = preg_replace('/\D/', '', $request->tel1); // Sadece rakamlar
        $tel1 = preg_replace('/(\d{3})(\d{3})(\d{4})/', '$1 $2 $3', $raw1);

        $raw2 = preg_replace('/\D/', '', $request->tel2); // Sadece rakamlar
        $tel2 = preg_replace('/(\d{3})(\d{3})(\d{4})/', '$1 $2 $3', $raw2);

        $musteriData = [
            'firma_id' => $firma->id,
            'personel_id' => auth()->id(),
            'musteriTipi' => $request->musteriTipi,
            'adSoyad' => $request->adSoyad,
            'tel1' => $tel1,
            'tel2' => $tel2,
            'il' => $request->il,
            'ilce' => $request->ilce,
            'adres' => $request->adres,
            'tcNo' => $request->tcNo,
            'vergiNo' => $request->vergiNo,
            'vergiDairesi' => $request->vergiDairesi,
            'created_at' => now(),
        ];
        $eskiMusteriId = $request->eskiMusteriId;

        if (empty($eskiMusteriId)) {
            // Yeni müşteri - önce aynı bilgilerle müşteri var mı kontrol et
            $musteriKontrol = Customer::where('firma_id', $firma->id)->where([
                'musteriTipi' => $request->musteriTipi,
                'adSoyad' => $request->adSoyad,
                'tel1' => $request->tel1,
                'tel2' => $request->tel2,
                'il' => $request->il,
                'ilce' => $request->ilce,
                'adres' => $request->adres,
                'tcNo' => $request->tcNo,
                'vergiNo' => $request->vergiNo,
                'vergiDairesi' => $request->vergiDairesi,
            ])->first();

            if ($musteriKontrol) {
                $musteriId = $musteriKontrol->id;
            } else {
                $musteri = Customer::create($musteriData);
                $musteriId = $musteri->id;
            }
        } else {
            // Eski müşteri seçilmiş - sadece güncelle, yeni müşteri oluşturma
            $mevcutMusteri = Customer::find($eskiMusteriId);
            if ($mevcutMusteri) {
                $mevcutMusteri->update($musteriData);
                $musteriId = $eskiMusteriId;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Seçilen müşteri bulunamadı.'
                ], 404);
            }
        }

        if ($musteriId) {

            // İlk servis durumunu al
            $servisDurum = ServiceStage::where('ilkServis', '1')->first();

            $servisData = [
                'firma_id' => $firma->id,
                'kid' => auth()->id(),
                'bid' => '0',
                'pid' => '0',
                'musteri_id' => $musteriId,
                'kayitTarihi' => now(),
                'servisKaynak' => $request->input('servisReso'),
                'musaitTarih' => $request->kayitTarihi,
                'musaitSaat1' => $request->input('musaitSaat1'),
                'musaitSaat2' => $request->input('musaitSaat2'),
                'cihazMarka' => $request->input('cihazMarka'),
                'cihazTur' => $request->input('cihazTur'),
                'cihazModel' => $request->input('cihazModel'),
                'cihazSeriNo' => $request->input('cihazSeriNo'),
                'cihazAriza' => $request->cihazAriza,
                'operatorNotu' => $request->input('opNot'),
                'garantiSuresi' => $request->input('cihazGaranti'),
                'servisDurum' => $servisDurum->id ?? null,
                'kayitAlan' => auth()->id(),
                'planDurum' => '0',
                'pbDurum' => 0,
                'durum' => 1,
                'acil' => 0,
            ];

            // Aynı servis kontrolü
            $servisKontrol = Service::orderBy('id', 'desc')->first();
            
            $ayniServis = false;
            if ($servisKontrol && 
                $servisKontrol->musteriid == $musteriId && 
                $servisKontrol->kayitTarihi->format('Y-m-d H:i:s') == now()->format('Y-m-d H:i:s') && 
                $servisKontrol->pid == auth()->id()) {
                $ayniServis = true;
            }

            if (!$ayniServis) {
                $servis = Service::create($servisData);
                $servisId = $servis->id;

                // Acil servis kontrolü
                // if ($request->input('acil') == "1") {
                //     $acilData = [
                //         'pid' => auth()->id(),
                //         'servisid' => $servisId,
                //     ];

                //     $acilServis = EmergencyService::create($acilData);
                    
                //     Service::where('id', $servisId)->update([
                //         'acil' => $acilServis->id
                //     ]);
                // }

                if ($servisId) {
                    
                    // SMS gönderimi için kod buraya eklenebilir
                    // ...

                    $notification = array(
                        'message' => 'Servis Başarıyla Eklendi',
                        'alert-type' => 'success'
                    );

                    return redirect()->back()->with($notification);
                } else {
                    $notification = array(
                        'message' => 'Servis Kayıt Edilemedi',
                        'alert-type' => 'warning'
                    );
                }
            } else {
                $notification = array(
                'message' => 'Aynı Servis Zaten Mevcut',
                'alert-type' => 'warning'
            );
            }
        } else {
            $notification = array(
                'message' => 'Servis Kayıt Edilemedi',
                'alert-type' => 'warning'
            );
        }
    }

    public function EditService($tenant_id, $id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $service_id = Service::where('firma_id', $firma->id)->findOrFail($id);
        
        return view('frontend.secure.all_services.edit_service', compact('firma', 'service_id'));
    }

    //servis-bilgileri kısmı(Tüm servisleri görmeye izni olanlar)
    public function TumServiceDesc($tenant_id, $id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $service_id = Service::where('firma_id', $firma->id)->findOrFail($id);
        $service_resources = ServiceResource::where('firma_id', $firma->id)->orderBy('kaynak', 'asc')->get();
        $iller = DB::table('ils')->orderBy('name', 'ASC')->get();
        $device_brands = DeviceBrand::where('firma_id', $firma->id)->orderBy('marka', 'asc')->get();
        $device_types = DeviceType::where('firma_id', $firma->id)->orderBy('cihaz', 'asc')->get();
        $warranty_periods = WarrantyPeriod::orderBy('garanti', 'asc')->get();
        
        $altAsamaIDs = [];
        $altAsamalar = collect(); // boş koleksiyon

        if (!empty($service_id->asamalar->altAsamalar)) {
            // Virgül ile ayrılmış ID listesini array'e dönüştür
            $altAsamaIDs = explode(',', $service_id->asamalar->altAsamalar);
            $altAsamalar = ServiceStage::whereIn('id', $altAsamaIDs)->orderBy('asama')->get();
        }
        return view('frontend.secure.all_services.service_information', compact('firma', 'service_id', 'service_resources', 'iller', 'device_brands', 'device_types', 'warranty_periods','altAsamalar'));
    }

    //servis-bilgileri2 kısmı(Sadece kendine atanan servisleri görebildiği ekran)
    public function KendiServiceDesc($tenant_id, $id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $servis = Service::where('firma_id', $firma->id)->with(['asamalar'])->findOrFail($id);

        if (!$servis) {
            return response()->json(['error' => 'Servis bulunamadı'], 404);
        }

        // Yetkilendirme kontrolü - Kullanıcının firma ID'si ile servisin firma ID'si eşleşmeli
        if ($servis->firma_id != $firma->id) {
            return response()->json(['error' => 'Bu servise erişim yetkiniz yok'], 403);
        }

        // Alt aşamaları getir
        $altAsamalar = [];
        if ($servis->asamalar && $servis->asamalar->altAsamalar) {
            $altAsamaIds = explode(',', $servis->asamalar->altAsamalar);
            $altAsamalar = ServiceStage::whereIn('id', $altAsamaIds)
                ->orderBy('asama')
                ->get();
        }

        // Konsinye cihaz bilgisi
        // $konsinyeCihaz = null;
        // if ($servis->konsinye) {
        //     $konsinyeCihaz = Stock::find($servis->konsinye);
        // }

        // Eski işlemler
        $eskiIslemler = ServicePlanning::where('servisid', $id)
            ->with('user')
            ->orderBy('id', 'desc')
            ->get();

        $eskiIslemler2 = ServicePlanning::where('servisid', $id)
            ->orderBy('id', 'desc')
            ->first();

        // Müsait tarih formatla
        $musaitTarih = explode('-', $servis->musaitTarih);

        // Garanti hesaplama
        $garantiBitis = null;
        $kalanGun = -1;
        
        if ($servis->warranty && $servis->warranty->garanti) {
            $garantiBitis = \Carbon\Carbon::parse($servis->created_at)
                ->addMonths($servis->warranty->garanti)
                ->format('Y-m-d');
                
            $garantiBitisFormatted = explode('-', $garantiBitis);
            
            // Kalan gün hesaplama
            $kalanGun = Carbon::now()->diffInDays(Carbon::parse($garantiBitis), false);
        }

        // Acil işlem kontrol
        $acilIslem = null;
        if ($servis->acil != 0) {
            $acilIslem = Service::where('id', $id)
                ->first();
        }

        // Servis notları
        $servisNotlari = ServiceOptNote::where('servisid', $id)
            ->with('user')
            ->orderBy('id', 'desc')
            ->get();
        return view('frontend.secure.all_services.own_service_information', compact('firma','servis',
            'altAsamalar',
            'eskiIslemler',
            'eskiIslemler2',
            'musaitTarih',
            'garantiBitis',
            'kalanGun',
            'acilIslem',
            'servisNotlari',));
    }

    //Servis Bilgileri düzenleme modalında yapılacak işlemler selectini seçince çıkan formun olduğu sayfayı gösteren fonksiyon
    public function ServiceStageQuestionShow($tenant_id ,$asamaid, $serviceid) {
        $firma = Tenant::where('id', $tenant_id)->first();
        
        $stage_id = ServiceStage::findOrFail($asamaid);
        $service_id = Service::where('firma_id', $firma->id)->findOrFail($serviceid);
        $stage_questions = StageQuestion::where('asama', $asamaid)->orderBy('sira', 'asc')->get();

         // İşlem türünü belirle (request'ten gelen islem parametresi)
        $islem = $stage_id;
        
        // Servis bilgilerini kontrol et
        $servisSec = Service::where('id', $serviceid)->first();

        // Normal servis işlemleri 
            
        if (auth()->user()->hasRole('Patron')) {
            // Patron: tüm personel stoklarını görür
            $stoklar = PersonelStock::where('firma_id', $firma->id)
                        ->orderBy('id', 'asc')
                        ->get();
        } else {  
                    $stoklar = PersonelStock::where('firma_id', $firma->id)
                                ->where('pid', auth()->user()->user_id)
                                ->orderBy('id', 'asc')
                                ->get();
        }
            //Personele ait toplam stok            
            $toplamPersonelStokAdedi = $stoklar->sum('adet');

        //Konsinye Cihaz Stok İşlemleri
        $konsinyeKategoriId = 3;
        $konsinyeCihazlar = Stock::where('firma_id', $firma->id)
            ->where('urunKategori', $konsinyeKategoriId)
            ->get();

        $toplamKonsinyeCihazAdedi = 0;

        foreach ($konsinyeCihazlar as $device) {
            // Giriş işlemleri (1: Alış, 4: Müşteriden İade)
            $girisAdet = StockAction::where('stokId', $device->id)
                ->whereIn('islem', [1, 4])
                ->sum('adet');
            
            // Çıkış işlemleri (2: Serviste Kullanım)
            $cikisAdet = StockAction::where('stokId', $device->id)
                ->where('islem', 2)
                ->sum('adet');
            
            // Güncel stok miktarını hesapla
            $device->current_stock_quantity = $girisAdet - $cikisAdet;
            
            // Sadece pozitif stokları toplama dahil et
            if ($device->current_stock_quantity > 0) {
                $toplamKonsinyeCihazAdedi += $device->current_stock_quantity;
            }
        }

        // Sadece stoku olan cihazları filtrele
        $konsinyeCihazlar = $konsinyeCihazlar->filter(function($device) {
            return $device->current_stock_quantity > 0;
        });

                        
        // Personel listesi al (grup kontrolü için)
            $personeller = User::where('tenant_id', $firma->id)
                            ->where('status', '1')
                            ->orderBy('name', 'asc')
                            ->get();
                            
        // Araç listesi al
            $araclar = Car::where('firma_id', $firma->id)
                        ->where('durum', '1')
                        ->orderBy('id', 'asc')
                        ->get();
                        
         // Bayi listesi al
            // $bayiler = DB::table('personeller')
            //             ->where('grup', '258')
            //             ->where('firma_id', $firma->id)
            //             ->where('durum', '1')
            //             ->orderBy('adsoyad', 'asc')
            //             ->get();
            
         return view('frontend.secure.all_services.service_stage_questions_show', 
                    compact('stage_questions', 'stage_id', 'service_id', 'firma', 'islem', 'personeller', 
                    'araclar','stoklar','toplamPersonelStokAdedi','konsinyeCihazlar','toplamKonsinyeCihazAdedi'));

        
    }

    //Servis Alt Aşamalarını veritabanına kaydederken yapılan işlemleri içeren fonksiyonlar
    public function SaveServicePlan(Request $request, $tenant_id) {
        $firma = Tenant::where('id', $tenant_id)->first();

        try {
            
            $servisId = $request->input('servis');
            $gelenIslem = json_decode($request->input('gelenIslem'), true);
            $gidenIslem = $request->input('gidenIslem');

            // Servis durumu kontrolü
            $servisDurum = Service::where('firma_id',$firma->id)->where('id', $servisId)->first();
            if (!$servisDurum || $servisDurum->firma_id != $tenant_id) {
                return response()->json(['status' => 'error', 'message' => '-1']);
            }

            // Stok kontrolü
            $stokHatasiVar = $this->stokKontrolEt($request, $gelenIslem);
            if ($stokHatasiVar) {
                return response()->json(['status' => 'error', 'message' => $stokHatasiVar]);
            }

            
            $kid = Auth()->user()->user_id;
            // Servis planlama kaydı
            $planData = [
                'firma_id' => $tenant_id,
                'kid' => $kid,
                'pid' => $kid,
                'servisid' => $servisId,
                'gelenIslem' => $gelenIslem['id'],
                'gidenIslem' => $gidenIslem,
                'tarihDurum' => 0,
                'tarihKontrol' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ];

            $planId = ServicePlanning::insertGetId($planData);

            if ($planId) {
                // Log kaydı
                

                // Servis durumunu güncelle
                Service::where('id', $servisId)
                    ->update([
                        'servisDurum' => $gidenIslem,
                        'planDurum' => $planId,
                        'updated_at' => now()
                    ]);
                $servis = Service::find($servisId);

                // Soru cevaplarını işle
                $this->soruCevaplariniIsle($request, $servisId, $planId, $tenant_id, $gelenIslem);

                // Özel durumları işle
                $this->ozelDurumlariIsle($request, $servisId, $planId, $tenant_id, $gidenIslem, $servisDurum);
                // Eğer yeni aşama 'Konsinye Cihaz Geri Alındı' ise ve kategori 3 olanları al
                if ($gidenIslem == 272) {
                    $konsinyeCihazlar = StockAction::where('servisid', $servisId)
                        ->where('planId', '<>', $planId)
                        ->where('islem', 2)
                        ->where('firma_id', $tenant_id)
                        ->get();

                    foreach ($konsinyeCihazlar as $cihaz) {
                        $stok = Stock::find($cihaz->stokId);
                        if ($stok && $stok->urunKategori == 3) {
                            $this->geriAlConsignmentDevice($cihaz->stokId, $cihaz->adet, $servisId, $planId, $tenant_id);
                        }
                    }
                }


                // Tarih durumu kontrolü
                $this->tarihDurumuKontrolEt($tenant_id);
                $currentStage = $servis->servisDurum; // veya hangi field'dan alıyorsanız
        
                // Bu aşamaya ait alt aşamaları getir. Servis planı eklendikten sonra altAsamalar kısmını güncellemek için bunu yaptım.
                $altAsamaIDs = explode(',', $servis->asamalar->altAsamalar);
                $altAsamalar = ServiceStage::whereIn('id', $altAsamaIDs)->orderBy('asama')->get();

              
                $guncellenmisAsamaBilgisi = $servis->asamalar->asama;
                return response()->json([
                    'status' => 'success',
                    'message' => 'Servis planı başarıyla kaydedildi.',
                    'asama' => $guncellenmisAsamaBilgisi,
                    'altAsamalar' => $altAsamalar,
                ]);

            } else {
               
                
                return response()->json(['status' => 'error', 'message' => 'HATA! Servis aşama eklenemedi.']);
            }

        } catch (\Exception $e) {
            
            return response()->json(['status' => 'error', 'message' => 'Bir hata oluştu: ' . $e->getMessage()]);
        }
    }
private function stokKontrolEt(Request $request, $gelenIslem)
{
    foreach ($request->all() as $key => $value) {
        if (strpos($key, 'soru') !== false && $value == "Parca") {
            if ($request->has('parca') && is_array($request->input('parca'))) {
                foreach ($request->input('parca') as $stageId => $selectedParts) {
                    foreach ($selectedParts as $stokId => $selectedStokValue) {
                        $adet = abs($request->input("adet.{$stageId}.{$stokId}", 0));

                        // Stok durumunu kontrol et
                        $stokHareketleri = StockAction::where('stokId', $stokId)
                            ->get();

                        if ($stokHareketleri->count() > 0) {
                            $toplam = 0;
                            foreach ($stokHareketleri as $hareket) {
                                if ($hareket->islem == "1") { // Giriş işlemi
                                    $toplam += $hareket->adet;
                                } elseif ($hareket->islem == "2") { // Çıkış işlemi (Serviste Kullanım, Teslimat vb.)
                                    $toplam -= $hareket->adet;
                                } elseif ($hareket->islem == "3") { // Farklı bir çıkış işlemi

                                    $toplam -= $hareket->adet;
                                }
                            }

                            if ($toplam <= 0 || $adet > $toplam) {
                                $stok = Stock::where('id', $stokId)->first();
                                return "STOKHATA: " . mb_convert_case($stok->urunAdi, MB_CASE_TITLE, "UTF-8") . " Stok Adeti Yetersizdir.";
                            }
                        } else {
                            $stok = Stock::where('id', $stokId)->first();
                            return "STOKHATA: " . mb_convert_case($stok->urunAdi, MB_CASE_TITLE, "UTF-8") . " Stok Adeti Yetersizdir.";
                        }
                    }
                }
            }

            // Parça teslim et işleminde stok seçimi zorunlu
            if ($gelenIslem == "238") {
                $stokSecildi = false;
                if ($request->has('parca') && is_array($request->input('parca'))) {
                    foreach ($request->input('parca') as $stageId => $selectedParts) {
                        if (!empty($selectedParts)) {
                            $stokSecildi = true;
                            break;
                        }
                    }
                }

                if (!$stokSecildi) {
                    return "STOKHATA: Parça Teslim Ederken Stok Seçmeni Zorunludur.";
                }
            }
        }

        if (strpos($key, 'soru') !== false && $value == "Konsinye Cihaz") {
            if ($request->has('konsinye_cihaz') && is_array($request->input('konsinye_cihaz'))) {
                foreach ($request->input('konsinye_cihaz') as $stageId => $selectedConsignments) {
                    foreach ($selectedConsignments as $consignmentId => $selectedConsignmentValue) {
                        $consignmentAdet = abs($request->input("konsinye_adet.{$stageId}.{$consignmentId}", 0));
                        if ($consignmentAdet > 0) { // Sadece adet girildiyse kontrol yap
                            $girisAdet = StockAction::where('stokId', $consignmentId)
                                ->whereIn('islem', [1, 4]) // 1: Alış, 4: Müşteriden İade (Konsinye için giriş)
                                ->sum('adet');
                            $cikisAdet = StockAction::where('stokId', $consignmentId)
                                ->where('islem', 2) // 2: Serviste Kullanım (Konsinye için çıkış)
                                ->sum('adet');

                            $currentConsignmentStock = $girisAdet - $cikisAdet;
                            if ($consignmentAdet > $currentConsignmentStock) {
                                $consignmentStock = Stock::where('id', $consignmentId)->first();
                                return "STOKHATA: " . mb_convert_case($consignmentStock->urun_adi, MB_CASE_TITLE, "UTF-8") . " Konsinye Cihaz Stok Adedi Yetersizdir.";
                            }
                        }
                    }
                }
            }
        }
    }
    return null;
}

private function soruCevaplariniIsle(Request $request, $servisId, $planId, $tenantId, $gelenIslem)
{
         if ($request->has('soru')) {
            foreach ($request->input('soru') as $soruId => $cevap) {
                if ($cevap == "Parca") {
                    $this->parcaIslemleriniYap($request, $servisId, $planId, $tenantId, $soruId, $gelenIslem);
                } elseif ($cevap == "Konsinye Cihaz") {
                    if ($request->has("konsinye_cihaz.{$soruId}")) {
                    foreach ($request->input("konsinye_cihaz.{$soruId}") as $konsinyeId => $value) {
                        $adet = abs($request->input("konsinye_adet.{$soruId}.{$konsinyeId}", 1));

                        if ($adet > 0) { // Sadece adet girildiyse işlem yap
                            $this->useConsignmentDevice($konsinyeId, $adet, $servisId, $planId, $tenantId,$soruId);
                        }
                    }
                }
                } else {
                    $kid = Auth()->user()->user_id;
                    if (is_array($cevap)) {
                        // Çoklu cevap (checkbox)
                        foreach ($cevap as $cevapItem) {
                            ServiceStageAnswer::create([
                                'firma_id' => $tenantId,
                                'kid' => $kid,
                                'servisid' => $servisId,
                                'planid' => $planId,
                                'soruid' => $soruId,
                                'cevap' => $cevapItem,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                        }
                    } else {
                        // Tekli cevap
                        ServiceStageAnswer::create([
                            'firma_id' => $tenantId,
                            'kid' => $kid,
                            'servisid' => $servisId,
                            'planid' => $planId,
                            'soruid' => $soruId,
                            'cevap' => $cevap,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            }
        }
    }

private function useConsignmentDevice($stokId, $adet, $servisId, $planId, $tenantId, $soruId)
{
    // Stok kaydını al
    $stok = Stock::where('id', $stokId)->first();

    if (!$stok || $stok->urunKategori != 3) {
        throw new \Exception("Bu stok bir konsinye cihaz değildir.");
    }

    // Önce aynı planId, stokId için eski çıkış kayıtlarını sil
    StockAction::where('firma_id', $tenantId)
        ->where('stokId', $stokId)
        ->where('planId', $planId)
        ->where('islem', 2)  // çıkış işlemi
        ->delete();


    //StokAction ile çıkış işlemini kaydet
    StockAction::create([
        'firma_id' => $tenantId,
        'kid' => auth()->id(),
        'stokId' => $stokId,
        'islem' => 2, // Konsinye cihaz kullanımı
        'servisid' => $servisId,
        'adet' => $adet,
        'planId' => $planId,
        'depo' => 1,
        'created_at' => now(),
        'updated_at' => now()
    ]);

    //ServiceStageAnswer kaydı
    ServiceStageAnswer::create([
        'firma_id' => $tenantId,
        'servisid' => $servisId,
        'planid' => $planId,
        'soruid' => $soruId,
        'cevap' => "{$stokId}---{$adet}", 
        'kid' =>  auth()->user()->user_id,
        'created_at' => now(),
        'updated_at' => now()
    ]);
}
private function geriAlConsignmentDevice($stokId, $adet, $servisId, $planId, $tenantId, $soruId = null)
{

    // Yeni giriş işlemi
    StockAction::create([
        'firma_id' => $tenantId,
        'kid' => auth()->id(),
        'stokId' => $stokId,
        'islem' => 4, // Geri alma
        'servisid' => $servisId,
        'adet' => $adet,
        'planId' => $planId,
        'depo' => 1,
        'created_at' => now(),
        'updated_at' => now()
    ]);


    // Cevap olarak kaydet
    if ($soruId) {
        ServiceStageAnswer::create([
            'firma_id' => $tenantId,
            'servisid' => $servisId,
            'planid' => $planId,
            'soruid' => $soruId,
            'cevap' => $cevapText,
            'kid' => auth()->user()->user_id,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}

private function parcaIslemleriniYap(Request $request, $servisId, $planId, $tenantId, $soruId, $gelenIslem)
{
    $stokCevapArray = [];
    if ($request->has("parca.{$soruId}")) {
        foreach ($request->input("parca.{$soruId}") as $stokId => $value) {
            $adet = abs($request->input("adet.{$soruId}.{$stokId}", 1));

            $stokCevapArray[] = "{$stokId}---{$adet}";
            if ($gelenIslem == 238) { // "Parça Teslim Et" aşaması için varsayılan ID 238
                $this->parcaTeslimEt($stokId, $adet, $servisId, $planId, $tenantId);
            } else {
                $this->parcaKullan($stokId, $adet, $servisId, $planId, $tenantId);
            }
        }
    }

    // Toplanan stok cevaplarını birleştirerek string oluşturun
    $stokCevap = implode(', ', $stokCevapArray);

    // Eğer birleştirilmiş cevap boş değilse, veritabanına kaydedin
    if (!empty($stokCevap)) {
        ServiceStageAnswer::create([
            'firma_id' => $tenantId,
            'servisid' => $servisId,
            'planid' => $planId,
            'soruid' => $soruId,
            'cevap' => $stokCevap, 
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
 private function parcaTeslimEt($stokId, $adet, $servisId, $planId, $tenantId)
    {
        // Önceki planı bul
        $sonPlan = ServicePlanning::where('servisid', $servisId)
            ->orderBy('id', 'desc')
            ->skip(1)
            ->first();

        // Personel stok ekle/güncelle
            $perStok = PersonelStock::where('pid', $sonPlan->pid ?? $sonPlan->kid ?? auth()->id()) 
                ->where('stokid', $stokId)
                ->first();

                if ($perStok) {
                    PersonelStock::where('id', $perStok->id)
                        ->update([
                            'adet' => $perStok->adet + $adet,
                            'updated_at' => now()
                        ]);
                    $perStokId = $perStok->id;
                } else {
                    $perStokId = PersonelStock::insertGetId([
                    'firma_id' => $tenantId, 
                    'pid' => $sonPlan->pid ?? $sonPlan->kid ?? auth()->id(),
                    'stokid' => $stokId,
                    'adet' => $adet,
                    'created_at' => now(),
                    'updated_at' => now()
                    ]);
                }


        // Stok hareketi kaydet
        StockAction::create([
            'firma_id' => $tenantId,
            'stokId' => $stokId,
            'islem' => 3,
            'adet' => $adet,
            'servisid' => $servisId,
            'fiyat' => 0,
            'fiyatBirim' => 1,
            'planId' => $planId,
             //'personel_stok_id' => $perStokId,
            'personel' => $sonPlan->user_id,
            'pid' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        
    }

    private function parcaKullan($stokId, $adet, $servisId, $planId, $tenantId)
    {
        $stok = Stock::where('firma_id',$tenantId)->where('id', $stokId)->first();
        $fiyat = $adet * $stok->fiyat;

        // Stok hareketi kaydet
        $stokHareketId = StockAction::insertGetId([
            'firma_id' => $tenantId,
            'pid' => auth()->id(),
            'stokId' => $stokId,
            'islem' => 2,
            'servisid' => $servisId,
            'depo' => 1,
            'adet' => $adet,
            'fiyat' => $fiyat,
            'fiyatBirim' => 1,
            'planId' => $planId,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Personel stoğundan düş
            $perStok = PersonelStock::where('pid', auth()->user()->user_id)
                ->where('stokid', $stokId) 
                ->first();
                if ($perStok) {
                    PersonelStock::where('id', $perStok->id)
                        ->update([
                            'adet' => $perStok->adet - $adet,
                            'updated_at' => now()
                        ]);
                }

        // Servis durumu bilgilerini al
        $servisDurum = Service::where('id', $servisId)->first();

        // Kasa hareketi ekle
        $stokIslem = PaymentType::where('parca', '1')->first();

        // DB::table('kasa_hareketleri')->insert([
        //     'tenant_id' => $tenantId,
        //     'user_id' => auth()->id(),
        //     'personel_id' => auth()->id(),
        //     'islem_tarihi' => now(),
        //     'odeme_yonu' => 2,
        //     'odeme_sekli' => 178,
        //     'odeme_turu' => $stokIslem->id,
        //     'odeme_durum' => 1,
        //     'fiyat' => $fiyat,
        //     'fiyat_birim' => 1,
        //     'aciklama' => "Stok ID: {$stokId} ({$stok->urun_adi})",
        //     'marka' => $servisDurum->cihaz_marka,
        //     'cihaz' => $servisDurum->cihaz_tur,
        //     'servis_id' => $servisDurum->id,
        //     'stok_islem' => $stokHareketId,
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]);

        // Servis para hareketi ekle
        // DB::table('servis_para_hareketleri')->insert([
        //     'tenant_id' => $tenantId,
        //     'servis_id' => $servisId,
        //     'tarih' => now(),
        //     'odeme_sekli' => 178,
        //     'odeme_durum' => 1,
        //     'fiyat' => $fiyat,
        //     'aciklama' => "Stok ID: {$stokId} ({$stok->urun_adi})",
        //     'odeme_yonu' => 2,
        //     'stok_islem' => $stokHareketId,
        //     'user_id' => auth()->id(),
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]);
    }

    private function ozelDurumlariIsle(Request $request, $servisId, $planId, $tenantId, $gidenIslem, $servisDurum)
    {
        // Parça Teslim Et (259) özel durumu
        if ($gidenIslem == "259") {
            $this->parcaTeslimEtOzelDurum($servisId, $planId, $tenantId);
        }

        // Diğer özel durumlar (254, 267, 268)
        if ($gidenIslem == "254") {
            $planlama = ServicePlanning::where('servisid', $servisId)
                ->orderBy('id', 'desc')
                ->skip(1)
                ->first();

            if ($planlama && $planlama->gidenIslem == "255") {
                ServicePlanning::where('id', $planlama->id)->delete();
            }
        }

        if ($gidenIslem == "267") {
            $this->musteriIadeEdildiIslem($request, $servisId, $planId, $tenantId, $servisDurum);
        }

        if ($gidenIslem == "268") {
            $this->fiyatYukseltildiIslem($request, $servisId, $planId, $tenantId, $servisDurum);
        }
    }

    private function parcaTeslimEtOzelDurum($servisId, $planId, $tenantId)
    {
        $planlama = ServicePlanning::where('servisid', $servisId)
            ->orderBy('id', 'desc')
            ->skip(1)
            ->first();

        // Yeni plan oluştur
        $yeniPlanId = ServicePlanning::insertGetId([
            'firma_id' => $tenantId,
            'servisid' => $servisId,
            'gelenIslem' => 259,
            'gidenIslem' => $planlama->gelen_islem,
            'kid' => auth()->id(),
            'pid' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Servis durumunu güncelle
        Service::where('id', $servisId)
            ->update([
                'servisDurum' => $planlama->gelen_islem,
                'planDurum' => $yeniPlanId,
                'updated_at' => now()
            ]);

        // Önceki cevapları kopyala
        $planlama2 = ServicePlanning::where('servisid', $servisId)
            ->orderBy('id', 'desc')
            ->skip(2)
            ->first();

        $cevaplar = ServiceStageAnswer::where('planid', $planlama2->id)
            ->orderBy('id', 'asc')
            ->get();

        foreach ($cevaplar as $cevap) {
            $soru = StageQuestion::where('id', $cevap->soru_id)->first();
            $cevapText = ($soru->cevap == "[Tarih]") ? now()->format('d/m/Y') : $cevap->cevap;
            
            ServiceStageAnswer::insert([
                'firma_id' => $tenantId,
                'servisid' => $servisId,
                'planid' => $yeniPlanId,
                'soruid' => $cevap->soru_id,
                'cevap' => $cevapText,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    private function musteriIadeEdildiIslem(Request $request, $servisId, $planId, $tenantId, $servisDurum)
    {
        $fiyat = $request->input('soru378');
        $aciklama = $request->input('soru376');

        // Servis para hareketi
        // $paraHareketId = DB::table('servis_para_hareketleri')->insertGetId([
        //     'tenant_id' => $tenantId,
        //     'user_id' => auth()->id(),
        //     'servis_id' => $servisId,
        //     'tarih' => now(),
        //     'odeme_yonu' => 2,
        //     'odeme_sekli' => 178,
        //     'odeme_durum' => 1,
        //     'fiyat' => $fiyat,
        //     'aciklama' => $aciklama,
        //     'plan_islem' => $planId,
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]);

        // Kasa hareketi
        // DB::table('kasa_hareketleri')->insert([
        //     'tenant_id' => $tenantId,
        //     'user_id' => auth()->id(),
        //     'islem_tarihi' => now(),
        //     'odeme_yonu' => 2,
        //     'odeme_sekli' => 178,
        //     'odeme_turu' => 214,
        //     'odeme_durum' => 1,
        //     'fiyat' => $fiyat,
        //     'fiyat_birim' => 1,
        //     'aciklama' => $aciklama,
        //     'servis_id' => $servisId,
        //     'marka' => $servisDurum->cihaz_marka,
        //     'cihaz' => $servisDurum->cihaz_tur,
        //     'servis_islem' => $paraHareketId,
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]);
    }

    private function fiyatYukseltildiIslem(Request $request, $servisId, $planId, $tenantId, $servisDurum)
    {
        $fiyat = $request->input('soru380');
        $aciklama = $request->input('soru379');

        // Servis para hareketi
        // $paraHareketId = DB::table('servis_para_hareketleri')->insertGetId([
        //     'tenant_id' => $tenantId,
        //     'user_id' => auth()->id(),
        //     'servis_id' => $servisId,
        //     'tarih' => now(),
        //     'odeme_yonu' => 1,
        //     'odeme_sekli' => 178,
        //     'odeme_durum' => 2,
        //     'fiyat' => $fiyat,
        //     'aciklama' => $aciklama,
        //     'plan_islem' => $planId,
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]);

        // Kasa hareketi
        // DB::table('kasa_hareketleri')->insert([
        //     'tenant_id' => $tenantId,
        //     'user_id' => auth()->id(),
        //     'islem_tarihi' => now(),
        //     'odeme_yonu' => 1,
        //     'odeme_sekli' => 178,
        //     'odeme_turu' => 202,
        //     'odeme_durum' => 2,
        //     'fiyat' => $fiyat,
        //     'fiyat_birim' => 2,
        //     'aciklama' => $aciklama,
        //     'servis_id' => $servisId,
        //     'marka' => $servisDurum->cihaz_marka,
        //     'cihaz' => $servisDurum->cihaz_tur,
        //     'servis_islem' => $paraHareketId,
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]);
    }

    private function tarihDurumuKontrolEt($tenant_id)
    {
        // Tarih durumu kontrolü - performans optimizasyonu
        $servisPlanlar = ServicePlanning::where('firma_id', $tenant_id)->where('tarihKontrol', '0')
            ->get();

        foreach ($servisPlanlar as $servisRow) {
            $tarihDurum = "0";
            $cevaplar = ServiceStageAnswer::where('firma_id', $tenant_id)->where('planid', $servisRow->id)
                ->get();

            foreach ($cevaplar as $cevapRow) {
                $soru = StageQuestion::where('id', $cevapRow->soruid)
                    ->first();

                if ($soru && $soru->cevapTuru == "[Tarih]") {
                    $tarihDurum = "1";
                    break;
                }
            }

            ServicePlanning::where('firma_id', $tenant_id)->where('id', $servisRow->id)
                ->update([
                    'tarihDurum' => $tarihDurum,
                    'tarihKontrol' => "1",
                    'updated_at' => now()
                ]);
        }

        // Cevap text güncelleme
        $cevaplar = ServiceStageAnswer::where('firma_id', $tenant_id)->where('cevapText', null)
            ->get();

        foreach ($cevaplar as $cevapRow) {
            $soru = StageQuestion::where('id', $cevapRow->soruid)
                ->first();

            if ($soru) {
                ServiceStageAnswer::where('firma_id', $tenant_id)->where('id', $cevapRow->id)
                    ->update([
                        'cevapText' => $soru->cevapTuru,
                        'updated_at' => now()
                    ]);
            }
        }
    }
    //Servis Alt Aşamalarının veritabanına kaydını yapan fonksiyonların SONU

    //Servis Alt Aşamalarını silme fonksiyonu
    public function DeleteServicePlan($tenant_id, $planid) {
        $servisPlanID = $planid;

        $plan = ServicePlanning::where('firma_id', $tenant_id)->findOrFail($servisPlanID);
        $servis = Service::where('firma_id', $tenant_id)->findOrFail($plan->servisid);
        $cevaplar = ServiceStageAnswer::where('planid', $servisPlanID)->get();

        $kullanici = auth()->user();

        try {
            // alt bayi işlemi silme (gidenIslem == 264)
            if ($plan->gidenIslem == 264) {
                // bayi ve ilgili tüm veriler silinir
                // aynı mantıkla çalıştırılır
            }

            // stok silme işlemi (gidenIslem == 259)
            if ($plan->gidenIslem == 259) {
                $stok_cevap = ServiceStageAnswer::where('firma_id', $tenant_id)->where('planid', $plan->id)->first();
                $stoklar = explode(', ', $stok_cevap->cevap);

                foreach ($stoklar as $stokCevap) {
                    [$stokID, $adet] = explode('---', $stokCevap);
                    $stok = StockAction::where('stokId', $stokID)->where('planId', $plan->id)->first();
                    // $perStok = PersonelStok::find($stok->perStokID);
                    // $perStok->update(['adet' => $perStok->adet - $adet]);
                    // $stok->delete();
                }
            }

            // ödeme silme işlemleri
            if (in_array($plan->gidenIslem, [267, 268])) {
                $servisPara = ServiceMoneyAction::where('planIslem', $servisPlanID)->first();
                if ($servisPara) {
                    // KasaHareket::where('servisIslem', $servisPara->id)->delete();
                    // $servisPara->delete();
                }
            }

            // stokları geri al
            $stokHareketleri = StockAction::where('planId', $servisPlanID)->get();
            foreach ($stokHareketleri as $stok) {
                // PersonelStok::where([
                //     'pid' => $plan->pid,
                //     'stokid' => $stok->stokid
                // ])->increment('adet', $stok->adet);

                //KasaHareket::where('stokIslem', $stok->id)->delete();
                ServiceMoneyAction::where('stokIslem', $stok->id)->delete();

                $stok->delete();
            }

            // cevapları sil
            ServiceStageAnswer::where('planid', $servisPlanID)->delete();

            $plan->delete();

            // son plan mıydı? servisDurum güncelle
            if ($servis->servisDurum == $plan->gidenIslem) {
                $sonPlan = ServicePlanning::where('servisid', $plan->servisid)->latest()->first();
                if ($sonPlan) {
                    $servis->update([
                        'servisDurum' => $sonPlan->gidenIslem,
                        'planDurum' => $sonPlan->id,
                    ]);
                } else {
                    $ilkAsama = ServiceStage::where('ilkServis', 1)->first();
                    $servis->update([
                        'servisDurum' => $ilkAsama->id,
                        'planDurum' => 0,
                    ]);
                }
            }

            // Bu aşamaya ait alt aşamaları getir. Servis planı eklendikten sonra altAsamalar kısmını güncellemek için bunu yaptım.
                $altAsamaIDs = explode(',', $servis->asamalar->altAsamalar);
                $altAsamalar = ServiceStage::whereIn('id', $altAsamaIDs)->orderBy('asama')->get();

              
                $guncellenmisAsamaBilgisi = $servis->asamalar->asama;
                return response()->json([
                    'asama' => $guncellenmisAsamaBilgisi,
                    'altAsamalar' => $altAsamalar,
                ]);

            $guncellenmisAsamaBilgisi = $servis->asamalar->asama;
            return response()->json([
                'asama' => $guncellenmisAsamaBilgisi // örn: $servis->asama->asama
            ]);

        } catch (\Exception $e) {
            return response("HATA! Servis Plan Silinemedi.", 500);
        }
    }

      //Servis planı düzenleme viewını açan fonksiyon
    public function EditServicePlan($tenant_id, $planid) {
        $firma = Tenant::where('id', $tenant_id)->first();
        
        if (!$firma) {
            return response()->json(['error' => 'Firma bulunamadı'], 404);
        }

        // Servis planı bilgilerini al
        $servisPlan = ServicePlanning::where('id', $planid)
            ->where('firma_id', $tenant_id)
            ->first();

        if (!$servisPlan) {
            return response()->json(['error' => 'Plan bulunamadı'], 404);
        }

        // Plan cevaplarını al
        $planCevaplar = ServiceStageAnswer::where('planid', $planid)
            ->orderBy('id', 'ASC')
            ->get();

        // Servis bilgilerini al
        $servis = Service::find($servisPlan->servisid);

        // Personelleri al
        $personellerAll = User::where('tenant_id', $tenant_id)
            ->where('status', '1')
            ->orderBy('name', 'ASC')
            ->get();

        // Stokları al (eğer işlem parça teslim değilse)
        $personel_id = Auth::user()->user_id;
        $stoklar = collect();
        if ($servisPlan->gidenIslem != "259") {
            $stoklar = PersonelStock::where('firma_id', $tenant_id)
                ->where('pid', $personel_id)
                ->with('stok')
                ->orderBy('id', 'ASC')
                ->get()
                ->filter(function($item) {
                    return $item->stok !== null;
                });
        }

        // Personelin üzerindeki toplam stok adedini hesapla (PersonelStock tablosundan)
        $toplamPersonelStokAdedi = PersonelStock::where('firma_id', $tenant_id)
            ->where('pid', $personel_id)
            ->sum('adet');

         // Konsinye Cihaz Stok İşlemleri
    $konsinyeKategoriId = 3; // İkinci fonksiyonda olduğu gibi konsinye kategori ID'si
    $konsinyeCihazlar = Stock::where('firma_id', $tenant_id)
        ->where('urunKategori', $konsinyeKategoriId)
        ->get();

    $toplamKonsinyeCihazAdedi = 0;

    foreach ($konsinyeCihazlar as $device) {
        // Giriş işlemleri (1: Alış, 4: Müşteriden İade)
        $girisAdet = StockAction::where('stokId', $device->id)
            ->whereIn('islem', [1, 4])
            ->sum('adet');

        // Çıkış işlemleri (2: Serviste Kullanım)
        $cikisAdet = StockAction::where('stokId', $device->id)
            ->where('islem', 2)
            ->sum('adet');

        // Güncel stok miktarını hesapla
        $device->current_stock_quantity = $girisAdet - $cikisAdet;

        // Sadece pozitif stokları toplama dahil et
        if ($device->current_stock_quantity > 0) {
            $toplamKonsinyeCihazAdedi += $device->current_stock_quantity;
        }
    }

    // Sadece stoku olan cihazları filtrele
    $konsinyeCihazlar = $konsinyeCihazlar->filter(function($device) {
        return $device->current_stock_quantity > 0;
    });   
    
        // Kullanıcı bilgilerini al
        $kullanici = auth()->user();

        return view('frontend.secure.all_services.edit_service_plan', compact(
            'servisPlan',
            'planCevaplar', 
            'servis',
            'personellerAll',
            'stoklar',
            'toplamPersonelStokAdedi',
            'kullanici',
            'tenant_id',
            'konsinyeCihazlar',
            'toplamKonsinyeCihazAdedi'

        ));
    }
    //servis planı düzenleme viewını açma fonksiyonu SONU

 //Servis plan aşama düzenleme güncelleme fonksiyonu
    public function UpdateServicePlan(Request $request, $tenant_id)
    {
        $planid = $request->input('planid');

        try {
            // Servis planını güncelle
            $servisPlan = ServicePlanning::where('id', $planid)
                ->where('firma_id', $tenant_id)
                ->first();

            if (!$servisPlan) {
                return response()->json(['error' => 'Plan bulunamadı'], 404);
            }

            // İşlemi yapan personeli güncelle
            if ($request->has('planIslemiYapan')) {
                $servisPlan->pid = $request->input('planIslemiYapan');
                $servisPlan->save();
            }

            // Plan cevaplarını güncelle
            $planCevaplar = ServiceStageAnswer::where('firma_id', $tenant_id)->where('planid', $planid)->get();
            
            foreach ($planCevaplar as $cevap) {
                $soruKey = 'soru' . $cevap->id;
                
                if ($request->has($soruKey)) {
                    $yeniCevap = $request->input($soruKey);
                    // Eğer parça seçimi varsa, checkbox'ları işle
                    if ($yeniCevap == 'Parca') {
                        $parcaCevap = $this->processParcaSelection($request, $tenant_id);
                        $cevap->cevap = $parcaCevap;
                    }elseif ($yeniCevap == 'Konsinye Cihaz') {
                    $konsinyeCevap = $this->processKonsinyeSelection($request, $tenant_id);
                    $cevap->cevap = $konsinyeCevap;
                    }else{
                        $cevap->cevap = $yeniCevap;
                    }
                    
                    $cevap->save();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Plan başarıyla güncellendi',
                'servis_id' => $servisPlan->servisid
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Güncelleme sırasında hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
    
private function processParcaSelection(Request $request, $tenant_id)
{
    $stokCevap = [];
    $planid = $request->input('planid');
    $servisPlan = ServicePlanning::where('id', $planid)
        ->where('firma_id', $tenant_id)
        ->first();

    if (!$servisPlan) {
        throw new \Exception("Servis planı bulunamadı");
    }
    $servisid = $servisPlan->servisid;

    foreach ($request->all() as $key => $value) {
        if (strpos($key, 'stokCheck') !== false) {
            $stokId = (int) filter_var($key, FILTER_SANITIZE_NUMBER_INT);
            $adet = abs($request->input("stokAdet{$stokId}", 1));

            // stokCevap dizisine ekle
            $stokCevap[] = "{$stokId}---{$adet}";

            // Kullanım mı teslim mi kontrol et
            if ($servisPlan->gelenIslem == "238") {
                $this->parcaTeslimEt($stokId, $adet, $servisid, $planid, $tenant_id);
            } else {
                $this->parcaKullan($stokId, $adet, $servisid, $planid, $tenant_id);
            }
        }
    }

    return implode(', ', $stokCevap); // View'da input name="soru..." olan alanın cevabına atanır
}
private function processKonsinyeSelection(Request $request, $tenant_id)
{
    $konsinyeCevap = [];
    $planid = $request->input('planid');
    $servisPlan = ServicePlanning::where('id', $planid)
        ->where('firma_id', $tenant_id)
        ->first();

    if (!$servisPlan) {
        throw new \Exception("Servis planı bulunamadı");
    }
    $servisid = $servisPlan->servisid;

    foreach ($request->all() as $key => $value) {
        if (strpos($key, 'konsinyeCheck') !== false) {
            $stokId = (int) filter_var($key, FILTER_SANITIZE_NUMBER_INT);
            $adet = abs($request->input("konsinyeAdet{$stokId}", 1));

            // konsinyeCevap dizisine ekle
            $konsinyeCevap[] = "{$stokId}---{$adet}";

            // Kullanım mı teslim mi kontrol et
            if ($servisPlan->gelenIslem == "238") {
                $this->konsinyeTeslimEt($stokId, $adet, $servisid, $planid, $tenant_id);
            } else {
                $this->konsinyeKullan($stokId, $adet, $servisid, $planid, $tenant_id);
            }
        }
    }
    return implode(', ', $konsinyeCevap); // View'da input name="soru..." olan alanın cevabına atanır
}
private function konsinyeKullan($stokId, $adet, $servisId, $planId, $tenantId)
{
    // Stok bilgilerini al
    $stok = Stock::where('id', $stokId)->first();
    if (!$stok) {
        throw new \Exception("Stok bulunamadı (ID: $stokId)");
    }
    $fiyat = $adet * $stok->fiyat;

    // Stok hareketi kaydet (islem=2: Serviste Kullanım)
    $stokHareketId = StockAction::insertGetId([
        'firma_id'    => $tenantId,
        'kid'         => auth()->id(),
        'stokId'      => $stokId,
        'islem'       => 2, // Konsinye kullanımı
        'servisid'    => $servisId,
        'depo'        => 1,
        'adet'        => $adet,
        'fiyat'       => $fiyat,
        'fiyatBirim'  => 1,
        'planId'      => $planId,
        'created_at'  => now(),
        'updated_at'  => now()
    ]);

    // Servis bilgilerini al
    $servisDurum = Service::find($servisId);

    // Kasa hareketi tipi (parça gibi işleniyor)
    $stokIslem = PaymentType::where('parca', '1')->first();

    // Kasa ve servis para hareketleri örnek olarak bırakıldı, istersen aç
    /*
    DB::table('kasa_hareketleri')->insert([
        'tenant_id'    => $tenantId,
        'user_id'      => auth()->id(),
        'personel_id'  => auth()->id(),
        'islem_tarihi' => now(),
        'odeme_yonu'   => 2,
        'odeme_sekli'  => 178,
        'odeme_turu'   => $stokIslem->id,
        'odeme_durum'  => 1,
        'fiyat'        => $fiyat,
        'fiyat_birim'  => 1,
        'aciklama'     => "Konsinye Cihaz: {$stok->urunAdi}",
        'marka'        => $servisDurum->cihaz_marka,
        'cihaz'        => $servisDurum->cihaz_tur,
        'servis_id'    => $servisDurum->id,
        'stok_islem'   => $stokHareketId,
        'created_at'   => now(),
        'updated_at'   => now()
    ]);
    */
}
    //Servis Aşamalarının servis-information blade'inde görüntülenmesini sağlayan ajaxı çalıştıran fonksionlar
    public function getServiceStageHistory($tenant_id, $servisId)
    {
        $servis = Service::with(['asamalar','users','cevaplar','plans'])->where('firma_id', $tenant_id)->findOrFail($servisId);
    
        $data = [
            'acilIslem' => null,
            'notlar' => [],
            'eskiIslemler' => [],
            'paraHareketleri' => []
        ];
        
        // Acil durum kontrolü - with kullan
        if ($servis->bid == 0 && $servis->acil != 0) {
            $acilIslem = EmergencyService::with('user:user_id,name')
                ->where('firma_id', $tenant_id)
                ->where('servisid', $servisId)
                ->first();
                
            if ($acilIslem) {
                $data['acilIslem'] = [
                    'tarih' => $acilIslem->created_at->format('d/m/Y'),
                    'personel' => $acilIslem->user->name ?? ''
                ];
            }
        }
        
        // Operatör notları - with kullan
        $notlar = ServiceOptNote::with('user:user_id,name')
            ->where('firma_id', $tenant_id)
            ->where('servisid', $servisId)
            ->orderBy('id', 'desc')
            ->get();
            
        foreach ($notlar as $not) {
            $data['notlar'][] = [
                'tarih' => $not->created_at->format('d/m/Y H:i'),
                'personel' => $not->user->name ?? '',
                'aciklama' => $not->aciklama
            ];
        }
        
        // Eski işlemler - nested with kullan
        $eskiIslemler = ServicePlanning::with([
            'user:user_id,name',
            'serviceStage:id,asama',
            'answers.question:id,soru,cevapTuru'
        ])->where('servisid', $servisId)
        ->orderBy('created_at', 'desc')
        ->get();
        
        $eklenenPara = [];
        
        foreach ($eskiIslemler as $eskiIslem) {
            $aciklamalar = [];
            foreach ($eskiIslem->answers as $cevap) {
                if (!empty($cevap->cevap)) {
                    $aciklamalar[] = $this->formatCevap($cevap->question, $cevap->cevap);
                }
            }
            
            $islemData = [
                'id' => $eskiIslem->id,
                'tarih' => $eskiIslem->created_at->format('d/m/Y H:i'),
                'personel' => $eskiIslem->user->name ?? '',
                'asama' => $eskiIslem->serviceStage->asama ?? '',
                'aciklamalar' => $aciklamalar,
                'pid' => $eskiIslem->pid,
            ];
            
            $data['eskiIslemler'][] = $islemData;
            
            // Para hareketleri için tarih
            $tarih = $eskiIslem->created_at->format('Y-m-d');
            $paraHareketleri = ServiceMoneyAction::with([
                'user:user_id,name',
                'paymentMethod:id,sekli'
            ])->where('firma_id', $tenant_id)
            ->where('servisid', $servisId)
            ->where('odemeYonu', 1)
            ->whereDate('created_at', $tarih)
            ->get();
                
            foreach ($paraHareketleri as $paraIslem) {
                if (!in_array($paraIslem->id, $eklenenPara)) {
                    $eklenenPara[] = $paraIslem->id;
                    $data['eskiIslemler'][] = $this->formatParaHareketi($paraIslem);
                }
            }
        }
        
        // Kalan para hareketleri
        $kalanParaHareketleri = ServiceMoneyAction::with([
            'personel:user_id,name',
            'odemeSekliRelation:id,odemeSekli'
        ])->where('firma_id', $tenant_id)
        ->where('servisid', $servisId)
        ->where('odemeYonu', 1)
        ->whereNotIn('id', $eklenenPara)
        ->orderBy('id', 'desc')
        ->get();
            
        foreach ($kalanParaHareketleri as $paraIslem) {
            $data['paraHareketleri'][] = $this->formatParaHareketi($paraIslem);
        }
        
        return response()->json($data);
    }
    
    private function formatCevap($soru, $cevap)
    {
        if (!$soru) return '';
        
        $result = '<strong>' . $soru->soru . '</strong>: ';
        
        if (strpos($soru->cevapTuru, 'Grup') !== false) {
            $personel = User::find($cevap);
            $result .= $personel->name ?? '';
        } elseif ($soru->cevapTuru == '[Arac]') {
            $arac = Car::find($cevap);
            $result .= $arac->arac ?? '';
        } elseif ($soru->cevapTuru == '[Parca]') {
            $parcalar = explode(', ', $cevap);
            $parcaMetinler = [];
            foreach ($parcalar as $parca) {
                $parcaData = explode('---', $parca);
                if (count($parcaData) >= 2) {
                    $parcaId = $parcaData[0];
                    $adet = $parcaData[1];
                    $stok = Stock::find($parcaId);
                    if ($stok) {
                        $parcaMetinler[] = $stok->urunAdi . ' (' . $adet . ')';
                    }
                }
            }
            $result .= implode(', ', $parcaMetinler);
        } elseif ($soru->cevapTuru == '[Konsinye Cihaz]') {
            $parcalar = explode(', ', $cevap);
            $parcaMetinler = [];
            foreach ($parcalar as $parca) {
                $parcaData = explode('---', $parca);
                if (count($parcaData) >= 2) {
                    $parcaId = $parcaData[0];
                    $adet = $parcaData[1];
                    $stok = Stock::find($parcaId);
                    if ($stok) {
                        $parcaMetinler[] = $stok->urunAdi . ' (' . $adet . ')';
                    }
                }
            }
            $result .= implode(', ', $parcaMetinler);
        }
        elseif ($soru->cevapTuru == '[Bayi]') {
            $bayi = User::find($cevap);
            $result .= $bayi->name ?? '';
        } else {
            $result .= $cevap;
        }
        
        return $result;
    }
    
    private function formatParaHareketi($paraIslem)
    {
        $personel = User::find($paraIslem->pid);
        $odemeSekli = PaymentMethod::find($paraIslem->odemeSekli);
        
        $odemeDurum = '';
        if ($paraIslem->odemeDurum == 2) {
            $odemeDurum = '<span style="color:red">Beklemede</span>';
        } elseif ($paraIslem->odemeDurum == 1) {
            $odemeDurum = '<span style="color:green">Tamamlandı</span>';
        }
        
        $odemeYonu = '';
        if ($paraIslem->odemeYonu == 2) {
            $odemeYonu = '<i style="color: red;">Gider - ' . ($odemeSekli->odemeSekli ?? '') . '</i>';
        } elseif ($paraIslem->odemeYonu == 1) {
            $odemeYonu = '<i style="color: green;">Gelir - ' . ($odemeSekli->odemeSekli ?? '') . '</i>';
        }
        
        $fiyat = number_format($paraIslem->fiyat, 2, ',', '.') . ' TL';
        
        return [
            'type' => 'para',
            'tarih' => Carbon::parse($paraIslem->created_at)->format('d/m/Y H:i'),
            'personel' => $personel->name ?? '',
            'islem' => 'Para Hareketi: ' . $odemeDurum,
            'aciklama' => $fiyat . ' (' . $odemeYonu . ' ) <br>' . ucfirst($paraIslem->aciklama)
        ];
    }
    

    //Servis Aşamalarının servis-bilgileri blade'inde görüntülenmesini sağlayan fonk SONU

    public function EditServiceCustomer($tenant_id, $id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $customer = Customer::where('firma_id', $firma->id)->find($id);
        $countries = Il::orderBy('name','asc')->get();
        return view('frontend.secure.all_services.edit_service_customer', compact('firma', 'customer', 'countries'));
    }

    
    public function UpdateService($tenant_id, Request $request) 
    {
        $firma = Tenant::where('id', $tenant_id)->first();
        
        if (!$firma) {
            return response()->json(['error' => 'Firma bulunamadı'], 404);
        }
        
        $user = auth()->user();
        
        // Yetki kontrolü - PHP kodundaki grup_izinler kontrolü
        if ($user->can('Servisleri Göremez')) { // 3 nolu izni olanlar güncelleme yapamaz
            return response()->json(['error' => 'Yetkiniz yok'], 403);
        }
        
        // Validation rules
        $rules = [
            'cihazModel' => 'required|string|max:255',
        ];
        
        // Eğer kullanıcı admin (1 nolu grup) ise ek validasyon kuralları
        if ($user->can('Tüm Servisleri Görebilir')) {
            $rules = array_merge($rules, [
                'servisKaynak' => 'nullable|integer',
                'musaitSaat1' => 'nullable|string|max:10',
                'musaitSaat2' => 'nullable|string|max:10',
                'cihazSeriNo' => 'nullable|string|max:255',
                'cihazAriza' => 'nullable|string|max:1000',
                'operatorNotu' => 'nullable|string|max:1000',
                'garantiSuresi' => 'nullable|integer',
                'faturaNumarasi' => 'nullable|string|max:255',
                'konsinye' => 'nullable|integer',
            ]);
        }
        
        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $validator->errors()
            ], 422);
        }
        
        $resource_id = $request->servisid;
        
        // Servisi bul ve kullanıcının bu servisi güncelleyebilir olup olmadığını kontrol et
        $service = Service::findOrFail($resource_id);
        
        try {
            // Güncelleme verilerini hazırla
            $data = [
                'kid' => $user->user_id,
                'cihazModel' => strip_tags(trim($request->cihazModel)),
                'acil' => $request->acil,
                'updated_at' => now(),
            ];
            
            // Eğer kullanıcı admin (1 nolu grup) ise tüm alanları güncelleyebilir
            if ($user->can('Tüm Servisleri Görebilir')) {
                $data = array_merge($data, [
                    'kid' => $user->user_id,
                    'acil' => $request->acil,
                    'servisKaynak' => $request->kaynak ?: null,
                    'musaitTarih' => $request->musaitTarih,
                    'musaitSaat1' => $request->musaitSaat1 ?: null,
                    'musaitSaat2' => $request->musaitSaat2 ?: null,
                    'cihazMarka' => $request->cihazMarka,
                    'cihazTur' => $request->cihazTur,
                    'cihazModel' => $request->cihazModel,
                    'cihazSeriNo' => $request->cihazSeriNo ?: null,
                    'cihazAriza' => $request->cihazAriza ?: null,
                    'operatorNotu' => $request->opNot ?: null,
                    'garantiSuresi' => $request->cihazGaranti ?: null,
                    'faturaNumarasi' => $request->faturaNumarasi ?: null,
                    'konsinye' => $request->konsinye ?: null,
                ]);
            }
            
            // Servisi güncelle
            $service->update($data);
            
            // Güncellenmiş servisi döndür
            $updatedResource = Service::with([
                'musteri:id,adSoyad,tel1,tel2',
                'markaCihaz:id,marka',
                'turCihaz:id,cihaz'
            ])->find($resource_id);
            
            return response()->json([
                'success' => true,
                'data' => $updatedResource,
                'message' => 'Servis başarıyla güncellendi'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Güncelleme sırasında hata oluştu',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function DeleteService($tenant_id, $id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        if (!$firma) {
            return redirect()->back()->with([
                'message' => 'Firma bulunamadı!',
                'alert-type' => 'danger',
            ]);
        }
        Service::where('firma_id', $tenant_id)->findOrFail($id)->update([
            'durum' => 0,
            'silinmeTarihi' => Carbon::now(),
            'silenKisi' => auth()->id(),
        ]);
        Log::info( $firma->firma_adi . ' firmasının ' . Auth::user()->name . '  personeli ' . $id. ' IDli servisi sildi.', [
            'ip_address' => request()->ip(),
        ]);
        $notification = array(
            'message' => 'Servis Başarıyla Silindi',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }

    //Servis yazdırma fonksiyonu
    public function ServicetoPdf($tenant_id, $id) {
        $servis = Service::findOrFail($id); 
        $kid = Auth()->user()->user_id;
        // Tarih ve saat bilgilerini ayır
        $data = $this->getServisDetay($tenant_id, $id);
        
        if (!$data) {
            return abort(404, 'Servis bulunamadı');
        }
        
        

        $pdf = Pdf::loadView('frontend.secure.all_services.service_to_pdf',$data)->setPaper('A4', 'portrait')
        ->setOption('isPhpEnabled', true)
        ->setOption('isHtml5ParserEnabled', true);
        return $pdf->stream();
    }

    private function getServisDetay($tenant_id, $servisId)
    {
        // Servis bilgisini al
        $servis = Service::where('firma_id', $tenant_id)->where('id', $servisId)->first();
        
        if (!$servis) {
            return null;
        }
        
        // Tarih ve saat bilgilerini ayır
        $tarihSaat = explode(' ', $servis->created_at);
        $tarih = explode('-', $tarihSaat[0]);
        $saat = explode(':', $tarihSaat[1]);
        
        // İlgili tabloların bilgilerini al
        $musteri = Customer::where('firma_id', $tenant_id)->where('id', $servis->musteri_id)->first();
        $cihazMarka = DeviceBrand::where('firma_id', $tenant_id)->where('id', $servis->cihazMarka)->first();
        $cihazTur = DeviceType::where('firma_id', $tenant_id)->where('id', $servis->cihazTur)->first();
        $servisDurum = ServiceStage::where(function ($query) use ($tenant_id) {
                $query->whereNull('firma_id')->orWhere('firma_id', $tenant_id);
            })->where('id', $servis->servisDurum)->first();
        
        // Son 5 işlemi al
        $eskiIslemler = ServicePlanning::where('firma_id', $tenant_id)->where('servisid', $servis->id)
                         ->orderBy('id', 'DESC')
                         ->limit(5)
                         ->get();
        
        // Para hareketlerini al
        $paraIslemler = ServiceMoneyAction::where('firma_id', $tenant_id)->where('servisid', $servis->id)
                         ->orderBy('id', 'DESC')
                         ->get();
        
        // Mesaj ayarlarını al
        $mesajObj = ServiceFormSetting::where('firma_id', $tenant_id)->first();
        $mesaj = $mesajObj ? $mesajObj->mesaj : '';
        
        // Garanti kontrolü
        $garantiBitis = null;
        if ($servis->garantiSuresi != "0") {
            $garanti = WarrantyPeriod::where('id', $servis->garantiSuresi)->first();
            
            if ($garanti) {
                $garantiBitisTarihi = Carbon::parse($tarihSaat[0])->addMonths($garanti->garanti);
                $garantiBitis = [
                    $garantiBitisTarihi->day,
                    $garantiBitisTarihi->month,
                    $garantiBitisTarihi->year
                ];
            }
        }
        
        // Servis planlama bilgilerini al
        $servisPlanlama = ServiceStageAnswer::where('firma_id', $tenant_id)
                           ->where('servisid', $servis->id)
                           ->orderBy('id', 'DESC')
                           ->get();
        
        // Bayi personel bilgilerini kontrol et
        $getUye = null;
        $logoPath = null;
        $webSitesi = " ";
        
        foreach ($servisPlanlama as $asama) {
            if ($asama && $asama->cevapText == '[Bayi]') {
                $bayiPersonelId = $asama->cevap;
                $getUye = User::where('tenant_id', $tenant_id)
                              ->where('status', '1')
                              ->whereHas('roles', function($query) {
                                  $query->whereIn('id', ['259']);
                              })->where('user_id', $bayiPersonelId)->first();
                
                if ($getUye) {
                    $logoPath = $getUye->image;
                    $webSitesi = " ";
                    $mesaj = str_replace("[TEL]", $getUye->tel, $mesaj);
                }
                break;
            }
        }
        
        if (!$getUye) {
            $getUye = Tenant::where('id', $tenant_id)->first();
            if ($getUye) {
                $logoPath = $getUye->logo;
                $webSitesi = $getUye->webSitesi ?? " ";
                $mesaj = str_replace("[TEL]", $getUye->tel1, $mesaj);
            }
        }
        
        // İşlem detaylarını hazırla
        $islemDetaylari = [];
        foreach ($eskiIslemler as $eskiIslem) {
            $tarihSaat = explode(" ", $eskiIslem->created_at);
            $tarihArray = explode("-", $tarihSaat[0]);
            $saatArray = explode(":", $tarihSaat[1]);
            
            $asama = ServiceStage::where('id', $eskiIslem->gidenIslem)->first();
            $aciklamalar = ServiceStageAnswer::where('firma_id', $tenant_id)
                            ->where('planid', $eskiIslem->id)
                            ->orderBy('id', 'ASC')
                            ->get();
            
            $aciklamaMetni = '';
            foreach ($aciklamalar as $aciklama) {
                if (!empty($aciklama->cevap)) {
                    $soru = StageQuestion::where('id', $aciklama->soruid)->first();
                    
                    if (strpos($soru->cevapTuru, "[Grup") !== false) {
                        $personel = User::where('tenant_id', $tenant_id)->where('user_id', $aciklama->cevap)->first();
                        $aciklamaMetni .= '<strong>' . $soru->soru . '</strong>: ' . ($personel->name ?? '') . "<br>";
                    } else if ($soru->cevapTuru == "[Arac]") {
                        $arac = Car::where('firma_id', $tenant_id)->where('id', $aciklama->cevap)->first();
                        $aciklamaMetni .= '<strong>' . $soru->soru . '</strong>: ' . ($arac->arac ?? '') . "<br>";
                    } else if ($soru->cevapTuru == "[Parca]") {
                        $aciklamaMetni .= '<strong>' . $soru->soru . '</strong>: ';
                        $parcalar = explode(", ", $aciklama->cevap);
                        foreach ($parcalar as $parca) {
                            $parcaArray = explode("---", $parca);
                            $parcaId = $parcaArray[0];
                            $adet = $parcaArray[1] ?? 1;
                            $stok = Stock::where('firma_id', $tenant_id)->where('id', $parcaId)->first();
                            $aciklamaMetni .= ($stok->urunAdi ?? '') . " (" . $adet . "), ";
                        }
                        $aciklamaMetni .= "<br>";
                    } else if ($soru->cevapTuru == "[Bayi]") {
                        $bayi = User::where('tenant_id', $tenant_id)
                              ->where('status', '1')
                              ->whereHas('roles', function($query) {
                                  $query->whereIn('id', ['259']);
                              })->where('id', $aciklama->cevap)->first();
                        $aciklamaMetni .= '<strong>' . $soru->soru . '</strong>: ' . ($bayi->name ?? '') . "<br>";
                    } else {               
                        $aciklamaMetni .= '<strong>' . $soru->soru . '</strong>: ' . $aciklama->cevap . "<br>"; 
                    }
                    
                }
            }
            
            $islemDetaylari[] = [
                'tarih' => $tarihArray[2] . "/" . $tarihArray[1] . "/" . $tarihArray[0] . ' - ' . $saatArray[0] . ":" . $saatArray[1],
                'asama' => $asama->asama ?? '',
                'aciklama' => $aciklamaMetni
            ];
        }
        
        // Para işlem detaylarını hazırla
        $paraDetaylari = [];
        foreach ($paraIslemler as $paraIslem) {
            $tarihSaat = explode(" ", $paraIslem->created_at);
            $tarihArray = explode("-", $tarihSaat[0]);
            
            $personel = User::where('tenant_id', $tenant_id)->where('user_id', $paraIslem->pid)->first();
            $odemeSekli = PaymentMethod::where('firma_id', $tenant_id)->where('id', $paraIslem->odemeSekli)->first();
            
            $odemeDurum = "";
            if ($paraIslem->odemeDurum == "2") {
                $odemeDurum = 'Beklemede';
            } else if ($paraIslem->odemeDurum == "1") {
                $odemeDurum = 'Tamamlandı';
            }
            
            $paraDetaylari[] = [
                'tarih' => $tarihArray[2] . "/" . $tarihArray[1] . "/" . $tarihArray[0],
                'personel' => $personel->name ?? '',
                'odemeSekli' => $odemeSekli->odemeSekli ?? '',
                'odemeDurum' => $odemeDurum,
                'fiyat' => number_format($paraIslem->fiyat, 2, ',', '.') . ' TL'
            ];
        }
        
        return [
            'servis' => $servis,
            'tarih' => $tarih,
            'saat' => $saat,
            'musteri' => $musteri,
            'cihazMarka' => $cihazMarka,
            'cihazTur' => $cihazTur,
            'servisDurum' => $servisDurum,
            'garantiBitis' => $garantiBitis,
            'getUye' => $getUye,
            'logoPath' => $logoPath,
            'webSitesi' => $webSitesi,
            'mesaj' => $mesaj,
            'islemDetaylari' => $islemDetaylari,
            'paraDetaylari' => $paraDetaylari
        ];
    }
    //Servis yazdırma fonksiyonu SONU 

    //Servisler modalında servis para hareketleri 
    public function ServiceMoneyActions($tenant_id, $service_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $servis = Service::where('firma_id', $tenant_id)->where('id', $service_id)->first();
        // Servis para hareketlerini personel bilgileri ile beraber al
        $servisParaHareketleri = ServiceMoneyAction::where('firma_id', $firma->id)
            ->where('servisid', $servis->id)
            ->with(['personel:user_id,name', 'odemeSekliRelation:id,odemeSekli'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Toplam hesaplama
        $toplamSonuc = 0;
        foreach ($servisParaHareketleri as $hareket) {
            if ($hareket->odemeYonu == 2) { // Gider
                $toplamSonuc -= $hareket->fiyat;
            } elseif ($hareket->odemeYonu == 1) { // Gelir
                $toplamSonuc += $hareket->fiyat;
            }
        }

        return view('frontend.secure.all_services.service_money_actions.service_money_actions', compact('firma', 'servis','servisParaHareketleri', 'toplamSonuc'));
    }

    public function AddServiceIncome($tenant_id, $service_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $servis = Service::where('firma_id', $tenant_id)->where('id', $service_id)->first();
        $personeller = User::where('tenant_id', $tenant_id)->where('status', '1')->get();
        $odemeSekilleri = PaymentMethod::get();
        return view('frontend.secure.all_services.service_money_actions.add_service_income', compact('firma', 'servis', 'personeller', 'odemeSekilleri'));
    }

    public function StoreServiceIncome($tenant_id, Request $request) {
        $rules = [
            'servisid' => 'required|numeric',
            'odemeSekli' => 'required|numeric',
            'odemeDurum' => 'required|in:1,2',
            'fiyat' => 'required|numeric|min:0',
            'aciklama' => 'nullable|string|max:255',
        ];

        // Patron ise ek validasyon kuralları
        if (auth()->user()->hasRole('Patron')) {
            $rules['tarih'] = 'required|date';
            $rules['personeller'] = 'required|numeric|exists:tb_user,user_id';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validasyon hatası', 
                'messages' => $validator->errors()
            ], 422);
        }

         // Temel verileri al
        $servisid = $request->input('servisid');
        $fiyat = str_replace(",", ".", trim($request->input('fiyat')));
        
        // Tarih değişkenini doğru yerde tanımla
        $tarih = Carbon::now(); // Varsayılan olarak şu anki tarih
        
        // Eğer kullanıcı Patron ise ve tarih gönderilmişse, o tarihi kullan
        if (auth()->user()->hasRole('Patron') && $request->input('tarih')) {
           $tarih = Carbon::parse($request->input('tarih') . ' ' . now()->format('H:i:s'));
        }

        // Ana tabloya eklenecek veri
        $data = [
            'firma_id' => $tenant_id,
            'kid'          => auth()->user()->user_id,
            'servisid'     => $servisid,
            'created_at'   => $tarih,
            'odemeSekli'   => $request->input('odemeSekli'),
            'odemeDurum'   => $request->input('odemeDurum'),
            'fiyat'        => $fiyat,
            'aciklama'     => $request->input('aciklama'),
            'odemeYonu'    => 1,
        ];

        // Personel ID'sini belirle
        if (auth()->user()->hasRole('Patron') && $request->input('personeller')) {
            $data['pid'] = $request->input('personeller');
        } else {
            $data['pid'] = auth()->user()->user_id;
        }

        // servis_para_hareketleri tablosuna veri ekle
        $sonuc = ServiceMoneyAction::where('firma_id', $tenant_id)->create($data);
    
        if ($sonuc) {
            // kasa_hareketleri için veri hazırlığı
            $kasaData = [
                'firma_id' => $tenant_id,
                'kid'          => auth()->user()->user_id,
                'created_at'   => $tarih, // Aynı tarih değişkenini kullan
                'odemeYonu'    => 1,
                'odemeSekli'   => $request->input('odemeSekli'),
                'odemeDurum'   => $request->input('odemeDurum'),
                'fiyat'        => $fiyat,
                'fiyatBirim'   => 1,
                'aciklama'     => $request->input('aciklama'),
                'marka'        => $request->input('markaid'),
                'cihaz'        => $request->input('cihazid'),
                'servis'       => $servisid,
                'servisIslem'  => $sonuc->id, // ID'yi al
            ];

            // Personel bilgilerini ekle
            if (auth()->user()->hasRole('Patron') && $request->input('personeller')) {
                $kasaData['pid'] = $request->input('personeller');
                $kasaData['personel'] = $request->input('personeller');
            } else {
                $kasaData['pid'] = auth()->user()->user_id;
                $kasaData['personel'] = auth()->user()->user_id;
            }

            // Ödeme türünü belirle
            $odemeTuru = PaymentType::where('servis', 1)->first();
            if ($odemeTuru) {
                $kasaData['odemeTuru'] = $odemeTuru->id;
            }

            // kasa_hareketleri tablosuna ekle
            $kasaID = CashTransaction::create($kasaData);

            return response()->json(['success' => 'Ödeme eklendi.']);
        } else {
            return response()->json(['error' => 'HATA! Ödeme eklenemedi.'], 500);
        }

    }

    public function AddServiceExpense($tenant_id, $service_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $servis = Service::where('firma_id', $tenant_id)->where('id', $service_id)->first();
        $personeller = User::where('tenant_id', $tenant_id)->where('status', '1')->get();
        $odemeSekilleri = PaymentMethod::get();
        return view('frontend.secure.all_services.service_money_actions.add_service_expense', compact('firma', 'servis','personeller','odemeSekilleri'));
    }

    public function StoreServiceExpense($tenant_id, Request $request) {
         $rules = [
            'servisid' => 'required|numeric',
            'odemeSekli' => 'required|numeric',
            'odemeDurum' => 'required|in:1,2',
            'fiyat' => 'required|numeric|min:0',
            'aciklama' => 'nullable|string|max:255',
        ];

        // Patron ise ek validasyon kuralları
        if (auth()->user()->hasRole('Patron')) {
            $rules['tarih'] = 'required|date';
            $rules['personeller'] = 'required|numeric|exists:tb_user,user_id';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validasyon hatası', 
                'messages' => $validator->errors()
            ], 422);
        }

         // Temel verileri al
        $servisid = $request->input('servisid');
        $fiyat = str_replace(",", ".", trim($request->input('fiyat')));
        
        // Tarih değişkenini doğru yerde tanımla
        $tarih = Carbon::now(); // Varsayılan olarak şu anki tarih
        
        // Eğer kullanıcı Patron ise ve tarih gönderilmişse, o tarihi kullan
        if (auth()->user()->hasRole('Patron') && $request->input('tarih')) {
            $tarih = Carbon::parse($request->input('tarih') . ' ' . now()->format('H:i:s'));
        }

        // Ana tabloya eklenecek veri
        $data = [
            'firma_id' => $tenant_id,
            'kid'          => auth()->user()->user_id,
            'servisid'     => $servisid,
            'created_at'   => $tarih,
            'odemeSekli'   => $request->input('odemeSekli'),
            'odemeDurum'   => $request->input('odemeDurum'),
            'fiyat'        => $fiyat,
            'aciklama'     => $request->input('aciklama'),
            'odemeYonu'    => 2,
        ];

        // Personel ID'sini belirle
        if (auth()->user()->hasRole('Patron') && $request->input('personeller')) {
            $data['pid'] = $request->input('personeller');
        } else {
            $data['pid'] = auth()->user()->user_id;
        }

        // servis_para_hareketleri tablosuna veri ekle
        $sonuc = ServiceMoneyAction::where('firma_id', $tenant_id)->create($data);
    
        if ($sonuc) {
            // kasa_hareketleri için veri hazırlığı
            $kasaData = [
                'firma_id' => $tenant_id,
                'kid'          => auth()->user()->user_id,
                'created_at'   => $tarih,
                'odemeYonu'    => 2,
                'odemeSekli'   => $request->input('odemeSekli'),
                'odemeDurum'   => $request->input('odemeDurum'),
                'fiyat'        => $fiyat,
                'fiyatBirim'   => 1,
                'aciklama'     => $request->input('aciklama'),
                'marka'        => $request->input('markaid'),
                'cihaz'        => $request->input('cihazid'),
                'servis'       => $servisid,
                'servisIslem'  => $sonuc->id, // ID'yi al
            ];

            // Personel bilgilerini ekle
            if (auth()->user()->hasRole('Patron') && $request->input('personeller')) {
                $kasaData['pid'] = $request->input('personeller');
                $kasaData['personel'] = $request->input('personeller');
            } else {
                $kasaData['pid'] = auth()->user()->user_id;
                $kasaData['personel'] = auth()->user()->user_id;
            }

            // Ödeme türünü belirle
            $odemeTuru = PaymentType::where('servis', 1)->first();
            if ($odemeTuru) {
                $kasaData['odemeTuru'] = $odemeTuru->id;
            }

            // kasa_hareketleri tablosuna ekle
            $kasaID = CashTransaction::create($kasaData);

            return response()->json(['success' => 'Ödeme eklendi.']);
        } else {
            return response()->json(['error' => 'HATA! Ödeme eklenemedi.'], 500);
        }
    }

    public function EditServiceMoneyAction($tenant_id, $payment_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $servisPara = ServiceMoneyAction::where('firma_id', $tenant_id)
            ->where('id', $payment_id)
            ->with(['personel', 'odemeSekliRelation'])
            ->first();
        
        if (!$servisPara) {
            abort(404, 'Ödeme kaydı bulunamadı.');
        }
        
        $personeller = User::where('tenant_id', $tenant_id)->where('status', '1')->get();
        $odemeSekli = PaymentMethod::get();
        
        return view('frontend.secure.all_services.service_money_actions.edit_service_money_action', 
            compact('firma', 'servisPara', 'personeller', 'odemeSekli'));
    }

    public function UpdateServiceMoneyAction(Request $request, $tenant_id)
    {
        try {
            // Validation
            $request->validate([
                'odemeSekli' => 'required|integer',
                'odemeDurum' => 'required|integer',
                'fiyat' => 'required|numeric|min:0',
                'odemeYonu' => 'required|integer|in:1,2',
                'aciklama' => 'nullable|string|max:255',
            ]);
            
            $user = Auth::user();
            $kid = $user->user_id;
            $id = $request->payment_id;
            
            // Mevcut kaydı getir
            $asamaSec = ServiceMoneyAction::where('firma_id', $tenant_id)
                ->where('id', $id)
                ->where('kid', $kid)
                ->first();
            
            if (!$asamaSec) {
                return response()->json(['error' => 'Kayıt bulunamadı'], 404);
            }
            
            // Tarih işlemi
            $tarih = null;
            if ($request->has('tarih') && !empty($request->tarih)) {
                $tarihArray = explode("/", $request->tarih);
                if (count($tarihArray) == 3) {
                    $tarih = $tarihArray[2] . "-" . $tarihArray[1] . "-" . $tarihArray[0] . " " . now()->format("H:i:s");
                }
            }
            
            // Fiyat formatı düzeltme
            $fiyat = str_replace(",", ".", $request->fiyat);
            
            // Güncelleme verilerini hazırla
            $updateData = [
                'kid' => $kid,
                'odemeSekli' => $request->odemeSekli,
                'odemeDurum' => $request->odemeDurum,
                'fiyat' => $fiyat,
                'aciklama' => $request->aciklama,
                'odemeYonu' => $request->odemeYonu,
                'updated_at' => now(),
            ];
            
            // Personel bilgisi (sadece yetkili kullanıcılar için)
            if (auth()->user()->hasRole('Patron')) {
                $updateData['pid'] = $request->personeller;
                if ($tarih) {
                    $updateData['created_at'] = $tarih;
                }
            }
            
            if (!$tarih) {
                $updateData['created_at'] = now();
            }
                    
            // Servis para hareketini güncelle
            $servisGuncellendi = ServiceMoneyAction::where('firma_id', $tenant_id)
                ->where('id', $id)
                ->update($updateData);
            
            if ($servisGuncellendi) {                
                // Kasa hareketini güncelle
                $kasaSec = CashTransaction::where('firma_id', $tenant_id)
                    ->where('servisIslem', $id)
                    ->first();
                
                if ($kasaSec) {
                    $kasaUpdateData = [
                        'kid' => $kid,
                        'odemeYonu' => $request->odemeYonu,
                        'odemeSekli' => $request->odemeSekli,
                        'odemeDurum' => $request->odemeDurum,
                        'fiyat' => $fiyat,
                        'fiyatBirim' => "1",
                        'aciklama' => $request->aciklama,
                        'servis' => $asamaSec->servisid,
                        'updated_at' => now(),
                    ];
                    
                    if (auth()->user()->hasRole('Patron')) {
                        $kasaUpdateData['pid'] = $request->personeller;
                        $kasaUpdateData['personel'] = $request->personeller;
                        if ($tarih) {
                            $kasaUpdateData['created_at'] = $tarih;
                        }
                    }
                    
                    if (!$tarih) {
                        $kasaUpdateData['created_at'] = now();
                    }
                    
                    // Ödeme türünü getir
                    $servisIslem = PaymentType::where('firma_id', $tenant_id)
                        ->where('servis', '1')
                        ->first();
                    
                    if ($servisIslem) {
                        $kasaUpdateData['odemeTuru'] = $servisIslem->id;
                    }
                    
                    CashTransaction::where('firma_id', $tenant_id)
                        ->where('id', $kasaSec->id)
                        ->update($kasaUpdateData);
                    
                }
                
                
                return response()->json([
                    'success' => true,
                    'message' => 'Ödeme güncellendi.'
                ]);
                
            } else {
                
                return response()->json([
                    'success' => false,
                    'message' => 'HATA! Ödeme güncellenemedi.'
                ]);
            }
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasyon hatası',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => 'Bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function DeleteServiceMoneyAction($tenant_id, $payment_id) {
        $paymentId = $payment_id;
    
        try {
            $payment = ServiceMoneyAction::where('firma_id', $tenant_id)
                ->where('id', $paymentId)
                ->first();
            
            if (!$payment) {
                return response()->json(['success' => false, 'message' => 'Ödeme kaydı bulunamadı.'], 404);
            }
            
            // İlgili kasa hareketini de sil
            CashTransaction::where('servisIslem', $payment->id)->delete();
            
            $payment->delete();
            
            return response()->json(['success' => true, 'message' => 'Ödeme kaydı başarıyla silindi.']);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Bir hata oluştu: ' . $e->getMessage()], 500);
        }
    }
    //Servisler modalında servis para hareketleri SONU

    //Servisler modalında servis fotoğrafları kısmı başlangıcı
    public function ServicePhotos($tenant_id, $service_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $servis = Service::where('firma_id', $tenant_id)->where('id', $service_id)->first();
        $photos = ServicePhoto::where('firma_id', $firma->id)->where('servisid', $servis->id)->orderBy('created_at', 'desc')->get();
        return view('frontend.secure.all_services.service_photos.all_service_photos', compact('firma', 'servis', 'photos'));
    }

    public function StoreServicePhoto($tenant_id, Request $request) {
        try {
            // Validasyon kuralları
            $validator = Validator::make($request->all(), [
                'belge' => 'required|file|mimes:jpg,jpeg,png|max:5120', // 5MB = 5120KB
            ], [
                'belge.required' => 'Lütfen bir dosya seçiniz.',
                'belge.mimes' => 'Sadece JPG, JPEG ve PNG dosyaları yükleyebilirsiniz.',
                'belge.max' => 'Dosya boyutu 5MB\'dan büyük olamaz.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            // ➊ İlgili servisteki mevcut fotoğraf sayısını kontrol et
            $currentCount = ServicePhoto::where('firma_id', $tenant_id)
                            ->where('servisid', $request->servisid)
                            ->count();

            if ($currentCount >= 4) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu servise en fazla 4 fotoğraf yükleyebilirsiniz.'
                ], 422);
            }

            // ➋ Devam eden paralel yüklemeler sırasında sınırı aşmamak için:
            //    toplamdaki +1 kontrolü
            if ($currentCount + 1 > 4) {            // sadece tek dosya geliyorsa yeterli
                return response()->json([
                    'success' => false,
                    'message' => 'Fotoğraf limiti aşıldı.'
                ], 422);
            }
            
            $firma = Tenant::where('id', $tenant_id)->first();

            // Dosya işlemleri
            $file     = $request->file('belge');
            $original = $file->getClientOriginalName();
            $ext      = $file->getClientOriginalExtension();
            $uuid     = Str::uuid()->toString() . '.' . $ext;

            $path = "service_photos/firma_{$firma->firma_slug}/servis_{$request->servisid}/" . now()->toDateString();

            // Dosyayı kaydet
            $storedPath = $file->storeAs($path, $uuid, 'public');  
            
            // Veritabanına kaydet
            $photo = ServicePhoto::create([
                'firma_id' => $tenant_id,
                'kid' => auth()->user()->user_id ?? null,
                'servisid' => $request->servisid,
                'resimyol' => $storedPath,
                'created_at' => Carbon::now(),
            ]);

            // Başarılı response
            return response()->json([
                'success' => true,
                'message' => 'Fotoğraf başarıyla yüklendi.',
                'photo' => [
                    'id' => $photo->id,
                    'url' => Storage::url($photo->resimyol),
                    'created_at' => $photo->created_at->format('d/m/Y')
                ]
            ]);

        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => 'Dosya yüklenirken bir hata oluştu. Lütfen tekrar deneyiniz.'
            ], 500);
        }
    }

    public function DeleteServicePhoto($tenant_id, $photo_id)
    {
        try {
            $photo = ServicePhoto::where('firma_id', $tenant_id)
                                ->where('id', $photo_id)
                                ->firstOrFail();

            // resimyol = "service_photos/firma_3/servis_5/2025-06-26/uuid.jpg"
            if (Storage::disk('public')->exists($photo->resimyol)) {
                Storage::disk('public')->delete($photo->resimyol);
            }
            // Veritabanından sil
            $photo->delete();

            return response()->json([
                'success' => true,
                'message' => 'Fotoğraf başarıyla silindi.'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fotoğraf bulunamadı.'
            ], 404);

        } catch (\Exception $e) {            
            return response()->json([
                'success' => false,
                'message' => 'Fotoğraf silinirken bir hata oluştu.'
            ], 500);
        }
    }
    //Servisler modalında servis fotoğrafları kısmı SONU

    //Servisler modalında fiş notu kısmı başlangıcı
    public function ServiceReceiptNotes($tenant_id, $service_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $servis  = Service::where('id', $service_id)->first();
        $servis_fis_notlari = ServiceReceiptNote::where('firma_id',$firma->id)->where('servisid', $servis->id)->get();
        return view('frontend.secure.all_services.service_receipt_notes.receipt_notes', compact('firma', 'servis','servis_fis_notlari'));
    }

    public function AddServiceReceiptNote($tenant_id, $service_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $servis  = Service::where('id', $service_id)->first();
        return view('frontend.secure.all_services.service_receipt_notes.add_receipt_note', compact('firma', 'servis'));
    }

    public function StoreReceiptNote($tenant_id, Request $request) {
        $kid = Auth::user()->user_id;
        $receiptNotes = ServiceReceiptNote::create([
            'firma_id' => $tenant_id,
            'kid' => $kid,
            'servisid' => $request->servisid,
            'aciklama' => $request->aciklama,
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
                'success' => true,
                'message' => 'Servis fiş notu başarıyla yüklendi.',
                'note' => $receiptNotes,
            ]);
    }

    public function EditServiceReceiptNote($tenant_id, $note_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $note_id = ServiceReceiptNote::where('firma_id', $tenant_id)->where('id', $note_id)->first();

        return view('frontend.secure.all_services.service_receipt_notes.edit_receipt_note', compact('firma','note_id'));
    }

    public function UpdateServiceReceiptNote($tenant_id, Request $request) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $user = Auth::user();
        $kid = $user->user_id;
        $id = $request->note_id;

        ServiceReceiptNote::findOrFail($id)->update([
            'kid' => $kid,
            'servisid' => $request->servisid,
            'aciklama' => $request->aciklama,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Servis fiş notu başarıyla güncellendi.'
        ]);
    }

    public function DeleteReceiptNote($tenant_id, $note_id) {

        try {
            $service_receipt_note = ServiceReceiptNote::where('firma_id', $tenant_id)->where('id', $note_id)->firstOrFail();
            
            $service_receipt_note->delete();

            return response()->json([
                'success' => true,
                'message' => 'Servis fiş notu başarıyla silindi.'
            ]);

        } catch (\Exception $e) {            
            return response()->json([
                'success' => false,
                'message' => 'Fiş notu silinirken bir hata oluştu.'
            ], 500);
        }
    }
    //Servisler modalında fiş notu kısmı SONU

    //Servisler modalında operatör notu kısmı başlangıcı
    public function ServiceOptNotes($tenant_id, $service_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $servis  = Service::where('id', $service_id)->first();
        $opt_notlari = ServiceOptNote::where('firma_id', $firma->id)->where('servisid', $servis->id)->get();
        return view('frontend.secure.all_services.service_opt_notes.service_operator_notes', compact('firma', 'servis', 'opt_notlari'));
    }

    public function AddServiceOptNote($tenant_id, $service_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $servis  = Service::where('id', $service_id)->first();

        return view('frontend.secure.all_services.service_opt_notes.add_opt_note', compact('firma', 'servis'));
    }

    public function StoreServiceOptNote($tenant_id, Request $request) {
        $kid = Auth::user()->user_id;
        $optNotes = ServiceOptNote::create([
            'firma_id' => $tenant_id,
            'pid' => $kid,
            'servisid' => $request->servisid,
            'aciklama' => $request->aciklama,
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
                'success' => true,
                'message' => 'Servis fiş notu başarıyla yüklendi.',
                'note' => $optNotes,
        ]);
    }

    public function EditServiceOptNote($tenant_id, $note_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $note_id = ServiceOptNote::where('firma_id', $firma->id)->where('id', $note_id)->first();
        $servis = Service::where('firma_id', $tenant_id)->where('id', $note_id->servisid)->first();
        return view('frontend.secure.all_services.service_opt_notes.edit_opt_note', compact('firma', 'note_id', 'servis'));
    }

    public function UpdateServiceOptNote($tenant_id, Request $request) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $user = Auth::user();
        $kid = $user->user_id;
        $id = $request->note_id;

        ServiceOptNote::findOrFail($id)->update([
            'pid' => $kid,
            'servisid' => $request->servisid,
            'aciklama' => $request->aciklama,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Servis fiş notu başarıyla güncellendi.'
        ]);
    }

    public function DeleteServiceOptNote($tenant_id, $note_id) {

        try {
            $service_receipt_note = ServiceOptNote::where('firma_id', $tenant_id)->where('id', $note_id)->firstOrFail();
            
            $service_receipt_note->delete();

            return response()->json([
                'success' => true,
                'message' => 'Servis operatör notu başarıyla silindi.'
            ]);

        } catch (\Exception $e) {            
            return response()->json([
                'success' => false,
                'message' => 'Operatör notu silinirken bir hata oluştu.'
            ], 500);
        }
    }
    //Servisler modalında operatör notu kısmı SONU

    //Servisler modalındaki teklifler bölümü
    public function CustomerOffers($tenant_id, $service_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $servis = Service::where('firma_id', $tenant_id)->where('id', $service_id)->first();
        $customer_offers = Offer::where('firma_id', $tenant_id)->where('musteri_id', $servis->musteri_id)->get();
        return view('frontend.secure.all_services.customer_offers', compact('servis','customer_offers','firma'));
    }

    //Servisler modalındaki faturalar Bölümü
    public function  CustomerInvoices($tenant_id, $service_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $servis = Service::where('firma_id', $tenant_id)->where('id', $service_id)->first();
        return view('frontend.secure.all_services.customer_invoices', compact('servis','firma'));
    }

}


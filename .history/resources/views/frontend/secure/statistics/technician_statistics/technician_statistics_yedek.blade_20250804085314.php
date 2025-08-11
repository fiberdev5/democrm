@extends('frontend.secure.user_master')
@section('user')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<div class="page-content servis-istatistik">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])
        <div class="card">
            <div class="card-header sayfaBaslik d-flex justify-content-between align-items-center">
                <span>Teknisyen İstatistikleri</span>
                <!-- Filtre Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-primary btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        FİLTRELE
                    </button>
                    <div class="dropdown-menu p-3 dropdown-menu-end" style="min-width: 310px;">
                        <form id="filtreForm">
                            <div class="row mb-3">
                                <label class="col-4 col-form-label">Cihaz Türü</label>
                                <div class="col-8">
                                    <select class="form-select" name="cihazTur" id="cihazTur">
                                        <option value="">Hepsi</option>
                                        @foreach($cihazTurleri as $cihaz)
                                            <option value="{{ $cihaz->id }}">{{ $cihaz->cihaz }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-4 col-form-label">Tarih Aralığı</label>
                                <div class="col-8">
                                    <input type="text" name="tarih1" id="tarih1" class="form-control datepicker mb-2" readonly value="{{ date('d/m/Y') }}" style="background:#fff;">
                                    <input type="text" name="tarih2" id="tarih2" class="form-control datepicker mb-2" readonly value="{{ date('d/m/Y') }}" style="background:#fff;">
                                    
                                    <div class="tarih-butonlari">
                                        <button type="button" class="btn btn-sm btn-outline-secondary tarih-btn me-1 mb-1" data-days="30">Son 1 Ay</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary tarih-btn me-1 mb-1" data-days="15">Son 15 Gün</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary tarih-btn me-1 mb-1" data-days="7">Son 7 Gün</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary tarih-btn me-1 mb-1" data-days="1" data-yesterday="true">Dün</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary tarih-btn me-1 mb-1" data-days="0">Bugün</button>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-primary btn-sm w-100" id="araBtn">ARA</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card-body">
                        <table class="table table-hover mb-0" id="teknisyenTable">
                             <thead class="title">
                                <tr>
                                    <th>Teknisyen</th>
                                    <th class="text-center" style="width: 90px;">Atanan<br><small>Servis</small></th>
                                    <th class="text-center" style="width: 90px;">Tamamlanan<br><small>Servis</small></th>
                                    <th class="text-center" style="width: 90px;">Şikayetçi<br><small>Servis</small></th>
                                    <th class="text-center" style="width: 90px;">İptal<br><small>Servis</small></th>
                                    <th class="text-center" style="width: 90px;">Haber<br><small>Verecek</small></th>
                                    <th class="text-center" style="width: 90px;">Fiyatta<br><small>Anlaşılamadı</small></th>
                                    <th class="text-center" style="width: 90px;">Alınan<br><small>Ücret</small></th>
                                    <th class="text-center" style="width: 90px;">Verilen<br><small>Teklif</small></th>
                                </tr>
                            </thead>
                            <tbody id="teknisyenTableBody">
                                <!-- AJAX ile doldurulacak -->
                            </tbody>
                        </table>
            </div>
        </div>
    </div>
</div>

<style>

.table tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.teknisyen-detay-btn {
    cursor: pointer;
    transition: background-color 0.2s;
}

.teknisyen-detay-btn:hover {
    background-color: rgba(0, 123, 255, 0.1);
}

.teknisyen-detay-btn.clicked {
    background-color: rgba(0, 123, 255, 0.15);
}

.detay-satir {
    background-color: #f8f9fa;
}

.detay-satir td {
    padding: 0;
}

</style>
<script>
$(document).ready(function() {
    // Datepicker initialize
    $('.datepicker').datepicker({
        language: 'tr',
        autoclose: true,
        format: 'dd/mm/yyyy',
        endDate: new Date()
    });

    let varsayilanBaslangic = moment().subtract(29, 'days').format('DD/MM/YYYY');
    let varsayilanBitis = moment().format('DD/MM/YYYY');
    $('#tarih1').val(varsayilanBaslangic);
    $('#tarih2').val(varsayilanBitis);

    // Tarih butonları
    $('.tarih-btn').click(function() {
        const days = $(this).data('days');
        const isYesterday = $(this).data('yesterday');
        
        let tarih1, tarih2;
        
        if (isYesterday) {
            // Dün
            tarih1 = moment().subtract(1, 'days').format('DD/MM/YYYY');
            tarih2 = moment().subtract(1, 'days').format('DD/MM/YYYY');
        } else if (days === 0) {
            // Bugün
            tarih1 = moment().format('DD/MM/YYYY');
            tarih2 = moment().format('DD/MM/YYYY');
        } else {
            // Belirtilen gün sayısı kadar geriye
            tarih1 = moment().subtract(1, 'months').format('DD/MM/YYYY');
            tarih2 = moment().format('DD/MM/YYYY');
        }
        
        $('#tarih1').val(tarih1);
        $('#tarih2').val(tarih2);
    });

    // Dropdown içindeki form gönderimini engelle
    $('.dropdown-menu').click(function(e) {
        e.stopPropagation();
    });

    // Sayfa yüklendiğinde varsayılan verileri getir
    loadTechnicianStatistics();

    // Ara butonu
    $('#araBtn').click(function() {
        $('.dropdown-toggle').dropdown('hide');
        loadTechnicianStatistics();
    });

    // Teknisyen istatistiklerini yükle
    function loadTechnicianStatistics() {
        $('#loadingDiv').show();
        $('#istatistikTable').hide();

        const tarih1 = $('#tarih1').val();
        const tarih2 = $('#tarih2').val();
        const cihazTur = $('#cihazTur').val();

        // DÜZELTME: Doğru URL formatı - tenant_id değişkenini doğru kullan
        $.ajax({
            url: '/{{ $tenant_id }}/teknisyen-istatistikleri/data',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                tarihAraligi: tarih1 + '---' + tarih2,
                cihazTur: cihazTur
            },
            success: function(response) {
                console.log('Response:', response); // Debug için
                if (response.success) {
                    buildStatisticsTable(response.data);
                } else {
                    showError(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.log('Error:', xhr.responseText); // Debug için
                showError('Bir hata oluştu: ' + error);
            },
            complete: function() {
                $('#loadingDiv').hide();
                $('#istatistikTable').show();
            }
        });
    }

    // Tablo oluştur
    function buildStatisticsTable(data) {
        let html = '';
        
        if (data.length === 0) {
            html = '<tr><td colspan="9" class="text-center">Veri bulunamadı.</td></tr>';
        } else {
            data.forEach(function(teknisyen) {
                html += `
                    <tr class="teknisyen-detay-btn" data-teknisyen-id="${teknisyen.id}">
                        <td><strong>${teknisyen.name}</strong></td>
                        <td class="text-center"><strong>${teknisyen.atanan_servis}</strong></td>
                        <td class="text-center"><strong>${teknisyen.tamamlanan_servis}</strong></td>
                        <td class="text-center"><strong>${teknisyen.sikayetci_servis}</strong></td>
                        <td class="text-center"><strong>${teknisyen.iptal_servis}</strong></td>
                        <td class="text-center"><strong>${teknisyen.haber_verecek}</strong></td>
                        <td class="text-center"><strong>${teknisyen.fiyat_anlasma}</strong></td>
                        <td class="text-center"><strong>${formatCurrency(teknisyen.alinan_ucret)}</strong></td>
                        <td class="text-center"><strong>${formatCurrency(teknisyen.verilen_teklif)}</strong></td>
                    </tr>
                `;
            });
        }

        $('#teknisyenTableBody').html(html);

        // DataTable initialize
        if ($.fn.DataTable.isDataTable('#teknisyenTable')) {
            $('#teknisyenTable').DataTable().destroy();
        }

        $('#teknisyenTable').DataTable({
            "paging": false,
            "info": false,
            "searching": false,
            "ordering": true,
            "order": [[7, 'desc']], // Alınan ücrete göre sırala
            "language": {
                "sEmptyTable": "Herhangi bir teknisyen verisi bulunamadı.",
                "sSearch": "Teknisyen Ara:",
                "sZeroRecords": "Eşleşen kayıt bulunamadı",
                "oPaginate": {
                    "sNext": "Sonraki",
                    "sPrevious": "Önceki"
                }
            },
            "columnDefs": [
                { "orderable": false, "targets": 0 }
            ]
        });
    }

    // Teknisyen detayına tıklama
    $(document).on('click', '.teknisyen-detay-btn', function() {
        const teknisyenId = $(this).data('teknisyen-id');
        const $this = $(this);
        
        // Önceki detay satırlarını kapat
        $('.detay-satir').remove();
        $('.teknisyen-detay-btn').removeClass('clicked');

        if ($this.hasClass('clicked')) {
            $this.removeClass('clicked');
        } else {
            $this.addClass('clicked');
            
            // Detay satırı ekle
            const detayHtml = `
                <tr class="detay-satir">
                    <td colspan="9">
                        <div class="p-3">
                            <div class="text-center">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="visually-hidden">Detay yükleniyor...</span>
                                </div>
                                <div class="mt-2">Detay bilgiler yükleniyor...</div>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
            
            $this.after(detayHtml);
            
            // Burada tekniszen detay bilgilerini yükleyebilirsiniz
            setTimeout(function() {
                $('.detay-satir').html(`
                    <td colspan="9">
                        <div class="p-3">
                            <h6>Teknisyen Detay Bilgileri</h6>
                            <p>Bu bölümde teknisyenin detaylı servis bilgileri gösterilebilir.</p>
                            <p><small class="text-muted">Detay sayfası geliştirme aşamasındadır.</small></p>
                        </div>
                    </td>
                `);
            }, 1000);
        }
    });

    // Yardımcı fonksiyonlar
    function formatCurrency(amount) {
        return parseFloat(amount || 0).toLocaleString('tr-TR') + ' TL';
    }

    function showError(message) {
        // toastr varsa kullan, yoksa alert kullan
        if (typeof toastr !== 'undefined') {
            toastr.error(message, 'Hata');
        } else {
            alert('Hata: ' + message);
        }
    }
});

// Moment.js için Türkçe lokalizasyon
if (typeof moment !== 'undefined') {
    moment.locale('tr');
}
</script>
@endsection
///////////////////////////////////////////////////////Technician Statistics///////////////////////////////////////////////////////////////////
 public function TechnicianStatistics($tenant_id)
    {
        // Cihaz türlerini al
        $cihazTurleri = DeviceType::where('firma_id', $tenant_id)
                                 ->orderBy('cihaz', 'ASC')
                                 ->get();

        return view('frontend.secure.statistics.technician_statistics', compact(
            'tenant_id',
            'cihazTurleri'
        ));
    }

    public function getTechnicianStatisticsData(Request $request, $tenant_id)
    {
        try {
            // Debug: Request parametrelerini kontrol et
            \Log::info('Request parametreleri:', [
                'tarihAraligi' => $request->tarihAraligi,
                'cihazTur' => $request->cihazTur,
                'tenant_id' => $tenant_id
            ]);

            // Teknisyenleri al
            $teknisyenler = User::where('tenant_id', $tenant_id)
                                ->where('status', 1)
                                ->whereNull('ayrilmaTarihi')
                                ->whereHas('roles', function($query) {
                                    $query->whereIn('name', ['Teknisyen', 'Teknisyen Yardımcısı']);
                                })
                                ->orderBy('name', 'ASC')
                                ->get();

            \Log::info('Bulunan teknisyen sayısı: ' . $teknisyenler->count());

            // Tarih aralığını parse et
            $tarihler = explode('---', $request->tarihAraligi);
            $tarih1 = Carbon::createFromFormat('d/m/Y', $tarihler[0])->format('Y-m-d');
            $tarih2 = Carbon::createFromFormat('d/m/Y', $tarihler[1])->format('Y-m-d');

            \Log::info('Tarih aralığı:', ['tarih1' => $tarih1, 'tarih2' => $tarih2]);

            // Tarih aralığındaki tüm tarihleri al
            $tarihListesi = $this->getDatesFromRange($tarih1, $tarih2);
            \Log::info('Tarih listesi:', $tarihListesi);

            // Seçilen tarihlerdeki servis planlarını al
            $servisPlanlar = $this->getServicePlansForDates($tarihListesi, $tenant_id, $request->cihazTur);
            \Log::info('Bulunan servis planları (ID\'ler):', $servisPlanlar);

            // Her teknisyen için istatistikleri hesapla
            $teknisyenIstatistikleri = [];

            foreach ($teknisyenler as $teknisyen) {
                $teknisyenServisleri = $this->getTechnicianServices($teknisyen->user_id, $servisPlanlar);
                \Log::info('Teknisyen: ' . $teknisyen->name . ' - Servis sayısı: ' . count($teknisyenServisleri));

                if (!empty($teknisyenServisleri)) {
                    $istatistik = $this->calculateTechnicianStats($teknisyen, $teknisyenServisleri, $tarih1, $tarih2);
                    $teknisyenIstatistikleri[] = $istatistik;
                }
            }

            // Hiç servisi olmayan teknisyenleri de ekle
            $mevcutTeknisyenIds = collect($teknisyenIstatistikleri)->pluck('id')->toArray();
            $digerTeknisyenler = $teknisyenler->whereNotIn('user_id', $mevcutTeknisyenIds);

            foreach ($digerTeknisyenler as $teknisyen) {
                $teknisyenIstatistikleri[] = [
                    'id' => $teknisyen->user_id,
                    'name' => $teknisyen->name,
                    'atanan_servis' => 0,
                    'tamamlanan_servis' => 0,
                    'sikayetci_servis' => 0,
                    'iptal_servis' => 0,
                    'haber_verecek' => 0,
                    'fiyat_anlasma' => 0,
                    'alinan_ucret' => 0,
                    'verilen_teklif' => 0
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $teknisyenIstatistikleri
            ]);

        } catch (\Exception $e) {
            \Log::error('Teknisyen istatistikleri hatası: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Hata: ' . $e->getMessage()
            ]);
        }
    }

    private function getDatesFromRange($start, $end)
    {
        $dates = [];
        $current = Carbon::parse($start);
        $endDate = Carbon::parse($end);

        while ($current <= $endDate) {
            $dates[] = $current->format('d/m/Y');
            $current->addDay();
        }

        return $dates;
    }

    private function getServicePlansForDates($tarihListesi, $tenant_id, $cihazTur = null)
    {
        $query = ServiceStageAnswer::query()
                    ->join('stage_questions as ss', 'ss.id', '=', 'service_stage_answers.soruid')
                    ->join('services', 'services.id', '=', 'service_stage_answers.servisid')
                    ->where('ss.cevapTuru', 'LIKE', '%Tarih%')
                    ->where('services.firma_id', $tenant_id);

        // Tarih formatını kontrol et - hem d/m/Y hem de Y-m-d formatlarını dene
        $tarihListesiYmd = array_map(function($tarih) {
            return Carbon::createFromFormat('d/m/Y', $tarih)->format('Y-m-d');
        }, $tarihListesi);

        $query->where(function($q) use ($tarihListesi, $tarihListesiYmd) {
            $q->whereIn('service_stage_answers.cevap', $tarihListesi)
              ->orWhereIn('service_stage_answers.cevap', $tarihListesiYmd);
        });

        if ($cihazTur) {
            $query->where('services.cihazTur', $cihazTur);
        }

        $seciliTarihler = $query->whereIn('service_stage_answers.soruid', [296, 326, 369, 306, 337, 341])
                                ->pluck('service_stage_answers.planid')
                                ->unique() // Tekrar eden plan ID'lerini önle
                                ->toArray();

        \Log::info('Servis plan sorgusu sonucu:', [
            'tarih_listesi' => $tarihListesi,
            'tarih_listesi_ymd' => $tarihListesiYmd,
            'bulunan_planlar' => count($seciliTarihler),
            'plan_ids' => $seciliTarihler
        ]);

        return $seciliTarihler;
    }


    private function getTechnicianServices($teknisyenId, $servisPlanlar)
    {
        if (empty($servisPlanlar)) {
            return [];
        }

        // ServiceStageAnswer modelini kullanarak teknisyen atamasını kontrol et
        $servisler1 = ServiceStageAnswer::where('cevap', $teknisyenId)
            ->whereHas('question', function ($query) { // İlişki varsayımı: ServiceStageAnswer modelinde 'question' ilişkisi StageQuestion'a
                $query->where('cevapTuru', 'LIKE', '%Grup%');
            })
            ->whereIn('planid', $servisPlanlar)
            ->pluck('servisid')
            ->unique()
            ->toArray();

        // ServicePlanning modelini kullanarak kontrol et
        $servisler2 = ServicePlanning::where('pid', $teknisyenId)
            ->whereIn('id', $servisPlanlar)
            ->pluck('servisid')
            ->unique()
            ->toArray();

        // İki sonucu birleştir
        $servisler = array_unique(array_merge($servisler1, $servisler2));

        \Log::info('Teknisyen servisleri:', [
            'teknisyen_id' => $teknisyenId,
            'stage_answers_servisleri' => count($servisler1),
            'plannings_servisleri' => count($servisler2),
            'toplam_servisler' => count($servisler)
        ]);

        return $servisler;
    }


    private function calculateTechnicianStats($teknisyen, $servisler, $tarih1, $tarih2)
    {
        // Tamamlanan servisler
        $tamamlanan = Service::whereIn('id', $servisler)
                              ->where('servisDurum', 255) // 255 = Tamamlandı
                              ->count();

        // Şikayetçi servisler
        $sikayetciSay = $this->calculateComplaintServices($teknisyen->user_id, $servisler);

        // İptal servisler
        $iptalSay = $this->calculateCancelledServices($teknisyen->user_id, $servisler);

        // Haber verecek servisler
        $haberSay = $this->calculateNotificationServices($teknisyen->user_id, $servisler);

        // Fiyat anlaşma servisler
        $fiyatSay = $this->calculatePriceDisagreementServices($teknisyen->user_id, $servisler);

        // Alınan ücret (kasa hareketlerinden)
        $alinanUcret = $this->calculateCollectedAmount($teknisyen->user_id, $servisler, $tarih1, $tarih2);

        // Verilen teklifler
        $verilenTeklif = $this->calculateOfferedAmount($teknisyen->user_id, $servisler, $tarih1, $tarih2);

        return [
            'id' => $teknisyen->user_id,
            'name' => $teknisyen->name,
            'atanan_servis' => count($servisler),
            'tamamlanan_servis' => $tamamlanan,
            'sikayetci_servis' => $sikayetciSay,
            'iptal_servis' => $iptalSay,
            'haber_verecek' => $haberSay,
            'fiyat_anlasma' => $fiyatSay,
            'alinan_ucret' => $alinanUcret,
            'verilen_teklif' => $verilenTeklif
        ];
    }

    private function calculateComplaintServices($teknisyenId, $servisler)
    {
        $sikayetciSay = 0;

        $sikayetciler = ServicePlanning::whereIn('servisid', $servisler)
                                       ->where('gidenIslem', 254) // 254 = Şikayetçi
                                       ->groupBy('servisid')
                                       ->get();

        foreach ($sikayetciler as $servis) {
            $ilkServis = ServicePlanning::where('servisid', $servis->servisid)
                                        ->where('pid', $teknisyenId) // pid = personel id
                                        ->orderBy('tarih', 'ASC')
                                        ->first();

            $sonSikayet = ServicePlanning::where('servisid', $servis->servisid)
                                         ->where('gidenIslem', 254)
                                         ->orderBy('tarih', 'DESC')
                                         ->first();

            if ($ilkServis && $sonSikayet) {
                $ilkTarih = Carbon::parse(explode(" ", $ilkServis->tarih)[0]);
                $sikayetTarih = Carbon::parse(explode(" ", $sonSikayet->tarih)[0]);

                if ($sikayetTarih->greaterThan($ilkTarih)) {
                    $sikayetciSay++;
                }
            }
        }

        return $sikayetciSay;
    }

    private function calculateCancelledServices($teknisyenId, $servisler)
    {
        $iptalSay = 0;

        $iptaller = ServicePlanning::whereIn('servisid', $servisler)
                                   ->where('gidenIslem', 244) // 244 = İptal
                                   ->groupBy('servisid')
                                   ->get();

        foreach ($iptaller as $servis) {
            $ilkServis = ServicePlanning::where('servisid', $servis->servisid)
                                        ->where('pid', $teknisyenId)
                                        ->orderBy('tarih', 'ASC')
                                        ->first();

            $sonIptal = ServicePlanning::where('servisid', $servis->servisid)
                                       ->where('gidenIslem', 244)
                                       ->orderBy('tarih', 'DESC')
                                       ->first();

            if ($ilkServis && $sonIptal) {
                $ilkTarih = Carbon::parse(explode(" ", $ilkServis->tarih)[0]);
                $iptalTarih = Carbon::parse(explode(" ", $sonIptal->tarih)[0]);

                if ($iptalTarih->greaterThan($ilkTarih)) {
                    $iptalSay++;
                }
            }
        }

        return $iptalSay;
    }

    private function calculateNotificationServices($teknisyenId, $servisler)
    {
        $haberSay = 0;

        $haberciler = ServicePlanning::whereIn('servisid', $servisler)
                                     ->where('gidenIslem', 247) // 247 = Haber verecek
                                     ->groupBy('servisid')
                                     ->get();

        foreach ($haberciler as $haber) {
            $ilkServis = ServicePlanning::where('servisid', $haber->servisid)
                                        ->where('pid', $teknisyenId)
                                        ->orderBy('tarih', 'ASC')
                                        ->first();

            $sonHaber = ServicePlanning::where('servisid', $haber->servisid)
                                       ->where('gidenIslem', 247)
                                       ->orderBy('tarih', 'DESC')
                                       ->first();

            if ($ilkServis && $sonHaber) {
                $ilkTarih = Carbon::parse(explode(" ", $ilkServis->tarih)[0]);
                $haberTarih = Carbon::parse(explode(" ", $sonHaber->tarih)[0]);

                // Buradaki mantıkta bir değişiklik yapılmış gibi duruyor, 'greaterThanOrEqualTo' kullanıldı.
                if ($haberTarih->greaterThanOrEqualTo($ilkTarih)) {
                    $haberSay++;
                }
            }
        }

        return $haberSay;
    }

    private function calculatePriceDisagreementServices($teknisyenId, $servisler)
    {
        $fiyatSay = 0;

        $fiyatlar = ServicePlanning::whereIn('servisid', $servisler)
                                   ->where('gidenIslem', 241) // 241 = Fiyatta anlaşılamadı
                                   ->groupBy('servisid')
                                   ->get();

        foreach ($fiyatlar as $fiyat) {
            $ilkServis = ServicePlanning::where('servisid', $fiyat->servisid)
                                        ->where('pid', $teknisyenId)
                                        ->orderBy('tarih', 'ASC')
                                        ->first();

            $sonFiyat = ServicePlanning::where('servisid', $fiyat->servisid)
                                       ->where('gidenIslem', 241)
                                       ->orderBy('tarih', 'DESC')
                                       ->first();

            if ($ilkServis && $sonFiyat) {
                $ilkTarih = Carbon::parse(explode(" ", $ilkServis->tarih)[0]);
                $fiyatTarih = Carbon::parse(explode(" ", $sonFiyat->tarih)[0]);

                // Buradaki mantıkta bir değişiklik yapılmış gibi duruyor, 'greaterThanOrEqualTo' kullanıldı.
                if ($fiyatTarih->greaterThanOrEqualTo($ilkTarih)) {
                    $fiyatSay++;
                }
            }
        }

        return $fiyatSay;
    }

    private function calculateCollectedAmount($teknisyenId, $servisler, $tarih1, $tarih2)
    {
        if (empty($servisler)) {
            return 0;
        }

        // Doğrudan CashTransaction modelini kullanın
        $paraToplam = CashTransaction::whereIn('servis', $servisler)
                                     ->where('odemeYonu', '1')
                                     ->where('personel', $teknisyenId)
                                     ->whereBetween('created_at', [$tarih1 . ' 00:00:00', $tarih2 . ' 23:59:59'])
                                     ->sum('fiyat');

        \Log::info('Kasa hareketleri bulundu:', [
            'tablo' => 'cash_transactions', // Model adı
            'teknisyen' => $teknisyenId,
            'toplam' => $paraToplam
        ]);

        return $paraToplam ?: 0;
    }

    private function calculateOfferedAmount($teknisyenId, $servisler, $tarih1, $tarih2)
    {
        if (empty($servisler)) {
            return 0;
        }

        $teklifToplam = ServiceStageAnswer::join('service_plannings as sp', 'sp.id', '=', 'service_stage_answers.planid')
            ->whereIn('service_stage_answers.soruid', [350, 351, 352, 353, 354, 355, 356])
            ->whereIn('service_stage_answers.servisid', $servisler)
            ->where('sp.pid', $teknisyenId)
            ->whereBetween('sp.created_at', [$tarih1 . ' 00:00:00', $tarih2 . ' 23:59:59'])
            ->where('service_stage_answers.cevap', '!=', '')
            ->where('service_stage_answers.cevap', '!=', '0')
            ->sum(DB::raw('CAST(service_stage_answers.cevap AS DECIMAL(10,2))'));

        \Log::info('Teklif tutarları bulundu:', [
            'teknisyen' => $teknisyenId,
            'toplam' => $teklifToplam
        ]);

        return $teklifToplam ?: 0;
    }

    public function debugTableStructure()
    {
        $tablolar = [
            'service_stage_answers',
            'stage_questions',
            'services',
            'service_plannings',
            'cash_transactions'
        ];

        foreach ($tablolar as $tablo) {
            if (Schema::hasTable($tablo)) {
                $columns = Schema::getColumnListing($tablo);
                \Log::info("Tablo: $tablo", ['columns' => $columns]);
            } else {
                \Log::info("Tablo bulunamadı: $tablo");
            }
        }
    }
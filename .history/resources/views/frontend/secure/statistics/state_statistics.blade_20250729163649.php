@extends('frontend.secure.user_master')
@section('user')

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<style>
    .table-modern {
        background: #ffffff;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }
    .table-modern thead {
        background: linear-gradient(135deg, #8e44ad 0%, #9b59b6 100%);
        color: white;
    }
    .table-modern tbody tr:hover {
        background: linear-gradient(135deg, rgba(142, 68, 173, 0.1), rgba(155, 89, 182, 0.1));
        transform: scale(1.01);
        transition: all 0.3s ease;
    }
    .badge.bg-primary {
        background-color: #8e44ad !important;
        color: white;
        padding: 5px 10px;
        border-radius: 15px;
    }
    .card-header {
        background: #f8f9fa;
        padding: 15px 20px;
        border-bottom: 1px solid #e9ecef;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
        font-weight: bold;
        color: #343a40;
        display: flex; /* Flexbox ekle */
        justify-content: space-between; /* İçerikleri iki yana yasla */
        align-items: center; /* Dikeyde ortala */
    }
    .btn-action {
        background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);
        border: none;
        border-radius: 20px;
        padding: 6px 14px;
        color: white !important;
        transition: all 0.3s ease;
        font-weight: 500;
    }
    .btn-action:hover {
        background: linear-gradient(135deg, #7d3c98 0%, #6c3483 100%);
        color: white !important;
    }
    /* Durum simgesi için küçük yuvarlak kutu */
    .status-icon {
        width: 38px; /* Boyut büyütüldü */
        height: 38px; /* Boyut büyütüldü */
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        margin-right: 12px; /* Kenar boşluğu artırıldı */
        font-size: 16px; /* Yazı boyutu büyütüldü */
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1); /* Hafif gölge eklendi */
    }

    /* Durum tiplerine göre renkler - ÖRNEKLER */
    /* Yeni Servisler */
    .status-yeni-servisler {
        background: linear-gradient(135deg, #3498db, #2980b9); /* Mavi */
    }
    /* Teknisyen Yönlendir */
    .status-teknisyen-yonlendir {
        background: linear-gradient(135deg, #9b59b6, #8e44ad); /* Mor */
    }
    /* Cihaz Atölyeye Alındı */
    .status-cihaz-atolye-alindi {
        background: linear-gradient(135deg, #f39c12, #e67e22); /* Turuncu */
    }
    /* Parça Talep Et */
    .status-parca-talep-et {
        background: linear-gradient(135deg, #e74c3c, #c0392b); /* Kırmızı */
    }
    /* Yerinde Bakım Yapıldı */
    .status-yerinde-bakim-yapildi {
        background: linear-gradient(135deg, #2ecc71, #27ae60); /* Yeşil */
    }
    /* Fiyatta Anlaşılamadı */
    .status-fiyatta-anlasilamadi {
        background: linear-gradient(135deg, #7f8c8d, #95a5a6); /* Gri */
    }
    /* Ürün Garantili Çıktı */
    .status-urun-garantili-cikti {
        background: linear-gradient(135deg, #1abc9c, #16a085); /* Turkuaz */
    }
    /* Müşteriye Ulaşılamadı */
    .status-musteriye-ulasilamadi {
        background: linear-gradient(135deg, #e67e22, #d35400); /* Koyu Turuncu */
    }
    /* Müşteri İptal Etti */
    .status-musteri-iptal-etti {
        background: linear-gradient(135deg, #c0392b, #a03022); /* Koyu Kırmızı */
    }
    /* Parçası Atölyeye Alındı */
    .status-parcasi-atolye-alindi {
        background: linear-gradient(135deg, #34495e, #2c3e50); /* Koyu Mavi/Gri */
    }
    /* Cihaz Tamir Edilemiyor */
    .status-cihaz-tamir-edilemiyor {
        background: linear-gradient(135deg, #ecf0f1, #bdc3c7); /* Açık Gri */
        color: #34495e; /* Yazı rengi */
    }
    /* Haber Verecek */
    .status-haber-verecek {
        background: linear-gradient(135deg, #f1c40f, #f39c12); /* Sarı */
    }
    /* Yeniden Teknisyen Yönlendir */
    .status-yeniden-teknisyen-yonlendir {
        background: linear-gradient(135deg, #5e72e4, #738ae8); /* Açık Mor */
    }
    /* Atölyede Tamir Ediliyor */
    .status-atolyede-tamir-ediliyor {
        background: linear-gradient(135deg, #20c997, #198754); /* Cam Yeşili */
    }
    /* Teknisyen Yönlendir (Teslim Edilecek) */
    .status-teknisyen-yonlendir-teslim-edilecek {
        background: linear-gradient(135deg, #6f42c1, #845ef7); /* Koyu Mor */
    }
    /* Teslimata Hazır (Tamamlandı) */
    .status-teslimata-hazir-tamamlandi {
        background: linear-gradient(135deg, #007bff, #0056b3); /* Lacivert */
    }
    /* Cihaz Teslim Edildi */
    .status-cihaz-teslim-edildi {
        background: linear-gradient(135deg, #28a745, #1e7e34); /* Koyu Yeşil */
    }
    /* Şikayetçi */
    .status-sikayetci {
        background: linear-gradient(135deg, #dc3545, #b02a37); /* Koyu Kırmızı */
    }
    /* Servisi Sonlandır */
    .status-servisi-sonlandir {
        background: linear-gradient(135deg, #6c757d, #5a6268); /* Gri */
    }
    /* Cihaz Satışı Yapıldı */
    .status-cihaz-satisi-yapildi {
        background: linear-gradient(135deg, #fd7e14, #e66b0e); /* Koyu Turuncu */
    }
    /* Parça Takmak İçin Teknisyen Yönlendir */
    .status-parca-takmak-icin-teknisyen-yonlendir {
        background: linear-gradient(135deg, #6610f2, #7a23f5); /* Açık Mor */
    }
    /* Tahsilata Gönder */
    .status-tahsilata-gonder {
        background: linear-gradient(135deg, #6f42c1, #845ef7); /* Mor (Öncekiyle aynı gibi duruyor, farklılaştırılabilir) */
    }
    /* Parça Teslim Et */
    .status-parca-teslim-et {
        background: linear-gradient(135deg, #0d6efd, #0b5ed7); /* Canlı Mavi */
    }
    /* Cihaz Teslim Edildi (Parça Takıldı) */
    .status-cihaz-teslim-edildi-parca-takildi {
        background: linear-gradient(135deg, #198754, #126b41); /* Daha koyu yeşil */
    }
    /* Parça Hazır */
    .status-parca-hazir {
        background: linear-gradient(135deg, #ffc107, #d39e00); /* Altın Sarısı */
    }
    /* Nakliye Gönder */
    .status-nakliye-gonder {
        background: linear-gradient(135deg, #6f42c1, #845ef7); /* Mor */
    }
    /* Parça Siparişte */
    .status-parca-sipariste {
        background: linear-gradient(135deg, #0dcaf0, #0aa0c4); /* Açık Mavi */
    }
    /* Bayiye Gönder */
    .status-bayiye-gonder {
        background: linear-gradient(135deg, #4b0082, #6a0dad); /* İndigo */
    }
    /* Müşteri Para İade Edilecek */
    .status-musteri-para-iade-edilecek {
        background: linear-gradient(135deg, #ff0000, #cc0000); /* Kırmızı */
    }
    /* Müşteri Para İade Edildi */
    .status-musteri-para-iade-edildi {
        background: linear-gradient(135deg, #a52a2a, #8b2626); /* Kahverengi */
    }
    /* Fiyat Yükseltildi */
    .status-fiyat-yukseltildi {
        background: linear-gradient(135deg, #000000, #333333); /* Siyah */
    }
    /* Deneme Aşaması */
    .status-deneme-asamasi {
        background: linear-gradient(135deg, #ff6347, #ff4500); /* Domates Kırmızısı */
    }
    /* Konsinye Cihaz Ata */
    .status-konsinye-cihaz-ata {
        background: linear-gradient(135deg, #8a2be2, #9932cc); /* Mavi-Menekşe */
    }
    /* Konsinye Cihaz Geri Alındı */
    .status-konsinye-cihaz-geri-alindi {
        background: linear-gradient(135deg, #5f9ea0, #4682b4); /* Kadet Mavi */
    }
</style>


<div class="page-content servis-istatistik" id="stateStats">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])

        <div class="row pageDetail">
            <div class="col-12">
                <div class="table-modern">
                    <div class="card-header">
                        Servis Durum İstatistikleri
                        <div class="searchWrap float-end">
                                                        <div class="btn-group mb-2">
                                <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Filtrele <i class="mdi mdi-chevron-down"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <div class="item">
                                        <div class="row">
                                            <label class="col-sm-4">Tarih Aralığı:</label>
                                            <div class="col-sm-8">
                                                <input id="daterange" class="tarih-araligi" />
                                                <div class="tarihAraligi mt-2 mb-2">
                                                    <button id="lastYear" class="btn btn-sm btn-secondary">Son 1 Yıl</button>
                                                    <button id="lastMonth" class="btn btn-sm btn-secondary">Son 1 Ay</button>
                                                    <button id="lastWeek" class="btn btn-sm btn-secondary">Son 7 Gün</button>
                                                    <button id="yesterday" class="btn btn-sm btn-secondary">Dün</button>
                                                    <button id="today" class="btn btn-sm btn-secondary">Bugün</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="datatableStateStats" class="table table-hover mb-0">
                            <thead class="title">
                                <tr>
                                    <th><i class="fas fa-flag me-2"></i>Durum</th>
                                    <th><i class="fas fa-list-ol me-2"></i>Toplam Servis Sayısı</th>
                                    <th>İşlem</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    let today = moment();
    let start_date = moment().startOf('month');
    let end_date = today;

    // Kısayol butonları
    $('#lastYear').click(function() {
        updateRange(moment().subtract(1, 'year'), today);
    });
    $('#lastMonth').click(function() {
        updateRange(moment().subtract(1, 'month'), today);
    });
    $('#lastWeek').click(function() {
        updateRange(moment().subtract(7, 'days'), today);
    });
    $('#yesterday').click(function() {
        updateRange(moment().subtract(1, 'days'), moment().subtract(1, 'days'));
    });
    $('#today').click(function() {
        updateRange(today, today);
    });

    function updateRange(start, end) {
        $('#daterange').data('daterangepicker').setStartDate(start);
        $('#daterange').data('daterangepicker').setEndDate(end);
        table.draw();
    }

    $('#daterange').daterangepicker({
        startDate: start_date,
        endDate: end_date,
        locale: {
            format: 'DD-MM-YYYY',
            separator: ' - ',
            applyLabel: 'Uygula',
            cancelLabel: 'İptal',
            weekLabel: 'H',
            daysOfWeek: ['Pz', 'Pzt', 'Sal', 'Çrş', 'Prş', 'Cm', 'Cmt'],
            monthNames: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'],
            firstDay: 1
        }
    }, function(start, end) {
        table.draw();
    });

    var table = $('#datatableStateStats').DataTable({
        processing: true,
        serverSide: true,
        order: [[1, 'desc']],
        language: {
            paginate: {
                previous: "<i class='mdi mdi-chevron-left'>",
                next: "<i class='mdi mdi-chevron-right'>"
            },
            sEmptyTable: "Veri yok",
            sInfo: "Durum Sayısı: _TOTAL_",
            sInfoEmpty: "Kayıt yok",
            sSearch: "Durum Ara:",
            sZeroRecords: "Eşleşen kayıt bulunamadı"
        },
        ajax: {
            url: "{{ route('state.statistics', $tenant_id) }}",
            data: function(data) {
                data.from_date = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
                data.to_date = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
            }
        },
        columns: [
            { data: 'durum', name: 'durum' },
            { 
                data: 'toplam', 
                name: 'toplam',
                render: function(data) {
                    return `<span class="badge bg-primary">${data}</span>`;
                }
            },
            {
                data: 'durum_id',
                orderable: false,
                render: function(data, type, row) {
                    let from = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    let to = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    let url = `{{ url($tenant_id . '/servisler') }}?state_id=${data}&state_date1=${from}&state_date2=${to}`;
                    return `<a href="${url}" target="_blank" class="btn btn-action btn-sm"><i class="fas fa-eye me-1"></i>Servisleri Gör</a>`;
                }
            }
        ],
        drawCallback: function() {
            $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
        },
        dom: '<"top">rt<"bottom"ilp><"clear">',
        lengthMenu: [ [25, 50, 100, -1], [25, 50, 100, "Tümü"] ]
    });
});
</script>
@endsection

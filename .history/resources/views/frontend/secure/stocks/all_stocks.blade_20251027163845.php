@extends('frontend.secure.user_master')
@section('user')

  {{-- Daterangepicker için gerekli kütüphaneler --}}
  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

  @php 
      if ($firma->isOnTrial()) {
      $stockLimit = $firma->stokSayisi ?? null;
    } else {
      $stockLimit = $firma->plan()?->limits['stocks'] ?? null;
    }
    $stockAll = App\Models\Stock::where('firma_id', $firma->id)
      ->where('durum', '1')
      ->where('urunKategori', '!=', 3)
      ->count();
  @endphp

  <style>
    .servisDrop {
      transition: none !important;
      animation: none !important;
      transform: translate3d(1px, 2px, 0px) !important;
    }

    .card-stock {
      border: 1px solid rgba(0, 0, 0, .125) !important;
    }

    .card-stock-header {
      background-color: #f7f7f7 !important;
      border-bottom: 1px solid rgba(0, 0, 0, .125) !important;
      margin-bottom: 7px !important;
      padding: 4px 7px !important;
    }

    .card-stock-body {
      padding: 3px 7px !important;
    }

    @media (min-width: 768px) {
      .custom-modal-width {
        max-width: 340px;
        margin: 1.75rem auto;
      }

      .searchWrap .dropdown-menu {
        width: 321px !important;
      }
    }


    @media (max-width: 767px) {
      .stock-header-top{margin-top: 30px;}
      .custom-p {
        padding-left: 0px !important;
      }

      .searchWrap {
        margin-top: 0px !important;
      }

      .pageDetail .searchWrap .dropdown-menu .item {
        margin-bottom: 0px !important;
      }

      .pageDetail .searchWrap {
        width: 30% !important;
      }

      .pageDetail .searchWrap {
        margin-bottom: 0px !important;
      }

      div.dataTables_filter input {
        margin-left: 0 !important;
      }

      .dataTables_filter {
        margin-right: 0px !important;
      }

      #datatableStock_filter label {
        width: 100% !important;
      }

      .pageDetail .searchWrap .dropdown-menu {
        transform: translate3d(11px, 1px, 0px) !important;
        width: 100% !important;
        min-width: calc(79vw - 20px) !important;
        padding: 0px !important;
      }

      li.paginate_button.next,
      li.paginate_button.previous {
        font-size: 15px;
      }
      .btn-secondary {
    color: #fff !important;
    background-color: #5c636a !important;
    border-color: #565e64 !important;
    }
  }
/******************** Stok Limiti Uyarı Banner Stilleri *********************************/
.stock-limit-banner {
  background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
  color: white;
  padding: 6px 12px;
  border-radius: 5px;
  margin-bottom: 4px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  box-shadow: 0 1px 4px rgba(45, 55, 72, 0.3);
  position: relative;
  overflow: hidden;
}

.stock-limit-banner-content {
  display: flex;
  align-items: center;
  gap: 8px;
  flex: 1;
  position: relative;
  z-index: 1;
}

.stock-limit-banner-icon {
  font-size: 16px;
}

.stock-limit-banner-text {
  flex: 1;
  display: flex;
  align-items: center;
  gap: 6px;
  flex-wrap: wrap;
}

.stock-limit-banner-title {
  font-weight: 600;
  font-size: 12px;
  margin-bottom: 0px;
}

.stock-limit-banner-subtitle {
  font-size: 12px;
  opacity: 0.9;
}

.stock-limit-banner-actions {
  display: flex;
  align-items: center;
  gap: 6px;
  position: relative;
  z-index: 1;
}

.stock-limit-banner-link {
  background: rgba(255, 255, 255, 0.2);
  color: white;
  padding: 4px 10px;
  border-radius: 4px;
  text-decoration: none;
  font-weight: 500;
  font-size: 12px;
  transition: all 0.2s ease;
  border: 1px solid rgba(255, 255, 255, 0.3);
  backdrop-filter: blur(10px);
  white-space: nowrap;
}

.stock-limit-banner-link:hover {
  background: rgba(255, 255, 255, 0.3);
  color: white;
  transform: translateY(-1px);
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
}

.stock-limit-banner-close {
  background: transparent;
  border: none;
  color: white;
  font-size: 16px;
  cursor: pointer;
  padding: 2px 6px;
  opacity: 0.7;
  transition: all 0.2s ease;
  border-radius: 3px;
}

.stock-limit-banner-close:hover {
  opacity: 1;
  background: rgba(255, 255, 255, 0.1);
}

@media (max-width: 768px) {
  .stock-limit-banner {
    flex-direction: column;
    gap: 8px;
    padding: 10px;
    align-items: stretch;
  }

  .stock-limit-banner-content {
    width: 100%;
    flex-direction: column;
    align-items: flex-start;
  }

  .stock-limit-banner-text {
    flex-direction: column;
    align-items: flex-start;
    gap: 3px;
  }

  .stock-limit-banner-actions {
    width: 100%;
    justify-content: space-between;
  }
}
/******************** Stok Limiti Uyarı Banner Stilleri *********************************/
  </style>

  <div class="page-content">
    <div class="container-fluid stock-header-top">
      <div class="row pageDetail">
        <div class="col-12">
          <div class="card card-stock">
            <div class="card-header card-stock-header sayfaBaslik">
              Depo Stoklar
            </div>
            <div class="card-body card-stock-body">
              @if(!is_null($stockLimit) && $stockLimit != -1 && $stockAll >= $stockLimit)
  <div class="stock-limit-banner" id="stockLimitBanner">
    <div class="stock-limit-banner-content">
      <div class="stock-limit-banner-icon">
        <i class="fas fa-exclamation-triangle"></i>
      </div>
      <div class="stock-limit-banner-text">
        <div class="stock-limit-banner-title">Stok Limiti Doldu!</div>
        <div class="stock-limit-banner-subtitle">
          Maksimum stok limiti ({{ $stockLimit }}) ulaşıldı. Daha fazla stok eklemek için planınızı yükseltin.
        </div>
      </div>
    </div>
    <div class="stock-limit-banner-actions">
      <a href="{{ route('abonelikler', ['tenant_id' => $firma->id]) }}" class="stock-limit-banner-link">
        <i class="fas fa-arrow-up me-1"></i>Planı Yükselt
      </a>
      <button type="button" class="stock-limit-banner-close" onclick="closeStockBanner()">
        <i class="fas fa-times"></i>
      </button>
    </div>
  </div>
@endif
<div class="stock-buttons-container">
  @if(is_null($stockLimit) || $stockLimit == -1 || $stockAll < $stockLimit)
    <a data-bs-toggle="modal" data-bs-target="#addStockModal" class="btn btn-success btn-sm addStock">
      <i class="fas fa-plus"></i><span>Stok Kartı Ekle</span>
    </a>
  @else
    <a class="btn btn-success btn-sm addStock" disabled style="pointer-events: none; opacity: .4; cursor: default;">
      <i class="fas fa-plus"></i><span>Stok Kartı Ekle</span>
    </a>
  @endif
  
  <a href="javascript:void(0);" class="btn btn-warning btn-sm printStocks">
    <i class="fas fa-print"></i><span>Yazdır</span>
  </a>
  
  <a href="{{ route('consignmentdevice', $firma->id) }}" class="btn btn-info btn-sm supplierBtn">
    <i class="fas fa-industry"></i><span class="ms-1">Konsinye Cihazlar</span>
  </a>
</div>


              <!-- Filtre dropdown butonu -->
              <div class="searchWrap float-end">
                <div class="btn-group" id="depo_filtre">
                  <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Filtrele <i class="mdi mdi-chevron-down"></i>
                  </button>
                  <div class="dropdown-menu servisDrop  p-3" style="min-width: 250px;">

                    <!-- Raf -->
                    <div class="item">
                      <div class="row align-items-center">
                        <label class="col-sm-4 custom-p custom-p-r-m col-5 mb-0">Raf</label>
                        <div class="col-sm-8 custom-p custom-p-m custom-p-r-m col-7">
                          <select id="raf" class="form-select form-select-sm">
                            <option value="">Hepsi</option>
                            @foreach($rafListesi as $raf)
                              <option value="{{ $raf->id }}">{{ $raf->raf_adi }}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                    </div>

                    <!-- Marka -->
                    <div class="item">
                      <div class="row align-items-center">
                        <label class="col-sm-4 custom-p custom-p-r-m col-5 mb-0">Marka</label>
                        <div class="col-sm-8 custom-p custom-p-m custom-p-r-m col-7">
                          <select id="marka" class="form-select form-select-sm">
                            <option value="">Hepsi</option>
                            @foreach($markalar as $marka)
                              <option value="{{ $marka->id }}">{{ $marka->marka }}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                    </div>

                    <!-- Cihaz -->
                    <div class="item">
                      <div class="row align-items-center">
                        <label class="col-sm-4 custom-p custom-p-r-m col-5 mb-0">Cihaz</label>
                        <div class="col-sm-8 custom-p custom-p-m custom-p-r-m col-7">
                          <select id="cihaz" class="form-select form-select-sm">
                            <option value="">Hepsi</option>
                            @foreach($cihazlar as $cihaz)
                              <option value="{{ $cihaz->id }}">{{ $cihaz->cihaz }}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                    </div>

                    <!-- Personel -->
                    <div class="item">
                      <div class="row align-items-center">
                        <label class="col-sm-4 custom-p custom-p-r-m col-5 mb-0">Personel</label>
                        <div class="col-sm-8 custom-p custom-p-m custom-p-r-m col-7">
                          <select id="personel" class="form-select form-select-sm">
                            <option value="">Hepsi</option>
                            @foreach($personeller as $personel)
                              <option value="{{ $personel->user_id }}" {{ request('personel') == $personel->user_id ? 'selected' : '' }}>{{ $personel->name }}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                    </div>

                    {{-- YENİ: TARİH ARALIĞI FİLTRESİ --}}
                    <div class="item">
                      <div class="row">
                        <label class="col-sm-4 col-5 custom-p custom-p-r-m mb-0">Tarih Aralığı:</label>
                        <div class="col-sm-8 col-7 custom-p custom-p-m custom-p-r-m">
                          <input id="daterangeStock" class="form-control form-control-sm mb-2">
                          <div class="tarihAraligi d-flex flex-wrap gap-1">
                            <button id="lastYearStock" class="btn btn-sm btn-secondary">Son 1 Yıl</button>
                            <button id="lastMonthStock" class="btn btn-sm btn-secondary">Son 1 Ay</button>
                            <button id="lastWeekStock" class="btn btn-sm btn-secondary">Son 7 Gün</button>
                            <button id="yesterdayStock" class="btn btn-sm btn-secondary">Dün</button>
                            <button id="todayStock" class="btn btn-sm btn-secondary">Bugün</button>
                          </div>
                        </div>
                      </div>
                    </div>

                  </div>
                </div><!-- /btn-group -->
              </div>

              <table id="datatableStock" class="table table-bordered dt-responsive nowrap" style="width: 100%;">
                <thead class="title">
                  <tr>
                    <th style="width: 10px">ID</th>
                    <th>Tarih</th>
                    <th>Ürün Adı</th>
                    <th>Ürün Kodu</th>
                    <th>Satış Fiyatı</th>
                    <th>Adet</th>
                    <th>Raf</th>
                    <th>Marka/Cihaz</th>
                    <th style="width: 96px;">Düzenle</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>

              <!-- Toplam alanı -->
              <div class="tableToplamaAlani">
                <div class="row r1" style="display: flex; align-items: center; justify-content: center;">
                  <div class="sol"><strong>Toplam Bilgiler</strong></div>
                  <div class="sag">
                    <div class="tur t1"><span>Toplam Ürün: </span><span id="toplamAdet">0</span></div>
                    <div class="tur t4"><span>Depo Kazanç: </span><span id="toplamFiyat">0 ₺</span></div>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modallar -->
  <div id="addStockModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addStockModalLabel"
    aria-hidden="true">
    <div class="modal-dialog custom-modal-width">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title" id="addStockModalLabel">Stok Kartı Ekle</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">Yükleniyor...</div>
      </div>
    </div>
  </div>


  <div id="editStockModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editStockModalLabel"
    aria-hidden="true">
    {{-- modal-dialog sınıfı JS tarafından eklenecek --}}
    <div class="modal-dialog ">
      <div class="modal-content">
        {{-- İçerik AJAX ile buraya gelecek --}}
      </div>
    </div>
  </div>

  <style>

  </style>

  <script>
$(document).ready(function () {
  var firma_id = {{ $firma->id }};

  $(".addStock").click(function () {
    $.ajax({
      url: "/" + firma_id + "/stok-ekle/"
    }).done(function (data) {
      if ($.trim(data) === "-1") {
        location.reload(true);
      } else {
        $('#addStockModal').modal('show');
        $('#addStockModal .modal-body').html(data);
      }
    });
  });

  // Ana modal temizleme işlemi
  $("#addStockModal").on("hidden.bs.modal", function (e) {
    var modal = $(this);

    if (e.target === this) {
      setTimeout(function () {
        if (!$('.modal.show').length) {
          console.log("Modal içeriği temizleniyor");
          modal.find(".modal-body").html("");

          // Tüm backdrop'ları zorla kaldır
          $('.modal-backdrop').remove();

          // Body'den tüm modal sınıflarını kaldır ve style'ları sıfırla
          $('body').removeClass('modal-open').removeAttr('style');
          $('html').removeClass('modal-open').removeAttr('style');

        } else {
          console.log("Başka modal açık, temizleme yapılmıyor");
        }
      }, 100);
    }
  });

  // Edit modal için özel temizlik
  $('#editStockModal').on('hidden.bs.modal', function (e) {
    // Sadece bu modal kapatılıyorsa (event target kontrolü)
    if (e.target === this) {
      setTimeout(function () {
        // Hiç modal açık değilse backdrop temizliği yap
        if (!$('.modal.show').length) {
          $('.modal-backdrop').remove();
          $('body').removeClass('modal-open').removeAttr('style');
          $('html').removeClass('modal-open').removeAttr('style');
        }
      }, 100);
    }
  });

  // Tüm alt modal'lar için ortak temizlik
  $('#addBrandModal, #addDeviceTypeModal, #addCategoryModal, #addShelfModal, #addSupplierModal, #hareketEkleModal').on('hidden.bs.modal', function (e) {
    if (e.target === this) {
      setTimeout(function () {
        // Ana modal hala açıksa body'ye modal-open sınıfını geri ekle
        if ($('#addStockModal').hasClass('show') || $('#editStockModal').hasClass('show')) {
          $('body').addClass('modal-open');
        } else if (!$('.modal.show').length) {
          // Hiçbir modal açık değilse tam temizlik
          $('.modal-backdrop').remove();
          $('body').removeClass('modal-open').removeAttr('style');
          $('html').removeClass('modal-open').removeAttr('style');
        }
      }, 50);
    }
  });

  // Edit Stock Modal - Buton click event'i 
  $('#datatableStock').on('click', '.editStock', function(){
    var id = $(this).data('bs-id');
    var modal = $('#editStockModal'); 
   
    modal.find('.modal-dialog').removeClass('modal-xl').addClass('modal-xl');
    modal.find('.modal-content').html('<div class="modal-body text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Yükleniyor...</span></div></div>');
    
    // Modal'ı göster
    modal.modal('show');
    
    // Sunucudan veriyi çek
    $.ajax({
      url: "/" + firma_id + "/stok/duzenle/" + id,
      dataType: 'json',
      success: function(data){
        if($.trim(data.html) === "-1"){
          location.reload(true);
        } else {
          modal.find('.modal-content').html(data.html);
        }
      },
      error: function () {
        modal.find('.modal-content').html('<div class="modal-header"><h5 class="modal-title">Hata</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><div class="alert alert-danger">İçerik yüklenirken bir hata oluştu.</div></div>');
      }
    });
  });

  // Mobilde ve masaüstünde satırın boş alanlarına tıklayınca da açılsın
  $('#datatableStock tbody').on('click', 'tr', function(e) {
    var $target = $(e.target);
    
    if ($target.closest('.editStock').length > 0 ||
        $target.closest('.btn').length > 0 || 
        $target.closest('td').index() === 8) {
      return;
    }
    
    var id = $(this).find('.editStock').first().data('bs-id');
    
    if (id) {
      var modal = $('#editStockModal');
      
      modal.find('.modal-dialog').removeClass('modal-xl').addClass('modal-xl');
      modal.find('.modal-content').html('<div class="modal-body text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Yükleniyor...</span></div></div>');
      
      modal.modal('show');
      
      $.ajax({
        url: "/" + firma_id + "/stok/duzenle/" + id,
        dataType: 'json',
        success: function(data){
          if($.trim(data.html) === "-1"){
            location.reload(true);
          } else {
            modal.find('.modal-content').html(data.html);
          }
        },
        error: function() {
          modal.find('.modal-content').html('<div class="modal-header"><h5 class="modal-title">Hata</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><div class="alert alert-danger">İçerik yüklenirken bir hata oluştu.</div></div>');
        }
      });
    }
  });
});
</script>

<script>
// DataTable
$(document).ready(function () {
  // Dashboard'dan gelen URL parametrelerini oku
  const urlParams = new URLSearchParams(window.location.search);
  const dashboardStartDate = urlParams.get('dashboard_istatistik_tarih1');
  const dashboardEndDate = urlParams.get('dashboard_istatistik_tarih2');

  // Daterangepicker başlatma (varsayılan son 3 gün)
  let initialStockStartDate = dashboardStartDate ? moment(dashboardStartDate) : moment().subtract(2, 'days').startOf('day');
  let initialStockEndDate = dashboardEndDate ? moment(dashboardEndDate) : moment().endOf('day');

  $('#daterangeStock').daterangepicker({
    startDate: initialStockStartDate,
    endDate: initialStockEndDate,
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
  },
  function (start_date, end_date) {
    $('#daterangeStock').val(start_date.format('DD-MM-YYYY') + ' - ' + end_date.format('DD-MM-YYYY'));
    table.draw();
  });

  // Hızlı tarih filtreleme butonları
  $('#lastYearStock').on('click', function () {
    $('#daterangeStock').data('daterangepicker').setStartDate(moment().subtract(1, 'year'));
    $('#daterangeStock').data('daterangepicker').setEndDate(moment());
    table.draw();
  });

  $('#lastMonthStock').on('click', function () {
    $('#daterangeStock').data('daterangepicker').setStartDate(moment().subtract(1, 'month'));
    $('#daterangeStock').data('daterangepicker').setEndDate(moment());
    table.draw();
  });

  $('#lastWeekStock').on('click', function () {
    $('#daterangeStock').data('daterangepicker').setStartDate(moment().subtract(7, 'days'));
    $('#daterangeStock').data('daterangepicker').setEndDate(moment());
    table.draw();
  });

  $('#yesterdayStock').on('click', function () {
    $('#daterangeStock').data('daterangepicker').setStartDate(moment().subtract(1, 'days'));
    $('#daterangeStock').data('daterangepicker').setEndDate(moment().subtract(1, 'days'));
    table.draw();
  });

  $('#todayStock').on('click', function () {
    $('#daterangeStock').data('daterangepicker').setStartDate(moment());
    $('#daterangeStock').data('daterangepicker').setEndDate(moment());
    table.draw();
  });

  var table = $('#datatableStock').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: "{{ route('stocks', $firma->id) }}",
        data: function (d) {
          d.raf = $('#raf').val();
          d.marka = $('#marka').val();
          d.cihaz = $('#cihaz').val();
          d.personel = $('#personel').val();
          
          // Stok tarih aralığını ekle
          d.from_date_stock = $('#daterangeStock').data('daterangepicker').startDate.format('YYYY-MM-DD');
          d.to_date_stock = $('#daterangeStock').data('daterangepicker').endDate.format('YYYY-MM-DD');
          
          // Dashboard tarih parametreleri varsa ekle
          if (dashboardStartDate && dashboardEndDate) {

              d.dashboard_istatistik_tarih1 = dashboardStartDate;
              d.dashboard_istatistik_tarih2 = dashboardEndDate;
            }
          }
        },
        columns: [
          { data: 'id', name: 'id' },
          { data: 'created_at', name: 'created_at' },
          { data: 'urunAdi', name: 'urunAdi' },
          { data: 'urunKodu', name: 'urunKodu' },
          { data: 'toplamTutar', name: 'toplamTutar' },
          { data: 'adet', name: 'adet' },
          { data: 'raf_adi', name: 'raf_adi' },
          { data: 'marka_cihaz', name: 'marka_cihaz' },
          { data: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        dom: 'B<"top"f>rt<"bottom"i<"float-end"lp>><"clear">',
       buttons: [{
        extend: 'print',
        text: 'Yazdır',
        autoPrint: true,
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6, 7],
          format: {
            body: function (data, row, column, node) {
              // Önce string'e çevir ve temizle
              if (data === null || data === undefined) {
                return '';
              }
              
              // jQuery objesi veya DOM elementi ise text içeriğini al
              if (typeof data === 'object') {
                data = $(data).text();
              }
              
              // String'e çevir
              data = String(data);
              
              // Etiketleri temizle
              data = data.replace(/ID:/gi, '');
              data = data.replace(/Tarih:/gi, '');
              data = data.replace(/Ürün Adı:/gi, '');
              data = data.replace(/Ürün Kodu:/gi, '');
              data = data.replace(/Satış Fiyatı:/gi, '');
              data = data.replace(/Adet:/gi, '');
              data = data.replace(/Raf:/gi, '');
              data = data.replace(/Marka\/Cihaz:/gi, '');
              
              return data.trim();
            }
          }
        },
        customize: function (win) {
          $(win.document.head).find('style, link').remove();
          
          $(win.document.head).append(
            '<style>' +
            '.print-header { display: flex; justify-content: space-between; align-items: center; padding-bottom: 10px; }' +
            '.print-title { text-align: left; font-size: 18px; font-weight: bold; margin-bottom: 13px; }' +
            'table { width: 100%; border-collapse: collapse; }' +
            'table th, table td { border: 1px solid #ddd; padding: 8px; text-align: left; color: #000 !important; }' +
            'table thead { display: table-header-group !important; }' +
            'table tbody { display: table-row-group !important; }' +
            'table tbody td * { color: #000 !important; }' +
            'table tbody td span { color: #000 !important; background-color: transparent !important; }' +
            'a, a:link, a:visited, a:hover, a:active { color: #000 !important; text-decoration: none !important; }' +
            '.print-footer { margin-top: 15px; text-align: left; border-top: 1px solid #ddd; padding-top: 10px; }' +
            '.page-number-bottom { text-align: center; margin-top: 30px; font-size: 14px; color: #666; font-weight: bold; }' +
            '@page { margin: 5mm; }' +
            '</style>'
          );
          
          var printDate = moment().format('DD.MM.YYYY HH:mm');
          var totalRecords = table.page.info().recordsDisplay;
          var firmaAdi = '{{ $firma->firma_adi ?? "Firma Adı" }}';
          
          $(win.document.body).find('h1').remove();
          
          // Inline style'ları temizle
          $(win.document.body).find('table tbody td').each(function() {
            $(this).find('*').removeAttr('style');
          });
          
          var header = '<div class="print-header">' +
                      '  <span>' + printDate + '</span>' +
                      '  <span>' + firmaAdi.toUpperCase() + '</span>' +
                      '</div>';
          $(win.document.body).prepend(header);
          
          var title = '<div class="print-title">Depo Stoklar</div>';
          $(win.document.body).find('table').before(title);
          
          var footer = '<div class="print-footer">' +
                      '  <span>Listelenen Stok Sayısı: ' + totalRecords + ' - Tarih: ' + moment().format('DD/MM/YYYY') + '</span>' +
                      '</div>';
          $(win.document.body).find('table').after(footer);
          
          var pageInfo = '<div class="page-number-bottom">1/1</div>';
          $(win.document.body).append(pageInfo);
        }
      }],
        language: {
          sDecimal: ",",
          sEmptyTable: "Tabloda herhangi bir veri mevcut değil",
          sInfo: "Listelenen Ürün Sayısı: _TOTAL_ ",
          sInfoEmpty: "Kayıt yok",
          sInfoFiltered: "",
          sInfoPostFix: "",
          sInfoThousands: ".",
          sLengthMenu: "_MENU_ ",
          sLoadingRecords: "Yükleniyor...",
          sProcessing: "İşleniyor...",
          sSearch: "",
          sZeroRecords: "Eşleşen kayıt bulunamadı",
          oPaginate: {
            sFirst: "İlk",
            sLast: "Son",
            sNext: '<i class="fas fa-angle-right"></i>',
            sPrevious: '<i class="fas fa-angle-left"></i>'
          },
          oAria: {
            sSortAscending: ": artan sütun sıralamasını aktifleştir",
            sSortDescending: ": azalan sütun sıralamasını aktifleştir"
          },
          select: {
            rows: {
              _: "%d kayıt seçildi",
              0: "",
              1: "1 kayıt seçildi"
            }
          }
        },

        drawCallback: function (settings) {
          var api = this.api();
          var json = api.ajax.json();

          if (json && json.toplamAdet) {
            $('#toplamAdet').text(json.toplamAdet);
            $('#toplamFiyat').text(json.toplamFiyat);
          }

          $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
        },

        lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "Tümü"]],
        initComplete: function (settings, json) {
          var searchContainer = $('#datatableStock_filter');
          var searchInput = searchContainer.find('input');
          var filterWrapper = $('.searchWrap');
          var flexContainer = $('<div class="d-flex justify-content-end w-100"></div>');

          searchContainer.find('label').contents().filter(function () {
            return this.nodeType == 3;
          }).remove();


          // Arama kutusunu ve filtreyi sarmalamak için
          searchContainer.addClass('flex-grow-1');

          searchInput.addClass('w-100');
          searchInput.attr('placeholder', 'Stok Ara...');

          flexContainer.append(searchContainer);
          flexContainer.append(filterWrapper);

          $('#datatableStock_wrapper .top').append(flexContainer); 

          $('.searchWrap').css({ visibility: 'visible', opacity: 1 });

          $('.tableToplamaAlani').insertBefore('#datatableStock_wrapper .bottom');
          // --- DEĞİŞTİRİLEN BÖLÜM SONU ---

        }
      });

      // Filtreler değiştiğinde tabloyu yeniden çiz
      $('#raf, #marka, #cihaz, #personel').change(function () {
        table.draw();
      });
      // Yazdır butonu click event'i
      $('.printStocks').on('click', function(e) {
        e.preventDefault();
        table.button('.buttons-print').trigger();
      });

    });
  </script>
  <script>
    $(document).ready(function () {
      var dropdownContainer = $('#depo_filtre');
      var filterButton = dropdownContainer.find('.filtrele');
      dropdownContainer.on('show.bs.dropdown', function () {
        filterButton.html('Kapat <i class="mdi mdi-chevron-down"></i>');
      });
      dropdownContainer.on('hide.bs.dropdown', function () {
        filterButton.html('Filtrele <i class="mdi mdi-chevron-down"></i>');
      });
    });

    // Banner Kapatma Fonksiyonu
function closeStockBanner() {
  const banner = document.getElementById('stockLimitBanner');
  if (banner) {
    banner.classList.add('closing');
    setTimeout(() => {
      banner.style.display = 'none';
    }, 300);
  }
}
  </script>

@endsection
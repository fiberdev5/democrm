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

    /* Stok Limiti Uyarı Banner Stilleri */
    .stock-limit-banner {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 12px 20px;
      border-radius: 8px;
      margin-bottom: 15px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
      animation: slideDown 0.3s ease-out;
      position: relative;
      overflow: hidden;
    }

    .stock-limit-banner::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
      animation: shimmer 2s infinite;
    }

    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes shimmer {
      0% { transform: translateX(-100%); }
      100% { transform: translateX(100%); }
    }

    .stock-limit-banner.closing {
      animation: slideUp 0.3s ease-out forwards;
    }

    @keyframes slideUp {
      from {
        opacity: 1;
        transform: translateY(0);
        max-height: 100px;
      }
      to {
        opacity: 0;
        transform: translateY(-20px);
        max-height: 0;
        padding: 0;
        margin: 0;
      }
    }

    .stock-limit-banner-content {
      display: flex;
      align-items: center;
      gap: 12px;
      flex: 1;
      position: relative;
      z-index: 1;
    }

    .stock-limit-banner-icon {
      font-size: 24px;
      animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.1); }
    }

    .stock-limit-banner-text {
      flex: 1;
    }

    .stock-limit-banner-title {
      font-weight: 600;
      font-size: 15px;
      margin-bottom: 2px;
    }

    .stock-limit-banner-subtitle {
      font-size: 13px;
      opacity: 0.9;
    }

    .stock-limit-banner-actions {
      display: flex;
      align-items: center;
      gap: 10px;
      position: relative;
      z-index: 1;
    }

    .stock-limit-banner-link {
      background: rgba(255, 255, 255, 0.2);
      color: white;
      padding: 8px 18px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: 500;
      font-size: 14px;
      transition: all 0.2s ease;
      border: 1px solid rgba(255, 255, 255, 0.3);
      backdrop-filter: blur(10px);
    }

    .stock-limit-banner-link:hover {
      background: rgba(255, 255, 255, 0.3);
      color: white;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .stock-limit-banner-close {
      background: transparent;
      border: none;
      color: white;
      font-size: 20px;
      cursor: pointer;
      padding: 5px 10px;
      opacity: 0.7;
      transition: all 0.2s ease;
      border-radius: 4px;
    }

    .stock-limit-banner-close:hover {
      opacity: 1;
      background: rgba(255, 255, 255, 0.1);
      transform: rotate(90deg);
    }

    @media (max-width: 768px) {
      .stock-limit-banner {
        flex-direction: column;
        gap: 12px;
        padding: 15px;
      }

      .stock-limit-banner-content {
        width: 100%;
      }

      .stock-limit-banner-actions {
        width: 100%;
        justify-content: space-between;
      }

      .stock-limit-banner-text {
        font-size: 14px;
      }

      .stock-limit-banner-title {
        font-size: 14px;
      }

      .stock-limit-banner-subtitle {
        font-size: 12px;
      }
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

              {{-- Stok Limiti Uyarı Banner'ı --}}
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
                            @foreach($personels as $personel)
                              <option value="{{ $personel->id }}">{{ $personel->ad }} {{ $personel->soyad }}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                    </div>

                    <!-- Tarih Aralığı -->
                    <div class="item">
                      <div class="row align-items-center">
                        <label class="col-sm-4 custom-p custom-p-r-m col-5 mb-0">Tarih</label>
                        <div class="col-sm-8 custom-p custom-p-m custom-p-r-m col-7">
                          <input type="text" id="dateRangeStock" class="form-control form-control-sm"
                            placeholder="Tarih Seçin">
                        </div>
                      </div>
                    </div>

                    <!-- Temizle butonu -->
                    <div class="text-end mt-2">
                      <button id="clearFilters" class="btn btn-secondary btn-sm">Temizle</button>
                    </div>

                  </div>
                </div>
              </div>

              <div class="col-lg-12 col-md-12 order-last">
                <table id="datatableStock" class="table table-striped table-bordered dt-responsive nowrap border"
                  style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Tarih</th>
                      <th>Ürün Adı</th>
                      <th>Ürün Kodu</th>
                      <th>Satış Fiyatı</th>
                      <th>Adet</th>
                      <th>Raf</th>
                      <th>Marka/Cihaz</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>

                <div class="tableToplamaAlani" style="display:none;">
                  <div class="row">
                    <div class="col-6 text-start">
                      <strong>Toplam Adet:</strong> <span id="toplamAdet">0</span>
                    </div>
                    <div class="col-6 text-end">
                      <strong>Toplam Fiyat:</strong> <span id="toplamFiyat">0 TL</span>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
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

    $(document).ready(function () {

      $('#dateRangeStock').daterangepicker({
        autoUpdateInput: false,
        locale: {
          format: 'DD/MM/YYYY',
          applyLabel: 'Uygula',
          cancelLabel: 'İptal',
          customRangeLabel: 'Özel Aralık',
          daysOfWeek: ['Pz', 'Pt', 'Sa', 'Ça', 'Pe', 'Cu', 'Ct'],
          monthNames: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim',
            'Kasım', 'Aralık'
          ],
          firstDay: 1
        },
        ranges: {
          'Bugün': [moment(), moment()],
          'Dün': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Son 7 Gün': [moment().subtract(6, 'days'), moment()],
          'Son 30 Gün': [moment().subtract(29, 'days'), moment()],
          'Bu Ay': [moment().startOf('month'), moment().endOf('month')],
          'Geçen Ay': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
      });

      $('#dateRangeStock').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        dashboardStartDate = picker.startDate.format('YYYY-MM-DD');
        dashboardEndDate = picker.endDate.format('YYYY-MM-DD');
        table.draw();
      });

      $('#clearFilters').click(function () {
        $('#raf').val('');
        $('#marka').val('');
        $('#cihaz').val('');
        $('#personel').val('');
        $('#dateRangeStock').val('');
        dashboardStartDate = null;
        dashboardEndDate = null;
        table.draw();
      });

      var dashboardStartDate = null;
      var dashboardEndDate = null;
      var table = $('#datatableStock').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: "{{ route('stockData', $firma->id) }}",
          data: function (d) {
            d.raf = $('#raf').val();
            d.marka = $('#marka').val();
            d.cihaz = $('#cihaz').val();
            d.personel = $('#personel').val();
            
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
  </script>

@endsection
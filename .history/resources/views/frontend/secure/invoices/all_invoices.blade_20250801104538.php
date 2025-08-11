@extends('frontend.secure.user_master')
@section('user')

<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<div class="page-content" id="passwords">
  <div class="container-fluid">
    <div class="row pageDetail">
      <div class="col-12">
        <div class="card">
          <div class="card-header sayfaBaslik">
            Faturalar
          </div>
          <div class="card-body">
            <table id="datatableInvoice" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                <a class="btn btn-success btn-sm addInvoice" data-bs-toggle="modal" data-bs-target="#addInvoiceModal"><i class="fas fa-plus"></i><span>Fatura Ekle</span></a> 
                <a class="btn btn-danger btn-sm tevkifatHesapla" data-bs-toggle="modal" data-bs-target="#tevkifatHesaplamaModal"><i class="fas fa-calculator"></i> <span>Tevkifat Hesaplama</span></a>
              <div class="searchWrap float-end">
                <div class="btn-group mb-2 ">
                  <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Filtrele <i class="mdi mdi-chevron-down"></i>
                  </button>
                  <div class="dropdown-menu">
                    <div class="item">
                      <div class="row form-group">
                        <label class="col-sm-4">Müşteri<span style="font-weight: bold; color: red;">*</span></label>
                        <div class="col-md-8">
                          <input id="search" type="text" name="adSoyad" class="form-control musteriAdSoyad" autocomplete="off" placeholder="Müşteri Adı" >
                          <input type="hidden" name="musteri" class="mus_id" id="alici"/>
                          <ul id="result" style="margin: 0; padding: 0"></ul>
                        </div>
                      </div>
                    </div>
                    <div class="item">
                      <div class="row">
                        <label class="col-sm-4">Durum:</label>
                        <div class="col-sm-8">
                          <select name="durum" id="durum" class="form-select">
                            <option value="">Hepsi</option>
                              <option value="sent">Gönderildi</option>
                              <option value="draft">Beklemede</option>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="item">
                      <div class="row">
                        <label class="col-sm-4">Tarih Aralığı:</label>
                        <div class="col-sm-8">
                          <input id="daterange" class="tarih-araligi">
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
                </div><!-- /btn-group -->
              </div>
              <thead class="title">
                <tr>
                  <th style="width: 10px">ID</th>
                  <th style="width: 10px">Tarih</th>
                  <th>F.No</th>
                  <th style="width: 250px">Müşteri Adı</th>
                  <th>G.Toplam</th>
                  <th>Durum</th>
                  <th data-priority="1" style="width: 96px;">Düzenle</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>

            <div class="tableToplamaAlani kasaToplamaAlani">
              <div class="row r1">
                <div class="sol"><strong>Toplam </strong></div>
                <div class="sag">
                  <div class="tur t1 toplamNakitTL"><span>Nakit:  </span></div>
                  <div class="tur t2 toplamHavaleTL"><span>EFT/Havale:  </span></div>
                  <div class="tur t3 toplamKartTL"><span>Kredi Kartı:  </span></div>
                  <div class="tur t4 toplamTutarTL1"><span>Toplam:  </span></div>
                </div>
              </div>

              <div class="row r2">
                <div class="sol"><strong>Toplam KDV</strong></div>
                <div class="sag">
                  <div class="tur t1 kdvNakitTL"><span>Nakit:  </span></div>
                  <div class="tur t2 kdvHavaleTL"><span>EFT/Havale:  </span></div>
                  <div class="tur t3 kdvKartTL"><span>Kredi Kartı:  </span></div>
                  <div class="tur t4 toplamTutarTL2"><span>Toplam:  </span></div>
                </div>
              </div>

              <div class="row r4">
                <div class="sol"><strong>Genel Toplam </strong></div>
                <div class="sag">
                <div class="tur t1 genelNakitTL"><span>Nakit:  </span></div>
                  <div class="tur t2 genelHavaleTL"><span>EFT/Havale:  </span></div>
                  <div class="tur t3 genelKartTL"><span>Kredi Kartı: </span></div>
                  <div class="tur t4 toplamTutarTL3"><span>Toplam: </span></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div> <!-- end col -->
    </div> <!-- end row -->
  </div>
</div>

<div class="modal fade" id="tevkifatHesaplamaModal" tabindex='-1' role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h6 class="modal-title">Tevkifat Hesaplama</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <!-- Modal body -->
      <div id="response" class="modal-body" style="padding: 10px;">
        <div class="row form-group">
          <label class="col-sm-4 ">KDV Oranı (%):</label>
          <div class="col-sm-8">
            <select class="form-control kdvOrani" style="font-weight: 500;">
              <option value="20" selected>%20</option>
              <option value="10">%10</option>
              <option value="8">%8</option>
              <option value="1">%1</option>
              <option value="0">%0</option>
            </select>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-sm-4 ">Tevkifat Oranı:</label>
          <div class="col-sm-8">
            <select class="form-control tevkifatOrani" style="font-weight: 500;">
              <option value="1">Yok</option>
              <option value="0.5">%50 (5/10)</option>
              <option value="0.9">%90 (9/10)</option>
              <option value="1.0">%100 (10/10)</option>
            </select>
          </div>
        </div>

        <div class="row form-group">
          <div class="col-md-4"><label style="color:red">KDV Dahil Tutar</label></div>
          <div class="col-md-8">
            <input class="form-control tutar" type="text" placeholder="0.00" style="width: calc(100% - 25px);display: inline-block;margin-right: -10px;font-weight: 500;">
            <input class="form-control" type="text" value="TL" disabled style="width: 30px;display: inline-block;background: #fff;border-left: 0;border-top-left-radius: 0;border-bottom-left-radius: 0;text-align: center;">
          </div>
        </div>

        <div class="row form-group">
          <div class="col-md-4"><label style="color:green">Hesaplanan KDV</label></div>
          <div class="col-md-8">
            <input type="text" class="form-control hesaplananKdv" disabled style="font-weight: 500; background: #e5e5e5;">
          </div>
        </div>

        <div class="row form-group">
          <div class="col-md-4"><label style="color:#ff9800">Tevkifat Tutarı</label></div>
          <div class="col-md-8">
            <input type="text" class="form-control tevkifatTutar" disabled style="font-weight: 500; background: #e5e5e5;">
          </div>
        </div>

        <div class="row form-group">
          <div class="col-md-4"><label style="color:red">KDV Hariç Tutar</label></div>
          <div class="col-md-8">
            <input type="text" class="form-control sonuc" disabled style="font-weight: 500; background: #e5e5e5;">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
        
<!-- add modal content -->
<div id="addInvoiceModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" style="max-width: 830px;">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" id="myModalLabel">Fatura Ekle</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      Yükleniyor...
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<!-- edit modal content -->
<div id="editInvoiceModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" style="max-width: 830px;">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" id="myModalLabel">Fatura Düzenle</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Yükleniyor...
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- edit modal content -->
<div id="InvoiceModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="padding-top: 50px;background: rgba(0, 0, 0, 0.50);">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" id="myModalLabel">Earşiv Yükle</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->



<script>
  var getUrlParameter = function getUrlParameter(sParam) {
      var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;
      for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
      }
    };

    var mid = getUrlParameter('did');
    var firma_id = {{$firma->id}};
    if(mid){
      $.ajax({
        url: "/" + firma_id + "/fatura/duzenle/"+ mid
      }).done(function(data) { 
        if($.trim(data)==="-1"){
          window.location.reload(true);
        }else{
          $('#editInvoiceModal').modal('show');
          $('#editInvoiceModal .modal-body').html(data);
        }
      });
    }
</script>

<script type="text/javascript">
$(document).ready(function(){
  $(".addInvoice").click(function(){
    var firma_id = {{$firma->id}};
    $.ajax({
      url: "/" + firma_id + "/fatura/ekle/"
    }).done(function(data) {
      if ($.trim(data) === "-1") {
        window.location.reload(true);
      } else {
        $('#addInvoiceModal').modal('show');
        $('#addInvoiceModal .modal-body').html(data);
      }
    });
  });
  $("#addInvoiceModal").on("hidden.bs.modal", function() {
      $('#addInvoiceModal .modal-body').html("");
    });
});
</script>

<script type="text/javascript">
$(document).ready(function(){
    $('#datatableInvoice').on('click', '.editInvoice', function(e){
        var id = $(this).attr("data-bs-id");
        var firma_id = {{$firma->id}};
        $.ajax({
            url: "/" + firma_id + "/fatura/duzenle/" + id
        }).done(function(data) {
            if ($.trim(data) === "-1") {
                window.location.reload(true);
            } else {
                $('#editInvoiceModal').modal('show');
                $('#editInvoiceModal .modal-body').html(data);
            }
        });
    });
    $("#editInvoiceModal").on("hidden.bs.modal", function() {
      $(".modal-body").html("");
    });
});
</script>
<script>
  function hesaplaKdv() {
    const tutar = parseFloat(document.querySelector('.tutar').value.replace(',', '.')) || 0;
    const kdvOrani = parseFloat(document.querySelector('.kdvOrani').value) || 0;
    const tevkifatOrani = parseFloat(document.querySelector('.tevkifatOrani').value) || 1;

    const kdvHaricTutar = tutar / (1 + kdvOrani / 100);
    const kdvTutar = tutar - kdvHaricTutar;
    const tevkifatTutar = kdvTutar * (1 - tevkifatOrani);

    document.querySelector('.sonuc').value = kdvHaricTutar.toFixed(2);
    document.querySelector('.hesaplananKdv').value = kdvTutar.toFixed(2);
    document.querySelector('.tevkifatTutar').value = tevkifatTutar.toFixed(2);
  }

  // input/select değiştikçe çalışsın
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelector('.tutar').addEventListener('input', hesaplaKdv);
    document.querySelector('.kdvOrani').addEventListener('change', hesaplaKdv);
    document.querySelector('.tevkifatOrani').addEventListener('change', hesaplaKdv);
  });
</script>

<script>
  var musteriListesi = @json($musteriler);

  function turkceKucukHarfeDonustur(text) {
    if (!text) return '';

    return text.replace(/Ğ/g, 'ğ')
               .replace(/Ü/g, 'ü')
               .replace(/Ş/g, 'ş')
               .replace(/İ/g, 'i')
               .replace(/Ö/g, 'ö')
               .replace(/Ç/g, 'ç')
               .toLowerCase();
  }

  $(document).ready(function () {
    $('#search').keyup(function () {
      $('#result').html('');
      var searchField = turkceKucukHarfeDonustur($('#search').val());
        var veriler = 'musteriGetir=' + searchField;
        if (searchField.length > 2) {
          var filteredMusteriler = musteriListesi.filter(function (musteri) {
            var adiKucukHarf = turkceKucukHarfeDonustur(musteri.m_adi);
            var firmaAdiKucukHarf = turkceKucukHarfeDonustur(musteri.firma_adi);
            return adiKucukHarf.includes(searchField) || firmaAdiKucukHarf.includes(searchField);
          });
          $.each(filteredMusteriler, function (key, value) {
            var tip = value.musteriTipi == "1" ? "Bireysel" : "Kurumsal";
            $('#result').append('<li class="list-group-item link-class" data-id="' + value.id + '" data-adSoyad="' + value.m_adi + '" data-firmaAdi="' + value.firma_adi + '" data-tel="' + value.telefon + '" data-adres="' + value.adres + '" ><span style="font-weight:500;">Ad Soyad: </span>' + value.m_adi + ' (' + value.firma_adi + ')<br><span style="font-weight:500;">Telefon: </span>' + value.telefon + '<br><span style="font-weight:500;">Adres: </span>' + value.adres + '</li>');
          });
        }
      });

      $('#result').on('click', 'li', function () {

        $('#result .li:selected').removeAttr('selected');

        var click_id = $(this).attr('data-id');
        var click_adSoyad = $(this).attr('data-adSoyad');

        $('#alici').attr('value', click_id);
        $('#search').attr('value', click_id);
        $('.musteriAdSoyad').val(click_adSoyad);
        $("#result").html('');
        return false;
        });
    });
</script>

<script>
  $(document).ready(function () {
    // Tarih aralığı seçenekleri
    var lastYear = moment().subtract(1, 'year');
    var lastMonth = moment().subtract(1, 'month');
    var lastWeek = moment().subtract(7, 'days');
    var yesterday = moment().subtract(1, 'days');
    var today = moment();

    // Butonları oluştur ve tarih aralığını güncelle
    $('#lastYear').on('click', function() {
        $('#daterange').data('daterangepicker').setStartDate(lastYear);
        $('#daterange').data('daterangepicker').setEndDate(today);
        filterData();
    });

    $('#lastMonth').on('click', function() {
        $('#daterange').data('daterangepicker').setStartDate(lastMonth);
        $('#daterange').data('daterangepicker').setEndDate(today);
        filterData();
    });

    $('#lastWeek').on('click', function() {
        $('#daterange').data('daterangepicker').setStartDate(lastWeek);
        $('#daterange').data('daterangepicker').setEndDate(today);
        filterData();
    });

    $('#yesterday').on('click', function() {
        $('#daterange').data('daterangepicker').setStartDate(yesterday);
        $('#daterange').data('daterangepicker').setEndDate(yesterday);
        filterData();
    });

    $('#today').on('click', function() {
        $('#daterange').data('daterangepicker').setStartDate(today);
        $('#daterange').data('daterangepicker').setEndDate(today);
        filterData();
    });
    
    // Filtreleme fonksiyonu
    function filterData() {
        $('#datatableInvoice').DataTable().draw();
    }
});
</script>

<script>
$(document).ready(function () {

  var start_date = '01-01-2020';
  var end_date = moment().add(1, 'day');

    $('#daterange').daterangepicker({
      startDate : start_date,
      endDate : end_date,
      opens: 'right',
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

    function(start_date, end_date){
      $('#daterange').html(start_date.format('DD-MM-YYYY') + '-' + end_date.format('DD-MM-YYYY'));
      table.draw();
    });

    var table = $('#datatableInvoice').DataTable({
      processing: true,
      serverSide: true,
      language: {
        paginate: {
          previous: "<i class='mdi mdi-chevron-left'>",
          next: "<i class='mdi mdi-chevron-right'>"
        }
      },
      ajax: {
        url: "{{ route('all.invoices', $firma->id) }}",
        data: function(data) {
          data.search = $('input[type="search"]').val();
          data.from_date = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
          data.to_date = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
          data.musteri = $('.mus_id').val();
          data.durum = $('#durum').val();
        }
      },
      'columns': [
        { data: 'id'},
        { data: 'faturaTarihi' },
        { data: 'faturaNumarasi' },
        { data: 'mid' },
        { data: 'genelToplam' },
        { data: 'odemeDurum' },
        { data: 'actions'}           
      ],
      
      drawCallback: function() {
        $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
      },
      "order": [[1, 'desc']],
      "columnDefs": [
        {
          "targets": 0,
          "className": "gizli"
        }
      ],
      "oLanguage": {
        "sDecimal":        ",",
        "sEmptyTable":     "Tabloda herhangi bir veri mevcut değil",
        "sInfo":           "Fatura Sayısı: _TOTAL_",
        "sInfoEmpty":      "Kayıt yok",
        "sInfoFiltered":   "",
        "sInfoPostFix":    "",
        "sInfoThousands":  ".",
        "sLengthMenu":     "_MENU_",
        "sLoadingRecords": "Yükleniyor...",
        "sProcessing":     "İşleniyor...",
        "sSearch":         "Ara:",
        "sZeroRecords":    "Eşleşen kayıt bulunamadı",
        "oPaginate": {
          "sFirst":    "İlk",
          "sLast":     "Son",
          "sNext":     '<i class="fas fa-angle-double-right"></i>',
          "sPrevious": '<i class="fas fa-angle-double-left"></i>'
        },
        "oAria": {
          "sSortAscending":  ": artan sütun sıralamasını aktifleştir",
          "sSortDescending": ": azalan sütun sıralamasını aktifleştir"
        },
        "select": {
              "rows": {
                  "_": "%d kayıt seçildi",
                  "0": "",
                  "1": "1 kayıt seçildi"
              }
          }
          },
          dom: '<"top"f>rt<"bottom"ilp><"clear">',
          "lengthMenu": [ [25, 50, 100, -1], [25, 50, 100, "Tümü"] ],
  });

  $('#result').on('click', 'li', function () {
    var selectedCustomerId = $(this).attr('data-id');
    table.column('musteri:name').search(selectedCustomerId).draw();
  });

  $('#durum').change(function(){
    table.draw();        
  });

  table.on('draw.dt', function () {
        updateValues();
    });

    var updateValues = function() {
      var startDate = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
      var endDate = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
      var musteri = $('.mus_id').val();
      var durum = $('#durum').val();

        $.ajax({
            url: '/{{$firma->id}}/fatura-sonuc',
            method: 'GET',
            data: {
              from_date: startDate,
              to_date: endDate,
              musteri:musteri,
              durum:durum,
            },
            
            success: function(response) {
              
                $('.toplamNakitTL').html('<span>Nakit:</span> ' + response.toplamNakitTL);
                $('.toplamHavaleTL').html('<span>EFT/Havale:</span> ' + response.toplamHavaleTL);
                $('.toplamKartTL ').html('<span>Kredi Kartı:</span> ' + response.toplamKartTL);
                $('.toplamTutarTL1 ').html('<span>Toplam:</span> ' + response.toplamTutarTL1);
                $('.kdvNakitTL ').html('<span>Nakit:</span> ' + response.kdvNakitTL);
                $('.kdvHavaleTL').html('<span>EFT/Havale:</span> ' + response.kdvHavaleTL);
                $('.kdvKartTL ').html('<span>Kredi Kartı:</span> ' + response.kdvKartTL);
                $('.toplamTutarTL2 ').html('<span>Toplam:</span> ' + response.toplamTutarTL2);
                $('.genelNakitTL ').html('<span>Nakit:</span> ' + response.genelNakitTL);
                $('.genelHavaleTL ').html('<span>EFT/Havale:</span> ' + response.genelHavaleTL);
                $('.genelKartTL').html('<span>Kredi Kartı:</span> ' + response.genelKartTL);
                $('.toplamTutarTL3 ').html('<span>Toplam:</span> ' + response.toplamTutarTL3);
              
              },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    };
    // Sayfa yüklendiğinde updateValues fonksiyonunu çağır
    updateValues();

    // DataTables çizeceği zaman updateValues fonksiyonunu çağır
    table.on('draw.dt', function () {
        updateValues();
    });

    // Filter butonuna tıklama olayında updateValues fonksiyonunu çağır
    $('#filterButton').click(function() {
        updateValues();
    });

    // Date range picker değiştiğinde updateValues fonksiyonunu çağır
    $('#daterange').on('apply.daterangepicker', function(ev, picker) {
        updateValues();
    });
});
</script>
@endsection

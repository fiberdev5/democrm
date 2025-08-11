@extends('frontend.secure.user_master')
@section('user')

<meta name="csrf-token" content="{{ csrf_token() }}">

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script> 

<div class="page-content" id="cash_transactions">
  <div class="container-fluid">
    <div class="row pageDetail">
      <div class="col-12">
        <div class="card">
          <div class="card-header sayfaBaslik">Kasa Hareketleri</div>
          <div class="card-body">
            <table id="datatableKasa" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
              @if(Auth::user()->can('Kasa Hareketi Ekleyebilir'))
                <a class="btn btn-success btn-sm addCashTransactions" data-bs-toggle="modal" data-bs-target="#addCashTransactionsModal"><i class="fas fa-plus"></i> <span>Kasa Hareketi Ekle</span></a>
              @endif  
              <a class="btn btn-danger btn-sm tevkifatHesapla" data-bs-toggle="modal" data-bs-target="#tevkifatHesaplamaModal"><i class="fas fa-calculator"></i> <span>Tevkifat Hesaplama</span></a>  
              <a class="btn btn-warning btn-sm statistics" data-bs-toggle="modal" data-bs-target="#cashTransactionStatisticsModal"><i class=" ri-pie-chart-fill"></i> <span>Kasa İstatistikleri</span></a>
              <button type="submit" id="kasaArama" class="btn btn-sm btn-dark searchBtn kasaArama float-end"><i class="fas fa-search"></i></button>

              <div class="searchWrap float-end">
                <div class="btn-group mb-2 ">
                  <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Filtrele <i class="mdi mdi-chevron-down"></i>
                  </button>
                  <div class="dropdown-menu">
                    <div class="item">
                      <div class="row">
                        <label class="col-sm-4">Ödeme Yönü:</label>
                        <div class="col-sm-8">
                          <select name="odeme_yonu" id="odemeYonu" class="form-select">
                            <option value="">Hepsi</option>
                            <option value="1">Gelen Ödeme(Borç)</option>
                            <option value="2">Giden Ödeme(Alacak)</option>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="item">
                      <div class="row">
                        <label class="col-sm-4">Ödeme Türü:</label>
                        <div class="col-sm-8">
                          <select name="odeme_turu" id="odemeTuru" class="form-select">
                            <option value="">Hepsi</option>
                            @foreach($payment_types as $type)
                              <option value="{{$type->id}}">{{$type->tur}}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="item">
                      <div class="row">
                        <label class="col-sm-4">Ödeme Şekli:</label>
                        <div class="col-sm-8">
                          <select name="odeme_sekil" id="odemeSekil" class="form-select">
                            <option value="">Hepsi</option>
                            @foreach($payment_methods as $method)
                              <option value="{{$method->id}}">{{$method->odeme_sekli}}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="item">
                      <div class="row">
                        <label class="col-sm-4">Personel:</label>
                        <div class="col-sm-8">
                          <select name="staff" id="staff" class="form-select">
                            <option value="">Hepsi</option>
                            @foreach($personel as $person)
                              <option value="{{$person->user_id}}">{{$person->name}}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                    </div>
        
                    <div class="item">
                      <div class="row">
                        <label class="col-sm-4">Tarih Aralığı:</label>
                        <div class="col-sm-8">
                          <input id="daterange" class="tarih-araligi" >
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
              <div id="kasaFilter" class="kasaFilter float-end"><label>
                <input id="search" type="text" name="adSoyad" class="form-control musteriAdSoyad" autocomplete="off" placeholder="Müşteri Adı" >
                <input type="hidden" name="musteri" class="mid" id="alici"/>
                <ul id="result" style="margin: 0; padding: 0"></ul></label>
              </div>
              <thead class="title">
                <tr>
                  <th style="width: 10px">ID</th>
                  <th data-priority="2" style="width: 10px">Tarih</th>
                  <th style="width: 10px;">Türü</th>
                  <th>Açıklama</th>
                  <th style="width: 10px;">Şekli</th>
                  <th style="width: 90px;">Borç(Gelen)</th>
                  <th style="width: 90px;">Alacak(Giden)</th>
                  <th style="width: 90px;">Bakiye(Toplam)</th>
                  <th data-priority="1" style="width: 96px;">Düzenle</th>
                </tr>
              </thead>
              <tbody></tbody> 
            </table>
            
            <div class="tableToplamaAlani kasaToplamaAlani">
              <div class="row r1">
                <div class="sol"><strong>Borç</strong></div>
                <div class="sag">
                  <div class="tur t1 gelenNakitTL"><span>Nakit: </span></div>
                  <div class="tur t2 gelenHavaleTL"><span>EFT/Havale: </span></div>
                  <div class="tur t3 gelenKartTL"><span>Kredi Kartı: </span></div>
                  <div class="tur t4 gelenToplamTL"><span>Toplam: </span></div>
                </div>
              </div>  
              <div class="row r2">
                <div class="sol"><strong>Alacak</strong></div>
                <div class="sag">
                  <div class="tur t1 gidenNakitTL"><span>Nakit: </span></div>
                  <div class="tur t2 gidenHavaleTL"><span>EFT/Havale: </span></div>
                  <div class="tur t3 gidenKartTL"><span>Kredi Kartı: </span></div>
                  <div class="tur t4 gidenToplamTL"><span>Toplam: </span></div>
                </div>
              </div>
              <div class="row r4">
                <div class="sol"><strong>Bakiye</strong></div>
                <div class="sag">
                  <div class="tur t1 genelToplamTL"><span>Toplam: </span></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div> <!-- end col -->
    </div> <!-- end row -->
  </div>
</div>
        
<!-- add modal content -->
<div id="addCashTransactionsModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" id="myModalLabel">Kasa Hareketi Ekle</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Yükleniyor...
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

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
        <div class="row">
          <label class="col-sm-4 col-form-label">KDV Oranı (%):</label>
          <div class="col-sm-8">
            <input name="hesap" class="form-control kdvOrani" type="text" value="20" disabled style="font-weight: 500;">
          </div>
        </div>
          
        <div class="row form-group">
          <div class="col-md-4 rw1"><label class="tutarLabel" style="color:red">KDV Dahil Tutar</label></div>
          <div class="col-md-8 rw2">
            <input class="form-control tutar" type="text" placeholder="0.00" style="width: calc(100% - 25px);display: inline-block;margin-right: -10px;font-weight: 500;">
            <input class="form-control" type="text" value="TL" disabled="" style="width: 30px;display: inline-block;background: #fff;border-left: 0;border-top-left-radius: 0; border-bottom-left-radius: 0;text-align: center;">
          </div>
        </div>
          
        <div class="row form-group">
          <div class="col-md-4 rw1"><label style="color:red">Birim Tutar </label></div>
          <div class="col-md-8 rw2">
            <input type="text" onkeyup="sayiKontrol(this)" class="form-control sonuc" placeholder="0.00" style="width: calc(100% - 25px);display: inline-block;margin-right: -10px;font-weight: 500;background: #e5e5e5;" disabled>
            <input type="text" onkeyup="sayiKontrol(this)" class="form-control" value="TL" disabled="" style="width: 30px;display: inline-block;background: #fff;border-left: 0;border-top-left-radius: 0; border-bottom-left-radius: 0;text-align: center;">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- add modal content -->
<div id="cashTransactionStatisticsModal" class="modal fade bs-example-modal-xl" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" id="myModalLabel">Kasa İstatistikleri</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="padding: .5rem">
        Yükleniyor...
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- edit modal content -->
<div id="editCashTransactionsModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" id="myModalLabel">Kasa Hareketi Düzenle</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Yükleniyor...
      </div>
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
  if(mid){
    $.ajax({
      url: "/kasa-hareketi/duzenle/"+ mid
    }).done(function(data) { 
      if($.trim(data)==="-1"){
        window.location.reload(true);
      }else{
        $('#editCashTransctionsModal').modal('show');
        $('#editCashTransactionsModal .modal-body').html(data);
      }
    });
  }
</script>

<script type="text/javascript">
$(document).ready(function(){
  $(".addCashTransactions").click(function(){
    $.ajax({
      url: "/kasa-hareketi/ekle/"
    }).done(function(data) {
      if ($.trim(data) === "-1") {
        window.location.reload(true);
      } else {
        $('#addCashTransactionsModal').modal('show');
        $('#addCashTransactionsModal .modal-body').html(data);
      }
    });
  });
  $("#addCashTransactionsModal").on("hidden.bs.modal", function() {
    $(".modal-body").html("");
  });
});
</script>

<script type="text/javascript">
  $(document).ready(function(){
    $('#datatableKasa').on('click', '.editCashTransactions', function(e){
      var id = $(this).attr("data-bs-id");
      $.ajax({
        url: "/kasa-hareketi/duzenle/" + id
      }).done(function(data) {
        if ($.trim(data) === "-1") {
          window.location.reload(true);
        } else {
          $('#editCashTransactionsModal').modal('show');
          $('#editCashTransactionsModal .modal-body').html(data);
        }
      });
    });
    $("#editCashTransactionsModal").on("hidden.bs.modal", function() {
      $(".modal-body").html("");
    });
  });
</script>

<script>
  $(document).ready(function () {
    $('#tevkifatHesaplamaModal .tutar').on('keyup', function() {
      var tutar = Number($("#tevkifatHesaplamaModal .tutar").val());
      var sonuc = tutar/1.14;
      sonuc = Number(sonuc.toFixed(2));
      $("#tevkifatHesaplamaModal .sonuc").val(sonuc);
    });
  });
</script>

<script type="text/javascript">
  $(document).ready(function(){
    $(".statistics").click(function(){
      $.ajax({
        url: "/kasa-istatistik/"
      }).done(function(data) {
        if ($.trim(data) === "-1") {
          window.location.reload(true);
        } else {
          $('#cashTransactionStatisticsModal').modal('show');
          $('#cashTransactionStatisticsModal .modal-body').html(data);
        }
      });
    });
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
    var baslangicYil = '01-01-2020';

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

    $('#kasaArama').on('click', function() {
      $('#daterange').data('daterangepicker').setStartDate(baslangicYil);
      $('#daterange').data('daterangepicker').setEndDate(today);
      filterData();
    });
    
    // Filtreleme fonksiyonu
    function filterData() {
      $('#datatableKasa').DataTable().draw();
    }
  });
</script>

<script>
  $(document).ready(function () {
    $('#kasaArama').click(function() {
      $('#baslangicYil').trigger('click');
    });
  });
</script>

<script>
  $(document).ready(function () {
    var start_date = moment();
    var end_date = moment();
    $('#daterange').daterangepicker({
      startDate : start_date,
      endDate : end_date,
      locale: {
        format: 'DD-MM-YYYY',
        separator: ' - ',
        applyLabel: 'Uygula',
        cancelLabel: 'İptal',
        weekLabel: 'H',
        daysOfWeek: ['Pz', 'Pzt', 'Sal', 'Çrş', 'Prş', 'Cm', 'Cmt'],
        monthNames: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'],
        firstDay: 1,
      }
    },
    function(start_date, end_date){
      $('#daterange').html(start_date.format('DD-MM-YYYY') + '-' + end_date.format('DD-MM-YYYY'));
      table.draw();
      updateValues();
    });

    var table = $('#datatableKasa').DataTable({
      processing: true,
      serverSide: true,
      order: [[0, 'desc']],
      language: {
        paginate: {
          previous: "<i class='mdi mdi-chevron-left'>",
          next: "<i class='mdi mdi-chevron-right'>"
        }
      },
      ajax: {
        url: "{{ route('kasa.filter') }}",
        data: function(data) {
          data.search = $('input[type="search"]').val();
          data.odemeTuru = $('#odemeTuru').val();
          data.from_date = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
          data.to_date = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
          data.customer = $('#customer').val();
          data.odemeSekil = $('#odemeSekil').val();
          data.staff = $('#staff').val();
          data.odenenBanka = $('#odenenBanka').val();
          data.odemeYonu = $('#odemeYonu').val();
          data.musteri = $('.mid').val();
        }
      },
      'columns': [
        { data: 'id' },
        { data: 'islemTarihi' },
        { data: 'odemeTuru' },
        { data: 'aciklama' },
        { data: 'odemeSekli' },
        { data: 'odemeYonuBorc', orderable:false },
        { data: 'odemeYonuAlacak', orderable:false},
        { data: 'fiyat' },
        { data: 'action'}           
      ],
      drawCallback: function(settings) {
        $(".dataTables_paginate > .pagination").addClass("pagination-rounded");    
      },
      order: [[1, 'desc']],
      "columnDefs": [{
        "targets": 0,
        "className": "gizli"
      }],
      "oLanguage": {
        "sDecimal":        ",",
        "sEmptyTable":     "Tabloda herhangi bir veri mevcut değil",
        "sInfo":           "Kasa Hareketi Sayısı: _TOTAL_",
        "sInfoEmpty":      "Kayıt yok",
        "sInfoFiltered":   "",
        "sInfoPostFix":    "",
        "sInfoThousands":  ".",
        "sLengthMenu":     "_MENU_",
        "sLoadingRecords": "Yükleniyor...",
        "sProcessing":     "İşleniyor...",
        "sSearch":         "Müşteri:",
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

    $('#odemeTuru').change(function(){
      table.draw();        
    }); 
    $('#customer').change(function(){
      table.draw();        
    });   
    $('#odemeSekil').change(function(){
      table.draw();
    });
    $('#staff').change(function(){
      table.draw();
    });
    $('#odenenBanka').change(function(){
      table.draw();
    });
    $('#odemeYonu').change(function(){
      table.draw();
    });

    $('#result').on('click', 'li', function () {
      var selectedCustomerId = $(this).attr('data-id');
      table.column('musteri:name').search(selectedCustomerId).draw();
    });

    table.on('draw.dt', function () {
      updateValues();
    });

    var updateValues = function() {
      var startDate = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
      var endDate = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
      var odemeTuru = $('#odemeTuru').val();
      var odemeYonu = $('#odemeYonu').val();
      var customer = $('#customer').val();
      var odemeSekil = $('#odemeSekil').val();
      var staff = $('#staff').val();
      var odenenBanka = $('#odenenBanka').val();
      var musteri = $('.mid').val();
      $.ajax({
        url: '/kasa-toplam',
        method: 'GET',
        data: {
          from_date: startDate,
          to_date: endDate,
          odemeTuru:odemeTuru,
          odemeYonu:odemeYonu,
          customer:customer,
          odemeSekil:odemeSekil,
          staff:staff,
          odenenBanka:odenenBanka,
          musteri:musteri,
        },
        success: function(response) {
          $('.gelenNakitTL').html('<span>Nakit:</span> ' + response.gelenNakitTL);
          $('.gelenHavaleTL').html('<span>EFT/Havale:</span> ' + response.gelenHavaleTL);
          $('.gelenKartTL').html('<span>Kredi Kartı:</span> ' + response.gelenKartTL);
          $('.gelenToplamTL').html('<span>Toplam:</span> ' + response.gelenToplamTL);
          $('.gidenNakitTL').html('<span>Nakit:</span> ' + response.gidenNakitTL);
          $('.gidenHavaleTL').html('<span>EFT/Havale:</span> ' + response.gidenHavaleTL);
          $('.gidenKartTL').html('<span>Kredi Kartı:</span> ' + response.gidenKartTL);
          $('.gidenToplamTL').html('<span>Toplam:</span> ' + response.gidenToplamTL);
          $('.genelToplamTL').html('<span>Toplam:</span> ' + response.genelToplamTL);
        },
        error: function(xhr, status, error) {
          console.error(error);
        }
      });
    }
  });
</script>

@endsection

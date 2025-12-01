<link href="{{ asset('frontend/css/invoices/edit_invoices.css') }}" rel="stylesheet" type="text/css" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<form method="post" id="editInvo" action="{{ route('update.invoices', $firma->id)}}" enctype="multipart/form-data" class="needs-validation" novalidate>
  @csrf
  <div class="card f5 card-invoices">
    <div class=" ch1" style="padding: 3px 10px;">
      <div class="tarihWrap d-flex justify-content-between align-items-center">
    
    <!-- Sol Taraf Grubu (Etiket ve Input) -->
    <div class="d-flex align-items-center">
        <label class="me-2 mb-0">Tarih<span style="font-weight: bold; color: red;">*</span></label>
        <input type="date" name="faturaTarihi" class="form-control datepicker kayitTarihi" value="{{ \Carbon\Carbon::parse($invoice_id->faturaTarihi)->format('Y-m-d')}}" style="width: 150px; background:#fff" required>
    </div>

    <!-- Sağ Taraf Grubu (İkon) -->
    <span>
        <a href="#" data-id="{{$invoice_id->musteriid}}" class="faturaMusteriDuzenleBtn">
            <i class="fas fa-edit" style="font-size: 15px; color: red; text-shadow: none;"></i>
        </a>
    </span>
    
</div>
    </div>
  </div> 

  <div class="card card-invoices f2">
     <div class="card-header card-invoices-header">MÜŞTERİ BİLGİSİ</div>
     <div class="card-body card-invoices-body">
        <div class="row" style="font-size: 14px;">
    <!-- Sol sütun: Servis -->
    <div class="col-md-6 d-flex flex-row align-items-center gap-2 border-end" style="padding-right: 15px;">
    <span> <strong> SERVİS İD: {{ $invoice_id->servisid }} </strong> </span>
    <a href="{{ route('all.services', [$firma->id, 'did' => $invoice_id->servisid]) }}" target="_blank" class="servisiAc btn btn-outline-danger btn-outline-danger-custom col-md-3  px-2 py-1" style="font-size: 13px; line-height: 1.3;">
        Servisi Aç
    </a>
</div>

    <!-- Sağ sütun: Müşteri Bilgisi -->
    <div class="col-md-6 d-flex flex-column gap-1" style="padding-left: 15px;">
        <span><strong>{{ $invoice_id->customer->adSoyad }}
            @if($invoice_id->customer->musteriTipi == '1')
                (BİREYSEL)
            @elseif($invoice_id->customer->musteriTipi == '2')
                (KURUMSAL)
            @endif
        </strong></span>

        <span>{{ $invoice_id->customer?->adres }} {{ $invoice_id->customer?->state?->ilceName }}/{{ $invoice_id->customer?->country?->name }}</span>

        @if(!empty($invoice_id->customer?->tcNo))
            <span>TC: {{ $invoice_id->customer->tcNo }}</span>
        @endif

        @if(!empty($invoice_id->customer?->vergiNo) || !empty($invoice_id->customer?->vergiDairesi))
            <span>VERGİ NO/DAİRESİ: {{ $invoice_id->customer->vergiNo }}/{{ $invoice_id->customer->vergiDairesi }}</span>
        @endif
    </div>
</div>


     </div>
  </div>

  <div class="card card-invoices f2">
    <div class="card-body card-invoices-body">
        <div class="row form-group head">
            <div class="col-5 rw1 col-sm-6"><label>Cinsi</label></div>
            <div class="col-2 rw2 col-sm-2"><label>Miktar</label></div>
            <div class="col-2 rw3 col-sm-2"><label>Fiyat</label></div>
            <div class="col-3 rw4 col-sm-2"><label>Tutar</label></div>
        </div>

        <div class="satirBody">
            @foreach($invoice_id->invoice_products as $key => $product)
                <div class="row form-group">
                    <div class="col-5 rw1 col-sm-6">
                        <input type="text" name="aciklama[]" value="{{ $product->aciklama }}" class="form-control aciklama aciklama{{ $key }} buyukYaz" placeholder="Ürün" autocomplete="off">
                    </div>
                    <div class="col-2 col-sm-2 rw2 custom-rw1">
                        <input type="text" name="miktar[]" value="{{ $product->miktar }}" onkeyup="sayiKontrol(this)" class="form-control miktar miktar{{ $key }}" autocomplete="off">
                    </div>
                    <div class="col-2 col-sm-2 rw3 custom-rw1">
                        <input type="text" name="fiyat[]" value="{{ $product->fiyat }}" onkeyup="sayiKontrol(this)" class="form-control fiyat fiyat{{ $key }}" autocomplete="off">
                    </div>
                    <div class="col-3 rw4 col-sm-2 custom-rw1 pr-custom">
                        <input type="text" name="tutar[]" value="{{ $product->tutar }}" onkeyup="sayiKontrol(this)" class="form-control tutar tutar{{ $key }}" autocomplete="off">
                    </div>
                </div>
            @endforeach
        </div>

      <div class="row form-group" style="margin: 0;border: 0;">
        <button type="button" class="col-xs-12 form-control btn btn-primary2 satirEkle" data-id="1" style="color: #fff;display: inline-block;">Satır Ekle</button>
      </div>
    </div>
  </div>
       
 
<div class="row cardRow1 mb-1">

    <!-- 1. Sütun (Sol Taraf) -->
    <div class="col-lg-6 mb-3 mb-lg-0 custom-p-m custom-p-r-m-k">
      <div class="card card-invoices f3 h-100">
        <div class="card-body card-invoices-body">

          {{-- YENİ EKLENEN: Tevkifat Kodu --}}
          <div class="row form-group" style="border:0">
            <div class="col-md-4 rw1"><label>Tevkifat Kodu</label></div>
            <div class="col-md-8 rw2">
              <select class="form-select tevkifatKodu" name="tevkifatKodu">
                <option value="">Seçiniz</option>
                @if(isset($tevkifatKodlari))
                  @foreach($tevkifatKodlari as $kod)
                    <option value="{{$kod->id}}" {{$invoice_id->tevkifatKodu == $kod->id ? 'selected' : ''}}>{{$kod->kodu}} - {{$kod->adi}}</option>
                  @endforeach
                @endif
              </select>
            </div>
          </div>

          {{-- YENİ EKLENEN: KDV Kodu --}}
          <div class="row form-group" style="border:0">
            <div class="col-md-4 rw1"><label>KDV Kodu</label></div>
            <div class="col-md-8 rw2">
              <select class="form-select kdvKodu" name="kdvKodu">
                <option value="">Seçiniz</option>
                @if(isset($kdvKodlari))
                  @foreach($kdvKodlari as $kod)
                    <option value="{{$kod->id}}" {{$invoice_id->kdvKodu == $kod->id ? 'selected' : ''}}>{{$kod->kodu}} - {{$kod->adi}}</option>
                  @endforeach
                @endif
              </select>
            </div>
          </div>

          {{-- YENİ EKLENEN: KDV Açıklaması --}}
          <div class="row form-group" style="border:0">
            <div class="col-md-4 rw1"><label>KDV Açıklaması</label></div>
            <div class="col-md-8 rw2">
              <input type="text" name="kdvAciklama" class="form-control kdvAciklama" value="{{$invoice_id->kdvAciklama ?? ''}}">
            </div>
          </div>

          {{-- YENİ EKLENEN: Fatura Açıklaması --}}
          <div class="row form-group" style="border:0">
            <div class="col-md-4 rw1"><label>Fatura Açıklaması</label></div>
            <div class="col-md-8 rw2">
              <input type="text" name="faturaAciklama" class="form-control faturaAciklama" value="{{$invoice_id->faturaAciklama ?? ''}}">
            </div>
          </div>

          <div class="row" style="border:0">
            <div class="col-md-4 rw1"><label>Ödeme Şekli<span style="font-weight: bold; color: red;">*</span></label></div>
            <div class="col-md-8 rw2">
              <select class="form-select odemeSekilleri" name="odemeSekli" required>
                <option value="">Seçiniz</option>
                @foreach($payment_methods as $method)
                  <option value="{{$method->id}}" {{$method->id == $invoice_id->odemeSekli ? 'selected' : ''}}>{{$method->odemeSekli}}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="row" style="border:0">
            <div class="col-md-4 rw1"><label>Fatura Durumu<span style="font-weight: bold; color: red;">*</span></label></div>
            <div class="col-md-8 rw2">
              <select class="form-select faturaDurumu" name="faturaDurumu" required>
                <option value="">Seçiniz</option>
                <option value="draft" {{$invoice_id->faturaDurumu == 'draft' ? 'selected' : ''}}>Beklemede</option>
                <option value="sent" {{$invoice_id->faturaDurumu == 'sent' ? 'selected' : ''}}>Gönderildi</option>
                <option value="error" {{$invoice_id->faturaDurumu == 'error' ? 'selected' : ''}}>Gönderilmedi</option>
              </select>
            </div>
          </div>

          <div class="row form-group" style="border:0">
            <div class="col-md-4 rw1"><label>Toplam Yazıyla</label></div>
            <div class="col-md-8 rw2"><input type="text" name="toplamYazi" autocomplete="off" value="{{$invoice_id->toplamYazi}}" class="form-control buyukYaz toplamYazi"></div>
          </div>

          <div class="row form-group" style="border:0">
            <div class="col-md-4 rw1"><label>Fatura No<span style="font-weight: bold; color: red;">*</span></label></div>
            <div class="col-md-8 rw2">
              <input type="text" name="faturaNumarasi" class="form-control buyukYaz faturaNumarasi" value="{{$invoice_id->faturaNumarasi}}" required>
            </div>
          </div>

          <div class="row form-group" style="border:0">
            <div class="col-md-4 rw1"><label>E-Arşiv<span style="font-weight: bold; color: red;">*</span></label></div>
            <div class="col-md-8 rw2">
              <div class="btnWrap ">
                @if($invoice_id->faturaPdf == null)
                <a href="{{asset($invoice_id->faturaPdf)}}" target="_blank" class="btn btn-warning btn-sm btn-block d-none">Görüntüle</a>
                @else
                <a href="{{asset($invoice_id->faturaPdf)}}" target="_blank" class="btn btn-warning btn-sm btn-block">Görüntüle</a>
                @endif
                <a href="javascript:void(0);" data-bs-id="{{$invoice_id->id}}" class="btn btn-warning btn-sm invoic_e" title="Düzenle"><i class="fas fa-edit"></i></a>
                <a href="" class="btn btn-danger btn-sm btn-block eArsivSil" data-id="{{$invoice_id->id}}">Sil</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 2. Sütun (Sağ Taraf) -->
    <div class="col-lg-6 custom-p-r-min custom-p-m-k custom-p-r-m-k">
      <div class="card card-invoices f4 h-100">
        <div class="card-body card-invoices-body" style="padding:17px 5px">
          <div class="row form-group">
            <div class="col-md-4 rw1"><label>Toplam<span style="font-weight: bold; color: red;">*</span></label></div>
            <div class="col-md-8 rw2 custom-rw2"><input type="text" onkeyup="sayiKontrol(this)" name="toplam" value="{{$invoice_id->toplam}}" autocomplete="off" class="form-control toplam" required></div>
          </div>

          <div class="row form-group">
            <div class="col-md-4 rw1"><label>İndirim</label></div>
            <div class="col-md-8 rw2 custom-rw2"><input type="text" onkeyup="sayiKontrol(this)" name="indirim" value="{{$invoice_id->indirim}}" autocomplete="off" class="form-control indirim"></div>
          </div>
          <div class="row form-group">
            <div class="col-md-4 rw1"><label>Ara Toplam</label></div>
            <div class="col-md-8 rw2 custom-rw2"><input type="text" onkeyup="sayiKontrol(this)" name="araToplam" value="{{number_format($invoice_id->toplam - $invoice_id->indirim, 2, '.', '')}}" autocomplete="off" class="form-control araToplam"></div>
          </div>

          <div class="row form-group">
            <div class="col-md-2 rw1"><label>KDV %</label></div>
            <div class="col-md-2 rw2 col-6"><input type="text" onkeyup="sayiKontrol(this)" name="kdvTutar" autocomplete="off" class="form-control kdvTutar" value="{{$invoice_id->kdvTutar ?? 20}}"></div>
            <div class="col-md-8 rw2 custom-rw2 col-6"><input type="text" onkeyup="sayiKontrol(this)" name="kdv" class="form-control kdv" value="{{$invoice_id->kdv}}"></div>
          </div>

          {{-- YENİ EKLENEN: Tevkifat Oranı ve Tutarı --}}
          <div class="row form-group">
            <div class="col-md-6 rw1"><label>Tevkifat Oranı</label></div>
            <div class="col-md-2 col-6 rw2">
              <select class="form-select tevkifatOrani" name="tevkifatOrani">
                <option value="0" {{($invoice_id->tevkifatOrani ?? 0) == 0 ? 'selected' : ''}}>0</option>
                <option value="2" {{($invoice_id->tevkifatOrani ?? 0) == 2 ? 'selected' : ''}}>2/10</option>
                <option value="3" {{($invoice_id->tevkifatOrani ?? 0) == 3 ? 'selected' : ''}}>3/10</option>
                <option value="4" {{($invoice_id->tevkifatOrani ?? 0) == 4 ? 'selected' : ''}}>4/10</option>
                <option value="5" {{($invoice_id->tevkifatOrani ?? 0) == 5 ? 'selected' : ''}}>5/10</option>
                <option value="7" {{($invoice_id->tevkifatOrani ?? 0) == 7 ? 'selected' : ''}}>7/10</option>
                <option value="9" {{($invoice_id->tevkifatOrani ?? 0) == 9 ? 'selected' : ''}}>9/10</option>
              </select>
            </div>
            <div class="col-md-4 custom-rw2 col-6 rw2">
              <input type="text" class="form-control tevkifatTutari" value="{{number_format($invoice_id->tevkifatTutari ?? 0, 2, '.', '')}}" disabled>
              <input type="hidden" name="tevkifatTutari" class="tevkifatTutari" value="{{$invoice_id->tevkifatTutari ?? 0}}">
            </div>
          </div>

          <div class="row form-group" style="padding-bottom: 0">
            <div class="col-md-4 rw1"><label>Genel Toplam<span style="font-weight: bold; color: red;">*</span></label></div>
            <div class="col-md-8 rw2 custom-rw2"><input type="text" onkeyup="sayiKontrol(this)" name="genelToplam" value="{{$invoice_id->genelToplam}}" autocomplete="off" class="form-control genelToplam" required></div>
          </div>

        </div>
      </div>
    </div>
</div>
    <div class="row">
      <div class="col-sm-12 gonderBtn">
        <input type="hidden" name="id" value="{{ $invoice_id->id }}">
        <input type="submit" class="btn btn-sm btn-info waves-effect waves-light" value="Kaydet">
      </div>
    </div>
  </div>
</form>



<script type="text/javascript">
$(document).ready(function(){
    $('#editInvo').on('click', '.invoic_e', function(e){
        var id = $(this).attr("data-bs-id");
        var firma_id = {{$firma->id}};
        $.ajax({
            url: "/" + firma_id + "/fatura/goruntule/" + id
        }).done(function(data) {
            console.log(data);
            if ($.trim(data) === "-1") {
                window.location.reload(true);
            } else {
                $('#InvoiceModal').modal('show');
                $('#InvoiceModal .modal-body').html(data);
            }
        });
    });
});
</script>
<script type="text/javascript">
  $(".faturaMusteriDuzenleBtn").click(function(){
    var id = {{$invoice_id->musteriid}};
    var firma_id = {{$firma->id}};
    $('#editInvoiceCustomerModal').modal('show');
    $.ajax({
      url: "/" + firma_id + "/servis-musteri/duzenle/" + id
    }).done(function(data) {
      if($.trim(data)==="-1"){
        window.location.reload(true);
      }else{
        $('#editInvoiceCustomerModal .modal-body').html(data);
      }
    });
  });
  
</script>

<script type="text/javascript">
  function sayiKontrol(v) {
    var isNum = /^[0-9-'.']*$/;
    if (!isNum.test(v.value)) { 
      v.value = v.value.replace(/[^0-9-',']/g, "");
    }                   
  }

  $('.buyukYaz').keyup(function(){
    this.value = this.value.toUpperCase();
  });

  $('.satirBody').on('keyup', '.buyukYaz', function () {
    this.value = this.value.toUpperCase();
  });

  $(".satirEkle").click(function () {
    var index = $(".satirBody .row").length; // Mevcut satır sayısı
    var satirClone = `
      <div class="row form-group align-items-center satir">
        <div class="col-5 rw1 col-sm-6">
          <input type="text" name="aciklama[]" class="form-control aciklama aciklama${index} buyukYaz" placeholder="Ürün" autocomplete="off">
        </div>
        <div class="col-2 rw2 custom-rw1 col-sm-2">
          <input type="text" name="miktar[]" onkeyup="sayiKontrol(this)" class="form-control miktar miktar${index}" autocomplete="off">
        </div>
        <div class="col-2 rw3 custom-rw1 col-sm-2">
          <input type="text" name="fiyat[]" onkeyup="sayiKontrol(this)" class="form-control fiyat fiyat${index}" autocomplete="off">
        </div>
        <div class="col-3 rw4 custom-rw1 pr-custom col-sm-2">
          <input type="text" name="tutar[]" onkeyup="sayiKontrol(this)" class="form-control tutar tutar${index}" autocomplete="off">
        </div>
      </div>
    `;
    $(".satirBody").append(satirClone);
  });
  
  $(document).on('click', '.satirSil', function () {
    $(this).closest('.satir').remove();
  });
</script>

<script>
  $(document).ready(function() {
    $('#editInvo').on('click', '.eArsivSil', function(e) {
      e.preventDefault();
      var confirmDelete = confirm("Bu e-faturayı silmek istediğinizden emin misiniz?");
      if (confirmDelete) {
        var id = $(this).attr('data-id');
        var firma_id = {{$firma->id}};
        $.ajax({
          url: '/' + firma_id + '/eArsiv/sil/' + id,
          type: 'POST',
          data: {
            _method: 'POST', 
            _token: '{{ csrf_token() }}'
          },
          success: function(data) {
            if (data) {
              $('#datatableInvoice').DataTable().ajax.reload();
              $('#InvoiceModal').modal('hide');
              $('#editInvoiceModal').modal('hide');
            } else {
              alert("Silme işlemi başarısız oldu.");
            }
          },
          error: function(xhr, status, error) {
            console.error(xhr.responseText);
          }
        });
      }
    });
  });
</script>

<script type="text/javascript">
function formatToTwoDecimalPlaces(value) {
    return Number(value).toFixed(2);
  }
  
  $(document).ready(function (e) {
    var sonucToplam = 0;
    var sonuc = 0;
    
    setTimeout(function (){
      $('.miktar').each(function(index, data) {
        var fiyat = Number($(".fiyat"+index).val());
        var miktar = Number($(this).val());
        sonuc = fiyat*miktar;
        sonucToplam = sonucToplam + sonuc;
        $(".tutar"+index).val(formatToTwoDecimalPlaces(sonuc));
      });     
    }, 500); 

    $('.satirBody').keyup(function() {
      sonucToplam = 0;
      $('.miktar').each(function(index, data) {
        var fiyat = Number($(".fiyat"+index).val());
        var miktar = Number($(this).val());
        sonuc = fiyat*miktar;
        sonucToplam = sonucToplam + sonuc;
        $(".tutar"+index).val(formatToTwoDecimalPlaces(sonuc))
        kdvHesapla(sonucToplam)
      });
    });

    function kdvHesapla(toplam){
      var indirim = Number($(".indirim").val());
      var kdvTutar = Number($(".kdvTutar").val());
      var tevkifatOrani = Number($(".tevkifatOrani").val());
      var araToplam = toplam - indirim;
      var kdv = ((araToplam * kdvTutar) / 100);

      if (tevkifatOrani > 0) {
        var tevkifatHesapla = (kdv * tevkifatOrani) / 10;
        var genelToplam = araToplam + (kdv - tevkifatHesapla);

        kdv = parseFloat(kdv.toFixed(2));
        tevkifatHesapla = parseFloat(tevkifatHesapla.toFixed(2));
        genelToplam = parseFloat(genelToplam.toFixed(2));

        $(".tevkifatTutari").val(tevkifatHesapla);
        $(".genelToplam").val(genelToplam);
      } else {
        var genelToplam = araToplam + kdv;
        kdv = parseFloat(kdv.toFixed(2));
        genelToplam = parseFloat(genelToplam.toFixed(2));
        
        $(".tevkifatTutari").val(0);
        $(".genelToplam").val(genelToplam);
      }

      $(".toplam").val(formatToTwoDecimalPlaces(toplam));
      $(".araToplam").val(formatToTwoDecimalPlaces(araToplam));
      $(".kdv").val(formatToTwoDecimalPlaces(kdv));
    }

    $('.kdvTutar').on('keyup', function() {
      var indirim = Number($(".indirim").val());
      var kdvTutar = Number($(".kdvTutar").val());
      var tevkifatOrani = Number($(".tevkifatOrani").val());
      var araToplam = sonucToplam - indirim;
      var kdv = ((araToplam * kdvTutar) / 100);

      if (tevkifatOrani > 0) {
        var tevkifatHesapla = (kdv * tevkifatOrani) / 10;
        var genelToplam = araToplam + (kdv - tevkifatHesapla);

        kdv = parseFloat(kdv.toFixed(2));
        tevkifatHesapla = parseFloat(tevkifatHesapla.toFixed(2));
        genelToplam = parseFloat(genelToplam.toFixed(2));

        $(".tevkifatTutari").val(tevkifatHesapla);
        $(".genelToplam").val(genelToplam);
      } else {
        var genelToplam = araToplam + kdv;
        kdv = parseFloat(kdv.toFixed(2));
        genelToplam = parseFloat(genelToplam.toFixed(2));

        $(".genelToplam").val(genelToplam);
      }

      $(".araToplam").val(formatToTwoDecimalPlaces(araToplam));
      $(".kdv").val(formatToTwoDecimalPlaces(kdv));
    });

    $('.indirim').on('keyup', function() {
      var indirim = Number($(".indirim").val());
      var kdvTutar = Number($(".kdvTutar").val());
      var tevkifatOrani = Number($(".tevkifatOrani").val());
      var araToplam = sonucToplam - indirim;
      var kdv = ((araToplam * kdvTutar) / 100);

      if (tevkifatOrani > 0) {
        var tevkifatHesapla = (kdv * tevkifatOrani) / 10;
        var genelToplam = araToplam + (kdv - tevkifatHesapla);

        kdv = parseFloat(kdv.toFixed(2));
        tevkifatHesapla = parseFloat(tevkifatHesapla.toFixed(2));
        genelToplam = parseFloat(genelToplam.toFixed(2));

        $(".tevkifatTutari").val(tevkifatHesapla);
        $(".genelToplam").val(genelToplam);
      } else {
        var genelToplam = araToplam + kdv;
        kdv = parseFloat(kdv.toFixed(2));
        genelToplam = parseFloat(genelToplam.toFixed(2));

        $(".genelToplam").val(genelToplam);
      }

      $(".araToplam").val(formatToTwoDecimalPlaces(araToplam));
      $(".kdv").val(formatToTwoDecimalPlaces(kdv));
    });

    // YENİ EKLENEN: Tevkifat oranı değiştiğinde hesaplama
    $('.tevkifatOrani').on('change', function() {
      var indirim = Number($(".indirim").val());
      var kdvTutar = Number($(".kdvTutar").val());
      var tevkifatOrani = Number($(this).val());
      var araToplam = sonucToplam - indirim;
      var kdv = ((araToplam * kdvTutar) / 100);

      if (tevkifatOrani > 0) {
        var tevkifatHesapla = (kdv * tevkifatOrani) / 10;
        var genelToplam = araToplam + (kdv - tevkifatHesapla);

        kdv = parseFloat(kdv.toFixed(2));
        tevkifatHesapla = parseFloat(tevkifatHesapla.toFixed(2));
        genelToplam = parseFloat(genelToplam.toFixed(2));

        $(".tevkifatTutari").val(tevkifatHesapla);
        $(".genelToplam").val(genelToplam);
        $(".kdv").val(kdv);
      } else {
        var genelToplam = araToplam + kdv;
        kdv = parseFloat(kdv.toFixed(2));
        genelToplam = parseFloat(genelToplam.toFixed(2));

        $(".tevkifatTutari").val(0);
        $(".genelToplam").val(genelToplam);
        $(".kdv").val(kdv);
      }
    });

    //Virgülleri nokta yapıyor
    $("input:text").keyup(function() {
      $(this).val($(this).val().replace(/[,]/g, "."));
    });
  });
</script>

<script>
  $('#editInvo').on('submit', function(e) {
  e.preventDefault(); // Her zaman engelle

  let formIsValid = true;
  $(this).find('input[required], select[required]').each(function() {
    if (!$(this).val()) {
      formIsValid = false;
      return false; // .each döngüsünü kır
    }
  });

  if (!formIsValid) {
    alert('Lütfen zorunlu alanları doldurun.');
    return;
  }

  // ↘ Eğer form geçerliyse AJAX işlemini başlat
  var formData = new FormData(this);
  $.ajax({
    url: $(this).attr("action"),
    type: "POST",
    data: formData,
    contentType: false,
    cache: false,
    processData: false,
    success: function(data) {
      if (data === false) {
        window.location.reload(true);
      } else {
        alert("Fatura güncellendi");
        $('#datatableInvoice').DataTable().ajax.reload();
        $('#editInvoiceModal').modal('hide');
      }
    },
    error: function(xhr, status, error) {
      alert("Güncelleme başarısız!");
      window.location.reload(true);
    },
  });
});
</script>
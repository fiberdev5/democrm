<style>
  body {
    background-color: #f4f6f9;
  }

  .card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    margin-bottom: 20px;
  }

  .card-header {
    background-color: #f8f9fa;
    font-weight: 600;
    font-size: 16px;
    padding: 10px 15px;
  }

  .rw1, .rw2, .rw3, .rw4 {
    margin-bottom: 5px;
  }

  .kisaMusteriBil span {
    display: block;
    margin-bottom: 4px;
  }

  .btnWrap .btn {
    margin-right: 5px;
    margin-bottom: 5px;
  }

  .btnWrap {
    display: flex;
    flex-wrap: wrap;
  }

  .tarihWrap input[type="date"] {
    font-size: 14px;
    padding: 5px 10px;
    border-radius: 6px;
    border: 1px solid #ced4da;
  }
</style>


<form method="post" id="editInvo" action="{{ route('update.invoices', $firma->id)}}" enctype="multipart/form-data" class="needs-validation" novalidate>
  @csrf
  <div class="card f5">
    <div class="card-header ch1" style="padding: 3px 10px;">
      <div class="tarihWrap" >
        <label style="text-align: left;width: auto;display: inline-block;margin: 0;">Tarih<span style="font-weight: bold; color: red;">*</span></label>
        <input type="date" name="faturaTarihi" class="form-control datepicker kayitTarihi"  value="{{ \Carbon\Carbon::parse($invoice_id->faturaTarihi)->format('Y-m-d')}}" style="width: 150px;display: inline-block;background:#fff" required>
      
    <span><a href="#" data-id="" class="faturaMusteriDuzenleBtn"><i class="fas fa-edit" style="font-size: 15px;color: red;text-shadow: none;float: right;"></i></a></span>
      <div class="clearfix"></div>
    </div>
    </div>
  </div> 

  <div class="card f2">
     <div class="card-header">MÜŞTERİ BİLGİSİ</div>
     <div class="card-body">
        <div class="row">
           <div class="col-sm-6 s1">
              <div class="row form-group">
                <div class="col-md-12 rw2 d-flex flex-wrap align-items-center gap-2">

                    <span>{{$invoice_id->servisid}}</span><a href="{{ route('all.services', [$firma->id, 'did' => $invoice_id->servisid]) }}" target="_blank" class="servisiAc btn btn-outline-danger px-2 py-1"style="font-size: 13px; line-height: 1.3;">Servisi Aç</a>
                </div>
              </div>
              <div class="kisaMusteriBil" style="font-size: 14px">
          	    <span><strong>{{$invoice_id->customer->adSoyad}}
                    @if($invoice_id->customer->musteriTipi == '1')
                    (BİREYSEL)
                    @elseif($invoice_id->customer->musteriTipi == '2')
                     (KURUMSAL) 
                    @endif
                    </strong>
                </span>
          	    <span>{{$invoice_id->customer?->adres}} {{$invoice_id->customer?->state?->ilceName}}/{{$invoice_id->customer?->country?->name}}</span>	
                @if(!empty($invoice_id->customer?->tcNo))
                <span>TC: 11111111111</span>
                @endif
                @if(!empty($invoice_id->customer?->vergiNo) || !empty($invoice_id->customer?->vergiDairesi))
          	    <span>VERGİ NO/DAİRESİ: 121212/bursa</span>
                @endif
            </div>
           </div>
        </div>
     </div>
  </div>

  <div class="card f2">
    <div class="card-body">
        <div class="row form-group head">
            <div class="col-5 rw1 col-form-label"><label>Cinsi</label></div>
            <div class="col-2 rw2 col-form-label"><label>Miktar</label></div>
            <div class="col-2 rw3 col-form-label"><label>Fiyat</label></div>
            <div class="col-3 rw4 col-form-label"><label>Tutar</label></div>
        </div>

        <div class="satirBody">
            @foreach($invoice_id->invoice_products as $key => $product)
                <div class="row form-group">
                    <div class="col-5 rw1 ">
                        <input type="text" name="aciklama[]" value="{{ $product->aciklama }}" class="form-control aciklama aciklama{{ $key }} buyukYaz" placeholder="Ürün" autocomplete="off">
                    </div>
                    <div class="col-2 rw2">
                        <input type="text" name="miktar[]" value="{{ $product->miktar }}" onkeyup="sayiKontrol(this)" class="form-control miktar miktar{{ $key }}" autocomplete="off">
                    </div>
                    <div class="col-2 rw3 ">
                        <input type="text" name="fiyat[]" value="{{ $product->fiyat }}" onkeyup="sayiKontrol(this)" class="form-control fiyat fiyat{{ $key }}" autocomplete="off">
                    </div>
                    <div class="col-3 rw4">
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
       
  <div class="row cardRow1">
    <div class="card col-lg-6 f3">
      <div class="card-body">
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
                <a href="{{route('delete.einvoice',[$firma->id,$invoice_id->id])}}" class="btn btn-danger btn-sm btn-block eArsivSil"  id="delete" data-id="">Sil</a>
              </div>
            </div>
          </div>       
        </div>
      </div>

      <div class="card col-lg-6 f4">
        <div class="card-body" style="padding:17px 5px">
          <div class="row form-group">
            <div class="col-md-8 rw1"><label>Toplam<span style="font-weight: bold; color: red;">*</span></label></div>
            <div class="col-md-4 rw2"><input type="text" onkeyup="sayiKontrol(this)" name="toplam" value="{{$invoice_id->toplam}}" autocomplete="off" class="form-control toplam" required></div>
          </div>

          <div class="row form-group">
          <div class="col-md-8 rw1"><label>İndirim</label></div>
          <div class="col-md-4 rw2"><input type="text" onkeyup="sayiKontrol(this)" name="indirim" value="{{$invoice_id->indirim}}" autocomplete="off" class="form-control indirim"></div>
        </div>
        <div class="row form-group">
          <div class="col-md-8 rw1"><label>Ara Toplam</label></div>
          <div class="col-md-4 rw2"><input type="text" onkeyup="sayiKontrol(this)" name="araToplam" value="{{$invoice_id->toplam-$invoice_id->indirim}}.00" autocomplete="off" class="form-control araToplam"></div>
        </div>

          <div class="row form-group">
            <div class="col-md-6 rw1"><label>KDV %</label></div>
            <div class="col-md-2 rw2"><input type="text" onkeyup="sayiKontrol(this)" name="kdvTutar" autocomplete="off" class="form-control kdvTutar" value="20" style="text-align: center;"></div>
            <div class="col-md-4 rw2"><input type="text" onkeyup="sayiKontrol(this)" name="kdv" class="form-control kdv" value="{{$invoice_id->kdv}}"></div>
          </div>

          <div class="row form-group" style="padding-bottom: 0">  
            <div class="col-md-8 rw1"><label>Genel Toplam<span style="font-weight: bold; color: red;">*</span></label></div>
            <div class="col-md-4 rw2"><input type="text" onkeyup="sayiKontrol(this)" name="genelToplam" value="{{$invoice_id->genelToplam}}" autocomplete="off" class="form-control genelToplam" required></div>
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
  function sayiKontrol(v) {
    var isNum = /^[0-9-'.']*$/;
    if (!isNum.test(v.value)) { 
      v.value = v.value.replace(/[^0-9-',']/g, "");
    }                   
  }

      $(".satirEkle").click(function () {
        var index = $(".satirBody .row").length; // Mevcut satır sayısı
        var satirClone = `
          <div class="row form-group align-items-center satir">
            <div class="col-5 rw1">
              <input type="text" name="aciklama[]" class="form-control aciklama aciklama${index} buyukYaz" placeholder="Ürün" autocomplete="off">
            </div>
            <div class="col-2 rw2">
              <input type="text" name="miktar[]" onkeyup="sayiKontrol(this)" class="form-control miktar miktar${index}" autocomplete="off">
            </div>
            <div class="col-2 rw3">
              <input type="text" name="fiyat[]" onkeyup="sayiKontrol(this)" class="form-control fiyat fiyat${index}" autocomplete="off">
            </div>
            <div class="col-2 rw4">
              <input type="text" name="tutar[]" onkeyup="sayiKontrol(this)" class="form-control tutar tutar${index}" autocomplete="off">
            </div>
            <div class="col-1 text-end">
              <button type="button" class="btn btn-danger btn-sm satirSil" title="Satırı Sil"><strong>&times;</strong></button>
            </div>
          </div>
        `;
        $(".satirBody").append(satirClone);
    });
    $(document).on('click', '.satirSil', function () {
      $(this).closest('.satir').remove();
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
      var araToplam = Number($(".araToplam").val());
      var kdv = (((toplam-indirim)*kdvTutar)/100);
      var araToplam = (toplam-indirim);
      var genelToplam = ((toplam-indirim) + kdv);

      $(".toplam").val(formatToTwoDecimalPlaces(toplam));
      $(".araToplam").val(formatToTwoDecimalPlaces(araToplam));
      $(".genelToplam").val(formatToTwoDecimalPlaces(genelToplam));
      $(".kdv").val(formatToTwoDecimalPlaces(kdv));
    }

    $('.kdvTutar').on('keyup', function() {
      var indirim = Number($(".indirim").val());
      var kdvTutar = Number($(".kdvTutar").val());
      var araToplam = Number($(".araToplam").val());
      var kdv = (((sonucToplam-indirim)*kdvTutar)/100);
      var araToplam = (sonucToplam-indirim);
      var genelToplam = ((sonucToplam-indirim) + kdv);

      $(".genelToplam").val(formatToTwoDecimalPlaces(genelToplam));
      $(".araToplam").val(formatToTwoDecimalPlaces(araToplam));
      $(".kdv").val(formatToTwoDecimalPlaces(kdv));
    });

    $('.indirim').on('keyup', function() {
      var indirim = Number($(".indirim").val());
      var kdvTutar = Number($(".kdvTutar").val());
      var araToplam = Number($(".araToplam").val());
      var kdv = (((sonucToplam-indirim)*kdvTutar)/100);
      var araToplam = (sonucToplam-indirim);
      var genelToplam = ((sonucToplam-indirim) + kdv);

      $(".araToplam").val(formatToTwoDecimalPlaces(araToplam));
      $(".genelToplam").val(formatToTwoDecimalPlaces(genelToplam));
      $(".kdv").val(formatToTwoDecimalPlaces(kdv));
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
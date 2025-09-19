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
    margin-bottom: 0;
  }

  .kisaFirmaBil span {
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

<meta name="csrf-token" content="{{ csrf_token() }}">

<form method="post" id="editInvo" action="{{ route('super.admin.invoices.update')}}" enctype="multipart/form-data" class="needs-validation" novalidate>
  @csrf
  <div class="card f5">
    <div class="card-header ch1" style="padding: 3px 10px;">
      <div class="tarihWrap" >
        <label style="text-align: left;width: auto;display: inline-block;margin: 0;">Tarih<span style="font-weight: bold; color: red;">*</span></label>
        <input type="date" name="faturaTarihi" class="form-control datepicker kayitTarihi"  value="{{ \Carbon\Carbon::parse($invoice_id->faturaTarihi)->format('Y-m-d')}}" style="width: 150px;display: inline-block;background:#fff" required>
      
      <div class="clearfix"></div>
    </div>
    </div>
  </div> 

  <div class="card f2">
     <div class="card-header">
       <div style="display: flex; justify-content: space-between; align-items: center;">
         <span>MÜŞTERİ BİLGİSİ</span>
         <button type="button" class="btn btn-sm btn-outline-primary musteriDuzenle" data-bs-toggle="modal" data-bs-target="#musteriDuzenleModal">
           <i class="fas fa-edit"></i> Düzenle
         </button>
       </div>
     </div>
     <div class="card-body">
        <div class="row">
           <div class="col-12">
              <div class="row form-group">
                 <div class="col-md-2 rw1"><label>Müşteri <span style="font-weight: bold; color: red;">*</span></label></div>
                 <div class="col-md-10 rw2">
                   <div id="seciliMusteri" style="background: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; border-radius: 4px;">
                     <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                       <div style="flex: 1;">
                         <div style="font-weight: 600; color: #495057; margin-bottom: 8px; font-size: 16px;">
                           {{ $invoice_id->tenant->firma_adi }}
                         </div>
                         <div style="font-size: 13px; color: #6c757d; line-height: 1.5;">
                           @if(!empty($invoice_id->tenant?->tel1))
                             <div style="margin-bottom: 3px;">📞 Telefon: {{ $invoice_id->tenant->tel1 }}</div>
                           @endif
                           <div style="margin-bottom: 3px;">📍 Konum: {{ $invoice_id->tenant->il }}/{{ $invoice_id->tenant->ilce }}</div>
                           @if(!empty($invoice_id->tenant?->vergiNo) || !empty($invoice_id->tenant?->vergiDairesi))
                             <div style="margin-bottom: 3px;">🏢 Vergi: {{ $invoice_id->tenant->vergiNo }} {{ $invoice_id->tenant->vergiDairesi ? ' - ' . $invoice_id->tenant->vergiDairesi : '' }}</div>
                           @endif
                           <div>📧 Adres: {{ $invoice_id->tenant->adres }}</div>
                         </div>
                       </div>
                     </div>
                   </div>
                 </div>
              </div>
              
              <!-- Hidden inputs for form submission -->
              <input type="hidden" name="firma_id" value="{{ $invoice_id->firma_id }}" class="seciliMusteriId">
              <input type="hidden" name="vergiNo" value="{{ $invoice_id->tenant->vergiNo }}" class="vergiNo">
              <input type="hidden" name="vergiDairesi" value="{{ $invoice_id->tenant->vergiDairesi }}" class="vergiDairesi">
              <input type="hidden" name="tel1" value="{{ $invoice_id->tenant->tel1 }}" class="tel1">
              <input type="hidden" name="tel2" value="" class="tel2">
              <input type="hidden" name="il" value="{{ $invoice_id->tenant->il }}" class="il">
              <input type="hidden" name="ilce" value="{{ $invoice_id->tenant->ilce }}" class="ilce">
              <textarea name="adres" class="adres" style="display: none;">{{ $invoice_id->tenant->adres }}</textarea>
           </div>
        </div>
     </div>
  </div>

  <div class="card f2">
    <div class="card-body">
        <div class="row form-group head">
            <div class="col-5 rw1 "><label>Cinsi</label></div>
            <div class="col-2 rw2 "><label>Miktar</label></div>
            <div class="col-2 rw3 "><label>Fiyat</label></div>
            <div class="col-3 rw4 "><label>Tutar</label></div>
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
                <a href="" class="btn btn-danger btn-sm btn-block eArsivSil"   data-id="{{$invoice_id->id}}">Sil</a>
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
          <div class="col-md-4 rw2"><input type="text" onkeyup="sayiKontrol(this)" name="araToplam" value="{{$invoice_id->toplam-$invoice_id->indirim}}" autocomplete="off" class="form-control araToplam"></div>
        </div>

          <div class="row form-group">
            <div class="col-md-6 rw1"><label>KDV %</label></div>
            <div class="col-md-2 rw2"><input type="text" onkeyup="sayiKontrol(this)" name="kdvTutar" autocomplete="off" class="form-control kdvTutar" value="{{$invoice_id->kdvTutar}}" style="text-align: center;"></div>
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

<!-- Müşteri Düzenleme Modalı -->
<div class="modal fade" id="musteriDuzenleModal" tabindex="-1" aria-labelledby="musteriDuzenleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="musteriDuzenleModalLabel">Müşteri Seç</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Müşteri Ara</label>
          <input type="text" id="musteriArama" class="form-control" placeholder="Müşteri adı yazın..." autocomplete="off">
          <ul id="musteriListesi" class="list-group mt-2" style="display: none;"></ul>
        </div>
        
        <div class="mb-3">
          <label class="form-label">Mevcut Müşteriler</label>
          <select class="form-select musteriSecim">
            <option value="">Müşteri Seçiniz</option>
            @foreach($tenants as $tenant)
              <option value="{{$tenant->id}}" 
                      data-firma-adi="{{$tenant->firma_adi}}"
                      data-tel1="{{$tenant->tel1}}"
                      data-il="{{$tenant->il}}"
                      data-ilce="{{$tenant->ilce}}"
                      data-adres="{{$tenant->adres}}"
                      data-vergiNo="{{$tenant->vergiNo}}"
                      data-vergiDairesi="{{$tenant->vergiDairesi}}"
                      {{$tenant->id == $invoice_id->firma_id ? 'selected' : ''}}>
                {{$tenant->firma_adi}}
              </option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
        <button type="button" class="btn btn-primary musteriKaydet">Kaydet</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
$(document).ready(function(){
    $('#editInvo').on('click', '.invoic_e', function(e){
        var id = $(this).attr("data-bs-id");
        $.ajax({
            url: "{{ route('super.admin.invoices.show', '') }}/" + id
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
    var index = $(".satirBody .row").length;
    var satirClone = '<div class="row form-group align-items-center satir">' +
      '<div class="col-5 rw1">' +
        '<input type="text" name="aciklama[]" class="form-control aciklama aciklama' + index + ' buyukYaz" placeholder="Ürün" autocomplete="off">' +
      '</div>' +
      '<div class="col-2 rw2">' +
        '<input type="text" name="miktar[]" onkeyup="sayiKontrol(this)" class="form-control miktar miktar' + index + '" autocomplete="off">' +
      '</div>' +
      '<div class="col-2 rw3">' +
        '<input type="text" name="fiyat[]" onkeyup="sayiKontrol(this)" class="form-control fiyat fiyat' + index + '" autocomplete="off">' +
      '</div>' +
      '<div class="col-2 rw4">' +
        '<input type="text" name="tutar[]" onkeyup="sayiKontrol(this)" class="form-control tutar tutar' + index + '" autocomplete="off">' +
      '</div>' +
      '<div class="col-1 text-end">' +
        '<button type="button" class="btn btn-danger btn-sm satirSil" title="Satırı Sil"><strong>&times;</strong></button>' +
      '</div>' +
    '</div>';
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
        $.ajax({
          url: '{{ route("super.admin.invoices.delete.einvoice", "") }}/' + id,
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

    // Müşteri değiştirme modalı
    $('.musteriKaydet').click(function() {
      var selectedOption = $('.musteriSecim option:selected');
      if (selectedOption.val()) {
        var id = selectedOption.val();
        var firmaAdi = selectedOption.data('firma-adi');
        var tel1 = selectedOption.data('tel1') || '';
        var il = selectedOption.data('il') || '';
        var ilce = selectedOption.data('ilce') || '';
        var adres = selectedOption.data('adres') || '';
        var vergiNo = selectedOption.data('vergiNo') || '';
        var vergiDairesi = selectedOption.data('vergiDairesi') || '';
        
        // Müşteri bilgilerini güncelle
        $('#seciliMusteri').html(
          '<div style="display: flex; justify-content: space-between; align-items: flex-start;">' +
            '<div style="flex: 1;">' +
              '<div style="font-weight: 600; color: #495057; margin-bottom: 8px; font-size: 16px;">' + firmaAdi + '</div>' +
              '<div style="font-size: 13px; color: #6c757d; line-height: 1.5;">' +
                (tel1 ? '<div style="margin-bottom: 3px;">📞 Telefon: ' + tel1 + '</div>' : '') +
                '<div style="margin-bottom: 3px;">📍 Konum: ' + il + '/' + ilce + '</div>' +
                (vergiNo ? '<div style="margin-bottom: 3px;">🏢 Vergi: ' + vergiNo + (vergiDairesi ? ' - ' + vergiDairesi : '') + '</div>' : '') +
                '<div>📧 Adres: ' + adres + '</div>' +
              '</div>' +
            '</div>' +
          '</div>'
        );
        
        // Hidden inputları güncelle
        $('.seciliMusteriId').val(id);
        $('.vergiNo').val(vergiNo);
        $('.vergiDairesi').val(vergiDairesi);
        $('.tel1').val(tel1);
        $('.il').val(il);
        $('.ilce').val(ilce);
        $('.adres').val(adres);
        
        // $('#musteriDuzenleModal').modal('hide');
      } else {
        alert('Lütfen bir müşteri seçin.');
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
        $(".tutar"+index).val(formatToTwoDecimalPlaces(sonuc));
        kdvHesapla(sonucToplam);
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

    // Virgülleri nokta yapıyor
    $("input:text").keyup(function() {
      $(this).val($(this).val().replace(/[,]/g, "."));
    });
});
</script>

<script>
$('#editInvo').on('submit', function(e) {
  e.preventDefault();

  let formIsValid = true;
  $(this).find('input[required], select[required]').each(function() {
    if (!$(this).val()) {
      formIsValid = false;
      return false;
    }
  });

  if (!formIsValid) {
    alert('Lütfen zorunlu alanları doldurun.');
    return;
  }

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

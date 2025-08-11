<form method="post" id="editInvo" action="{{ route('update.invoices', $firma->id)}}" enctype="multipart/form-data" class="needs-validation" novalidate>
  @csrf
  <div class="card f5">
    <div class="card-header ch1" style="padding: 3px 10px;">
      <div class="tarihWrap">
        <label style="text-align: left;width: auto;display: inline-block;margin: 0;">Tarih<span style="font-weight: bold; color: red;">*</span></label>
        <input type="date" name="faturaTarihi" class="form-control datepicker kayitTarihi"  value="{{ \Carbon\Carbon::parse($invoice_id->faturaTarihi)->format('Y-m-d')}}" style="width: 150px;display: inline-block;background:#fff" required>
      </div>

      <div class="clearfix"></div>
    </div>
  </div> 

  <div class="card f2">
     <div class="card-header">MÜŞTERİ BİLGİSİ</div>
     <div class="card-body">
        <div class="row">
           <div class="col-sm-6 s1">
              <div class="row form-group">
                <div class="col-md-4 rw1"><label>Servis Ara</label></div>
                <div class="col-md-8 rw2 d-flex flex-wrap align-items-center gap-2">
                    <input id="search" type="text" name="servisid" value="{{$invoice_id->servisid}}" class="form-control servisid" data-bs-id="" autocomplete="off" placeholder="Servis ID" style="flex: 1 1 auto; max-width: 160px;">

                    <a href="{{ url($firma->id . '/servisler?did=$invoice_id->servisid') }}" target="_blank" class="servisiAc btn btn-outline-danger px-2 py-1"style="font-size: 13px; line-height: 1.3;">Servisi Aç</a>
                </div>
              </div>
              <div class="row form-group">
                 <div class="col-md-4 rw1"><label><span class="musteriAdiSpan">Müşteri Adı</span> <span style="font-weight: bold; color: red;">*</span></label></div>
                 <div class="col-md-8 rw2">
                   <input type="text" name="adSoyad" class="form-control buyukYaz adSoyad" data-id="" autocomplete="off" placeholder="Müşteri Adı">
                    
                </div>
              </div>
              <input type="hidden" name="mid" class="eskiMusteriId" value="">
              <div class="row form-group">
                 <div class="col-md-4 rw1"><label>Vergi No/Dairesi</label></div>
                 <div class="col-md-4 col-6 rw2">
                    <input type="number" name="vergiNo" class="form-control vergiNo" placeholder="Vergi No" autocomplete="off">
                 </div>
                 <div class="col-md-4 col-6 rw2">
                    <input type="text" name="vergiDairesi" class="form-control buyukYaz vergiDairesi" placeholder="Vergi Dairesi" autocomplete="off">
                 </div>
              </div>
           </div>
           <div class="col-sm-6 s2">
              <div class="row form-group">
                 <div class="col-sm-2"><label>İl/İlçe</label></div>
                <div class="col-sm-5">
                <select name="il" id="country" class="form-control form-select" style="width:100%!important;">
                    <option value="" selected disabled>-Seçiniz-</option>
                    @foreach($countries as $item)
                    <option value="{{ $item->id }}">{{ $item->name}}</option>
                    @endforeach
                </select>
                </div>
                <div class="col-sm-5">
                <select name="ilce" id="city" class="form-control form-select" style="width:100%!important;">
                    <option value="" selected disabled>-Seçiniz-</option>                              
                </select>
                </div>
              </div>

              <div class="row form-group">
                 <div class="col-md-2 rw1"><label>Adres <span style="font-weight: bold; color: red;font-size:12px;">*</span></label></div>
                 <div class="col-md-10 rw2"><textarea name="adres" class="form-control buyukYaz adres" placeholder="Adres" rows="3" style="resize: none !important"></textarea></div>
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
        <div class="row form-group">
          <div class="col-5 rw1 col-form-label"><input type="text" name="aciklama[]" class="form-control aciklama aciklama0 buyukYaz" placeholder="Ürün" autocomplete="off"></div>
          <div class="col-2 rw2 col-form-label"><input type="text" name="miktar[]" onkeyup="sayiKontrol(this)" class="form-control miktar miktar0" autocomplete="off"></div>
          <div class="col-2 rw3 col-form-label"><input type="text" name="fiyat[]" onkeyup="sayiKontrol(this)" class="form-control fiyat fiyat0" autocomplete="off"></div>
          <div class="col-3 rw4 col-form-label"><input type="text" name="tutar[]" onkeyup="sayiKontrol(this)" class="form-control tutar tutar0" autocomplete="off"></div>
        </div>
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
                  <option value="{{$method->id}}">{{$method->odemeSekli}}</option>
                @endforeach
              </select>
            </div>
           
          </div>

          <div class="row form-group" style="border:0">
            <div class="col-md-4 rw1"><label>Toplam Yazıyla</label></div>
            <div class="col-md-8 rw2"><input type="text" name="toplamYazi" autocomplete="off" class="form-control buyukYaz toplamYazi"></div>
          </div>

          <div class="row form-group" style="border:0">
            <div class="col-md-4 rw1"><label>Fatura No<span style="font-weight: bold; color: red;">*</span></label></div>
            <div class="col-md-8 rw2">
              <input type="text" name="faturaNumarasi" class="form-control buyukYaz faturaNumarasi" value="" required>
            </div>
          </div>

          <div class="row form-group" style="border:0">
            <div class="col-md-4 rw1"><label>E-Arşiv<span style="font-weight: bold; color: red;">*</span></label></div>
            <div class="col-md-8 rw2">
              <input type="file" class="form-control" name="document" id="customFile" required>
            </div>
          </div>       
        </div>
      </div>

      <div class="card col-lg-6 f4">
        <div class="card-body" style="padding:17px 5px">
          <div class="row form-group">
            <div class="col-md-8 rw1"><label>Toplam<span style="font-weight: bold; color: red;">*</span></label></div>
            <div class="col-md-4 rw2"><input type="text" onkeyup="sayiKontrol(this)" name="toplam" autocomplete="off" class="form-control toplam" required></div>
          </div>

          <div class="row form-group">
          <div class="col-md-8 rw1"><label>İndirim</label></div>
          <div class="col-md-4 rw2"><input type="text" onkeyup="sayiKontrol(this)" name="indirim" autocomplete="off" class="form-control indirim"></div>
        </div>
        <div class="row form-group">
          <div class="col-md-8 rw1"><label>Ara Toplam</label></div>
          <div class="col-md-4 rw2"><input type="text" onkeyup="sayiKontrol(this)" name="araToplam" autocomplete="off" class="form-control araToplam"></div>
        </div>

          <div class="row form-group">
            <div class="col-md-6 rw1"><label>KDV %</label></div>
            <div class="col-md-2 rw2"><input type="text" onkeyup="sayiKontrol(this)" name="kdvTutar" autocomplete="off" class="form-control kdvTutar" value="20" style="text-align: center;"></div>
            <div class="col-md-4 rw2"><input type="text" onkeyup="sayiKontrol(this)" name="kdv" class="form-control kdv" value="0"></div>
          </div>

          <div class="row form-group" style="padding-bottom: 0">
            <div class="col-md-8 rw1"><label>Genel Toplam<span style="font-weight: bold; color: red;">*</span></label></div>
            <div class="col-md-4 rw2"><input type="text" onkeyup="sayiKontrol(this)" name="genelToplam" autocomplete="off" class="form-control genelToplam" required></div>
          </div>
               
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-12 gonderBtn">
        <input type="submit" class="btn btn-sm btn-info waves-effect waves-light" value="Kaydet">
      </div>
    </div>
  </div>
</form>

<script type="text/javascript">
$(document).ready(function(){
    $('#editInvo').on('click', '.invoic_e', function(e){
        var id = $(this).attr("data-bs-id");
        $.ajax({
            url: "/fatura/goruntule/" + id
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
</script>

<script>
  $(document).ready(function () {
    $('#editInvo').submit(function (event) {
      var formIsValid = true;
      $(this).find('input, select').each(function () {
        var isRequired = $(this).prop('required');
        var isEmpty = !$(this).val();

        if (isRequired && isEmpty) {
          formIsValid = false;
          return false;
        }
      });

      if (!formIsValid) {
        event.preventDefault();
        alert('Lütfen zorunlu alanları doldurun.');
        return false;
      }
    });
  });
</script>

<script>
  $(document).ready(function () {
    $('#editInvo').submit(function (event) {
      event.preventDefault();
      var formData = new FormData(this);
      $.ajax({
        url: $(this).attr("action"),
        type: "POST",
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        success: function (data) {
          if (data === false) {
            
            window.location.reload(true);
          } else {
            alert("Fatura güncellendi");
            $('#datatableInvoice').DataTable().ajax.reload();
            $('#editInvoiceModal').modal('hide');
          }
        },
        error: function (xhr, status, error) {
          alert("Güncelleme başarısız!");
          window.location.reload(true);
        },
      });
    });
  });
</script>
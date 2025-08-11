<form method="post" id="editInvo" action="{{ route('update.invoices', $firma->id)}}" enctype="multipart/form-data" class="needs-validation" novalidate>
  @csrf
  <div class="card f5">
    <div class="card-header ch1" style="padding: 3px 10px;">
      <div class="tarihWrap">
        <label style="text-align: left;width: auto;display: inline-block;margin: 0;">Tarih<span style="font-weight: bold; color: red;">*</span></label>
        <input type="date" name="faturaTarihi" class="form-control datepicker kayitTarihi"  value="{{ \Carbon\Carbon::parse($invoice_id->faturaTarihi)->format('Y-m-d')}}" style="width: 150px;display: inline-block;background:#fff" required>
      </div>

      <span><a href="#" data-id="" class="faturaMusteriDuzenleBtn"><i class="fas fa-edit" style="font-size: 15px;color: red;text-shadow: none;"></i></a></span>
      <div class="clearfix"></div>
    </div>
  </div> 

  <div class="card f2">
     <div class="card-header">MÜŞTERİ BİLGİSİ</div>
     <div class="card-body">
        <div class="row">
           <div class="col-sm-6 s1">
              <div class="row form-group">
                <div class="col-md-3 rw1"><label>Servis: </label></div>
                <div class="col-md-9 rw2 d-flex flex-wrap align-items-center gap-2">

                    <span>{{$invoice_id->servisid}}</span><a href="{{ route('all.services', [$firma->id, 'did' => $invoice_id->servisid]) }}" target="_blank" class="servisiAc btn btn-outline-danger px-2 py-1"style="font-size: 13px; line-height: 1.3;">Servisi Aç</a>
                </div>
              </div>
              <div class="kisaMusteriBil" style="font-size: 14px">
             
          	<span><strong>deneme(BİREYSEL) </strong></span>
          	

          	<span>sdsdg arnavutköy/istanbul</span>
          	
          <span>TC: 11111111111</span>
          	
          	<span>VERGİ NO/DAİRESİ: 121212/bursa</span>
          	

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
<link href="{{ asset('frontend/css/invoices/add_invoices.css') }}" rel="stylesheet" type="text/css" />
@if(App\Services\InvoiceIntegrationFactory::hasIntegration($firma->id))
    <div class="alert alert-info mb-3">
        <i class="fas fa-sync-alt"></i> 
        <strong>Paraşüt Entegrasyonu Aktif:</strong> 
        Faturanız otomatik olarak Paraşüt'e gönderilecek ve e-Arşiv PDF'i oluşturulacaktır.
    </div>
@else
    <div class="alert alert-secondary mb-3">
        <i class="fas fa-info-circle"></i> 
        Fatura entegrasyonu aktif değil. Manuel olarak e-Arşiv PDF yüklemeniz gerekir.
    </div>
@endif

<form method="post" id="addInvo" action="{{ route('store.invoices', $firma->id)}}" enctype="multipart/form-data" class="needs-validation" novalidate>
  @csrf
  <input type="hidden" name="form_token" id="invoiceFormToken" value="">
  
  <div class="card card-invoices d-md-none d-flex f5">
    <div class="ch1" style="padding: 3px 10px;">
      <div class="tarihWrap">
        <label style="text-align: left;width: auto;display: inline-block;margin: 0; margin-right: 2px;">Tarih<span style="font-weight: bold; color: red;">*</span></label>
        <input type="date" name="faturaTarihi" class="form-control datepicker kayitTarihi" value="{{date('Y-m-d')}}" style="width: 150px!important;display: inline-block;background:#fff" required>
      </div>
      <div class="clearfix"></div>
    </div>
  </div> 

  <div class="card card-invoices f2">
     <div class="card-header card-invoices-header d-flex flex-column flex-md-row justify-content-md-between align-items-md-center">
        <span>MÜŞTERİ BİLGİSİ</span>
        <div class="tarihWrap d-md-flex d-none mt-2 mt-md-0">
            <label style="text-align: left;width: auto;display: inline-block;margin: 0; margin-right: 2px;">Tarih<span style="font-weight: bold; color: red;">*</span></label>
            <input type="date" name="faturaTarihi" class="form-control datepicker kayitTarihi" value="{{date('Y-m-d')}}" style="width: 150px!important;display: inline-block;background:#fff" required>
        </div>
     </div>
     <div class="card-body card-invoices-body">
        <div class="row">
           <div class="col-sm-6 s1">
              <div class="row form-group">
                <div class="col-md-4 rw1"><label>Servis Ara</label></div>
                <div class="col-md-8 rw2 d-flex flex-wrap align-items-center gap-3">
                    <input id="search" type="text" name="servisid" class="form-control servisid" data-bs-id="" autocomplete="off" placeholder="Servis ID" style="flex: 1 1 auto; max-width: 160px;">
                    <a href="#" target="_blank" class="servisiAc btn btn-outline-danger px-2 py-1" style="font-size: 13px; line-height: 1.3;">Servisi Aç</a>
                </div>
              </div>

              {{-- Müşteri Tipi Alanı Eklendi --}}
              <div class="row form-group">
                 <div class="col-md-4 rw1"><label>Müşteri Tipi <span style="font-weight: bold; color: red;">*</span></label></div>
                 <div class="col-md-8 rw2">
                   <select class="form-select musteriTipi" name="musteriTipi" required>
                     <option value="2">KURUMSAL</option>
                     <option value="1">BİREYSEL</option>
                   </select>
                 </div>
              </div>

              <div class="row form-group">
                 <div class="col-md-4 rw1"><label><span class="musteriAdiSpan">Müşteri Adı</span> <span style="font-weight: bold; color: red;">*</span></label></div>
                 <div class="col-md-8 rw2">
                   <input type="text" name="adSoyad" class="form-control buyukYaz adSoyad" data-id="" autocomplete="off" placeholder="Müşteri Adı" required>
                 </div>
              </div>
              
              <input type="hidden" name="mid" class="eskiMusteriId" value="">

              {{-- TC No Alanı Eklendi --}}
              <div class="row form-group" id="tcNo" style="display: none;">
                 <div class="col-md-4 rw1"><label>T.C. No</label></div>
                 <div class="col-md-8 rw2">
                   <input type="number" name="tcNo" class="form-control tcNo" autocomplete="off" placeholder="Kimlik No">
                 </div>
              </div>

              <div class="row form-group" id="vergiBox">
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
                 <div class="col-sm-2"><label>İl/İlçe <span style="font-weight: bold; color: red;">*</span></label></div>
                <div class="col-sm-5 col-6">
                <select name="il" id="country" class="form-control form-select" style="width:100%!important;" required>
                    <option value="" selected disabled>-Seçiniz-</option>
                    @foreach($countries as $item)
                    <option value="{{ $item->id }}">{{ $item->name}}</option>
                    @endforeach
                </select>
                </div>
                <div class="col-sm-5 col-6">
                <select name="ilce" id="city" class="form-control form-select" style="width:100%!important;" required>
                    <option value="" selected disabled>-Seçiniz-</option>                              
                </select>
                </div>
              </div>

              <div class="row form-group">
                 <div class="col-md-2 rw1"><label>Adres <span style="font-weight: bold; color: red;font-size:12px;">*</span></label></div>
                 <div class="col-md-10 rw2"><textarea name="adres" class="form-control buyukYaz adres" placeholder="Adres" rows="3" style="resize: none !important" required></textarea></div>
              </div>
           </div>
        </div>
     </div>
  </div>

  {{-- Ürün Satırları --}}
  <div class="card card-invoices f2">
    <div class="card-body card-invoices-body">
      <div class="row form-group head">
        <div class="col-3 rw1"><label>Cinsi</label></div>
        <div class="col-3 rw2"><label>Miktar</label></div>
        <div class="col-3 rw3"><label>Fiyat</label></div>
        <div class="col-3 rw4"><label>Tutar</label></div>
      </div>

      <div class="satirBody mb-1">
        <div class="row form-group fatura-mobil-add">
          <div class="col-3 rw1"><input type="text" name="aciklama[]" class="form-control aciklama aciklama0 buyukYaz" placeholder="Ürün" autocomplete="off"></div>
          <div class="col-3 rw2"><input type="text" name="miktar[]" onkeyup="sayiKontrol(this)" class="form-control miktar miktar0" autocomplete="off"></div>
          <div class="col-3 rw3"><input type="text" name="fiyat[]" onkeyup="sayiKontrol(this)" class="form-control fiyat fiyat0" autocomplete="off"></div>
          <div class="col-3 rw4"><input type="text" name="tutar[]" onkeyup="sayiKontrol(this)" class="form-control tutar tutar0" autocomplete="off"></div>
        </div>
      </div>

      <div class="row form-group" style="margin: 0;border: 0;">
        <button type="button" class="col-xs-12 form-control btn btn-primary2 satirEkle" data-id="1" style="color: #fff;display: inline-block;">Satır Ekle</button>
      </div>
    </div>
  </div>
       
  <div class="row cardRow1 mb-1 fatura-mobil-add">
    <div class="col-lg-6 mb-3 mb-lg-0 custom-p-m">
      <div class="card card-invoices f3 h-100">
        <div class="card-body card-invoices-body">
          
          {{-- KDV'li/KDV'siz Fiyat Hesaplama Eklendi --}}
          <div class="row form-group" style="border:0">
            <div class="col-md-6 rw1"><input type="text" autocomplete="off" class="form-control kdvliFiyat" placeholder="KDV'li Fiyat"></div>
            <div class="col-md-6 rw2"><input type="text" class="form-control kdvsizFiyat" placeholder="KDV'siz Fiyat" disabled></div>
          </div>

          {{-- Tevkifat Kodu Eklendi --}}
          <div class="row form-group" style="border:0">
            <div class="col-md-4 rw1"><label>Tevkifat Kodu</label></div>
            <div class="col-md-8 rw2">
              <select class="form-select tevkifatKodu" name="tevkifatKodu">
                <option value="">Seçiniz</option>
                {{-- Tevkifat kodlarını buraya ekleyin --}}
              </select>
            </div>
          </div>

          {{-- KDV Kodu Eklendi --}}
          <div class="row form-group" style="border:0">
            <div class="col-md-4 rw1"><label>KDV Kodu</label></div>
            <div class="col-md-8 rw2">
              <select class="form-select kdvKodu" name="kdvKodu">
                <option value="">Seçiniz</option>
                {{-- KDV kodlarını buraya ekleyin --}}
              </select>
            </div>
          </div>

          {{-- KDV Açıklaması Eklendi --}}
          <div class="row form-group" style="border:0">
            <div class="col-md-4 rw1"><label>KDV Açıklaması</label></div>
            <div class="col-md-8 rw2">
              <input type="text" name="kdvAciklama" class="form-control kdvAciklama">
            </div>
          </div>

          {{-- Fatura Açıklaması Eklendi --}}
          <div class="row form-group" style="border:0">
            <div class="col-md-4 rw1"><label>Fatura Açıklaması</label></div>
            <div class="col-md-8 rw2">
              <input type="text" name="faturaAciklama" class="form-control faturaAciklama">
            </div>
          </div>

          <div class="row form-group" style="border:0">
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

          {{-- Toplam Yazıyla alanını kaldırdım, genelde gerekmiyor --}}

          @if(!App\Services\InvoiceIntegrationFactory::hasIntegration($firma->id))
          <div class="row form-group" style="border:0">
            <div class="col-md-4 rw1"><label>Fatura No<span style="font-weight: bold; color: red;">*</span></label></div>
            <div class="col-md-8 rw2">
              <input type="text" name="faturaNumarasi" class="form-control buyukYaz faturaNumarasi" value="">
            </div>
          </div>

          <div class="row form-group" style="border:0">
            <div class="col-md-4 rw1"><label>E-Arşiv<span style="font-weight: bold; color: red;">*</span></label></div>
            <div class="col-md-8 rw2">
              <input type="file" class="form-control" name="document" id="customFile">
            </div>
          </div>
          @endif
        </div>
      </div>
    </div>

    <div class="col-lg-6 custom-p-r-m">
      <div class="card card-invoices f4 h-100">
        <div class="card-body card-invoices-body" style="padding:17px 5px">
          <div class="row form-group">
            <div class="col-md-4 rw1"><label>Toplam<span style="font-weight: bold; color: red;">*</span></label></div>
            <div class="col-md-8 rw2 custom-rw2"><input type="text" onkeyup="sayiKontrol(this)" name="toplam" autocomplete="off" class="form-control toplam" required></div>
          </div>

          <div class="row form-group">
            <div class="col-md-4 rw1"><label>İndirim</label></div>
            <div class="col-md-8 rw2 custom-rw2"><input type="text" onkeyup="sayiKontrol(this)" name="indirim" autocomplete="off" class="form-control indirim" value="0.00"></div>
          </div>
          
          <div class="row form-group">
            <div class="col-md-4 rw1"><label>Ara Toplam</label></div>
            <div class="col-md-8 rw2 custom-rw2"><input type="text" onkeyup="sayiKontrol(this)" name="araToplam" autocomplete="off" class="form-control araToplam"></div>
          </div>

          <div class="row form-group">
            <div class="col-md-2 rw1"><label>KDV %</label></div>
            <div class="col-md-2 col-6 rw2"><input type="text" onkeyup="sayiKontrol(this)" name="kdvTutar" autocomplete="off" class="form-control kdvTutar" value="20"></div>
            <div class="col-md-8 custom-rw2 col-6 rw2"><input type="text" onkeyup="sayiKontrol(this)" name="kdv" class="form-control kdv" value="0"></div>
          </div>

          {{-- Tevkifat Oranı ve Tutarı Eklendi --}}
          <div class="row form-group">
            <div class="col-md-6 rw1"><label>Tevkifat Oranı</label></div>
            <div class="col-md-2 col-6 rw2">
              <select class="form-select tevkifatOrani" name="tevkifatOrani">
                <option value="0">0</option>
                <option value="2">2/10</option>
                <option value="3">3/10</option>
                <option value="4">4/10</option>
                <option value="5">5/10</option>
                <option value="7">7/10</option>
                <option value="9">9/10</option>
              </select>
            </div>
            <div class="col-md-4 custom-rw2 col-6 rw2">
              <input type="text" class="form-control tevkifatTutari" disabled>
              <input type="hidden" name="tevkifatTutari" class="tevkifatTutari">
            </div>
          </div>

          <div class="row form-group" style="padding-bottom: 0">
            <div class="col-md-4 rw1"><label>Genel Toplam<span style="font-weight: bold; color: red;">*</span></label></div>
            <div class="col-md-8 rw2 custom-rw2"><input type="text" onkeyup="sayiKontrol(this)" name="genelToplam" autocomplete="off" class="form-control genelToplam" required></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="row">
    <div class="col-sm-12 gonderBtn">
      <input type="submit" class="btn btn-sm btn-info waves-effect waves-light" value="Kaydet">
    </div>
  </div>
</form>

{{-- Mevcut scriptler... --}}

{{-- Müşteri Tipi değişiminde TC No / Vergi alanlarını göster/gizle --}}
<script>
$(document).ready(function() {
    $('#tcNo').hide();
    
    $('.musteriTipi').on('change', function() {
        var val = $(this).val();
        if (val == 2) {
            $('#vergiBox').show();
            $('#tcNo').hide();
        } else {
            $('#vergiBox').hide();
            $('#tcNo').show();
        }
    });

    // KDV'li fiyat hesaplama
    $(".kdvliFiyat").on("input", function() {
        var kdvTutari = parseFloat($(this).val());
        if (!isNaN(kdvTutari)) {
            var kdvOrani = 0.20;
            var kdvsizFiyat = kdvTutari / (1 + kdvOrani);
            $(".kdvsizFiyat").val(kdvsizFiyat.toFixed(2));
        } else {
            $(".kdvsizFiyat").val("");
        }
    });

    // Tevkifat hesaplama - mevcut kdvHesapla fonksiyonuna ekleme
    function kdvHesapla(toplam) {
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
        } else {
            var genelToplam = araToplam + kdv;
            kdv = parseFloat(kdv.toFixed(2));
            genelToplam = parseFloat(genelToplam.toFixed(2));
        }
        
        $(".toplam").val(toplam);
        $(".araToplam").val(araToplam);
        $(".genelToplam").val(genelToplam);
        $(".kdv").val(kdv);
    }

    // Tevkifat oranı değiştiğinde
    $('.tevkifatOrani').on('change', function() {
        var sonucToplam = Number($(".toplam").val());
        if (sonucToplam > 0) {
            kdvHesapla(sonucToplam);
        }
    });
});
</script>
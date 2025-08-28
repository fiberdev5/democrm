@extends('frontend.secure.user_master')
@section('user')
<div class="page-content">
  <div class="container-fluid">
    <div class="row pageDetail">
      <div class="col-12">
        <div class="card">
          <div class="card-header sayfaBaslik">
            Depo Stoklar
          </div>
          <div class="card-body">
            <a data-bs-toggle="modal" data-bs-target="#addStockModal" class="btn btn-success btn-sm addStock">
              <i class="fas fa-plus"></i> <span>Stok Kartı Ekle</span>
            </a>
            <a href="{{ route('consignmentdevice', $firma->id) }}"  data-bs-target="#supplierModal" class="btn btn-info btn-sm supplierBtn">
              <i class="fas fa-industry"></i> Konsinye Cihazlar 
            </a>
            <!-- Filtre dropdown butonu -->
            <div class="searchWrap float-end">
              <div class="btn-group mb-2 ">
                <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Filtrele <i class="mdi mdi-chevron-down"></i>
                </button>
                <div class="dropdown-menu p-3" style="min-width: 250px;">
                  
                  <!-- Raf -->
                  <div class="item mb-2">
                    <div class="row align-items-center">
                      <label class="col-sm-5 mb-0">Raf</label>
                      <div class="col-sm-7">
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
                  <div class="item mb-2">
                    <div class="row align-items-center">
                      <label class="col-sm-5 mb-0">Marka</label>
                      <div class="col-sm-7">
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
                  <div class="item mb-2">
                    <div class="row align-items-center">
                      <label class="col-sm-5 mb-0">Cihaz</label>
                      <div class="col-sm-7">
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
                      <label class="col-sm-5 mb-0">Personel</label>
                      <div class="col-sm-7">
                        <select id="personel" class="form-select form-select-sm">
                          <option value="">Hepsi</option>
                          @foreach($personeller as $personel)
                            <option value="{{ $personel->user_id }}"
                              {{ request('personel') == $personel->user_id ? 'selected' : '' }}>{{ $personel->name }}</option>
                          @endforeach
                        </select>
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
                  <th>Fiyat</th>
                  <th>Adet</th>
                  <th>Raf</th>
                  <th>Marka/Cihaz</th>
                  <th style="width: 96px;">Düzenle</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>

            <div class="mt-3" style="font-size: 16px;">
              <div><strong>Toplam Adet:</strong> <span id="toplamAdet">0</span></div>
              <div><strong>Toplam Değer:</strong> <span id="toplamFiyat">0 ₺</span></div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modallar -->
<div id="addStockModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addStockModalLabel" aria-hidden="true">
  <div class="modal-dialog ">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" id="addStockModalLabel">Stok Kartı Ekle</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">Yükleniyor...</div>
    </div>
  </div>
</div>


<div id="editStockModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editStockModalLabel" aria-hidden="true">
  {{-- modal-dialog sınıfı JS tarafından eklenecek --}}
  <div class="modal-dialog"> 
    <div class="modal-content">
      {{-- İçerik AJAX ile buraya gelecek --}}
    </div>
  </div>
</div>

<script>
$(document).ready(function(){
  var firma_id = {{ $firma->id }};

  $(".addStock").click(function(){
    $.ajax({
      url: "/"+firma_id+"/stok-ekle/"
    }).done(function(data){
      if($.trim(data) === "-1"){
        location.reload(true);
      } else {
        $('#addStockModal').modal('show');
        $('#addStockModal .modal-body').html(data);
        // AJAX ile içerik yüklendikten SONRA Select2'leri başlat
        initializeStockFormSelect2s();
      }
    });
  });

  // Select2'leri başlatan fonksiyon
  function initializeStockFormSelect2s() {
    var tenantId = {{ $firma->id }};
    
    function initializeSelect2(selector, placeholder, url) {
      // Önce mevcut Select2'yi temizle
      if ($(selector).hasClass("select2-hidden-accessible")) {
        $(selector).select2('destroy');
      }
      
      var parentModal = $(selector).closest('.modal');
      if (parentModal.length === 0) {
        parentModal = $('.modal:visible').last();
      }
      
      $(selector).select2({
        theme: "bootstrap-5",
        placeholder: placeholder,
        allowClear: true,
        dropdownParent: parentModal.length ? parentModal : $('body'),
        ajax: {
          url: url,
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return { q: params.term };
          },
          processResults: function (data) {
            return { results: data };
          },
          cache: true
        }
      });
    }

    // Ana formdaki Select2'leri başlat
    initializeSelect2('#addStock select[name="marka_id"]', 'Marka ara...', '/' + tenantId + '/search-brands');
    initializeSelect2('#addStock select[name="cihaz_id"]', 'Cihaz türü ara...', '/' + tenantId + '/search-devices');
    initializeSelect2('#addStock select[name="urunKategori"]', 'Kategori ara...', '/' + tenantId + '/search-categories');
    initializeSelect2('#addStock select[name="raf_id"]', 'Raf ara...', '/' + tenantId + '/search-shelves');
  }

  // Ana modal temizleme işlemi
  $("#addStockModal").on("hidden.bs.modal", function(e){
    var modal = $(this);
    
    if (e.target === this) {
      setTimeout(function(){
        if (!$('.modal.show').length) {
          console.log("Modal içeriği temizleniyor");
          modal.find(".modal-body").html("");
   
          // Tüm backdrop'ları kaldır
          $('.modal-backdrop').remove();
          // Body'den modal sınıflarını kaldır
          $('body').removeClass('modal-open').css('overflow', '').css('padding-right', '');
          // HTML'den de kaldır (bazı durumlarda gerekebilir)
          $('html').removeClass('modal-open').css('overflow', '').css('padding-right', '');
          
        } else {
          console.log("Başka modal açık, temizleme yapılmıyor");
        }
      }, 100);
    }
  });

  // Alt modal'ların kapatılması sırasında da backdrop temizliği
  $('#addBrandModal, #addDeviceTypeModal, #addCategoryModal, #addShelfModal').on('hidden.bs.modal', function(){
    setTimeout(function(){
      // Eğer hiçbir modal açık değilse backdrop'u temizle
      if (!$('.modal.show').length) {
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('overflow', '').css('padding-right', '');
        $('html').removeClass('modal-open').css('overflow', '').css('padding-right', '');
      }
    }, 50);
  });

  // Düzenleme modalı kapatıldığında ana formu yeniden başlat
  $('#editStockModal').on('hidden.bs.modal', function(){
    setTimeout(function(){
      // Ana modal hala açıksa Select2'leri yeniden başlat
      if ($('#addStockModal').is(':visible')) {
        initializeStockFormSelect2s();
      }
    }, 100);
  });

  $('#datatableStock').on('click', '.editStock', function(){
    var id = $(this).data('bs-id');
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
  });
});
</script>


@endsection

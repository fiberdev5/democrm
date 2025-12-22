@extends('frontend.secure.user_master')
@section('user')

<div class="page-content">
  <div class="container-fluid">
    <div class="row pageDetail">
      <div class="col-12">
        <div class="card">
          <div class="card-header sayfaBaslik">
            Firmalarımız
          </div>
          <div class="card-body">
            <table id="datatableTenants" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
              <div class="searchWrap float-end">
                <div class="btn-group mb-2 ">
                  <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Filtrele <i class="mdi mdi-chevron-down"></i>
                  </button>
                  <div class="dropdown-menu">
                    <div class="item">
                      <div class="row">
                        <label class="col-sm-5">Durum</label>
                        <div class="col-sm-7">
                          <select name="musteriTipi" id="musteriTipi" class="form-select">
                            <option value="">Hepsi</option>
                            <option value="1" >Bireysel</option>
                            <option value="2">Kurumsal</option>
                          </select>
                        </div>
                      </div>
                    </div>

                    <div class="item">
                      <div class="row">
                        <label class="col-sm-5">İl</label>
                        <div class="col-sm-7">
                          <select name="il" id="countrySelect" class="form-control form-select" style="width:100%!important;">
                            <option value="" selected disabled>-Seçiniz-</option>
                            @foreach($countries as $item)
                              <option value="{{ $item->id }}">{{ $item->name}}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                    </div>

                    <div class="item">
                      <div class="row">
                        <label class="col-sm-5">İlçe</label>
                        <div class="col-sm-7">
                          <select name="ilce" id="citySelect" class="form-control form-select" style="width:100%!important;">
                            <option value="" selected disabled>-Seçiniz-</option>                              
                          </select>
                        </div>
                      </div>
                    </div>

                  </div>
                </div><!-- /btn-group -->
              </div>
              
              <thead class="title">
                <tr>
                  <th style="width: 10px">ID</th>
                  <th data-priority="2">Ad Soyad</th>
                  <th>Telefon</th>
                  <th>Adres</th>
                  <th>Durum</th>
                  <th data-priority="1" style="width: 96px;">Düzenle</th>
                </tr>
              </thead>
              <tbody>
               
              </tbody>
            </table>
          </div>
        </div>
      </div> <!-- end col -->
    </div> <!-- end row -->
  </div>
</div>

<!-- edit modal content -->
<div class="modal fade" id="editTenantModal" tabindex="-1" aria-labelledby="tenantDetailLabel" aria-hidden="true" style="background: rgba(0,0,0,0.5);">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

      <div class="modal-body" style="padding: 0;">
        Yükleniyor...
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Kullanıcıları Görüntüleme Modal -->
<div class="modal fade" id="tenantUsersModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fas fa-users me-2"></i>
          <span id="modalTenantName">Firma</span> Kullanıcıları
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="tenantUsersContent">
        <div class="text-center p-4">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Yükleniyor...</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Impersonation Onay Modal -->
<div class="modal fade" id="impersonationModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title">
          <i class="fas fa-user-secret me-2"></i>Kullanıcı Kimliği Değiştir
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-warning">
          <i class="fas fa-exclamation-triangle me-2"></i>
          <strong>Dikkat!</strong> Bu işlem kayıt altına alınacaktır.
        </div>
        <p>
          <strong><span id="targetCompanyName"></span></strong> firmasında 
          <strong><span id="targetUserName"></span></strong> olarak giriş yapmak üzeresiniz.
        </p>
        <div class="mb-3">
          <label for="impersonationReason" class="form-label">Sebep</label>
          <textarea class="form-control" id="impersonationReason" rows="2" 
                    placeholder="Bu işlemi neden yapıyorsunuz? (opsiyonel)"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
        <button type="button" class="btn btn-warning" id="confirmImpersonationBtn">
          <i class="fas fa-user-secret me-1"></i>Giriş Yap
        </button>
      </div>
    </div>
  </div>
</div>



<script type="text/javascript">
$(document).ready(function(){
    $('#datatableTenants').on('click', '.editTenant', function(e){
        var id = $(this).attr("data-bs-id");
        var firma_id = {{$firma->id}};
        $.ajax({
            url: "/"+ firma_id + "/firma-duzenle/" + id
        }).done(function(data) {
            if ($.trim(data) === "-1") {
                window.location.reload(true);
            } else {
                $('#editTenantModal').modal('show');
                $('#editTenantModal .modal-body').html(data);
            }
        });
    });
    $("#editTenantModal").on("hidden.bs.modal", function() {
      $('#editTenantModal .modal-body').html("");
    });
});

// Copy to clipboard function
function copyToClipboard(text, element) {
    navigator.clipboard.writeText(text).then(function() {
        // Success feedback
        const originalIcon = element.innerHTML;
        element.innerHTML = '<i class="mdi mdi-check text-success"></i>';
        element.classList.add('text-success');
        
        setTimeout(function() {
            element.innerHTML = originalIcon;
            element.classList.remove('text-success');
        }, 2000);
    }).catch(function(err) {
        console.error('Kopyalama hatası: ', err);
    });
}
</script>

<script>
$(document).ready(function () {
  var table = $('#datatableTenants').DataTable({
      processing: true,
      serverSide: true,
      language: {
        paginate: {
          previous: "<i class='mdi mdi-chevron-left'>",
          next: "<i class='mdi mdi-chevron-right'>"
        }
      },
      ajax: {
        url: "{{ route('all.tenants',$firma->id) }}",
        data: function(data) {
          data.search = $('input[type="search"]').val();
          data.tip = $('#musteriTipi').val();
          data.il = $('#countrySelect').val();
          data.ilce = $('#citySelect').val();
        }
      },
      'columns': [
        { data: 'id'},
        { data: 'name' },
        { data: 'tel' },
        { data: 'address' },
        { data: 'durum' },
        { data: 'action'}           
      ],
      drawCallback: function() {
        $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
      },
        "order": [[0, 'desc']],
        "columnDefs": [{
          "targets": 0,
          "className": "gizli"
        }],
        "oLanguage": {
            "sDecimal":        ",",
          "sEmptyTable":     "Tabloda herhangi bir veri mevcut değil",
          "sInfo":           "Firma Sayısı: _TOTAL_",
          "sInfoEmpty":      "Kayıt yok",
          "sInfoFiltered":   "",
          "sInfoPostFix":    "",
          "sInfoThousands":  ".",
          "sLengthMenu":     "_MENU_",
          "sLoadingRecords": "Yükleniyor...",
          "sProcessing":     "İşleniyor...",
          "sSearch":         "Firma Ara:",
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

  $('#musteriTipi').change(function(){
    table.draw();        
  });

  $('#countrySelect').change(function(){
    table.draw();        
  });

  $('#citySelect').change(function(){
    table.draw();        
  });

});
</script>

<script>
    $(document).ready(function() {
      // Ülke seçildiğinde şehirleri getir
      $("#countrySelect").change(function() {
        var selectedCountryId = $(this).val();
        if (selectedCountryId) {
          loadCities(selectedCountryId);
        }
      });
    
      // Şehirleri yüklemek için kullanılan fonksiyon
      function loadCities(countryId) {
        var citySelect = $("#citySelect");
        citySelect.empty(); // Önceki seçenekleri temizle
        citySelect.append(new Option("Yükleniyor...", "")); // Kullanıcıya yükleniyor bilgisi ver
    
        // AJAX isteğiyle şehirleri al
        $.get("/get-states/" + countryId, function(data) {
          citySelect.empty(); // Yükleniyor mesajını temizle
          citySelect.append(new Option("-Seçiniz-", "")); // İlk boş seçeneği ekle
          $.each(data, function(index, city) {
            citySelect.append(new Option(city.ilceName, city.id));
          });
        }).fail(function() {
          citySelect.empty(); // Hata durumunda temizle
          citySelect.append(new Option("Unable to load cities", ""));
        });
      }
    });
    </script>

@endsection

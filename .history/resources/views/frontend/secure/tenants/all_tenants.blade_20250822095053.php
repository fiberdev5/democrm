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

<!-- Modern Company Detail Modal -->
<div id="editTenantModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="companyDetailLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <!-- Modal Header with Gradient -->
      <div class="modal-header bg-gradient-primary text-white border-0 position-relative overflow-hidden" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="position-absolute top-0 start-0 w-100 h-100 opacity-10">
          <div class="bg-white" style="clip-path: polygon(0 0, 100% 0, 85% 100%, 0% 100%);"></div>
        </div>
        <div class="d-flex align-items-center position-relative">
          <div class="me-3">
            <div class="rounded-circle bg-white bg-opacity-20 d-flex justify-content-center align-items-center" 
                 style="width:60px; height:60px; backdrop-filter: blur(10px);">
              <i class="mdi mdi-domain text-white" style="font-size: 28px;"></i>
            </div>
          </div>
          <div>
            <h5 class="modal-title mb-1" id="companyDetailLabel">Müşteri Detayları</h5>
            <small class="text-white-50">Detaylı bilgiler ve işlemler</small>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white position-relative" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body p-0">
        <div id="modalContent" class="p-4">
          <div class="d-flex justify-content-center align-items-center py-5">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Yükleniyor...</span>
            </div>
            <span class="ms-2 text-muted">Bilgiler yükleniyor...</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Custom CSS for Modal -->
<style>
.bg-gradient-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.company-info-card {
  background: linear-gradient(135deg, #f8f9ff 0%, #f0f2ff 100%);
  border: 1px solid #e3e6f0;
  border-radius: 12px;
  transition: all 0.3s ease;
}

.company-info-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.info-icon {
  width: 45px;
  height: 45px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  transition: all 0.3s ease;
}

.info-icon.phone { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.info-icon.address { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
.info-icon.date { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
.info-icon.email { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }
.info-icon.web { background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%); }
.info-icon.person { background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); }

.copy-btn {
  opacity: 0;
  transition: all 0.3s ease;
}

.company-info-card:hover .copy-btn {
  opacity: 1;
}

.status-badge {
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.status-active {
  background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
  color: white;
}

.status-inactive {
  background: linear-gradient(135deg, #fc466b 0%, #3f5efb 100%);
  color: white;
}

.action-btn {
  border-radius: 10px;
  padding: 10px 20px;
  font-weight: 600;
  transition: all 0.3s ease;
  border: none;
}

.action-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.btn-edit {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.btn-delete {
  background: linear-gradient(135deg, #fc466b 0%, #3f5efb 100%);
  color: white;
}

.stats-card {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 15px;
  color: white;
  position: relative;
  overflow: hidden;
}

.stats-card::before {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  width: 100px;
  height: 100px;
  background: rgba(255,255,255,0.1);
  border-radius: 50%;
  transform: translate(30px, -30px);
}
</style>

<script type="text/javascript">
$(document).ready(function(){
    $('#datatableTenants').on('click', '.editTenant', function(e){
        var id = $(this).attr("data-bs-id");
        var firma_id = {{$firma->id}};
        
        // Modal'ı göster ve loading state'i ayarla
        $('#editTenantModal').modal('show');
        $('#modalContent').html(`
          <div class="d-flex justify-content-center align-items-center py-5">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Yükleniyor...</span>
            </div>
            <span class="ms-2 text-muted">Bilgiler yükleniyor...</span>
          </div>
        `);
        
        $.ajax({
            url: "/"+ firma_id + "/firma-duzenle/" + id,
            type: 'GET',
            success: function(data) {
                if ($.trim(data) === "-1") {
                    window.location.reload(true);
                } else {
                    $('#modalContent').html(data);
                }
            },
            error: function() {
                $('#modalContent').html(`
                  <div class="text-center py-5">
                    <i class="mdi mdi-alert-circle text-danger" style="font-size: 48px;"></i>
                    <h5 class="mt-3 text-danger">Hata Oluştu</h5>
                    <p class="text-muted">Bilgiler yüklenirken bir hata oluştu.</p>
                  </div>
                `);
            }
        });
    });
    
    $("#editTenantModal").on("hidden.bs.modal", function() {
        $('#modalContent').html("");
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
          "sInfo":           "Müşteri Sayısı: _TOTAL_",
          "sInfoEmpty":      "Kayıt yok",
          "sInfoFiltered":   "",
          "sInfoPostFix":    "",
          "sInfoThousands":  ".",
          "sLengthMenu":     "_MENU_",
          "sLoadingRecords": "Yükleniyor...",
          "sProcessing":     "İşleniyor...",
          "sSearch":         "Müşteri Ara:",
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
<style>
  .bolgeler option{
    font-size: 12px !important;
  }
  .cihazlar option{
    font-size: 12px !important;
  }
  .kaynaklar option{
    font-size: 12px !important;
  }
  .sag{
    padding-left: 0px !important;
  }
  @media (max-width: 767px) {
    .row{
        --bs-gutter-x: 0px !important;
    }
    .servisListe .card-body{
      height: auto !important;
    }
    .personelServisListele{
      margin-top: 5px;
    }
  }
</style>
<div class="row wrap">
  <div class="col-lg-4 sol">
    <div id="planlamaSearch" class="collapse show">
      <div class="card" style="margin-bottom:0!important;">
        <div class="card-header" style="padding: 5px!important;">
          <div class="card-body" style="padding: 5px!important;">
          <form id="filterForm">
            <div class="row form-group">
              <div class="col-md-4 rw1"><label>Tarih</label></div>
              <div class="col-md-8 rw2">
                <input type="date" class="form-control datepicker planTarih" value="{{ $tomorrow }}" style="background:#fff">
              </div>
            </div>

            <div class="row form-group">
              <div class="col-md-4 rw1"><label>İl Seç</label></div>
              <div class="col-md-8 rw2">
                <select class="form-control il" id="il">
                  @foreach ($iller as $item)
                    <option value="{{$item->id}}" {{ $item->id == 34 ? 'selected' : '' }}>{{$item->name}}</option>
                  @endforeach                               
                </select>
              </div>
            </div>

            <div class="row form-group">
              <div class="col-md-4 rw1"><label>Bölgeler</label></div>
              <div class="col-md-8 rw2">
                <select class="form-control bolgeler" id="ilce" multiple style="height: 155px">
                  <option value="0" selected>HEPSİ</option>
                </select>
              </div>
            </div>

            <div class="row form-group">
              <div class="col-md-4 rw1"><label>Cihazlar</label></div>
              <div class="col-md-8 rw2">
                <select class="form-control cihazlar" multiple style="height: 155px">
                  <option value="0" selected>HEPSİ</option>
                  @foreach($deviceTypes as $device)
                    <option style="text-transform: uppercase;" value="{{ $device->id }}">{{ $device->cihaz }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="row form-group">
              <div class="col-md-4 rw1"><label>Kaynaklar</label></div>
              <div class="col-md-8 rw2">
                <select class="form-control kaynaklar" multiple style="height: 100px">
                  <option value="0" selected>HEPSİ</option>
                  @foreach($serviceSources as $source)
                    <option style="text-transform: uppercase;" value="{{ $source->id }}">{{ $source->kaynak }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="row form-group">
              <div class="col-md-4 rw1"><label>Durumlar</label></div>
              <div class="col-md-8 rw2">
                <select class="form-control durumlar">
                  <option value="240">Atölyeye Aldır (Nakliye Gönder)</option>
                  <option value="264">Bayiye Gönder</option>
                  <option value="237">Cihaz Atölyeye Alındı</option>
                  <option value="246">Cihaz Tamir Edilemiyor</option>
                  <option value="261">Parça Hazır</option>
                  <option value="254">Şikayetçi</option>
                  <option value="252">Teslimata Hazır (Tamamlandı)</option>
                  <option value="235" selected>Yeni Servisler</option>
                  <option value="235-2">Yeni Servisler (Bayiye Gönder)</option>
                  <option value="248">Yeniden Teknisyen Yönlendir</option>
                </select>
              </div>
            </div>

            <div class="col-md-12" style="padding: 0 5px">
              <input type="submit" class="btn btn-block btn-primary btn-sm servisPlanListele" style="width:100%;" value="Listele">
            </div>
          </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-8 sag">
    <div class="card" style="margin-bottom: 0!important;">
      <div class="servisListe" style="padding: 0">
        <!-- Service list will be loaded here -->
      </div>         
    </div>
  </div>
</div>

<script>
  // Modal ve Servis Listesi Yönetimi İçin Geliştirilmiş Çözüm
$(document).ready(function() {
    // Global değişkenler
    let serviceListData = null;
    let isModalOpen = false;
    let lastFilterData = null;
    
    // İlçeleri yükle
    $("#il").on("change", function() {
        loadDistricts($(this).val());
    });

    // İlk yüklemeler
    loadDistricts($("#il").val());
    loadServiceList();

    // Form gönderimi
    $("#filterForm").on("submit", function(e) {
        e.preventDefault();
        loadServiceList();
    });

    // İlçe yükleme fonksiyonu
    function loadDistricts(cityId) {
        $('#ilce').html('<option value="0" selected>HEPSİ</option>');

        $.get('{{ route('service.districts', $firma->id) }}', 
            { city_id: cityId },
            function (districts) {
                districts.forEach(d =>
                    $('#ilce').append(
                        `<option value="${d.id}">${d.ilceName}</option>`
                    )
                );
            }
        ).fail(function (xhr) {
            console.error('Hata', xhr.status, xhr.responseText);
        });
    }

    // Servis listesi yükleme fonksiyonu - GELİŞTİRİLMİŞ
    function loadServiceList(forceReload = false) {
        // Modal açıkken yenilemeyi engelle (forceReload hariç)
        if (isModalOpen && !forceReload) {
            console.log('Modal açık, yenileme engellendi');
            return;
        }

        var formData = {
            planTarih: $(".planTarih").val().replace(/\//g, '-'),
            il: $(".il").val(),
            bolgeler: $(".bolgeler").val(),
            cihazlar: $(".cihazlar").val(),  
            kaynaklar: $(".kaynaklar").val(),
            durumlar: $(".durumlar").val()
        };

        // Form verilerini sakla
        lastFilterData = formData;

        $(".servisListe").html('<div class="text-center p-3"><i class="fas fa-spinner fa-spin"></i> Yükleniyor...</div>');
        
        $.ajax({
            url: "{{ route('service.list', $firma->id) }}",
            type: "GET",
            data: formData,
            success: function(data) {
                // Veriyi sakla
                serviceListData = data;
                
                // Modal açık değilse göster
                if (!isModalOpen) {
                    $(".servisListe").html(data);
                    initializeServiceListEvents();
                }
            },
            error: function() {
                $(".servisListe").html('<div class="alert alert-danger">Bir hata oluştu!</div>');
            }
        });
    }

    // Servis listesi event'lerini başlat
    function initializeServiceListEvents() {
        // Personel servis düzenle click eventi
        $(".personelServisDuzenle").off('click').on('click', function(e) {
            var id = $(this).attr("data-bs-id");
            var name = $(this).attr("data-bs-name");
            var firma_id = {{$firma->id}};
            
            $.ajax({
                url: "/" + firma_id + "/servis/duzenle/" + id
            }).done(function(data) {
                if ($.trim(data) === "-1") {
                    window.location.reload(true);
                } else {
                    $('#personelServisDuzenleModal').modal('show');
                    $('#personelServisDuzenleModal .modal-title').html(name+" ("+id+")");
                    $('#personelServisDuzenleModal .modal-body').html(data);
                }
            });
        });

        // Personel servisleri listeleme
        $('.personelServisListele').off('click').on('click', function () {
            const persID = $('.personelList').val();
            
            if(!persID) {
                alert('Lütfen bir personel seçin!');
                return;
            }

            $('.servisListe').html('<div class="text-center p-3"><i class="fa fa-spinner fa-spin"></i> Yükleniyor...</div>');

            $.ajax({
                url: `/${{{$firma->id}}}/servis-liste-getir/`,
                method: 'GET',
                data: { persID: persID },
                success: function(res) {
                    serviceListData = res;
                    $('.servisListe').html(res);
                    initializeServiceListEvents();
                },
                error: function() {
                    $('.servisListe').html('<div class="alert alert-danger">Liste alınamadı. Lütfen tekrar deneyin.</div>');
                }
            });
        });

        // Atama yap butonu
        $('#assignBtn').off('click').on('click', function () {
            const ids = $('input.selectService:checked').map((_,e)=>e.value).get();
            if (!ids.length) { 
                alert('Servis seçiniz'); 
                return; 
            }
            
            const servisidler = ids.join(',');
            const gidenDurum  = $(this).data('id');
            const personelID  = $(this).data('pers') || null;
            const gelenDurum  = $('.durumlar').val();

            // Modal açılacağını işaretle
            isModalOpen = true;

            if (personelID) {
                $.get("{{ route('service.plan.update.form', $firma->id) }}",
                    { servisidler, personel: personelID, gidenDurum })
                .done(html => {
                    $('#servisPersonelAtamaModal .modal-body').html(html);
                    $('#servisPersonelAtamaModal').modal('show');
                });
            } else {
                $('#servisPersonelAtamaModal .modal-body')
                    .html('<div class="text-center p-3"><i class="fa fa-spinner fa-spin"></i> Form yükleniyor…</div>');
                $('#servisPersonelAtamaModal').modal('show');

                $.get("{{ route('service.plan.form', $firma->id) }}",
                    { servisidler, gelenDurum, gidenDurum })
                .done(html => $('#servisPersonelAtamaModal .modal-body').html(html))
                .fail(() => $('#servisPersonelAtamaModal .modal-body')
                    .html('<div class="alert alert-danger">Form yüklenemedi.</div>'));
            }
        });
    }

    // Modal event'leri - GELİŞTİRİLMİŞ
    $('#servisPersonelAtamaModal').on('show.bs.modal', function () {
        isModalOpen = true;
        // Mevcut servis listesini koru
        if (serviceListData) {
            localStorage.setItem('tempServiceList', serviceListData);
        }
    });

    $('#servisPersonelAtamaModal').on('shown.bs.modal', function () {
        // Modal tamamen açıldıktan sonra, servis listesi boşsa yeniden yükle
        setTimeout(function() {
            checkServiceList();
        }, 100);
    });

    $('#servisPersonelAtamaModal').on('hide.bs.modal', function () {
        isModalOpen = false;
    });

    $('#servisPersonelAtamaModal').on('hidden.bs.modal', function () {
        isModalOpen = false;
        // Modal kapandıktan sonra listeyi yenile
        setTimeout(function() {
            restoreServiceList();
        }, 300);
    });

    // Diğer modallar için
    $('#personelServisDuzenleModal').on('show.bs.modal', function () {
        isModalOpen = true;
    });

    $('#personelServisDuzenleModal').on('hidden.bs.modal', function () {
        isModalOpen = false;
        setTimeout(function() {
            checkServiceList();
        }, 300);
    });

    // Servis listesini kontrol et
    function checkServiceList() {
        if ($('.servisListe').children().length === 0 || 
            $('.servisListe').html().trim() === '' ||
            $('.servisListe').html().includes('Yükleniyor...')) {
            
            restoreServiceList();
        }
    }

    // Servis listesini geri yükle
    function restoreServiceList() {
        // Önce localStorage'dan kontrol et
        const tempData = localStorage.getItem('tempServiceList');
        
        if (tempData && tempData !== 'null') {
            $('.servisListe').html(tempData);
            initializeServiceListEvents();
            localStorage.removeItem('tempServiceList');
        } else if (serviceListData) {
            // Hafızadan yükle
            $('.servisListe').html(serviceListData);
            initializeServiceListEvents();
        } else {
            // Son çare olarak yeniden yükle
            loadServiceList(true);
        }
    }

    // Responsive collapse
    if ($(window).width() < 992) {
        $("#planlamaSearch").removeClass("show");
    }

    // Sayfa görünürlüğü değiştiğinde kontrol et
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden && !isModalOpen) {
            setTimeout(function() {
                checkServiceList();
            }, 500);
        }
    });

    // Ajax global eventi - tüm ajax işlemleri tamamlandığında
    $(document).ajaxComplete(function(event, xhr, settings) {
        // Servis listesi ile ilgili bir ajax işlemi değilse ve modal açık değilse
        if (!settings.url.includes('servis-liste') && !isModalOpen) {
            setTimeout(function() {
                checkServiceList();
            }, 200);
        }
    });

    // Periyodik kontrol (son güvenlik önlemi)
    setInterval(function() {
        if (!isModalOpen) {
            const servisListeContent = $('.servisListe').html();
            if (!servisListeContent || servisListeContent.trim() === '' || 
                servisListeContent.includes('Yükleniyor...')) {
                checkServiceList();
            }
        }
    }, 5000); // Her 5 saniyede bir kontrol et
});
</script>
{{-- 
BU DOSYA İKİ BÖLÜMDEN OLUŞUYOR:
1. İlk dosya (servis listesi ana görünümü)
2. İkinci dosya (servis listesi içeriği)
--}}

{{-- ============ 1. DOSYA: ana-sayfa.blade.php (veya index.blade.php) ============ --}}
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

{{-- ================ ÖNEMLİ: ESKİ SCRIPT'LERİ SİLİN VE BUNU EKLEYIN ================ --}}
<script>
$(document).ready(function() {
    // ========== GLOBAL DEĞİŞKENLER ==========
    let serviceListData = null;
    let isModalOpen = false;
    let lastFilterData = null;
    let serviceListBackup = null;
    
    // ========== İLK YÜKLEMELER ==========
    loadDistricts($("#il").val());
    loadServiceList();

    // ========== EVENT LISTENERS ==========
    $("#il").on("change", function() {
        loadDistricts($(this).val());
    });

    $("#filterForm").on("submit", function(e) {
        e.preventDefault();
        loadServiceList();
    });

    // ========== İLÇE YÜKLEME ==========
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
            console.error('İlçe yükleme hatası:', xhr.status, xhr.responseText);
        });
    }

    // ========== SERVİS LİSTESİ YÜKLEME - YENİ VERSİYON ==========
    function loadServiceList(forceReload = false) {
        // Modal açıkken yenilemeyi engelle
        if (isModalOpen && !forceReload) {
            console.log('Modal açık, liste yenileme engellendi');
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

        lastFilterData = formData;
        
        // Mevcut veriyi yedekle
        backupServiceList();

        $(".servisListe").html('<div class="text-center p-3"><i class="fas fa-spinner fa-spin"></i> Yükleniyor...</div>');
        
        $.ajax({
            url: "{{ route('service.list', $firma->id) }}",
            type: "GET",
            data: formData,
            success: function(data) {
                serviceListData = data;
                
                // Modal açık değilse göster
                if (!isModalOpen) {
                    $(".servisListe").html(data);
                    initializeAllEvents();
                }
            },
            error: function() {
                $(".servisListe").html('<div class="alert alert-danger">Bir hata oluştu!</div>');
            }
        });
    }

    // ========== VERİ YEDEKLEME FONKSİYONLARI ==========
    function backupServiceList() {
        const currentContent = $(".servisListe").html();
        if (currentContent && !currentContent.includes('Yükleniyor')) {
            serviceListBackup = currentContent;
            sessionStorage.setItem('serviceListBackup', currentContent);
            sessionStorage.setItem('backupTime', Date.now());
        }
    }

    function restoreServiceList() {
        // Öncelik sırası: 1. Memory, 2. SessionStorage, 3. Yeniden yükle
        if (serviceListData) {
            $(".servisListe").html(serviceListData);
            initializeAllEvents();
        } else {
            const backup = sessionStorage.getItem('serviceListBackup');
            const backupTime = sessionStorage.getItem('backupTime');
            
            // 5 dakikadan yeni veri
            if (backup && backupTime && (Date.now() - parseInt(backupTime) < 300000)) {
                $(".servisListe").html(backup);
                initializeAllEvents();
            } else {
                loadServiceList(true);
            }
        }
    }

    // ========== EVENT'LERİ YENİDEN BAĞLAMA ==========
    function initializeAllEvents() {
        // Personel servis düzenleme
        $(".personelServisDuzenle").off('click').on('click', function(e) {
            var id = $(this).attr("data-bs-id");
            var name = $(this).attr("data-bs-name");
            var firma_id = {{$firma->id}};
            
            backupServiceList(); // Düzenleme öncesi yedekle
            
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

        // Personel servisleri göster
        $('.personelServisListele').off('click').on('click', function () {
            const persID = $('.personelList').val();
            
            if(!persID) {
                alert('Lütfen bir personel seçin!');
                return;
            }

            backupServiceList(); // İşlem öncesi yedekle

            $('.servisListe').html('<div class="text-center p-3"><i class="fa fa-spinner fa-spin"></i> Yükleniyor...</div>');

            $.ajax({
                url: `/${{{$firma->id}}}/servis-liste-getir/`,
                method: 'GET',
                data: { persID: persID },
                success: function(res) {
                    serviceListData = res;
                    $('.servisListe').html(res);
                    initializeAllEvents();
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

            backupServiceList(); // Modal açmadan önce yedekle
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

    // ========== MODAL EVENT'LERİ ==========
    // Toplu planlama modalı
    $('#servisPersonelAtamaModal').on('show.bs.modal', function () {
        isModalOpen = true;
        backupServiceList();
    });

    $('#servisPersonelAtamaModal').on('hidden.bs.modal', function () {
        isModalOpen = false;
        setTimeout(function() {
            checkAndRestoreList();
        }, 300);
    });

    // Düzenleme modalı
    $('#personelServisDuzenleModal').on('show.bs.modal', function () {
        isModalOpen = true;
        backupServiceList();
    });

    $('#personelServisDuzenleModal').on('hidden.bs.modal', function () {
        isModalOpen = false;
        setTimeout(function() {
            checkAndRestoreList();
        }, 300);
    });

    // ========== LİSTE KONTROL VE KURTARMA ==========
    function checkAndRestoreList() {
        const content = $('.servisListe').html();
        
        if (!content || content.trim() === '' || content.includes('Yükleniyor')) {
            console.log('Liste boş, restore ediliyor...');
            restoreServiceList();
        }
    }

    // ========== OTOMATİK KURTARMA MEKANİZMASI ==========
    setInterval(function() {
        if (!isModalOpen) {
            const content = $('.servisListe').html();
            if (!content || content.trim() === '' || content.includes('Yükleniyor')) {
                console.log('Otomatik kurtarma tetiklendi');
                checkAndRestoreList();
            }
        }
    }, 5000); // Her 5 saniyede kontrol

    // ========== RESPONSIVE ==========
    if ($(window).width() < 992) {
        $("#planlamaSearch").removeClass("show");
    }

    // ========== SAYFA GÖRÜNÜRLÜğÜ DEĞİŞTİĞİNDE ==========
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden && !isModalOpen) {
            setTimeout(function() {
                checkAndRestoreList();
            }, 500);
        }
    });
});
</script>
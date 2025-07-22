        <div class="card-header cardBaslik" style="padding: 5px 10px;font-size: 14px">
    @if(!empty($persID))
        @php $selectedPersonnel = $personnel->find($persID) @endphp
        {{ $selectedPersonnel ? $selectedPersonnel->name . ' Servisleri' : 'Personel Servisleri' }}
    @else
        {{ request('planTarih') }} - Servisler
    @endif
    ({{ $services->count() }})
</div>
        <div class="card-body" style="padding: 0!important;height: 462px;overflow: auto;">
            <table class="table table-hover table-striped" id="serviceTable" width="100%" cellspacing="0">
                <thead class="title">
                    <tr>
                        <th>Seç</th>
                        <th>ID</th>
                        <th>Müşteri Adı</th>
                        <th>İlçe</th>
                        <th>Cihaz</th>
                        <th>Arıza</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $service)
                    <tr>
                        <td><input type="checkbox" class="selectService" value="{{ $service->id }}"></td>
                        <td >{{ $service->id }}</td>
                        <td style="vertical-align: middle;font-size: 11px; padding: 3px 10px;cursor:pointer;" class="personelServisDuzenle" data-id="{{$service->id}}" data-name=""><strong>{{ $service->musteri->adSoyad ?? '-' }}</strong></td>
                        <td style="vertical-align: middle;font-size: 11px; padding: 3px 10px;cursor:pointer;" class="personelServisDuzenle" data-id="{{$service->id}}" data-name=""><strong>{{ $service->musteri->ilce }}</strong></td>
                        <td style="vertical-align: middle;font-size: 11px; padding: 3px 10px;cursor:pointer;" class="personelServisDuzenle" data-id="{{$service->id}}" data-name=""><strong>{{ $service->markaCihaz->marka ?? '-'}}, {{$service->turCihaz->cihaz ?? '-'}}</strong></td>
                        <td style="vertical-align: middle;font-size: 11px; padding: 3px 10px;cursor:pointer;" class="personelServisDuzenle" data-id="{{$service->id}}" data-name=""><strong>{{ $service->cihazAriza ?? '-' }}</strong></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center">Kayıt bulunamadı</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer" style="padding: 5px 10px;font-size: 14px">
          <div class="form-group row">
              <label for="personel" class="col-md-2 col-form-label">Personel</label>
              <div class="col-md-4">
                <select id="personel" class="form-control personelList">
                  @foreach ($personeller as $pers)
                    @php
                        $count = $personelAtamaSayilari[$pers->user_id] ?? 0;
                    @endphp
                    <option value="{{ $pers->user_id }}">
                        {{ $pers->name }} ({{ $count }})
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-4">
                <button type="button" class="btn btn-primary btn-block btn-sm personelServisListele">Servisleri Göster</button>
              </div>
          </div>
        </div>

<button id="assignBtn" class="btn btn-success btn-sm mt-2">Atamayı Yap</button>

<script>
$(function(){
    $('#assignBtn').on('click', function(){
        var selected = [];
        $('.selectService:checked').each(function(){
            selected.push($(this).val());
        });

        var personelId = $('#personel').val(); // Dropdown'dan al

        if(selected.length > 0 && personelId) {
            // Confirmation dialog
            if(!confirm(`${selected.length} servisi ${$('#personel option:selected').text()} kişisine atamak istediğinizden emin misiniz?`)) {
                return;
            }

            // Loading state
            $(this).prop('disabled', true).text('Atanıyor...');

            $.ajax({
                url: '{{ route("service.assign", $firma->id) }}',
                method: 'POST',
                data: {
                    servisidler: selected,
                    personel: personelId,
                    gidenDurum: 237,
                    _token: '{{ csrf_token() }}',
                },
                success: function(res) {
                    if(res.status === 'success') {
                        alert(`${selected.length} servis başarıyla atandı!`);
                        // Checkboxları temizle
                        $('.selectService').prop('checked', false);
                        // Listeyi yenile
                        $('#filtreleBtn').click();
                    } else {
                        alert('Atama başarısız: ' + (res.message || 'Bilinmeyen hata'));
                    }
                },
                error: function(xhr, status, error) {
                    alert('Atama sırasında hata oluştu: ' + error);
                },
                complete: function() {
                    // Loading state'i kaldır
                    $('#assignBtn').prop('disabled', false).text('Atamayı Yap');
                }
            });
        } else {
            alert('Lütfen en az bir servis seçin ve personel seçin!');
        }
    });

    // Tümünü seç/bırak
    $('#selectAll').on('change', function(){
        $('.selectService').prop('checked', $(this).prop('checked'));
    });

   
});
</script>

<script>
  $(function () {
    // Personel servisleri listeleme - Düzeltilmiş versiyon
    $('.personelServisListele').on('click', function (e) {
        e.preventDefault(); // Varsayılan form submit'i engelle
        e.stopPropagation(); // Event bubbling'i engelle
        
        const persID = $('.personelList').val();
        const firma_id = {{ $firma->id }};

        if (!persID) {
            alert('Lütfen bir personel seçin!');
            return false;
        }

        // Button'u devre dışı bırak ve loading state'e geçir
        const $btn = $(this);
        const originalText = $btn.text();
        $btn.prop('disabled', true).text('Yükleniyor...');

        // Loading göstergesi
        $('.servisListe').html(`
            <div class="text-center p-4">
                <i class="fa fa-spinner fa-spin fa-2x"></i>
                <p class="mt-2">Personel servisleri yükleniyor...</p>
            </div>
        `);

        $.ajax({
            url: `/${firma_id}/servis-liste-getir/`,
            method: 'GET',
            data: { 
                persID: persID,
                _: Date.now() // Cache busting için timestamp ekle
            },
            timeout: 10000, // 10 saniye timeout
            success: function(res) {
                console.log('Personel servisleri başarıyla yüklendi', {persID});
                $('.servisListe').html(res);
                
                // URL'yi güncellemeden history'e ekle (opsiyonel)
                if (window.history && window.history.pushState) {
                    const newUrl = new URL(window.location);
                    newUrl.searchParams.set('persID', persID);
                    window.history.replaceState({}, '', newUrl);
                }
            },
            error: function(xhr, status, error) {
                console.error('Personel servisleri yüklenirken hata:', {xhr, status, error});
                let errorMessage = 'Liste alınamadı. Lütfen tekrar deneyin.';
                
                if (status === 'timeout') {
                    errorMessage = 'İstek zaman aşımına uğradı. Lütfen tekrar deneyin.';
                } else if (xhr.status === 404) {
                    errorMessage = 'Servis bulunamadı.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Sunucu hatası oluştu.';
                }
                
                $('.servisListe').html(`
                    <div class="alert alert-danger">
                        <i class="fa fa-exclamation-triangle"></i> ${errorMessage}
                        <br><small>Hata detayı: ${error}</small>
                    </div>
                `);
            },
            complete: function() {
                // Button'u tekrar aktif et
                $btn.prop('disabled', false).text(originalText);
            }
        });
        
        return false; // Event'in devam etmesini engelle
    });

    // Sayfa yenilenme durumunda personel servislerini tekrar yükle
    $(document).ready(function() {
        const urlParams = new URLSearchParams(window.location.search);
        const persID = urlParams.get('persID');
        
        if (persID) {
            $('.personelList').val(persID);
            $('.personelServisListele').trigger('click');
        }
    });

    // Form submit'lerini engelle (eğer button bir form içindeyse)
    $('form').on('submit', function(e) {
        // Sadece personel servisleri butonuna tıklandıysa engelle
        if ($(document.activeElement).hasClass('personelServisListele')) {
            e.preventDefault();
            return false;
        }
    });

    // Diğer butonların personel listesini etkilememesi için
    $(document).on('click', 'button:not(.personelServisListele)', function(e) {
        // Eğer başka bir button'a tıklandıysa ve bu personel listesi button'u değilse
        // personel listesini temizleme (opsiyonel)
    });
});
</script>


{{-- resources/views/frontend/secure/all_services/service_batch_planning/personnel_services_partial.blade.php --}}

<div class="card-header cardBaslik" style="padding: 5px 10px;font-size: 14px; background-color: #e3f2fd;">
    @php 
        $selectedPersonnel = $personeller->where('user_id', $persID)->first();
    @endphp
    <i class="fa fa-user"></i> 
    {{ $selectedPersonnel ? $selectedPersonnel->name . ' Servisleri' : 'Personel Servisleri' }}
    ({{ $services->count() }})
    @if($services->count() == 0)
        <span class="badge badge-warning ml-2">Bu personele bugün atanmış servis bulunmuyor</span>
    @endif
</div>

<div class="card-body" style="padding: 0!important;height: 462px;overflow: auto;">
    <table class="table table-hover table-striped" id="personnelServiceTable" width="100%" cellspacing="0">
        <thead class="title" style="background-color: #f8f9fa;">
            <tr>
                <th><input type="checkbox" id="selectAllPersonnel"> Seç</th>
                <th>ID</th>
                <th>Müşteri Adı</th>
                <th>İlçe</th>
                <th>Cihaz</th>
                <th>Arıza</th>
                <th>Durum</th>
            </tr>
        </thead>
        <tbody>
            @forelse($services as $service)
            <tr data-service-id="{{ $service->id }}" class="personnel-service-row">
                <td><input type="checkbox" class="selectPersonnelService" value="{{ $service->id }}"></td>
                <td><span class="badge badge-primary">{{ $service->id }}</span></td>
                <td style="vertical-align: middle;font-size: 11px; padding: 3px 10px;cursor:pointer;" 
                    class="personelServisDuzenle" data-id="{{$service->id}}" data-name="">
                    <strong>{{ $service->musteri->adSoyad ?? '-' }}</strong>
                    <br><small class="text-muted">{{ $service->musteri->telefon ?? '' }}</small>
                </td>
                <td style="vertical-align: middle;font-size: 11px; padding: 3px 10px;">
                    <strong>{{ $service->musteri->ilce ?? '-' }}</strong>
                    <br><small class="text-muted">{{ $service->musteri->il ?? '' }}</small>
                </td>
                <td style="vertical-align: middle;font-size: 11px; padding: 3px 10px;">
                    <strong>{{ $service->markaCihaz->marka ?? '-'}}</strong>
                    <br><small>{{$service->turCihaz->cihaz ?? '-'}}</small>
                </td>
                <td style="vertical-align: middle;font-size: 11px; padding: 3px 10px;">
                    <strong>{{ Str::limit($service->cihazAriza ?? '-', 50) }}</strong>
                </td>
                <td style="vertical-align: middle;font-size: 11px; padding: 3px 10px;">
                    @php
                        $statusClass = '';
                        $statusText = '';
                        switch($service->servisDurum) {
                            case '235':
                                $statusClass = 'badge-warning';
                                $statusText = 'Bekliyor';
                                break;
                            case '237':
                                $statusClass = 'badge-info';
                                $statusText = 'Atandı';
                                break;
                            case '264':
                                $statusClass = 'badge-success';
                                $statusText = 'Tamamlandı';
                                break;
                            default:
                                $statusClass = 'badge-secondary';
                                $statusText = 'Bilinmiyor';
                        }
                    @endphp
                    <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center p-4">
                    <i class="fa fa-info-circle fa-2x text-muted mb-2"></i>
                    <p class="text-muted">Bu personele bugün için atanmış servis bulunmuyor.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($services->count() > 0)
<div class="card-footer" style="padding: 5px 10px;font-size: 14px; background-color: #f8f9fa;">
    <div class="row">
        <div class="col-md-6">
            <button type="button" class="btn btn-outline-secondary btn-sm" id="clearPersonnelFilter">
                <i class="fa fa-times"></i> Personel Filtresini Temizle
            </button>
        </div>
        <div class="col-md-6 text-right">
            <button type="button" class="btn btn-success btn-sm" id="updatePersonnelServices">
                <i class="fa fa-sync"></i> Seçili Servisleri Güncelle
            </button>
        </div>
    </div>
</div>
@endif

<script>
$(document).ready(function() {
    // Personel servisleri için tümünü seç/bırak
    $('#selectAllPersonnel').on('change', function(){
        $('.selectPersonnelService').prop('checked', $(this).prop('checked'));
    });

    // Personel filtresini temizle
    $('#clearPersonnelFilter').on('click', function() {
        $('.personelList').val('');
        $('#filtreleBtn').click(); // Ana listeyi tekrar yükle
    });

    // Seçili servisleri güncelle
    $('#updatePersonnelServices').on('click', function() {
        var selected = [];
        $('.selectPersonnelService:checked').each(function(){
            selected.push($(this).val());
        });

        if(selected.length > 0) {
            alert(`${selected.length} servis seçildi. Güncelleme işlemi buraya eklenebilir.`);
        } else {
            alert('Lütfen en az bir servis seçin!');
        }
    });

    console.log('Personnel services loaded:', {{ $services->count() }});
});
</script>
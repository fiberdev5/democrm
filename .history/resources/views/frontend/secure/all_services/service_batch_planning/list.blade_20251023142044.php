<style>
  @media (max-width: 767px) {
    .card-footer{
          background-color: rgba(0, 0, 0, .03);
    border-top: 1px solid rgba(0, 0, 0, .125) !important;
}
.servisListe{
  border: 1px solid rgba(0, 0, 0, .125) !important;
  padding: 5px 6px !important;
}
.cardBaslik{
  margin-bottom: 5px !important;
}
.col-form-label{
  padding-bottom: 1px !important;
}
.card-footer{
         padding: 5px 0px !important;
}
#assignBtn{
  width: 100% !important;
}
.personelServisListele{width: 100% !important;}
  }

</style>
<div class="card-header cardBaslik" style="padding: 5px 10px;font-size: 14px">
  @if(!empty($persID))
    @php $selectedPersonnel = $personeller->firstWhere('user_id', $persID) @endphp
    {{ 'Bugün Atanan' . $selectedPersonnel ? $selectedPersonnel->name . ' Servisleri' : 'Personel Servisleri' }}
  @else
    {{ request('planTarih') }} - Servisler
  @endif
  ({{ $services->count() }})
</div>
<div class="card-body" style="padding: 0!important;height: 450px;overflow: auto;">
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
          <td style="vertical-align: middle;font-size: 11px; padding: 3px 10px;cursor:pointer;" class="personelServisDuzenle" data-bs-id="{{$service->id}}" data-bs-name="{{ $service->musteri->adSoyad ?? '-' }}"><strong>{{ $service->musteri->adSoyad ?? '-' }}</strong></td>
          <td style="vertical-align: middle;font-size: 11px; padding: 3px 10px;cursor:pointer;" class="personelServisDuzenle" data-bs-id="{{$service->id}}" data-bs-name="{{ $service->musteri->adSoyad ?? '-' }}"><strong>{{ $service->musteri->state->ilceName }}</strong></td>
          <td style="vertical-align: middle;font-size: 11px; padding: 3px 10px;cursor:pointer;" class="personelServisDuzenle" data-bs-id="{{$service->id}}" data-bs-name="{{ $service->musteri->adSoyad ?? '-' }}"><strong>{{ $service->markaCihaz->marka ?? '-'}}, {{$service->turCihaz->cihaz ?? '-'}}</strong></td>
          <td style="vertical-align: middle;font-size: 11px; padding: 3px 10px;cursor:pointer;" class="personelServisDuzenle" data-bs-id="{{$service->id}}" data-bs-name="{{ $service->musteri->adSoyad ?? '-' }}"><strong>{{ $service->cihazAriza ?? '-' }}</strong></td>
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
          <option value="{{ $pers->user_id }}">{{ $pers->name }} ({{ $count }})</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-4">
      <button type="button" class="btn btn-primary btn-block btn-sm personelServisListele">Servisleri Göster</button>
    </div>
  </div>
  @php
    use App\Models\User;

    // 1) gelenDurum dropdown’daki değer
    $gelenDurum = $statuses;
    // 2) varsayılan gidenDurum haritası
    $map = [
            '237' => 250, '245' => 250,
            '252' => 251, '246' => 251,
            '240' => 262,
            '235-2' => 264, '264' => 264,
          ];
    $gidenDurum = $map[$gelenDurum] ?? 236;   // default teknisyen yönlendir

    // 3) personele özel kural
    $dataPers = null;
    if (!empty($persID)) {
      $dataPers =  $persID;
      $perSec = User::find($persID);
      if ($perSec->hasAnyRole(['Atölye Ustası', 'Atölye Çırak'])) {
        $gidenDurum = 250;
      } else {
        $gidenDurum = 236;
      }             
    }
  @endphp
  <button id="assignBtn" class="btn btn-success btn-sm mt-1 atamaBtn" data-id="{{ $gidenDurum }}" @if($dataPers) data-pers="{{ $dataPers }}" @endif>Atama Yap</button>
</div>

<div id="servisPersonelAtamaModal" class="modal fade" style="padding-top: 50px;background: rgba(0, 0, 0, 0.50);">
  <div class="modal-dialog ">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" >Servis Planlama</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Yükleniyor...
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="personelServisDuzenleModal" class="modal fade" data-bs-backdrop="static" tabindex='-1' style="padding-top: 20px;background: rgba(0, 0, 0, 0.50);">  {{--data-bs-backdrop="static" data-bs-keyboard="false"  modalın hemen kapanmaması için bunu eklemiştim. Eğer eklenmesi gerekirse aria-hidden in yanına ekleyebilirsin--}}
  <div class="modal-dialog modal-lg" style="width: 980px;">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" id="editCustomerLabel">Servis Düzenle</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Yükleniyor...
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->





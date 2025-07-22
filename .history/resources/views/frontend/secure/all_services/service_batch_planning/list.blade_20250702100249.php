        <div class="card-header" style="padding: 5px 10px!important;font-size: 14px">
            Servis ID:
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

    var personel = prompt("Personel ID Giriniz:");

    if(selected.length > 0 && personel) {
      $.ajax({
        url: '{{ route("service.assign",$firma->id) }}',
        method: 'POST',
        data: {
          servisidler: selected,
          personel: personel,
          gidenDurum: 237,
          _token: '{{ csrf_token() }}',
        },
        success: function(res) {
          if(res.status === 'success') {
            alert('Atama başarılı!');
            $('#filtreleBtn').click(); // Listeyi yenile
          } else {
            alert('Atama başarısız: ' + (res.message || ''));
          }
        }
      });
    } else {
      alert('Lütfen en az bir servis seçin ve personel ID girin!');
    }
  });
});

// Edit service
    $(document).on('click', '.personelServisDuzenle', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var firma_id = {{$firma->id}};
        $.ajax({
            url: "/" + firma_id + "/servis/duzenle/" + id,
            success: function(data) {
                 $('#personelServisDuzenleModal').modal('show');
                $('#personelServisDuzenleModal .modal-body').html(data);
               
            }
        });
    });
</script>

<script type="text/javascript">
  $(document).ready(function(){

    $(".personelServisListele").click( function(){
      var persID = $(".personelList").val();
      var firma_id = {{$firma->id}};
      $(".servisListe").html("<img src='images/ajax_load.gif'>");
      $.ajax({
        url: "/" + firma_id + "/service.list?persID=" + persID
      }).done(function(data) {
        if($.trim(data)==="-1"){
          window.location.reload(true);
        }else{
          $('.servisListe').html(data); // display data
        }
      });
    });
 });
</script>


<table class="table table-hover table-striped" id="serviceTable" width="100%" cellspacing="0">
    <thead>
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
            <td>{{ $service->id }}</td>
            <td>{{ $service->customer->adSoyad ?? '-' }}</td>
            <td>{{ $service->ilce }}</td>
            <td>{{ $service->deviceType->cihaz ?? '-' }}</td>
            <td>{{ $service->ariza ?? '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center">Kayıt bulunamadı</td></tr>
        @endforelse
    </tbody>
</table>

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
        url: '{{ route("service.planning.assign") }}',
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
</script>


<style>
div.dataTables_wrapper div.dataTables_filter {
    display: none;
}
</style>
<div class="row mt-3 mb-2">
  <div class="col-12">
    <div class=" d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0 fw-bold text-gray border-bottom pb-2" style="font-size: 15px;">Silinen Servisler</h4>
    </div>
  </div>
</div>

        <div  id="deletedServices">
            <p>Bu sayfada, sistemden son 7 gün içerisinde silinen servisler görüntülenir. Sayfayı yenileyip kontrol ediniz. Silmekten vazgeçtiyseniz, servisi geri alabilirsiniz. </p>
          <table id="datatableDeletedService" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
            <thead class="title">
              <tr>
                <th style="width: 10px">ID</th>
                <th data-priority="2">Tarih</th>
                <th>Silen Kişi</th>
                <th>Müşteri Adı</th>
                <th>Cihaz</th>
                <th>Servis Durumu</th>
                <th data-priority="1" style="width: 96px;">Geri Al</th>
              </tr>
            </thead>
            <tbody>
              @foreach($deleted_services as $item)
                <tr data-id="{{$item->id}}">
                  <td class="gizli">{{$item->id}}</td>
                  <td><div class="mobileTitle">Tarih:</div>{{ Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i')}}</td>
                  <td><div class="mobileTitle">Silen Kişi:</div>{{$item->staffwhodeleted->name ?? ''}}</td>
                  <td><div class="mobileTitle">Müşteri:</div>{{$item->musteri->adSoyad ?? ''}}</td>
                  <td><div class="mobileTitle">Cihaz:</div>{{$item->markaCihaz->marka ?? ''}}, {{$item->turCihaz->cihaz?? ''}}</td>
                  <td><div class="mobileTitle">S. Durumu:</div>{{$item->asamalar->asama ?? ''}}</td>
                  <td class="tabloBtn">
                    <button class="btn btn-outline-danger btn-sm mobilBtn restoreService" title="Geri Al" data-id="{{ $item->id }}"> <i class="fas fa-undo"></i> </button></td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

<script>
$(document).on('click', '.restoreService', function () {
    const btn = $(this);
    const servisId = btn.data('id');

    if (!confirm("Bu servisi geri almak istediğinize emin misiniz?")) {
        return;
    }

    $.ajax({
        url: `/{{ $firma->id }}/servis-geri-al/${servisId}`,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function (response) {
            if (response.success) {
                // Tablo satırını kaldır
                let table = $('#datatableDeletedService').DataTable();
                table.row(btn.closest('tr')).remove().draw();

                // (İsteğe bağlı) Toast veya alert mesajı
                alert(response.message);
            }
        },
        error: function (xhr) {
            alert('Bir hata oluştu: ' + xhr.responseText);
        }
    });
});
</script>  
  
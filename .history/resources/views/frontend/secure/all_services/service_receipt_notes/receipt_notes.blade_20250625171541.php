<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="card" style="margin-bottom: 3px;margin-top:3px;">
  <div class="card-header" style="padding: 3px 5px!important;">
    <button type="button" class="btn btn-success btn-sm fisNotuEkle" data-bs-id ={{$servis->id}}>Fiş Notu Ekle</button>
  </div>

  <div class="card-body fisNotlari" style="padding: 0!important;"></div> 
</div>

<div class="card">
  <div class="card-body" style="padding: 0!important">
    <div class="table-responsive" style="margin: 0!important;">
      <table class="table table-hover table-striped" id="fisNotuTablo" width="100%" cellspacing="0" style="margin: 0">
        <thead class="title">
          <tr>
            <th style="padding: 5px 10px;font-size: 12px;">Tarih</th>
            <th style="padding: 5px 10px;font-size: 12px;">İşlemi Yapan</th>
            <th style="padding: 5px 10px;font-size: 12px;">Açıklama</th>
            <th style="padding: 5px 10px;font-size: 12px;"></th>
          </tr>
        </thead>
        <tbody>
          @foreach($servis_fis_notlari as $item)
            <tr>
              <td style="vertical-align: middle;width: 100px; font-size: 11px; padding: 0 10px;">
                {{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}
              </td>
              <td style="vertical-align: middle;font-size: 11px; padding: 0 10px;">
                {{ $item->personel->name }}
              </td>
              <td style="vertical-align: middle;font-size: 11px; padding: 0 10px;">
                <strong>{{ $item->aciklama }}</strong>
              </td>
              @if($item->kid == auth()->user()->user_id)
                <td style="vertical-align: middle;width: 55px;padding: 0 10px;">
                  <a href="#" style="font-size: 11px;" class="btn btn-danger btn-sm fisNotuSil" data-id="{{ $item->id }}">Sil</a>
                </td>
              @else
                <td style="vertical-align: middle;width: 55px;padding: 0 10px;font-size:11px;">Yetkiniz Yok</td>
              @endif                                   
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>      

<script type="text/javascript">
  $(document).ready(function () {
    $(".fisNotuEkle").click(function(){
      var id = $(this).attr("data-bs-id");
      var firma_id = {{$firma->id}};
      if(id){
        $.ajax({
          url: "/" + firma_id + "/servis-fis-notu/ekle/"+ id
        }).done(function(data) {
          if($.trim(data)==="-1"){
            window.location.reload(true);
          }else{
            $('.fisNotlari').html(data).show();
          }
        });
      }
    });
  });
</script>

<script>
  $(document).ready(function() {
    $('#fisNotuTablo').on('click', '.fisNotuSil', function(e) {
      e.preventDefault();
      var confirmDelete = confirm("Bu servis fiş notunu istediğinize emin misiniz?");
      if (confirmDelete) {
        var id = $(this).attr('data-id');
        var firma_id = {{$firma->id}};
        $.ajax({
          url: '/' + firma_id + '/servis-fis-notu/sil/' + id,
          type: 'POST',
          data: {
            _method: 'DELETE', 
            _token: '{{ csrf_token() }}'
          },
          success: function(data) {
            if (data) {
              alert("Servis fiş notu başarıyla silindi.");
              $('#datatableService').DataTable().ajax.reload();
              $('.nav7').trigger('click');
            } else {
              alert("Silme işlemi başarısız oldu.");
            }
          },
          error: function(xhr, status, error) {
            console.error(xhr.responseText);
          }
        });
      }
    });
  });
</script>
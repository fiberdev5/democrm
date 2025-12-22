

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables CSS & JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>

<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="card" style="margin-bottom: 3px;margin-top:3px;">
  <div class="card-header" style="padding: 3px 5px!important;">
    <button type="button" class="btn btn-success btn-sm optNotuEkle" data-bs-id ={{$servis->id}}>Operatör Notu Ekle</button>
  </div>

  <div class="card-body optNotlari" style="padding: 0!important;"></div> 
</div>

<div class="card">
  <div class="card-body" style="padding: 0!important">
    @if(count($opt_notlari) > 0)
      <div class="table-responsive" style="margin: 0!important;">
        <table class="table table-hover table-striped" id="operatorNotuTablo" width="100%" cellspacing="0" style="margin: 0">
          <thead class="title">
            <tr>
              <th style="padding: 5px 10px;font-size: 12px;">Tarih</th>
              <th style="padding: 5px 10px;font-size: 12px;">İşlemi Yapan</th>
              <th style="padding: 5px 10px;font-size: 12px;">Açıklama</th>
              <th style="padding: 5px 10px;font-size: 12px;"></th>
              <th style="padding: 5px 10px;font-size: 12px;"></th>
            </tr>
          </thead>
          <tbody>
            @foreach($opt_notlari as $item)
              <tr>
                <td style="vertical-align: middle;width: 100px; font-size: 11px; padding: 0 10px;">
                  {{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}
                </td>
                <td style="vertical-align: middle;font-size: 11px; padding: 0 10px;">
                  {{ $item->user->name }}
                </td>
                <td style="vertical-align: middle;font-size: 11px; padding: 0 10px;">
                  <strong>{{ $item->aciklama }}</strong>
                </td>
                @if($item->pid == auth()->user()->user_id)
                  <td style="vertical-align: middle;width: 55px;padding: 0 10px;">
                    <a href="#" style="font-size: 11px;" class="btn btn-primary btn-sm optNotuDuzenle" data-bs-id="{{ $item->id }}">Düzenle</a>
                  </td>
                  <td style="vertical-align: middle;width: 55px;padding: 0 10px;">
                    <a href="#" style="font-size: 11px;" class="btn btn-danger btn-sm optNotuSil" data-id="{{ $item->id }}">Sil</a>
                  </td>
                @else
                  <td style="vertical-align: middle;width: 55px;padding: 0 10px;font-size:11px;">Yetkiniz Yok</td>
                  <td style="vertical-align: middle;width: 55px;padding: 0 10px;font-size:11px;">Yetkiniz Yok</td>
                @endif                                   
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @else
      <div id="noOperatorNotu" class="text-center text-muted" style="padding: 20px;">
        <i class="fas fa-user-tie fa-3x mb-3" style="font-size: 2.5em; color: #ddd;"></i>
        <p style="font-size: 14px; color: #6c757d; margin: 0;">Henüz operatör notu eklenmemiş</p>
      </div>
    @endif
  </div>
</div>      

<script type="text/javascript">
  $(document).ready(function () {
    $(".optNotuEkle").click(function(){
      var id = $(this).attr("data-bs-id");
      var firma_id = {{$firma->id}};
      if(id){
        $.ajax({
          url: "/" + firma_id + "/servis-opt-notu/ekle/"+ id
        }).done(function(data) {
          if($.trim(data)==="-1"){
            window.location.reload(true);
          }else{
            $('.optNotlari').html(data).show();
          }
        });
      }
    });
  });
</script>

<script type="text/javascript">
  $(document).ready(function () {
    $(".optNotuDuzenle").click(function(){
      var id = $(this).attr("data-bs-id");
      var firma_id = {{$firma->id}};
      if(id){
        $.ajax({
          url: "/" + firma_id + "/servis-opt-notu/duzenle/"+ id
        }).done(function(data) {
          if($.trim(data)==="-1"){
            window.location.reload(true);
          }else{
            $('.optNotlari').html(data).show();
          }
        });
      }
    });
  });
</script>

<script>
  $(document).ready(function() {
    // Operatör notu sayısını kontrol eden fonksiyon
    function checkOperatorNotuCount() {
      const operatorNotuCount = $('#operatorNotuTablo tbody tr').length;
      if (operatorNotuCount === 0) {
        $('#operatorNotuTablo').closest('.table-responsive').hide();
        if ($('#noOperatorNotu').length === 0) {
          $('.card-body').append(`
            <div id="noOperatorNotu" class="text-center text-muted" style="padding: 40px 20px;">
              <i class="fas fa-user-tie fa-3x mb-3" style="font-size: 2.5em; color: #ddd;"></i>
              <p style="font-size: 14px; color: #6c757d; margin: 0;">Henüz operatör notu eklenmemiş</p>
              <small style="color: #adb5bd;">Yukarıdaki "Operatör Notu Ekle" butonunu kullanarak not ekleyebilirsiniz</small>
            </div>
          `);
        } else {
          $('#noOperatorNotu').show();
        }
      } else {
        $('#noOperatorNotu').hide();
        $('#operatorNotuTablo').closest('.table-responsive').show();
      }
    }

    $('#operatorNotuTablo').on('click', '.optNotuSil', function(e) {
      e.preventDefault();
      var confirmDelete = confirm("Bu servis operatör notunu silmek istediğinize emin misiniz?");
      if (confirmDelete) {
        var id = $(this).attr('data-id');
        var firma_id = {{$firma->id}};
        var $row = $(this).closest('tr');
        
        $.ajax({
          url: '/' + firma_id + '/servis-opt-notu/sil/' + id,
          type: 'POST',
          data: {
            _method: 'DELETE', 
            _token: '{{ csrf_token() }}'
          },
          success: function(data) {
            if (data) {
              alert("Servis operatör notu başarıyla silindi.");
              $row.fadeOut(300, function() {
                $(this).remove();
                checkOperatorNotuCount(); // Silme işlemi sonrası kontrol et
              });
              $('#datatableService').DataTable().ajax.reload();
              loadServiceHistory({{ $servis->id }});
              $('.nav8').trigger('click');
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
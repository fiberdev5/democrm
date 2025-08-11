@extends('frontend.secure.user_master')
@section('user')

<div class="page-content" id="passwords">
  <div class="container-fluid">
<div class="row pageDetail">
    <div class="col-12">
      <div class="card" style="margin-bottom: 185px;">
        <div class="card-header sayfaBaslik" style="font-size:13px;">
          Gelen Çağrılar
        </div>
        <div class="card-body" id="gelenCagrilar">
          <table id="datatableIncomingCalls" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
            <a  class="btn btn-success btn-sm mb-1 gelenCagriBtn" style="position: relative!important;left:0;" data-bs-toggle="modal" data-bs-target="#gelenCagriModal"><i class="fas fa-plus"></i><span>Yeni Çağrı Ekle</span></a>
            <thead class="title">
              <tr>
                <th style="width: 10px">ID</th>
                <th data-priority="2">Tarih</th>
                <th>Telefon</th>
                <th>Marka</th>
                <th>Açıklama</th>
                <th>Personel</th>
                <th data-priority="1" style="width: 96px;">Düzenle</th>
              </tr>
            </thead>
            <tbody>
              
            </tbody>
          </table>
        </div>
      </div>
    </div> <!-- end col -->
  </div> <!-- end row -->
  </div>
  </div>

  
<div id="gelenCagriModal" class="modal fade" data-bs-backdrop="static" tabindex='-1'>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" >Yeni Çağrı Ekle</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Yükleniyor...
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
  
  <!-- edit modal content -->
  <div id="editIncomingCallModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title" id="myModalLabel">Çağrı Düzenle</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Yükleniyor...
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
  
  
<script type="text/javascript">
$(document).ready(function(){
  $(".gelenCagriBtn").click(function(){
    var firma_id = {{$firma->id}};
    $.ajax({
      url: "/"+ firma_id + "/yeni-cagri-ekle/"
    }).done(function(data) {
      if ($.trim(data) === "-1") {
        window.location.reload(true);
      } else {
        $('#gelenCagriModal').modal('show');
        $('#gelenCagriModal .modal-body').html(data);
      }
    });
  });
});
</script>
  
  <script type="text/javascript">
  $(document).ready(function(){
    $('#gelenCagrilar').on('click', '.editIncomingCall', function(e){
      var id = $(this).attr("data-bs-id");
      var firma_id = {{$firma->id}};
      $.ajax({
        url: "/"+ firma_id + "/yeni-cagri-duzenle/" + id
      }).done(function(data) {
        if ($.trim(data) === "-1") {
          window.location.reload(true);
        } else {
          $('#editIncomingCallModal').modal('show');
          $('#editIncomingCallModal .modal-body').html(data);
        }
      });
    });
    $("#editIncomingCallModal").on("hidden.bs.modal", function() {
      $(".modal-body").html("");
    });
  
    // Kategori silme işlemi
    $('#gelenCagrilar').on('click', '.deleteIncomingCall', function(e){
      e.preventDefault();
      var id = $(this).attr("data-bs-id");
      var row = $(this).closest('tr');
      var firma_id = {{$firma->id}};
      if(confirm('Bu cihazı silmek istediğinize emin misiniz?')) {
        $.ajax({
          url: "/"+ firma_id + "/yeni-cagri-sil/" + id,
          type: "DELETE",
          data: {
            "_token": "{{ csrf_token() }}", // CSRF koruması için token ekleyin
          },
          success: function(response) {
            if(response.success) {
              row.remove(); // Satırı tablodan kaldır
              alert('Gelen çağrı başarıyla silindi.');
            } else {
              alert('Gelen çağrı silinirken bir hata oluştu.');
            }
          },
          error: function(xhr) {
            alert('Gelen çağrı silinirken bir hata oluştu.');
          }
        });
      }
    });
  });
  </script>

  <script>
$(document).ready(function() {
    var table = $('#datatableIncomingCalls').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("gelen-cagrilar.datatable", $firma->id) }}',
            data: function(d) {
                d.tarih = $('#filterTarih').val();
            }
        },
        columns: [
            { data: 'servis_kaynagi', name: 'servis_kaynagi' },
            { data: 'marka', name: 'marka' },
            { data: 'aciklama', name: 'aciklama' }
        ],
        language: {
            processing: "Yükleniyor...",
            search: "Ara:",
            lengthMenu: "_MENU_ kayıt göster",
            info: "_TOTAL_ kayıt arasında _START_ - _END_ gösteriliyor",
            paginate: {
                previous: "Önceki",
                next: "Sonraki"
            }
        }
    });

    $('#filterTarih').on('change', function() {
        table.ajax.reload();
    });
});
</script>
@endsection
  
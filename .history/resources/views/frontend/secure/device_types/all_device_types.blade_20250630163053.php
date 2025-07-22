<div class="row pageDetail">
    <div class="col-12">
      <div class="card" style="margin-bottom: 185px;">
        <div class="card-header sayfaBaslik" style="font-size:13px;">
          Cihaz Türleri
        </div>
        <div class="card-body" id="cihazTuru">
          <table id="datatableDeviceTypes" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
            <a  class="btn btn-success btn-sm mb-1 addDeviceType" data-bs-toggle="modal" data-bs-target="#addDeviceTypeModal"><i class="fas fa-plus"></i><span>Cihaz Ekle</span></a>
            <thead class="title">
              <tr>
                <th style="width: 10px">ID</th>
                <th data-priority="2">Cihaz</th>
                <th>Operatör Prim</th>
                <th>Atolye Prim</th>
                <th data-priority="1" style="width: 96px;">Düzenle</th>
              </tr>
            </thead>
            <tbody>
              @foreach($device_types as $item)
                <tr data-id="{{$item->id}}">
                  <td class="gizli"><a class="t-link editDeviceType idWrap" href="javascript:void(0);" data-bs-id="{{$item->id}}" data-bs-toggle="modal" data-bs-target="#editDeviceTypeModal">{{$item->id}}</a></td>
                  <td><a class="t-link editDeviceType" href="javascript:void(0);" data-bs-id="{{$item->id}}" data-bs-toggle="modal" data-bs-target="#editDeviceTypeModal"><div class="mobileTitle">Cihaz:</div>{{$item->cihaz}}</a></td>
                  <td><a class="t-link editDeviceType" href="javascript:void(0);" data-bs-id="{{$item->id}}" data-bs-toggle="modal" data-bs-target="#editDeviceTypeModal"><div class="mobileTitle">Opt Prim:</div>{{$item->operatorPrim}}</a></td>
                  <td><a class="t-link editDeviceType" href="javascript:void(0);" data-bs-id="{{$item->id}}" data-bs-toggle="modal" data-bs-target="#editDeviceTypeModal"><div class="mobileTitle">Atolye Prim:</div>{{$item->atolyePrim}}</a></td>
                  <td class="tabloBtn">
                    <a href="javascript:void(0);" class="btn btn-warning btn-sm editDeviceType mobilBtn mbuton1" data-bs-id="{{$item->id}}" data-bs-toggle="modal" data-bs-target="#editDeviceTypeModal" title="Düzenle"><i class="fas fa-edit"></i> <span> Düzenle</span></a>
                    <a href="javascript:void(0);"  class="btn btn-danger btn-sm mobilBtn deleteDeviceType" data-bs-id="{{$item->id}}" title="Sil"><i class="fas fa-trash-alt"></i> <span> Sil</span></a>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div> <!-- end col -->
  </div> <!-- end row -->
  
  <!-- add modal content -->
  <div id="addDeviceTypeModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title" id="myModalLabel">Cihaz Ekle</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Yükleniyor...
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
  
  <!-- edit modal content -->
  <div id="editDeviceTypeModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title" id="myModalLabel">Cihaz Düzenle</h6>
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
    $(".addDeviceType").click(function(){
        var firma_id = {{$firma->id}};
      $.ajax({
        url: "/"+ firma_id + "/cihaz-turu/ekle"
      }).done(function(data) {
        if ($.trim(data) === "-1") {
          window.location.reload(true);
        } else {
          $('#addDeviceTypeModal').modal('show');
          $('#addDeviceTypeModal .modal-body').html(data);
        }
      });
    });
  });
  </script>
  
  <script type="text/javascript">
  $(document).ready(function(){
    $('#cihazTuru').on('click', '.editDeviceType', function(e){
      var id = $(this).attr("data-bs-id");
      var firma_id = {{$firma->id}};
      $.ajax({
        url: "/"+ firma_id + "/cihaz-turu/duzenle/" + id
      }).done(function(data) {
        if ($.trim(data) === "-1") {
          window.location.reload(true);
        } else {
          $('#editDeviceTypeModal').modal('show');
          $('#editDeviceTypeModal .modal-body').html(data);
        }
      });
    });
    $("#editDeviceTypeModal").on("hidden.bs.modal", function() {
      $(".modal-body").html("");
    });
  
    // Kategori silme işlemi
    $('#cihazTuru').on('click', '.deleteDeviceType', function(e){
      e.preventDefault();
      var id = $(this).attr("data-bs-id");
      var row = $(this).closest('tr');
      var firma_id = {{$firma->id}};
      if(confirm('Bu cihazı silmek istediğinize emin misiniz?')) {
        $.ajax({
          url: "/"+ firma_id + "/cihaz-turu/sil/" + id,
          type: "DELETE",
          data: {
            "_token": "{{ csrf_token() }}", // CSRF koruması için token ekleyin
          },
          success: function(response) {
            if(response.success) {
              row.remove(); // Satırı tablodan kaldır
              alert('Cihaz türü başarıyla silindi.');
            } else {
              alert('Cihaz türü silinirken bir hata oluştu.');
            }
          },
          error: function(xhr) {
            alert('Cihaz türü silinirken bir hata oluştu.');
          }
        });
      }
    });
  });
  </script>
  
  
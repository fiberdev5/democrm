<div class="row pageDetail">
    <div class="col-12">
      <div class="card" style="margin-bottom: 185px;">
        <div class="card-header sayfaBaslik" style="font-size:13px;">
          Stok Rafları
        </div>
        <div class="card-body" id="stokTedarikcileri">
          <table id="datatableStockSupplier" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
            <a  class="btn btn-success btn-sm mb-1 addStockSupplier" data-bs-toggle="modal" data-bs-target="#addStockSupplierModal"><i class="fas fa-plus"></i><span>Stok Tedarikçisi Ekle</span></a>
            <thead class="title">
              <tr>
                <th>Tedarikçi</th>
                <th data-priority="1" style="width: 96px;">Düzenle</th>
              </tr>
            </thead>
            <tbody>
              @foreach($suppliers as $item)
                <tr data-id="{{$item->id}}">
                  <td><a class="t-link editStockSupplier" href="javascript:void(0);" data-bs-id="{{$item->id}}" data-bs-toggle="modal" data-bs-target="#editStockSupplierModal"><div class="mobileTitle">Tedarikçi:</div>{{$item->tedarikci}}</a></td>
                  <td class="tabloBtn">
                    <a href="javascript:void(0);" class="btn btn-warning btn-sm editStockSupplier mobilBtn mbuton1" data-bs-id="{{$item->id}}" data-bs-toggle="modal" data-bs-target="#editStockSupplierModal" title="Düzenle"><i class="fas fa-edit"></i> <span> Düzenle</span></a>
                    <a href="javascript:void(0);"  class="btn btn-danger btn-sm mobilBtn deleteStockSupplier" data-bs-id="{{$item->id}}" title="Sil"><i class="fas fa-trash-alt"></i> <span> Sil</span></a>
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
  <div id="addStockSupplierModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title" id="myModalLabel">Stok Tedarikçi Ekle</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Yükleniyor...
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
  
  <!-- edit modal content -->
  <div id="editStockSupplierModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title" id="myModalLabel">Stok Tedarikçi Düzenle</h6>
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
    $(".addStockSupplier").click(function(){
        var firma_id = {{$firma->id}};
      $.ajax({
        url: "/"+ firma_id + "/stok-tedarikci/ekle"
      }).done(function(data) {
        if ($.trim(data) === "-1") {
          window.location.reload(true);
        } else {
          $('#addStockSupplierModal').modal('show');
          $('#addStockSupplierModal .modal-body').html(data);
        }
      });
    });
  });
</script>
  
<script type="text/javascript">
  $(document).ready(function(){
    $('#stokTedarikcileri').on('click', '.editStockSupplier', function(e){
      var id = $(this).attr("data-bs-id");
      var firma_id = {{$firma->id}};
      $.ajax({
        url: "/"+ firma_id + "/stok-tedarikci/duzenle/" + id
      }).done(function(data) {
        if ($.trim(data) === "-1") {
          window.location.reload(true);
        } else {
          $('#editStockSupplierModal').modal('show');
          $('#editStockSupplierModal .modal-body').html(data);
        }
      });
    });
    $("#editStockSupplierModal").on("hidden.bs.modal", function() {
      $(".modal-body").html("");
    });
  
    // silme işlemi
    $('#stokTedarikcileri').on('click', '.deleteStockSupplier', function(e){
      e.preventDefault();
      var id = $(this).attr("data-bs-id");
      var row = $(this).closest('tr');
      var firma_id = {{$firma->id}};
      if(confirm('Bu stok tedarikçisini silmek istediğinize emin misiniz?')) {
        $.ajax({
          url: "/"+ firma_id + "/stok-tedarikci/sil/" + id,
          type: "DELETE",
          data: {
            "_token": "{{ csrf_token() }}", // CSRF koruması için token
          },
          success: function(response) {
            if(response.success) {
              row.remove(); // Satırı tablodan kaldırır
              alert('Stok tedarikçisi başarıyla silindi.');
            } else {
              alert('Stok tedarikçisi silinirken bir hata oluştu.');
            }
          },
          error: function(xhr) {
            alert('Stok tedarikçisi silinirken bir hata oluştu.');
          }
        });
      }
    });
  });
</script>
  
  
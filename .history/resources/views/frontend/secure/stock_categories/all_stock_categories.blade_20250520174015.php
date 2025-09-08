<div class="row pageDetail">
    <div class="col-12">
      <div class="card" style="margin-bottom: 185px;">
        <div class="card-header sayfaBaslik" style="font-size:13px;">
          Stok Kategorileri
        </div>
        <div class="card-body" id="stokKategori">
          <table id="datatableStockCategory" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
            <a  class="btn btn-success btn-sm mb-1 addStockCategory" data-bs-toggle="modal" data-bs-target="#addStockCategoryModal"><i class="fas fa-plus"></i><span>Stok Kategorisi Ekle</span></a>
            <thead class="title">
              <tr>
                <th>Kategori</th>
                <th data-priority="1" style="width: 96px;">Düzenle</th>
              </tr>
            </thead>
            <tbody>
              @foreach($categories as $item)
                <tr data-id="{{$item->id}}">
                  <td><a class="t-link editStockCategory" href="javascript:void(0);" data-bs-id="{{$item->id}}" data-bs-toggle="modal" data-bs-target="#editStockCategoryModal"><div class="mobileTitle">Kategori:</div>{{$item->kategori}}</a></td>
                  <td class="tabloBtn">
                    <a href="javascript:void(0);" class="btn btn-warning btn-sm editStockCategory mobilBtn mbuton1" data-bs-id="{{$item->id}}" data-bs-toggle="modal" data-bs-target="#editStockCategoryModal" title="Düzenle"><i class="fas fa-edit"></i> <span> Düzenle</span></a>
                    <a href="javascript:void(0);"  class="btn btn-danger btn-sm mobilBtn deleteStockCategory" data-bs-id="{{$item->id}}" title="Sil"><i class="fas fa-trash-alt"></i> <span> Sil</span></a>
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
  <div id="addStockCategoryModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title" id="myModalLabel">Kategori Ekle</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Yükleniyor...
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
  
  <!-- edit modal content -->
  <div id="editStockCategoryModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title" id="myModalLabel">Kategori Düzenle</h6>
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
    $(".addStockCategory").click(function(){
        var firma_id = {{$firma->id}};
      $.ajax({
        url: "/"+ firma_id + "/stok-kategori/ekle"
      }).done(function(data) {
        if ($.trim(data) === "-1") {
          window.location.reload(true);
        } else {
          $('#addStockCategoryModal').modal('show');
          $('#addStockCategoryModal .modal-body').html(data);
        }
      });
    });
  });
</script>
  
<script type="text/javascript">
  $(document).ready(function(){
    $('#stokKategori').on('click', '.editStockCategory', function(e){
      var id = $(this).attr("data-bs-id");
      var firma_id = {{$firma->id}};
      $.ajax({
        url: "/"+ firma_id + "/stok-kategori/duzenle/" + id
      }).done(function(data) {
        if ($.trim(data) === "-1") {
          window.location.reload(true);
        } else {
          $('#editStockCategoryModal').modal('show');
          $('#editStockCategoryModal .modal-body').html(data);
        }
      });
    });
    $("#editStockCategoryModal").on("hidden.bs.modal", function() {
      $(".modal-body").html("");
    });
  
    // silme işlemi
    $('#stokKategori').on('click', '.deleteStockCategory', function(e){
      e.preventDefault();
      var id = $(this).attr("data-bs-id");
      var row = $(this).closest('tr');
      var firma_id = {{$firma->id}};
      if(confirm('Bu servis kaynağını silmek istediğinize emin misiniz?')) {
        $.ajax({
          url: "/"+ firma_id + "/stok-kategori/sil/" + id,
          type: "DELETE",
          data: {
            "_token": "{{ csrf_token() }}", // CSRF koruması için token ekleyin
          },
          success: function(response) {
            if(response.success) {
              row.remove(); // Satırı tablodan kaldır
              alert('Stok kategorisi başarıyla silindi.');
            } else {
              alert('Stok kategorisi silinirken bir hata oluştu.');
            }
          },
          error: function(xhr) {
            alert('Stok kategorisi silinirken bir hata oluştu.');
          }
        });
      }
    });
  });
</script>
  
  
@extends('frontend.secure.user_master')
@section('user')

<style>
    .dataTables_wrapper .dataTables_length {
    float: left;
}
</style>

<div class="page-content" id="passwords">
  <div class="container-fluid">
<div class="row pageDetail">
    <div class="col-12">
      <div class="card" style="margin-bottom: 185px;">
        <div class="card-header sayfaBaslik" style="font-size:13px;">
          Silinen Servisler
        </div>
        <div class="card-body" id="gelenCagrilar">
            <p>Bu sayfada, sistemden son 7 gün içerisinde silinen servisler görüntülenir. Sayfayı yenileyip kontrol ediniz. Silmekten vazgeçtiyseniz, servisi geri alabilirsiniz. </p>
          <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
            <thead class="title">
              <tr>
                <th style="width: 10px">ID</th>
                <th data-priority="2">Tarih</th>
                <th>Silen Kişi</th>
                <th>Müşteri Adı</th>
                <th>Cihaz</th>
                <th>Servis Durumu</th>
                <th data-priority="1" style="width: 96px;">Düzenle</th>
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
                    <a href="javascript:void(0);"  class="btn btn-danger btn-sm mobilBtn deleteService" data-bs-id="{{$item->id}}" title="Sil"> <label style="margin-bottom: 0;"> Sil</label></a>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div> <!-- end col -->
  </div> <!-- end row -->
  </div>
  </div>

  
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
@endsection
  
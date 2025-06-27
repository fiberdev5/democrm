<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="card" style="margin-bottom: 3px;margin-top:3px;">
  <div class="card-header" style="padding: 3px 5px!important;">
    <button type="button" class="btn btn-success btn-sm odemeEkleBtn">Gelir Ekle</button>
    <button type="button" class="btn btn-danger btn-sm giderEkleBtn">Gider Ekle</button>
  </div>

  <div class="card-body odemeList" style="padding: 0!important;"></div>
    
</div>

<div class="card">
  <div class="card-body" style="padding: 0!important">
    <div class="table-responsive" style="margin: 0!important;">
      <table class="table table-hover table-striped" width="100%" cellspacing="0" style="margin: 0">
        <thead class="title">
          <tr>
            <th style="padding: 5px 10px;font-size: 12px;">Tarih</th>
            <th style="padding: 5px 10px;font-size: 12px;">İşlemi Yapan</th>
            <th style="padding: 5px 10px;font-size: 12px;">Ödeme Şekli</th>
            <th style="padding: 5px 10px;font-size: 12px;">Açıklama</th>
            <th style="padding: 5px 10px;font-size: 12px;">Durum</th>
            <th style="padding: 5px 10px;font-size: 12px;">Fiyat</th>
            <th style="padding: 5px 10px;font-size: 12px;"></th>
            <th style="padding: 5px 10px;font-size: 12px;"></th>
          </tr>
        </thead>
        <tbody>

        </tbody>
      </table>
    </div>
  </div>
</div>      

<script type="text/javascript">
  $(document).ready(function () {
    $(".odemeEkleBtn").click(function(){
      var id = $(this).attr("data-bs-id");
      var firma_id = {{$firma->id}};
      if(id){
        $.ajax({
          url: "/" + firma_id + "/servis-gelir-ekle/"+ id
        }).done(function(data) {
          if($.trim(data)==="-1"){
            window.location.reload(true);
          }else{
            $('.notList').html(data).show();
          }
        });
      }
    });
  });
</script>

<script type="text/javascript">
  $(document).ready(function () {
    $(".giderEkleBtn").click(function(){
      var id = $(this).attr("data-bs-id");
      if(id){
        $.ajax({
          url: "/add-document/"+ id
        }).done(function(data) {
          if($.trim(data)==="-1"){
            window.location.reload(true);
          }else{
            $('.odemeList').html(data).show();
          }
        });
      }
    });
  });
</script>
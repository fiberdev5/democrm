<ul class="nav nav-pills" role="tablist" style="margin-bottom: 5px;">
  <li class="nav-item"><a class="nav-link active" data-bs-toggle="pill" href="#tab1" data-id="{{ $stock->id }}" role="tab">Ürün Bilgileri</a></li>
  <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#tab2" data-id="{{ $stock->id }}" role="tab">Stok Hareketleri</a></li>
  <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#tab3" data-id="{{ $stock->id }}" role="tab">Personel Stokları</a></li>
  <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#tab4" data-id="{{ $stock->id }}" role="tab">Fotoğraflar</a></li>
</ul>

<div class="tab-content">
  <div id="tab1" class="tab-pane active">
    {{-- Konsinye cihaz bilgileri formu --}}
  </div>
  <div id="tab2" class="tab-pane fade"></div>
  <div id="tab3" class="tab-pane fade"></div>
  <div id="tab4" class="tab-pane fade"></div>
</div>

<script>
  $(".nav-link[href='#tab2']").click(function() {
    var id = $(this).data('id');
    var tenant_id = {{ $firma->id }};
    $.ajax({
      url: "/" + tenant_id + "/stok-konsinye-hareketleri/" + id
    }).done(function(data) {
      $('#tab2').html(data);
    });
  });

  $(".nav-link[href='#tab3']").click(function() {
    var id = $(this).data('id');
    var tenant_id = {{ $firma->id }};
    $.ajax({
      url: "/" + tenant_id + "/personel-konsinye-stoklari/" + id
    }).done(function(data) {
      $('#tab3').html(data);
    });
  });

  $(".nav-link[href='#tab4']").click(function() {
    var id = $(this).data('id');
    var tenant_id = {{ $firma->id }};
    $.ajax({
      url: "/" + tenant_id + "/stok-konsinye-fotograflar/" + id
    }).done(function(data) {
      $('#tab4').html(data);
    });
  });
</script>

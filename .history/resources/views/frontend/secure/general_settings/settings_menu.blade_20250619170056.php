<style>
  .custom-icon {
    font-size: 14px;
  }
  .fa-angle-down{display:inline-block!important;}
  
  .dropdown {
    position: relative!important;
    z-index: 1050!important;
  }
  
  .dropdown-menu {
    position: absolute!important;
    z-index: 1050!important; /* Yeterince yüksek bir z-index değeri kullanın */
  }
  
</style>
<div class="kasaSubMenu"  style="margin-top:15px">
  <ul class="nav nav-pills nav-justified" role="tablist" style="margin-bottom: 5px">
    <li class="nav-item" style="font-size: 14px;">
      <div class="dropdown">
        <a href="#" class="btn btn-secondary dropdown-toggle nav-link" data-bs-toggle="dropdown" aria-expanded="true">
          <span>Firma Ayarları</span> <i class="fa fa-angle-down custom-icon"></i>
        </a>
        <div class="dropdown-menu" style="">
          <a class="dropdown-item nav1 active" data-bs-toggle="pill" href="#tab1" data-id="" role="tab">
            <i class="fas fa-money custom-icon"></i>Firma Bilgileri
          </a>
          <a class="dropdown-item nav2" data-bs-toggle="pill" href="#tab2" data-id="" role="tab">
            <i class="fas fa-money custom-icon"></i>Sms Ayarları
          </a>
        </div>
      </div>
    </li>
    
    <li class="nav-item" style="font-size: 14px;">
      <div class="dropdown" style="z-index:auto!important;">
        <a href="#" class="btn btn-secondary dropdown-toggle nav-link" data-bs-toggle="dropdown" aria-expanded="true">
          <span>Servis Ayarları</span> <i class="fa fa-angle-down custom-icon" ></i>
        </a>
        <div class="dropdown-menu" style="">
          <a class="dropdown-item nav3" data-bs-toggle="pill" href="#tab3" data-id="" role="tab">
            <i class="fas fa-money custom-icon"></i>Cihaz Markaları
          </a>
          <a class="dropdown-item nav4" data-bs-toggle="pill" href="#tab4" data-id="" role="tab">
            <i class="fas fa-money custom-icon"></i>Cihaz Türleri
          </a>
          <a class="dropdown-item nav5" data-bs-toggle="pill" href="#tab5" data-id="" role="tab">
            <i class="fas fa-money custom-icon"></i>Garanti Süreleri
          </a>
          <a class="dropdown-item nav6" data-bs-toggle="pill" href="#tab6" data-id="" role="tab">
            <i class="fas fa-money custom-icon"></i>Servis Araçları
          </a>
          <a class="dropdown-item nav7" data-bs-toggle="pill" href="#tab7" data-id="" role="tab">
            <i class="fas fa-money custom-icon"></i>Servis Aşamaları
          </a>
          <a class="dropdown-item nav8" data-bs-toggle="pill" href="#tab8" data-id="" role="tab">
            <i class="fas fa-money custom-icon"></i>Servis Aşama Soruları
          </a>
          <a class="dropdown-item nav9" data-bs-toggle="pill" href="#tab9" data-id="" role="tab">
            <i class="fas fa-money custom-icon"></i>Servis Görüntüleme Zamanı
          </a>
          <a class="dropdown-item nav10" data-bs-toggle="pill" href="#tab10" data-id="" role="tab">
            <i class="fas fa-money custom-icon"></i>Servis Kaynakları
          </a>
          <a class="dropdown-item " data-bs-toggle="pill" href="#tab11" data-id="" role="tab">
            <i class="fas fa-money custom-icon"></i>Servis Palanlama Personel Ayarları
          </a>
          <a class="dropdown-item " data-bs-toggle="pill" href="#tab12" data-id="" role="tab">
            <i class="fas fa-money custom-icon"></i>Servis Planlama Durumları
          </a>
          <a class="dropdown-item " data-bs-toggle="pill" href="#tab13" data-id="" role="tab">
            <i class="fas fa-money custom-icon"></i>Silinen Servisler
          </a>
        </div>
      </div>
    </li>
    
    <li class="nav-item" style="font-size: 14px;">
      <div class="dropdown">
        <a href="#" class="btn btn-secondary dropdown-toggle nav-link" data-bs-toggle="dropdown" aria-expanded="true">
          <span>İzinler ve Roller</span> <i class="fa fa-angle-down custom-icon"></i>
        </a>
        <div class="dropdown-menu" style="">
          <a class="dropdown-item nav14" data-bs-toggle="pill" href="#" data-id="#tab14" role="tab">
            <i class="fas fa-money custom-icon"></i>İzinler
          </a>
          <a class="dropdown-item nav15" data-bs-toggle="pill" href="#" data-id="#tab15" role="tab">
            <i class="fas fa-money custom-icon"></i>Roller
          </a>
        </div>
      </div>
    </li>

    <li class="nav-item" style="font-size: 14px;">
      <div class="dropdown">
        <a href="#" class="btn btn-secondary dropdown-toggle nav-link" data-bs-toggle="dropdown" aria-expanded="true">
          <span>Depo Ayarları</span> <i class="fa fa-angle-down custom-icon"></i>
        </a>
        <div class="dropdown-menu" style="">
          <a class="dropdown-item nav16" data-bs-toggle="pill" href="#" data-id="#tab16" role="tab">
            <i class="fas fa-money custom-icon"></i>Stok Kategorileri
          </a>
          <a class="dropdown-item nav17" data-bs-toggle="pill" href="#" data-id="#tab17" role="tab">
            <i class="fas fa-money custom-icon"></i>Stok Rafları
          </a>
          <a class="dropdown-item nav18" data-bs-toggle="pill" href="#" data-id="#tab18" role="tab">
            <i class="fas fa-money custom-icon"></i>Tedarikçiler
          </a>
        </div>
      </div>
    </li>

    <li class="nav-item" style="font-size: 14px;">
      <div class="dropdown">
        <a href="#" class="btn btn-secondary dropdown-toggle nav-link" data-bs-toggle="dropdown" aria-expanded="true">
          <span>Kasa Ayarları</span> <i class="fa fa-angle-down custom-icon"></i>
        </a>
        <div class="dropdown-menu" style="">
          <a class="dropdown-item nav19" data-bs-toggle="pill" href="#" data-id="#tab19" role="tab">
            <i class="fas fa-money custom-icon"></i>Ödeme Türleri
          </a>
          <a class="dropdown-item nav20" data-bs-toggle="pill" href="#" data-id="#tab20" role="tab">
            <i class="fas fa-money custom-icon"></i>Ödeme Şekilleri
          </a>
          
        </div>
      </div>
    </li>

    <li class="nav-item" style="font-size: 14px;">
      <div class="dropdown">
        <a href="#" class="btn btn-secondary dropdown-toggle nav-link" data-bs-toggle="dropdown" aria-expanded="true">
          <span>Yazıcı ve Uygulama Ayarları</span> <i class="fa fa-angle-down custom-icon"></i>
        </a>
        <div class="dropdown-menu" style="">
          
          <a class="dropdown-item nav22" data-bs-toggle="pill" href="#" data-id="#tab22" role="tab">
            <i class="fas fa-money custom-icon"></i>Servis Form Ayarları
          </a>
          <a class="dropdown-item nav23" data-bs-toggle="pill" href="#" data-id="#tab23" role="tab">
            <i class="fas fa-money custom-icon"></i>Yazıcı Fiş Tasarımı
          </a>
          
        </div>
      </div>
    </li>
    
  </ul> 
  <div class="tab-content">
    <div id="tab1" class="tab-pane active" style="padding: 0" role="tabpanel"></div>
    <div id="tab2" class="tab-pane fade in" style="padding: 0" role="tabpanel"></div>
    <div id="tab3" class="tab-pane fade in" style="padding: 0" role="tabpanel"></div>
    <div id="tab4" class="tab-pane fade in" style="padding: 0" role="tabpanel"></div>
    <div id="tab5" class="tab-pane fade in" style="padding: 0" role="tabpanel"></div>
    <div id="tab6" class="tab-pane fade in" style="padding: 0" role="tabpanel"></div>
    <div id="tab7" class="tab-pane fade in" style="padding: 0" role="tabpanel"></div>
    <div id="tab8" class="tab-pane fade in" style="padding: 0" role="tabpanel"></div>
    <div id="tab9" class="tab-pane fade in" style="padding: 0" role="tabpanel"></div>
    <div id="tab10" class="tab-pane fade in" style="padding: 0" role="tabpanel"></div>
    <div id="tab11" class="tab-pane fade in" style="padding: 0" role="tabpanel"></div>
    <div id="tab12" class="tab-pane fade in" style="padding: 0" role="tabpanel"></div>
    <div id="tab13" class="tab-pane fade in" style="padding: 0" role="tabpanel"></div>
    <div id="tab14" class="tab-pane fade in" style="padding: 0" role="tabpanel"></div>
    <div id="tab15" class="tab-pane fade in" style="padding: 0" role="tabpanel"></div>
    <div id="tab16" class="tab-pane fade in" style="padding: 0" role="tabpanel"></div>
    <div id="tab17" class="tab-pane fade in" style="padding: 0" role="tabpanel"></div>
    <div id="tab18" class="tab-pane fade in" style="padding: 0" role="tabpanel"></div>
    <div id="tab19" class="tab-pane fade in" style="padding: 0" role="tabpanel"></div>
    <div id="tab20" class="tab-pane fade in" style="padding: 0" role="tabpanel"></div>
    <div id="tab22" class="tab-pane fade in" style="padding: 0" role="tabpanel"></div>
    <div id="tab23" class="tab-pane fade in" style="padding: 0" role="tabpanel"></div>

  </div>
</div>
  
<script type="text/javascript">
  $(document).ready(function (e) {
    var firma_id = {{$firma->id}};
    $.ajax({
      url: "/"+ firma_id + "/firma-bilgileri"
    }).done(function(data) {
      if($.trim(data)==="-1"){
        window.location.reload(true);
      }else{
        $('#tab1').html(data);
      }
    });
  });
</script>
  
<script>
  $(document).ready(function () {
    function loadData(url, tabId) {
      $.ajax({
        url: url,
      }).done(function (data) {
        if ($.trim(data) === "-1") {
          window.location.reload(true);
        } else {
          $(tabId).html(data);
        }
      });
    }
  
    // Nav-link'ler için click olaylarını ayarlayın
    $('.kasaSubMenu .nav-link').on('click', function () {
      var tabMap = {
        "nav1": "/{{$firma->id}}/firma-bilgileri",
        "nav2": "/{{$firma->id}}/sms-ayarlari",
        "nav3": "/{{$firma->id}}/cihaz-markalari",
        "nav4": "/{{$firma->id}}/cihaz-turleri",
        "nav5": "/{{$firma->id}}/garanti-sureleri",
        "nav6": "/{{$firma->id}}/araclar",
        "nav7": "/{{$firma->id}}/servis-asamalari",
        "nav8": "/{{$firma->id}}/servis-asama-sorulari",
        "nav9": "/{{$firma->id}}/servis-zamanlama",
        "nav10": "/{{$firma->id}}/servis-kaynaklari",
        "nav11": "",
        "nav12": "",
        "nav13": "",
        "nav14": "/{{$firma->id}}/izinler",
        "nav15": "/{{$firma->id}}/roller",
        "nav16": "/{{$firma->id}}/stok-kategorileri",
        "nav17": "/{{$firma->id}}/stok-raflari",
        "nav18": "/{{$firma->id}}/stok-tedarikcileri",
        "nav19": "/{{$firma->id}}/odeme-turleri",
        "nav20": "/{{$firma->id}}/odeme-sekilleri",
        "nav22": "/{{$firma->id}}/servis-form/ayarlari",
        "nav23": "/{{$firma->id}}/yazici-fis/tasarimi",
      };
  
      var id = $(this).attr("class").split(' ')[1];
      var url = tabMap[id];
      var tabId = "#" + id.replace("nav", "tab");
  
      loadData(url, tabId);
    });
  
    // Dropdown-item'lar için click olaylarını ayarladık
    $('.kasaSubMenu .dropdown-item').on('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
  
      var tabMap = {
        "nav2": "/{{$firma->id}}/sms-ayarlari",
        "nav3": "/{{$firma->id}}/cihaz-markalari",
        "nav4": "/{{$firma->id}}/cihaz-turleri",
        "nav5": "/{{$firma->id}}/garanti-sureleri",
        "nav6": "/{{$firma->id}}/araclar",
        "nav7": "/{{$firma->id}}/servis-asamalari",
        "nav8": "/{{$firma->id}}/servis-asama-sorulari",
        "nav9": "/{{$firma->id}}/servis-zamanlama",
        "nav10": "/{{$firma->id}}/servis-kaynaklari",
        "nav11": "",
        "nav12": "",
        "nav13": "",
        "nav14": "/{{$firma->id}}/izinler",
        "nav15": "/{{$firma->id}}/roller",
        "nav16": "/{{$firma->id}}/stok-kategorileri",
        "nav17": "/{{$firma->id}}/stok-raflari",
        "nav18": "/{{$firma->id}}/stok-tedarikcileri",
        "nav19": "/{{$firma->id}}/odeme-turleri",
        "nav20": "/{{$firma->id}}/odeme-sekilleri",
        "nav22": "/{{$firma->id}}/servis-form/ayarlari",
        "nav23": "/{{$firma->id}}/yazici-fis/tasarimi",
      };
  
      var id = $(this).attr("class").split(' ')[1]; // dropdown-item'in ikinci class'ını alır
      var url = tabMap[id];
      var tabId = "#" + id.replace("nav", "tab"); // tab id'yi oluşturur
  
      loadData(url, tabId);
  
      // İlgili tab'ı aktif yap ve show sınıfını ekle
      $('.kasaSubMenu .tab-pane').removeClass('active show');
      $(tabId).addClass('active show');
    });
  
    // Dropdown'un kapanmasını engelle
    $('.kasaSubMenu .dropdown-menu').on('click', function (e) {
      e.stopPropagation(); // Olayın yayılmasını durdurur
    });
  });
</script>

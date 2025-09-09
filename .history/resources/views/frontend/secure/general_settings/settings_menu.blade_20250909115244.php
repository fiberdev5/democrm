<style>
        /* Ana konteyner için temel ayarlar */
        .kasaSubMenu {
            position: relative;
            z-index: 1000;
            background: #fff;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        /* Dropdown container için güçlendirilmiş z-index */
        .kasaSubMenu .dropdown {
            position: relative !important;
        }

        /* Dropdown menu için geliştirilmiş stil */
        .kasaSubMenu .dropdown-menu {
            position: absolute !important;
            top: 100% !important;
            left: 0 !important;
            min-width: 250px;
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            background-color: #ffffff;
            transform: translateY(2px);
            margin-top: 0;
        }

        /* Dropdown açık olduğunda ek ayarlar */
        .kasaSubMenu .dropdown.show .dropdown-menu {
            display: block !important;
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        /* Dropdown item'lar için stil */
        .kasaSubMenu .dropdown-item {
            padding: 10px 15px;
            transition: all 0.2s ease;
            white-space: nowrap;
            border-bottom: 1px solid #f8f9fa;
        }

        .kasaSubMenu .dropdown-item:last-child {
            border-bottom: none;
        }

        .kasaSubMenu .dropdown-item:hover {
            background-color: #f8f9fa;
            color: #495057;
        }

        .kasaSubMenu .dropdown-item.active {
            background-color: #007bff;
            color: #fff;
        }

        /* Nav pills için özelleştirilmiş stil */
        .kasaSubMenu .nav-pills .nav-link {
            border-radius: 6px;
            margin: 0 2px;
            padding: 8px 16px;
            transition: all 0.2s ease;
        }

        .kasaSubMenu .nav-pills .nav-link.dropdown-toggle::after {
            margin-left: 8px;
        }

        /* Custom icon boyutu */
        .custom-icon {
            font-size: 14px;
            margin-right: 8px;
        }

        .fa-angle-down {
            display: inline-block !important;
            transition: transform 0.2s ease;
        }

        /* Dropdown açık olduğunda ok döndürme */
        .dropdown.show .fa-angle-down {
            transform: rotate(180deg);
        }

        /* Tab content için stil */
        .tab-content {
            min-height: 100px;
            background: #fff;
            border-radius: 6px;
            padding: 5px;
        }

        .tab-pane {
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .tab-pane.active {
            opacity: 1;
            visibility: visible;
        }

        /* Responsive tasarım için */
        @media (max-width: 768px) {
            .kasaSubMenu .nav-pills {
                flex-direction: column;
            }
            
            .kasaSubMenu .nav-item {
                width: 100%;
                margin-bottom: 5px;
            }
            
            .kasaSubMenu .dropdown-menu {
                position: static !important;
                width: 100%;
                box-shadow: none;
                border: 1px solid #dee2e6;
                margin-top: 5px;
            }
        }

        /* Scroll bar için özel stil */
        .kasaSubMenu .dropdown-menu::-webkit-scrollbar {
            width: 6px;
        }

        .kasaSubMenu .dropdown-menu::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .kasaSubMenu .dropdown-menu::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        .kasaSubMenu .dropdown-menu::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Loading state için */
        .tab-content.loading {
            position: relative;
        }

        .tab-content.loading::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.8);
            z-index: 100;
        }

        .tab-content.loading::after {
            content: 'Yükleniyor...';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 101;
            padding: 10px 20px;
            background: #fff;
            border-radius: 4px;
        }
</style>

<div class="kasaSubMenu"  > {{-- Genel ayarlarda dropdown altta kaldığı zaman bu css'i eklemiştim. Daha sonra modalları bozmaktaydı. style="margin-top:15px;position:relative;z-index:10;"  --}}

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
          <a class="dropdown-item nav24" data-bs-toggle="pill" href="#tab24" data-id="" role="tab">
            <i class="fas fa-money custom-icon"></i>Prim Ayarları
          </a>
        </div>
      </div>
    </li>
    
    <li class="nav-item" style="font-size: 14px;">
      <div class="dropdown">
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
          {{-- <a class="dropdown-item " data-bs-toggle="pill" href="#tab11" data-id="" role="tab">
            <i class="fas fa-money custom-icon"></i>Servis Palanlama Personel Ayarları
          </a>
          <a class="dropdown-item " data-bs-toggle="pill" href="#tab12" data-id="" role="tab">
            <i class="fas fa-money custom-icon"></i>Servis Planlama Durumları
          </a> --}}
          <a class="dropdown-item nav13" data-bs-toggle="pill" href="#tab13" data-id="" role="tab">
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
    <div id="tab24" class="tab-pane fade in" style="padding: 0" role="tabpanel"></div>

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
    // Dropdown'ların düzgün kapanması için
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.dropdown').length) {
                    $('.dropdown-menu').removeClass('show');
                    $('.dropdown-toggle').attr('aria-expanded', 'false');
                }
            });
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
        "nav13": "/{{$firma->id}}/silinen-servisler",
        "nav14": "/{{$firma->id}}/izinler",
        "nav15": "/{{$firma->id}}/roller",
        "nav16": "/{{$firma->id}}/stok-kategorileri",
        "nav17": "/{{$firma->id}}/stok-raflari",
        "nav18": "/{{$firma->id}}/stok-tedarikcileri",
        "nav19": "/{{$firma->id}}/odeme-turleri",
        "nav20": "/{{$firma->id}}/odeme-sekilleri",
        "nav22": "/{{$firma->id}}/servis-form/ayarlari",
        "nav23": "/{{$firma->id}}/yazici-fis/tasarimi",
        "nav24": "/{{$firma->id}}/prim-ayarlari",
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
        "nav13": "/{{$firma->id}}/silinen-servisler",
        "nav14": "/{{$firma->id}}/izinler",
        "nav15": "/{{$firma->id}}/roller",
        "nav16": "/{{$firma->id}}/stok-kategorileri",
        "nav17": "/{{$firma->id}}/stok-raflari",
        "nav18": "/{{$firma->id}}/stok-tedarikcileri",
        "nav19": "/{{$firma->id}}/odeme-turleri",
        "nav20": "/{{$firma->id}}/odeme-sekilleri",
        "nav22": "/{{$firma->id}}/servis-form/ayarlari",
        "nav23": "/{{$firma->id}}/yazici-fis/tasarimi",
        "nav24": "/{{$firma->id}}/prim-ayarlari",
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

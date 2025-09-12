<!-- HTML KODU (İkonlar Kaldırıldı) -->
<div class="row pageDetail">
  <div class="col-12">
    <div class="row">
      <div class="col-12">
        <div class="statistics-menu">
          <a href="{{ route('statistics', $tenant_id) }}" class="btn btn-dark statistic-btn btn-servis">
            Servis İstatistikleri
          </a>
          <a href="{{ route('technician.statistics', $tenant_id) }}" class="btn btn-success statistic-btn btn-teknisyen">
            Teknisyen İstatistikleri
          </a>
          <a href="{{ route('operator.statistics', $tenant_id) }}" class="btn btn-info statistic-btn btn-operator">
            Operatör İstatistikleri
          </a>
          <a href="{{ route('state.statistics', $tenant_id) }}" class="btn statistic-btn btn-durum" style="background-color:#8e44ad;">
            Durum İstatistikleri
          </a>
          <a href="{{ route('stage.statistics', $tenant_id) }}" class="btn btn-warning statistic-btn btn-asama">
            Aşama İstatistikleri
          </a>
          <a href="{{ route('stock.statistics', $tenant_id) }}" class="btn statistic-btn btn-depo" style="background-color:#6e4c1e; ">
            Depo İstatistikleri
          </a>
          <a href="{{ route('ilce.statistics', $tenant_id) }}" class="btn statistic-btn btn-ilce" style="background-color:#1c3aa9; ">
            İlçe İstatistikleri
          </a>
          <a href="{{ route('survey.statistics', $tenant_id) }}" class="btn statistic-btn btn-anket" style="background-color:#4e6e3f; ">
            Anket İstatistikleri
          </a>
          <a href="{{ route('cash.statistics', $tenant_id) }}" class="btn btn-danger statistic-btn btn-kasa">
            Kasa İstatistikleri
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- YENİ ve DÜZELTİLMİŞ CSS KODU -->
<style>
    /* Menü Konteyneri: Otomatik olarak alt satıra geçen grid yapısı */
    .statistics-menu {
        display: grid;
        /* Bu sihirli satır, konteynere sığdığı kadar kartı yan yana dizer.
           Her kartın minimum 140px genişliğinde olmasını sağlar ve boşluk kalırsa
           eşit şekilde genişletir (1fr). */
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 1rem; /* Kartlar arasındaki boşluk */
    }

    /* Kompakt istatistik butonları (Kartlar) */
    .statistic-btn {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: 1px solid #dee2e6;
        border-radius: 12px;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        color: #495057;
        text-decoration: none;
        font-weight: 600; /* Yazıyı biraz daha belirgin yaptım */
        padding: 1rem;
        position: relative;
        overflow: hidden;
        transform: perspective(1px) translateZ(0);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 90px; /* Yüksekliği biraz artırdım */
        text-align: center;
        font-size: 0.85rem; /* Yazı boyutunu biraz büyüttüm */
    }

    .statistic-btn:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 6px 20px rgba(0,0,0,0.12);
        color: #495057;
        text-decoration: none;
    }

    .statistic-btn:active {
        transform: translateY(-1px) scale(1.02);
    }

    .statistic-btn:focus {
        outline: none;
        box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.15);
        border-color: #86b7fe;
    }

    /* Arka plan ikon stilleri: Sağ üst köşe için güncellendi */
    .statistic-btn::before {
        content: '';
        position: absolute;
        top: 8px;   /* Değişti */
        right: 8px; /* Değişti */
        transform: none; /* Değişti */
        width: 30px;     /* Değişti: Küçültüldü */
        height: 30px;    /* Değişti: Küçültüldü */
        opacity: 0.15;   /* Değişti: Daha şeffaf */
        transition: all 0.3s ease;
        z-index: 1;
    }

    .statistic-btn:hover::before {
        opacity: 0.3; /* Hover durumunda belirginleşir */
        transform: scale(1.1); /* Hafif büyüme efekti */
    }
    
    /* Her buton için özel arka plan ikonları (değişiklik yok) */
    .btn-servis::before { background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="%23343a40" d="M78.6 5C69.1-2.4 55.6-1.5 47 7L7 47c-8.5 8.5-9.4 22-2.1 31.6l80 104c4.5 5.9 11.6 9.4 19.1 9.4h54.1l109 109c-14.7 29-10 65.4 14.3 89.6l112 112c12.5 12.5 32.8 12.5 45.3 0l64-64c12.5-12.5 12.5-32.8 0-45.3L400.4 295.3c-24.2-24.2-60.6-29-89.6-14.3l-109-109V117.9c0-7.5-3.5-14.6-9.4-19.1L112 18.7C107.6 15.3 92.9 0.8 78.6 5z"/></svg>') no-repeat center; background-size: contain; }
    .btn-teknisyen::before { background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path fill="%23198754" d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c1.8 0 3.5-.2 5.3-.5c-76.3-55.1-99.8-141-103.1-200.2c-16.1-4.8-33.1-7.3-50.7-7.3H178.3zm308.8-78.3l-120 48C358 277.4 352 286.2 352 296c0 63.3 25.9 168.8 134.8 214.2c5.9 2.5 12.6 2.5 18.5 0C614.1 464.8 640 359.3 640 296c0-9.8-6-18.6-15.1-22.3l-120-48c-5.7-2.3-12.1-2.3-17.8 0zM591.4 312c-3.9 50.7-27.2 116.7-95.4 149.7V273.8L591.4 312z"/></svg>') no-repeat center; background-size: contain; }
    .btn-operator::before { background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="%230dcaf0" d="M256 64H64C28.7 64 0 92.7 0 128v256c0 35.3 28.7 64 64 64H256c35.3 0 64-28.7 64-64V128c0-35.3-28.7-64-64-64zM64 112c0-8.8 7.2-16 16-16s16 7.2 16 16v32c0 8.8-7.2 16-16 16s-16-7.2-16-16V112zM272 384c0 8.8-7.2 16-16 16s-16-7.2-16-16v-32c0-8.8 7.2-16 16-16s16 7.2 16 16v32zm240-176h-32c-8.8 0-16-7.2-16-16s7.2-16 16-16h32c8.8 0 16 7.2 16 16s-7.2 16-16 16z"/></svg>') no-repeat center; background-size: contain; }
    .btn-durum::before { background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="%238e44ad" d="M192 0c-41.8 0-77.4 26.7-90.5 64H64C28.7 64 0 92.7 0 128V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V128c0-35.3-28.7-64-64-64H282.5C269.4 26.7 233.8 0 192 0zm0 64a32 32 0 1 1 0 64 32 32 0 1 1 0-64zM72 272a24 24 0 1 1 48 0 24 24 0 1 1 -48 0zm104-16H304c8.8 0 16 7.2 16 16s-7.2 16-16 16H176c-8.8 0-16-7.2-16-16s7.2-16 16-16zM72 368a24 24 0 1 1 48 0 24 24 0 1 1 -48 0zm104-16H304c8.8 0 16 7.2 16 16s-7.2 16-16 16H176c-8.8 0-16-7.2-16-16s7.2-16 16-16z"/></svg>') no-repeat center; background-size: contain; }
    .btn-asama::before { background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="%23ffc107" d="M4.1 38.2C1.4 34.2 0 29.4 0 24.6C0 11 11 0 24.6 0h62.8C99.9 0 110.9 11 110.9 24.6c0 4.8-1.4 9.6-4.1 13.6L85.3 80H384c35.3 0 64 28.7 64 64v352c0 35.3-28.7 64-64 64H128c-35.3 0-64-28.7-64-64V144c0-35.3 28.7-64 64-64h21.3L4.1 38.2zM160 224c-8.8 0-16 7.2-16 16s7.2 16 16 16H352c8.8 0 16-7.2 16-16s-7.2-16-16-16H160z"/></svg>') no-repeat center; background-size: contain; }
    .btn-depo::before { background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path fill="%236e4c1e" d="M0 488V171.3c0-26.2 15.9-49.7 40.2-59.4L331.1 4.8c7.6-3.1 16.1-3.1 23.8 0L645.8 111.9c24.3 9.7 40.2 33.3 40.2 59.4V488c0 13.3-10.7 24-24 24H568c-13.3 0-24-10.7-24-24V432c0-17.7-14.3-32-32-32H384c-17.7 0-32 14.3-32 32v56c0 13.3-10.7 24-24 24H24c-13.3 0-24-10.7-24-24z"/></svg>') no-repeat center; background-size: contain; }
    .btn-ilce::before { background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="%231c3aa9" d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 128a64 64 0 1 1 0 128 64 64 0 1 1 0-128z"/></svg>') no-repeat center; background-size: contain; }
    .btn-anket::before { background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="%234e6e3f" d="M160 80c0-26.5 21.5-48 48-48h32c26.5 0 48 21.5 48 48V432c0 26.5-21.5 48-48 48H208c-26.5 0-48-21.5-48-48V80zM0 272c0-26.5 21.5-48 48-48H80c26.5 0 48 21.5 48 48V432c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V272zM368 96h32c26.5 0 48 21.5 48 48V432c0 26.5-21.5 48-48 48H368c-26.5 0-48-21.5-48-48V144c0-26.5 21.5-48 48-48z"/></svg>') no-repeat center; background-size: contain; }
    .btn-kasa::before { background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="%23dc3545" d="M64 64C28.7 64 0 92.7 0 128V384c0 35.3 28.7 64 64 64H512c35.3 0 64-28.7 64-64V128c0-35.3-28.7-64-64-64H64zm64 320H96c-17.7 0-32-14.3-32-32s14.3-32 32-32h32c17.7 0 32 14.3 32 32s-14.3 32-32 32zM96 192h32c17.7 0 32-14.3 32-32s-14.3-32-32-32H96c-17.7 0-32 14.3-32 32s14.3 32 32 32zm320 192c-17.7 0-32-14.3-32-32s14.3-32 32-32h64c17.7 0 32 14.3 32 32s-14.3 32-32 32H416zm64-192c17.7 0 32-14.3 32-32s-14.3-32-32-32H416c-17.7 0-32 14.3-32 32s14.3 32 32 32h64zM288 160a96 96 0 1 1 0 192 96 96 0 1 1 0-192z"/></svg>') no-repeat center; background-size: contain; }

    /*
      NOT: Eski kodunuzdaki @media ile başlayan tüm responsive
      ayarlarını silebilirsiniz. Yukarıdaki yeni .statistics-menu
      yapısı bu ihtiyacı ortadan kaldırmıştır.
    */
</style>
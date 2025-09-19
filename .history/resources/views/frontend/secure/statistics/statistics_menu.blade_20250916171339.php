<div class="row pageDetail">
  <div class="col-12">
    <div class="row">
      <div class="col-12">
        <div class="statistics-menu">
          <a href="{{ route('statistics', $tenant_id) }}" class="btn statistic-btn btn-servis">
            Servis İstatistikleri
          </a>
          <a href="{{ route('technician.statistics', $tenant_id) }}" class="btn  statistic-btn btn-teknisyen">
            Teknisyen İstatistikleri
          </a>
          <a href="{{ route('operator.statistics', $tenant_id) }}" class="btn  statistic-btn btn-operator">
            Operatör İstatistikleri
          </a>
          <a href="{{ route('state.statistics', $tenant_id) }}" class="btn statistic-btn btn-durum">
            Durum İstatistikleri
          </a>
          <a href="{{ route('stage.statistics', $tenant_id) }}" class="btn  statistic-btn btn-asama">
            Aşama İstatistikleri
          </a>
          <a href="{{ route('stock.statistics', $tenant_id) }}" class="btn statistic-btn btn-depo">
            Depo İstatistikleri
          </a>
          <a href="{{ route('ilce.statistics', $tenant_id) }}" class="btn statistic-btn btn-ilce">
            İlçe İstatistikleri
          </a>
          <a href="{{ route('survey.statistics', $tenant_id) }}" class="btn statistic-btn btn-anket">
            Anket İstatistikleri
          </a>
          <a href="{{ route('cash.statistics', $tenant_id) }}" class="btn  statistic-btn btn-kasa">
            Kasa İstatistikleri
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
 .statistic-btn {
        background: linear-gradient(135deg, var(--card-bg-1), var(--card-bg-2));
        border-radius: 16px;
        padding: 15px 20px;
        color: white;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        border: none;
        margin-bottom: 16px;
        cursor: pointer;
        text-decoration: none;
        display: block;
        font-weight: 600;
        text-align: center;
        font-size: 0.80rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .statistic-btn:hover {
        text-decoration: underline !important;
        color: #fff !important;
    }


    /* .statistic-btn::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 66px;
        height: 66px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
        transform: translate(30px, -30px);
    } */

    /* Arka plan ikon stilleri: Sağ üst köşe */
    .statistic-btn::after {
        content: '';
        position: absolute;
        top: 8px;
        right: 8px;
        width: 20px;
        height: 20px;
        opacity: 0.3;
        transition: all 0.3s ease;
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        z-index: 2;
    }

    /* Renk tanımlamaları */
    .btn-servis { --card-bg-1: #495057; --card-bg-2: #6c757d; }
    .btn-teknisyen { --card-bg-1: #495057; --card-bg-2: #6c757d; }
    .btn-operator { --card-bg-1: #495057; --card-bg-2: #6c757d; }
    .btn-durum { --card-bg-1: #495057; --card-bg-2: #6c757d; }
    .btn-asama { --card-bg-1: #495057; --card-bg-2: #6c757d; }
    .btn-depo { --card-bg-1: #495057; --card-bg-2: #6c757d; }
    .btn-ilce { --card-bg-1: #495057; --card-bg-2: #6c757d; }
    .btn-anket { --card-bg-1: #495057; --card-bg-2: #6c757d; }
    .btn-kasa { --card-bg-1: #495057; --card-bg-2: #6c757d; }

    .statistics-menu {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        gap: 0.75rem;
        width: 100%;
    }

    @media (max-width: 1400px) {
        .statistics-menu {
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        }
    }

    @media (max-width: 1200px) {
        .statistics-menu {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 992px) {
        .statistics-menu {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
        }
    }
      
    @media (max-width: 768px) {
        .statistics-menu {
            grid-template-columns: 1fr;
            gap: 0.5rem;
        }
        
        .statistic-btn {
            justify-content: flex-start;
            padding: 10px 12px;
        }
    }

    @media (max-width: 576px) {
        .statistic-btn {
            font-size: 0.8rem;
             padding: 8px 10px;
        }
    }
            
    /* Her buton için özel arka plan ikonları - beyaz renkte */
    .btn-servis::after { 
        background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="%23ffffff" d="M78.6 5C69.1-2.4 55.6-1.5 47 7L7 47c-8.5 8.5-9.4 22-2.1 31.6l80 104c4.5 5.9 11.6 9.4 19.1 9.4h54.1l109 109c-14.7 29-10 65.4 14.3 89.6l112 112c12.5 12.5 32.8 12.5 45.3 0l64-64c12.5-12.5 12.5-32.8 0-45.3L400.4 295.3c-24.2-24.2-60.6-29-89.6-14.3l-109-109V117.9c0-7.5-3.5-14.6-9.4-19.1L112 18.7C107.6 15.3 92.9 0.8 78.6 5z"/></svg>'); 
    }
    .btn-teknisyen::after { 
        background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path fill="%23ffffff" d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c1.8 0 3.5-.2 5.3-.5c-76.3-55.1-99.8-141-103.1-200.2c-16.1-4.8-33.1-7.3-50.7-7.3H178.3zm308.8-78.3l-120 48C358 277.4 352 286.2 352 296c0 63.3 25.9 168.8 134.8 214.2c5.9 2.5 12.6 2.5 18.5 0C614.1 464.8 640 359.3 640 296c0-9.8-6-18.6-15.1-22.3l-120-48c-5.7-2.3-12.1-2.3-17.8 0zM591.4 312c-3.9 50.7-27.2 116.7-95.4 149.7V273.8L591.4 312z"/></svg>'); 
    }
    .btn-operator::after { 
        background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="%23ffffff" d="M256 64H64C28.7 64 0 92.7 0 128v256c0 35.3 28.7 64 64 64H256c35.3 0 64-28.7 64-64V128c0-35.3-28.7-64-64-64zM64 112c0-8.8 7.2-16 16-16s16 7.2 16 16v32c0 8.8-7.2 16-16 16s-16-7.2-16-16V112zM272 384c0 8.8-7.2 16-16 16s-16-7.2-16-16v-32c0-8.8 7.2-16 16-16s16 7.2 16 16v32zm240-176h-32c-8.8 0-16-7.2-16-16s7.2-16 16-16h32c8.8 0 16 7.2 16 16s-7.2 16-16 16z"/></svg>'); 
    }
    .btn-durum::after { 
        background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="%23ffffff" d="M192 0c-41.8 0-77.4 26.7-90.5 64H64C28.7 64 0 92.7 0 128V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V128c0-35.3-28.7-64-64-64H282.5C269.4 26.7 233.8 0 192 0zm0 64a32 32 0 1 1 0 64 32 32 0 1 1 0-64zM72 272a24 24 0 1 1 48 0 24 24 0 1 1 -48 0zm104-16H304c8.8 0 16 7.2 16 16s-7.2 16-16 16H176c-8.8 0-16-7.2-16-16s7.2-16 16-16zM72 368a24 24 0 1 1 48 0 24 24 0 1 1 -48 0zm104-16H304c8.8 0 16 7.2 16 16s-7.2 16-16 16H176c-8.8 0-16-7.2-16-16s7.2-16 16-16z"/></svg>'); 
    }
    .btn-asama::after { 
        background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="%23ffffff" d="M4.1 38.2C1.4 34.2 0 29.4 0 24.6C0 11 11 0 24.6 0h62.8C99.9 0 110.9 11 110.9 24.6c0 4.8-1.4 9.6-4.1 13.6L85.3 80H384c35.3 0 64 28.7 64 64v352c0 35.3-28.7 64-64 64H128c-35.3 0-64-28.7-64-64V144c0-35.3 28.7-64 64-64h21.3L4.1 38.2zM160 224c-8.8 0-16 7.2-16 16s7.2 16 16 16H352c8.8 0 16-7.2 16-16s-7.2-16-16-16H160z"/></svg>'); 
    }
    .btn-depo::after { 
        background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path fill="%23ffffff" d="M0 488V171.3c0-26.2 15.9-49.7 40.2-59.4L331.1 4.8c7.6-3.1 16.1-3.1 23.8 0L645.8 111.9c24.3 9.7 40.2 33.3 40.2 59.4V488c0 13.3-10.7 24-24 24H568c-13.3 0-24-10.7-24-24V432c0-17.7-14.3-32-32-32H384c-17.7 0-32 14.3-32 32v56c0 13.3-10.7 24-24 24H24c-13.3 0-24-10.7-24-24z"/></svg>'); 
    }
    .btn-ilce::after { 
        background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="%23ffffff" d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 128a64 64 0 1 1 0 128 64 64 0 1 1 0-128z"/></svg>'); 
    }
    .btn-anket::after { 
        background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="%23ffffff" d="M160 80c0-26.5 21.5-48 48-48h32c26.5 0 48 21.5 48 48V432c0 26.5-21.5 48-48 48H208c-26.5 0-48-21.5-48-48V80zM0 272c0-26.5 21.5-48 48-48H80c26.5 0 48 21.5 48 48V432c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V272zM368 96h32c26.5 0 48 21.5 48 48V432c0 26.5-21.5 48-48 48H368c-26.5 0-48-21.5-48-48V144c0-26.5 21.5-48 48-48z"/></svg>'); 
    }
    .btn-kasa::after { 
        background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="%23ffffff" d="M64 64C28.7 64 0 92.7 0 128V384c0 35.3 28.7 64 64 64H512c35.3 0 64-28.7 64-64V128c0-35.3-28.7-64-64-64H64zm64 320H96c-17.7 0-32-14.3-32-32s14.3-32 32-32h32c17.7 0 32 14.3 32 32s-14.3 32-32 32zM96 192h32c17.7 0 32-14.3 32-32s-14.3-32-32-32H96c-17.7 0-32 14.3-32 32s14.3 32 32 32zm320 192c-17.7 0-32-14.3-32-32s14.3-32 32-32h64c17.7 0 32 14.3 32 32s-14.3 32-32 32H416zm64-192c17.7 0 32-14.3 32-32s-14.3-32-32-32H416c-17.7 0-32 14.3-32 32s14.3 32 32 32h64zM288 160a96 96 0 1 1 0 192 96 96 0 1 1 0-192z"/></svg>'); 
    }
</style>
<div class="row pageDetail">
      <div class="col-12">
        {{-- <div class="card">
          <div class="card-body"> --}}
            <div class="row">
              <div class="col-12">
                <div class="statistics-menu">
                  <a href="{{ route('statistics', $tenant_id) }}" class="btn btn-dark statistic-btn btn-servis mb-2">
                    <i class="fas fa-tools"></i> Servis İstatistikleri
                  </a>
                  <a href="{{ route('technician.statistics', $tenant_id) }}" class="btn btn-success statistic-btn btn-teknisyen mb-2">
                    <i class="fas fa-user-cog"></i> Teknisyen İstatistikleri
                  </a>
                  <a href="{{ route('operator.statistics', $tenant_id) }}" class="btn btn-info statistic-btn btn-operator mb-2">
                    <i class="fas fa-headset"></i> Operatör İstatistikleri
                  </a>
                  <a href="{{ route('state.statistics', $tenant_id) }}" class="btn statistic-btn btn-durum mb-2" style="background-color:#8e44ad; color: #fff;">
                    <i class="fas fa-clipboard-check"></i> Durum İstatistikleri
                  </a>

                  <a href="{{ route('stage.statistics', $tenant_id) }}" class="btn btn-warning statistic-btn mb-2">
                    <i class="fas fa-stream"></i> Aşama İstatistikleri
                  </a>
                  <a href="{{ route('stock.statistics', $tenant_id) }}" class="btn statistic-btn mb-2" style="background-color:#6e4c1e; color: #fff;">
                    <i class="fas fa-warehouse"></i> Depo İstatistikleri
                  </a>
                  <a href="{{ route('ilce.statistics', $tenant_id) }}" class="btn statistic-btn mb-2" style="background-color:#1c3aa9; color: #fff;">
                    <i class="fas fa-map-marked-alt"></i> İlçe İstatistikleri
                  </a>
                  <a href="{{ route('survey.statistics', $tenant_id) }}" class="btn statistic-btn mb-2" style="background-color:#4e6e3f; color: #fff;">
                    <i class="fas fa-poll"></i> Anket İstatistikleri
                  </a>
                  <a href="{{ route('cash.statistics', $tenant_id) }}" class="btn btn-danger statistic-btn btn-kasa mb-2">
                    <i class="fas fa-money-bill"></i> Kasa İstatistikleri
                  </a>

                </div>
              </div>
            </div>
          {{-- </div>
        </div> --}}
      </div>
    </div>

<style>
          /* İstatistik menü kartı */
        .statistics-menu-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: none;
            margin-bottom: 2.5rem;
            padding: 2rem;
        }

        .statistics-menu-header {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f1f3f4;
        }

        .statistics-menu-header h5 {
            color: #2c3e50;
            font-weight: 600;
            margin: 0;
        }

        /* Grid düzenlemesi */
        .statistics-menu {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1rem;
            width: 100%;
        }

        /* Modern istatistik butonları */
        .statistic-btn {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 1px solid #dee2e6;
            border-radius: 16px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            color: #495057;
            text-decoration: none;
            font-weight: 500;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
            transform: perspective(1px) translateZ(0);
            display: flex;
            align-items: center;
            justify-content: flex-start;
            min-height: 70px;
        }

        .statistic-btn:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            color: #495057;
            text-decoration: none;
        }

        .statistic-btn:active {
            transform: translateY(-2px) scale(1.01);
        }

        .statistic-btn:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
            border-color: #86b7fe;
        }

        /* İkon stilleri - arka planda büyük */
        .statistic-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            width: 60px;
            height: 60px;
            opacity: 0.1;
            transition: all 0.3s ease;
            z-index: 1;
        }

        .statistic-btn i {
            font-size: 1.5rem;
            margin-right: 1rem;
            transition: all 0.3s ease;
            z-index: 2;
            position: relative;
        }

        .statistic-btn span {
            font-size: 1rem;
            font-weight: 500;
            z-index: 2;
            position: relative;
        }

        /* Her buton için özel renkler */
        .btn-servis::before { 
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="%23666" d="M78.6 5C69.1-2.4 55.6-1.5 47 7L7 47c-8.5 8.5-9.4 22-2.1 31.6l80 104c4.5 5.9 11.6 9.4 19.1 9.4h54.1l109 109c-14.7 29-10 65.4 14.3 89.6l112 112c12.5 12.5 32.8 12.5 45.3 0l64-64c12.5-12.5 12.5-32.8 0-45.3L400.4 295.3c-24.2-24.2-60.6-29-89.6-14.3l-109-109V117.9c0-7.5-3.5-14.6-9.4-19.1L112 18.7C107.6 15.3 92.9 0.8 78.6 5z"/></svg>') no-repeat center;
            background-size: contain;
        }
        
        .btn-teknisyen::before { 
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path fill="%23666" d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c1.8 0 3.5-.2 5.3-.5c-76.3-55.1-99.8-141-103.1-200.2c-16.1-4.8-33.1-7.3-50.7-7.3H178.3zm308.8-78.3l-120 48C358 277.4 352 286.2 352 296c0 63.3 25.9 168.8 134.8 214.2c5.9 2.5 12.6 2.5 18.5 0C614.1 464.8 640 359.3 640 296c0-9.8-6-18.6-15.1-22.3l-120-48c-5.7-2.3-12.1-2.3-17.8 0zM591.4 312c-3.9 50.7-27.2 116.7-95.4 149.7V273.8L591.4 312z"/></svg>') no-repeat center;
            background-size: contain;
        }
        
        .btn-operator::before { 
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="%23666" d="M256 64H64C28.7 64 0 92.7 0 128v256c0 35.3 28.7 64 64 64H256c35.3 0 64-28.7 64-64V128c0-35.3-28.7-64-64-64zM64 112c0-8.8 7.2-16 16-16s16 7.2 16 16v32c0 8.8-7.2 16-16 16s-16-7.2-16-16V112zM272 384c0 8.8-7.2 16-16 16s-16-7.2-16-16v-32c0-8.8 7.2-16 16-16s16 7.2 16 16v32zm240-176h-32c-8.8 0-16-7.2-16-16s7.2-16 16-16h32c8.8 0 16 7.2 16 16s-7.2 16-16 16z"/></svg>') no-repeat center;
            background-size: contain;
        }
        
        .btn-durum::before { 
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="%23666" d="M192 0c-41.8 0-77.4 26.7-90.5 64H64C28.7 64 0 92.7 0 128V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V128c0-35.3-28.7-64-64-64H282.5C269.4 26.7 233.8 0 192 0zm0 64a32 32 0 1 1 0 64 32 32 0 1 1 0-64zM72 272a24 24 0 1 1 48 0 24 24 0 1 1 -48 0zm104-16H304c8.8 0 16 7.2 16 16s-7.2 16-16 16H176c-8.8 0-16-7.2-16-16s7.2-16 16-16zM72 368a24 24 0 1 1 48 0 24 24 0 1 1 -48 0zm104-16H304c8.8 0 16 7.2 16 16s-7.2 16-16 16H176c-8.8 0-16-7.2-16-16s7.2-16 16-16z"/></svg>') no-repeat center;
            background-size: contain;
        }
        
        .btn-asama::before { 
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="%23666" d="M4.1 38.2C1.4 34.2 0 29.4 0 24.6C0 11 11 0 24.6 0h62.8C99.9 0 110.9 11 110.9 24.6c0 4.8-1.4 9.6-4.1 13.6L85.3 80H384c35.3 0 64 28.7 64 64v352c0 35.3-28.7 64-64 64H128c-35.3 0-64-28.7-64-64V144c0-35.3 28.7-64 64-64h21.3L4.1 38.2zM160 224c-8.8 0-16 7.2-16 16s7.2 16 16 16H352c8.8 0 16-7.2 16-16s-7.2-16-16-16H160z"/></svg>') no-repeat center;
            background-size: contain;
        }
        
        .btn-depo::before { 
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path fill="%23666" d="M0 488V171.3c0-26.2 15.9-49.7 40.2-59.4L331.1 4.8c7.6-3.1 16.1-3.1 23.8 0L645.8 111.9c24.3 9.7 40.2 33.3 40.2 59.4V488c0 13.3-10.7 24-24 24H568c-13.3 0-24-10.7-24-24V432c0-17.7-14.3-32-32-32H384c-17.7 0-32 14.3-32 32v56c0 13.3-10.7 24-24 24H24c-13.3 0-24-10.7-24-24z"/></svg>') no-repeat center;
            background-size: contain;
        }
        
        .btn-ilce::before { 
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="%23666" d="M302.8 312C334.9 271.9 408 174.6 408 120C408 53.7 354.3 0 288 0S168 53.7 168 120c0 54.6 73.1 151.9 105.2 192c7.7 9.6 22 9.6 29.6 0zM416 503l144.9-58c9.1-3.6 15.1-12.5 15.1-22.3V152c0-17-17.1-28.6-32.9-22.3l-116 46.4c-.5 1.2-1 2.5-1.5 3.7c-2.9 6.8-6.1 13.7-9.6 20.6V503zM15.1 187.3C6 191 0 199.8 0 209.6v213.1c0 9.8 6 18.6 15.1 22.3l144.9 58V200.4c-3.5-6.9-6.7-13.8-9.6-20.6c-.5-1.2-1-2.4-1.5-3.7L32 130.7c-15.8-6.3-32.9 5.3-32.9 22.3z"/></svg>') no-repeat center;
            background-size: contain;
        }
        
        .btn-anket::before { 
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="%23666" d="M160 80c0-26.5 21.5-48 48-48h32c26.5 0 48 21.5 48 48V432c0 26.5-21.5 48-48 48H208c-26.5 0-48-21.5-48-48V80zM0 272c0-26.5 21.5-48 48-48H80c26.5 0 48 21.5 48 48V432c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V272zM368 96h32c26.5 0 48 21.5 48 48V432c0 26.5-21.5 48-48 48H368c-26.5 0-48-21.5-48-48V144c0-26.5 21.5-48 48-48z"/></svg>') no-repeat center;
            background-size: contain;
        }
        
        .btn-kasa::before { 
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="%23666" d="M512 80c0 18-14.3 34.6-38.4 48c-29.1 16.1-72.5 27.5-122.3 30.9c-3.7-1.8-7.4-3.7-11.3-5.5C434.9 121.1 512 94.5 512 80zM463.9 0c36.6 0 66.4 29.8 66.4 66.4c0 17.4-8.5 32.8-21.6 42.4C497.4 118.1 504 147.6 504 178.3c0 88.4-71.6 160-160 160s-160-71.6-160-160c0-30.7 6.6-60.2 18.9-89.4C189.8 99.2 181.3 83.8 181.3 66.4C181.3 29.8 211.1 0 247.7 0c36.6 0 66.4 29.8 66.4 66.4c0 6.3-.9 12.4-2.6 18.2c14.8-2.6 30.1-4 45.7-4c15.6 0 30.9 1.4 45.7 4c-1.7-5.8-2.6-11.9-2.6-18.2C400.3 29.8 430.1 0 466.7 0z"/></svg>') no-repeat center;
            background-size: contain;
        }

        /* İkon renkleri */
        .btn-servis i { color: #343a40; }
        .btn-teknisyen i { color: #198754; }
        .btn-operator i { color: #0dcaf0; }
        .btn-durum i { color: #8e44ad; }
        .btn-asama i { color: #ffc107; }
        .btn-depo i { color: #6e4c1e; }
        .btn-ilce i { color: #1c3aa9; }
        .btn-anket i { color: #4e6e3f; }
        .btn-kasa i { color: #dc3545; }

        /* İstatistik kartı için margin düzenlemesi */
        .istatistik-card {
            margin-top: 1.5rem;
            margin-bottom: 1.5rem;
        }

        /* Responsive düzenlemeler */
        @media (max-width: 1400px) {
            .statistics-menu {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            }
        }

        @media (max-width: 1200px) {
            .statistics-menu {
                grid-template-columns: repeat(3, 1fr);
                gap: 0.75rem;
            }
        }

        @media (max-width: 992px) {
            .statistics-menu {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.75rem;
            }
            
            .statistic-btn {
                font-size: 0.9rem;
                padding: 1.25rem;
                min-height: 60px;
            }

            .statistic-btn i {
                font-size: 1.3rem;
            }

            .statistic-btn::before {
                width: 50px;
                height: 50px;
            }
        }

        @media (max-width: 768px) {
            .statistics-menu {
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }
            
            .statistics-menu-card {
                padding: 1.5rem;
                margin-bottom: 2rem;
            }
            
            .statistic-btn {
                padding: 1.25rem;
            }
        }

        @media (max-width: 576px) {
            .statistic-btn {
                font-size: 0.85rem;
                padding: 1rem;
                min-height: 55px;
            }
            
            .statistic-btn i {
                font-size: 1.2rem;
                margin-right: 0.75rem;
            }

            .statistic-btn::before {
                width: 45px;
                height: 45px;
                right: 10px;
            }
        }
</style>
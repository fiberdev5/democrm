 <!-- ========== Left Sidebar Start ========== -->
 @php 
 $siteurl= App\Models\Settings::find(1);
 $user = Auth::user();
 @endphp


 
 <div class="vertical-menu" style="width:186px;">
   <div data-simplebar class="h-100">

     <!--- Sidebar -->
     <div id="sidebar-menu">
       <ul class="metismenu list-unstyled" id="side-menu" style="padding-top: 2px;"> 
                          @if(Auth::user()->isSuperAdmin())
              <li>
                  <a href="{{ route('super.admin.tenants') }}" class="waves-effect">
                      <i class="fas fa-building"></i><span class="badge rounded-pill bg-success float-end"></span>
                      <span>Tüm Firmalar</span>
                  </a>
              </li>
              
              {{-- Destek Talepleri Ana Menüsü --}}
              <li>
                  <a href="{{ route('super.admin.destek.dashboard') }}" class="waves-effect">
                      <i class="fas fa-life-ring"></i>
                      @php
                          $openTicketsCount = \App\Models\SupportTicket::where('status', 'acik')->count();
                      @endphp
                      @if($openTicketsCount > 0)
                          <span class="badge rounded-pill bg-danger float-end">{{ $openTicketsCount }}</span>
                      @endif
                      <span>Destek Talepleri</span>
                  </a>
              </li>

              {{-- Tüm Talepler (Ayrı bir menü öğesi olarak) --}}
              <li>
                  <a href="{{ route('super.admin.destek.index') }}" class="waves-effect" style="padding-left: 40px;"> {{-- İçeride görünmesi için boşluk --}}
                      <i class="fas fa-list-alt"></i> {{-- Farklı bir ikon kullanabilirsiniz --}}
                      <span>Tüm Talepler</span>
                  </a>
              </li>

              {{-- Açık Talepler (Ayrı bir menü öğesi olarak) --}}
              <li>
                  <a href="{{ route('super.admin.destek.index', ['status' => 'acik']) }}" class="waves-effect" style="padding-left: 40px;">
                      <i class="fas fa-folder-open"></i> {{-- Farklı bir ikon kullanabilirsiniz --}}
                      <span>Açık Talepler</span>
                  </a>
              </li>

              {{-- Acil Talepler (Ayrı bir menü öğesi olarak) --}}
              <li>
                  <a href="{{ route('super.admin.destek.index', ['priority' => 'yuksek']) }}" class="waves-effect" style="padding-left: 40px;">
                      <i class="fas fa-exclamation-triangle"></i> {{-- Farklı bir ikon kullanabilirsiniz --}}
                      <span>Acil Talepler</span>
                  </a>
              </li>
          @endif

        @if(auth()->user()->can('Anasayfayı Görebilir'))
         <li>
           <a href="{{ route('secure.home', $user->tenant_id)}}" class="waves-effect">
             <i class="ri-dashboard-line"></i><span class="badge rounded-pill bg-success float-end"></span>
             <span>Anasayfa</span>
           </a>
         </li>
         @endif

@cannot('Servisleri Göremez')
    <li>
        <a href="{{ route('all.services', $user->tenant_id)}}" class="waves-effect">
            <i class="ri-file-paper-2-line"></i>
            <span class="badge rounded-pill bg-success float-end"></span>
            <span>Servisler</span>
        </a>
    </li>
@endcannot


         @if(auth()->user()->can('Müşterileri Görebilir'))
        <li>
          <a href="{{route('customers', $user->tenant_id)}}" class="waves-effect">
            <i class="fas fa-address-card"></i><span class="badge rounded-pill bg-success float-end"></span>
            <span>Müşteriler</span>
          </a>
        </li>
        @endif

         @if(auth()->user()->can('Personelleri Görebilir'))
         <li>
          <a href="{{ route('staffs',$user->tenant_id)}}" class="waves-effect">
            <i class="ri-account-circle-line"></i><span class="badge rounded-pill bg-success float-end"></span>
            <span>Personeller</span>
          </a>
        </li>
        @endif

        @if(auth()->user()->can('Bayileri Görebilir'))
        <li>
          <a href="{{ route('dealers',$user->tenant_id)}}" class="waves-effect">
            <i class="ri-store-2-line"></i><span class="badge rounded-pill bg-success float-end"></span>
            <span>Bayiler</span>
          </a>
        </li>
        @endif

        @if(auth()->user()->can('Depoyu Görebilir'))
        <li>
          <a href="{{ route('stocks',$user->tenant_id)}}" class="waves-effect">
            <i class="ri-stock-fill"></i><span class="badge rounded-pill bg-success float-end"></span>
            <span>Depo</span>
          </a>
        </li>
        @endif

        @if(auth()->user()->can('Faturaları Görebilir'))
        <li>
          <a href="{{ route('all.invoices', $user->tenant_id) }}" class="waves-effect">
            <i class="ri-edit-box-fill"></i><span class="badge rounded-pill bg-success float-end"></span>
            <span>Faturalar</span>
          </a>
        </li>
        @endif

        @if(auth()->user()->can('Teklifleri Görür'))
        <li>
          <a href="{{route('offers', $user->tenant_id)}}" class="waves-effect">
            <i class="fas fa-text-width"></i><span class="badge rounded-pill bg-success float-end"></span>
            <span>Teklifler</span>
          </a>
        </li>


        <li>
          <a href="{{route('statistics', $user->tenant_id)}}" class="waves-effect">
            <i class="fas fa-chart-pie"></i><span class="badge rounded-pill bg-success float-end"></span>
            <span>Raporlar</span>
          </a>
        </li>
        @endif

        @if(auth()->user()->can('Kasayı Görebilir'))
        <li>
          <a href="{{ route('kasa.filter', $user->tenant_id)}}" class="waves-effect">
            <i class="ri-money-dollar-circle-fill"></i><span class="badge rounded-pill bg-success float-end"></span>
            <span>Kasa</span>
          </a>
        </li>
        @endif

        @if(auth()->user()->can('Firmaları Görebilir'))
         <li>
          <a href="{{ route('all.tenants',$user->tenant_id)}}" class="waves-effect">
            <i class="ri-account-circle-line"></i><span class="badge rounded-pill bg-success float-end"></span>
            <span>Firmalar</span>
          </a>
        </li>
        @endif

         {{-- <li>
           <a href="javascript: void(0);" class="has-arrow waves-effect">
             <i class="ri-settings-3-fill"></i>
             <span>Genel Ayarlar</span>
           </a>
           <ul class="sub-menu" aria-expanded="false">
             <li><a href="{{ route('site.settings')}}">Site Ayarları</a></li>
             <li><a href="{{ route('email.settings')}}">Email Ayarları</a></li>
             <li><a href="{{ route('google.settings')}}">Google Ayarları</a></li>
             <li><a href="{{ route('company.settings')}}">Firma Ayarları</a></li>
             <li><a href="{{ route('social.media.settings')}}">Sosyal Medya Ayarları</a></li>
           </ul>
         </li> --}}
       </ul>
     </div>
     <!-- Sidebar -->
   </div>
 </div>
 <!-- Left Sidebar End -->
 
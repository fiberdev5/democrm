@php 
$user = Auth::user();
@endphp

<header id="page-topbar">
    <div class="navbar-header">
      <div class="d-flex">
        <button type="button" class="btn btn-sm px-3 font-size-24 header-item" id="vertical-menu-btn">
          <i class="ri-menu-2-line align-middle"></i>
        </button>
        <div class="navbar-brand-box">
          <a href="{{route('secure.home', $user->tenant_id)}}" class="logo logo-light">
            <span class="logo-lg">
              @if(!empty($user->tenant->firma_adi))
              {{$user->tenant->firma_adi}}
              @else 
                Yönetim Paneli
              @endif
            </span>
          </a>
        </div>
      </div>
  
      <div class="d-flex">
  
        @php 
        $id=Auth::user()->user_id;
        $adminData = App\Models\User::find($id);
        @endphp
  
        <div class="dropdown d-inline-block user-dropdown">
          <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="d-none d-xl-inline-block ms-1"><i class="ri-user-line align-middle me-1"></i> {{ $adminData->name }}</span>
            <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end">
          <!-- item-->
            <a class="dropdown-item" href="{{ route('edit.profile')}}"><i class="ri-user-line align-middle me-1"></i>Profil Güncelle</a>
            <a class="dropdown-item" href="{{route('general.settings', $user->tenant_id)}}"><i class="ri-settings-3-fill align-middle me-1"></i>Genel Ayarlar</a>
            <div class="dropdown-divider"></div>
              <a class="dropdown-item text-danger" href="{{ route('logout') }}"><i class="ri-shut-down-line align-middle me-1 text-danger"></i> Çıkış yap</a>
            </div>
          </div>
          
       
                
        </div>
    </div>
  </header>
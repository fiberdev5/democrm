<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    @php
        $user = Auth::user();
    @endphp
    <title>{{ $user && $user->tenant && $user->tenant->firma_adi ? $user->tenant->firma_adi : 'Yönetim Paneli' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="shortcut icon" href="{{ asset('backend/assets/images/favicon.ico') }}">

    <link href="{{ asset('backend/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    {{-- Not: Eğer backend/assets içinde yerel kopyası varsa, CDN yerine onu kullanabilirsiniz:
         <link href="{{ asset('backend/assets/libs/datatables.net-buttons/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    --}}

    <link href="{{ asset('backend/assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">
    <link rel="stylesheet" href="{{asset('backend/assets/libs/select2/css/select2.min.css')}}" type="text/css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/bootstrap.tagsinput/0.8.0/bootstrap-tagsinput.css">

    <link href="{{ asset('backend/assets/css/custom.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('frontend/css/secure.css') }}" rel="stylesheet" type="text/css" />

    <script src="{{ asset('backend/assets/libs/jquery/jquery.min.js') }}"></script>
    
    <link href="{{asset('backend/assets/libs/dropzone/min/dropzone.min.css')}}" rel="stylesheet" type="text/css" />
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
</head>

<body data-topbar="dark">
    <div id="layout-wrapper">
        @include('frontend.secure.body.header')
        @include('frontend.secure.body.sidebar')

        <div class="main-content">
            @yield('user')
            @include('frontend.secure.body.footer')
        </div>
    </div>

    <script src="{{ asset('backend/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/metismenu/metisMenu.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/node-waves/waves.min.js') }}"></script>

    <script src="{{ asset('backend/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    {{-- Not: Eğer backend/assets içinde yerel kopyası varsa, CDN yerine onu kullanabilirsiniz:
         <script src="{{ asset('backend/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
         <script src="{{ asset('backend/assets/libs/datatables.net-buttons/js/buttons.bootstrap4.min.js') }}"></script>
         <script src="{{ asset('backend/assets/libs/jszip/jszip.min.js') }}"></script>
         <script src="{{ asset('backend/assets/libs/pdfmake/build/pdfmake.min.js') }}"></script>
         <script src="{{ asset('backend/assets/libs/pdfmake/build/vfs_fonts.js') }}"></script>
         <script src="{{ asset('backend/assets/libs/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
         <script src="{{ asset('backend/assets/libs/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
         <script src="{{ asset('backend/assets/libs/datatables.net-buttons/js/buttons.colVis.min.js') }}"></script>
    --}}

    <script src="https://cdn.datatables.net/colreorder/1.5.2/js/dataTables.colReorder.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>

    <script src="{{ asset('backend/assets/js/app.js') }}"></script>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        @if (Session::has('message'))
            var type = "{{ Session::get('alert-type', 'info') }}"
            switch (type) {
                case 'info':
                    toastr.info(" {{ Session::get('message') }} ");
                    break;
                case 'success':
                    toastr.success(" {{ Session::get('message') }} ");
                    break;
                case 'warning':
                    toastr.warning(" {{ Session::get('message') }} ");
                    break;
                case 'error':
                    toastr.error(" {{ Session::get('message') }} ");
                    break;
            }
        @endif
    </script>

    <script src="{{ asset('backend/assets/libs/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/pages/form-editor.init.js') }}"></script>
    <script src="{{asset('backend/assets/js/pages/form-validation.init.js')}}"></script>
    <script src="{{asset('backend/assets/libs/select2/js/select2.min.js')}}"></script>
    <script src="{{asset('backend/assets/js/pages/form-advanced.init.js')}}"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script src="{{ asset('backend/assets/js/code.js') }}"></script>

    <script src="{{asset('backend/assets/libs/dropzone/min/dropzone.min.js')}}"></script>

</body>
</html>
<!DOCTYPE html>
<html lang="tr">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Kaydı</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{asset('frontend/custom.css')}}" rel="stylesheet">
  
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- jQuery Mask Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    
    <style>
        .register-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        form {
            max-width: 400px;
            width: 100%;
        }

        img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }
        .sbmtButon{background-color: #f27c22;color: #fff;}
        .sbmtButon:hover{background-color: #f58733;color: #fff;}
        a{text-decoration: none;}
    </style>
  </head>
  <body>  
    <div class="container d-flex align-items-center justify-content-center register-container" style="min-height: 100vh;">
      <div class="row w-100">
        {{-- Sol tarafta form --}}
        <div class="col-md-6 d-flex align-items-center">
          <form class="w-100" method="POST" action="{{ route('kayit.action') }}">
            @csrf
            <h2 class="mb-4"><label style="color:#f27c22;">Serbis</label>'e Kayıt Olun.</h2>
            <div class="mb-3">
              <input type="text" name="name" id="name" class="form-control" placeholder="Ad Soyad" required>
              @error('name')
              <small class="text-danger">{{ $message }}</small>
              @enderror
          </div>
          
          <div class="mb-3">
              <input type="text" name="firma_adi" id="firma_adi" class="form-control" placeholder="Firma Adı" required>
              @error('firma_adi')
              <small class="text-danger">{{ $message }}</small>
              @enderror
          </div>
          
          <div class="mb-3">
              <input type="text" name="tel" id="tel" class="form-control tel" placeholder="Telefon" required>
              @error('tel')
              <small class="text-danger">{{ $message }}</small>
              @enderror
          </div>
          
          <div class="mb-3">
              <input type="email" name="email" id="email" class="form-control" placeholder="E-posta" required>
              @error('email')
              <small class="text-danger">{{ $message }}</small>
              @enderror
          </div>
          
          <div class="mb-3">
              <input type="password" name="password" id="password" class="form-control" placeholder="Şifre" required>
              <small class="">Şifre en az 6 karakter olmalıdır.</small>
              @error('password')
              <small class="text-danger">{{ $message }}</small>
              @enderror
          </div>
            <button type="submit" class="btn sbmtButon w-100">Kayıt Ol</button>
            <p class="mt-2">Zaten hesabın var mı? <a href="{{route('giris')}}" style="color: #f27c22;"> Giriş Yap </a> </p>
          </form>
        </div>

        {{-- Sağ tarafta resim --}}
        <div class="col-md-6 d-flex align-items-center justify-content-center">
          <img src="{{ asset('frontend/img/undraw_website_27ju.png') }}" alt="Kayıt Resmi" class="img-fluid" style="max-height: 400px;">
        </div>
      </div>
    </div>

    <script>
      $(document).ready(function () {
        $(".tel").mask("999 999 9999");
      });
    </script>
    <!-- Optional JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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
  </body>
</html>
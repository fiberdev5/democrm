<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{asset('frontend/custom.css')}}" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/enterprise.js" async defer></script>
    <style>
        body {
            background-color: #f0f0f0; /* Gri arka plan */
        }

        .container {
            max-width: 400px;
            margin-top: 50px;
        }

        .login-box {
            background-color: #ffffff; /* Beyaz kutu arka planı */
            padding: 30px;
            border-radius: 10px;
            border: 1px solid #ddd; /* Kutu çevresine ince bir çizgi */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Hafif gölge */
            text-align: center;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 50px; /* Form ile logo arasında boşluk */
        }

        .logo-container img {
            width: 150px; /* Logonun genişliği */
            height: auto; /* Oranlı yüksekliği */
        }

        h5 {
            font-size: 15px;
            color: #8f8383;
        }
        .sbmtButon{background-color: #f27c22;color: #fff;}
        .sbmtButon:hover{background-color: #f58733;color: #fff;}
        a{text-decoration: none;}

         .input-wrapper {
        display: flex;
        align-items: center;
        position: relative; /* Konumlandırma için gereklidir */
    }

    .info-icon {
        margin-left: 10px;
        cursor: pointer;
        position: relative;
        display: inline-block;
    }

    /* Bilgi balonu (tooltip) */
    .info-icon .tooltip-text {
        visibility: hidden; /* Başlangıçta gizli */
        width: 220px;
        background-color: #555;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 8px;
        position: absolute;
        z-index: 1;
        bottom: 125%; /* İkonun üstünde konumlandır */
        left: 50%;
        margin-left: -110px; /* Genişliğin yarısı kadar sola çekerek ortala */
        opacity: 0;
        transition: opacity 0.3s;
    }
    
    /* Balonun altındaki ok */
    .info-icon .tooltip-text::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #555 transparent transparent transparent;
    }

    /* İkonun üzerine gelindiğinde balonu görünür yap */
    .info-icon:hover .tooltip-text {
        visibility: visible;
        opacity: 1;
    }
    </style>
</head>
<body>
    <div class="container">
        <!-- Firma Logosu -->
        <div class="logo-container">
            <img src="{{ asset('frontend/img/serbis-logo.png') }}" alt="Firma Logosu">
        </div>

        <div class="login-box">
            <form method="POST" action="{{ route('giris.action') }}">
                @csrf
                <h5 class="mb-4">Serbis'e hoş geldiniz, kullanıcı adı ve parolanız ile güvenli giriş yapabilirsiniz.</h5>

                <!-- Başarılı mesaj -->
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Hata mesajları -->
                @error('email')
                    <div class="alert alert-danger">
                        {{ $message }}
                    </div>
                @enderror

                <div class="mb-3 input-wrapper">
                    <input type="email" name="email" id="email" class="form-control" placeholder="kullaniciadi@firmaadi.com" required>
                    
                    <div class="info-icon">
                        <!-- SVG Info İkonu -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                        </svg>
                        <span class="tooltip-text">Lütfen kurumsal e-posta adresinizi giriniz. Bu adres, kullaniciadiniz@firmaadi.com şeklinde olmalıdır.</span>
                    </div>
                </div>

                <div class="mb-3">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Şifre" required>
                </div>

                <div class="form-group mb-3 row" class="g-recaptcha" data-sitekey="6Ldl86UrAAAAAIo9asM85k5ajB363yYtf8FuKQgu" data-action="LOGIN">
                                {!! htmlFormSnippet() !!}
                    @if($errors->has('g-recaptcha-response'))
                        <div>
                            <small class="text-danger">
                                {{$errors->first('g-recaptcha-response')}}
                            </small>
                        </div>
                    @endif
                </div>

                <button type="submit" class="btn sbmtButon w-100">Giriş Yap</button>
                <p class="mt-3">Hesabınız yok mu? <a href="{{ route('kayit') }}" style="color: #f27c22;">Kayıt Ol</a></p>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
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

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
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-question-circle-fill" viewBox="0 0 16 16">
  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.496 6.033h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286a.237.237 0 0 0 .241.247zm2.325 6.443c.61 0 1.029-.394 1.029-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94 0 .533.425.927 1.01.927z"/>
</svg>
                        <span class="tooltip-text">Lütfen kurumsal e-posta adresinizi giriniz. Bu adres, bildirimler için kullanılacaktır.</span>
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

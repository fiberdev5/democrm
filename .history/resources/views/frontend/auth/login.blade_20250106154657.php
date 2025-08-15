<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{asset('frontend/custom.css')}}" rel="stylesheet">
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

                <div class="mb-3">
                    <input type="email" name="email" id="email" class="form-control" placeholder="E-posta" required>
                </div>

                <div class="mb-3">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Şifre" required>
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

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Renk Şeması Değişiklikleri</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f8f9fa;
            line-height: 1.6;
        }
        
        .section {
            background: white;
            margin: 20px 0;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .color-box {
            display: inline-block;
            width: 30px;
            height: 30px;
            border-radius: 5px;
            vertical-align: middle;
            margin-right: 10px;
            border: 2px solid #ddd;
        }
        
        .color1 { background-color: #3e546a; }
        .color2 { background-color: #f27c22; }
        
        .code-block {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            margin: 10px 0;
            overflow-x: auto;
            border-left: 4px solid #007bff;
        }
        
        .old-code {
            background: #fff3cd;
            border-left-color: #856404;
        }
        
        .new-code {
            background: #d1ecf1;
            border-left-color: #0c5460;
        }
        
        .preview {
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            text-align: center;
        }
        
        .preview-logo {
            background: #3e546a;
            color: white;
            padding: 15px;
            border-radius: 10px;
        }
        
        .preview-button {
            background: #f27c22;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
        }
        
        .preview-step {
            display: inline-block;
            width: 30px;
            height: 30px;
            background: #3e546a;
            color: white;
            border-radius: 50%;
            line-height: 30px;
            margin: 0 5px;
        }
        
        h1 { color: #2c3e50; }
        h2 { color: #34495e; }
        h3 { color: #3e546a; }
    </style>
</head>
<body>
    <h1>İki Rengin Farklı Kullanımı - Değişiklik Rehberi</h1>
    
    <div class="section">
        <h2>Renk Planınız</h2>
        <p><span class="color-box color1"></span><strong>#3e546a (Koyu Mavi-Gri)</strong> → Logo bölümü, step göstergeleri, vurgular için</p>
        <p><span class="color-box color2"></span><strong>#f27c22 (Turuncu)</strong> → Butonlar, linkler, hover efektleri için</p>
    </div>

    <div class="section">
        <h2>1. Logo Bölümü Değişikliği</h2>
        <p><strong>Mevcut:</strong> Turuncu gradient</p>
        <p><strong>Yeni:</strong> Koyu mavi-gri</p>
        
        <div class="preview">
            <div class="preview-logo">
                <h3 style="margin: 5px 0;">Serbis</h3>
                <p style="margin: 5px 0; font-size: 14px;">Teknik Servis Yönetim Sistemi</p>
            </div>
        </div>
        
        <h4>Eski Kod:</h4>
        <div class="code-block old-code">
.logo-section {
    /* background: linear-gradient(135deg, #f27c22 0%, #f58733 100%); */
    background-image: linear-gradient(to right, #3e546a, #3e546a);
}
        </div>
        
        <h4>Yeni Kod:</h4>
        <div class="code-block new-code">
.logo-section {
    background: #3e546a; /* Tek renk */
    /* VEYA gradient istiyorsanız: */
    /* background: linear-gradient(135deg, #3e546a 0%, #4a6280 100%); */
}
        </div>
    </div>

    <div class="section">
        <h2>2. Step Göstergeleri Değişikliği</h2>
        <p><strong>Mevcut:</strong> Turuncu</p>
        <p><strong>Yeni:</strong> Koyu mavi-gri</p>
        
        <div class="preview">
            <span class="preview-step">1</span>
            <span class="preview-step">2</span>
            <span class="preview-step">3</span>
        </div>
        
        <h4>Eski Kod:</h4>
        <div class="code-block old-code">
.step.active .step-icon {
    background: linear-gradient(135deg, #f27c22 0%, #f58733 100%);
    color: white;
    transform: scale(1.1);
}

.step.active .step-label {
    color: #f27c22;
    font-weight: 700;
}
        </div>
        
        <h4>Yeni Kod:</h4>
        <div class="code-block new-code">
.step.active .step-icon {
    background: linear-gradient(135deg, #3e546a 0%, #4a6280 100%);
    color: white;
    transform: scale(1.1);
}

.step.active .step-label {
    color: #3e546a;
    font-weight: 700;
}
        </div>
    </div>

    <div class="section">
        <h2>3. Butonlar (Turuncu Kalacak)</h2>
        <p>Ana butonlar turuncu kalacak (değişiklik yok)</p>
        
        <div class="preview">
            <button class="preview-button">İleri →</button>
        </div>
        
        <div class="code-block">
.btn-primary {
    background: linear-gradient(135deg, #f27c22 0%, #f58733 100%); /* Kalacak */
}
        </div>
    </div>

    <div class="section">
        <h2>4. Diğer Turuncu Vurgular → Koyu Mavi-Gri</h2>
        
        <h4>Form Focus Rengi:</h4>
        <div class="code-block old-code">
.form-control:focus {
    border-color: #f27c22;
    box-shadow: 0 0 0 0.2rem rgba(242, 124, 34, 0.25);
}
        </div>
        
        <div class="code-block new-code">
.form-control:focus {
    border-color: #3e546a;
    box-shadow: 0 0 0 0.2rem rgba(62, 84, 106, 0.25);
}
        </div>

        <h4>Info Icon Rengi:</h4>
        <div class="code-block old-code">
.info-icon {
    color: #f27c22;
}
        </div>
        
        <div class="code-block new-code">
.info-icon {
    color: #3e546a;
}
        </div>

        <h4>Link Renkleri:</h4>
        <div class="code-block old-code">
#switchToRegister, #switchToLogin {
    color: #f27c22;
}
        </div>
        
        <div class="code-block new-code">
#switchToRegister, #switchToLogin {
    color: #3e546a;
}
        </div>

        <h4>SMS Info Başlık:</h4>
        <div class="code-block old-code">
.sms-info h5 {
    color: #f27c22;
}
        </div>
        
        <div class="code-block new-code">
.sms-info h5 {
    color: #3e546a;
}
        </div>

        <h4>Countdown Timer Border:</h4>
        <div class="code-block old-code">
.countdown-timer {
    border-left: 4px solid #f27c22;
}
        </div>
        
        <div class="code-block new-code">
.countdown-timer {
    border-left: 4px solid #3e546a;
}
        </div>
    </div>

    <div class="section">
        <h2>5. Toggle Button Border (İsteğe Bağlı)</h2>
        <p>Form toggle butonlarının kenar çizgisi</p>
        
        <div class="code-block old-code">
.toggle-btn {
    border: 1px solid #f27c22;
}
        </div>
        
        <div class="code-block new-code">
.toggle-btn {
    border: 1px solid #3e546a;
}
        </div>
    </div>

    <div class="section">
        <h2>Tam CSS Değişiklikleri (Kopyala-Yapıştır)</h2>
        <p>Aşağıdaki CSS kodlarını mevcut style bölümünüzde bulup değiştirin:</p>
        
        <div class="code-block new-code">
/* 1. Logo bölümü */
.logo-section {
    text-align: center;
    padding: 10px 0 10px;
    color: white;
    background: #3e546a; /* Değişti */
}

/* 2. Step göstergeleri */
.step.active .step-icon {
    background: linear-gradient(135deg, #3e546a 0%, #4a6280 100%); /* Değişti */
    color: white;
    transform: scale(1.1);
}

.step.active .step-label {
    color: #3e546a; /* Değişti */
    font-weight: 700;
}

/* 3. Form focus */
.form-control:focus {
    border-color: #3e546a; /* Değişti */
    box-shadow: 0 0 0 0.2rem rgba(62, 84, 106, 0.25); /* Değişti */
}

/* 4. Info icon */
.info-icon {
    color: #3e546a; /* Değişti */
}

/* 5. Link renkleri */
#switchToRegister, #switchToLogin {
    color: #3e546a; /* Değişti */
}

/* 6. SMS info başlık */
.sms-info h5 {
    color: #3e546a; /* Değişti */
}

/* 7. Countdown timer */
.countdown-timer {
    border-left: 4px solid #3e546a; /* Değişti */
}

/* 8. Toggle button (isteğe bağlı) */
.toggle-btn {
    border: 1px solid #3e546a; /* Değişti */
}
        </div>
    </div>

    <div class="section">
        <h2>Sonuç</h2>
        <p><strong>Koyu Mavi-Gri (#3e546a):</strong> Logo, step göstergeleri, linkler, form focus, bilgi ikonları</p>
        <p><strong>Turuncu (#f27c22):</strong> Ana butonlar, toggle slider, hover efektleri</p>
        <p>Bu şekilde iki renginiz farklı bölümlerde kullanılmış olacak ve daha dengeli bir tasarım elde edeceksiniz.</p>
    </div>
</body>
</html>
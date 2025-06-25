<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Ödeme Düzenle</h5>
                </div>
                <div class="card-body">
                    <form method="POST" id="servisOdemeDuzenle">
                        @csrf
                        
                        @hasanyrole('Patron|Admin')
                            <div class="row form-group mb-3">
                                <div class="col-lg-5"><label>Tarih <span class="text-danger">*</span></label></div>
                                <div class="col-lg-7">
                                    <input type="date" name="tarih" class="form-control datepicker" readonly 
                                           value="{{ \Carbon\Carbon::parse($servisPara->created_at)->format('d/m/Y H') }}" 
                                           style="width: 105px; display: inline-block; background:#fff">
                                </div>
                            </div>
                            
                            <div class="row form-group mb-3">
                                <div class="col-lg-5"><label>Personel <span class="text-danger">*</span></label></div>
                                <div class="col-lg-7">
                                    <select class="form-control personeller" name="personeller">
                                        @foreach ($personeller as $personel)
                                            <option value="{{ $personel->id }}" 
                                                {{ $servisPara->pid == $personel->user_id ? 'selected' : '' }}>
                                                {{ $personel->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endhasanyrole

                        <div class="row form-group mb-3">
                            <div class="col-lg-5"><label>Ödeme Yönü</label></div>
                            <div class="col-lg-7">
                                <select class="form-control odemeYonu" name="odemeYonu">
                                    <option value="1" {{ $servisPara->odemeYonu == "1" ? 'selected' : '' }}>Gelir Ekle</option>
                                    <option value="2" {{ $servisPara->odemeYonu == "2" ? 'selected' : '' }}>Gider Ekle</option>
                                </select>
                            </div>
                        </div>

                        <div class="row form-group mb-3">
                            <div class="col-lg-5"><label>Ödeme Şekli <span class="text-danger">*</span></label></div>
                            <div class="col-lg-7">
                                <select class="form-control odemeSekli" name="odemeSekli" required>
                                    @foreach ($odemeSekli as $sekli)
                                        <option value="{{ $sekli->id }}" 
                                            {{ $servisPara->odemeSekli == $sekli->id ? 'selected' : '' }}>
                                            {{ $sekli->odemeSekli }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row form-group mb-3">
                            <div class="col-lg-5"><label>Ödeme Durumu <span class="text-danger">*</span></label></div>
                            <div class="col-lg-7">
                                <select class="form-control odemeDurum" name="odemeDurum" required>
                                    <option value="1" {{ $servisPara->odemeDurum == "1" ? 'selected' : '' }}>Tamamlandı</option>
                                    <option value="2" {{ $servisPara->odemeDurum == "2" ? 'selected' : '' }}>Beklemede</option>
                                </select>
                            </div>
                        </div>

                        <div class="row form-group mb-3">
                            <div class="col-lg-5"><label>Fiyat <span class="text-danger">*</span></label></div>
                            <div class="col-lg-7">
                                <input type="text" name="fiyat" class="form-control fiyat" 
                                       autocomplete="off" value="{{ $servisPara->fiyat }}" required>
                            </div>
                        </div>

                        <div class="row form-group mb-3">
                            <div class="col-lg-5"><label>Açıklama</label></div>
                            <div class="col-lg-7">
                                <input type="text" name="aciklama" class="form-control aciklama" 
                                       autocomplete="off" value="{{ $servisPara->aciklama }}">
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <button type="submit" class="btn btn-primary btn-sm">Güncelle</button>
                            <a href="" class="btn btn-secondary btn-sm">İptal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Datepicker initialization
    $('.datepicker').datepicker({
        language: 'tr',
        autoclose: true,
        format: 'dd/mm/yyyy'
    });
    
    // Sayı kontrolü
    function sayiKontrol(input) {
        var value = input.value;
        // Sadece rakam, nokta ve virgül kabul et
        var isNum = /^[0-9.,]*$/;
        if (!isNum.test(value)) {
            input.value = value.replace(/[^0-9.,]/g, "");
        }
    }
    
    // Fiyat inputu için sayı kontrolü
    $('.fiyat').on('keyup', function() {
        sayiKontrol(this);
    });
    
    // Ödeme yönü değişikliği
    $('.odemeYonu').on('change', function() {
        var val = $(this).val();
        if (val == "2") {
            $(".odemeDurum").val(1);
        }
    });

    $('.odemeDurum').on('change', function() {
        var val = $(".odemeYonu").val();
        if (val == "2") {
            $(".odemeDurum").val(1);
        }
    });
    
    // Form submit
    $("#servisOdemeDuzenle").on('submit', function(e) {
        e.preventDefault();
        
        var odemeSekli = $.trim($(".odemeSekli").val());
        var odemeDurum = $.trim($(".odemeDurum").val());
        var fiyat = $.trim($(".fiyat").val());

        if (odemeSekli.length === 0) {
            alert("Ödeme şekli boş geçilemez");
            $(".odemeSekli").focus();
            return false;
        } else if (odemeDurum.length === 0) {
            alert("Ödeme durumu boş geçilemez");
            $(".odemeDurum").focus();
            return false;
        } else if (fiyat.length === 0) {
            alert("Fiyat alanı boş geçilemez");
            $(".fiyat").focus();
            return false;
        }
        
        // Loading state
        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.html();
        submitBtn.html('Güncelleniyor...').prop('disabled', true);
        
        $.ajax({
            url: "",
            type: "POST",
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    // Sayfayı yenile veya yönlendir
                    if (typeof servislerTable !== "undefined" && servislerTable !== null) {
                        servislerTable.draw();
                    }
                    if (typeof raporlarTable !== "undefined" && raporlarTable !== null) {
                        raporlarTable.draw();
                    }
                    // Modal kapatma veya yönlendirme
                    window.location.href = "";
                } else {
                    alert(response.message || 'Bir hata oluştu');
                }
            },
            error: function(xhr) {
                var errorMessage = 'Bir hata oluştu';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.status === 422 && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                }
                alert(errorMessage);
            },
            complete: function() {
                // Loading state'i kaldır
                submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });
});
</script>
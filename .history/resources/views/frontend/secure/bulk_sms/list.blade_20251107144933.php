<div class="card">
    <div class="card-header cardBaslik" style="padding: 5px 10px;font-size: 14px">
        {{ $durumBaslik }}
        <span class="servisSaySonuc">({{ $servisler->count() }})</span>
    </div>

    <div class="card-body" style="padding: 0" id="smsCard">
        <table class="table table-hover table-striped" id="dataTable2" width="100%" cellspacing="0" style="margin: 0 !important">
            <thead class="title">
                <tr>
                    <th style="padding: 5px 10px;font-size: 12px;">
                        <input type="checkbox" id="checkAll" style="width: 15px; height: 15px;">
                    </th>
                    <th style="padding: 5px 10px;font-size: 12px;">ID</th>
                    <th style="padding: 5px 10px;font-size: 12px;">Müşteri Adı</th>
                    <th style="padding: 5px 10px;font-size: 12px;">İlçe</th>
                    <th style="padding: 5px 10px;font-size: 12px;">Telefon</th>
                    <th style="padding: 5px 10px;font-size: 12px;">Cihaz</th>
                </tr>
            </thead>
            <tbody>
                @forelse($servisler as $servis)
                <tr>
                    <td style="vertical-align: middle;font-size: 11px; padding: 3px 10px;">
                        <label class="servisCheckListWrap">
                            <input type="checkbox" name="servisCheckList" value="{{ $servis->id }}" 
                                   style="width: 15px;height: 15px;top: 6px;">
                        </label>
                    </td>
                    <td style="vertical-align: middle;font-size: 11px; padding: 3px 10px;cursor:pointer;" 
                        class="personelServisDuzenle" data-id="{{ $servis->id }}" data-name="{{ $servis->musteri->adSoyad }}">
                        <strong>{{ $servis->id }}</strong>
                    </td>
                    <td style="vertical-align: middle;width: 120px;font-size: 11px; padding: 3px 10px;cursor:pointer;" 
                        class="personelServisDuzenle" data-id="{{ $servis->id }}" data-name="{{ $servis->musteri->adSoyad }}">
                        <strong>{{ $servis->musteri->adSoyad }}</strong>
                    </td>
                    <td style="vertical-align: middle;font-size: 11px; padding: 3px 10px;cursor:pointer;" 
                        class="personelServisDuzenle" data-id="{{ $servis->id }}" data-name="{{ $servis->musteri->adSoyad }}">
                        <strong>{{ $servis->musteri->ilce }}</strong>
                    </td>
                    <td style="vertical-align: middle;width: 120px;font-size: 11px; padding: 3px 10px;cursor:pointer;" 
                        class="personelServisDuzenle" data-id="{{ $servis->id }}" data-name="{{ $servis->musteri->adSoyad }}">
                        <strong>{{ $servis->musteri->tel1 }}</strong>
                    </td>
                    <td style="vertical-align: middle;font-size: 11px; padding: 3px 10px;cursor:pointer;" 
                        class="personelServisDuzenle" data-id="{{ $servis->id }}" data-name="{{ $servis->musteri->adSoyad }}">
                        <strong>{{ $servis->markaCihaz->marka }}, {{ $servis->turCihaz->cihaz }}</strong>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-danger font-weight-bold">Kayıt bulunamadı</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer" style="padding: 5px 10px;font-size: 14px">
        <label style="margin-bottom: 4px">Mesajınız (Max. 120 karakter olmalıdır.)</label>
        <textarea id="mesaj" name="mesaj" class="form-control" rows="3" 
                  style="resize: none;position: relative;" placeholder="Mesajınız"></textarea>
        <span class="mesajSayaci">0/120</span>
        <button class="btn btn-success btn-block btn-sm mesajGonder">Mesaj Gönder</button>
    </div>
</div>

<script>
$(document).ready(function(){
    // Tümünü seç checkbox
    $("#checkAll").click(function () {
        $('#dataTable2 input:checkbox').not(this).prop('checked', this.checked);
    });

    // Mesaj sayacı
    $("#mesaj").on("keyup", function(){
        var yazi = $("#mesaj").val();
        if(yazi.length > 120){
            $(".mesajSayaci").css("color", "red");
            $(".mesajSayaci").html(yazi.length + "/120");
        } else {
            $(".mesajSayaci").css("color", "black");
            $(".mesajSayaci").html(yazi.length + "/120");
        }
    });

    // Checkbox değiştiğinde modal'ı kapat
    $('input[name="servisCheckList"]').click(function () {
        $('#personelServisDuzenleModal').modal('hide');
        $('#servisPersonelAtamaModal .modal-body').html("");
    });

    // Servis düzenle modal
    $(".personelServisDuzenle").click(function(){
        var id = $(this).attr('data-id');
        var name = $(this).attr('data-name').toUpperCase();
        var firma_id = {{$firma->id}};
        $.ajax({
            url: "/" + firma_id + "/servis-duzenle/" + id,
            success: function(data) {
                $('#personelServisDuzenleModal').modal('show');
                $('#personelServisDuzenleModal .modal-title').html(name + " (" + id + ")");
                $('#personelServisDuzenleModal .modal-body').html(data);
            },
            error: function(){
                alert('Bir hata oluştu');
            }
        });
    });

    // Mesaj gönder
    $(".mesajGonder").click(function(){
        var servisidler = "";
        $('input[name="servisCheckList"]:checked').each(function() {
            if(!servisidler){
                servisidler = this.value;
            } else {
                servisidler = servisidler + ", " + this.value;
            }
        });

        if(!servisidler){
            alert("Mesaj gönderebilmek için, en az 1 tane müşteri seçmeniz gerekmektedir.");
            return false;
        }

        var mesaj = $("#mesaj").val();
        
        if(!mesaj || mesaj.trim() === ''){
            alert("Lütfen mesaj giriniz.");
            return false;
        }

        if(mesaj.length > 120){
            alert("Mesaj en fazla 120 karakter olabilir.");
            return false;
        }

        if(!confirm('Seçili müşterilere SMS göndermek istediğinize emin misiniz?')){
            return false;
        }

        $.ajax({
            url: "{{ route('toplu-sms.gonder', $firma->id) }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                servisidler: servisidler,
                mesaj: mesaj
            },
            beforeSend: function(){
                $(".mesajGonder").prop('disabled', true).text('Gönderiliyor...');
            },
            success: function(response) {
                if(response.success){
                    alert(response.message);
                    location.reload();
                } else {
                    alert(response.message || 'Bir hata oluştu');
                    $(".mesajGonder").prop('disabled', false).text('Mesaj Gönder');
                }
            },
            error: function(xhr){
                var message = 'Bir hata oluştu';
                if(xhr.responseJSON && xhr.responseJSON.message){
                    message = xhr.responseJSON.message;
                }
                alert(message);
                $(".mesajGonder").prop('disabled', false).text('Mesaj Gönder');
            }
        });
    });
});
</script>
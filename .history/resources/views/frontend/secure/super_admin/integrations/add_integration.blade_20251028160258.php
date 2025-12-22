<style>
  
@media (min-width: 767px) {
.custom-p-r{
    padding-right: 0px !important;
  }
  .custom-p-l{
    padding-left: 0px !important;
  }

}
</style>
<form method="post" id="addIntegration" action="{{ route('super.admin.integration.store')}}" enctype="multipart/form-data">
                        @csrf   
                        
                        <div class="row">
                            <label class="col-sm-3 custom-p-r">Entegrasyon Adı<span style="font-weight: bold; color: red;">*</span></label>
                            <div class="col-sm-9 custom-p-l">
                                <input name="name" class="form-control buyukYaz" type="text" placeholder="Entegrasyon adını giriniz" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <label class="col-sm-3 custom-p-r">Kategori<span style="font-weight: bold; color: red;">*</span></label>
                            <div class="col-sm-9 custom-p-l">
                                <select name="category" class="form-select" required>
                                    <option value="" selected disabled>-Seçiniz-</option>
                                    <option value="payment">Ödeme</option>
                                    <option value="email">E-posta</option>
                                    <option value="sms">SMS</option>
                                    <option value="crm">CRM</option>
                                    <option value="accounting">Muhasebe</option>
                                    <option value="storage">Depolama</option>
                                    <option value="other">Diğer</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <label class="col-sm-3 custom-p-r">Fiyat (₺)</label>
                            <div class="col-sm-9 custom-p-l">
                                <input name="price" class="form-control" type="number" step="0.01" min="0" placeholder="0.00">
                                <small class="text-muted">Ücretsiz ise boş bırakabilirsiniz</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <label class="col-sm-3 custom-p-r">Logo</label>
                            <div class="col-sm-9 custom-p-l">
                                <input name="logo" class="form-control" type="file" accept="image/*">
                            </div>
                        </div>
                        
                        <div class="row">
                            <label class="col-sm-3 custom-p-r">Kısa Açıklama</label>
                            <div class="col-sm-9 custom-p-l">
                                <textarea name="description" type="text" class="form-control" rows="3" placeholder="Entegrasyon hakkında kısa açıklama yazınız..."></textarea>
                            </div>
                        </div>
                        
                        <div class="row">
                            <label class="col-sm-3 custom-p-r">Detaylı Açıklama</label>
                            <div class="col-sm-9 custom-p-l">
                                <textarea name="explanation" type="text" class="form-control" rows="5" placeholder="Entegrasyon hakkında detaylı açıklama yazınız..."></textarea>
                            </div>
                        </div>
                        
                        <div class="row">
                            <label class="col-sm-3 custom-p-r">Durum</label>
                            <div class="col-sm-9 custom-p-l">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked>
                                    <label class="form-check-label" for="is_active">Entegrasyon Aktif</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">               
                            <div class="col-sm-12 gonderBtn">
                                <input type="submit" class="btn btn-sm btn-info waves-effect waves-light" value="Kaydet">
                                <a href="{{route('super.admin.integrations')}}" class="btn btn-sm btn-light waves-effect">İptal</a>
                            </div>
                        </div>
                    </form>
  
<script>
  $('.buyukYaz').keyup(function(){
    this.value = this.value.toUpperCase();
  });


  $(document).ready(function () {
    $(".phone").mask("999 999 9999");
  });

  $(document).ready(function(){
    $("#tcKimlik").mask("99999999999");
  });

  $(document).ready(function(){
    $("#vergiNo").mask("9999999999");
  });

  $('#addCust #vergiBox').hide();

  $(document).ready(function (e) {
    $('#addCust .musteriTipi').on('change', function () {
      var val = $(this).val();
      if (val == 2) {
        $("#addCust .musteriAdiSpan").text("Firma Adı");
        $('#addCust #vergiBox').show();
        $('#addCust #tcNo').hide();
      } else {
        $("#addCust .musteriAdiSpan").text("Müşteri Adı");
        $('#addCust #vergiBox').hide();
        $('#addCust #tcNo').show();
      }
    });
  });
</script>
  
<script>
  $(document).ready(function () {
    $('#addCust').submit(function (event) {
      var formIsValid = true;
      $(this).find('input, select').each(function () {
        var isRequired = $(this).prop('required');
        var isEmpty = !$(this).val();
        if (isRequired && isEmpty) {
          formIsValid = false;
          return false;
        }
      });
  
      if (!formIsValid) {
        event.preventDefault();
        alert('Lütfen zorunlu alanları doldurun.');
        return false;
      }
    });
  });
</script>
  
<script>
  $(document).ready(function() {
    // Ülke seçildiğinde şehirleri getir
    $("#country").change(function() {
      var selectedCountryId = $(this).val();
      if (selectedCountryId) {
        loadCities(selectedCountryId);
      }
    });
    // Şehirleri yüklemek için kullanılan fonksiyon
    function loadCities(countryId) {
      var citySelect = $("#city");
      citySelect.empty(); // Önceki seçenekleri temizle
      citySelect.append(new Option("Yükleniyor...", "")); // Kullanıcıya yükleniyor bilgisi ver
  
      // AJAX isteğiyle şehirleri al
      $.get("/get-states/" + countryId, function(data) {
        citySelect.empty(); // Yükleniyor mesajını temizle
        citySelect.append(new Option("-Seçiniz-", "")); // İlk boş seçeneği ekle
        $.each(data, function(index, city) {
          citySelect.append(new Option(city.ilceName, city.id));
        });
      }).fail(function() {
        citySelect.empty(); // Hata durumunda temizle
        citySelect.append(new Option("Unable to load cities", ""));
      });
    }
  });
</script>
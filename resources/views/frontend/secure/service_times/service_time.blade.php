<div class="row mt-3 mb-2">
  <div class="col-12">
    <div class=" d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0" style="font-size: 13px;">Servis Görüntüleme Zamanı</h4>
    </div>
  </div>
</div>
    
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <form id="serviceTime" method="post" action="{{ route('update.service.time',$firma->id)}}" enctype="multipart/form-data" class="needs-validation" novalidate style="width: 50%;">
          @csrf
          <input type="hidden" name="id" value="{{ $service_time ? $service_time->id : '' }}">

          <div class="row mb-3">
            <label for="hour" class="col-sm-4">Saat:</label>
            <div class="col-sm-8">
              <select id="hour" name="hour" class="form-select" required>
                <option value="">Saat</option>
                @for($i = 0; $i < 24; $i++)
                  <option value="{{ sprintf('%02d', $i) }}"   @if($service_time && explode(':', $service_time->zaman)[0] == sprintf('%02d', $i)) selected @endif>{{ sprintf('%02d', $i) }}</option>
                @endfor
              </select>
            </div>
          </div>
          <div class="row mb-3">
            <label for="hour" class="col-sm-4">Dakika:</label>
            <div class="col-sm-8">
              <select id="minute" name="minute" class="form-select" required>
                <option value="">Dakika</option>
                @for($i = 0; $i < 60; $i += 1)
                  <option value="{{ sprintf('%02d', $i) }}"  @if($service_time && explode(':', $service_time->zaman)[1] == sprintf('%02d', $i)) selected @endif>{{ sprintf('%02d', $i) }}</option>
                @endfor
              </select>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label"></label>
            <div class="col-sm-8">
              <input type="submit" class="btn btn-info waves-effect waves-light" value="Kaydet">
            </div>
          </div>
        </form>
      </div>
    </div>
  </div> <!-- end col -->
</div>
  

<script>
  $(document).ready(function () {
    $('#serviceTime').on('submit', function (e) {
      e.preventDefault(); // Sayfanın yeniden yüklenmesini engelle

      let formData = $(this).serialize(); // Form verilerini al

      $.ajax({
        url: $(this).attr('action'), // Formun action değerini al
        type: 'POST',
        data: formData,
        success: function (response) {
          // İşlem başarılı olduğunda yapılacaklar
          alert("Servis görüntüleme zamanı başarıyla güncellendi.");
        },
        error: function (xhr, status, error) {
          // İşlem hatalı olduğunda yapılacaklar
          console.error("Hata:", xhr.responseText);
          alert("Bir hata oluştu. Lütfen tekrar deneyin.");
        }
      });
    });
  });
</script>
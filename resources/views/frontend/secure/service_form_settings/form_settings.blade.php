<div class="row pageDetail">
  <div class="col-12">
    <div class="card">
      <div class="card-header sayfaBaslik" style="font-size:13px;">
        Servis Form Ayarları
      </div>
      <div class="card-body">
        <form id="serviceFormSettings" method="post" action="{{ route('update.service.form.settings',$firma->id)}}" enctype="multipart/form-data" class="needs-validation" novalidate style="width: 50%;">
          @csrf
          <input type="hidden" name="id" value="{{ $ayar->id ?? '' }}">

          <p style="display: block;font-size: 14px;font-weight: 500">Servis formu sonunda çıkan alt bilgilendirme mesajlarını bu alana girebilirsiniz. [TEL] firma bilgilerindeki telefon numarasını ifade eder.</p>
  
          <div class="row mb-3">
            <div class="col-sm-12">
              <textarea class="form-control" name="mesaj" type="text" rows="10"  required>{{$ayar->mesaj ?? ''}}</textarea>
            </div>
          </div>
          <!-- end row -->

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
    $('#serviceFormSettings').submit(function (event) {
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
  $(document).ready(function(){
    $('#serviceFormSettings').submit(function(e){
      e.preventDefault();
      if (this.checkValidity() === false) {
        e.stopPropagation();
      } else {
        var formData = $(this).serialize();
        $.ajax({
          url: $(this).attr('action'),
          type: 'POST',
          data: formData,
          success: function(response) {
            alert("Servis form bilgileri güncellendi");
          },
          error: function(xhr, status, error) {
            console.error(xhr.responseText);
          }
        });
      }
    });
  });
</script>
  
    
    
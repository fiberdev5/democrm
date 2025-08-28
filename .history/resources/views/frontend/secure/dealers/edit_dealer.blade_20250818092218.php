<form method="post" id="editBayi" action="{{ route('update.dealer', [$firma->id, $bayi->user_id]) }}" enctype="multipart/form-data">
  @csrf

  <div class="row">
    <label class="col-sm-4">Başlama Tarihi<span style="font-weight: bold; color: red;">*</span></label>
    <div class="col-sm-8">
      <input name="baslamaTarihi" class="form-control datepicker" type="date" value="{{ $bayi->baslamaTarihi }}" required>
    </div>
  </div>

  <div class="row">
      <label class="col-sm-4">Bayi Durumu:<span style="font-weight: bold; color: red;">*</span></label>
      <div class="col-sm-8">
        <select name="status" class="form-select durum" required>
          <option value="1" {{ $bayi->status == "1" ? 'selected' : ''}}>Çalışıyor</option>
          <option value="0" {{ $bayi->status == "0" ? 'selected' : ''}}>Ayrıldı</option>
        </select>
      </div>
  </div> <!--end row-->
  
  <div class="row ayrilmaTarihi">
      <label class="col-sm-4">Ayrılma Tarihi:<span style="font-weight: bold; color: red;">*</span></label>
      <div class="col-sm-8">
          <input name="ayrilmaTarihi" class="form-control datepicker ayrilmaTarihi" type="date" value="{{$bayi->ayrilmaTarihi}}" style="border: 1px solid #ced4da;">
      </div>
  </div>

  <div class="row">
    <label class="col-sm-4">Ad Soyad<span style="font-weight: bold; color: red;">*</span></label>
    <div class="col-sm-8">
      <input name="name" class="form-control" type="text" value="{{ $bayi->name }}" required>
    </div>
  </div>

  <div class="row">
    <label class="col-sm-4">Vergi No<span style="font-weight: bold; color: red;">*</span></label>
    <div class="col-sm-8">
      <input name="vergiNo" class="form-control" type="text" value="{{ $bayi->vergiNo }}" required>
    </div>
  </div>

  <div class="row">
    <label class="col-sm-4">Vergi Dairesi<span style="font-weight: bold; color: red;">*</span></label>
    <div class="col-sm-8">
      <input name="vergiDairesi" class="form-control" type="text" value="{{ $bayi->vergiDairesi }}" required>
    </div>
  </div>

  
 <div class="row">
    <label class="col-sm-4">Bayi Belgesi</label>
    <div class="col-sm-8">
      @if($bayi->belgePdf)
        @php
          $belgeler = json_decode($bayi->belgePdf, true) ?: [$bayi->belgePdf];
        @endphp
        
        <div class="row">
        <label class="col-sm-4">Mevcut Belgeler</label>
        <div class="col-sm-8" id="currentDocuments">
            <!-- Burada mevcut belgeler listelenecek -->
            <div class="document-item" data-index="0">
                <div class="document-actions">
                    <i class="fas fa-file-pdf text-danger"></i>
                    <span>PDF Belge 1</span>
                    <button type="button" class="delete-document" onclick="deleteDocument(0)">
                        <i class="fas fa-trash"></i> Sil
                    </button>
                    <a href="#" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye"></i> Görüntüle
                    </a>
                </div>
            </div>
            
            <div class="document-item" data-index="1">
                <div class="document-actions">
                    <i class="fas fa-image text-success"></i>
                    <span>Resim Belge 2</span>
                    <button type="button" class="delete-document" onclick="deleteDocument(1)">
                        <i class="fas fa-trash"></i> Sil
                    </button>
                    <a href="#" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye"></i> Görüntüle
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Yeni belge yükleme -->
    <div class="row">
        <label class="col-sm-4">Yeni Belge Ekle</label>
        <div class="col-sm-8">
            <input name="belgePdf[]" id="belgePdfEdit" class="form-control" type="file" accept=".pdf,.jpg,.jpeg,.png,.svg" multiple>
            <small class="text-muted" id="fileCountInfo">Şu anda 2 belgeniz var. En fazla 2 belge olabilir.</small>
        </div>
    </div>

    <!-- Silinecek belgeler için gizli input -->
    <input type="hidden" name="deletedDocuments" id="deletedDocuments" value="">


    <div class="row">
        <div class="col-sm-4"><label>İl/İlçe<span style="font-weight: bold; color: red;">*</span></label></div>
        <div class="col-sm-4">
          <select name="il" id="countrySelect" class="form-control form-select" style="width:100%!important;" required>
            <option value="" selected disabled>-Seçiniz-</option>
            @foreach($countries as $item)
              <option value="{{ $item->id }}" {{ $bayi->il == $item->id ? 'selected' : ''}}>{{ $item->name}}</option>
            @endforeach
          </select>
        </div>
        <div class="col-sm-4">
          <select name="ilce" id="citySelect" class="form-control form-select" style="width:100%!important;" required>
            <option value="" selected disabled>-Seçiniz-</option>                              
          </select>
        </div>
      </div> 


    <div class="row">
      <label class="col-sm-4">Telefon<span style="font-weight: bold; color: red;">*</span></label>
      <div class="col-sm-8">
        <input name="tel" id="tel" class="form-control phone" type="text" value="{{ $bayi->tel }}" required>
      </div>
    </div>

    <div class="row">
      <label class="col-sm-4">Adress:</label>
      <div class="col-sm-8">
      <textarea name="address" type="text" class="form-control">{{$bayi->address}}</textarea>
      </div>
    </div>

  <div class="row">
    <label class="col-sm-4">Kullanıcı Adı<span style="font-weight: bold; color: red;">*</span></label>
    <div class="col-sm-8">
      <input name="username" class="form-control" type="text" value="{{ $bayi->username }}" required>
    </div>
  </div>

  <div class="row">
    <label class="col-sm-4">Yeni Şifre:<span style="font-weight: bold; color: red;">*</span></label>
    <div class="col-sm-8">
      <input name="password" class="form-control" type="password" placeholder="Şifre değiştirmek istemiyorsan boş bırak">
    </div>
  </div>

  <div class="row">
      <div class="col-sm-12 gonderBtn">
        <input type="hidden" name="id" value="{{$bayi->user_id}}">
        <input type="submit" class="btn btn-sm btn-info waves-effect waves-light" value="Kaydet">
      </div>
    </div>
  </form>

<script>
  $(document).ready(function () {
    $(".phone").mask("999 999 9999");
  });
</script>

<script type="text/javascript">
  var getDurum = $(".durum").val();
  if (getDurum == 1) {
    $(".ayrilmaTarihi").hide();
  } else if (getDurum == 0) {
    $(".ayrilmaTarihi").show();
  }

  $(".durum").change(function () {
    var getDurum = $(".durum").val();
    if (getDurum == 1) {
      $(".ayrilmaTarihi").hide();
    } else if (getDurum == 0) {
      $(".ayrilmaTarihi").show();
    }
  });
</script>
<script>
        let currentDocumentCount = 2;
        let deletedDocuments = [];

        function deleteDocument(index) {
            if (confirm('Bu belgeyi silmek istediğinizden emin misiniz?')) {
                // Belgeyi listeden kaldır
                $(`.document-item[data-index="${index}"]`).remove();
                
                // Silinecek belgeler listesine ekle
                deletedDocuments.push(index);
                $('#deletedDocuments').val(JSON.stringify(deletedDocuments));
                
                // Belge sayısını güncelle
                currentDocumentCount--;
                updateFileCountInfo();
            }
        }

        function updateFileCountInfo() {
            const remainingSlots = 2 - currentDocumentCount;
            const info = currentDocumentCount === 0 
                ? 'En fazla 2 belge yükleyebilirsiniz.'
                : `Şu anda ${currentDocumentCount} belgeniz var. ${remainingSlots} belge daha ekleyebilirsiniz.`;
            
            $('#fileCountInfo').text(info);
        }

        // Dosya seçimi kontrolü
        $('#belgePdfEdit').on('change', function() {
            const selectedFiles = this.files.length;
            const availableSlots = 2 - currentDocumentCount;
            
            if (selectedFiles > availableSlots) {
                alert(`En fazla ${availableSlots} belge daha ekleyebilirsiniz!`);
                this.value = '';
                return;
            }
        });

        // Sayfa yüklendiğinde bilgi güncelle
        $(document).ready(function() {
            updateFileCountInfo();
        });
    </script>

<script>
  $(document).ready(function () {
    $('#editBayi').submit(function (event) {
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
  $(document).ready(function () {
    var selectedCountryId = {{ $bayi->il == '' ? '0' : $bayi->il }};
    var selectedCityId = {{ $bayi->ilce == '' ? '0' : $bayi->ilce }};
    var citySelect = $("#citySelect");

    if (selectedCountryId) {
      $.get("/get-states/" + selectedCountryId, function (data) {
        citySelect.empty().append(new Option("-Seçiniz-", ""));
        $.each(data, function (index, city) {
          var isSelected = (city.id == selectedCityId);
          citySelect.append(new Option(city.ilceName, city.id, isSelected, isSelected));
        });
      });
    }

    $("#countrySelect").change(function () {
      var selectedIl = $(this).val();
      if (selectedIl) {
        $.get("/get-states/" + selectedIl, function (data) {
          citySelect.empty().append(new Option("-Seçiniz-", ""));
          $.each(data, function (index, city) {
            citySelect.append(new Option(city.ilceName, city.id));
          });
        });
      }
    });
  });
</script>

<script>
  $(document).ready(function () {
    $("#editBayi").submit(function (event) {
      event.preventDefault();
      var formData = new FormData(this);

       $.ajax({
        url: $(this).attr("action"),
        type: "POST",
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: function () {
          $(".btnWrap").html("Yükleniyor. Bekleyin..");
        },
        success: function (data) {
          if (data === false) {
            
            window.location.reload(true);
          } else {
            alert("Bayi bilgileri güncellendi");
            $('#datatableBayi').DataTable().ajax.reload();
            $('#editBayiModal').modal('hide');
            
          }
        },
        error: function (xhr, status, error) {
          alert("Güncelleme başarısız!");
          window.location.reload(true);
        },
      });
    });
  });
  </script>
  
<style>
  <style>
        .document-item {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
            background: #f8f9fa;
        }
        .document-actions {
            display: flex;
            gap: 5px;
            align-items: center;
        }
        .delete-document {
            background: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
        }
        .delete-document:hover {
            background: #c82333;
        }
    </style>
</style>
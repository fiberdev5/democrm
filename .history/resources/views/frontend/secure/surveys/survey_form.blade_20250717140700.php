<!-- JavaScript kodu -->
<script>
    
    var firma_id = {{ $firma->id }};

function openSurveyModal(serviceId) {
    $('#surveyModal').modal('show');
    

    // Modal içeriğini yükle
    $.ajax({
        url:`/${firma_id}/anket/form/${serviceId}`,
        method: 'GET',
        success: function(response) {
            $('#surveyModalBody').html(response);
        },
        error: function() {
            $('#surveyModalBody').html('<div class="alert alert-danger">Form yüklenirken hata oluştu!</div>');
        }
    });
}

// Form submit işlemi
$(document).on('submit', '#surveyForm', function(e) {
    e.preventDefault();
    
    $.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            if (response.success) {
                $('#surveyModal').modal('hide');
                toastr.success(response.message);
                // Sayfayı yenile veya tabloyu güncelle
                location.reload();
            } else {
                toastr.error(response.message);
            }
        },
        error: function(xhr) {
            let errors = xhr.responseJSON.errors;
            if (errors) {
                Object.keys(errors).forEach(function(key) {
                    toastr.error(errors[key][0]);
                });
            } else {
                toastr.error('Bir hata oluştu!');
            }
        }
    });
});
</script>
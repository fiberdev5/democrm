<!-- JavaScript kodu -->
<script>
    
 var tenantId = {{ $firma->id }};
    function openSurveyModal(serviceId) {
        let urlTemplate = "{{ route('survey.form', ['tenant_id' => '__TENANT__', 'service_id' => '__SERVICE__']) }}";
        let url = urlTemplate.replace('__TENANT__', tenantId).replace('__SERVICE__', serviceId);

        $('#surveyModal').modal('show');

        $.ajax({
            url: url,
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
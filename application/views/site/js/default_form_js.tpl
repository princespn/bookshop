<script>
    $("#form").validate({
        errorContainer: $("#error-alert"),
        submitHandler: function (form) {
            ajax_it('{{ current_url() }}', '#form');
        }
    });

    function ajax_it(current_url, form_id) {
        $.ajax({
            url: current_url,
            type: 'post',
            data: $(form_id).serialize(),
            dataType: 'json',
            success: function (data) {
                if (data['type'] == 'error') {
                    $('#response').html('{{ alert('error') }}');
                    if (data['error_fields']) {
                        $.each(data['error_fields'], function (key, val) {
                            $('#' + key).addClass('error');
                        });
                    }
                }
                else if (data['type'] == 'success') {
                    if (data['redirect']) {
                        location.href = data['redirect'];
                    }
                    else {
                        $('#response').html('{{ alert('success') }}');
                        $('.form-control').removeClass('error');

                        setTimeout(function () {
                            $('.alert-msg').fadeOut('slow');
                        }, 5000);
                    }
                }

                $('#msg-details').html(data['msg']);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                {{ js_debug() }}(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }
</script>
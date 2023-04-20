<script>
    function submit_payment_form(form)
    {
        $.ajax({
            url: '{{ submit_url }}',
            type: 'post',
            data: $('#payment-form').serialize(),
            dataType: 'json',
            beforeSend: function () {
                $('.submit-button i ').addClass('fa-spin');
                $('.submit-button').addClass('disabled');
                $('.submit-button span').html('{{ lang('processing') }}');
            },
            complete: function () {
                // re-enable the submit button
                $('.submit-button').removeAttr('disabled');
                $('.submit-button').removeClass('disabled');
                $('.submit-button i ').removeClass('fa-spin');
                $('.submit-button span').html('{{ lang('submit') }}');
            },
            success: function (data) {
                if (data['type'] == 'error') {
                    $('#response').html('{{ alert('error') }}');
                    $('#msg-details').html(data['msg']);
                }
                else if (data['type'] == 'success') {
                    if (data['redirect']) {
                        location.href = data['redirect'];
                    }
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                {{ js_debug() }}(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }
</script>
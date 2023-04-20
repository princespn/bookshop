<script>
    function ajax_it(current_url, form_id)
    {
        $.ajax({
            url: current_url,
            type: 'post',
            data: $(form_id).serialize(),
            dataType: 'json',
            beforeSend: function () {
                $('.submit-button').addClass('disabled');
                $('.submit-button span').html('{{ lang('processing') }}');
            },
            complete: function () {
                // re-enable the submit button
                $('.submit-button').removeAttr('disabled');
                $('.submit-button').removeClass('disabled');
                $('.submit-button span').html('{{ lang('add_to_'~layout_design_shopping_cart_or_bag) }}');
            },
            success: function(data) {
                if (data['type'] == 'error') {
                   $('#response').html('{{ alert('error') }}');
                    console.log();
                }
                else {
                    $('.alert-danger').remove();
                    $('.form-control').removeClass('error');
                    //redirect to the next page if set
                    if (data['redirect']) {
                        location.href = data['redirect'];
                    }
                    else if (data['type'] == 'success') {
                        $('#response').html('{{ alert('success') }}');
                    }
                }


                $('#msg-details').html(data['msg']);
            },
            error: function(xhr, ajaxOptions, thrownError) {
                {{ js_debug() }}(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }
</script>
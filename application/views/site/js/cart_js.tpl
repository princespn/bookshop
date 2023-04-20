<script src="{{ base_url('js/owl-carousel/owl.carousel.min.js') }}"></script>
<script>
    $("#cart-form").validate();

    function rm_item(url) {
        bootbox.confirm('{{ lang('are_you_sure_you_want_to_remove_from_cart') }}', function (result) {
            if (result) {
                location.href = url;
            }
        });
    }

    $('.submit-button').on('click', function () {
        $('.submit-button i').removeClass('fa-caret-right');
        $('.submit-button i').addClass('fa-refresh fa-spin');
        $('.submit-button').addClass('disabled');
        $('.submit-button span').html('{{ lang('please_wait') }}');
    });

    //apply coupon
    $('#apply-discount').on('click', function () {
        $.ajax({
            url: '{{ site_url('cart/apply_coupon') }}',
            type: 'get',
            data: 'coupon=' + encodeURIComponent($('#discount-code').val()),
            dataType: 'json',
            beforeSend: function () {
                $('#apply-discount i ').addClass('fa-spin');
                $('#apply-discount').addClass('disabled');
                $('#apply-discount span').html('{{ lang('processing') }}');
            },
            complete: function () {
                // re-enable the submit button
                $('#apply-discount').removeAttr('disabled');
                $('#apply-discount').removeClass('disabled');
                $('#apply-discount i ').removeClass('fa-spin');
                $('#apply-discount span').html('{{ lang('apply') }}');
            },
            success: function (data) {
                if (data['type'] == 'error') {
                    $('#response').html('{{ alert('error') }}');
                    $('#msg-details').html(data['msg']);
                }
                else if (data['type'] == 'success') {
                    location.href = data['redirect'];
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                {{ js_debug() }}(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
    }
    });
    });
    $('.carousel-three').owlCarousel({
        loop: true,
        margin: 10,
        items: 3,
        responsiveClass: true,
        slideSpeed: 200,
        paginationSpeed: 800,
        rewindSpeed: 1000,
        autoPlay: true,
        stopOnHover: true
    })
</script>
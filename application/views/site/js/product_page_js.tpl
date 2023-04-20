<script src="{{ base_url('js/slider/jquery.bxslider.min.js') }}"></script>
<script src="{{ base_url('js/zoom/jquery.zoom.min.js') }}"></script>
<script src="{{ base_url('js/wow/wow.min.js') }}"></script>
<script src="{{ base_url('js/owl-carousel/owl.carousel.min.js') }}"></script>
<script src="{{ base_url('js/masonry/masonry.js') }}"></script>
<script src="{{ base_url('js/masonry/imagesloaded.js') }}"></script>
<script src="{{ base_url('js/countdown/jquery.countdown.min.js') }}"></script>
<script>
    $(document).ready(function () {
        {% if p.photos %}
        {% for photo in p.photos %}
        {% if config_enabled('layout_design_enable_product_image_zoom') %}
        $('div#zoom-{{ photo.photo_id }}').zoom({
            url: '{{ base_url('images/uploads/products/'~photo.photo_file_name) }}',
            callback: function () {
                $(this).colorbox({href: this.src});
            }
        });
        {% endif %}
        {% endfor %}
        {% endif %}

        $('.slider').bxSlider({
            pagerCustom: '#thumbs',
            mode: 'fade',
            adaptiveHeight: true,
            onSliderLoad: function(){
                $("#image-container").removeClass('invisible');
            }
        });

        $(".image-group").colorbox({rel: 'image-group'});

        {% if p.enable_up_sell %}
        $('#similar-products').load('{{ site_url('product/similar/'~id) }}');
        {% endif %}

        $("#form").validate({
            errorContainer: $("#error-alert"),
            submitHandler: function (form) {
                ajax_it('{{ site_url('cart/add/'~id) }}', '#form');
            }
        });
    });

    $('button[id^=\'button-upload\']').on('click', function () {
        var node = this;
        $('#form-upload').remove();
        $('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="files" /><input type="hidden" name="{{ csrf_token }}" value="{{ csrf_value }}" /></form>');
        $('#form-upload input[name=\'files\']').trigger('click');

        timer = setInterval(function () {
            if ($('#form-upload input[name=\'files\']').val() != '') {
                clearInterval(timer);
                $.ajax({
                    url: '{{ site_url('cart/upload/') }}',
                    type: 'post',
                    dataType: 'json',
                    data: new FormData($('#form-upload')[0]),
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (data) {
                        if (data['type'] == 'error') {
                            $('#response').html('{{ alert('error') }}');
                        } else if (data['type'] == 'success') {
                            $(node).parent().find('span').html(data['file']);
                            $(node).parent().find('input').attr('value', data['key']);
                            $('#response').html('{{ alert('success') }}');
                        }

                        $('#msg-details').html(data['msg']);
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        {{ js_debug() }}(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
            }
        );
    }
    },
    500
    )
    ;
    })
    ;

    function imageChange(id) {
        $("select#attribute-" + id + " option:selected").each(function () {
            var img = ($(this).attr("value"));
            $('#preview-' + id).html('');
            if (img != '') {
                $('#image-box-' + id).removeClass('hide');
                $('#preview-' + id).html('<img src=' + img + ' class="img-fluid animated fadeIn">');
            }
        });
    }

    function updateProductAttribute(id, type) {

        if (type == 'radio') {
            var option = {option_id: id + '-' + id};
        } else {
            var option = {option_id: id + '-' + $('#attribute-' + id).val()};
        }

        $.get('{{ site_url('product/update_option') }}', option,
            function (data) {
                if (data == false) {
                    {% for photo in p.photos %}
                    {% if loop.first %}
                    $('#image-{% if p.default_video_code %}1{% else %}0{% endif %}').attr("src", '{{ photo_path(photo.photo_file_name) }}');
                    $('#imagelink-{% if p.default_video_code %}1{% else %}0{% endif %}').attr("href", '{{ photo_path(photo.photo_file_name) }}');
                    {% endif %}
                    {% endfor %}
                } else {
                    $('#image-{% if p.default_video_code %}1{% else %}0{% endif %}').attr("src", data);
                    $('#imagelink-{% if p.default_video_code %}1{% else %}0{% endif %}').attr("href", data);
                }

                $('#thumb-{% if p.default_video_code %}1{% else %}0{% endif %}').trigger('click');
            });
    }

    {% if p.enable_timer == true %}
    $('#countdown-timer').countdown('{{ p.date_expires }}')
        .on('update.countdown', function (event) {
            var format = '%H:%M:%S';
            if (event.offset.totalDays > 0) {
                format = '%-d day%!d ' + format;
            }
            if (event.offset.weeks > 0) {
                format = '%-w week%!w ' + format;
            }
            $(this).html(event.strftime(format));
        })
        .on('finish.countdown', function (event) {
            $(this).html('This offer has expired!')
                .parent().addClass('disabled');

        });
    {% endif %}

    $("#show-link").click(function () {
        $("p.cp_links").toggle(300);
    });

    var popupWindow = null;
    function centeredPopup(url,winName,w,h,scroll){
        LeftPosition = (screen.width) ? (screen.width-w)/2 : 0;
        TopPosition = (screen.height) ? (screen.height-h)/2 : 0;
        settings =
            'height='+h+',width='+w+',top='+TopPosition+',left='+LeftPosition+',scrollbars='+scroll+',resizable'
        popupWindow = window.open(url,winName,settings)
    }
</script>
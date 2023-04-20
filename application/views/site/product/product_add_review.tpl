{% extends "global/base.tpl" %}
{% block title %}{{ p.meta_title }}{% endblock %}
{% block meta_description %}{{ p.meta_description }}{% endblock meta_description %}
{% block meta_keywords %}{{ p.meta_keywords }}{% endblock meta_keywords %}
{% block css %}
<link href="{{ base_url }}js/star-rating/star-rating.css" rel="stylesheet" type="text/css"/>
{{ parent() }}

{% endblock css %}
{% block page_header %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">{{ lang('add_review') }}</h2>
            </div>
        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div class="row">
        <div class="col-md-12">
            {{ breadcrumb }}
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h3>{{ p.product_name }}</h3>
        </div>
    </div>
    <hr/>
    {{ form_open('register', 'id="form"') }}
    <div class="row">
        <div class="col-md-9">
            <div class="form-group row">
                <div class="col-md-10 offset-md-2">
                    <div class="ratings">
                        <input class="rating hide" name="ratings"
                               value="5.0" data-symbol="&#xf005;"
                               data-glyphicon="false" data-rating-class="rating-fa" data-size="sm"
                               data-show-clear="false" data-show-caption="false">
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label for="title"
                       class="col-md-2 col-form-label text-md-right">
                    {{ lang('title') }}
                </label>
                <div class="col-md-10">
                    {{ form_input('title', '', 'class="form-control required"') }}
                </div>
            </div>
            <div class="form-group row">
                <label for="comment"
                       class="col-md-2 col-form-label text-md-right">
                    {{ lang('your_review') }}
                </label>
                <div class="col-md-10">
                    {{ form_textarea('comment', '', 'class="form-control required"') }}
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-3 offset-md-2">
                    <button type="submit" id="submit-button" class="btn btn-primary">
                        {{ i('fa fa-refresh') }} <span>{{ lang('submit') }}</span></button>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <a href="{{ page_url('product', p) }}">
                {{ image('products', p.photo_file_name, p.product_name, 'img-fluid p-3 mx-auto card-img-top', TRUE) }}
                </a>
                <div class="card-body">
                    <strong>{{ p.product_name }}</strong>
                    <p class="overview">
                        <small class="text-muted">{{ p.product_overview }}</small>
                    <p>
                        <a href="{{ page_url('product', p) }}"
                           class="btn btn-primary more-info">{{ i('fa fa-info-circle') }}
                            {{  lang('view') }}</a>
                    </p>
                </div>

            </div>
        </div>
    </div>
    {{ form_hidden('product_id', p.product_id) }}
    {{ form_hidden('member_id', sess('member_id')) }}
    {{ form_hidden('sort_order', '0') }}
    {{ form_close() }}
{% endblock content %}
{% block javascript_footer %}
    {{ parent() }}
    <script src="{{ base_url('js/star-rating/star-rating.min.js') }}"></script>
    <script>
        $('.rating').rating();

        $("#form").validate({
            ignore: "",
            submitHandler: function (form) {
                $.ajax({
                    url: '{{ current_url() }}',
                    type: 'POST',
                    dataType: 'json',
                    data: $('#form').serialize(),
                    beforeSend: function () {
                        $('#submit-button i ').addClass('fa-spin');
                        $('#submit-button').addClass('disabled');
                        $('#submit-button span').html('{{ lang('please_wait') }}');
                    },
                    complete: function () {
                        $('#submit-button i ').removeClass('fa-spin');
                        $('#submit-button').removeClass('disabled');
                        $('#submit-button span').html('{{ lang('submit') }}');
                    },
                    success: function (response) {
                        if (response.type == 'success') {
                            $('.alert-danger').remove();
                            $('.form-control').removeClass('error');

                            if (response.redirect) {
                                location.href = response.redirect;
                            }
                            else {
                                $('#response').html('{{ alert('success') }}');

                                setTimeout(function () {
                                    $('.alert-msg').fadeOut('slow');
                                }, 5000);
                            }
                        }
                        else {
                            $('#response').html('{{ alert('error') }}');
                            if (response['error_fields']) {
                                $.each(response['error_fields'], function (key, val) {
                                    $('#' + key).addClass('error');
                                    $('#' + key).focus();
                                });
                            }
                        }

                        $('#msg-details').html(response.msg);
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        {{ js_debug() }}(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    }
                });
            }
        });
    </script>
{% endblock javascript_footer %}
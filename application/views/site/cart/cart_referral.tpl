{% extends "global/base.tpl" %}
{% block title %}{{ lang('your_cart')|capitalize }}{% endblock %}
{% block meta_description %}{{ lang('your_shopping_cart') }}{% endblock meta_description %}
{% block meta_keywords %}{{ lang('your_shopping_cart') }}{% endblock meta_keywords %}
{% block meta_robots %}noindex, nofollow{% endblock meta_robots %}
{% block page_header %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">{{ lang('referral_required') }}</h2>
            </div>

        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div class="cart-referral">
        {{ breadcrumb }}
        <div class="row">
            <div class="col-md-12">
                {{ form_open('', 'id="form"') }}
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ i('fa fa-info-circle') }} {{ lang('referring_user_required') }}</h5>
                        <hr />
                        <p>{{ lang('cart_referral_username_required') }}</p>
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    {{ form_input('username', '', 'id="username" class="form-control required"') }}
                                    <div class="input-group-append">
                                       <button class="btn btn-light" type="submit" id="submit-button">
                                           {{ i('fa fa-refresh') }} <span>{{ lang('continue') }}</span></button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                {{ form_close() }}
            </div>
        </div>
    </div>
{% endblock content %}
    {% block javascript_footer %}
        {{ parent() }}
        <script>
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
                                $('#username').addClass('error');
                                $('#username').focus();
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
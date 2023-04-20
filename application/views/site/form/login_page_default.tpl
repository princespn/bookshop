{% extends "global/base.tpl" %}
{% block title %}{{ lang('login') }}{% endblock %}
{% block meta_description %}{{ lang('login') }}{% endblock meta_description %}
{% block meta_robots %}noindex, nofollow{% endblock meta_robots %}
{% block page_header %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">{{ lang('account_login') }}</h2>
            </div>

        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div class="row">
        <div class="col-md-12">
            {{ breadcrumb }}
            <div class="row">
                <div class="{% if (config_option('layout_design_login_enable_social_login') == 0) %}offset-md-3{% endif %} col-md-6">
                    {{ form_open('login', 'id="form"') }}
                    <div class="login">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-text">
                                    <div class="form-group row">
                                        <label for="{{ user_login_email_field }}"
                                               class="col-md-4 col-form-label text-md-right">{{ lang('email_address') }}</label>
                                        <div class="col-md-8">
                                            <input name="{{ user_login_email_field }}" type="email" id="email"
                                                   placeholder="{{ lang('your_email_address') }}"
                                                   class="form-control required email">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="{{ user_login_password_field }}"
                                               class="col-md-4 col-form-label text-md-right">{{ lang('password') }}</label>
                                        <div class="col-md-8">
                                            <input name="{{ user_login_password_field }}" type="password" id="password"
                                                   placeholder="{{ lang('password') }}" class="form-control required">
                                        </div>
                                    </div>
                                    {% if config_enabled('sts_form_enable_login_captcha') %}
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label text-right">
                                            {{ lang('security_captcha') }}
                                        </label>
                                        <div class="col-sm-8">
                                            <div class="g-recaptcha" data-sitekey="{{ sts_form_captcha_key }}"></div>
                                        </div>
                                    </div>
                                    <hr/>
                                    {% endif %}
                                    <div class="form-group row">
                                        <div class="offset-md-4 col-md-8">
                                            <button id="login-button" type="submit" class="btn btn-lg  btn-primary">
                                                {{ i('fa fa-caret-right') }} {{ lang('login') }}
                                            </button>
                                        </div>
                                    </div>
                                    <hr />
                                    <div class="form-group row">
                                        <div class="col-md-12 text-center">
                                            <a href="{{ site_url('login/reset_password') }}"
                                               class="btn btn-outline-primary">{{ i('fa fa-undo') }} {{ lang('reset_password') }}</a>
                                            <a href="register" class="btn btn-primary">
                                                {{ i('fa fa-user') }} {{ lang('create_your_account') }}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {% if redirect %}
                        {{ form_hidden('redirect', redirect) }}
                    {% endif %}
                    {{ form_close() }}
                </div>
                {% if config_enabled('layout_design_login_enable_social_login') %}
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-text">
                                <div class="form-group row">
                                    <div class="col-md-12 text-center">
                                        <h3 class="card-title">{{ lang('sign_in_using') }}</h3>
                                        <hr />
                                        {% if config_enabled('layout_design_login_enable_facebook_login') %}
                                        <a href="{{ site_url('login/social/Facebook') }}"
                                           class="btn btn-lg btn-facebook btn-icon">{{ i('fa fa-facebook') }} {{ lang('facebook') }}</a>
                                        {% endif %}
                                        {% if config_enabled('layout_design_login_enable_twitter_login') %}
                                        <a href="{{ site_url('login/social/Twitter') }}"
                                           class="btn btn-lg btn-twitter btn-icon">{{ i('fa fa-twitter') }} {{ lang('twitter') }}</a>
                                        {% endif %}
                                        {% if config_enabled('layout_design_login_enable_google_login') %}
                                        <a href="{{ site_url('login/social/Google') }}"
                                           class="btn btn-lg btn-google-plus btn-icon">{{ i('fa fa-google') }} {{ lang('google') }}</a>
                                        {% endif %}

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {% endif %}
            </div>
        </div>
    </div>
{% endblock %}
{% block javascript_footer %}
    {{ parent() }}
    <script>
        $("#form").validate({
            rules: {
        {{ user_login_email_field }}:
        {
            required: true,
                    email: true
        },
        {{ user_login_password_field }}:
        {
            required: true,
                    minlength: 6,
                    maxlength: 30
        }},
        errorContainer: $("#error-alert"),
                submitHandler:function (form) {
            $.ajax({
                url: '{{ current_url() }}',
                type: 'post',
                data: $('#form').serialize(),
                dataType: 'json',
                beforeSend: function () {
                    $('#login-button i').removeClass('fa-caret-right');
                    $('#login-button i').addClass('fa-refresh fa-spin');
                    $('#login-button').addClass('disabled');
                    $('#login-button span').html('{{ lang('processing') }}');
                },
                complete: function () {
                    // re-enable the submit button
                    $('#login-button').removeAttr('disabled');
                    $('#login-button').removeClass('disabled');
                    $('#login-button i ').removeClass('fa-refresh fa-spin');
                    $('#login-button i').addClass('fa-caret-right');
                    $('#login-button span').html('{{ lang('login') }}');
                },
                success: function (data) {
                    if (data['type'] == 'error') {
                        $.each(data, function (key, val) {
                            if (key == 'msg') {
                                $('#response').html('{{ alert('error') }}');
                                $('#msg-details').html(val);
                            }
                        });
                    }
                    else if (data['redirect']) {
                        location.href = data['redirect'];
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    {{ js_debug() }}(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        }
        })
    </script>
{% endblock javascript_footer %}
{% extends "global/base.tpl" %}
{% block title %}{{ lang('registration') }}{% endblock %}
{% block meta_description %}{{ lang('registration') }}{% endblock meta_description %}
{% block meta_robots %}noindex, nofollow{% endblock meta_robots %}
{% block page_header %}
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h2 class="headline">{{ lang('register') }}</h2>
        </div>
    </div>
</div>
{% endblock page_header %}
{% block content %}
<div class="register">
    {{ breadcrumb }}
    <div class="row">
        <div class="col-sm-5 d-none d-md-block">
            <div class="card create-account-description">
                <div class="card-body">
                    <h3 class="card-title">
                        {{ i('fa fa-user')}}  {{ lang('account_gives_access_to') }}
                    </h3>
                    <hr/>
                    <h5>{{ lang('create_account_1_reason') }}</h5>
                    <p class="card-text">{{ lang('create_account_1_description') }}</p>
                    <h5>{{ lang('create_account_2_reason') }}</h5>
                    <p class="card-text">{{ lang('create_account_2_description') }}</p>
                    {% if config_enabled('sts_support_enable') %}
                    <h5>{{ lang('create_account_3_reason') }}</h5>
                    <p class="card-text">{{ lang('create_account_3_description') }}</p>
                    {% endif %}
                    {% if config_enabled('affiliate_marketing') %}
                    <h5>{{ lang('create_account_4_reason') }}</h5>
                    <p class="card-text">{{ lang('create_account_4_description') }}</p>
                    {% endif %}
                </div>
            </div>
        </div>
        <div class="col-sm-7">
            {{ form_open('register', 'id="form"') }}
            <div class="register">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">{{ i('fa fa-file-text-o')}} {{ lang('fill_in_details') }}</h3>
                        <hr />
                        {% if (config_option('affiliate_data')) %}
                        <div class="form-group row">
                            <label for="sponsor"
                                   class="col-sm-4 col-form-label text-sm-right">
                                {{ lang('referred_by') }}
                            </label>

                            <div class="col-sm-8">
                                <span class="form-control">{{ config_option('affiliate_data', 'username') }}</span>
                                {{  form_hidden('sponsor_id', config_option('affiliate_data', 'member_id')) }}
                            </div>
                        </div>
                        {% elseif (config_enabled('sts_affiliate_require_referral_code')) %}
                        <div class="form-group row">
                            <label for="sponsor"
                                   class="col-sm-4 col-form-label text-sm-right">
                                {{ lang('who_referred_you') }}
                            </label>
                            <div class="col-sm-8">
                                {{  form_dropdown('sponsor_id', '', '', 'id="sponsor_id" class="form-control required"') }}
                            </div>
                        </div>
                        {% endif %}
                        {% if (fields.values) %}
                        {% for s in fields.values %}
                        {% if s.field_type != 'hidden' %}
                        <div class="form-group row">
                            <label for="{{ s.form_field }}"
                                   class="col-sm-4 col-form-label text-sm-right">
                                {{ s.field_name }}
                            </label>

                            <div class="col-sm-8">{{ s.field }}</div>
                        </div>
                        {% else %}
                        {{ s.field }}
                        {% endif %}
                        {% endfor %}
                        {% endif %}
                        {% if config_enabled('sts_form_enable_captcha') %}
                        <div class="form-group row">
                            <label for="sts_form_enable_captcha"
                                   class="col-sm-4 col-form-label text-sm-right">
                                {{ lang('security_captcha') }}
                            </label>

                            <div class="col-sm-8">
                                <div class="g-recaptcha" data-sitekey="{{ sts_form_captcha_key }}"></div>
                            </div>
                        </div>
                        {% endif %}
                        {% if config_enabled('sts_form_enable_tos_checkbox') %}
                        <hr/>
                        <div class="form-group row">
                            <label class="col-sm-4">
                                <a href="{{tos_link}}" target="_blank">
                                    {{ lang('terms_of_service') }} {{ i('fa fa-external-link') }}</a>
                            </label>
                            <div class="col-sm-8">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="tos" class="required"> {{ lang('agree_with_tos') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        {% endif %}
                        {% if config_enabled('sts_form_enable_list_subscribe_checkbox') %}
                        <hr/>
                        <div class="form-group row">
                            <label class="col-sm-4">{{ lang('subscribe_to_mailing_list') }}</label>
                            <div class="col-sm-8">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="subscribe" checked> {{ lang('yes_subscribe_me') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        {% endif %}
                        <div class="form-group row">
                            <div class="offset-sm-4 col-sm-8">
                                <button type="submit" id="submit-button" class="btn-lg btn btn-primary">
                                    {{ i('fa fa-refresh') }} <span>{{ lang('submit') }}</span>
                                </button>
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
<script src="{{ base_url('js/select2/select2.min.js') }}"></script>
<script>
    $("#sponsor_id").select2({
        ajax: {
            url: '{{ site_url('search/affiliates/username') }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    username: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data, page) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.member_id,
                            text: item.username
                        }
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 3
    });
    $(function () {
        $('.datepicker-input').datepicker({format: '{{ format_date }}'});
    });

    select_country('#billing_country');
    select_country('#payment_country');

    function updateregion(select, type) {
        $.get('{{ site_url('search/load_regions/state') }}', {country_id: $('#' + type + '_country').val()},
            function (data) {
                $('#' + type + '_state').html(data);
                $(".s2").select2();
            }
        );
    }

    function select_country(id) {
        //search countries
        $(id).select2({
            ajax: {
                url: '{{ site_url('search/search_countries/') }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        country_name: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, page) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                id: item.country_id,
                                text: item.country_name
                            }
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 2
        });
    }

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
                        } else {
                            $('#response').html('{{alert('success')}}');

                            setTimeout(function () {
                                $('.alert-msg').fadeOut('slow');
                            }, 5000);
                        }
                    } else {
                        $('#response').html('{{alert('error')}}');
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
    })
    ;
</script>
{% endblock javascript_footer %}
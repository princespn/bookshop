{% extends "global/base.tpl" %}
{% block title %}{{ lang('email_downline') }}{% endblock %}
{% block page_header %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">{{  lang('mass_email') }}</h2>
            </div>

        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div id="email" class="content">
        {{ form_open_multipart('', 'id="form"') }}
        {{ breadcrumb }}
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ lang('send_downline_email') }}
                        </h5>
                        <small>
                            {{ lang('send_downline_email_description') }}
                        </small>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <fieldset class="form-group">
                                    <label for="message">{{ lang('message') }}</label>
                                    <textarea name="message" rows="10"
                                              class="form-control required"></textarea>
                                </fieldset>
                            </div>
                        </div>
                        {% if config_enabled('sts_form_enable_captcha') %}
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="g-recaptcha" data-sitekey="{{ sts_form_captcha_key }}"></div>
                                </div>
                            </div>
                            <hr/>
                        {% endif %}
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-lg btn-primary btn-block-sm submit-button">
                                    {{ i('fa fa-refresh') }} <span>{{ lang('send') }}</span>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        {{ form_hidden('member_id', sess('member_id')) }}
       {{ form_close() }}
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
                    $('.submit-button').addClass('disabled');
                    $('.submit-button span').html('{{ lang('please_wait') }}');
                },
                complete: function () {
                    $('.submit-button i ').removeClass('fa-spin');
                    $('.submit-button').removeClass('disabled');
                    $('.submit-button span').html('{{ lang('send') }}');
                },
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
    });
</script>
{% endblock javascript_footer %}

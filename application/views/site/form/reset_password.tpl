{% extends "global/base.tpl" %}
{% block title %}{{ lang('reset_password')|capitalize }}{% endblock %}
{% block meta_description %}{{ lang('reset_password') }}{% endblock meta_description %}
{% block meta_keywords %}{{ lang('reset_password') }}{% endblock meta_keywords %}
{% block meta_robots %}noindex, nofollow{% endblock meta_robots %}
{% block page_header %}
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2 class="headline">{{ lang('reset_password') }}</h2>
        </div>

    </div>
</div>
{% endblock page_header %}
{% block content %}
<div class="reset-password">
    {{ breadcrumb }}
    <div class="row">
        <div class="col-md-12">
            {{ form_open('', 'id="form"') }}
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">{{ i('fa fa-info-circle') }} {{ lang('reset_your_password') }}</h3>
                    <hr />
                    {% if code %}
                    <div class="form-group row">
                        <label for="{{ user_login_password_field }}"
                               class="col-md-2 col-form-label">{{ lang('new_password') }}</label>
                        <div class="col-md-5">
                            {{ form_password('cpass', '', 'id="cpass" class="form-control required"') }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="{{ user_login_password_field }}"
                               class="col-md-2 col-form-label">{{ lang('confirm_password') }}</label>
                        <div class="col-md-5">
                            {{ form_password('cpassconf', '', 'id="cpassconf" class="form-control required"') }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-5 offset-md-2">
                            {{ form_hidden('code', code)}}
                            <button class="btn btn-secondary" type="submit" id="submit-button">
                                {{ i('fa fa-refresh') }} <span>{{ lang('submit') }}</span></button>
                        </div>
                    </div>
                    {% else %}
                    <p>{{ lang('enter_username_to_reset_password') }}</p>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <div class="input-group">
                                {{ form_input('email', '', 'id="email" class="form-control email required"') }}
                                <div class="input-group-append">
                                    <button class="btn btn-secondary" type="submit" id="submit-button">
                                        {{ i('fa fa-refresh') }} <span>{{ lang('submit') }}</span></button>
                                </div>
                            </div>

                        </div>
                    </div>
                    {% endif %}
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
            rules: {
                cpass: {
                    required: true,
                    minlength: {{ min_member_password_length }},
                maxlength: {{ max_member_password_length }}
        },
        cpassconf
    :
    {
        required: true,
            equalTo
    :
        '#cpass'
    }
    },
    ignore: "",
        submitHandler
    :

    function (form) {
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
                        $('#response').html('{{ alert('success') }}');

                        setTimeout(function () {
                            $('.alert-msg').fadeOut('slow');
                        }, 5000);
                    }
                } else {
                    $('#response').html('{{ alert('error') }}');
                    $('#email').addClass('error');
                    $('#email').focus();
                }

                $('#msg-details').html(response.msg);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                {{ js_debug() }}(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
    }
    }

    )
    ;
    }
    })
    ;
</script>
{% endblock javascript_footer %}
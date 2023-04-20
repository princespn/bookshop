{% extends "global/base.tpl" %}
{% block title %}{{ form.form_name }}{% endblock %}
{% block meta_description %}{{ lang('contact_us') }}{% endblock meta_description %}
{% block meta_robots %}noindex, nofollow{% endblock meta_robots %}
{% block page_header %}
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2 class="headline">{{ lang(form.form_name) }}</h2>
        </div>
    </div>
</div>
{% endblock page_header %}
{% block content %}
<div class="custom-form">
    {{ breadcrumb }}
    <div class="row">
        <div class="col-md-7">
            {{ form_open('', 'id="form"') }}
            <div class="contact">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">{{ i('fa fa-envelope') }} {{ lang('fill_in_details') }}</h3>
                        <hr />
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
                        <div class="form-group  row">
                            <label for="sts_form_enable_captcha"
                                   class="col-sm-4 col-form-label text-sm-right">
                                {{ lang('security_captcha') }}
                            </label>

                            <div class="col-sm-8">
                                <div class="g-recaptcha"
                                     data-sitekey="{{ sts_form_captcha_key }}"></div>
                            </div>
                        </div>
                        {% endif %}
                        <div class="form-group  row">
                            <div class="offset-md-4 col-md-8">
                                <button type="submit" id="submit-button" class="btn-lg btn btn-primary">
                                     <span id="submit-span">{{ i('fa fa-refresh') }} {{ lang('submit') }}</span>
                                </button>
                            </div>
                            <hr/>
                        </div>
                    </div>
                </div>
            </div>
            {{ form_close() }}
        </div>
        <div class="col-md-5">
            <div class="card contact-description">
                <div class="card-body">
                    <h3 class="card-title">{{ i('fa fa-file-text-o') }} {{ lang('signup') }}</h3>
                    <hr/>
                    <p class="card-text">{{ html_entity_decode(form.form_description) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
{% endblock content %}
{% block javascript_footer %}
{{ parent() }}
<script>
    $(function () {
        $('.datepicker-input').datepicker({format: '{{ format_date }}'});
    });

    $("#form").validate({
        submitHandler: function(form) {
            form.submit();
            $('#submit-span').html('{{ i('fa fa-spinner fa-spin')}} {{ lang('please_wait') }}');
        }
    });
</script>
{% endblock javascript_footer %}
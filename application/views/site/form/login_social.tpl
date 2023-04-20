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
    <div class="col-lg-12">
        {{ breadcrumb }}
        <div class="row">
            <div class="col-md-6 offset-md-3">
                {{ form_open('', 'id="form"') }}
                <div class="login">
                    <div class="card">
                        <div class="card-header">
                            <h3>{{ lang('required_fields') }}</h3>
                        </div>
                        <div class="card-body">

                            <div class="card-text">
                                <div class="form-group row">
                                    <label for="fname"
                                           class="col-md-4 col-form-label">{{ lang('fname') }}</label>
                                    <div class="col-md-8">
                                        <input name="firstName" type="text" id="firstName"
                                               placeholder="{{ lang('first_name') }}" value="{{ user.firstName }}"
                                               class="form-control required">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="email"
                                           class="col-md-4 col-form-label">{{ lang('email_address') }}</label>
                                    <div class="col-md-8">
                                        <input name="email" type="email" id="email"
                                               placeholder="{{ lang('enter_your_email_address') }}"
                                               value="{{ user.email }}"
                                               class="form-control required email">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="offset-md-4 col-md-8">
                                        <button type="submit" class="btn btn-primary">
                                            {{ i('fa fa-caret-right') }} {{ lang('login') }}
                                        </button>
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
        </div>
    </div>
</div>
{% endblock %}
{% block javascript_footer %}
{{ parent() }}
<script>
    $("#form").validate();
</script>
{% endblock javascript_footer %}
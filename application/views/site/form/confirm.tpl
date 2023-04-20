{% extends "global/base.tpl" %}
{% block title %}{{ lang('account_confirmation') }}{% endblock %}
{% block meta_description %}{{ lang('account_confirmation') }}{% endblock meta_description %}
{% block meta_robots %}noindex, nofollow{% endblock meta_robots %}
    {% block page_header %}
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="headline">{{ lang('account_registration') }}</h2>
                </div>

            </div>
        </div>
    {% endblock page_header %}
    {% block content %}
        <div class="confirm">
            {{ breadcrumb }}
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="card-title">{{ lang('account_confirmation') }}</h2>
                            {% if confirmed %}
                                <p class="card-text">
                                    {{ lang('your_account_has_been_confirmed') }}
                                </p>
                                {% if config_enabled('sts_affiliate_admin_approval_required') %}
                                    <p class="card-text">
                                    {{ lang('admin_approval_required') }}
                                    </p>
                                {% endif %}
                            {% else %}
                                <p class="card-text">
                                    {{ lang('thank_you_for_registering') }}
                                </p>
                                <p class="card-text">
                                    {% if config_option('sts_email_require_confirmation_on_signup') %}
                                        {{ lang('email_confirmation_sent_to_your_email_address') }}
                                    {% else %}
                                        {{ lang('proceed_to_login_page') }}
                                    {% endif %}
                                </p>
                            {% endif %}
                            <p class="card-text">
                                <a href="{{ site_url('login') }}"
                                   class="btn btn-primary">{{ i('fa fa-lock') }} {{ lang('login_page') }}</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {% endblock content %}
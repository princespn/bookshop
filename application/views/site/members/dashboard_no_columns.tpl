{% extends "global/base.tpl" %}
{% block title %}{{ lang('members_dashboard') }}{% endblock %}
{% block page_header %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">{{ lang('members') }}</h2>
            </div>

        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div id="dashboard" class="content">
        {{ breadcrumb }}
        <div class="row">
            <div class="col-md-12">
                <div id="dashboard-icons" class="row">
                    {% if icons %}
                        {% for p in icons %}
                            <div class="col-md-3 col-6">
                                <a href="{{ p.url }}">
                                    <div class="card mb-3 mx-auto icons">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">{{ lang(p.title) }}</h5>
                                            {{ i('fa fa-5x '~p.icon) }}
                                        </div>
                                    </div>
                                </a>
                            </div>
                        {% endfor %}
                    {% endif %}
                </div>
            </div>
        </div>
        {% if config_enabled('affiliate_marketing') %}
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-3">
                        {% if sess('is_affiliate') == 1 %}
                            <div class="card-body">
                                <h4 class="card-title">{{ lang('your_referral_link') }}</h4>
                                <hr />
                                <h5 class="text-center">
                                    {{ lang('main_affiliate_link') }}: <a href="{{ affiliate_url() }}">{{ affiliate_url() }}</a>
                                </h5>
                            </div>
                        {% else %}
                            <div class="card-header">{{ lang('activate_your_account') }}</div>
                            <div class="card-body">
                                <a href="{{ site_url('members/dashboard/activate_affiliate') }}" class="btn btn-primary">
                                    {{ i('fa fa-angle-double-right') }}
                                    {{ lang('click_here_to_activate_affiliate_account') }}</a>
                            </div>
                        {% endif %}
                    </div>
                </div>
            </div>
        {% endif %}
    </div>
{% endblock content %}
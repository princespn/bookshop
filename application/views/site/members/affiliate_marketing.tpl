{% extends "global/base.tpl" %}
{% block title %}{{ lang('affiliate_marketing')|capitalize }}{% endblock %}
{% block page_header %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">{{ lang('affiliate_marketing') }}</h2>
            </div>

        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div id="affiliate_marketing" class="content container">
        {{ breadcrumb }}
        {% if config_enabled('affiliate_marketing') %}
            <div class="row">
                <div class="col-md-12">
                    <div class="card text-sm-center">
                        <div class="card-body">
                            <h5><span>{{ i('fa fa-link') }} {{ lang('main_affiliate_link') }}</span>:
                                <a href="{{ affiliate_url() }}">{{ affiliate_url() }}</a></h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    {% if tools %}
                        <div class="card-columns">
                            {% for p in tools %}
                                <div class="card cursor"
                                     onclick="window.location='{{ page_url('members', 'affiliate_marketing/module/'~p.module_id) }}'">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ i('fa fa-cog') }} {{ p.module_name }}</h5>
                                        <p class="card-text">{{ lang(p.module_description) }}</p>
                                        <p class="card-text text-sm-right">
                                            <a href="{{ page_url('members', 'affiliate_marketing/module/'~p.module_id) }}"
                                               class="btn btn-secondary btn-sm">{{ i('fa fa-search') }} {{ lang('view') }}</a>
                                        </p>
                                    </div>
                                </div>
                            {% endfor %}
                        </div>
                    {% endif %}
                </div>
            </div>
        {% else %}
            <div class="card">
                <div class="card-header">{{ lang('activate_your_account') }}</div>
                <div class="card-body text-sm-center">
                    <a href="{{ page_url('members', 'affiliate_marketing/activate') }}" class="btn btn-primary btn-lg">
                        {{ i('fa fa-check') }}
                        {{ lang('click_here_to_activate_affiliate_account') }}</a>
                    <a href="javascript:window.history.back()" class="btn btn-secondary btn-lg">
                        {{ i('fa fa-undo') }}
                        {{ lang('go_back') }}</a>
                </div>
            </div>
        {% endif %}
    </div>
{% endblock content %}
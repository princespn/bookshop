{% extends "global/base.tpl" %}
{% block title %}{{ lang('error') }}{% endblock %}
{% block meta_description %}{{ lang('error') }}{% endblock meta_description %}
{% block container %}
    <div class="bg-404">
        <div class="page-not-found">
            <div class="container">
                <div class="cell  text-sm-center">
                    <h1><span>{{ i('fa fa-frown-o') }}</span></h1>
                    <h2 class="headline">{{ error_message }}</h2>
                </div>
            </div>
        </div>
    </div>
{% endblock container %}
{% block javascript_footer %}
    {{ parent() }}

{% endblock javascript_footer %}
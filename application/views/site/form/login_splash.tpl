{% extends "global/meta.tpl" %}
{% block title %}{{ lang('login') }}{% endblock %}
{% block meta_description %}{{ lang('login') }}{% endblock meta_description %}
{% block meta_robots %}noindex, nofollow{% endblock meta_robots %}
{% block body %}
<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="mx-auto text-center animated fadeIn">

            {% if layout_design_site_logo %}
            <a href="{{ site_url() }}">
                <img src="{{ layout_design_site_logo }}" alt="{{ lang('logo') }}"
                     class="img-responsive"/>
            </a>
            {% endif %}

            <p><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i></p>
            <h4>{{ lang('please_wait') }}</h4>
            <h4><a href="{{ redirect }}" class="">{{ lang('if_not_forwarded_click_here') }}</a></h4>


        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        setTimeout(function () {
            window.location = '{{ redirect }}';
        }, 1000);

    });
</script>
{% endblock body %}
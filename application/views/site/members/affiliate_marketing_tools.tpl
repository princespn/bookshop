{% extends "global/base.tpl" %}
{% block title %}{{ lang(p.title|capitalize) }}{% endblock %}
{% block page_header %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">{{ lang(p.title) }}</h2>
            </div>

        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div id="aff-tools" class="content">
        {{ breadcrumb }}
        {{ p.rendered_html }}
    </div>
{% endblock content %}
{% block javascript_footer %}
    {{ parent() }}
    <script src="{{ base_url('js/popup.js') }}"></script>
    <script>
        $('.popup').popupWindow({
            height:300,
            width:600,
            top:50,
            left:250
        });
    </script>
{% endblock javascript_footer %}
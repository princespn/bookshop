{% extends "global/base.tpl" %}
{% block container %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Sample Page</h1>
            </div>
        </div>
    </div>
{% endblock container %}
{% block javascript_footer %}
    {{ parent() }}
{% endblock javascript_footer %}
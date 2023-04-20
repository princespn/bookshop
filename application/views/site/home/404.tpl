{% extends "global/base.tpl" %}
{% block title %}{{ lang('page_not_found') }}{% endblock %}
{% block meta_description %}{{ lang('page_not_found') }}{% endblock meta_description %}
{% block page_header %}
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2 class="headline">{{ lang('page_not_found') }}</h2>
        </div>
    </div>
</div>
{% endblock page_header %}
{% block content %}
<div class="bg-404">
    <div class="container text-center">
        <div class="row">
            <div class="col-12">
                <div class="jumbotron">
                    <h1 class="display-1">404</h1>
                    <h1 class="display-3">{{ lang('page_not_found') }}</h1>
                    {% if config_enabled('layout_design_show_search_form') %}
                    <form action="{{ site_url('search') }}" method="get" id="top-search-form"
                          class="form-horizontal" accept-charset="utf-8">
                        <div class="row">
                            <div class="col-6 offset-lg-3 mt-3">
                                <div class="input-group">
                                    <input type="text" name="search_term" class="form-control-lg form-control"
                                           placeholder="Search For...">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary"
                                                type="submit">{{ lang('go') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    {% endif %}
                </div>
            </div>
        </div>
    </div>
</div>
{% endblock content %}
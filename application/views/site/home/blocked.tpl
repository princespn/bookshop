{% extends "global/base.tpl" %}
{% block title %}{{ lang('page_not_found') }}{% endblock %}
{% block meta_description %}{{ lang('page_not_found') }}{% endblock meta_description %}
{% block container %}
<div class="bg-404">
    <div class="page-not-found">
        <div class="container">
            <div class="cell  text-sm-center">
                <h1><span>{{ i('fa fa-frown-o') }}</span></h1>
                <h2 class="headline">404 {{ lang('page_not_found') }}</h2>
                {% if config_enabled('layout_design_show_search_form') %}
                <br/>
                <form action="{{ site_url('search')}}" method="get" role="form" id="top-search-form"
                      class="form-horizontal" accept-charset="utf-8">
                    <div class="row">
                        <div class="col-6 offset-lg-3">
                            <div class="input-group">
                                <input type="text" name="search_term" class="form-control-lg form-control"
                                       placeholder="Search For...">
                                <div class="input-group-append">
                                    <button class="btn btn-secondary"
                                            type="submit">Go
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
{% endblock container %}
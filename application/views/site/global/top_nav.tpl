<div id="top-nav">
    <nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-top-nav">
        <button id="sidebar-button" class="navbar-toggler d-md-none">
            {{ i('fa fa-bars') }}
        </button>
        <a class="navbar-brand d-block  mb-0" href="{{ site_url }}">
            {% if affiliate_data.fname %}
            {{ lang('referred_by') }} {{ affiliate_data.fname }}
            {% else %}
            {{ sts_site_name }}
            {% endif %}
        </a>

        <button id="right-toggle" class="d-lg-none navbar-toggler navbar-toggler-right" type="button"
                data-toggle="collapse"
                data-target="#navbar-nav-dropdown" aria-controls="navbar-nav-dropdown" aria-expanded="false"
                aria-label="Toggle navigation">
            {{ i('fa fa-ellipsis-v') }}
        </button>
        <div class="collapse navbar-collapse ml-auto" id="navbar-nav-dropdown">

            <ul class="navbar-nav ml-auto">
                {% if config_enabled('layout_design_show_search_form') %}
                <li class="nav-item d-none d-md-block">
                    {{ form_open('search', 'method="get" id="search-form-bar" class="hide form-inline"') }}
                    <div class="input-group">
                        <input type="text" name="search_term" class="form-control"
                               placeholder="{{ lang('search_for') }}...">
                        <div class="input-group-append">
                            <button class="btn btn-secondary"
                                    type="submit">{{ lang('go') }}
                            </button>
                        </div>
                    </div>
                    {{ form_close() }}
                </li>
                <li class="nav-item  d-none d-md-block">
                    <a id="search-button" class="nav-link">
                        <i class="fa fa-search"></i></a>
                </li>
                {% endif %}
                {% include ('global/cart_nav.tpl') %}
                {% include ('global/top_currency.tpl') %}
                {% include ('global/top_lang_selector.tpl') %}
                {% include ('global/my_account.tpl') %}

            </ul>
        </div>
    </nav>
</div> <!-- /.top-nav -->
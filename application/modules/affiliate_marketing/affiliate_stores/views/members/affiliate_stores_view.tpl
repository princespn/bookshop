{% if rows %}
    {{ form_open('members/affiliate_marketing/module/'~id~'/update', 'role="form" id="form" class="form-horizontal"') }}
    <div class="card panel panel-default">
        <div class="card-header">
            {{ lang('store_details') }}
        </div>
        {{ p.rendered_html }}
        <div class="card-body panel-body">
            <div class="form-group row">
                <div class="col-md-6">
                    <h3>{{ lang('affiliate_store_details') }}</h3>
                </div>
                {{ rendered_html }}
                <div class="col-md-6 text-sm-right">
                    <a href="{{ site_url('shop') }}/{{ sess('username') }}" target="_blank" class="btn btn-secondary">
                        {{ i('fa fa-search') }} {{ lang('view_store') }}</a>
                    <a href="{{ site_url('store') }}" class="btn btn-primary">
                        {{ i('fa fa-shopping-'~layout_design_shopping_cart_or_bag) }} {{ lang('select_products') }}</a>
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <label class="col-md-4 control-label">
                    {{ lang('store_name') }}
                </label>
                <div class="col-md-5">
                    {{ form_input('name', rows.name, 'class="form-control"') }}
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <label class="col-md-4 control-label">
                    {{ lang('welcome_headline') }}
                </label>
                <div class="col-md-5">
                    {{ form_input('welcome_headline', rows.welcome_headline, 'class="form-control"') }}
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <label class="col-md-4 control-label">
                    {{ lang('welcome_text') }}
                </label>
                <div class="col-md-5">
                    {{ form_input('welcome_text', rows.welcome_text, 'class="form-control"') }}
                </div>
            </div>
            <hr/>
            {% if config_enabled('module_affiliate_marketing_affiliate_stores_allow_affiliate_select_background') %}
                <h4>{{ lang('select_header_background') }}</h4>
                <hr/>
                {% if backgrounds %}
                    <div class="row">
                    <div class="images cold-md-12">
                    {% for b in backgrounds %}
                        <div class="w-25 float-left">
                            <input id="{{ b }}" type="radio" name="header_image" value="{{ base_url('images/uploads/backgrounds/'~b) }}" {% if rows.header_image ==  base_url('images/uploads/backgrounds/'~b) %}checked="checked"{% endif %}/>
                            <label class="header-cc" for="{{ b }}"
                                   style="background-image:url('{{ profile_photo(b) }}')"></label>
                        </div>
                    {% endfor %}
                    </div>
                    </div>
                    <hr />
                {% endif %}
            {% endif %}
            <div class="form-group row">
                <div class="col-md-12 text-sm-right">
                    <button type="submit" class="btn btn-primary">
                        {{ i('fa fa-refresh') }} {{ lang('save_changes') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    {{ form_hidden('member_id', sess('member_id')) }}
    {{ form_close() }}
{% else %}
    {{ form_open('members/affiliate_marketing/module/'~id~'/activate', 'role="form" id="form" class="form-horizontal"') }}
    {{ form_hidden('activate', 1) }}
    <div class="alert alert-info">
        {{ i('fa fa-info-circle') }} {{ lang('affiliate_stores_members_description') }}
    </div>
    <button type="submit" class="btn btn-primary">
        {{ i('fa fa-angle-double-right') }} {{ lang('activate_your_store') }}
    </button>
    {{ form_close() }}
{% endif %}
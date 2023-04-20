{% extends "global/base.tpl" %}
{% block title %}{{ lang('site_map') }}{% endblock %}
{% block meta_description %}{{ lang('site_map') }}{% endblock meta_description %}
{% block page_header %}
    <div id="blog-list-header" class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">{{ lang('site_map') }}</h2>
            </div>

        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div class="sitemap">
        {{ breadcrumb }}
        <div class="row">
            <div class="col-4">
                <ul class="list-group">
                    {% if (config_enabled('sts_store_enable')) %}
                    <li class="list-group-item">{{ i('fa fa-angle-right') }} {{ anchor(site_url('store'), lang('store')) }}
                        <br/>
                        <small class="text-muted">{{ lang('view_our_store') }}</small>
                    </li>
                    <li class="list-group-item">{{ i('fa fa-angle-right') }} {{ anchor(site_url('product_categories'), lang('product_categories')) }}
                        <br/>
                        <small class="text-muted">{{ lang('view_our_product_categories') }}</small>
                    </li>
                    <li class="list-group-item">{{ i('fa fa-angle-right') }} {{ anchor(site_url('brands'), lang('brands')) }}
                        <br/>
                        <small class="text-muted">{{ lang('view_brands_we_sell') }}</small>
                    </li>
                    {% endif %}
                    <li class="list-group-item">{{ i('fa fa-angle-right') }} {{ anchor(site_url('login'), lang('members_area')) }}
                        <br/>
                        <small class="text-muted">{{ lang('login_to_our_members_area') }}</small>
                    </li>
                </ul>
            </div>
            <div class="col-4">
                <ul class="list-group">
                    <li class="list-group-item">{{ i('fa fa-angle-right') }} {{ anchor(site_url('contact'), lang('contact_us')) }}
                        <br/>
                        <small class="text-muted">{{ lang('need_to_contact_us_click_here') }}</small>
                    </li>
                    <li class="list-group-item">{{ i('fa fa-angle-right') }} {{ anchor(site_url('login/reset_password'), lang('reset_password')) }}
                        <br/>
                        <small class="text-muted">{{ lang('need_to_reset_your_members_area_password_click_here') }}</small>
                    </li>
                    <li class="list-group-item">{{ i('fa fa-angle-right') }} {{ anchor(site_url('terms_of_service'), lang('terms_of_service')) }}
                        <br/>
                        <small class="text-muted">{{ lang('read_our_terms_of_service') }}</small>
                    </li>
                    <li class="list-group-item">{{ i('fa fa-angle-right') }} {{ anchor(site_url('privacy_policy'), lang('privacy_policy')) }}
                        <br/>
                        <small class="text-muted">{{ lang('review_our_privacy_policy') }}</small>
                    </li>
                </ul>
            </div>
            <div class="col-4">
                <ul class="list-group">
                    {% if (config_enabled('sts_blog_enable')) %}
                    <li class="list-group-item">{{ i('fa fa-angle-right') }} {{ anchor(site_url('blog'), lang('blog')) }}
                        <br/>
                        <small class="text-muted">{{ lang('read_our_blog') }}</small>
                    </li>
                    {% endif %}
                    <li class="list-group-item">{{ i('fa fa-angle-right') }} {{ anchor(site_url('faq'), lang('faq')) }}
                        <br/>
                        <small class="text-muted">{{ lang('view_our_frequently_asked_questions') }}</small>
                    </li>
                    {% if (config_enabled('sts_forum_enable')) %}
                    <li class="list-group-item">{{ i('fa fa-angle-right') }} {{ anchor(site_url('forum'), lang('forum')) }}
                        <br/>
                        <small class="text-muted">{{ lang('visit_with_our_community_forum_members') }}</small>
                    </li>
                    {% endif %}
                    {% if (config_enabled('sts_kb_enable')) %}
                    <li class="list-group-item">{{ i('fa fa-angle-right') }} {{ anchor(site_url('kb'), lang('kb')) }}
                        <br/>
                        <small class="text-muted">{{ lang('read_our_knowledgebase') }}</small>
                    </li>
                    {% endif %}
                </ul>
            </div>
        </div>
    </div>
{% endblock content %}
{% block javascript_footer %}
    {{ parent() }}

{% endblock javascript_footer %}
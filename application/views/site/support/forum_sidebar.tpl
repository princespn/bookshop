<div class="col-md-3">
    {% if config_enabled('sts_site_refer_friend_enable') %}
        <div class="card mb-3">
            <div class="card-header">{{ lang('share_with_your_friends') }}</div>
            <div class="card-body text-md-center">
                {{ html_decode(config_option('sts_site_refer_friend_code')) }}
            </div>
        </div>
    {% endif %}
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">
                {% if sess('user_logged_in') %}
                {{ lang('hello') }} {{ sess('fname') }}!
                {% else %}
                {{ lang('community_access') }}
                {% endif %}
            </h5><hr />
            <p>
            {% if sess('user_logged_in') %}
                <a href="{{ site_url(forum_uri~'/add_topic/'~id) }}" class="btn btn-block btn-primary">
                    {{ i('fa fa-plus') }} {{ lang('add_new_topic') }}</a>
            {% else %}
                <a href="{{ site_url('login') }}?redirect={{ site_url(forum_uri) }}"
                   class="btn btn-block btn-primary">
                    {{ i('fa fa-lock') }} {{ lang('login') }}</a>
                <a href="{{ site_url('register') }}" class="btn btn-block btn-secondary">
                    {{ i('fa fa-user') }} {{ lang('register_now') }}</a>
            {% endif %}
            </p>
            {% if sess('user_logged_in') == false %}
            {% if config_enabled('layout_design_login_enable_social_login') %}
            <p class="text-center">- {{ lang('sign_in_using') }} - </p>
            <div class="text-center">
            {% if config_enabled('layout_design_login_enable_facebook_login') %}
                <a href="{{ site_url('login/social/Facebook') }}"
                   class="btn btn-facebook btn-icon">{{ i('fa fa-facebook') }}</a>
            {% endif %}
            {% if config_enabled('layout_design_login_enable_twitter_login') %}
                <a href="{{ site_url('login/social/Twitter') }}"
                   class="btn btn-twitter btn-icon">{{ i('fa fa-twitter') }}</a>
            {% endif %}
            {% if config_enabled('layout_design_login_enable_google_login') %}
                <a href="{{ site_url('login/social/Google') }}"
                   class="btn btn-google-plus btn-icon">{{ i('fa fa-google') }}</a>
            {% endif %}
            </div>
            {% endif %}
            {% endif %}
        </div>
    </div>
    {% if forum_categories %}
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">{{ lang('categories') }}</h5>
                <hr />
                {% for p in forum_categories %}
                    <div class="row">
                        <div class="col-10">
                            <strong>
                                <a href="{{ site_url }}{{forum_uri}}/topics/{{ p.category_url }}">
                                    {{ i('fa fa-folder-o') }} {{ p.category_name }}</a>
                            </strong><br />
                            <small>{{ p.description }}</small>
                        </div>
                        <div class="col-1 text-sm-center">
                            <h4 class="badge badge-light">{{ p.topics }}</h4>
                        </div>
                    </div>
                    {% if loop.last == false %}
                        <hr/>
                    {% endif %}
                {% endfor %}
            </div>
        </div>
    {% endif %}
</div>
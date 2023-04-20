{% if member_logged_in %}
    <li class="nav-item dropdown ">
        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-user"></i> <span
                    class="d-inline-block">{{ lang('my_account') }}
                <span class="caret"></span></span>
        </a>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <a class="dropdown-item" href="{{ ssl_url('members') }}">
                {{ i('fa fa-file-text-o') }} {{ lang('client_area') }}</a>
            <a class="dropdown-item" href="{{ ssl_url('members/account#reset_password') }}">
                {{ i('fa fa-key') }} {{ lang('reset_password') }}</a>
            {% if config_enabled('sts_site_enable_wish_lists') %}
                <a class="dropdown-item" href="{{ site_url('wish_list') }}/{{ sess('username') }}">
                    {{ i('fa fa-magic') }} {{ lang('wish_list') }}</a>
            {% endif %}
        </div>
    </li>
    <li class="nav-item"><a href="{{ ssl_url('logout') }}" class="nav-link">
            <i class="fa fa-unlock"></i> <span
                    class="d-inline-block">{{ lang('logout') }}</span></a></li>
{% else %}

    <li class="nav-item"><a href="{{ ssl_url('login') }}" class="nav-link">
            <i class="fa fa-lock"></i> <span
                    class="d-inline-block">{{ lang('login') }}</span></a></li>
    <li class="nav-item"><a href="{{ ssl_url('register') }}" class="nav-link">
            <i class="fa fa-user"></i> <span
                    class="d-inline-block">{{ lang('register') }}</span></a></li>
{% endif %}
{% if sts_site_facebook_url %}
{% if sts_content_facebook_comments_app_id %}
<li class="nav-item">
    <iframe src="//www.facebook.com/plugins/like.php?href={{ sts_site_facebook_url|escape }}&amp;width&amp;layout=button&amp;action=like&amp;show_faces=false&amp;share=false&amp;height=100&amp;width=50&amp;appId={{sts_content_facebook_comments_app_id}}" style="border: none; padding: 10px 0px 0 10px;overflow:hidden; height:30px; width: 70px; "></iframe>
</li>
{% endif %}
{% endif %}


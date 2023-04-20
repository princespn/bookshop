<div class="jrox cpp">
    <div class="container">
        <div class="row">
            <div class="col-sm-8">
                <ul class="list-inline">
                    <li>&copy; {{ 'now'|date('Y') }} {{ sts_site_name }} {{ lang('all_rights_reserved') }}</li>
                    <li><a href="{{ site_url('tos') }}">{{ lang('terms_of_service') }}</a></li>
                    <li><a href="{{ site_url('privacy_policy') }}">{{ lang('privacy_policy') }}</a></li>
                    <li><a href="{{ site_url('site_map') }}">{{ lang('site_map') }}</a></li>
                </ul>
            </div>
            <div class="col-sm-4 text-sm-right">
                {# Removing the poweredby line requires a valid copyright license.  Taking it out
                constitutes a violation in the End User License Agreement... #}
                {% include ('global/cp.tpl') %}
            </div>
        </div>
    </div>
</div>
{% if check_cookie_consent() %}
<div id="cookie-consent" class="collapse show">
    <nav class="navbar animated fadeInUp fixed-bottom navbar-dark bg-info">
                            <span class="navbar-text">
                                {{ i('fa fa-info-circle') }} {{ lang('website_uses_cookies') }}
                                <a href="{{ site_url('privacy_policy') }}">{{ lang('cookie_privacy_policy') }}</a>
                            </span>
        <a id="cookie-btn" class="btn btn-danger ml-auto btn-block-sm" data-toggle="collapse" href="#cookie-consent" role="button" aria-expanded="false" aria-controls="cookie-consent">{{ i('fa fa-check') }} {{ lang('got_it') }}</a>
    </nav>
</div>

<script>
    $("#cookie-btn").click(function(){
        document.cookie = "cookie_consent=Yes; expires={{ set_date('1') }} UTC; path=/";
    });
</script>
{% endif %}
{% if config_enabled('sts_site_refer_friend_enable') %}
{{ html_decode(config_option('sts_site_refer_friend_code')) }}
{% endif %}
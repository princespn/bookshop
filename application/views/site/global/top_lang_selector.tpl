{% if sts_site_enable_language_selector == 1 %}
<li class="nav-item dropdown ">
    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
      {% if (default_language) %}
        <i class="flag-{{default_lang_image}}"></i>
        <span>{{ default_language }}
            <span class="caret"></span></span>
        {% else %}
        <i class="flag-{{sts_site_default_language_image}}"></i>
        <span>{{ sts_site_default_language_name }}
            <span class="caret"></span></span>
        {% endif %}
    </a>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        {% for lang in site_languages %}
        <a class="dropdown-item"
           href="{{ site_url }}language/update/{{ lang.name }}"><i
                    class="flag-{{ lang.image }}"></i> {{ lang.name }}</a>
        {% endfor %}
    </div>
</li>
{% endif %}
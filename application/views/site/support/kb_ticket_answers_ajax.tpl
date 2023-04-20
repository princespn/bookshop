{% if articles %}
<div class="animated fadeIn">
<strong>{{ lang('some_possible_answers_from_kb') }}</strong>
{% for p in articles %}
    <div>
        <a href="{{ site_url }}{{kb_uri}}/article/{{ p.url }}">{{ p.kb_title }}</a></><br />
        <small class="text-muted">{{ p.overview }}</small>
    </div>
{% endfor %}
</div>
{% endif %}
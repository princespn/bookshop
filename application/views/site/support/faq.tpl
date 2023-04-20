{% extends "global/base.tpl" %}
{% block title %}{{ parent() }} {{ lang('frequently_asked_questions') }}{% endblock %}
{% block meta_description %}{{ parent() }} {{ lang('frequently_asked_questions') }} {% endblock meta_description %}
{% block meta_keywords %}{{ parent() }} {{ lang('frequently_asked_questions') }} {% endblock meta_keywords %}
{% block page_header %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">{{ lang('frequently_asked_questions') }}</h2>
            </div>

        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div class="product-list">
        {{ breadcrumb }}
        <div class="row">
            <div class="col-md-12">
                {% if faqs %}
                    <div class="faq">
                        {% for p in faqs %}
                            <div id="faq-{{ p.faq_id }}">
                                <h4><a class="faq-question cursor" data-toggle="collapse"
                                       data-target="#faq-answer-{{ p.faq_id }}">{{ p.question }}</a>
                                </h4>
                                <p id="faq-answer-{{ p.faq_id }}" class="faq-answer collapse show">
                                    {{ p.answer }}
                                </p>
                            </div>
                        {% endfor %}
                    </div>
                {%  else %}
                    <div role="alert" class="alert alert-info">
                        <h4>{{ lang('no_faqs_found') }}</h4>
                    </div>
                {% endif %}
            </div>
        </div>
    </div>
{% endblock content %}
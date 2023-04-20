<div class="footer">
    {% block footer %}
        {% block footer_menu %}
            <footer>
                {% if config_enabled('layout_design_global_show_footer') %}
                {% include ('global/footer_menu.tpl') %}
                {% endif %}
            </footer>
        {% endblock footer_menu %}
    {% endblock footer %}
    {% include ('global/privacy.tpl') %}
</div>
<!-- {{ app_revision }} -->
<div class="top-menu-box">
    <div class="container">
        {% block header %}
        {% block logo %}
        <div class="row">
            <div class="col-md-4 col-lg-3">
                <div class="logo">
                    <a href="{{ site_url() }}">
                        {% if layout_design_site_logo %}
                        <img src="{{ layout_design_site_logo }}" alt="{{ lang('logo') }}"
                             class="img-fluid logo"/>
                        {% else %}
                        <h4>{{ sts_site_name }}</h4>
                        {% endif %}
                    </a>
                </div>
            </div>
            <div class="col-md-8 col-lg-9">
                {% block top_menu %}
                <div class="top-menu">
                    <nav class="nav navbar navbar-white navbar-toggleable-sm d-none d-md-block">
                        <div id="top-menu-bar">
                            {{ format_menu(top_menu) }}
                        </div>
                    </nav>
                </div> <!-- /.top-menu -->
                {% endblock top_menu %}
            </div>
        </div>
        {% endblock logo %}
        {% endblock header %}
    </div>
</div>
<div class="container">
    {% block header %}
    {% block logo %}
    <div class="row">
        <div class="col-md-8 col-lg-12">
            {% block top_menu %}
            <div class="top-menu d-flex justify-content-center">
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
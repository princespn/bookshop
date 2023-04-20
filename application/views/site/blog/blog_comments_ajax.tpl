{% block content %}
    <div class="row">
        <div class="col-md-12">
            <div class="comments">
                <h3 class="min-pad-bottom">{{ lang('comments') }}</h3>
                {% if comments %}
                    {% for r in comments %}
                        <div id="comment-{{ r.id }}" class="min-pad-bottom">
                            <div class="row">
                                <div class="{{ r.type }}-icon col-md-1 col-sm-2 d-none d-sm-block text-sm-center">

                                        {% if r.type == 'admin' %}
                                            {{ image('admin', r.admin_photo, r.admin_username, 'img-thumbnail rounded-circle', FALSE) }}
                                        {% else %}
                                            {{ image('blog', r.profile_photo, r.fname, 'img-thumbnail rounded-circle', FALSE) }}
                                        {% endif %}

                                </div>
                                <div class="col-md-11 col-sm-10">
                                    <p>
                                        <small>
                                            {% if r.type == 'admin' %}
                                                <strong>{{ r.admin_fname }} {{ r.admin_lname }}</strong>
                                            {% else %}
                                                <strong>{{ r.name }}</strong>
                                            {% endif %}

                                            <span class="float-right text-muted">{{ local_date(r.date) }}</span>
                                        </small>
                                    </p>
                                    <div class="minimize comment-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p class="minimize">{{ r.comment }}</p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 text-sm-right">
                                                <a href="#" onclick="add_comment('{{ r.id }}')"
                                                   class="btn btn-sm btn-outline-secondary btn-comment-reply">
                                                    {{ lang('reply') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {% if r.sub %}
                            {% for s in r.sub %}
                                <div id="comment-{{ s.id }}" class="min-pad-bottom">
                                    <div class="row">
                                        <div class="{{ s.type }}-icon col-md-1 d-none d-sm-block offset-md-1 col-sm-2 offset-sm-1 text-sm-center">

                                                {% if s.type == 'admin' %}
                                                    {{ image('admin', s.admin_photo, s.admin_username, 'img-thumbnail rounded-circle', FALSE) }}
                                                {% else %}
                                                    {{ image('blog', s.profile_photo, s.fname, 'img-thumbnail rounded-circle', FALSE) }}
                                                {% endif %}

                                        </div>
                                        <div class="col-md-10 col-sm-9 ">
                                            <p>
                                                <small>
                                                    {% if s.type == 'admin' %}
                                                        <strong>{{ s.admin_fname }} {{ s.admin_lname }}</strong>
                                                    {% else %}
                                                        <strong>{{ s.name }}</strong>
                                                    {% endif %}

                                                    <span class="float-right text-muted">{{ local_date(r.date) }}</span>
                                                </small>
                                            </p>
                                            <div class="minimize comment-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <p class="minimize">{{ s.comment }}</p>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12 text-sm-right">
                                                        <a href="#" onclick="add_comment('{{ r.id }}')"
                                                           class="btn btn-sm btn-outline-secondary btn-comment-reply">
                                                            {{ lang('reply') }}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            {% endfor %}
                        {% endif %}
                    {% endfor %}
                {% endif %}
            </div>
        </div>
    </div>
{% endblock content %}
{% block javascript_footer %}

    <script>
        jQuery(function () {
            var minimized_elements = $('p.minimize');
            var min_length = 300;
            minimized_elements.each(function () {
                var t = $(this).text();
                if (t.length < min_length) return;

                $(this).html(
                        t.slice(0, min_length) + '<span>... </span><a href="#" class="more">{{ lang('more') }}</a>' +
                        '<span style="display:none;">' + t.slice(min_length, t.length) + ' <a href="#" class="less">{{ lang('less') }}</a></span>'
                );

            });

            $('a.more', minimized_elements).click(function (event) {
                event.preventDefault();
                $(this).hide().prev().hide();
                $(this).next().show();
            });

            $('a.less', minimized_elements).click(function (event) {
                event.preventDefault();
                $(this).parent().hide().prev().show().prev().show();
            });
        });

        function add_comment(id) {
            $('#parent_id').val(id);
            $("html, body").animate({scrollTop: $(document).height()}, "slow");
            return false;
        }

    </script>

{% endblock javascript_footer %}
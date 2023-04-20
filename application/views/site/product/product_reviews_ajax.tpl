{% block body %}
    {%  if reviews %}
        {% for r in reviews %}
            <div class="row">
                <div class="user-icon col-1 text-sm-center">
                    {{ image('members', r.profile_photo, r.username, 'img-thumbnail rounded-circle d-none d-sm-block', FALSE) }}
                </div>
                <div class="col-11">
                    <h5>{{ r.title }}
                        <div class="star-rating float-lg-right">  {{ format_ratings(r.ratings)}}</div>
                    </h5>
                    <div class="box-meta">
                            <ul class="list-inline">
                                <li>{{ i('fa fa-user') }} {{ lang('by') }} {{ format_name(r.username) }}</li>
                                <li class="d-none d-md-inline-block">{{ i('fa fa-clock-o') }} {{ lang('posted_on') }} {{ display_date(r.date, FALSE, 3) }}</li>
                            </ul>
                    </div>
                    <p class="minimize">{{ html_entity_decode(r.comment|nl2br) }}</p>
                </div>
            </div>
            <hr />
        {% endfor %}
        <div class="row">
            <div class="col-md-12 text-sm-right">
                <a href="{{ site_url }}product_reviews/view/{{ id }}" class="btn btn-lg btn-secondary">{{ i('fa fa-search') }} {{ lang('read_more_reviews') }}</a>
            </div>
        </div>
    {%  else %}
        <div class="alert alert-info" role="alert">
            <strong>{{ lang('no_reviews_yet') }} - </strong>
            {%  if member_logged_in == 1 %}
                <a href="{{ site_url }}product_reviews/add/{{ id }}"> {{ lang('be_first_write_review') }}</a>
            {% else %}
                <a data-toggle="modal" data-target="#login-modal" href="#">{{ lang('login_to_add_your_review') }}</a>
            {% endif %}

        </div>
    {% endif %}
{% endblock body %}
{% block javascript_footer %}
    <script>
        jQuery(function(){

            var minimized_elements = $('p.minimize');
            var min_length = 300;
            minimized_elements.each(function(){
                var t = $(this).text();
                if(t.length < min_length) return;

                $(this).html(
                        t.slice(0,min_length)+'<span>... </span><a href="#" class="more">{{ lang('more') }}</a>'+
                                '<span style="display:none;">'+ t.slice(min_length,t.length)+' <a href="#" class="less">{{ lang('less') }}</a></span>'
                );

            });

            $('a.more', minimized_elements).click(function(event){
                event.preventDefault();
                $(this).hide().prev().hide();
                $(this).next().show();
            });

            $('a.less', minimized_elements).click(function(event){
                event.preventDefault();
                $(this).parent().hide().prev().show().prev().show();
            });

        });
    </script>
{% endblock javascript_footer %}
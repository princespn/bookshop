{% if sts_content_enable_comments %}
<div class="comments-box">
    <div class="row">
        <div class="col-md-12">
            {% if p.enable_comments %}
            <div id="comments-box">
                {% if sts_content_enable_comments  == '2' %}
                <div id="disqus_thread"></div>
                <script>

                    var disqus_config = function () {
                    this.page.url = '{{current_url()}}';
                    this.page.identifier = '{{blog_id}}';
                    };

                    (function() { // DON'T EDIT BELOW THIS LINE
                        var d = document, s = d.createElement('script');
                        s.src = 'https://{{sts_content_disqus_shortname}}.disqus.com/embed.js';
                        s.setAttribute('data-timestamp', +new Date());
                        (d.head || d.body).appendChild(s);
                    })();
                </script>
                <noscript>Please enable JavaScript to view the comments</noscript>
                {% elseif sts_content_enable_comments == '3' %}
                <div class="fb-comments" data-href="{{current_url()}}" data-width="100%" data-numposts="10"></div>
                {% endif %}
            </div>
            <a id="comments"></a>
            {% if config_enabled('sts_content_require_login_comment') and member_logged_in == 0 %}
            <div class="row pad-bottom-40">
                <div class="col-md-12 text-sm-center">
                    <a href="{{ site_url('login') }}"
                       class="btn btn-primary btn-lg">{{ i('fa fa-lock') }} {{ lang('login_to_comment') }}</a>
                </div>
            </div>
            {% else %}
            {% if sts_content_enable_comments  == '1' %}
            <div class="add-comment card">
                <div class="card-body">
                    {{ form_open('blog/add_comment/'~p.blog_id, 'id="form"') }}
                    <h5>{{ i('fa fa-pencil') }} {{ lang('add_your_comment') }}</h5>
                    <hr/>
                    <div class="row">
                        <div class="col-md-6">
                            <fieldset class="form-group">
                                <input type="text" name="name" value="{{ sess('fname') }}"
                                       class="form-control required" required
                                       placeholder="{{ lang('enter_your_name') }}">
                            </fieldset>
                        </div>
                        <div class="col-md-6">
                            <fieldset class="form-group">
                                <input type="email" name="email"
                                       value="{{ sess('primary_email') }}"
                                       class="form-control required email" required
                                       placeholder="{{ lang('user@domain.com') }}">
                            </fieldset>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <fieldset class="form-group">
                                                            <textarea name="comment" rows="5" id="comment"
                                                                      class="form-control required"
                                                                      placeholder="{{ lang('comments') }}"></textarea>
                            </fieldset>
                        </div>
                    </div>
                    {% if config_enabled('sts_form_enable_blog_captcha') %}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="g-recaptcha"
                                 data-sitekey="{{ sts_form_captcha_key }}"></div>
                        </div>
                    </div>
                    {% endif %}
                    <hr/>
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-lg btn-primary">{{ i('fa fa-refresh') }} {{ lang('submit') }}
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="blog_id" value="{{ p.blog_id }}"/>
                    <input type="hidden" name="parent_id" value="0" id="parent_id"/>
                    {% if member_logged_in == 1 %}
                    {{ form_hidden('user_id', sess('member_id')) }}
                    {% endif %}
                    {{ form_close() }}
                </div>
            </div>
            {% endif %}
            {% endif %}
            {% else %}
            <div class="alert alert-info">
                {{ i('fa fa-info-circle') }} {{ lang('comments_have_been_disabled_for_this_post') }}
            </div>
            {% endif %}
        </div>
    </div>
</div>
{% endif %}
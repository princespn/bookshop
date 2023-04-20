{% block login_modal %}
<div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="modal-title">
    <div class="modal-dialog">
        {{ form_open(site_url('login'), ' id="login-form"') }}
        <div class="modal-content">
            <div class="modal-body">
                <h4 id="modal-title"><i class="fa fa-lock"></i>
                    {{ lang('account_login') }}
                    {% if require_user_login() %}
                    {{ lang('required') }}
                    {% endif %}
                </h4>
                <hr/>
                {% if config_enabled('sts_site_require_login') %}
                <p class="lead">{{ lang('account_login_required_to_continue') }}</p>
                {% endif %}
                <div class="form-group row">
                    <label
                            class="col-md-4 control-label">{{ lang('email_address') }}</label>

                    <div class="col-md-8">
                        <input type="text" name="{{ user_login_email_field }}" placeholder="you@domain.com"
                               class=" form-control required email"/>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-4 control-label">{{ lang('password') }}</label>
                    <div class="col-md-8">
                        <input type="password" name="{{ user_login_password_field }}" placeholder="password"
                               class=" form-control required"/>
                    </div>
                </div>
                {% if config_enabled('sts_form_enable_login_captcha') %}
                <div class="form-group row">
                    <label class="col-md-4 control-label">{{ lang('check_captcha') }}</label>
                    <div class="col-8">
                        <div class="g-recaptcha" data-sitekey="{{ sts_form_captcha_key }}"></div>
                    </div>
                </div>
                <hr/>
                {% endif %}
                <div class="form-group row">
                    <div class="col-6 offset-3">
                        <button type="submit"
                                class="btn btn-primary btn-block">{{ i('fa fa-lock') }} {{ lang('login') }}
                        </button>
                    </div>
                </div>
                {% if config_enabled('layout_design_login_enable_social_login') %}
                <div class="form-group row">
                    <label class="col-3 col-form-label">{{ lang('sign_in_using') }}</label>
                    <div class="col-9">
                        {% if config_enabled('layout_design_login_enable_facebook_login') %}
                        <a href="{{ site_url('login/social/Facebook') }}?redirect={{ current_url() }}"
                           class="btn btn-facebook btn-icon">{{ i('fa fa-facebook') }} Facebook</a>
                        {% endif %}
                        {% if config_enabled('layout_design_login_enable_twitter_login') %}
                        <a href="{{ site_url('login/social/Twitter') }}?redirect=checkout"
                           class="btn btn-twitter btn-icon">{{ i('fa fa-twitter') }} Twitter</a>
                        {% endif %}
                        {% if config_enabled('layout_design_login_enable_google_login') %}
                        <a href="{{ site_url('login/social/Google') }}?redirect=checkout"
                           class="btn btn-google-plus btn-icon">{{ i('fa fa-google') }} Google</a>
                        {% endif %}

                    </div>
                </div>
                {% endif %}
                <hr />
                <div class="row">
                    <div class="col-md-12 text-md-right">
                        <a href="{{ site_url('login/reset_password') }}"
                           class="btn btn-outline-secondary btn-block-sm">{{ i('fa fa-undo') }} {{ lang('reset_password') }}</a>
                        <a href="{{ site_url('register') }}"
                           class="btn btn-primary btn-block-sm">{{ i('fa fa-user') }} {{ lang('create_account') }}</a>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="redirect" value="{{ current_url() }}"/>
        {{ form_close() }}
    </div>
</div>
{% endblock login_modal %}
{% include 'form/timer_modal.tpl' %}
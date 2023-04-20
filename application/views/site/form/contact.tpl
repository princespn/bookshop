{% extends "global/base.tpl" %}
{% block title %}{{ lang('contact_us') }}{% endblock %}
{% block meta_description %}{{ lang('contact_us') }}{% endblock meta_description %}
{% block meta_robots %}noindex, nofollow{% endblock meta_robots %}
{% block page_header %}
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2 class="headline">{{ lang('contact_us') }}</h2>
        </div>

    </div>
</div>
{% endblock page_header %}
{% block content %}
<div class="contact">
    {{ breadcrumb }}
    <div class="row">
        <div class="col-md-7">
            {{ form_open('', 'id="contact-form"') }}
            <div class="contact">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">{{ i('fa fa-envelope') }} {{ lang('send_us_message') }}</h3>
                        <hr/>
                        {% if (fields.values) %}
                        {% for s in fields.values %}
                        {% if s.field_type != 'hidden' %}
                        <div class="form-group row">
                            <label for="{{ s.form_field }}" class="col-sm-4 col-form-label text-sm-right">
                                {{ s.field_name }}
                            </label>
                            <div class="col-sm-8">{{ s.field }}</div>
                        </div>
                        {% else %}
                        {{ s.field }}
                        {% endif %}
                        {% endfor %}
                        {% endif %}
                        {% if config_enabled('sts_form_enable_contact_captcha') %}
                        <div class="form-group  row">
                            <label class="col-sm-4 col-form-label text-sm-right">
                                {{ lang('security_captcha') }}
                            </label>
                            <div class="col-sm-8">
                                <div class="g-recaptcha" data-sitekey="{{ sts_form_captcha_key }}"></div>
                            </div>
                        </div>

                        {% endif %}
                        <div class="form-group  row">
                            <div class="offset-md-4 col-md-8">
                                <button type="submit" id="submit-button" class="btn-lg btn btn-primary btn-block-sm">
                                    <span id="submit-span"> {{ i('fa fa-refresh') }} {{ lang('submit') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{ form_close() }}
        </div>
        <div class="col-md-5">
            {% if (config_enabled('layout_design_contact_show_social_icons')) %}
            <div class="card mb-3 contact-description">
                <div class="card-body">
                    <h3 class="card-title">
                        {{ i('fa fa-share') }} {{ lang('get_connected') }}
                    </h3>
                    <hr/>
                    <p class="card-text text-md-center">
                        {% if (sts_site_youtube_url) %}
                        <a href="{{ sts_site_youtube_url }}"
                           class="btn btn-danger btn-icon">{{ i('fa fa-youtube-play') }}</a>
                        {% endif %}
                        {% if (sts_site_facebook_url) %}
                        <a href="{{ sts_site_facebook_url }}"
                           class="btn btn-facebook btn-icon">{{ i('fa fa-facebook') }}</a>
                        {% endif %}
                        {% if (sts_site_twitter_url) %}
                        <a href="{{ sts_site_twitter_url }}"
                           class="btn btn-twitter btn-icon">{{ i('fa fa-twitter') }}</a>
                        {% endif %}
                        {% if (sts_site_instagram_url) %}
                        <a href="{{ sts_site_instagram_url }}"
                           class="btn btn-instagram btn-icon">{{ i('fa fa-instagram') }}</a>
                        {% endif %}
                        {% if (sts_site_pinterest_url) %}
                        <a href="{{ sts_site_pinterest_url }}"
                           class="btn btn-pinterest btn-icon">{{ i('fa fa-pinterest') }}</a>
                        {% endif %}
                        {% if (sts_site_linkedin_url) %}
                        <a href="{{ sts_site_linkedin_url }}"
                           class="btn btn-linkedin btn-icon">{{ i('fa fa-linkedin') }}</a>
                        {% endif %}
                    </p>
                </div>
            </div>
            {% endif %}
            {% if (config_enabled('layout_design_contact_show_phone')) %}
            <div class="card mb-3 contact-description">
                <div class="card-body">
                    <h3 class="card-title">
                        {{ i('fa fa-phone') }} {{ lang('give_us_a_call') }}
                    </h3>
                    <hr/>
                    <h5 class="card-text">{{ sts_site_phone_number }}</h5>
                </div>
            </div>
            {% endif %}
            {% if (config_enabled('layout_design_contact_show_locations_link')) %}
            <div class="card mb-3 contact-description">
                <div class="card-body">
                    <h3 class="card-title">
                        {{ i('fa fa-map-marker') }} {{ lang('store_locations') }}
                    </h3>
                    <hr/>
                    <h5 class="card-text"><a href="locations">{{ lang('view_our_other_locations') }}</a></p>
                </div>
            </div>
            {% endif %}
            {% if (config_enabled('layout_design_contact_show_mailing_address')) %}
            <div class="card contact-description">
                <div class="card-body">
                    <h3 class="card-title">
                        {{ i('fa fa-location-arrow') }} {{ lang('mailing_address') }}
                    </h3>
                    <hr/>
                    <p class="card-text">
                    <address>
                        <strong>{{ sts_site_name }}</strong><br/>
                        {{ sts_site_shipping_name }}<br/>
                        {{ sts_site_shipping_address_1 }}<br/>
                        {{ sts_site_shipping_city }} {{ sts_site_shipping_region_name }}
                        {{ sts_site_shipping_postal_code }}
                        <br/>
                        {{ sts_site_shipping_country_name }}
                    </address>
                    {% if (config_enabled('layout_design_contact_show_map')) %}
                    <div class="google-map">
                        <address id="gmap">
                            {{ sts_site_shipping_address_1 }}<br/>
                            {{ sts_site_shipping_city }} {{ sts_site_shipping_region_name }}
                            {{ sts_site_shipping_postal_code }}
                            {{ sts_site_shipping_country_name }}
                        </address>
                    </div>
                    {% endif %}
                    </p>
                </div>
            </div>
            {% endif %}
        </div>
    </div>
</div>
{% endblock content %}
{% block javascript_footer %}
{{ parent() }}
<script>
    $("#contact-form").validate({
        submitHandler: function (form) {
            form.submit();
            $('#submit-span').html('{{ i('fa fa-spinner fa-spin')}} {{ lang('please_wait') }}');
        }
    });
    {% if (config_enabled('layout_design_contact_show_map')) %}
    $(document).ready(function () {
        $("#gmap").each(function () {
            var embed = "<iframe width='100%' height='350' frameborder='0' scrolling='no'  marginheight='0' marginwidth='0'   src='https://maps.google.com/maps?&amp;q=" + encodeURIComponent($(this).text()) + "&amp;output=embed'></iframe>";
            $(this).html(embed);
        });
    });
    {% endif %}
</script>
{% endblock javascript_footer %}
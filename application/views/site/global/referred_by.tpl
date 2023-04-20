{% if show_profile() %}
<div class="card product-referred-by">
    <div class="card-body">
        <h5 class="card-title">{{ lang('referred_by') }}
            {% if (config_option('sts_site_enable_user_profiles')) %}
            <a href="{{ site_url('profile') }}/{{ affiliate_data.username }}">
                {{ affiliate_data.fname }} {{ affiliate_data.lname }}</a>
            {% else %}
            {{ affiliate_data.fname }} {{ affiliate_data.lname }}
            {% endif %}
        </h5>
        {% if config_option('affiliate_data', 'profile_photo') %}
        <hr/>
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <p class="card-text"><img class="card-img-top img-thumbnail mx-auto d-block"
                                          src="{{ config_option('affiliate_data', 'profile_photo') }}"/></p>
            </div>
        </div>
        {% endif %}
    </div>
{% endif %}
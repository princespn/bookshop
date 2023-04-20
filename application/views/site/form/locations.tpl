{% extends "global/base.tpl" %}
{% block title %}{{ lang('locations') }}{% endblock %}
{% block meta_description %}{{ lang('locations') }}{% endblock meta_description %}
{% block page_header %}
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2 class="headline">{{ lang('our_locations') }}</h2>
        </div>
    </div>
</div>
{% endblock page_header %}
{% block content %}
<div class="locations">
    {{ breadcrumb }}
    <div class="row">
        <div class="col-md-4">
            {% if (addresses) %}
            {% for s in addresses %}
            <div class="location card">
                <div class="card-body">
                    <address id="location-{{loop.index}}">
                        <h5 class="card-title">{{ i('fa fa-map-marker') }} {{  s.name}}</h5>
                        <p class="cursor add" onclick="load_location('{{ s.address_1|url_encode }}{% if s.address_2 %}+{{ s.address_2|url_encode }}{% endif %}+{{ s.city|url_encode }}+{{ s.region_name|url_encode }}+{{ s.postal_code|url_encode }}+{{ s.country_name|url_encode }}')">
                        {{ s.address_1 }}<br/>
                        {% if s.address_2 %}
                        {{ s.address_2 }}<br/>
                        {% endif %}
                        {{ s.city }} {{ s.region_code }} {{ s.postal_code }} {{ s.country_iso_code_3 }}<br/>
                        {{ i('fa fa-phone') }} {{ s.phone }}
                        </p>
                    </address>
                </div>
            </div>
            {% endfor %}
            {% endif %}
        </div>
        <div class="col-md-8">
            <div class="google-map">
                <address id="gmap">
                    <iframe width='100%' height='500' id='iframe-map' frameborder='0' scrolling='no'  marginheight='0' marginwidth='0' src='https://maps.google.com/maps?q="{{ sts_site_shipping_address_1|url_encode }}{% if sts_site_shipping_address_2 %}+{{ sts_site_shipping_address_2|url_encode }}{% endif %}+{{ sts_site_shipping_city|url_encode }}+{{ sts_site_shipping_region_name|url_encode }}+{{ sts_site_shipping_postal_code|url_encode }}+{{ sts_site_shipping_country_name|url_encode }}"&amp;output=embed'></iframe>
                </address>
            </div>
        </div>
    </div>
</div>
{% endblock content %}
{% block javascript_footer %}
{{ parent() }}
<script>
   function load_location(add) {
        $("#gmap").each(function () {
            var embed = "<iframe width='100%' height='500' id='iframe-map' frameborder='0' scrolling='no'  marginheight='0' marginwidth='0'   src='https://maps.google.com/maps?&amp;q=" + (add) + "&amp;output=embed'></iframe>";
            $(this).html(embed);
        });
    }
</script>
{% endblock javascript_footer %}
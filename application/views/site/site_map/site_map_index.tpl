<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    {% for p in site_maps %}
    {% if p != 'site_map_index' %}
        {% if p == 'affiliates' %}
            {% if config_enabled('affiliate_marketing') %}
                <sitemap>
                    <loc>{{ site_url('site_map/id/'~p~'.xml') }}</loc>
                    <lastmod>{{ date('Y-m-d') }}</lastmod>
                </sitemap>
            {% endif %}
        {% else %}
            <sitemap>
                <loc>{{ site_url('site_map/id/'~p~'.xml') }}</loc>
                <lastmod>{{ date('Y-m-d') }}</lastmod>
            </sitemap>
        {% endif %}
    {% endif %}
    {% endfor %}
</sitemapindex>
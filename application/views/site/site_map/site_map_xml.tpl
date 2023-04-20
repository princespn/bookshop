<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ site_url(type) }}</loc>
        <lastmod>{{ date('Y-m-d') }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>1.0</priority>
    </url>
    {% if rows %}
    {% for p in rows %}
        <url>
            <loc>{{ p.link }}</loc>
            <lastmod>{{ p.date_modified }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.7</priority>
        </url>
    {% endfor %}
    {% endif %}
</urlset>
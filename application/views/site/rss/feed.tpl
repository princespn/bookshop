<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0"
     xmlns:dc="http://purl.org/dc/elements/1.1/"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
     xmlns:admin="http://webns.net/mvcb/"
     xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
     xmlns:content="http://purl.org/rss/1.0/modules/content/">

    <channel>

        <title>{{ feed.name }}</title>
        <atom:link href="{{ feed.location }}" rel="self" type="application/rss+xml"/>
        <link>{{ site_url(type) }}</link>
        <description>{{ feed.description }} </description>
        <dc:language>en</dc:language>
        <dc:creator>{{ feed.email }}</dc:creator>

        <dc:rights>{{ lang('copyright') }} {{ date('Y') }}</dc:rights>
        <admin:generatorAgent rdf:resource="{{ site_url() }}"/>

        {% for p in feed.data %}
            <item>
                <title>{{ xml_convert(p.title) }}</title>
                <link>{{ p.url }}</link>
                <guid>{{ p.url }}</guid>
                <description><![CDATA[
                    <table>
                        <tr>
                            <td>
                                <a href="{{ p.url }}">{{ p.image }}</a>
                            </td>
                            <td>
                                <p>{{ p.description }}</p>
                            </td>
                        </tr>
                    </table>
                    ]]>
                </description>
                <pubDate>{{ p.date }}</pubDate>
            </item>
        {% endfor %}


    </channel>
</rss>
{% if load_google_fonts == true %}
<script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js"></script>
<script>
    WebFont.load({
        google: {
            families: [{% if layout_design_theme_header_font in google_fonts %}'{{layout_design_theme_header_font}}:100,200,300,400,500,600,700'{% endif %}{% if layout_design_theme_base_font in google_fonts %},'{{layout_design_theme_base_font}}:100,200,300,400,500,600,700'{% endif %}]
        }
    });
</script>
{% endif %}
{% if config_enabled('lazy_load_images') %}
<script src="https://polyfill.io/v2/polyfill.min.js?features=IntersectionObserver"></script>
<script src="{{ base_url('js/yall/yall.min.js') }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", yall);
</script>
{% endif %}
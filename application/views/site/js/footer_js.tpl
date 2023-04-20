<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
<script src="{{ base_folder_path('js/colorbox/jquery.colorbox-min.js') }}"></script>
<script src="{{ base_folder_path('js/jquery.form.min.js') }}"></script>
<script src="{{ base_folder_path('js/datepicker/js/bootstrap-datepicker.js') }}"></script>
<script src="{{ base_folder_path('js/jquery.validate.js') }}"></script>
<script src="{{ base_folder_path('js/bootbox.min.js') }}"></script>
<script src="{{ base_folder_path('js/tabdrop/js/tabdrop.js') }}"></script>
<script src="{{ base_folder_path('js/site.js') }}"></script>
<!-- {{ constant('Twig_Environment::VERSION') }} -->
{% if config_enabled('layout_design_modal_enable_timer') %}
<script src="{{ base_folder_path('js/js.cookie.js')}}"></script>
{% endif %}
<script>
    function confirm_prompt(url) {
        bootbox.confirm('{{ lang('are_you_sure') }}', function (result) {
            if (result) {
                location.href = url;
            }
        });
    }
    {% if require_user_login() %}
    $(document).ready(function () {
        $('#login-modal').modal({
            show: true,
            keyboard: false,
            backdrop: 'static'
        })
    });
    {% endif %}

    {% if config_enabled('layout_design_modal_enable_timer') %}
    $(document).ready(function () {
        if (Cookies.get("{{ timer_modal_cookie }}") != 'true') {
            setTimeout(function() {
            $('#timed-modal').modal();
            }, {{ layout_design_modal_timer_seconds}}000);
            Cookies.set("{{ timer_modal_cookie }}", "true", { expires: {{ layout_design_modal_cookie_expires }} });
        }
    });
    {% endif %}

    $("#login-form").validate();

    $("#search-button").click(function() {
        $('#search-form-bar').toggle('show');
    });

</script>
{% include('js/lazy_load.tpl') %}
{% if config_enabled('sts_form_enable_captcha') %}
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
{% endif %}
{{ layout_design_meta_footer_info }}

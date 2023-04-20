{% block timer_modal %}
{% if layout_design_modal_enable_timer == 1 %}
<div class="modal fade" id="timed-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <br />
                <div class=""container">
                {{ layout_design_modal_timer_text }}
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{ lang('close') }}</button>

            </div>
        </div>
    </div>
</div>
{% endif %}
{% endblock timer_modal %}
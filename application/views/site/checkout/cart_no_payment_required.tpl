{% block content %}
    <h5>{{ lang('order_notes') }}</h5>
    <hr/>
    <div class="row">
    <div class="col-md-10 offset-md-1">
        {{ form_open(site_url('checkout/payment/free'), 'id="payment-form"') }}
        <div class="form-group row">
            <div class="col-sm-12">
                <textarea name="order_notes" class="form-control" rows="3" id="order-notes" placeholder="{{ lang('add_notes_to_order') }}"></textarea>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-12 text-right">
                <button type="submit" class="submit-button btn btn-primary">
                    {{ i('fa fa-refresh') }} {{ lang('submit') }}
                </button>
            </div>
            {{ form_close() }}
        </div>
    </div>
{% endblock content %}
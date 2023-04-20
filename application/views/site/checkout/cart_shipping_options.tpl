{% block content %}
    {% if shipping_options %}
        <div class="animated fadeIn">
            <h5>{{ lang('select_shipping_option') }}</h5>
            <hr/>
            <div class="form-group row">
                <div class="col-md-10 offset-md-1">
                    {% for k, p in shipping_options %}
                        <div class="radio">
                            <label>
                                {% if (sess('checkout_shipping_selected', 'sid')) == k %}
                                    <input type="radio" name="select_shipping" class="required"
                                           id="shipping-option-{{ k }}" checked="checked" value="{{ k }}"/>
                                {% else %}
                                    <input type="radio" name="select_shipping" class="required"
                                           id="shipping-option-{{ k }}" {% if k == 1 %} checked="checked"
                                    {% endif %} value="{{ k }}"/>
                                {% endif %}
                                {{ check_desc(p.shipping_description) }} - {{ format_amount(p.shipping_total) }}
                            </label>
                        </div>
                    {% endfor %}
                </div>
            </div>
            <hr/>
            <div class="row">
                <div class="col-md-11 text-right">
                    <button type="submit" class="btn btn-primary">
                        {{ lang('continue') }} {{ i('fa fa-caret-right') }}
                    </button>
                </div>
            </div>
        </div>
    {% else %}
        <div class="alert alert-danger" role="alert">
            <h5>{{ i('fa fa-exclamation-circle') }} {{ lang('no_shipping_options_found') }}</h5>
            <a href="{{ site_url('contact')}}" class="alert-link" target="_blank">{{ lang('click_here') }}</a>
            {{ lang('to_contact_us_regarding_this_error') }}
        </div>
    {% endif %}
{% endblock content %}
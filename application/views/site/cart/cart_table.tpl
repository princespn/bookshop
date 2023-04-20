<table class="cart-table table table-hover table-striped table-responsive">
    <thead class="capitalize thead-dark">
    <tr>
        <th class="hidden-sm-down"></th>
        <th>{{ lang('product') }}</th>
        <th class="text-sm-center hidden-sm-down">{{ lang('price') }}</th>
        <th class="text-sm-center hidden-sm-down">{{ lang('qty') }}</th>
        <th class="text-sm-center hidden-sm-down">{{ lang('subtotal') }}</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    {% if cart.items %}
        {% for p in cart.items %}
            <tr>
                <td style="width: 7%" class="hidden-sm-down">
                    {% if p.photo_file_name %}
                        <img src="{{ base_url('images/uploads/products/'~p.photo_file_name) }}"
                             class="img-thumbnail cart-thumb"/>
                    {% else %}
                        <img src="{{ base_url('images/no-photo.jpg') }}"
                             class="img-thumbnail cart-thumb"/>
                    {% endif %}
                </td>
                <td style="width: 25%">
                    <h5><a href="{{ page_url('product', p) }}">{{ p.product_name }}</a></h5>

                    <div class="product-attributes">{{ format_attribute_data(p) }}</div>
                    {% if p.discount_data %}
                        {% for d in p.discount_data %}
                            <small class="label label-default">
                                {{ cart_discount_text(d) }}
                            </small>
                        {% endfor %}
                    {% endif %}
                </td>
                <td style="width: 12%" class="text-sm-center hidden-sm-down">
                    {{ cart_unit_price(p) }}
                    <div class="cart-unit-price">
                        <small class="text-muted">{{ lang('unit_price') }}
                            : {{ format_amount(p.unit_price) }}</small>
                    </div>
                </td>
                <td style="width: 10%">
                    <div class="qty">
                        <input type="number" readonly name="qty[{{ p.item_id }}]" value="{{ p.quantity }}"
                               class="form-control required number"/>
                    </div>
                </td>
                <td style="width: 10%" class="text-sm-center hidden-sm-down">
                    {{ cart_qty_total(p) }}<br />
                    <small class="text-muted">{{ cart_pricing(p) }}</small>
                </td>
                <td style="width: 5%" class="text-sm-right hidden-sm-down"><span
                            onclick="confirm_prompt('{{ site_url('cart/delete/'~p.item_id) }}')"
                            class="btn btn-danger">{{ i('fa fa-remove') }}</span></td>
            </tr>
        {% endfor %}
    {% endif %}
    </tbody>
</table>
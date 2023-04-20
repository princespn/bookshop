<div class="d-none d-md-block">
    <div class="row">
        <div class="col-{% if (config_option('layout_design_cart_layout') == 'column') %}5{% else %}6{% endif %}"><h5>{{ lang('product') }}</h5></div>
        <div class="col-2"><h5 class="text-center">{{ lang('price') }}</h5></div>
        <div class="col-{% if (config_option('layout_design_cart_layout') == 'column') %}2{% else %}1{% endif %}"><h5 class="text-center">{{ lang('qty') }}</h5></div>
        <div class="col-2"><h5 class="text-center">{{ lang('subtotal') }}</h5></div>
        <div class="col-1 text-right">
            <a data-href="{{ site_url('cart/destroy') }}"
               data-toggle="modal" data-target="#empty-cart"
               href="#"
               class="btn btn-dark tip"  data-toggle="tooltip" data-placement="bottom" title="{{ lang('reset_cart') }}">
                {{ i('fa fa-remove') }}
            </a>
        </div>
    </div>
</div>

{% if cart.items %}
    {% for p in cart.items %}
        <hr/>
        <div class="row">
            <div class="col-{% if (config_option('layout_design_cart_layout') == 'column') %}5{% else %}6{% endif %}">
                <div class="media">
                    <img src="{% if p.photo_file_name %}{{ base_url('images/uploads/products/'~p.photo_file_name) }}{% else %}{{ base_url('images/no-photo.jpg') }} {% endif %}"
                         alt="photo" class="img-thumbnail cart-thumb mr-3"/>
                    <div class="media-body">
                        <h5 class="mt-0 mb-1"><a href="{{ page_url('product', p) }}">{{ p.product_name }}</a></h5>
                        {% if p.attribute_data %}
                            <div class="product-attributes">{{ format_attribute_data(p) }}</div>
                        {% endif %}
                        {% if p.discount_data %}
                            {% for d in p.discount_data %}
                                <small class="text-danger">
                                    {{ cart_discount_text(d) }}
                                </small>
                            {% endfor %}
                        {% endif %}
                    </div>
                </div>
            </div>
            <div class="col-2 d-none d-md-block text-center">
                {{ cart_unit_price(p) }}
                <div class="cart-unit-price">
                    <small class="text-muted">{{ lang('unit_price') }}
                        : {{ format_amount(p.unit_price) }}
                    </small>
                </div>
            </div>
            <div class="col-{% if (config_option('layout_design_cart_layout') == 'column') %}2{% else %}1{% endif %}">
                <div class="qty">
                    <input type="number" readonly name="qty[{{ p.item_id }}]" value="{{ p.quantity }}"
                           class="form-control required number" {% if p.product_type == 'subscription' %}readonly{% endif %}/>
                </div>
            </div>
            <div class="col-2 text-center">
                {{ cart_qty_total(p) }}<br/>
                <small class="text-muted">{{ cart_pricing(p) }}</small>
            </div>
            <div class="col-1 text-right">
                <span onclick="rm_item('{{ site_url('cart/delete/'~p.item_id) }}')"
                      class="btn btn-light">{{ i('fa fa-remove') }}</span>
            </div>
        </div>
    {% endfor %}
    <div class="modal fade" id="empty-cart" tabindex="-1" role="dialog" aria-labelledby="modal-title"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body capitalize">
                    <h3 id="modal-title"><i class="fa fa-trash-o"></i> {{ lang('reset_'~layout_design_shopping_cart_or_bag) }}</h3>
                    {{ lang('are_you_sure_you_want_to_empty_'~layout_design_shopping_cart_or_bag) }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ lang('cancel') }}</button>
                    <a href="#" class="btn btn-primary warning">{{ lang('proceed') }}</a>
                </div>
            </div>
        </div>
    </div>
    <script>
        $('#empty-cart').on('show.bs.modal', function (e) {
            $(this).find('.warning').attr('href', $(e.relatedTarget).data('href'));
        });
    </script>
{% endif %}
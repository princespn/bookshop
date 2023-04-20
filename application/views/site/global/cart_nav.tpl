{% if  show_cart() %}
<li class="nav-item dropdown">
    <a href="{{ site_url('cart') }}" class="nav-link d-md-none">
        {{ i('fa fa-shopping-'~layout_design_shopping_cart_or_bag) }}
        {{ lang('view_'~layout_design_shopping_cart_or_bag) }}
        <span class="badge badge-info">{{ cart.items|length }}</span>
    </a>
    <a href="{{ site_url('cart') }}" class="nav-link dropdown-toggle d-none d-md-block" data-toggle="dropdown">
        {{ i('fa fa-shopping-'~layout_design_shopping_cart_or_bag) }}
        {{ lang('view_'~layout_design_shopping_cart_or_bag) }}</a>
    <ul class="dropdown-menu">
        <li>
            <div class="mega-content">
                <div class="shopping-cart">
                    <div class="shopping-cart-header">
                               <span class="badge badge-light">
                                   {{ cart.items|length }} {{ lang('total') }}
                                   </span>
                        <div class="shopping-cart-total">
                            <span class="lighter-text">{{ lang('total') }}:</span>
                            <span>{{ format_amount(cart.totals.total) }}</span>
                        </div>
                    </div> <!-- end shopping-cart-header -->
                    <!-- cart dropdown -->
                    <div class="cart-nav-box">
                    <ul class="shopping-cart-items list-unstyled">
                        {% for p in cart.items %}
                        <li class="clearfix">
                            <img src=" {% if p.photo_file_name %}{{ base_url('images/uploads/products/'~p.photo_file_name) }} {% else %}{{ base_url('images/no-photo.jpg') }}{% endif %}"
                                 class="img-thumbnail rounded-circle"/>
                            <h5 class="item-name">{{ character_limiter(p.product_name, 20) }}</h5>
                            <small class="item-price">{{ cart_unit_price(p) }}</small>
                            <small class="item-quantity">{{ lang('quantity') }}: {{ p.quantity }}</small>
                        </li>
                        {% endfor %}
                    </ul>
                    </div>
                    <hr/>
                    <a href="{{ site_url('cart') }}"
                       class="btn-cart-nav btn btn-lg btn-primary btn-block">
                        {{ i('fa fa-caret-right') }} {{ lang('proceed_to_checkout')|upper }}
                    </a>
                </div> <!--end shopping-cart -->
            </div>
        </li>
    </ul>
</li>
{% endif %}
{% if (config_enabled('sts_cart_allow_currency_conversion')) %}
<li class="nav-item dropdown ">
    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
       <span>{{ lang('currency') }}<span class="caret"></span></span>
    </a>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        {% for c in currencies %}
        <a href="{{ site_url('switch_currency/'~c.code) }}" class="dropdown-item">
        {{c.symbol_left }} {{c.code}}
        </a>
        {% endfor %}
    </div>
</li>
{% endif %}
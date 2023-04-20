<div class="footer-menu">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <h4>{{ i('fa fa-group') }} {{ lang('about_us') }}</h4>
                <p>{{ lang(sts_site_description) }}</p>
            </div>
            <div class="col-md-3">
                <h4>{{ i('fa fa-map-marker') }} {{ lang('contact_us') }}</h4>
                <address>
                    <strong>{{ sts_site_name }}</strong><br/>
                    {{ sts_site_shipping_address_1 }}<br/>
                    {{ sts_site_shipping_city }},
                    {{ sts_site_shipping_region_name }} {{ sts_site_shipping_postal_code }}
                    {{ sts_site_shipping_country_iso_code_3 }}<br/>
                    {{ sts_site_phone_number }}<br/>
                    {{ encode_email(sts_site_email) }}
                </address>
            </div>
            <div class="col-md-3">
                <h4>{{ i('fa fa-link') }} {{ lang('quick_links') }}</h4>
                <ul class="list-unstyled">
                <li><a href="{{ site_url('store') }}">{{ lang('visit_our_store') }}</a> {{ i('fa fa-angle-right') }}
                    </li>
                    
                    <li><a href="{{ site_url('blog') }}">{{ lang('read_our_blog') }}</a> {{ i('fa fa-angle-right') }}</li>
                    <li>
                        <a href="{{ site_url('faq') }}">{{ lang('frequently_asked_questions') }}</a> {{ i('fa fa-angle-right') }}
                    </li>
                    <li><a href="{{ ssl_url('login') }}">{{ lang('login_to_members_area') }}</a> {{ i('fa fa-angle-right') }}</li>
                </ul>
            </div>
            <div class="col-md-3">
            <div class="text-center">

                <h4></h4>

                    <a href="http://www.bookzim.com/">
                    <img src="http://www.bookzim.com/images/uploads/img-20230416-wa0005.jpg" alt="Logo" class="img-fluid logo">
                                            </a>
                
                </div>
                
            </div>
        </div>
    </div>
</div>
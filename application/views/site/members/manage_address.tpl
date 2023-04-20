{% extends "global/base.tpl" %}
{% block title %}{{ lang('update_address') }}{% endblock %}
{% block page_header %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">{{ lang('address_details') }}</h2>
            </div>

        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div id="address" class="content">
        {{ form_open('', 'id="form"') }}
        {{ breadcrumb }}
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-10 offset-md-1">
                        <div class="row">
                            <div class="col-md-6">
                                <fieldset class="form-group">
                                    <label for="fname">{{ lang('fname') }}</label>
                                    {{ form_input('fname', set_value('fname', address.fname), 'id="fname" class="form-control required"') }}
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset class="form-group">
                                    <label for="lname">{{ lang('lname') }}</label>
                                    {{ form_input('lname', set_value('lname', address.lname), 'id="lname" class="form-control required"') }}
                                </fieldset>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <fieldset class="form-group">
                                    <label for="company">{{ lang('company') }}</label>
                                    {{ form_input('company', set_value('company', address.company), 'id="company" class="form-control"') }}
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset class="form-group">
                                    <label for="phone">{{ lang('phone') }}</label>
                                    {{ form_input('phone', set_value('phone', address.phone), 'id="phone" class="form-control "') }}
                                </fieldset>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <fieldset class="form-group">
                                    <label for="address_1">{{ lang('address_1') }}</label>
                                    {{ form_input('address_1', set_value('address_1', address.address_1), 'id="address_1" class="form-control required"') }}
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset class="form-group">
                                    <label for="address_2">{{ lang('address_2') }}</label>
                                    {{ form_input('address_2', set_value('address_2', address.address_2), 'id="address_2" class="form-control"') }}
                                </fieldset>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <fieldset class="form-group">
                                    <label for="city">{{ lang('city') }}</label>
                                    {{ form_input('city', set_value('city', address.city), 'id="city" class="form-control required"') }}
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset class="form-group">
                                    <label for="postal_code">{{ lang('postal_code') }}</label>
                                    {{ form_input('postal_code', set_value('postal_code', address.postal_code), 'id="postal_code" class="form-control required"') }}
                                </fieldset>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <fieldset class="form-group">
                                    <label for="state">{{ lang('state') }}</label>
                                    {{ form_dropdown('state', address.regions_array , address.region_id, 'id="state" class="form-control required"') }}
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset class="form-group">
                                    <label for="country">{{ lang('country') }}</label>
                                    {{ form_dropdown('country', address.country_array , address.country, 'onchange="updateregion(\'state\')" id="country" class="country_id form-control required"') }}
                                </fieldset>
                            </div>
                        </div>
                        <hr/>
                        <div class="row">
                            <div class="col-md-12 text-sm-center">
                                <button type="submit"
                                        class="btn btn-lg btn-primary">{{ i('fa fa-refresh') }} {{ lang('save_changes') }}</button>
                                <a href="{{ site_url('members/account/#addresses') }}"
                                   class="btn btn-lg btn-secondary">{{ i('fa fa-undo') }} {{ lang('go_back') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {% if address.id %}
            {{ form_hidden('id', address.id) }}
        {% endif %}
        {{ form_hidden('member_id', sess('member_id')) }}
        {{ form_close() }}

    </div>
{% endblock content %}
{% block javascript_footer %}
    {{ parent() }}
    {{ include('js/default_form_js.tpl') }}
    <script src="{{ site_url('js/select2/select2.min.js') }}"></script>
    <script>
        //search countries
        $("#country").select2({
            ajax: {
                url: '{{ site_url('search/search_countries/') }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        country_name: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, page) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                id: item.country_id,
                                text: item.country_name
                            }
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 2
        });

        function updateregion(select) {
            $.get('{{ site_url('search/load_regions/state') }}', {country_id: $('#country').val()},
                function (data) {
                    $('#state').html(data);
                    $(".s2").select2();
                }
            );
        }
    </script>
{% endblock javascript_footer %}
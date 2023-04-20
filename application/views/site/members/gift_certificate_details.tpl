{% extends "global/base.tpl" %}
{% block title %}{{ lang('gift_certificate_details')|capitalize }}{% endblock %}
{% block meta_description %}{{ lang('gift_certificate_details') }}{% endblock meta_description %}
{% block meta_keywords %}{{ lang('gift_certificate_details') }}{% endblock meta_keywords %}
{% block meta_robots %}noindex, nofollow{% endblock meta_robots %}
{% block content %}
    {{ form_open_multipart('', 'id="form" class="form-horizontal"') }}
    <div class="checkout-thankyou">
        <div class="row">
            <div class="col-md-12">
                {{ breadcrumb }}
                <div class="card content">
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header">{{ i('fa fa-file-text-o') }} {{ lang('gift_certificate') }}</div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-9">
                                        <h5>{{ lang('certificate_code') }}: {{ p.code }}</h5>
                                    </div>
                                    <div class="col-md-3 text-md-right">
                                        <h5><strong>{{ format_amount(p.amount) }}</strong></h5>
                                    </div>
                                </div>
                                <hr />
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label for="from_name"
                                                   class="col-sm-3 col-form-label">
                                                {{ lang('from_name') }}
                                            </label>

                                            <div class="col-sm-9">  {{ form_input('from_name', set_value('from_name', p.from_name), 'class="form-control required"') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label for="from_email"
                                                   class="col-sm-3 col-form-label">
                                                {{ lang('from_email') }}
                                            </label>

                                            <div class="col-sm-9">  {{ form_input('from_email', set_value('from_email', p.from_email), 'class="form-control email required"') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label for="to_name"
                                                   class="col-sm-3 col-form-label">
                                                {{ lang('to_name') }}
                                            </label>

                                            <div class="col-sm-9">  {{ form_input('to_name', set_value('to_name', p.to_name), 'class="form-control required"') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label for="to_email"
                                                   class="col-sm-3 col-form-label">
                                                {{ lang('to_email') }}
                                            </label>

                                            <div class="col-sm-9">  {{ form_input('to_email', set_value('to_email', p.to_email), ' class="form-control email required"') }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <fieldset class="form-group">
                                            <textarea name="message" placeholder="{{ lang('enter_message_for_recipient') }}" rows="5" class="form-control required">{{ p.message }}</textarea>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button class="btn btn-lg btn-primary" type="submit">{{ i('fa fa-envelope') }} {{ lang('send') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    {{ form_close() }}
{% endblock content %}
{% block javascript_footer %}
    {{ parent() }}
    {{ include('js/default_form_js.tpl') }}
{% endblock javascript_footer %}
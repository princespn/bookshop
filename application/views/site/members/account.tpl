{% extends "global/base.tpl" %}
{% block title %}{{ lang('account_profile') }}{% endblock %}
{% block page_header %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">{{ lang('account_profile') }}</h2>
            </div>

        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div id="account" class="content">
        {{ breadcrumb }}
        <div class="row">
            <div class="col-md-3">
                <div class="card mb-3">
                    <div class="card-body">
                        {% if sess('profile_photo') %}
                            <img src="{{ sess('profile_photo') }}" alt="" id="image-1"
                                 class="img-fluid rounded-circle mx-auto d-block">
                        {% else %}
                            <img src="{{ base_url('images/profile.png') }}" alt="" id="image-1"
                                 class="img-fluid rounded-circle mx-auto d-block">
                        {% endif %}
                    </div>
                    {% if config_enabled('sts_allow_user_uploads') %}
                        <div class="card-footer text-md-center">
                            {{ form_hidden('profile_photo', member.profile_photo, 'id="profile_photo"') }}
                            <button type="button" id="upload-photo" class="btn btn-secondary btn-block-sm">
                                {{ i('fa fa-upload') }} {{ lang('update_photo') }}</button>
                        </div>
                    {% endif %}
                </div>
                <div class="list-group">
                    <a class="list-group-item list-group-item-action active" href="#overview" id="overview_tab" role="tab"
                       data-toggle="tab">{{ lang('overview') }}</a>
                    <a href="#addresses" class="list-group-item list-group-item-action" id="addresses_tab" role="tab"
                       data-toggle="tab">{{ lang('addresses') }}</a>
                    {% if config_enabled('sts_site_enable_user_profiles') %}
                            <a href="#profile" class="list-group-item list-group-item-action" id="profile_tab" role="tab"
                               data-toggle="tab">{{ lang('user_profile') }}</a>
                    {% endif %}
                    {% if check_module('payment_gateways', 'stripe') %}
                            <a href="#billing" id="billing-tab" class="ajax-link list-group-item list-group-item-action " id="billing_tab" role="tab"
                               data-remote-div="#load_billing"
                               data-tab-remote="{{ page_url('members', 'account/billing?q=ajax') }}" data-toggle="tab"
                               aria-controls="billing" aria-selected="false">{{ lang('billing_details') }}</a>

                    {% endif %}
                        <a class="list-group-item list-group-item-action " href="#reset_password" id="reset_password_tab" role="tab"
                           data-toggle="tab">{{ lang('reset_password') }}</a>
                    {% if member.points > 0 %}
                        <strong class="list-group-item list-group-item-action ">{{member.points}} {{ lang('member_points') }}</strong>
                    {% endif %}
                </div>
            </div>
            <div class="col-md-9">
                <div class="card">
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="overview">
                        {{ form_open_multipart('', 'id="form"') }}
                        <h3>{{ lang('account_overview') }}</h3>
                        <hr/>
                        {% if fields.values %}
                            {% for p in fields.values %}
                                {% if p.sub_form == '' %}
                                    {% if p.show_account == 1 %}
                                        <div class="form-group row">
                                            <label for="{{ p.form_field }}"
                                                   class="col-md-3 col-form-label text-sm-right">
                                                {{ p.field_name }}</label>
                                            <div class="col-md-9">
                                                {{ p.field }}
                                            </div>
                                        </div>
                                    {% endif %}
                                {% endif %}
                            {% endfor %}
                        {% endif %}
                        {% if custom_fields %}
                            {% for p in custom_fields %}
                                <div class="form-group row">
                                    <label for="{{ p.form_field }}"
                                           class="col-md-3 col-form-label text-sm-right">{{ p.field_name }}</label>
                                    <div class="col-md-9">
                                        {{ p.field }}
                                    </div>
                                </div>
                            {% endfor %}
                        {% endif %}
                        <hr/>
                        <div class="text-sm-right">
                            <button type="submit"
                                    class="btn btn-primary submit-button">{{ i('fa fa-refresh') }} <span>{{ lang('save_changes') }}</span></button>
                        </div>
                        {{ form_hidden('member_id', sess('member_id')) }}
                        {{ form_close() }}
                    </div>
                    <div role="tabpanel" class="tab-pane" id="addresses">
                        <h3>{{ lang('addresses') }}
                            <a href="{{ page_url('members', 'account/add_address') }}"
                               class="btn btn-primary float-right">{{ i('fa fa-plus') }} {{ lang('add_new_address') }}</a>
                        </h3>
                        <hr/>

                        {% if member.addresses %}
                            <div id="addresses" class="card-columns">
                                {% for a in member.addresses %}
                                    <div class="card">
                                        <div class="card-body">
                                            <address>
                                                {% if a.company %}
                                                    <strong>{{ a.company }}</strong><br>
                                                {% endif %}
                                                <strong>{{ a.fname }} {{ a.lname }}</strong><br>
                                                {% if a.address_1 %}
                                                    {{ a.address_1 }}<br>
                                                {% endif %}
                                                {% if a.address_2 %}
                                                    {{ a.address_2 }}<br>
                                                {% endif %}
                                                {{ a.city }}, {{ a.region_name }} {{ a.postal_code }}<br>
                                                {% if a.country_name %}
                                                    {{ a.country_name }}<br>
                                                {% endif %}
                                                {% if a.phone %}
                                                    <abbr title="Phone">P:</abbr> {{ a.phone }}
                                                {% endif %}
                                            </address>
                                            <p class="card-text">
                                                <a href="{{ page_url('members', 'account/update_address') }}/{{ a.id }}"
                                                   class="btn btn-primary">{{ i('fa fa-pencil') }}</a>
                                                <a data-href="{{ page_url('members', 'account/delete_address') }}/{{ a.id }}"
                                                   data-toggle="modal" data-target="#confirm-delete" href="#"
                                                   class="btn btn-danger">{{ i('fa fa-trash-o') }}</a>
                                            </p>
                                        </div>
                                    </div>
                                {% endfor %}
                            </div>
                        {% else %}
                        <div class="alert alert-info">{{ lang('no_addresses_found') }}</div>
                        {% endif %}
                    </div>
                    {% if config_enabled('sts_site_enable_user_profiles') %}
                        <div role="tabpanel" class="tab-pane" id="profile">
                            {{ form_open_multipart(page_url('members', 'account/profile'), 'id="profile_form"') }}
                            <h3><a href="{{ site_url('profile/'~username) }}" target="_blank"
                                   class="float-right btn btn-primary">{{ i('fa fa-search') }} {{ lang('view_profile_page') }}</a>{{ lang('account_profile') }}
                            </h3>
                            <hr/>
                            <div class="form-group row">
                                <label for="{{ lang('profile_byline') }}"
                                       class="col-md-3 col-form-label text-sm-right">{{ lang('profile_line') }}
                                </label>
                                <div class="col-md-9">
                                    {{ form_input('profile_line', member.profile_line, 'class="form-control"') }}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="{{ lang('profile_description') }}"
                                       class="col-md-3 col-form-label text-sm-right">{{ lang('profile_description') }}
                                </label>
                                <div class="col-md-9">
                                    {{ form_textarea('profile_description', member.profile_description, 'class="form-control"') }}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="{{ lang('background_photo') }}"
                                       class="col-md-3 col-form-label text-sm-right">{{ lang('select_profile_background') }}
                                </label>
                                <div class="col-md-9">
                                    {% if backgrounds %}
                                    <div class="images">
                                        {% for b in backgrounds %}
                                            <div class="w-25 float-left">
                                                <input id="{{ b }}" type="radio" name="profile_background"
                                                       value="{{ base_url('/images/uploads/backgrounds/'~b) }}"
                                                       {% if member.profile_background ==  base_url('/images/uploads/backgrounds/'~b) %}checked="checked"{% endif %}/>
                                                <label class="header-cc" for="{{ b }}"
                                                       style="background-image:url('{{ profile_photo(b) }}')"></label>
                                            </div>
                                        {% endfor %}
                                        {% endif %}
                                    </div>
                                </div>
                            </div>
                            <hr/>
                            <div class="text-sm-right">
                                <button type="submit"
                                        class="btn btn-primary submit-button">{{ i('fa fa-refresh') }} <span>{{ lang('save_changes') }}</span></button>
                            </div>
                            {{ form_hidden('member_id', sess('member_id')) }}
                            {{ form_close() }}
                        </div>
                    {% endif %}
                    {% if check_module('payment_gateways', 'stripe') %}
                        <div role="tabpanel" class="tab-pane" id="billing">
                            <div id="load_billing" class="ajaxFade"></div>

                        </div>
                    {% endif %}
                    <div role="tabpanel" class="tab-pane" id="reset_password">
                        {{ form_open(page_url('members', 'account/reset_password'), 'id="reset-form"') }}
                        <h3>{{ lang('reset_your_password') }}</h3>
                        <hr/>
                        <div class="row">
                            <div class="col-md-12">
                                <fieldset class="form-group">
                                    <label for="current">{{ lang('current_password') }}</label>
                                    <input name="current" type="password" class="form-control" placeholder="******">
                                </fieldset>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <fieldset class="form-group">
                                    <label for="new">{{ lang('new_password') }}</label>
                                    <input id="password" name="password" type="password" class="form-control"
                                           placeholder="******">
                                </fieldset>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <fieldset class="form-group">
                                    <label for="confirm">{{ lang('confirm_password') }}</label>
                                    <input id="confirm" name="confirm" type="password" class="form-control"
                                           placeholder="******">
                                </fieldset>
                            </div>
                        </div>
                        <button type="submit"
                                class="btn btn-primary">{{ i('fa fa-refresh') }} {{ lang('reset_password') }}</button>
                        {{ form_hidden('member_id', sess('member_id')) }}
                        {{ form_close() }}
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
{% endblock content %}
{% block javascript_footer %}
    {{ parent() }}
    <script>
        $(function () {
            $('.datepicker-input').datepicker({format: '{{ format_date }}'});
        });

        $("#reset-form").validate({
            rules: {
                current: {
                    required: true,
                    minlength: {{ min_member_password_length }},
                    maxlength: {{ max_member_password_length }}
                },
                password: {
                    required: true,
                    minlength: {{ min_member_password_length }},
                    maxlength: {{ max_member_password_length }}
                },
                confirm: {
                    required: true,
                    equalTo: '#password'
                }
            }
        });

        {% if config_enabled('sts_allow_user_uploads') %}
        $('#upload-photo').on('click', function () {
            var node = this;
            $('#form-upload').remove();
            $('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="files" /><input type="hidden" name="{{ csrf_token }}" value="{{ csrf_value }}" /></form>');
            $('#form-upload input[name=\'files\']').trigger('click');

            timer = setInterval(function () {
                if ($('#form-upload input[name=\'files\']').val() != '') {
                    clearInterval(timer);
                    $.ajax({
                        url: '{{ site_url('members/account/upload/'~sess('member_id')) }}',
                        type: 'post',
                        dataType: 'json',
                        data: new FormData($('#form-upload')[0]),
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function (data) {
                            if (data['type'] == 'error') {
                                $('#response').html('{{ alert('error') }}');
                            }
                            else if (data['type'] == 'success') {
                                $('#image-1').attr('src', data['file_name']);
                                $('#response').html('{{ alert('success') }}');
                            }

                            $('#msg-details').html(data['msg']);
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            {{ js_debug() }}(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                        }
                    });
                }
            }, 500);
        });
        {% endif %}
    </script>
    {{ include('js/default_form_js.tpl') }}
{% endblock javascript_footer %}

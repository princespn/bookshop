{% extends "global/base.tpl" %}
{% block title %}{{ lang('create_ticket')|capitalize }}{% endblock %}
{% block page_header %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">{{ lang('support_ticket') }}</h2>
            </div>

        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div id="create-ticket" class="content">
        {{ form_open_multipart('', 'id="form"') }}
        {{ breadcrumb }}
        <div class="d-md-none">
            <h2>{{ lang('create_ticket') }}</h2>
            <hr/>
        </div>
        <div class="row">

            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        {{ i('fa fa-folder-o') }} {{ lang('department') }}
                    </div>
                    <div class="card-body">
                        {% if categories %}
                            <div class="row">
                                <div class="col-md-12">
                                    {% for c in categories %}
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="category_id" value="{{ c.category_id }}"
                                                       class="required"/>
                                                <strong>{{ c.category_name }}</strong>
                                                <br /><small>{{ c.category_description }}</small>
                                            </label>
                                        </div>
                                    {% endfor %}
                                </div>
                            </div>
                        {% endif %}
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        {{ i('fa fa-exclamation-circle') }} {{ lang('priority') }}
                    </div>
                    <div class="card-body">
                        <div class="radio">
                            <label>
                                <input type="radio" name="priority" value="low"  checked="checked" class="required"/>
                                {{ lang('low_priority') }}
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="priority" value="normal" class="required"/>
                                {{ lang('normal_priority') }}
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="priority" value="high" class="required"/>
                                {{ lang('high_priority') }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div id="create-box">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="add-ticket">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>{{ i('fa fa-pencil') }} {{ lang('create_ticket') }}</h5>
                                    </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <fieldset class="form-group">
                                                <label for="reply_content">{{ lang('subject') }}</label>
                                                <input type="text" id="ticket_subject" name="ticket_subject" class="form-control required"/>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <fieldset class="form-group">
                                                <label for="reply_content">{{ lang('message') }}</label>
                                                <textarea name="reply_content" rows="10"
                                                          class="form-control required"></textarea>
                                            </fieldset>
                                        </div>
                                    </div>
                                    {% if config_enabled('sts_support_enable_file_uploads') %}
                                        <div class="row">
                                            <div class="col-md-12">
                                                <small class="text-muted float-right d-none d-md-block">{{ lang('allowed_file_types') }}
                                                    : {{ str_replace('|', ',', sts_support_upload_download_types) }}
                                                    {{ sts_support_max_upload_per_reply }} {{ lang('files_max_per_upload') }}
                                                </small>
                                                <label>{{ lang('add_attachments') }}</label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <button id="add-file" class="btn btn-warning btn-block" type="button">
                                                    {{ i('fa fa-plus') }} {{ lang('add_more') }}
                                                </button>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="file" name="files[]" class="form-control"/>

                                                <div id="add-attachments"></div>
                                                <div class="text-sm-right">

                                                </div>
                                            </div>

                                        </div>
                                        <hr/>
                                    {% endif %}
                                    <div class="row">
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-lg btn-primary btn-block-sm">
                                                {{ i('fa fa-refresh') }} {{ lang('submit') }}
                                            </button>
                                        </div>
                                        <div class="col-md-9">

                                        </div>
                                    </div>
                                    <div id="answers"></div>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a name="reply"></a>
                </div>
            </div>
            {{ form_hidden('reply_type', 'member') }}
            {{ form_hidden('ticket_status', 'new') }}
            {{ form_hidden('member_id', sess('member_id')) }}
        </div>
        {{ form_close() }}
    </div>
{% endblock content %}
{% block javascript_footer %}
    {{ parent() }}
    <script>
        var next = 2;
        $("#form").validate();
        $('#add-file').click(function () {
            if (next <= {{ sts_support_max_upload_per_reply }}) {
                $("#add-attachments").append('<div id="file-' + next + '"><br /><div class="input-group"><input type="file" name="files[]" class="form-control"/><div class="input-group-addon"><a href="javascript:remove_upload(\'#file-' + next + '\')"><i class="fa fa-trash-o "></i></a></div></div></div>');
                next++;
            }
        });

        function remove_upload(id) {
            $(id).fadeOut(300, function () {
                $(this).remove();
            });
            next--;
        }

        {% if (config_enabled('sts_kb_enable_ticket_answers')) %}
        $(document).ready(function(){

            $('#ticket_subject').keyup(function(){
                var search = $(this).val();
                $('#answers').html('');
                if(search != '')
                {
                    $.ajax({
                        url:"{{site_url('kb/ticket_answers')}}",
                        method: 'get',
                        data: 'search_term=' + encodeURIComponent($('#ticket_subject').val()),
                        success:function(data){
                            $('#answers').html(data);
                        }
                    })
                }
            });
        });
        {% endif %}

    </script>
{% endblock javascript_footer %}

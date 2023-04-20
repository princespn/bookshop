{% extends "global/base.tpl" %}
{% block title %}{{ lang('user_subscriptions') }}{% endblock %}
{% block meta_description %}{{ lang('user_subscriptions') }}{% endblock meta_description %}
{% block meta_robots %}noindex, nofollow{% endblock meta_robots %}
{% block page_header %}
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2 class="headline">{{ lang('user_subscriptions') }}</h2>
        </div>

    </div>
</div>
{% endblock page_header %}
{% block content %}
<div class="mailing-lists">
    {{ breadcrumb }}
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">{{ i('fa fa-envelope') }} {{ lang('mailing_lists') }}</h4>
                    <hr/>
                    {% if lists %}
                    <p class="card-text">
                        <strong>{{ email }} {{ lang('subscribed_to') }}:</strong>
                    </p>
                    {% for p in lists %}
                    <div class="row">
                        <div class="col-md-11">
                            <strong>{{ lang(p. list_name) }}</strong><br/>
                            <small>{{ lang(p.description) }}</small>
                        </div>
                        <div class="col-md-1">
                            <a data-href="{{ site_url('email/subscriptions/'~key~'/'~email~'/'~p.list_id) }}"
                               title="{{ lang('delete_topic') }}"
                               data-toggle="modal" data-target="#confirm-unsubscribe"
                               href="#"
                               class="float-right btn btn-sm btn-secondary">
                                {{ i('fa fa-undo') }}
                                {{ lang('unsubscribe') }}
                            </a>
                        </div>
                    </div>
                    <br/>
                    {% endfor %}
                    {% else %}
                    <div class="alert alert-info">
                        {{ i('fa fa-info-circle') }} {{ lang('no_email_subscriptions_found') }}
                    </div>
                    {% endif %}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="confirm-unsubscribe" tabindex="-1" role="dialog" aria-labelledby="modal-title"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body capitalize">
                <h3 id="modal-title"><i class="fa fa-trash-o"></i> {{ lang('confirm_unsubscribe') }}</h3>
                {{ lang('are_you_sure_you_want_to_unsubscribe') }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">{{ lang('cancel') }}
                </button>
                <a href="#" class="btn btn-danger danger">{{ lang('proceed') }}</a>
            </div>
        </div>
    </div>
</div>
<script>
    $('#confirm-unsubscribe').on('show.bs.modal', function (e) {
        $(this).find('.danger').attr('href', $(e.relatedTarget).data('href'));
    });
</script>
{% endblock content %}
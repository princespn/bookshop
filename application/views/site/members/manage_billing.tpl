<h3>{{ lang('credit_card_details') }}</h3>
<hr/>
{% if card.cc_four == false %}
    <div class="alert alert-info">{{ lang('no_billing_details_found') }}</div>
{% else %}
<div class="form-group row">
    <label class="col-sm-1 col-form-label text-md-right">{{ lang('type') }}</label>
    <div class="col-sm-2 r">
        <span class="form-control">{{ card.cc_type }}</span>
    </div>
    <label class="col-sm-2 col-form-label text-md-right">{{ lang('last_four') }}</label>

    <div class="col-sm-2 r">
        <span class="form-control">{{ card.cc_four }}</span>
    </div>
    <label class="col-sm-2 col-form-label text-md-right">{{ lang('exp_date') }}</label>

    <div class="col-sm-2 r">
        <span class="form-control">{{ card.cc_month }} / {{ card.cc_year }}</span>
    </div>
    <div class="col-sm-1 r">
        <a data-href="{{ page_url('members', 'account/delete_billing/'~card.id) }}"
           data-toggle="modal" data-target="#confirm-delete" href="#" class="btn btn-danger">{{ i('fa fa-trash') }}</a>
    </div>
</div>
<hr/>
<div class="text-sm-right">
    {{ form_open_multipart(page_url('members', 'account/billing'), 'id="billing_form"') }}
        {{ update_form }}
   {{ form_close() }}
</div>
{% endif %}


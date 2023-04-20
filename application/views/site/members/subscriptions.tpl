{% extends "global/base.tpl" %}
{% block title %}{{ lang('product_subscriptions')|capitalize }}{% endblock %}
{% block page_header %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">{{ lang('product_subscriptions') }}</h2>
            </div>

        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div id="subscriptions" class="content">
        {{ breadcrumb }}
        <div class="row">
            <div class="col-md-12 table-responsive ">
                {% if subscriptions.rows %}
                <table id="subscriptions-table" class="table table-striped table-hover dt-responsive nowrap">
                    <thead>
                    <tr>
                        <th>{{ lang('product') }}</th>
                        <th class="text-sm-center"  style="width: 10%">{{ lang('next_due_date') }}</th>
                        <th class="text-sm-center"  style="width: 10%">{{ lang('price') }}</th>
                        <th class="text-sm-center"  style="width: 10%">{{ lang('interval') }}</th>
                        {% if config_enabled('sts_products_enable_subscription_cancellations') %}
                        <th class="text-sm-center"  style="width: 10%">{{ lang('cancel') }}</th>
                        {% endif %}
                        <th class="text-sm-center"  style="width: 10%"></th>
                    </tr>
                    </thead>
                    <tbody>
                    {% for p in subscriptions.rows %}
                        <tr>
                            <td>
                                <strong><a href="#id-{{ p.sub_id }}" data-toggle="collapse">{{ p.product_name }}</a></strong>
                                <div id="id-{{ p.sub_id }}" class="collapse attribute">
                                {% if p.attribute_data %}
                                    {{ format_attribute_data(p) }}
                                {% endif %}
                                </div>
                            </td>
                            <td style="width: 10%" class="text-sm-center">
                                {% if p.status == 1 %}
                                    {{ display_date(p.next_due_date) }}
                                {% else %}
                                    <span class="badge badge-danger">{{ lang('cancelled') }}</span>
                                {% endif %}
                                </td>
                            <td style="width: 13%" class="text-sm-center">{{ format_amount(p.product_price, FALSE) }}</td>
                            <td style="width: 10%" class="text-sm-center">{{ p.interval_amount }}
                                {% if p.interval_amount > 1 %}
                                    {{ lang(plural(p.interval_type)) }}
                                {% else %}
                                    {{ lang(singular(p.interval_type)) }}
                                {% endif %}
                            </td>
                            {% if config_enabled('sts_products_enable_subscription_cancellations') %}
                            <td style="width: 10%" class="text-sm-center">
                                {% if p.status == 1 %}
                                    <a data-href="{{  page_url('members', 'subscriptions/cancel/'~p.sub_id) }}"
                                       data-toggle="modal" data-target="#confirm-delete" href="#"
                                       class="btn btn-sm btn-warning">{{ lang('cancel') }}</a>
                                {% else %}
                                    <span class="btn btn-sm btn-danger">{{ lang('cancelled') }}</span>
                                {% endif %}
                            </td>
                            {% endif %}
                            <td style="width: 5%" class="text-sm-right">
                                <a href="{{  page_url('members', 'subscriptions/details/'~p.sub_id) }}"
                                   class="btn btn-secondary">
                                    {{i('fa fa-search')}}
                                </a>
                            </td>
                        </tr>
                    {% endfor %}
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="6"></td>
                    </tr>
                    </tfoot>
                </table>
                {% else %}
                    <div class="alert alert-info" role="alert">
                        {{ lang('no_subscriptions_found') }}
                    </div>
                {% endif %}
            </div>
        </div>
    </div>
{% endblock content %}
 {% block confirm_delete %}
     <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog">
         <div class="modal-dialog">
             <div class="modal-content">
                 <div class="modal-body capitalize">
                     <h3><i class="fa fa-trash-o"></i> {{ lang('confirm_cancellation') }}</h3>
                     {{ lang('are_you_sure_you_want_to_cancel') }}
                 </div>
                 <div class="modal-footer">
                     <button type="button" class="btn btn-secondary"
                             data-dismiss="modal">{{ lang('cancel') }}</button>
                     <a href="#" class="btn btn-danger danger">{{ lang('proceed') }}</a>
                 </div>
             </div>
         </div>
     </div>
 {% endblock confirm_delete %}
{% block javascript_footer %}
    {{ parent() }}
    {{ include('js/datatables.tpl') }}
{% endblock javascript_footer %}

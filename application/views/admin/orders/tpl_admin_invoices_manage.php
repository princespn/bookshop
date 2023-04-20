<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-5">
        <h2 class="sub-header block-title"><?= i('fa fa-pencil') ?> <?= lang(CONTROLLER_FUNCTION . '_' . singular(CONTROLLER_CLASS)) ?></h2>
    </div>
    <div class="col-md-7 text-right">
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('left', CONTROLLER_METHOD, $row['prev']) ?>
            <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $id) ?>" data-toggle="modal"
               data-target="#confirm-delete" href="#"
               class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
                        class="hidden-xs"><?= lang('delete') ?></span></a>
		<?php endif; ?>
        <a href="<?= admin_url(CONTROLLER_CLASS) ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
                    class="hidden-xs"><?= lang('view_invoices') ?></span></a>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('right', CONTROLLER_METHOD, $row['next']) ?>
		<?php endif; ?>
    </div>
</div>
<hr/>
<div class="box-info">
    <ul class="resp-tabs nav nav-tabs text-capitalize" role="tablist">
        <li class="active"><a href="#view" role="tab" data-toggle="tab"><?= lang('details') ?></a></li>
		<?php if (!(is_disabled('update', TRUE))): ?>
            <li><a href="#update" role="tab" data-toggle="tab"><?= lang('update') ?></a></li>
            <li><a href="#transactions" role="tab" data-toggle="tab"><?= lang('transactions') ?></a></li>
			<?php if (config_enabled('affiliate_marketing')): ?>
                <li><a href="#commissions" role="tab" data-toggle="tab"><?= lang('commissions') ?></a></li>
			<?php endif; ?>
		<?php endif; ?>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade in active" id="view">
            <h3 class="text-capitalize">
				<?= i('fa fa-file-text-o') ?> <?= lang('invoice_details') ?>
                <span class="pull-right hidden-xs">
                            <?php if (!empty($row['order_number'])): ?>
                                <a href="<?= admin_url('orders/update/' . $row['order_id']) ?>"
                                   class="btn btn-primary hidden-xs text-center"><?= i('fa fa-file-text-o') ?> <?= lang('order') ?>
                                    # <?= $row['order_number'] ?></a>
                            <?php endif; ?>
                    <a href="<?= admin_url('invoices/print_copy/' . $id) ?>" target="_blank" id="print"
                       class="btn btn-default block-phone">
						<?= i('fa fa-print') ?> <?= lang('print_invoice') ?>
					</a>
                    <?php if ($row['payment_status_id'] == '1'): ?>
                    <a href="<?=admin_url('members/login_member/' . $row['member_id'] . '/checkout-invoice-payment-' . $row['invoice_id'])?>"
                       class="btn btn-info block-phone" target="_blank"> <?= i('fa fa-user') ?> <?= lang('login_to_members_area') ?></a>
                    <?php endif; ?>
                </span>
            </h3>
            <hr/>
            <div clas="row">
                <div id="invoice-details" class="col-md-7 col-md-offset-2 animated fadeIn">
                    <div class="box-info">
                        <div class="row">
                            <div class="col-md-9">
                                <h3 class="text-capitalize">
									<?= lang('invoice') ?> #<?= $row['invoice_number'] ?>
                                </h3>
                            </div>
                            <div class="col-md-3 text-right">
                                <h3 class="text-capitalize">
                                    <strong id="payment_status" style="color: <?= $row['color'] ?>">
										<?= $row['payment_status'] ?>
                                    </strong>
                                </h3>
                            </div>
                        </div>
                        <hr/>
                        <div class="row">
                            <div class="col-sm-6">
								<?php if (!empty($row['date_purchased'])): ?>
                                    <h5 class="text-capitalize"><strong><?= lang('date_purchased') ?></strong>
										<?= display_date($row['date_purchased']) ?></h5>
								<?php endif; ?>
                            </div>
                            <div class="col-sm-6">
                                <h5 class="text-capitalize"><strong><?= lang('due_date') ?></strong>
									<?= display_date($row['due_date']) ?></h5>
                            </div>
                        </div>
                        <hr/>
                        <div id="address-box" class="row">
                            <div class="col-xs-6">
                                <address>
                                    <h5 class="text-capitalize"><strong><?= lang('bill_to') ?></strong></h5>
                                    <hr/>
                                    <h5 id="customer_name">
                                        <?php if (!empty($row['member_id'])):?>
                                        <a href="<?=admin_url('members/login_member/' . $row['member_id'] . '/members-invoices-details-' . $row['invoice_id'])?>" target="_blank"> <?= $row['customer_name'] ?></a>
                                                <?php else: ?>
                                        <?= $row['customer_name'] ?>
                                        <?php endif; ?>
                                    </h5>
                                    <h5 id="customer_company"><?= $row['customer_company'] ?></h5>
                                    <h5 id="customer_primary_email"><?= $row['customer_primary_email'] ?></h5>
                                    <h5 id="customer_telephone"><?= $row['customer_telephone'] ?></h5>
                                    <h5>
                                        <span id="customer_address_1"><?= $row['customer_address_1'] ?></span>
                                        <span id="customer_address_2"><?= $row['customer_address_2'] ?></span>
                                    </h5>
                                    <h5>
                                        <span id="customer_city"><?= $row['customer_city'] ?></span>
                                        <span id="region_code"><?= $row['customer_state_code'] ?></span>
                                        <span id="customer_postal_code"><?= $row['customer_postal_code'] ?></span>
                                    </h5>
                                    <h5 id="country_name"><?= $row['customer_country_name'] ?></h5>
                                </address>
                            </div>
                            <div class="col-xs-6">
                                <div id="shipping_data"
                                     class="<?php if (empty($row['shipping_address_1'])): ?> hide<?php endif; ?>">
                                    <address>
                                        <h5 class="text-capitalize"><strong><?= lang('ship_to') ?></strong></h5>
                                        <hr/>
                                        <h5 id="shipping_name"><?= $row['shipping_name'] ?></h5>
                                        <h5 id="shipping_company"><?= $row['shipping_company'] ?></h5>
                                        <h5>
                                            <span id="shipping_address_1"><?= $row['shipping_address_1'] ?></span>
                                            <span id="shipping_address_2"><?= $row['shipping_address_2'] ?></span>
                                        </h5>
                                        <h5>
                                            <span id="shipping_city"><?= $row['shipping_city'] ?></span>
                                            <span id="shipping_state_code"><?= $row['shipping_state_code'] ?></span>
                                            <span id="shipping_postal_code"><?= $row['shipping_postal_code'] ?></span>
                                        </h5>
                                        <h5 id="shipping_country_name"><?= $row['shipping_country_name'] ?></h5>
                                    </address>
                                </div>

                            </div>
                        </div>
                        <hr/>
                        <div id="items-box">
                            <div class="row">
                                <div class="col-md-12">

									<?php if (empty($row['items'])): ?>
                                        <div class="alert alert-warning">
											<?= i('fa fa-exclamation-circle') ?> <?= lang('no_products_added_to_invoice') ?>
                                        </div>
									<?php else: ?>
                                        <div class="row text-capitalize">
                                            <div class="col-sm-2 text-center hidden-xs">
                                                <h5>
                                                    <h5><strong><?= lang('sku') ?></strong></h5>
                                            </div>
                                            <div class="col-sm-5 col-xs-6">
                                                <h5><strong><?= lang('product_name') ?></strong></h5></div>
                                            <div class="col-xs-2 text-center">
                                                <h5><strong><?= lang('unit') ?></strong></h5></div>
                                            <div class="col-sm-1 col-xs-2 text-center">
                                                <h5><strong><?= lang('qty') ?></strong></h5></div>
                                            <div class="col-xs-2 text-center">
                                                <h5><strong><?= lang('price') ?></strong></h5></div>
                                        </div>
                                        <hr/>
										<?php foreach ($row['items'] as $v): ?>
                                            <div class="row">
                                                <div class="col-sm-2 text-center hidden-xs">
                                                    <h5><?= $v['product_sku'] ?></h5>
                                                </div>
                                                <div class="col-sm-5 col-xs-6">
                                                    <h5><?= $v['invoice_item_name'] ?></h5>
                                                    <p style="overflow: auto;"><?= nl2br($v['item_notes']) ?></p>
                                                </div>
                                                <div class="col-xs-2 text-center">
                                                    <h5><?= format_amount($v['unit_price'], FALSE) ?></h5>
                                                </div>
                                                <div class="col-sm-1 col-xs-2 text-center">
                                                    <h5 class="view-field"><?= $v['quantity'] ?></h5>
                                                </div>
                                                <div class="col-xs-2 text-center">
                                                    <h5><?= format_amount($v['unit_price'] * $v['quantity'], FALSE) ?></h5>
                                                </div>
                                            </div>
                                            <hr/>
										<?php endforeach; ?>
									<?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-8">
                                <h5 class="text-capitalize"><strong><?= lang('invoice_notes') ?></strong></h5>
                                <hr/>
                                <div id="invoice_notes">
									<?= $row['invoice_notes'] ?>
                                </div>
                                <hr class="visible-xs"/>
                            </div>
                            <div class="col-sm-4">
                                <h5 class="text-capitalize"><strong><?= lang('totals') ?></strong></h5>
                                <hr/>
                                <div id="view-totals" class="text-capitalize">
									<?php foreach ($row['totals'] as $k => $v): ?>
										<?php if ($v['type'] == 'sub_total'): ?>
                                            <div class="row">
                                                <div class="col-xs-7 control-label">
                                                    <h5><?= lang('sub_total') ?></h5>
                                                </div>
                                                <div class="col-xs-5">
                                                    <h5><?= format_amount($v['amount']) ?></span></h5>
                                                </div>
                                            </div>
										<?php elseif ($v['type'] == 'points'): ?>
                                            <div class="row">
                                                <div class="col-xs-7 control-label">
                                                    <h5><?= lang('points') ?></h5></div>

                                                <div class="col-xs-5">
                                                    <h5><?= (int)$v['amount'] ?></h5>
                                                </div>
                                            </div>
										<?php elseif ($v['type'] == 'shipping'): ?>
                                            <div class="row">
                                                <div class="col-xs-7 control-label">
                                                    <h5><?= lang('shipping') ?></h5></div>
                                                <div class="col-xs-5">
                                                    <h5><?= format_amount($v['amount']) ?></h5>
                                                </div>
                                            </div>

										<?php elseif ($v['type'] == 'tax'): ?>
                                            <div class="row">
                                                <div class="col-xs-7 control-label">
                                                    <h5><?= lang('taxes') ?></h5></div>

                                                <div class="col-xs-5">
                                                    <h5> <?= format_amount($v['amount']) ?></h5>
                                                </div>
                                            </div>
										<?php elseif ($v['type'] == 'total'): ?>
                                            <hr/>
                                            <div class="row">
                                                <div class="col-xs-7 control-label">
                                                    <h5><?= lang('total') ?></h5></div>

                                                <div class="col-xs-5">
                                                    <h5><?= format_amount($v['amount']) ?></h5>
                                                </div>
                                            </div>
										<?php endif; ?>
									<?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <?php if (!empty($row['payments'])): ?>

                            <div class="row">
                                <div class="col-md-12">
                                    <h5 class="text-capitalize"><strong><?= lang('payment') ?></strong></h5>
                                    <hr/>
                                </div>
                            </div>
                            <div class="row text-capitalize">
                                <div class="col-md-2 col-xs-4 text-center"><h5><?= lang('date') ?></h5></div>
                                <div class="col-md-4 col-xs-6"><h5><?= lang('transaction_id') ?></h5></div>
                                <div class="col-sm-4 hidden-xs"><h5><?= lang('description') ?></h5></div>
                                <div class="col-xs-2 text-center"><h5><?= lang('amount') ?></h5></div>
                            </div>
                            <hr/>
							<?php foreach ($row['payments'] as $v): ?>
                                <div class="row">
                                    <div class="col-md-2 col-xs-4 text-center"><?= display_date($v['date']) ?></div>
                                    <div class="col-md-4 col-xs-6"><?= $v['transaction_id'] ?></div>
                                    <div class="col-sm-4 hidden-xs"><strong
                                                class="text-capitalize"><?= $v['method'] ?></strong>
                                        - <?= $v['description'] ?></div>
                                    <div class="col-xs-2 text-center"><?= format_amount($v['amount']) ?></div>
                                </div>
                                <hr/>
							<?php endforeach; ?>
						<?php endif; ?>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <a href="<?=EXTERNAL_IP_LOOKUP?><?=$row['ip_address']?>" target="_blank"><?=lang('ip_address')?> <?=$row['ip_address']?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<?php if (!(is_disabled('update', TRUE))): ?>
            <div class="tab-pane fade in" id="update">
				<?= form_open('', 'role="form" id="invoice-form" class="form-horizontal"') ?>
                <h3 class="hidden-xs hidden-sm text-capitalize"><?= i('fa fa-file-text-o') ?> <?= lang('update_invoice') ?></h3>
                <hr class="hidden-xs"/>
                <div class="row">
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading text-capitalize">
										<?= tb_header('billing_information', '', FALSE, '', 'fa fa-credit-card') ?>
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="customer_name" class="col-md-4 control-label">
														<?= lang('customer_name') ?>
                                                    </label>
                                                    <div class="col-md-8">
                                                        <input type="text" name="customer_name"
                                                               value="<?= set_value('customer_name', $row['customer_name']) ?>"
                                                               class="form-control"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="customer_company" class="col-md-4 control-label">
														<?= lang('company') ?>
                                                    </label>
                                                    <div class="col-md-8">
                                                        <input type="text" name="customer_company"
                                                               value="<?= set_value('customer_company', $row['customer_company']) ?>"
                                                               class="form-control"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="customer_address_1" class="col-md-4 control-label">
														<?= lang('customer_address_1') ?>
                                                    </label>
                                                    <div class="col-md-8">
                                                        <input type="text" name="customer_address_1"
                                                               value="<?= set_value('customer_address_1', $row['customer_address_1']) ?>"
                                                               class="form-control"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="customer_address_2" class="col-md-4 control-label">
														<?= lang('customer_address_2') ?>
                                                    </label>
                                                    <div class="col-md-8">
                                                        <input type="text" name="customer_address_2"
                                                               value="<?= set_value('customer_address_2', $row['customer_address_2']) ?>"
                                                               class="form-control"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="customer_city" class="col-md-4 control-label">
														<?= lang('customer_city') ?>
                                                    </label>
                                                    <div class="col-md-8">
                                                        <input type="text" name="customer_city"
                                                               value="<?= set_value('customer_city', $row['customer_city']) ?>"
                                                               class="form-control"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="customer_state"
                                                           class="col-md-4 control-label"><?= lang('state_province') ?></label>
                                                    <div class="col-md-8">
														<?= form_dropdown('customer_state', load_regions($row['customer_country']), $row['customer_state'], 'id="customer-state" class="form-control required"') ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="customer_country_name"
                                                           class="col-md-4 control-label"><?= lang('country') ?></label>
                                                    <div class="col-md-8">
														<?= form_dropdown('customer_country', array($row['customer_country'] => $row['customer_country_name']), '', 'onchange="updateregion(\'customer\')"id="customer-country" class="country form-control required"') ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="customer_postal_code" class="col-md-4 control-label">
														<?= lang('postal_code') ?>
                                                    </label>
                                                    <div class="col-md-8">
                                                        <input type="text" name="customer_postal_code"
                                                               value="<?= set_value('customer_postal_code', $row['customer_postal_code']) ?>"
                                                               class="form-control"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading text-capitalize">
										<?= tb_header('shipping_information', '', FALSE, '', 'fa fa-truck') ?>
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="shipping_name" class="col-md-4 control-label">
														<?= lang('shipping_name') ?>
                                                    </label>
                                                    <div class="col-md-8">
                                                        <input type="text" name="shipping_name"
                                                               value="<?= set_value('shipping_name', $row['shipping_name']) ?>"
                                                               class="form-control"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="shipping_company" class="col-md-4 control-label">
														<?= lang('shipping_company') ?>
                                                    </label>
                                                    <div class="col-md-8">
                                                        <input type="text" name="shipping_company"
                                                               value="<?= set_value('shipping_company', $row['shipping_company']) ?>"
                                                               class="form-control"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="shipping_address_1" class="col-md-4 control-label">
														<?= lang('shipping_address_1') ?>
                                                    </label>
                                                    <div class="col-md-8">
                                                        <input type="text" name="shipping_address_1"
                                                               value="<?= set_value('shipping_address_1', $row['shipping_address_1']) ?>"
                                                               class="form-control"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="shipping_address_2" class="col-md-4 control-label">
														<?= lang('shipping_address_2') ?>
                                                    </label>
                                                    <div class="col-md-8">
                                                        <input type="text" name="shipping_address_2"
                                                               value="<?= set_value('shipping_address_2', $row['shipping_address_2']) ?>"
                                                               class="form-control"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="shipping_city" class="col-md-4 control-label">
														<?= lang('shipping_city') ?>
                                                    </label>
                                                    <div class="col-md-8">
                                                        <input type="text" name="shipping_city"
                                                               value="<?= set_value('shipping_city', $row['shipping_city']) ?>"
                                                               class="form-control"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="shipping_state"
                                                           class="col-md-4 control-label"><?= lang('state_province') ?></label>
                                                    <div class="col-md-8">
														<?= form_dropdown('shipping_state', load_regions($row['shipping_country']), $row['shipping_state'], 'id="shipping-state" class="form-control required"') ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="shipping_country_name"
                                                           class="col-md-4 control-label"><?= lang('country') ?></label>
                                                    <div class="col-md-8">
														<?= form_dropdown('shipping_country', array($row['shipping_country'] => $row['shipping_country_name']), '', 'onchange="updateregion(\'shipping\')"id="shipping-country" class="country form-control required"') ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="shipping_postal_code" class="col-md-4 control-label">
														<?= lang('postal_code') ?>
                                                    </label>
                                                    <div class="col-md-8">
                                                        <input type="text" name="shipping_postal_code"
                                                               value="<?= set_value('shipping_postal_code', $row['shipping_postal_code']) ?>"
                                                               class="form-control"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
						<?php if (!empty($row['order_number'])): ?>
                            <a href="<?= admin_url('orders/update/' . $row['order_id']) ?>"
                               class="btn btn-lg btn-block btn-primary text-center"><?= i('fa fa-file-text-o') ?> <?= lang('order') ?>
                                # <?= $row['order_number'] ?></a>
                            <hr/>
						<?php endif; ?>
                        <div class="panel panel-default">
                            <div class="panel-heading text-capitalize">
								<?= tb_header('invoice_information', '', FALSE, '', 'fa fa-file-o') ?>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="invoice_number"
                                                   class="col-md-4 control-label"><?= i('fa fa-file-ext-o') ?> <?= lang('invoice_number') ?></label>
                                            <div class="col-md-8">
                                                <input type="text" name="invoice_number"
                                                       value="<?= set_value('invoice_number', $row['invoice_number']) ?>"
                                                       class="form-control required"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
											<?= lang('payment_status', 'payment_status_id', array('class' => 'col-md-4 control-label')) ?>
                                            <div class="col-md-8">
												<?= form_dropdown('payment_status_id', options('payment_statuses'), $row['payment_status_id'], 'class="form-control"') ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
											<?= lang('invoice_date', 'date_purchased', array('class' => 'col-md-4 control-label')) ?>
                                            <div class="col-md-8">
                                                <div class="input-group">
                                                    <input type="text" name="date_purchased"
                                                           value="<?= set_value('date_purchased', $row['date_purchased_formatted']) ?>"
                                                           class="form-control datepicker-input required"/>
                                                    <span class="input-group-addon"><i
                                                                class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
											<?= lang('due_date', 'due_date', array('class' => 'col-md-4 control-label')) ?>
                                            <div class="col-md-8">
                                                <div class="input-group">
                                                    <input type="text" name="due_date"
                                                           value="<?= set_value('due_date', $row['due_date_formatted']) ?>"
                                                           class="form-control datepicker-input required"/>
                                                    <span class="input-group-addon"><i
                                                                class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="customer_primary_email" class="col-md-4 control-label">
												<?= i('fa fa-envelope') ?> <?= lang('email_address') ?>
                                            </label>
                                            <div class="col-md-8">
                                                <input type="text" name="customer_primary_email"
                                                       value="<?= set_value('customer_primary_email', $row['customer_primary_email']) ?>"
                                                       class="email form-control required"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
											<?= lang('referred_by', 'affiliate_id', array('class' => 'col-md-4 control-label')) ?>
                                            <div class="col-md-8">
                                                <select id="affiliate_id" class="form-control select2"
                                                        name="affiliate_id">
                                                    <option value="<?= $row['affiliate_id'] ?>"
                                                            selected><?= $row['affiliate_username'] ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="customer_telephone"
                                                   class="col-md-4 control-label"><?= i('fa fa-phone') ?> <?= lang('phone') ?></label>
                                            <div class="col-md-8">
                                                <input type="text" name="customer_telephone"
                                                       value="<?= set_value('customer_telephone', $row['customer_telephone']) ?>"
                                                       class="form-control"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="customer_fax"
                                                   class="col-md-4 control-label"><?= i('fa fa-fax') ?> <?= lang('fax') ?></label>
                                            <div class="col-md-8">
                                                <input type="text" name="customer_fax"
                                                       value="<?= set_value('customer_fax', $row['customer_fax']) ?>"
                                                       class="form-control"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading text-capitalize">
                        <div class="row text-capitalize">
                            <div class="col-md-9"><?= tb_header('item_description', '', FALSE, '', 'fa fa-shopping-cart') ?></div>
                            <div class="col-md-1 text-center visible-lg"><?= tb_header('quantity', '', FALSE) ?></div>
                            <div class="col-md-1 text-center visible-lg"><?= tb_header('price', '', FALSE) ?></div>
                            <div class="col-md-1"></div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div id="item_rows">
							<?php if (!empty($row['items'])): ?>
								<?php foreach ($row['items'] as $k => $v): ?>
                                    <div id="itemdiv-<?= $k ?>">
                                        <div class="row">
                                            <div class="r col-md-9">
                                                <input type="hidden" name="items[<?= $k ?>][invoice_item_id]"
                                                       value="<?= $v['invoice_item_id'] ?>"/>
                                                <input type="text" name="items[<?= $k ?>][invoice_item_name]"
                                                       value="<?= $v['invoice_item_name'] ?>"
                                                       placeholder="<?= lang('item_description') ?>"
                                                       class="form-control required"/>
                                            </div>
                                            <div class="r col-md-1 text-center">
                                                <input type="text" name="items[<?= $k ?>][quantity]"
                                                       value="<?= $v['quantity'] ?>"
                                                       placeholder="1"
                                                       class="calc qty form-control digits required"/>
                                            </div>
                                            <div class="r col-md-1 text-center">
                                                <input type="text" name="items[<?= $k ?>][unit_price]"
                                                       placeholder="0.00"
                                                       value="<?= input_amount($v['unit_price']) ?>"
                                                       class="calc form-control number required"/>
                                            </div>
                                            <div class="col-md-1 text-right">
												<?php if ($k > 0): ?>
                                                    <a href="javascript:remove_item('#itemdiv-<?= $k ?>')"
                                                       class="btn btn-danger block-phone <?= is_disabled('delete') ?>"><?= i('fa fa-minus') ?></a>
												<?php endif; ?>
                                                <a class="tip btn btn-default block-phone"
                                                   title="<?= lang('view_item_notes') ?>"
                                                   data-toggle="collapse"
                                                   href="#item-notes-<?= $k ?>" aria-expanded="false"
                                                   aria-controls="item-notes-<?= $k ?>"><?= i('fa fa-list') ?></a>
                                            </div>
                                        </div>
                                        <div id="item-notes-<?= $k ?>" class="collapse">
                                            <hr/>
                                            <div class="row">
                                                <div class="col-md-1">
                                                    <small><?= lang('item_specific_notes') ?></small>
                                                </div>
                                                <div class="col-md-8">
                                    <textarea name="items[<?= $k ?>][item_notes]" class="form-control"
                                              rows="3"><?= $v['item_notes'] ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <hr/>
                                    </div>
								<?php endforeach; ?>
							<?php endif; ?>
                        </div>
                        <div class="row text-right">
                            <div class="col-md-12">
                                <a href="javascript:add_item(<?= count($row['items']) + 1 ?>)"
                                   class="btn btn-default block-phone"><?= i('fa fa-plus') ?> <?= lang('add_more_items') ?></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <div class="row text-capitalize">
                                    <div class="col-md-12">
										<?= tb_header('invoice_notes', '', FALSE, '', 'fa fa-list') ?>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">
                                    <textarea name="invoice_notes" class="form-control"
                                              rows="10"> <?= $row['invoice_notes'] ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="panel panel-default">
                            <div class="panel-heading text-capitalize">
								<?= tb_header('totals', '', FALSE, '', 'fa fa-shopping-cart') ?>
                            </div>
                            <div class="panel-body">
								<?php foreach ($row['totals'] as $k => $v): ?>
									<?php if ($v['type'] == 'sub_total'): ?>
                                        <div class="form-group">
                                            <label class="col-md-7 control-label">
                                                <strong><?= lang('sub_total') ?></strong>
                                            </label>
                                            <div class="col-md-5">
                                                <label class="form-control">
                                                <span class="sub-total">
                                                    <?= format_amount($v['amount'], FALSE) ?></span>
                                                </label>
                                                <input type="hidden" name="sub_total_id"
                                                       value="<?= $v['invoice_total_id'] ?>"/>
                                            </div>
                                        </div>
									<?php elseif ($v['type'] == 'points'): ?>
                                        <div class="form-group">
                                            <label class="col-md-7 control-label">
                                                <strong><?= lang('points') ?></strong>
                                            </label>
                                            <div class="col-md-5">
                                                <input type="number" name="totals[<?= $v['invoice_total_id'] ?>]"
                                                       value="<?= (int)$v['amount'] ?>" class="form-control"/>
                                            </div>
                                        </div>
									<?php elseif ($v['type'] == 'shipping'): ?>
                                        <div class="form-group">
                                            <label class="col-md-7 control-label">
                                                <strong><?= lang('shipping') ?></strong></label>

                                            <div class="col-md-5">
                                                <input id="shipping" name="totals[<?= $v['invoice_total_id'] ?>]"
                                                       type="text" value="<?= input_amount($v['amount']) ?>"
                                                       class="calc number form-control"/>
                                            </div>
                                        </div>
									<?php elseif ($v['type'] == 'tax'): ?>
                                        <div class="form-group">
                                            <label class="col-md-7 control-label">
                                                <strong><?= lang('taxes') ?></strong></label>

                                            <div class="col-md-5">
                                                <input id="taxes" name="totals[<?= $v['invoice_total_id'] ?>]"
                                                       type="text"
                                                       value="<?= input_amount($v['amount']) ?>"
                                                       class="calc number form-control"/>
                                            </div>
                                        </div>
									<?php elseif ($v['type'] == 'total'): ?>
                                        <div class="form-group">
                                            <label class="col-md-7 control-label">
                                                <strong><?= lang('total') ?></strong></label>

                                            <div class="col-md-5">
                                                <label class="form-control">
                                                    <span class="total"><?= format_amount($v['amount'], FALSE) ?></span>
                                                </label>
                                                <input id="total" name="totals[<?= $v['invoice_total_id'] ?>]"
                                                       type="hidden"
                                                       value="<?= input_amount($v['amount']) ?>"/>
                                                <input type="hidden" name="total_id"
                                                       value="<?= $v['invoice_total_id'] ?>"/>
                                            </div>
                                        </div>
									<?php endif; ?>
								<?php endforeach; ?>
                                <button id="update-button"
                                        class="btn btn-info btn-lg btn-block"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
                            </div>
                        </div>
                    </div>
                </div>
				<?= form_hidden('invoice_id', $id) ?>
				<?= form_close() ?>
            </div>
            <div class="tab-pane fade in" id="transactions">
                <h3 class="text-capitalize">
					<?= i('fa fa-file-text-o') ?> <?= lang('invoice_transactions') ?>
                </h3>
                <hr/>
				<?php if (!empty($row['payments'])): ?>
                    <div class="row hidden-xs">
                        <div class="col-md-1 text-center"><h5><?= lang('date') ?></h5></div>
                        <div class="col-md-3 text-center"><h5><?= lang('transaction_id') ?></h5></div>
                        <div class="col-md-2 text-center"><h5><?= lang('method') ?></h5></div>
                        <div class="col-md-3"><h5><?= lang('description') ?></h5></div>
                        <div class="col-md-1 text-center"><h5><?= lang('amount') ?></h5></div>
                        <div class="col-md-2"></div>
                    </div>
                    <hr/>
					<?php foreach ($row['payments'] as $v): ?>
                        <div class="row">
                            <div class="col-md-1 text-center"><?= display_date($v['date']) ?></div>
                            <div class="col-md-3 text-center"><?= $v['transaction_id'] ?></div>
                            <div class="col-md-2 text-center"><strong
                                        class="text-capitalize"><?= $v['method'] ?></strong></div>
                            <div class="col-md-3"><?= $v['description'] ?></div>
                            <div class="col-md-1 text-center"><?= format_amount($v['amount']) ?></div>
                            <div class="col-md-2 text-right">
                                <a data-href="<?= admin_url(TBL_INVOICE_PAYMENTS . '/delete/' . $v['invoice_payment_id']) ?>"
                                   data-toggle="modal" data-target="#confirm-delete" href="#"
                                   class="md-trigger btn btn-danger hidden-xs <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
                                <a href="<?= admin_url(TBL_INVOICE_PAYMENTS . '/update/' . $v['invoice_payment_id']) ?>"
                                   class="btn btn-default"><?= i('fa fa-pencil') ?></a>
                            </div>
                        </div>
                        <hr/>
					<?php endforeach; ?>
				<?php endif; ?>
                <div class="row">
                    <div class="col-md-12 text-right">
	                    <?php if ($row['payment_status_id'] == '1' && !empty($credits)): ?>
                            <a href="<?= admin_url(TBL_INVOICE_PAYMENTS . '/apply_credits/' . $row['invoice_id']) ?>"
                                class="btn btn-default navbar-btn" id="apply-credits"
			                    <?= is_disabled('update', TRUE) ?>>
			                    <?= i('fa fa-refresh') ?> <?=lang('apply_credits')?>
                            </a>
	                    <?php endif; ?>
                        <a href="<?= admin_url(TBL_INVOICE_PAYMENTS . '/create/' . $row['invoice_id']) ?>"
                           class="btn btn-info">
							<?= i('fa fa-plus') ?> <?= lang('add_transaction') ?></a>
                    </div>
                </div>
            </div>
			<?php if (config_enabled('affiliate_marketing')): ?>
                <div class="tab-pane fade in" id="commissions">
                    <h3 class="text-capitalize">
						<?= i('fa fa-file-text-o') ?> <?= lang('referral_commissions') ?>
                    </h3>
                    <hr/>
					<?php if (!empty($row['commissions'])): ?>
                        <div class="row hidden-xs text-center">
                            <div class="col-md-2"><h5><?= lang('status') ?></h5></div>

							<?php if (config_option('sts_affiliate_commission_levels') > 1): ?>
                                <div class="col-md-3"><h5><?= lang('username') ?></h5></div>
                                <div class="col-md-2"><h5><?= lang('commission') ?></h5></div>
                                <div class="col-md-3"><h5><?= lang('level') ?></h5></div>
							<?php else: ?>
                                <div class="col-md-3"><h5><?= lang('username') ?></h5></div>
                                <div class="col-md-3"><h5><?= lang('commission') ?></h5></div>
							<?php endif; ?>
                            <div class="col-md-2"></div>
                        </div>
                        <hr/>
						<?php foreach ($row['commissions'] as $v): ?>
                            <div class="row text-center">
                                <div class="col-md-2">
									<?php if ($v['comm_status'] == 'pending'): ?>
                                        <span class="label label-danger"> <?= lang('pending') ?></span>
									<?php elseif ($v['comm_status'] == 'unpaid') : ?>
                                        <span class="label label-warning"><?= lang('unpaid') ?></span>
									<?php
									else : ?>
                                        <span class="label label-success"><?= lang('paid') ?></span>
									<?php endif; ?>
                                </div>

								<?php if (config_option('sts_affiliate_commission_levels') > 1): ?>
                                    <div class="col-md-3">
                                        <a href="<?= admin_url(TBL_MEMBERS . '/update/' . $v['member_id']) ?>"><?= $v['username'] ?></a>
                                    </div>
                                    <div class="col-md-2"><?= format_amount($v['commission_amount']) ?></div>
                                    <div class="col-md-3"><?= $v['commission_level'] ?></div>

								<?php else: ?>
                                    <div class="col-md-3">
                                        <a href="<?= admin_url(TBL_MEMBERS . '/update/' . $v['member_id']) ?>"><?= $v['username'] ?></a>
                                    </div>
                                    <div class="col-md-3"><?= format_amount($v['commission_amount']) ?></div>
								<?php endif; ?>
                                <div class="col-md-2 text-right">
                                    <a data-href="<?= admin_url(TBL_AFFILIATE_COMMISSIONS . '/delete/' . $v['comm_id']) ?>"
                                       data-toggle="modal" data-target="#confirm-delete" href="#"
                                       class="md-trigger btn btn-danger hidden-xs <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
                                    <a href="<?= admin_url(TBL_AFFILIATE_COMMISSIONS . '/update/' . $v['comm_id']) ?>"
                                       class="btn btn-default"><?= i('fa fa-pencil') ?></a>
                                </div>
                            </div>
                            <hr/>
						<?php endforeach; ?>
					<?php endif; ?>
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <a href="<?= admin_url(TBL_AFFILIATE_COMMISSIONS . '/create/' . $row['invoice_id']) ?>"
                               class="btn btn-info block-phone">
								<?= i('fa fa-plus') ?> <?= lang('add_commission') ?>
                            </a>
                        </div>
                    </div>
                </div>
			<?php endif; ?>
		<?php endif; ?>
    </div>
</div>
<script>

    var next_item = <?=count($row['items'])?> +1;

    $(document).ready(function () {
        $('.calc').bind('keyup', function () {
            updateTotals();
        });
    });

    function updateTotals() {
        $.ajax({
            url: '<?=admin_url('invoices/invoice_totals/')?>',
            type: 'POST',
            dataType: 'json',
            data: $('#invoice-form').serialize(),
            success: function (data) {
                var subTotal = data.toFixed(2);
                $("span.sub-total").html(subTotal);
                var Ship = parseFloat($('#shipping').val()) || 0;
                var Tax = parseFloat($('#taxes').val()) || 0;
                var Total = parseFloat(data) + Ship + Tax;
                $('span.total').text(Total.toFixed(2));
                $('#total').val(Total.toFixed(2));
            },
            error: function (xhr, ajaxOptions, thrownError) {
				<?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }

    function remove_item(id) {
        $(id).addClass('animated fadeOutDown');
        $(id).fadeOut(300, function () {
            $(this).remove();
            updateTotals();
        });
    }

    function add_item(item) {

        var html = '<div id="itemdiv-' + next_item + '" class="animated fadeIn">';
        html += '   <div class="row">';
        html += '    <div class="r col-md-9">';
        html += '   <input type="text" name="items[' + next_item + '][invoice_item_name]" value="" placeholder="<?=lang('item_description')?>" class="form-control required" />';
        html += '    </div>';
        html += '    <div class="r col-md-1 text-center">';
        html += '        <input type="text" name="items[' + next_item + '][quantity]" value="1" placeholder="1" class="calc qty form-control digits required" />';
        html += '        </div>';
        html += '    <div class="r col-md-1 text-center">';
        html += '        <input type="text" name="items[' + next_item + '][unit_price]" placeholder="0.00" class="calc form-control number required" />';
        html += '        </div>';
        html += '    <div class="r col-md-1 text-right">';
        html += '   <a href="javascript:remove_item(\'#itemdiv-' + next_item + '\')" class="btn btn-danger block-phone <?=is_disabled('delete')?>"><?=i('fa fa-minus')?></a>';
        html += '        <a class="tip btn btn-default block-phone" title="<?=lang('view_item_notes')?>"  data-toggle="collapse" href="#item-notes-' + next_item + '" aria-expanded="false" aria-controls="item-notes-' + next_item + '"><?=i('fa fa-list')?></a>';
        html += '    </div>';
        html += '    </div>';
        html += '    <div id="item-notes-' + next_item + '" class="collapse">';
        html += '        <hr />';
        html += '        <div class="row">';
        html += '        <div class="r col-md-1"><small><?=lang('item_specific_notes')?></small></div>';
        html += '    <div class="col-md-8"><textarea name="items[' + next_item + '][item_notes]" class="form-control" rows="3"></textarea></div>';
        html += '    </div>';
        html += '    </div>';
        html += '    <hr />';
        html += '    </div>';

        $('#item_rows').append(html);
        $('.calc').bind('keyup', function () {
            updateTotals();
        });
        next_item++;
    }

    $("#affiliate_id").select2({
        ajax: {
            url: '<?=admin_url(TBL_MEMBERS . '/search/ajax/')?>',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    username: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data, page) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.member_id,
                            text: item.username
                        }
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });

    select_country('#shipping-country');
    select_country('#customer-country');

    function select_country(id) {
        //search countries
        $(id).select2({
            ajax: {
                url: '<?=site_url('search/search_countries/')?>',
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
    }

    function updateregion(type) {
        $.post('<?=site_url('search/load_regions/state')?>', {country_id: $('#' + type + '-country').val()},
            function (data) {
                $('#' + type + '-state').html(data);
                $(".s2").select2();
            }
        );
    }

    $("#invoice-form").validate({
        ignore: "",
        submitHandler: function (form) {
            $.ajax({
                url: '<?=current_url()?>',
                type: 'POST',
                dataType: 'json',
                data: $('#invoice-form').serialize(),
                beforeSend: function () {
                    $('#update-button').button('loading');
                },
                complete: function () {
                    $('#update-button').button('reset');
                },
                success: function (response) {
                    if (response.type == 'success') {
                        $('.alert-danger').remove();
                        $('.form-control').removeClass('error');

                        if (response.redirect) {
                            location.href = response.redirect;
                        }
                        else {
                            $('#response').html('<?=alert('success')?>');

                            setTimeout(function () {
                                $('.alert-msg').fadeOut('slow');
                            }, 5000);
                        }
                    }
                    else {
                        $('#response').html('<?=alert('error')?>');
                        if (response['error_fields']) {
                            $.each(response['error_fields'], function (key, val) {
                                $('#' + key).addClass('error');
                                $('#' + key).focus();
                            });
                        }
                    }

                    $('#msg-details').html(response.msg);
                },
                error: function (xhr, ajaxOptions, thrownError) {
					<?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        }
    });
</script>
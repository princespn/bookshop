<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-5">
        <h2 class="sub-header block-title">
			<?= i('fa fa-pencil') ?> <?= lang('update_order') ?>
        </h2>
    </div>
    <div class="col-md-7 text-right">
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('left', CONTROLLER_METHOD, $row['prev']) ?>
		<?php endif; ?>
        <a href="<?= admin_url(TBL_ORDERS) ?>" class="btn btn-primary">
			<?= i('fa fa-search') ?> <span class="hidden-xs"><?= lang('view_orders') ?></span>
        </a>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('right', CONTROLLER_METHOD, $row['next']) ?>
		<?php endif; ?>
    </div>
</div>
<hr/>
<div class="box-info">
    <ul class="resp-tabs nav nav-tabs text-capitalize" role="tablist">
        <li class="active"><a href="#edit" role="tab" data-toggle="tab"><?= lang('details') ?></a></li>
		<?php if (!is_disabled('update', TRUE)): ?>
            <li><a href="#notes" role="tab" data-toggle="tab"><?= lang('notes') ?></a></li>
            <li><a href="#tracking" role="tab" data-toggle="tab"><?= lang('shipping') ?></a></li>
		<?php endif; ?>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade in active" id="edit">
            <div class="row">
                <div class="col-sm-4">
                    <h3 class="text-capitalize">
						<?= lang('order') ?> #<?= $row['order_number'] ?>
                    </h3>
                </div>
                <div class="col-sm-8 text-right">
					<?php if ($row['order_status_id'] == '1'): ?>
                        <a href="<?= admin_url('orders/process/' . $id) ?>" class="btn btn-success">
							<?= i('fa fa-cog') ?>
                            <span class="hidden-xs"><?= lang('accept_and_process') ?></span>
                        </a>
					<?php endif; ?>
                    <a href="<?= admin_url('orders/packing_list/' . $id) ?>" target="_blank" id="print-list"
                       class="btn btn-default">
						<?= i('fa fa-print') ?>
                        <span class="hidden-xs"><?= lang('print_packing_list') ?></span>
                    </a>
					<?php if (!is_disabled('update', TRUE)): ?>
                        <button type="button" id="edit-order-button"
                                class="view-field btn btn-info <?= is_disabled('update', TRUE) ?>">
							<?= i('fa fa-edit') ?> <span class="hidden-xs"><?= lang('update_this_order') ?></span>
                        </button>
					<?php endif; ?>
                    <button type="button" id="view-order-button" class="update-field btn btn-info hide">
						<?= i('fa fa-search-plus') ?> <span class="hidden-xs"><?= lang('view_this_order') ?></span>
                    </button>
                </div>
            </div>
            <hr/>
            <div class="row">
                <div id="update-details" class="update-field col-lg-5 animated fadeIn hide">
                    <div class="box-info">
                        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="heading-one">
                                    <h5 class="panel-title text-capitalize">
                            <span id="step-one-heading">
                                <a data-toggle="collapse" data-parent="#accordion" href="#step-one" aria-expanded="true"
                                   aria-controls="step-one"
                                   class="panel-title"><?= lang('update_order_data') ?> <?= i('fa fa-caret-down') ?></a>
                            </span>
                                    </h5>
                                </div>
                                <div id="step-one" class="panel-collapse collapse in" role="tabpanel"
                                     aria-labelledby="heading-one">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-12">
												<?= form_open(admin_url('orders/update_order_profile'), 'role="form" id="update-profile-form" class="form-horizontal"') ?>
												<?= form_hidden('order_id', $id) ?>
                                                <h3 class="text-capitalize"> <?= lang('order_information') ?></h3>
                                                <span class="text-capitalize">
                                                <?= lang('update_order_and_billing_information_data') ?>
                                            </span>
                                                <hr/>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div id="profile">
                                                            <div class="form-group">
                                                                <label class="col-md-4 control-label">
                                                                    * <?= lang('order_status') ?></label>

                                                                <div class="col-md-6">
																	<?= form_dropdown('order_status_id', options('order_statuses'), $row['order_status_id'], 'class="form-control"') ?>
                                                                </div>
                                                            </div>
                                                            <hr/>
                                                            <div class="form-group">
                                                                <label class="col-md-4 control-label">
                                                                    * <?= lang('order_name') ?></label>

                                                                <div class="col-md-6">
																	<?= form_input('order_name', set_value('order_name', $row['order_name']), 'class="' . css_error('order_name') . 'se form-control required"') ?>
                                                                </div>
                                                            </div>
                                                            <hr/>
															<?php if (config_enabled('affiliate_marketing')): ?>
                                                                <div class="form-group">
                                                                    <label class="col-md-4 control-label">
																		<?= lang('referred_by') ?>
                                                                    </label>

                                                                    <div class="col-md-6">
                                                                        <select id="affiliate_id"
                                                                                class="form-control select2"
                                                                                name="affiliate_id">
                                                                            <option
                                                                                    value="0"><?= lang('enter_referral_username_if_any') ?></option>
																			<?php if (!empty($row['affiliate_id'])): ?>
                                                                                <option
                                                                                        value="<?= $row['affiliate_id'] ?>"
                                                                                        selected><?= $row['affiliate_username'] ?></option>
																			<?php endif; ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <hr/>
															<?php endif; ?>
                                                            <div class="form-group">
                                                                <label
                                                                        class="col-md-4 control-label"><?= lang('company') ?></label>

                                                                <div class="col-md-6">
																	<?= form_input('order_company', set_value('order_company', $row['order_company']), 'class="' . css_error('order_company') . 'se form-control"') ?>
                                                                </div>
                                                            </div>
                                                            <hr/>
                                                            <div class="form-group">
                                                                <label class="col-md-4 control-label">
                                                                    * <?= lang('email_address') ?></label>

                                                                <div class="col-md-6">
																	<?= form_input('order_primary_email', set_value('order_primary_email', $row['order_primary_email']), 'class="' . css_error('order_primary_email') . 'se form-control required email"') ?>
                                                                </div>
                                                            </div>
                                                            <hr/>
                                                            <div class="form-group">
                                                                <label class="col-md-4 control-label">
																	<?= lang('telephone') ?>
                                                                </label>

                                                                <div class="col-md-6">
																	<?= form_input('order_telephone', set_value('order_telephone', $row['order_telephone']), 'class="' . css_error('order_telephone') . 'se form-control"') ?>
                                                                </div>
                                                            </div>
                                                            <hr/>
                                                            <div class="form-group">
                                                                <label class="col-md-4 control-label">
																	<?= lang('address_1') ?>
                                                                </label>

                                                                <div class="col-md-6">
																	<?= form_input('order_address_1', set_value('order_address_1', $row['order_address_1']), 'class="' . css_error('order_address_1') . 'se form-control"') ?>
                                                                </div>
                                                            </div>
                                                            <hr/>
                                                            <div class="form-group">
                                                                <label class="col-md-4 control-label">
																	<?= lang('address_2') ?>
                                                                </label>

                                                                <div class="col-md-6">
																	<?= form_input('order_address_2', set_value('order_address_2', $row['order_address_2']), 'class="' . css_error('order_address_2') . 'se form-control"') ?>
                                                                </div>
                                                            </div>
                                                            <hr/>
                                                            <div class="form-group">
                                                                <label class="col-md-4 control-label">
																	<?= lang('city') ?>
                                                                </label>

                                                                <div class="col-md-6">
																	<?= form_input('order_city', set_value('order_city', $row['order_city']), 'class="' . css_error('order_city') . 'se form-control"') ?>
                                                                </div>
                                                            </div>
                                                            <hr/>
                                                            <div class="form-group">
                                                                <label class="col-md-4 control-label">
																	<?= lang('state_province') ?>
                                                                </label>

                                                                <div class="col-md-6">
																	<?= form_dropdown('order_state', load_regions($row['order_country']), $row['order_state'], 'id="order-state" class="form-control required"') ?>
                                                                </div>
                                                            </div>
                                                            <hr/>
                                                            <div class="form-group">
                                                                <label class="col-md-4 control-label">
																	<?= lang('address_2') ?>
                                                                </label>

                                                                <div class="col-md-6">
																	<?= form_dropdown('order_country', array($row['order_country'] => $row['order_country_name']), '', 'onchange="updateregion(\'order\')"id="order-country" class="country form-control required"') ?>
                                                                </div>
                                                            </div>
                                                            <hr/>
                                                            <div class="form-group">
                                                                <label class="col-md-4 control-label">
																	<?= lang('postal_code') ?>
                                                                </label>

                                                                <div class="col-md-6">
																	<?= form_input('order_postal_code', set_value('order_postal_code', $row['order_postal_code']), 'class="' . css_error('order_postal_code') . 'se form-control"') ?>
                                                                </div>
                                                            </div>
                                                            <hr/>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12 text-right">
                                                                <button id="submit-one-button"
                                                                        class="steps-two submit-button btn btn-info navbar-btn block-phone <?= is_disabled('update', TRUE) ?>"
                                                                        type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
												<?= form_close() ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="heading-two">
                                    <h5 class="panel-title text-capitalize">
                                        <span id="step-two-heading"><?= lang('update_items_in_order') ?></span>
                                    </h5>
                                </div>
                                <div id="step-two" class="panel-collapse collapse" role="tabpanel"
                                     aria-labelledby="heading-two">
                                    <div class="panel-body">
										<?= form_open(admin_url('orders/add_product/'), 'role="form" id="update-prod-form" class="form-horizontal"') ?>
										<?= form_hidden('order_id', $id) ?>
                                        <div>
                                            <h3 class="text-capitalize"><?= lang('add_products_to_order') ?></h3>
                                            <hr/>
                                            <div class="row hidden-xs">
                                                <div class="col-md-10"><?= tb_header('search_by_product_name') ?></div>
                                                <div class="col-md-2 text-center"><?= tb_header('quantity') ?></div>
                                            </div>
                                            <div id="products-div">
                                                <div class="row">
                                                    <div class="col-md-10">
                                                        <select id="product_id" class="form-control select2 required"
                                                                name="product_id">
                                                            <option value=""
                                                                    selected><?= lang('type_product_name') ?></option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <input type="number" name="quantity" value="1"
                                                               class="form-control"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr/>
                                            <div id="product-options"></div>
                                            <div class="text-right">
                                                <button id="add-button"
                                                        class="btn btn-primary navbar-btn block-phone <?= is_disabled('update', TRUE) ?>"
                                                        type="submit"><?= i('fa fa-plus') ?> <?= lang('add_to_order') ?></button>

                                                <button id="submit-step-two"
                                                        class="submit-button btn btn-info navbar-btn block-phone <?= is_disabled('update', TRUE) ?>"
                                                        type="button"><?= i('fa fa-arrow-circle-right') ?> <?= lang('continue_to_next_step') ?></button>
                                            </div>
                                        </div>
										<?= form_close() ?>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="heading-three">
                                    <h5 class="panel-title text-capitalize">
                            <span id="step-three-heading">
                                <?= lang('set_shipping_address') ?></span>
                                    </h5>
                                </div>
                                <div id="step-three" class="panel-collapse collapse" role="tabpanel"
                                     aria-labelledby="heading-three">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-12">
												<?= form_open(admin_url('orders/update_shipping_info'), 'role="form" id="step-three-form" class="form-horizontal"') ?>
												<?= form_hidden('order_id', $id) ?>
                                                <h3 class="text-capitalize"> <?= lang('shipping_information') ?></h3>
                                                <span
                                                        class="text-capitalize"><?= lang('update_shipping_address') ?></span>
                                                <hr/>
                                                <div class="form-group">
                                                    <label
                                                            class="col-md-4 control-label"><?= lang('charge_shipping') ?></label>

                                                    <div class="col-md-6">
                                                        <select id="charge_shipping" class="form-control"
                                                                name="charge_shipping">
                                                            <option value="0" <?php if (empty(trim($row['shipping_name']))): ?>selected<?php endif; ?>><?= lang('no') ?></option>
                                                            <option value="1" <?php if (!empty(trim($row['shipping_name']))): ?>selected<?php endif; ?>><?= lang('yes') ?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <hr/>
                                                <div id="shipping-fields"
                                                     class="collapse  <?php if (!empty($row['shipping_name'])): ?>in <?php endif; ?>">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label class="col-md-4 control-label">
																	<?= lang('shipping_name') ?></label>

                                                                <div class="col-md-6">
																	<?= form_input('shipping_name', set_value('shipping_name', $row['shipping_name']), 'class="' . css_error('shipping_name') . 'se shipping form-control required"') ?>
                                                                </div>
                                                            </div>
                                                            <hr/>
                                                            <div class="form-group">
                                                                <label
                                                                        class="col-md-4 control-label"><?= lang('company') ?></label>

                                                                <div class="col-md-6">
																	<?= form_input('shipping_company', set_value('shipping_company', $row['shipping_company']), 'class="' . css_error('shipping_company') . 'se form-control"') ?>
                                                                </div>
                                                            </div>
                                                            <hr/>
                                                            <div class="form-group">
                                                                <label class="col-md-4 control-label">
																	<?= lang('address_1') ?>
                                                                </label>

                                                                <div class="col-md-6">
																	<?= form_input('shipping_address_1', set_value('shipping_address_1', $row['shipping_address_1']), 'class="' . css_error('shipping_address_1') . 'se shipping form-control"') ?>
                                                                </div>
                                                            </div>
                                                            <hr/>
                                                            <div class="form-group">
                                                                <label class="col-md-4 control-label">
																	<?= lang('address_2') ?>
                                                                </label>

                                                                <div class="col-md-6">
																	<?= form_input('shipping_address_2', set_value('shipping_address_2', $row['shipping_address_2']), 'class="' . css_error('shipping_address_2') . 'se form-control"') ?>
                                                                </div>
                                                            </div>
                                                            <hr/>
                                                            <div class="form-group">
                                                                <label class="col-md-4 control-label">
																	<?= lang('city') ?>
                                                                </label>

                                                                <div class="col-md-6">
																	<?= form_input('shipping_city', set_value('shipping_city', $row['shipping_city']), 'class="' . css_error('shipping_city') . 'se shipping form-control"') ?>
                                                                </div>
                                                            </div>
                                                            <hr/>
                                                            <div class="form-group">
                                                                <label class="col-md-4 control-label">
																	<?= lang('state_province') ?>
                                                                </label>

                                                                <div class="col-md-6">
																	<?= form_dropdown('shipping_state', load_regions($row['shipping_country']), $row['shipping_state'], 'id="shipping-state" class="shipping form-control required"') ?>
                                                                </div>
                                                            </div>
                                                            <hr/>
                                                            <div class="form-group">
                                                                <label class="col-md-4 control-label">
																	<?= lang('country') ?>
                                                                </label>

                                                                <div class="col-md-6">
																	<?= form_dropdown('shipping_country', array($row['shipping_country'] => $row['shipping_country_name']), '', 'onchange="updateregion(\'shipping\')"id="shipping-country" class="country shipping form-control required"') ?>
                                                                </div>
                                                            </div>
                                                            <hr/>
                                                            <div class="form-group">
                                                                <label class="col-md-4 control-label">
																	<?= lang('postal_code') ?>
                                                                </label>

                                                                <div class="col-md-6">
																	<?= form_input('shipping_postal_code', set_value('shipping_postal_code', $row['shipping_postal_code']), 'class="' . css_error('shipping_postal_code') . 'se shipping form-control"') ?>
                                                                </div>
                                                            </div>
                                                            <hr/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12 text-right">
                                                        <button id="submit-three-button"
                                                                class="submit-button btn btn-info navbar-btn block-phone <?= is_disabled('update', TRUE) ?>"
                                                                type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
                                                    </div>
                                                </div>
												<?= form_close() ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="heading-four">
                                    <h5 class="panel-title text-capitalize">
                            <span id="step-four-heading">
                                <?= lang('update_shipping_options') ?>
                            </span>
                                    </h5>
                                </div>
                                <div id="step-four" class="panel-collapse collapse" role="tabpanel"
                                     aria-labelledby="heading-four">
                                    <div class="panel-body">
                                        <div id="shipping-options-box">
                                            <div class="alert alert-warning text-capitalize">
												<?= i('fa fa-spinner fa-spin') ?> <?= lang('loading_shipping_options') ?>
												<?= lang('please_wait') ?>...
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="order-details" class="col-lg-7 col-lg-offset-2 animated fadeIn">
                    <div class="box-info">
                        <div class="row">
                            <div class="col-xs-6">
                                <h3 class="text-capitalize">
									<?php if (!empty($row['invoice_payment_status'])): ?>
                                        <strong style="color: <?= $row['invoice_payment_color'] ?>"><?= $row['invoice_payment_status'] ?></strong>
									<?php endif; ?>
                                </h3>
                            </div>
                            <div class="col-xs-6 text-right">
                                <h3 class="text-capitalize">
                                    <strong id="order_status" style="color: <?= $row['color'] ?>">
										<?= lang($row['order_status']) ?>
                                    </strong>
                                </h3>
                            </div>
                        </div>
                        <hr/>
                        <div class="row">
                            <div class="col-sm-6">
                                <h5 class="text-capitalize"><strong><?= lang('date_ordered') ?></strong>
									<?= display_date($row['date_ordered']) ?></h5>
                            </div>
                            <div class="col-sm-6">
								<?php if (!empty($row['due_date'])): ?>
                                    <h5 class="text-capitalize"><strong><?= lang('due_date') ?></strong>
										<?= display_date($row['due_date']) ?></h5>
								<?php endif; ?>
                            </div>
                        </div>
                        <hr/>
                        <div id="address-box" class="row">
                            <div class="col-sm-6">
                                <address>
                                    <h5 class="text-capitalize"><strong><?= lang('bill_to') ?></strong></h5>
                                    <hr/>
									<?php if (!empty($row['invoice_number'])): ?>
                                        <div class="text-capitalize">
                                            <a href="<?= admin_url('invoices/update/' . $row['invoice_id']) ?>"
                                               class="text-center"><strong><?= lang('invoice') ?>
                                                    #<?= $row['invoice_number'] ?></strong></a>
                                        </div>
									<?php endif; ?>
                                    <div id="order_name">
										<?php if (!empty($row['member_id'])): ?>
											<?= anchor(admin_url(TBL_MEMBERS . '/update/' . $row['member_id']), $row['order_name']) ?>
										<?php else: ?>
											<?= $row['order_name'] ?>
										<?php endif; ?>
                                    </div>
                                    <div id="order_company"><?= $row['order_company'] ?></div>
                                    <div id="order_primary_email"><?= $row['order_primary_email'] ?></div>
                                    <div id="order_telephone"><?= $row['order_telephone'] ?></div>
                                    <div>
                                        <span id="order_address_1"><?= $row['order_address_1'] ?></span>
                                        <span id="order_address_2"><?= $row['order_address_2'] ?></span>
                                    </div>
                                    <div>
                                        <span id="order_city"><?= $row['order_city'] ?></span>
                                        <span id="region_code"><?= $row['order_state_code'] ?></span>
                                        <span id="order_postal_code"><?= $row['order_postal_code'] ?></span>
                                    </div>
                                    <div id="country_name"><?= $row['order_country_name'] ?></div>
                                </address>
                            </div>
                            <div class="col-sm-6">

                                <div id="shipping_data"
                                     class="<?php if (empty($row['shipping_address_1'])): ?> hide<?php endif; ?>">
                                    <address>
                                        <h5 class="text-capitalize"><strong><?= lang('ship_to') ?></strong></h5>
                                        <hr/>
                                        <div>
                                            <strong><span id="shipping_carrier"><?= $row['carrier'] ?></span>
                                                <span id="shipping_service"><?= $row['service'] ?></span></strong>
                                        </div>

                                        <div class="text-capitalize">
                                            <strong id="tracking_id">
												<?php if (!empty($row['tracking_id'])): ?>
													<?= lang('tracking_id') ?>
                                                    : <?= $row['tracking_id'] ?>
												<?php endif; ?>
                                            </strong>
                                        </div>

                                        <div id="shipping_name"><?= $row['shipping_name'] ?></div>
                                        <div id="shipping_company"><?= $row['shipping_company'] ?></div>
                                        <div>
                                            <span id="shipping_address_1"><?= $row['shipping_address_1'] ?></span>
                                            <span id="shipping_address_2"><?= $row['shipping_address_2'] ?></span>
                                        </div>
                                        <div>
                                            <span id="shipping_city"><?= $row['shipping_city'] ?></span>
                                            <span id="shipping_state_code"><?= $row['shipping_state_code'] ?></span>
                                            <span id="shipping_postal_code"><?= $row['shipping_postal_code'] ?></span>
                                        </div>
                                        <div id="shipping_country_name"><?= $row['shipping_country_name'] ?></div>
                                    </address>
                                </div>

                            </div>
                        </div>
						<?= form_open(admin_url('orders/update_order_items/' . $id), 'role="form" id="update-cart-form" class="form-horizontal"') ?>
						<?= form_hidden('order_id', $id) ?>
                        <div id="products-box">
                            <div class="row">
                                <div class="col-md-12">
                                    <hr/>
									<?php if (empty($row['items'])): ?>
                                        <div class="alert alert-warning">
											<?= i('fa fa-exclamation-circle') ?> <?= lang('no_products_added_to_order') ?>
                                        </div>
									<?php else: ?>
                                        <div class="row text-capitalize">
                                            <div class="col-sm-2 text-center hidden-xs"><h5><?= lang('sku') ?></h5>
                                            </div>
                                            <div class="col-sm-5 col-xs-7"><h5><?= lang('product_name') ?></h5></div>
                                            <div class="col-sm-3 text-center"></div>
                                            <div class="col-xs-2 text-center"><h5><?= lang('qty') ?></h5></div>
                                        </div>
                                        <hr/>
										<?php $c = 1 ?>
										<?php foreach ($row['items'] as $p): ?>
											<?php $c++ ?>
                                            <div class="row">
                                                <div class="col-sm-2 text-center hidden-xs">
													<?= $p['product_sku'] ?>
                                                </div>
                                                <div class="col-sm-5 col-xs-7">
                                                    <strong><?= $p['order_item_name'] ?></strong>
													<?php if (!empty($p['attribute_data'])): ?>
                                                        <ul class="list-unstyled">
															<?php foreach ($p['attribute_data'] as $k => $v): ?>
																<?php $c++ ?>
                                                                <li>
																	<?= order_attributes($c, $v) ?>
                                                                </li>
                                                                <div class="collapse" id="option-info-<?= $c ?>">
                                                                    <div class="alert alert-info">
                                                                        <strong class="text-info">
																			<?= lang('option_info') ?>:</strong><br/>
																		<?php foreach ($v as $a => $b): ?>
																			<?php if (in_array($a, $default_order_attribute_info)): ?>
                                                                                <small><?= $a ?> - <?= $b ?></small>
                                                                                <br/>
																			<?php endif; ?>
																		<?php endforeach; ?>
                                                                    </div>
                                                                </div>
															<?php endforeach; ?>
                                                        </ul>
													<?php endif; ?>
                                                </div>
                                                <div class="col-xs-3">
													<?php if (!empty($p['specification_data'])): ?>
                                                    <strong><?= lang('specs') ?></strong>
														<?php foreach ($p['specification_data'] as $v): ?>
                                                            <div><?= order_specs($v) ?></div>
														<?php endforeach; ?>
													<?php endif; ?>
                                                </div>
                                                <div class="col-xs-2 text-center">
                                                    <h5 class="view-field"><?= $p['quantity'] ?></h5>
                                                    <input type="number" name="item[<?= $p['order_item_id'] ?>]"
                                                           value="<?= $p['quantity'] ?>"
                                                           class="update-field form-control required number hide"/>
                                                </div>
                                            </div>
                                            <hr/>
										<?php endforeach; ?>
									<?php endif; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="update-field col-sm-12 text-right hide">
                                    <button id="update-order-button" class="btn btn-primary"
                                            type="submit"><?= i('fa fa-refresh') ?> <?= lang('update_contents') ?> </button>
                                </div>
                            </div>
                        </div>
						<?= form_close() ?>
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="text-capitalize"><strong><?= lang('order_notes') ?></strong></h5>
                                <hr/>
                                <div id="order_notes">
									<?= $row['order_notes'] ?>
                                </div>
                            </div>
                        </div>
                        <hr />
                        <div class="row">
                            <div class="col-md-12 text-center">
                            <a href="<?=EXTERNAL_IP_LOOKUP?><?=$row['ip_address']?>" target="_blank"><?=lang('ip_address')?> <?=$row['ip_address']?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<?php if (!is_disabled('update', TRUE)): ?>
            <div class="tab-pane fade in" id="notes">
				<?= form_open(admin_url('orders/update_notes/' . $id), 'role="form" id="notes-form" class="form-horizontal"') ?>
				<?= form_hidden('order_id', $id) ?>
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="text-capitalize"><?= lang('order_notes') ?></h3>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-6 col-md-offset-3">
						<?= form_textarea('order_notes', set_value('order_notes', $row['order_notes']), 'class="' . css_error('order_notes') . 'se form-control"') ?>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-6 col-md-offset-3 text-right">
                        <button id="update-notes-button" class="btn btn-primary"
                                type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?> </button>
                    </div>
                </div>
				<?= form_close() ?>
            </div>
            <div class="tab-pane fade in" id="tracking">
				<?= form_open(admin_url('orders/update_tracking/' . $id), 'role="form" id="tracking-form" class="form-horizontal"') ?>
				<?= form_hidden('order_id', $id) ?>
                <div class="row">
                    <div class="col-md-6">
                        <h3 class="text-capitalize"><?= lang('shipping_and_tracking_information') ?></h3>
                        <hr class="visible-xs"/>
                    </div>
                    <div class="col-md-6 text-right">
						<?php if (!empty($row['label_url'])): ?>
                            <a href="<?= admin_url('orders/generate_postage/' . $id . '/print') ?>" target="_blank"
                               class="btn btn-default">
								<?= i('fa fa-print') ?> <?= lang('print_postage_label') ?>
                            </a>
						<?php endif; ?>
						<?php if ($this->ship->module_enabled('easypost')): ?>
							<?php if (!empty($row['api_id'])): ?>
                                <a href="<?= admin_url('orders/generate_postage/' . $id) ?>"
                                   class="btn btn-info block-phone">
									<?= i('fa fa-truck') ?> <?= lang('generate_new_postage_label') ?>
                                </a>
							<?php endif; ?>
						<?php endif; ?>
                    </div>
                </div>
                <hr/>
                <div class="form-group">
                    <label class="col-md-3 control-label">
						<?= lang('carrier') ?>
                    </label>

                    <div class="col-md-6">
						<?= form_input('carrier', set_value('carrier', $row['carrier']), 'id="update_shipping_carrier" class="' . css_error('carrier') . 'se form-control"') ?>
                    </div>
                </div>
                <hr/>
                <div class="form-group">
                    <label class="col-md-3 control-label">
						<?= lang('service') ?>
                    </label>

                    <div class="col-md-6">
						<?= form_input('service', set_value('service', $row['service']), 'id="update_shipping_service" class="' . css_error('service') . 'se form-control"') ?>
                    </div>
                </div>
                <hr/>
                <div class="form-group">
                    <label class="col-md-3 control-label">
						<?= lang('shipping_rate') ?>
                    </label>

                    <div class="col-md-6">
						<?= form_input('rate', set_value('rate', input_amount($row['rate'])), 'id="update_shipping_rate" class="' . css_error('rate') . 'se form-control"') ?>
                    </div>
                </div>
                <hr/>
                <div class="form-group">
                    <label class="col-md-3 control-label">
						<?= lang('tracking_id') ?>
                    </label>
                    <div class="col-md-6">
						<?= form_input('tracking_id', set_value('tracking_id', $row['tracking_id']), 'class="' . css_error('tracking_id') . 'se form-control"') ?>
                    </div>
                </div>
                <hr/>
				<?php if (!empty($row['tracking_url'])): ?>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
							<?= lang('tracking_url') ?>
                        </label>
                        <div class="col-md-6">
                            <div class="input-group">
								<?= form_input('tracking_url', set_value('tracking_url', $row['tracking_url']), 'class="' . css_error('tracking_url') . 'se form-control" readonly') ?>
                                <span class="input-group-addon">
                                    <a href="<?= $row['tracking_url'] ?>" target="_blank"><?= i('fa fa-external-link') ?> <?= lang('launch') ?></a></span>
                            </div>
                        </div>
                    </div>
                    <hr/>
				<?php endif; ?>
				<?php if (!empty($row['label_url'])): ?>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
							<?= lang('shipping_label') ?>
                        </label>
                        <div class="col-md-6">
							<?= form_input('label_url', set_value('label_url', $row['label_url']), ' class="' . css_error('label_url') . 'se form-control" readonly') ?>
                        </div>
                    </div>
                    <hr/>
				<?php endif; ?>
                <div class="form-group">
                    <label class="col-md-3 control-label">
						<?= lang('shipping_notes') ?>
                    </label>

                    <div class="col-md-6">
						<?= form_textarea('shipping_notes', set_value('shipping_notes', $row['shipping_notes']), 'class="' . css_error('shipping_notes') . 'se form-control"') ?>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-6 col-md-offset-3 text-right">
                        <button id="update-tracking-button" class="btn btn-primary"
                                type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?> </button>
                    </div>
                </div>
				<?= form_close() ?>
            </div>
		<?php endif; ?>
    </div>
</div>
<script>
    $('#edit-order-button').click(function () {
        $('#order-details').removeClass('col-lg-offset-2');
        $('.update-field').removeClass('hide');
        $('.view-field').addClass('hide');
    });

    $('#view-order-button').click(function () {
        $('#order-details').addClass('col-lg-offset-2');
        $('.update-field').addClass('hide');
        $('.view-field').removeClass('hide');
    });

    //for entering a referring sponsor via username
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
    select_country('#order-country');

    <?php if (!empty(trim($row['shipping_name']))): ?>
    $('.shipping.form-control').addClass('required');
    <?php else: ?>
    $('.shipping.form-control').removeClass('required');
    <?php endif; ?>

    $('#charge_shipping').on('change', function () {
        if (this.value == 1) {
            $('#shipping-fields').collapse('show');
            $('.shipping.form-control').prop('disabled', false);
            select_country('#shipping-country');
            $('.shipping.form-control').addClass('required');
        }
        else {
            $('#shipping-fields').collapse('hide');
            $('.shipping.form-control').prop('disabled', true);
            $('.shipping.form-control').removeClass('required');
        }
    });

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
        $.get('<?=site_url('search/load_regions/state')?>', {country_id: $('#' + type + '-country').val()},
            function (data) {
                $('#' + type + '-state').html(data);
                $(".s2").select2();
            }
        );
    }

    $("#update-profile-form").validate({
        ignore: "",
        errorContainer: $("#error-alert"),
        submitHandler: function (form) {
            $.ajax({
                url: '<?=admin_url('orders/update_order_profile')?>',
                type: 'POST',
                dataType: 'json',
                data: $('#update-profile-form').serialize(),
                beforeSend: function () {
                    $('.submit-button').button('loading');
                },
                complete: function () {
                    $('.submit-button').button('reset');
                },
                success: function (response) {
                    if (response.type == 'success') {
                        $('.alert-danger').remove();
                        $('.form-control').removeClass('error');

                        //update order details
                        if (response['data']) {
                            $.each(response['data'], function (key, val) {
                                $('#' + key).html(val);

                                if (key == 'color') {
                                    $('#order_status').css('color', val);
                                }
                            });
                        }

                        //activate tabs
                        $("#step-one-heading").html('<a data-toggle="collapse" data-parent="#accordion" href="#step-one" aria-expanded="true" aria-controls="step-one" class="panel-title"><?= lang('update_order_data') ?> <?=i('fa fa-caret-down')?></a>');
                        $("#step-two-heading").html('<a data-toggle="collapse" data-parent="#accordion" href="#step-two" aria-expanded="true" aria-controls="step-two" class="steps-two panel-title" id="step-two-heading"><?= lang('update_items_in_order') ?> <?=i('fa fa-caret-down')?></a>');
                        $('a[href=\'#step-two\']').trigger('click');

                    }
                    else {
                        $('#response').html('<?=alert('error')?>');
                        if (response['error_fields']) {
                            $.each(response['error_fields'], function (key, val) {
                                $('#' + key).addClass('error');
                                $('#' + key).focus();
                            });
                        }

                        $('#msg-details').html(response.msg);
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
					<?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        }
    });

    //load existing client data
    $('#member_id').on('change', function () {
        $.get('<?=admin_url(TBL_MEMBERS . '/ajax_user/create_order')?>', {member_id: $('#member_id').val()},
            function (data) {
                $('#profile').html(data);
                $(".s2").select2();
            }
        );
    });

    $("#update-prod-form").validate({
        ignore: "",
        errorContainer: $("#error-alert"),
        submitHandler: function (form) {
            $.ajax({
                url: '<?=admin_url('orders/add_product')?>',
                type: 'POST',
                dataType: 'json',
                data: $('#update-prod-form').serialize(),
                beforeSend: function () {
                    $('#add-button').button('loading');
                },
                complete: function () {
                    $('#add-button').button('reset');
                },
                success: function (response) {
                    if (response.type == 'success') {
                        $('.alert-danger').remove();
                        $('.form-control').removeClass('error');

                        //update the order contents
                        $('#products-box').load('<?=admin_url('orders/update_order_contents/' . $id)?>');
                    }
                    else {
                        $('#response').html('<?=alert('error')?>');
                        if (response['error_fields']) {
                            $.each(response['error_fields'], function (key, val) {
                                $('#' + key).addClass('error');
                                $('#' + key).focus();
                            });
                        }

                        $('#msg-details').html(response.msg);
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        }
    });

    $("#update-cart-form").validate({
        ignore: "",
        errorContainer: $("#error-alert"),
        submitHandler: function (form) {
            $.ajax({
                url: '<?=admin_url('orders/update_order_items')?>',
                type: 'POST',
                dataType: 'json',
                data: $('#update-cart-form').serialize(),
                beforeSend: function () {
                    $('#update-order-button').button('loading');
                },
                complete: function () {
                    $('#update-order-button').button('reset');
                },
                success: function (response) {
                    if (response.type == 'success') {
                        $('.alert-danger').remove();
                        $('.form-control').removeClass('error');
                        $('#response').html('<?=alert('success')?>');

                        setTimeout(function () {
                            $('.alert-msg').fadeOut('slow');
                        }, 5000);

                        //update the order contents
                        $('#products-box').load('<?=admin_url('orders/update_order_contents/' . $id)?>');
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

    $('#product_id').on('change', function () {
        $.ajax({
            url: '<?=admin_url(TBL_PRODUCTS_ATTRIBUTES . '/get_product_attributes/json/')?>',
            type: 'POST',
            dataType: 'json',
            data: $('#update-prod-form').serialize(),
            beforeSend: function () {
                $('.submit-button').button('loading');
            },
            complete: function () {
                $('.submit-button').button('reset');
            },
            success: function (response) {
                if (response.error) {
                    $('#msg-details').html(response.msg);
                }
                else {
                    if (response.attributes) {
                        $('#product-options').html(response.attributes);
                        $('#product-options').show(300);
                    }
                    else {
                        $('#product-options').hide(300);
                    }
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
				<?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });

    //search for products
    $("#product_id").select2({
        ajax: {
            url: '<?=admin_url(TBL_PRODUCTS . '/search/orders')?>',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    product_name: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data, page) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.product_id,
                            text: item.product_name
                        }
                    })
                };
            }
        },
        minimumInputLength: 2
    });

    $('#submit-step-two').on('click', function () {
        $.ajax({
            url: '<?=admin_url('orders/check_order_contents/' . $id)?>',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.error) {
                    $('#response').html('<?=alert('error')?>');
                    $('#msg-details').html(response.msg);
                }
                else {

                    //activate tabs
                    $("#step-three-heading").html('<a data-toggle="collapse" data-parent="#accordion" href="#step-three" aria-expanded="true" aria-controls="step-three" class="panel-title"><?= lang('set_shipping_address') ?> <?=i('fa fa-caret-down')?></a>');
                    //set shipping data
                    $('a[href=\'#step-three\']').trigger('click');
                    $('.alert-danger').remove();
                    $('#discount-box').load('<?=admin_url('orders/set_discounts')?>');
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
				<?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });

    $('#step-two-heading').on('click', function () {
        $('#update-cart-form :input').attr('readonly', false);
        $('#update-cart-button').removeClass('hide');

        //activate tabs
        $("#step-four-heading").html('<?= lang('update_shipping_options') ?>');
    });

    $("#step-three-form").validate({
        ignore: "",
        errorContainer: $("#error-alert"),
        submitHandler: function (form) {
            $.ajax({
                url: '<?=admin_url('orders/update_shipping_info')?>',
                type: 'POST',
                dataType: 'json',
                data: $('#step-three-form').serialize(),
                beforeSend: function () {
                    $('.submit-button').button('loading');
                },
                complete: function () {
                    $('.submit-button').button('reset');
                },
                success: function (response) {
                    if (response.type == 'success') {
                        $('.alert-danger').remove();
                        $('.form-control').removeClass('error');

                        if (response.charge_shipping) {
                            $('#shipping_data').removeClass('hide');

                            //update order details
                            if (response['data']) {
                                $.each(response['data'], function (key, val) {

                                    if (key == 'region_code') {
                                        $('#shipping_state_code').html(val);
                                    }
                                    else if (key == 'country_name') {
                                        $('#shipping_' + key).html(val);
                                    }
                                    else {
                                        $('#' + key).html(val);
                                    }
                                });
                            }

                            $('#shipping-options-box').html('<div class="alert alert-warning text-capitalize"><i class="fa fa-spinner fa-spin"></i> <?=lang('loading_shipping_options')?> <?=lang('please_wait')?>...</div>');
                            $('#shipping-options-box').load('<?=admin_url('orders/shipping_options/update/' . $id)?>');

                            //activate tabs
                            $("#step-four-heading").html('<a data-toggle="collapse" data-parent="#accordion" href="#step-four" aria-expanded="true" aria-controls="step-four" class="steps-two panel-title" id="step-four-heading"><?= lang('update_shipping_options') ?> <?=i('fa fa-caret-down')?></a>');
                            $('a[href=\'#step-four\']').trigger('click');
                        }
                        else {
                            $('#shipping_data').addClass('hide');
                            $('#response').html('<?=alert('success')?>');
                            $('#msg-details').html(response.msg);
                            $('#view-order-button').trigger('click');
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

                        $('#msg-details').html(response.msg);
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
					<?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        }
    });

    $("#notes-form").validate({
        ignore: "",
        errorContainer: $("#error-alert"),
        submitHandler: function (form) {
            $.ajax({
                url: '<?=admin_url('orders/update_notes')?>',
                type: 'POST',
                dataType: 'json',
                data: $('#notes-form').serialize(),
                beforeSend: function () {
                    $('#update-notes-button').button('loading');
                },
                complete: function () {
                    $('#update-notes-button').button('reset');
                },
                success: function (response) {
                    if (response.type == 'success') {
                        $('.alert-danger').remove();
                        $('.form-control').removeClass('error');
                        $('#response').html('<?=alert('success')?>');

                        if (response['data']) {
                            $.each(response['data'], function (key, value) {
                                $('#' + key).html(value);
                            });

                            setTimeout(function () {
                                $('.alert-msg').fadeOut('slow');
                            }, 5000);
                        }

                        setTimeout(function () {
                            $('.alert-msg').fadeOut('slow');
                        }, 5000);

                    }
                    else {
                        $('#response').html('<?=alert('error')?>');
                    }

                    $('#msg-details').html(response.msg);
                },
                error: function (xhr, ajaxOptions, thrownError) {
					<?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        }
    });

    function updateProductAttribute() {
    };

    $("#tracking-form").validate({
        ignore: "",
        errorContainer: $("#error-alert"),
        submitHandler: function (form) {
            $.ajax({
                url: '<?=admin_url('orders/update_tracking')?>',
                type: 'POST',
                dataType: 'json',
                data: $('#tracking-form').serialize(),
                beforeSend: function () {
                    $('#update-tracking-button').button('loading');
                },
                complete: function () {
                    $('#update-tracking-button').button('reset');
                },
                success: function (response) {
                    if (response.type == 'success') {
                        $('.alert-danger').remove();
                        $('.form-control').removeClass('error');
                        $('#response').html('<?=alert('success')?>');

                        if (response['data']) {
                            $.each(response['data'], function (key, value) {

                                if (key == 'tracking_id') {
                                    if (value) {
                                        $('#' + key).html('<?=lang('tracking_id')?>: ' + value);
                                    }
                                    else {
                                        $('#' + key).html('');
                                    }
                                }
                                else {
                                    $('#' + key).html(value);
                                }
                            });

                            setTimeout(function () {
                                $('.alert-msg').fadeOut('slow');
                            }, 5000);
                        }

                        setTimeout(function () {
                            $('.alert-msg').fadeOut('slow');
                        }, 5000);

                    }
                    else {
                        $('#response').html('<?=alert('error')?>');
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

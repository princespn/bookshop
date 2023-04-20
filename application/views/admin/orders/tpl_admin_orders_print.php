<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<body style="background: #f4f4f4">
<div class="container">
    <div class="row">
        <div class="col-md-6 col-md-offset-3" style="background: #fff; border: 20px solid white">
            <table cellpadding="5" cellspacing="5" width="100%" align="center">
                <tr>
                    <td valign="top" style="vertical-align: top">
						<?php if (config_enabled('sts_invoice_enable_logo') && !empty($layout_design_site_logo)): ?>
                            <img src="<?= $layout_design_site_logo ?>" style="max-height: 80px;"/>
						<?php else: ?>
                            <h4 style="margin-top: 0; margin-bottom: 5px"><?= $sts_site_name ?></h4>
						<?php endif; ?>
                        <div>
                            <small class="text-muted"><?= $sts_site_shipping_address_1 ?>
								<?= $sts_site_shipping_city ?> <?= $sts_site_shipping_region_code ?> <?= $sts_site_shipping_postal_code ?>
								<?= $sts_site_shipping_country_name ?><br />
								<?= $sts_site_phone_number ?>
                            </small>
                        </div>
                        <div class="hidden-print">
                            <button class="btn btn-info btn-sm" onclick="window.print()"><?=i('fa fa-print')?> <?=lang('print')?></button>
                        </div>
                    </td>
                    <td class="text-right" style="vertical-align: top" align="right" valign="top">
                        <h4 style="margin-top: 0; margin-bottom: 5px" class="text-capitalize">
	                        <?= lang('order') ?> #<?= $row['order_number'] ?>
                        </h4>
                        <small class="text-muted text-capitalize">
	                        <?php if (!empty($row['invoice_number'])): ?>
		                        <?= lang('invoice') ?> #<?= $row['invoice_number'] ?><br />
	                        <?php endif; ?>
							<?= lang('due_date') ?> - <?= display_date($row['due_date']) ?>
							<?php if (!empty($row['date_purchased'])): ?><br/>
								<?= lang('date_purchased') ?> - <?= display_date($row['date_purchased']) ?>
							<?php endif; ?>
                        </small>
                        <br/><strong class="text-capitalize" id="order_status"
                                     style="color: <?= $row['color'] ?>"><?= $row['order_status'] ?></strong>
                        </strong>
                    </td>
                </tr>
            </table>
            <br />
            <table cellpadding="5" cellspacing="5" width="100%" align="center">
                <tr>
                    <td style="width: 50%;vertical-align: top">
                        <address>
                            <h4 class="text-capitalize"><strong><?= lang('bill_to') ?></strong></h4>
                            <div id="order_name"><?= $row['order_name'] ?></div>
                            <div id="order_company"><?= $row['order_company'] ?></div>
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
                            <div id="order_primary_email"><?= $row['order_primary_email'] ?></div>
                        </address>
                    </td>
                    <td style="width: 50%; vertical-align: top">
                        <div id="shipping_data">
                            <address>
                                <h4 class="text-capitalize"><strong><?= lang('ship_to') ?></strong></h4>
                                <div class="<?php if (empty($row['carrier'])): ?> hide<?php endif; ?>">
                                    <strong><span id="shipping_carrier"><?= strtoupper(lang($row['carrier'])) ?></span>
                                        <span id="shipping_service"> - <?= $row['service'] ?></span></strong>
                                </div>
	                            <?php if (!empty($row['tracking_id'])): ?>
                                <div class="text-capitalize">
                                    <strong id="tracking_id">
						                    <?= lang('tracking_id') ?> -
                                            <?= $row['tracking_id'] ?>
                                    </strong>
                                </div>
	                            <?php endif; ?>
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
                    </td>
                </tr>
            </table>
            <table cellpadding="5" cellspacing="5" width="100%" align="center">
                <tr>
                    <td>
						<?php if (empty($row['items'])): ?>
                            <div class="alert alert-warning">
								<?= i('fa fa-exclamation-circle') ?> <?= lang('no_products_added_to_order') ?>
                            </div>
						<?php else: ?>
                            <h4 class="text-capitalize"><strong><?= lang('items') ?></strong></h4>
                            <table class="table table-condensed table-striped" width="100%" border="0" cellspacing="5"
                                   cellpadding="5">
                                <tr>
                                    <th scope="col">
                                        <h5 align="center"><strong><?= lang('sku') ?></strong></h5>
                                    </th>
                                    <th scope="col">
                                        <h5><strong><?= lang('product_name') ?></strong></h5>
                                    </th>
                                    <th scope="col">

                                    </th>
                                    <th scope="col">
                                        <h5 align="center"><strong><?= lang('qty') ?></strong></h5>
                                    </th>
                                </tr>
								<?php foreach ($row['items'] as $p): ?>
                                    <tr style="vertical-align: top">
                                        <td align="center" style="width: 15%"> <?= $p['product_sku'] ?></td>
                                        <td>
                                            <h5><?= $p['order_item_name'] ?></h5>
	                                        <?php if (!empty($p['attribute_data'])): ?>
		                                        <?php foreach ($p['attribute_data'] as $k => $v): ?>
			                                        <?= order_attributes($k, $v, FALSE) ?>
		                                        <?php endforeach; ?>
	                                        <?php endif; ?>
                                        </td>
                                        <td>
	                                        <?php if (!empty($p['specification_data'])): ?>
                                                <strong><?= lang('specs') ?></strong>
		                                        <?php foreach ($p['specification_data'] as $v): ?>
                                                    <div><small><?= order_specs($v) ?></small></div>
		                                        <?php endforeach; ?>
	                                        <?php endif; ?>
                                        </td>
                                        <td align="center" style="width: 15%">
                                            <h5><?= $p['quantity'] ?></h5>
                                        </td>
                                    </tr>
								<?php endforeach; ?>
                            </table>

						<?php endif; ?>
                    </td>
                </tr>
            </table>
            <table cellpadding="5" cellspacing="5" width="100%">
                <tr>
                    <td valign="top">
                        <h4 class="text-capitalize"><strong><?= lang('order_notes') ?></strong></h4>
                        <div id="order_notes">
	                        <?= $row['order_notes'] ?>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
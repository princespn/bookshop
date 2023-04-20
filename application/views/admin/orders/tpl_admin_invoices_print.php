<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<body style="background: #f4f4f4;">
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
                            <button class="btn btn-info btn-sm"
                                    onclick="window.print()"><?= i('fa fa-print') ?> <?= lang('print') ?></button>
                        </div>
                    </td>
                    <td class="text-right" style="vertical-align: top" align="right" valign="top">
                        <h4 style="margin-top: 0; margin-bottom: 5px" class="text-capitalize">
							<?= lang('invoice') ?> #<?= $row['invoice_number'] ?>
                        </h4>
                        <small class="text-muted text-capitalize">
							<?= lang('due_date') ?> - <?= display_date($row['due_date']) ?>
							<?php if (!empty($row['date_purchased'])): ?><br/>
								<?= lang('date_purchased') ?> - <?= display_date($row['date_purchased']) ?>
							<?php endif; ?>
                        </small>
                        <br/><strong class="text-capitalize" id="payment_status" style="color: <?= $row['color'] ?>">
							<?= $row['payment_status'] ?>
                        </strong>
                    </td>
                </tr>
            </table>
            <br/>
            <table cellpadding="5" cellspacing="5" width="100%" align="center">
                <tr>
                    <td style="width: 50%; vertical-align: top">
                        <div class="billing_data">
                            <address>
                                <h4 class="text-capitalize"><strong><?= lang('bill_to') ?></strong></h4>
                                <div id="customer_name"><?= $row['customer_name'] ?></div>
                                <div id="customer_company"><?= $row['customer_company'] ?></div>
                                <div id="customer_telephone"><?= $row['customer_telephone'] ?></div>
                                <div>
                                    <span id="customer_address_1"><?= $row['customer_address_1'] ?></span>
                                    <span id="customer_address_2"><?= $row['customer_address_2'] ?></span>
                                </div>
                                <div>
                                    <span id="customer_city"><?= $row['customer_city'] ?></span>
                                    <span id="region_code"><?= $row['customer_state_code'] ?></span>
                                    <span id="customer_postal_code"><?= $row['customer_postal_code'] ?></span>
                                </div>
                                <div id="country_name"><?= $row['customer_country_name'] ?></div>
                                <div id="customer_primary_email"><?= $row['customer_primary_email'] ?></div>
                            </address>
                        </div>
                    </td>
                    <td style="width: 50%; vertical-align: top">
                        <div id="shipping_data"
                             class="<?php if (empty($row['shipping_address_1'])): ?> hide<?php endif; ?>">
                            <address>
                                <h4 class="text-capitalize"><strong><?= lang('ship_to') ?></strong></h4>
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
								<?= i('fa fa-exclamation-circle') ?> <?= lang('no_products_added_to_invoice') ?>
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
                                        <h5 align="center"><strong><?= lang('unit') ?></strong></h5>
                                    </th>
                                    <th scope="col">
                                        <h5 align="center"><strong><?= lang('qty') ?></strong></h5>
                                    </th>
                                    <th scope="col">
                                        <h5 align="center"><strong><?= lang('price') ?></strong></h5>
                                    </th>
                                </tr>
								<?php foreach ($row['items'] as $v): ?>
                                    <tr style="vertical-align: top">
                                        <td align="center" style="width: 15%"> <?= $v['product_sku'] ?></td>
                                        <td>
                                            <h5><?= $v['invoice_item_name'] ?></h5>
                                            <?php if (!empty($v['item_notes'])): ?>
                                            <p style="overflow: auto;"><small><?= nl2br($v['item_notes']) ?></small></p>
                                            <?php endif; ?>
                                        </td>
                                        <td align="center" style="width: 15%">
                                            <h5><?= format_amount($v['unit_price']) ?></h5>
                                        </td>
                                        <td align="center" style="width: 10%">
                                            <h5 class="view-field"><?= $v['quantity'] ?></h5>
                                        </td>
                                        <td align="center" style="width: 15%">
                                            <h5><?= format_amount($v['unit_price'] * $v['quantity']) ?></h5>
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
                    <td style="width: 70%" valign="top">
                        <h4 class="text-capitalize"><strong><?= lang('invoice_notes') ?></strong></h4>
                        <div id="invoice_notes" style="padding-right: 2em">
							<?= $row['invoice_notes'] ?>
                        </div>
                    </td>
                    <td valign="top">
                        <h4 class="text-capitalize"><strong><?= lang('totals') ?></strong></h4>
                        <table cellpadding="5" cellspacing="5" width="100%">
							<?php foreach ($row['totals'] as $k => $v): ?>
								<?php if ($v['type'] == 'sub_total'): ?>
                                    <tr>
                                        <td>
                                            <h4><?= lang('sub_total') ?></h4>
                                        </td>
                                        <td>
                                            <h4><?= format_amount($v['amount']) ?></span></h4>
                                        </td>
                                    </tr>
								<?php endif; ?>
							<?php endforeach; ?>
							<?php foreach ($row['totals'] as $k => $v): ?>
								<?php if ($v['type'] == 'shipping'): ?>
                                    <tr>
                                        <td>
                                            <h4><?= lang('shipping') ?></h4></td>
                                        <td>
                                            <h4><?= format_amount($v['amount']) ?></h4>
                                        </td>
                                    </tr>

								<?php elseif ($v['type'] == 'tax'): ?>
                                    <tr>
                                        <td>
                                            <h4><?= lang('taxes') ?></h4></td>
                                        <td>
                                            <h4> <?= format_amount($v['amount']) ?></h4>
                                        </td>
                                    </tr>

								<?php elseif ($v['type'] == 'total'): ?>
                                    <tr>
                                        <td>
                                            <h4><?= lang('total') ?></h4></td>
                                        <td>
                                            <h4><?= format_amount($v['amount']) ?></h4>
                                        </td>
                                    </tr>
								<?php endif; ?>
							<?php endforeach; ?>
                        </table>
                    </td>

                </tr>
            </table>
			<?php if (!empty($row['payments'])): ?>
                <h5 class="text-capitalize"><strong><?= lang('payment') ?></strong></h5>
                <table class="table table-condensed" cellpadding="5" cellspacing="5" width="100%">
                    <tr>
                        <th class="text-center"><h5><?= lang('date') ?></h5></th>
                        <th><h5><?= lang('transaction_id') ?></h5></th>
                        <th><h5><?= lang('description') ?></h5></th>
                        <th class="text-center"><h5><?= lang('amount') ?></h5></th>
                    </tr>
					<?php foreach ($row['payments'] as $v): ?>
                        <tr>
                            <td class="text-center"><?= display_date($v['date']) ?></td>
                            <td><?= $v['transaction_id'] ?></td>
                            <td>
                                <strong class="text-capitalize"><?= $v['method'] ?></strong>
                                - <?= $v['description'] ?>
                            </td>
                            <td class="col-sm-2 text-center"><?= format_amount($v['amount']) ?></td>
                        </tr>
					<?php endforeach; ?>
                    <tr>
                        <td>

                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
			<?php endif; ?>
        </div>
    </div>
</div>
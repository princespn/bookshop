<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="container invoice">
	<div class="box-info">
		<table class="table heading">
			<thead class="text-capitalize">
			<tr>
				<th>
					<p class="lead"><?= lang('payee_information') ?></p>
				</th>
				<th class="text-right" style="width:150px">
					<?php if (config_option('layout_design_site_logo')): ?>
						<img src="<?= config_option('layout_design_site_logo') ?>" style="max-height: 60px;"
						     class="img-responsive"/>
					<?php else: ?>
						<address>
							<h5><?= $sts_site_shipping_name ?></h5>
							<small class="text-muted"><?= $sts_site_shipping_address_1 ?><br/>
								<?= $sts_site_shipping_city ?> <?= $sts_site_shipping_region_name ?> <?= $sts_site_shipping_postal_code ?>
							</small>
						</address>
					<?php endif; ?>

				</th>
			</tr>
			<tr>
				<td colspan="2">
					<address>
						<strong><?= $member_name ?></strong><br/>
						<?= $address_1 ?> <?= $address_2 ?><br/>
						<?= $city ?> <?= $region_code ?> <?= $postal_code ?><br/>
						<?= $country_name ?><br/>
						<?= $phone ?><br/>
						<?= $primary_email ?><br/>
					</address>
				</td>
			</tr>
		</table>
		<hr/>
		<div class="row text-capitalize description">
			<div class="col-md-12">
				<table class="table">
					<thead class="text-capitalize">
					<tr>
						<th style="width: 80%">
							<p class="lead"><?= lang('invoice_description') ?></p>

						</th>
						<th class="text-capitalize">
							<p class="lead"><?= lang('amount') ?></p>

						</th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td>
							<p><?= config_option('module_affiliate_payments_print_invoices_payment_details') ?></p>
						</td>
						<td class="text-capitalize">
							<p><strong><?= format_amount($total_amount) ?></strong></p>
						</td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
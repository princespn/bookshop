<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="checkBg">
	<div class="check-box">
		<div class="check-date"><?=display_date(now(), FALSE, 3, TRUE)?></div>

		<div class="check-name"><?=$member_name?></div>
		<div class="payment-amount"><?= $total_amount ?></div
		><div class="payment-words"><?=num2words($total_amount);?></div>
		<div class="payment-address">
			<strong><?= $member_name ?></strong><br/>
			<?= $address_1 ?> <?= $address_2 ?><br/>
			<?= $city ?> <?= $region_code ?> <?= $postal_code ?><br/>
			<?= $country_name ?><br/>
		</div>
	</div>
	<div class="second-box">
		<div class="box-note-left"><?=$member_name?> -
			<?=config_option(('module_affiliate_payments_print_checks_payment_details'))?></div>
		<div class="box-note-right"><?= format_amount($total_amount) ?></div>
	</div>

	<div class="third-box">
		<div class="box-note-left"><?=$member_name?> -
			<?=config_option('module_affiliate_payments_print_checks_payment_details')?></div>
		<div class="box-note-right"><?= format_amount($total_amount) ?></div>
	</div>
</div>


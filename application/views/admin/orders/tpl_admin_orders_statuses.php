<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
	<div class="col-md-8">
		<div class="input-group text-capitalize">
			<?= generate_sub_headline('status_options', 'fa-file-text-o') ?>
		</div>
	</div>
	<div class="col-md-4 text-right">
		<a href="<?= admin_url('payment_gateways') ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
				class="hidden-xs"><?= lang('view_payment_options') ?></span></a>
	</div>
</div>
<hr/>
<div class="box-info">
	<ul class="resp-tabs nav nav-tabs text-capitalize" role="tablist">
		<li class="active"><a href="#payment" role="tab" data-toggle="tab"><?= lang('payments') ?></a></li>
		<li><a href="#order" role="tab" data-toggle="tab"><?= lang('orders') ?></a></li>
		<li><a href="#card" role="tab" data-toggle="tab"><?= lang('card_types') ?></a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane fade in active" id="payment">
			<?= form_open('', 'role="form" id="payment-form" class="form-horizontal"') ?>
			<h3 class="text-capitalize">
				<span class="pull-right"><a href="javascript:add_payment(<?= count($payment_statuses) ?>)"
				                            class="btn btn-primary"><?= i('fa fa-plus') ?> <?= lang('add_payment_status') ?></a></span>
				<?= lang('payment_statuses') ?></h3>
			<hr/>
			<div id="payment-div">
				<div class="row text-capitalize hidden-xs">
					<div class="col-md-7"><?= tb_header('status_name', '', FALSE) ?></div>
					<div class="col-md-4"><?= tb_header('color', '', FALSE) ?></div>
					<div class="col-md-1"></div>
				</div>
				<br />
				<?php $i = 1; ?>
				<?php if (!empty($payment_statuses)): ?>
					<?php foreach ($payment_statuses as $k => $v): ?>
						<div id="rowdiv-<?= $i ?>">
							<div class="row text-capitalize">
								<div class="col-md-7 r">
									<input id="payment_id-<?= $i ?>" type="text" class="form-control required"
									       name="payment_statuses[<?= $k ?>][payment_status]" <?= is_disabled('update') ?>
									       value="<?= $v['payment_status'] ?>" <?php if ($v['payment_status_id'] <= 4): ?>readonly<?php endif; ?> />
									<?= form_hidden('payment_statuses[' . $k . '][payment_status_id]', $v['payment_status_id']) ?>
								</div>
								<div class="col-md-4 r">
									<input type="text" class="form-control required colors"
									       name="payment_statuses[<?= $k ?>][color]" <?= is_disabled('update') ?>
									       value="<?= $v['color'] ?>"/>
								</div>
								<div class="col-md-1 r text-right">
									<?php if ($v['payment_status_id'] > 4): ?>
										<a href="javascript:remove_div('#rowdiv-<?= $i ?>')"
										   class="btn btn-danger block-phone <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> </a>
									<?php endif; ?>
								</div>
							</div>
							<hr/>
						</div>
						<?php $i++; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
			<div class="row text-capitalize hidden-xs">
				<div class="col-md-12 text-right">
					<button class="btn btn-info navbar-btn block-phone"
					        id="update-button" <?= is_disabled('update', TRUE) ?>
					        type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
				</div>
			</div>
			<?=form_hidden('type', 'payment')?>
			<?= form_close() ?>
		</div>
		<div class="tab-pane fade in" id="order">
			<?= form_open('', 'role="form" id="order-form" class="form-horizontal"') ?>
			<h3 class="text-capitalize">
				<span class="pull-right"><a href="javascript:add_order(<?= count($order_statuses) ?>)"
				                            class="btn btn-primary"><?= i('fa fa-plus') ?> <?= lang('add_order_status') ?></a></span>
				<?= lang('order_statuses') ?></h3>
			<hr/>
			<div id="order-div">
				<div class="row text-capitalize hidden-xs">
					<div class="col-md-7"><?= tb_header('status_name', '', FALSE) ?></div>
					<div class="col-md-4"><?= tb_header('color', '', FALSE) ?></div>
					<div class="col-md-1"></div>
				</div>
				<br />
				<?php $i = 1; ?>
				<?php if (!empty($order_statuses)): ?>
					<?php foreach ($order_statuses as $k => $v): ?>
						<div id="orderdiv-<?= $i ?>">
							<div class="row text-capitalize">
								<div class="col-md-7 r">
									<input id="order_id-<?= $i ?>" type="text" class="form-control required"
									       name="order_statuses[<?= $k ?>][order_status]" <?= is_disabled('update') ?>
									       value="<?= $v['order_status'] ?>" <?php if ($v['order_status_id'] <= 9): ?>readonly<?php endif; ?>/>
									<?= form_hidden('order_statuses[' . $k . '][order_status_id]', $v['order_status_id']) ?>
								</div>
								<div class="col-md-4 r">
									<input type="text" class="form-control required colors"
									       name="order_statuses[<?= $k ?>][color]" <?= is_disabled('update') ?>
									       value="<?= $v['color'] ?>"/>
								</div>
								<div class="col-md-1 r text-right">
									<?php if ($v['order_status_id'] > 9): ?>
										<a href="javascript:remove_div('#orderdiv-<?= $i ?>')"
										   class="btn btn-danger block-phone <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> </a>
									<?php endif; ?>
								</div>
							</div>
							<hr/>
						</div>
						<?php $i++; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
			<div class="row text-capitalize hidden-xs">
				<div class="col-md-12 text-right">
					<button class="btn btn-info navbar-btn block-phone"
					        id="update-button" <?= is_disabled('update', TRUE) ?>
					        type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
				</div>
			</div>
			<?=form_hidden('type', 'order')?>
			<?= form_close() ?>
		</div>
		<div class="tab-pane fade in" id="card">
			<?= form_open('', 'role="form" id="card-form" class="form-horizontal"') ?>
			<h3 class="text-capitalize">
				<span class="pull-right"><a href="javascript:add_card(<?= count($cc_types) ?>)"
				                            class="btn btn-primary"><?= i('fa fa-plus') ?> <?= lang('add_card_type') ?></a></span>
				<?= lang('credit_card_types') ?></h3>
			<hr/>
			<div id="card-div">
				<div class="row text-capitalize hidden-xs">
					<div class="col-md-7"><?= tb_header('credit_card', '', FALSE) ?></div>
					<div class="col-md-1"></div>
				</div>
				<br />
				<?php $i = 1; ?>
				<?php if (!empty($cc_types)): ?>
					<?php foreach ($cc_types as $k => $v): ?>
						<div id="cctypediv-<?= $i ?>">
							<div class="row text-capitalize">
								<div class="col-md-11 r">
									<input id="cc_type_id-<?= $i ?>" type="text" class="form-control required"
									       name="cc_types[<?= $k ?>][cc_type]" <?= is_disabled('update') ?>
									       value="<?= $v['cc_type'] ?>"/>
									<?= form_hidden('cc_types[' . $k . '][cc_type_id]', $v['cc_type_id']) ?>
								</div>
								<div class="col-md-1 r text-right">
									<?php if ($v['cc_type_id'] > 4): ?>
										<a href="javascript:remove_div('#cctypediv-<?= $i ?>')"
										   class="btn btn-danger block-phone <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> </a>
									<?php endif; ?>
								</div>
							</div>
							<hr/>
						</div>
						<?php $i++; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
			<div class="row text-capitalize hidden-xs">
				<div class="col-md-12 text-right">
					<button class="btn btn-info navbar-btn block-phone"
					        id="update-button" <?= is_disabled('update', TRUE) ?>
					        type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
				</div>
			</div>
			<?=form_hidden('type', 'cc_types')?>
			<?= form_close() ?>
		</div>
	</div>
</div>
<script>

	var next_pid = <?=count($payment_statuses) + 1?>;
	var next_oid = <?=count($order_statuses) + 1?>;
	var next_cid = <?=count($cc_types) + 1?>;

	$(document).ready(function () {

		$('.colors').each(function () {
			$(this).minicolors({
				control: $(this).attr('data-control') || 'hue',
				defaultValue: $(this).attr('data-defaultValue') || '',
				format: $(this).attr('data-format') || 'hex',
				keywords: $(this).attr('data-keywords') || '',
				inline: $(this).attr('data-inline') === 'true',
				letterCase: $(this).attr('data-letterCase') || 'lowercase',
				opacity: $(this).attr('data-opacity'),
				position: $(this).attr('data-position') || 'bottom left',
				swatches: $(this).attr('data-swatches') ? $(this).attr('data-swatches').split('|') : [],
				change: function (value, opacity) {
					if (!value) return;
					if (opacity) value += ', ' + opacity;
					if (typeof console === 'object') {
						console.log(value);
					}
				},
				theme: 'bootstrap'
			});

		});

	});

	function add_payment(pid) {

		var html = '<div id="rowdiv-' + next_pid + '">';
		html += '    <div class="row text-capitalize">';
		html += '    <div class="col-md-7 r">';
		html += '    	<input id="payment_id-' + next_pid + '" type="text" class="form-control required"';
		html += '    name="payment_statuses[' + next_pid + '][payment_status]" <?= is_disabled('update') ?> />';
		html += '    	</div>';
		html += '    	<div class="col-md-4 r">';
		html += '    	<input type="text" class="form-control required colors" name="payment_statuses[' + next_pid + '][color]" <?= is_disabled('update') ?> value="#2e6ab0"/>';
		html += '    	</div>';
		html += '    	<div class="col-md-1 r text-right">';
		html += '    	<a href="javascript:remove_div(\'#rowdiv-' + next_pid + '\')" class="btn btn-danger block-phone <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> </a>';
		html += '    </div>';
		html += '   </div>';
		html += '   <hr />';
		html += '</div>';

		$('#payment-div').append(html);

		$('.colors').each(function () {
			$(this).minicolors({
				control: $(this).attr('data-control') || 'hue',
				defaultValue: $(this).attr('data-defaultValue') || '',
				format: $(this).attr('data-format') || 'hex',
				keywords: $(this).attr('data-keywords') || '',
				inline: $(this).attr('data-inline') === 'true',
				letterCase: $(this).attr('data-letterCase') || 'lowercase',
				opacity: $(this).attr('data-opacity'),
				position: $(this).attr('data-position') || 'bottom left',
				swatches: $(this).attr('data-swatches') ? $(this).attr('data-swatches').split('|') : [],
				change: function (value, opacity) {
					if (!value) return;
					if (opacity) value += ', ' + opacity;
					if (typeof console === 'object') {
						console.log(value);
					}
				},
				theme: 'bootstrap'
			});
		});

		next_pid++;
	}
	
	function add_order(oid) {

		var html = '<div id="rowdiv-' + next_oid + '">';
		html += '    <div class="row text-capitalize">';
		html += '    <div class="col-md-7 r">';
		html += '    	<input id="order_id-' + next_oid + '" type="text" class="form-control required"';
		html += '    name="order_statuses[' + next_oid + '][order_status]" <?= is_disabled('update') ?> />';
		html += '    	</div>';
		html += '    	<div class="col-md-4 r">';
		html += '    	<input type="text" class="form-control required colors" name="order_statuses[' + next_oid + '][color]" <?= is_disabled('update') ?> value="#2e6ab0"/>';
		html += '    	</div>';
		html += '    	<div class="col-md-1 r text-right">';
		html += '    	<a href="javascript:remove_div(\'#orderdiv-' + next_oid + '\')" class="btn btn-danger block-phone <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> </a>';
		html += '    </div>';
		html += '   </div>';
		html += '   <hr />';
		html += '</div>';

		$('#order-div').append(html);

		$('.colors').each(function () {
			$(this).minicolors({
				control: $(this).attr('data-control') || 'hue',
				defaultValue: $(this).attr('data-defaultValue') || '',
				format: $(this).attr('data-format') || 'hex',
				keywords: $(this).attr('data-keywords') || '',
				inline: $(this).attr('data-inline') === 'true',
				letterCase: $(this).attr('data-letterCase') || 'lowercase',
				opacity: $(this).attr('data-opacity'),
				position: $(this).attr('data-position') || 'bottom left',
				swatches: $(this).attr('data-swatches') ? $(this).attr('data-swatches').split('|') : [],
				change: function (value, opacity) {
					if (!value) return;
					if (opacity) value += ', ' + opacity;
					if (typeof console === 'object') {
						console.log(value);
					}
				},
				theme: 'bootstrap'
			});
		});

		next_oid++;
	}

	function add_card(oid) {

		var html = '<div id="cctypediv-' + next_cid + '">';
		html += '    <div class="row text-capitalize">';
		html += '    <div class="col-md-11 r">';
		html += '    	<input id="cc_type_id-' + next_cid + '" type="text" class="form-control required"';
		html += '    name="cc_types[' + next_cid + '][cc_type]" <?= is_disabled('update') ?> />';
		html += '    	</div>';
		html += '    	<div class="col-md-1 r text-right">';
		html += '    	<a href="javascript:remove_div(\'#cctypediv-' + next_cid + '\')" class="btn btn-danger block-phone <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> </a>';
		html += '    </div>';
		html += '   </div>';
		html += '   <hr />';
		html += '</div>';

		$('#card-div').append(html);

	}

	$("#payment-form").validate();
	$("#order-form").validate();
	$("#card-form").validate();
</script>
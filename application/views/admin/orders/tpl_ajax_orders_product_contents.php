<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div id="reload-contents">
	<div class="animated fadeIn">
		<?= form_open(admin_url('orders/update_cart'), 'role="form" id="update-cart-form" class="form-horizontal"') ?>
		<?php if (empty($cart['items'])): ?>
			<div class="alert alert-warning">
				<?= i('fa fa-exclamation-circle') ?> <?= lang('no_products_added_to_order') ?>
			</div>
		<?php else: ?>
			<?php if (!empty($cart['shipping_address'])): ?>
				<hr/>
				<div class="row">
					<div class="col-md-6">
						<div id="shipping-address-box">
							<h5 class="text-capitalize"><?= lang('shipping_address') ?></h5>

							<div><?= $cart['shipping_address']['shipping_fname'] ?> <?= $cart['shipping_address']['shipping_lname'] ?></div>
							<div><?= $cart['shipping_address']['order_company'] ?></div>
							<div><?= $cart['shipping_address']['shipping_address_1'] ?></div>
							<div><?= $cart['shipping_address']['shipping_address_2'] ?></div>
							<div>
								<?= $cart['shipping_address']['shipping_city'] ?>
								<?= $cart['shipping_address']['shipping_state_code'] ?>
								<?= $cart['shipping_address']['shipping_postal_code'] ?>
							</div>
							<div><?= $cart['shipping_address']['shipping_country_name'] ?></div>
						</div>
					</div>
					<div class="col-md-6">
						<?php if (!empty($cart['shipping'])): ?>
							<div id="shipping-info-box" class="text-capitalize">
								<h5><?= lang('shipping_information') ?></h5>

								<div><?= $cart['shipping']['shipping_description'] ?></div>
								<h5><?= lang('shipping_cost') ?></h5>

								<div><?= format_amount($cart['totals']['shipping']) ?></div>
							</div>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>
			<hr/>
			<div class="row text-capitalize">
				<div class="col-sm-2 text-center"><h5><?= lang('qty') ?></h5></div>
				<div class="col-sm-6"><h5><?= lang('product_name') ?></h5></div>
				<div class="col-sm-2 text-center"><h5><?= lang('price') ?></h5></div>
				<div class="col-sm-2 text-center"><h5><?= lang('sub_total') ?></h5></div>
			</div>
			<hr/>
			<?php foreach ($cart['items'] as $k => $p): ?>
				<div class="row">
					<div class="col-sm-2 text-center">
						<input type="number" name="item[<?= $k ?>]" value="<?= $p['quantity'] ?>"
						       class="form-control required number" />
					</div>
					<div class="col-sm-6 text-capitalize">
						<h5><?= $p['product_name'] ?></h5>

						<?php if (!empty($p['attribute_data'])): ?>
							<ul class="list-unstyled">
								<?php foreach ($p['attribute_data'] as $a => $b): ?>
									<li>
										<?= order_attributes($a, $b) ?>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</div>
					<div class="col-sm-2 text-center">
						<input type="text" name="price[<?= $k ?>]"
						       value="<?= format_amount($p['unit_price'], FALSE, FALSE, FALSE, TRUE) ?>"
						       class="form-control required number"/>
						<br/>
						<small class="text-muted"><?= lang('tax') ?>: <?= format_amount($p['unit_tax']) ?></small>
					</div>
					<div class="col-sm-2 text-center">
						<?= format_amount(cart_order_price($p) * $p['quantity']) ?>
					</div>
				</div>
				<hr/>
			<?php endforeach; ?>
			<div class="row text-capitalize">
				<div class="col-sm-6"></div>
				<div class="col-sm-4 text-right">
					<h5><?= lang('sub_total') ?></h5>
				</div>
				<div class="col-sm-2 text-center">
					<h5><?= format_amount($cart['totals']['sub_total']) ?></h5>
				</div>
			</div>
			<hr/>
			<?php if (!empty($cart['totals']['coupons'])): ?>
				<div class="row text-capitalize">
					<div class="col-sm-6"></div>
					<div class="col-sm-4 text-right">
						<h5><?= lang('coupon') ?>
							<p>
								<small
									class="text-muted">
									<?php foreach ($cart['totals']['coupon_codes'] as $c): ?>
										<?= $c['coupon_code'] ?>
									<?php endforeach; ?>
								</small>
							</p>
						</h5>
					</div>
					<div class="col-sm-2 text-center">
						<h5><?= format_amount(check_order_coupon_amount($cart['totals'])) ?></h5>
					</div>
				</div>
				<hr/>
			<?php endif; ?>
			<?php if (!empty($cart['totals']['shipping'])): ?>
				<div class="row text-capitalize">
					<div class="col-sm-6"></div>
					<div class="col-sm-4 text-right">
						<h5><?= lang('shipping') ?>
							<p>
								<small
									class="text-muted"><?= $cart['totals']['shipping_item']['shipping_description'] ?></small>
							</p>
						</h5>
					</div>
					<div class="col-sm-2 text-center"><h5><?= format_amount($cart['totals']['shipping']) ?></h5>
					</div>
				</div>
				<hr/>
			<?php endif; ?>
			<div class="row text-capitalize">
				<div class="col-sm-6"></div>
				<div class="col-sm-4 text-right"><h5><?= lang('taxes') ?></h5></div>
				<div class="col-sm-2 text-center"><h5><?= format_amount($cart['totals']['taxes']) ?></h5></div>
			</div>
			<hr/>
			<div class="row text-capitalize">
				<div class="col-sm-6"></div>
				<div class="col-sm-4 text-right">
					<h5><strong><?= lang('total') ?></strong></h5>
				</div>
				<div class="col-sm-2 text-center">
					<h5><strong><?= format_amount($cart['totals']['total_with_shipping']) ?></strong></h5>
				</div>
			</div>
			<hr/>
			<div class="row">
				<div class="col-sm-12 text-right">
					<button id="update-cart-button" class="btn btn-primary"
					        type="submit"><?= i('fa fa-refresh') ?> <?= lang('update_contents') ?> </button>
				</div>
			</div>
		<?php endif; ?>
		<?= form_close() ?>
	</div>
</div>
<script>
	<?php if (uri(4) == 'disable'): ?>
	$('#update-cart-form :input').attr('readonly', true);
	$('#update-cart-button').addClass('hide');
	<?php endif; ?>
	$("#update-cart-form").validate({
		ignore: "",
		errorContainer: $("#error-alert"),
		submitHandler: function (form) {
			$.ajax({
				url: '<?=admin_url('orders/update_cart')?>',
				type: 'POST',
				dataType: 'json',
				data: $('#update-cart-form').serialize(),
				beforeSend: function () {
					$('#update-cart-button').button('loading');
				},
				complete: function () {
					$('#update-cart-button').button('reset');
				},
				success: function (response) {
					if (response.type == 'success') {
						$('.alert-danger').remove();
						$('.form-control').removeClass('error');

						//update the order contents
						$('#reload-contents').load('<?=admin_url('orders/order_contents')?>');
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
</script>
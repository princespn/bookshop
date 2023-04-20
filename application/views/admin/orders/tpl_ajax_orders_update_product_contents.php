<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="animated fadeIn">
	<div class="row">
		<div class="col-md-12">
			<hr/>
			<?php if (empty($row['items'])): ?>
				<div class="alert alert-warning">
					<?= i('fa fa-exclamation-circle') ?> <?= lang('no_products_added_to_order') ?>
				</div>
			<?php else: ?>
				<div class="row text-capitalize">
					<div class="col-sm-2 text-center hidden-xs"><h5><?= lang('sku') ?></h5></div>
					<div class="col-sm-5"><h5><?= lang('product_name') ?></h5></div>
					<div class="col-sm-3 text-center"><h5><?= lang('specs') ?></h5></div>
					<div class="col-sm-2 text-center"><h5><?= lang('qty') ?></h5></div>
				</div>
				<hr/>
				<?php foreach ($row['items'] as $p): ?>
					<div class="row">
						<div class="col-sm-2 text-center hidden-xs">
							<?= $p['product_sku'] ?>
						</div>
						<div class="col-sm-5">
							<h5><?= $p['order_item_name'] ?></h5>
							<?php if (!empty($p['attribute_data'])): ?>
								<ul class="list-unstyled">
									<?php foreach ($p['attribute_data'] as $k => $v): ?>
										<li>
											<?= order_attributes($k, $v) ?>
										</li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						</div>
						<div class="col-sm-3 text-center">
							<?php if (!empty($p['specification_data'])): ?>
								<?php foreach ($p['specification_data'] as $v): ?>
									<div><?= order_specs($v) ?></div>
								<?php endforeach; ?>
							<?php endif; ?>
						</div>
						<div class="col-sm-2 text-center">
							<input type="number" name="item[<?= $p['order_item_id'] ?>]"
							       value="<?= $p['quantity'] ?>"
							       class="update-field form-control required number"/>
						</div>
					</div>
					<hr/>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
	<div class="row">
		<div class="update-field col-sm-12 text-right">
			<button id="update-order-button" class="btn btn-primary"
			        type="submit"><?= i('fa fa-refresh') ?> <?= lang('update_contents') ?> </button>
		</div>
	</div>
</div>

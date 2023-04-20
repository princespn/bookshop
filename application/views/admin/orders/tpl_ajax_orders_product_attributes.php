<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
	<div class="col-lg-12">
		<?php if (!empty($product['pricing_options']) && $product['product_type'] == 'subscription'): ?>
			<h5 class="text-capitalize"><?= lang('select_product_pricing') ?></h5>
			<hr/>
			<div class="form-group">
				<?= lang('pricing_options', 'pricing_options', array('class' => 'col-sm-4 control-label')) ?>
				<div class="col-lg-6">
					<div class="radio">
							<?php foreach ($product['pricing_options'] as $v): ?>
								<input type="radio" name="pricing_data" value="<?= base64_encode(serialize($v)) ?>" checked>
								<label for="pricing_data"><?=format_subscription_interval($v, $product['product_type'])?></label>
								<br/>
							<?php endforeach; ?>
					</div>
				</div>
			</div>
			<hr/>
		<?php endif; ?>
		<?php if (!empty($row)): ?>
			<h5 class="text-capitalize"><?= lang('select_product_options') ?></h5>
			<hr/>
			<?php foreach ($row as $v): ?>
				<div class="form-group">
					<?= lang($v['attribute_name'], $v['attribute_name'], array('class' => 'col-sm-4 control-label')) ?>
					<div class="col-lg-6">
						<?= $v['form_html'] ?>
					</div>
				</div>
				<hr/>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
</div>
<script>
	$('button[id^=\'button-upload\']').on('click', function () {
		var node = this;
		$('#form-upload').remove();
		$('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="files" /><input type="hidden" name="<?=$csrf_token?>" value="<?=$csrf_value?>" /></form>');
		$('#form-upload input[name=\'files\']').trigger('click');

		timer = setInterval(function () {
			if ($('#form-upload input[name=\'files\']').val() != '') {
				clearInterval(timer);
				$.ajax({
					url: '<?=admin_url('orders/upload/') ?>',
					type: 'post',
					dataType: 'json',
					data: new FormData($('#form-upload')[0]),
					cache: false,
					contentType: false,
					processData: false,
					success: function (data) {
						if (data['type'] == 'error') {
							$('#response').html('<?=alert('error')?>');
							$('#msg-details').html(data['msg']);
						}
						else if (data['type'] == 'success') {
							$(node).parent().find('input').attr('value', data['key']);
							$(node).parent().find('input').after('<div class="text-success">' + data['msg'] + '</div>');

						}
					},
					error: function (xhr, ajaxOptions, thrownError) {
						<?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			}
		}, 500);
	});
</script>

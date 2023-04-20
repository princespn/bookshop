<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
	<div class="col-md-8">
		<div class="input-group text-capitalize">
			<?= generate_sub_headline('measurements', 'fa-file-text-o') ?>
		</div>
	</div>
	<div class="col-md-4 text-right"></div>
</div>
<hr/>
<div class="box-info">
	<ul class="resp-tabs nav nav-tabs text-capitalize" role="tablist">
		<li class="active"><a href="#measurements" role="tab" data-toggle="tab"><?= lang('measurements') ?></a></li>
		<li><a href="#weight" role="tab" data-toggle="tab"><?= lang('weight') ?></a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane fade in active" id="measurements">
			<?= form_open('', 'id="measurement-form" class="form-horizontal"') ?>
			<h3 class="text-capitalize">
				<span class="pull-right"><a href="javascript:add_measurement(<?= count($measurements) ?>)"
				                            class="btn btn-primary"><?= i('fa fa-plus') ?> <?= lang('add_measurement') ?></a></span>
				<?= lang('measurement_options') ?></h3>
			<hr/>
			<div id="measurement-div">
				<div class="row text-capitalize hidden-xs">
					<div class="col-md-5"><?= tb_header('name', '', FALSE) ?></div>
					<div class="col-md-4"><?= tb_header('unit', '', FALSE) ?></div>
					<div class="col-md-1"><?= tb_header('value', '', FALSE) ?></div>
					<div class="col-md-1"><?= tb_header('sort_order', '', FALSE) ?></div>
					<div class="col-md-1"></div>
				</div>
				<br />
				<?php $i = 1; ?>
				<?php if (!empty($measurements)): ?>
					<?php foreach ($measurements as $k => $v): ?>
						<div id="rowdiv-<?= $i ?>">
							<div class="row text-capitalize">
								<div class="col-md-4 r">
									<input id="measurement_id-<?= $i ?>" type="text" class="form-control required"
									       name="measurements[<?= $k ?>][name]" <?= is_disabled('update') ?>
									       value="<?= $v['name'] ?>"/>
									<?= form_hidden('measurements[' . $k . '][measure_id]', $v['measure_id']) ?>
								</div>
								<div class="col-md-4 r">
									<input type="text" class="form-control required"
									       name="measurements[<?= $k ?>][unit]" <?= is_disabled('update') ?>
									       value="<?= $v['unit'] ?>"/>
								</div>
								<div class="col-md-2 r">
									<input type="text" class="form-control required number"
									       name="measurements[<?= $k ?>][value]" <?= is_disabled('update') ?>
									       value="<?= $v['value'] ?>"/>
								</div>
								<div class="col-md-1 r">
									<input type="text" class="form-control required digits"
									       name="measurements[<?= $k ?>][sort_order]" <?= is_disabled('update') ?>
									       value="<?= $v['sort_order'] ?>"/>
								</div>
								<div class="col-md-1 r text-right">
									<?php if ($v['measure_id'] > 3): ?>
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
            <nav class="navbar navbar-fixed-bottom save-changes">
                <div class="container text-right">
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-info btn-submit navbar-btn block-phone"
                                    id="update-button" <?= is_disabled('update', TRUE) ?>
                                    type="submit"><?= i('fa fa-refresh') ?> <span><?= lang('save_changes') ?></span></button>
                        </div>
                    </div>
                </div>
            </nav>
			<?=form_hidden('type', 'measurement')?>
			<?= form_close() ?>
		</div>
		<div class="tab-pane fade in" id="weight">
			<?= form_open('', 'id="order-form" class="form-horizontal"') ?>
			<h3 class="text-capitalize">
				<span class="pull-right"><a href="javascript:add_weight(<?= count($weight) ?>)"
				                            class="btn btn-primary"><?= i('fa fa-plus') ?> <?= lang('add_weight') ?></a></span>
				<?= lang('weight_options') ?></h3>
			<hr/>
			<div id="order-div">
				<div class="row text-capitalize hidden-xs">
					<div class="col-md-5"><?= tb_header('name', '', FALSE) ?></div>
					<div class="col-md-4"><?= tb_header('unit', '', FALSE) ?></div>
					<div class="col-md-1"><?= tb_header('value', '', FALSE) ?></div>
					<div class="col-md-1"><?= tb_header('sort_order', '', FALSE) ?></div>
					<div class="col-md-1"></div>
				</div>
				<br />
				<?php $j = 1; ?>
				<?php if (!empty($weight)): ?>
					<?php foreach ($weight as $k => $v): ?>
						<div id="weightdiv-<?= $j?>">
							<div class="row text-capitalize">
								<div class="col-md-4 r">
									<input id="measurement_id-<?= $j ?>" type="text" class="form-control required"
									       name="weight[<?= $k ?>][name]" <?= is_disabled('update') ?>
									       value="<?= $v['name'] ?>"/>
									<?= form_hidden('weight[' . $k . '][weight_id]', $v['weight_id']) ?>
								</div>
								<div class="col-md-4 r">
									<input type="text" class="form-control required"
									       name="weight[<?= $k ?>][unit]" <?= is_disabled('update') ?>
									       value="<?= $v['unit'] ?>"/>
								</div>
								<div class="col-md-2 r">
									<input type="text" class="form-control required number"
									       name="weight[<?= $k ?>][value]" <?= is_disabled('update') ?>
									       value="<?= $v['value'] ?>"/>
								</div>
								<div class="col-md-1 r">
									<input type="text" class="form-control required digits"
									       name="weight[<?= $k ?>][sort_order]" <?= is_disabled('update') ?>
									       value="<?= $v['sort_order'] ?>"/>
								</div>
								<div class="col-md-1 r text-right">
									<?php if ($v['weight_id'] > 4): ?>
										<a href="javascript:remove_div('#weightdiv-<?= $j ?>')"
										   class="btn btn-danger block-phone <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> </a>
									<?php endif; ?>
								</div>
							</div>
							<hr/>
						</div>
						<?php $j++; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
            <nav class="navbar navbar-fixed-bottom save-changes">
                <div class="container text-right">
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-info btn-submit navbar-btn block-phone"
                                    id="update-button" <?= is_disabled('update', TRUE) ?>
                                    type="submit"><?= i('fa fa-refresh') ?> <span><?= lang('save_changes') ?></span></button>
                        </div>
                    </div>
                </div>
            </nav>
			<?=form_hidden('type', 'weight')?>
			<?= form_close() ?>
		</div>
	</div>
</div>
<script>

	var next_pid = <?=$i++?>;
	var next_oid = <?=$j++?>;

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

	function add_measurement(pid) {

		var html = '<div id="rowdiv-' + next_pid + '">';
		html += '    <div class="row text-capitalize">';
		html += '    <div class="col-md-4 r">';
		html += '    	<input id="measurement_id-' + next_pid + '" type="text" class="form-control required"';
		html += '    name="measurements[' + next_pid + '][name]" <?= is_disabled('update') ?> />';
		html += '    	</div>';
		html += '    	<div class="col-md-4 r">';
		html += '    	<input type="text" class="form-control required" name="measurements[' + next_pid + '][unit]" <?= is_disabled('update') ?> value=""/>';
		html += '    	</div>';
		html += '    	<div class="col-md-2 r">';
		html += '    	<input type="text" class="form-control required number" name="measurements[' + next_pid + '][value]" <?= is_disabled('update') ?> value=""/>';
		html += '    	</div>';
		html += '    	<div class="col-md-1 r">';
		html += '    	<input type="text" class="form-control required digits" name="measurements[' + next_pid + '][sort_order]" <?= is_disabled('update') ?> value="1"/>';
		html += '    	</div>';
		html += '    	<div class="col-md-1 r text-right">';
		html += '    	<a href="javascript:remove_div(\'#rowdiv-' + next_pid + '\')" class="btn btn-danger block-phone <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> </a>';
		html += '    </div>';
		html += '   </div>';
		html += '   <hr />';
		html += '</div>';

		$('#measurement-div').append(html);

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
	
	function add_weight(oid) {

		var html = '<div id="weightdiv-' + next_oid + '">';
		html += '    <div class="row text-capitalize">';
		html += '    <div class="col-md-4 r">';
		html += '    	<input id="weight-' + next_oid + '" type="text" class="form-control required"';
		html += '    name="weight[' + next_oid + '][name]" <?= is_disabled('update') ?> />';
		html += '    	</div>';
		html += '    	<div class="col-md-4 r">';
		html += '    	<input type="text" class="form-control required" name="weight[' + next_oid + '][unit]" <?= is_disabled('update') ?> value=""/>';
		html += '    	</div>';
		html += '    	<div class="col-md-2 r">';
		html += '    	<input type="text" class="form-control required" name="weight[' + next_oid + '][value]" <?= is_disabled('update') ?> value=""/>';
		html += '    	</div>';
		html += '    	<div class="col-md-1 r">';
		html += '    	<input type="text" class="form-control required" name="weight[' + next_oid + '][sort_order]" <?= is_disabled('update') ?> value="1"/>';
		html += '    	</div>';
		html += '    	<div class="col-md-1 r text-right">';
		html += '    	<a href="javascript:remove_div(\'#weightdiv-' + next_oid + '\')" class="btn btn-danger block-phone <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> </a>';
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

    $("#measurement-form").validate({
        ignore: "",
        submitHandler: function (form) {
            ajax_it('<?=current_url()?>', 'measurement-form');
        }
    });

	$("#order-form").validate({
        ignore: "",
        submitHandler: function (form) {
            ajax_it('<?=current_url()?>', 'order-form');
        }
    });
</script>
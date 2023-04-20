<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
<div class="row">
	<div class="col-md-4">
		<?= generate_sub_headline(lang(CONTROLLER_CLASS) . ' - ' . $row['module']['module_name'], 'fa-anchor', '', FALSE) ?>
    </div>
	<div class="col-md-8 text-right">
		<a href="<?= admin_url('shipping/view') ?>" class="btn btn-primary">
			<?= i('fa fa-search') ?> <span class="hidden-xs"><?= lang('view_shipping_options') ?></span></a>
	</div>
</div>
<hr/>
<div class="row">
	<div class="col-md-12">
		<div class="box-info">
			<ul class="nav nav-tabs text-capitalize" role="tablist">
				<li class="active"><a href="#config" role="tab" data-toggle="tab"><?= lang('configuration') ?></a></li>
				<li><a href="#regions" role="tab" data-toggle="tab"><?= lang('shipping_zones') ?></a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="config">
					<h3 class="text-capitalize">
						<?= lang('module_configuration') ?>
					</h3>
					<span><?= $row['module']['module_description'] ?></span>
					<hr/>
					<div class="form-group">
						<?= lang('shipping_name', 'module_name', array('class' => 'col-md-3 control-label')) ?>
						<div class="col-md-5">
							<?= form_input('module_name', set_value('module_name', $row['module']['module_name']), 'class="' . css_error('module_name') . ' form-control"') ?>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<?= lang('enabled', 'module_status', array('class' => 'col-md-3 control-label')) ?>
						<div class="col-md-5">
							<?= form_dropdown('module_status', options('yes_no'), $row['module']['module_status'], 'class="form-control"') ?>
						</div>
					</div>
					<hr/>
					<?php if (!empty($row['values'])): ?>
						<?php foreach ($row['values'] as $v): ?>
							<div class="form-group">
								<?= lang(format_settings_label($v['key'], CONTROLLER_CLASS, $row['module']['module_folder']), $v['key'], array('class' => 'col-md-3 control-label')) ?>
								<div class="col-md-5">
									<?= generate_settings_field($v, $v['value']) ?>
								</div>
							</div>
							<hr/>
						<?php endforeach; ?>
					<?php endif; ?>
					<div class="form-group">
						<?= lang('sort_order', 'module_sort_order', array('class' => 'col-md-3 control-label')) ?>
						<div class="col-md-5">
							<input type="number" name="module_sort_order"
							       value="<?= set_value('module_sort_order', $row['module']['module_sort_order']) ?>"
							       class="form-control number">
						</div>
					</div>
					<hr/>
				</div>
				<div class="tab-pane" id="regions">
					<div>
						<h3 class="text-capitalize">
							<span class="visible-lg"><?= lang('manage_shipping_zones') ?></span>
							<span class="pull-right"><a href="javascript:add_zone(<?= count($module_row['zones']) ?>)"
							                            class="btn btn-primary"><?= i('fa fa-plus') ?> <?= lang('add_zone') ?></a></span>
						</h3>
						<span class="visible-lg"><?= lang('setup_zone_specific_shipping_amounts') ?></span>
					</div>
					<hr/>
					<div id="regions-div">
						<div class="row text-capitalize hidden-xs">
							<div class="col-lg-3"><?= tb_header('ship_to_zones', '', FALSE) ?></div>
							<div class="col-lg-2"><?= tb_header('shipping_description', '', FALSE) ?></div>
							<div class="col-lg-2"><?= tb_header('min_quantity_amount', '', FALSE) ?></div>
							<div class="col-lg-2"><?= tb_header('max_quantity_amount', '', FALSE) ?></div>
							<div class="col-lg-2"><?= tb_header('shipping_amount', '', FALSE) ?></div>
							<div class="col-lg-1"></div>
						</div>
						<?php $i = 1; ?>
                        <div id="sortable">
						<?php if (!empty($module_row['zones'])): ?>
							<?php foreach ($module_row['zones'] as $k => $v): ?>
                                <div class="ui-state-default rowdiv-<?= $i ?>" id="sortid-<?= $v['id'] ?>">
									<div class="row text-capitalize">
										<div class="col-lg-3 r">
											<select id="zone_id-<?= $i ?>" class="zone_id form-control select2"
											        name="zone[<?= $k ?>][zone_id]">
												<option value="<?= $v['zone_id'] ?>"
												        selected><?= $v['zone_name'] ?></option>
											</select>
											<?= form_hidden('zone[' . $k . '][id]', $v['id']) ?>
										</div>
										<div class="col-lg-2 r">
											<input type="text" name="zone[<?= $k ?>][shipping_description]"
											       value="<?= set_value('shipping_description', $v['shipping_description']) ?>"
											       class="form-control">
										</div>
										<div class="col-lg-2 r">
											<input type="text" name="zone[<?= $k ?>][min_amount]"
											       value="<?= set_value('min_amount', $v['min_amount']) ?>"
											       placeholder="<?= lang('min_amount') ?>"
											       class="form-control">
										</div>
										<div class="col-lg-2 r">
											<input type="text" name="zone[<?= $k ?>][max_amount]"
											       value="<?= set_value('max_amount', $v['max_amount']) ?>"
											       placeholder="<?= lang('max_amount') ?>"
											       class="form-control">
										</div>
										<div class="col-lg-2 r">
											<input type="text" name="zone[<?= $k ?>][amount]"
											       value="<?= set_value('amount', $v['amount']) ?>"
											       placeholder="<?= lang('amount') ?>" class="form-control">
										</div>
										<div class="col-lg-1 r text-right">
                                            <span class="btn btn-primary handle visible-lg <?= is_disabled('update', TRUE) ?>">
                                            <i class="fa fa-sort"></i></span>
											<a href="javascript:remove_div('.rowdiv-<?= $i ?>')"
											   class="btn btn-danger block-phone <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> </a>
										</div>
									</div>
									<hr/>
								</div>
								<?php $i++; ?>
							<?php endforeach; ?>
						<?php else: ?>
							<hr/>
							<div class="rowdiv-0">
								<div class="row text-capitalize">
									<div class="col-lg-3">
										<select id="zone_id-0" class="zone_id form-control select2"
										        name="zone[0][zone_id]">
											<option value="0" selected><?= lang('select_zone') ?></option>
										</select>
									</div>
									<div class="col-lg-2 r">
										<input type="text" name="zone[0][shipping_description]"
										       class="form-control">
									</div>
									<div class="col-lg-2 r">
										<input type="text" name="zone[0][min_amount]" value="0.00"
										       class="form-control">
									</div>
									<div class="col-lg-2 r">
										<input type="text" name="zone[0][max_amount]" value="0.00"
										       class="form-control">
									</div>
									<div class="col-lg-2 r">
										<input type="text" name="zone[0][amount]" value="0.00"
										       class="form-control">
									</div>
									<div class="col-lg-1 text-right">
										<a href="javascript:remove_div('.rowdiv-0')"
										   class="btn btn-danger block-phone <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> </a>
									</div>
								</div>
								<hr/>
							</div>
						<?php endif; ?>
                        </div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php if (CONTROLLER_FUNCTION == 'update'): ?>
	<?= form_hidden('module_id', $id) ?>
<?php endif; ?>
<nav class="navbar navbar-fixed-bottom save-changes">
	<div class="container text-right">
		<div class="row">
			<div class="col-md-12">
				<?php if (CONTROLLER_FUNCTION == 'create'): ?>
					<input type="submit" name="redir_button" value="<?= lang('save_add_another') ?>"
					       class="btn btn-success navbar-btn block-phone"/>
				<?php endif; ?>
				<button class="btn btn-info navbar-btn block-phone"
				        id="update-button" <?= is_disabled('update', TRUE) ?>
				        type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
			</div>
		</div>
	</div>
</nav>
<?= form_close() ?>
<br/>
<div id="update"></div>
<script>
    $(function () {
        $('#sortable').sortable({
            handle: '.handle',
            placeholder: "ui-state-highlight",
            update: function () {
                var order = $('#sortable').sortable('serialize');
                $("#update").load("<?=admin_url(CONTROLLER_CLASS . '/update_order/' . $row['module']['module_folder'])?>?" + order);
            }
        });
    });

	var next_id = <?=count($module_row['zones']) + 1?>;

	//search countries
	$(".zone_id").select2({
		ajax: {
			url: '<?=admin_url(TBL_ZONES . '/search_zones/')?>',
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					zone_name: params.term, // search term
					page: params.page
				};
			},
			processResults: function (data, page) {
				return {
					results: $.map(data, function (item) {
						return {
							id: item.zone_id,
							text: item.zone_name
						}
					})
				};
			},
			cache: true
		},
		minimumInputLength: 2
	});

	function add_zone(image_id) {

		var html = '<div id="rowdiv-' + next_id + '">';
		html += '    <div class="row text-capitalize">';
		html += '        <div class="col-lg-3 r">';
		html += '            <select id="zone_id-' + next_id + '" class="zone_id form-control select2" name="zone[' + next_id + '][zone_id]">';
		html += '            <option value="0" selected><?=lang('select_zone')?></option>';
		html += '            </select>';
		html += '        </div>';
		html += '        <div class="col-lg-2 r">';
		html += '            <input type="text" name="zone[' + next_id + '][shipping_description]" class="form-control">';
		html += '        </div>';
		html += '        <div class="col-lg-2 r">';
		html += '            <input type="text" name="zone[' + next_id + '][min_amount]" value="0.00" class="form-control">';
		html += '        </div>';
		html += '        <div class="col-lg-2 r">';
		html += '            <input type="text" name="zone[' + next_id + '][max_amount]" value="0.00" class="form-control">';
		html += '        </div>';
		html += '        <div class="col-lg-2 r">';
		html += '            <input type="text" name="zone[' + next_id + '][amount]" value="0.00" class="form-control">';
		html += '        </div>';
		html += '       <div class="col-lg-1 text-right">';
		html += '           <a href="javascript:remove_div(\'#imagediv-' + next_id + '\')" class="btn btn-danger <?=is_disabled('delete')?>"><?=i('fa fa-trash-o')?> </a>';
		html += '       </div>';
		html += '   </div>';
		html += '   <hr />';
		html += '</div>';

		$('#regions-div').append(html);

		$(".zone_id").select2({
			ajax: {
				url: '<?=admin_url(TBL_ZONES . '/search_zones/')?>',
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						zone_name: params.term, // search term
						page: params.page
					};
				},
				processResults: function (data, page) {
					return {
						results: $.map(data, function (item) {
							return {
								id: item.zone_id,
								text: item.zone_name
							}
						})
					};
				},
				cache: true
			},
			minimumInputLength: 1
		});
		next_id++;
	}

	$("#form").validate({
		ignore: "",
		submitHandler: function (form) {
			$.ajax({
				url: '<?=current_url()?>',
				type: 'POST',
				dataType: 'json',
				data: $('#form').serialize(),
				success: function (response) {
					if (response.type == 'success') {
						$('.alert-danger').remove();
						$('.form-control').removeClass('error');

						if (response.redirect) {
							location.href = response.redirect;
						}
						else {
							$('#response').html('<?=alert('success')?>');

							setTimeout(function () {
								$('.alert-msg').fadeOut('slow');
							}, 5000);
						}
					}
					else {
						$('#response').html('<?=alert('error')?>');
					}

					$('#msg-details').html(response.msg);
				},
				error: function (xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	});
</script>
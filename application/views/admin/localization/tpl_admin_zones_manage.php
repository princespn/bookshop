<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'id="form" class="form-horizontal"') ?>
<div class="row">
	<div class="col-md-4">
		<?= generate_sub_headline(CONTROLLER_CLASS, 'fa-edit', '') ?>
	</div>
	<div class="col-md-8 text-right">
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('left', CONTROLLER_METHOD, $row['prev']) ?>
			<?php if ($id > 1): ?>
				<a data-href="<?= admin_url(TBL_ZONES . '/delete/' . $row['zone_id']) ?>" data-toggle="modal"
				   data-target="#confirm-delete" href="#" 
				   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
						class="hidden-xs"><?= lang('delete') ?></span></a>
			<?php endif; ?>
		<?php endif; ?>

		<a href="<?= admin_url(TBL_ZONES . '/view') ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
				class="hidden-xs"><?= lang('view_zones') ?></span></a>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('right', CONTROLLER_METHOD, $row['next']) ?>
		<?php endif; ?>
	</div>
</div>
<hr/>
<?php if (empty($row['regions'])): ?>
	<?php $n = 1;?>
<?php else: ?>
	<?php $n = count($row['regions']) ?>
<?php endif; ?>
<div class="row">
	<div class="col-md-12">
		<div class="box-info">
			<ul class="nav nav-tabs text-capitalize" role="tablist">
				<li class="active"><a href="#name" role="tab" data-toggle="tab"><?= lang('name') ?></a></li>
				<li><a href="#regions" role="tab" data-toggle="tab"><?= lang('zone_regions') ?></a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="name">
					<div class="hidden-xs">
						<h3 class="text-capitalize">
							<?php if (CONTROLLER_FUNCTION == 'update'): ?>
								<?= lang('update_zone') ?>
							<?php else: ?>
								<?= lang('add_zone') ?>
							<?php endif; ?>
						</h3>
						<span><?= lang('setup_zones_for_shipping_and_taxes') ?></span>
					</div>
					<hr/>
					<div class="form-group">
						<label for="zone_name" class="col-md-3 control-label"><?= lang('zone_name') ?></label>

						<div class="col-md-5">
							<?= form_input('zone_name', set_value('zone_name', $row['zone_name']), 'class="' . css_error('zone_name') . ' form-control"') ?>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<label for="zone_description" class="col-md-3 control-label"><?= lang('description') ?></label>

						<div class="col-md-5">
                            <textarea name="zone_description" rows="4"
                                      class="form-control"><?= set_value('zone_description', $row['zone_description']) ?></textarea>
						</div>
					</div>
					<hr/>
				</div>
				<div class="tab-pane" id="regions">
					<div>
						<h3 class="text-capitalize"> <?= lang('manage_regions') ?> <span class="pull-right"><a
									href="javascript:add_region(<?= $n ?>)"
									class="btn btn-primary"><?= i('fa fa-plus') ?> <?= lang('add_region') ?></a></span>
						</h3>
						<span><?= lang('add_regions_to_zones') ?></span>
						<hr/>
						<div id="regions-div">
							<div class="row text-capitalize">
								<div class="col-lg-1"><?= tb_header('priority', '', FALSE) ?></div>
								<div class="col-lg-5"><?= tb_header('country', '', FALSE) ?></div>
								<div class="col-lg-5"><?= tb_header('region', '', FALSE) ?></div>
								<div class="col-lg-1"></div>
							</div>
							<?php $i = 1; ?>
							<?php if (!empty($row['regions'])): ?>
								<?php foreach ($row['regions'] as $k => $v): ?>
									<div id="rowdiv-<?= $i ?>">
										<div class="row text-capitalize">
											<div class="col-lg-1">
												<input type="number" name="zone[<?= $k ?>][priority]"
												       value="<?= set_value('priority', $v['priority']) ?>"
												       class="form-control">
											</div>
											<div class="col-lg-5">
												<select id="country_id-<?= $i ?>"
												        class="country_id form-control select2"
												        name="zone[<?= $k ?>][country_id]"
												        onchange="updateregion('<?= $i ?>', 'zone[<?= $k ?>][region_id]')">
													<option value="<?= $v['country_id'] ?>"
													        selected><?= $v['country_name'] ?></option>
												</select>
											</div>
											<div class="col-lg-5">
												<div id="region_id-<?= $i ?>">
													<?= form_dropdown('zone[' . $k . '][region_id]', options('regions', FALSE, $v['regions_array']), $v['region_id'], 'class="form-control s2"') ?>
													<?= form_hidden('zone[' . $k . '][region_zone_id]', $v['region_zone_id']) ?>
												</div>
											</div>
											<div class="col-lg-1 text-right">
												<a href="javascript:remove_div('#rowdiv-<?= $i ?>')"
												   class="btn btn-danger block-phone <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> </a>
											</div>
										</div>
										<hr/>
									</div>
									<?php $i++; ?>
								<?php endforeach; ?>
							<?php else: ?>
								<hr/>
								<div id="rowdiv-0">
									<div class="row text-capitalize">
										<div class="col-lg-1">
											<input type="number" name="zone[0][priority]" value="1"
											       class="form-control">
										</div>
										<div class="col-lg-5">
											<select id="country_id-0" class="country_id form-control select2"
											        name="zone[0][country_id]"
											        onchange="updateregion('0', 'zone[0][region_id]')">
												<option value="0" selected><?= lang('all_countries') ?></option>
											</select>
										</div>
										<div class="col-lg-5">
											<div id="region_id-0">
												<select id="region_id-0" class="country_id form-control select2"
												        name="zone[0][region_id]">
													<option value="0" selected><?= lang('all_regions') ?></option>
												</select>

											</div>
										</div>
										<div class="col-lg-1 text-right">
											<a href="javascript:remove_div('#rowdiv-0')"
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
	<?= form_hidden('zone_id', $row['zone_id']) ?>
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
<!-- Load JS for Page -->
<script>
	var next_id = <?=$n + 1?>;

	//search countries
	$(".country_id").select2({
		ajax: {
			url: '<?=admin_url(TBL_COUNTRIES . '/search_countries/')?>',
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					country_name: params.term, // search term
					page: params.page
				};
			},
			processResults: function (data, page) {
				return {
					results: $.map(data, function (item) {
						return {
							id: item.country_id,
							text: item.country_name
						}
					})
				};
			},
			cache: true
		},
		minimumInputLength: 2
	});

	function add_region(image_id) {

		var html = '<div id="rowdiv-' + next_id + '">';
		html += '    <div class="row text-capitalize">';
		html += '        <div class="col-lg-1">';
		html += '            <input type="number" name="zone[' + next_id + '][priority]" value=' + next_id + ' class="form-control">';
		html += '        </div>';
		html += '        <div class="col-lg-5">';
		html += '            <select id="country_id-' + next_id + '" class="country_id form-control select2" name="zone[' + next_id + '][country_id]" onchange="updateregion(\'' + next_id + '\', \'zone[' + next_id + '][region_id]\')">';
		html += '            <option value="0" selected><?=lang('all_countries')?></option>';
		html += '            </select>';
		html += '        </div>';
		html += '       <div class="col-lg-5">';
		html += '       <div id="region_id-' + next_id + '">';
		html += '           <select id="region_id-' + next_id + '" class="region_id form-control select2" name="zone[' + next_id + '][region_id]">';
		html += '               <option value="0" selected><?=lang('all_regions')?></option>';
		html += '           </select>';
		html += '       </div>';
		html += '       </div>';
		html += '       <div class="col-lg-1 text-right">';
		html += '           <a href="javascript:remove_div(\'#imagediv-' + next_id + '\')" class="btn btn-danger <?=is_disabled('delete')?>"><?=i('fa fa-trash-o')?> </a>';
		html += '       </div>';
		html += '   </div>';
		html += '   <hr />';
		html += '</div>';

		$('#regions-div').append(html);

		$(".country_id").select2({
			ajax: {
				url: '<?=admin_url(TBL_COUNTRIES . '/search_countries/all_regions/')?>',
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						country_name: params.term, // search term
						page: params.page
					};
				},
				processResults: function (data, page) {
					return {
						results: $.map(data, function (item) {
							return {
								id: item.country_id,
								text: item.country_name
							}
						})
					};
				},
				cache: true
			},
			minimumInputLength: 2
		});


		next_id++;
	}

	function updateregion(id, select) {
		$.get('<?=admin_url('regions/load_regions/')?>' + select + '/all_regions', {country_id: $('#country_id-' + id).val()},
			function (data) {
				$('#region_id-' + id).html(data);
				$(".s2").select2();
			}
		);
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
					<?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	});
</script>
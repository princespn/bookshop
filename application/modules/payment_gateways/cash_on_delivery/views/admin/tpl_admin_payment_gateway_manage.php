<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
<div class="row">
	<div class="col-md-4">
		<?= generate_sub_headline(lang(CONTROLLER_CLASS) . ' - ' . $row['module']['module_name'], 'fa-pencil', '',  FALSE) ?>
	</div>
	<div class="col-md-8 text-right">
		<a href="<?= admin_url(CONTROLLER_CLASS . '/view') ?>" class="btn btn-primary">
			<?= i('fa fa-search') ?> <span class="hidden-xs"><?= lang('view_payment_gateways') ?></span></a>
	</div>
</div>
<hr/>
<div class="row">
	<div class="col-md-12">
		<div class="box-info">
			<ul class="nav nav-tabs text-capitalize" role="tablist">
				<li class="active"><a href="#config" role="tab" data-toggle="tab"><?= lang('configuration') ?></a></li>
				<li><a href="#regions" role="tab" data-toggle="tab"><?= lang('payment_zones') ?></a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="config">
					<h3 class="text-capitalize">
						<?= lang('module_configuration') ?>
					</h3>
					<span><?= $row['module']['module_name'] ?></span>
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
							       class="form-control number required">
						</div>
					</div>
					<hr/>
				</div>
				<div class="tab-pane" id="regions">
					<div>
						<h3 class="text-capitalize">
							<span class="visible-lg"><?= lang('manage_payment_zones_for_shipped_items') ?></span>
							<span class="pull-right"><a href="javascript:add_zone(<?= count($module_row['zones']) ?>)"
							                            class="btn btn-primary"><?= i('fa fa-plus') ?> <?= lang('add_zone') ?></a></span>
						</h3>
						<span class="visible-lg"><?= lang('restrict_payment_option_to_specific_shipping_zones') ?></span>
					</div>
					<hr/>
					<div id="regions-div">
						<div class="row text-capitalize hidden-xs">
							<div class="col-lg-6"><?= tb_header('payment_zones', '', FALSE) ?></div>
							<div class="col-lg-5"><?= tb_header('description', '', FALSE) ?></div>
							<div class="col-lg-1"></div>
						</div>
						<?php $i = 1; ?>
						<?php if (!empty($module_row['zones'])): ?>
							<?php foreach ($module_row['zones'] as $k => $v): ?>
								<div id="rowdiv-<?= $i ?>">
									<div class="row text-capitalize">
										<div class="col-lg-6 r">
											<select id="zone_id-<?= $i ?>" class="zone_id form-control select2"
											        name="zone[<?= $k ?>][zone_id]">
												<option value="<?= $v['zone_id'] ?>"
												        selected><?= $v['zone_name'] ?></option>
											</select>
											<?= form_hidden('zone[' . $k . '][id]', $v['id']) ?>
										</div>
										<div class="col-lg-5 r">
											<input type="text" name="zone[<?= $k ?>][description]"
											       value="<?= set_value('description', $v['description']) ?>"
											       class="form-control">
										</div>
										<div class="col-lg-1 r text-right">
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
									<div class="col-lg-6 r">
										<select id="zone_id-0" class="zone_id form-control select2"
										        name="zone[0][zone_id]">
											<option value="0" selected><?= lang('all_zones') ?></option>
										</select>
									</div>
									<div class="col-lg-5 r">
										<input type="text" name="zone[0][description]" class="form-control">
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


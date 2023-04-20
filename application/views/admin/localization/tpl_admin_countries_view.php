<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'id="form"') ?>
	<div class="row">
		<div class="col-md-8">
			<div class="input-group text-capitalize">
				<?= generate_sub_headline('countries', 'fa-map-marker', '') ?>
			</div>
		</div>
		<div class="col-md-4 text-right">
			<?= next_page('left', $paginate); ?>
			<a href="<?= admin_url(CONTROLLER_CLASS . '/create/') ?>"
			   class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
					class="hidden-xs"><?= lang('add_country') ?></span></a>
			<?= next_page('right', $paginate); ?>
		</div>
	</div>
	<hr/>
<?php if (empty($rows['values'])): ?>
	<?= tpl_no_values(CONTROLLER_CLASS . '/create', 'add_country') ?>
<?php else: ?>
	<div class="box-info mass-edit">
		<div>
			<table class="table table-striped table-hover">
				<thead class="text-capitalize">
				<tr>
					<th class="text-center"><?= form_checkbox('', '', '', 'class="check-all"') ?></th>
					<th class="text-center hidden-xs"><?= tb_header('visible', 'status') ?></th>
					<th><?= tb_header('country', 'country_name') ?></th>
					<th class="text-center"><?= tb_header('iso2', 'country_iso_code_2') ?></th>
					<th class="text-center"><?= tb_header('iso3', 'country_iso_code_3') ?></th>
					<th class="text-center hidden-xs"><?= tb_header('flag', '', FALSE) ?></th>
					<th class="text-center hidden-xs"><?= tb_header('sort', 'sort_order') ?></th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($rows['values'] as $v): ?>
					<tr>
						<td class="text-center"><?= form_checkbox('country[' . $v['country_id'] . '][update]', $v['country_id']) ?></td>
						<td style="width: 8%" class="text-center hidden-xs">
							<a href="<?= admin_url('update_status/table/' . CONTROLLER_CLASS . '/type/status/key/country_id/id/' . $v['country_id']) ?>"
							   class="btn btn-default"><?= set_status($v['status']) ?></a>
						</td>
						<td>
							<input type="text" class="form-control required"
							       name="country[<?= $v['country_id'] ?>][country_name]" value="<?= $v['country_name'] ?>"/>
						</td>
						<td style="width: 8%" class="text-center">
							<?= $v['country_iso_code_2'] ?>
						</td>
						<td style="width: 8%" class="text-center">
							<?= $v['country_iso_code_3'] ?>
						</td>
						<td class="text-center hidden-xs">
							<?= i('flag flag-' . strtolower($v['country_iso_code_2'])) ?>
						</td>
						<td style="width: 8%" class="text-center hidden-xs">
							<input type="number" class="form-control required digits"
							       name="country[<?= $v['country_id'] ?>][sort_order]" value="<?= $v['sort_order'] ?>"/>
						</td>
						<td class="text-right">
							<a href="<?= admin_url('regions/view?country_id=' . $v['country_id'] . '&column=region_name&order=ASC') ?>"
							   class="tip btn btn-info" data-toggle="tooltip" data-placement="bottom"
							   title="<?= lang('view_regions') ?>"><?= i('fa fa-map-marker') ?></a>
							<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['country_id']) ?>"
							   class="btn btn-default <?= is_disabled('update', TRUE) ?>"
							   title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
							<?php if ($v['country_id'] != $sts_site_default_country): ?>
								<!-- <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['country_id']) ?>"
                                   data-toggle="modal" data-target="#confirm-delete" href="#"
                                   class="md-trigger btn btn-danger visible-lg <?= is_disabled('delete') ?>">
                                   <?= i('fa fa-trash-o') ?></a> -->
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
				<tfoot>
				<tr>
					<td colspan="4">
						<div class="input-group text-capitalize">
							<span class="input-group-addon"><small><?= lang('mark_checked_as') ?></small></span>
							<?= form_dropdown('change-status', options('active', 'deleted'), '', 'id="change-status" class="form-control"') ?>
							<span class="input-group-btn">
                        <button class="btn btn-primary <?= is_disabled('update', TRUE) ?>"
                                type="submit"><?= lang('save_changes') ?></button>
                    </span>
						</div>
					</td>
					<td colspan="4">
						<div class="btn-group hidden-xs pull-right">
							<?php if (!empty($paginate['num_pages']) AND $paginate['num_pages'] > 1): ?>
								<button disabled
								        class="btn btn-default visible-lg"><?= $paginate['num_pages'] . ' ' . lang('total_pages') ?></button>
							<?php endif; ?>
							<button type="button" class="btn btn-primary dropdown-toggle"
							        data-toggle="dropdown"><?= i('fa fa-list') ?>
								<?= lang('select_rows_per_page') ?> <span class="caret"></span>
							</button>
							<?= $paginate['select_rows'] ?>
						</div>
					</td>
				</tr>
				</tfoot>
			</table>
		</div>
		<div class="container text-center"><?= $paginate['rows'] ?></div>
	</div>
	<?php form_close(); ?>
	<br/>
	<!-- Load JS for Page -->
	<script>
		$("#form").validate();
	</script>
<?php endif; ?>
<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'id="form"') ?>
	<div class="row">
		<div class="col-md-8">
			<div class="input-group text-capitalize">
				<?= generate_sub_headline('site_menus', 'fa-list', '') ?>
			</div>
		</div>
		<div class="col-md-4 text-right">
			<a href="<?= admin_url(CONTROLLER_CLASS . '/create/') ?>"
			   class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
					class="hidden-xs"><?= lang('add_menu') ?></span></a>
		</div>
	</div>
	<hr/>
<?php if (empty($rows)): ?>
	<?= tpl_no_values(CONTROLLER_CLASS . '/create/', 'add_menu') ?>
<?php else: ?>
	<div class="box-info mass-edit">
		<div>
			<table class="table table-striped table-hover">
				<thead class="text-capitalize">
				<tr>
					<th><?= tb_header('menu_name', 'menu_name') ?></th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($rows as $v): ?>
					<tr>
						<td>
							<input name="menu[<?= $v['menu_id'] ?>]" <?= is_disabled('update', TRUE) ?> type="text"
							       value="<?= $v['menu_name'] ?>" class="form-control required"/>
						</td>
						<td class="text-right">
							<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['menu_id']) ?>"
							   class="tip btn btn-default" data-toggle="tooltip" data-placement="bottom"
							   title="<?= lang('manage_menu_links') ?>"><?= i('fa fa-link') ?></a>
							<?php if ($v['menu_id'] > 3): ?>
								<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['menu_id']) ?>"
								   data-toggle="modal" data-target="#confirm-delete" href="#"
								   class="md-trigger btn btn-danger visible-lg <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
				<tfoot>
				<tr>
					<td colspan="2">
						<button class="btn btn-primary <?= is_disabled('update', TRUE) ?>"
						        type="submit"><?= lang('save_changes') ?></button>
					</td>
				</tr>
				</tfoot>
			</table>
		</div>
	</div>
	<?php form_close(); ?>
	<br/>
	<!-- Load JS for Page -->
	<script>
		$("#form").validate();
	</script>
<?php endif; ?>
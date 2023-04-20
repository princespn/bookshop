<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'role="form" id="form"') ?>
	<div class="row">
		<div class="col-md-6 col-lg-5">
			<div class="input-group text-capitalize">
				<?= generate_sub_headline('categories', 'fa-tags', $rows['total']) ?>
			</div>
		</div>
		<div class="col-md-6 col-lg-7 text-right">
			<?= next_page('left', $paginate); ?>
			<a href="<?= admin_url(CONTROLLER_CLASS . '/create/' . $parent_id) ?>"
			   class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
					class="hidden-xs"><?= lang('create_category') ?></span></a>
			<?= next_page('right', $paginate); ?>
		</div>
	</div>
	<hr/>
<?php if (empty($rows['values'])): ?>
	<?= tpl_no_values(CONTROLLER_CLASS . '/create/' . $parent_id, 'create_category') ?>
<?php else: ?>
	<div class="box-info mass-edit">
		<div class="<?= mobile_view('hidden-xs') ?>">
			<table class="table table-striped table-hover">
				<thead class="text-capitalize">
				<tr>
					<th class="text-center"><?= form_checkbox('', '', '', 'class="check-all"') ?></th>
					<th class="hidden-xs"></th>
					<th class="text-center hidden-xs"><?= tb_header('status', 'status') ?></th>
					<th class="text-center"><?= tb_header('category_name', 'category_name') ?></th>
					<th class="hidden-xs"><?= tb_header('category_path', 'path') ?></th>
					<th><?= tb_header('sort', 'sort_order') ?></th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($rows['values'] as $v): ?>
					<tr>
						<td style="width: 5%"
						    class="text-center"><?= form_checkbox('cat[' . $v['category_id'] . '][category_status]', 1) ?></td>
						<td style="width: 5%"
						    class="text-center hidden-xs"><?= photo(CONTROLLER_METHOD, $v, 'img-thumbnail dash-photo') ?></td>
						<td style="width: 5%" class="text-center hidden-xs"><a
								href="<?= admin_url('update_status/table/' . CONTROLLER_CLASS . '/type/category_status/key/category_id/id/' . $v['category_id']) ?>" <?= is_disabled('update', TRUE) ?>
								class="btn btn-default"><?= set_status($v['category_status']) ?></a></td>
						<td style="width: 13%" class="text-center"><h5><strong><?= $v['category_name'] ?></strong></h5>
						</td>
						<td class="hidden-xs"><?= get_cat_path($v) ?></td>
						<td style="width: 8%" class="text-center"><input
								type="number" <?= is_disabled('update', TRUE) ?> class="form-control digits required"
								name="cat[<?= $v['category_id'] ?>][sort_order]" value="<?= $v['sort_order'] ?>"
								tabindex="1"/></td>
						<td style="width: 12%" class="text-right">
							<?php if (!$disable_sql_category_count): ?>
								<a href="<?= admin_url('products/view?category_id=' . $v['category_id']) ?>"
								   class="btn btn-primary">
									<small style="font-size: 10px"><?= $v['total'] ?> <?= i('fa fa-tags') ?></small>
								</a>
							<?php endif; ?>
							<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['category_id']) ?>"
							   class="btn btn-default" title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
							<?php if ($v['category_id'] != 1): ?>
								<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['category_id']) ?>"
								   data-toggle="modal" data-target="#confirm-delete" href="#"
								   class="md-trigger btn btn-danger visible-lg <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
				<tfoot>
				<tr>
					<td colspan="5">
						<div class="input-group text-capitalize">
							<span class="input-group-addon"><small><?= lang('mark_checked_as') ?></small></span>
							<?= form_dropdown('change-status', options('active', 'deleted'), '', 'id="change-status" class="form-control"') ?>
							<span class="input-group-btn">
                        <button class="btn btn-primary <?= is_disabled('update', TRUE) ?>"
                                type="submit"><?= lang('save_changes') ?></button>
                    </span>
						</div>
					</td>
					<td colspan="2">
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
		<?php if (mobile_view()): ?>
			<div class="visible-xs">
				<?php foreach ($rows['values'] as $v): ?>
					<div class="box-info card">
						<h2>
							<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['category_id']) ?>">
								<?= $v['category_name'] ?></a>
						</h2>
						<div class="additional-btn">
							<a class="additional-icon" href="#" data-toggle="collapse"
							   data-target="#box-<?= $v['category_id'] ?>"><i class="fa fa-chevron-down"></i></a>
						</div>
						<div id="box-<?= $v['category_id'] ?>" class="collapse in">
							<?= photo(CONTROLLER_METHOD, $v, 'img-responsive', TRUE) ?>
							<hr/>
							<p><?= $v['description'] ?></p>
							<hr/>
							<div class="text-right">
								<a href="<?= admin_url('update_status/' . CONTROLLER_CLASS . '/' . $v['category_id'] . '/' . $v['category_status']) ?>"
								   class="btn btn-default"><?= set_status($v['category_status']) ?></a>
								<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['category_id']) ?>"
								   class="btn btn-default  <?= is_disabled('update', TRUE) ?>"
								   title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?> <?= lang('edit') ?></a>
								<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['category_id']) ?>"
								   data-toggle="modal" data-target="#confirm-delete" href="#"
								   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <?= lang('delete') ?></a>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<div class="container text-center"><?= $paginate['rows'] ?></div>
	</div>
	<?php form_close(); ?>
	<!-- Load JS for Page -->
	<script>
		$("#form").validate({
			ignore: "",
			submitHandler: function (form) {
				$.ajax({
					url: '<?=admin_url(CONTROLLER_CLASS . '/mass_update/')?>',
					type: 'POST',
					dataType: 'json',
					data: $('#form').serialize(),
					success: function (response) {
						if (response.type == 'success') {
							$('.alert-danger').remove();
							$('.form-control').removeClass('error');

							if (response.reload) {
								window.location.reload();
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
<?php endif; ?>
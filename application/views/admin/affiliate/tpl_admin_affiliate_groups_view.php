<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'role="form" id="form"') ?>
	<div class="row">
		<div class="col-md-8">
			<?= generate_sub_headline('affiliate_groups', 'fa-group', $rows[ 'total' ]) ?>
			<hr class="visible-xs"/>
		</div>
		<div class="col-md-4 text-right">
			<?= next_page('left', $paginate); ?>
			<a href="<?= admin_url(TBL_MEMBERS . '/view/?is_affiliate=1') ?>"
			   class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-search') ?> <span
					class="hidden-xs"><?= lang('view_affiliates') ?></span></a>

			<a href="<?= admin_url(CONTROLLER_CLASS . '/create') ?>"
			   class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
					class="hidden-xs"><?= lang('add_affiliate_group') ?></span></a>

			<?= next_page('right', $paginate); ?>
		</div>
	</div>
	<hr/>
<?php if (empty($rows[ 'values' ])): ?>
	<?= tpl_no_values(CONTROLLER_CLASS . '/create/', 'add_group') ?>
<?php else: ?>
	<div class="box-info mass-edit">
		<table class="table table-striped table-hover table-responsive">
			<thead class="text-capitalize">
			<tr>
				<th><?= tb_header('priority', 'tier') ?></th>
				<th class="text-center"><?= tb_header('name', 'aff_group_name') ?></th>
                <th class="hidden-xs">
	                <?php if (config_item('$sts_affiliate_commission_levels') > 1): ?>
	                <?= tb_header('tiers - ' . $sts_affiliate_commission_levels, 'commission_amounts', FALSE) ?>
                    <?php else: ?>
		                <?= tb_header('commission_amount', 'commission_amounts', FALSE) ?>
                    <?php endif; ?>
                </th>
				<th class="hidden-xs"><?= tb_header('type', 'type') ?></th>
				<th></th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ($rows[ 'values' ] as $v): ?>
				<tr>
					<td class="text-center">
						<?= form_dropdown('groups[' . $v[ 'group_id' ] . '][tier]', total_tiers($rows[ 'total' ]), $v[ 'tier' ], 'class="tier select2 form-control"') ?>
					</td>
                    <td class="text-center"><?=$v[ 'aff_group_name' ]?></td>
					<td class="visible-md visible-lg">
						<div style="width: 100%; overflow: auto;">
						<table>
							<tr>
								<?php for ($i = 1; $i <= $sts_affiliate_commission_levels; $i++): ?>
									<td>
										<?php if (config_item('sts_affiliate_commission_levels') > 1): ?>
										<small class="text-muted"> <?= lang('level') ?> <?= $i ?></small>
                                        <?php endif; ?>
										<?= form_input('groups[' . $v[ 'group_id' ] . '][commission_amounts][' . $i . ']', $v[ 'commission_level_' . $i ], 'placeholder="' . lang('level') . ' ' . $i . '" class="required  number form-control"') ?>
									</td>
								<?php endfor ?>
							</tr>
						</table>
						</div>
					</td>

					<td class="hidden-xs">
						<?= form_dropdown('groups[' . $v[ 'group_id' ] . '][commission_type]', options('flat_percent'), $v[ 'commission_type' ], 'class="required form-control"') ?>
					</td>
					<td class="text-right">
						<?php if (!$disable_sql_category_count): ?>
						<a href="<?= admin_url('affiliates/view/?table=affiliate&type_id=group_id&group_id=' . $v[ 'group_id' ]) ?>"
						   class="btn btn-primary hidden-xs" title="<?= lang('members') ?>">
							<small style="font-size: 10px"><?= $v[ 'total' ] ?> <?= i('fa fa-user') ?></small>
						</a>
						<?php endif; ?>
						<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v[ 'group_id' ]) ?>"
						   class="btn btn-default" title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
						<?php if ($v[ 'group_id' ] != $sts_affiliate_default_registration_group): ?>
							<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v[ 'group_id' ]) ?>/2/"
							   data-toggle="modal" data-target="#confirm-delete" href="#"
							   class="md-trigger btn btn-danger hidden-xs <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
						<?php endif ?>
					</td>
				</tr>
			<?php endforeach ?>
			</tbody>
			<tfoot>
			<tr>
				<td colspan="<?= $sts_affiliate_commission_levels + 5 ?>">
					<div class="btn-group hidden-xs pull-right">
						<?php if (!empty($paginate[ 'num_pages' ]) AND $paginate[ 'num_pages' ] > 1): ?>
							<button disabled
							        class="btn btn-default visible-lg"><?= $paginate[ 'num_pages' ] . ' ' . lang('total_pages') ?></button>
						<?php endif ?>
						<button type="button" class="btn btn-primary dropdown-toggle"
						        data-toggle="dropdown"><?= i('fa fa-list') ?>
							<?= lang('select_rows_per_page') ?> <span class="caret"></span>
						</button>
						<?= $paginate[ 'select_rows' ] ?>
					</div>
					<button class="btn btn-primary block-phone <?= is_disabled('update', TRUE) ?>"
					        type="submit"><?= i('fa fa-refresh') ?> <?= lang('update_groups') ?></button>
				</td>

			</tr>
			</tfoot>
		</table>

		<div class="container text-center"><?= $paginate[ 'rows' ] ?></div>

	</div>
	<?= form_hidden('group', 'affiliate') ?>
	<?= form_close() ?>
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
<?php endif ?>
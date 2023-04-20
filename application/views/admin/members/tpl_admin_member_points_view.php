<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update', array('id' => 'form'))) ?>
<div class="row">
	<div class="col-md-5">
		<?= generate_sub_headline('most_rewards_points', 'fa-users') ?>
		<hr class="visible-xs"/>
	</div>
	<div class="col-md-7 text-right">
		<?= next_page('left', $paginate); ?>
		<a href="<?= admin_url(CONTROLLER_CLASS . '/create') ?>"
		   class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
				class="hidden-xs"><?= lang('add_user') ?></span></a>
		<?= next_page('right', $paginate); ?>
	</div>
</div>
<hr/>
<?php if (empty($rows['values'])): ?>
	<?= tpl_no_values(TBL_MEMBERS . '/create/client', 'add_user') ?>
<?php else: ?>
	<div class="box-info valign">
		<div class="row hidden-xs text-center">
			<div class="col-sm-1 hidden-xs hidden-sm"></div>
			<div class="col-sm-1"></div>
			<div class="col-sm-1"><?= tb_header('status', 'status') ?></div>
			<div class="col-sm-2"><?= tb_header('name', 'fname') ?></div>
			<div class="col-sm-2"><?= tb_header('username', 'username') ?></div>
			<div class="col-sm-3 hidden-sm hidden-md"><?= tb_header('points', 'points') ?></div>
			<div class="col-sm-2"></div>
		</div>
		<hr class="hidden-xs"/>
		<?php foreach ($rows['values'] as $v): ?>
			<div class="hover">
				<div class="row text-center">
					<div class="col-sm-1 hidden-xs hidden-sm"><?= form_checkbox('id[]', $v['mid']) ?></div>
					<div class="r col-sm-1">
						<div>
							<?= photo(CONTROLLER_METHOD, $v, 'img-thumbnail img-circle dash-photo') ?>
							<?php if ($v['is_affiliate']): ?>
								<span
									class="label label-danger is_affiliate"><?= config_option('is_affiliate_icon') ?></span>
							<?php endif; ?>
						</div>
					</div>
					<div class="r col-sm-1"><?= set_status($v['status'], TRUE) ?></div>
					<div class="col-sm-2">
						<h5>
							<a href="<?= admin_url(TBL_MEMBERS . '/update/' . $v['mid']) ?>"><?= $v['fname'] . ' ' . $v['lname'] ?></a>
						</h5>
					</div>
					<div class="col-sm-2"><?= heading($v['username'], 5) ?></div>
					<div class="col-sm-3 hidden-sm hidden-md"><h5><strong><?= $v['points'] ?></strong></h5></div>
					<div class="col-sm-6 col-md-5 col-lg-2 text-right">
						<hr class="hidden-lg hidden-md hidden-sm"/>
						<?php if ($v['points'] > config_item('sts_rewards_point_conversion')): ?>
							<a data-href="<?= admin_url(TBL_REWARDS . '/redeem/' . $v['mid']) ?>"
							   data-toggle="modal" data-target="#confirm-redeem" href="#"
							   class="md-trigger btn btn-info <?= is_disabled('delete') ?> tip" data-toggle="tooltip" data-placement="bottom"
                            title="<?= lang('redeem') ?>"><?= i('fa fa-refresh') ?></a>
						<?php endif; ?>
						<a href="<?= admin_url(TBL_MEMBERS . '/update/' . $v['mid']) ?>" class="btn btn-default"
						   title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
						<a href="<?= admin_url('email_send/user/' . $v['mid']) ?>"
						   class="btn btn-default"><?= i('fa fa-envelope') ?></a>
						<a data-href="<?= admin_url(TBL_MEMBERS . '/delete/' . $v['mid']) ?>/2/"
						   data-toggle="modal" data-target="#confirm-delete" href="#"
						   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
					</div>
				</div>
				<hr/>
			</div>
		<?php endforeach; ?>
		<div class="row">
			<div class="col-sm-1 text-center hidden-xs hidden-sm">
				<?= form_checkbox('', '', '', 'class="check-all"') ?>
			</div>
			<div class="col-sm-4 col-lg-4">
				<div class="input-group hidden-xs hidden-sm">
					<span class="input-group-addon"><?= lang('mark_checked_as') ?> </span>
					<?= form_dropdown('change-status', options('members'), '', 'id="change-status" class="form-control"') ?>
					<span class="input-group-btn">
                <button class="btn btn-primary <?= is_disabled('update', TRUE) ?>"
                        type="submit"><?= lang('go') ?></button></span>
				</div>
			</div>
			<div class="col-sm-3 col-lg-2">
				<div class="mass-group hide" id="blog">
					<select id="blog_group_id" class="form-control select2" name="blog_group">
						<option value="" selected><?= lang('enter_blog_group') ?></option>
					</select>
				</div>
				<div class="mass-group hide" id="discount">
					<select id="discount_group_id" class="form-control select2" name="discount_group">
						<option value="" selected><?= lang('enter_discount_group') ?></option>
					</select>
				</div>
				<div class="mass-group hide" id="affiliate">
					<select id="affiliate_group_id" class="form-control select2" name="affiliate_group">
						<option value="" selected><?= lang('enter_affiliate_group') ?></option>
					</select>
				</div>
				<div class="mass-group hide" id="list">
					<select id="mailing_list_id" class="form-control select2" name="list_id">
						<option value="" selected><?= lang('enter_mailing_list') ?></option>
					</select>
				</div>
			</div>
			<div class="col-sm-7 col-md-6 col-lg-5 text-right">
				<div class="btn-group hidden-xs">
					<button type="button" class="btn btn-primary dropdown-toggle"
					        data-toggle="dropdown"><?= i('fa fa-list') ?>
						<?= lang('select_rows_per_page') ?> <span class="caret"></span>
					</button>
					<?= $paginate['select_rows'] ?>
				</div>
			</div>
		</div>
		<?php if (!empty($paginate['rows'])): ?>
			<div class="text-center"><?= $paginate['rows'] ?></div>
			<div class="text-center">
				<small class="text-muted"><?= $paginate['num_pages'] ?> <?= lang('total_pages') ?></small>
			</div>
		<?php endif; ?>
	</div>
	<?php form_close() ?>
	<div class="modal fade" id="confirm-redeem" tabindex="-1" role="dialog" aria-labelledby="modal-title"
	     aria-hidden="true">
		<div class="modal-dialog" id="modal-title">
			<div class="modal-content">
				<div class="modal-body capitalize">
					<h3><?= lang('reward_points_redemption') ?></h3>
					<?= lang('redeem_points_for_gift_certificate') ?>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('cancel') ?></button>
					<a href="#" class="btn btn-danger danger"><?= lang('yes') ?></a>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>
<script>
	$("#change-status").change(function () {
			var option = $(this).val();
			if (option == 'set_discount_group') {
				$('.mass-group').addClass('hide');
				$('#discount').removeClass('hide');
			}
			else if (option == 'set_blog_group') {
				$('.mass-group').addClass('hide');
				$('#blog').removeClass('hide');
			}
			else if (option == 'set_affiliate_group') {
				$('.mass-group').addClass('hide');
				$('#affiliate').removeClass('hide');
			}
			else if (option == 'add_mailing_list') {
				$('.mass-group').addClass('hide');
				$('#list').removeClass('hide');
			}
			else if (option == 'remove_mailing_list') {
				$('.mass-group').addClass('hide');
				$('#list').removeClass('hide');
			}
			else {
				$('.mass-group').addClass('hide');
			}
		}
	);

	$("#affiliate_group_id").select2({
		ajax: {
			url: '<?=admin_url(TBL_AFFILIATE_GROUPS . '/search/ajax/')?>',
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					aff_group_name: params.term, // search term
					page: params.page
				};
			},
			processResults: function (data, page) {
				return {
					results: $.map(data, function (item) {
						return {
							id: item.group_id,
							text: item.aff_group_name
						}
					})
				};
			},
			cache: true
		},
		minimumInputLength: 2
	});
	$("#discount_group_id").select2({
		ajax: {
			url: '<?=admin_url(TBL_DISCOUNT_GROUPS . '/search/ajax/')?>',
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					disc_group_name: params.term, // search term
					page: params.page
				};
			},
			processResults: function (data, page) {
				return {
					results: $.map(data, function (item) {
						return {
							id: item.group_id,
							text: item.group_name
						}
					})
				};
			},
			cache: true
		},
		minimumInputLength: 2
	});
	$("#blog_group_id").select2({
		ajax: {
			url: '<?=admin_url(TBL_BLOG_GROUPS . '/search/ajax/')?>',
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					blog_group_name: params.term, // search term
					page: params.page
				};
			},
			processResults: function (data, page) {
				return {
					results: $.map(data, function (item) {
						return {
							id: item.group_id,
							text: item.group_name
						}
					})
				};
			},
			cache: true
		},
		minimumInputLength: 2
	});
	$("#mailing_list_id").select2({
		ajax: {
			url: '<?=admin_url(TBL_EMAIL_MAILING_LISTS . '/search/ajax/')?>',
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					list_name: params.term, // search term
					page: params.page
				};
			},
			processResults: function (data, page) {
				return {
					results: $.map(data, function (item) {
						return {
							id: item.list_id,
							text: item.list_name
						}
					})
				};
			},
			cache: true
		},
		minimumInputLength: 2
	});

	$('#confirm-redeem').on('show.bs.modal', function(e) {
		$(this).find('.danger').attr('href', $(e.relatedTarget).data('href'));
	});
</script>

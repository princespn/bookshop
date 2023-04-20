<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'id="form"') ?>
<div class="row">
	<div class="col-md-8">
		<div class="input-group text-capitalize">
			<?= generate_sub_headline('mailing_lists', 'fa-envelope', $rows['total']) ?>
		</div>
	</div>
	<div class="col-md-4 text-right">
		<?= next_page('left', $paginate); ?>
		<a href="<?= admin_url(CONTROLLER_CLASS . '/create/') ?>"
		   class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
				class="hidden-xs"><?= lang('create_mailing_list') ?></span></a>
		<?= next_page('right', $paginate); ?>
	</div>
</div>
<hr/>
<div class="box-info">
	<ul class="nav nav-tabs text-capitalize" role="tablist">
		<li class="active">
			<a href="#lists" aria-controls="lists" role="tab"
			   data-toggle="tab"><?= lang('internal_mailing_lists') ?></a></li>
		<?php if (!empty($modules)): ?>
			<li><a href="#modules" aria-controls="modules" role="tab"
			       data-toggle="tab"><?= lang('third_party_modules') ?></a></li>
		<?php endif; ?>
	</ul>
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="lists">
			<hr/>
			<div class="row">
				<div class="col-md-9"><?= tb_header('list_name', 'list_name') ?></div>
                <?php if (check_section('email_follow_ups')): ?>
				<div class="col-md-1 hidden-xs"><?= tb_header('follow_ups', 'follow_ups') ?></div>
                <?php endif; ?>
				<div class="col-md-<?php if (check_section('email_follow_ups')): ?>2<?php else:?>3<?php endif; ?>"></div>
			</div>
			<hr/>
			<?php if (!empty($rows['values'])): ?>
				<?php foreach ($rows['values'] as $v): ?>
					<div class="row">
						<div class="col-md-9">
							<h5>
								<a href="<?= admin_url(CONTROLLER_CLASS . '/view_subscribers?p-list_id=' . $v['list_id']) ?>"><?= humanize($v['list_name']) ?></a>
							</h5>
							<small><?= $v['description'] ?></small>
						</div>
						<?php if (check_section('email_follow_ups')): ?>
						<div class="col-md-1 hidden-xs text-center">

							<a href="<?= admin_url(TBL_EMAIL_FOLLOW_UPS . '/view/?list_id=' . $v['list_id']) ?>"
							   class="tip btn btn-default" data-toggle="tooltip" data-placement="bottom"
							   title="<?= lang('view_follow_up_messages') ?>"><?= $v['follow_ups'] ?> <?= i('fa fa-envelope') ?></a>

						</div>
						<?php endif; ?>
						<div class="col-md-<?php if (check_section('email_follow_ups')): ?>2<?php else:?>3<?php endif; ?> text-right">
							<?php if (!$disable_sql_category_count): ?>
								<a href="<?= admin_url(CONTROLLER_CLASS . '/view_subscribers?p-list_id=' . $v['list_id']) ?>"
								   class="tip btn btn-primary" data-toggle="tooltip" data-placement="bottom"
								   title="<?= $v['total'] ?> <?= lang('members') ?>">
									<small style="font-size: 10px"><?= $v['total'] ?> <?= i('fa fa-group') ?></small>
								</a>
							<?php endif; ?>
							<a href="<?= admin_url('email_send/mailing_list/' . $v['list_id']) ?>"
							   class="btn btn-info"
							   title="<?= lang('mass_email') ?>"><i class="fa fa-envelope"></i></a>
							<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['list_id']) ?>"
							   class="btn btn-default"
							   title="<?= lang('edit') ?>"><i class="fa fa-pencil"></i></a>
							<?php if ($v['list_id'] > 3): ?>
                                <?php if (!empty($sts_members_default_mailing_list)): ?>
                                    <?php if ($v['list_id'] != $sts_members_default_mailing_list): ?>
                                        <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['list_id']) ?>"
                                           data-toggle="modal" data-target="#confirm-delete" href="#"
                                           class="md-trigger btn btn-danger <?= is_disabled('delete') ?> "><?= i('fa fa-trash-o') ?></a>
                                    <?php endif; ?>
                                <?php endif; ?>
							<?php endif; ?>
						</div>
					</div>
					<hr/>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
		<?php if (!empty($modules)): ?>
			<div role="tabpanel" class="tab-pane" id="modules">
				<hr/>
				<div class="row">
					<div class="col-md-9"><?= tb_header('module_name', 'module_name') ?></div>
					<div class="col-md-3 text-right">
						<a href="<?=admin_url(TBL_MODULES . '/view?module_type=mailing_lists')?>" class="btn btn-primary">
							<?=i('fa fa-search')?> <span class="hidden-xs"><?=lang('view_modules')?></span></a>
					</div>
				</div>
				<hr/>
				<?php foreach ($modules as $v): ?>

						<div class="row text-capitalize">
							<div class="col-sm-8 r">
								<h5>
									<a href="<?= admin_url( 'email_mailing_list_modules/update/' . $v['module_id']) ?>">
										<?= $v['module_name'] ?></a>
								</h5>
								<small><?= check_desc($v['module_description']) ?></small>
							</div>
							<div class="col-sm-2 r">
								<a href="<?= admin_url(TBL_MODULES . '/external/' . $v['module_type'] . '/' . $v['module_folder']) ?>"
								   target="_blank">
									<?php if (file_exists(PUBPATH . '/images/modules/module_mailing_lists_' . $v['module_folder'] . '.png')): ?>
										<img
											src="<?= base_url('/images/modules/module_mailing_lists_' . $v['module_folder'] . '.png') ?>"
											alt="preview" class=" img-responsive"/>
									<?php endif; ?>
								</a>
							</div>
							<div class="col-sm-2 r text-right">
								<a href="<?= admin_url('update_status/table/' . TBL_MODULES . '/type/module_status/key/module_id/id/' . $v['module_id']) ?>"
								   class="btn btn-default <?= is_disabled('update', TRUE) ?> "><?= set_status($v['module_status']) ?></a>
								<a href="<?= admin_url('email_mailing_list_modules/update/' . $v['module_id']) ?>"
								   class="btn btn-default block-phone" title="<?= lang('edit') ?>"><i
										class="fa fa-pencil"></i>
									<span class="visible-xs"><?= lang('edit') ?></span> </a>
							</div>
						</div>
					<hr/>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</div>
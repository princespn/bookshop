<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
	<div class="col-md-8">
		<div class="input-group text-capitalize">
			<?= generate_sub_headline('email_templates', 'fa-envelope', $rows['total']) ?>
		</div>
	</div>
	<div class="col-md-4 text-right">
		<?= next_page('left', $paginate); ?>
		<a href="<?= admin_url(CONTROLLER_CLASS . '/create/') ?>"
		   class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
				class="hidden-xs"><?= lang('create_email_template') ?></span></a>
		<?= next_page('right', $paginate); ?>
	</div>
</div>
<hr/>
<div class="box-info">
	<?php if (!empty($rows['values'])): ?>
		<div role="tabpanel">
			<ul class="nav nav-tabs text-capitalize" role="tablist">
				<li class="active"><a href="#admin" aria-controls="home" role="tab"
				                      data-toggle="tab"><?= lang('admin_templates') ?></a></li>
				<li><a href="#member" aria-controls="profile" role="tab"
				       data-toggle="tab"><?= lang('member_templates') ?></a></li>
				<?php if (config_enabled('affiliate_marketing')): ?>
					<li><a href="#affiliate" aria-controls="profile" role="tab"
					       data-toggle="tab"><?= lang('affiliate_templates') ?></a></li>
				<?php endif; ?>
				<li><a href="#custom" aria-controls="messages" role="tab"
				       data-toggle="tab"><?= lang('custom_templates') ?></a></li>
				<li><a href="#header" aria-controls="messages" role="tab"
				       data-toggle="tab"><?= lang('template_header') ?></a></li>
			</ul>
			<div class="tab-content">
				<div role="tabpanel" class="tab-pane active" id="admin">
					<hr/>
					<?php foreach ($rows['values'] as $v): ?>
						<?php if ($v['email_type'] == 'admin'): ?>
							<div class="row text-capitalize">
								<div class="col-sm-10">
									<h5>
										<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['template_id']) ?>"><?= humanize($v['template_name']) ?></a>
									</h5>
									<small><?= $v['description'] ?></small>
								</div>
								<div class="col-sm-2 text-right">
									<a href="<?= admin_url('update_status/table/' . CONTROLLER_CLASS . '/type/status/key/template_id/id/' . $v['template_id']) ?>"
									   class="btn btn-default <?= is_disabled('update', TRUE) ?>">
										<?= set_status($v['status']) ?></a>
									<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['template_id']) ?>"
									   class="btn btn-default block-phone" title="<?= lang('edit') ?>"><i
											class="fa fa-pencil"></i> <span
											class="visible-xs"><?= lang('edit') ?></span> </a>
								</div>
							</div>
							<hr/>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
				<div role="tabpanel" class="tab-pane" id="member">
					<hr/>
					<?php foreach ($rows['values'] as $v): ?>
						<?php if ($v['email_type'] == 'member'): ?>
							<div class="row text-capitalize">
								<div class="col-sm-10">
									<h5>
										<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['template_id']) ?>"><?= humanize($v['template_name']) ?></a>
									</h5>
									<small><?= $v['description'] ?></small>
								</div>
								<div class="col-sm-2 text-right">
									<a href="<?= admin_url('update_status/table/' . CONTROLLER_CLASS . '/type/status/key/template_id/id/' . $v['template_id']) ?>"
									   class="btn btn-default <?= is_disabled('update', TRUE) ?>">
										<?= set_status($v['status']) ?></a>
									<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['template_id']) ?>"
									   class="btn btn-default block-phone" title="<?= lang('edit') ?>"><i
											class="fa fa-pencil"></i> <span
											class="visible-xs"><?= lang('edit') ?></span> </a>
								</div>
							</div>
							<hr/>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
				<?php if (config_enabled('affiliate_marketing')): ?>
					<div role="tabpanel" class="tab-pane" id="affiliate">
						<hr/>
						<?php foreach ($rows['values'] as $v): ?>
							<?php if ($v['email_type'] == 'affiliate'): ?>
								<div class="row text-capitalize">
									<div class="col-sm-10">
										<h5>
											<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['template_id']) ?>"><?= humanize($v['template_name']) ?></a>
										</h5>
										<small><?= $v['description'] ?></small>
									</div>
									<div class="col-sm-2 text-right">
										<a href="<?= admin_url('update_status/table/' . CONTROLLER_CLASS . '/type/status/key/template_id/id/' . $v['template_id']) ?>"
										   class="btn btn-default <?= is_disabled('update', TRUE) ?>">
											<?= set_status($v['status']) ?></a>
										<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['template_id']) ?>"
										   class="btn btn-default block-phone" title="<?= lang('edit') ?>"><i
												class="fa fa-pencil"></i> <span
												class="visible-xs"><?= lang('edit') ?></span> </a>
									</div>
								</div>
								<hr/>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<div role="tabpanel" class="tab-pane" id="custom">
					<hr/>
					<?php foreach ($rows['values'] as $v): ?>
						<?php if ($v['email_type'] == 'custom'): ?>
							<div class="row text-capitalize">
								<div class="col-sm-10">
									<h5>
										<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['template_id']) ?>"><?= humanize($v['template_name']) ?></a>
									</h5>
									<small><?= $v['description'] ?></small>
								</div>
								<div class="col-sm-2 text-right">
									<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['template_id']) ?>"
									   class="btn btn-default block-phone" title="<?= lang('edit') ?>"><i
											class="fa fa-pencil"></i> <span
											class="visible-xs"><?= lang('edit') ?></span></a>
									<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['template_id']) ?>"
									   data-toggle="modal" data-target="#confirm-delete" href="#"
									   class="md-trigger btn btn-danger visible-lg <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
								</div>
							</div>
							<hr/>
						<?php endif; ?>
					<?php endforeach; ?>

				</div>
				<div role="tabpanel" class="tab-pane" id="header">
					<?= form_open(admin_url(CONTROLLER_CLASS . '/update_header'), 'id="form" class="form-horizontal"') ?>
					<hr/>
					<div class="form-group">
						<?= lang('email_header', 'layout_design_email_template_header', 'class="col-md-3 control-label"') ?>
						<div class="col-md-6">
							<?= form_textarea('layout_design_email_template_header', set_value('layout_design_email_template_header', $layout_design_email_template_header, FALSE), 'class="' . css_error('text_body') . ' form-control"') ?>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<?= lang('email_footer', 'layout_design_email_template_footer', 'class="col-md-3 control-label"') ?>
						<div class="col-md-6">
							<?= form_textarea('layout_design_email_template_footer', set_value('layout_design_email_template_header', $layout_design_email_template_footer, FALSE), 'class="' . css_error('text_body') . ' form-control"') ?>
						</div>
					</div>
					<hr/>
					<div class="row">
						<div class="col-md-9 col-md-offset-3">
						<button class="btn btn-info navbar-btn block-phone"
						        id="update-button" <?= is_disabled('update', TRUE) ?>
						        type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>

						</div>
					</div>
					<?= form_close() ?>
				</div>
			</div>

		</div>
	<?php endif; ?>
</div>
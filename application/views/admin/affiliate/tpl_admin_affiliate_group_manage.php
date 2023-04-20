<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'role="form" id="prod_form" class="form-horizontal"') ?>
    <div class="row">
        <div class="col-md-4">
			<?= generate_sub_headline(CONTROLLER_CLASS, 'fa-group', '') ?>
        </div>
        <div class="col-md-8 text-right">
			<?php if (CONTROLLER_FUNCTION == 'update'): ?>
				<?= prev_next_item('left', CONTROLLER_METHOD, $row['prev']) ?>
				<?php if ($id != $sts_affiliate_default_registration_group): ?>
                    <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $row['group_id']) ?>"
                       data-toggle="modal" data-target="#confirm-delete" href="#"
                       class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
                                class="hidden-xs"><?= lang('delete') ?></span></a>
				<?php endif; ?>
			<?php endif; ?>

            <a href="<?= admin_url(CONTROLLER_CLASS . '/view') ?>" class="btn btn-primary"><?= i('fa fa-search') ?>
                <span class="hidden-xs"><?= lang('view_affiliate_groups') ?></span></a>
			<?php if (CONTROLLER_FUNCTION == 'update'): ?>
				<?= prev_next_item('right', CONTROLLER_METHOD, $row['next']) ?>
			<?php endif; ?>
        </div>
    </div>
    <hr/>
    <div class="row">
        <div class="col-md-12">
            <div class="box-info">
                <h3 class="header"><?= lang('manage_group_details') ?></h3>
                <span class="text-muted">
				<?= lang('manage_group_details_description') ?>
				<?= lang('manage_affiliate_group_description') ?>
			</span>
                <hr/>
                <div class="form-group">
					<?= lang('aff_group_name', 'aff_group_name', 'class="col-md-3 control-label"') ?>
                    <div class="col-lg-5">
						<?= form_input('aff_group_name', set_value('aff_group_name', $row['aff_group_name']), 'class="' . css_error('aff_group_name') . ' form-control required"') ?>
                    </div>
                </div>
                <hr/>
                <div class="form-group">
					<?= lang('aff_group_description', 'aff_group_description', 'class="col-md-3 control-label"') ?>

                    <div class="col-lg-5">
						<?= form_textarea('aff_group_description', set_value('aff_group_description', $row['aff_group_description']), 'class="' . css_error('aff_group_description') . ' form-control required"') ?>
                    </div>
                </div>
                <hr/>
                <div class="form-group">
					<?= lang('commission_type', 'commission_type', 'class="col-md-3 control-label"') ?>

                    <div class="col-lg-5">
						<?= form_dropdown('commission_type', options('flat_percent'), $row['commission_type'], 'class="form-control"') ?>
                    </div>
                </div>
                <hr/>

				<?php if (config_item('sts_affiliate_commission_levels') > 1): ?>
                    <div class="form-group">
                        <div class="col-md-offset-3 col-md-5">
							<?= lang('commission_per_level_tiers', 'commission_per_levels', 'class="control-label"') ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-offset-3 col-lg-5">
                            <div class="row">
								<?php for ($i = 1; $i <= config_item('sts_affiliate_commission_levels'); $i++): ?>
                                    <div class="col-lg-2 col-md-4 text-center">
										<?= lang('level_' . $i, 'commission_per_level_' . $i, 'class="control-label"') ?>
										<?= form_input('commission_level_' . $i, set_value('commission_level_' . $i, $row['commission_level_' . $i]), 'class="form-control required number"') ?>
                                    </div>
								<?php endfor; ?>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="form-group">
						<?= lang('commission_amount', 'commission_amount', 'class="col-md-3 control-label"') ?>
                        <div class="col-lg-5">
	                        <?= form_input('commission_level_1', set_value('commission_level_1', $row['commission_level_1']), 'class="form-control required number"') ?>
                        </div>
                    </div>
				<?php endif; ?>
				<?php if (defined('AFFILIATE_MARKETING_CHARGE_FEES')): ?>
                    <hr/>
                    <div class="form-group">
						<?= lang('enable_fees', 'enable_fees', 'class="col-md-3 control-label"') ?>

                        <div class="col-lg-5">
							<?= form_dropdown('enable_fees', options('yes_no'), $row['enable_fees'], 'class="form-control"') ?>
                        </div>
                    </div>
                    <hr/>
                    <div class="form-group">
						<?= lang('fee_amount', 'fee_amount', 'class="col-md-3 control-label"') ?>
                        <div class="col-lg-5">
							<?= form_input('fee_amount', set_value('fee_amount', $row['fee_amount']), 'class="' . css_error('fee_amount') . ' form-control"') ?>
                        </div>
                    </div>
                    <hr/>
                    <div class="form-group">
						<?= lang('fee_type', 'fee_type', 'class="col-md-3 control-label"') ?>

                        <div class="col-lg-5">
							<?= form_dropdown('fee_type', options('flat_percent'), $row['fee_type'], 'class="form-control"') ?>
                        </div>
                    </div>

				<?php endif; ?>
            </div>
        </div>
    </div>
<?php if (CONTROLLER_FUNCTION == 'update'): ?>
	<?= form_hidden('group_id', $id) ?>
<?php endif; ?>
    <nav class="navbar navbar-fixed-bottom save-changes">
        <div class="container text-right">
            <div class="row">
                <div class="col-lg-12">
					<?php if (CONTROLLER_FUNCTION == 'create'): ?>
                        <button name="redir_button" value="1"
                                class="btn btn-success navbar-btn block-phone <?= is_disabled('update', TRUE) ?>"
                                id="update-button"
                                type="submit"><?= i('fa fa-plus') ?> <?= lang('save_add_another') ?></button>
					<?php endif; ?>
                    <button class="btn btn-info navbar-btn block-phone"
                            id="update-button" <?= is_disabled('update', TRUE) ?>
                            type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
                </div>
            </div>
        </div>
    </nav>
<?= form_close() ?>
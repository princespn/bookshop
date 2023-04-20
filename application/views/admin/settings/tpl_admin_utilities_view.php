<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
<div class="row">
    <div class="col-md-4">
		<?= generate_sub_headline('utilities', 'fa fa-cogs', '', FALSE) ?>
    </div>
    <div class="col-md-8 text-right">
        <a href="<?= admin_url('settings') ?>" class="btn btn-primary"><?= i('fa fa-cog') ?>
            <span class="hidden-xs"><?= lang('settings') ?></span></a>
    </div>
</div>
<hr/>
<div class="row">
    <div class="col-md-9">
        <div class="box-info" id="settings-div">
            <h3><?= lang('system_utilities') ?></h3>
            <span><?= lang('system_utilities_description') ?></span>
            <hr/>
            <div class="form-group">
                <div class="col-md-5 text-right">
                    <strong class="control-label text-right">
						<?= lang('system_backups') ?></strong>
                    <br/>
                    <small><?= lang('system_backups_description') ?></small>
                </div>
                <div class="col-md-7">
                    <a href="<?= admin_url('backup/view') ?>" class="btn btn-primary">
                        <?=i('fa fa-database')?> <?=lang('manage_system_backups')?>
                    </a>
                </div>
            </div>
            <hr/>
            <div class="form-group">
                <div class="col-md-5 text-right">
                    <strong class="control-label text-right">
				        <?= lang('system_logs') ?></strong>
                    <br/>
                    <small><?= lang('system_logs_description') ?></small>
                </div>
                <div class="col-md-7">
                    <a href="<?= admin_url('logs/view') ?>" class="btn btn-primary">
				        <?=i('fa fa-cog')?> <?=lang('view_system_logs')?>
                    </a>
                </div>
            </div>
            <hr/>
            <div class="form-group">
                <div class="col-md-5 text-right">
                    <strong class="control-label text-right">
				        <?= lang('check_updates') ?></strong>
                    <br/>
                    <small><?= lang('check_updates_description') ?></small>
                </div>
                <div class="col-md-7">
                    <a href="<?= admin_url('updates/check') ?>" class="btn btn-primary">
				        <?=i('fa fa-download')?> <?=lang('check_for_updates')?>
                    </a>
                </div>
            </div>
            <hr/>
            <?php if (ENVIRONMENT == 'development'): ?>
                <h3><?= lang('advanced_utilities') ?></h3>
                <span><?= lang('advanced_utilities_description') ?></span>
                <hr/>
                <p class="alert alert-danger">
		            <?=i('fa fa-info-circle')?> <?=lang('advanced_utilities_warning')?>
                </p>
                <div class="form-group">
                    <div class="col-md-5 text-right">
                        <strong class="control-label text-right">
				            <?= lang('change_decimal_places') ?></strong>
                        <br/>
                        <small><?= lang('change_decimal_places_description') ?></small>
                    </div>
                    <div class="col-md-7">
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
			                    <?=lang('change_decimal_points')?> <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <?php for ($i=2;$i<=10;$i++):?>
                                <li><a href="<?=admin_url('utilities/amount_point/' . $i)?>"><?=round(0.1111111111, $i)?></a></li>
                               <?php endfor; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <hr/>
            <?php endif; ?>
        </div>
    </div>
	<?= settings_sidebar() ?>
</div>
<?= form_close() ?>

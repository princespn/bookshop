<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
    <div class="row">
        <div class="col-md-8">
			<?= generate_sub_headline('clicks', 'fa-file-text-o', $rows['total']) ?>
            <hr class="visible-xs"/>
        </div>
        <div class="col-md-4 text-right">
			<?= next_page('left', $paginate); ?>
            <?php if (config_enabled('sts_affiliate_enable_traffic_blocks')):?>
            <a href="<?= admin_url(CONTROLLER_CLASS . '/block_traffic') ?>"
               class="btn btn-danger"><?= i('fa fa-ban') ?>
                <span class="hidden-xs"><?= lang('block_traffic') ?></span></a>
            <?php endif; ?>
            <a href="<?= admin_url(TBL_MEMBERS . '/view?is_affiliate=1') ?>"
               class="btn btn-primary"><?= i('fa fa-search') ?>
                <span class="hidden-xs"><?= lang('view_affiliates') ?></span></a>
			<?= next_page('right', $paginate); ?>
        </div>
    </div>
    <hr/>
<?php if (empty($rows['values'])): ?>
	<?= tpl_no_values(TBL_TRACKING . '/create/') ?>
<?php else: ?>
    <div class="box-info">
		<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'role="form" id="form"') ?>
        <div class="row text-capitalize hidden-xs hidden-sm">
            <div class="col-md-1 text-center"><?= tb_header('date', 'date') ?></div>
            <div class="col-md-6"><?= tb_header('referrer', 'referrer') ?></div>
            <div class="col-md-1 text-center"><?= tb_header('ip_address', 'ip_address') ?></div>
            <div class="col-md-1 text-center"><?= tb_header('affiliate', 'username') ?></div>
            <div class="col-md-1 text-center"><?= tb_header('os', 'os') ?></div>
            <div class="col-md-1 text-center"><?= tb_header('browser', 'browser') ?></div>
            <div class="col-md-1"></div>
        </div>
        <hr/>
		<?php foreach ($rows['values'] as $v): ?>
            <div class="row">
                <div class="col-md-1  text-center hidden-xs hidden-sm">
                    <small class="text-muted"><?= display_date($v['date']) ?><br/>
	                    <?= display_time($v['date']) ?></small>
                </div>
                <div class="col-md-6">
					<span>
						<?php if ($v['referrer'] == lang('unknown_referrer')): ?>
							<?= $v['referrer'] ?>
						<?php else: ?>
							<?= anchor($v['referrer'], $v['referrer'], 'target=_blank') ?>
						<?php endif; ?>
					</span>
					<?php if (!empty($v['tool_type'])): ?>
                        <br /><small class="text-muted"><?=lang('via')?> <?=lang($v['tool_type'])?></small>
					<?php endif; ?>
                </div>
                <div class="col-md-1  text-center">
                    <span class="text-muted">
                        <a href="<?=EXTERNAL_IP_LOOKUP?><?=$v['ip_address']?>" target="_blank">
                        <?= $v['ip_address'] ?>
                        </a>
                    </span>
                </div>
                <div class="col-md-1  text-center">
                    <a href="<?= admin_url(TBL_MEMBERS . '/update/' . $v['member_id']) ?>">
                        <span class="badge"><?= $v['username'] ?></span></a>
                </div>
                <div class="col-md-1  text-center">
                    <span class="text-muted"><?= $v['os'] ?></span>
                </div>
                <div class="col-md-1  text-center">
                    <span class="text-muted">
					<?php if (empty($v['browser'])): ?>
						<?= lang('unknown_browser') ?>
					<?php else: ?>
                        <i class="fa fa-<?= url_title(strtolower($v['browser'])) ?>"></i> <?= $v['browser'] ?>
					<?php endif; ?>
                        </span>
                </div>
                <div class="col-md-1 text-right">
                    <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['traffic_id']) ?>"
                       data-toggle="modal" data-target="#confirm-delete" href="#"
                       class="md-trigger btn btn-danger block-phone <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
                </div>
            </div>
            <hr/>
		<?php endforeach; ?>
        <div class="row">
            <div class="col-md-6"></div>
            <div class="col-md-6 text-right">
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
		<?= form_close() ?>
    </div>
<?php endif; ?>
<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
    <div class="row">
        <div class="col-md-8">
            <div class="input-group text-capitalize">
				<?= generate_sub_headline('transaction_log', 'fa-list') ?>
            </div>
        </div>
        <div class="col-md-4 text-right">
            <a data-href="<?= admin_url(CONTROLLER_CLASS . '/reset/') ?>" data-toggle="modal"
               data-target="#confirm-delete" href="#"
               class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
                        class="hidden-xs"><?= lang('prune_log') ?></span></a>
            <a href="<?= admin_url('settings') ?>" class="btn btn-primary"><?= i('fa fa-cog') ?>
                <span class="hidden-xs"><?= lang('settings') ?></span></a>
        </div>
    </div>
    <hr/>
<?php if (empty($rows['values'])): ?>
	<?= tpl_no_values() ?>
<?php else: ?>
    <div class="box-info mass-edit">
        <div>
            <table class="table table-striped table-hover table-responsive">
                <thead class="text-capitalize">
                <tr>
                    <th style="width: 15%" class="text-center"><?= tb_header('date', 'date') ?></th>
                    <th style="width: 10%" class="text-center"><?= tb_header('user', 'user') ?></th>
                    <th style="width: 10%" class="text-center"><?= tb_header('ip_address', 'ip') ?></th>
                    <th style="width: 8%" class="text-center"><?= tb_header('level', 'level') ?></th>
                    <th style="width: 15%" class="text-center"><?= tb_header('method', 'method') ?></th>
                    <th><?= tb_header('message', 'message') ?></th>
                </tr>
                </thead>
                <tbody>
				<?php foreach ($rows['values'] as $v): ?>
                    <tr>
                        <td class="text-center"><?= local_date($v['date']) ?></td>
                        <td class="text-center"><?= $v['user'] ?></td>
                        <td class="text-center">
                            <a href="<?=EXTERNAL_IP_LOOKUP?><?= $v['ip'] ?>" target="_blank"><?=$v['ip']?></a></td>
                        <td class="text-center">
							<span class="label label-<?= $v['level'] ?>">
							<?= $v['level'] ?>
							</span>
                        </td>
                        <td class="text-center">
                            <small><?= $v['method'] ?></small>
                        </td>
                        <td>
							<?php if (!empty($v['vars'])): ?>
                                <a href="#vars-<?= $v['id'] ?>" data-toggle="collapse" role="button">
									<?= $v['message'] ?>
                                </a>
							<?php else: ?>
								<?= $v['message'] ?>
							<?php endif; ?>
							<?php if (ENVIRONMENT == 'development'): ?>
								<?php if (!empty($v['vars'])): ?>
                                    <div class="collapse" id="vars-<?= $v['id'] ?>">
								<textarea class="form-control" rows="10"
                                          readonly><?php if (!is_array($v['vars'])):?><?= print_r(unserialize($v['vars'])) ?><?php else: ?><?=$v['vars']?><?php endif; ?></textarea>
                                    </div>
								<?php endif; ?>
							<?php endif; ?>
                        </td>
                    </tr>
				<?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr class=" hidden-xs">
                    <td colspan="6">
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
		<?php if (!empty($paginate['rows'])): ?>
            <div class="text-center"><?= $paginate['rows'] ?></div>
            <div class="text-center">
                <small class="text-muted"><?= $paginate['num_pages'] ?> <?= lang('total_pages') ?></small>
            </div>
		<?php endif; ?>
    </div>
<?php endif; ?>
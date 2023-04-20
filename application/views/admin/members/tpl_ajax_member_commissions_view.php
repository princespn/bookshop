<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (empty($rows)): ?>
    <hr />
    <?= tpl_no_values('', '', 'no_commissions_found', 'warning', FALSE) ?>
<?php else: ?>
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th class="text-center"><h5 class="table-header"><?=lang('status')?></h5></th>
            <th class="text-center"><h5 class="table-header"><?=lang('date')?></h5></th>
            <th><h5 class="table-header"><?=lang('transaction_id')?></h5></th>
            <th class="text-center"><h5 class="table-header"><?=lang('commission')?></h5></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $v): ?>
            <tr>
                <td style="width: 5%" class="hidden-xs text-center">
                    <?php if ($v[ 'comm_status' ] == 'pending'): ?>
                        <span class="label label-danger"> <?= lang('pending') ?></span>
                    <?php elseif ($v[ 'comm_status' ] == 'unpaid') : ?>
                        <span class="label label-unpaid"><?= lang('unpaid') ?></span>
                        <?php
                    else : ?>
                        <span class="label label-success"><?= lang('paid') ?></span>
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <?= display_date($v[ 'date' ]) ?>
                </td>
                <td>
                    <a href="<?= admin_url(TBL_AFFILIATE_COMMISSIONS . '/update/' . $v[ 'comm_id' ]) ?>">
                        <?=$v['trans_id']?>
                    </a>
                </td>
                <td class="text-center"><?= format_amount($v[ 'commission_amount' ]) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
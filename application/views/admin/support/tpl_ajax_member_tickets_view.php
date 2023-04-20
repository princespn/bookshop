<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (empty($rows)): ?>
    <hr /> 
    <?= tpl_no_values('', '', 'no_tickets_found', 'warning', FALSE) ?>
<?php else: ?>
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th class="text-center"><h5 class="table-header"><?=lang('status')?></h5></th>
            <th class="text-center"><h5 class="table-header"><?=lang('date')?></h5></th>
            <th><h5 class="table-header"><?=lang('subject')?></h5></th>
            <th class="text-center"><h5 class="table-header"><?=lang('priority')?></h5></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $v): ?>
            <tr>
                <td style="width: 5%" class="text-center">
                    <span class="label label-default label-<?= $v['ticket_status'] ?>">
                        <?= lang($v['ticket_status']) ?></span>
                </td>
                <td class="text-center"><?= display_date($v[ 'date_added' ]) ?></td>
                <td><a href="<?=admin_url(TBL_SUPPORT_TICKETS . '/update/' . $id)?>"><?=$v['ticket_subject']?></a></td>
                <td class="text-center"><span class="label label-default label-<?= $v['priority'] ?>">
                        <?= lang($v['priority']) ?></span>
                </td>

            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
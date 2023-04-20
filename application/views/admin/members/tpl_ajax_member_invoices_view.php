<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (empty($invoices['rows'])): ?>
    <hr />
    <?= tpl_no_values('', '', 'no_invoices_found', 'warning', FALSE) ?>
<?php else: ?>
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th class="text-center"><h5 class="table-header"><?=lang('status')?></h5></th>
            <th class="text-center"><h5 class="table-header"><?=lang('date')?></h5></th>
            <th class="text-center"><h5 class="table-header"><?=lang('invoice')?></h5></th>
            <th class="text-center"><h5 class="table-header"><?=lang('amount')?></h5></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($invoices['rows'] as $v): ?>
            <tr>
                <td style="width: 5%" class="hidden-xs text-center">
                            <span class="label label-default" style="background-color: <?= $v[ 'color' ] ?>">
                                <?= $v[ 'payment_status' ] ?></span>
                </td>
                <td class="text-center">
                    <?= display_date($v[ 'date_purchased' ]) ?>
                </td>
                <td class="text-center">
                    <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v[ 'invoice_id' ]) ?>">
                        <strong>
                            <?php if (empty($v[ 'invoice_number' ])): ?>
                                <?= $v[ 'invoice_id' ] ?>
                            <?php else: ?>
                                <?= $v[ 'invoice_number' ] ?>
                            <?php endif; ?>
                        </strong>
                    </a>
                </td>
                <td class="text-center"><?= format_amount($v[ 'total' ]) ?></td>
                <td class="text-right">
                    <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v[ 'invoice_id' ]) ?>"
                       class="tip block-phone btn btn-default btn-sm <?= is_disabled('update') ?>" data-toggle="tooltip"
                       data-placement="bottom" title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
<div class="row">
    <div class="col-md-4">
        <?= generate_sub_headline(CONTROLLER_CLASS, 'fa-edit', '') ?>
    </div>
    <div class="col-md-8 text-right">
        <?php if (CONTROLLER_FUNCTION == 'update'): ?>
            <?= prev_next_item('left', CONTROLLER_METHOD, $row['prev']) ?>
            <?php if ($id > 1): ?>
                <a data-href="<?= admin_url(TBL_TAX_CLASSES . '/delete/' . $row['tax_class_id']) ?>" data-toggle="modal"
                   data-target="#confirm-delete" href="#" 
				   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
                        class="hidden-xs"><?= lang('delete') ?></span></a>
            <?php endif; ?>
        <?php endif; ?>

        <a href="<?= admin_url(TBL_TAX_CLASSES . '/view') ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
                class="hidden-xs"><?= lang('view_tax_classes') ?></span></a>
        <?php if (CONTROLLER_FUNCTION == 'update'): ?>
            <?= prev_next_item('right', CONTROLLER_METHOD, $row['next']) ?>
        <?php endif; ?>
    </div>
</div>
<hr/>
<div class="row">
    <div class="col-md-12">
        <div class="box-info">
            <ul class="nav nav-tabs text-capitalize" role="tablist">
                <li class="active"><a href="#name" role="tab" data-toggle="tab"><?= lang('name') ?></a></li>
                <li><a href="#rules" role="tab" data-toggle="tab"><?= lang('rate_rules') ?></a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="name">
                    <div class="hidden-xs">
                        <h3 class="text-capitalize">
                            <?php if (CONTROLLER_FUNCTION == 'update'): ?>
                                <?= lang('update_tax_class') ?>
                            <?php else: ?>
                                <?= lang('add_tax_class') ?>
                            <?php endif; ?>
                        </h3>
                        <span><?= lang('setup_tax_classes_for_shipping_and_taxes') ?></span>
                    </div>
                    <hr/>
                    <div class="form-group">
                        <label for="class_name" class="col-md-3 control-label"><?= lang('class_name') ?></label>

                        <div class="col-md-5">
                            <?= form_input('class_name', set_value('class_name', $row['class_name']), 'class="' . css_error('class_name') . ' form-control required"') ?>
                        </div>
                    </div>
                    <hr/>
                    <div class="form-group">
                        <label for="class_description"
                               class="col-md-3 control-label"><?= lang('class_description') ?></label>

                        <div class="col-md-5">
                            <textarea name="class_description" rows="4"
                                      class="form-control required"><?= set_value('class_description', $row['class_description']) ?></textarea>
                        </div>
                    </div>
                    <hr/>
                </div>
                <div class="tab-pane" id="rules">
                    <div class="hidden-xs">
                        <h3 class="text-capitalize"><span class="pull-right"><a
                                    href="<?= admin_url(TBL_TAX_RATES . '/assign/' . $id) ?>" id="manage_attributes"
                                    class="btn btn-primary iframe <?= is_disabled('update', true) ?>"><?= i('fa fa-plus') ?> <?= lang('assign_tax_rates') ?></a></span>
                            <?= lang('tax_rate_rules') ?>
                        </h3>
                        <span><?= lang('enable_tax_rates_for_this_tax_class') ?></span>
                    </div>
                    <hr/>
                    <?php if (!empty($row['rates'])): ?>
                        <table class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th><?= lang('rate_name') ?></th>
                                <th class="text-center"><?= lang('tax_type') ?></th>
                                <th class="text-center"><?= lang('tax_amount') ?></th>
                                <th class="text-center"><?= lang('amount_type') ?></th>
                                <th class="text-center"><?= lang('calculate_from') ?></th>
                                <th class="tex-center"><?= lang('priority') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($row['rates'] as $k => $v): ?>
                                <tr>
                                    <td><?= $v['tax_rate_name'] ?></td>
                                    <td class="text-center"><span
                                            class="label label-primary label-<?= $v['tax_type'] ?>"><?= $v['tax_type'] ?></span>
                                    </td>
                                    <td class="text-center"><?= $v['tax_amount'] ?></td>
                                    <td class="text-center"><span
                                            class="label label-default label-<?= $v['amount_type'] ?>"><?= $v['amount_type'] ?></span>
                                    </td>
                                    <td style="width: 15%">
                                        <?= form_dropdown('rate_rules[' . $k . '][calculation]', options('bill_ship_address'), $v['calculation'], 'class="form-control"') ?>
                                    </td>
                                    <td style="width: 10%" class="tex-center">
                                        <input type="number" name="rate_rules[<?= $k ?>][priority]"
                                               value="<?= set_value('tax_amount', $v['priority']) ?>"
                                               class="form-control required">
                                        <?php if (!empty($v['tax_rate_id'])): ?>
                                            <?= form_hidden('rate_rules[' . $k . '][tax_rate_id]', $v['tax_rate_id']) ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div
                            class="alert alert-warning"><?= i('fa fa-info-circle') ?> <?= lang('no_tax_rates_assigned') ?></div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>
<?php if (CONTROLLER_FUNCTION == 'update'): ?>
    <?= form_hidden('tax_class_id', $row['tax_class_id']) ?>
<?php endif; ?>
<nav class="navbar navbar-fixed-bottom save-changes">
    <div class="container text-right">
        <div class="row">
            <div class="col-md-12">
                <button class="btn btn-info navbar-btn block-phone <?= is_disabled('update', true) ?>" id="update-button"
                        type="submit"><?=i('fa fa-refresh')?> <?= lang('save_changes') ?></button>
            </div>
        </div>
    </div>
</nav>
<?= form_close() ?>
<!-- Load JS for Page -->
<script>
    $("#form").validate({
        ignore: "",
        submitHandler: function (form) {
            $.ajax({
                url: '<?=current_url()?>',
                type: 'POST',
                dataType: 'json',
                data: $('#form').serialize(),
                success: function (response) {
                    if (response.type == 'success') {
                        $('.alert-danger').remove();
                        $('.form-control').removeClass('error');

                        if (response.redirect) {
                            location.href = response.redirect;
                        }
                        else {
                            $('#response').html('<?=alert('success')?>');

                            setTimeout(function () {
                                $('.alert-msg').fadeOut('slow');
                            }, 5000);
                        }
                    }
                    else {
                        $('#response').html('<?=alert('error')?>');
                    }

                    $('#msg-details').html(response.msg);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    <?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        }
    });
</script>
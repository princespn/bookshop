<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'id="form" class="form-horizontal"') ?>
<div class="row">
    <div class="col-md-4">
        <?= generate_sub_headline(CONTROLLER_CLASS, 'fa-edit', '') ?>
    </div>
    <div class="col-md-8 text-right">
        <?php if (CONTROLLER_FUNCTION == 'update'): ?>
            <?= prev_next_item('left', CONTROLLER_METHOD, $row['prev']) ?>
            <?php if ($id > 1): ?>
                <a data-href="<?= admin_url(TBL_TAX_RATES . '/delete/' . $row['tax_rate_id']) ?>" data-toggle="modal"
                   data-target="#confirm-delete" href="#" 
				   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
                        class="hidden-xs"><?= lang('delete') ?></span></a>
            <?php endif; ?>
        <?php endif; ?>
        <a href="<?= admin_url(TBL_TAX_RATES . '/view/') ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
                class="hidden-xs"><?= lang('view_tax_rates') ?></span></a>
        <?php if (CONTROLLER_FUNCTION == 'update'): ?>
            <?= prev_next_item('right', CONTROLLER_METHOD, $row['next']) ?>
        <?php endif; ?>
    </div>
</div>
<hr/>
<div class="row">
    <div class="col-md-12">
        <div class="box-info">
            <div class="hidden-xs">
                <h4 class="text-capitalize">
                    <?= i('fa fa-cog') ?> <?= lang(CONTROLLER_FUNCTION) ?>
                </h4>
            </div>
            <br/>

            <div class="form-group">
                <label for="tax_rate_name" class="col-md-3 control-label"><?= lang('tax_rate_name') ?></label>

                <div class="col-md-5">
                    <?= form_input('tax_rate_name', set_value('tax_rate_name', $row['tax_rate_name']), 'class="' . css_error('tax_rate_name') . ' form-control required"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
                <?= lang('tax_type', 'tax_type', array('class' => 'col-md-3 control-label')) ?>
                <div class="col-md-5">
                    <?= form_dropdown('tax_type', options('tax_types'), $row['tax_type'], 'class="form-control"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
                <?= lang('zone_id', 'zone_id', array('class' => 'col-md-3 control-label')) ?>
                <div class="col-md-5">
                    <div class="input-group">
                        <?= form_dropdown('zone_id', options('zones', FALSE, $zones), $row['zone_id'], 'class="form-control"') ?>
                        <span class="input-group-addon"><a href="<?= admin_url(TBL_ZONES) ?>"><i
                                    class="fa fa-search"></i> <?= lang('view_zones') ?></a></span>
                    </div>
                </div>
            </div>
            <hr/>
            <div class="form-group">
                <label for="tax_amount" class="col-md-3 control-label"><?= lang('tax_amount') ?></label>

                <div class="col-md-5">
                    <?= form_input('tax_amount', set_value('tax_amount', $row['tax_amount']), 'class="' . css_error('tax_amount') . ' form-control required"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
                <?= lang('amount_type', 'amount_type', array('class' => 'col-md-3 control-label')) ?>
                <div class="col-md-5">
                    <?= form_dropdown('amount_type', options('flat_percent'), $row['amount_type'], 'class="form-control"') ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if (CONTROLLER_FUNCTION == 'update'): ?>
    <?= form_hidden('tax_rate_id', $row['tax_rate_id']) ?>
<?php endif; ?>
<nav class="navbar navbar-fixed-bottom save-changes">
    <div class="container text-right">
        <div class="row">
            <div class="col-md-12">
                <?php if (CONTROLLER_FUNCTION == 'create'): ?>
                    <input type="submit" name="redir_button" value="<?= lang('save_add_another') ?>"
                           class="btn btn-success navbar-btn block-phone"/>
                <?php endif; ?>
                <button class="btn btn-info navbar-btn block-phone <?= is_disabled('update', true) ?>" id="update-button"
                        type="submit"><?=i('fa fa-refresh')?> <?= lang('save_changes') ?></button>
            </div>
        </div>
    </div>
</nav>
<?= form_close() ?>
<br/>
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
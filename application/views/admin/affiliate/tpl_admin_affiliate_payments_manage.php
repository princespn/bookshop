<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
<div class="row">
    <div class="col-md-5">
        <h2 class="sub-header block-title"><?= i('fa fa-pencil') ?> <?= lang(CONTROLLER_FUNCTION . '_' . singular(CONTROLLER_CLASS)) ?></h2>
    </div>
    <div class="col-md-7 text-right">
        <?php if (CONTROLLER_FUNCTION == 'update'): ?>
            <?= prev_next_item('left', CONTROLLER_METHOD, $row[ 'prev' ]) ?>
            <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $id) ?>" data-toggle="modal"
               data-target="#confirm-delete" href="#" 
				   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
                    class="hidden-xs"><?= lang('delete') ?></span></a>
        <?php endif; ?>
        <a href="<?= admin_url(CONTROLLER_CLASS) ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
                class="hidden-xs"><?= lang('view_payments') ?></span></a>
        <?php if (CONTROLLER_FUNCTION == 'update'): ?>
            <?= prev_next_item('right', CONTROLLER_METHOD, $row[ 'next' ]) ?>
        <?php endif; ?>
    </div>
</div>
<hr/>
<div class="box-info">
    <ul class="nav nav-tabs text-capitalize">
        <li class="active"><a href="#main" data-toggle="tab"><?= lang('details') ?></a></li>
        <li><a href="#notes" data-toggle="tab"><?= lang('notes') ?></a></li>
    </ul>
    <div class="tab-content">
        <div id="main" class="tab-pane fade in active">
            <h3 class="text-capitalize">
                <?= lang('affiliate_payment_details') ?>
            </h3>
            <hr/>
            <div class="form-group">
                <label class="col-lg-3 control-label"><?= lang('payment_date') ?></label>
                <div class="col-lg-5">
                    <div class="input-group">
                        <input name="payment_date" id="payment_date" type="text"
                               value="<?= set_value('payment_date', $row[ 'payment_date_formatted' ]) ?>" class="datepicker-input form-control required"/>
                        <span class="input-group-addon"><i class="fa fa-calendar"></i> </span>
                    </div>
                </div>
            </div>
            <hr/>
            <div class="form-group">
                <label class="col-lg-3 control-label"><?= lang('payment_name') ?></label>
                <div class="col-lg-5">
                    <div class="input-group">
                        <input name="payment_name" id="payment_name" type="text"
                               value="<?= set_value('payment_name', $row[ 'payment_name' ]) ?>" class="form-control required"/>
                        <?php if (!empty($row['username'])): ?>
                        <span class="input-group-addon"><a href="<?=admin_url(TBL_MEMBERS . '/update/' . $row['member_id'])?>"><i class="fa fa-user"></i></a></span>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
            <hr/>
            <div class="form-group">
                <label class="col-lg-3 control-label"><?= lang('payment_amount') ?></label>
                <div class="col-lg-5">
                    <input name="payment_amount" id="payment_amount" type="text"
                           value="<?= input_amount($row[ 'payment_amount' ]) ?>" class="form-control required number"/>
                </div>
            </div>
            <hr/>
            <div class="form-group">
                <label class="col-lg-3 control-label"><?= lang('payment_type') ?></label>
                <div class="col-lg-5">
                    <input name="payment_type" id="payment_type" type="text" value="<?= set_value('payment_type', $row[ 'payment_type' ]) ?>" class="form-control required"/>
                </div>
            </div>
            <hr/>
        </div>
        <div id="notes" class="tab-pane fade in">
            <h3 class="text-capitalize"><?= lang('commission_notes') ?></h3>
            <hr/>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('notes', 'payment_notes', array( 'class' => 'col-md-3 control-label' )) ?>
                        <div class="r col-md-5">
                            <textarea name="payment_notes" class="form-control" rows="10"><?= set_value('payment_notes', $row[ 'payment_notes' ]) ?></textarea>
                        </div>
                    </div>
                    <hr/>
                </div>
            </div>
        </div>
    </div>
    <nav class="navbar navbar-fixed-bottom save-changes">
        <div class="container text-right">
            <div class="row">
                <div class="col-md-12">
                    <a href="<?= admin_url(TBL_AFFILIATE_COMMISSIONS . '/view/?payment_id=' . $id) ?>"
                       class="btn btn-primary" title=""><i
                            class="fa fa-search"></i> <?= lang('view_associated_commissions') ?></a>
                    <button class="btn btn-info navbar-btn block-phone"
                            id="update-button" <?= is_disabled('update', TRUE) ?>
                            type="submit"><?=i('fa fa-refresh')?> <?= lang('save_changes') ?></button>
                </div>
            </div>
        </div>
    </nav>
</div>
<script>
    $("#form").validate({
        ignore: "",
        submitHandler: function (form) {
            $.ajax({
                url: '<?=current_url()?>',
                type: 'POST',
                dataType: 'json',
                data: $('#form').serialize(),
                beforeSend: function () {
                    $('#update-button').button('loading');
                },
                complete: function () {
                    $('#update-button').button('reset');
                },
                success: function (response) {
                    if (response.type == 'success') {
                        $('.alert-danger').remove();
                        $('.form-control').removeClass('error');

                        $('#response').html('<?=alert('success')?>');

                        setTimeout(function () {
                            $('.alert-msg').fadeOut('slow');
                        }, 5000);
                    }
                    else {
                        $('#response').html('<?=alert('error')?>');
                        if (response['error_fields']) {
                            $.each(response['error_fields'], function (key, val) {
                                $('#' + key).addClass('error');
                                $('#' + key).focus();
                            });
                        }
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
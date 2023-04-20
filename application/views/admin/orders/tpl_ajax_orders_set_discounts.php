<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open(admin_url('orders/update_discount'), 'role="form" id="update-discount-form" class="form-horizontal"') ?>
<div class="row">
    <div class="col-md-12">
        <h3 class="text-capitalize"><?= lang('apply_discounts_and_coupons') ?></h3>
        <hr/>
        <div class="form-group">
            <label class="col-md-4 control-label"><?= lang('discount_code') ?></label>

            <div class="col-md-6">
                <input name="code" type="text" value="" class="form-control"/>
                <input type="hidden" name="discount_type" value="coupon" />
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-md-12 text-right">
                <button id="update-button"
                        class="btn btn-primary navbar-btn block-phone <?= is_disabled('update', TRUE) ?>"
                        type="submit"><?= i('fa fa-refresh') ?> <?= lang('redeem_code') ?></button>

                <button id="submit-step-three"
                        class="submit-button btn btn-info navbar-btn block-phone <?= is_disabled('update', TRUE) ?>"
                        type="button"><?= i('fa fa-arrow-circle-right') ?> <?= lang('continue_to_next_step') ?></button>
            </div>
        </div>
    </div>
</div>
<?= form_close() ?>
<script>
    $("#update-discount-form").validate({
        ignore: "",
        errorContainer: $("#error-alert"),
        submitHandler: function (form) {
            $.ajax({
                url: '<?=admin_url('orders/update_discount')?>',
                type: 'POST',
                dataType: 'json',
                data: $('#update-discount-form').serialize(),
                beforeSend: function () {
                    $('#update-button').button('loading');
                },
                complete: function () {
                    $('#update-button').button('reset');
                },
                success: function (response) {
                    if (response.type == 'success') {
                        $('.alert-danger').remove();
                        $('#products-box').load('<?=admin_url('orders/order_contents/disable')?>');
                    }
                    else {
                        $('#response').html('<?=alert('error')?>');
                        $('#msg-details').html(response.msg);
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    <?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        }
    });

    $('#submit-step-three').on('click', function () {
        //activate tabs
        $("#step-four-heading").html('<a data-toggle="collapse" data-parent="#accordion" href="#step-four" aria-expanded="true" aria-controls="step-four" class="panel-title"><?= lang('step_four') ?>: <?= lang('set_shipping_address') ?> <?=i('fa fa-caret-down')?></a>');
        //set coupon data
        $('a[href=\'#step-four\']').trigger('click');

        $('.alert-danger').remove();
        $('#shipping-address-box').load('<?=admin_url('orders/set_shipping')?>');
    });
</script>

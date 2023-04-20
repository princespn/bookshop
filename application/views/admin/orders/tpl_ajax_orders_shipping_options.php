<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="animated fadeIn">
    <?php if (empty($shipping_options)): ?>
        <div class="alert alert-danger text-capitalize" role="alert">
            <h5 class="alert-danger"><?= i('fa fa-exclamation-circle') ?> <?= lang('no_shipping_options_found') ?></h5>

            <p><a href="<?= admin_url('shipping') ?>"
                  class="alert-danger"><?= lang('click_here_to_configure_one') ?></a></p>
        </div>
    <?php else: ?>
    <?= form_open($form_url, 'role="form" id="shipping-options-form" class="form-horizontal"') ?>
        <h3 class="text-capitalize"><?= lang('select_shipping_option') ?></h3>
    <hr/>
        <div class="form-group row">
            <?php foreach ($shipping_options as $k => $p): ?>
                <div class="col-md-5 col-md-offset-1">
                    <div>
                        <input type="radio" name="select_shipping" class="required"
                               id="shipping-option-<?= $k ?>" <?php if ($k == 1): ?> checked="checked"
                        <?php endif; ?> value="<?= $k ?>"/>
                        <?= lang($p[ 'shipping_description' ]) ?> - <?= format_amount($p[ 'shipping_total' ]) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <hr/>
        <div class="row">
            <div class="col-md-12 text-right">
                <button id="submit-step-five"
                        class="submit-button btn btn-info navbar-btn block-phone <?= is_disabled('update', TRUE) ?>"
                        type="submit"><?= i('fa fa-refresh') ?> <span id="update-shipping-button"><?= lang('save_changes') ?></span></button>
            </div>
        </div>
    <?= form_close() ?>
        <script>
            $("#shipping-options-form").validate({
                errorContainer: $("#error-alert"),
                submitHandler: function (form) {
                    $.ajax({
                        url: '<?=$form_url?>',
                        type: 'POST',
                        dataType: 'json',
                        data: $('#shipping-options-form').serialize(),
                        beforeSend: function () {
                            $('#submit-step-five').button('loading');
                            $('#billing_information-box').html('<div class="alert alert-warning text-capitalize"><?=lang('loading_billing_information')?>...</div>');
                        },
                        complete: function () {
                            $('#submit-step-five').button('reset');
                        },
                        success: function (response) {
                            if (response.type == 'success') {
                                $('.alert-danger').remove();

                                if (response['data']) {
                                    $('#response').html('<?=alert('success')?>');
                                    $('#msg-details').html(response.msg);
                                    $.each(response['data'], function (key, value) {
                                        $('#' + key).html(value);
                                        $('#update_' + key).val(value);
                                    });

                                    setTimeout(function () {
                                        $('.alert-msg').fadeOut('slow');
                                    }, 5000);
                                    $('#view-order-button').trigger('click');
                                }
                                else
                                {
                                    $('#products-box').load('<?=admin_url('orders/order_contents/disable')?>');
                                    $('#billing_information-box').load('<?=admin_url('orders/billing_information')?>');
                                    $("#step-six-heading").html('<a data-toggle="collapse" data-parent="#accordion" href="#step-six" aria-expanded="true" aria-controls="step-six" class="panel-title"><?= lang('step_six') ?>: <?= lang('billing_information') ?> <?=i('fa fa-caret-down')?></a>');
                                    //set coupon data
                                    $('a[href=\'#step-six\']').trigger('click');
                                }
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
        </script>
    <?php endif; ?>
</div>

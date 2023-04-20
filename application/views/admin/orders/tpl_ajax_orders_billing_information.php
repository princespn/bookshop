<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open(admin_url('orders/billing_information'), 'role="form" id="set-billing-form" class="form-horizontal"') ?>
    <div class="row">
        <div class="col-md-12">
            <h3 class="text-capitalize"><?= lang('billing_information') ?></h3>
            <hr/>
            <div id="billing-fields" class="row">
                <div class="col-md-12 text-capitalize">
                    <?php if (!empty($fields[ 'billing' ])): ?>
                        <?php foreach ($fields[ 'billing' ] as $v): ?>
                            <?php if ($v[ 'field_type' ] != 'hidden'): ?>
                                <div class="form-group row">
                                    <label for="<?= $v[ 'form_field' ] ?>"
                                           class="col-sm-4 col-form-label text-md-right">
                                        <?= $v[ 'field_name' ] ?>
                                    </label>

                                    <div class="col-sm-6"><?= $v[ 'field' ] ?></div>
                                </div>
                            <?php else: ?>
                                <?= $v[ 'field' ] ?>
                            <?php endif; ?>
                            <hr />
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-right">
                    <button id="submit-step-seven"
                            class="submit-button btn btn-info navbar-btn block-phone <?= is_disabled('update', TRUE) ?>"
                            type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_and_continue') ?></button>
                </div>
            </div>
        </div>
    </div>
<?= form_close() ?>
<script>
    $("#set-billing-form").validate({
        errorContainer: $("#error-alert"),
        submitHandler: function (form) {
            $.ajax({
                url: '<?=admin_url('orders/billing_information')?>',
                type: 'POST',
                dataType: 'json',
                data: $('#set-billing-form').serialize(),
                beforeSend: function () {
                    $('#submit-step-seven').button('loading');
                },
                complete: function () {
                    $('#submit-step-seven').button('reset');
                },
                success: function (response) {
                    if (response.type == 'success') {
                        load_payment_tab();
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

    function load_payment_tab()
    {
        $('.alert-danger').remove();

        $('#products-box').load('<?=admin_url('orders/order_contents/disable')?>');

        $("#step-seven-heading").html('<a data-toggle="collapse" data-parent="#accordion" href="#step-seven" aria-expanded="true" aria-controls="step-seven" class="panel-title"><?= lang('step_seven') ?>: <?= lang('generate_order') ?> <?=i('fa fa-caret-down')?></a>');
        //set coupon data
        $('a[href=\'#step-seven\']').trigger('click');
        $('#order-payment-box').load('<?=admin_url('orders/load_generate_payment')?>');
    }

    $('#billing_country').select2({
        ajax: {
            url: '<?=site_url('search/search_countries/')?>',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    country_name: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data, page) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.country_id,
                            text: item.country_name
                        }
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });

    function updateregion(select, type) {
        $.post('<?=site_url('search/load_regions/state')?>', {country_id: $('#' + type + '_country').val()},
            function (data) {
                $('#' + type + '_state').html(data);
                $(".s2").select2();
            }
        );
    }
</script>

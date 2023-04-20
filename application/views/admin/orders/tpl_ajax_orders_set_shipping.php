<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open(admin_url('orders/set_shipping'), 'role="form" id="set-shipping-form" class="form-horizontal"') ?>
<div class="row">
    <div class="col-md-12">
        <h3 class="text-capitalize"><?= lang('add_shipping_to_this_order') ?></h3>
        <hr/>
        <div class="form-group">
            <label class="col-md-4 control-label"><?= lang('charge_shipping') ?></label>

            <div class="col-md-6">
                <select id="charge_shipping" class="form-control" name="charge_shipping">
                    <option value="0"><?= lang('no') ?></option>
                    <option value="1"><?= lang('yes') ?></option>
                </select>
            </div>
        </div>
        <hr/>
        <div id="shipping-fields" class="row collapse">
            <div class="col-md-12 text-capitalize">
                <?php if (!empty($fields[ 'shipping' ])): ?>
                    <h5><?= lang('shipping_information') ?></h5>
                    <hr />
                    <?php foreach ($fields[ 'shipping' ] as $v): ?>
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
                <button id="submit-step-four"
                        class="submit-button btn btn-info navbar-btn block-phone <?= is_disabled('update', TRUE) ?>"
                        type="submit"><?= i('fa fa-arrow-circle-right') ?> <?= lang('continue_to_next_step') ?></button>
            </div>
        </div>
    </div>
</div>
<?= form_close() ?>
<script>
    $("#set-shipping-form").validate({
        errorContainer: $("#error-alert"),
        submitHandler: function (form) {
            $.ajax({
                url: '<?=admin_url('orders/set_shipping')?>',
                type: 'POST',
                dataType: 'json',
                data: $('#set-shipping-form').serialize(),
                beforeSend: function () {
                    $('#submit-step-four').button('loading');
                    $('#shipping-options-box').html('<div class="alert alert-warning text-capitalize"><?=i('fa fa-spinner fa-pulse')?>  <?=lang('loading_shipping_options')?>...</div>');
                },
                complete: function () {
                    $('#submit-step-four').button('reset');
                },
                success: function (response) {
                    if (response.type == 'success') {
                        $('.alert-danger').remove();

                        $('#products-box').load('<?=admin_url('orders/order_contents/disable')?>');

                        if (response.charge_shipping) {
                            $('#shipping-options-box').load('<?=admin_url('orders/shipping_options')?>');
                            $("#step-five-heading").html('<a data-toggle="collapse" data-parent="#accordion" href="#step-five" aria-expanded="true" aria-controls="step-five" class="panel-title"><?= lang('step_five') ?>: <?= lang('shipping_options') ?> <?=i('fa fa-caret-down')?></a>');
                            //set coupon data
                            $('a[href=\'#step-five\']').trigger('click');
                        }
                        else
                        {
                            $('#billing_information-box').load('<?=admin_url('orders/billing_information')?>');
                            $("#step-five-heading").html('<?= lang('step_five') ?>: <?= lang('no_shipping_required') ?>');
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

    select_country('#shipping_country');

    $('#charge_shipping').on('change', function () {
        if (this.value == 1) {
            $('#shipping-fields').collapse('show');
            $('.shipping.form-control').prop('disabled', false);
            select_country('#shipping_country');
        }
        else {
            $('#shipping-fields').collapse('hide');
            $('.shipping.form-control').prop('disabled', true);
        }
    });

    function select_country(id) {
        //search countries
        $(id).select2({
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
    }

    function updateregion(select, type) {
        $.get('<?=site_url('search/load_regions/state')?>', {country_id: $('#' + type + '_country').val()},
            function (data) {
                $('#' + type + '_state').html(data);
                $(".s2").select2();
            }
        );
    }
</script>

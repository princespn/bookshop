<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-5">
        <h2 class="sub-header block-title">
            <?= i('fa fa-pencil') ?> <?= lang('create_order') ?>
        </h2>
    </div>
    <div class="col-md-7 text-right">
        <a href="<?= admin_url(TBL_ORDERS) ?>" class="btn btn-primary">
            <?= i('fa fa-search') ?> <span class="hidden-xs"><?= lang('view_orders') ?></span>
        </a>
    </div>
</div>
<hr/>
<div class="row">
    <div class="col-md-5">
        <div class="box-info">
            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-one">
                        <h5 class="panel-title text-capitalize">
                            <span id="step-one-heading">
                                <?= lang('step_one') ?>: <?= lang('select_client') ?> <?= i('fa fa-caret-down') ?>
                            </span>
                        </h5>
                    </div>
                    <div id="step-one" class="panel-collapse collapse in" role="tabpanel"
                         aria-labelledby="heading-one">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <?= form_open(admin_url('orders/create'), 'role="form" id="step-one-form" class="form-horizontal"') ?>
                                    <h3 class="text-capitalize"> <?= lang('client_profile') ?></h3>
                                    <span class="text-capitalize"><?= lang('type_in_client_name_for_order') ?></span>
                                    <hr/>
                                    <div class="form-group">
                                        <?= lang('search_client', 'member_id', array( 'class' => 'col-md-4 control-label' )) ?>
                                        <div class="col-md-6">
                                            <select id="member_id" class="form-control select2" name="member_id">
                                                <?php if (!empty($row['member_id'])): ?>
                                                    <option value="<?=$row['member_id']?>" selected><?= $row['fname'] . ' ' . $row['lname'] ?></option>
                                                <?php else: ?>
                                                <option value="0" selected><?= lang('new_client') ?></option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <hr/>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div id="profile">
                                                <div class="form-group">
                                                    <label class="col-md-4 control-label">
                                                        * <?= lang('first_name') ?></label>

                                                    <div class="col-md-6">
                                                        <?= form_input('fname', set_value('fname', $row['fname']), 'id="fname" class="' . css_error('fname') . 'se form-control required"') ?>
                                                    </div>
                                                </div>
                                                <hr/>
                                                <div class="form-group">
                                                    <label class="col-md-4 control-label">
                                                        * <?= lang('last_name') ?></label>

                                                    <div class="col-md-6">
                                                        <?= form_input('lname', set_value('lname', $row[ 'lname' ]), 'id="lname" class="' . css_error('lname') . 'se form-control required"') ?>
                                                    </div>
                                                </div>
                                                <hr/>
                                                <?php if (config_enabled('affiliate_marketing')): ?>
                                                    <div class="form-group">
                                                        <label
                                                            class="col-md-4 control-label"><?= lang('referred_by') ?></label>

                                                        <div class="col-md-6" id="aff-box">
                                                            <select id="affiliate_id" class="form-control select2"
                                                                    name="affiliate_id">
                                                                <option value="0"
                                                                        selected><?= lang('enter_referral_username_if_any') ?></option>
                                                                <?php if (!empty($row[ 'sponsor_id' ])): ?>
                                                                    <option value="<?= $row[ 'sponsor_id' ] ?>"
                                                                            selected><?= $row[ 'sponsor_username' ] ?></option>
                                                                <?php endif; ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <hr/>
                                                <?php endif; ?>
                                                <div class="form-group">
                                                    <label class="col-md-4 control-label"><?= lang('company') ?></label>

                                                    <div class="col-md-6">
                                                        <?= form_input('order_company', set_value('order_company', $row[ 'order_company' ]), 'id="company" class="' . css_error('order_company') . 'se form-control"') ?>
                                                    </div>
                                                </div>
                                                <hr/>
                                                <div class="form-group">
                                                    <label class="col-md-4 control-label">
                                                        * <?= lang('email_address') ?></label>

                                                    <div class="col-md-6">
                                                        <?= form_input('order_primary_email', set_value('order_primary_email', $row[ 'order_primary_email' ]), 'id="primary_email" class="' . css_error('order_primary_email') . 'se form-control required email"') ?>
                                                    </div>
                                                </div>
                                                <hr/>
                                                <div class="form-group">
                                                    <label class="col-md-4 control-label">
                                                        <?= lang('telephone') ?>
                                                    </label>

                                                    <div class="col-md-6">
                                                        <?= form_input('order_telephone', set_value('order_telephone', $row[ 'order_telephone' ]), 'id="home_phone" class="' . css_error('order_telephone') . 'se form-control"') ?>
                                                    </div>
                                                </div>
                                                <hr/>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 text-right">
                                                    <button id="submit-one-button"
                                                            class="steps-two submit-button btn btn-info navbar-btn block-phone <?= is_disabled('update', TRUE) ?>"
                                                            type="submit"><?= i('fa fa-arrow-circle-right') ?> <?= lang('continue_to_next_step') ?></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?= form_close() ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-two">
                        <h5 class="panel-title text-capitalize">
                            <span id="step-two-heading"><?= lang('step_two') ?>: <?= lang('select_products') ?></span>
                        </h5>
                    </div>
                    <div id="step-two" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-two">
                        <div class="panel-body">
                            <?= form_open(admin_url('orders/add_product'), 'role="form" id="add-prod-form" class="form-horizontal"') ?>
                            <div>
                                <h3 class="text-capitalize"><?= lang('add_products_to_order') ?></h3>
                                <hr/>
                                <div class="row hidden-xs">
                                    <div class="col-md-10"><?= tb_header('search_by_product_name') ?></div>
                                    <div class="col-md-2 text-center"><?= tb_header('quantity') ?></div>
                                </div>
                                <div id="products-div">
                                    <div class="row">
                                        <div class="col-md-10">
                                            <select id="product_id" class="form-control select2" name="product_id">
                                                <option value="" selected><?= lang('type_product_name') ?></option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 text-center">
                                            <input type="number" name="quantity" value="1"
                                                   class="form-control"/>
                                        </div>
                                    </div>
                                </div>
                                <hr/>
                                <div id="product-options"></div>
                                <div class="text-right">
                                    <button id="add-button"
                                            class="btn btn-primary navbar-btn block-phone <?= is_disabled('update', TRUE) ?>"
                                            type="submit"><?= i('fa fa-plus') ?> <?= lang('add_to_order') ?></button>

                                    <button id="submit-step-two"
                                            class="submit-button btn btn-info navbar-btn block-phone <?= is_disabled('update', TRUE) ?>"
                                            type="button"><?= i('fa fa-arrow-circle-right') ?> <?= lang('continue_to_next_step') ?></button>
                                </div>
                            </div>
                            <?= form_close() ?>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-three">
                        <h5 class="panel-title text-capitalize">
                            <span id="step-three-heading"><?= lang('step_three') ?>
                                : <?= lang('discounts_and_coupons') ?></span>
                        </h5>
                    </div>
                    <div id="step-three" class="panel-collapse collapse" role="tabpanel"
                         aria-labelledby="heading-three">
                        <div class="panel-body">
                            <div id="discount-box"></div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-four">
                        <h5 class="panel-title text-capitalize">
                            <span id="step-four-heading"><?= lang('step_four') ?>
                                : <?= lang('set_shipping_address') ?></span>
                        </h5>
                    </div>
                    <div id="step-four" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-four">
                        <div class="panel-body">
                            <div id="shipping-address-box"></div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-five">
                        <h5 class="panel-title text-capitalize">
                            <span id="step-five-heading">
                                <?= lang('step_five') ?>: <?= lang('shipping_options') ?>
                            </span>
                        </h5>
                    </div>
                    <div id="step-five" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-five">
                        <div class="panel-body">
                            <div id="shipping-options-box">
                                <div class="alert alert-warning text-capitalize">
                                    <?=i('fa fa-spinner fa-pulse')?> <?= lang('loading_shipping_options') ?>...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-six">
                        <h5 class="panel-title text-capitalize">
                            <span id="step-six-heading"><?= lang('step_six') ?>
                                : <?= lang('billing_information') ?></span>
                        </h5>
                    </div>
                    <div id="step-six" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-six">
                        <div class="panel-body">
                            <div id="billing_information-box">
                                <div class="alert alert-warning text-capitalize">
                                    <?= lang('loading_billing_information') ?>...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-seven">
                        <h5 class="panel-title text-capitalize">
                            <span id="step-seven-heading"><?= lang('step_seven') ?>
                                : <?= lang('generate_order') ?></span>
                        </h5>
                    </div>
                    <div id="step-seven" class="panel-collapse collapse" role="tabpanel"
                         aria-labelledby="heading-seven">
                        <div class="panel-body">
                            <div id="order-payment-box">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="box-info">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="pull-right"><?= lang('order_details') ?></h3>

                    <div id="client-box" class="hide">
                        <address>
                            <h3><span id="fname"></span> <span id="lname"></span></h3>

                            <div id="order_company"></div>
                            <div id="order_primary_email"></div>
                            <div id="order_telephone"></div>
                        </address>
                    </div>
                </div>
            </div>
            <div id="products-box">
                <div class="row">
                    <div class="col-md-12">
                        <hr/>
                        <div class="alert alert-warning">
                            <?= i('fa fa-exclamation-circle') ?> <?= lang('no_products_added_to_order') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    //for selecting an existing client
    $("#member_id").select2({
        ajax: {
            url: '<?=admin_url(TBL_MEMBERS . '/search/ajax/full_name/')?>',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    username: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data, page) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.member_id,
                            text: item.username
                        }
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });

    function updateProductAttribute () {};

    //for entering a referring sponsor via username
    $("#affiliate_id").select2({
        ajax: {
            url: '<?=admin_url(TBL_MEMBERS . '/search/ajax/')?>',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    username: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data, page) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.member_id,
                            text: item.username
                        }
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });

    //load existing client data
    $('#member_id').on('change', function () {
        $.get('<?=admin_url(TBL_MEMBERS . '/ajax_user/create_order')?>', {member_id: $('#member_id').val()},
            function (data) {
                $('#profile').html(data);
                $(".s2").select2();
            }
        );
    });

    $('#product_id').on('change', function () {
        $.ajax({
            url: '<?=admin_url(TBL_PRODUCTS . '/get_product_attributes/json/')?>',
            type: 'POST',
            dataType: 'json',
            data: $('#add-prod-form').serialize(),
            beforeSend: function () {
                $('.submit-button').button('loading');
            },
            complete: function () {
                $('.submit-button').button('reset');
            },
            success: function (response) {
                if (response.error) {
                    $('#msg-details').html(response.msg);
                }
                else {
                    if (response.attributes) {
                        $('#product-options').html(response.attributes);
                        $('#product-options').show(300);
                    }
                    else {
                        $('#product-options').hide(300);
                    }
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                <?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });

    //search for products
    $("#product_id").select2({
        ajax: {
            url: '<?=admin_url(TBL_PRODUCTS . '/search/orders')?>',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    product_name: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data, page) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.product_id,
                            text: item.product_name
                        }
                    })
                };
            }
        },
        minimumInputLength: 2
    });

    $('#submit-step-two').on('click', function () {
        $.ajax({
            url: '<?=admin_url('orders/check_cart_contents')?>',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.error) {
                    $('#response').html('<?=alert('error')?>');
                    $('#msg-details').html(response.msg);
                    $("#step-three-heading").html('<?= lang('step_three') ?>: <?= lang('discounts_and_coupons') ?>');
                }
                else {
                    //set order details to read only
                    $('#update-cart-form :input').attr('readonly', true);
                    $('#update-cart-button').addClass('hide');

                    //activate tabs
                    $("#step-three-heading").html('<a data-toggle="collapse" data-parent="#accordion" href="#step-three" aria-expanded="true" aria-controls="step-three" class="panel-title"><?= lang('step_three') ?>: <?= lang('discounts_and_coupons') ?> <?=i('fa fa-caret-down')?></a>');
                    //set coupon data
                    $('a[href=\'#step-three\']').trigger('click');
                    $('.alert-danger').remove();
                    $('#discount-box').load('<?=admin_url('orders/set_discounts')?>');
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                <?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });

    $('#step-two-heading').on('click', function () {
        $('#update-cart-form :input').attr('readonly', false);
        $('#update-cart-button').removeClass('hide');

        //deactivate tabs
        $("#step-three-heading").html('<?= lang('step_three') ?>: <?= lang('discounts_and_coupons') ?>');
        $("#step-four-heading").html('<?= lang('step_four') ?>: <?= lang('set_shipping_address') ?>');
        $("#step-five-heading").html('<?= lang('step_five') ?>: <?= lang('shipping_options') ?>');
        $("#step-six-heading").html('<?= lang('step_six') ?>: <?= lang('billing_information') ?>');
        $("#step-seven-heading").html('<?= lang('step_seven') ?>: <?= lang('payment_confirmation') ?>');
    });

    $("#step-one-form").validate({
	    ignore: "",
	    errorContainer: $("#error-alert"),
	    submitHandler: function (form) {
		    $.ajax({
			    url: '<?=current_url()?>',
			    type: 'POST',
			    dataType: 'json',
			    data: $('#step-one-form').serialize(),
			    beforeSend: function () {
				    $('.submit-button').button('loading');
			    },
			    complete: function () {
				    $('.submit-button').button('reset');
			    },
			    success: function (response) {
				    if (response.type == 'success') {
					    $('.alert-danger').remove();
					    $('.form-control').removeClass('error');

					    //update order details
					    if (response['data']) {
						    $.each(response['data'], function (key, val) {
							    $('#' + key).html(val);

						    });
						    $('#client-box').removeClass('hide');
					    }

					    //activate tabs
					    $("#step-one-heading").html('<a data-toggle="collapse" data-parent="#accordion" href="#step-one" aria-expanded="true" aria-controls="step-one" class="panel-title"><?= lang('step_one') ?>: <?= lang('select_client') ?> <?=i('fa fa-caret-down')?></a>');
					    $("#step-two-heading").html('<a data-toggle="collapse" data-parent="#accordion" href="#step-two" aria-expanded="true" aria-controls="step-two" class="steps-two panel-title" id="step-two-heading"><?= lang('step_two') ?>: <?= lang('select_products') ?> <?=i('fa fa-caret-down')?></a>');
					    $('a[href=\'#step-two\']').trigger('click');

				    }
				    else {
					    $('#response').html('<?=alert('error')?>');
					    if (response['error_fields']) {
						    $.each(response['error_fields'], function (key, val) {
							    $('#' + key).addClass('error');
							    $('#' + key).focus();
						    });
					    }

					    $('#msg-details').html(response.msg);
				    }
			    },
			    error: function (xhr, ajaxOptions, thrownError) {
				    <?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			    }
		    });
	    }
    });

    $("#add-prod-form").validate({
	    ignore: "",
	    errorContainer: $("#error-alert"),
	    submitHandler: function (form) {
		    $.ajax({
			    url: '<?=admin_url('orders/add_product')?>',
			    type: 'POST',
			    dataType: 'json',
			    data: $('#add-prod-form').serialize(),
			    beforeSend: function () {
				    $('#add-button').button('loading');
			    },
			    complete: function () {
				    $('#add-button').button('reset');
			    },
			    success: function (response) {
				    if (response.type == 'success') {
					    $('.alert-danger').remove();
					    $('.form-control').removeClass('error');

					    //update the order contents
					    $('#products-box').removeClass('hide');
					    $('#products-box').load('<?=admin_url('orders/order_contents')?>');
				    }
				    else {
					    $('#response').html('<?=alert('error')?>');
					    if (response['error_fields']) {
						    $.each(response['error_fields'], function (key, val) {
							    $('#' + key).addClass('error');
							    $('#' + key).focus();
						    });
					    }

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

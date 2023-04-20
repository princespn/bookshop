<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-5">
        <h2 class="sub-header block-title"><?= i('fa fa-pencil') ?> <?= lang('manage_subscription') ?></h2>
    </div>
    <div class="col-md-7 text-right">
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('left', CONTROLLER_METHOD, $row['prev']) ?>
            <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $id) ?>"
               data-toggle="modal" data-target="#confirm-delete" href="#" <?= is_disabled('delete') ?>
               class="md-trigger btn btn-danger"><?= i('fa fa-trash-o') ?> <?= lang('delete') ?></a>
		<?php endif; ?>
        <a href="<?= admin_url(CONTROLLER_CLASS . '/view/') ?>"
           class="btn btn-primary"><?= i('fa fa-search') ?> <span
                    class="hidden-xs"><?= lang('view_subscriptions') ?></span></a>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('right', CONTROLLER_METHOD, $row['next']) ?>
		<?php endif; ?>
    </div>
</div>
<hr/>
<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
<div class="box-info">
    <ul class="resp-tabs nav nav-tabs text-capitalize" role="tablist">
        <li class="active"><a href="#details" role="tab" data-toggle="tab"><?= lang('details') ?></a></li>
        <li><a href="#notes" role="tab" data-toggle="tab"><?= lang('notes') ?></a></li>
        <?php if (!empty($row['charge_shipping'])): ?>
        <li><a href="#shipping" role="tab" data-toggle="tab"><?= lang('shipping') ?></a></li>
        <?php endif; ?>
        <?php if (!empty($row['history'])): ?>
            <li><a href="#history" role="tab" data-toggle="tab"><?= lang('order_history') ?></a></li>
		<?php endif; ?>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade in active" id="details">
            <h3 class="text-capitalize"><?= lang('subscription_details') ?></h3>
            <hr/>
            <div class="form-group">
				<?= lang('status', 'status', 'class="col-md-3 control-label"') ?>

                <div class="r col-md-2">
					<?= form_dropdown('status', options('active'), $row['status'], 'class="form-control"') ?>
                </div>
				<?= lang('order_number', 'order_number', array('class' => 'col-md-1 control-label')) ?>
                <div class="r col-md-2">
                    <select id="order_id" class="form-control required select2"
                            name="order_id">
						<?php if (CONTROLLER_FUNCTION == 'update' && !empty($row['order_number'])): ?>
                            <option value="<?= $row['order_id'] ?>"
                                    selected><?= $row['order_number'] ?></option>
						<?php endif; ?>
                    </select>
                </div>
            </div>
            <hr/>
            <div class="form-group">
				<?= lang('member', 'member_id', array('class' => 'col-md-3 control-label')) ?>
                <div class="r col-md-2">
                    <select id="member_id" class="form-control select2" name="member_id">
						<?php if (!empty($row['member_id'])): ?>
                            <option value="<?= $row['member_id'] ?>"
                                    selected><?= $row['fname'] . ' ' . $row['lname'] . ' - ' . $row['primary_email'] ?></option>
						<?php else: ?>
                            <option value="0" selected><?= lang('new_client') ?></option>
						<?php endif; ?>
                    </select>
                </div>
	            <?= lang('product', 'product_id', array('class' => 'col-md-1 control-label')) ?>
                <div class="r col-md-2">
                    <select id="product_id" class="form-control select2" name="product_id">
			            <?php if (!empty($row['product_id'])): ?>
                            <option value="<?= $row['product_id'] ?>" selected><?= $row['product_name'] ?></option>
			            <?php else: ?>
                            <option value="0" selected><?= lang('select_product') ?></option>
			            <?php endif; ?>
                    </select>
                </div>
            </div>
            <hr/>
            <div class="form-group">
		        <?= lang('product_price', 'product_price', array('class' => 'col-md-3 control-label')) ?>
                <div class="r col-md-2">
			        <?= form_input('product_price', set_value('product_price', input_amount($row['product_price'])), 'class="' . css_error('product_price') . ' form-control required"') ?>
                </div>
	            <?= lang('tax_amount', 'tax_amount', array('class' => 'col-md-1 control-label')) ?>
                <div class="r col-md-2">
		            <?= form_input('tax_amount', set_value('tax_amount', input_amount($row['tax_amount'])), 'class="' . css_error('tax_amount') . ' form-control"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
                <div class="r col-md-5 col-md-offset-3">
                    <div id="product-options">
                        <div class="row">
                            <div class="col-lg-12">
								<?php if (!empty($row['attributes'])): ?>
                                    <h5 class="text-capitalize"><?= lang('select_product_options') ?></h5>
                                    <hr/>
									<?php foreach ($row['attributes'] as $v): ?>
										<?php if ($v['attribute_type'] != 'file'): ?>
                                        <div class="form-group">
											<?= lang($v['attribute_name'], $v['attribute_name'], array('class' => 'col-sm-3 control-label')) ?>
                                            <div class="col-lg-5">
													<?= $v['form_html'] ?>
                                            </div>
                                        </div>
                                        <hr/>
										<?php endif; ?>
									<?php endforeach; ?>
								<?php endif; ?>
                            </div>
                        </div>
                        <script>
                            $('button[id^=\'button-upload\']').on('click', function () {
                                var node = this;
                                $('#form-upload').remove();
                                $('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="files" /><input type="hidden" name="<?=$csrf_token?>" value="<?=$csrf_value?>" /></form>');
                                $('#form-upload input[name=\'files\']').trigger('click');

                                timer = setInterval(function () {
                                    if ($('#form-upload input[name=\'files\']').val() != '') {
                                        clearInterval(timer);
                                        $.ajax({
                                            url: '<?=admin_url('orders/upload/') ?>',
                                            type: 'post',
                                            dataType: 'json',
                                            data: new FormData($('#form-upload')[0]),
                                            cache: false,
                                            contentType: false,
                                            processData: false,
                                            success: function (data) {
                                                if (data['type'] == 'error') {
                                                    $('#response').html('<?=alert('error')?>');
                                                    $('#msg-details').html(data['msg']);
                                                }
                                                else if (data['type'] == 'success') {
                                                    $(node).parent().find('input').attr('value', data['key']);
                                                    $(node).parent().find('input').after('<div class="text-success">' + data['msg'] + '</div>');

                                                }
                                            },
                                            error: function (xhr, ajaxOptions, thrownError) {
												<?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                                            }
                                        });
                                    }
                                }, 500);
                            });
                        </script>
                    </div>
                </div>
            </div>
            <div class="form-group">
				<?= lang('interval_amount', 'interval_amount', array('class' => 'col-md-3 control-label')) ?>
                <div class="r col-md-2">
					<?= form_input('interval_amount', set_value('interval_amount', $row['interval_amount'], FALSE), 'class="' . css_error('interval_amount') . ' form-control digits required"') ?>
                </div>
				<?= lang('interval_type', 'interval_type', array('class' => 'col-md-1 control-label')) ?>
                <div class="r col-md-2">
					<?= form_dropdown('interval_type', options('interval_types'), $row['interval_type'], 'class="form-control"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
		        <?= lang('intervals_generated', 'intervals_generated', array('class' => 'col-md-3 control-label')) ?>
                <div class="r col-md-2">
			        <?= form_input('intervals_generated', set_value('intervals_generated', $row['intervals_generated'], FALSE), 'class="' . css_error('intervals_generated') . ' form-control digits required"') ?>
                </div>
		        <?= lang('max_intervals', 'intervals_required', array('class' => 'col-md-1 control-label')) ?>
                <div class="r col-md-2">
	                <?= form_input('intervals_required', set_value('intervals_required', $row['intervals_required'], FALSE), 'class="' . css_error('intervals_required') . ' form-control digits required"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
				<?= lang('start_date', 'date', array('class' => 'col-md-3 control-label')) ?>
                <div class="r col-md-2">
                    <div class="input-group">
                        <input type="text" name="start_date"
                               value="<?= set_value('start_date', $row['start_date_formatted']) ?>"
                               class="form-control datepicker-input required"/>
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>
				<?= lang('next_due_date', 'next_due_date', array('class' => 'col-md-1 control-label')) ?>
                <div class="r col-md-2">
                    <div class="input-group">
                        <input type="text" name="next_due_date"
                               value="<?= set_value('next_due_date', $row['next_due_date_formatted']) ?>"
                               class="form-control datepicker-input required"/>
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>
            </div>
            <hr/>

            <div class="form-group">
				<?= lang('payment_type', 'payment_type', array('class' => 'col-md-3 control-label')) ?>
                <div class="r col-md-2">
					<?= form_dropdown('payment_type', options('payment_gateways'), $row['payment_type'], 'class="form-control"') ?>
                </div>
				<?= lang('subscription_id', 'subscription_id', array('class' => 'col-md-1 control-label')) ?>
                <div class="r col-md-2">
					<?= form_input('subscription_id', set_value('subscription_id', $row['subscription_id']), 'class="' . css_error('subscription_id') . ' form-control required"') ?>
                </div>
            </div>
            <hr/>
        </div>
        <div class="tab-pane fade in" id="notes">
            <h3 class="text-capitalize"><?= lang('subscription_notes') ?></h3>
            <hr/>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
						<?= lang('notes', 'notes', array('class' => 'col-md-3 control-label')) ?>
                        <div class="r col-md-5">
							<textarea name="notes" class="form-control"
                                      rows="10"><?= $row['notes'] ?></textarea>
                        </div>
                    </div>
                    <hr/>
                </div>
            </div>
        </div>
	    <?php if (!empty($row['charge_shipping'])): ?>
        <div class="tab-pane fade in" id="shipping">
            <h3 class="text-capitalize"><?= lang('shipping') ?></h3>
            <hr/>
            <div class="form-group">
                <label class="col-md-3 control-label">
			        <?= lang('carrier') ?>
                </label>
                <div class="col-md-6">
			        <?= form_input('carrier', set_value('carrier', $row['shipping_data']['carrier']), 'id="update_shipping_carrier" class="' . css_error('carrier') . 'se form-control"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
                <label class="col-md-3 control-label">
			        <?= lang('service') ?>
                </label>
                <div class="col-md-6">
			        <?= form_input('service', set_value('service', $row['shipping_data']['service']), 'id="update_shipping_service" class="' . css_error('service') . 'se form-control"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
                <label class="col-md-3 control-label">
			        <?= lang('shipping_rate') ?>
                </label>
                <div class="col-md-6">
			        <?= form_input('shipping_amount', set_value('shipping_amount', input_amount($row['shipping_amount'])), 'id="update_shipping_rate" class="' . css_error('shipping_amount') . 'se form-control"') ?>
                </div>
            </div>
            <hr/>
        </div>
        <?php endif; ?>
		<?php if (!empty($row['history'])): ?>
            <div class="tab-pane fade in" id="history">
                <h3 class="text-capitalize"><?= lang('order_history') ?></h3>
                <hr/>
				<?php foreach ($row['history'] as $v): ?>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>
                                <a href="<?= admin_url(TBL_ORDERS . '/update/' . $v['order_id']) ?>"><?= $v['order_number'] ?></a>
                            </h5>
                        </div>
                    </div>
                    <hr/>
				<?php endforeach; ?>
            </div>
		<?php endif; ?>
    </div>
</div>
<nav class="navbar navbar-fixed-bottom save-changes">
    <div class="container text-right">
        <div class="row">
            <div class="col-md-12">
                <button class="btn btn-info navbar-btn block-phone"
                        id="update-button" <?= is_disabled('update', TRUE) ?>
                        type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
            </div>
        </div>
    </div>
</nav>
<?php if (CONTROLLER_CLASS == 'update'): ?>
	<?= form_hidden('sub_id', $id) ?>
<?php endif; ?>
<?= form_close() ?>
<br/>
<script>
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

    $("#product_id").select2({
        ajax: {
            url: '<?=admin_url(TBL_PRODUCTS . '/search/ajax/?product_type=subscription')?>',
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
            },
            cache: true
        },
        minimumInputLength: 1
    });

    $('#product_id').on('change', function () {
        $.ajax({
            url: '<?=admin_url(TBL_PRODUCTS_ATTRIBUTES . '/get_product_attributes/json/')?>',
            type: 'POST',
            dataType: 'json',
            data: $('#form').serialize(),
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

    $("#order_id").select2({
        ajax: {
            url: '<?=admin_url(TBL_ORDERS . '/search/ajax/')?>',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    invoice_number: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data, page) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.order_id,
                            text: item.order_number
                        }
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });

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
                    console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        }
    });

</script>
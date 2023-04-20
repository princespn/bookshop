<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
<div class="row">
    <div class="col-md-4">
		<?= generate_sub_headline(lang('members_credits_update'), 'fa-edit', '', FALSE) ?>
    </div>
    <div class="col-md-8 text-right">
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('left', CONTROLLER_METHOD, $row['prev']) ?>
            <a data-href="<?= admin_url(TBL_MEMBERS_CREDITS . '/delete/' . $row['mcr_id']) ?>" data-toggle="modal"
               data-target="#confirm-delete" href="#"
               class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
                        class="hidden-xs"><?= lang('delete') ?></span></a>
		<?php endif; ?>

        <a href="<?= admin_url(TBL_MEMBERS_CREDITS . '/view') ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
                    class="hidden-xs"><?= lang('members_credits_view') ?></span></a>
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
                <h3 class="text-capitalize"> <?= lang('member_credits') ?></h3>
                <span><?= lang('member_credit_description') ?></span>
            </div>
            <hr/>
            <div class="form-group">
		        <?= lang('date', 'date', array('class' => 'col-md-3 control-label')) ?>
                <div class="col-md-2">
                    <div class="input-group">
				        <?= form_input('date', set_value('date', $row['date']), 'class="' . css_error('date') . ' form-control required datepicker-input"') ?>
                        <span class="input-group-addon"><i class="fa fa-calendar"></i> </span>
                    </div>
                </div>
                <label class="col-md-1 control-label"><?= lang('member') ?></label>

                <div class="col-md-2">
                    <select id="member_id" class="form-control select2" name="member_id">
                        <option value="<?= $row['member_id'] ?>"
                                selected><?= $row['username'] ?></option>
                    </select>
                </div>
            </div>
            <hr/>
            <div class="form-group">
		        <?= lang('amount', 'amount', array('class' => 'col-md-3 control-label')) ?>
                <div class="col-md-2">
				        <?= form_input('amount', set_value('amount', $row['amount']), 'class="' . css_error('amount') . ' form-control required number"') ?>
                </div>
	            <?= lang('transaction_id', 'transaction_id', array('class' => 'col-md-1 control-label')) ?>
                <div class="col-md-2">
		            <?= form_input('transaction_id', set_value('transaction_id', $row['transaction_id']), 'class="' . css_error('transaction_id') . ' form-control required"') ?>

                </div>
            </div>
            <hr/>
            <div class="form-group">
		        <?= lang('notes', 'notes', 'class="col-md-3 control-label"') ?>

                <div class="col-lg-5">
			        <?= form_textarea('notes', set_value('notes', $row[ 'notes' ]), 'class="' . css_error('notes') . ' form-control"') ?>
                </div>
            </div>
            <hr/>
        </div>
    </div>
</div>
<?php if (CONTROLLER_FUNCTION == 'update'): ?>
	<?= form_hidden('mcr_id', $id) ?>
<?php endif; ?>
<nav class="navbar navbar-fixed-bottom save-changes">
    <div class="container text-right">
        <div class="row">
            <div class="col-lg-12">
                <button class="btn btn-info navbar-btn block-phone"
                        id="update-button" <?= is_disabled('update', TRUE) ?>
                        type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
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
                    }

                    $('#msg-details').html(response.msg);
                },
                error: function (xhr, ajaxOptions, thrownError) {
					<?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        }
    });

    $("#member_id").select2({
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
</script>
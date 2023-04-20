<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'id="form" class="form-horizontal"') ?>
<div class="row">
    <div class="col-md-4">
        <h2 class="sub-header block-title"><?= i('fa fa-list') ?> <?= lang('manage_form_field') ?></h2>
    </div>
    <div class="col-md-8 text-right">
		<?php if (CONTROLLER_FUNCTION == 'update_fields'): ?>
			<?= prev_next_item('left', CONTROLLER_METHOD, $row['prev']) ?>
		<?php endif; ?>
        <a href="<?= admin_url(CONTROLLER_CLASS . '/update_fields/' . $row['form_id']) ?>" class="btn btn-primary"><i
                    class="fa fa-undo"></i> <span class="hidden-xs"><?= lang('back_to_form') ?></span></a>
        <a href="<?= admin_url(CONTROLLER_CLASS . '/view') ?>" class="btn btn-primary"><i class="fa fa-search"></i>
            <span class="hidden-xs"><?= lang('view_forms') ?></span></a>
		<?php if (CONTROLLER_FUNCTION == 'update_fields'): ?>
			<?= prev_next_item('right', CONTROLLER_METHOD, $row['next']) ?>
		<?php endif; ?>
    </div>
</div>
<hr/>
<div class="box-info">
    <h3><?= lang('form_field_details') ?></h3>
    <span><?= lang('form_field_description') ?></span>
    <hr/>
    <ul class="nav nav-tabs text-capitalize" role="tablist">
        <li class="active"><a href="#name<?= $row['field_id'] ?>" role="tab"
                              data-toggle="tab"><?= lang('language_names') ?></a></li>
		<?php if ($row['custom'] == 1): ?>
            <li><a href="#config<?= $row['field_id'] ?>" role="tab" data-toggle="tab"><?= lang('config') ?></a></li>
		<?php endif; ?>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="name<?= $row['field_id'] ?>">
            <hr/>
			<?php foreach ($row['names'] as $g): ?>
                <div class="form-group">
                    <label for="field_name"
                           class="r col-md-3 control-label"><?= i('flag-' . $g['image']) ?> <?= $g['name'] ?> </label>

                    <div class="r col-md-3">
                        <input type="text" name="names[<?= $g['language_id'] ?>][field_name]"
                               value="<?= $g['field_name'] ?>" placeholder="<?= lang('field_name') ?>"
                               class="form-control"/>
                    </div>
                    <div class="r col-md-4">
                        <input type="text" name="names[<?= $g['language_id'] ?>][field_description]"
                               value="<?= $g['field_description'] ?>" placeholder="<?= lang('field_description') ?>"
                               class="form-control"/>
                    </div>
                </div>
                <hr/>
			<?php endforeach; ?>
        </div>
		<?php if ($row['custom'] == 1): ?>
        <div class="tab-pane" id="config<?= $row['field_id'] ?>">
            <hr/>
            <div class="form-group">
                <label for="form_field" class="col-md-3 control-label"><?= lang('form_field') ?></label>

                <div class="col-md-5">
					<?= form_input('form_field', set_value('form_field', $row['form_field']), 'class="form-control"') ?>

                </div>
            </div>
			<?php if ($row['form_id'] < 3): ?>
                <hr/>
                <div class="form-group">
                    <label for="sub_form" class="col-md-3 control-label"><?= lang('sub_form') ?></label>

                    <div class="col-md-5">
						<?= form_dropdown('sub_form', options('sub_form'), $row['sub_form'], 'class="form-control"') ?>
                    </div>
                </div>
			<?php endif; ?>
            <hr/>
            <div class="form-group">
                <label for="field_type" class="col-md-3 control-label"><?= lang('field_type') ?></label>

                <div class="col-md-5">
					<?= form_dropdown('field_type', options('form_field_types'), $row['field_type'], 'id="field_type" class="form-control"') ?>
                </div>
            </div>
            <div id="options" <?php if ($row['field_type'] != 'select' && $row['field_type'] != 'radio'): ?> style="display:none"<?php endif; ?>>
                <hr/>
                <div class="form-group">
                    <label for="field_options" class="col-md-3 control-label"><?= lang('field_options') ?> - <?=lang('one_per_line')?></label>

                    <div class="col-md-5">
                            <textarea name="field_options" rows="4" id="field_options" class="form-control"
                                      placeholder="<?= lang('enter_one_option_per_line') ?>"><?= $row['field_options'] ?></textarea>
                    </div>
                </div>
            </div>
            <div id="validation" <?php if ($row['field_type'] != 'text'): ?> style="display:none"<?php endif; ?>>
                <hr/>
                <div class="form-group">
                    <label for="field_validation"
                           class="col-md-3 control-label"><?= lang('field_validation') ?></label>

                    <div class="col-md-5">
						<?= form_input('field_validation', set_value('field_validation', $row['field_validation']), ' class="form-control"') ?>

                    </div>
                </div>
            </div>
			<?php endif; ?>
            <div id="hidden" <?php if ($row['field_type'] != 'hidden'): ?> style="display:none"<?php endif; ?>>
                <hr/>
                <div class="form-group">
                    <label for="field_value"
                           class="col-md-3 control-label"><?= lang('default_field_value') ?></label>

                    <div class="col-md-5">
				        <?= form_input('field_value', set_value('field_value', $row['field_value']), ' class="form-control"') ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-fixed-bottom  save-changes">
        <div class="container text-right">
            <div class="row">
                <div class="col-md-12">
                    <button id="save-changes"
                            class="btn btn-info navbar-btn block-phone" <?= is_disabled('update', TRUE) ?>
                            type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
                </div>
            </div>
        </div>
    </nav>
	<?= form_hidden('field_id', $id) ?>
	<?php if (empty($row['custom']) ): ?>
		<?= form_hidden('form_field', $row['form_field']) ?>
		<?= form_hidden('field_type', $row['field_type']) ?>
	<?php endif; ?>
	<?= form_close() ?>
    <a name="custom"></a>
    <script>
        $("select#field_type").change(function () {
            $("select#field_type option:selected").each(function () {
                if ($(this).attr("value") == "select") {
                    $("#options").show(300);
                    $("#validation").hide(300);
                    $("#hidden").hide(300);
                }
                else if ($(this).attr("value") == "radio") {
                    $("#options").show(300);
                    $("#validation").hide(300);
                    $("#hidden").hide(300);
                }
                else if ($(this).attr("value") == "text") {
                    $("#options").hide(300);
                    $("#validation").show(300);
                    $("#hidden").show(300);
                }
                else if ($(this).attr("value") == "textarea") {
                    $("#options").hide(300);
                    $("#validation").show(300);
                    $("#hidden").show(300);
                }
                else if ($(this).attr("value") == "hidden") {
                    $("#options").hide(300);
                    $("#validation").hide(300);
                    $("#hidden").show(300);
                }
                else {
                    $("#options").hide(300);
                    $("#validation").hide(300);
                    $("#hidden").hide(300);
                }
            });
        }).change();

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
    </script>
<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'role="form" id="form" class="dropzone form-horizontal"') ?>
<div class="row">
    <div class="col-md-7">
        <h2 class="sub-header block-title"><?= i('fa fa-pencil') ?> <?= lang(CONTROLLER_FUNCTION . '_' . singular(CONTROLLER_CLASS)) ?></h2>
    </div>
    <div class="col-md-5 text-right">
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('left', CONTROLLER_METHOD, $row['prev']) ?>
            <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $id) ?>" data-toggle="modal"
               data-target="#confirm-delete" href="#"
               class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
                        class="hidden-xs"><?= lang('delete') ?></span></a>
		<?php endif; ?>
        <a href="<?= admin_url(CONTROLLER_CLASS) ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
                    class="hidden-xs"><?= lang('view_slide_shows') ?></span></a>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('right', CONTROLLER_METHOD, $row['next']) ?>
		<?php endif; ?>
    </div>
</div>
<hr/>
<div class="box-info">
    <div class="hidden-xs">
        <h3 class="text-capitalize"><?= lang('slide_show_details') ?></h3>
        <span><?= lang('configure_slide_show_details_description') ?></span>
    </div>
    <hr/>
    <ul class="nav nav-tabs text-capitalize" role="tablist">
        <li class="active"><a href="#config" data-toggle="tab"><?= lang('config') ?></a></li>
        <li><a href="#text" data-toggle="tab"><?= lang('text') ?></a></li>
        <li><a href="#options" data-toggle="tab"><?= lang('options') ?></a></li>
    </ul>
    <br/>
    <div class="tab-content">
        <div class="tab-pane fade in active" id="config">
            <hr/>
            <div class="form-group">
                <label class="col-md-3 control-label"><?= lang('name') ?></label>
                <div class="r col-md-5">
					<?= form_input('name', set_value('name', $row['name']), 'class="form-control"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
				<?= lang('start_date', 'start_date', array('class' => 'col-md-3 control-label')) ?>
                <div class="r col-md-2">
                    <div class="input-group">
                        <input type="text" name="start_date"
                               value="<?= set_value('start_date', $row['start_date']) ?>"
                               class="form-control datepicker-input required"/>
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>
				<?= lang('expires_on', 'end_date', array('class' => 'col-md-1 control-label')) ?>
                <div class="r col-md-2">
                    <div class="input-group">
                        <input type="text" name="end_date"
                               value="<?= set_value('end_date', $row['end_date']) ?>"
                               class="form-control datepicker-input required"/>
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>
            </div>
            <hr/>
            <div class="form-group">
                <label class="col-md-3 control-label"><?= lang('background_image') ?></label>
                <div class="col-md-5">
                    <div class="input-group">
                        <input type="text" name="background_image"
                               value="<?= set_value('background_image', $row['background_image']) ?>" id="1"
                               class="form-control"/>
                        <span class="input-group-btn">
				        <a class='iframe block-phone btn btn-default text-center'
                           href="<?= base_url() ?>filemanager/dialog.php?fldr=backgrounds&type=1&akey=<?= $file_manager_key ?>&field_id=1">
						<?= i('fa fa-upload') ?> <?= lang('select_image') ?></a>
				      </span>
                    </div>

                </div>
            </div>
	        <?php if ($row['type'] == 'simple'): ?>
            <hr/>
            <div class="form-group">
                <label class="col-md-3 control-label"><?= lang('background_color') ?></label>
                <div class="col-md-2">
                    <input type="text" name="background_color"
                           value="<?= set_value('background_color', $row['background_color']) ?>"
                           class="form-control colors"/>
                </div>
                <label class="col-md-1 control-label"><?= lang('text_color') ?></label>
                <div class="col-md-2">
                    <input type="text" name="text_color"
                           value="<?= set_value('text_color', $row['text_color']) ?>"
                           class="form-control colors"/>
                </div>
            </div>
            <hr/>
            <div class="form-group">
                <label class="col-md-3 control-label"><?= lang('text_position') ?></label>

                <div class="col-md-2">
					<?= form_dropdown('position', options('position'), $row['position'], 'class="form-control" id="position"') ?>
                </div>
                <label class="col-md-1 control-label"><?= lang('action_url') ?></label>
                <div class="col-md-2">
                    <input type="text" name="action_url"
                           value="<?= set_value('action_url', $row['action_url']) ?>" placeholder="<?= site_url() ?>"
                           class="form-control"/>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <div class="tab-pane fade in" id="text">
            <ul class="nav nav-tabs text-capitalize" role="tablist">
				<?php foreach ($row['lang'] as $v): ?>
                    <li <?php if ($v['language_id'] == $sts_site_default_language): ?> class="active" <?php endif; ?>>
                        <a href="#<?= $v['image'] ?>" data-toggle="tab"><?= i('flag-' . $v['image']) ?>
                            <span class="visible-lg"><?= $v['name'] ?></span></a>
                    </li>
				<?php endforeach; ?>
            </ul>
            <br/>
            <div class="tab-content">
				<?php foreach ($row['lang'] as $v): ?>
                    <div class="tab-pane fade in <?php if ($v['language_id'] == $sts_site_default_language): ?> active <?php endif; ?>"
                         id="<?= $v['image'] ?>">
						<?php if ($row['type'] == 'simple'): ?>
                            <div class="form-group">
                                <label class="col-md-2 control-label"><?= lang('headline') ?></label>
                                <div class="col-md-8">
                                    <input type="text" name="lang[<?= $v['language_id'] ?>][headline]"
                                           value="<?= set_value('headline', $v['headline']) ?>" class="form-control"/>
                                </div>
                            </div>
						<?php endif; ?>
                        <hr/>
                        <div class="form-group">
                            <label
                                    class="col-md-2 control-label"><?= lang('text') ?></label>

                            <div class="col-md-8">
                                <textarea name="lang[<?= $v['language_id'] ?>][slide_show]" rows="10"
                                          class="<?php if ($row['type'] == 'simple'): ?>editor<?php endif; ?> form-control"><?= set_value('slide_show', $v['slide_show']) ?></textarea>
                            </div>
                        </div>
                        <?php if ($row['type'] == 'simple'): ?>
                        <hr/>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?= lang('button_text') ?></label>
                            <div class="col-md-4">
                                <input type="text" name="lang[<?= $v['language_id'] ?>][button_text]"
                                       value="<?= set_value('button_text', $v['button_text']) ?>"
                                       placeholder="<?= lang('click_here') ?>"
                                       class="form-control"/>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
					<?= form_hidden('lang[' . $v['language_id'] . '][language]', $v['name']) ?>
				<?php endforeach; ?>
            </div>
        </div>
        <div class="tab-pane fade in" id="options">
            <hr/>
            <?php if ($row['type'] == 'advanced'): ?>
            <div class="form-group">
                <label class="col-md-3 control-label"><?= lang('meta_data') ?></label>
                <div class="r col-md-5">
                    <textarea name="meta_data" rows="8"
                              class="form-control"><?= set_value('meta_data', $row['meta_data']) ?></textarea>
                </div>
            </div>
            <hr/>
            <div class="form-group">
                <label class="col-md-3 control-label"><?= lang('footer_data') ?></label>
                <div class="r col-md-5">
                    <textarea name="footer_data" rows="8"
                              class="form-control"><?= set_value('footer_data', $row['footer_data']) ?></textarea>
                </div>
            </div>
            <hr/>
            <?php endif; ?>
            <div class="form-group">
                <label class="col-md-3 control-label"><?= lang('sort_order') ?></label>
                <div class="r col-md-1">
                    <input type="number" name="sort_order" value="<?=set_value('sort_order', $row['sort_order'])?>" class="form-control digits" />
                </div>
            </div>
            <hr/>
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
</div>
<?php if (CONTROLLER_FUNCTION == 'update'): ?>
	<?= form_hidden('slide_id', $id) ?>
	<?= form_hidden('type', $row['type']) ?>
<?php endif; ?>
<?= form_close() ?>

<script>

	<?=html_editor('init', 'public_text')?>

    $("#form").validate({
        ignore: "",
        submitHandler: function (form) {
            tinyMCE.triggerSave();
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
                    }

                    $('#msg-details').html(response.msg);
                },
                error: function (xhr, ajaxOptions, thrownError) {
					<?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        }
    });

    $(document).ready(function () {

        $('.colors').each(function () {
            $(this).minicolors({
                control: $(this).attr('data-control') || 'hue',
                defaultValue: $(this).attr('data-defaultValue') || '',
                format: $(this).attr('data-format') || 'rgb',
                keywords: $(this).attr('data-keywords') || '',
                inline: $(this).attr('data-inline') === 'true',
                letterCase: $(this).attr('data-letterCase') || 'lowercase',
                opacity: $(this).attr('data-opacity') || true,
                position: $(this).attr('data-position') || 'bottom left',
                swatches: $(this).attr('data-swatches') ? $(this).attr('data-swatches').split('|') : [],
                change: function (value, opacity) {
                    if (!value) return;
                    if (opacity) value += ', ' + opacity;
                    if (typeof console === 'object') {
                        console.log(value);
                    }
                },
                theme: 'bootstrap'
            });

        });

    });
</script>

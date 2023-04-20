<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
<div class="row">
    <div class="col-md-7">
		<?= generate_sub_headline(CONTROLLER_CLASS, 'fa-edit', '') ?>
    </div>
    <div class="col-md-5 text-right">
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('left', CONTROLLER_METHOD, $row['prev']) ?>
			<?php if ($id > 1): ?>
                <a data-href="<?= admin_url(TBL_PRODUCTS_ATTRIBUTES . '/delete/' . $row['attribute_id']) ?>"
                   data-toggle="modal" data-target="#confirm-delete" href="#"
                   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
                            class="hidden-xs"><?= lang('delete') ?></span></a>
			<?php endif; ?>
		<?php endif; ?>

        <a href="<?= admin_url(TBL_PRODUCTS_ATTRIBUTES . '/view') ?>" class="btn btn-primary"><?= i('fa fa-search') ?>
            <span class="hidden-xs"><?= lang('view_attributes') ?></span></a>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('right', CONTROLLER_METHOD, $row['next']) ?>
		<?php endif; ?>
    </div>
</div>
<hr/>
<?php if (empty($row['option_values'])): ?>
	<?php $n = 1;?>
<?php else: ?>
	<?php $n = count($row['option_values']) ?>
<?php endif; ?>
<div class="row">
    <div class="col-md-12">
        <div class="box-info">
            <ul class="nav nav-tabs text-capitalize" role="tablist">
                <li class="active"><a href="#name" role="tab" data-toggle="tab"><?= lang('name') ?></a></li>
                <li><a href="#options" role="tab" data-toggle="tab"><?= lang('options') ?></a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="name">
                    <div class="hidden-xs">
                        <h3 class="text-capitalize" id="title-<?=$sts_site_default_language?>"> <?= $row['attribute_name'] ?></h3>
                        <span><?= lang('set_locale_specific_descriptions_each_tab') ?></span>
                    </div>
                    <hr/>
                    <ul class="nav nav-tabs text-capitalize" role="tablist">
						<?php foreach ($row['lang'] as $v): ?>
                            <li <?php if ($v['language_id'] == $sts_site_default_language): ?> class="active" <?php endif; ?>>
                                <a href="#<?= $v['image'] ?>" data-toggle="tab"><?= i('flag-' . $v['image']) ?> <span
                                            class="visible-lg"><?= $v['name'] ?></span></a>
                            </li>
						<?php endforeach; ?>
                    </ul>
                    <hr/>
                    <div class="tab-content">
						<?php foreach ($row['lang'] as $k => $v): ?>
                            <div
                                    class="tab-pane <?php if ($v['language_id'] == $sts_site_default_language): ?> active <?php endif; ?>"
                                    id="<?= $v['image'] ?>">
                                <div class="form-group">
                                    <label for="attribute_name"
                                           class="col-sm-3 control-label"><?= lang('attribute_name') ?></label>

                                    <div class="col-lg-5">
										<?php if (count($row['lang']) > 1 && $v['language_id'] == $sts_site_default_language): ?>
                                            <div class="input-group">
												<?= form_input('lang[' . $v['language_id'] . '][attribute_name]', set_value('attribute_name', $v['attribute_name']), 'id="name-' . $v['language_id'] . '" class="' . css_error('attribute_name') . ' form-control "') ?>
                                                <span class="input-group-addon">
				                                    <a href="javascript:void(0)"
                                                       id="copy_fields"><?= i('fa fa-clone') ?> <?= lang('copy_field') ?></a></span>
                                            </div>
										<?php else: ?>
											<?= form_input('lang[' . $v['language_id'] . '][attribute_name]', set_value('attribute_name', $v['attribute_name']), 'id="name-' . $v['language_id'] . '" class="' . css_error('attribute_name') . ' form-control "') ?>

										<?php endif; ?>
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group">
                                    <label for="description"
                                           class="col-sm-3 control-label"><?= lang('description') ?></label>

                                    <div class="col-lg-7">
										<?= form_textarea('lang[' . $v['language_id'] . '][description]', set_value('description', $v['description']), 'class="editor ' . css_error('description') . ' form-control "') ?>
                                    </div>
                                </div>
                                <hr/>
                            </div>
							<?= form_hidden('lang[' . $v['language_id'] . '][language]', $v['name']) ?>
						<?php endforeach; ?>
                    </div>

                </div>
                <div class="tab-pane" id="options">
                    <hr/>
                    <div class="form-group">
                        <label for="sort_order" class="col-sm-3 control-label"><?= lang('sort_order') ?></label>

                        <div class="col-lg-5">
							<?= form_input('sort_order', set_value('sort_order', $row['sort_order']), 'class="' . css_error('sort_order') . ' form-control "') ?>
                        </div>
                    </div>
                    <hr/>
                    <div class="form-group">
                        <label for="attribute_sku" class="col-sm-3 control-label"><?= lang('attribute_sku') ?></label>
                        <div class="col-lg-5">
							<?= form_input('attribute_sku', set_value('attribute_sku', $row['attribute_sku']), 'class="' . css_error('attribute_sku') . ' form-control "') ?>
                        </div>
                    </div>
                    <hr/>
                    <div class="form-group">
                        <label for="attribute_type" class="col-sm-3 control-label"><?= lang('attribute_type') ?></label>

                        <div class="col-lg-5">
							<?= form_dropdown('attribute_type', options('attribute_types'), $row['attribute_type'], 'id="attribute_type" class="form-control"') ?>
                        </div>
                    </div>
                    <hr/>
                    <div id="auto-update-image">
                        <div class="form-group">
                            <label for="auto_update_image"
                                   class="col-sm-3 control-label"><?= lang('auto_update_image') ?></label>

                            <div class="col-lg-5">
								<?= form_dropdown('auto_update_image', options('yes_no'), $row['auto_update_image'], 'id="auto_update_image" class="form-control"') ?>
                            </div>
                        </div>
                        <hr/>
                    </div>
                    <div id="select-options" <?php if (!check_attribute_type($row['attribute_type'])): ?>
                         style="display:none"> <?php endif; ?>
                        <div class="box-info">
                            <span class="pull-right"><a
                                        href="javascript:add_options(<?= $n ?>)"
                                        class="btn btn-primary <?= is_disabled('update', TRUE) ?>"><?= i('fa fa-plus') ?> <?= lang('add_new_option') ?></a></span>
                            <h5 class="text-capitalize"><?= lang('configure_options_for_attribute') ?></h5>
                            <hr/>
                            <div id="option-div">
                                <div class="row text-capitalize">
                                    <div class="col-lg-1"><?= tb_header('default_image', '', FALSE) ?></div>
                                    <div class="col-lg-3"><?= tb_header('option_name', '', FALSE) ?></div>
                                    <div class="col-lg-6"><?= tb_header('short_description', '', FALSE) ?></div>
                                    <div class="col-lg-1"><?= tb_header('sort_order', '', FALSE) ?></div>
                                    <div class="col-lg-1"></div>
                                </div>
                                <hr/>
								<?php $i = 1; ?>
								<?php if (!empty($row['option_values'])): ?>
									<?php foreach ($row['option_values'] as $k => $v): ?>
                                        <div id="imagediv-<?= $i ?>">
                                            <div class="row">
                                                <div class="col-lg-1 text-center">
                                                    <a class='iframe'
                                                       href="<?= base_url() ?>filemanager/dialog.php?type=1&akey=<?= $file_manager_key ?>&field_id=<?= $i ?>&fldr=products">
														<?php if (!empty($v['path'])): ?>
                                                            <img id="image-<?= $i ?>" src="<?= $v['path'] ?>"
                                                                 class="att-photo img-thumbnail img-responsive"/>
														<?php else: ?>
                                                            <img id="image-<?= $i ?>"
                                                                 src="<?= base_url(TPL_DEFAULT_PRODUCT_PHOTO) ?>"
                                                                 class="att-photo img-thumbnail img-responsive"/>
														<?php endif; ?>
                                                    </a>
                                                    <input type="hidden" name="option_values[<?= $k ?>][path]"
                                                           value="<?= $v['path'] ?>" id="<?= $i ?>"/>
                                                    <input type="hidden" name="option_values[<?= $k ?>][option_id]"
                                                           value="<?= $v['option_id'] ?>"/>

                                                </div>
                                                <div class="col-lg-3">
													<?php foreach ($v['lang'] as $a => $b): ?>
                                                        <div class="input-group">
                                                            <span
                                                                    class="input-group-addon"><?= i('flag-' . $b['image']) ?></span>
															<?= form_input('option_values[' . $k . '][lang][' . $a . '][option_name]', set_value('option_values[' . $k . '][lang][' . $a . '][name]', $b['option_name']), 'class="form-control "') ?>

                                                        </div>
                                                        <br/>
													<?php endforeach; ?>
                                                </div>
                                                <div class="col-lg-6">
													<?php foreach ($v['lang'] as $a => $b): ?>
														<?= form_input('option_values[' . $k . '][lang][' . $a . '][option_description]', set_value('option_values[' . $k . '][lang][' . $a . '][option_description]', $b['option_description']), 'class="' . css_error('date_available') . ' form-control"') ?>
                                                        <input type="hidden"
                                                               name="option_values[<?= $k ?>][lang][<?= $a ?>][att_option_name_id]"
                                                               value="<?= $b['att_option_name_id'] ?>"/>
                                                        <input type="hidden"
                                                               name="option_values[<?= $k ?>][lang][<?= $a ?>][language]"
                                                               value="<?= $b['lang_name'] ?>"/>
                                                        <input type="hidden"
                                                               name="option_values[<?= $k ?>][lang][<?= $a ?>][language_id]"
                                                               value="<?= $b['language_id'] ?>"/>
                                                        <br/>
													<?php endforeach; ?>

                                                </div>
                                                <div class="col-lg-1">
                                                    <input type="number" placeholder="<?= lang('sort_order') ?>"
                                                           name="option_values[<?= $k ?>][sort_order]"
                                                           value="<?= $v['sort_order'] ?>" id="<?= $i ?>"
                                                           class="form-control"/>
                                                </div>
                                                <div class="col-lg-1 text-right">
													<?php if ($k != '0'): ?>
                                                        <a href="javascript:remove_div('#imagediv-<?= $i ?>')"
                                                           class="btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> </a>
													<?php endif; ?>
                                                </div>
                                            </div>
                                            <hr/>
                                        </div>
										<?php $i++; ?>
									<?php endforeach; ?>
								<?php else: ?>
                                    <div id="imagediv-0">
                                        <div class="row">
                                            <div class="col-lg-1 text-center">
                                                <a class='iframe'
                                                   href="<?= base_url() ?>filemanager/dialog.php?type=1&akey=<?= $file_manager_key ?>&field_id=0&fldr=products">
                                                    <img id="image-0" src="<?= base_url(TPL_DEFAULT_PRODUCT_PHOTO) ?>"
                                                         class="att-photo img-thumbnail img-responsive"/>
                                                </a>
                                                <input type="hidden" name="new_option[0][path]" id="0"/>
                                            </div>
                                            <div class="col-lg-3">
												<?php foreach ($languages as $b): ?>
                                                    <div class="input-group">
                                                        <span class="input-group-addon">
	                                                        <?= i('flag-' . $b['image']) ?></span>
														<?= form_input('new_option[0][lang][' . $b['language_id'] . '][option_name]', '', 'class="form-control "') ?>
                                                        <input type="hidden"
                                                               name="new_option[0][lang][<?= $b['language_id'] ?>][language_id]"
                                                               value="<?= $b['language_id'] ?>"/>

                                                    </div>
                                                    <br/>
												<?php endforeach; ?>
                                            </div>
                                            <div class="col-lg-6">
												<?php foreach ($languages as $b): ?>
													<?= form_input('new_option[0][lang][' . $b['language_id'] . '][option_description]', '', 'class="form-control"') ?>
                                                    <br/>
												<?php endforeach; ?>

                                            </div>
                                            <div class="col-lg-1">
                                                <input type="number" placeholder="<?= lang('sort_order') ?>"
                                                       name="new_option[0][sort_order]" value="1" id="0"
                                                       class="form-control"/>
                                            </div>
                                            <div class="col-lg-1 text-right">
                                                <a href="javascript:remove_div('#imagediv-0')"
                                                   class="btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> </a>
                                            </div>
                                        </div>
                                        <hr/>
                                    </div>
								<?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if (CONTROLLER_FUNCTION == 'update'): ?>
	<?= form_hidden('attribute_id', $id) ?>
<?php endif; ?>
<nav class="navbar navbar-fixed-bottom save-changes">
    <div class="container text-right">
        <div class="row">
            <div class="col-lg-12">
				<?php if (CONTROLLER_FUNCTION == 'create'): ?>
                    <input type="submit" name="redir_button" value="<?= lang('save_add_another') ?>"
                           class="btn btn-success navbar-btn block-phone"/>
				<?php endif; ?>
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
    var next_id = <?=$n + 1?>;
    var next_option = <?=$n + 1?>;

    $("select#attribute_type").change(function () {
        $("select#attribute_type option:selected").each(function () {
            $("#auto-update-image").hide(300);
            if ($(this).attr("value") == "select") {
                $("#select-options").show(300);
                $("#auto-update-image").show(300);
            }
            else if ($(this).attr("value") == "image") {
                $("#select-options").show(300);
                $("#auto-update-image").show(300);
            }
            else if ($(this).attr("value") == "radio") {
                $("#select-options").show(300);
            }
            else {
                $("#select-options").hide(300);
            }
        });
    }).change();

    function add_options(image_id) {

        var html = ' <div id="imagediv-' + next_id + '"><div class="row">';
        html += '   <div class="col-lg-1 text-center">';
        html += '       <a class="iframe" href="<?=base_url()?>filemanager/dialog.php?type=1&akey=<?=$file_manager_key?>&field_id=' + next_id + '&fldr=products">';
        html += '       <img id="image-' + next_id + '"" src="<?=base_url(TPL_DEFAULT_PRODUCT_PHOTO)?>" class="att-photo img-thumbnail img-responsive" />';
        html += '       </a>';
        html += '       <input type="hidden" name="new_option[' + next_id + '][path]" id="' + next_id + '" />';
        html += '   </div>';
        html += '   <div class="col-lg-3">';
		<?php foreach ($languages as $b): ?>
        html += '       <div class="input-group">';
        html += '           <span class="input-group-addon"><?=i('flag-' . $b['image'])?></span>';
        html += '           <input type="text" name="new_option[' + next_id + '][lang][<?=$b['language_id']?>][option_name]" class="form-control " />';
        html += '       </div>';
        html += '       <br />';
		<?php endforeach; ?>
        html += '   </div>';
        html += '   <div class="col-lg-6">';
		<?php foreach ($languages as $b): ?>
        html += '       <input type="text" name="new_option[' + next_id + '][lang][<?=$b['language_id']?>][option_description]" class="form-control" />';
        html += '       <input type="hidden" name="new_option[' + next_id + '][lang][<?=$b['language_id']?>][language_id]"  value="<?= $b['language_id'] ?>" />';
        html += '   <br />';
		<?php endforeach; ?>
        html += '   </div>';
        html += '   <div class="col-lg-1">';
        html += '   <input type="number" placeholder="<?=lang('sort_order')?>" name="new_option[' + next_id + '][sort_order]" value="' + next_id + '" id="<?=$i?>" class="form-control"/>';
        html += '   </div>';
        html += '   <div class="col-lg-1 text-right">';
        html += '       <a href="javascript:remove_div(\'#imagediv-' + next_id + '\')" class="btn btn-danger <?=is_disabled('delete')?>"><?=i('fa fa-trash-o')?> </a>';
        html += '   </div>';
        html += '</div>';
        html += '<hr />';
        html += '</div>';

        $('#option-div').append(html);
        $(".iframe").colorbox({iframe: true, width: "50%", height: "50%"});
        next_id++;
    }

	<?php if (count($row['lang']) > 1): ?>
    $('#copy_fields').click(function () {
		<?php foreach ($row['lang'] as $k => $v): ?>
		<?php if ($v['language_id'] != $sts_site_default_language): ?>
        $('#name-<?=$v['language_id']?>').val($('#name-<?=$sts_site_default_language?>').val());
		<?php endif; ?>
		<?php endforeach; ?>
    });
	<?php endif; ?>

    $("#form").validate({
        ignore: "",
        submitHandler: function (form) {
            $.ajax({
                url: '<?=current_url()?>',
                type: 'POST',
                dataType: 'json',
                data: $('#form').serialize(),
                success: function (response) {
                    if (response.type == 'success') {
                        $('.alert-danger').remove();
                        $('.form-control').removeClass('error');

                        if (response.redirect) {
                            location.href = response.redirect;
                        }
                        else {
                            $('#response').html('<?=alert('success')?>');

                            n = $($('#name-<?=$sts_site_default_language?>')).val();
                            $('#title-<?=$sts_site_default_language?>').html(n);

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
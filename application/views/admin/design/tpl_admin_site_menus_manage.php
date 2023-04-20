<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'id="form" class="form-horizontal"') ?>
<div class="row">
    <div class="col-md-4">
        <h2 class="sub-header block-title"><?= i('fa fa-pencil') ?> <?= lang(CONTROLLER_CLASS) ?>
            - <?= $row['menu_name'] ?></h2>
    </div>
    <div class="col-md-8 text-right">
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('left', CONTROLLER_METHOD, $row['prev']) ?>
			<?php if ($id > 1): ?>
                <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $id) ?>" data-toggle="modal"
                   data-target="#confirm-delete" href="#"
                   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><i class="fa fa-trash-o"></i> <span
                            class="hidden-xs"><?= lang('delete') ?></span></a>
			<?php endif; ?>
		<?php endif; ?>
        <a href="<?= admin_url(CONTROLLER_CLASS . '/view') ?>" class="btn btn-primary"><i class="fa fa-search"></i>
            <span class="hidden-xs"><?= lang('view_site_menus') ?></span></a>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('right', CONTROLLER_METHOD, $row['next']) ?>
		<?php endif; ?>
    </div>
</div>
<hr/>
<div class="box-info">
    <div class="row">
        <div class="col-md-4">
            <h3 class="text-capitalize"><?= lang('main_menu_links') ?></h3>
            <hr/>
            <ul id="sortable" class="menu-links">
				<?php if (!empty($row['menu_links'])): ?>
					<?php foreach ($row['menu_links'] as $k => $v): ?>
                        <li id="menu_<?= $v['menu_link_id'] ?>"
                            class="ui-state-default linkdiv-<?= $v['menu_link_id'] ?>">
                            <div class="btn-group">
                                <a href="javascript:remove_link('.linkdiv-', '<?= $v['menu_link_id'] ?>')"
                                   class="btn btn-danger">
                                    <i class="fa fa-minus-circle"></i>
                                </a>
                                <a href="javascript:show_config('<?= $v['menu_link_id'] ?>')" class="btn btn-primary">
									<?= $v['names']['0']['menu_link_name'] ?>
                                </a>
								<?php if ($v['menu_link_type'] == 'dropdown'): ?>
                                    <a href="<?= admin_url(CONTROLLER_CLASS . '/add_link/' . $row['menu_id'] . '/' . $v['menu_link_id']) ?>"
                                       class="btn btn-primary">
										<?= i('fa fa-plus') ?>
                                    </a>
								<?php elseif ($v['menu_link_type'] == 'mega'): ?>
                                    <button type="button" class="disabled btn btn-primary">
                                        M
                                    </button>
								<?php
								else: ?>
                                    <button type="button" class="disabled btn btn-primary">
										<?= i('fa fa-link') ?>
                                    </button>
								<?php endif; ?>
                                <span class="btn btn-primary handle visible-lg <?= is_disabled('update', TRUE) ?>"><i
                                            class="fa fa-sort"></i></span>
                            </div>
                            <ul>
								<?php if (!empty($v['sub_menu_links'])): ?>
									<?php foreach ($v['sub_menu_links'] as $a => $b): ?>

                                        <li id="sublinkdiv-<?= $b['menu_link_id'] ?>">
                                            <div class="btn-group">
                                                <a href="javascript:remove_link('#sublinkdiv-', '<?= $b['menu_link_id'] ?>')"
                                                   class="btn btn-danger">
                                                    <i class="fa fa-minus-circle"></i>
                                                </a>
                                                <a href="javascript:show_config('<?= $b['menu_link_id'] ?>')"
                                                   class="btn btn-primary">
													<?= $b['names']['0']['menu_link_name'] ?>
                                                </a>
                                                <a href="javascript:show_config('<?= $b['menu_link_id'] ?>')"
                                                   class="btn btn-primary">
													<?= i('fa fa-pencil') ?>
                                                </a>
                                            </div>
                                        </li>
									<?php endforeach; ?>
								<?php endif; ?>
                            </ul>
                        </li>
					<?php endforeach; ?>
				<?php endif; ?>

            </ul>
            <ul class="menu-links">
                <li>
                    <a href="<?= admin_url(CONTROLLER_CLASS . '/add_link/' . $row['menu_id'] . '/0') ?>"
                       class="btn btn-primary">
						<?= i('fa fa-plus') ?> <?= lang('add_top_link') ?>
                    </a>
                </li>
            </ul>
        </div>
        <div class="col-md-8">
			<?php if (!empty($row['menu_links'])): ?>
				<?php foreach ($row['menu_links'] as $k => $v): ?>
                    <div class="menu-config linkdiv-<?= $v['menu_link_id'] ?>" id="menu-<?= $v['menu_link_id'] ?>">
                        <h3 class="text-capitalize"><?= $v['names']['0']['menu_link_name'] ?></h3>
                        <hr/>
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <ul class="nav nav-tabs text-capitalize" role="tablist">
                                    <li class="active"><a href="#config<?= $v['menu_link_id'] ?>" role="tab"
                                                          data-toggle="tab"><?= lang('config') ?></a></li>
                                    <li><a href="#name<?= $v['menu_link_id'] ?>" role="tab"
                                           data-toggle="tab"><?= lang('name') ?></a></li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="config<?= $v['menu_link_id'] ?>">
                                        <h5 class="text-capitalize"><?= lang('menu_item_configuration') ?></h5>
                                        <hr/>
                                        <div class="form-group">
                                            <label for="menu_link_status"
                                                   class="col-md-4 control-label"><?= lang('show') ?></label>

                                            <div class="col-md-5">
												<?= form_dropdown('menu[' . $v['menu_link_id'] . '][menu_link_status]', options('yes_no'), $v['menu_link_status'], 'class="form-control"') ?>
                                            </div>
                                        </div>
                                        <hr/>
                                        <div class="form-group">
                                            <label for="menu_link_type"
                                                   class="col-md-4 control-label"><?= lang('link_type') ?></label>

                                            <div class="col-md-5">
												<?= form_dropdown('menu[' . $v['menu_link_id'] . '][menu_link_type]', options('menu_link_type'), $v['menu_link_type'], 'id="select_link_type-' . $v['menu_link_id'] . '"class="form-control" onchange="change_link_type(\'' . $v['menu_link_id'] . '\')"') ?>
                                            </div>
                                        </div>

                                        <div id="general_type-<?= $v['menu_link_id'] ?>" class="link_type"
                                             style="display: <?php if ($v['menu_link_type'] == 'link'): ?>block<?php else: ?>none<?php endif; ?>">
                                            <hr/>
                                            <div class="form-group">
                                                <label for="menu_link"
                                                       class="col-md-4 control-label"><?= lang('link_url') ?></label>

                                                <div class="col-md-5">
													<?= form_input('menu[' . $v['menu_link_id'] . '][menu_link]', set_value('menu_link', $v['menu_link']), 'placeholder="' . site_url() . '" class="' . css_error('menu_link') . ' form-control"') ?>
                                                </div>
                                            </div>
                                            <hr/>
                                            <div class="form-group">
                                                <label for="menu_options"
                                                       class="col-md-4 control-label"><?= lang('css_and_js_options') ?></label>

                                                <div class="col-md-5">
													<?= form_input('menu[' . $v['menu_link_id'] . '][menu_options]', set_value('menu_options', $v['menu_options']), 'placeholder="target=_blank" class="' . css_error('menu_options') . ' form-control"') ?>
                                                </div>
                                            </div>
                                        </div>
                                        <hr/>
                                        <div id="mega_type-<?= $v['menu_link_id'] ?>" class="link_type"
                                             style="display: <?php if ($v['menu_link_type'] == 'mega'): ?>block<?php else: ?>none<?php endif; ?>">
                                            <div class="form-group">
                                                <label for="menu_link"
                                                       class="col-md-4 control-label"><?= lang('mega_menu_html_code') ?></label>

                                                <div class="col-md-8">
													<?= form_textarea('menu[' . $v['menu_link_id'] . '][menu_code]', set_value('menu_code', $v['menu_code'], FALSE), 'class="' . css_error('menu_code') . ' form-control"') ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-8  col-md-offset-4">
                                                    <p class="alert alert-warning"><?= i('fa fa-info-circle') ?> <?= lang('mega_menu_advanced_html_info') ?></p>
                                                </div>
                                            </div>
                                            <hr/>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="name<?= $v['menu_link_id'] ?>">
										<?php if (!empty($v['names'])): ?>
                                            <h5 class="text-capitalize"><?= lang('menu_translation') ?></h5>
                                            <hr/>
											<?php foreach ($v['names'] as $t): ?>
                                                <div class="form-group">
                                                    <label for="menu_link_name"
                                                           class="col-md-4 control-label"><?= i('flag-' . $t['image']) ?> <?= $t['name'] ?> </label>

                                                    <div class="col-md-5">
                                                        <input type="text" name="names[<?= $t['link_name_id'] ?>]"
                                                               value="<?= $t['menu_link_name'] ?>"
                                                               class="form-control"/>
                                                    </div>
                                                </div>
                                                <hr/>
											<?php endforeach; ?>
										<?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
					<?php if (!empty($v['sub_menu_links'])): ?>
						<?php foreach ($v['sub_menu_links'] as $a => $b): ?>
                            <div class="menu-config sublinkdiv-<?= $b['menu_link_id'] ?>"
                                 id="menu-<?= $b['menu_link_id'] ?>"
							     <?php if (uri(5) == $b['menu_link_id']): ?>style="display:block"<?php endif; ?>>
                                <h3 class="text-capitalize"><?= $b['names']['0']['menu_link_name'] ?></h3>
                                <hr/>
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <ul class="nav nav-tabs text-capitalize" role="tablist">
                                            <li class="active"><a href="#config<?= $b['menu_link_id'] ?>" role="tab"
                                                                  data-toggle="tab"><?= lang('config') ?></a></li>
                                            <li><a href="#name<?= $b['menu_link_id'] ?>" role="tab"
                                                   data-toggle="tab"><?= lang('name') ?></a></li>
                                        </ul>
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="config<?= $b['menu_link_id'] ?>">
                                                <h5 class="text-capitalize"><?= lang('menu_item_configuration') ?></h5>
                                                <hr/>
                                                <div class="form-group">
                                                    <label for="menu_link_status"
                                                           class="col-md-4 control-label"><?= lang('show') ?></label>

                                                    <div class="col-md-5">
														<?= form_dropdown('menu[' . $b['menu_link_id'] . '][menu_link_status]', options('yes_no'), $b['menu_link_status'], 'class="form-control"') ?>
														<?= form_hidden('menu[' . $b['menu_link_id'] . '][menu_link_type]', $b['menu_link_type']) ?>
                                                    </div>
                                                </div>
                                                <hr/>
                                                <div id="general_type-<?= $b['menu_link_id'] ?>" class="link_type">
                                                    <div class="form-group">
                                                        <label for="menu_link"
                                                               class="col-md-4 control-label"><?= lang('link_url') ?></label>

                                                        <div class="col-md-5">
															<?= form_input('menu[' . $b['menu_link_id'] . '][menu_link]', set_value('menu_link', $b['menu_link']), 'placeholder="' . site_url() . '" class="' . css_error('menu_link') . ' form-control"') ?>
                                                        </div>
                                                    </div>
                                                    <hr/>
                                                    <div class="form-group">
                                                        <label for="menu_options"
                                                               class="col-md-4 control-label"><?= lang('css_and_js_options') ?></label>

                                                        <div class="col-md-5">
															<?= form_input('menu[' . $b['menu_link_id'] . '][menu_options]', set_value('menu_options', $b['menu_options']), 'class="' . css_error('menu_options') . ' form-control"') ?>
                                                        </div>
                                                    </div>
                                                    <hr/>
                                                    <div class="form-group">
                                                        <label for="menu_options"
                                                               class="col-md-4 control-label"><?= lang('sort_order') ?></label>

                                                        <div class="col-md-5">
                                                            <input type="number"
                                                                   name="menu[<?= $b['menu_link_id'] ?>][menu_sort_order]"
                                                                   value="<?= $b['menu_sort_order'] ?>"
                                                                   class="form-control"/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="name<?= $b['menu_link_id'] ?>">
												<?php if (!empty($b['names'])): ?>
                                                    <h5 class="text-capitalize"><?= lang('menu_translation') ?></h5>
                                                    <hr/>
													<?php foreach ($b['names'] as $g): ?>
                                                        <div class="form-group">
                                                            <label for="menu_link_name"
                                                                   class="col-md-4 control-label"><?= i('flag-' . $g['image']) ?> <?= $g['name'] ?> </label>

                                                            <div class="col-md-5">
                                                                <input type="text"
                                                                       name="names[<?= $g['link_name_id'] ?>]"
                                                                       value="<?= $g['menu_link_name'] ?>"
                                                                       class="form-control"/>
                                                            </div>
                                                        </div>
                                                        <hr/>
													<?php endforeach; ?>
												<?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
						<?php endforeach; ?>
					<?php endif; ?>
				<?php endforeach; ?>
			<?php endif; ?>
            <div class="edit-msg" <?php if ((uri(5))): ?>style="display:none"<?php endif; ?>>
                <h3 class="text-capitalize"><?= lang('menu_link_configuration') ?></h3>
                <hr/>
                <p class="alert alert-warning"><?= i('fa fa-info-circle') ?> <?= lang('click_on_link_to_edit_its_settings') ?></p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h3><?= lang('some_internal_links_to_use') ?></h3>
            <hr />
            <div class="row">
                <div class="col-md-4">
                    <ul class="list-group">
		                <?php foreach ($links[1] as $v): ?>
                            <li class="list-group-item"><strong><?=lang('site_menu_' . $v)?></strong> - {{site_url}}<?=$v?></li>
		                <?php endforeach ?>
                    </ul>
                </div>
                <div class="col-md-4">
                    <ul class="list-group">
			            <?php foreach ($links[2] as $v): ?>
                            <li class="list-group-item"><strong><?=lang('site_menu_' . $v)?></strong> - {{site_url}}<?=$v?></li>
			            <?php endforeach ?>
                    </ul>
                </div>
                <div class="col-md-4">
                    <ul class="list-group">
			            <?php foreach ($links[3] as $v): ?>
                            <li class="list-group-item"><strong><?=lang('site_menu_' . $v)?></strong> - {{site_url}}<?=$v?></li>
			            <?php endforeach ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<nav class="navbar navbar-fixed-bottom  save-changes">
    <div class="container text-right">
        <div class="row">
            <div class="col-md-12">
                <button id="save-changes" class="btn btn-info navbar-btn block-phone" <?= is_disabled('update', TRUE) ?>
                        type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
            </div>
        </div>
    </div>
</nav>
<?= form_hidden('submit', '1') ?>
<?= form_close() ?>
<div id="update"></div>
<script>
    function change_link_type(id) {
        $("select#select_link_type-" + id + " option:selected").each(function () {
            if ($(this).attr("value") == "link") {
                $("#general_type-" + id).show(300);
                $("#mega_type-" + id).hide(300);
            }
            else if ($(this).attr("value") == "dropdown") {
                $("#general_type-" + id).hide(300);
                $("#mega_type-" + id).hide(300);
            }
            else {
                $("#mega_type-" + id).show(300);
                $("#general_type-" + id).hide(300);
            }
        });
    }

    function show_config(id) {
        $(".edit-msg").hide();
        $(".menu-config").hide();
        $("#menu-" + id).show();
    }

    $(function () {
        $('#sortable').sortable({
            handle: '.handle',
            placeholder: "ui-state-highlight",
            update: function () {
                var order = $('#sortable').sortable('serialize');
                $("#update").load("<?=admin_url(CONTROLLER_CLASS . '/update_order/')?>?" + order);
            }
        });
    });

    function remove_link(c, id) {
        $.ajax({
            url: '<?=admin_url(CONTROLLER_CLASS . '/delete_link')?>/' + id,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.type == 'success') {
                    remove_div(c + id);
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
                    console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        }
    });
</script>
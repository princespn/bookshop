<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-8">
        <div class="input-group text-capitalize">
			<?= generate_sub_headline('themes', 'fa-list') ?>
        </div>
    </div>
    <div class="col-md-4 text-right">
		<?php if (config_item('more_themes_url')): ?>
            <a href="<?= $more_themes_url ?>" class="btn btn-info"
               target="_blank"><?= i('fa fa-download') ?> <?= lang('get_more_themes') ?></a>
		<?php endif; ?>
        <button data-toggle="collapse" data-target="#add_block"
                class="btn btn-primary"><?= i('fa fa-upload') ?> <?= lang('install_theme') ?></button>
    </div>
</div>
<hr/>
<div id="add_block" class="row capitalize collapse">
	<?= form_open_multipart(admin_url(CONTROLLER_CLASS . '/unzip'), 'class="form-horizontal"') ?>
    <div class="col-lg-12">
        <br/>
        <div class="panel panel-default">
            <div class="panel-body">
                <h4 class="text-capitalize"><?= lang('theme_zip_file') ?></h4>
                <div class="alert alert-warning text-warning">
					<?= i('fa fa-info-circle') ?> <?= lang('zip_not_allowed') ?>
					<?= lang('upload_your_theme_folder_to') ?> <strong><?= PUBPATH . '/themes/site' ?></strong>
                </div>
                <hr/>
                <div class="col-md-2">
                    <div>
                        <button type="button" id="button-upload" class="btn btn-default btn-block">
                            <span id="wait"><?= i('fa fa-upload') ?> <?= lang('file_upload') ?></span>
                        </button>
                        <small class="text-muted">
                            * <?= lang('allowed_file_types') ?>
                            : <span class="text-danger">zip</span>
                        </small>
                    </div>
                </div>
                <input type="hidden" name="zip_file" id="zip_file">
                <button class="btn btn-primary"
                        type="submit"><?= i('fa fa-caret-right') ?> <?= lang('proceed') ?></button>
            </div>
        </div>
    </div>
    </form>
</div>
<?= form_open_multipart('', 'class="form-horizontal" id="form"') ?>
<?php if (empty($rows)): ?>
	<?= tpl_no_values() ?>
<?php else: ?>
    <div class="box-info">
        <ul class="nav nav-tabs text-capitalize">
            <li class="active"><a href="#themes" data-toggle="tab"><?= lang('manage_themes') ?></a></li>
            <li><a href="#logo" data-toggle="tab"><?= lang('upload_logo') ?></a></li>
            <li><a href="#palette" data-toggle="tab"><?= lang('color_palette') ?></a></li>
            <li><a href="#css" data-toggle="tab"><?= lang('custom_css') ?></a></li>
        </ul>
        <div class="tab-content">
            <div id="themes" class="tab-pane fade in active">
                <h3 class="text-capitalize"><?= lang('select_themes') ?></h3>
                <hr/>
                <div class="gallery-wrap">
					<?php foreach ($rows as $v): ?>
						<?php if ($layout_design_site_theme == $v['theme_folder']): ?>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="thumbnail">
                                    <div class="gallery-item">
										<?php if (!empty($v['theme_image'])): ?>
											<?php if (file_exists(PUBPATH . '/themes/site/' . $v['theme_folder'] . '/' . $v['theme_image'])): ?>
                                                <img
                                                        src="<?= base_url('themes/site/' . $v['theme_folder'] . '/' . $v['theme_image']) ?>"
                                                        alt="preview" class="theme-preview img-responsive"/>
											<?php else: ?>
                                                <img src="<?= base_url('images/no-photo.jpg') ?>" alt="preview"
                                                     class="img-responsive"/>
											<?php endif; ?>
										<?php endif; ?>

                                        <div class="img-title">
                                            <h5>
                                                <i class="fa fa-check"></i> <?= lang('current_theme') ?>
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="caption text-center">
                                        <a class=" disabled btn btn-success btn-block"><i
                                                    class="fa fa-check"></i> <?= lang('current_theme') ?></a>
                                    </div>
                                </div>
                            </div>
						<?php endif; ?>
					<?php endforeach; ?>
					<?php foreach ($rows as $v): ?>
						<?php if ($layout_design_site_theme != $v['theme_folder']): ?>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="thumbnail">
                                    <div class="gallery-item">
										<?php if (!empty($v['theme_image'])): ?>
											<?php if (file_exists(PUBPATH . '/themes/site/' . $v['theme_folder'] . '/' . $v['theme_image'])): ?>
                                                <img src="<?= base_url('themes/site/' . $v['theme_folder'] . '/' . $v['theme_image']) ?>"
                                                     alt="preview" class="theme-preview img-responsive"/>
											<?php else: ?>
                                                <img src="<?= base_url('images/no-photo.jpg') ?>" alt="preview"
                                                     class="img-responsive"/>
											<?php endif; ?>
										<?php endif; ?>
                                        <div class="img-title">
                                            <h5>
                                                <a href="<?= base_url('themes/site/' . $v['theme_folder'] . '/' . $v['theme_image']) ?>"
                                                   class="theme-img btn btn-primary"><i
                                                            class="fa fa-download"></i> <?= lang('preview') ?></a>
                                                <a href="<?= admin_url() ?>themes/set_theme/<?= $v['theme_folder'] ?>"
                                                   class="btn btn-success"><i
                                                            class="fa fa-check"></i> <?= lang('activate') ?></a>
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="caption text-center">
                                        <strong><a href="<?= admin_url() ?>themes/set_theme/<?= $v['theme_folder'] ?>"
                                                   class="btn btn-default btn-block"><?= i('fa fa-caret-right') ?> <?= $v['theme_folder'] ?></a></strong>
                                    </div>
                                </div>
                            </div>
						<?php endif; ?>
					<?php endforeach; ?>
                </div>
            </div>
            <div id="logo" class="tab-pane fade in">
                <div class="row">
                    <div class="col-md-9">
                        <h3 class="text-capitalize"><?= lang('upload_your_own_logo') ?></h3>
                    </div>
                    <div class="col-md-3 text-right">
	                    <?php if (config_item('logo_design_url')): ?>
                            <h3><a href="<?= $logo_design_url ?>" target="_blank"
                               class="btn btn-sm btn-success"><?= i('fa fa-') ?> <?= lang('get_a_custom_logo') ?></a>
                            </h3>
	                    <?php endif; ?>
                    </div>
                </div>
                <hr/>
                <div class="form-group">
					<?= lang('current_logo', 'layout_design_site_logo', array('class' => 'col-sm-3 control-label')) ?>
                    <div class="col-lg-5">
                        <img src="<?= $layout_design_site_logo ?>" id="image-1"/>
                    </div>
                </div>
                <hr/>
                <div class="form-group">
					<?= lang('logo_path', 'layout_design_site_logo', array('class' => 'col-sm-3 control-label')) ?>
                    <div class="col-lg-5">
                        <div class="input-group">
                            <input type="text" name="layout_design_site_logo" value="<?= $layout_design_site_logo ?>"
                                   id="1"
                                   class="form-control"/>
                            <span class="input-group-addon">
                                    <a href="<?= base_url() ?>filemanager/dialog.php?type=1&akey=<?= $file_manager_key ?>&field_id=1"
                                       class="iframe cboxElement"><?= i('fa fa-camera') ?> <?= lang('upload_logo') ?></a></span>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="form-group">
					<?= lang('favicon', 'favicon', array('class' => 'col-sm-3 control-label')) ?>
                    <div class="col-lg-5">
                        <img src="<?= $layout_design_site_favicon ?>" id="image-2"/>
                    </div>
                </div>
                <hr/>
                <div class="form-group">
					<?= lang('favicon_path', 'layout_design_site_favicon', array('class' => 'col-sm-3 control-label')) ?>
                    <div class="col-lg-5">
                        <div class="input-group">
                            <input type="text" name="layout_design_site_favicon"
                                   value="<?= $layout_design_site_favicon ?>"
                                   id="2"
                                   class="form-control"/>
                            <span class="input-group-addon">
                                    <a href="<?= base_url() ?>filemanager/dialog.php?type=1&akey=<?= $file_manager_key ?>&field_id=2"
                                       class="iframe cboxElement"><?= i('fa fa-camera') ?> <?= lang('upload_favicon') ?></a></span>
                        </div>
                    </div>
                </div>
				<?php if (config_enabled('custom_admin_logo')): ?>
                <hr/>
                    <div class="form-group">
						<?= lang('admin_login_logo', 'layout_design_site_logo', array('class' => 'col-sm-3 control-label')) ?>
                        <div class="col-lg-5">
                            <img src="<?= $layout_design_admin_logo ?>" id="image-3"/>
                        </div>
                    </div>
                    <hr/>
                    <div class="form-group">
						<?= lang('admin_login_logo', 'layout_design_admin_logo', array('class' => 'col-sm-3 control-label')) ?>
                        <div class="col-lg-5">
                            <div class="input-group">
                                <input type="text" name="layout_design_admin_logo"
                                       value="<?= $layout_design_admin_logo ?>"
                                       id="3"
                                       class="form-control"/>
                                <span class="input-group-addon">
                                    <a href="<?= base_url() ?>filemanager/dialog.php?type=1&akey=<?= $file_manager_key ?>&field_id=3"
                                       class="iframe cboxElement"><?= i('fa fa-camera') ?> <?= lang('upload_admin_logo') ?></a></span>
                            </div>
                        </div>
                    </div>
				<?php endif; ?>
            </div>
            <div id="palette" class="tab-pane fade in">
                <div class="col-lg-6">
                    <h3 class="text-capitalize"><?= lang('bootstrap_theme_palette') ?></h3>
                    <hr/>
                    <div class="row">
						<?php $colors = array('primary', 'secondary', 'success', 'danger') ?>
						<?php foreach ($colors as $c): ?>
                            <div class="col-lg-3">
                                <a id="<?= $c ?>-button" class="btn btn-block <?= $c ?>-color"
                                   style="color: white; background-color: <?= config_item('layout_design_theme_' . $c . '_button') ?>"><?= lang($c) ?></a>
                                <br/>
                                <div class="input-group">
                                    <input id="<?= $c ?>-input" type="text" class="form-control"
                                           name="layout_design_theme_<?= $c ?>_button"
                                           value="<?= config_item('layout_design_theme_' . $c . '_button') ?>""/>
                                    <span class="input-group-addon"><input type="text" id="<?= $c ?>"/></span>
                                </div>
                            </div>
						<?php endforeach; ?>
                    </div>
                    <br/>
                    <div class="row">
						<?php $colors = array('warning', 'info', 'light', 'dark') ?>
						<?php foreach ($colors as $c): ?>
                            <div class="col-lg-3">
                                <a id="<?= $c ?>-button" class="btn btn-block <?= $c ?>-color"
                                   style="color: <?php if ($c == 'light'): ?>#666666<?php else: ?>white<?php endif; ?>; background-color: <?= config_item('layout_design_theme_' . $c . '_button') ?>"><?= lang($c) ?></a>
                                <br/>
                                <div class="input-group">
                                    <input id="<?= $c ?>-input" type="text" class="form-control"
                                           name="layout_design_theme_<?= $c ?>_button"
                                           value="<?= config_item('layout_design_theme_' . $c . '_button') ?>""/>
                                    <span class="input-group-addon"><input type="text" id="<?= $c ?>"/></span>
                                </div>
                            </div>
						<?php endforeach; ?>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-lg-3">
                            <a id="background-button" class="btn btn-block background-color"
                               style=" color: <?= $layout_design_theme_text_color ?>; background-color: <?= $layout_design_theme_background_color ?>"><?= lang('background_color') ?></a>
                            <br/>
                            <div class="input-group">
                                <input id="background-input" type="text" class="form-control"
                                       name="layout_design_theme_background_color"
                                       value="<?= $layout_design_theme_background_color ?>"/>
                                <span class="input-group-addon"><input type="text" id="background"/></span>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <a id="text-button" class="btn btn-block text-color"
                               style=" background-color: <?= $layout_design_theme_background_color ?>; color: <?= $layout_design_theme_text_color ?>"><?= lang('text_color') ?></a>
                            <br/>
                            <div class="input-group">
                                <input id="text-input" type="text" class="form-control"
                                       name="layout_design_theme_text_color"
                                       value="<?= $layout_design_theme_text_color ?>"/>
                                <span class="input-group-addon"><input type="text" id="text"/></span>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <a id="footer-button" class="btn btn-block footer-color"
                               style="color: <?= $layout_design_theme_footertext_color ?>; background-color: <?= $layout_design_theme_footer_color ?>"><?= lang('footer_color') ?></a>
                            <br/>
                            <div class="input-group">
                                <input id="footer-input" type="text" class="form-control"
                                       name="layout_design_theme_footer_color"
                                       value="<?= $layout_design_theme_footer_color ?>"/>
                                <span class="input-group-addon"><input type="text" id="footer"/></span>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <a id="footertext-button" class="btn btn-block footertext-color"
                               style=" background-color: <?= $layout_design_theme_footer_color ?>; color: <?= $layout_design_theme_footertext_color ?>"><?= lang('footer_text') ?></a>
                            <br/>
                            <div class="input-group">
                                <input id="footertext-input" type="text" class="form-control"
                                       name="layout_design_theme_footertext_color"
                                       value="<?= $layout_design_theme_footertext_color ?>"/>
                                <span class="input-group-addon"><input type="text" id="footertext"/></span>
                            </div>
                        </div>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-lg-3">
                            <a id="pageheader-button" class="btn btn-block pageheader-color"
                               style="color: <?= $layout_design_theme_pageheadertext_color ?>; background-color: <?= $layout_design_theme_pageheader_color ?>"><?= lang('pageheader_color') ?></a>
                            <br/>
                            <div class="input-group">
                                <input id="pageheader-input" type="text" class="form-control"
                                       name="layout_design_theme_pageheader_color"
                                       value="<?= $layout_design_theme_pageheader_color ?>"/>
                                <span class="input-group-addon"><input type="text" id="pageheader"/></span>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <a id="pageheadertext-button" class="btn btn-block pageheadertext-color"
                               style="background-color: <?= $layout_design_theme_pageheader_color ?>;color: <?= $layout_design_theme_pageheadertext_color ?>"><?= lang('pageheadertext_color') ?></a>
                            <br/>
                            <div class="input-group">
                                <input id="pageheadertext-input" type="text" class="form-control"
                                       name="layout_design_theme_pageheadertext_color"
                                       value="<?= $layout_design_theme_pageheadertext_color ?>"/>
                                <span class="input-group-addon"><input type="text" id="pageheadertext"/></span>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <a id="topnav-button" class="btn btn-block topnav-color"
                               style="color: <?= $layout_design_theme_topnavtext_color ?>; background-color: <?= $layout_design_theme_topnav_color ?>"><?= lang('topnav_color') ?></a>
                            <br/>
                            <div class="input-group">
                                <input id="topnav-input" type="text" class="form-control"
                                       name="layout_design_theme_topnav_color"
                                       value="<?= $layout_design_theme_topnav_color ?>"/>
                                <span class="input-group-addon"><input type="text" id="topnav"/></span>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <a id="topnavtext-button" class="btn btn-block topnavtext-color"
                               style="background-color: <?= $layout_design_theme_topnav_color ?>; color: <?= $layout_design_theme_topnavtext_color ?>"><?= lang('topnavtext_color') ?></a>
                            <br/>
                            <div class="input-group">
                                <input id="topnavtext-input" type="text" class="form-control"
                                       name="layout_design_theme_topnavtext_color"
                                       value="<?= $layout_design_theme_topnavtext_color ?>"/>
                                <span class="input-group-addon"><input type="text" id="topnavtext"/></span>
                            </div>
                        </div>
                    </div>
                    <br />
                    <div class="row">
                        <div class="col-lg-3">
                            <a id="link-button" class="btn btn-block link-color"
                               style="color: <?= $layout_design_theme_link_color ?>"><?= lang('link_color') ?></a>
                            <br/>
                            <div class="input-group">
                                <input id="link-input" type="text" class="form-control"
                                       name="layout_design_theme_link_color"
                                       value="<?= $layout_design_theme_link_color ?>"/>
                                <span class="input-group-addon"><input type="text" id="link"/></span>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <a id="enable-gradients" class="btn btn-block"
                               style="color: #333"><?= lang('use_gradients') ?></a>
                            <br/>
							<?= form_dropdown('layout_design_theme_enable_gradients', options('yes_no'), config_option('layout_design_theme_enable_gradients'), 'class="form-control"') ?>
                        </div>
                        <div class="col-lg-2">
                            <a id="enable-rounded" class="btn btn-block"
                               style="color: #333"><?= lang('round_corners') ?></a>
                            <br/>
							<?= form_dropdown('layout_design_theme_enable_rounded', options('yes_no'), config_option('layout_design_theme_enable_rounded'), 'class="form-control"') ?>
                        </div>
                        <div class="col-lg-3">
                            <a id="border-radius" class="btn btn-block"
                               style="color: #333"><?= lang('border_radius') ?></a>
                            <br/>
                            <div class="input-group">
                                <input id="border_radius" type="text" class="form-control required number"
                                       name="layout_design_theme_border_radius"
                                       value="<?= $layout_design_theme_border_radius ?>"/>
                                <span class="input-group-addon">rem</span>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <a id="yi-contrast" class="btn btn-block" style="color: #333"><?= lang('yi_contrast') ?></a>
                            <br/>
                            <input id="yi_contrast" type="number" class="form-control required digits"
                                   name="layout_design_theme_yi_contrast"
                                   value="<?= $layout_design_theme_yi_contrast ?>"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <h3 class="text-capitalize"><?= lang('header_font') ?></h3>
                            <hr/>
                            <select id="header_font" class="form-control select2" name="layout_design_theme_header_font"
                                    onchange="updateregion('header')">
								<?php foreach (array_merge(config_item('regular_fonts'), config_item('google_fonts')) as $font): ?>
                                    <option style="font-size: 20px; font-family: '<?= $font ?>'" value="<?= $font ?>"
									        <?php if (config_item('layout_design_theme_header_font') == $font): ?>selected<?php endif; ?>><?= $font ?></option>
								<?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-lg-6">
                            <h3 class="text-capitalize"><?= lang('base_font') ?></h3>
                            <hr/>
                            <select id="base_font" class="form-control select2" name="layout_design_theme_base_font"
                                    onchange="updateregion('base')">
								<?php foreach (array_merge(config_item('regular_fonts'), config_item('google_fonts')) as $font): ?>
                                    <option style="font-size: 20px; font-family: '<?= $font ?>'" value="<?= $font ?>"
									        <?php if (config_item('layout_design_theme_base_font') == $font): ?>selected<?php endif; ?>><?= $font ?></option>
								<?php endforeach; ?>
                            </select></div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <hr/>
                            <div id="text-sample" style="background-color: <?=$layout_design_theme_background_color?>; padding: 10px">
                            <h1 id="header-font-p" style="color: <?=$layout_design_theme_text_color?>; font-family: <?= $layout_design_theme_header_font ?>">This Is
                                A Sample Headline</h1>
                            <p id="base-font-p"
                               style="color: <?=$layout_design_theme_text_color?>; font-size: 16px; font-family: <?= $layout_design_theme_base_font ?>">
                                Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum
                                has been the industry's
                                standard dummy text ever since the 1500s, when an unknown printer took a galley of type
                                and scrambled it to make
                                a type specimen book. It has survived not only five centuries, but also the leap into
                                electronic typesetting,
                                remaining essentially unchanged. It was popularised in the 1960s with the release of
                                Letraset sheets containing
                                Lorem Ipsum passages, and more recently with desktop publishing software like Aldus
                                PageMaker including versions
                                of Lorem Ipsum.
                            </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
					<?php if (!empty($palettes)): ?>
                        <h3 class="text-capitalize"><?= lang('click_to_load_palette') ?></h3>
                        <hr/>
						<?php foreach ($palettes as $k => $v): ?>
                            <div class="row" onclick="loadPalette('<?= $k ?>')" style="cursor: pointer">
                                <div class="col-lg-1"></div>
                                <div class="col-lg-9" style="background-color: <?= $v['background'] ?>">
                                    <p>
                                    <div style="color: <?= $v['text'] ?>; font-size: 20px; font-family:<?= $v['header_font'] ?>"><?= $v['header_font'] ?></div>
                                    <a class="btn" style="background-color: <?= $v['primary'] ?>"></a>
                                    <a class="btn" style="background-color: <?= $v['secondary'] ?>"></a>
                                    <a class="btn" style="background-color: <?= $v['success'] ?>"></a>
                                    <a class="btn" style="background-color: <?= $v['danger'] ?>"></a>
                                    <a class="btn" style="background-color: <?= $v['warning'] ?>"></a>
                                    <a class="btn" style="background-color: <?= $v['info'] ?>"></a>
                                    <a class="btn" style="background-color: <?= $v['light'] ?>"></a>
                                    <a class="btn" style="background-color: <?= $v['dark'] ?>"></a>
                                    </p>
                                    <p style="color: <?= $v['text'] ?>; font-family: <?= $v['base_font'] ?>">Lorem Ipsum
                                        is simply dummy text
                                        of the printing and typesetting industry. Lorem Ipsum has been the industry's
                                        standard dummy text ever since the 1500s,</p>

                                </div>
                                <div class="col-lg-2 text-center ">
                                    <a href="<?= admin_url(CONTROLLER_CLASS . '/load_palette/' . $k) ?>"
                                       class="btn btn-sm btn-default" style="margin-top: 2em"><?= lang('load') ?></a>
                                </div>
                            </div>
                            <hr/>
						<?php endforeach; ?>
					<?php endif; ?>
                </div>
            </div>
            <div id="css" class="tab-pane fade in">
                <div class="col-lg-12">
                    <h3 class="text-capitalize"><?= lang('custom_css') ?></h3>
                    <hr/>
                    <textarea name="layout_design_custom_css" rows="25" id="code2"
                              class="form-control"><?= set_value('layout_design_custom_css', $layout_design_custom_css) ?></textarea>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<nav class="navbar navbar-fixed-bottom  save-changes">
    <div class="container text-right">
        <div class="row">
            <div class="col-lg-12">
                <button class="btn btn-info navbar-btn block-phone <?= is_disabled('update', TRUE) ?>"
                        type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
            </div>
        </div>
    </div>
</nav>
<?= form_close() ?>
<script src="<?= base_url('js/colorpicker/spectrum.js') ?>"></script>
<script>
    function loadPalette(type) {
        window.location.href = '<?=admin_url(CONTROLLER_CLASS . '/load_palette/')?>' + type;
    }

    function updateregion(type) {
        font = $('#' + type + '_font').val();
        $('#' + type + '-font-p').css('font-family', font);

    }
	<?php $colors = array('primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark')?>
	<?php foreach ($colors as $c): ?>
    $('#<?=$c?>').spectrum({
        showPaletteOnly: true,
        showPalette: true,
        togglePaletteOnly: true,
        togglePaletteMoreText: 'more',
        togglePaletteLessText: 'less',
        color: '<?=config_item('layout_design_theme_' . $c . '_button')?>',
        palette: [
            ["#000", "#444", "#666", "#999", "#ccc", "#eee", "#f3f3f3", "#fff"],
            ["#f00", "#f90", "#ff0", "#0f0", "#0ff", "#00f", "#90f", "#f0f"],
            ["#f4cccc", "#fce5cd", "#fff2cc", "#d9ead3", "#d0e0e3", "#cfe2f3", "#d9d2e9", "#ead1dc"],
            ["#ea9999", "#f9cb9c", "#ffe599", "#b6d7a8", "#a2c4c9", "#9fc5e8", "#b4a7d6", "#d5a6bd"],
            ["#e06666", "#f6b26b", "#ffd966", "#93c47d", "#76a5af", "#6fa8dc", "#8e7cc3", "#c27ba0"],
            ["#c00", "#e69138", "#f1c232", "#6aa84f", "#45818e", "#3d85c6", "#674ea7", "#a64d79"],
            ["#900", "#b45f06", "#bf9000", "#38761d", "#134f5c", "#0b5394", "#351c75", "#741b47"],
            ["#600", "#783f04", "#7f6000", "#274e13", "#0c343d", "#073763", "#20124d", "#4c1130"]
        ],
        move: function (color) {
            $('#<?=$c?>-button').css('backgroundColor', color.toHexString());
            $('#<?=$c?>-input').val(color.toHexString());
        }
    });
	<?php endforeach; ?>
	<?php $b = array('background', 'text', 'link', 'footer', 'footertext',
                     'pageheader', 'pageheadertext', 'topnav', 'topnavtext'); ?>
	<?php foreach ($b as $c): ?>
    $('#<?=$c?>').spectrum({
        showPaletteOnly: true,
        showPalette: true,
        togglePaletteOnly: true,
        togglePaletteMoreText: 'more',
        togglePaletteLessText: 'less',
        color: '<?=config_item('layout_design_theme_' . $c . '_color')?>',
        palette: [
            ["#000", "#444", "#666", "#999", "#ccc", "#eee", "#f3f3f3", "#fff"],
            ["#f00", "#f90", "#ff0", "#0f0", "#0ff", "#00f", "#90f", "#f0f"],
            ["#f4cccc", "#fce5cd", "#fff2cc", "#d9ead3", "#d0e0e3", "#cfe2f3", "#d9d2e9", "#ead1dc"],
            ["#ea9999", "#f9cb9c", "#ffe599", "#b6d7a8", "#a2c4c9", "#9fc5e8", "#b4a7d6", "#d5a6bd"],
            ["#e06666", "#f6b26b", "#ffd966", "#93c47d", "#76a5af", "#6fa8dc", "#8e7cc3", "#c27ba0"],
            ["#c00", "#e69138", "#f1c232", "#6aa84f", "#45818e", "#3d85c6", "#674ea7", "#a64d79"],
            ["#900", "#b45f06", "#bf9000", "#38761d", "#134f5c", "#0b5394", "#351c75", "#741b47"],
            ["#600", "#783f04", "#7f6000", "#274e13", "#0c343d", "#073763", "#20124d", "#4c1130"]
        ],
        move: function (color) {
            $('#<?=$c?>-button').css('<?php if ($c == 'background' || $c == 'footer' || $c == 'pageheader' || $c == 'topnav'): ?>backgroundColor<?php else: ?>color<?php endif; ?>', color.toHexString());
            $('#<?=$c?>-input').val(color.toHexString());
            <?php if ($c == 'background'): ?>
            $('#text-sample').css('backgroundColor', color.toHexString());
            <?php endif; ?>
	        <?php if ($c == 'text'): ?>
            $('#base-font-p').css('color', color.toHexString());
            $('#header-font-p').css('color', color.toHexString());
	        <?php endif; ?>
        }
    });
	<?php endforeach; ?>
    function copyCss(id) {
        $('#code2').val($('#' + id).val());
    }
	<?php if (!is_disabled('update', TRUE)): ?>
    $('#button-upload').on('click', function () {
        var node = this;
        $('#form-upload').remove();
        $('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="files" /><input type="hidden" name="<?=$csrf_token?>" value="<?=$csrf_value?>" /></form>');
        $('#form-upload input[name=\'files\']').trigger('click');

        timer = setInterval(function () {
            if ($('#form-upload input[name=\'files\']').val() != '') {
                clearInterval(timer);
                $.ajax({
                    url: '<?=admin_url(CONTROLLER_CLASS . '/upload/')?>',
                    type: 'post',
                    dataType: 'json',
                    data: new FormData($('#form-upload')[0]),
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function () {
                        $('#wait').html('<?=i('fa fa-spinner fa-spin')?> <?=lang('uploading_please_wait')?>');
                        $("#button-upload").attr("disabled", 'disabled');
                    },
                    success: function (data) {
                        if (data['type'] == 'error') {
                            $('#response').html('<?=alert('error')?>');
                        } else if (data['type'] == 'success') {
                            $('#zip_file').attr('value', data['file_name']);
                            $('#response').html('<?=alert('success')?>');
                        }
                        $('#msg-details').html(data['msg']);
                    },
                    complete: function () {
                        $('#wait').html('<?=i('fa fa-upload')?> <?=lang('file_upload')?>');
                        $("#button-upload").removeAttr("disabled", 'disabled');
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
						<?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    }
                });
            }
        }, 500);
    });
	<?php endif; ?>
    $(document).ready(function () {
        $('.gallery-item').hover(function () {
            $(this).find('.img-title').fadeIn(300);
        }, function () {
            $(this).find('.img-title').fadeOut(100);
        });

        $(".theme-img").colorbox({rel: 'theme-img'});
    });

    $("#form").validate();
</script>
<script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js"></script>
<script>
    WebFont.load({
        google: {
            families: [<?php foreach (config_item('google_fonts') as $font): ?>'<?=$font?>',<?php endforeach;?>]
        }
    });
</script>
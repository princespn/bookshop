<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (empty($row)): ?>
	<?= tpl_no_values('', '', 'no_records_found') ?>
<?php else: ?>
    <div class="row">
        <div class="col-lg-8 col-md-6">
            <h2 class="sub-header block-title"><?= i('fa fa-pencil') ?>
				<?= $row['product_name'] ?> - <?= lang($row['product_type']) ?></h2>
        </div>
        <div class="col-lg-4 col-md-6 text-right">
			<?php if (CONTROLLER_FUNCTION == 'update'): ?>
				<?= prev_next_item('left', CONTROLLER_METHOD, $row['prev']) ?>
				<?php if ($id > 1): ?>
                    <a data-href="<?= admin_url(TBL_PRODUCTS . '/delete/' . $id) ?>" data-toggle="modal"
                       data-target="#confirm-delete" href="#"
                       FALSE class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?>
                        <span
                                class="hidden-xs"><?= lang('delete') ?></span></a>
				<?php endif; ?>
			<?php endif; ?>
            <a href="<?= admin_url(TBL_PRODUCTS . '/clone_product/' . $id) ?>"
               class="btn btn-info"><?= i('fa fa-copy') ?> <?= lang('clone') ?></a>
            <a href="<?= admin_url(TBL_PRODUCTS) ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
                        class="hidden-xs"><?= lang('view_products') ?></span></a>
			<?php if (CONTROLLER_FUNCTION == 'update'): ?>
				<?= prev_next_item('right', CONTROLLER_METHOD, $row['next']) ?>
			<?php endif; ?>
        </div>
    </div>
    <hr/>
    <div class="row">
    <div class="col-lg-12">
		<?= form_open_multipart('', 'role="form" id="form" class="form-horizontal"') ?>
        <div class="box-info">
            <ul class="nav nav-tabs resp-tabs text-capitalize" role="tablist">
                <li class="active"><a href="#availability" role="tab"
                                      data-toggle="tab"><?= lang('availability') ?></a>
                </li>
                <li><a href="#pricing" role="tab" data-toggle="tab"><?= lang('pricing') ?></a></li>
                <li><a href="#name" role="tab" data-toggle="tab"><?= lang('description') ?></a></li>
                <li><a href="#specs" role="tab" data-toggle="tab"><?= lang('specifications') ?></a></li>
                <li><a href="#media" role="tab" data-toggle="tab"><?= lang('media') ?></a></li>
                <li><a href="#categories" role="tab" data-toggle="tab"><?= lang('categories') ?></a></li>
                <li><a href="#attributes" role="tab" data-toggle="tab"><?= lang('attributes') ?></a></li>
                <li><a href="#shipping" role="tab" data-toggle="tab"><?= lang('shipping') ?></a></li>
				<?php
				    switch($row['product_type']) {
                        case 'general':
                        case 'subscription':
				    ?>
                    <li><a href="#discounts" role="tab" data-toggle="tab"><?= lang('discounts') ?></a></li>
				<?php break; } ?>
                <li><a href="#cross_sell" role="tab" data-toggle="tab"><?= lang('cross_sell') ?></a></li>
				<?php if (config_enabled('affiliate_marketing')): ?>
                    <li><a href="#affiliate_marketing" role="tab"
                           data-toggle="tab"><?= lang('affiliate_marketing') ?></a></li>
				<?php endif; ?>
                <li><a href="#options" role="tab" data-toggle="tab"><?= lang('options') ?></a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="availability">
                    <div class="col-lg-2">
                        <br/>
                        <div class="text-center box-info">
							<?= photo(CONTROLLER_METHOD, $row, 'img-responsive img-rounded') ?>
                            <hr/>
                            <div class="caption text-capitalize">
                                <ul class="nav nav-pills nav-stacked">
                                    <li class="active">
                                        <small><a href="<?= page_url('product', $row) ?>" target="_blank">
												<?= lang('view_product_page') ?>
                                            </a></small>
                                    </li>
                                    <li class="active">
                                        <small><?= lang('product_id') ?>: <?= $id ?></small>
                                    </li>
                                    <li class="active">
                                        <small><?= lang('last_update') ?>
                                            : <?= display_date($row['modified']) ?></small>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-10">
                        <div class="hidden-xs">
                            <h3 class="header capitalize"><?= $row['product_name'] ?>
                                <span class="pull-right label label-info"><?= lang($row['product_type']) ?></span>
                            </h3>
                            <span><?= word_limiter($row['product_overview'], 25) ?></span>
                        </div>
                        <hr/>
                        <div class="form-group">
							<?= lang('status', 'product_status', array('class' => 'col-lg-3 control-label')) ?>
                            <div class="col-lg-2">
								<?= form_dropdown('product_status', options('active'), $row['product_status'], 'class="form-control"') ?>
                            </div>
                            <hr class="hidden-lg">
							<?= lang('featured', 'product_featured', array('class' => 'col-lg-2 control-label')) ?>
                            <div class="col-lg-2">
								<?= form_dropdown('product_featured', options('yes_no'), $row['product_featured'], 'class="form-control"') ?>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group">
							<?= lang('date_expires', 'date_expires', array('class' => 'col-lg-3 control-label')) ?>
                            <div class="col-lg-2">
                                <div class="input-group">
									<?= form_input('date_expires', set_value('date_expires', $row['date_expires_formatted']), 'class="' . css_error('date_expires') . ' form-control datepicker-input"') ?>
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i> </span>
                                </div>
                            </div>
                            <hr class="hidden-lg"/>
							<?= lang('limited_offer', 'enable_timer', array('class' => 'col-lg-2 control-label')) ?>
                            <div class="col-lg-2">
								<?= form_dropdown('enable_timer', options('yes_no'), $row['enable_timer'], 'class="form-control"') ?>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group">
							<?= lang('enable_inventory', 'enable_inventory', array('class' => 'col-lg-3 control-label')) ?>
                            <div class="col-lg-2">
								<?= form_dropdown('enable_inventory', options('yes_no'), $row['enable_inventory'], 'class="form-control"') ?>
                            </div>
                            <hr class="hidden-lg"/>
							<?= lang('inventory_amount', 'inventory_amount', array('class' => 'col-lg-2 control-label')) ?>
                            <div class="col-lg-2">
								<?= form_input('inventory_amount', set_value('inventory_amount', $row['inventory_amount']), 'class="' . css_error('inventory_amount') . ' form-control"') ?>
                            </div>
                        </div>
                        <hr/>
                        <?php if ($row['product_type'] == 'general'): ?>
                        <div class="form-group">
							<?= lang('min_quantity_required', 'min_quantity_required', array('class' => 'col-lg-3 control-label')) ?>
                            <div class="col-lg-2">
								<?= form_input('min_quantity_required', set_value('min_quantity_required', $row['min_quantity_required']), 'class="' . css_error('min_quantity_required') . ' form-control"') ?>
                            </div>
                            <hr class="hidden-lg"/>
							<?= lang('max_quantity_allowed', 'max_quantity_allowed', array('class' => 'col-lg-2 control-label')) ?>
                            <div class="col-lg-2">
								<?= form_input('max_quantity_allowed', set_value('max_quantity_allowed', $row['max_quantity_allowed']), 'class="' . css_error('max_quantity_allowed') . ' form-control"') ?>
                            </div>
                        </div>
                        <hr/>
                        <?php endif; ?>
                        <div class="form-group">
							<?= lang('hidden_product', 'hidden_product', array('class' => 'col-lg-3 control-label')) ?>
                            <div class="col-lg-2">
								<?= form_dropdown('hidden_product', options('yes_no'), $row['hidden_product'], 'class="form-control"') ?>
                            </div>
                            <hr class="hidden-lg"/>
							<?= lang('points', 'points', array('class' => 'col-lg-2 control-label')) ?>
                            <div class="col-lg-2">
								<?= form_input('points', set_value('points', $row['points']), 'class="' . css_error('points') . ' form-control"') ?>
                            </div>
                        </div>
                        <hr/>
                    </div>
                </div>
                <div class="tab-pane" id="pricing">
                    <h3 class="header capitalize"><?= lang('product_pricing') ?> </h3>
                    <span><?= lang('setup_pricing_options_for_product') ?></span>
                    <hr/>
					<?php if ($row['product_type'] == 'general'): ?>
                        <div class="form-group">
							<?= lang('product_price', 'product_price', array('class' => 'col-lg-3 control-label')) ?>
                            <div class="col-lg-2">
								<?= form_input('product_price', set_value('product_price', input_amount($row['product_price'])), 'class="' . css_error('product_price') . ' form-control"') ?>
                            </div>
                            <hr class="hidden-lg"/>
							<?= lang('product_sale_price', 'product_sale_price', array('class' => 'col-lg-2 control-label')) ?>
                            <div class="col-lg-2">
								<?= form_input('product_sale_price', set_value('product_sale_price', input_amount($row['product_sale_price'])), 'class="' . css_error('product_sale_price') . ' form-control"') ?>
                            </div>
                        </div>
                        <hr/>
					<?php endif; ?>
                    <div class="form-group">
						<?= lang('login_for_price', 'login_for_price', array('class' => 'col-lg-3 control-label')) ?>
                        <div class="col-lg-2">
							<?= form_dropdown('login_for_price', options('yes_no'), $row['login_for_price'], 'class="form-control"') ?>
                        </div>
						<?= lang('tax_class_id', 'tax_class_id', array('class' => 'col-lg-2 control-label')) ?>
                        <div class="col-lg-2">
							<?= form_dropdown('tax_class_id', options('tax_classes', FALSE, $row['tax_classes']), $row['tax_class_id'], 'class="form-control"') ?>
                        </div>
                        <hr/>
                    </div>
                    <hr/>
					<?php $n = 1; ?>
					<?php if ($row['product_type'] == 'subscription'): ?>
                        <div class="box-info">
							 <span class="pull-right">
								 <a href="javascript:add_subscription_pricing(<?= count($row['pricing_options']) ?>)"
                                    class="btn btn-primary"><?= i('fa fa-plus') ?> <?= lang('add_pricing_options') ?></a>
							 </span>
                            <h5 class="text-capitalize hidden-xs"><?= lang('configure_pricing_options_for_subscription') ?></h5>
                            <hr/>

                            <div class="row hidden-md visible-lg">
                                <div class="col-md-2"><?= tb_header('enabled_amount_default', '', FALSE) ?></div>
                                <div class="col-md-1"><?= tb_header('interval', '', FALSE) ?></div>
                                <div class="col-md-1"><?= tb_header('interval_type', '', FALSE) ?></div>
                                <div class="col-md-1"><?= tb_header('recurrence', '', FALSE) ?></div>
                                <div class="col-md-2"><?= tb_header('enable_initial_amount', '', FALSE) ?></div>
                                <div class="col-md-1"><?= tb_header('initial_interval', '', FALSE) ?></div>
                                <div class="col-md-1"><?= tb_header('initial_type', '', FALSE) ?></div>
                                <div class="col-md-2"><?= tb_header('name', '', FALSE) ?></div>
                                <div class="col-md-1"></div>
                            </div>
                            <hr class="visible-lg"/>
                            <div id="pricing-div">
								<?php if (!empty($row['pricing_options'])): ?>
									<?php foreach ($row['pricing_options'] as $v): ?>
                                        <div id="pricingdiv-<?= $n ?>">
                                            <div class="row">
                                                <div class="col-md-2 r">
                                                    <div class="input-group">
			                                            <span class="input-group-addon">
			                                            <?= form_checkbox('pricing_options[' . $n . '][enable]', '1', $v['enable']); ?>
			                                            </span>
														<?= form_input('pricing_options[' . $n . '][amount]', set_value('pricing_options', input_amount($v['amount'])), 'class="form-control"') ?>
                                                        <span class="input-group-addon">
			                                            <?= form_radio('default_subscription_price', $v['prod_price_id'], $v['default_price']); ?>
			                                            </span>
                                                    </div>

                                                    <input type="hidden"
                                                           name="pricing_options[<?= $n ?>][prod_price_id]"
                                                           value="<?= $v['prod_price_id'] ?>"/>
                                                </div>
                                                <div class="col-md-1 r">
                                                    <input type="number"
                                                           name="pricing_options[<?= $n ?>][interval_amount]"
                                                           value="<?= set_value('interval_amount', $v['interval_amount']) ?>"
                                                           class="form-control">
                                                </div>
                                                <div class="col-md-1 r">
													<?= form_dropdown('pricing_options[' . $n . '][interval_type]', options('interval_types'), $v['interval_type'], 'class="form-control"') ?>
                                                </div>
                                                <div class="col-md-1 r">
                                                    <input type="number"
                                                           name="pricing_options[<?= $n ?>][recurrence]"
                                                           value="<?= set_value('recurrence', $v['recurrence']) ?>"
                                                           class="form-control">
                                                </div>
                                                <div class="col-md-2 r">
                                                    <div class="input-group">
			                                            <span class="input-group-addon">
			                                            <?= form_checkbox('pricing_options[' . $n . '][enable_initial_amount]', '1', $v['enable_initial_amount']); ?>
			                                            </span>
														<?= form_input('pricing_options[' . $n . '][initial_amount]', set_value('pricing_options', input_amount($v['initial_amount'])), 'class="form-control"') ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-1 r">
                                                    <input type="number"
                                                           name="pricing_options[<?= $n ?>][initial_interval]"
                                                           value="<?= set_value('initial_interval', $v['initial_interval']) ?>"
                                                           class="form-control">
                                                </div>
                                                <div class="col-md-1 r">
													<?= form_dropdown('pricing_options[' . $n . '][initial_interval_type]', options('interval_types'), $v['initial_interval_type'], 'class="form-control"') ?>
                                                </div>
                                                <div class="col-md-2 r">
													<?= form_input('pricing_options[' . $n . '][name]', set_value('pricing_options', $v['name']), 'class="form-control"') ?>
                                                </div>
                                                <div class="col-md-1 r">
													<?php if (empty($v['default_price'])): ?>
                                                        <a href="javascript:remove_div('#pricingdiv-<?= $n ?>')"
                                                           class="btn btn-danger block-phone <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
													<?php endif; ?>
                                                </div>
                                            </div>
                                            <hr/>
                                        </div>
										<?php $n++; ?>
									<?php endforeach; ?>
								<?php else: ?>
									<?php $n++; ?>
								<?php endif; ?>
                            </div>

                        </div>
					<?php endif; ?>
                </div>
                <div class="tab-pane" id="name">
                    <div class="hidden-xs">
                        <h3 class="text-capitalize"> <?= lang('product_description') ?></h3>
                        <span><?= lang('set_locale_specific_descriptions_each_tab') ?></span>
                    </div>
                    <hr/>
                    <ul class="nav nav-tabs text-capitalize" role="tablist">
						<?php foreach ($row['name'] as $v): ?>
                            <li <?php if ($v['language_id'] == $sts_site_default_language): ?> class="active" <?php endif; ?>>
                                <a href="#<?= $v['image'] ?>" data-toggle="tab"><?= i('flag-' . $v['image']) ?>
                                    <span
                                            class="visible-lg"><?= $v['name'] ?></span></a>
                            </li>
						<?php endforeach; ?>
                    </ul>
                    <div class="tab-content">
						<?php foreach ($row['name'] as $v): ?>
                            <div
                                    class="tab-pane <?php if ($v['language_id'] == $sts_site_default_language): ?> active <?php endif; ?>"
                                    id="<?= $v['image'] ?>">
                                <hr/>
                                <div class="form-group">
									<?= lang('product_name', 'product_name', array('class' => 'col-lg-2 control-label')) ?>
                                    <div class="col-lg-6">
										<?= form_input('product_name[' . $v['language_id'] . '][product_name]', set_value('product_name[' . $v['language_id'] . ']', $v['product_name'], FALSE), 'class="' . css_error('product_name[' . $v['language_id'] . '][product_name]') . ' form-control"') ?>
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group">
									<?= lang('product_overview', 'product_overview', array('class' => 'col-lg-2 control-label')) ?>
                                    <div class="col-lg-9">
										<?= form_input('product_name[' . $v['language_id'] . '][product_overview]', set_value('product_name[' . $v['language_id'] . ']', $v['product_overview'], FALSE), 'maxlength="255" class="' . css_error('product_name[' . $v['language_id'] . '][product_overview]') . ' form-control"') ?>

                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group">
									<?= lang('product_description', 'product_description', array('class' => 'col-lg-2 control-label')) ?>
                                    <div class="col-lg-9">
										<?= form_textarea('product_name[' . $v['language_id'] . '][product_description]', set_value('product_name[' . $v['language_id'] . '][product_description]', $v['product_description'], FALSE), 'class="editor ' . css_error('product_name[' . $v['language_id'] . '][product_description]') . ' form-control"') ?>
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group">
									<?= lang('meta_title', 'meta_title', array('class' => 'col-lg-2 control-label')) ?>
                                    <div class="col-lg-4">
										<?= form_input('product_name[' . $v['language_id'] . '][meta_title]', set_value('product_name[' . $v['language_id'] . '][meta_title]', $v['meta_title']), 'class="' . css_error('product_name[' . $v['language_id'] . '][meta_title]') . ' form-control"') ?>
                                    </div>
									<?= lang('meta_keywords', 'meta_keywords', array('class' => 'col-lg-2 control-label')) ?>
                                    <div class="col-lg-3">
										<?= form_input('product_name[' . $v['language_id'] . '][meta_keywords]', set_value('product_name[' . $v['language_id'] . '][meta_keywords]', $v['meta_keywords']), 'class="' . css_error('product_name[' . $v['language_id'] . '][meta_keywords]') . ' form-control"') ?>
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group">
									<?= lang('meta_description', 'meta_description', array('class' => 'col-lg-2 control-label')) ?>
                                    <div class="col-lg-9">
										<?= form_input('product_name[' . $v['language_id'] . '][meta_description]', set_value('product_name[' . $v['language_id'] . '][meta_description]', $v['meta_description']), 'class="' . css_error('product_name[' . $v['language_id'] . '][meta_description]') . ' form-control"') ?>
                                    </div>
                                </div>
                                <hr/>
                            </div>
						<?php endforeach; ?>
                    </div>
                </div>
                <div class="tab-pane" id="specs">
                    <div class="hidden-xs">
                        <h3 class="text-capitalize"> <?= lang('product_specifications') ?><span
                                    class="pull-right"><a
                                        href="<?= admin_url(TBL_PRODUCTS_SPECIFICATIONS . '/update_product_specifications/' . $id) ?>"
                                        id="manage_specifications"
                                        class="btn btn-primary add_product_groups"><?= i('fa fa-plus') ?> <?= lang('assign_product_specifications') ?></a></span>
                        </h3>
                        <span><?= lang('add_specifications_to_product') ?></span>
                    </div>
                    <hr/>
					<?php if (!empty($row['product_specs'])): ?>
                        <ul class="nav nav-tabs text-capitalize" role="tablist">

							<?php foreach ($row['product_specs'] as $k => $v): ?>
                                <li <?php if ($k == $sts_site_default_language): ?> class="active" <?php endif; ?>>
                                    <a href="#specs<?= $v[0]['name'] ?>"
                                       data-toggle="tab"><?= i('flag-' . $v[0]['image']) ?> <span
                                                class="visible-lg"><?= $v[0]['name'] ?></span></a>
                                </li>
							<?php endforeach; ?>
                        </ul>
                        <div class="tab-content">
							<?php $a = 1; ?>
							<?php foreach ($row['product_specs'] as $k => $v): ?>
                                <div
                                        class="tab-pane <?php if ($k == $sts_site_default_language): ?>active<?php endif; ?>"
                                        id="specs<?= $v[0]['name'] ?>">
									<?php for ($s = 0; $s < count($v); $s++): ?>
                                        <div id="specsdiv-<?= $a ?>" class="specsdiv-<?= $s ?>">
                                            <hr/>
                                            <div class="row">
                                                <div class="form-group">
													<?= lang($v[$s]['specification_name'], $v[$s]['specification_name'], array('class' => 'col-lg-3 control-label')) ?>
                                                    <div class="col-md-7">
                                        <textarea class="form-control" rows="3"
                                                  name="product_specs[<?= $a ?>][spec_value]"><?= $v[$s]['spec_value'] ?></textarea>
														<?= form_hidden('product_specs[' . $a . '][prod_spec_id]', $v[$s]['prod_spec_id']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
										<?php $a++; ?>
									<?php endfor; ?>
                                </div>
							<?php endforeach; ?>
                        </div>
					<?php else: ?>
                        <div
                                class="alert alert-warning"><?= i('fa fa-info-circle') ?> <?= lang('no_product_specifications_assigned') ?></div>
					<?php endif; ?>
                </div>
                <div class="tab-pane" id="media">
                    <br/>
                    <ul class="nav nav-tabs text-capitalize" role="tablist">
                        <li class="active"><a href="#imagesub" role="tab"
                                              data-toggle="tab"><?= lang('images') ?></a>
                        </li>
                        <li><a href="#videosub" role="tab" data-toggle="tab"><?= lang('videos') ?></a></li>
						<?php if ($row['product_type'] != 'third_party'): ?>
                            <li><a href="#filesub" role="tab" data-toggle="tab"><?= lang('download_files') ?></a></li>
						<?php endif; ?>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="imagesub">
                            <h3 class="text-capitalize"><span class="pull-right"><a
                                            href="javascript:add_image(<?= count($row['photos']) ?>)"
                                            class="btn btn-primary <?= is_disabled('update', TRUE) ?>"><?= i('fa fa-plus') ?> <?= lang('add_new_image') ?></a></span> <?= lang('product_images') ?>
                            </h3>
                            <span><?= lang('only_jpg_gif_png_files_are_allowed') ?></span>
                            <hr/>
                            <div class="collapse in" id="photos">
                                <div class="row" id="photo-div">
									<?php $i = 1; ?>
									<?php if (!empty($row['photos'])): ?>
										<?php foreach ($row['photos'] as $v): ?>
                                            <div class="col-lg-2" id="imagediv-<?= $i ?>">
                                                <div class="thumbnail">
                                                    <div class="photo-panel">
                                                        <a class='iframe'
                                                           href="<?= base_url() ?>filemanager/dialog.php?type=1&akey=<?= $file_manager_key ?>&field_id=<?= $i ?>&fldr=products">
                                                            <img id="image-<?= $i ?>"
                                                                 src="<?= base_url('images/uploads/products/' . $v['photo_file_name']) ?>"/>
                                                        </a>

                                                        <div class="tip image_id" data-toggle="tooltip"
                                                             data-placement="bottom"
                                                             title="<?= lang('photo_id') ?>"><?= $v['photo_id'] ?></div>
                                                    </div>
                                                    <input type="hidden" name="images[<?= $i ?>][photo_file_name]"
                                                           value="<?= base_url('images/uploads/products/' . $v['photo_file_name']) ?>"
                                                           id="<?= $i ?>"/>
                                                    <input type="hidden" name="images[<?= $i ?>][photo_id]"
                                                           value="<?= $v['photo_id'] ?>" id="<?= $i ?>"/>

                                                    <div class="caption text-center">

														<?php if ($v['product_default'] == 0): ?>
                                                            <a href="<?= admin_url(CONTROLLER_CLASS . '/set_default_photo/' . $v['photo_id']) ?>"
                                                               class="tip btn btn-default btn-sm block-phone"
                                                               data-toggle="tooltip"
                                                               data-placement="bottom"
                                                               title="<?= lang('set_as_default_image') ?>"><?= i('fa fa-check') ?></a>
                                                            <a href="javascript:remove_div('#imagediv-<?= $i ?>')"
                                                               class="btn btn-danger btn-sm block-phone <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
														<?php else: ?>
                                                            <button disabled
                                                                    class="btn btn-success btn-sm block-phone"
                                                                    title="<?= lang('default_image') ?>"><?= i('fa fa-check') ?></button>
														<?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
											<?php $i++; ?>
										<?php endforeach; ?>
									<?php else: ?>
                                        <div class="col-lg-2" id="imagediv-0">
                                            <div class="thumbnail">
                                                <div class="photo-panel">
                                                    <a href="<?= base_url() ?>filemanager/dialog.php?type=1&amp;akey=<?= $file_manager_key ?>&field_id=0&fldr=products"
                                                       class="iframe cboxElement">
                                                        <img src="<?= base_url(TPL_DEFAULT_PRODUCT_PHOTO) ?>"
                                                             id="image-0"></a>
                                                </div>
                                                <input type="hidden" name="images[0][photo_file_name]" id="0"/>

                                                <div class="caption text-center"><a
                                                            href="javascript:remove_div('#imagediv-0')"
                                                            class="btn btn-danger btn-sm block-phone"><i
                                                                class="fa fa-trash-o "></i></a>
                                                </div>
                                            </div>
                                        </div>
									<?php endif; ?>
                                </div>
                                <hr/>
                            </div>
                        </div>
                        <div class="tab-pane" id="videosub">
                            <h3 class="text-capitalize"><?= lang('product_videos') ?></h3>
                            <span><?= lang('add_video_links_and_embed_code_for_products_here') ?></span>
                            <hr/>
                            <div>
                                <div class="form-group">
									<?= lang('set_videos_as_default_media_on_product_pages', 'video_as_default', array('class' => 'col-lg-3 control-label')) ?>
                                    <div class="col-lg-5">
                                        <select id="video_as_default" class="form-control select2"
                                                name="video_as_default">
											<?php if (!empty($row['video_as_default'])): ?>
                                                <option value="<?= $row['video_as_default'] ?>"
                                                        selected><?= $row['default_video_name'] ?></option>
											<?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group">
									<?= lang('assign_videos', 'videos', array('class' => 'col-lg-3 control-label')) ?>
                                    <div class="col-lg-5">
                                        <select multiple id="videos" class="form-control select2" name="videos[]">
											<?php if (!empty($row['videos'])): ?>
												<?php foreach ($row['videos'] as $v): ?>
                                                    <option value="<?= $v['video_id'] ?>"
                                                            selected><?= $v['video_name'] ?></option>
												<?php endforeach; ?>
											<?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
						<?php if ($row['product_type'] != 'third_party'): ?>
                            <div class="tab-pane" id="filesub">
                                <h3 class="text-capitalize"><?= lang('product_downloads') ?></h3>
                                <span><?= lang('add_downloadable_files_for_your_product') ?></span>
                                <hr/>
                                <div class="row">
                                    <div class="form-group">
										<?= lang('max_downloads_user', 'max_downloads_user', array('class' => 'col-lg-3 control-label')) ?>
                                        <div class="col-lg-1">
                                            <input type="number" name="max_downloads_user"
                                                   value="<?= set_value('max_downloads_user', $row['max_downloads_user']) ?>"
                                                   class=" form-control">
                                        </div>
                                    </div>
                                    <hr/>
                                    <div class="form-group">
										<?= lang('assigned_download_files', 'product_downloads', array('class' => 'col-lg-3 control-label')) ?>
                                        <div class="col-lg-5">
                                            <select multiple id="product_downloads" class="form-control select2"
                                                    name="product_downloads[]">
												<?php if (!empty($row['downloads'])): ?>
													<?php foreach ($row['downloads'] as $v): ?>
                                                        <option value="<?= $v['download_id'] ?>"
                                                                selected><?= $v['download_name'] ?></option>
													<?php endforeach; ?>
												<?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
						<?php endif; ?>
                    </div>
                </div>
                <div class="tab-pane" id="categories">
                    <br/>
                    <ul class="nav nav-tabs text-capitalize" role="tablist">
                        <li class="active"><a href="#cattab" role="tab" data-toggle="tab">
								<?= lang('categories') ?></a>
                        </li>
                        <li><a href="#tag" role="tab" data-toggle="tab"><?= lang('tags') ?></a></li>
                        <li><a href="#brand" role="tab" data-toggle="tab"><?= lang('brand') ?></a></li>
                        <li><a href="#supplier" role="tab" data-toggle="tab"><?= lang('supplier') ?></a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="cattab">
                            <h3 class="text-capitalize"> <?= lang('product_categories') ?></h3>
                            <span><?= lang('click_product_categories_box_assign_categories') ?></span>
                            <hr/>
                            <div class="form-group">
								<?= lang('assign_product_categories', 'product_categories', array('class' => 'col-lg-3 control-label')) ?>
                                <div class="col-lg-5">
                                    <select multiple id="product_categories" class="form-control select2"
                                            name="product_categories[]">
										<?php if (!empty($row['categories'])): ?>
											<?php foreach ($row['categories'] as $v): ?>
                                                <option value="<?= $v['category_id'] ?>"
                                                        selected><?= $v['path'] ?>
                                                    / <?= $v['category_name'] ?></option>
											<?php endforeach; ?>
										<?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="tag">
                            <h3 class="text-capitalize"> <?= lang('product_tags') ?></h3>
                            <span><?= lang('add_tags_to_identify_product') ?></span>
                            <hr/>
                            <div class="form-group">
								<?= lang('assign_product_tags', 'product_tags', array('class' => 'col-lg-3 control-label')) ?>
                                <div class="col-lg-5">
                                    <select multiple id="tags" class="form-control select2"
                                            name="product_tags[]">
										<?php if (!empty($row['tags'])): ?>
											<?php foreach ($row['tags'] as $v): ?>
                                                <option value="<?= $v['tag'] ?>"
                                                        selected><?= $v['tag'] ?></option>
											<?php endforeach; ?>
										<?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="brand">
                            <h3 class="text-capitalize"><?= lang('brand_name') ?></h3>
                            <span><?= lang('assign_brand_to_this_product') ?></span>
                            <hr/>
                            <div class="form-group">
								<?= lang('brand_name', 'brand_name', array('class' => 'col-lg-3 control-label')) ?>
                                <div class="col-lg-5">
                                    <select id="brand_id" class="form-control select2" name="brand_id">
                                        <option value="<?= $row['brand_id'] ?>"
                                                selected><?= $row['brand_name'] ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="supplier">
                            <h3 class="text-capitalize"><?= lang('supplier_name') ?></h3>
                            <span><?= lang('assign_supplier_to_this_product') ?></span>
                            <hr/>
                            <div class="form-group">
								<?= lang('supplier_name', 'supplier_name', array('class' => 'col-lg-3 control-label')) ?>
                                <div class="col-lg-5">
                                    <select id="supplier_id" class="form-control select2" name="supplier_id">
                                        <option value="<?= $row['supplier_id'] ?>"
                                                selected><?= $row['supplier_name'] ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="attributes">
                    <h3 class="text-capitalize"> <?= lang('product_attributes') ?><span class="pull-right"><a
                                    href="<?= admin_url(TBL_PRODUCTS_ATTRIBUTES . '/update_product_attributes/' . $id) ?>"
                                    id="manage_attributes"
                                    class="btn btn-primary add_product_groups <?= is_disabled('update', TRUE) ?>"><?= i('fa fa-plus') ?> <?= lang('assign_product_attributes') ?></a></span>
                    </h3>
                    <span><?= lang('assign_attributes_to_your_product') ?></span>
                    <hr/>
                    <ul class="nav nav-pills text-capitalize" role="tablist">
						<?php if (!empty($row['attributes'])): ?>
							<?php for ($m = 0; $m < count($row['attributes']); $m++): ?>
                                <li <?php if ($m == 0): ?> class="active" <?php endif; ?>>
                                    <a href="#prod_att_id-<?= $row['attributes'][$m]['prod_att_id'] ?>"
                                       role="tab"
                                       data-toggle="tab"><?= $row['attributes'][$m]['attribute_name'] ?></a>
                                </li>
							<?php endfor; ?>
						<?php endif; ?>
                    </ul>
                    <br/>

                    <div class="tab-content box-info">
						<?php if (!empty($row['attributes'])): ?>
							<?php $t = 99 ?>
							<?php for ($m = 0; $m < count($row['attributes']); $m++): ?>
                                <div class="tab-pane <?php if ($m == 0): ?>active<?php endif; ?>"
                                     id="prod_att_id-<?= $row['attributes'][$m]['prod_att_id'] ?>">
                                    <div class="hidden-xs">
                                        <p class="alert alert-info">
                            <span class="pull-right badge"><?= lang('attribute_type') ?>: <span
                                        class=""><?= $row['attributes'][$m]['attribute_type'] ?></span> </span>
											<?= $row['attributes'][$m]['description'] ?></p>
                                    </div>
                                    <hr/>
                                    <div class="form-group">
										<?= lang('required', 'required', array('class' => 'col-lg-3 control-label')) ?>
                                        <div class="col-lg-5">
											<?= form_dropdown('attributes[' . $m . '][required]', options('yes_no'), $row['attributes'][$m]['required'], 'class="form-control"') ?>
											<?= form_hidden('attributes[' . $m . '][prod_att_id]', $row['attributes'][$m]['prod_att_id']) ?>
											<?= form_hidden('attributes[' . $m . '][attribute_id]', $row['attributes'][$m]['attribute_id']) ?>
                                        </div>
                                    </div>
                                    <hr/>
									<?php switch ($row['attributes'][$m]['attribute_type']):
										case 'text':
											?>
                                            <div class="form-group">
												<?= lang('default_value', 'default_value', array('class' => 'col-lg-3 control-label')) ?>
                                                <div class="col-lg-5">
													<?= form_input('attributes[' . $m . '][value]', set_value('attributes[' . $m . '][value]', $row['attributes'][$m]['value']), 'class="form-control"') ?>
                                                </div>
                                            </div>
                                            <hr/>
											<?php break; ?>
										<?php
										case 'text':
											?>
                                            <div class="form-group">
												<?= lang('default_value', 'default_value', array('class' => 'col-lg-3 control-label')) ?>
                                                <div class="col-lg-5">
													<?= form_input('attributes[' . $m . '][value]', set_value('attributes[' . $m . '][value]', $row['attributes'][$m]['value']), 'class="form-control"') ?>
                                                </div>
                                            </div>
                                            <hr/>
											<?php break; ?>
										<?php
										case 'textarea':
											?>
                                            <div class="form-group">
												<?= lang('default_value', 'default_value', array('class' => 'col-lg-3 control-label')) ?>
                                                <div class="col-lg-5">
													<?= form_textarea('attributes[' . $m . '][value]', set_value('attributes[' . $m . '][value]', $row['attributes'][$m]['value']), 'class="form-control"') ?>
                                                </div>
                                            </div>
                                            <hr/>
											<?php break; ?>
										<?php
										case 'file':
											?>
                                            <div class="form-group">
												<?= lang('preview', 'preview', array('class' => 'col-lg-3 control-label')) ?>
                                                <div class="col-lg-5">
                                                    <input type="file" disabled class="btn btn-default"/> <span
                                                            class="label label-default"><?= lang('disabled') ?> </span>
                                                </div>
                                            </div>
                                            <hr/>
											<?php break; ?>
										<?php
										case 'date':
											?>
                                            <div class="form-group">
												<?= lang('default_value', 'default_value', array('class' => 'col-lg-3 control-label')) ?>
                                                <div class="col-lg-5">
                                                    <div class="input-group">
														<?= form_input('attributes[' . $m . '][value]', set_value('attributes[' . $m . '][value]', $row['attributes'][$m]['value']), 'class="form-control datepicker-input"') ?>
                                                        <span class="input-group-addon"><i
                                                                    class="fa fa-calendar"></i> </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr/>
											<?php break; ?>
										<?php
										case 'select':
											?>
										<?php
										case 'radio':
											?>
										<?php
										case 'image':
											?>
                                            <h5 class="text-capitalize"><?= lang('configure_options_for_product') ?></h5>
                                            <hr/>
                                            <div class="overflow table-responsive">
                                                <table class="table table-striped table-hover">
                                                    <thead>
                                                    <tr>
                                                        <th class="text-center"><?= lang('enable') ?></th>
                                                        <th></th>
                                                        <th class="text-center"><?= lang('image') ?></th>
														<?php if ($row['attributes'][$m]['attribute_type'] != 'image'): ?>
                                                            <th class="text-center">
																<?= lang('option_name') ?>
                                                            </th>
														<?php endif; ?>
                                                        <th class="text-center"><?= lang('option_sku') ?></th>
                                                        <th colspan="2"
                                                            class="text-center"><?= lang('add_subtract_price') ?></th>
                                                        <th colspan="2"
                                                            class="text-center"><?= lang('add_subtract_weight') ?></th>
                                                        <th colspan="2"
                                                            class="text-center"><?= lang('add_subtract_points') ?></th>
                                                        <th><?= lang('enable_inventory') ?></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
													<?php if (!empty($row['attributes'][$m]['option_values'])): ?>
														<?php foreach ($row['attributes'][$m]['option_values'] as $r => $p): ?>
                                                            <tr>
                                                                <td style="width:5%"
                                                                    class="text-center"><?= form_checkbox('attributes[' . $m . '][option_values][' . $r . '][status]', '1', $p['option_status']); ?></td>
                                                                <td class="text-center"><?= i('flag-' . $p['image']) ?> </td>
                                                                <td class="text-center">
                                                                    <a class='iframe'
                                                                       href="<?= base_url() ?>filemanager/dialog.php?type=1&akey=<?= $file_manager_key ?>&field_id=<?= $t ?>&fldr=products">
																		<?php if (!empty($p['unique_path'])): ?>
                                                                            <img id="image-<?= $t ?>"
                                                                                 src="<?= $p['unique_path'] ?>"
                                                                                 class="dash-photo img-thumbnail img-responsive"/>
																		<?php elseif (!empty($p['path'])): ?>
                                                                            <img id="image-<?= $t ?>"
                                                                                 src="<?= $p['path'] ?>"
                                                                                 class="dash-photo img-thumbnail img-responsive"/>
																		<?php else: ?>
                                                                            <img id="image-<?= $t ?>"
                                                                                 src="<?= base_url(TPL_DEFAULT_PRODUCT_PHOTO) ?>"
                                                                                 class="dash-photo img-thumbnail img-responsive"/>
																		<?php endif; ?>
                                                                    </a>
                                                                    <input type="hidden"
                                                                           name="attributes[<?= $m ?>][option_values][<?= $r ?>][unique_path]"
                                                                           value="<?= $p['unique_path'] ?>"
                                                                           id="<?= $t ?>"/>
                                                                </td>
	                                                            <?php if ($row['attributes'][$m]['attribute_type'] != 'image'): ?>
                                                                <td class="text-center">
                                                                    <strong><?= $p['option_name'] ?></strong></td>
                                                                <?php endif; ?>
                                                                <td style="width: 12%">
																	<?= form_input('attributes[' . $m . '][option_values][' . $r . '][option_sku]', $p['option_sku'], 'class="form-control"') ?>
                                                                </td>
                                                                <td style="width: 8%">
																	<?= form_dropdown('attributes[' . $m . '][option_values][' . $r . '][price_add]', options('plus_minus'), $p['price_add'], 'class="form-control"') ?>
                                                                </td>
                                                                <td style="width: 12%">
																	<?= form_input('attributes[' . $m . '][option_values][' . $r . '][price]', $p['price'], 'class="form-control"') ?>
                                                                </td>
                                                                <td style="width: 8%">
																	<?= form_dropdown('attributes[' . $m . '][option_values][' . $r . '][weight_add]', options('plus_minus'), $p['weight_add'], 'class="form-control"') ?>
                                                                </td>
                                                                <td style="width: 10%">
																	<?= form_input('attributes[' . $m . '][option_values][' . $r . '][weight]', $p['weight'], 'class="form-control"') ?>
                                                                </td>
                                                                <td style="width: 8%">
																	<?= form_dropdown('attributes[' . $m . '][option_values][' . $r . '][points_add]', options('plus_minus'), $p['points_add'], 'class="form-control"') ?>
                                                                </td>
                                                                <td style="width: 8%">
                                                                    <input type="number"
                                                                           name="attributes[<?= $m ?>][option_values][<?= $r ?>][points]"
                                                                           value="<?= $p['points'] ?>"
                                                                           class="form-control">
                                                                </td>
                                                                <td style="width: 10%">
                                                                    <div class="input-group">
						                                                <span class="input-group-addon">
						                                                <?= form_checkbox('attributes[' . $m . '][option_values][' . $r . '][enable_inventory]', '1', $p['enable_inventory']); ?>
						                                                </span>
                                                                        <input type="number"
                                                                               name="attributes[<?= $m ?>][option_values][<?= $r ?>][inventory]"
                                                                               value="<?= $p['inventory'] ?>"
                                                                               class="form-control">
																		<?= form_hidden('attributes[' . $m . '][option_values][' . $r . '][prod_att_value_id]', $p['prod_att_value_id']) ?>
                                                                    </div>
                                                                </td>
                                                            </tr>
															<?php $t++ ?>
														<?php endforeach; ?>
													<?php endif; ?>
                                                    </tbody>
                                                </table>
                                                <br/>
                                                <a href="<?= admin_url(TBL_PRODUCTS_ATTRIBUTES . '/update/' . $row['attributes'][$m]['attribute_id'] . '#options') ?>"
                                                   class="btn btn-primary"><?= i('fa fa-plus') ?> <?= lang('add_more_options') ?></a>
                                            </div>
											<?php break; ?>
										<?php endswitch ?>
                                </div>
							<?php endfor; ?>
						<?php else: ?>
                            <div
                                    class="alert alert-warning"><?= i('fa fa-info-circle') ?> <?= lang('no_product_attributes_assigned') ?></div>
						<?php endif; ?>
                    </div>
                </div>
                <div class="tab-pane" id="shipping">
                    <div class="hidden-xs">
                        <h3 class="text-capitalize"> <?= lang('shipping') ?><span class="pull-right"></h3>
                        <span><?= lang('enable_shipping_options_per_product') ?></span>
                    </div>
                    <hr/>
                    <div class="form-group">
						<?= lang('charge_shipping', 'charge_shipping', array('class' => 'col-lg-3 control-label')) ?>
                        <div class="col-lg-2">
							<?= form_dropdown('charge_shipping', options('yes_no'), $row['charge_shipping'], 'class="form-control"') ?>
                        </div>
                        <hr class="hidden-lg"/>
						<?= lang('add_per_item_shipping_cost', 'shipping_cost', array('class' => 'col-lg-2 control-label')) ?>
                        <div class="col-lg-2">
							<?= form_input('shipping_cost', set_value('shipping_cost', $row['shipping_cost']), 'class="' . css_error('shipping_cost') . ' form-control"') ?>
                        </div>
                    </div>
                    <hr/>
                    <div class="form-group">
						<?= lang('length', 'length', array('class' => 'col-lg-3 control-label')) ?>
                        <div class="col-lg-2">
							<?= form_input('length', set_value('length', $row['length']), 'class="' . css_error('length') . ' form-control"') ?>
                        </div>
                        <hr class="hidden-lg"/>
						<?= lang('width', 'width', array('class' => 'col-lg-2 control-label')) ?>
                        <div class="col-lg-2">
							<?= form_input('width', set_value('width', $row['width']), 'class="' . css_error('width') . ' form-control"') ?>
                        </div>
                    </div>
                    <hr/>
                    <div class="form-group">
						<?= lang('height', 'height', array('class' => 'col-lg-3 control-label')) ?>
                        <div class="col-lg-2">
							<?= form_input('height', set_value('height', $row['height']), 'class="' . css_error('height') . ' form-control"') ?>
                        </div>
                        <hr class="hidden-lg"/>
						<?= lang('weight', 'weight', array('class' => 'col-lg-2 control-label')) ?>
                        <div class="col-lg-2">
							<?= form_input('weight', set_value('weight', $row['weight']), 'class="' . css_error('weight') . ' form-control"') ?>
                        </div>
                    </div>
                    <hr/>
                    <div class="form-group">
						<?= lang('measurement', 'length_type', array('class' => 'col-lg-3 control-label')) ?>
                        <div class="col-lg-2">
							<?= form_dropdown('length_type', options('measurements', FALSE, $row['measurements']), $row['length_type'], 'class="form-control"') ?>
                        </div>
                        <hr class="hidden-lg"/>
						<?= lang('weight_conversion', 'weight_type', array('class' => 'col-lg-2 control-label')) ?>
                        <div class="col-lg-2">
							<?= form_dropdown('weight_type', options('weight_options', FALSE, $row['weight_options']), $row['weight_type'], 'class="form-control"') ?>
                        </div>
                    </div>
                    <hr/>
                    <div class="form-group">
						<?= lang('location', 'product_location', array('class' => 'col-lg-3 control-label')) ?>
                        <div class="col-lg-6">
							<?= form_input('product_location', set_value('product_location', $row['product_location']), 'class="' . css_error('product_location') . ' form-control"') ?>
                        </div>
                    </div>
                </div>
				<?php
				    switch($row['product_type']) {
                        case 'general':
                        case 'subscription':

                            ?>
                    <div class="tab-pane" id="discounts">
                        <h3 class="text-capitalize"> <?= lang('discount_groups') ?> <span class="pull-right">
                                <a href="javascript:add_discount_group(<?= count($row['discount_groups']) ?>)" class="btn btn-primary <?= is_disabled('update', TRUE) ?>"><?= i('fa fa-plus') ?> <?= lang('add_discount_group') ?></a></span>
                        </h3>
                        <span><?= lang('set_custom_product_discounts_per_item') ?></span>
                        <hr/>
                        <div class="row hidden-md visible-lg">
                            <div class="col-md-2"><?= tb_header('group', '', FALSE) ?></div>
                            <div class="col-md-1"><?= tb_header('amount', '', FALSE) ?></div>
                            <div class="col-md-1"><?= tb_header('quantity', '', FALSE) ?></div>
                            <div class="col-md-1"><?= tb_header('type', '', FALSE) ?></div>
                            <div class="col-md-1"><?= tb_header('points', '', FALSE) ?></div>
                            <div class="col-md-2"><?= tb_header('start_date', '', FALSE) ?></div>
                            <div class="col-md-2"><?= tb_header('expires_on', '', FALSE) ?></div>
                            <div class="col-md-1"><?= tb_header('sort_order', '', FALSE) ?></div>
                            <div class="col-md-1"></div>
                        </div>
                        <div id="discount-div">
							<?php if (!empty($row['discount_groups'])): ?>
								<?php foreach ($row['discount_groups'] as $v): ?>
                                    <div id="discountdiv-<?= $k ?>">
                                        <div class="row">
                                            <div
                                                    class="col-md-2 r"><?= form_dropdown('discount_groups[' . $k . '][group_id]', $row['dg_array'], $v['group_id'], 'class="form-control"') ?></div>
                                            <div
                                                    class="col-md-1 r"><?= form_input('discount_groups[' . $k . '][group_amount]', set_value('group_amount', $v['group_amount']), 'class="' . css_error('group_amount') . ' form-control"') ?></div>
                                            <div
                                                    class="col-md-1 r">
                                                <?php if ($row['product_type'] == 'subscription'): ?>
                                                <?= form_input('discount_groups[' . $k . '][quantity]', set_value('quantity', $v['quantity']), 'class="' . css_error('quantity') . ' form-control" readonly') ?></div>

                                            <?php else: ?>
                                                <?= form_input('discount_groups[' . $k . '][quantity]', set_value('quantity', $v['quantity']), 'class="' . css_error('quantity') . ' form-control"') ?></div>
                                                <?php endif; ?>
                                            <div class="col-md-1 r">
												<?= form_dropdown('discount_groups[' . $k . '][discount_type]', options('flat_percent'), $v['discount_type'], 'class="form-control"') ?>
                                            </div>
                                            <div class="col-md-1 r">
                                                <input type="number" name="discount_groups[<?= $k ?>][points]"
                                                       value="<?= set_value('points', $v['points']) ?>"
                                                       class="<?= css_error('points') ?> form-control"/>
                                            </div>
                                            <div class="col-md-2 r">
                                                <div class="input-group">
													<?= form_input('discount_groups[' . $k . '][start_date]', set_value('end_date', $v['start_date']), 'class="' . css_error('end_date') . ' form-control datepicker-input"') ?>
                                                    <span class="input-group-addon"><i
                                                                class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-2 r">
                                                <div class="input-group">
													<?= form_input('discount_groups[' . $k . '][end_date]', set_value('end_date', $v['end_date']), 'class="' . css_error('end_date') . ' form-control datepicker-input"') ?>
                                                    <span class="input-group-addon"><i
                                                                class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-1 r">
                                                <input type="number" name="discount_groups[<?= $k ?>][priority]"
                                                       value="<?= $v['priority'] ?>" class="form-control"/>
                                                <input type="hidden" name="discount_groups[<?= $k ?>][id]"
                                                       value="<?= $v['id'] ?>"/>
                                            </div>
                                            <div class="col-md-1 r">
                                                <a href="javascript:remove_div('#discountdiv-<?= $k ?>')"
                                                   class="btn btn-danger block-phone <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
                                            </div>
                                        </div>
                                        <hr/>
                                    </div>
									<?php $k++; ?>
								<?php endforeach; ?>
							<?php else: ?>
                                <div id="discountdiv-<?= $k ?>"></div>
							<?php endif; ?>
                        </div>
                    </div>
				<?php break; } ?>
                <div class="tab-pane" id="cross_sell">
                    <h3 class="text-capitalize"> <?= lang('marketing_cross_sells') ?></h3>
                    <span><?= lang('up_sell_and_crossell_other_products_with_this_product') ?></span>
                    <hr/>
                    <div class="form-group">
						<?= lang('up_sell_with_similar_product_tags', 'enable_up_sell', array('class' => 'col-lg-3 control-label')) ?>
                        <div class="col-lg-5">
							<?= form_dropdown('enable_up_sell', options('yes_no'), $row['enable_up_sell'], 'id="enable_up_sell" class="form-control"') ?>
                        </div>
                    </div>
                    <hr/>
                    <div class="form-group">
						<?= lang('cross_sell_these_products', 'product_cross_sell', array('class' => 'col-lg-3 control-label')) ?>
                        <div class="col-lg-5">
                            <select multiple id="product_cross_sell" class="form-control select2"
                                    name="product_cross_sell[]">
								<?php if (!empty($row['cross_sell'])): ?>
									<?php foreach ($row['cross_sell'] as $v): ?>
										<?php if (!empty($v['product_cross_sell_id'])): ?>
                                            <option value="<?= $v['product_cross_sell_id'] ?>" selected>
												<?= $v['product_name'] ?></option>
										<?php endif; ?>
									<?php endforeach; ?>
								<?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>
				<?php if (config_enabled('affiliate_marketing')): ?>
                    <div class="tab-pane" id="affiliate_marketing">
                        <h3 class="text-capitalize"> <?= lang('affiliate_groups') ?></h3>
                        <span><?= lang('set_custom_commission_amounts_per_group') ?></span>
                        <hr/>
                        <div class="form-group">
							<?= lang('disable_commissions_for_product', 'disable_commissions', array('class' => 'col-lg-4 control-label')) ?>
                            <div class="col-lg-4">
								<?= form_dropdown('disable_commissions', options('yes_no'), $row['disable_commissions'], 'class="disable-comms form-control"') ?>
                            </div>
                        </div>
                        <hr/>
                        <div id="disable-comms"
						     <?php if ($row['disable_commissions'] == '1'): ?>style="display:none"<?php endif; ?>>
                            <div class="form-group">
								<?= lang('enable_custom_commissions_per_product', 'enable_custom_commissions', array('class' => 'col-lg-4 control-label')) ?>
                                <div class="col-lg-4">
									<?= form_dropdown('enable_custom_commissions', options('yes_no'), $row['enable_custom_commissions'], 'class="enable-comms form-control"') ?>
                                </div>
                            </div>
                            <hr/>
                            <div id="enable-comms" class="box-info">
                                <div class="overflow">
                    <span class="pull-right">
	                    <a href="<?= admin_url(TBL_AFFILIATE_GROUPS . '/update_product_affiliate_groups/' . $id) ?>"
                           id="manage_affiliate_groups"
                           class="btn btn-primary add_product_groups"><?= i('fa fa-plus') ?> <?= lang('assign_affiliate_groups') ?></a></span>
                                    <h5 class="text-capitalize"><?= lang('configure_commissions_per_affiliate_group') ?></h5>
                                    <hr/>
									<?php if (!empty($row['affiliate_groups'])): ?>
                                        <table class="table table-striped table-responsive table-hover">
                                            <thead>
                                            <tr>
                                                <th><?= lang('group_name') ?></th>
                                                <th><?= lang('check_to_enable_affiliate_commission_levels - ' . $sts_affiliate_commission_levels) ?></th>
                                                <th class="text-center"><?= lang('type') ?></th>
                                            </tr>
                                            </thead>
                                            <tbody>
											<?php foreach ($row['affiliate_groups'] as $k => $v): ?>
                                                <tr>
                                                    <td style="width: 15%"><?= $v['aff_group_name'] ?></td>
                                                    <td>
														<?php for ($i = 1; $i <= $sts_affiliate_commission_levels; $i++): ?>
                                                            <div class="pull-left col-lg-3 comm-levels">
                                                                <div class="input-group">
                                            <span class="input-group-addon">
                                            <?= form_checkbox('affiliate_groups[' . $k . '][enable_level_' . $i . ']', '1', $v['enable_level_' . $i]); ?>
                                            </span>
																	<?= form_input('affiliate_groups[' . $k . '][commission_level_' . $i . ']', $v['commission_level_' . $i], 'placeholder="' . lang('level') . ' ' . $i . '" class="form-control"') ?>
																	<?= form_hidden('affiliate_groups[' . $k . '][id]', $v['id']); ?>
                                                                </div>
                                                            </div>
														<?php endfor ?>
                                                    </td>
                                                    <td class="text-center"><span
                                                                class="label label-primary"><?= $v['commission_type'] ?></span>
                                                    </td>
                                                </tr>
											<?php endforeach; ?>
                                            </tbody>
                                        </table>
									<?php else: ?>
                                        <div
                                                class="alert alert-warning"><?= i('fa fa-info-circle') ?> <?= lang('no_affiliate_groups_assigned') ?></div>
									<?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
				<?php endif; ?>
                <div class="tab-pane" id="options">
                    <div class="hidden-xs">
                        <h3 class="header capitalize"><?= lang('custom_product_options') ?> </h3>
                    </div>
                    <hr/>
                    <div class="form-group">
						<?= lang('sku', 'product_sku', array('class' => 'col-lg-3 control-label')) ?>
                        <div class="col-lg-2">
							<?= form_input('product_sku', set_value('product_sku', $row['product_sku']), 'class="' . css_error('product_sku') . ' form-control"') ?>
                        </div>
                        <hr class="hidden-lg"/>
						<?= lang('upc', 'product_upc', array('class' => 'col-lg-2 control-label')) ?>
                        <div class="col-lg-2">
							<?= form_input('product_upc', set_value('product_upc', $row['product_upc']), 'class="' . css_error('product_upc') . ' form-control"') ?>
                        </div>
                    </div>
                    <hr/>
                    <div class="form-group">
						<?= lang('ean', 'product_ean', array('class' => 'col-lg-3 control-label')) ?>
                        <div class="col-lg-2">
							<?= form_input('product_ean', set_value('product_ean', $row['product_ean']), 'class="' . css_error('product_ean') . ' form-control"') ?>
                        </div>
                        <hr class="hidden-lg"/>
						<?= lang('jan', 'product_jan', array('class' => 'col-lg-2 control-label')) ?>
                        <div class="col-lg-2">
							<?= form_input('product_jan', set_value('product_jan', $row['product_jan']), 'class="' . css_error('product_jan') . ' form-control"') ?>
                        </div>
                    </div>
                    <hr/>
                    <div class="form-group">
						<?= lang('isbn', 'product_isbn', array('class' => 'col-lg-3 control-label')) ?>
                        <div class="col-lg-2">
							<?= form_input('product_isbn', set_value('product_isbn', $row['product_isbn']), 'class="' . css_error('product_isbn') . ' form-control"') ?>
                        </div>
                        <hr class="hidden-lg"/>
						<?= lang('mpn', 'product_mpn', array('class' => 'col-lg-2 control-label')) ?>
                        <div class="col-lg-2">
							<?= form_input('product_mpn', set_value('product_mpn', $row['product_mpn']), 'class="' . css_error('product_mpn') . ' form-control"') ?>
                        </div>
                    </div>
                    <hr/>
                    <div class="form-group">
						<?= lang('add_mailing_list', 'add_mailing_list', array('class' => 'col-lg-3 control-label')) ?>
                        <div class="col-lg-2">
                            <select id="add_mailing_list" class="ajax_mailing_list form-control select2"
                                    name="add_mailing_list">
                                <option value="<?= $row['add_mailing_list'] ?>"
                                        selected><?= $row['add_list_name'] ?></option>
                            </select>
                        </div>
                        <hr class="hidden-lg"/>
						<?= lang('remove_mailing_list', 'remove_mailing_list', array('class' => 'col-lg-2 control-label')) ?>
                        <div class="col-lg-2">
                            <select id="remove_mailing_list" class="ajax_mailing_list form-control select2"
                                    name="remove_mailing_list">
                                <option value="<?= $row['remove_mailing_list'] ?>"
                                        selected><?= $row['remove_list_name'] ?></option>
                            </select>
                        </div>
                    </div>
                    <hr/>
					<?php if ($row['product_type'] == 'subscription'): ?>
                        <div class="form-group">
							<?= lang('add_to_affiliate_group', 'add_to_affiliate_group', array('class' => 'col-lg-3 control-label')) ?>
                            <div class="col-lg-2">
                                <select id="aff_group_name" class="form-control select2" name="affiliate_group">
                                    <option value="<?= $row['affiliate_group'] ?>"
                                            selected><?= $row['aff_group_name'] ?></option>
                                </select>
                            </div>
                            <hr class="hidden-lg"/>
							<?= lang('add_to_discount_group', 'add_to_discount_group', array('class' => 'col-lg-2 control-label')) ?>
                            <div class="col-lg-2">
                                <select id="disc_group_name" class="form-control select2" name="discount_group">
                                    <option value="<?= $row['discount_group'] ?>"
                                            selected><?= $row['disc_group_name'] ?></option>
                                </select>
                            </div>
                        </div>
                        <hr/>
					<?php endif; ?>
					<?php if ($row['product_type'] == 'subscription'): ?>
                        <div class="form-group">
							<?= lang('add_to_blog_group', 'add_to_blog_group', array('class' => 'col-lg-3 control-label')) ?>
                            <div class="col-lg-2">
                                <select id="blog_group_name" class="form-control select2" name="blog_group">
                                    <option value="<?= $row['blog_group'] ?>"
                                            selected><?= $row['blog_group_name'] ?></option>
                                </select>
                            </div>
                        </div>
                        <hr/>
					<?php endif; ?>
                    <div class="form-group">
						<?= lang('product_views', 'product_views', array('class' => 'col-lg-3 control-label')) ?>
                        <div class="col-lg-2">
							<?= form_input('product_views', set_value('product_views', $row['product_views']), 'class="' . css_error('product_views') . ' form-control"') ?>
                        </div>
						<?php if (!empty($row['page_templates'])): ?>
							<?= lang('product_page_template', 'product_page_template', array('class' => 'col-lg-2 control-label')) ?>
                            <div class="col-lg-2">
								<?= form_dropdown('product_page_template', options('product_page_template', FALSE, $row['page_templates']), $row['product_page_template'], 'class=" form-control"') ?>
                            </div>
						<?php endif; ?>
                    </div>
                </div>
            </div>
			<?php if (CONTROLLER_FUNCTION == 'update'): ?>
				<?= form_hidden('product_id', $id) ?>
				<?= form_hidden('product_type', $row['product_type']) ?>
			<?php endif; ?>
            <nav class="navbar navbar-fixed-bottom  save-changes">
                <div class="container text-right">
                    <div class="row">
                        <div class="col-lg-12">
                            <button id="save-changes"
                                    class="btn btn-info navbar-btn block-phone" <?= is_disabled('update', TRUE) ?>
                                    type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
                        </div>
                    </div>
                </div>
            </nav>
			<?= form_close() ?>
        </div>
    </div>
    <!-- Load JS for Page -->
    <script>
        var next_id = <?=count($row['photos']) + 1?>;
        var next_dg_id = <?=$k?>;
        var next_price_id = <?=$n?>;

        $("select.disable-comms").change(function () {
            $("select.disable-comms option:selected").each(function () {
                if ($(this).attr("value") == "1") {
                    $("#disable-comms").hide(300);
                }
                if ($(this).attr("value") == "0") {
                    $("#disable-comms").show(300);
                }
            });
        }).change();
        $("select.enable-comms").change(function () {
            $("select.enable-comms option:selected").each(function () {
                if ($(this).attr("value") == "0") {
                    $("#enable-comms").hide(300);
                }
                if ($(this).attr("value") == "1") {
                    $("#enable-comms").show(300);
                }
            });
        }).change();

        function add_image(image_id) {
            var html = '<div class="col-lg-2" id="imagediv-' + next_id + '" class="animated fadeIn"><div class="thumbnail"><div class="photo-panel">';
            html += '   <a href="<?=base_url()?>filemanager/dialog.php?type=1&akey=<?=$file_manager_key?>&field_id=' + next_id + '&fldr=products" class="iframe">';
            html += '   <img src="<?=base_url(TPL_DEFAULT_PRODUCT_PHOTO)?>" id="image-' + next_id + '"/>';
            html += '   </a>';
            html += '<input type="hidden" name="images[' + next_id + '][photo_file_name]" value="" id="' + next_id + '" />';
            html += '</div>';
            html += '<div class="caption text-center">';
            html += '   <a href="javascript:remove_div(\'#imagediv-' + next_id + '\')" class="btn btn-danger btn-sm <?=is_disabled('delete')?>"><?=i('fa fa-trash-o')?></a>';
            html += '</div>';

            $('#photo-div').append(html);
            $(".iframe").colorbox({iframe: true, width: "50%", height: "50%"});
            next_id++;
        }

        function add_subscription_pricing(mid) {
            var html = '    <div id="pricingdiv-' + next_price_id + '">';
            html += '       <div class="row">';
            html += '            <div class="col-md-2 r">';
            html += '               <div class="input-group">';
            html += '   	            <span class="input-group-addon">';
            html += '   	                <input type="checkbox" name="pricing_options[' + next_price_id + '][enable]" value="1"  />';
            html += '                   </span>';
            html += '                   <input type="text" name="pricing_options[' + next_price_id + '][amount]" value="1.00" class="form-control" />';
            html += '               </div>';
            html += '            </div>';
            html += '            <div class="col-md-1 r">';
            html += '                 <input type="number" name="pricing_options[' + next_price_id + '][interval_amount]" value="1" class="form-control">';
            html += '                 </div>';
            html += '            <div class="col-md-1 r">';
            html += '                <select name="pricing_options[' + next_price_id + '][interval_type]" class="form-control">';
			<?php foreach ($recurring_interval_types as $v):  ?>
            html += '         <option value"<?=$v?>"><?=lang($v)?></option>';
			<?php endforeach; ?>
            html += '            </select>';
            html += '       </div>';
            html += '       <div class="col-md-1 r">';
            html += '           <input type="number" name="pricing_options[' + next_price_id + '][recurrence]" class="form-control">';
            html += '       </div>';
            html += '       <div class="col-md-2 r">';
            html += '           <div class="input-group">';
            html += '               <span class="input-group-addon">';
            html += '                   <input type="checkbox" name="pricing_options[' + next_price_id + '][enable_initial_amount]" value="1"  />';
            html += '               </span>';
            html += '               <input type="text" name="pricing_options[' + next_price_id + '][initial_amount]" class="form-control" />';
            html += '          </div>';
            html += '       </div>';
            html += '       <div class="col-md-1 r">';
            html += '           <input type="number" name="pricing_options[' + next_price_id + '][initial_interval]" value="1" class="form-control">';
            html += '       </div>';
            html += '       <div class="col-md-1 r">';
            html += '           <select name="pricing_options[' + next_price_id + '][initial_interval_type]" class="form-control">';
            html += '               <option value="month" selected="selected">month</option>';
			<?php foreach ($recurring_interval_types as $v):  ?>
            html += '         <option value"<?=$v?>"><?=lang($v)?></option>';
			<?php endforeach; ?>
            html += '          </select>';
            html += '       </div>';
            html += '           <div class="col-md-2 r">';
            html += '	            <input type="text" name="pricing_options[' + next_price_id + '][name]" value="" class="form-control">';
            html += '           </div>';
            html += '           <div class="col-md-1 r">';
            html += '               <a href="javascript:remove_div(\'#pricingdiv-' + next_price_id + '\')" class="btn btn-danger block-phone "><i class="fa fa-trash-o "></i></a>';
            html += '           </div>';
            html += '       </div>';
            html += '       <hr />';
            html += '   </div>';
            $('#pricing-div').append(html);
            $('input').iCheck({
                checkboxClass: 'icheckbox_minimal-grey',
                radioClass: 'iradio_minimal-grey',
                increaseArea: '20%' // optional
            });
            next_price_id++;
        }

        function add_discount_group(dgid) {

            var html = '<div id="discountdiv-' + next_dg_id + '" class="animated fadeIn">';
            html += '<div class="row">';
            html += '<div class="col-lg-2 r">';
            html += '   <select name="discount_groups[' + next_dg_id + '][group_id]" class="form-control">';
			<?php if (!empty($row['dg_array'])): ?>
			<?php foreach ($row['dg_array'] as $k => $v): ?>
            html += '   <option value="<?=$k?>"><?=$v?></option>';
			<?php endforeach; ?>
			<?php endif; ?>
            html += '   </select>';
            html += '</div>';
            html += '<div class="col-lg-1 r">';
            html += '   <input type="text" name="discount_groups[' + next_dg_id + '][group_amount]" placeholder="<?=lang('amount')?>" value="" class="form-control"  />';
            html += '</div>';
            html += '<div class="col-lg-1 r">';
            html += '   <input type="text" name="discount_groups[' + next_dg_id + '][quantity]"  value="1" class="form-control" <?php if ($row['product_type'] == 'subscription'): ?>readonly<?php endif; ?>/>';
            html += '</div>';
            html += '<div class="col-lg-1 r">';
            html += '   <select name="discount_groups[' + next_dg_id + '][discount_type]" class="form-control">';
            html += '       <option value="flat"><?=lang('flat')?></option>';
            html += '       <option value="percent"><?=lang('percent')?></option>';
            html += '   </select>';
            html += '</div>';
            html += '<div class="col-lg-1 r">';
            html += '   <input type="number" name="discount_groups[' + next_dg_id + '][points]" value="0" placeholder="<?=lang('points')?>" class=" form-control" />';
            html += '</div>';
            html += '<div class="col-lg-2 r">';
            html += '   <div class="input-group">';
            html += '       <input type="text" name="discount_groups[' + next_dg_id + '][start_date]" placeholder="<?=lang('start_date')?>"  class="form-control datepicker-input" />';
            html += '       <span class="input-group-addon"><i class="fa fa-calendar"></i></span>';
            html += '   </div>';
            html += '</div>';
            html += '<div class="col-lg-2 r">';
            html += '   <div class="input-group">';
            html += '       <input type="text" name="discount_groups[' + next_dg_id + '][end_date]" placeholder="<?=lang('end_date')?>"  class="form-control datepicker-input" />';
            html += '       <span class="input-group-addon"><i class="fa fa-calendar"></i></span>';
            html += '   </div>';
            html += '</div>';
            html += '<div class="col-lg-1 r">';
            html += '   <input type="number" name="discount_groups[' + next_dg_id + '][priority]" value="' + next_dg_id + '" placeholder="<?=lang('priority')?>" class="form-control"/>';
            html += '</div>';
            html += '<div class="col-lg-1 r">';
            html += '   <a href="javascript:remove_div(\'#discountdiv-' + next_dg_id + '\')" class="btn btn-danger block-phone <?=is_disabled('delete')?>"><?=i('fa fa-trash-o')?></a>';
            html += '</div>';
            html += '</div>';
            html += '<hr />';
            html += '</div>';

            $('#discount-div').append(html);
            $('.datepicker-input').datepicker({format: '<?=$format_date?>'});
            next_dg_id++;
        }

        $("select#expires").change(function () {
            $("select#expires option:selected").each(function () {
                if ($(this).attr("value") == "1") {
                    $("#date_expires").show(100);
                }
                else {
                    $("#date_expires").hide(100);
                }
            });
        }).change();

		<?=html_editor('init', TBL_PRODUCTS)?>

        $("#tags").select2({
            tags: true
        });

        $("#aff_group_name").select2({
            ajax: {
                url: '<?=admin_url(TBL_AFFILIATE_GROUPS . '/search/ajax/')?>',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        aff_group_name: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, page) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                id: item.group_id,
                                text: item.aff_group_name
                            }
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 2
        });

        $("#product_cross_sell").select2({
            ajax: {
                url: '<?=admin_url(TBL_PRODUCTS . '/search/ajax/')?>',
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
            minimumInputLength: 2
        });

        $("#blog_group_name").select2({
            ajax: {
                url: '<?=admin_url(TBL_BLOG_GROUPS . '/search/ajax/')?>',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        blog_group_name: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, page) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                id: item.group_id,
                                text: item.group_name
                            }
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 2
        });

        $("#disc_group_name").select2({
            ajax: {
                url: '<?=admin_url(TBL_DISCOUNT_GROUPS . '/search/ajax/')?>',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        disc_group_name: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, page) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                id: item.group_id,
                                text: item.group_name
                            }
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 2
        });

        //add mailing list
        $(".ajax_mailing_list").select2({
            ajax: {
                url: '<?=admin_url(TBL_EMAIL_MAILING_LISTS . '/search/ajax/')?>',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        list_name: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, page) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                id: item.list_id,
                                text: item.list_name
                            }
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 2
        });

        //product categories
        $("#product_categories").select2({
            ajax: {
                url: '<?=admin_url(TBL_PRODUCTS_CATEGORIES . '/search/ajax/')?>',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        category_name: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, page) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                id: item.category_id,
                                text: item.category_name
                            }
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 2
        });

        //videos
        $("#videos").select2({
            ajax: {
                url: '<?=admin_url(TBL_VIDEOS . '/search/ajax/')?>',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        video_name: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, page) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                id: item.video_id,
                                text: item.video_name
                            }
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 2
        });

        $("#video_as_default").select2({
            ajax: {
                url: '<?=admin_url(TBL_VIDEOS . '/search/ajax/')?>',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        video_name: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, page) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                id: item.video_id,
                                text: item.video_name
                            }
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 2
        });

        //brand id
        $("#brand_id").select2({
            ajax: {
                url: '<?=admin_url(TBL_BRANDS . '/search/ajax/')?>',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        brand_name: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, page) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                id: item.brand_id,
                                text: item.brand_name
                            }
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 2
        });
        //supplier id
        $("#supplier_id").select2({
            ajax: {
                url: '<?=admin_url(TBL_SUPPLIERS . '/search/ajax/')?>',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        supplier_name: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, page) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                id: item.supplier_id,
                                text: item.supplier_name
                            }
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 2
        });
        //attributes
        $("#product_attributes").select2({
            ajax: {
                url: '<?=admin_url(TBL_PRODUCTS_ATTRIBUTES . '/search/ajax/')?>',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        attribute_name: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, page) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                id: item.attribute_id,
                                text: item.attribute_name
                            }
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 2
        });
        //downloads
        $("#product_downloads").select2({
            ajax: {
                url: '<?=admin_url(TBL_PRODUCTS_DOWNLOADS . '/search/ajax/')?>',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        download_name: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, page) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                id: item.download_id,
                                text: item.download_name
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
                tinyMCE.triggerSave();
                $.ajax({
                    url: '<?=current_url()?>',
                    type: 'POST',
                    dataType: 'json',
                    data: $('#form').serialize(),
                    beforeSend: function () {
                        $('#save-changes').button('loading');
                    },
                    complete: function () {
                        $('#save-changes').button('reset');
                    },
                    success: function (response) {
                        if (response.type == 'success') {
                            $('.alert-danger').remove();
                            $('.form-control').removeClass('error');

                            if (response['data']) {
                                $.each(response['data'], function (key, val) {
                                    $('.' + key).html(val);
                                });
                            }

                            //$('#response').html('<?=alert('success')?>');
                            window.location.reload();

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
                        console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    }
                });
            }
        });

    </script>
<?php endif; ?>
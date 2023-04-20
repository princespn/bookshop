<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (empty($row)): ?>
	<?= tpl_no_values('', '', 'no_records_found') ?>
<?php else: ?>
<div class="row">
    <div class="col-lg-8 col-md-6">
		<h2 class="sub-header block-title"><?= i('fa fa-pencil') ?> <?= lang(CONTROLLER_FUNCTION . '_' . singular(CONTROLLER_CLASS)) ?> - <?= lang('third_party_store') ?></h2>
	</div>
    <div class="col-lg-4 col-md-6 text-right">
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('left', CONTROLLER_METHOD, $row[ 'prev' ]) ?>
			<?php if ($id > 1): ?>
				<a data-href="<?= admin_url(TBL_PRODUCTS . '/delete/' . $id) ?>" data-toggle="modal"
				   data-target="#confirm-delete" href="#" <?= is_disabled('delete') ?>
				   FALSE class="md-trigger btn btn-danger"><?= i('fa fa-trash-o') ?> <span
						class="hidden-xs"><?= lang('delete') ?></span></a>
			<?php endif; ?>
		<?php endif; ?>
        <a href="<?= admin_url(TBL_PRODUCTS . '/duplicate/' . $id) ?>" class="btn btn-info"><?=i('fa fa-copy')?> <?=lang('duplicate')?></a>
		<a href="<?= admin_url(TBL_PRODUCTS) ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
				class="hidden-xs"><?= lang('view_products') ?></span></a>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('right', CONTROLLER_METHOD, $row[ 'next' ]) ?>
		<?php endif; ?>
	</div>
</div>
<hr/>
<div class="row">
	<div class="col-lg-12">
		<?= form_open_multipart('', 'role="form" id="form" class="form-horizontal"') ?>
		<div class="box-info">
			<ul class="nav nav-tabs resp-tabs text-capitalize" role="tablist">
				<li class="active"><a href="#availability" role="tab" data-toggle="tab"><?= lang('availability') ?></a>
				</li>
				<li><a href="#pricing" role="tab" data-toggle="tab"><?= lang('pricing') ?></a></li>
				<li><a href="#name" role="tab" data-toggle="tab"><?= lang('description') ?></a></li>
				<li><a href="#specs" role="tab" data-toggle="tab"><?= lang('specifications') ?></a></li>
				<li><a href="#media" role="tab" data-toggle="tab"><?= lang('media') ?></a></li>
				<li><a href="#categories" role="tab" data-toggle="tab"><?= lang('categories') ?></a></li>
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
										<small><a href="<?=page_url('product', $row)?>" target="_blank">
												<?=lang('view_product_page')?>
											</a> </small>
									</li>
									<li class="active">
										<small><?= lang('product_id') ?>: <?= $id ?></small>
									</li>
									<li class="active">
										<small><?= lang('last_update') ?>
											: <?= display_date($row[ 'modified' ]) ?></small>
									</li>
								</ul>
							</div>
						</div>
					</div>
					<div class="col-lg-10">
						<div class="hidden-xs">
							<h3 class="header"><?= $row[ 'product_name' ] ?> <span
									class="pull-right label label-info"><?= lang('third_party_store') ?></span></h3>
							<span><?= word_limiter($row[ 'product_overview' ], 25) ?></span>
						</div>
						<hr/>
						<div class="form-group">
							<?= lang('status', 'product_status', array( 'class' => 'col-lg-3 control-label' )) ?>
							<div class="col-lg-2">
								<?= form_dropdown('product_status', options('active'), $row[ 'product_status' ], 'class="form-control"') ?>
							</div>
							<hr class="hidden-lg">
							<?= lang('featured', 'product_featured', array( 'class' => 'col-lg-2 control-label' )) ?>
							<div class="col-lg-2">
								<?= form_dropdown('product_featured', options('yes_no'), $row[ 'product_featured' ], 'class="form-control"') ?>
							</div>
						</div>
						<hr/>
						<div class="form-group">
							<?= lang('expires', 'date_expires', array( 'class' => 'col-lg-3 control-label' )) ?>
							<div class="col-lg-2">
								<div class="input-group">
									<?= form_input('date_expires', set_value('date_expires', $row[ 'date_expires_formatted' ]), 'class="' . css_error('date_expires') . ' form-control datepicker-input"') ?>
									<span class="input-group-addon"><i class="fa fa-calendar"></i> </span>
								</div>
							</div>
							<hr class="hidden-lg"/>
							<?= lang('hidden_product', 'hidden_product', array( 'class' => 'col-lg-2 control-label' )) ?>
							<div class="col-lg-2">
								<?= form_dropdown('hidden_product', options('yes_no'), $row[ 'hidden_product' ], 'class="form-control"') ?>
							</div>
						</div>
						<hr/>
						<div class="form-group">
							<?= lang('affiliate_redirect', 'affiliate_redirect', array( 'class' => 'col-lg-3 control-label' )) ?>
							<div class="col-lg-6">
								<?= form_input('affiliate_redirect', set_value('affiliate_redirect', $row[ 'affiliate_redirect' ]), 'placeholder="http://www.third_party_domain.com/affiliate_page.html" class="' . css_error('affiliate_redirect') . ' form-control required"') ?>
							</div>
						</div>
						<hr/>
					</div>
				</div>
				<div class="tab-pane" id="pricing">
					<h3 class="header capitalize"><?= lang('product_pricing') ?> </h3>
					<span><?= lang('setup_pricing_options_for_product') ?></span>
					<hr/>
						<div class="form-group">
							<?= lang('product_price', 'product_price', array( 'class' => 'col-lg-3 control-label' )) ?>
							<div class="col-lg-2">
								<?= form_input('product_price', set_value('product_price', $row[ 'product_price' ]), 'class="' . css_error('product_price') . ' form-control"') ?>
							</div>
						</div>
						<hr/>
				</div>
				<div class="tab-pane" id="name">
					<div class="hidden-xs">
						<h3 class="text-capitalize"> <?= lang('product_description') ?></h3>
						<span><?= lang('set_locale_specific_descriptions_each_tab') ?></span>
					</div>
					<hr/>
					<ul class="nav nav-tabs text-capitalize" role="tablist">
						<?php foreach ($row[ 'name' ] as $v): ?>
							<li <?php if ($v[ 'language_id' ] == $sts_site_default_language): ?> class="active" <?php endif; ?>>
								<a href="#<?= $v[ 'image' ] ?>" data-toggle="tab"><?= i('flag-' . $v[ 'image' ]) ?>
									<span
										class="visible-lg"><?= $v[ 'name' ] ?></span></a>
							</li>
						<?php endforeach; ?>
					</ul>
					<div class="tab-content">
						<?php foreach ($row[ 'name' ] as $v): ?>
							<div
								class="tab-pane <?php if ($v[ 'language_id' ] == $sts_site_default_language): ?> active <?php endif; ?>"
								id="<?= $v[ 'image' ] ?>">
								<hr/>
								<div class="form-group">
									<?= lang('product_name', 'product_name', array( 'class' => 'col-lg-2 control-label' )) ?>
									<div class="col-lg-6">
										<?= form_input('product_name[' . $v[ 'language_id' ] . '][product_name]', set_value('product_name[' . $v[ 'product_name_id' ] . ']', $v[ 'product_name' ], FALSE), 'class="' . css_error('product_name[' . $v[ 'product_name_id' ] . '][product_name]') . ' form-control"') ?>
									</div>
								</div>
								<hr/>
								<div class="form-group">
									<?= lang('product_overview', 'product_overview', array( 'class' => 'col-lg-2 control-label' )) ?>
									<div class="col-lg-9">
										<?= form_input('product_name[' . $v[ 'language_id' ] . '][product_overview]', set_value('product_name[' . $v[ 'product_name_id' ] . ']', $v[ 'product_overview' ], FALSE), 'class="' . css_error('product_name[' . $v[ 'product_name_id' ] . '][product_overview]') . ' form-control"') ?>

									</div>
								</div>
								<hr/>
								<div class="form-group">
									<?= lang('product_description', 'product_description', array( 'class' => 'col-lg-2 control-label' )) ?>
									<div class="col-lg-9">
										<?= form_textarea('product_name[' . $v[ 'language_id' ] . '][product_description]', set_value('product_name[' . $v[ 'product_name_id' ] . '][product_description]', $v[ 'product_description' ], FALSE), 'class="editor ' . css_error('product_name[' . $v[ 'product_name_id' ] . '][product_description]') . ' form-control"') ?>
									</div>
								</div>
								<hr/>
								<div class="form-group">
									<?= lang('meta_title', 'meta_title', array( 'class' => 'col-lg-2 control-label' )) ?>
									<div class="col-lg-6">
										<?= form_input('product_name[' . $v[ 'language_id' ] . '][meta_title]', set_value('product_name[' . $v[ 'product_name_id' ] . '][meta_title]', $v[ 'meta_title' ]), 'class="' . css_error('product_name[' . $v[ 'product_name_id' ] . '][meta_title]') . ' form-control"') ?>
									</div>
								</div>
								<hr/>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
				<div class="tab-pane" id="specs">
					<div class="hidden-xs">
						<h3 class="text-capitalize"> <?= lang('product_specifications') ?><span class="pull-right"><a
									href="<?= admin_url(TBL_PRODUCTS_SPECIFICATIONS . '/update_product_specifications/' . $id) ?>"
									id="manage_specifications"
									class="btn btn-primary colorbox"><?= i('fa fa-plus') ?> <?= lang('assign_product_specifications') ?></a></span>
						</h3>
						<span><?= lang('add_specifications_to_product') ?></span>
					</div>
					<hr/>
					<?php if (!empty($row[ 'product_specs' ])): ?>
						<ul class="nav nav-tabs text-capitalize" role="tablist">

							<?php foreach ($row[ 'product_specs' ] as $k => $v): ?>
								<li <?php if ($k == $sts_site_default_language): ?> class="active" <?php endif; ?>>
									<a href="#specs<?= $v[ 0 ][ 'name' ] ?>"
									   data-toggle="tab"><?= i('flag-' . $v[ 0 ][ 'image' ]) ?> <span
											class="visible-lg"><?= $v[ 0 ][ 'name' ] ?></span></a>
								</li>
							<?php endforeach; ?>
						</ul>
						<div class="tab-content">
							<?php $a = 1; ?>
							<?php foreach ($row[ 'product_specs' ] as $k => $v): ?>
								<div
									class="tab-pane <?php if ($k == $sts_site_default_language): ?>active<?php endif; ?>"
									id="specs<?= $v[ 0 ][ 'name' ] ?>">
									<?php for ($s = 0; $s < count($v); $s++): ?>
										<div id="specsdiv-<?= $a ?>" class="specsdiv-<?= $s ?>">
											<hr/>
											<div class="row">
												<div class="form-group">
													<?= lang($v[ $s ][ 'specification_name' ], $v[ $s ][ 'specification_name' ], array( 'class' => 'col-lg-3 control-label' )) ?>
													<div class="col-md-7">
                                        <textarea class="form-control" rows="3"
                                                  name="product_specs[<?= $a ?>][spec_value]"><?= $v[ $s ][ 'spec_value' ] ?></textarea>
														<?= form_hidden('product_specs[' . $a . '][prod_spec_id]', $v[ $s ][ 'prod_spec_id' ]) ?>
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
						<li class="active"><a href="#imagesub" role="tab" data-toggle="tab"><?= lang('images') ?></a>
						</li>
						<li><a href="#videosub" role="tab" data-toggle="tab"><?= lang('videos') ?></a></li></ul>
					<div class="tab-content">
						<div class="tab-pane active" id="imagesub">
							<h3 class="text-capitalize"><span class="pull-right"><a
										href="javascript:add_image(<?= count($row[ 'photos' ]) ?>)"
										class="btn btn-primary <?= is_disabled('update', true) ?>"><?= i('fa fa-plus') ?> <?= lang('add_new_image') ?></a></span> <?= lang('product_images') ?>
							</h3>
							<span><?= lang('only_jpg_gif_png_files_are_allowed') ?></span>
							<hr/>
							<div class="collapse in" id="photos">
								<div class="row" id="photo-div">
									<?php $i = 1; ?>
									<?php if (!empty($row[ 'photos' ])): ?>
										<?php foreach ($row[ 'photos' ] as $v): ?>
											<div class="col-lg-2" id="imagediv-<?= $i ?>">
												<div class="thumbnail">
													<div class="photo-panel">
														<a class='iframe'
														   href="<?= base_url() ?>filemanager/dialog.php?type=1&akey=<?= $file_manager_key ?>&field_id=<?= $i ?>">
															<img id="image-<?= $i ?>"
															     src="<?= base_url('images/uploads/products/' . $v[ 'photo_file_name' ]) ?>"/>
														</a>

														<div class="tip image_id" data-toggle="tooltip"
														     data-placement="bottom"
														     title="<?= lang('photo_id') ?>"><?= $v[ 'photo_id' ] ?></div>
													</div>
													<input type="hidden" name="images[<?= $i ?>][photo_file_name]"
													       value="<?= base_url('images/uploads/products/' . $v[ 'photo_file_name' ]) ?>"
													       id="<?= $i ?>"/>
													<input type="hidden" name="images[<?= $i ?>][photo_id]"
													       value="<?= $v[ 'photo_id' ] ?>" id="<?= $i ?>"/>

													<div class="caption text-center">

														<?php if ($v[ 'product_default' ] == 0): ?>
															<a href="<?= admin_url(CONTROLLER_CLASS . '/set_default_photo/' . $v[ 'photo_id' ]) ?>"
															   class="tip btn btn-default btn-sm block-phone"
															   data-toggle="tooltip"
															   data-placement="bottom"
															   title="<?= lang('set_as_default_image') ?>"><?= i('fa fa-check') ?></a>
															<a href="javascript:remove_div('#imagediv-<?= $i ?>')"
															   class="btn btn-danger btn-sm block-phone <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
														<?php else: ?>
															<button disabled class="btn btn-success btn-sm block-phone"
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
													<a href="<?= base_url() ?>filemanager/dialog.php?type=1&amp;akey=<?= $file_manager_key ?>&field_id=0"
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
									<?= lang('set_videos_as_default_media_on_product_pages', 'video_as_default', array( 'class' => 'col-lg-3 control-label' )) ?>
									<div class="col-lg-1">
										<?= form_dropdown('video_as_default', options('yes_no'), $row[ 'video_as_default' ], 'class="form-control"') ?>
									</div>
									<hr class="hidden-lg"/>
									<?= lang('youtube_playlist', 'youtube_playlist', array( 'class' => 'col-lg-3 control-label' )) ?>
									<div class="col-lg-3">
										<?= form_input('youtube_playlist', set_value('youtube_playlist', $row[ 'youtube_playlist' ]), 'class="' . css_error('youtube_playlist') . ' form-control"') ?>
									</div>
								</div>
								<hr/>
								<div class="form-group">
									<?= lang('assign_videos', 'videos', array( 'class' => 'col-lg-3 control-label' )) ?>
									<div class="col-lg-5">
										<select multiple id="videos" class="form-control select2" name="videos[]">
											<?php if (!empty($row[ 'videos' ])): ?>
												<?php foreach ($row[ 'videos' ] as $v): ?>
													<option value="<?= $v[ 'video_id' ] ?>"
													        selected><?= $v[ 'video_name' ] ?></option>
												<?php endforeach; ?>
											<?php endif; ?>
										</select>
									</div>
								</div>
							</div>
						</div>
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
								<?= lang('assign_product_categories', 'product_categories', array( 'class' => 'col-lg-3 control-label' )) ?>
								<div class="col-lg-5">
									<select multiple id="product_categories" class="form-control select2"
									        name="product_categories[]">
										<?php if (!empty($row[ 'categories' ])): ?>
											<?php foreach ($row[ 'categories' ] as $v): ?>
												<option value="<?= $v[ 'category_id' ] ?>"
												        selected><?= $v[ 'path' ] ?>
													/ <?= $v[ 'category_name' ] ?></option>
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
								<?= lang('assign_product_tags', 'product_tags', array( 'class' => 'col-lg-3 control-label' )) ?>
								<div class="col-lg-5">
									<select multiple id="tags" class="form-control select2"
									        name="product_tags[]">
										<?php if (!empty($row[ 'tags' ])): ?>
											<?php foreach ($row[ 'tags' ] as $v): ?>
												<option value="<?= $v[ 'tag' ] ?>"
												        selected><?= $v[ 'tag' ] ?></option>
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
								<?= lang('brand_name', 'brand_name', array( 'class' => 'col-lg-3 control-label' )) ?>
								<div class="col-lg-5">
									<select id="brand_id" class="form-control select2" name="brand_id">
										<option value="<?= $row[ 'brand_id' ] ?>"
										        selected><?= $row[ 'brand_name' ] ?></option>
									</select>
								</div>
							</div>
						</div>
						<div class="tab-pane" id="supplier">
							<h3 class="text-capitalize"><?= lang('supplier_name') ?></h3>
							<span><?= lang('assign_supplier_to_this_product') ?></span>
							<hr/>
							<div class="form-group">
								<?= lang('supplier_name', 'supplier_name', array( 'class' => 'col-lg-3 control-label' )) ?>
								<div class="col-lg-5">
									<select id="supplier_id" class="form-control select2" name="supplier_id">
										<option value="<?= $row[ 'supplier_id' ] ?>"
										        selected><?= $row[ 'supplier_name' ] ?></option>
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane" id="options">
					<div class="hidden-xs">
						<h3 class="header capitalize"><?= lang('custom_product_options') ?> </h3>
					</div>
					<hr/>
					<div class="form-group">
						<?= lang('sku', 'product_sku', array( 'class' => 'col-lg-3 control-label' )) ?>
						<div class="col-lg-2">
							<?= form_input('product_sku', set_value('product_sku', $row[ 'product_sku' ]), 'class="' . css_error('product_sku') . ' form-control"') ?>
						</div>
						<hr class="hidden-lg"/>
						<?= lang('upc', 'product_upc', array( 'class' => 'col-lg-2 control-label' )) ?>
						<div class="col-lg-2">
							<?= form_input('product_upc', set_value('product_upc', $row[ 'product_upc' ]), 'class="' . css_error('product_upc') . ' form-control"') ?>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<?= lang('ean', 'product_ean', array( 'class' => 'col-lg-3 control-label' )) ?>
						<div class="col-lg-2">
							<?= form_input('product_ean', set_value('product_ean', $row[ 'product_ean' ]), 'class="' . css_error('product_ean') . ' form-control"') ?>
						</div>
						<hr class="hidden-lg"/>
						<?= lang('jan', 'product_jan', array( 'class' => 'col-lg-2 control-label' )) ?>
						<div class="col-lg-2">
							<?= form_input('product_jan', set_value('product_jan', $row[ 'product_jan' ]), 'class="' . css_error('product_jan') . ' form-control"') ?>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<?= lang('isbn', 'product_isbn', array( 'class' => 'col-lg-3 control-label' )) ?>
						<div class="col-lg-2">
							<?= form_input('product_isbn', set_value('product_isbn', $row[ 'product_isbn' ]), 'class="' . css_error('product_isbn') . ' form-control"') ?>
						</div>
						<hr class="hidden-lg"/>
						<?= lang('mpn', 'product_mpn', array( 'class' => 'col-lg-2 control-label' )) ?>
						<div class="col-lg-2">
							<?= form_input('product_mpn', set_value('product_mpn', $row[ 'product_mpn' ]), 'class="' . css_error('product_mpn') . ' form-control"') ?>
						</div>
					</div>
					<hr/>
					<?php if ($row[ 'product_type' ] == 'subscription'): ?>
						<div class="form-group">
							<?= lang('add_to_affiliate_group', 'add_to_affiliate_group', array( 'class' => 'col-lg-3 control-label' )) ?>
							<div class="col-lg-2">
								<select id="aff_group_name" class="form-control select2" name="affiliate_group">
									<option value="<?= $row[ 'affiliate_group' ] ?>"
									        selected><?= $row[ 'aff_group_name' ] ?></option>
								</select>
							</div>
							<hr class="hidden-lg"/>
							<?= lang('add_to_discount_group', 'add_to_discount_group', array( 'class' => 'col-lg-2 control-label' )) ?>
							<div class="col-lg-2">
								<select id="disc_group_name" class="form-control select2" name="discount_group">
									<option value="<?= $row[ 'discount_group' ] ?>"
									        selected><?= $row[ 'disc_group_name' ] ?></option>
								</select>
							</div>
						</div>
						<hr/>
					<?php endif; ?>
					<div class="form-group">
						<?= lang('product_views', 'product_views', array( 'class' => 'col-lg-3 control-label' )) ?>
						<div class="col-lg-2">
							<?= form_input('product_views', set_value('product_views', $row[ 'product_views' ]), 'class="' . css_error('product_views') . ' form-control"') ?>
						</div>
						<?php if (!empty($row[ 'page_templates' ])): ?>
								<?= lang('product_page_template', 'product_page_template', array( 'class' => 'col-lg-2 control-label' )) ?>
								<div class="col-lg-2">
									<?= form_dropdown('product_page_template', options('product_page_template', FALSE, $row[ 'page_templates' ]), $row[ 'product_page_template' ], 'class=" form-control"') ?>
								</div>
						<?php endif; ?>
					</div>
					<hr/>
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
	var next_id = <?=count($row[ 'photos' ]) + 1?>;
	var next_dg_id = <?=$k?>;

	function add_image(image_id) {
		var html = '<div class="col-lg-2" id="imagediv-' + next_id + '" class="animated fadeIn"><div class="thumbnail"><div class="photo-panel">';
		html += '   <a href="<?=base_url()?>filemanager/dialog.php?type=1&akey=<?=$file_manager_key?>&field_id=' + next_id + '" class="iframe">';
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

	<?=html_editor('init', TBL_PRODUCTS)?>

	$("#tags").select2({
		tags: true
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
					<?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	});
</script>
<?php endif; ?>
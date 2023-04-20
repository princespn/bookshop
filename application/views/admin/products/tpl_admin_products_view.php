<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
	<div class="row">
		<div class="col-md-6">
			<div class="input-group text-capitalize">
				<?= generate_sub_headline('products', 'fa-shopping-cart', $rows['total']) ?>
			</div>
		</div>
		<div class="col-md-6 text-right">
			<?= next_page('left', $paginate); ?>
			<a data-toggle="collapse" data-target="#search_block"
			   class="btn btn-primary"><?= i('fa fa-search') ?> <?= lang('search') ?></a>
			<a data-toggle="modal" data-target="#add-product" href="#"
			   class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
					class="hidden-xs"><?= lang('create_product') ?></span></a>
			<?= next_page('right', $paginate); ?>
		</div>
	</div>
	<hr/>
	<div id="search_block" class="collapse">
		<?= form_open(admin_url(CONTROLLER_CLASS . '/general_search'), 'method="get" role="form" id="search-form" class="form-horizontal"') ?>
		<div class="box-info">
			<h4><?=i('fa fa-search')?> <?= lang('search_products') ?></h4>
			<div class="row">
				<div class="col-md-12">
					<div class="input-group">
						<input type="text" name="search_term" class="form-control required" placeholder="<?=lang('enter_search_term')?>">
						<span class="input-group-btn">
				        <button class="btn btn-default" type="submit"><?=lang('search')?></button>
				      </span>
					</div>
				</div>
			</div>
		</div>
		<?=form_close() ?>
	</div>
<?php if (empty($rows['values'])): ?>
	<?= tpl_no_values() ?>
<?php else: ?>
	<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'role="form" id="form"') ?>
	<div class="box-info mass-edit">
		<div class="<?= mobile_view('hidden-xs') ?>">
			<table class="table table-striped table-hover table-responsive">
				<thead class="text-capitalize">
				<tr>
					<th class="text-center"><?= form_checkbox('', '', '', 'class="check-all"') ?></th>
                    <th class="text-center hidden-xs"><?= tb_header('id', '', FALSE) ?></th>
					<th class="hidden-xs"></th>
					<th class="text-center hidden-xs"><?= tb_header('status', 'product_status') ?></th>
					<th class="text-center hidden-xs"><?= tb_header('type', 'product_type') ?></th>
					<th class="text-center hidden-xs"><?= tb_header('sku', 'product_sku') ?></th>
					<th><?= tb_header('name', 'product_name') ?></th>
					<th><?= tb_header('price', 'product_price') ?></th>
					<th class="visible-lg"><?= tb_header('sale_price', 'product_sale_price') ?></th>
					<th class="hidden-xs"><?= tb_header('sort', 'product_sort') ?></th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($rows['values'] as $k => $v): ?>
					<?php $k++ ?>
					<tr>
						<td style="width: 5%" class="text-center">
							<?= form_checkbox('products[' . $v['product_id'] . '][update]', $v['product_id']) ?>
						</td>
                        <td style="width: 5%" class="text-center hidden-xs">
                            <small class="text-muted"><?= $v['product_id'] ?></small>
                        </td>
                        <td style="width: 5%" class="hidden-xs">
							<?= photo(CONTROLLER_METHOD, $v, 'img-thumbnail img-circle dash-photo') ?></td>
						<td style="width: 5%" class="text-center hidden-xs">
							<a href="<?= admin_url('update_status/table/' . CONTROLLER_CLASS . '/type/product_status/key/product_id/id/' . $v['product_id']) ?>"
								class="btn btn-default <?= is_disabled('update', TRUE) ?>"><?= set_status($v['product_status']) ?></a>
						</td>
						<td style="width: 8%" class="text-center hidden-xs">

                            <?php if ($v['product_type'] == 'general'): ?>
                            <?php if ($v['charge_shipping'] == '1'): ?>
                            <span class="label label-primary label-shipped">
		                    <?= lang('shipped') ?>
                            <?php else: ?>
                           <span class="label label-info label-digital">
		                    <?= lang('digital') ?>
                            <?php endif; ?>
                            <?php else: ?>
							<span class="label label-primary label-<?=$v['product_type']?>">
		                    <?= lang($v['product_type']) ?>
		                    <?php endif; ?>
                            </span>
						</td>
						<td style="width: 10%" class="text-center hidden-xs">
							<input
								name="products[<?= $v['product_id'] ?>][product_sku]" <?= is_disabled('update', TRUE) ?>
								type="text" value="<?= $v['product_sku'] ?>" class="form-control required"
								tabindex="<?= $k ?>"/>
						</td>
						<td style="width: 25%">
							<input
								name="products[<?= $v['product_id'] ?>][product_name]" <?= is_disabled('update', TRUE) ?>
								type="text" value="<?= $v['product_name'] ?>" class="form-control required"
								tabindex="<?= $k ?>"/>
						</td>
						<td style="width: 10%">
							<?php if ($v['product_type'] != 'subscription'): ?>
								<input
									name="products[<?= $v['product_id'] ?>][product_price]" <?= is_disabled('update', TRUE) ?>
									type="text" value="<?= input_amount($v['product_price']) ?>" class="form-control required number"
									tabindex="<?= $k ?>"/>
							<?php else: ?>
								<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['product_id']) ?>#pricing">
								<span class="label label-default"><?= lang('varies') ?></span>
								</a>
							<?php endif; ?>
						</td>
						<td style="width: 10%" class="visible-lg">
							<?php if ($v['product_type'] != 'subscription'): ?>
								<input
									name="products[<?= $v['product_id'] ?>][product_sale_price]" <?= is_disabled('update', TRUE) ?>
									type="text" value="<?= input_amount($v['product_sale_price']) ?>"
									class="form-control required number" tabindex="<?= $k ?>"/>
							<?php else: ?>
								<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['product_id']) ?>#pricing">
									<span class="label label-default"><?= lang('varies') ?></span>
								</a>
							<?php endif; ?>
						</td>
						<td style="width: 8%" class="hidden-xs hidden-xs">
							<input
								name="products[<?= $v['product_id'] ?>][sort_order]" <?= is_disabled('update', TRUE) ?>
								type="number" value="<?= $v['sort_order'] ?>" class="form-control digits"
								tabindex="<?= $k ?>"/>
						</td>
						<td class="text-right">
							<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['product_id']) ?>"
							   class="btn btn-default" title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
							<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['product_id']) ?>"
							   data-toggle="modal" data-target="#confirm-delete" href="#"
							   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>">
								<?= i('fa fa-trash-o') ?></a>
						</td>
					</tr>
				<?php endforeach ?>
				</tbody>
				<tfoot>
				<tr>
					<td colspan="5">
						<div class="input-group text-capitalize">
							<span class="input-group-addon"><small><?= lang('mark_checked_as') ?></small></span>
							<?= form_dropdown('change-status', options('products'), '', 'id="change-status" class="form-control"') ?>
							<span class="input-group-btn">
                            <button class="btn btn-primary <?= is_disabled('update', TRUE) ?> "
                                    type="submit"><?= lang('save_changes') ?></button>
                        </span>
						</div>
					</td>
					<td colspan="2" class="hidden-xs">
						<div class="<?= mobile_view('hidden-xs') ?> display_none" id="cat">
							<div id="show-categories">
								<select id="category_id" class="form-control select2" name="category_id">
									<option value="" selected><?= lang('enter_category_name') ?></option>
								</select>
							</div>
						</div>
                        <div class="<?= mobile_view('hidden-xs') ?> display_none" id="brand">
                            <div id="show-brands">
                                <select id="brand_id" class="form-control select2" name="brand_id">
                                    <option value="" selected><?= lang('enter_brand_name') ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="<?= mobile_view('hidden-xs') ?> display_none" id="tag">
                            <div id="show-tags">
                                <select id="tag_id" class="form-control select2" name="tag_id">
                                    <option value="" selected><?= lang('enter_tag_name') ?></option>
                                </select>
                            </div>
                        </div>
					</td>
					<td colspan="4">
						<div class="btn-group hidden-xs pull-right">
							<?php if (!empty($paginate['num_pages']) AND $paginate['num_pages'] > 1): ?>
								<button disabled
								        class="btn btn-default visible-lg"><?= $paginate['num_pages'] . ' ' . lang('total_pages') ?></button>
							<?php endif; ?>
							<button type="button" class="btn btn-primary dropdown-toggle"
							        data-toggle="dropdown"><?= i('fa fa-list') ?>
								<?= lang('select_rows_per_page') ?> <span class="caret"></span>
							</button>
							<?= $paginate['select_rows'] ?>
						</div>
					</td>
				</tr>
				</tfoot>
			</table>
		</div>

		<?php if (mobile_view()): ?>
			<div class="visible-xs">
				<?php foreach ($rows['values'] as $v): ?>
					<div class="box-info card">
						<h2><span class="pull-right"><?= format_amount($v['product_price']) ?></span>
							<a href="<?= admin_url('update_status/' . CONTROLLER_CLASS . '/product_id/' . $v['product_id']) ?>"><?= $v['product_name'] ?></a>
						</h2>
						<?= photo(CONTROLLER_METHOD, $v, 'img-responsive', TRUE) ?>
						<hr/>
						<p><strong class="text-capitalize"><?= lang('product_overview') ?></strong></p>

						<p><?= strip_tags($v['product_overview']) ?></p>
						<hr/>
						<div class="text-right">
							<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['product_id']) ?>"
							   class="btn btn-default <?= is_disabled('update', TRUE) ?> "
							   title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?> <?= lang('edit') ?></a>
							<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['product_id']) ?>"
							   data-toggle="modal" data-target="#confirm-delete" href="#"
							   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <?= lang('delete') ?></a>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<br/>

		<div class="container text-center"><?= $paginate['rows'] ?></div>
	</div>
	<?= form_close() ?>
	<br/>
<?php endif ?>
<div class="modal fade" id="add-product" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
			<?= form_open(admin_url(CONTROLLER_CLASS . '/create'), 'role="form" id="add_form"') ?>
            <div class="modal-body text-capitalize">
                <h3><i class="fa fa-tags"></i> <?= lang('select_product_type') ?></h3>
                <hr/>
				<?= get_product_types() ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('cancel') ?></button>
                <button class="btn btn-primary" type="submit"><?= lang('continue') ?></button>
            </div>
			<?= form_close() ?>
        </div>
    </div>
</div>

<!-- Load JS for Page -->
<script>
    $("#form").validate();
    $(document).ready(function () {
        $("#change-status").change
        (
            function () {
                var selectedValue = $(this).val();
                if (selectedValue == "add_category") {
                    $("#cat").show(300);
                    $("#tag").hide(300);
                    $("#brand").hide(300);
                }
                else if (selectedValue == "remove_category") {
                    $("#cat").show(300);
                    $("#tag").hide(300);
                    $("#brand").hide(300);
                }
                else if (selectedValue == "add_tag") {
                    $("#tag").show(300);
                    $("#cat").hide(300);
                    $("#brand").hide(300);
                }
                else if (selectedValue == "remove_tag") {
                    $("#tag").show(300);
                    $("#cat").hide(300);
                    $("#brand").hide(300);
                }
                else if (selectedValue == "add_brand") {
                    $("#tag").hide(300);
                    $("#cat").hide(300);
                    $("#brand").show(300);
                }
                else if (selectedValue == "remove_brand") {
                    $("#tag").hide(300);
                    $("#cat").hide(300);
                    $("#brand").show(300);
                }
                else {
                    $("#cat").hide(300);
                    $("#tag").hide(300);
                    $("#brand").hide(300);
                }
            }
        );
    });

    $("#category_id").select2({
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

    $("#tag_id").select2({
        ajax: {
            url: '<?=admin_url(TBL_PRODUCTS_TAGS . '/search/ajax/')?>',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    tag: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data, page) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.tag_id,
                            text: item.tag
                        }
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });

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

    $("#search-form").validate();

</script>

<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'role="form" id="form"') ?>
    <div class="row">
        <div class="col-md-6 col-lg-7">
            <div class="input-group text-capitalize">
				<?= generate_sub_headline('product_attributes', 'fa-tags', $rows['total']) ?>
            </div>
        </div>
        <div class="col-md-6 col-lg-5 text-right">
			<?= next_page('left', $paginate); ?>
            <a data-toggle="modal" data-target="#add-record" href="#"
               class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
                        class="hidden-xs"><?= lang('create_attribute') ?></span></a>
			<?= next_page('right', $paginate); ?>
        </div>
    </div>
    <hr/>
<?php if (empty($rows['values'])): ?>
	<?= tpl_no_values(CONTROLLER_CLASS . '/create/', 'create_attribute') ?>
<?php else: ?>
    <div class="box-info mass-edit">
        <div class="<?= mobile_view('hidden-xs') ?>">
            <table class="table table-striped table-hover">
                <thead class="text-capitalize">
                <tr>
                    <th><?= tb_header('attribute_name', 'attribute_name') ?></th>
                    <th><?= tb_header('type', 'attribute_type') ?></th>
                    <th><?= tb_header('sort_order', 'sort_order') ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
				<?php foreach ($rows['values'] as $v): ?>
                    <tr>
                        <td><input type="text" class="form-control required"
                                   name="att[<?= $v['attribute_id'] ?>][attribute_name]"
                                   value="<?= $v['attribute_name'] ?>" tabindex="1"/>
                        </td>
                        <td><span class="label label-default"> <?= $v['attribute_type'] ?></span></td>
                        <td style="width: 8%">
                            <input type="number" class="form-control digits required"
                                   name="att[<?= $v['attribute_id'] ?>][sort_order]" value="<?= $v['sort_order'] ?>"
                                   tabindex=1"/>
                        </td>
                        <td style="width: 20%" class="text-right">
							<?php if (!$disable_sql_category_count): ?>
                                <span
                                        class="tip btn btn-primary" data-toggle="tooltip" data-placement="bottom"
                                        title="<?= lang('products_using_this_attribute') ?>">
									<small style="font-size: 10px"><?= $v['total'] ?> <?= i('fa fa-tags') ?></small>
								</span>
							<?php endif; ?>
                            <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['attribute_id']) ?>"
                               class="btn btn-default" title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
							<?php if ($v['attribute_id'] != 1): ?>
                                <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['attribute_id']) ?>"
                                   data-toggle="modal" data-target="#confirm-delete" href="#"
                                   class="md-trigger btn btn-danger visible-lg <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
							<?php endif; ?>
                        </td>
                    </tr>
				<?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="3">
                        <button class="btn btn-primary <?= is_disabled('update', TRUE) ?>"
                                type="submit"><?= lang('save_changes') ?></button>
                    </td>
                    <td colspan="2">
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
                        <div class="row">
                            <div class="col-xs-8">
                                <h5>
                                    <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['attribute_id']) ?>">
										<?= $v['attribute_name'] ?>
                                    </a></h5>
                                <hr/>
                            </div>
                            <div class="col-xs-4 text-right">
                                <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['attribute_id']) ?>"
                                   class="btn btn-default"
                                   title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
                                <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['attribute_id']) ?>"
                                   data-toggle="modal" data-target="#confirm-delete" href="#"
                                   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?>
                                </a>
                            </div>
                        </div>
                        <div class="text-muted"><?= $v['description'] ?></div>
                    </div>
				<?php endforeach; ?>
            </div>
		<?php endif; ?>
        <div class="container text-center"><?= $paginate['rows'] ?></div>
    </div>
	<?= form_close() ?>
    <br/>

    <div class="modal fade" id="add-record" tabindex="-1" role="dialog" aria-labelledby="modal-title"
         aria-hidden="true">
        <div class="modal-dialog" id="modal-title">
            <div class="modal-content">
				<?= form_open(admin_url(CONTROLLER_CLASS . '/create'), 'role="form" id="add_form"') ?>
                <div class="modal-body text-capitalize">
                    <h3><i class="fa fa-tags"></i> <?= lang('select_attribute_type') ?></h3>
                    <hr/>
					<?= get_attribute_types() ?>
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
        $("#form").validate({
            ignore: "",
            submitHandler: function (form) {
                $.ajax({
                    url: '<?=admin_url(CONTROLLER_CLASS . '/mass_update/')?>',
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
<?php endif; ?>
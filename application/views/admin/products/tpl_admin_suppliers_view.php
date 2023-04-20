<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'role="form" id="form"') ?>
    <div class="row">
        <div class="col-md-8">
            <div class="text-capitalize">
				<?= generate_sub_headline(SUPPLIERS, 'fa-tags', $rows['total']) ?>
            </div>
        </div>
        <div class="col-md-4 text-right">
			<?= next_page('left', $paginate); ?>
            <a href="<?= admin_url(CONTROLLER_CLASS . '/create/') ?>"
               class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
                        class="hidden-xs"><?= lang('create_supplier') ?></span></a>
			<?= next_page('right', $paginate); ?>
        </div>
    </div>
    <hr/>
<?php if (empty($rows['values'])): ?>
	<?= tpl_no_values(CONTROLLER_CLASS . '/create/', 'create_supplier') ?>
<?php else: ?>
    <div class="box-info mass-edit">
        <div class="<?= mobile_view('hidden-xs') ?>">
            <table class="table table-striped table-hover">
                <thead class="text-capitalize">
                <tr>
                    <th><?= tb_header('supplier_name', 'supplier_name') ?></th>
                    <th><?= tb_header('email', 'supplier_email') ?></th>
                    <th><?= tb_header('phone', 'supplier_phone') ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
				<?php foreach ($rows['values'] as $v): ?>
                    <tr>
                        <td>
                            <input type="text" class="form-control required"
                                   name="suppliers[<?= $v['supplier_id'] ?>][supplier_name]"
                                   value="<?= $v['supplier_name'] ?>" <?= is_disabled('update', TRUE) ?> tabindex="1"/>
                        </td>
                        <td>
                            <input type="text" class="form-control required email"
                                   name="suppliers[<?= $v['supplier_id'] ?>][supplier_email]"
                                   value="<?= $v['supplier_email'] ?>" <?= is_disabled('update', TRUE) ?> tabindex="1"/>
                        </td>
                        <td>
                            <input type="text" class="form-control required"
                                   name="suppliers[<?= $v['supplier_id'] ?>][supplier_phone]"
                                   value="<?= $v['supplier_phone'] ?>" <?= is_disabled('update', TRUE) ?> tabindex="1"/>
                        </td>
                        <td class="text-right">
							<?php if (!$disable_sql_category_count): ?>
                                <a href="<?= admin_url('products/view?supplier_id=' . $v['supplier_id']) ?>"
                                   class="btn btn-primary">
                                    <small style="font-size: 10px"><?= $v['total'] ?> <?= i('fa fa-tags') ?></small>
                                </a>
							<?php endif; ?>
                            <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['supplier_id']) ?>"
                               class="btn btn-default" title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
							<?php if ($v['supplier_id'] != $default_supplier_id): ?>
                                <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['supplier_id']) ?>"
                                   data-toggle="modal" data-target="#confirm-delete" href="#"
                                   class="md-trigger btn btn-danger visible-lg <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
							<?php endif; ?>
                        </td>
                    </tr>

				<?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="2">
                        <button class="btn btn-primary <?= is_disabled('create') ?>"
                                type="submit"><?= lang('save_changes') ?></button>
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
                        <div class="row">
                            <div class="col-xs-7">
                                <h5>
                                    <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['supplier_id']) ?>"><?= $v['supplier_name'] ?></a>
                                </h5>
                            </div>
                            <div class="col-xs-5 text-right">
                                <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['supplier_id']) ?>"
                                   class="btn btn-default"
                                   title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
                                <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['supplier_id']) ?>"
                                   data-toggle="modal" data-target="#confirm-delete" href="#"
                                   class="btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?>
                                </a>
                            </div>
                        </div>
                        <hr/>
                        <div>
                            <address class="text-muted">
                                <a href="mailto:<?= $v['supplier_email'] ?>"><?= $v['supplier_email'] ?></a><br/>
								<?= $v['supplier_phone'] ?>
                            </address>
                        </div>
                    </div>
				<?php endforeach; ?>
            </div>
		<?php endif; ?>
        <div class="container text-center"><?= $paginate['rows'] ?></div>
    </div>
	<?php form_close(); ?>
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
<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'role="form" id="form"') ?>
    <div class="row">
        <div class="col-md-8">
            <?= generate_sub_headline('blog_groups', 'fa-group', $rows['total']) ?>
            <hr class="visible-xs"/>
        </div>
        <div class="col-md-4 text-right">
            <?= next_page('left', $paginate); ?>
            <a href="<?= admin_url(CONTROLLER_CLASS . '/create') ?>"
               class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
                    class="hidden-xs"><?= lang('add_blog_group') ?></span></a>
            <?= next_page('right', $paginate); ?>
        </div>
    </div>
    <hr/>
<?php if (empty($rows['values'])): ?>
    <?= tpl_no_values(CONTROLLER_CLASS . '/create', 'add_group') ?>
<?php else: ?>
    <div class="box-info mass-edit">
        <table class="table table-striped table-hover">
            <thead class="text-capitalize">
            <tr>
                <th><?= tb_header('group_name', 'group_name') ?></th>
                <th class="hidden-xs"><?= tb_header('description', '', FALSE) ?></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($rows['values'] as $v): ?>
                <tr>
                    <td><?= form_input('groups[' . $v['group_id'] . '][group_name]', $v['group_name'], is_disabled('update', true) . ' class="required form-control"') ?></td>
                    <td><?= form_input('groups[' . $v['group_id'] . '][group_description]', $v['group_description'], is_disabled('update', true) . ' class="required form-control"') ?></td>
                    <td class="text-right">
	                    <?php if (!$disable_sql_category_count): ?>
                        <a href="<?= admin_url('members/view/?table=blog&type_id=group_id&group_id=' . $v['group_id']) ?>"
                           class="btn btn-primary hidden-xs" title="<?= lang('members') ?>">
                            <small style="font-size: 10px"><?= $v['total'] ?> <?= i('fa fa-user') ?></small>
                        </a>
	                    <?php endif; ?>
                        <?php if ($v['group_id'] != $sts_members_default_blog_group): ?>
                            <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['group_id']) ?>"
                               data-toggle="modal" data-target="#confirm-delete" href="#"
                               class="md-trigger btn btn-danger hidden-xs <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
                        <?php endif ?>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
            <tfoot>
            <tr class="hidden-xs">
                <td colspan="1">
	                <button class="btn btn-primary <?= is_disabled('update', true) ?>"
	                        type="submit"><?=i('fa fa-refresh')?> <?= lang('save_changes') ?></button>
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
        <div class="container text-center"><?= $paginate['rows'] ?></div>
    </div>
	<?=form_hidden('group', 'blog') ?>
    <?= form_close() ?>
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
<?php endif ?>
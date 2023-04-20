<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'role="form" id="form"') ?>
    <div class="row">
        <div class="col-md-8">
			<?= generate_sub_headline('slide_shows', 'fa-rss', $rows['total']) ?>
            <hr class="visible-xs"/>
        </div>
        <div class="col-md-4 text-right">
			<?= next_page('left', $paginate); ?>
            <a href="<?= admin_url(CONTROLLER_CLASS . '/create/simple') ?>"
               class="btn btn-info <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
                        class="hidden-xs"><?= lang('create_simple_slide') ?></span></a>
            <a href="<?= admin_url(CONTROLLER_CLASS . '/create/advanced') ?>"
               class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
                        class="hidden-xs"><?= lang('create_advanced_slide') ?></span></a>
			<?= next_page('right', $paginate); ?>
        </div>
    </div>
    <hr/>
<?php if (empty($rows['values'])): ?>
	<?= tpl_no_values(CONTROLLER_CLASS . '/create/', 'create_slide_show') ?>
<?php else: ?>
    <div class="box-info">
        <br/>
        <div class="row">
            <div class="col-xs-1 text-center">
				<?= form_checkbox('', '', '', 'class="check-all"') ?>
            </div>
            <div class="col-xs-1 text-center">
				<?= tb_header('status', 'status') ?>
            </div>
            <div class="col-xs-8 col-sm-4 text-center">
				<?= tb_header('name', 'name') ?>
            </div>
            <div class="col-xs-2 hidden-xs text-center">
				<?= tb_header('start_date', 'start_date') ?>
            </div>
            <div class="col-xs-2 hidden-xs text-center">
				<?= tb_header('expires', 'end_date') ?>
            </div>
            <div class="col-xs-2"></div>
        </div>
        <div>
            <hr/>
            <div id="sortable">
				<?php foreach ($rows['values'] as $v): ?>
                    <div class="ui-state-default text-center" id="formid-<?= $v['slide_id'] ?>">
                        <div class="row">
                            <div class="col-xs-1 text-center">
								<?= form_checkbox('slide_id[]', $v['slide_id']) ?>
                            </div>
                            <div class="col-xs-1 text-center">
                                <a href="<?= admin_url('update_status/table/' . CONTROLLER_CLASS . '/type/status/key/slide_id/id/' . $v['slide_id']) ?>"
                                   class="btn btn-default <?= is_disabled('update', TRUE) ?>"><?= set_status($v['status']) ?></a>
                            </div>
                            <div class="col-xs-8 col-sm-4 text-center">
                                <h5><a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['slide_id']) ?>"><?= $v['name'] ?></a></h5>
                            </div>
                            <div class="col-xs-2 hidden-xs text-center">
								<?= display_date($v['start_date']) ?>
                            </div>
                            <div class="col-xs-2 hidden-xs text-center">
								<?= display_date($v['end_date']) ?>
                            </div>
                            <div class="col-xs-2 text-right">
                                <span class="btn btn-primary handle visible-lg <?= is_disabled('update', TRUE) ?>"><i
                                            class="fa fa-sort"></i></span>
                                <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['slide_id']) ?>"
                                   class="btn btn-<?php if ($v['type'] == 'simple'): ?>info<?php else: ?>default<?php endif; ?>"
                                   title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
                                <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['slide_id']) ?>"
                                   data-toggle="modal" data-target="#confirm-delete" href="#"
                                   class="md-trigger btn btn-danger hidden-xs <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
                            </div>
                        </div>
                        <hr/>
                    </div>
				<?php endforeach; ?>
            </div>
        </div>
        <div class="row hidden-xs">
            <div class="col-md-3">
                <div class="input-group text-capitalize">
                    <span class="input-group-addon"><small><?= lang('mark_checked_as') ?></small></span>
					<?= form_dropdown('change-status', options('active', 'deleted'), '', 'id="change-status" class="form-control"') ?>
                    <span class="input-group-btn">
                    <button class="btn btn-primary  <?= is_disabled('update', TRUE) ?>"
                            type="submit"><?= lang('go') ?></button></span>
                </div>
            </div>
            <div class="col-md-9 text-right">
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
            </div>
        </div>
    </div>
    <div class="container text-center"><?= $paginate['rows'] ?></div>
    <div id="update"></div>
	<?= form_close() ?>
    <br/>
    <!-- Load JS for Page -->
    <script>
        $("#form").validate();

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
    </script>
<?php endif ?>
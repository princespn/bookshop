<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-8">
		<?= generate_sub_headline('site_pages', 'fa-file-text-o', $rows['total']) ?>
        <hr class="visible-xs"/>
    </div>
    <div class="col-md-4 text-right">
		<?= next_page('left', $paginate); ?>
        <a data-toggle="collapse" data-target="#search_block"
           class="btn btn-primary"><?= i('fa fa-search') ?> <?= lang('search') ?></a>
	    <?php if (check_site_builder()): ?>
        <a data-toggle="modal" data-target="#add-builder" href="#"
           class="btn btn-info <?= is_disabled('create') ?>"><?= i('fa fa-html5') ?> <span
                    class="hidden-xs"><?= lang('use_site_builder') ?></span></a>
        <?php endif; ?>
        <a href="<?= admin_url(CONTROLLER_CLASS . '/create') ?>"
           class="btn btn-primary <?= is_disabled('create') ?> "><?= i('fa fa-plus') ?> <span
                    class="hidden-xs"><?= lang('create_basic_page') ?></span></a>
		<?= next_page('right', $paginate); ?>
    </div>
</div>
<hr/>
<div id="search_block" class="collapse">
	<?= form_open(admin_url(CONTROLLER_CLASS . '/general_search'), 'method="get" role="form" id="search-form" class="form-horizontal"') ?>
    <div class="box-info">
        <h4><?= i('fa fa-search') ?> <?= lang('search_site_pages') ?></h4>
        <div class="row">
            <div class="col-md-12">
                <div class="input-group">
                    <input type="text" name="search_term" class="form-control required"
                           placeholder="<?= lang('enter_search_term') ?>">
                    <span class="input-group-btn">
				        <button class="btn btn-default" type="submit"><?= lang('search') ?></button>
				      </span>
                </div>
            </div>
        </div>
    </div>
	<?= form_close() ?>
</div>
<?php if (empty($rows['values'])): ?>
	<?= tpl_no_values(CONTROLLER_CLASS . '/create/', 'create_site_page') ?>
<?php else: ?>
	<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'role="form" id="form"') ?>
    <div class="box-info mass-edit">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="text-capitalize">
                <tr>
                    <th class="text-center"><?= form_checkbox('', '', '', 'class="check-all"') ?></th>
                    <th></th>
                    <th><?= tb_header('name', 'name') ?></th>
                    <th><?= tb_header('meta_title', 'meta_title') ?></th>
                    <th><?= tb_header('meta_description', 'meta_description') ?></th>
                    <th><?= tb_header('keywords', 'meta_keywords') ?></th>
                    <th><?= tb_header('permalink', 'url') ?></th>
                    <th><?= tb_header('sort', 'sort') ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
				<?php foreach ($rows['values'] as $k => $v): ?>
					<?php $k++ ?>
                    <tr <?php if (config_option('layout_design_home_page_content_layout') == 'builder'): ?><?php if ($v['type'] == 'builder'): ?><?php if (config_option('sts_site_builder_default_home_page') == $v['page_id']): ?>class="info"<?php endif; ?><?php endif; ?><?php endif; ?>>
                        <td style="width: 3%"
                            class="text-center"><?= form_checkbox('page[' . $v['page_id'] . '][update]', $v['page_id']) ?></td>
                        <td style="width: 3%" class="text-center">
                            <a
                                    href="<?= admin_url('update_status/table/' . CONTROLLER_CLASS . '/type/status/key/page_id/id/' . $v['page_id']) ?>"
                                    class="btn btn-default <?= is_disabled('update', TRUE) ?>"><?= set_status($v['status']) ?></a>
                        </td>
                        <td class="text-center">
                            <input
                                    name="page[<?= $v['page_id'] ?>][title]" <?= is_disabled('update', TRUE) ?>
                                    type="text" value="<?= $v['title'] ?>" class="form-control required"
                                    placeholder="<?= lang('title') ?>"
                                    tabindex="<?= $k ?>"/>
                        </td>
                        <td class="text-center">
                            <input
                                    name="page[<?= $v['page_id'] ?>][meta_title]" <?= is_disabled('update', TRUE) ?>
                                    type="text" value="<?= $v['meta_title'] ?>" class="form-control required"
                                    placeholder="<?= lang('meta_title') ?>"
                                    tabindex="<?= $k ?>"/>
                        </td>
                        <td class="text-center">
                            <input
                                    name="page[<?= $v['page_id'] ?>][meta_description]" <?= is_disabled('update', TRUE) ?>
                                    type="text" value="<?= $v['meta_description'] ?>" class="form-control required"
                                    placeholder="<?= lang('meta_description') ?>"
                                    tabindex="<?= $k ?>"/>
                        </td>
                        <td style="width: 12%" class="text-center">
                            <input
                                    name="page[<?= $v['page_id'] ?>][meta_keywords]" <?= is_disabled('update', TRUE) ?>
                                    type="text" value="<?= $v['meta_keywords'] ?>" class="form-control required"
                                    placeholder="<?= lang('meta_keywords') ?>"
                                    tabindex="<?= $k ?>"/>
                        </td>
                        <td style="width: 12%" class="text-center">
                            <input
                                    name="page[<?= $v['page_id'] ?>][url]" <?= is_disabled('update', TRUE) ?>
                                    type="text" value="<?= $v['url'] ?>" class="form-control required"
                                    placeholder="<?= lang('permalink') ?>"
                                    tabindex="<?= $k ?>"/>
                        </td>
                        <td style="width: 6%"><input
                                    name="page[<?= $v['page_id'] ?>][sort_order]"
                                    type="number"
                                    value="<?= $v['sort_order'] ?>"
                                    class="form-control digits"/></td>
                        <td style="width: 16%" class="text-right">
	                        <?php if (check_site_builder()): ?>
							<?php if (config_option('layout_design_home_page_content_layout') == 'builder'): ?>
								<?php if ($v['type'] == 'builder'): ?>
									<?php if (config_option('sts_site_builder_default_home_page') == $v['page_id']): ?>
                                        <a
                                                class="disabled btn btn-success <?= is_disabled('update') ?>"
                                                data-toggle="tooltip"
                                                data-placement="bottom"
                                                title="<?= lang('home_page') ?>"><?= i('fa fa-home') ?></a>
									<?php else: ?>
                                        <a href="<?= admin_url('site_builder/set_default/' . $v['page_id']) ?>"
                                           class="tip block-phone btn btn-primary <?= is_disabled('update') ?>"
                                           data-toggle="tooltip"
                                           data-placement="bottom"
                                           title="<?= lang('set_as_home_page') ?>"><?= i('fa fa-home') ?></a>
									<?php endif; ?>
								<?php endif; ?>
							<?php endif; ?>
	                        <?php endif; ?>
                            <a href="<?= base_url(config_item('page_uri') . '/' . $v['url']) ?>?preview=1" class="btn btn-default"
                               title="<?= lang('view_page') ?>" target="_blank"><?= i('fa fa-external-link') ?></a>
                            <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['page_id']) ?>"
                               class="btn btn-default" title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
							<?php if ($v['page_id'] > '1'): ?>
                                <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['page_id']) ?>"
                                   data-toggle="modal" data-target="#confirm-delete" href="#"
                                   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
							<?php endif; ?>
                        </td>
                    </tr>
				<?php endforeach ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="3">
                        <div class="input-group text-capitalize">
                            <span class="input-group-addon"><small><?= lang('mark_checked_as') ?></small></span>
							<?= form_dropdown('change-status', options('active', 'delete'), '', 'id="change-status" class="form-control"') ?>
                            <span class="input-group-btn">
                    <button class="btn btn-primary <?= is_disabled('update', TRUE) ?>"
                            type="submit"><?= lang('save_changes') ?></button></span>
                        </div>
                    </td>
                    <td colspan="6">
                        <div class="btn-group pull-right">
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
        <div class="container text-center"><?= $paginate['rows'] ?></div>
    </div>
	<?= form_close() ?>
    <br/>

<?php endif ?>
<div class="modal fade" id="add-builder" tabindex="-1" role="dialog" aria-labelledby="modal-title"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
			<?= form_open(admin_url('site_builder/create'), 'class="form-horizontal" role="form" id="builder-form"') ?>
            <div class="modal-body text-capitalize">
                <h3 id="modal-title"><i class="fa fa-html5"></i> <?= lang('create_site_builder_page') ?></h3>
                <hr/>
                <div class="form-group">
					<?= lang('title', 'title', 'class="col-md-3 control-label"') ?>
                    <div class="col-md-9">
                        <input name="title" <?= is_disabled('create', TRUE) ?>
                                type="text" value="<?= lang('landing_page') ?>" class="form-control required"
                                placeholder="<?= lang('title') ?>"/>
                    </div>
                </div>
                <hr/>
                <div class="form-group">
					<?= lang('description', 'description', 'class="col-md-3 control-label"') ?>
                    <div class="col-md-9">
                        <input name="meta_description" <?= is_disabled('create', TRUE) ?>
                                type="text" value="<?= lang('new_sitebuilder_landing_page') ?>" class="form-control required"
                                placeholder="<?= lang('description') ?>"/>
                    </div>
                </div>
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
    $("#search-form").validate();
    $("#builder-form").validate();
</script>
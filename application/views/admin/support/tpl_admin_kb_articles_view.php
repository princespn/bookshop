<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
	<div class="row">
		<div class="col-md-8">
			<?= generate_sub_headline(lang('kb_articles'), 'fa-file-text-o', $rows[ 'total' ], TRUE, $category_name) ?>

			<hr class="visible-xs"/>
		</div>
		<div class="col-md-4 text-right">
			<?= next_page('left', $paginate); ?>
			<a href="<?= admin_url('update_status/settings/sts_kb_enable/') ?>"
			   class="btn btn-info <?= is_disabled('create') ?>">
				<?php if (config_enabled('sts_kb_enable')): ?>
					<?= i('fa fa-info-circle') ?>
					<span class="hidden-xs"><?= lang('deactivate_kb') ?></span>
				<?php else: ?>
					<?= i('fa fa-info-circle') ?>
					<span class="hidden-xs"><?= lang('activate_kb') ?></span>
				<?php endif; ?>
			</a>
			<?php if (config_enabled('sts_kb_enable')): ?>
                <a href="<?=site_url('kb')?>" class="btn btn-info tip" data-toggle="tooltip"
                   data-placement="bottom" title="<?= lang('knowledgebase') ?>" target="_blank"><?= i('fa fa-external-link') ?></a>
			<?php endif; ?>
			<a data-toggle="collapse" data-target="#search_block"
			   class="btn btn-primary"><?= i('fa fa-search') ?> <?= lang('search') ?></a>
			<?php if (config_enabled('enable_section_kb_articles')): ?>
			<a href="<?= admin_url(CONTROLLER_CLASS . '/create/' . $id) ?>"
			   class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
					class="hidden-xs"><?= lang('create_article') ?></span></a>
            <?php endif; ?>
			<?= next_page('right', $paginate); ?>
		</div>
	</div>
	<hr/>
	<div id="search_block" class="collapse">
		<?= form_open(admin_url(CONTROLLER_CLASS . '/general_search'), 'method="get" role="form" id="search-form" class="form-horizontal"') ?>
		<div class="box-info">
			<h4><?=i('fa fa-search')?> <?= lang('search_knowledgebase') ?></h4>
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
<?php if (empty($rows[ 'values' ])): ?>
	<?= tpl_no_values() ?>
<?php else: ?>
	<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'role="form" id="form"') ?>
	<div class="box-info">
		<div>
			<table class="table table-striped table-hover">
				<thead class="text-capitalize">
				<tr>
					<th class="text-center hidden-xs"><?= form_checkbox('', '', '', 'class="check-all"') ?></th>
					<th class="text-center hidden-xs"><?= tb_header('published', 'status') ?></th>
					<th><?= tb_header('kb_title', 'kb_title') ?></th>
					<th class="text-center hidden-xs"><?= tb_header('category', 'category_name') ?></th>
					<th class="hidden-xs"><?= tb_header('sort', 'sort_order') ?></th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($rows[ 'values' ] as $v): ?>
					<tr>
						<td style="width: 5%"
						    class="text-center hidden-xs"><?= form_checkbox('kb_id[]', $v[ 'kb_id' ]) ?></td>
						<td style="width: 6%" class="text-center hidden-xs">
							<a href="<?= admin_url('update_status/table/' . CONTROLLER_CLASS . '/type/status/key/kb_id/id/' . $v[ 'kb_id' ]) ?>"
							   class="btn btn-default <?= is_disabled('update', TRUE) ?>">
								<?= set_status($v[ 'status' ]) ?>
							</a>
						</td>
						<td>
							<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v[ 'kb_id' ]) ?>"><?= $v[ 'kb_title' ] ?></a>
						</td>
						<td class="text-center hidden-xs"><a
								href="<?= admin_url(CONTROLLER_CLASS . '/view?m-category_id=' . $v[ 'category_id' ]) ?>"><?= $v[ 'category_name' ] ?></a>
						</td>
						<td style="width: 8%" class="hidden-xs">
							<input name="sort_order[<?= $v[ 'kb_id' ] ?>]" type="number" value="<?= $v[ 'sort_order' ] ?>"
							       class="form-control digits" <?= is_disabled('update', TRUE) ?>/>
						</td>
						<td style="width: 15%" class="text-right">
							<a href="<?= base_url(config_item('kb_uri') . '/article/' . $v[ 'url' ]) ?>" class="btn btn-default tip"
							   data-toggle="tooltip" data-placement="bottom" title="<?= lang('view_page') ?>"
							   target="_blank"><?= i('fa fa-external-link') ?></a>

							<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v[ 'kb_id' ]) ?>"
							   class="btn btn-default"
							   title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>

							<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v[ 'kb_id' ]) ?>"
							   data-toggle="modal" data-target="#confirm-delete" href="#" class="btn btn-danger"
							   class="md-trigger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
						</td>
					</tr>
				<?php endforeach ?>
				</tbody>
				<tfoot>
				<tr>
					<td colspan="4" class="hidden-xs">
						<div class="input-group text-capitalize">
							<span class="input-group-addon"><small><?= lang('mark_checked_as') ?></small></span>
							<?= form_dropdown('change-status', options('active', 'deleted'), '', 'id="change-status" class="form-control"') ?>
							<span class="input-group-btn">
                    <button class="btn btn-primary <?= is_disabled('update', TRUE) ?>"
                            type="submit"><?= lang('go') ?></button></span>
						</div>
					</td>
					<td colspan="2">
						<div class="btn-group hidden-xs pull-right">
							<?php if (!empty($paginate[ 'num_pages' ]) AND $paginate[ 'num_pages' ] > 1): ?>
								<button disabled
								        class="btn btn-default visible-lg"><?= $paginate[ 'num_pages' ] . ' ' . lang('total_pages') ?></button>
							<?php endif; ?>
							<button type="button" class="btn btn-primary dropdown-toggle"
							        data-toggle="dropdown"><?= i('fa fa-list') ?>
								<?= lang('select_rows_per_page') ?> <span class="caret"></span>
							</button>
							<?= $paginate[ 'select_rows' ] ?>
						</div>
					</td>
				</tr>
				</tfoot>
			</table>
		</div>
		<div class="container text-center"><?= $paginate[ 'rows' ] ?></div>
	</div>
	<?= form_close() ?>
	<br/>
	<!-- Load JS for Page -->
	<script>
		$("#form").validate();
	</script>
<?php endif ?>
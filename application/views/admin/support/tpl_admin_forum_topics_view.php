<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
    <div class="row">
        <div class="col-md-8">
			<?= generate_sub_headline('community_forum_topics', 'fa-file-text-o', $rows['total']) ?>
            <hr class="visible-xs"/>
        </div>
        <div class="col-md-4 text-right">
			<?= next_page('left', $paginate); ?>
            <a href="<?= admin_url('update_status/settings/sts_forum_enable/') ?>"
               class="btn btn-info <?= is_disabled('create') ?>">
				<?php if (config_enabled('sts_forum_enable')): ?>
					<?= i('fa fa-info-circle') ?>
                    <span class="hidden-xs"><?= lang('deactivate_forum') ?></span>
				<?php else: ?>
					<?= i('fa fa-info-circle') ?>
                    <span class="hidden-xs"><?= lang('activate_forum') ?></span>

				<?php endif; ?>
            </a>
			<?php if (config_enabled('sts_forum_enable')): ?>
                <a href="<?= site_url('forum') ?>" class="btn btn-info tip" data-toggle="tooltip"
                   data-placement="bottom" title="<?= lang('forum') ?>"
                   target="_blank"><?= i('fa fa-external-link') ?></a>
			<?php endif; ?>
            <a data-toggle="collapse" data-target="#search_block"
               class="btn btn-primary"><?= i('fa fa-search') ?> <?= lang('search') ?></a>
			<?php if (config_enabled('enable_section_forum_topics')): ?>
                <a href="<?= admin_url(CONTROLLER_CLASS . '/create') ?>"
                   class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
                            class="hidden-xs"><?= lang('add_topic') ?></span></a>
			<?php endif; ?>
			<?= next_page('right', $paginate); ?>
        </div>
    </div>
    <hr/>
    <div id="search_block" class="collapse">
		<?= form_open(admin_url(CONTROLLER_CLASS . '/general_search'), 'method="get" role="form" id="search-form" class="form-horizontal"') ?>
        <div class="box-info">
            <h4><?= i('fa fa-search') ?> <?= lang('search_forum_topics') ?></h4>
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
	<?= tpl_no_values() ?>
<?php else: ?>
	<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update')) ?>
    <div class="box-info">
        <div class="row hidden-xs">
            <div class="col-sm-1 text-center hidden-xs hidden-sm">
                <h5><?= form_checkbox('', '', '', 'class="check-all"') ?></h5></div>
            <div class="col-sm-1 text-center hidden-xs"><?= tb_header('member', 'username') ?></div>
            <div class="col-sm-1 text-center hidden-xs"><?= tb_header('status', 'status') ?></div>
            <div class="col-sm-6"><?= tb_header('topic', 'topic') ?></div>
            <div class="col-sm-1"><?= tb_header('category', 'category_name') ?></div>
            <div class="col-sm-3"></div>
        </div>
        <hr/>
        <div id="sortable">
			<?php foreach ($rows['values'] as $v): ?>
                <div class="ui-state-default" id="id-<?= $v['topic_id'] ?>">
                    <div class="row">
                        <div class="col-sm-1 text-center hidden-xs hidden-sm">
                            <small><?=local_date($v['date_modified'])?></small>
                            <br/>
                            <p><?= form_checkbox('topic_id[]', $v['topic_id']) ?></p>

                        </div>
                        <div class="r col-sm-1 text-center hidden-sm">
							<?= photo(CONTROLLER_METHOD, $v, 'img-thumbnail img-responsive dash-photo') ?>
                            <br/>
							<?php if (!empty($v['admin_id'])): ?>
                                <a href="<?= admin_url(TBL_ADMIN_USERS . '/update/' . $v['admin_id']) ?>">
                                    <small><?= lang('admin') ?> <br/><?= $v['admin_username'] ?></small>
                                </a>
							<?php else: ?>
                                <a href="<?= admin_url(TBL_MEMBERS . '/update/' . $v['member_id']) ?>">
                                    <small><?= $v['username'] ?></small>
                                </a>
							<?php endif; ?>
                        </div>
                        <div class="col-sm-1 text-center hidden-xs">
                            <a href="<?= admin_url('update_status/table/' . CONTROLLER_CLASS . '/type/status/key/topic_id/id/' . $v['topic_id']) ?>"
                               class="btn btn-default"><?= set_status($v['status']) ?></a>
                        </div>
                        <div class="col-sm-6">
                            <h5>
                                <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['topic_id']) ?>"><?= $v['title'] ?></a>
                            </h5>
                            <p><?= word_limiter(format_response($v['topic']), 50) ?></p>
                        </div>
                        <div class="col-sm-1">
                            <h5>
                                <a href="<?= admin_url(CONTROLLER_CLASS . '/view?m-category_id=' . $v['category_id']) ?>">
                                    <span class="label label-info"><?= $v['category_name'] ?></span></a>
                            </h5>
                        </div>
                        <div class="col-sm-2 text-right">
							<?php if ($v['pinned'] == '1'): ?>
                                <a href="<?= admin_url('update_status/table/' . CONTROLLER_CLASS . '/type/pinned/key/topic_id/id/' . $v['topic_id']) ?>"
                                   class="btn btn-success tip" data-toggle="tooltip" data-placement="bottom"
                                   title="<?= lang('pinned') ?>"><?= i('fa fa-thumb-tack') ?></a>
							<?php else: ?>
                                <a href="<?= admin_url('update_status/table/' . CONTROLLER_CLASS . '/type/pinned/key/topic_id/id/' . $v['topic_id']) ?>"
                                   class="btn btn-default tip" data-toggle="tooltip" data-placement="bottom"
                                   title="<?= lang('pin_this') ?>"><?= i('fa fa-thumb-tack') ?></a>
							<?php endif; ?>
                            <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['topic_id']) ?>"
                               class="tip btn btn-default block-phone" data-toggle="tooltip" data-placement="bottom"
                               title="<?= lang('view_replies') ?>">
                                <small style="font-size: 10px"><?= $v['replies'] ?></small> <?= i('fa fa-comments') ?>
                                <span
                                        class="visible-xs"><?= lang('replies') ?></span> </a>
                            <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['topic_id']) ?>"
                               data-toggle="modal" data-target="#confirm-delete" href="#"
                               class="md-trigger btn btn-danger block-phone  <?= is_disabled('delete') ?>"><i
                                        class="fa fa-trash-o"></i> <span class="visible-xs"><?= lang('delete') ?></span></a>
                        </div>
                    </div>
                    <hr/>
                </div>
			<?php endforeach; ?>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <div class="input-group hidden-xs hidden-sm">
                    <span class="input-group-addon"><?= lang('move_checked_to_category') ?> </span>
					<?= form_dropdown('category_id', options('forum_categories'), '', 'id="category_id" class="form-control"') ?>
                    <span class="input-group-btn"><button class="btn btn-primary <?= is_disabled('update', TRUE) ?>"
                                                          type="submit"><?= lang('save_changes') ?></button></span>
                </div>
            </div>
            <div class="col-sm-8 text-right">
                <div class="btn-group hidden-xs">
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
		<?php if (!empty($paginate['rows'])): ?>
            <div class="text-center"><?= $paginate['rows'] ?></div>
            <div class="text-center">
                <small class="text-muted"><?= $paginate['num_pages'] ?> <?= lang('total_pages') ?></small>
            </div>
		<?php endif; ?>
    </div>
	<?= form_hidden('redirect', query_url()) ?>
	<?php form_close() ?>

<?php endif; ?>
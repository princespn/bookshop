<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
    <div class="row">
        <div class="col-md-8">
            <?= generate_sub_headline($title, 'fa-lock', $rows['total'], true, $sub_title) ?>
            <hr class="visible-xs"/>
        </div>
        <div class="col-md-4 text-right">
            <?= next_page('left', $paginate); ?>
	        <a data-toggle="collapse" data-target="#search_block"
	           class="btn btn-primary"><?= i('fa fa-search') ?> <?= lang('search') ?></a>
            <div class="btn-group hidden-xs text-capitalize">
                <button type="button" class="btn btn-primary dropdown-toggle"
                        data-toggle="dropdown"><?= i('fa fa-search') ?> <?= lang('filter_tickets') ?>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="<?= admin_url('support_tickets/view?closed=0') ?>"><?= lang('open_tickets') ?></a></li>
                    <li><a href="<?= admin_url('support_tickets/view?closed=1') ?>"><?= lang('closed_tickets') ?></a>
                    </li>
                    <?php foreach ($ticket_status_options as $v): ?>
                        <li>
                            <a href="<?= admin_url('support_tickets/view?&ticket_status=' . $v . '&closed=' . $this->input->get('closed')) ?>"><?= lang($v . '_tickets') ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <?= next_page('right', $paginate); ?>
        </div>
    </div>
    <hr/>
	<div id="search_block" class="collapse">
		<?= form_open(admin_url(CONTROLLER_CLASS . '/general_search'), 'method="get" role="form" id="search-form" class="form-horizontal"') ?>
		<div class="box-info">
			<h4><?=i('fa fa-search')?> <?= lang('search_tickets') ?></h4>
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
	<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update')) ?>
    <div class="box-info">
        <div class="row hidden-xs">
            <div class="col-sm-1 text-center hidden-xs hidden-sm">
                <h5><?= form_checkbox('', '', '', 'class="check-all"') ?></h5></div>
            <div class="col-sm-1 text-center hidden-xs hidden-sm"><?= tb_header('category', 'category_name') ?></div>
            <div class="col-sm-1 text-center"><?= tb_header('priority', 'priority') ?></div>
            <div class="col-sm-6 col-md-4"><?= tb_header('issue', 'ticket_subject') ?></div>
            <div class="col-sm-1 text-center"><?= tb_header('assigned', 'admin_id') ?></div>
            <div class="col-sm-1 text-center"><?= tb_header('date', 'date_added') ?></div>
            <div class="col-sm-1 text-center"><?= tb_header('status', 'status') ?></div>
            <div class="col-sm-1 text-center"><?= tb_header('last_reply', 'last_reply', false) ?></div>
            <div class="col-sm-1"></div>
        </div>
        <hr/>
        <?php foreach ($rows['values'] as $v): ?>
            <div class="hover <?= $v['priority'] ?>">
                <div class="row tickets">
                    <div class="col-sm-1 text-center hidden-xs hidden-sm">
	                    <?= photo(CONTROLLER_METHOD, $v, 'img-thumbnail img-circle img-responsive dash-photo') ?>
                        <br/>
                        <a href="<?= admin_url(TBL_MEMBERS . '/update/' . $v['member_id']) ?>">
                            <small><?= $v['username'] ?></small>
                        </a>
                        <br />
                        <?= form_checkbox('ticket_id[]', $v['ticket_id']) ?>
                    </div>
                    <div class="col-sm-1 text-center hidden-xs hidden-sm"><span
                            class="badge"><?= $v['category_name'] ?></span></div>
                    <div class="r col-sm-1 text-center"><span
                            class="label label-default label-<?= $v['priority'] ?>"><?= lang($v['priority']) ?></span>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['ticket_id']) ?>">#<?= $v['ticket_id'] ?>
                                - <?= $v['ticket_subject'] ?></a>
                    </div>
                    <div class="r col-sm-1 text-center">
                        <small>
                        <?php if (empty($v['admin_username'])): ?>
                            <?=lang('none')?>
                        <?php else: ?>
	                        <?= $v['admin_username'] ?>
                        <?php endif; ?>
                        </small>
                    </div>
                    <div class="r col-sm-1 text-center">
                        <small><?= display_date($v['date_added']) ?><br />
			                <?= display_time($v['date_added']) ?></small>
                    </div>
                    <div class="r col-sm-1 text-center">
                        <span
                            class="label label-default label-<?= $v['ticket_status'] ?>"><?= lang($v['ticket_status']) ?></span>
                    </div>
                    <div class="r col-sm-1 text-center">
                        <small class="label label-default text-capitalize">
                            <?php if (!empty($v['date_modified'])): ?>
                                <?= timespan(strtotime($v['date_modified']), strtotime(local_date('', FALSE)), 1) ?> <?=lang('ago')?>
                            <?php else: ?>
                                <?= lang('none') ?>
                            <?php endif; ?>
                        </small>
                    </div>
                    <div class="col-sm-1 text-right">
                        <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['ticket_id']) ?>"
                           class="tip btn btn-default block-phone" data-toggle="tooltip" data-placement="bottom"
                           title="<?= lang('view_replies') ?>">
                            <small style="font-size: 10px"><?= $v['replies'] ?></small> <?= i('fa fa-comments') ?> <span
                                class="visible-xs"><?= lang('replies') ?></span> </a>
                        <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['ticket_id']) ?>/2/"
                           data-toggle="modal" data-target="#confirm-delete" href="#"
                           class="md-trigger btn btn-danger block-phone <?= is_disabled('delete') ?>"><i
                                class="fa fa-trash-o"></i> <span class="visible-xs"><?= lang('delete') ?></span></a>
                    </div>
                </div>
                <hr/>
            </div>
        <?php endforeach; ?>
        <div class="row hidden-xs hidden-sm">
            <div class="col-md-6 col-lg-4">
                <div class="input-group hidden-xs hidden-sm">
                    <span class="input-group-addon"><?= lang('mark_checked_as') ?> </span>
                    <?= form_dropdown('change-status', options('mark_ticket_priority', 'deleted'), '', 'id="change-status" class="form-control"') ?>
                    <span class="input-group-btn"><button class="btn btn-primary <?= is_disabled('update', true) ?>"
                                                          type="submit"><?= lang('save_changes') ?></button></span>
                </div>
            </div>
            <div class="col-md-2">
                <div class="<?= mobile_view('hidden-xs') ?> display_none" id="cat">
                    <div id="show-admins">
                        <select id="admin_id" class="form-control select2" name="admin_id">
                            <option value="" selected><?= lang('admin') . ' ' . lang('username') ?></option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-lg-6 text-right">
                <div class="btn-group">
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
<script>
	$("#search-form").validate();
    $(document).ready(function () {
        $("#change-status").change
        (
            function () {
                var selectedValue = $(this).val();
                if (selectedValue == "admin_id") {
                    $("#cat").show(300);
                }
                else {
                    $("#cat").hide(300);
                }
            }
        );

        $("#admin_id").select2({
            ajax: {
                url: '<?=admin_url(TBL_ADMIN_USERS . '/search/ajax/')?>',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        username: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, page) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                id: item.admin_id,
                                text: item.username
                            }
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 2
        });
    });


</script>

<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update')) ?>
<div class="row">
	<div class="col-md-8">
		<?= generate_sub_headline('frequently_asked_questions', 'fa-lock', $rows[ 'total' ]) ?>
		<hr class="visible-xs"/>
	</div>
	<div class="col-md-4 text-right">
		<?= next_page('left', $paginate); ?>
        <a href="<?= base_url(TBL_FAQ) ?>" class="btn btn-info"
           title="<?= lang('view_page') ?>" target="_blank"><?= i('fa fa-external-link') ?></a>
		<a href="<?= admin_url(CONTROLLER_CLASS . '/create') ?>"
		   class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
				class="hidden-xs"><?= lang('add_question') ?></span></a>
		<?= next_page('right', $paginate); ?>
	</div>
</div>
<hr/>
<?php if (empty($rows[ 'values' ])): ?>
	<?= tpl_no_values(CONTROLLER_CLASS . '/create/', 'add_question') ?>
<?php else: ?>
	<div class="box-info">
		<div class="row hidden-xs">
			<div class="col-sm-1 text-center hidden-xs"><?= tb_header('status', 'status') ?></div>
			<div class="col-sm-9"><?= tb_header('question', 'question') ?></div>
			<div class="col-sm-2"></div>
		</div>
		<hr/>
		<div id="sortable">
		<?php foreach ($rows[ 'values' ] as $v): ?>
			<div class="ui-state-default" id="faqid-<?= $v['faq_id'] ?>">
				<div class="row">
					<div class="col-sm-1 text-center hidden-xs">
						<a href="<?= admin_url('update_status/table/' . CONTROLLER_CLASS . '/type/status/key/faq_id/id/' . $v['faq_id']) ?>"
						   class="btn btn-default"><?= set_status($v['status']) ?></a>
					</div>
					<div class="col-sm-8">
						<h5><a class="faq-question" data-toggle="collapse"
						       data-target="#faq<?= $v[ 'faq_id' ] ?>"><?= $v[ 'question' ] ?></a></h5>

						<div id="faq<?= $v[ 'faq_id' ] ?>"
						     class="faq-answer collapse"><?= word_limiter(strip_tags($v[ 'answer' ]), 50) ?></div>
					</div>
					<div class="col-sm-3 text-right">
						<span class="btn btn-primary handle visible-lg <?= is_disabled('update', TRUE) ?>"><i
								class="fa fa-sort"></i></span>
						<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v[ 'faq_id' ]) ?>"
						   class="btn btn-default block-phone" title="<?= lang('edit') ?>"><i class="fa fa-pencil"></i>
							<span class="visible-xs"><?= lang('edit') ?></span> </a>
						<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v[ 'faq_id' ]) ?>"
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
			<div class="col-sm-3"></div>
			<div class="col-sm-9 text-right">
				<div class="btn-group hidden-xs">
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
			</div>
		</div>
		<?php if (!empty($paginate[ 'rows' ])): ?>
			<div class="text-center"><?= $paginate[ 'rows' ] ?></div>
			<div class="text-center">
				<small class="text-muted"><?= $paginate[ 'num_pages' ] ?> <?= lang('total_pages') ?></small>
			</div>
		<?php endif; ?>
	</div>
	<?= form_hidden('redirect', query_url()) ?>
	<?php form_close() ?>
<?php endif; ?>
<div id="update"></div>
<script>
	$(function () {
		$('#sortable').sortable({
			handle: '.handle',
		placeholder: "ui-state-highlight",
			update: function () {
			var order = $('#sortable').sortable('serialize');
				console.log(order);
			$("#update").load("<?=admin_url(CONTROLLER_CLASS . '/update_order/')?>?" + order);
		}
	});
	});

</script>
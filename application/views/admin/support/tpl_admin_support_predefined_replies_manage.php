<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
	<div class="row">
		<div class="col-md-5">
			<h2 class="sub-header block-title"><?= i('fa fa-pencil') ?> <?= lang(CONTROLLER_FUNCTION . '_' . singular(CONTROLLER_CLASS)) ?></h2>
		</div>
		<div class="col-md-7 text-right">
			<?php if (CONTROLLER_FUNCTION == 'update'): ?>
				<?= prev_next_item('left', CONTROLLER_METHOD, $row[ 'prev' ]) ?>
				<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $id) ?>" data-toggle="modal"
				   data-target="#confirm-delete" href="#" 
				   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
						class="hidden-xs"><?= lang('delete') ?></span></a>
			<?php endif; ?>
			<a href="<?= admin_url(CONTROLLER_CLASS) ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
					class="hidden-xs"><?= lang('view_replies') ?></span></a>
			<?php if (CONTROLLER_FUNCTION == 'update'): ?>
				<?= prev_next_item('right', CONTROLLER_METHOD, $row[ 'next' ]) ?>
			<?php endif; ?>
		</div>
	</div>
	<hr/>
	<div class="box-info">
		<h3 class="text-capitalize"><?= lang('reply_details') ?></h3>
		<hr/>
		<div class="form-group">
			<?= lang('title', 'title', array( 'class' => 'col-md-3 control-label' )) ?>
			<div class="r col-md-5">
				<?= form_input('title', set_value('title', $row[ 'title' ]), 'class="form-control required"') ?>
			</div>
		</div>
		<hr/>
		<div class="form-group">
			<?= lang('ticket_subject', 'title', array( 'class' => 'col-md-3 control-label' )) ?>
			<div class="r col-md-5">
				<?= form_input('ticket_subject', set_value('ticket_subject', $row[ 'ticket_subject' ]), 'class="form-control required"') ?>
			</div>
		</div>
		<hr/>
		<div class="form-group">
			<?= lang('reply_content', 'title', array( 'class' => 'col-md-3 control-label' )) ?>
			<div class="r col-md-5">
				<?= form_textarea('reply_content', set_value('reply_content', $row[ 'reply_content' ], FALSE), 'class="form-control required"') ?>
			</div>
		</div>
		<hr/>
		<nav class="navbar navbar-fixed-bottom save-changes">
			<div class="container text-right">
				<div class="row">
					<div class="col-md-12">
						<button class="btn btn-info navbar-btn block-phone"
						        id="update-button" <?= is_disabled('update', TRUE) ?>
						        type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
					</div>
				</div>
			</div>
		</nav>
	</div>
<?php if (CONTROLLER_FUNCTION == 'update'): ?>
	<?=form_hidden('id', $id)?>
	<?php endif; ?>
<?= form_close() ?>
<script>
	$("#form").validate();
</script>

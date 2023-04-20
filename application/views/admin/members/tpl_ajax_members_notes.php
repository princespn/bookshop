<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<h3 class="text-capitalize">
	<?= lang('recent_notes') ?></h3>
<hr/>
<?php if (empty($row)): ?>
	<?= tpl_no_values('', '', 'no_notes_found', 'warning', FALSE) ?>
<?php else: ?>
	<?php foreach ($row as $v): ?>
		<div class="row">
			<div class="r col-md-11">
                <small class="text-muted"><?= local_date($v['updated_on']) ?></small><br />
				<a href="#" class="editable <?= is_disabled('update') ?>" data-name="note" data-type="textarea"
				   data-pk="<?= $v['note_id'] ?>"
				   data-url="<?= admin_url(TBL_MEMBERS_NOTES . '/update/' . $v['note_id']) ?>"
				   data-title="<?= lang('update_note') ?>"><?= $v['note'] ?></a>
			</div>
			<div class="r col-md-1">
				<a
					data-href="<?= admin_url(TBL_MEMBERS_NOTES . '/delete/' . $v['member_id'] . '/' . $v['note_id']) ?>"
					data-toggle="modal" data-target="#confirm-delete" href="#" <?= is_disabled('delete') ?>
					class="block-phone btn btn-danger btn-sm"><?= i('fa fa-trash-o') ?></a>
			</div>
		</div>
		<hr/>
	<?php endforeach; ?>
<?php endif; ?>
<script src="<?= base_url('js/xeditable/bootstrap-editable.min.js') ?>"></script>
<script>
	$.fn.editable.defaults.mode = 'inline';
    $.fn.editable.defaults.ajaxOptions = {type: "GET"};
	$(document).ready(function () {
		$('.editable').editable({
			rows: 4
		});
	});


</script>
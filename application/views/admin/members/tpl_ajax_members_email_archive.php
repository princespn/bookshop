<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div>
	<h3 class="text-capitalize">
		<a href="<?= admin_url(CONTROLLER_CLASS . '/view/?member_id=' . $id) ?>"
		   class="btn btn-primary btn-sm pull-right"><?= i('fa fa-search') ?> <?= lang('view_archive') ?></a>
		<?= lang('recent_emails') ?></h3>
	<hr/>
	<?php if (empty($row)): ?>
		<?= tpl_no_values('', '', 'no_emails_found', 'warning', FALSE) ?>
	<?php else: ?>
		<?php foreach ($row as $v): ?>
			<div class="row">
				<div class="r col-md-10">
					<strong><?= $v['subject'] ?></strong><br/>
					<span class="text-muted"><?= word_limiter(strip_tags($v['html_body']), 25) ?></span>
				</div>
				<div class="r col-md-1">
					<span><?= display_date($v['send_date']) ?></span>
				</div>
				<div class="r col-md-1 text-right">
					<a href="<?= admin_url(CONTROLLER_CLASS . '/resend/' . $v['id']) ?>"
					   class="tip block-phone btn btn-default btn-sm <?= is_disabled('update') ?>" data-toggle="tooltip"
					   data-placement="bottom" title="<?= lang('resend') ?>"><?= i('fa fa-envelope') ?></a>
				</div>
			</div>
			<hr/>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
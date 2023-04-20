<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div>
	<h2 class="text-capitalize"><?= lang('manage_events_for') ?> <?= $current_day ?></h2>
	<?php if (empty($events)): ?>
		<div class="alert alert-info">
			<h3 class="text-info"><?= lang('no_events_planned') ?></h3>
			<p>
				<a href="<?= admin_url() ?><?= CONTROLLER_CLASS ?>/create/<?= $y ?>/<?= $m ?>/<?= $d ?>"
				   class="btn btn-info <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <?= lang('create_event') ?></a>
			</p>
		</div>
	<?php else: ?>
		<div class="the-timeline">
			<ul>
				<?php foreach ($events as $v): ?>
					<li>
						<div class="the-date">
							<small><?= $v['start_time'] ?></small>
						</div>
                         <span class="pull-right">
                            <a data-href="<?= admin_url(TBL_EVENTS_CALENDAR) ?>/delete/<?= $v['id'] ?>/<?= $y ?>/<?= $m ?>/<?= $d ?>"
                               data-toggle="modal" data-target="#confirm-delete" href="#"
                               class="md-trigger btn btn-sm btn-danger"><i class="fa fa-trash-o"></i></a>
	                         <?php if ($v['status'] == 1): ?>
		                         <a href="javascript:ChangeEventStatus('<?= $v['id'] ?>')"
		                            class="btn btn-sm btn-success"><i class="fa fa-check"></i></a>
	                         <?php else: ?>
		                         <a href="javascript:ChangeEventStatus('<?= $v['id'] ?>')"
		                            class="btn btn-sm btn-warning"><i class="fa fa-exclamation-triangle"></i></a>
	                         <?php endif; ?>
	                         <a href="<?= admin_url(TBL_EVENTS_CALENDAR) ?>/update/<?= $v['id'] ?>/<?= $y ?>/<?= $m ?>/<?= $d ?>"
	                            class="btn btn-sm btn-default"><i class="fa fa-pencil"></i></a>
                        </span>
						<h5><?= $v['title'] ?></h5>
						<p>
							<small class="text-muted"><?= lang('time') ?>: <?= $v['start_time'] ?>
								- <?= $v['end_time'] ?>
								<?php if (!empty($v['location'])): ?>
									<?= lang('location') ?>: <?= $v['location'] ?>
								<?php endif; ?>
							</small>
						</p>

						<p><?= $v['description'] ?></p>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
</div>
<script>
	function ChangeEventStatus(id) {
		$("#events-content").load("<?=admin_url(TBL_EVENTS_CALENDAR . '/update_status')?>/" + id + "/<?=$y?>/<?=$m?>/<?=$d?>");
	}
</script>
<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (empty($rows)): ?>
	<br/>
	<div class="alert alert-warning">
		<h5 class="text-warning"><?= lang('no_revisions_saved') ?></h5>
	</div>
<?php else: ?>
	<div class="row">
		<div class="col-md-6">
			<h3 class="text-capitalize"><?= lang('revision_history') ?>
				- <?= MAX_BLOG_POST_REVISIONS ?> <?= lang('most_recent') ?></h3>
			<hr/>
			<ul class="list-group">
				<?php foreach ($rows as $v): ?>
					<li class="list-group-item">
			<span>
				<a href="<?= admin_url(TBL_BLOG_POSTS . '/update/' . $id . '?revision=' . $v[ 'revision_id' ]) ?>">
					<?= $v[ 'revision_id' ] ?> - <?= display_date($v[ 'date' ], TRUE) ?>
				</a>
			</span>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
<?php endif; ?>

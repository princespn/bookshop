<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
	<div class="row">
		<div class="col-md-8">
			<?= generate_sub_headline('predefined_ticket_replies', 'fa-file-text-o', $rows['total']) ?>
			<hr class="visible-xs"/>
		</div>
		<div class="col-md-4 text-right">
			<a href="<?= admin_url(TBL_SUPPORT_PREDEFINED_REPLIES . '/view') ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
					class="hidden-xs"><?= lang('view_replies') ?></span></a>
			<a href="<?= admin_url(CONTROLLER_CLASS . '/create') ?>"
			   class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
					class="hidden-xs"><?= lang('create_reply') ?></span></a>
		</div>
	</div>
	<hr/>
<?php if (empty($rows['values'])): ?>
	<?= tpl_no_values(CONTROLLER_CLASS . '/create/', 'create') ?>
<?php else: ?>
	<div class="box-info mass-edit">
		<div>
			<table class="table table-striped table-hover">
				<thead class="text-capitalize">
				<tr>
					<th><?= tb_header('title', 'title') ?></th>
					<th><?= tb_header('ticket_subject', 'ticket_subject') ?></th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($rows['values'] as $v): ?>
					<tr>
						<td style="width: 25%">
							<strong class="text-capitalize">
								<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['id']) ?>"><?= $v['title'] ?></a>
							</strong>
						</td>
						<td>

								<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['id']) ?>"><?= $v['ticket_subject'] ?></a>

						</td>
						<td class="text-right">
								<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['id']) ?>"
								   class="btn btn-default hidden-xs"
								   title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
								<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['id']) ?>"
								   data-toggle="modal" data-target="#confirm-delete" href="#"
								   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
						</td>
					</tr>
				<?php endforeach ?>
				</tbody>
			</table>
		</div>
	</div>
<?php endif ?>
<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
	<div class="row">
		<div class="col-md-8">
			<?= generate_sub_headline('search_results', 'fa-list', $rows[ 'total' ]) ?>
			<hr class="visible-xs"/>
		</div>
		<div class="col-md-4 text-right">
		</div>
	</div>
	<hr/>

<?php if (empty($rows[ 'values' ])): ?>
	<?= tpl_no_values() ?>
<?php else: ?>
	<div class="box-info">
		<div class="table-responsive">
			<table class="table table-striped table-hover">
				<thead class="text-capitalize">
				<tr>
					<?php foreach ($rows['fields'] as $f): ?>
						<?php if (in_array($f->name, config_item('exclude_fields'))) continue; ?>
					<th><?= tb_header($f->name, '', FALSE) ?></th>
					<?php endforeach ?>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($rows[ 'values' ] as $v): ?>
					<tr>
						<?php foreach ($rows['fields'] as $f): ?>
							<?php if (in_array($f->name, config_item('exclude_fields'))) continue; ?>
							<td><?= $this->s->check_search_link($v[$f->name], $table, $f->name)?></td>
						<?php endforeach ?>
					</tr>
				<?php endforeach ?>
				</tbody>
			</table>
		</div>
	</div>
<?php endif ?>
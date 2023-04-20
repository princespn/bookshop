<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<form action="" role="form" id="form" class="form-horizontal" method="get" accept-charset="utf-8">
	<div class="row">
		<div class="col-md-4">
			<?= generate_sub_headline(lang('advanced_search'), 'fa-search', '', FALSE) ?>
		</div>
		<div class="col-md-8 text-right">
		</div>
	</div>
	<hr/>
	<div class="row">
		<div class="col-md-12">
			<div class="box-info">
				<div class="tab-pane active" id="name">
					<div class="hidden-xs">
						<h3 class="text-capitalize"><?= lang('advanced_search') ?></h3>
						<span><?= lang('select_the_options_to_search_for') ?></span>
					</div>
					<hr/>
					<div class="form-group">
						<label for="table" class="col-md-3 control-label"><?= lang('select_table') ?></label>
						<div class="col-md-5">
							<?= form_dropdown('table', options('db_tables'), $default_table, 'onchange="updatecolumn(1)" id="table-1" class="form-control required"') ?>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<label for="column"
						       class="col-md-3 control-label"><?= lang('column') ?></label>
						<div class="col-md-2">
							<?= form_dropdown('column', options('', '', $default_columns), '', 'id="column-1" class="form-control required"') ?>
						</div>
						<label for="operator"
						       class="col-md-1 control-label"><?= lang('operator') ?></label>
						<div class="col-md-2">
							<?= form_dropdown('operator', options('operator'), 'LIKE', 'id="operator" class="form-control required"') ?>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<label for="value" class="col-md-3 control-label"><?= lang('search_term') ?></label>
						<div class="col-md-5">
							<?= form_input('value', '', 'id="value" class="form-control required"') ?>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<label for="operator"
						       class="col-md-3 control-label"><?= lang('record_limit') ?></label>
						<div class="col-md-2">
							<?= form_dropdown('limit', options('record_limit'), '50', 'id="limit" class="form-control required"') ?>
						</div>
						<label for="value" class="col-md-1 control-label"><?= lang('display_as') ?></label>
						<div class="col-md-2">
							<?= form_dropdown('output', options('search_display_output'), '', 'id="output" class="form-control required"') ?>

						</div>
					</div>
					<hr/>
					<div class="row">
						<div class="col-md-3 col-md-offset-3">
							<a href="#" class="btn btn-default" data-toggle="collapse" data-target="#advanced">
								+ <?= lang('advanced') ?>
							</a>
						</div>
					</div>
					<div id="advanced" class="collapse">
						<hr/>
						<div class="form-group">
							<label for="table" class="col-md-3 control-label"><?= lang('join_table') ?></label>
							<div class="col-md-5">
								<?= form_dropdown('join_table_1', options('db_tables', 'none'), '0', 'onchange="joincolumn(1)" id="join-table-1" class="form-control required"') ?>
							</div>
						</div>
						<hr/>
						<div id="join-column-box-1">
							<div class="form-group">
								<label for="on_column"
								       class="col-md-3 control-label"><?= lang('on_column') ?></label>
								<div class="col-md-2">
									<?= form_dropdown('join_column_1', options('', 'none', $default_columns), '', 'id="join-column-1" class="form-control required"') ?>
								</div>
								<label for="column"
								       class="col-md-1 control-label"><?= lang('equals') ?></label>

								<div class="col-md-2">
									<?= form_dropdown('on_column_1', options('', '', $default_columns), '', 'id="on-column-1" class="form-control required"') ?>
								</div>
							</div>
						</div>
						<hr/>
					</div>
				</div>
			</div>
		</div>
	</div>
	<nav class="navbar navbar-fixed-bottom save-changes">
		<div class="container text-right">
			<div class="row">
				<div class="col-md-12">
					<button class="btn btn-info navbar-btn block-phone" id="update-button"
					        type="submit"><?= i('fa fa-search') ?> <?= lang('search') ?></button>
				</div>
			</div>
		</div>
	</nav>
	<?= form_close() ?>
	<script>
		$('#join-column-box-1').hide(300);
		function updatecolumn(id) {
			$.get('<?=admin_url('search/load_columns/')?>', {table: $('#table-' + id).val()},
				function (data) {
					$('#column-' + id).html(data);
					$(".s2").select2();
				}
			);

			$.get('<?=admin_url('search/load_columns/on_column')?>', {table: $('#table-' + id).val()},
				function (data) {
					$('#on-column-' + id).html(data);
					$(".s2").select2();
				}
			);
		}

		function joincolumn(id) {
			if ($('#join-table-' + id).val() == '0') {
				$('#join-column-box-' + id).hide(300);
			}
			else {
				$('#join-column-box-' + id).show(300);
				$.get('<?=admin_url('search/load_columns/')?>', {table: $('#join-table-' + id).val()},
					function (data) {
						$('#join-column-' + id).html(data);
						$(".s2").select2();
					}
				);
			}
		}

		$("#form").validate();

	</script>

<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
	<div class="col-md-8">
		<?= generate_sub_headline(lang($row['name']), 'fa-list', '',FALSE) ?>
		<hr class="visible-xs"/>
	</div>
	<div class="col-md-4 text-right">
		<a href="<?= admin_url(CONTROLLER_CLASS . '/view') ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
				class="hidden-xs"><?= lang('view_languages') ?></span></a>
	</div>
</div>
<hr/>
<div class="row">
	<div class="col-md-2">
		<div class="list-group-item">
			<a class="pull-right additional-icon" href="#" data-toggle="collapse" data-target="#box-1"><i
					class="fa fa-chevron-down"></i></a>
			<strong class="text-capitalize"><?= i('fa fa-list') ?> <?= lang('language_files') ?></strong>
		</div>
		<div id="box-1" class="collapse in">
			<?php if (!empty($lang_files)): ?>
				<?php foreach ($lang_files as $m): ?>
					<a href="<?= admin_url(CONTROLLER_CLASS . '/update_entries/' . $id . '?file=' . str_replace('_lang.php',  '', $m) ) ?>"
					   class="list-group-item <?php if ($m == $file . '_lang.php'): ?> active <?php endif; ?> ">
						<span class="list-group-item-heading"><?= i('fa fa-file-text-o') ?> <?= $m ?></span>
					</a>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
		<br/>
		<div class="list-group-item">
			<a class="pull-right additional-icon" href="#" data-toggle="collapse" data-target="#box-2"><i
					class="fa fa-chevron-down"></i></a>
			<strong class="text-capitalize"><?= i('fa fa-list') ?> <?= lang('custom_language_entries') ?></strong>
		</div>
		<div id="box-2" class="collapse in">
			<a href="<?= admin_url(CONTROLLER_CLASS . '/update_custom_entries/' . $id . '/?file=custom') ?>"
			   class="list-group-item <?php if (uri(3) == 'update_custom_entries'): ?> active <?php endif; ?>">
				<span class="list-group-item-heading"><?= i('fa fa-file-text-o') ?> <?= lang('view_entries') ?></span>
			</a>
		</div>
	</div>
	<div class="col-md-10">
		<div class="box-info">
			<div class="row">
				<div class="col-md-7">
					<h3 class="text-capitalize">
						<?php if ($file == 'custom'): ?>
							<?= lang('custom_language_entries') ?>
						<?php else: ?>
							<?= lang('language_entries') ?>
						<?php endif; ?>
					</h3>
        <span class="text-capitalize">
	        <?php if ($file == 'custom'): ?>
		        <?= lang('custom_entries_description') ?>
		        <?php else: ?>
		        <?= lang('language_entries_description') ?>
	        <?php endif; ?>
                </span>
				</div>
				<div class="col-md-5 text-right">
					<?php if ($file != 'custom'): ?>
					<?= form_open('', 'method="get" id="search-form" class="form-horizontal"') ?>
					<?= form_hidden('file', $file) ?>
					<h3>
						<div class="input-group">
							<input type="text" name="term" class="form-control" value="<?=$this->input->get('term')?>"
							       placeholder="<?= lang('search_for_an_entry') ?>...">
				      <span class="input-group-btn">
				        <button class="btn btn-default" type="submit"><?=i('fa fa-search')?> <?= lang('search') ?></button>
				      </span>
						</div>
					</h3>
					<?= form_close() ?>
					<?php endif; ?>
					<?php if ($file == 'custom'): ?>
                        <a href="<?= admin_url(CONTROLLER_CLASS . '/map_custom_entries/' . $id . '/?file=custom') ?>"
                           class="btn btn-primary text-right <?= is_disabled('create') ?>"><?= i('fa fa-refresh') ?> <span
                                    class="hidden-xs"><?= lang('map_custom_entries') ?></span></a>
                        <a data-toggle="modal" data-target="#add-entry" href="#"
                           class="btn btn-primary text-right <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
                                    class="hidden-xs"><?= lang('add_custom_entry') ?></span></a>
					<?php endif; ?>
				</div>
			</div>
			<hr/>
			<?php if (empty($lang_entries)): ?>
				<?= tpl_no_values() ?>
			<?php else: ?>
				<?= form_open('', 'id="form" class="form-horizontal"') ?>
				<div class=" hidden-xs">
					<div class="row text-capitalize">
						<div class="col-md-4 text-right"><?= tb_header('tag_default_value', '', FALSE) ?></div>
						<div class="col-md-8"><?= tb_header('custom_value', '', FALSE) ?></div>
					</div>
					<hr/>
				</div>
				<?php foreach ($lang_entries as $k => $v): ?>
					<div class="form-group">
						<div class="col-md-4 text-right">
							<strong class="control-label text-right">
								<?php if ($file == 'custom'): ?>
									<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete_entry/' . $k .'/' . $id) ?>"
									   data-toggle="modal" data-target="#confirm-delete" href="#"
									   class="text-danger <?= is_disabled('delete') ?>"><?= i('fa  fa-minus-circle') ?></a>
								<?php endif; ?>
								<?= format_tag($k) ?></strong>
							<br/>
							<?php if ($file != 'custom'): ?>
								<small><?= lang($v) ?></small>
							<?php endif; ?>
						</div>
						<div class="col-md-8">
							<textarea name="lang[<?= $k ?>]" class="form-control"><?= check_custom_value($k, $v, $lang_custom_entries) ?></textarea>
						</div>
					</div>
					<hr/>
				<?php endforeach; ?>
				<nav class="navbar navbar-fixed-bottom  save-changes">
					<div class="container text-right">
						<div class="row">
							<div class="col-md-12">
                                <a class="btn btn-danger" href="<?=admin_url(CONTROLLER_CLASS . '/reset/' . $id) ?>">
                                    <?=i('fa fa-refresh')?> <?=lang('reset_entries')?></a>
								<button class="btn btn-info navbar-btn block-phone <?= is_disabled('update', TRUE) ?>"
								        type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
							</div>
						</div>
					</div>
				</nav>
				<?= form_hidden('file', $file) ?>
				<?= form_hidden('id', $id) ?>
				<?= form_close() ?>
			<?php endif; ?>
		</div>
	</div>
</div>
<div class="modal fade" id="add-entry" tabindex="-1" role="dialog" aria-labelledby="modal-title"
     aria-hidden="true">
	<div class="modal-dialog" id="modal-title">
		<div class="modal-content">
			<?= form_open(admin_url(CONTROLLER_CLASS . '/create_custom_entry/' . $id), 'id="create-form"') ?>
			<div class="modal-body text-capitalize">
				<h3><i class="fa fa-key"></i> <?= lang('enter_language_key') ?></h3>
				<span><?= lang('enter_unique_language_key_to_use') ?></span>
				<hr/>
				<input type="text" name="key" class="form-control required"/>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('cancel') ?></button>
				<button class="btn btn-primary" type="submit"><?= lang('continue') ?></button>
			</div>
			<?= form_close() ?>
		</div>
	</div>
</div>
<br />
<!-- Load JS for Page -->
<script>
	$("#search-form").validate();
	$("#form").validate({
		ignore: "",
		submitHandler: function (form) {
			$.ajax({
				url: '<?=current_url()?>',
				type: 'POST',
				dataType: 'json',
				data: $('#form').serialize(),
				beforeSend: function () {
					$('#update-button').button('loading');
				},
				complete: function () {
					$('#update-button').button('reset');
				},
				success: function (response) {
					if (response.type == 'success') {
						$('.alert-danger').remove();
						$('.form-control').removeClass('error');

						if (response.redirect) {
							location.href = response.redirect;
						}
						else {
							$('#response').html('<?=alert('success')?>');

							setTimeout(function () {
								$('.alert-msg').fadeOut('slow');
							}, 5000);
						}
					}
					else {
						$('#response').html('<?=alert('error')?>');
					}

					$('#msg-details').html(response.msg);
				},
				error: function (xhr, ajaxOptions, thrownError) {
					<?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	});

</script>
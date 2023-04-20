<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'id="form" class="form-horizontal"') ?>
<div class="row">
	<div class="col-md-4">
		<h2 class="sub-header block-title"><?= i('fa fa-pencil') ?> <?= lang('manage_form') ?></h2>
	</div>
	<div class="col-md-8 text-right">
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('left', CONTROLLER_METHOD, $row['prev']) ?>
			<?php if ($id > 1): ?>
				<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $id) ?>" data-toggle="modal"
				   data-target="#confirm-delete" href="#"
				   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><i class="fa fa-trash-o"></i> <span
						class="hidden-xs"><?= lang('delete') ?></span></a>
			<?php endif; ?>
		<?php endif; ?>
		<a href="<?= admin_url(CONTROLLER_CLASS . '/view') ?>" class="btn btn-primary"><i class="fa fa-search"></i>
			<span class="hidden-xs"><?= lang('view_forms') ?></span></a>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('right', CONTROLLER_METHOD, $row['next']) ?>
		<?php endif; ?>
	</div>
</div>
<hr/>
<div class="box-info">
	<h3><?= lang('manage_form_details') ?></h3>
	<span><?= lang('manage_form_details_desc') ?></span>
	<hr/>
	<div class="form-group">
		<label for="form_name" class="col-md-3 control-label"><?= lang('form_name') ?></label>

		<div class="col-md-5">
			<?php if ($row['form_type'] == 'custom'): ?>
				<?= form_input('form_name', set_value('form_name', $row['form_name']), 'class="' . css_error('form_name') . ' form-control required"') ?>
			<?php else: ?>
				<span class="form-control"><?= lang($row['form_name']) ?></span>
			<?php endif; ?>
		</div>
	</div>
	<hr/>
	<div class="form-group">
		<label for="form_description" class="col-md-3 control-label"><?= lang('description') ?></label>

		<div class="col-md-5">
			<?php if ($row['form_type'] == 'custom'): ?>
				<?= form_textarea('form_description', set_value('form_description', $row['form_description']), 'class="' . css_error('form_description') . ' form-control required"') ?>
			<?php else: ?>
				<span class="form-control"><?= lang($row['form_description']) ?></span>
			<?php endif; ?>
		</div>
	</div>
	<?php if ($row['form_type'] == 'custom'): ?>
		<hr/>
		<div class="form-group">
			<label for="form_method" class="col-md-3 control-label"><?= lang('form_method') ?></label>

			<div class="col-md-5">
				<?= form_dropdown('form_method', options('get_post'), $row['form_method'], 'class="form-control"') ?>
			</div>
		</div>
		<hr/>
		<div class="form-group">
			<label for="form_processor" class="col-md-3 control-label"><?= lang('process_form_function') ?></label>

			<div class=" col-md-5">
				<?= form_dropdown('form_processor', options('form_processor'), $row['form_processor'], 'id="processor" class="form-control"') ?>
			</div>
		</div>
		<hr/>
		<div class="form-group">
			<label for="function" class="col-md-3 control-label"><?= lang('function') ?></label>

			<div class="col-md-5">
				<?= form_input('function', set_value('function', $row['function']), 'placeholder="' . $sts_site_email . '" class="' . css_error('function') . ' form-control required"') ?>
			</div>
		</div>
		<hr/>
		<div class="form-group">
			<label for="redirect_url" class="col-md-3 control-label"><?= lang('thank_you_url') ?></label>

			<div class="col-md-5">
				<?= form_input('redirect_url', set_value('redirect_url', $row['redirect_url']), 'placeholder="' . base_url() . '" class="' . css_error('redirect_url') . ' form-control"') ?>
			</div>
		</div>
		<hr/>
		<?php if ($id > 3): ?>
		<div class="form-group">
			<label for="list_id" class="col-md-3 control-label"><?= lang('subscribe_form_to_list') ?></label>
			<div class="col-md-5">
				<select id="list_id" class="ajax_mailing_list form-control select2"
				        name="list_id">
					<?php if (empty($row['list_id'])): ?>
						<option value="0"
						        selected><?= lang('none')?></option>
					<?php else: ?>
					<option value="<?= $row['list_id'] ?>"
					        selected><?= $row['list_name'] ?></option>
					<?php endif; ?>
				</select>
				</div>
		</div>
		<hr/>
		<?php endif; ?>
	<?php endif; ?>
</div>
<?php if ($row['form_type'] == 'custom'): ?>
	<nav class="navbar navbar-fixed-bottom  save-changes">
		<div class="container text-right">
			<div class="row">
				<div class="col-md-12">
                    <a href="<?= admin_url(CONTROLLER_CLASS . '/update_fields/' . $row['form_id']) ?>"
                       class="tip btn btn-default" <?= is_disabled('update', TRUE) ?>>
                        <?= i('fa fa-list') ?> <?= lang('manage_form_fields') ?></a>
					<button id="save-changes"
					        class="btn btn-info navbar-btn block-phone" <?= is_disabled('update', TRUE) ?>
					        type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
				</div>
			</div>
		</div>
	</nav>
<?php endif; ?>
<?php if (CONTROLLER_FUNCTION == 'update'): ?>
	<?= form_hidden('form_id', $id) ?>
<?php endif; ?>
<?= form_close() ?>
<div id="info"></div>
<script>
    //add mailing list
    $(".ajax_mailing_list").select2({
        ajax: {
            url: '<?=admin_url(TBL_EMAIL_MAILING_LISTS . '/search/ajax/')?>',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    list_name: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data, page) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.list_id,
                            text: item.list_name
                        }
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });

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
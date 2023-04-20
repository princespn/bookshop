<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
<div class="row">
	<div class="col-md-5">
		<?= generate_sub_headline(lang('manage_review'), 'fa-list', '', FALSE) ?>
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
				class="hidden-xs"><?= lang('view_reviews') ?></span></a>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('right', CONTROLLER_METHOD, $row[ 'next' ]) ?>
		<?php endif; ?>
	</div>
</div>
<hr/>
<div class="box-info">
	<ul class="resp-tabs nav nav-tabs text-capitalize" role="tablist">
		<li class="active"><a href="#details" role="tab" data-toggle="tab"><?= lang('details') ?></a></li>
		<li><a href="#notes" role="tab" data-toggle="tab"><?= lang('notes') ?></a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane fade in active" id="details">
			<h3 class="text-capitalize"><?= lang('review_details') ?></h3>
			<hr/>
			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<?= lang('rating', 'date', array( 'class' => 'col-md-3 control-label' )) ?>
						<div class="r col-md-2">
							<input name="ratings" class="rating form-control hide" value="<?= $row[ 'ratings' ] ?>"
							       data-symbol="&#xf005;"
							       data-glyphicon="false" data-rating-class="rating-fa" data-size="xs"
							       data-show-clear="false" data-show-caption="false">
						</div>
						<?= lang('date', 'date', array( 'class' => 'col-md-1 control-label' )) ?>
						<div class="r col-md-2">
							<div class="input-group">
								<?= form_input('date', set_value('date', $row['date_formatted']), 'class="' . css_error('date_formatted') . ' form-control datepicker-input required"') ?>
                                <span class="input-group-addon"><i class="fa fa-calendar"></i> </span>
							</div>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<?= lang('member', 'member', array( 'class' => 'col-md-3 control-label' )) ?>
						<div class="r col-md-2">
							<select id="member_id" class="form-control select2"
							        name="member_id">
								<option value="<?= set_value('member_id', $row[ 'member_id' ]) ?>"
								        selected><?=$row[ 'username' ]?></option>
							</select>
						</div>
						<?= lang('product', 'product', array( 'class' => 'col-md-1 control-label' )) ?>
						<div class="r col-md-2">
							<select id="product_id" class="form-control select2"
							        name="product_id">
								<option value="<?= set_value('product_id', $row[ 'product_id' ]) ?>"
								        selected><?=$row[ 'product_name' ]?></option>
							</select>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<?= lang('approved', 'status', array( 'class' => 'col-md-3 control-label' )) ?>
						<div class="r col-md-2">
							<?= form_dropdown('status', options('yes_no'), $row[ 'status' ], 'class="form-control required" id="status"') ?>
						</div>
						<?= lang('sort_order', 'sort_order', array( 'class' => 'col-md-1 control-label' )) ?>
						<div class="r col-md-2">
                            <input type="number" name="sort_order" value="<?=set_value('sort_order', $row[ 'sort_order' ])?>" class="form-control required digits" />
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<?= lang('title', 'title', array( 'class' => 'col-md-3 control-label' )) ?>
						<div class="r col-md-5">
							<?= form_input('title', set_value('title', $row[ 'title' ], FALSE), 'class="' . css_error('title') . ' form-control required"') ?>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<?= lang('comment', 'comment', array( 'class' => 'col-md-3 control-label' )) ?>
						<div class="r col-md-5">
							<textarea name="comment" class="form-control required"
							          rows="10"><?= set_value('comment', $row[ 'comment' ], FALSE) ?></textarea>
						</div>
					</div>
					<hr/>
				</div>
			</div>
		</div>
		<div class="tab-pane fade in" id="notes">
			<h3 class="text-capitalize"><?= lang('notes') ?></h3>
			<hr/>
			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<?= lang('notes', 'notes', array( 'class' => 'col-md-3 control-label' )) ?>
						<div class="r col-md-5">
							<textarea name="notes" class="form-control"
							          rows="10"><?= $row[ 'notes' ] ?></textarea>
						</div>
					</div>
					<hr/>
				</div>
			</div>
		</div>
	</div>
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
	<?= form_hidden('id', $id) ?>
<?php endif; ?>
<?= form_close() ?>
<script src="<?= base_url('themes/admin/' . $sts_admin_layout_theme . '/third/star-rating/star-rating.min.js') ?>"></script>
<script>
	$("#member_id").select2({
		ajax: {
			url: '<?=admin_url(TBL_MEMBERS . '/search/ajax/')?>',
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					username: params.term, // search term
					page: params.page
				};
			},
			processResults: function (data, page) {
				return {
					results: $.map(data, function (item) {
						return {
							id: item.member_id,
							text: item.username
						}
					})
				};
			},
			cache: true
		},
		minimumInputLength: 2
	});

	$("#product_id").select2({
		ajax: {
			url: '<?=admin_url(TBL_PRODUCTS . '/search/ajax/')?>',
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					product_name: params.term, // search term
					page: params.page
				};
			},
			processResults: function (data, page) {
				return {
					results: $.map(data, function (item) {
						return {
							id: item.product_id,
							text: item.product_name
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
						if (response['error_fields']) {
							$.each(response['error_fields'], function (key, val) {
								$('#' + key).addClass('error');
								$('#' + key).focus();
							});
						}
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
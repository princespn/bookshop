<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
<div class="row">
    <div class="col-md-5">
        <h2 class="sub-header block-title"><?= i('fa fa-pencil') ?> <?= lang(CONTROLLER_FUNCTION . '_' . singular(CONTROLLER_CLASS)) ?></h2>
    </div>
    <div class="col-md-7 text-right">
        <a href="<?= admin_url(CONTROLLER_CLASS) ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
                    class="hidden-xs"><?= lang('view_filters') ?></span></a>
    </div>
</div>
<hr/>
<div class="box-info">
    <ul class="resp-tabs nav nav-tabs text-capitalize" role="tablist">
        <li class="active"><a href="#details" role="tab" data-toggle="tab"><?= lang('details') ?></a></li>
		<?php if (!empty($row['values'])): ?>
            <li><a href="#values" role="tab" data-toggle="tab"><?= lang('values') ?></a></li>
		<?php endif; ?>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade in active" id="details">
            <h3 class="text-capitalize"><?= lang('filter_details') ?></h3>
            <hr/>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
						<?= lang('status', 'status', array('class' => 'col-md-3 control-label')) ?>
                        <div class="r col-md-5">
							<?= form_dropdown('status', options('active'), $row['status'], 'class="form-control required" id="status"') ?>
                        </div>
                    </div>
                    <hr/>
                    <div class="form-group">
						<?= lang('filter_name', 'filter_name', array('class' => 'col-md-3 control-label')) ?>
                        <div class="r col-md-5">
							<?= form_input('filter_name', set_value('filter_name', $row['filter_name']), 'class="' . css_error('filter_name') . ' form-control required"') ?>
                        </div>
                    </div>
                    <hr/>
                    <div class="form-group">
						<?= lang('filter_description', 'filter_description', array('class' => 'col-md-3 control-label')) ?>
                        <div class="r col-md-5">
							<?= form_input('filter_description', set_value('filter_description', $row['filter_description']), 'class="' . css_error('filter_description') . ' form-control required"') ?>
                        </div>
                    </div>
                    <hr/>
                </div>
            </div>
        </div>
		<?php $i = 1; ?>
		<?php if (!empty($row['values'])): ?>
            <div class="tab-pane fade in" id="values">
                <div id="regions-div">
                    <h3 class="text-capitalize"><?= lang('filter_values') ?>
                        <span class="pull-right"><a
                                    href="javascript:add_region(<?= count($row['values']) ?>)"
                                    class="btn btn-primary"><?= i('fa fa-plus') ?> <?= lang('add_filter_value') ?></a></span>
                    </h3>
                    <span><?= lang('specify_different_filter_values_for_your_store_items') ?></span>
                    <hr/>
					<?php foreach ($row['values'] as $k => $v): ?>
                        <div id="rowdiv-<?= $i ?>">
                            <div class="form-group">
								<?= lang('between', 'between', array('class' => 'col-md-3 control-label')) ?>
                                <div class="r col-md-2">
                                    <input type="text" name="values[<?= $k ?>][initial_value]"
                                           value="<?= set_value('initial_value', $v['initial_value']) ?>"
                                           class="form-control number required"/>
                                </div>
								<?= lang('and', 'end_date', array('class' => 'col-md-1 control-label')) ?>
                                <div class="r col-md-2">
                                    <input type="text" name="values[<?= $k ?>][secondary_value]"
                                           value="<?= set_value('secondary_value', $v['secondary_value']) ?>"
                                           class="form-control number required"/>
	                                <?= form_hidden('values[' . $k . '][id]', $v['id']) ?>
                                </div>
								<?php if ($k > 1): ?>
                                    <div class="col-md-1 text-right">
                                        <a href="javascript:remove_div('#rowdiv-<?= $i ?>')"
                                           class="btn btn-danger block-phone <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> </a>
                                    </div>
								<?php endif; ?>
                            </div>
                            <hr/>
                        </div>
						<?php $i++; ?>
					<?php endforeach; ?>
                </div>
            </div>
		<?php endif; ?>
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
</div>
<?= form_hidden('filter_id', $id) ?>
<?= form_close() ?>
<script>
    <?php if (!empty($row['values'])): ?>
    var next_id = <?=count($row['values']) + 1?>;

    function add_region(image_id) {

        if (next_id < 21) {
            var html = '<div id="rowdiv-' + next_id + '">';
            html += '       <div class="form-group">';
            html += '           <?= lang('between', 'between', array('class' => 'col-md-3 control-label')) ?>';
            html += '           <div class="r col-md-2">';
            html += '               <input type="text" name="values[' + next_id + '][initial_value]" value="<?= set_value('initial_value', $v['initial_value']) ?>"class="form-control number required"/>';
            html += '           </div>';
            html += '           <?= lang('and', 'end_date', array('class' => 'col-md-1 control-label')) ?>';
            html += '           <div class="r col-md-2">';
            html += '               <input type="text" name="values[' + next_id + '][secondary_value]" value="<?= set_value('secondary_value', $v['secondary_value']) ?>"class="form-control number required"/> </div>';
            html += '                   <div class="col-md-1 text-right">';
            html += '           <a href="javascript:remove_div(\'#rowdiv-' + next_id + '\')"class="btn btn-danger block-phone <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> </a> </div>';
            html += '       </div>';
            html += '   <hr/>';
            html += '   </div>';

            $('#regions-div').append(html);

            next_id++;
        }
    }
    <?php endif; ?>
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
                        } else {
                            $('#response').html('<?=alert('success')?>');

                            setTimeout(function () {
                                $('.alert-msg').fadeOut('slow');
                            }, 5000);
                        }

                    } else {
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
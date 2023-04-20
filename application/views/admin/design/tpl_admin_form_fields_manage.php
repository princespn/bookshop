<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'id="form" class="form-horizontal"') ?>
<div class="row">
    <div class="col-md-4">
        <h2 class="sub-header block-title"><?= i('fa fa-list') ?> <?= lang('manage_form_fields') ?></h2>
    </div>
    <div class="col-md-8 text-right">
        <?php if (CONTROLLER_FUNCTION == 'update_fields'): ?>
            <?= prev_next_item('left', CONTROLLER_METHOD, $row['prev']) ?>
        <?php endif; ?>
        <a href="<?= admin_url(CONTROLLER_CLASS . '/view') ?>" class="btn btn-primary"><i class="fa fa-search"></i>
            <span class="hidden-xs"><?= lang('view_forms') ?></span></a>
        <?php if (CONTROLLER_FUNCTION == 'update_fields'): ?>
            <?= prev_next_item('right', CONTROLLER_METHOD, $row['next']) ?>
        <?php endif; ?>
    </div>
</div>
<hr/>
<div class="box-info">
    <br/>
    <div class="col-md-12">
        <h3 class="text-capitalize"><?= $row['form_name'] ?></h3>
        <hr/>
        <?php if (empty($row['values'])): ?>
            <?= tpl_no_values(CONTROLLER_CLASS . '/create_field/' . $row['form_id'], 'add_form_field') ?>
        <?php else: ?>
        <div class="row visible-lg div-header">
            <div class="col-md-1">
                <?= tb_header(form_checkbox('check-all', '', '', 'class="req-all"') . ' ' . lang('required'), '', FALSE) ?>
            </div>
            <div class="col-md-3 col-md-offset-2">
                <?= tb_header('default_values', '', FALSE) ?>
            </div>
            <div class="col-md-2 text-center">
                <?= tb_header(form_checkbox('check-all', '', '', 'class="public-all"') . ' ' . lang('visible'), '', FALSE) ?></div>
            <div class="col-md-2 text-center">
                <?php if ($row['form_id'] == 1): ?>
                    <?= tb_header(form_checkbox('check-all', '', '', 'class="account-all"') . ' ' . lang('visible_in_members_area'), '', FALSE) ?>
               <?php else: ?>
                    <?=tb_header('sub_form', '', FALSE) ?>
                <?php endif ?>
            </div>
        </div>
        <hr class="hidden-xs"/>
        <div id="sortable">
            <?php foreach ($row['values'] as $v): ?>
                <div class="ui-state-default" id="formid-<?= $v['field_id'] ?>">
                    <div class="row">
                        <div class="col-md-1 text-center">
	                        <?php if (in_array($v['form_field'], config_option('default_required_fields'))): ?>
							<span class="label label-warning"><?=lang('required')?></span>
		                        <?= form_hidden('form_field[' . $v['field_id'] . '][field_required]', '1', 1); ?>
		                    <?php else: ?>
                            <?= form_checkbox('form_field[' . $v['field_id'] . '][field_required]', '1', $v['field_required'], 'class="req"'); ?>
                            <span class="visible-xs btn btn-default"><?= lang('required') ?></span>
	                        <?php endif; ?>
                        </div>
                        <label for="<?=$v['form_field']?>" class="col-md-2 control-label"><?=$v['field_name']?></label>
                        <div class="r col-md-3">
                            <?= $v['field'] ?>
                        </div>
                        <div class="r col-md-2 text-center">
	                        <?php if (in_array($v['form_field'], config_option('default_required_fields'))): ?>
		                        <span class="label label-success"><?=lang('yes')?></span>
		                        <?= form_hidden('form_field[' . $v['field_id'] . '][show_public]', '1', 1); ?>
	                        <?php else: ?>
                            <?= form_checkbox('form_field[' . $v['field_id'] . '][show_public]', '1', $v['show_public'], 'class="public"'); ?>
                            <span class="visible-xs btn btn-default"><?= lang('visible') ?></span>
	                        <?php endif; ?>
                        </div>
                        <div class="r col-md-2 text-center">
                            <?php if ($row['form_id'] == 1): ?>
                                <?php if ($v['form_field'] == 'password'): ?>
                                    <?= form_hidden('form_field[' . $v['field_id'] . '][show_account]', '0', $v['show_account']); ?>
                                <?php else: ?>
                                    <?php if (empty($v['sub_form'])): ?>
                                        <?= form_checkbox('form_field[' . $v['field_id'] . '][show_account]', '1', $v['show_account'], 'class="account"'); ?>
                                        <span class="visible-xs btn btn-default"><?= lang('visible_in_members_area') ?></span>
                                    <?php else: ?>
                                        <span class="label label-info"><?=$v['sub_form']?></span>
                                        <?= form_hidden('form_field[' . $v['field_id'] . '][show_account]', '1', $v['show_account']); ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="label label-info"><?=$v['sub_form']?></span>
	                            <input type="hidden" name="form_field[<?=$v['field_id']?>][show_account]"
	                                   value="<?=set_value('show_account', $v['show_account'])?>" />
                            <?php endif; ?>
                        </div>
                        <div class="r col-md-2 text-right">
                            <span class="btn btn-primary handle visible-lg <?= is_disabled('update', TRUE) ?>"><i
                                    class="fa fa-sort"></i></span>
                            <a href="<?= admin_url(CONTROLLER_CLASS . '/update_field/' . $v['field_id']) ?>" <?= is_disabled('update') ?>
                               class="md-trigger btn btn-default block-phone"><?= i('fa fa-pencil') ?></a>
                            <?php if ($v['custom'] == 1): ?>
                                <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete_field/' . $v['field_id']) ?>"
                                   data-toggle="modal" data-target="#confirm-delete" href="#"
                                   class="md-trigger btn btn-danger block-phone <?= is_disabled('delete') ?>"><i
                                        class="fa fa-trash-o"></i> <span class="visible-xs"><?= lang('delete') ?></span></a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <hr/>
                </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<nav class="navbar navbar-fixed-bottom  save-changes">
    <div class="container text-right">
        <div class="row">
            <div class="col-md-12">
                <a href="<?= admin_url(CONTROLLER_CLASS . '/create_field/' . $row['form_id']) ?>"
                   class="btn btn-success navbar-btn block-phone <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <?= lang('add_custom_field') ?></a>
                <button id="save-changes" class="btn btn-info navbar-btn block-phone" <?= is_disabled('update', true) ?>
                        type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
            </div>
        </div>
    </div>
</nav>
<?=form_hidden('form_id', $id)?>
<?= form_close() ?>
<div id="update"></div>
<a name="custom"></a>
<script>
    $("select#field_type").change(function () {
        $("select#field_type option:selected").each(function () {
            if ($(this).attr("value") == "select") {
                $("#options").show(300);
            }
            else {
                $("#options").hide(300);
            }
        });
    }).change();

    $(".country_id").select2({
        ajax: {
            url: '<?=admin_url(TBL_COUNTRIES. '/search_countries/')?>',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    country_name: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data, page) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.country_id,
                            text: item.country_name
                        }
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 1
    });

    function updateregion(id, select) {
        $.get('<?=admin_url('regions/load_regions/')?>' + select + '/', { country_id: $('#' + select + '_country').val() },
            function (data) {
                $('#' + id).html(data);
                $(".s2").select2();
            }
        );
    }

    $('.req-all')
        .on('ifChecked', function (event) {
            $('.req').iCheck('check');
        })
        .on('ifUnchecked', function () {
            $('.req').iCheck('uncheck');
        });

    $('.public-all')
        .on('ifChecked', function (event) {
            $('.public').iCheck('check');
        })
        .on('ifUnchecked', function () {
            $('.public').iCheck('uncheck');
        });

    $('.account-all')
        .on('ifChecked', function (event) {
            $('.account').iCheck('check');
        })
        .on('ifUnchecked', function () {
            $('.account').iCheck('uncheck');
        });
    $(function () {
        $('#sortable').sortable({
            handle: '.handle',
            placeholder: "ui-state-highlight",
            update: function () {
                var order = $('#sortable').sortable('serialize');
                $("#update").load("<?=admin_url(CONTROLLER_CLASS . '/update_order/' . $id)?>?" + order);
            }
        });
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
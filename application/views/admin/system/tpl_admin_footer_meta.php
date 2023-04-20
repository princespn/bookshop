<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

	<!-- Slimscroll js -->
	<script
		src="<?= base_url(); ?>themes/admin/<?= $sts_admin_layout_theme ?>/third/slimscroll/jquery.slimscroll.min.js"></script>

	<!-- password meter -->
	<script src="<?= base_url() ?>js/password-meter/pwstrength-bootstrap.js"></script>

	<!-- Bootstrap file input js -->
	<script
		src="<?= base_url(); ?>themes/admin/<?= $sts_admin_layout_theme ?>/third/input/bootstrap.file-input.js"></script>

	<!-- Icheck js -->
	<script src="<?= base_url(); ?>themes/admin/<?= $sts_admin_layout_theme ?>/third/icheck/icheck.min.js"></script>

	<script src="<?= base_url(); ?>themes/admin/<?= $sts_admin_layout_theme ?>/third/tabdrop/js/tabdrop.js"></script>

	<script src="<?= base_url(); ?>themes/admin/<?= $sts_admin_layout_theme ?>/third/form/jquery.form.js"></script>

	<!-- for left hand admin menu -->
	<script src="<?= base_url(); ?>themes/admin/<?= $sts_admin_layout_theme ?>/third/menu/menu.js"></script>

	<script src="<?= base_url(); ?>js/colorbox/jquery.colorbox-min.js"></script>

	<!-- TEMPLATE JAVASCRIPT -->
	<script src="<?= base_url(); ?>themes/admin/<?= $sts_admin_layout_theme ?>/js/theme.js"></script>

	<script>

		$(function () {
			$('.datepicker-input').datepicker({format: '<?=$format_date?>'});
		});

		$("#logout-button").click(function () {
			if (confirm("<?=lang('are_you_sure_logout')?>")) {
				return true;
			}

			return false;
		});

		<?php if (!is_enabled('update')): ?>
		$('.content-body .form-control').attr('readonly', true);
		<?php endif; ?>

        function ajax_it(current_url, form_id)
        {
            $.ajax({
                url: current_url,
                type: 'POST',
                dataType: 'json',
                data: $('#' + form_id).serialize(),
                beforeSend: function () {
                    $('.btn-submit').button('loading');
                },
                complete: function () {
                    $('.btn-submit').button('reset');
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
	</script>

    <?php if (!empty($meta_footer)): ?>
        <?=$meta_footer?>
    <?php endif; ?>
	</body>
	</html>

<?php if ($enable_db_debugging): ?>
	<div style="height: 960px;"></div>
	<?php $this->output->enable_profiler(TRUE); ?>
<?php endif; ?>
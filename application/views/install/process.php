<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
	<div class="card">
		<div class="card-body">
			<div class="row">
				<div class="col-lg-12 text-center">
                    <i id="smiley" class="text-secondary fa fa-smile-o fa-5x"></i>
					<textarea class="form-control"  rows="20" id="status" readonly></textarea>
				</div>
			</div>
            <br />
			<div class="row">
				<div class="col-lg-12">
					<div id="progress-box" class="text-center">
						<div id="progress-div" class="progress progress-striped active">
							<div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="height: 2rem; width: 1%"></div>
						</div>
                        <h4 class="mt-3 mb-3"><?=lang('please_wait_while_your_system_is_setup')?>
                           </h4>
                        <div class="mb-3"><a class="btn btn-sm btn-outline-secondary" id="show-window" href="javascript:void(0)"><?=lang('show_window')?></a></div>
					</div>
					<a href="<?=$url?>" id="continue"
					        class="d-none btn btn-primary btn-lg btn-block"><?= i('fa fa-caret-right') ?> <?=lang('done')?>! <?= lang('click_here_to_continue') ?></a>
				</div>
			</div>
		</div>
	</div>
	<?php foreach ($row as $k => $v): ?>
    <?php if ($k == 'step'): ?>
			<?=form_hidden($k, 'sql')?>
   <?php else: ?>
	<?=form_hidden($k, $v)?>
		<?php endif; ?>
	<?php endforeach; ?>
	<input type="hidden" name="offset" value="0" id="offset" />

</form>
<script>
    submit_form();
    $("#status").hide();
    $("#show-window").click(function(){
        $("#status").toggle(500);
        $("#smiley").toggle(500);
    });
    function submit_form() {
        $.ajax({
            url: '<?=site_url('install')?>',
            type: 'POST',
            dataType: 'json',
            data: $('#form').serialize(),
            success: function (response) {
                if (response.type == 'continue') {

                    if (response.error == 0) {
                        $('#offset').val(response.offset);
                        $("#status").append(response.text);
                        $("#progress-bar").css('width', response.width + '%');//update the progress bar width

                        var textArea = $('#status');
                        textArea.scrollTop(textArea[0].scrollHeight - textArea.height());
                        submit_form();
                    }
                    else {
                        console.log(response.msg);
                        alert(response.msg);
                    }
                }
                else {
                    $("#status").append("All Done on <?=$row['db_database']?>...........  \\\\ (•◡•) //");
                    $("#continue").removeClass('d-none');
                    $("#progress-box").addClass('d-none');

                    var url = '<?=$url?>';
                    $(location).attr('href',url);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }
</script>
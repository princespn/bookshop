<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<form action="" method="get" role="form" id="form" class="form-horizontal" accept-charset="utf-8">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="form-control" style="font-size: 13px; height: 500px; overflow: auto"><?= nl2br($license) ?></div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 text-center">
                    <p>
                    <div class="checkbox">
                        <label style="font-size: 18px;">
                            <input type="checkbox" id="agree" name="agree" value="1">
                            <?= lang('agree_to_license') ?>
                        </label>
                    </div>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <button id="continue" class="btn btn-primary btn-lg btn-block">
                        <?= i('fa fa-caret-right') ?> <?= lang('click_here_to_continue') ?></button>
                </div>
            </div>
        </div>
    </div>
    <?=form_hidden('lang', $lang)?>
	<?= form_hidden('step', 'requirements') ?>
	<?= form_hidden('cpanel_username', $this->input->get('u')) ?>
	<?= form_hidden('cpanel_password', $this->input->get('p')) ?>

</form>
<script>
    $('#continue').prop("disabled", true);
    $("#agree").change(function () {
        if (this.checked) {
            $('#continue').show(100);
            $('#continue').prop("disabled", false);
        }
        else {
            $('#continue').prop("disabled", true);
        }
    });
</script>

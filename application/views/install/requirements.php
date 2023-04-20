<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<form action="" method="get" role="form" id="form" class="form-horizontal" accept-charset="utf-8">
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-lg-12">
				<?php if (!empty($requirements['errors'])): ?>
                    <div class="alert alert-danger">
                    <h3><?= i('fa fa-cog') ?> <?= lang('system_errors') ?></h3>
                    <hr/>
					<?= $requirements['errors'] ?>
                    </div>
				<?php endif; ?>

				<?php if (!empty($requirements['success'])): ?>
                <div class="alert alert-success">
                    <h3><?= i('fa fa-cog') ?> <?= lang('requirements_passed') ?></h3>
                    <hr/>
					<?= $requirements['success'] ?>
                </div>
				<?php endif; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
			    <?php if (!empty($requirements['errors'])): ?>
                    <p class="lead text-danger text-center"><?= i('fa fa-times-circle') ?> <?= lang('please_fix_errors_to_continue') ?></p>
                    <p><a href="#" onclick="window.location.reload()" id="continue"
                          class="btn btn-danger btn-lg btn-block"><?= i('fa fa-refresh') ?> <?= lang('refresh_this_page') ?></a>
                    </p>
                    <p class="lead text-center">
                        <button id="continue"
                                class="btn btn-primary btn-lg btn-block"><?= i('fa fa-caret-right') ?> <?= lang('try_install_anyway') ?></button>
			    <?php else: ?>
                    <button id="continue"
                            class="btn btn-primary btn-lg btn-block"><?= i('fa fa-caret-right') ?> <?= lang('click_here_to_continue') ?></button>
			    <?php endif; ?>
            </div>
        </div>
    </div>
</div>
	<?=form_hidden('lang', $lang)?>
<?=form_hidden('step','configuration')?>
</form>

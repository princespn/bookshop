<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'id="form"') ?>
<div class="row">
    <div class="col-md-4">
        <h2 class="sub-header block-title"><?= i('fa fa-calendar') ?> <?= lang('events_calendar') ?></h2>
    </div>
    <div class="col-md-8 text-right">
        <a href="<?= admin_url(TBL_EVENTS_CALENDAR . '/create') ?>"
           class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <?= lang('create_event') ?></a>
    </div>
</div>
<hr/>
<div>
    <div class="row">
        <div class="col-md-5">
            <div class="box-info">
                <?= $calendar ?>
            </div>
        </div>
        <div class="col-md-7">
            <div class="box-info">
                <div id="events-content"></div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        $("#events-content").load("<?=admin_url(TBL_EVENTS_CALENDAR . '/events/' . $y . '/' . $m . '/' . $d)?>");
    });
    function ViewEvents(id) {
        $("#events-content").load("<?=admin_url(TBL_EVENTS_CALENDAR . '/events/' . $y . '/' . $m . '/"+id+"')?>");
        //$('#loading').hide();
    }
</script>
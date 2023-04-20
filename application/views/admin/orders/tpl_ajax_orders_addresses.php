<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="form-group">
    <label class="col-md-4 control-label"><?= lang('name') ?></label>

    <div class="col-md-6">
        <input name="name" type="text" value="<?= $row['name'] ?>" class="form-control required"/>
    </div>
</div>
<hr/>
<div id="address">
    <div class="form-group">
        <label class="col-md-4 control-label"><?= lang('company') ?></label>

        <div class="col-md-6">
            <input name="company" type="text" value="<?= $row['company'] ?>" class="form-control"/>
        </div>
    </div>
    <hr/>
    <div class="form-group">
        <label class="col-md-4 control-label"><?= lang('address_1') ?></label>

        <div class="col-md-6">
            <input name="address_1" type="text" value="<?= $row['address_1'] ?>" class="form-control required"/>
        </div>
    </div>
    <hr/>
    <div class="form-group">
        <label class="col-md-4 control-label"><?= lang('address_2') ?></label>

        <div class="col-md-6">
            <input name="address_2" type="text" value="<?= $row['address_2'] ?>" class="form-control"/>
        </div>
    </div>
    <hr/>
    <div class="form-group">
        <label class="col-md-4 control-label"><?= lang('city') ?></label>

        <div class="col-md-6">
            <input name="city" type="text" value="<?= $row['city'] ?>" class="form-control required"/>
        </div>
    </div>
    <hr/>
    <div class="form-group">
        <label class="col-md-4 control-label"><?= lang('state_province') ?></label>

        <div class="col-md-6">
            <div id="region_select">
                <?= form_dropdown('state', load_regions($row['country']), $row['state'], 'class="s2 select2 form-control required"') ?>
            </div>
        </div>
    </div>
    <hr/>
    <div class="form-group">
        <label class="col-md-4 control-label"><?= lang('country') ?></label>

        <div class="col-md-6">
            <?= form_dropdown('country', options('countries'), $row['country'], 'id="country" class="s2 select2 form-control required"') ?>
        </div>
    </div>
    <hr/>
    <div class="form-group">
        <label class="col-md-4 control-label"><?= lang('postal_code') ?></label>

        <div class="col-md-6">
            <input name="postal_code" type="text" value="<?= $row['postal_code'] ?>" class="form-control required"/>
        </div>
    </div>
    <hr/>
    <div class="form-group">
        <label class="col-md-4 control-label"><?= lang('phone') ?></label>

        <div class="col-md-6">
            <input name="phone" type="text" value="<?= $row['phone'] ?>" class="form-control"/>
        </div>
    </div>
    <hr/>
</div>
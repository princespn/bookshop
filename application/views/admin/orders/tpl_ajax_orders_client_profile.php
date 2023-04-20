<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div>
    <div class="form-group">
        <label class="col-md-4 control-label"><?= lang('first_name') ?></label>

        <div class="col-md-6">
            <input name="fname" type="text" value="<?= $row[ 'fname' ] ?>"
                   class="form-control required"/>
        </div>
    </div>
    <hr/>
    <div class="form-group">
        <label class="col-md-4 control-label"><?= lang('last_name') ?></label>

        <div class="col-md-6">
            <input name="lname" type="text" value="<?= $row[ 'lname' ] ?>"
                   class="form-control required"/>
        </div>
    </div>
    <hr/>
    <?php if (config_enabled('affiliate_marketing')): ?>
        <div class="form-group">
            <label class="col-md-4 control-label"><?= lang('referred_by') ?></label>

            <div class="col-md-6">
                <select id="affiliate_id" class="form-control select2" name="affiliate_id">
                    <option value="0" selected><?=lang('enter_referral_username_if_any')?></option>
                    <?php if (!empty($row[ 'sponsor_id' ])): ?>
                        <option value="<?= $row[ 'sponsor_id' ] ?>" selected><?= $row[ 'sponsor_username' ] ?></option>
                    <?php endif; ?>
                </select>
            </div>
        </div>
        <hr/>
    <?php endif; ?>

    <div class="form-group">
        <label class="col-md-4 control-label"><?= lang('company') ?></label>

        <div class="col-md-6">
            <input name="order_company" type="text" value="<?= $row[ 'company' ] ?>" class="form-control"/>
        </div>
    </div>
    <hr/>
    <div class="form-group">
        <label class="col-md-4 control-label"><?= lang('email_address') ?></label>

        <div class="col-md-6">
            <input name="order_primary_email" type="text" value="<?= $row[ 'primary_email' ] ?>"
                   class="form-control required email"/>
        </div>
    </div>
    <hr/>
    <div class="form-group">
        <label class="col-md-4 control-label"><?= lang('telephone') ?></label>

        <div class="col-md-6">
            <input name="order_telephone" type="text" value="<?= $row[ 'home_phone' ] ?>" class="form-control"/>
            <input name="member_id" type="hidden" value="<?= $row[ 'member_id' ] ?>" class="form-control"/>
        </div>
    </div>
    <hr/>
</div>
<script>
    $("#affiliate_id").select2({
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
</script>
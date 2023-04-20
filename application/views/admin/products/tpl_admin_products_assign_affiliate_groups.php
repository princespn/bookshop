<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<body class="tooltips">
<div class="container">
    <?= form_open('', 'role="form" id="form" target="_top" class="form-horizontal"') ?>
    <h3 class="text-capitalize"> <?= lang('affiliate_groups') ?></h3>
    <span><?= lang('search_for_affiliate_groups') ?></span>
    <hr/>
    <select multiple id="affiliate_groups" class="form-control select2" name="affiliate_groups[]">
        <?php if (!empty($groups)): ?>
            <?php foreach ($groups as $v): ?>
                <option value="<?= $v['group_id'] ?>" selected><?= $v['aff_group_name'] ?></option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
    <?= form_hidden('product_id', $id) ?>
    <hr/>
    <div class="text-right">
        <button type="submit" class="btn btn-primary"><?= lang('save_changes') ?></button>
    </div>
    <?= form_close(); ?>
    <script>
        //attributes
        $("#affiliate_groups").select2({
            ajax: {
                url: '<?=admin_url(TBL_AFFILIATE_GROUPS. '/search/ajax/')?>',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        aff_group_name: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, page) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                id: item.group_id,
                                text: item.aff_group_name
                            }
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 2
        });
    </script>
</div>
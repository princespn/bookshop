<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<body class="tooltips">
<div class="container">
    <?= form_open('', 'id="form" target="_top" class="form-horizontal"') ?>
    <h3 class="text-capitalize"> <?= lang('tax_rates') ?></h3>
    <span><?= lang('search_for_tax_rates') ?></span>
    <hr/>
    <select multiple id="tax_rates" class="form-control select2" name="tax_rates[]">
        <?php if (!empty($tax_rates)): ?>
            <?php foreach ($tax_rates as $v): ?>
                <option value="<?= $v['tax_rate_id'] ?>" selected><?= $v['tax_rate_name'] ?></option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
    <?= form_hidden('tax_class_id', $id) ?>
    <hr/>
    <div class="text-right">
        <button type="submit" class="btn btn-primary"><?= lang('save_changes') ?></button>
    </div>
    <?= form_close(); ?>
    <script>
        //attributes
        $("#tax_rates").select2({
            ajax: {
                url: '<?=admin_url(TBL_TAX_RATES. '/search/ajax/')?>',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        tax_rate_name: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, page) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                id: item.tax_rate_id,
                                text: item.tax_rate_name
                            }
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 1
        });
    </script>
</div>
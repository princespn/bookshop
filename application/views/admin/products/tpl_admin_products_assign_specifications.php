<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<body class="tooltips">
<div class="container">
    <?= form_open('', 'role="form" id="form" target="_top" class="form-horizontal"') ?>
    <h3 class="text-capitalize"> <?= lang('product_specifications') ?></h3>
    <span><?= lang('search_for_product_specifications') ?></span>
    <hr/>
    <select multiple id="product_specifications" class="form-control select2" name="product_specifications[]">
        <?php if (!empty($specifications)): ?>
            <?php foreach ($specifications as $v): ?>
                <option value="<?= $v['spec_id'] ?>" selected><?= $v['specification_name'] ?></option>
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
        //specifications
        $("#product_specifications").select2({
            ajax: {
                url: '<?=admin_url(TBL_PRODUCTS_SPECIFICATIONS. '/search/ajax/')?>',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        specification_name: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, page) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                id: item.spec_id,
                                text: item.specification_name
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
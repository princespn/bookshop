<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<body class="tooltips">
<div class="container">
    <?= form_open('', 'role="form" id="form" target="_top" class="form-horizontal"') ?>
    <h3 class="text-capitalize"> <?= lang('product_attributes') ?></h3>
    <span><?= lang('search_for_product_attributes') ?></span>
    <hr/>
    <select multiple id="product_attributes" class="form-control select2" name="product_attributes[]">
        <?php if (!empty($attributes)): ?>
            <?php foreach ($attributes as $v): ?>
                <option value="<?= $v['attribute_id'] ?>" selected><?= $v['attribute_name'] ?></option>
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
        $("#product_attributes").select2({
            ajax: {
                url: '<?=admin_url(TBL_PRODUCTS_ATTRIBUTES. '/search/ajax/')?>',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        attribute_name: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, page) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                id: item.attribute_id,
                                text: item.attribute_name
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
<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <title><?= lang('site_builder') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
	<?= link_tag(SITE_BUILDER . '/assets/minimalist-blocks/content.css') ?>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

	<?= link_tag('themes/css/font-awesome/css/font-awesome.min.css') ?>
    <?= link_tag(SITE_BUILDER . '/box/box.css') ?>

	<?= link_tag(SITE_BUILDER . '/assets/scripts/simplelightbox/simplelightbox.css') ?>

    <!-- Required css for editing (not needed in production) -->
	<?= link_tag(SITE_BUILDER . '/contentbuilder/contentbuilder.css') ?>
    <?= link_tag(SITE_BUILDER . '/contentbox/contentbox.css') ?>
    <style>
        .save_now {
            width: 20%;
            height: 50px;
            font-size: 13px;
            font-family: Arial, Baskerville, monospace;
            position: fixed;

            padding: 0;
            box-sizing: border-box;
            text-align: center;
            white-space: nowrap;
            z-index: 10001;
        }

        #topCms {
            top: 5px;
            left: 53px;
        }
        #panelCms {
            bottom: 0;
            right: 51px;
        }

        .module_text {
            text-align: center;
            font-size: 40px;
            margin-top: 1em;
        }

    </style>
	<?php if (config_enabled('sts_admin_enable_admin_login_timer')): ?>
        <script>
            var ctime;
            function timer() {
                ctime=window.setTimeout("redirect()",<?=DEFAULT_ADMIN_SESSION_TIMER?>);
            }
            function redirect() {
                window.location = "<?=admin_url('logout?timer_expired=1&redirect=' . urlencode(uri_string()))?>";
            }
            function detime() {
                window.clearTimeout(ctime);
                timer();
            }
        </script>
	<?php endif; ?>
</head>
<body id="contentarea" class="tooltips" <?php if (config_enabled('sts_admin_enable_admin_login_timer')): ?>onload="timer()"
      onmousemove="detime()" <?php endif; ?>>
<div id="topCms" class="save_now">
    <div id="response"></div>
    <?php if ($this->input->get('full_screen')): ?>
    <a href="<?= admin_url('site_pages/update/' . $id) ?>" class="btn btn-danger" style="color: white" target="_top"><?=i('fa fa-home')?> <?=lang('home')?></a>
    <?php else: ?>
        <a href="<?= base_url(SITE_BUILDER . '/' . $id . '?full_screen=1') ?>" target="_top" class="btn btn-primary"><?= i('fa fa-refresh') ?>
            <span class="hidden-xs"><?= lang('full_screen') ?></span></a>
    <?php endif; ?>
    <a href="<?= site_url('page/' . $row['url'] . '?preview=1') ?>" class="btn btn-success" target="_blank" style="color: white"><?=i('fa fa-search')?> <?=lang('preview')?></a>
    <button id="update-button" onclick="save('1')" class="btn btn-info" style="color: white"><?=i('fa fa-refresh')?> <span><?=lang('save')?></span></button>
</div>

<div class="is-wrapper">
	<?= $row['page_content'] ?>
</div>

<div id="panelCms" class="save_now">
    <div id="response"></div>

    <a href="<?= site_url('page/' . $row['url'] . '?preview=1') ?>" class="btn btn-success" target="_blank" style="color: white"><?=i('fa fa-search')?> <?=lang('preview')?></a>
    <button id="update-button" onclick="save('1')" class="btn btn-info" style="color: white"><?=i('fa fa-refresh')?> <span><?=lang('save')?></span></button>
</div>

<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
<input id="content_data" name="page_content" type="hidden"/>
<input id="main_css" name="main_css" type="hidden"/>
<input id="section_css" name="section_css" type="hidden"/>

<?php if (!empty($id)): ?>
    <input name="page_id" value="<?= $id ?>" type="hidden"/>
<?php endif; ?>
<?= form_close() ?>

<!-- Required js for production -->
<script src="<?=base_folder_path(SITE_BUILDER )?>/contentbuilder/jquery.min.js" type="text/javascript"></script>
<script src="<?=base_folder_path(SITE_BUILDER)?>/assets/scripts/simplelightbox/simple-lightbox.min.js" type="text/javascript"></script>

<!-- Required js for editing (not needed in production) -->
<script src="<?=base_folder_path(SITE_BUILDER)?>/contentbuilder/contentbuilder.min.js" type="text/javascript"></script>
<script src="<?=base_folder_path(SITE_BUILDER)?>/contentbox/contentbox.min.js" type="text/javascript"></script>


<script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js" type="text/javascript"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" type="text/javascript"></script>
<script src="<?=base_folder_path(SITE_BUILDER)?>/assets/minimalist-blocks/content.js" type="text/javascript"></script>

<script type="text/javascript">

    jQuery(document).ready(function ($) {

        //Enable editing
        $(".is-wrapper").contentbox({
            framework: 'bootstrap', /* use Bootstrap framework */
            customTags: [["<?=lang('affiliate_first_name')?>", "{{affiliate_data.fname}}"],
                ["<?=lang('affiliate_last_name')?>", "{{affiliate_data.lname}}"],
                ["<?=lang('affiliate_email')?>", "{{affiliate_data.primary_email}}"],],
            contentStylePath: '<?=base_folder_path(SITE_BUILDER . '/assets/styles')?>/',
            coverImageHandler: '<?=admin_url(SITE_BUILDER . '/save_cover')?>',
            largerImageHandler: '<?=admin_url(SITE_BUILDER . '/save_cover')?>',
            moduleConfig: [{
                "moduleSaveImageHandler": "<?=admin_url(SITE_BUILDER . '/save_image_module')?>" /* for module purpose image saving (ex. slider) */
            }],
            onRender: function () {
                //Add lightbox script (This is optional. If used, lightbox js & css must be included)
                $('a.is-lightbox').simpleLightbox({ closeText: '<i style="font-size:35px" class="icon ion-ios-close-empty"></i>', navText: ['<i class="icon ion-ios-arrow-left"></i>', '<i class="icon ion-ios-arrow-right"></i>'], disableScroll: false });
            },
            <?php if (config_enabled('layout_design_enable_sb_auto_save')): ?>
            onChange: function () {
                //Auto Save
                var timeoutId;
                clearTimeout(timeoutId);
                timeoutId = setTimeout(function () {
                    save('2');
                }, 1000);
            },
            <?php endif; ?>
            useSidebar: true,
            enableContentStyle: false /* (1) Applicable only if useSidebar is set true.
                                        (2) If enableContentStyle is set true, styles must also be saved. Use .mainCss() & sectionCss() to get the styles. See save() function below. */

        });

        $('a.is-lightbox').simpleLightbox({ closeText: '<i style="font-size:35px" class="icon ion-ios-close-empty"></i>', navText: ['<i class="icon ion-ios-arrow-left"></i>', '<i class="icon ion-ios-arrow-right"></i>'], disableScroll: false });

    });

    function save(num) {
        $('#update-button i ').addClass('fa-spin');
        $('#update-button span').html('<?=lang('saving')?>');
        //Save Images
        $("body").saveimages({
            handler: '<?=admin_url( SITE_BUILDER . '/save_images')?>',
            onComplete: function () {
                //Get Content
                var sContent = $('.is-wrapper').data('contentbox').html();
                var sMainCss = $('.is-wrapper').data('contentbox').mainCss();
                var sSectionCss = $('.is-wrapper').data('contentbox').sectionCss();

                $('#content_data').val(sContent);
                $('#main_css').val(sMainCss);
                $('#section_css').val(sSectionCss);

                $.ajax({
                    url: '<?=current_url()?>',
                    type: 'POST',
                    dataType: 'json',
                    data: $('#form').serialize(),
                    beforeSend: function () {
                        $('#update-button i ').addClass('fa-spin');
                        $('#update-button span').html('<?=lang('saving')?>');
                    },
                    complete: function () {
                        $('#update-button i ').removeClass('fa-spin');
                        $('#update-button span').html('<?=lang('save_page')?>');
                    },
                    success: function (response) {
                        if (response.type == 'success') {
                            if (num == '1') {
                                alert('<?=lang('system_updated_successfully')?>');
                            }
                        }
                        else {
                            alert('<?=lang('could_not_save_data')?>');
                        }

                        $('#msg-details').html(response.msg);
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
					    <?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    }
                });

            }
        });
        $("body").data('saveimages').save();

    }

    (function (jQuery) {

        jQuery.saveimages = function (element, options) {

            var defaults = {
                handler: '<?=admin_url( SITE_BUILDER . '/save_images')?>',
                onComplete: function () { },
                customval: ''
            };

            this.settings = {};

            var $element = jQuery(element),
                element = element;

            this.init = function () {

                this.settings = jQuery.extend({}, defaults, options);

            };

            this.save = function (s) {

                var handler = this.settings.handler;
                var customval = this.settings.customval;

                //Get quality info (from content builder plugin)
                var hiquality = false;
                /*try {
				hiquality = $element.data('contentbuilder').settings.hiquality;
				} catch (e) { };*/

                var count = 0;

                //Check all images
                $element.find('img').not('#divCb img').each(function () {

                    //Find base64 images

                    var attr = $(this).attr('src');
                    if (typeof attr !== typeof undefined && attr !== false) {

                        if (jQuery(this).attr('src').indexOf('base64') != -1) {

                            count++;

                            //Read image (base64 string)
                            var image = jQuery(this).attr('src');
                            image = image.replace(/^data:image\/(png|jpeg);base64,/, "");

                            //Prepare form to submit image
                            if (jQuery('#form-' + count).length == 0) {
                                var s = '<form id="form-' + count + '" target="frame-' + count + '" method="post" enctype="multipart/form-data">' +
                                    '<input id="hidimg-' + count + '" name="hidimg-' + count + '" type="hidden" />' +
                                    '<input id="hidname-' + count + '" name="hidname-' + count + '" type="hidden" />' +
                                    '<input id="hidtype-' + count + '" name="hidtype-' + count + '" type="hidden" />' +
                                    '<input id="csrftoken-' + count + '" name="<?=$csrf_token?>" value="<?=$csrf_value?>" type="hidden" />' +
                                    '<input id="hidcustomval-' + count + '" name="hidcustomval-' + count + '" type="hidden" />' +
                                    '<iframe id="frame-' + count + '" name="frame-' + count + '" style="width:1px;height:1px;border:none;visibility:hidden;position:absolute"></iframe>' +
                                    '</form>';
                                jQuery('body').append(s);
                            }

                            //Give ID to image
                            jQuery(this).attr('id', 'img-' + count);

                            //Set hidden field with image (base64 string) to be submitted
                            jQuery('#hidimg-' + count).val(image);

                            //Set hidden field with custom value to be submitted
                            jQuery('#hidcustomval-' + count).val(customval);

                            //Set hidden field with file name to be submitted
                            var filename = '';
                            if (jQuery(this).data('filename') != undefined) {
                                filename = jQuery(this).data('filename'); //get filename data from the imagemebed plugin
                            }
                            var filename_without_ext = filename.substr(0, filename.lastIndexOf('.')) || filename;
                            filename_without_ext = filename_without_ext.toLowerCase().replace(/ /g, '-');
                            jQuery('#hidname-' + count).val(filename_without_ext);

                            //Set hidden field with file extension to be submitted
                            if (hiquality) {
                                //If high quality is set true, set image as png
                                jQuery('#hidtype-' + count).val('png'); //high quality
                            } else {
                                //If high quality is set false, depend on image extension
                                var extension = filename.substr((filename.lastIndexOf('.') + 1));
                                extension = extension.toLowerCase();
                                if (extension == 'jpg' || extension == 'jpeg') {
                                    jQuery('#hidtype-' + count).val('jpg');
                                } else {
                                    jQuery('#hidtype-' + count).val('png');
                                }
                            }

                            //Submit form
                            //jQuery('#form-' + count).attr('action', handler + '?count=' + count);
                            jQuery('#form-' + count).attr('action', handler + (handler.indexOf('?') >= 0 ? '&' : '?') + 'count=' + count);
                            jQuery('#form-' + count).submit();

                            //Note: the submitted image will be saved on the server
                            //by saveimage.php (if using PHP) or saveimage.ashx (if using .NET)
                            //and the image src will be changed with the new saved image.
                        }

                    }
                });

                //Check per 2 sec if all images have been changed with the new saved images.
                var int = setInterval(function () {

                    var finished = true;
                    $element.find('img').not('#divCb img').each(function () {

                        var attr = $(this).attr('src');
                        if (typeof attr !== typeof undefined && attr !== false) {

                            if (jQuery(this).attr('src').indexOf('base64') != -1) { //if there is still base64 image, means not yet finished.
                                finished = false;
                            }
                        }
                    });

                    if (finished) {

                        $element.data('saveimages').settings.onComplete();

                        window.clearInterval(int);

                        //remove unused forms (previously used for submitting images)
                        for (var i = 1; i <= count; i++) {
                            jQuery('#form-' + i).remove();
                        }
                    }
                }, 2000);

            };

            this.init();

        };

        jQuery.fn.saveimages = function (options) {

            return this.each(function () {

                if (undefined == jQuery(this).data('saveimages')) {
                    var plugin = new jQuery.saveimages(this, options);
                    jQuery(this).data('saveimages', plugin);

                }

            });
        };
    })(jQuery);
</script>

<!-- Required js for production -->
<script src="<?=base_folder_path(SITE_BUILDER)?>/box/box.js" type="text/javascript"></script>

</body>
</html>


<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 * Copyright (c) 2007-2020, JROX Technologies, Inc.
 *
 * This script may be only used and modified in accordance to the license
 * agreement attached (license.txt) except where expressly noted within
 * commented areas of the code body. This copyright notice and the
 * comments above and below must remain intact at all times.  By using this
 * code you agree to indemnify JROX Technologies, Inc, its corporate agents
 * and affiliates from any liability that might arise from its use.
 *
 * Selling the code for this program without prior written consent is
 * expressly forbidden and in violation of Domestic and International
 * copyright laws.
 *
 * @package      eCommerce Suite
 * @author       JROX Technologies, Inc.
 * @copyright    Copyright (c) 2007 - 2020, JROX Technologies, Inc. (https://www.jrox.com/)
 * @link         https://www.jrox.com
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * @param string $cmd
 * @param string $module
 * @param bool $admin
 * @return string|void
 */
function html_editor($cmd = '', $module = '', $admin = TRUE)
{
	if (!config_enabled('sts_admin_enable_wysiwyg_content')) return;

	if ($cmd == 'head')
	{
		$link = $admin == TRUE ? base_url() . 'themes/admin/default/third' : base_url('js');
		return '<script src="' . $link . '/tinymce/tinymce.' . TINYMCE_COMPRESSOR . '.js"></script>';
	}

	switch ($module)
	{
		case 'products':

			echo "tinymce.init({
        selector: '.editor',
         setup: function (editor) {
	        editor.on('change', function () {
	            tinymce.triggerSave();
	        });
        },
        height : 400,
        relative_urls: false,
         content_css : '" . base_url('themes/css/bootstrap/css/bootstrap.css') . "?' + new Date().getTime(),
         content_style: 'body {padding: 10px}',
        plugins: [
            'advlist autolink lists link image charmap print preview anchor',
            'searchreplace visualblocks code responsivefilemanager',
            'insertdatetime media table contextmenu paste'
        ],
        toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
        image_advtab: true ,

   external_filemanager_path: '" . base_url() . "/filemanager/',
   filemanager_title:'Image Manager' ,
   filemanager_access_key:'" . sha1(config_option('file_manager_access_token')) . "' ,
   external_plugins: { 'filemanager' : '" . base_url() . "/filemanager/plugin.min.js'}
    });";

			break;

		case 'blog':
			echo "tinymce.init({
        selector: '.editor',
         setup: function (editor) {
	        editor.on('change', function () {
	            tinymce.triggerSave();
	        });
        },
        height : 400,
        relative_urls: false,
        content_css : '" . base_url('themes/css/bootstrap/css/bootstrap.css') . "?' + new Date().getTime(),
        content_style: 'body {padding: 10px}',
        plugins: [
            'advlist autolink lists link image charmap print preview anchor',
            'searchreplace visualblocks code responsivefilemanager',
            'wordcount',
            'pagebreak',
            'codesample',
            'insertdatetime media table contextmenu paste'
        ],
         codesample_languages: [
			{text: 'HTML/XML', value: 'markup'},
			{text: 'JavaScript', value: 'javascript'},
			{text: 'CSS', value: 'css'},
			{text: 'PHP', value: 'php'},
			{text: 'Ruby', value: 'ruby'},
			{text: 'Python', value: 'python'},
			{text: 'Java', value: 'java'},
			{text: 'C', value: 'c'},
			{text: 'C#', value: 'csharp'},
			{text: 'C++', value: 'cpp'}
		],
        toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
        image_advtab: true ,
        pagebreak_separator: '{{page_break}}', 

   external_filemanager_path: '" . base_url('filemanager') . "/',
   filemanager_title:'Image Manager' ,
   filemanager_access_key:'" . sha1(config_option('file_manager_access_token')) . "' ,
   external_plugins: { 'filemanager' : '" . base_url() . "/filemanager/plugin.min.js'}
    });";
			break;

		case 'html':
			echo "tinymce.init({
        selector: '.editor',
        setup: function (editor) {
	        editor.on('change', function () {
	            tinymce.triggerSave();
	        });
        },
        height : 400,
        relative_urls: true,
        content_css : '" . base_url('themes/css/bootstrap/css/bootstrap.css') . "?' + new Date().getTime(),
        content_style: 'body {padding: 10px}',
        plugins: [
            'advlist autolink lists link image charmap print preview anchor',
            'searchreplace visualblocks code responsivefilemanager',
            'wordcount',
            'codesample',
            'insertdatetime media table contextmenu paste'
        ],
         codesample_languages: [
			{text: 'HTML/XML', value: 'markup'},
			{text: 'JavaScript', value: 'javascript'},
			{text: 'CSS', value: 'css'},
			{text: 'PHP', value: 'php'},
			{text: 'Ruby', value: 'ruby'},
			{text: 'Python', value: 'python'},
			{text: 'Java', value: 'java'},
			{text: 'C', value: 'c'},
			{text: 'C#', value: 'csharp'},
			{text: 'C++', value: 'cpp'}
		],
        toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
        image_advtab: true ,

   external_filemanager_path: '" . base_url('filemanager') . "/',
   filemanager_title:'Image Manager' ,
   filemanager_access_key:'" . sha1(config_option('file_manager_access_token')) . "' ,
   external_plugins: { 'filemanager' : '" . base_url() . "/filemanager/plugin.min.js'}
    });";
			break;

		case 'basic':

			echo "tinymce.init({
        selector: '.editor',
         setup: function (editor) {
	        editor.on('change', function () {
	            tinymce.triggerSave();
	        });
        },
        height : 200,
        relative_urls: false,
        plugins: [
            'advlist autolink lists link image charmap print preview anchor',
            'searchreplace visualblocks code',
            'insertdatetime media table contextmenu paste'
        ],
        toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image'
    });";

			break;

		case 'bbcode':

			echo "tinymce.init({
        selector: '.editor',
         setup: function (editor) {
	        editor.on('change', function () {
	            tinymce.triggerSave();
	        });
        },
        height : 200,
        relative_urls: false,
         plugins: 'bbcode code',
         bbcode_dialect: 'punbb',
        toolbar: 'undo redo | bold italic underline | code'
    });";

			break;

		case 'text_only':

			echo "tinymce.init({
        selector: '.editor',
         setup: function (editor) {
	        editor.on('change', function () {
	            tinymce.triggerSave();
	        });
        },
        height : 200,
        relative_urls: false,
        plugins: [
            'advlist autolink lists link charmap print preview anchor',
            'searchreplace code',
            'contextmenu paste'
        ],
        toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link'
    });";

			break;

		case 'public_text':

			echo "tinymce.init({
        selector: '.editor',
         setup: function (editor) {
	        editor.on('change', function () {
	            tinymce.triggerSave();
	        });
        },
        height : 200,
        relative_urls: false,
        //content_css : '" . base_url('themes/css/bootstrap/css/bootstrap.css') . "?' + new Date().getTime(),
        content_style: 'body {padding: 10px}',
        plugins: [
            'advlist autolink lists link charmap print preview anchor',
            'searchreplace code',
            'insertdatetime contextmenu paste'
        ],
        toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link'
    });";
			break;
	}
}

/* End of file html_editor_helper.php */
/* Location: ./application/helpers/html_editor_helper.php */
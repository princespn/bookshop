<?php
/**
 * Module Name: Aweber Mailing List
 * Description: Aweber Mailing List Module
 */

defined('BASEPATH') OR exit('No direct script access allowed');

$config['module_alias'] = 'aweber';
$config['module_name'] = 'Aweber Mailing List';
$config['module_description'] = 'Aweber Mailing List Module';
$config['module_external_url'] = 'https://my.jrox.com/link.php?id=22';
$config['module_models'] = array('Aweber_model' => $config['module_alias']);
$config['module_template'] = 'tpl_admin_mailing_list_manage'; //specify a custom template to use here
$config['module_add_user'] = 'add_user';
$config['module_remove_user'] = 'remove_user';
$config['module_mailing_list_table'] = 'module_mailing_list_aweber_lists';
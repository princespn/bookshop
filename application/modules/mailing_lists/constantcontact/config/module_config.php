<?php
/**
 * Module Name: Constant Contact Module
 * Description: Constant Contact Mailing List Module
 */

defined('BASEPATH') OR exit('No direct script access allowed');

$config['module_alias'] = 'Constant Contact';
$config['module_name'] = 'Constant Contact Mailing List';
$config['module_description'] = 'Constant Contact Mailing List Module';
$config['module_external_url'] = 'https://my.jrox.com/link.php?id=37';
$config['module_models'] = array('constantcontact_model' => $config['module_alias']);
$config['module_template'] = 'tpl_admin_mailing_list_manage'; //specify a custom template to use here
$config['module_add_user'] = 'add_user';
$config['module_remove_user'] = 'remove_user';
$config['module_subscriber_status'] = 'subscribed';
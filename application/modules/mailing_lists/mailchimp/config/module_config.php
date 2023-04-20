<?php
/**
 * Module Name: Mailchimp Module
 * Description: Mailchimp Mailing List Module
 */

defined('BASEPATH') OR exit('No direct script access allowed');

$config['module_alias'] = 'mailchimp';
$config['module_name'] = 'Mailchimp Mailing List';
$config['module_description'] = 'Mailchimp Mailing List Module';
$config['module_external_url'] = 'https://my.jrox.com/link.php?id=21';
$config['module_models'] = array('Mailchimp_model' => $config['module_alias']);
$config['module_template'] = 'tpl_admin_mailing_list_manage'; //specify a custom template to use here
$config['module_add_user'] = 'add_user';
$config['module_remove_user'] = 'remove_user';
$config['module_api_url'] = 'api.mailchimp.com/3.0';
$config['module_subscriber_status'] = 'subscribed';
$config['module_mailing_list_table'] = 'module_mailing_list_mailchimp_lists';
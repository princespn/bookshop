<?php
/**
 * Module Name: Import Members
 * Description: Mass Import Members data into the Database
 */

defined('BASEPATH') OR exit('No direct script access allowed');

$config['module_alias'] = 'members';
$config['module_name'] = 'Import Members';
$config['module_description'] = 'Mass Import Members data into the Database';
$config['module_models'] = array('Import_members_model' => $config['module_alias'],
                                 'Members_model' => 'mem');
$config['module_config_template'] = 'tpl_admin_data_import_manage'; //template for uploading the file
$config['module_map_fields_template'] = 'tpl_admin_data_import_map_fields'; //template for mapping fields
$config['module_import_tables'] = array(TBL_MEMBERS,
                                        TBL_MEMBERS_ADDRESSES,
                                        TBL_MEMBERS_PASSWORDS,
                                        TBL_MEMBERS_PROFILES,
                                        TBL_MEMBERS_AFFILIATE_GROUPS,
                                        TBL_MEMBERS_BLOG_GROUPS,
                                        TBL_MEMBERS_DISCOUNT_GROUPS,
                                        TBL_MEMBERS_EMAIL_MAILING_LIST,
                                        TBL_MEMBERS_PERMISSIONS,
                                        TBL_MEMBERS_SPONSORS,
                                        TBL_MEMBERS_ALERTS,);
$config['module_exclude_keys'] = array(TBL_MEMBERS . '.alert_id',
                                       TBL_MEMBERS . '.email_confirmed',
                                       TBL_MEMBERS . '.id',
                                       TBL_MEMBERS . '.last_login_date',
                                       TBL_MEMBERS . '.last_login_ip',
                                       TBL_MEMBERS . '.date',
                                       TBL_MEMBERS . '.updated_by',
                                       TBL_MEMBERS . '.updated_on',
                                       TBL_MEMBERS_ADDRESSES . '.id',
                                       TBL_MEMBERS_ADDRESSES . '.member_id',
                                       TBL_MEMBERS_AFFILIATE_GROUPS . '.id',
                                       TBL_MEMBERS_AFFILIATE_GROUPS . '.member_id',
                                       TBL_MEMBERS_ALERTS . '.alert_id',
                                       TBL_MEMBERS_ALERTS . '.member_id',
                                       TBL_MEMBERS_BLOG_GROUPS . '.id',
                                       TBL_MEMBERS_BLOG_GROUPS . '.member_id',
                                       TBL_MEMBERS_DISCOUNT_GROUPS . '.id',
                                       TBL_MEMBERS_DISCOUNT_GROUPS . '.member_id',
                                       TBL_MEMBERS_EMAIL_MAILING_LIST . '.id',
                                       TBL_MEMBERS_EMAIL_MAILING_LIST . '.member_id',
                                       TBL_MEMBERS_EMAIL_MAILING_LIST . '.sequence_id',
                                       TBL_MEMBERS_EMAIL_MAILING_LIST . '.email_address',
                                       TBL_MEMBERS_EMAIL_MAILING_LIST . '.send_date',
                                       TBL_MEMBERS_EMAIL_MAILING_LIST . '.sub_data',
                                       TBL_MEMBERS_PASSWORDS . '.confirmation_id',
                                       TBL_MEMBERS_PASSWORDS . '.date_modified',
                                       TBL_MEMBERS_PASSWORDS . '.id',
                                       TBL_MEMBERS_PASSWORDS . '.member_id',
                                       TBL_MEMBERS_PERMISSIONS . '.id',
                                       TBL_MEMBERS_PERMISSIONS . '.member_id',
                                       TBL_MEMBERS_PROFILES . '.member_id',
                                       TBL_MEMBERS_PROFILES . '.profile_id',
                                       TBL_MEMBERS_SPONSORS . '.id',
                                       TBL_MEMBERS_SPONSORS . '.member_id',
                                       TBL_MEMBERS_SPONSORS . '.original_sponsor_id',
);
$config['module_import_memory_limit'] = '256M';
$config['module_import_time_limit'] = '360';
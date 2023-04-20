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
 * @package	eCommerce Suite
 * @author	JROX Technologies, Inc.
 * @copyright	Copyright (c) 2007 - 2019, JROX Technologies, Inc. (https://www.jrox.com/)
 * @link	https://www.jrox.com
 * @filesource
 */

/*
|--------------------------------------------------------------------------
| Cron timer settings
|--------------------------------------------------------------------------
|
| These are setting the timers for the cron job
|
*/

$config['timer_archive_reports'] = 60 * 60 * 24; // 1 day
$config['timer_auto_approve_commissions'] = 60 * 60 * 24; // 1 day
$config['timer_auto_close_support_tickets'] = 60 * 60 * 12; // 12 hours
$config['timer_backup_database'] = 60 * 60 * 24; //60 seconds * 60 minutes * 24 hours = 1 day
$config['timer_backup_files'] =  60 * 60 * 24 * 7; // 7 days
$config['timer_cancel_expired_subscriptions'] = 60 * 60 * 2; // 6 hours
$config['timer_cancel_unpaid_invoices'] = 60 * 60 * 24; // 6 hours
$config['timer_generate_invoices'] = 60 * 60 * 24; // 1 day
$config['timer_generate_payments'] = 60 * 60 * 24; // 1 day
$config['timer_generate_rewards'] = 60 * 60 * 24; // 1 day
$config['timer_optimize_tables'] =  60 * 60 * 24; // 1 day
$config['timer_prune_affiliate_traffic'] = 60 * 60 * 24 * 7; // 7 days
$config['timer_prune_email_archive'] = 60 * 60 * 12; // 12 hours
$config['timer_prune_transaction_log'] = 60 * 60 * 24; // 1 day
$config['timer_schedule_follow_ups'] = 60 * 15; //15 minutes
$config['timer_send_email'] = 60 * 5; //5 minutes
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

//enable database cache
$config['enable_database_cache'] = FALSE; //this option must be set to TRUE before the others below will work.

//cache home page data
$config['cache_site_data'] = FALSE;
$config['site_cache_limit'] = 600; //seconds

//cache global settings
$config['cache_settings_data'] = FALSE;
$config['settings_cache_limit'] = 3600; //seconds

//product_filters
$config['cache_product_filters_data'] = FALSE;
$config['product_filters_cache_limit'] = 3600 * 24 * 1; //1 hour * 24 hours * 1 days; //seconds

//cache public page db queries
$config['cache_public_db_query_data'] = FALSE;
$config['public_db_query_cache_limit'] = 3600 * 1 * 1; //1 hour * 1 hour * 1 day; //1 hour

//cache downline view queries
$config['cache_downline_db_query_data'] = FALSE;
$config['downline_db_query_cache_limit'] = 3600 * 24 * 1; //1 hour * 24 hours * 1 day; //seconds

//cache admin db view queries
$config['cache_admin_data'] = FALSE;
$config['admin_cache_limit'] = 300; //seconds

//cache admin db queries
$config['cache_db_query_data'] = FALSE;
$config['db_query_cache_limit'] = 3600 * 24 * 3; //1 hour * 24 hours * 3 days; //seconds

/* End of file version.php */
/* Location: ./application/config/version.php */
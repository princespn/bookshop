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

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param string $reply_id
 * @param bool $array
 * @return mixed|string
 */
function list_attachments($data = array(), $reply_id = '', $array = FALSE)
{
    $html = '';
    $files = unserialize($data);

    //return array only
    if ($array == TRUE)
    {
        return $files;
    }

    foreach ($files as $f)
    {
        $html .= '<span class="tag">';
        $html .=  ' ' . anchor(uri(1) . '/' . uri(2) . '/download/' . $f['file_name'] . '/' . $reply_id, i('fa fa-download') . ' ' . $f['file_name'], 'class="name"');
        $html .= '</span><br />';
    }

    return $html;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param string $template
 * @return mixed|string
 */
function merge_predefined_fields($data = array(), $template = '')
{
    //add supporting variables

    $data[ 'admin_login_url' ] = admin_login_url();
    $data[ 'login_url' ] = site_url('login');
    $data[ 'site_url' ] = site_url();
    $data[ 'site_name' ] = config_option('sts_site_name');
    $data[ 'charset' ] = config_option('sts_email_charset');

    //set date
    $data[ 'current_date' ] = date(config_option('format_date2'), get_time());
    $data[ 'current_time' ] = date(config_option('default_time_format'), get_time());

    //replace template strings
    foreach ($data as $key => $value)
    {
        if (!is_array($value))
        {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }
    }

    return $template;
}

// ------------------------------------------------------------------------

/**
 * @param array $files
 * @return bool|string
 */
function save_attachments($files = array())
{
    //get file names from the files array for storage in the ticket
    $f = array();

    if (is_array($files))
    {
        foreach ($files as $v)
        {
            array_push($f,$v['file_data']);
        }
    }

    return empty($f) ? FALSE : serialize($f);
}

// ------------------------------------------------------------------------

/**
 * @param bool $sub
 * @return string
 */
function get_title($sub = false)
{
    $CI = & get_instance();

    if ($sub == true)
    {
        return !$CI->input->get('ticket_status') ? '' : '<small class="visible-lg label label-' . $CI->input->get('ticket_status') . '">' .lang($CI->input->get('ticket_status')) . '</small>';
    }

    return $CI->input->get('closed') == 0 ? 'open_support_tickets' : 'closed_support_tickets';
}

// ------------------------------------------------------------------------

/**
 * @param string $id
 * @param int $lang_id
 * @param string $col
 * @return bool
 */
function get_support_category($id = '', $lang_id = 1, $col = 'category_name')
{
	$CI = & get_instance();

	$CI->db->where('language_id', $lang_id);

	if (!$q = $CI->db->where('category_id', valid_id($id))->get(TBL_SUPPORT_CATEGORIES_NAME))
	{
		get_error(__FILE__, __METHOD__, __LINE__);
	}

	if ($q->num_rows() > 0)
	{
		$row = $q->row_array();

		return $row[$col];
	}

	return FALSE;
}


/* End of file support_helper.php */
/* Location: ./application/helpers/support_helper.php */
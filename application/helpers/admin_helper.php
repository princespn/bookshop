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
 * @package	    eCommerce Suite
 * @author	    JROX Technologies, Inc.
 * @copyright	Copyright (c) 2007 - 2019, JROX Technologies, Inc. (https://www.jrox.com/)
 * @link	    https://www.jrox.com
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * @param string $sel
 * @return array
 */
function get_admin_permissions($sel = '')
{
    $CI =& get_instance();

    $CI->load->helper('directory');

    $files = directory_map('./application/controllers/' . ADMIN_ROUTE);
    asort($files);

    $options = array();
    foreach ($files as $f)
    {
        $f = strtolower(str_replace('.php','', $f));

        switch ($f)
        {
            case 'dashboard':
            case 'login':
            case 'logout':
            case 'migrate':
	        case 'status':
	        case 'email_send':
            case 'update_session':
            case 'update_status':
	        case 'error_pages':

                //continue;

                break;

            default:

                $options['view_' . $f] = $f . '/view';
                $options['create_' . $f] = $f . '/create';
                $options['update_' . $f] = $f . '/update';
                $options['delete_' . $f] = $f . '/delete';

                break;
        }


    }

    $view = array();
    $create = array();
    $update = array();
    $delete = array();

    foreach ($options as $k => $v)
    {
        if (preg_match('/create*/', $k))
        {
            $a = !empty($sel['create'][$v])? 'true' : '';
            $create[$k] = form_checkbox('permissions[create][' . $v . ']', 1, $a, 'class="create"');
        }
        elseif (preg_match('/update*/', $k))
        {
            $a = !empty($sel['update'][$v]) ? 'true' : '';
            $update[$k] = form_checkbox('permissions[update][' . $v . ']', 1, $a, 'class="update"');
        }
        elseif (preg_match('/delete*/', $k))
        {
            $a = !empty($sel['delete'][$v]) ? 'true' : '';
            $delete[$k] = form_checkbox('permissions[delete][' . $v . ']', 1, $a, 'class="delete"');
        }
        elseif (preg_match('/view*/', $k))
        {
            $a = !empty($sel['view'][$v]) ? 'true' : '';
            $view[$k] = form_checkbox('permissions[view][' . $v . ']', 1, $a, 'class="view"');
        }
    }

    $perms = array('view' => $view, 'create' => $create, 'update' => $update, 'delete' => $delete);

    return $perms;

}

// ------------------------------------------------------------------------

/**
 * @param string $str
 * @return mixed
 */
function generate_cc($str = '')
{
	return base64_decode($str);
}

// ------------------------------------------------------------------------

/**
 * @param string $status
 * @return bool|false|string
 */
function get_admins($status = 'active')
{
	$CI = &get_instance();

	$sql = 'SELECT *  
				FROM ' . $CI->db->dbprefix(TBL_ADMIN_USERS) . ' p
	            LEFT JOIN ' . $CI->db->dbprefix(TBL_ADMIN_ALERTS) . ' d 
	                ON p.admin_id = d.admin_id
	                WHERE p.status = \'' . valid_id($status, TRUE) . '\'';

	//run the query
	if (!$q = $CI->db->query($sql))
	{
		get_error(__FILE__, __METHOD__, __LINE__);
	}

	return $q->num_rows() > 0 ? sc($q->result_array()) : FALSE;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return bool
 */
function check_link_permissions($data = array())
{
	if ($_SESSION['admin']['admin_group_id'] == 1)
	{
		return TRUE;
	}

	foreach ($data as $v)
	{
		if (isset($_SESSION['admin']['permissions'][$v . '/view']))
		{
			return TRUE;
		}
	}

	return FALSE;
}

/* End of file admin_helper.php */
/* Location: ./application/helpers/admin_helper.php */
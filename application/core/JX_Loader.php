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

class JX_Loader extends CI_Loader {

    public function page($tpl = '', $data = array(), $c = 'admin', $header = TRUE, $footer = TRUE, $meta = TRUE, $return = FALSE, $charset = TRUE)
    {
        $CI =& get_instance();

        //set the charset
	    if ($charset == TRUE)
	    {
		    $CI->output->set_content_type(TPL_ADMIN_CONTENT_TYPE, $CI->config->item('charset'));
	    }

        if ($meta == TRUE)
        {
            $this->view($c .'/' . 'system/' . TPL_ADMIN_HEADER_META, $data);
        }

        if ($header == TRUE)
        {
            $this->view($c .'/' . 'system/' . TPL_ADMIN_HEADER, $data);
        }

        //get the page
        if ($return == TRUE)
        {
           return $this->view($c . '/' . $tpl, $data, TRUE);
        }
        else
        {
            $this->view($c . '/' . $tpl, $data);
        }

        //finish with the footer
        if ($footer == true)
        {
            $this->view($c .'/' . 'system/' . TPL_ADMIN_FOOTER, $data);

        }
        if ($meta == TRUE)
        {
            $this->view($c .'/' . 'system/' . TPL_ADMIN_FOOTER_META, $data);
        }

    }
}

/* End of file JX_Loader.php */
/* Location: ./application/core/JX_Loader.php */
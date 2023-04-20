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
 * @package    eCommerce Suite
 * @author    JROX Technologies, Inc.
 * @copyright    Copyright (c) 2007 - 2020, JROX Technologies, Inc. (https://www.jrox.com/)
 * @link    https://www.jrox.com
 * @filesource
 */
class Faq extends Public_Controller
{
    protected $data;

    public function __construct()
    {
        parent::__construct();

        $this->load->model(__CLASS__ .'_model', 'faq');

	    $this->data = $this->init->initialize('site');

        log_message('debug', __CLASS__ . ' Class Initialized');
    }

	// ------------------------------------------------------------------------

	public function view()
    {
        //get the faqs
        $rows = $this->faq->load_faqs(sess('default_lang_id'));

        //set the faqs array
        $this->data['faqs'] = empty($rows) ? FALSE : $rows;

        $tpl = $this->input->get('q') == 'ajax' ? '_ajax' : '';
        $this->show->display('support', 'faq' . $tpl, $this->data);
    }
}

/* End of file Faq.php */
/* Location: ./application/controllers/Faq.php */
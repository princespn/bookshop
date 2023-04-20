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
class Downloads extends Member_Controller
{
    protected $data;

    public function __construct()
    {
        parent::__construct();

        $models = array(
            'members' => 'mem',
            'products' => 'prod',
            'products_downloads' => 'dw'
        );

        foreach ($models as $k => $v)
        {
            $this->load->model($k . '_model', $v);
        }

        $this->load->helper('content');
	    $this->load->helper('download');

        $this->data = $this->init->initialize('site');
		
		log_message('debug', __CLASS__ . ' Class Initialized');
    }

    /**
     * index file
     */
    public function index()
    {
        redirect_page($this->uri->uri_string() . '/view');
    }

    /**
     * Downloads
     *
     * View downloadable files for the user
     */
    public function view()
    {

        //get the downloads paid for by this user
        $this->data['downloads'] = $this->dw->get_user_downloads(sess('member_id'), sess('default_lang_id'));

        $this->show->display(MEMBERS_ROUTE,  CONTROLLER_CLASS, $this->data);
    }

    /**
     * Get download
     *
     * Verify and get file to download
     */
    public function get()
    {

	    //check for valid id
	    $this->data['id'] = (int)uri(4);

	    $row = $this->dw->get_download_details($this->data['id'], TRUE);

	    if (!empty($row['success']))
	    {
		    if ($this->sec->verify_ownership($row['row']['member_id'], (int)sess('member_id')))
		    {
			    $this->dw->update_limits($this->data['id']);

			    download_file($row['row']['file_name'], 'downloads');
		    }
	    }
	    else
	    {
		    show_error(lang('download_expired'));
	    }
    }
}

/* End of file Downloads.php */
/* Location: ./application/controllers/members/Downloads.php */
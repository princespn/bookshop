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
class Traffic extends Public_Controller
{
    protected $data;

    public function __construct()
    {
        parent::__construct();

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
     * Traffic
     *
     * View referral traffic stats for the user
     */
    public function view()
    {
        $this->data['page_options'] = query_options($this->data);

        $this->data['traffic'] = $this->aff->get_traffic($this->data['page_options'], sess('member_id'));

        //check for pagination
        if (!empty($this->data['traffic']['total']))
        {
            $this->data['page_options'] = array(
                'uri' => $this->data['uri'],
                'total_rows' => $this->data['traffic']['total'],
                'per_page' => $this->data['session_per_page'],
                'segment' => $this->data['db_segment'],
                'next_link' => lang('next'),
                'prev_link' => lang('previous')
            );

            $this->data['paginate'] = $this->paginate->generate($this->data['page_options'], CONTROLLER_CLASS, MEMBERS_ROUTE, 'pagination-md');
        }

        $this->show->display(MEMBERS_ROUTE,  CONTROLLER_CLASS, $this->data);
    }
}

/* End of file Traffic.php */
/* Location: ./application/controllers/members/Traffic.php */
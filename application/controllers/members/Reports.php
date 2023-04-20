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
class Reports extends Member_Controller
{
    protected $data;

    public function __construct()
    {
        parent::__construct();

        $models = array(
            'reports' => 'report',
            'modules' => 'mod',
            'Events_calendar' => 'events'
        );

        foreach ($models as $k => $v)
        {
            $this->load->model($k . '_model', $v);
        }

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
     * Reports
     *
     * View available member reports
     */
    public function view()
    {

        //get the reports
        $this->data['reports'] = $this->mod->get_modules('member_reporting', TRUE);

        $this->show->display(MEMBERS_ROUTE,  CONTROLLER_CLASS, $this->data);
    }

    /**
     * Generate report
     *
     * Generate the report data
     */
    public function generate()
    {

        $this->data['id'] = (int)uri(4);

        $this->data['row'] = $this->mod->get_module_details($this->data['id'], TRUE, 'member_reporting');

        if (! $this->data['row'])
        {
            log_error('error', lang('no_record_found'));
        }

        //initialize the require files for the module
	    $this->init_module('member_reporting', $this->data['row']['module']['module_folder']);

        //get the reports
        $func = $this->config->item('module_generate_function');

        $this->data['report'] = $this->module->$func($this->data, sess('member_id'));

        //generate calendar if monthly
	    if (substr($this->data['report']['template'], 0,7) == 'monthly')
	    {
		    init_calendar('reports');
		    $this->data['calendar'] = $this->calendar->generate( current_date('Y'), current_date('m'),  format_cell_data($this->data['report']['rows']));

	    }

        $this->show->display(MEMBERS_ROUTE,  $this->data['report']['template'], $this->data);
    }
}

/* End of file Reports.php */
/* Location: ./application/controllers/members/Reports.php */
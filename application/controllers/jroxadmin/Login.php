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

class Login extends Admin_Session_Controller {

    protected $data;

    public function __construct()
    {
        parent::__construct();

        $this->load->model('login_model', 'login');

        $this->data = $this->init->initialize();

		log_message('debug', __CLASS__ . ' Class Initialized');

	    load_lang_files('admin');
    }

	// ------------------------------------------------------------------------

	public function index()
    {
        //check ip restriction
        $this->sec->check_admin_ip_restriction();

        //check ssl
        $this->sec->check_ssl('admin');

	   $this->lc->login_check();

        $url = ADMIN_LOGIN;
		$msg = '';

        if ($this->input->post())
        {
            //check if the form submitted is correct
            if ($this->login->validate_admin_login())
            {
                //run plugin
                $this->plugin->init_plugin(__METHOD__,$this->session->userdata);

                //log it!
                $msg = $this->session->admin['username'] . ' ' . lang('admin_logged_in_successfully');
	            $this->dbv->rec(array('method' => __METHOD__, 'msg' => $msg, 'level' => 'security'));

                $url = ADMIN_ROUTE . '/login/process';

	            if ($this->input->post('page_redirect'))
	            {
		            $url .= '?page_redirect=' . $this->input->post('page_redirect');
	            }

                $this->sec->auto_block_ip('remove', $this->input->ip_address());
            }
            else
            {
                $this->sec->auto_block_ip('block', $this->input->ip_address());

                //set language
	            $lang_id =  !$this->input->post('language') ? config_item('sts_admin_default_language') :  $this->input->post('language');

	            if ($admins = get_admins()) //get active admins to send alerts to.
	            {
		            foreach ($admins as $a)
		            {
			            if (!empty($a['alert_admin_failed_login']))
			            {
				            $this->mail->send_template(EMAIL_ADMIN_FAILED_LOGIN, format_admin_failed_login_email($this->input->post()), FALSE, $lang_id, $a['primary_email']);
			            }
		            }
	            }

	            //log it!
                $msg = validation_errors();
	            $this->dbv->rec(array('method' => __METHOD__, 'msg' => strip_tags($msg), 'vars' => $this->input->post(), 'level' => 'security'));

                redirect_flashdata($url, $msg, 'error');
            }
        }

        redirect_flashdata($url, $msg);
    }

	// ------------------------------------------------------------------------

	public function process()
    {
        //check ip restriction
        $this->sec->check_admin_ip_restriction();

        if ($this->session->success)
        {
            $this->login->update_login_data('admin', $this->session->admin['admin_id']);

	        set_file_manager_cookie();

            $this->data['homepage'] =  admin_url($this->session->admin['admin_home_page']);

	        if ($this->input->get('page_redirect'))
	        {
		        $this->data['homepage'] = site_url($this->input->get('page_redirect'));
	        }

            $this->load->page('system/' . TPL_ADMIN_PROCESS_LOGIN, $this->data, 'admin', false, false);
        }
        else
        {
            redirect_page();
        }
    }
}

/* End of file Login.php */
/* Location: ./application/controllers/admin/Login.php */
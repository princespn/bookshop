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
class Admin_login extends Admin_Session_Controller
{
    protected $data = array();

    public function __construct()
    {
        parent::__construct();

        $this->load->model('login_model', 'login');
	    $this->load->model('languages_model', 'language');

	    $this->data = $this->init->initialize();

        log_message('debug', __CLASS__ . ' Class Initialized');

	    load_lang_files('admin');
    }

	// ------------------------------------------------------------------------

	public function index()
    {
        $this->sec->check_admin_folder();

        $this->sec->check_installer();

        $this->sec->check_admin_ip_restriction();

        $this->sec->check_ssl('admin');

        $this->data[ 'page_redirect' ] = $this->input->get('page_redirect', TRUE);

        $this->data[ 'languages' ] = get_languages();

        $this->load->page('system/' . TPL_ADMIN_LOGIN, $this->data, 'admin', FALSE, FALSE, FALSE);
    }

	// ------------------------------------------------------------------------

	public function reset_password()
    {
	    $this->sec->check_admin_folder();

        $this->sec->check_ssl('admin');

        if ($this->input->post())
        {
            $row = $this->login->validate_pass_reset($this->input->post(NULL, TRUE), TBL_ADMIN_USERS);

            if (!empty($row[ 'success' ]))
            {
                //send out the email
                $this->mail->send_template(EMAIL_ADMIN_RESET_PASSWORD, $row[ 'data' ], FALSE, sess('default_lang_id'));
            }
            else
            {
                $this->sec->auto_block_ip('block', $this->input->ip_address());
            }

	        //redirect the URL
	        redirect_flashdata(current_url(), 'reset_password_sent');
        }

        $this->load->page('system/' . TPL_ADMIN_RESET_PASS, $this->data, 'admin', FALSE, FALSE, FALSE);
    }

	// ------------------------------------------------------------------------

	public function confirm()
    {
        $this->sec->check_ssl('admin');

        $this->data[ 'code' ] = $this->uri->segment(3);

        try //try the confirm code first or send an error
        {
            $this->login->check_reset_confirmation($this->data[ 'code' ]);
        }
        catch (Exception $e)
        {
            log_error('error', $e->getMessage());
        }

        if ($this->input->post())
        {
            $row = $this->login->validate_reset_confirm($this->input->post(NULL, TRUE), TBL_ADMIN_USERS);

            if (!empty($row[ 'success' ]))
            {
	            //log it!
	            $this->dbv->rec(array('method' => __METHOD__, 'msg' => $row['msg_text']));

                //set the session flash and redirect the page
                redirect_flashdata(ADMIN_LOGIN, $row[ 'msg_text' ]);
            }
            else
            {
                $this->data[ 'error' ] = validation_errors();
            }
        }

        $this->load->page('/system/' . TPL_ADMIN_RESET_PASS_CONFIRM, $this->data, 'admin', FALSE, FALSE, FALSE);
    }
}

/* End of file Admin_login.php */
/* Location: ./application/controllers/Admin_login.php */
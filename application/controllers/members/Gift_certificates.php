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
class Gift_certificates extends Public_Controller
{
    protected $data;

    public function __construct()
    {
        parent::__construct();

        $models = array(
            'gift_certificates' => 'gift'
        );

        foreach ($models as $k => $v)
        {
            $this->load->model($k . '_model', $v);
        }

        $this->load->helper('content');

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

	// ------------------------------------------------------------------------

	public function view()
    {

        //get the downloads paid for by this user
        $this->data['certificates'] = $this->gift->get_user_certificates(sess('member_id'));

        $this->show->display(MEMBERS_ROUTE,  CONTROLLER_CLASS, $this->data);
    }

	// ------------------------------------------------------------------------

	public function details()
	{

		$this->data['id'] = valid_id(uri(4));

		$this->data['p'] = $this->gift->get_details($this->data['id'], 'cert_id', TRUE);

		//get the details
		if (!$this->data['p'])
			log_error('error', lang('no_record_found'));

		//send and update the gift certificates....
		if ($this->input->post())
		{
			//update cert id
			$data = format_gift_certificate_template($this->data['p'], $this->input->post());
			$row = $this->dbv->update(TBL_ORDERS_GIFT_CERTIFICATES, 'cert_id', $data);

			if (!empty($row['success']))
			{
				$this->mail->send_template(EMAIL_MEMBER_GIFT_CERTIFICATE_DETAILS, $data, TRUE, sess('default_lang_id'), $this->input->post('to_email'));
			}

			//set the default response
			$response = array( 'type'     => 'success',
			                   'msg'      => lang('email_sent_successfully'),
			);

			ajax_response($response);

		}

		$this->show->display(MEMBERS_ROUTE,  'gift_certificate_details', $this->data);
	}

}

/* End of file Gift_certificates.php */
/* Location: ./application/controllers/members/Gift_certificates.php */
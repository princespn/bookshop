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
class Language extends Public_Controller
{
	protected $data;

	public function __construct()
	{
		parent::__construct();

		$this->load->model('languages_model', 'email');
		$this->load->model('email_mailing_lists_model', 'list');

		$this->data = $this->init->initialize('site');

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		$this->data['language'] = valid_id(uri(3), TRUE);

		$row = $this->dbv->get_record(TBL_LANGUAGES, 'name', $this->data['language']);

		$this->session->set_userdata('default_language', $row['name']);
		$this->session->set_userdata('default_lang_id', $row['language_id']);
		$this->session->set_userdata('default_lang_code', $row['code']);
		$this->session->set_userdata('default_lang_image', $row['image']);

		redirect_flashdata($this->agent->referrer());
	}
}

/* End of file Language.php */
/* Location: ./application/controllers/Language.php */
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
class Support_predefined_replies_model extends CI_Model
{
	protected $id = 'reply_id';

	// ------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();

	}

	// ------------------------------------------------------------------------

	public function validate($data = array())
	{
		$this->form_validation->set_data($data);

		//get the list of fields required for this
		$required = $this->config->item('support_predefined_replies', 'required_input_fields');

		//now get the list of fields directly from the table
		$fields = $this->db->list_fields(TBL_SUPPORT_PREDEFINED_REPLIES);

		//go through each field and
		foreach ($fields as $f)
		{
			//set the default rule
			$rule = 'trim';

			//if this field is a required field, let's set that
			if (in_array($f, $required))
			{
				$rule .= '|required';
			}

			switch ($f)
			{
				case 'reply_content':

					$rule .= config_enabled('allow_programming_codes_in_text') ? '' : '|xss_clean';

					break;

				default:

					$rule .= '|strip_tags|xss_clean';

					break;
			}



			$this->form_validation->set_rules($f, 'lang:' . $f, $rule);
		}

		if ($this->form_validation->run())
		{
			$row = array( 'success' => TRUE,
			              'data'    => $this->dbv->validated($data, FALSE)
			);
		}
		else
		{
			$row = array( 'error'    => TRUE,
			              'msg_text' => validation_errors()
			);
		}

		return empty($row) ? FALSE : sc($row);
	}
}

/* End of file Support_predefined_replies_model.php */
/* Location: ./application/models/Support_predefined_replies_model.php */
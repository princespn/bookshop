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
class Api_model extends CI_Model
{
	/**
	 * Api_model constructor.
	 */
	public function __construct()
	{
		//initialize the data and verify api access
		$this->initialize();
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool
	 */
	private function initialize($data = array())
	{
		if ($this->input->post_get('api_key') && $this->input->post_get('api_token'))
		{
			$row =  $this->verify($this->input->post_get('api_key'), $this->input->post_get('api_token'));

			if (!empty($row['success'])) return TRUE;
		}

		$this->set_status_code(500, lang('access_token_invalid'));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $key
	 * @param string $token
	 * @return array|bool
	 */
	private function verify($key = '', $token = '')
	{
		if (config_item('sts_site_api_key') == $key && config_item('sts_site_api_token') == $token)
		{
			//check for restricted IPs
			if (config_item('sts_site_api_restrict_ips'))
			{
				$ip = explode("\n", config_item('sts_site_api_restrict_ips'));

				if (!empty($ip))
				{
					foreach ($ip as $i)
					{
						if ($this->input->ip_address() &&$this->input->ip_address() == $i)
						{
							return array('success' => TRUE);
						}
					}

					return FALSE;
				}
			}

			return array('success' => TRUE);
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $msg
	 * @return array
	 */
	public function error($msg = '')
	{
		$error = empty($msg) ? 'invalid_api_access'  : $msg;

		$this->set_status_code(500);

		return array('error' => TRUE,
		             'msg_text' => lang($error));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $msg
	 */
	public function show($data = array(), $msg = 'no_data_found')
	{
		if (empty($data['success']))
		{
			$msg = !empty($data['msg_text']) ? $data['msg_text'] : $msg;

			$data = $this->error($msg);
		}

		//return any data if it is available
		$this->output
			->set_content_type('application/json')
			->set_output( sc($data, 'json'));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $code
	 * @param string $msg
	 * @param string $html
	 */
	protected function set_status_code($code = '200', $msg = '', $html = '1')
	{
		$this->output->set_status_header($code, $msg);

		if ($code == 500 && !empty($msg))
		{
			//show the error message
			//echo heading($msg, $html);
			echo json_encode(array('error' => TRUE,
			                  'msg_text' => lang($msg)));
			exit;
		}
	}


}
/* End of file Api_model.php */
/* Location: ./application/models/Api_model.php */


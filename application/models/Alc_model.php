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
/* EDITING THIS FILE CONSTITUTES A VIOLATION OF THE END USER LICENSE AGREEMENT */


/**
 * Class Alc_model
 */
class Alc_model extends CI_Model
{
	/*
	 * If you got here, looks like you may know a little bit of code.... editing
	 * or sharing this code violates the license agreement, so you know....
	 */

	// ------------------------------------------------------------------------

	/**
	 * @var mixed|string
	 */
	protected $server = '';

	// ------------------------------------------------------------------------

	/**
	 * @var mixed|string
	 */
	protected $secret_key = '';

	// ------------------------------------------------------------------------

	/**
	 * Alc_model constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->secret_key = config_item('lc_key');
		$this->server = config_item('lc_server');
	}

	// ------------------------------------------------------------------------

	/**
	 * @return bool|string
	 */
	public function reset($force = FALSE)
	{
		$c = $force == TRUE ? NUMBER_LC_CHECK + 1 : config_item('sts_system_remote_lc_check');

		if ($c > NUMBER_LC_CHECK)
		{
			if ($this->set->update_db_settings(array('sts_site_key'               => '',
			                                         'sts_mx_license_data'        => '',
			                                         'sts_local_key'              => '',
			                                         'sts_feature_data'           => '',
			                                         'sts_copyright_license_data' => '',
			                                         'sts_site_license_data'      => ''))
			)
			{
				$row = array('success'  => TRUE,
				             'msg_text' => lang('system_updated_successfully'));
			}

			$v = 0;
		}
		else
		{
			$v = config_item('sts_system_remote_lc_check') + 1;
		}

		//update license checks
		$this->set->update_db_settings(array('sts_system_remote_lc_check' => $v));

		return !empty($row) ? sc($row) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|string
	 */
	public function validate($data = array())
	{
		$type = substr($data['sts_site_key'], 0, 3) == DEFAULT_ST_LICENSE_PREFIX ? 'standard' : 'matrix';

		$check = $this->check_licensing($data['sts_site_key'], $type);

		if (!empty($check['status']) && $check['status'] == 'Active')
		{
			//check for leased license
			if (!empty($check['billingcycle']))
			{
				$data['sts_local_key'] = $check['billingcycle'] == 'One Time' ? '' : $check['localkey'];
			}

			//set license details
			$data['sts_site_license_data'] = serialize(array('status'        => $check['status'],
			                                                 'product'       => $check['productname'],
			                                                 'cycle'         => $check['billingcycle'],
			                                                 'due_date'      => $check['nextduedate'],
			                                                 'valid_domains' => $check['validdomain'],
			                                                 'valid_ips'     => $check['validip'],
			));

			if (substr($data['sts_site_key'], 0, 3) == DEFAULT_MX_LICENSE_PREFIX)
			{
				$data = array('sts_mx_license_data' => serialize(array('license_key'   => $data['sts_site_key'],
				                                                       'status'        => $check['status'],
				                                                       'product'       => $check['productname'],
				                                                       'cycle'         => $check['billingcycle'],
				                                                       'due_date'      => $check['nextduedate'],
				                                                       'valid_domains' => $check['validdomain'],
				                                                       'valid_ips'     => $check['validip'])));
			}
			elseif (substr($data['sts_site_key'], 0, 3) == DEFAULT_CP_LICENSE_PREFIX)
			{
				$data = array('sts_copyright_license_data' => serialize(array('license_key'   => $data['sts_site_key'],
				                                                              'status'        => $check['status'],
				                                                              'product'       => $check['productname'],
				                                                              'cycle'         => $check['billingcycle'],
				                                                              'due_date'      => $check['nextduedate'],
				                                                              'valid_domains' => $check['validdomain'],
				                                                              'valid_ips'     => $check['validip'])));
			}
			else
			{
				$data['sts_feature_data'] = $this->generate_features();
			}

			$row = array('success' => TRUE,
			             'license' => !empty($check) ? $check : '',
			             'data'    => $data);
		}
		else
		{
			$row = array('error'    => TRUE,
			             'msg_text' => lang('license_key') . ' - ' . lang($check['status']));
		}

		return !empty($row) ? sc($row) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * check license key on login
	 */
	public function login_check()
	{
		$a = rand(1, 100);

		if (!defined('DISABLE_LC'))
		{
			if ($a < PERCENT_LC && !empty($this->config->item('sts_local_key')))
			{
				$row = $this->validate(array('sts_site_key' => $this->config->item('sts_site_key')));

				if (empty($row['success']))
				{
					$this->reset();
				}
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @return bool
	 */
	public function check_network()
	{
		if (file_exists(APPPATH . '/config/network.php'))
		{
			$this->load->config('network');
		}
		else
		{
			if (config_item('sts_site_key') && config_item('sts_site_license_data'))
			{
				if (config_item('sts_feature_data'))
				{
					$network = unserialize(base64_decode(config_item('sts_feature_data')));

					foreach ($network as $k => $v)
					{
						$this->config->set_item($k, $v);
					}
				}
			}
		}

		if (config_item('sts_mx_license_data'))
		{
			$this->config->set_item('layout_enable_forced_matrix', TRUE);
		}

		if (config_item('sts_copyright_license_data'))
		{
			$this->config->set_item('poweredby', '');
			$this->config->set_item('custom_admin_logo', TRUE);
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $str
	 * @return string
	 */
	public function check_aff($str = '1')
	{
		if (config_item('unlimited_affiliates') == FALSE)
		{
			if ($this->aff->count_affiliates() >= config_item('total_affiliates'))
			{
				return '0';
			}
		}

		return $str;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $type
	 */
	public function check($type = '')
	{
		$type = strtolower($type);

		switch ($type)
		{
			case 'affiliate_groups':
			case 'discount_groups':

				$a = 'enable_multi_' . $type;

				break;

			default:

				$a = 'enable_section_' . $type;

				break;
		}

		if (!config_enabled($a))
		{
			redirect(admin_url('error_pages/license/link'));
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $licensekey
	 * @param string $type
	 * @return array|mixed
	 */
	protected function check_licensing($licensekey = '', $type = 'standard')
	{
		// -----------------------------------
		//  -- Configuration Values --
		// -----------------------------------

		$localkey = $type == 'standard' ? $this->config->item('sts_local_key') : '';

		// The number of days to wait between performing remote license checks
		$localkeydays = $this->config->item('localkeydays');
		// The number of days to allow failover for after local key expiry
		$allowcheckfaildays = $this->config->item('allowcheckfaildays');

		// -----------------------------------
		//  -- Do not edit below this line --
		// -----------------------------------

		$check_token = time() . md5(mt_rand(1000000000, 9999999999) . $licensekey);
		$checkdate = date("Ymd");
		$originalcheckdate = '';
		$domain = $this->config->item('base_domain');
		$usersip = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : $_SERVER['LOCAL_ADDR'];
		$dirpath = dirname(__FILE__);
		$verifyfilepath = 'modules/servers/licensing/verify.php';
		$localkeyvalid = FALSE;
		if (!empty($localkey))
		{
			$localkey = str_replace("\n", '', $localkey); # Remove the line breaks
			$localdata = substr($localkey, 0, strlen($localkey) - 32); # Extract License Data
			$md5hash = substr($localkey, strlen($localkey) - 32); # Extract MD5 Hash
			if ($md5hash == md5($localdata . $this->secret_key))
			{
				$localdata = strrev($localdata); # Reverse the string
				$md5hash = substr($localdata, 0, 32); # Extract MD5 Hash
				$localdata = substr($localdata, 32); # Extract License Data
				$localdata = base64_decode($localdata);
				$localkeyresults = unserialize($localdata);

				$originalcheckdate = $localkeyresults['checkdate'];
				if ($md5hash == md5($originalcheckdate . $this->secret_key))
				{
					$localexpiry = date("Ymd", mktime(0, 0, 0, date("m"), date("d") - $localkeydays, date("Y")));
					if ($originalcheckdate > $localexpiry)
					{
						$localkeyvalid = TRUE;
						$results = $localkeyresults;
						$validdomains = explode(',', $results['validdomain']);
						if (!in_array($domain, $validdomains))
						{
							$localkeyvalid = FALSE;
							$localkeyresults['status'] = "Invalid";
							$results = array();
						}
						/*
						$validips = explode(',', $results['validip']);
						if (!in_array($usersip, $validips))
						{
							$localkeyvalid = FALSE;
							$localkeyresults['status'] = "Invalid";
							$results = array();
						}

						$validdirs = explode(',', $results['validdirectory']);
						if (!in_array($dirpath, $validdirs))
						{
							$localkeyvalid = FALSE;
							$localkeyresults['status'] = "Invalid";
							$results = array();
						}
						*/
					}
				}
			}
		}
		if (!$localkeyvalid)
		{
			$responseCode = 0;
			$postfields = array(
				'licensekey' => $licensekey,
				'domain'     => $domain,
				'ip'         => $usersip,
				'dir'        => $dirpath,
			);
			if ($check_token)
			{
				$postfields['check_token'] = $check_token;
			}
			$query_string = '';
			foreach ($postfields AS $k => $v)
			{
				$query_string .= $k . '=' . urlencode($v) . '&';
			}
			if (function_exists('curl_exec'))
			{
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $this->server . $verifyfilepath);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
				curl_setopt($ch, CURLOPT_TIMEOUT, 30);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$data = curl_exec($ch);
				$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				curl_close($ch);
			}
			else
			{
				$responseCodePattern = '/^HTTP\/\d+\.\d+\s+(\d+)/';
				$fp = @fsockopen($this->server, 80, $errno, $errstr, 5);
				if ($fp)
				{
					$newlinefeed = "\r\n";
					$header = "POST " . $this->server . $verifyfilepath . " HTTP/1.0" . $newlinefeed;
					$header .= "Host: " . $this->server . $newlinefeed;
					$header .= "Content-type: application/x-www-form-urlencoded" . $newlinefeed;
					$header .= "Content-length: " . @strlen($query_string) . $newlinefeed;
					$header .= "Connection: close" . $newlinefeed . $newlinefeed;
					$header .= $query_string;
					$data = $line = '';
					@stream_set_timeout($fp, 20);
					@fputs($fp, $header);
					$status = @socket_get_status($fp);
					while (!@feof($fp) && $status)
					{
						$line = @fgets($fp, 1024);
						$patternMatches = array();
						if (!$responseCode
							&& preg_match($responseCodePattern, trim($line), $patternMatches)
						)
						{
							$responseCode = (empty($patternMatches[1])) ? 0 : $patternMatches[1];
						}
						$data .= $line;
						$status = @socket_get_status($fp);
					}
					@fclose($fp);
				}
			}
			if ($responseCode != 200)
			{
				$localexpiry = date("Ymd", mktime(0, 0, 0, date("m"), date("d") - ($localkeydays + $allowcheckfaildays), date("Y")));
				if ($originalcheckdate > $localexpiry)
				{
					$results = $localkeyresults;
				}
				else
				{
					$results = array();
					$results['status'] = "Invalid";
					$results['description'] = "Remote Check Failed";

					return $results;
				}
			}
			else
			{
				preg_match_all('/<(.*?)>([^<]+)<\/\1>/i', $data, $matches);
				$results = array();
				foreach ($matches[1] AS $k => $v)
				{
					$results[$v] = $matches[2][$k];
				}
			}
			if (!is_array($results))
			{
				return array('error'    => TRUE,
				             'msg_text' => 'Invalid License Server Response');
			}
			if (!empty($results['md5hash']))
			{
				if ($results['md5hash'] != md5($this->secret_key . $check_token))
				{
					$results['status'] = "Invalid";
					$results['description'] = "MD5 Checksum Verification Failed";
					$results['error'] = TRUE;

					return $results;
				}
			}
			if ($results['status'] == "Active")
			{
				$results['checkdate'] = $checkdate;
				$data_encoded = serialize($results);
				$data_encoded = base64_encode($data_encoded);
				$data_encoded = md5($checkdate . $this->secret_key) . $data_encoded;
				$data_encoded = strrev($data_encoded);
				$data_encoded = $data_encoded . md5($data_encoded . $this->secret_key);
				$data_encoded = wordwrap($data_encoded, 80, "\n", TRUE);
				$results['localkey'] = $data_encoded;
			}
			$results['remotecheck'] = TRUE;
		}
		else
		{
			$results['localkey'] = $localkey;

		}

		unset($postfields, $data, $matches, $this->server, $this->secret_key, $checkdate, $usersip, $localkeydays, $allowcheckfaildays, $md5hash);

		return $results;
	}

	// ------------------------------------------------------------------------

	/**
	 * @return string
	 */
	protected function generate_features()
	{
		$features['is_licensed'] = TRUE;
		$features['max_commission_levels'] = 10;
		$features['enable_section_gift_certificates'] = TRUE;
		$features['enable_section_subscriptions'] = TRUE;
		$features['enable_section_affiliate_commission_rules'] = TRUE;
		$features['enable_section_promotional_rules'] = TRUE;
		$features['enable_section_kb_articles'] = TRUE;
		$features['enable_section_widgets'] = TRUE;
		$features['enable_section_site_builder'] = TRUE;
		$features['enable_section_forum_topics'] = TRUE;
		$features['enable_section_rewards'] = TRUE;
		$features['enable_section_email_follow_ups'] = TRUE;
		$features['enable_multi_affiliate_groups'] = TRUE;
		$features['enable_multi_discount_groups'] = TRUE;
		$features['unlimited_affiliates'] = TRUE;
		$features['product_types'] = array('general',
		                                   'certificate',
		                                   //'subscription', //todo
		                                   'third_party',
		);

		return base64_encode(serialize($features));
	}
}

//show_alc($abc);

/* End of file Alc_model.php */
/* Location: ./application/models/Alc_model.php */
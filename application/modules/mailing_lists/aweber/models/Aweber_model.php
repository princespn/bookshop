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
class Aweber_model extends Modules_model
{
	public function __construct()
	{
		parent::__construct();

		require_once(APPPATH . 'modules/mailing_lists/aweber/libraries/aweber_api/aweber_api.php');
	}

	public function install($id = '')
	{

		$config = array(
			'settings_key'        => 'module_mailing_lists_aweber_app_id',
			'settings_value'      => '',
			'settings_module'     => 'mailing_lists',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '1',
			'settings_function'   => '',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_mailing_lists_aweber_list_id',
			'settings_value'      => '',
			'settings_module'     => 'mailing_lists',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '2',
			'settings_function'   => '',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_mailing_lists_aweber_authorization_code',
			'settings_value'      => '',
			'settings_module'     => 'mailing_lists',
			'settings_type'       => 'textarea',
			'settings_group'      => $id,
			'settings_sort_order' => '3',
			'settings_function'   => '',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_mailing_lists_aweber_consumer_key',
			'settings_value'      => '',
			'settings_module'     => 'mailing_lists',
			'settings_type'       => 'password',
			'settings_group'      => $id,
			'settings_sort_order' => '4',
			'settings_function'   => '',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_mailing_lists_aweber_consumer_secret',
			'settings_value'      => '',
			'settings_module'     => 'mailing_lists',
			'settings_type'       => 'password',
			'settings_group'      => $id,
			'settings_sort_order' => '5',
			'settings_function'   => '',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_mailing_lists_aweber_access_key',
			'settings_value'      => '',
			'settings_module'     => 'mailing_lists',
			'settings_type'       => 'password',
			'settings_group'      => $id,
			'settings_sort_order' => '6',
			'settings_function'   => '',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_mailing_lists_aweber_access_secret',
			'settings_value'      => '',
			'settings_module'     => 'mailing_lists',
			'settings_type'       => 'password',
			'settings_group'      => $id,
			'settings_sort_order' => '7',
			'settings_function'   => '',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return array(
			'success'  => TRUE,
			'msg_text' => lang('module_installed_successfully'),
		);
	}

	public function uninstall($id = '')
	{
		//remove settings from database
		$this->mod->remove_config($id, 'mailing_lists');

		//update sts_email_mailing_list_module
		if (config_option('sts_email_mailing_list_module') == $id)
		{
			$this->set->update_db_settings(array('sts_email_mailing_list_module' => 'internal'));
		}

		return array(
			'success'  => TRUE,
			'msg_text' => lang('module_uninstalled_successfully'),
		);
	}

	public function add_user($list = '', $email = '', $data = array())
	{
		$this->consumerKey = config_item('module_mailing_lists_aweber_consumer_key');
		$this->consumerSecret = config_item('module_mailing_lists_aweber_consumer_secret');
		$this->accessToken = config_item('module_mailing_lists_aweber_access_key');
		$this->accessSecret = config_item('module_mailing_lists_aweber_access_secret');

		try
		{
			$this->application = new AWeberAPI($this->consumerKey, $this->consumerSecret);
			$this->account = $this->application->getAccount($this->accessToken, $this->accessSecret);

			$this->list = $this->findList(config_item('module_mailing_lists_aweber_list_id'));

			$this->findSubscriber($email);

			$list = $this->findList(config_item('module_mailing_lists_aweber_list_id'));

			$subscriber = array(
				'email' => $email,
				'name'  => is_var($data, 'fname') . ' ' . is_var($data, 'lname'),
			);

			$this->addSubscriber($subscriber, $list);

		} catch (Exception $e)
		{
			show_error($e->getMessage());
		}

		return empty($row) ? FALSE : $row;
	}

	function findList($listName)
	{
		try
		{
			$foundLists = $this->account->lists->find(array('name' => $listName));

			//must pass an associative array to the find method

			return $foundLists[0];
		} catch (Exception $e)
		{
			$this->dbv->rec(array('method' => __METHOD__, 'msg' => $e->getMessage(), 'level' => 'error'));
		}
	}

	function findSubscriber($email)
	{
		try
		{
			$foundSubscribers = $this->account->findSubscribers(array('email' => $email));

			//must pass an associative array to the find method

			return $foundSubscribers[0];
		} catch (Exception $e)
		{
			$this->dbv->rec(array('method' => __METHOD__, 'msg' => $e->getMessage(), 'level' => 'error'));
		}
	}

	function addSubscriber($subscriber, $list)
	{
		try
		{
			$listUrl = "/accounts/{$this->account->id}/lists/{$list->id}";
			$list = $this->account->loadFromUrl($listUrl);

			$newSubscriber = $list->subscribers->create($subscriber);

			return TRUE;
		} catch (Exception $e)
		{
			$this->dbv->rec(array('method' => __METHOD__, 'msg' => $e->getMessage(), 'level' => 'error'));
		}
	}

	public function remove_user($list = '', $email = '', $data = array())
	{
		return TRUE; //todo
	}

	public function update_module($data = array())
	{
		if (!empty($data['module_mailing_lists_aweber_authorization_code']) && empty($data['module_mailing_lists_aweber_access_key']))
		{
			try
			{
				$credentials = AWeberAPI::getDataFromAweberID($data['module_mailing_lists_aweber_authorization_code']);

				if (!empty($credentials))
				{
					list($consumerKey, $consumerSecret, $accessKey, $accessSecret) = $credentials;

					$data['module_mailing_lists_aweber_access_key'] = $accessKey;
					$data['module_mailing_lists_aweber_access_secret'] = $accessSecret;
					$data['module_mailing_lists_aweber_consumer_key'] = $consumerKey;
					$data['module_mailing_lists_aweber_consumer_secret'] = $consumerSecret;
				}
			} catch (Exception $e)
			{
				$this->dbv->rec(array('method' => __METHOD__, 'msg' => $e->getMessage(), 'level' => 'error'));
			}
		}

		//update module data
		$this->mod->update($data);

		$row = array('success'  => TRUE,
		             'data'     => $data,
		             'msg_text' => lang('system_updated_successfully'),
		);

		return empty($row) ? FALSE : sc($row);
	}

	public function get_module_options()
	{
		return TRUE;
	}

	public function validate_module($data = array())
	{
		$error = '';

		$this->form_validation->set_data($data);

		$row = $this->mod->get_module_details(valid_id($data['module_id']));

		//validate the module configuration settings...
		if (!empty($row['values']))
		{
			foreach ($row['values'] as $v)
			{
				$rule = 'trim|xss_clean';

				$lang = format_settings_label($v['key'], $row['module']['module_type'], $row['module']['module_folder']);

				switch ($v['type'])
				{
					case 'text':

						if ($v['function'] != 'none' && $v['function'] != '')
						{
							$rule .= '|' . trim($v['function']);
						}

						break;

					case 'dropdown':

						$options = array();
						foreach (options($v['function']) as $a => $b)
						{
							array_push($options, $a);
						}

						$rule .= '|in_list[' . implode(',', $options) . ']';

						break;
				}

				$this->form_validation->set_rules($v['key'], 'lang:' . $lang, $rule);
			}
		}

		if (!$this->form_validation->run())
		{
			$error .= validation_errors();
		}

		if (!empty($error))
		{
			//sorry! got some errors here....
			$row = array('error'    => TRUE,
			             'msg_text' => $error,
			);
		}
		else
		{
			//cool! no errors...
			$row = array('success' => TRUE,
			             'data'    => $this->dbv->validated($data, FALSE),
			);
		}

		return $row;
	}
}
/* End of file Aweber_model.php */
/* Location: ./modules/mailing_lists/aweber/models/Aweber_model.php */
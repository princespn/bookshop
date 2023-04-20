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

use Ctct\ConstantContact;
use Ctct\Components\Contacts\Contact;
use Ctct\Components\Contacts\ContactList;
use Ctct\Components\Contacts\EmailAddress;
use Ctct\Exceptions\CtctException;

class Constantcontact_model extends Modules_model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function install($id = '')
	{
		$config = array(
			'settings_key'        => 'module_mailing_lists_constantcontact_api_url',
			'settings_value'      => 'http://api2.constant_contact.com',
			'settings_module'     => 'text',
			'settings_type'       => 'password',
			'settings_group'      => $id,
			'settings_sort_order' => '1',
			'settings_function'   => '',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_mailing_lists_constantcontact_api_key',
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
			'settings_key'        => 'module_mailing_lists_constantcontact_access_token',
			'settings_value'      => '',
			'settings_module'     => 'mailing_lists',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '3',
			'settings_function'   => '',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_mailing_lists_constantcontact_list_id',
			'settings_value'      => '',
			'settings_module'     => 'mailing_lists',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '4',
			'settings_function'   => '',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_mailing_lists_constantcontact_lists',
			'settings_value'      => '',
			'settings_module'     => 'mailing_lists',
			'settings_type'       => 'hidden',
			'settings_group'      => $id,
			'settings_sort_order' => '5',
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
		$cc = new ConstantContact(config_option('module_mailing_lists_constantcontact_api_key'));

		try {
			$response = $cc->contactService->getContacts(config_option('module_mailing_lists_constantcontact_access_token'), array("email" => $email));

			if (empty($response->results)) {
				$action = "Creating Contact";
				$contact = new Contact();
				$contact->addEmail($email);
				$contact->addList(config_option('module_mailing_lists_constantcontact_list_id'));
				$contact->first_name = is_var($data, 'fname');
				$contact->last_name = is_var($data,'lname');

				$returnContact = $cc->contactService->addContact(config_option('module_mailing_lists_constantcontact_access_token'), $contact);

				$row = array('type' => 'success',
							'data' => $returnContact
					);
			}
			// catch any exceptions thrown during the process and print the errors to screen
		} catch (CtctException $ex) {
			$err = $ex->getErrors();

			$row = array('error'    => TRUE,
			             'msg_text' => $err[0]->error_message,
			);
		}

		return empty($row) ? FALSE : $row;
	}

	public function remove_user($list = '', $email = '', $data = array())
	{
		return empty($row) ? FALSE : $row;
	}

	public function update_module($data = array())
	{
		//update module data
		$this->mod->update($data);

		//update list ID
		$cc = new ConstantContact(config_item('module_mailing_lists_constantcontact_api_key'));

		try {
			$lists = $cc->listService->getLists(config_item('module_mailing_lists_constantcontact_access_token'));
		} catch (CtctException $ex) {
			foreach ($ex->getErrors() as $error) {

				$row = array('error'    => TRUE,
				             'msg_text' => $error,
				);
			}
		}

		if (!empty($lists))
		{
			$a = array();

			foreach ($lists as $list)
			{
				$a[$list->id] = $list->name;
			}

			$b = serialize($a);
			$this->set->update_db_settings(array('module_mailing_lists_constantcontact_lists' => $b));
		}

		$row = array('success'  => TRUE,
		             'data'     => $data,
		             'msg_text' => lang('system_updated_successfully'),
		);

		return empty($row) ? FALSE : sc($row);
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

						if (!empty($v['function']))
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
/* End of file Constantcontact_model.php */
/* Location: ./modules/mailing_lists/constantcontact/models/Constantcontact_model.php */
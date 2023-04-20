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
class Members extends Admin_Controller
{
	protected $table = TBL_MEMBERS;

	// ------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();

		$models = array(
			'members'             => 'mem',
			'affiliate_groups'    => 'aff_group',
			'discount_groups'     => 'disc_group',
			'blog_groups'         => 'blog_group',
			'email_mailing_lists' => 'lists',
			'regions'             => 'regions',
			'forms'               => 'form',
			'login'               => 'login',
		);

		foreach ($models as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

		$m = $this->input->get('is_affiliate') == 1 ? 'affiliates' : 'clients';

		$this->config->set_item('menu', $m);

		$this->data = $this->init->initialize();

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	// ------------------------------------------------------------------------

	public function index()
	{
		redirect_page($this->uri->uri_string() . '/view');
	}

	// ------------------------------------------------------------------------

	public function view()
	{
		//show users
		$this->data['page_options'] = query_options($this->data);

		//set query type
		$type = '';

		if ($this->input->get('type_id'))
		{
			$type = $this->input->get(array('table', 'type_id', 'group_id'), TRUE);
		}

		$this->data['rows'] = $this->mem->get_rows($this->data['page_options'], $type);

		//check for pagination
		if (!empty($this->data['rows']['total']))
		{
			$this->data['page_options'] = array(
				'uri'        => $this->data['uri'],
				'total_rows' => $this->data['rows']['total'],
				'per_page'   => $this->data['session_per_page'],
				'segment'    => $this->data['db_segment'],
			);

			$this->data['paginate'] = $this->paginate->generate($this->data['page_options'], CONTROLLER_CLASS, 'admin');
		}

		//run the page
		$tpl = init_member_template();
		$this->load->page('members/' . $tpl, $this->data);
	}

	// ------------------------------------------------------------------------

	public function create()
	{
		$row = $this->mem->init_new_member(generic_user(), uri(4));

		//generate rewards
		$this->rewards->add_reward_points($row['id'], 'reward_user_account_registration');

		if (!empty($row['success']))
		{
			$this->done(__METHOD__, $row);

			//set the session flash and redirect the page
			redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/update/' . $row['id'], $row[ 'msg_text' ]);
		}
		else
		{
			log_error(lang('could_not_create_record'));
		}
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		$this->data['id'] = (int)$this->uri->segment(4);

		//get form field data
		$this->data['fields'] = $this->form->init_form(2, sess('default_lang_id'), '');
		$this->data['custom_fields'] = $this->form->get_member_custom_fields($this->data['id'], sess('default_lang_id'));

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->mem->validate('update_admin', $this->input->post(), $this->data['fields']['values']);

			if (!empty($row['success']))
			{
				$row = $this->mem->update($row['data'], $this->data['custom_fields']);

				$this->done(__METHOD__, $row);

				//set the new affiliate URL
				$row['data']['affiliate_url'] = affiliate_url($row['data']['username']);

				//set the default response
				$response = array('type' => 'success',
				                  'msg'  => $row['msg_text'],
				                  'data' => $row['data']);
			}
			else
			{
				$response = array('type' => 'error',
				                  'msg'  => validation_errors(),
				);
			}

			ajax_response($response);
		}

		$this->data['row'] = $this->mem->get_details($this->data['id'], TRUE);

		$this->data['permission_fields'] = $this->dbv->get_fields(TBL_MEMBERS_PERMISSIONS);

		if (!$this->data['row'])
		{
			log_error('error', lang('no_record_found'));
		}

		//add editable data for inline editing
		$this->data['meta_data'] = link_tag('js/xeditable/bootstrap-editable.css');
		$this->data['meta_data'] .= '<script src="' . base_url('js/xeditable/bootstrap-editable.min.js') . '"></script>';

		$this->load->page('members/' . TPL_ADMIN_MEMBERS_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$id = valid_id(uri(4));

		$row = $this->mem->delete($id);

		if (!empty($row['success']))
		{
			$this->done(__METHOD__, $row);

			//set the session flash and redirect the page
			redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row['msg_text']);
		}
	}

	// ------------------------------------------------------------------------

	public function ajax_user()
	{
		$this->data['id'] = !$this->input->get() ? (int)uri(4) : $this->input->get('member_id');

		if (!$this->data['row'] =  $this->mem->get_details($this->data['id']))
		{
			show_error(lang('invalid_user'));
		}


		$this->load->page('orders/' . TPL_AJAX_ORDERS_CLIENT_PROFILE, $this->data, 'admin', FALSE, FALSE);
	}

	// ------------------------------------------------------------------------

	public function get_address()
	{
		$this->data['id'] = !$this->input->post() ? (int)$this->uri->segment(4) : $this->input->post('address_id');

		$this->data['row'] = $this->mem->get_member_address($this->data['id']);

		$this->load->page('orders/' . TPL_AJAX_ORDERS_ADDRESSES, $this->data, 'admin', FALSE, FALSE);
	}

	// ------------------------------------------------------------------------

	public function delete_address()
	{
		$row = $this->mem->delete_address((int)uri(4));

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/update/' . (int)uri(5), $row[ 'msg_text' ]);
	}

	// ------------------------------------------------------------------------

	public function add_address()
	{
		$this->data['id'] = (int)$this->uri->segment(4);

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->mem->validate_address(__FUNCTION__, $this->input->post(NULL, TRUE));

			if (!empty($row['success']))
			{
				$this->done(__METHOD__, $row);

				//set the session flash and redirect the page
				redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/update/' . $this->data['id'] . '#addresses', $row[ 'msg_text' ]);
			}
			else
			{
				//show errors on form
				$this->data['error'] = validation_errors();
			}
		}

		//fill in default values for input fields
		$this->data['row'] = set_default_address_data();

		$this->load->page('members/' . TPL_ADMIN_MEMBERS_ADDRESSES_MANAGE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function update_address()
	{
		$this->data['id'] = valid_id(uri(4));

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->mem->validate_address(__FUNCTION__, $this->input->post(NULL, TRUE));

			if (!empty($row['success']))
			{
				$this->done(__METHOD__, $row);

				//set the session flash and redirect the page
				redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/update_address/' . $this->data['id'], $row[ 'msg_text' ]);
			}
			else
			{
				//show errors on form
				$this->data['error'] = validation_errors();
			}
		}

		$this->data['row'] = $this->mem->get_member_address($this->data['id']);

		if (!$this->data['row'])
		{
			log_error('error', lang('no_record_found'));
		}

		$this->load->page('members/' . TPL_ADMIN_MEMBERS_ADDRESSES_MANAGE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function subscriptions()
	{
		$this->init->check_ajax_security();

		$this->data['id'] = valid_id(uri(4));

		$this->data['row'] = $this->mem->get_subscriptions($this->data['id'], sess('default_lang_id'));

		$this->load->page('members/' . TPL_AJAX_MEMBER_SUBSCRIPTIONS_VIEW, $this->data, 'admin', FALSE, FALSE);
	}

	// ------------------------------------------------------------------------

	public function reset_password()
	{
		$this->data['id'] = uri(4);

		//set default msg..
		$response = array('type' => 'error',
		                  'msg'  => lang('invalid_ajax_request'));

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->mem->reset_password($this->input->post());

			if (!empty($row['success']))
			{
				//send an email if needed
				if ($this->input->post('send_to_user'))
				{
					$vars = $this->mem->get_details($row['id']);

					$vars['password'] = $row['data'];

					//send template email
					$this->mail->send_template(EMAIL_MEMBER_LOGIN_DETAILS, $vars, FALSE, sess('default_lang_id'));
				}

				$this->done(__METHOD__, $row);

				//set the default response
				$response = array('type' => 'success',
				                  'msg'  => $row['msg_text'],
				);
			}
			else
			{
				$response['msg'] = lang('invalid_password');
			}
		}

		ajax_response($response);
	}

	/**
	 * Mass update records via checkboxes
	 */
	public function mass_update()
	{
		if ($this->input->post('id'))
		{
			switch ($this->input->post('change-status'))
			{
				case 'active':
				case 'inactive':
				case 'delete':
				case 'activate_affiliate':
				case 'deactivate_affiliate':

					$this->mem->mass_update($this->input->post('id'), $this->input->post('change-status'));

					break;

				case 'set_blog_group':

					$this->blog_group->mass_update($this->input->post('id'), $this->input->post('blog_group'));

					break;

				case 'set_discount_group':

					$this->disc_group->mass_update($this->input->post('id'), $this->input->post('discount_group'));

					break;

				case 'set_affiliate_group':

					$this->aff_group->mass_update($this->input->post('id'), $this->input->post('affiliate_group'));

					break;

				case 'add_mailing_list':
				case 'remove_mailing_list':

					$type = $this->input->post('change-status') == 'add_mailing_list' ? 'add' : 'remove';

					$this->lists->mass_update($type, $this->input->post('id'), $this->input->post('list_id'));

					break;
			}
		}

		$url = !$this->agent->referrer() ? ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view' : $this->agent->referrer();

		redirect_flashdata($url, 'system_updated_successfully');
	}

	// ------------------------------------------------------------------------

	public function login_member()
	{
		$this->data['id'] = valid_id(uri(4));

		if ($row = $this->mem->get_details($this->data['id']))
		{
			$url = site_url('login/admin/' . $this->data['id'] . '/' . generate_login_code($row) . '/' . uri(5));
		}

		redirect_flashdata($url);
	}

	// ------------------------------------------------------------------------

	public function send_login_details()
	{
		$this->data['id'] = valid_id(uri(4));

		if ($row = $this->mem->get_details($this->data['id']))
		{
			$vars = format_registration_email(EMAIL_MEMBER_LOGIN_DETAILS, $row);

			$vars['password'] = random_string('alnum', config_item('default_member_password_length'));

			$this->mem->update_password($vars);

			$this->mail->send_template(EMAIL_MEMBER_LOGIN_DETAILS, $vars, FALSE, sess('default_lang_id'), $vars['primary_email']);
		}

		$url = !$this->agent->referrer() ? ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view' : $this->agent->referrer();

		redirect_flashdata($url, 'email_sent_successfully');
	}

	// ------------------------------------------------------------------------

	public function general_search()
	{
		//show users
		$this->data['page_options'] = query_options($this->data);

		//set query type
		$type = '';

		if ($this->input->get('type_id'))
		{
			$type = $this->input->get(array('table', 'type_id', 'group_id'), TRUE);
		}

		$this->data['rows'] = $this->mem->search($this->data['page_options'], $type);

		//check for pagination
		if (!empty($this->data['rows']['total']))
		{
			$this->data['page_options'] = array(
				'uri'        => $this->data['uri'],
				'total_rows' => $this->data['rows']['total'],
				'per_page'   => $this->data['session_per_page'],
				'segment'    => $this->data['db_segment'],
			);

			$this->data['paginate'] = $this->paginate->generate($this->data['page_options'], CONTROLLER_CLASS, 'admin');
		}

		//run the page
		$tpl = init_member_template();
		$this->load->page('members/' . $tpl, $this->data);
	}

	// ------------------------------------------------------------------------

	public function search()
	{
		$term = $this->input->get('username', TRUE);

		$rows = $this->mem->ajax_search(uri(5, 'username'), $term);

		echo json_encode($rows);
	}
}

/* End of file Members.php */
/* Location: ./application/controllers/admin/Members.php */
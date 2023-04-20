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

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Email_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $table = TBL_EMAIL_ARCHIVE;

	// ------------------------------------------------------------------------

	/**
	 * Email_model constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->helper('email');
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool
	 */
	public function archive($data = array())
	{
		$data = array('member_id'       => is_var($data, 'member_id'),
		              'send_date'       => get_time(now(), TRUE),
		              'from_name'       => $data['from_name'],
		              'from_email'      => $data['from_email'],
		              'recipient_name'  => format_recipient_name($data),
		              'recipient_email' => $data['primary_email'],
		              'cc'              => is_var($data, 'cc'),
		              'bcc'             => is_var($data, 'bcc'),
		              'subject'         => is_var($data, 'subject'),
		              'html_body'       => is_var($data, 'html_body'),
		              'text_body'       => is_var($data, 'text_body'),
		);

		if (!$this->db->insert(TBL_EMAIL_ARCHIVE, $data))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $offset
	 * @param string $type
	 * @return array
	 */
	public function flush($offset = '0', $type = '')
	{
		$sent = 0;

		$row = $this->get_queued_emails($offset, config_option('sts_email_limit_mass_mailing'), $type);

		if (!empty($row))
		{
			foreach ($row as $k)
			{
				//send email
				if ($this->send($k, $k['primary_email']))
				{
					$sent++;
					$this->dbv->delete(TBL_EMAIL_QUEUE, 'id', $k['id']);
				}
			}
		}

		return array('total_sent' => $sent,
		             'offset'     => $offset + config_option('sts_email_limit_mass_mailing'),
		             'msg_text'   => $sent . ' ' . lang('emails_sent_successfully'));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param int $limit
	 * @return bool|false|string
	 */
	public function get_user_archive($id = '', $limit = ADMIN_MEMBERS_RECENT_DATA)
	{
		if (!$q = $this->db->where('member_id', $id)->order_by('id', 'DESC')->limit($limit)->get($this->table))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->result_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param int $offset
	 * @param string $limit
	 * @param string $type
	 * @return bool|false|string
	 */
	public function get_queued_emails($offset = 0, $limit = '', $type = '')
	{
		$sql = 'SELECT * FROM ' . $this->db->dbprefix(TBL_EMAIL_QUEUE);

		if ($type == 'checkout')
		{
			$limit = '10';
			$sql .= ' ORDER BY id DESC';
		}

		$sql .= ' LIMIT ' . $offset . ', ' . $limit;


		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->result_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $offset
	 * @return array
	 */
	public function queue_mass_email($data = array(), $offset = '0')
	{
		$sent = 0;

		//get the users from the list
		$row = $this->lists->get_subscribers(array('offset' => $offset,
		                                           'limit'  => config_option('sts_email_limit_mass_mailing')),
			$data['list_id']);

		if (!empty($row['values']))
		{
			foreach ($row['values'] as $v)
			{
				//queue the email
				$user = format_mass_email($v);
				$template_ready = $this->prepare_template($user, $data);

				//merge arrays
				$tpl = array_merge($user, $template_ready);

				if ($this->queue($tpl))
				{
					$sent++;
				}
			}
		}

		return array('total_sent' => $sent,
		             'offset'     => $offset + config_option('sts_email_limit_mass_mailing'));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool
	 */
	public function queue($data = array())
	{
		if (valid_email($data['primary_email']))
		{
			$insert = array(
				'member_id'      => is_var($data, 'member_id'),
				'email_type'     => is_var($data, 'email_type', FALSE, 'html'),
				'send_date'      => is_var($data, 'send_date', FALSE, get_time(now(), TRUE)),
				'from_name'      => is_var($data, 'from_name', FALSE, config_option('sts_site_name')),
				'from_email'     => is_var($data, 'from_email', FALSE, config_option('sts_site_email')),
				'recipient_name' => format_recipient_name($data),
				'primary_email'  => $data['primary_email'],
				'cc'             => is_var($data, 'cc'),
				'bcc'            => is_var($data, 'bcc'),
				'subject'        => $data['subject'],
				'html_body'      => $data['html_body'],
				'text_body'      => $data['text_body'],
			);

			if (!$this->db->insert('email_queue', $insert))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if (config_option('sts_email_send_queue') == 1)
			{
				$this->flush();
			}

			return TRUE;
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $template
	 * @return string
	 */
	public function prepare_template($data = array(), $template = '')
	{
		//add supporting variables
		foreach ($this->config->config as $k => $v)
		{
			$data[$k] = $v;
		}

		$data['admin_login_url'] = admin_login_url();
		$data['login_url'] = site_url('login');
		$data['site_url'] = site_url();
		$data['site_name'] = config_option('sts_site_name');
		$data['site_email'] = config_option('sts_site_email');
		$data['site_name'] = config_option('sts_site_name');
		$data['site_email'] = config_option('sts_site_email');
		$data['charset'] = config_option('sts_email_charset');
		$data['ip_address'] = $this->input->ip_address();

		if (!empty($data['primary_email']))
		{
			$data['unsubscribe_url_text'] = site_url('email/subscriptions/' . md5(config_Item('sts_system_domain_key')) . '/' . $data['primary_email']);
			$data['unsubscribe_url'] = anchor($data['unsubscribe_url_text']);
		}

		//set date
		$data['current_date'] = date(config_option('format_date2'), get_time());
		$data['current_time'] = date(config_option('default_time_format'), get_time());

		if (!empty($template['from_name']))
		{
			$template['from_name'] = str_replace('{{site_name}}', $data['site_name'], $template['from_name']);
		}
		if (!empty($template['from_email']))
		{
			$template['from_email'] = str_replace('{{site_email}}', $data['site_email'], $template['from_email']);
		}

		if (!empty($template['subject']))
		{
			$template['subject'] = $this->show->parse_tpl($data, $template['subject']);
		}

		if (!empty($template['text_body']))
		{
			$template['text_body'] = $this->show->parse_tpl($data, $template['text_body']);
		}

		//parse the html header and footer if any and append to the html email itself....
		if (!empty($template['html_body']))
		{
			$header = $this->show->parse_tpl($data, html_entity_decode(config_option('layout_design_email_template_header')));
			$template['html_body'] = $header . $template['html_body'];
			$template['html_body'] = $this->show->parse_tpl($data, html_entity_decode($template['html_body']));
			$template['html_body'] .= $this->show->parse_tpl($data, html_entity_decode(config_option('layout_design_email_template_footer')));
		}

		return $template;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 */
	public function send_email_events($data = array())
	{
		$this->send_template(ADMIN_EMAIL_EVENT_ALERT_TEMPLATE, $data, FALSE, config_item('sts_admin_default_language'), config_item('sts_sec_admin_debug_security_email'));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $type
	 * @param array $data
	 * @param int $lang_id
	 */
	public function send_forum_alerts($type = 'topic', $data = array(), $lang_id = 1)
	{
		switch ($type)
		{
			case 'topic':

				$templates = array(EMAIL_ADMIN_FORUM_TOPIC_ALERT);

				break;

			case 'reply':

				$templates = array(EMAIL_ADMIN_FORUM_REPLY_ALERT, EMAIL_MEMBER_FORUM_REPLY_ALERT);

				break;
		}

		foreach ($templates as $v)
		{
			switch ($v)
			{
				case EMAIL_MEMBER_FORUM_REPLY_ALERT:

					$this->send_template($v, $data, TRUE, $lang_id, $data['primary_email']);

					break;

				default:

					if ($admins = get_admins()) //get active admins to send alerts to.
					{
						foreach ($admins as $a)
						{
							if (!empty($a['alert_forum_topic']))
							{
								$data['admin_fname'] = $a['fname'];
								$data['admin_lname'] = $a['lname'];

								$this->send_template($v, $data, TRUE, $lang_id, $a['primary_email']);
							}
						}
					}

					break;
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 */
	public function send_registration_emails($data = array())
	{
		$templates = array(EMAIL_ADMIN_ALERT_NEW_SIGNUP, EMAIL_MEMBER_EMAIL_CONFIRMATION,
		                   EMAIL_MEMBER_AFFILIATE_DOWNLINE_SIGNUP, EMAIL_MEMBER_ALERT_SIGNUP_BONUS,
		                   EMAIL_MEMBER_AFFILIATE_REFERRAL_SIGNUP_BONUS, EMAIL_MEMBER_LOGIN_DETAILS);

		foreach ($templates as $v)
		{
			switch ($v)
			{
				case EMAIL_ADMIN_ALERT_NEW_SIGNUP:

					if ($admins = get_admins()) //get active admins to send alerts to.
					{
						foreach ($admins as $a)
						{
							if (!empty($a['alert_affiliate_signup']))
							{
								$data['admin_fname'] = $a['fname'];
								$data['admin_lname'] = $a['lname'];

								$vars = format_registration_email($v, $data);

								$this->send_template($v, $vars, FALSE, sess('default_lang_id'), $a['primary_email']);
							}
						}
					}

					break;

				case EMAIL_MEMBER_EMAIL_CONFIRMATION:

					if (config_enabled(('sts_email_require_confirmation_on_signup')))
					{
						$vars = format_registration_email($v, $data);

						$this->send_template($v, $vars, FALSE, sess('default_lang_id'), $data['primary_email']);
					}

					break;

				case EMAIL_MEMBER_AFFILIATE_DOWNLINE_SIGNUP: //send an alert to the referring affiliate

					if (!empty($data['sponsor_data']['alert_downline_signup']))
					{
						$vars = format_registration_email($v, $data);

						$this->send_template($v, $vars, FALSE, sess('default_lang_id'), $data['sponsor_data']['primary_email']);
					}

					break;

				case EMAIL_MEMBER_LOGIN_DETAILS:

					if (config_enabled('sts_email_send_registration_login_details'))
					{
						$vars = format_registration_email($v, $data);

						$this->send_template($v, $vars, FALSE, sess('default_lang_id'), $data['primary_email']);
					}

					break;

				case EMAIL_MEMBER_ALERT_SIGNUP_BONUS:

					if (!empty($data['signup_bonus']))
					{
						$vars = format_registration_email($v, $data);

						$this->send_template($v, $vars, FALSE, sess('default_lang_id'), $data['primary_email']);
					}

					break;

				case EMAIL_MEMBER_AFFILIATE_REFERRAL_SIGNUP_BONUS:

					if (!empty($data['referral_bonus']))
					{
						if (!empty($data['sponsor_data']['alert_new_commission']))
						{
							$vars = format_registration_email($v, $data);

							$this->send_template($v, $vars, FALSE, sess('default_lang_id'), $data['sponsor_data']['primary_email']);
						}
					}

					break;
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $lang_id
	 * @param string $email
	 */
	public function send_download_access_emails($data = array(), $lang_id = '1', $email = '')
	{
		//set the download links text
		$vars = format_email_download_links($data);

		$primary_email = !empty($email) ? $email : $data['primary_email'];

		$this->send_template(EMAIL_MEMBER_DOWNLOAD_ACCESS, $vars, TRUE, $lang_id, $primary_email);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $type
	 */
	public function send_customer_order_emails($data = array(), $type = 'checkout')
	{
		//send order template
		$templates = array(EMAIL_MEMBER_ORDER_DETAILS, EMAIL_MEMBER_ALERT_SIGNUP_BONUS,
		                   EMAIL_MEMBER_AFFILIATE_REFERRAL_SIGNUP_BONUS, EMAIL_MEMBER_PAYMENT_INVOICE,
		                   EMAIL_MEMBER_AFFILIATE_COMMISSION);

		foreach ($templates as $v)
		{
			switch ($v)
			{
				case EMAIL_MEMBER_ORDER_DETAILS:

					$order = format_checkout_email('order', $data, $type);

					$this->send_template($v, $order, TRUE, sess('default_lang_id'), $data['customer']['primary_email']);

					break;

				case EMAIL_MEMBER_PAYMENT_INVOICE:

					$invoice = format_checkout_email('invoice', $data, $type);

					$this->send_template($v, $invoice, TRUE, sess('default_lang_id'), $data['customer']['primary_email']);

					break;

				case EMAIL_MEMBER_AFFILIATE_COMMISSION:

					if (!empty($data['commissions']))
					{
						$send_email = array('alert_pending', 'alert_unpaid');

						if (in_array(config_item('sts_affiliate_new_commission'), $send_email))
						{
							foreach ($data['commissions'] as $c)
							{
								if (!empty($c['is_affiliate']) && !empty($c['alert_new_commission']))
								{
									$comm = format_checkout_email('commission', $c, $type);

									$this->send_template($v, $comm, TRUE, sess('default_lang_id'), $c['primary_email']);
								}
							}
						}
					}

					break;

				case EMAIL_MEMBER_ALERT_SIGNUP_BONUS:

					if (!empty($data['bonuses']['signup_bonus']))
					{
						$vars = format_checkout_email('bonus', $data['bonuses'], $type);

						$this->send_template($v, $vars, FALSE, sess('default_lang_id'), $data['customer']['primary_email']);
					}

					break;

				case EMAIL_MEMBER_AFFILIATE_REFERRAL_SIGNUP_BONUS:

					if (!empty($data['bonuses']['referral_bonus']))
					{
						$data['bonuses']['sponsor_data'] = check_referral_data($data['bonuses']);

						if (!empty($data['bonuses']['sponsor_data']['alert_new_commission']))
						{
							$vars = format_checkout_email('referral_bonus', $data['bonuses'], $type);

							$this->send_template($v, $vars, FALSE, sess('default_lang_id'), $data['sponsor_data']['primary_email']);
						}
					}

					break;
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 */
	public function send_admin_blog_alerts($data = array())
	{
		if ($admins = get_admins()) //get active admins to send alerts to.
		{
			foreach ($admins as $a)
			{
				$this->send_template(EMAIL_ADMIN_ALERT_COMMENT_MODERATION, $data, TRUE, sess('default_lang_id'), $a['primary_email']);
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $type
	 */
	public function send_admin_order_emails($data = array(), $type = 'checkout')
	{
		//send order details template
		$templates = array(EMAIL_ADMIN_ALERT_NEW_ORDER, EMAIL_ADMIN_AFFILIATE_COMMISSION);

		if ($admins = get_admins()) //get active admins to send alerts to.
		{
			foreach ($admins as $a)
			{
				foreach ($templates as $v)
				{
					switch ($v)
					{
						case EMAIL_ADMIN_ALERT_NEW_ORDER:

							if (!empty($a['alert_store_order']))
							{
								$data['admin_fname'] = $a['fname'];
								$data['admin_lname'] = $a['lname'];

								$order = format_checkout_email('order', $data, $type);

								$this->send_template($v, $order, TRUE, sess('default_lang_id'), $a['primary_email']);
							}

							break;

						case EMAIL_ADMIN_AFFILIATE_COMMISSION:

							if (!empty($data['commissions']) && !empty($a['alert_affiliate_commission']))
							{
								foreach ($data['commissions'] as $c)
								{
									$comm = format_checkout_email('commission', $c, $type);

									$this->send_template($v, $comm, TRUE, sess('default_lang_id'), $a['primary_email']);
								}
							}

							break;
					}
				}
			}
		}

		$this->send_supplier_alert_emails($data);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $type
	 */
	public function send_supplier_alert_emails($data = array(), $type = 'checkout')
	{
		if (config_enabled('sts_shipping_send_supplier_email_alert'))
		{
			//now send any supplier related emails
			if (!empty($data['order']['items']))
			{
				foreach ($data['order']['items'] as $v)
				{
					if (!empty($v['supplier_email']) && !empty($v['supplier_send_alert']))
					{
						$supplier_data = array_merge($data['order'], $v);
						$supplier = format_checkout_email('supplier', $supplier_data, $type);

						$this->send_template(EMAIL_ADMIN_ALERT_SUPPLIER, $supplier, TRUE, sess('default_lang_id'), $v['supplier_email']);
					}
				}
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param array $users
	 * @return array
	 */
	public function send_downline_email($data = array(), $users = array())
	{
		$total = 0;

		foreach ($users as $v)
		{
			$row = array_merge($data, $v);
			$user = format_mass_downline_email($row);

			$this->send_template(EMAIL_MEMBER_AFFILIATE_SEND_DOWNLINE_EMAIL, $user, TRUE, sess('default_lang_id'), $user['primary_email']);

			$total++;
		}

		return array('success'  => TRUE,
		             'msg_text' => $total . ' ' . lang('mass_downline_emails_sent_successfully'));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $func
	 * @param array $data
	 * @param string $type
	 */
	public function send_support_alerts($func = 'create', $data = array(), $type = 'members')
	{
		//send out template alerts for members and admins
		switch ($type)
		{
			case 'members':

				$support = format_checkout_email('support_member', $data);

				//send alerts to members
				switch ($func)
				{
					case 'create':

						$this->send_template(EMAIL_MEMBER_CREATE_SUPPORT_TICKET, $support, FALSE, sess('default_lang_id'), $support['primary_email']);

						break;

					case 'update':

						$this->send_template(EMAIL_MEMBER_SUPPORT_TICKET_REPLY, $support, FALSE, sess('default_lang_id'), $support['primary_email']);

						break;
				}

				break;

			case 'admins':

				if ($admins = get_admins()) //get active admins to send alerts to.
				{
					foreach ($admins as $a)
					{
						if (!empty($a['alert_ticket_response']))
						{
							$data['admin_fname'] = $a['fname'];
							$data['admin_lname'] = $a['lname'];

							$support = format_checkout_email('support_admin', $data);

							switch ($func)
							{
								case 'create':

									$this->send_template(EMAIL_ADMIN_CREATE_SUPPORT_TICKET, $support, FALSE, sess('default_lang_id'), $a['primary_email']);

									break;

								case 'update':

									$this->send_template(EMAIL_ADMIN_SUPPORT_TICKET_REPLY, $support, FALSE, sess('default_lang_id'), $a['primary_email']);

									break;
							}
						}
					}
				}

				break;
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param array $data
	 * @param bool $queue
	 * @param string $lang_id
	 * @param string $email
	 * @return bool
	 */
	public function send_template($id = '', $data = array(), $queue = FALSE, $lang_id = '1', $email = '')
	{
		//get email template first
		$sql = 'SELECT *
                  FROM ' . $this->db->dbprefix(TBL_EMAIL_TEMPLATES) . ' p
                  LEFT JOIN ' . $this->db->dbprefix(TBL_EMAIL_TEMPLATES_NAME) . ' n
                    ON p.template_id = n.template_id
                    AND language_id = \'' . (int)$lang_id . '\'
                  WHERE p.template_name = \'' . valid_id($id, TRUE) . '\'
                    AND p.status = \'1\'';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() == 1) //check if the template exists
		{
			$template = $q->row_array();

			$row = $data;

			$row['member_id'] = is_var($data, 'member_id');

			//substitute all template variables for real data
			$template_ready = $this->prepare_template($row, $template);

			//merge arrays
			$tpl = array_merge($row, $template_ready);

			//set the email address
			if (!empty($email))
			{
				$tpl['primary_email'] = $email;
			}

			if ($queue == TRUE && config_option('sts_email_send_queue') == 0)
			{
				$msg = $this->queue($tpl) == TRUE ? TRUE : FALSE;

				return $msg;
			}
			else
			{
				$msg = $this->send($tpl) == TRUE ? TRUE : FALSE;

				return $msg;
			}
		}
		else
		{
			return FALSE;
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $email
	 * @return bool|false|string
	 */
	public function send($data = array(), $email = '')
	{
		if (!empty($email))
		{
			$data['primary_email'] = $email;
		}

		if (valid_email($data['primary_email']))
		{
			//set defaults
			switch (config_option('sts_email_server_provider'))
			{
				case 'sendgrid':

					$response = $this->use_sendgrid($data);

					break;

				case 'phpmailer':

					$response = $this->use_phpmailer($data);

					break;

				default: //built-in codeigniter

					//use built-in codeigniter email library
					$response = $this->use_mail($data);

					break;
			}
			//check if email is to be archived
			if (config_option('sts_email_enable_archive') == 1)
			{
				$this->archive($data);
			}

			if ($response)
			{
				$row = array(
					'success'  => TRUE,
					'msg_text' => lang('email_sent_successfully'),
					'data'     => $data,
				);
			}
		}

		return !empty($row) ? sc($row) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return false|string
	 */
	public function validate_mass_email($data = array())
	{
		//run the validation...
		$this->form_validation->set_data($data);

		$rules = array('list_id'   => 'trim|integer',
		               'subject'   => 'trim|strip_tags|required|max_length[255]',
		               'html_body' => 'trim|required',
		               'text_body' => 'trim|strip_tags',
		               'html'      => 'trim|integer',
		);

		foreach ($rules as $k => $v)
		{
			$this->form_validation->set_rules($k, 'lang:' . $k, $v);
		}

		if ($this->form_validation->run())
		{
			$row = array(
				'success' => TRUE,
				'data'    => $this->dbv->validated($data, FALSE));
		}
		else
		{
			$row = array(
				'error'    => TRUE,
				'msg_text' => validation_errors(),
			);
		}

		return sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $merge
	 * @return false|string
	 */
	public function validate($data = array(), $merge = '')
	{
		//set defaults
		$data['from_email'] = empty($data['from_email']) ? config_option('sts_site_email') : $data['from_email'];
		$data['from_name'] = empty($data['from_name']) ? config_option('sts_site_name') : $data['from_name'];

		//check if we're doing a merge
		$data = !empty($merge) ? $this->prepare_template($merge, $data) : $data;

		//run the validation...
		$this->form_validation->set_data($data);

		$rules = array('from_email'    => 'required|valid_email',
		               'from_name'     => 'trim|strip_tags|required|max_length[255]',
		               'primary_email' => 'required|valid_email',
		               'cc'            => 'valid_emails',
		               'bcc'           => 'valid_emails',
		               'subject'       => 'trim|strip_tags|required|max_length[255]',
		               'html_body'     => 'trim|required',
		               'text_body'     => 'trim|strip_tags',
		               'member_id'     => 'trim|integer',
		);

		foreach ($rules as $k => $v)
		{
			$this->form_validation->set_rules($k, 'lang:' . $k, $v);
		}

		if ($this->form_validation->run())
		{
			$row = array(
				'success' => TRUE,
				'data'    => $this->dbv->validated($data, FALSE));
		}
		else
		{
			$row = array(
				'error'    => TRUE,
				'msg_text' => validation_errors(),
			);
		}

		return sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|string
	 */
	public function use_sendgrid($data = array())
	{
		$url = $this->config->slash_item('sts_email_smtp_host');

		$params = array(
			'api_user' => config_option('sts_email_smtp_username'),
			'api_key'  => config_option('sts_email_smtp_password'),
			'to'       => $data['primary_email'],
			'subject'  => $data['subject'],
			'html'     => $data['html_body'],
			'text'     => $data['text_body'],
			'from'     => $data['from_email'],
		);

		$request = $url . 'api/mail.send.json';

		// Generate curl request
		$session = curl_init($request);
		// Tell curl to use HTTP POST
		curl_setopt($session, CURLOPT_POST, TRUE);
		// Tell curl that this is the body of the POST
		curl_setopt($session, CURLOPT_POSTFIELDS, $params);
		// Tell curl not to return headers, but do return the response
		curl_setopt($session, CURLOPT_HEADER, FALSE);
		// Tell PHP not to use SSLv3 (instead opting for TLS)
		curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, TRUE);

		// obtain response
		$response = curl_exec($session);
		curl_close($session);

		return $response;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool
	 * @throws Exception
	 */
	public function use_phpmailer($data = array())
	{
		$mail = new PHPMailer;

		//for debugging
		$mail->SMTPDebug = config_option('sts_email_enable_debugging') == 1 ? 2 : 0;

		switch (config_option('sts_email_mailer_type'))
		{
			case 'smtp':

				$mail->IsSMTP();
				$mail->Host = config_option('sts_email_smtp_host');
				$mail->Port = config_option('sts_email_smtp_port');
				$mail->Timeout = config_option('sts_email_smtp_timeout');

				if (config_option('sts_email_enable_ssl') != 'none')
				{
					$mail->SMTPSecure = config_option('sts_email_enable_ssl');
				}

				//check if SMTP authorization is needed
				if (config_option('sts_email_use_smtp_authentication') == 1)
				{
					$mail->SMTPAuth = TRUE;
					$mail->Username = config_option('sts_email_smtp_username');
					$mail->Password = config_option('sts_email_smtp_password');
				}

				break;

			case 'sendmail':

				$mail->IsSendmail();

				break;

			case 'qmail':

				$mail->IsQmail();
				break;


			default: //php

				$mail->IsMail();

				break;
		}

		//set the charset
		$mail->CharSet = config_option('sts_email_charset');

		$mail->From = $data['from_email'];
		$mail->FromName = $data['from_name'];
		$mail->Subject = $data['subject'];

		$html_body = empty($data['html_body']) ? '' : $data['html_body'];
		$text_body = empty($data['text_body']) ? '' : $data['text_body'];

		if (!empty($html_body))
		{
			$mail->Body = stripslashes($html_body);
			$mail->isHTML(TRUE);
			$mail->AltBody = stripslashes($text_body);
		}
		else
		{
			$mail->Body = stripslashes($text_body);
		}

		//add recipients
		$mail->AddAddress($data['primary_email']);

		//add CC:
		if (!empty($data['cc']))
		{
			$cc_recipients = explode(',', $data['cc']);

			foreach ($cc_recipients as $value)
			{
				$mail->AddCC($value);
			}
		}

		//add BCC:
		if (!empty($data['bcc']))
		{
			$bcc_recipients = explode(',', $data['bcc']);

			foreach ($bcc_recipients as $value)
			{
				$mail->AddBCC($value);
			}
		}

		//add reply to
		if (!empty($data['reply_to_email']))
		{
			$mail->AddReplyTo($data['reply_to_email'], $data['reply_to_email']);
		}
		else
		{
			//$mail->AddReplyTo($data['from_email']);
			$mail->AddReplyTo(config_option('sts_site_email'));
		}

		//send it!
		if ($mail->Send())
		{
			$mail->ClearAddresses();
			$mail->ClearAttachments();

			return TRUE;
		}
		else
		{
			if (config_enabled('sts_email_enable_debugging'))
			{
				show_error($mail->ErrorInfo);
			}
			else
			{
				$this->dbv->rec(array('method' => __METHOD__, 'msg' => $mail->ErrorInfo, 'level' => 'error'));
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool
	 */
	public function use_mail($data = array())
	{
		$this->load->library('email');

		switch (config_option('sts_email_mailer_type'))
		{
			case 'smtp':

				$vars = array(
					'protocol'  => 'smtp',
					'smtp_host' => config_option('sts_email_smtp_host'),
					'smtp_user' => config_option('sts_email_smtp_username'),
					'smtp_pass' => config_option('sts_email_smtp_password'),
					'smtp_port' => config_option('sts_email_smtp_port'),
				);

				break;

			case 'sendmail':

				$vars = array(
					'protocol' => 'sendmail',
					'mailpath' => '/usr/sbin/sendmail',
				);

				break;

			default: //php

				$vars['protocol'] = 'mail';

				break;
		}

		$vars['crlf'] = "\r\n";
		$vars['newline'] = "\r\n";
		$vars['charset'] = config_option('sts_email_charset');

		if (config_option('sts_email_enable_ssl') != 'none')
		{
			$vars['smtp_crypto'] = config_option('sts_email_enable_ssl');
		}

		$this->email->initialize($vars);

		$this->email->from($data['from_email'], $data['from_name']);

		//add reply to
		if (!empty($data['reply_to_email']))
		{
			$this->email->reply_to($data['reply_to_email'], $data['reply_to_email']);
		}
		else
		{
			$this->email->reply_to(config_option('sts_site_email'), config_option('sts_site_email'));
		}

		//add recipients
		$this->email->to($data['primary_email']);

		//add CC:
		if (!empty($data['cc']))
		{
			$this->email->cc($data['cc']);
		}

		//add BCC:
		if (!empty($data['bcc']))
		{
			$this->email->bcc($data['bcc']);
		}

		$this->email->subject($data['subject']);

		$html_body = empty($data['html_body']) ? '' : $data['html_body'];
		$text_body = empty($data['text_body']) ? '' : $data['text_body'];

		if (!empty($data['html']))
		{
			$this->email->message(strip_slashes($html_body));
			$this->email->set_alt_message($text_body);
		}
		else
		{
			$this->email->message($text_body);
		}

		if (config_enabled('sts_email_enable_debugging'))
		{
			$this->email->send(FALSE);

			$this->email->print_debugger(array('headers'));
		}
		else
		{
			if (!$this->email->send())
			{
				return FALSE;
			}
		}

		$this->email->clear(TRUE);

		return TRUE;
	}
}
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
class Cron extends Cron_Controller
{
	protected $data = array();

	protected $timers = array();

	protected $msg_text = array();

	public function __construct()
	{
		parent::__construct();

		//set memory
		@ini_set("memory_limit", CRON_MEMORY_LIMIT);
		@set_time_limit(CRON_TIME_LIMIT);

		log_message('debug', __CLASS__ . ' Class Initialized');

		$this->data = $this->init->initialize('external');

		$this->timers = $this->cron->get_timers(TRUE);
	}

	// ------------------------------------------------------------------------

	public function run()
	{
		$time_start = microtime(TRUE);

		foreach ($this->timers as $v)
		{
			if (get_time() > $v['timestamp'])
			{
				$func = $v['name'];

				if (method_exists(__CLASS__, $func))
				{
					if ($this->$func())
					{
						$this->cron->update_timer($func);
					}
				}
			}
		}

		if (count($this->msg_text) > 0)
		{
			$time_end = microtime(TRUE);

			array_push($this->msg_text, array('cron_job' => 'execution_time',
			                                  'date' => current_date(),
			                                  'msg_text' => ($time_end - $time_start) . ' ' . lang('seconds')));

			$this->dbv->rec(array('method' => __METHOD__, 'msg' => lang('cron_job_ran_successfully'), 'vars' => $this->msg_text));

			//send out the cron report
			if (config_enabled('sts_cron_send_debug_cron_reports'))
			{
				send_debug_email($this->msg_text, 'cron report ' . date(config_item('default_time_format')));
			}

			echo '<pre>';
			print_r($this->msg_text);
		}
		else
		{
			echo lang('no_cron_jobs_ran');
		}
	}

	// ------------------------------------------------------------------------

	public function archive_reports()
	{
		if (date('j') == '1')
		{
			$this->init->db_trans('trans_begin');

			$rows = $this->mod->get_modules('admin_reporting', TRUE);

			$msg = '';

			foreach ($rows as $v)
			{
				$html = '';
				$r = array();

				if (substr($v['module_folder'], 0, 6) == 'month_')
				{
					$r = array('row'        => $v,
					           'no_archive' => TRUE);

					//initialize the require files for the module
					$this->init_module('admin_reporting', $v['module_folder']);

					$model = $this->config->item('module_model_alias');
					$func = $this->config->item('module_generate_function'); //generate_module()

					$m = date('m', strtotime(date('F') . ' last month'));
					$y = date('Y', strtotime(date('F') . ' last month'));

					$r['report'] = $this->$model->$func($this->data, $m, $y);
					$r['report']['dates'] = current_date('M', $m, '', $y) . ' ' . current_date('Y', $m, '', $y);

					$html = $this->load->page('reports/' . $this->config->item('module_admin_view_template'), $r, 'admin', FALSE, FALSE, FALSE, TRUE);

					if ($row = $this->report->archive_report($r['report']['dates'] . ' ' . $v['module_name'], $html))
					{
						$row = array('success' => TRUE);
						$msg .= $r['report']['dates'] . ' ' . $v['module_name'] . ' ' . lang('report_generated') . "\n";
					}
					//reset modules
					$this->remove_module('admin_reporting', $v['module_folder']);
				}
			}

			if (!empty($msg))
			{
				array_push($this->msg_text, array('cron_job' => __FUNCTION__,
				                                  'msg_text' => $msg));

				$this->dbv->rec(array('method' => __METHOD__,
				                      'msg' => $msg));

				$this->init->db_trans('trans_commit');

				return TRUE;
			}

			$this->init->db_trans('trans_rollback');
		}
	}

	// ------------------------------------------------------------------------

	public function auto_approve_commissions()
	{
		$this->init->db_trans('trans_begin');

		$row = $this->comm->auto_approve_commissions();

		if (!empty($row['success']))
		{
			array_push($this->msg_text, array('cron_job' => __FUNCTION__,
			                                  'msg_text' => $row['msg_text']));

			$this->dbv->rec(array('method' => __METHOD__, 'msg' => $row['msg_text']));

			$this->init->db_trans('trans_commit');

			return TRUE;
		}

		$this->init->db_trans('trans_rollback');
	}

	// ------------------------------------------------------------------------

	public function auto_close_support_tickets()
	{
		$this->init->db_trans('trans_begin');

		$row = $this->support->auto_close_tickets();

		if (!empty($row['success']))
		{
			array_push($this->msg_text, array('cron_job' => __FUNCTION__,
			                                  'msg_text' => $row['msg_text']));

			$this->dbv->rec(array('method' => __METHOD__, 'msg' => $row['msg_text']));

			$this->init->db_trans('trans_commit');

			return TRUE;
		}

		$this->init->db_trans('trans_rollback');
	}

	// ------------------------------------------------------------------------

	public function backup_database()
	{
		$this->load->library('zip');

		@ini_set("memory_limit", BACKUP_MEMORY_LIMIT);

		$row = $this->backup->backup_db();

		if (!empty($row['success']))
		{
			array_push($this->msg_text, array('cron_job' => __FUNCTION__,
			                                  'msg_text' => $row['msg_text']));

			$this->dbv->rec(array('method' => __METHOD__, 'msg' => $row['msg_text']));

			return TRUE;
		}
	}

	// ------------------------------------------------------------------------

	public function backup_files()
	{
		$this->load->library('zip');

		@ini_set("memory_limit", BACKUP_MEMORY_LIMIT);

		$row = $this->backup->backup_files(BACKUP_FILES_PATH);

		if (!empty($row['success']))
		{
			array_push($this->msg_text, array('cron_job' => __FUNCTION__,
			                                  'msg_text' => $row['msg_text']));

			$this->dbv->rec(array('method' => __METHOD__, 'msg' => $row['msg_text']));

			return TRUE;
		}
	}

	// ------------------------------------------------------------------------

	public function cancel_expired_subscriptions()
	{
		$this->init->db_trans('trans_begin');

		$row = $this->sub->cancel_subscriptions();

		if (!empty($row['success']))
		{
			array_push($this->msg_text, array('cron_job' => __FUNCTION__,
			                                  'msg_text' => $row['msg_text']));

			$this->dbv->rec(array('method' => __METHOD__, 'msg' => $row['msg_text']));

			$this->init->db_trans('trans_commit');

			return TRUE;
		}

		$this->init->db_trans('trans_rollback');
	}

	// ------------------------------------------------------------------------

	public function cancel_unpaid_invoices()
	{
		$this->init->db_trans('trans_begin');

		$row = $this->invoices->cancel_invoices();

		if (!empty($row['success']))
		{
			array_push($this->msg_text, array('cron_job' => __FUNCTION__,
			                                  'msg_text' => $row['msg_text']));

			$this->dbv->rec(array('method' => __METHOD__, 'msg' => $row['msg_text']));

			$this->init->db_trans('trans_commit');

			return TRUE;
		}
		else
		{
			$this->init->db_trans('trans_rollback');
		}
	}

	// ------------------------------------------------------------------------

	public function generate_invoices()
	{
		//get active subscriptions that are due
		$rows = $this->sub->get_active_subscriptions(config_item('sts_admin_default_language'));

		if (!empty($rows))
		{
			$total = 0;

			foreach ($rows as $row)
			{
				$this->init->db_trans('trans_begin');

				//create new orders
				$row['order_data'] = $this->orders->create_subscription_order($row, config_item('sts_admin_default_language'));

				if (!empty($row['order_data']['success']))
				{
					$this->dbv->rec(array('method' => __METHOD__, 'msg' =>  $row['order_data']['msg_text']));

					//generate new invoices
					$row['invoice_data'] = $this->invoices->create_invoice($row['order_data'], 'cron');

					//update the subscription
					$this->sub->update_subscription_history($row);

					//send out the invoice
					$this->mail->send_template(EMAIL_MEMBER_PAYMENT_INVOICE, $row['invoice_data']['data'], TRUE, config_item('sts_admin_default_language'), $row['invoice_data']['data']['customer_primary_email']);

					$total++;

					$this->init->db_trans('trans_commit');
				}
				else
				{
					$this->init->db_trans('trans_rollback');
				}

			}

			$msg = $total . ' ' . lang('subscription_invoices_created_successfully');

			array_push($this->msg_text, array('cron_job' => __FUNCTION__,
			                                  'msg_text' => $msg));

			$this->dbv->rec(array('method' => __METHOD__, 'msg' => $msg));

			return TRUE;
		}
	}

	// ------------------------------------------------------------------------

	public function generate_payments()
	{
		//get invoices that are due
		$rows = $this->invoices->get_invoices_due(TRUE);

		if (!empty($rows))
		{
			$total = 0;

			foreach ($rows as $row)
			{
				$this->init->db_trans('trans_begin');

				$this->data['order_data'] = array(
					'invoice'     => $row,
					'customer'    => $this->mem->get_details($row['member_id']),
					'affiliate'   => check_order_affiliate($row['affiliate_id']),
					'order_notes' => '',
					'language'    => config_item('sts_site_default_language'),
					'currency'    => $this->config->item('currency'),
					'user_agent'  => browser_info(),
				);

				if (!empty($row['order_id']))
				{
					$this->data['order_data']['order'] = $this->orders->get_details($row['order_id'], FALSE, TRUE);
				}

				//initialize the require files for the module
				$this->init_module('payment_gateways', $row['module_folder']);

				//set model and function alias for calling methods
				$module = $this->config->item('module_alias');

				//run only if the method is available
				if (config_item('module_redirect_type') == 'onsite')
				{
					if (method_exists($this->$module, 'generate_payment'))
					{
						$mod = $row;
						$payment = $this->$module->generate_payment($row, $mod, 'cron');

						//we got paid!
						if (!empty($payment['type']) && $payment['type'] == 'success')
						{
							$this->data['order_data']['transaction'] = $payment;

							$this->data['order_data'] = $this->pay_invoice();

							$total++;
						}

						$this->dbv->rec(array('method' => __METHOD__, 'msg' => is_var($payment, 'msg_text', $payment, lang('payment_generated_successfully'))));
					}
				}

				//reset modules
				$this->remove_module('payment_gateways', $row['module_folder']);

				$this->init->db_trans('trans_commit');
			}

			$msg = $total . ' ' . lang('invoices_paid_successfully');

			array_push($this->msg_text, array('cron_job' => __FUNCTION__,
			                                  'msg_text' => $msg));

			$this->dbv->rec(array('method' => __METHOD__, 'msg' => $msg));

			return TRUE;
		}
	}

	// ------------------------------------------------------------------------

	public function generate_rewards()
	{
		if (config_enabled('affiliate_marketing'))
		{
			$rules = $this->comm_rules->get_comm_rules();

			if (!empty($rules))
			{
				//get all affiliates
				$affs = $this->aff->get_active_affiliates('member_id');

				if (!empty($affs))
				{
					$total = 0;

					foreach ($affs as $aff)
					{
						$this->init->db_trans('trans_begin');

						$row = $this->comm_rules->process_cron_rules($rules, $aff);

						if (!empty($row['success']))
						{
							$total++;
							$this->init->db_trans('trans_commit');
						}
						else
						{
							$this->init->db_trans('trans_rollback');

						}}

					$msg = $total . ' ' . lang('affiliate_rewards_generated_successfully');

					array_push($this->msg_text, array('cron_job' => __FUNCTION__,
					                                  'msg_text' => $msg));

					$this->dbv->rec(array('method' => __METHOD__, 'msg' => $msg));
				}
			}
		}

		//run loyalty rewards
		$rows = $this->mem->get_user_birthdays();

		$total = 0;

		if (!empty($rows))
		{
			foreach ($rows as $v)
			{
				$this->init->db_trans('trans_begin');

				$this->rewards->add_reward_points($v['member_id'], 'reward_user_birthday');

				$this->init->db_trans('trans_commit');

				$total++;
			}

			$msg = $total . ' ' . lang('birthday_rewards_generated_successfully');

			array_push($this->msg_text, array('cron_job' => __FUNCTION__,
			                                  'msg_text' => $msg));

			$this->dbv->rec(array('method' => __METHOD__, 'msg' => $msg));
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	public function optimize_tables()
	{
		$this->load->dbutil();

		$tables = $this->db->list_tables();

		$total = 0;

		foreach ($tables as $table)
		{
			$this->dbutil->optimize_table($table);

			$total++;
		}

		$msg = $total . ' ' . lang('tables_optimized_successfully');

		$this->dbv->rec(array('method' => __METHOD__, 'msg' => $msg));

		array_push($this->msg_text, array('cron_job' => __FUNCTION__,
		                                  'msg_text' => $msg));

		return TRUE;
	}

	// ------------------------------------------------------------------------

	public function prune_affiliate_traffic()
	{
		$this->init->db_trans('trans_begin');

		$row = $this->cron->prune_table(TBL_AFFILIATE_TRAFFIC, config_item('sts_tracking_auto_prune_days'), 'date');

		if (!empty($row['success']))
		{
			array_push($this->msg_text, array('cron_job' => __FUNCTION__,
			                                  'msg_text' => $row['msg_text']));

			$this->dbv->rec(array('method' => __METHOD__, 'msg' => $row['msg_text']));

			$this->init->db_trans('trans_commit');

			return TRUE;
		}

		$this->init->db_trans('trans_rollback');
	}

	// ------------------------------------------------------------------------

	public function prune_email_archive()
	{
		$this->init->db_trans('trans_begin');

		$row = $this->cron->prune_table(TBL_EMAIL_ARCHIVE, config_item('sts_email_auto_prune_archive'), 'send_date');

		if (!empty($row['success']))
		{
			array_push($this->msg_text, array('cron_job' => __FUNCTION__,
			                                  'msg_text' => $row['msg_text']));

			$this->dbv->rec(array('method' => __METHOD__, 'msg' => $row['msg_text']));

			$this->init->db_trans('trans_commit');

			return TRUE;
		}

		$this->init->db_trans('trans_rollback');
	}

	// ------------------------------------------------------------------------

	public function prune_transaction_log()
	{
		$this->init->db_trans('trans_begin');

		$row = $this->cron->prune_table(TBL_TRANSACTIONS, config_item('sts_cron_transaction_log_interval'));

		if (!empty($row['success']))
		{
			array_push($this->msg_text, array('cron_job' => __FUNCTION__,
			                                  'msg_text' => $row['msg_text']));

			$this->dbv->rec(array('method' => __METHOD__, 'msg' => $row['msg_text']));

			$this->init->db_trans('trans_commit');

			return TRUE;
		}

		$this->init->db_trans('trans_rollback');
	}

	// ------------------------------------------------------------------------

	public function schedule_follow_ups()
	{
		$rows = $this->follow_up->get_member_follow_ups();

		$total = 0;

		if (!empty($rows))
		{
			$follow_ups = $this->dbv->get_all(TBL_EMAIL_FOLLOW_UPS, 'list_id', 'ASC');

			foreach ($rows as $k => $v)
			{
				if (!empty($v['subject']) && !empty($v['html_body']))
				{
					$this->init->db_trans('trans_begin');

					//add the email to the queue
					if ($this->mail->queue($this->mail->prepare_template($v, $v)))
					{
						$this->follow_up->update_member_list_sequence($v, $follow_ups);

						$this->init->db_trans('trans_commit');

						$total++;
					}
					else
					{
						$this->init->db_trans('trans_rollback');
					}
				}
			}
		}

		$msg = $total . ' ' . lang('follow_up_emails_queued_successfully');

		array_push($this->msg_text, array('cron_job' => __FUNCTION__,
		                                  'msg_text' => $msg));

		if ($total > 0)
		{
			$this->dbv->rec(array('method' => __METHOD__, 'msg' => $msg));
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	public function send_email()
	{
		$sent = 0;

		$row = $this->mail->get_queued_emails('0', config_option('sts_email_limit_mass_mailing'));

		if (!empty($row))
		{
			foreach ($row as $k)
			{
				$this->init->db_trans('trans_begin');

				//send email
				if ($this->mail->send($k, $k['primary_email']))
				{
					if ($this->dbv->delete(TBL_EMAIL_QUEUE, 'id', $k['id']))
					{
						$this->init->db_trans('trans_commit');
					}

					$sent++;
				}

				$this->init->db_trans('trans_rollback');
			}
		}

		$msg = $sent . ' ' . lang('emails_sent_successfully');

		array_push($this->msg_text, array('cron_job' => __FUNCTION__,
		                                  'msg_text' => $msg));

		if ($sent > 0)
		{
			$this->dbv->rec(array('method' => __METHOD__, 'msg' => $msg));
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	protected function pay_invoice()
	{
		//set the order_data array
		$p = $this->data['order_data'];

		if (!empty($p['transaction']))
		{
			$p['payment'] = $this->invoices->create_payment($p, $p['invoice']['invoice_id']);

			//mark orders as paid
			if (!empty($p['payment']['paid']) && !empty($p['invoice']['order_id']))
			{
				$this->orders->mark_paid($p['invoice']['order_id']);
			}

			//COMMISSIONS
			//generate commission for all downline members if any
			if (config_enabled('affiliate_marketing') && !empty($p['affiliate']))
			{
				$p['commissions'] = $this->comm->generate_commissions($p, 'cron');
			}
		}

		//SEND OUT EMAILS

		//to customer if order alert emails are enabled
		$this->mail->send_customer_order_emails($p, 'invoice');

		//to all admins who have the alert enabled
		$this->mail->send_admin_order_emails($p, 'invoice');

		//run modules
		$this->done(__METHOD__, $p);

		return $p;
	}
}

/* End of file Cron.php */
/* Location: ./application/controllers/Cron.php */
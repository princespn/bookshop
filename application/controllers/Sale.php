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

class Sale extends Public_Controller
{
    protected $data;

	// ------------------------------------------------------------------------

    public function __construct()
    {
        parent::__construct();

        $this->load->model('affiliate_commissions_model', 'comm');
	    $this->load->model('affiliate_commission_rules_model', 'comm_rules');

        log_message('debug', __CLASS__ . ' Class Initialized');

	    $this->data = $this->init->initialize('site');

	    if (!config_enabled('sts_affiliate_track_external_sites')) exit;
    }

	// ------------------------------------------------------------------------

	/**
	 * Amount
	 */
	public function generate()
	{
		//https://www.domain.com/sale/generate/sub_total/100/trans_id/TEST/key/2ks92ks

		//check for affiliate data
		if (config_enabled('affiliate_marketing') && config_item('sts_affiliate_track_site_key'))
		{
			$data = $this->uri->uri_to_assoc(3);

			if (empty($data['key']) || $data['key'] != config_item('sts_affiliate_track_site_key'))
			{
				show_error(lang('invalid_key'), 500);
			}

			//generate commission for all downline members if any
			if (config_item('affiliate_data') && !empty($data['sub_total']) && !empty($data['trans_id']))
			{
				if (!$this->comm->check_trans_id($data['trans_id']))
				{
					$data['affiliate'] = config_item('affiliate_data');

					$commissions = $this->comm->generate_commissions($data, 'external');

					if (!empty($commissions))
					{
						$send_email = array('alert_pending', 'alert_unpaid');
						$admins = get_admins();

						if (in_array(config_item('sts_affiliate_new_commission'), $send_email))
						{
							foreach ($commissions as $c)
							{
								if (!empty($c['is_affiliate']) && !empty($c['alert_new_commission']))
								{
									$comm = format_checkout_email('commission', $c);

									$this->mail->send_template(EMAIL_MEMBER_AFFILIATE_COMMISSION, $comm, TRUE, sess('default_lang_id'), $c['primary_email']);
								}
							}

							if ($admins = get_admins()) //get active admins to send alerts to.
							{
								foreach ($admins as $a)
								{
									if (!empty($a['alert_affiliate_commission']))
									{
										foreach ($commissions as $c)
										{
											$comm = format_checkout_email('commission', $c, 'admin');

											$this->mail->send_template(EMAIL_ADMIN_AFFILIATE_COMMISSION, $comm, TRUE, sess('default_lang_id'), $a['primary_email']);
										}
									}
								}
							}
						}
					}

					$this->dbv->rec(array('method' => __METHOD__, 'msg' => lang('commissions_generated_successfully'), 'vars' => $commissions));
				}
				else
				{
					show_error(lang('unique_transaction_id_required'));
				}
			}
			else
			{
				show_error(lang('amount_and_transaction_id_required'));
			}
		}
	}
}

/* End of file Sale.php */
/* Location: ./application/controllers/Sale.php */
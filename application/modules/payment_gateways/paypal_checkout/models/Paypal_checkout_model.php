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

use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use PayPal\Api\Payer;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\WebProfile;
use PayPal\Api\InputFields;
use PayPal\Api\Patch;
use PayPal\Api\PatchRequest;
use PayPal\Api\Capture;
use PayPal\Api\Refund;
use PayPal\Api\RefundRequest;
use PayPal\Api\Agreement;
use PayPal\Api\Plan;
use PayPal\Api\ChargeModel;
use PayPal\Api\Currency;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\PaymentDefinition;
use PayPal\Common\PayPalModel;


class Paypal_checkout_model extends Modules_model
{
	private $apiKeys;

	public function __construct()
	{
		parent::__construct();
	}

	public function install($id = '')
	{
		$config = array(
			'settings_key'        => 'module_payment_gateways_paypal_checkout_title',
			'settings_value'      => 'Pay via Paypal',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '1',
			'settings_function'   => 'required',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_paypal_checkout_description',
			'settings_value'      => 'Make your payment via Paypal checkout Payments',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'textarea',
			'settings_group'      => $id,
			'settings_sort_order' => '2',
			'settings_function'   => 'required',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_paypal_checkout_paypal_email',
			'settings_value'      => '',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '3',
			'settings_function'   => 'required|valid_email',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_paypal_checkout_client_id',
			'settings_value'      => '',
			'settings_module'     => 'payment_gateways',
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
			'settings_key'        => 'module_payment_gateways_paypal_checkout_secret',
			'settings_value'      => '',
			'settings_module'     => 'payment_gateways',
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
			'settings_key'        => 'module_payment_gateways_paypal_checkout_currency_code',
			'settings_value'      => 'USD',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '6',
			'settings_function'   => '',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_payment_gateways_paypal_checkout_checkout_logo',
			'settings_value'      => '',
			'settings_module'     => 'payment_gateways',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '7',
			'settings_function'   => 'image_manager',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return array(
			'id'       => $id,
			'success'  => TRUE,
			'msg_text' => lang('module_installed_successfully'),
		);
	}

	public function uninstall($id = '')
	{
		//remove settings from database
		$this->mod->remove_config($id, 'payment_gateways');

		return array(
			'success'  => TRUE,
			'msg_text' => lang('module_uninstalled_successfully'),
		);
	}

	public function generate_payment($data = array(), $module = array(), $type = 'checkout')
	{
		$this->init();

		if ($this->input->get('success') == TRUE)
		{
			$paymentId = $this->input->get('paymentId');
			$payment = Payment::get($paymentId, $this->apiKeys);

			$execution = new PaymentExecution();
			$execution->setPayerId($this->input->get('PayerID'));

			try
			{
				$result = $payment->execute($execution, $this->apiKeys);
			} catch (Exception $e)
			{
				show_error($e->getMessage());

				return array('type'       => 'error',
				             'msg_text'   => $e->getMessage(),
				             'debug_info' => '',
				);
			}

			$transactions = $payment->getTransactions();
			$related_resources = $transactions[0]->getRelatedResources();
			$sale = $related_resources[0]->getSale();

			$vars = array(
				'type'           => 'success',
				'msg_text'       => 'payment_generated_successfully',
				'module'         => $module['module']['module_folder'],
				'description'    => $module['module']['module_description'],
				'status'         => '1',
				'transaction_id' => $sale->getId(),
				'amount'         => $sale->getAmount()->getTotal(),
				'fee'            => $sale->getTransactionFee()->getValue(),
				'currency_code'  => check_currency('module_payment_gateways_paypal_checkout_currency_code'),
				'debug'          => serialize($sale),
				'card_data'      => '',

			);

			return $vars;
		}

		return array('type'     => 'error',
		             'msg_text' => lang('invalid_gateway_access'),
		);
	}

	public function generate_gateway_form($data = array(), $amount = '0', $type = 'checkout', $cart = array())
	{
		$this->init();

		$payer = new Payer();
		$payer->setPaymentMethod("paypal");

		$total = $type == 'invoice' ? $data['total'] : $cart['totals']['total_with_shipping'];

		$webProfile = new WebProfile();
		$webProfile->setName(uniqid());
		$inputFields = new InputFields();
		$inputFields->setNoShipping(1);
		$webProfile->setInputFields($inputFields);

		$createProfileResponse = $webProfile->create($this->apiKeys);
		$createProfileResponse = json_decode($createProfileResponse);
		$web_profile_id = $createProfileResponse->id;

		$amount = new Amount();
		$amount->setCurrency(check_currency('module_payment_gateways_paypal_checkout_currency_code'))
			->setTotal($total);

		$transaction = new Transaction();
		$transaction->setAmount($amount)
			->setDescription(format_amount($total, FALSE, FALSE) . ' ' . check_currency('module_payment_gateways_paypal_checkout_currency_code') . ' '  . lang('payment'))
			->setInvoiceNumber(uniqid());

		$redirectUrls = new RedirectUrls();
		$page = $type == 'invoice' ? 'invoice/pay/paypal_checkout' : 'payment/pay';
		$redirectUrls->setReturnUrl(ssl_url('checkout/' . $page . '?success=TRUE'))
			->setCancelUrl(ssl_url('checkout'));

		$payment = new Payment();
		$payment->setIntent("sale")
			->setPayer($payer)
			->setRedirectUrls($redirectUrls)
			->setExperienceProfileId($web_profile_id)
			->setTransactions(array($transaction));
		try
		{
			$payment->create($this->apiKeys);
		} catch (Exception $e)
		{
			$this->dbv->rec(array('method' => __METHOD__, 'msg' => $e->getMessage(), 'level' => 'error'));

			return FALSE;
		}

		$url = $payment->getApprovalLink();

		return $url;
	}

	public function process_refund($data = array())
	{
		$this->init();

		$amount = new Amount();
		$amount->setCurrency(check_currency('module_payment_gateways_paypal_checkout_currency_code'))
			->setTotal($data['refund_amount']);
		$refundRequest = new RefundRequest();
		$refundRequest->setAmount($amount);

		try
		{
			$capture = Capture::get($data['transaction_id'], $this->apiKeys);
			$captureRefund = $capture->refundCapturedPayment($refundRequest, $this->apiKeys);
		} catch (Exception $e)
		{

			$vars['error'] = TRUE;
			$vars['msg_text'] = $e->getMessage();
			$vars['data']['debug_info'] = $e;
		}

		$vars = array('success' => TRUE,
		              'data'    => $data);

		$vars['data']['date'] = get_time(now(), TRUE);
		$vars['data']['refund_amount'] = $captureRefund->getAmount()->getTotal();
		$vars['data']['fee'] = $captureRefund->getRefundFromTransactionFee()->getValue();
		$vars['data']['transaction_id'] = $captureRefund->getId();
		$vars['data']['description'] = lang('refund') . ' - paypal_checkout';
		$vars['data']['debug_info'] = base64_encode(serialize($captureRefund));

		return !empty($vars) ? $vars : FALSE;
	}

	public function update_module($data = array())
	{
		//update module data
		$row = $this->mod->update($data);

		return $row;
	}

	public function validate_payment_module($data = array(), $table = '')
	{
		$row = $this->pay->validate_module($data, $table);

		return $row;
	}

	private function init()
	{
		$this->apiKeys = new ApiContext(
			new OAuthTokenCredential(
				config_item('module_payment_gateways_paypal_checkout_client_id'),
				config_item('module_payment_gateways_paypal_checkout_secret')
			)
		);
	}
}

/* End of file Paypal_checkout_model.php */
/* Location: ./application/models/Paypal_checkout_model.php */
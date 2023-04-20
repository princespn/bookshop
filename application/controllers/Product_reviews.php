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
class Product_reviews extends Product_Controller
{
    public $data;

    public function __construct()
    {
        parent::__construct();

	    $this->data = $this->init->initialize('site');

	    log_message('debug', __CLASS__ . ' Class Initialized');

	    if (!$this->config->item('sts_products_enable_reviews'))
	    {
	    	redirect();
	    }
    }

	// ------------------------------------------------------------------------

	public function view()
    {
        //set the product_id
        $this->data['id'] = (int)uri(3);

        if (!$row = $this->prod->get_details($this->data['id'], sess('default_lang_id'), TRUE, FALSE))
        {
            $this->show->page('404', $this->data);
        }
        else
        {
            //map product details
            $this->data['p'] = format_products($row, sess('default_lang_id'));

            //set the custom offset
            $opt = array('offset' => (int)uri(4, 0),
                'session_per_page' => ! sess('rows_per_page') ? $this->data['layout_design_products_per_page'] : sess('rows_per_page')
            );

            //get the reviews
            $rows = $this->rev->get_rows(query_options($opt), $this->data['id'], TRUE);

            //set the array
            $this->data['reviews'] = $rows['values'];

            //check for pagination
            if (!empty($rows['total']))
            {
                $this->data['page_options'] = array(
                    'uri' => $this->data['uri'],
                    'total_rows' => $rows['total'],
                    'per_page' => $opt['session_per_page'],
                    'segment' => $this->data['site_db_segment'],
                );

                $this->data['paginate'] = $this->paginate->generate($this->data['page_options'], CONTROLLER_CLASS);
                $this->data['next_scroll'] = check_infinite_scroll($this->data);
            }

            //set breadcrumbs
            $this->data['breadcrumb'] = set_breadcrumb(array(lang('store') => 'store',
                $row['product_name'] => page_url('product', $row, TRUE),
                'reviews' => '',
            ));

            //set the default listing template
            $tpl = $this->input->get('q') == 'ajax' ? '_ajax' : '';
            $this->show->display('product', 'product_reviews' . $tpl, $this->data);
        }
    }

	// ------------------------------------------------------------------------

	public function add()
    {
	    //check if the user is logged in
	    check_login('login?redirect=' . urlencode($this->uri->uri_string()));

        //set the product_id
        $this->data['id'] = (int)uri(3);

	    if (!$this->data['p'] = $this->prod->get_details($this->data['id'], sess('default_lang_id'), TRUE, FALSE))
	    {
		    $this->show->page('404', $this->data);
	    }

        if ($this->input->post())
        {
            //check if the form submitted is correct
            $row = $this->rev->validate( $this->input->post(NULL, true), 'member');

            if (!empty($row['success']))
            {
	            $row = $this->rev->create($row[ 'data' ]);

	            //generate rewards
	            if (sess('member_id'))
	            {
		            $this->rewards->add_reward_points(sess('member_id'), 'reward_product_review');
	            }

	            //log it!
	            $this->dbv->rec(array('method' => __METHOD__, 'msg' => $row['msg_text']));

	            //send email
	            $vars = format_product_email('product_review', array_merge($row['data'], $this->data['p'], $_SESSION));

	            $this->mail->send_template(EMAIL_ADMIN_ALERT_PRODUCT_REVIEW_TEMPLATE, $vars, FALSE, sess('default_lang_id'), sess('primary_email'));

	            //set the default response
	            $response = array('type'     => 'success',
	                              'msg'          => lang('review_submitted_successfully'),
	                              'redirect' => site_url($this->uri->uri_string()),
	            );
            }
            else
            {
	            $response = array('type'         => 'error',
	                              'error_fields' => $row['error_fields'],
	                              'msg'          => $row['msg_text'],
	            );
            }

	        ajax_response($response);
        }

			//set breadcrumbs
		    $this->data['breadcrumb'] = set_breadcrumb(array(lang('store') => 'store',
		                                                     $this->data['p']['product_name'] => page_url('product', $this->data['p'], TRUE),
		                                                     'add_review' => '',
		    ));


		    $this->show->display('product', 'product_add_review', $this->data);

    }
}

/* End of file Product_reviews.php */
/* Location: ./application/controllers/Product_reviews.php */
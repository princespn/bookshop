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
class Form extends Public_Controller
{
    protected $data;

    public function __construct()
    {
        parent::__construct();

        $this->load->model('forms_model', 'form');
		$this->load->model('email_mailing_lists_model','lists');

	    $this->data = $this->init->initialize('site');

        log_message('debug', __CLASS__ . ' Class Initialized');
    }

	// ------------------------------------------------------------------------

	public function id()
    {
	    $this->data['id'] = valid_id(uri(3));

	    //get form details
	    $this->data['form'] = $this->form->get_details($this->data['id']);

       //show custom forms
	    $this->data['fields'] = $this->form->get_form_fields($this->data['id'], sess('default_lang_id'), '', TRUE);

	    //set breadcrumbs
	    $this->data[ 'breadcrumb' ] = set_breadcrumb(array($this->data['form']['form_name'] =>''));

	    if ($this->input->post())
	    {
		    $row = $this->form->validate_fields('form', $this->input->post(), $this->data['fields'][ 'values' ]);

		    if (!empty($row['success']))
		    {
		    	//send to email
			    if ($this->data['form']['form_processor'])
			    {
			    	$form = $this->data['form'];
			    	$form['fields'] =  format_custom_form_fields($row['data']);
				    $url = !empty($form['redirect_url']) ? $form['redirect_url'] : $this->uri->uri_string();

			    	switch ($form['form_processor'])
				    {
					    case 'email':

						    //send the custom email template
						    $this->mail->send_template(EMAIL_ADMIN_SEND_CUSTOM_FORM, $form, FALSE, sess('default_lang_id'), $form['function']);

					    	break;

					    case 'page':

					    	if ($form['form_method'] == 'POST')
						    {
							    $row = use_curl($form['function'], $form['fields']);
						    }

						    $url .= '?' . http_build_query($form['fields']);

					    	break;
				    }

				    //add to list if needed
				    if ($this->data['form']['list_id'] && !empty($form['fields']['primary_email']))
				    {
					    $this->update_list('add_user', $this->data['form']['list_id'],$form['fields']['primary_email'], $form['fields'] , sess('default_lang_id'));
				    }

			    }

			    $this->done(__METHOD__, $row);

			    redirect_flashdata($url, 'form_submitted_successfully');
		    }
		    else
		    {
			    $this->data['error'] =  validation_errors();
		    }
	    }

        $this->show->display(CONTROLLER_CLASS,  'custom_form', $this->data);
    }

	// ------------------------------------------------------------------------

	public function contact()
    {
		//first lets get the form fields
	    $this->data[ 'fields' ] = $this->form->get_form_fields(3, sess('default_lang_id'), '', TRUE);

	    if ($this->input->post())
	    {
		    $row = $this->form->validate_fields('contact', $this->input->post(), $this->data[ 'fields' ][ 'values' ]);

		    if (!empty($row['success']))
		    {
			    //send the contact email template
			    $this->mail->send_template(EMAIL_ADMIN_ALERT_CONTACT_US, $row['data'], FALSE, sess('default_lang_id'), config_item('sts_email_contact_email'));

			    $row['msg_text'] = lang('form_submitted_successfully');

			    $this->done(__METHOD__, $row);

			    //redirect
			    redirect_flashdata($this->uri->uri_string(), $row['msg_text']);
		     }
		     else
		     {
			     $this->data['error'] =  validation_errors();
		     }
	    }

	    $this->show->display(CONTROLLER_CLASS, 'contact', $this->data);
    }

	// ------------------------------------------------------------------------

	public function addresses()
	{
		$this->data['addresses'] = $this->set->get_site_addresses();

		if (count($this->data['addresses']) > 1)
		{
			$this->show->display(CONTROLLER_CLASS, 'locations', $this->data);
		}
		else
		{
			redirect('contact');
		}
	}
}

/* End of file Form.php */
/* Location: ./application/controllers/Form.php */
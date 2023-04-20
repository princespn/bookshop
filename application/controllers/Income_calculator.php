<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| COPYRIGHT NOTICE                                                     
| Copyright 2010 JROX Technologies, Inc.  All Rights Reserved.    
| -------------------------------------------------------------------------                                                                        
| This script may be only used and modified in accordance to the license      
| agreement attached (license.txt) except where expressly noted within      
| commented areas of the code body. This copyright notice and the  
| comments above and below must remain intact at all times.  By using this 
| code you agree to indemnify JROX Technologies, Inc, its corporate agents   
| and affiliates from any liability that might arise from its use.                                                        
|                                                                           
| Selling the code for this program without prior written consent is       
| expressly forbidden and in violation of Domestic and International 
| copyright laws.  
|	
| -------------------------------------------------------------------------
| FILENAME - error.php
| -------------------------------------------------------------------------     
| 
| This controller file is used to show 404 errors
|
*/


class Income_Calculator extends Public_Controller {

	protected $data;

    public function __construct()
    {
        parent::__construct();

        $this->load->model('init_model', 'init');
		$this->load->model('members_model','members');

		$this->load->helper('country');
		

	    $this->data = $this->init->initialize('site');

        log_message('debug', __CLASS__ . ' Class Initialized');
    }



	function view()
	{
		$this->show->display('support', 'ganaincome_calculator', $this->data);	
					
	
	}

	function viewus()
	{
		$this->show->display('support', 'usincome_calculator', $this->data);	
				
	}
	
	
	// ------------------------------------------------------------------------
	
	
}

?>
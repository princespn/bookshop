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
class Site_map extends Public_Controller
{
    protected $data;

    public function __construct()
    {
        parent::__construct();

        $this->load->model(__CLASS__ . '_model', 'sitemap');

        log_message('debug', __CLASS__ . ' Class Initialized');

	    $this->data = $this->init->initialize('site');
    }

	/**
	 * index file
	 */
	public function index()
	{
		$this->show->display(CONTROLLER_CLASS, 'site_map', $this->data);
	}

    public function site_map_index()
    {
	    if (!config_enabled('sts_content_auto_generate_xml_sitemap')) exit;

	    $this->output->set_content_type('text/xml');

	    //load the template
	    $this->show->display(CONTROLLER_CLASS, 'site_map_index', $this->data);
    }

	// ------------------------------------------------------------------------

	public function id()
	{
		if (!config_enabled('sts_content_auto_generate_xml_sitemap')) exit;

		$this->output->set_content_type('text/xml');

		$this->data['type'] = str_replace('.xml', '', uri(3));

		$this->data['rows'] = $this->sitemap->generate_site_links($this->data['type'], sess('default_lang_id'));

		//load the template
		$this->show->display(CONTROLLER_CLASS, 'site_map_xml', $this->data);
	}
}

/* End of file Sitemap.php */
/* Location: ./application/controllers/Sitemap.php */
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
class Pagination_model extends CI_Model
{
	/**
	 * Pagination_model constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param string $id
	 * @param string $type
	 * @param string $class
	 * @return mixed
	 */
	public function generate($options = '', $id = '', $type = '', $class = DEFAULT_PAGINATION_CSS_SIZE)
	{
		$this->load->library('pagination');

		$config = array(
			'base_url'           => $options[ 'uri' ],
			'total_rows'         => $options[ 'total_rows' ],
			'per_page'           => $options[ 'per_page' ],
			'num_links'          => $type == 'admin' ? $this->config->item('admin_pagination_links') : $this->config->item('member_pagination_links'),
			'uri_segment'        => $options[ 'segment' ],
			'full_tag_open'      =>  $type == 'admin' ? '<div id="' . $id . '_pagination"><ul class="text-capitalize pagination ' . $class . '">' : '',
			'full_tag_close'     => $type == 'admin' ? '</ul></div>' : '',
			'num_tag_open'       => '<li class="page-item">',
			'num_tag_close'      => '</li>',
			'cur_tag_open'       => '<li class="page-item active"><span class="page-link">',
			'cur_tag_close'      => '</span></li>',
			'prev_tag_open'      => '<li class="page-item">',
			'prev_tag_close'     => '</li>',
			'next_tag_open'      => '<li class="page-item next">',
			'next_tag_close'     => '</li>',
			'first_tag_open'     => '<li class="page-item">',
			'first_tag_close'    => '</li>',
			'last_tag_open'      => '<li class="page-item">',
			'last_tag_close'     => '</li>',
			'first_link'         => !empty($options[ 'first_link' ]) ? $options[ 'first_link' ] : '<<',
			'last_link'          => !empty($options[ 'last_link' ]) ? $options[ 'last_link' ] : '>>',
			'next_link'          => !empty($options[ 'next_link' ]) ? $options[ 'next_link' ] : '>',
			'prev_link'          => !empty($options[ 'prev_link' ]) ? $options[ 'prev_link' ] : '<',
			'reuse_query_string' => TRUE,
			'attributes'         => array('class' => 'page-link'),
        );

        $this->pagination->initialize($config);

        $row[ 'rows' ] = $this->pagination->create_links();

        $config[ 'display_pages' ] = FALSE;
        $this->pagination->initialize($config);

        $row[ 'no_pages' ] = $this->pagination->create_links();

        $row[ 'num_pages' ] = ceil($config[ 'total_rows' ] / $config[ 'per_page' ]);

        $q = base64_encode(urlencode($this->input->server('REQUEST_URI')));

        $base = $type == 'admin' ? ADMIN_ROUTE : $type;

        $page_array = array();
        foreach ($this->config->item('db_select_page_rows') as $n)
        {
	        array_push($page_array, anchor($base . '/update_session/rows/' . $n . '/' . $q, $n . ' ' . lang('rows_per_page')));
        }

        $row[ 'select_rows' ] = ul($page_array, 'class="dropdown-menu" role="menu"');
        $row[ 'segment' ] = $options[ 'segment' ];
        $row[ 'prev_next' ] = $this->pagination->next_buttons($row[ 'num_pages' ]);

        return $row;
    }
}
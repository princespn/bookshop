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
class Member_month_click_stats_model extends Reports_model
{
	/**
	 * Install module
	 *
	 * Install the module and add any config items into the
	 * settings table
	 *
	 * @return array
	 */
	public function install()
	{
		return array(
			'success'  => TRUE,
			'msg_text' => lang('module_installed_successfully'),
		);
	}

	/**
	 * Uninstall module
	 *
	 * Uninstall the module and remove any config items from
	 * the settings table
	 *
	 * @param string $id
	 * @return array
	 */
	public function uninstall($id = '')
	{
		//remove settings from database
		$this->mod->remove_config($id, 'member_reporting');

		return array(
			'success'  => TRUE,
			'msg_text' => lang('module_uninstalled_successfully'),
		);
	}

	/**
	 * Generate module
	 *
	 * Generate the needed details for the report including
	 * charts and table data
	 *
	 * @param array $data
	 * @param string $member_id
	 * @return array|bool
	 */
	public function generate_module($data = array(), $member_id = '')
	{
		//generate the data
		$days = days_in_month(current_date('m'), current_date('Y'));

		//run the SQL query for the data
		$rows = $this->run_query($data, $days, $member_id);

		//generate the data for html and charts
		$data['results'] = $this->init_chart($rows, $days);

		$row = array('title'         => $this->config->item('module_title'), //title of the report
		             'template'      => $this->config->item('module_template'), //template to use for reporting
		             'rows'          => $data['results']['data'], //data array
		             'rendered_html' => render_template($data, 'members'), //rendered html for chart
		             'dates'         => generate_month_dropdown(), //dropdown for selecting different months
		             'currency'      => FALSE //use currency when showing amounts on the report
		);

		return empty($row) ? FALSE : $row;
	}

	/**
	 * Initialize chart
	 *
	 * Initialize and generate the chart data and html
	 *
	 * @param array $data
	 * @param string $days
	 * @return array
	 */
	public function init_chart($data = array(), $days = '')
	{
		$html = $this->init_data($data, $days);

		$html['title'] = lang('clicks_generated_per_day');
		$html['x_axis'] = lang('amount');

		// $html['graph_type'] = 'HighRollerAreaChart'; //set the type of chart to use

		return $this->generate_chart($html);
	}


	/**
	 * Run Query
	 *
	 * Run the database query for this specific report
	 *
	 * @param array $data
	 * @param string $days
	 * @param string $member_id
	 * @return bool
	 */
	protected function run_query($data = array(), $days = '', $member_id = '')
	{
		$sql = 'SELECT DAY( date ) AS day,
                  COUNT( traffic_id ) AS amount
                  FROM ' . $this->db->dbprefix(TBL_AFFILIATE_TRAFFIC) . '
                  WHERE date BETWEEN \'' . current_date('Y') . '-' . current_date('m') . '-01 00:00:00\'
                    AND \'' . current_date('Y') . '-' . current_date('m') . '-' . $days . ' 23:59:59\'
                    AND member_id = \'' . (int)$member_id . '\'
                    GROUP BY day';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? $q->result_array() : FALSE;
	}
}

/* End of file Member_month_click_stats_model.php */
/* Location: ./application/modules/member_reporting/Month_click_stats/models/Member_month_click_stats_model.php */
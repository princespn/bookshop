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
class Reports_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'report_id';

	// ------------------------------------------------------------------------

	/**
	 * @param string $name
	 * @param string $html
	 * @return mixed
	 */
	public function archive_report($name = '', $html = '')
	{
		$row = $this->dbv->create(TBL_REPORT_ARCHIVE, array('report_name' => $name,
		                                                    'report_html' => $html));

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $limit
	 * @param string $key
	 * @param string $field
	 * @return array
	 */
	public function init_chart_data($data = array(), $limit = '0', $key = 'day', $field = 'amount')
	{
		$row = array();
		for ($i = 1; $i <= $limit; $i++)
		{
			$a = array('day' => $i, 'amount' => '0');
			if (!empty($data))
			{
				foreach ($data as $v)
				{
					if ($v['day'] == $i)
					{
						$a = array(
							$key => (int)$i,
							$field => (float)$v['amount']);
					}
				}
			}

			$row[] = $a;
		}

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * Initialize report data
	 *
	 * Initialize and format the data for charts and
	 * table reporting to have correct days / month
	 *
	 * @param array $data
	 * @param $days
	 * @return mixed
	 */
	public function init_data($data = array(), $limit = '0', $type = 'stats', $dates = array())
	{
		//generate data for the charts
		$row['data'] = array();

		switch ($type)
		{
			case 'top':

				for ($i = 1; $i <= $limit; $i++)
				{
					if (!empty($data))
					{
						foreach ($data as $v)
						{
							$row['data'][ $v['name'] ] = $v['amount'];
						}
					}
				}

				//format the keys and values for charting
				$row['keys'] = array();
				$row['values'] = array();

				foreach ($row['data'] as $k => $v)
				{
					array_push($row['keys'], $k);
					array_push($row['values'], (float)($v));
				}

				break;

			case 'dates': //custom start and end dates

				$range = date_range($dates['start'], $dates['end'], 'M d');

				foreach ($range as $k => $v)
				{
					list($m, $d) = explode('-', $k);
					$row['data'][ $v ] = 0;

					foreach ($data as $a)
					{
						if ($d == $a['day'])
						{
							$row['data'][ $v ] = format_amount($a['amount'], FALSE, FALSE, TRUE, TRUE);
						}
					}
				}

				//format the keys and values for charting
				$row['keys'] = array();
				$row['values'] = array();

				foreach ($row['data'] as $k => $v)
				{
					array_push($row['keys'], $k);
					array_push($row['values'], (float)($v));
				}

				break;

			default:

				for ($i = 1; $i <= $limit; $i++)
				{
					$row['data'][ $i ] = 0;
					if (!empty($data))
					{
						foreach ($data as $v)
						{
							if ($v['day'] == $i)
							{
								$row['data'][ $i ] = $v['amount'];
							}
						}
					}
				}

				//format the keys and values for charting
				$row['keys'] = array();
				$row['values'] = array();

				foreach ($row['data'] as $k => $v)
				{
					array_push($row['keys'], $k);
					array_push($row['values'], (float)($v));
				}

				break;
		}

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * Generate chart
	 *
	 * Generate the JavaScript and JSON data required for showing
	 * the data in graphical chart format
	 *
	 * @param array $data
	 * @param array $data2
	 * @return array
	 */
	protected function generate_chart($data = array(), $data2 = array(), $type = 'admin')
	{
		$graph = !empty($data['graph_type']) ? $data['graph_type'] : 'HighRollerColumnChart';

		//check if we have any custom back / foreground colors for the chart
		$color = get_chart_colors($type);

		//add all required libraries for highcharts / highroller
		require_once(APPPATH . 'libraries/highcharts/HighRoller.php');
		require_once(APPPATH . 'libraries/highcharts/HighRollerSeriesData.php');
		require_once(APPPATH . 'libraries/highcharts/' . $graph . '.php');

		$linechart = new $graph();

		//initialize these so we do't get any weird PHP errors
		$linechart->yAxis = new stdClass();
		$linechart->xAxis = new stdClass();
		$linechart->yAxis->title = new stdClass();
		$linechart->credits = new stdClass();
		$linechart->legend = new stdClass();
		$linechart->plotOptions = new stdClass();
		$linechart->plotOptions->area = new stdClass();
		$linechart->plotOptions->area->marker = new stdClass();
		$linechart->plotOptions->area->marker->enabled = new stdClass();

		$linechart->chart->renderTo = 'chart_report';
		$linechart->title->text = !empty($data['title']) ? $data['title'] : lang('total');
		$linechart->chart->backgroundColor = $color['bg_color'];
		$linechart->xAxis->categories = $data['keys'];
		$linechart->credits->enabled = FALSE;
		$linechart->plotOptions->area->marker->enabled = FALSE;

		$series1 = new HighRollerSeriesData();
		$series1->addName($data['x_axis'])->addColor($color['grid_color'])->addData($data['values']);

		$linechart->addSeries($series1);

		//check if we're adding a second chart series
		if (!empty($data2))
		{
			$series2 = new HighRollerSeriesData();
			$series2->addName($data2['x_axis'])->addColor($color['grid_color2'])->addData($data2['values']);

			$linechart->addSeries($series2);
		}

		//set the chart width if needed
		if (!empty($data['chart_width']))
		{
			$linechart->chart->width = $data['chart_width'];
		}

		//set the chart height if needed
		if (!empty($data['chart_height']))
		{
			$linechart->chart->height = $data['chart_height'];
		}

		//show it!
		$data['chart'] = $linechart->renderChart();

		return $data;
	}
}

/* End of file Reports_model.php */
/* Location: ./application/models/Reports_model.php */
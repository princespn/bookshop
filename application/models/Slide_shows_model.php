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
class Slide_shows_model extends CI_Model
{

	/**
	 * @var string
	 */
	protected $id = 'slide_id';

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $type
	 * @return false|string
	 */
	public function create($data = array(), $type = 'simple')
	{
		$vars = array('status'           => '0',
		              'type'             => $type,
		              'name'             => $type . ' ' . lang('slide_show'),
		              'start_date'       => date('Y-m-d', now() - (3600 * 24)),
		              'end_date'         => date('Y-m-d', now() + 31536000),
		              'background_color' => 'rgba(0, 0, 0, 0.3)',
		              'text_color'       => '#FFFFFF',
		              'action_url'       => site_url(),
		              'sort_order'       => '0',
		);

		if (!$q = $this->db->insert(TBL_SLIDE_SHOWS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$vars['slide_id'] = $this->db->insert_id();

		//now add an entry for each language
		foreach ($data as $v)
		{
			$vars = array(
				$this->id     => $vars['slide_id'],
				'language_id' => $v['language_id'],
				'headline'    => lang('welcome_to_our_site'),
				'button_text' => lang('click_here_for_more_info'),
				'slide_show'  => $type == 'simple' ? 'This Is Just an Example Text For The Simple Slideshow Option' : '<div class="container">
                            									<div class="row">
                                                                    <div class="col-12 slide-div-left">
                                        								<h1 class="slide-headline animated slideInDown" style="color: #ffffff">Sample Code For Slideshow</h1>
                                        								<div class="slide-description animated slideInLeft" style="color: #ffffff">
                                            								<p>This is an example code for the Advanced Slideshow Option</p>
                                                                            <p><a href="#" class="btn btn-lg btn-primary">Click For More Info</a></p>
                                                                        </div>
                                    								</div>
                                                            	</div>
                        									</div>',
			);

			if (!$q = $this->db->insert(TBL_SLIDE_SHOWS_NAME, $vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		return sc(array(
			'id'       => $vars['slide_id'],
			'success'  => TRUE,
			'data'     => $vars,
			'msg_text' => 'record_created_successfully',
		));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param int $lang_id
	 * @return bool|false|string
	 */
	public function get_rows($options = '', $lang_id = 1)
	{
		$sort = $this->config->item(TBL_SLIDE_SHOWS, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$sql = 'SELECT * FROM ' . $this->db->dbprefix(TBL_SLIDE_SHOWS) . ' p
				LEFT JOIN ' . $this->db->dbprefix(TBL_SLIDE_SHOWS_NAME) . ' c ON (p.' . $this->id . ' = c.' . $this->id . '
				AND c.language_id = \'' . $lang_id . '\')';

		if (!empty($options['query']))
		{
			$this->dbv->validate_columns(array(TBL_SLIDE_SHOWS, TBL_SLIDE_SHOWS_NAME), $options['query']);

			$sql .= $options['where_string'];
		}

		$sql .= ' ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'] . ' LIMIT ' . $options['offset'] . ', ' . $options['limit'];

		$query = $this->db->query($sql);


		if ($query->num_rows() > 0)
		{
			$row = array(
				'values'  => $query->result_array(),
				'total'   => $this->dbv->get_table_totals($options, TBL_SLIDE_SHOWS),
				'success' => TRUE,
			);

			return sc($row);
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param int $lang_id
	 * @param array $data
	 * @param bool $widget
	 * @return array|bool
	 */
	public function get_slideshows($lang_id = 1, $data = array(), $widget = FALSE)
	{
		$sql = 'SELECT * FROM ' . $this->db->dbprefix(TBL_SLIDE_SHOWS) . ' p
				LEFT JOIN ' . $this->db->dbprefix(TBL_SLIDE_SHOWS_NAME) . ' c
					ON (p.' . $this->id . ' = c.' . $this->id . '
					AND c.language_id = \'' . $lang_id . '\')
				WHERE p.status = \'1\'
					AND p.start_date <= ' . local_time('sql') . '
					AND p.end_date >= ' . local_time('sql') . '
				ORDER BY sort_order ASC';

		$q = $this->db->query($sql);

		if ($q->num_rows() > 0)
		{
			$rows = $q->result_array();

			$row = array(
				'meta_data'   => '',
				'footer_data' => '',
			);

			foreach ($rows as $k => $v)
			{
				$rows[$k]['slide_show'] = parse_string($v['slide_show'], $data);
				$row['meta_data'] .= $v['meta_data'];
				$row['footer_data'] .= $v['footer_data'];
			}

			$row['slide_shows'] = $rows;
		}

		return empty($row) ? FALSE : ($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param int $lang_id
	 * @param bool $public
	 * @return bool|false|string
	 */
	public function get_details($id = '', $lang_id = 1, $public = FALSE)
	{
		$sql = 'SELECT p.*, c.*,
					DATE_FORMAT(p.start_date,\'' . $this->config->item('sql_date_format') . '\')
                        AS start_date,
                    DATE_FORMAT(p.end_date,\'' . $this->config->item('sql_date_format') . '\')
                        AS end_date ';

		if ($public == FALSE)
		{
			$sql .= ', (SELECT ' . $this->id . '
				        FROM ' . $this->db->dbprefix(TBL_SLIDE_SHOWS) . ' p
				        WHERE p.' . $this->id . ' < ' . (int)$id . '
				        ORDER BY `' . $this->id . '` DESC LIMIT 1)
				        AS prev,
				    (SELECT ' . $this->id . '
				        FROM ' . $this->db->dbprefix(TBL_SLIDE_SHOWS) . ' p
				        WHERE p.' . $this->id . ' > ' . (int)$id . '
				        ORDER BY `' . $this->id . '` ASC LIMIT 1)
				        AS next';
		}

		$sql .= '   FROM ' . $this->db->dbprefix(TBL_SLIDE_SHOWS) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_SLIDE_SHOWS_NAME) . ' c
                        ON (p.' . $this->id . ' = c.' . $this->id . '
                        AND c.language_id = \'' . $lang_id . '\')
                    WHERE p.' . $this->id . ' = \'' . valid_id($id, TRUE) . '\'';

		if ($public == TRUE)
		{
			$sql .= ' AND p.status = \'1\' AND p.start_date >= ' . local_time() .
				'AND  p.end_date <= ' . local_time();
		}

		$cache = __METHOD__ . md5($sql);
		$cache_type = $public == TRUE ? 'public_db_query' : 'db_query';
		if (!$row = $this->init->cache($cache, $cache_type))
		{
			if (!$q = $this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = $q->row_array();

				if ($public == FALSE)
				{
					$row['lang'] = $this->dbv->get_names(TBL_SLIDE_SHOWS_NAME, $this->id, $id);
				}
			}

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, $cache_type);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|false|string
	 */
	public function mass_update($data = array())
	{
		if (!empty($data['slide_id']))
		{
			foreach ($data['slide_id'] as $v)
			{
				$vars['status'] = $data['change-status'];

				if (!$this->db->where($this->id, $v)->update(TBL_SLIDE_SHOWS, $vars))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}
			}
		}

		foreach ($data['sort_order'] as $k => $v)
		{
			$s['sort_order'] = $v;

			if (!$this->db->where($this->id, $k)->update(TBL_SLIDE_SHOWS, $s))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		$row = array('success'  => TRUE,
		             'data'     => $data,
		             'msg_text' => 'mass_update_successful',
		);

		//order the tier groups numerically
		$this->dbv->db_sort_order(TBL_SLIDE_SHOWS, 'slide_id', 'sort_order');

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function update($data = array())
	{
		$vars = $this->dbv->clean($data, TBL_SLIDE_SHOWS, FALSE);

		$this->db->where($this->id, $data[$this->id]);

		if (!$this->db->update(TBL_SLIDE_SHOWS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		foreach ($data['lang'] as $k => $v)
		{
			$vars = $this->dbv->clean($v, TBL_SLIDE_SHOWS_NAME);

			$this->db->where($this->id, $data[$this->id]);
			$this->db->where('language_id', $k);

			if (!$this->db->update(TBL_SLIDE_SHOWS_NAME, $vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		$row = array(
			'msg_text' => 'system_updated_successfully',
			'success'  => TRUE,
			'data'     => $data,
		);

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 */
	public function update_sort_order($data = array())
	{
		if (!empty($data))
		{
			foreach ($data as $k => $v)
			{
				if (!$q = $this->db->where($this->id, $v)
					->update(TBL_SLIDE_SHOWS, array('sort_order' => $k))
				)
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function validate($data = array())
	{
		$error = '';

		foreach ($data['lang'] as $k => $v)
		{
			$this->form_validation->reset_validation();
			$this->form_validation->set_data($v);

			$required = $this->config->item('slide_shows_name', 'required_input_fields');

			//now get the list of fields directly from the table
			$fields = $this->db->field_data(TBL_SLIDE_SHOWS_NAME);

			foreach ($fields as $f)
			{
				//set the default rule
				$rule = 'trim';

				if ($k == config_item('sts_site_default_language'))
				{
					//if this field is a required field, let's set that
					if (is_array($required) && in_array($f->name, $required))
					{
						$rule .= '|required';
					}
				}

				if ($data['type'] == 'simple')
				{
					if ($f->name == 'headline')
					{
						$rule .= '|xss_clean|required';
					}
				}

				$clean = $f->name == 'slide_show' ? FALSE : TRUE;
				$rule .= generate_db_rule($f->type, $f->max_length, $clean);

				$this->form_validation->set_rules($f->name, 'lang:' . $f->name, $rule);
			}

			if (!$this->form_validation->run())
			{
				$error .= validation_errors();
			}
			else
			{
				$data['lang'][$k] = $this->dbv->validated($v, FALSE);
			}
		}

		$this->form_validation->reset_validation();
		$this->form_validation->set_data($data);

		//get the list of fields required for this
		$required = $this->config->item('slide_shows', 'required_input_fields');

		//now get the list of fields directly from the table
		$fields = $this->db->field_data(TBL_SLIDE_SHOWS);

		//go through each field and
		foreach ($fields as $f)
		{
			//set the default rule
			$rule = 'trim|xss_clean';

			//if this field is a required field, let's set that
			if (is_array($required) && in_array($f->name, $required))
			{
				$rule .= '|required';
			}

			$rule .= generate_db_rule($f->type, $f->max_length, FALSE, $f->name);

			$this->form_validation->set_rules($f->name, 'lang:' . $f->name, $rule);
		}

		if (!$this->form_validation->run())
		{
			$error .= validation_errors();
		}

		if (!empty($error))
		{
			//sorry! got some errors here....
			$row = array('error'    => TRUE,
			             'msg_text' => $error,
			);
		}
		else
		{
			//cool! no errors...
			$row = array('success' => TRUE,
			             'data'    => $this->dbv->validated($data, FALSE),
			);
		}

		return $row;
	}
}

/* End of file Slide_shows_model.php */
/* Location: ./application/models/Slide_shows_model.php */
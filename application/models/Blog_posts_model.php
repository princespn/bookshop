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
class Blog_posts_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'blog_id';

	// ------------------------------------------------------------------------

	/**
	 * @param string $str
	 * @return bool
	 */
	public function check_url($str = '')
	{
		$str = empty($str) ? $this->input->post('url') : $str;

		if (!empty($str))
		{
			$this->db->where('url', $str);

			if ($this->input->post($this->id))
			{
				$this->db->where($this->id . ' !=', (int)$this->input->post($this->id));
			}

			if (!$q = $this->db->get(TBL_BLOG_POSTS))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				return FALSE;
			}
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return false|string
	 */
	public function create($data = array())
	{
		$vars = $this->dbv->clean($data, TBL_BLOG_POSTS);

		//finally... generate a unique URL if empty
		if (empty($vars['url']))
		{
			$vars['url'] = $this->generate_post_url($data['lang'][config_item('sts_site_default_language')]['title']);
		}

		if (!$q = $this->db->insert(TBL_BLOG_POSTS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$id = $this->db->insert_id();

		//now add an entry for each language
		foreach ($data['lang'] as $k => $v)
		{
			$vars = array(
				$this->id          => $id,
				'language_id'      => $k,
				'title'            => empty($v['title']) ? $data['lang'][config_item('sts_site_default_language')]['title'] : $v['title'],
				'body'             => empty($v['body']) ? $data['lang'][config_item('sts_site_default_language')]['body'] : $v['body'],
				'meta_title'       => empty($v['meta_title']) ? $v['title'] : $v['meta_title'],
				'meta_description' => empty($v['meta_description']) ? $v['title'] : $v['meta_description'],
				'meta_keywords'    => empty($v['meta_keywords']) ? $v['title'] : $v['meta_keywords'],
			);

			$vars['overview'] = empty($v['overview']) ? word_limiter(strip_tags($vars['body']), 50) : $v['overview'];

			if (!$q = $this->db->insert(TBL_BLOG_POSTS_NAME, $vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		$this->update_blog_downloads($id, $data);
		$this->update_blog_tags($id, $data);
		$this->update_blog_groups($id, $data);

		return sc(array('success'  => TRUE,
		                'id'       => $id,
		                'data'     => $data,
		                'msg_text' => lang('record_created_successfully'),
		));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return false|string
	 */
	public function delete($id = '')
	{
		if (!$this->db->where($this->id, $id)->delete(TBL_BLOG_POSTS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return sc(array('success'  => TRUE,
		                'id'       => $id,
		                'msg_text' => lang('record_deleted_successfully')));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $str
	 * @return string
	 */
	public function generate_post_url($str = '')
	{
		$str = url_title(filter_stop_words(strtolower($str)));
		$url = empty($str) ? random_string('alpha', 8) : $str;

		while ($this->dbv->check_unique($url, TBL_BLOG_POSTS, 'url'))
		{
			$url = $str;
			$url .= '-' . rand(1, 10000);
		}

		return $url;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $type
	 * @param string $category_id
	 * @return bool
	 */
	public function get_table_totals($data = array(), $type = '', $category_id = '')
	{
		switch ($type)
		{
			case 'tags':

				$sql = 'SELECT COUNT(*)
                            AS total
                            FROM ' . $this->db->dbprefix(TBL_BLOG_TO_TAGS) . ' t
                            LEFT JOIN ' . $this->db->dbprefix(TBL_BLOG_TAGS) . ' g
                                ON (t.tag_id = g.tag_id)
                            LEFT JOIN ' . $this->db->dbprefix(TBL_BLOG_POSTS) . ' p
                                ON (t.' . $this->id . ' = p.' . $this->id . ')
                            WHERE g.tag = \'' . $data['tag'] . '\'
                            AND (p.status = \'1\'
                                AND p.date_published <= ' . local_time() . ')';

				break;

			default:
				$sql = 'SELECT COUNT(*)
                            AS total
                            FROM ' . $this->db->dbprefix(TBL_BLOG_POSTS) . ' p
                            WHERE p.status = \'1\'
                                AND p.date_published <= ' . local_time('sql');

				if (!empty($data['query']))
				{
					$this->dbv->validate_columns(array(TBL_BLOG_POSTS, TBL_BLOG_POSTS_NAME,
					                                   TBL_BLOG_CATEGORIES_NAME), $data['query']);

					$sql .= $data['and_string'];
				}
				break;
		}

		if (!empty($category_id))
		{
			$sql .= ' AND p.category_id = \'' . $category_id . '\' ';
		}

		if (!$query = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($query->num_rows() > 0)
		{
			$q = $query->row();

			return $q->total;
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $tag_id
	 * @return bool|false|string
	 */
	public function get_blog_tags($id = '', $tag_id = '')
	{
		$sql = 'SELECT *
                    FROM ' . $this->db->dbprefix(TBL_BLOG_TO_TAGS) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_BLOG_TAGS) . ' n
                        ON p.tag_id = n.tag_id
                    WHERE p.' . $this->id . ' = \'' . valid_id($id) . '\'';

		if (!empty($tag_id))
		{
			$sql .= ' AND p.tag_id = \'' . valid_id($tag_id) . '\'';
		}

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->result_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool|false|string
	 */
	public function get_blog_groups($id = '')
	{
		$this->db->join(TBL_BLOG_GROUPS,
			$this->db->dbprefix(TBL_BLOG_TO_GROUPS) . '.group_id = ' .
			$this->db->dbprefix(TBL_BLOG_GROUPS) . '.group_id', 'left');

		if (!$q = $this->db->where($this->id, $id)->get(TBL_BLOG_TO_GROUPS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->result_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param int $lang_id
	 * @return bool|false|string
	 */
	public function get_blog_downloads($id = '', $lang_id = 1)
	{
		$sql = 'SELECT *
                    FROM ' . $this->db->dbprefix(TBL_BLOG_TO_DOWNLOADS) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_DOWNLOADS) . ' n
                        ON p.download_id = n.download_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_DOWNLOADS_NAME) . ' c
                        ON (c.download_id = n.download_id
                        AND c.language_id = \'' . $lang_id . '\')
                    WHERE p.' . $this->id . ' = \'' . $id . '\'';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->result_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param int $lang_id
	 * @param bool $public
	 * @param string $col
	 * @return bool|false|string
	 */
	public function get_details($id = '', $lang_id = 1, $public = FALSE, $col = 'blog_id')
	{
		$sql = 'SELECT p.*, c.*, v.*,
					DATE_FORMAT(p.date_published,\'' . $this->config->item('sql_date_format') . '\')
                        AS date_formatted,
                    DATE_FORMAT(p.date_published, \'%b\') AS month,
                    DATE_FORMAT(p.date_published, \'%d\') AS day,
                    DATE_FORMAT(p.date_published, \'%Y\') AS year,
                    DATE_FORMAT(p.date_published, \'%T\') AS time,
                    COUNT(k.blog_id)
                        AS comments,';

		if ($public == FALSE)
		{
			$sql .= ' (SELECT ' . $this->id . '
				        FROM ' . $this->db->dbprefix(TBL_BLOG_POSTS) . ' p
				        WHERE p.' . $this->id . ' < ' . (int)$id . '
				        ORDER BY `' . $this->id . '` DESC LIMIT 1)
				        AS prev,
				    (SELECT ' . $this->id . '
				        FROM ' . $this->db->dbprefix(TBL_BLOG_POSTS) . ' p
				        WHERE p.' . $this->id . ' > ' . (int)$id . '
				        ORDER BY `' . $this->id . '` ASC LIMIT 1)
				        AS next,';
		}

		$sql .= '   (SELECT GROUP_CONCAT(g.tag SEPARATOR \'-\')
                        FROM ' . $this->db->dbprefix(TBL_BLOG_TO_TAGS) . ' t
                        LEFT JOIN ' . $this->db->dbprefix(TBL_BLOG_TAGS) . ' g
                        ON t.tag_id = g.tag_id
                        WHERE t.blog_id = p.' . $this->id . ') AS tags,
                    (SELECT GROUP_CONCAT(t.tag_id SEPARATOR \'-\')
                        FROM ' . $this->db->dbprefix(TBL_BLOG_TO_TAGS) . ' t
                        WHERE t.blog_id = p.' . $this->id . ') AS tag_ids,
                    (SELECT category_name
                        FROM ' . $this->db->dbprefix(TBL_BLOG_CATEGORIES_NAME) . ' b
                        WHERE b.category_id =  p.category_id
                        AND language_id = \'' . $lang_id . '\')
                        AS category_name
                    FROM ' . $this->db->dbprefix(TBL_BLOG_POSTS) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_BLOG_COMMENTS) . ' k
                        ON (p.' . $this->id . ' = k.' . $this->id . ')
                     LEFT JOIN ' . $this->db->dbprefix(TBL_VIDEOS) . ' v
                        ON (p.video_id = v.video_id)    
                    LEFT JOIN ' . $this->db->dbprefix(TBL_BLOG_POSTS_NAME) . ' c
                        ON (p.' . $this->id . ' = c.' . $this->id . '
                        AND c.language_id = \'' . $lang_id . '\')
                    WHERE p.' . $col . ' = \'' . valid_id($id, TRUE) . '\'';

		if ($public == TRUE)
		{
			$sql .= ' AND p.status = \'1\' AND p.date_published <= ' . local_time();
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

				if (!empty($row[$this->id]))
				{
					$row['blog_downloads'] = $this->get_blog_downloads($row[$this->id], $lang_id);

					if ($public == FALSE)
					{
						$row['lang'] = $this->dbv->get_names(TBL_BLOG_POSTS_NAME, $this->id, $row[$this->id]);
						$row['blog_groups'] = $this->get_blog_groups($row[$this->id]);
						$row['blog_tags'] = $this->get_blog_tags($row[$this->id]);
					}
					else
					{
						//get next and previous posts
						$row['previous'] = $this->get_more_posts($row[$this->id], $lang_id, 'previous');
						$row['next'] = $this->get_more_posts($row[$this->id], $lang_id, 'next');

						if (!empty($row['restrict_group']))
						{
							$row['blog_groups'] = $this->get_blog_groups($row[$this->id]);
						}
					}
				}
			}

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, $cache_type);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return false|string|void
	 */
	public function get_revision($id = '')
	{
		$row = $this->dbv->get_names(TBL_BLOG_POSTS_REVISIONS_NAME, 'revision_id', $id);

		return empty($row) ? show_error(lang('no_record_found')) : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param int $lang_id
	 * @return bool|false|string
	 */
	public function get_rows($options = '', $lang_id = 1)
	{
		$cache = __METHOD__ . $options['md5'] . $lang_id;
		if (!$row = $this->init->cache($cache, 'db_query'))
		{
			$sort = $this->config->item(TBL_BLOG_POSTS, 'db_sort_order');

			$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
			$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

			$sql = 'SELECT *, b.category_name,
                    (SELECT GROUP_CONCAT(g.tag)
                        FROM ' . $this->db->dbprefix(TBL_BLOG_TO_TAGS) . ' t
                        LEFT JOIN ' . $this->db->dbprefix(TBL_BLOG_TAGS) . ' g
                        ON t.tag_id = g.tag_id
                        WHERE t.blog_id = p.' . $this->id . ')
                            AS tags';

			if (!$this->config->item('disable_sql_category_count'))
			{
				$sql .= ', (SELECT COUNT(' . $this->id . ')
                                FROM ' . $this->db->dbprefix(TBL_BLOG_COMMENTS) . '
                                WHERE ' . 'c.' . $this->id . ' = ' . $this->db->dbprefix(TBL_BLOG_COMMENTS) . '.' . $this->id . ')
                                AS comments';
			}

			$sql .= ' FROM ' . $this->db->dbprefix(TBL_BLOG_POSTS) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_BLOG_CATEGORIES_NAME) . ' b
                        ON b.category_id =  p.category_id
                         AND b.language_id = \'' . $lang_id . '\'
                     LEFT JOIN ' . $this->db->dbprefix(TBL_BLOG_POSTS_NAME) . ' c
                        ON (p.' . $this->id . ' = c.' . $this->id . '
                        AND c.language_id = \'' . $lang_id . '\')';

			if (!empty($options['query']))
			{
				$this->dbv->validate_columns(array(TBL_BLOG_POSTS, TBL_BLOG_POSTS_NAME,
				                                   TBL_BLOG_CATEGORIES_NAME), $options['query']);

				$sql .= $options['where_string'];
			}

			$sql .= ' ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'] . '
                        LIMIT ' . $options['offset'] . ', ' . $options['limit'];

			if (!$query = $this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}


			if ($query->num_rows() > 0)
			{
				$row = array(
					'values'  => $query->result_array(),
					'total'   => $this->dbv->get_table_totals($options, TBL_BLOG_POSTS),
					'success' => TRUE,
				);
			}

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, 'db_query');
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $tags
	 * @param int $lang_id
	 * @return bool
	 */
	public function get_related_posts($id = '', $tags = '', $lang_id = 1)
	{
		if (!empty($tags))
		{
			$ids = explode('-', $tags);

			$sql = 'SELECT *,
					(SELECT category_name
                        FROM ' . $this->db->dbprefix(TBL_BLOG_CATEGORIES_NAME) . ' b
                        WHERE b.category_id =  a.category_id
                        AND language_id = \'' . $lang_id . '\')
                        AS category_name
					FROM ' . $this->db->dbprefix(TBL_BLOG_TO_TAGS) . ' p
					LEFT JOIN ' . $this->db->dbprefix(TBL_BLOG_POSTS) . ' a
                        ON (p.' . $this->id . ' = a.' . $this->id . ')
                     LEFT JOIN ' . $this->db->dbprefix(TBL_BLOG_POSTS_NAME) . ' c
                        ON (p.' . $this->id . ' = c.' . $this->id . '
                        AND c.language_id = \'' . (int)$lang_id . '\')
                     WHERE p.blog_id != \'' . valid_id($id) . '\'
                     AND a.status = \'1\'
                     AND a.drip_feed = \'0\'';

			foreach ($ids as $k => $v)
			{
				$sql .= $k == 0 ? ' AND (' : ' OR ';

				$sql .= ' p.tag_id = ' . valid_id($v);
			}

			$sql .= ')  GROUP BY p.blog_id
						ORDER BY RAND()
						LIMIT ';

			$sql .= DEFAULT_TOTAL_RELATED_BLOGS;

			if (!$q = $this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			$row = $q->result_array();
		}

		return !empty($row) ? $row : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param int $lang_id
	 * @param string $operator
	 * @return bool
	 */
	protected function get_more_posts($id = '', $lang_id = 1, $operator = 'next')
	{
		$sql = 'SELECT *
					FROM ' . $this->db->dbprefix(TBL_BLOG_POSTS) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_BLOG_POSTS_NAME) . ' c
                        ON (p.' . $this->id . ' = c.' . $this->id . '
                        AND c.language_id = \'' . (int)$lang_id . '\')
                    WHERE p.status = \'1\' AND p.date_published <= ' . local_time();

		if ($operator == 'next')
		{
			$sql .= ' AND p.' . $this->id . ' > ' . valid_id($id) . '
				        ORDER BY p.' . $this->id . ' ASC LIMIT 1';
		}
		else
		{
			$sql .= ' AND p.' . $this->id . ' < ' . valid_id($id) . '
				        ORDER BY p.' . $this->id . ' DESC LIMIT 1';
		}

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? $q->row_array() : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param array $data
	 * @return bool
	 */
	protected function insert_revision_name($id = '', $data = array())
	{
		//now add an entry for each language
		foreach ($data['lang'] as $k => $v)
		{
			$vars = $this->dbv->clean($v, TBL_BLOG_POSTS_REVISIONS_NAME);

			$vars['revision_id'] = $id;
			$vars['language_id'] = $k;

			if (!$q = $this->db->insert(TBL_BLOG_POSTS_REVISIONS_NAME, $vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool|false|string
	 */
	public function load_revisions($id = '')
	{
		$this->db->order_by('date', 'DESC');
		if (!$q = $this->db->where($this->id, $id)->get(TBL_BLOG_POSTS_REVISIONS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->result_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param int $lang_id
	 * @return bool
	 */
	public function load_blog($lang_id = 1)
	{
		$opt = array('offset'           => '0',
		             'session_per_page' => config_option('layout_design_blogs_per_home_page'),
		);

		$rows = $this->load_blog_posts(query_options($opt), $lang_id);

		return !empty($rows['values']) ? $rows['values'] : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param int $lang_id
	 * @param string $category_id
	 * @return bool|false|string
	 */
	public function load_blog_posts($options = '', $lang_id = 1, $category_id = '')
	{
		$sort = $this->config->item(TBL_BLOG_POSTS, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$sql = 'SELECT *,
                        DATE_FORMAT(p.date_published, \'%b\') AS month,
                        DATE_FORMAT(p.date_published, \'%d\') AS day,
                        DATE_FORMAT(p.date_published, \'%Y\') AS year,
                        DATE_FORMAT(p.date_published, \'%T\') AS time,
                        (SELECT COUNT(*)
                            FROM ' . $this->db->dbprefix(TBL_BLOG_COMMENTS) . ' m
                            WHERE m.' . $this->id . ' = c.' . $this->id . '
                            AND m.status = \'1\')
                            AS comments,
                        (SELECT GROUP_CONCAT(g.tag SEPARATOR \'-\')
                            FROM ' . $this->db->dbprefix(TBL_BLOG_TO_TAGS) . ' t
                            LEFT JOIN ' . $this->db->dbprefix(TBL_BLOG_TAGS) . ' g
                            ON t.tag_id = g.tag_id
                            WHERE t.blog_id = p.' . $this->id . ') AS tags,
                        (SELECT category_name
                            FROM ' . $this->db->dbprefix(TBL_BLOG_CATEGORIES_NAME) . ' b
                            WHERE b.category_id =  p.category_id
                                AND language_id = \'' . $lang_id . '\')
                            AS category_name
                        FROM ' . $this->db->dbprefix(TBL_BLOG_POSTS) . ' p
                        LEFT JOIN ' . $this->db->dbprefix(TBL_BLOG_POSTS_NAME) . ' c
                            ON (p.' . $this->id . ' = c.' . $this->id . '
                            AND c.language_id = \'' . $lang_id . '\')
                        WHERE p.status = \'1\'
                            AND p.date_published <= ' . local_time();

		if (!empty($category_id))
		{
			$sql .= ' AND p.category_id = \'' . $category_id . '\' ';
		}

		if (!empty($options['query']))
		{
			$this->dbv->validate_columns(array(TBL_BLOG_POSTS, TBL_BLOG_POSTS_NAME,
			                                   TBL_BLOG_CATEGORIES_NAME), $options['query']);

			$sql .= $options['and_string'];
		}

		$sql .= ' ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'] . '
                        LIMIT ' . $options['offset'] . ', ' . $options['limit'];

		$cache = __METHOD__ . md5($sql);
		if (!$row = $this->init->cache($cache, 'public_db_query'))
		{
			if (!$q = $this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = array(
					'values'  => $q->result_array(),
					'total'   => $this->get_table_totals($options, 'default', $category_id),
					'success' => TRUE,
				);
			}

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, 'public_db_query');
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param string $tag
	 * @param int $lang_id
	 * @return bool|false|string
	 */
	public function load_blogs_per_tag($options = '', $tag = '', $lang_id = 1)
	{
		$cache = __METHOD__ . $options['md5'];

		if (!$row = $this->init->cache($cache, 'public_db_query'))
		{
			$sort = $this->config->item(TBL_BLOG_POSTS, 'db_sort_order');

			$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
			$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

			$sql = 'SELECT *,
                        DATE_FORMAT(p.date_published, \'%b\') AS month,
                        DATE_FORMAT(p.date_published, \'%d\') AS day,
                        DATE_FORMAT(p.date_published, \'%Y\') AS year,
                        DATE_FORMAT(p.date_published, \'%T\') AS time,
                     (SELECT COUNT(*)
                            FROM ' . $this->db->dbprefix(TBL_BLOG_COMMENTS) . ' m
                            WHERE m.' . $this->id . ' = p.' . $this->id . '
                            AND m.status = \'1\')
                            AS comments,
                        (SELECT GROUP_CONCAT(w.tag)
                            FROM ' . $this->db->dbprefix(TBL_BLOG_TO_TAGS) . ' t
                            LEFT JOIN ' . $this->db->dbprefix(TBL_BLOG_TAGS) . ' w
                            ON t.tag_id = w.tag_id
                            WHERE t.blog_id = p.' . $this->id . ') AS tags,
                        (SELECT category_name
                            FROM ' . $this->db->dbprefix(TBL_BLOG_CATEGORIES_NAME) . ' b
                            WHERE b.category_id =  p.category_id
                            AND language_id = \'' . $lang_id . '\')
                            AS category_name
                    FROM ' . $this->db->dbprefix(TBL_BLOG_TO_TAGS) . ' t
                    LEFT JOIN ' . $this->db->dbprefix(TBL_BLOG_TAGS) . ' g
                        ON (t.tag_id = g.tag_id)
                    LEFT JOIN ' . $this->db->dbprefix(TBL_BLOG_POSTS) . ' p
                        ON (t.blog_id = p.blog_id)
                    LEFT JOIN ' . $this->db->dbprefix(TBL_BLOG_POSTS_NAME) . ' n
                        ON (n.blog_id = t.blog_id
                        AND n.language_id = \'' . $lang_id . '\')
                    WHERE g.tag = \'' . $tag . '\'
                    AND (p.status = \'1\'
                        AND p.date_published <= ' . local_time() . ')';

			if (!empty($options['query']))
			{
				$this->dbv->validate_columns(array(TBL_BLOG_POSTS, TBL_BLOG_POSTS_NAME,
				                                   TBL_BLOG_CATEGORIES_NAME), $options['query']);

				$sql .= $options['and_string'];
			}

			$sql .= ' ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'] . '
                        LIMIT ' . $options['offset'] . ', ' . $options['limit'];

			if (!$q = $this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				//set the tag
				$options['tag'] = $tag;
				$row = array(
					'values'  => $q->result_array(),
					'total'   => $this->get_table_totals($options, 'tags'),
					'success' => TRUE,
				);
			}

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, 'public_db_query');
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
		if (!empty($data['blog_id']))
		{
			foreach ($data['blog_id'] as $v)
			{
				if ($data['change-status'] == 'delete')
				{
					$this->delete($v);
				}
				else
				{
					switch ($data['change-status'])
					{
						case '1':
						case '0':

							$vars['status'] = $data['change-status'];

							break;
					}

					if (!$this->db->where($this->id, $v)->update(TBL_BLOG_POSTS, $vars))
					{
						get_error(__FILE__, __METHOD__, __LINE__);
					}
				}
			}

			$row = array('success'  => TRUE,
			             'data'     => $data,
			             'msg_text' => lang('mass_update_successful'),
			);
		}

		//order the tier groups numerically
		$this->dbv->db_sort_order(TBL_BRANDS, 'brand_id', 'sort_order');

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function save_revision($data = array())
	{
		$key = md5(serialize($data));

		$this->db->where('blog_id', $data['blog_id']);
		$this->db->where('key', $key);

		if (!$q = $this->db->get(TBL_BLOG_POSTS_REVISIONS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() < 1)
		{
			//insert the new revision
			$vars = array('blog_id' => $data['blog_id'],
			              'key'     => $key,
			);

			if (!$q = $this->db->where($this->id, $data['blog_id'])->insert(TBL_BLOG_POSTS_REVISIONS, $vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			$id = $this->db->insert_id();

			//add revisions per locale
			$this->insert_revision_name($id, $data);

			//prune old revisions
			$this->prune_revisions($data['blog_id']);

			return array('success'  => TRUE,
			             'id'       => $id,
			             'msg_text' => lang('revision_saved_successfully'),
			             'data'     => $data);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 */
	public function prune_revisions($id = '')
	{
		$sql = 'SELECT revision_id
					FROM ' . $this->db->dbprefix(TBL_BLOG_POSTS_REVISIONS) . '
						WHERE ' . $this->id . ' = \'' . $id . '\'
					ORDER BY date
						DESC LIMIT ' . MAX_BLOG_POST_REVISIONS . ',1';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = $q->row_array();

		$sql = 'DELETE
					FROM ' . $this->db->dbprefix(TBL_BLOG_POSTS_REVISIONS) . '
						WHERE revision_id  <= \'' . $row['revision_id'] . '\'
						AND ' . $this->id . ' = \'' . $id . '\'';

		if (!$this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param int $lang_id
	 * @return bool|false|string
	 */
	public function search($options = '', $lang_id = 1, $public = FALSE)
	{
		$sort = $this->config->item(TBL_BLOG_POSTS, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$count = 'SELECT COUNT(*) AS total 
 					FROM ' . $this->db->dbprefix(TBL_BLOG_POSTS) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_BLOG_CATEGORIES_NAME) . ' b
                        ON b.category_id =  p.category_id
                         AND b.language_id = \'' . $lang_id . '\'
                     LEFT JOIN ' . $this->db->dbprefix(TBL_BLOG_POSTS_NAME) . ' c
                        ON (p.' . $this->id . ' = c.' . $this->id . '
                        AND c.language_id = \'' . $lang_id . '\')';

		$sql = 'SELECT *, b.category_name,
                    (SELECT GROUP_CONCAT(g.tag)
                        FROM ' . $this->db->dbprefix(TBL_BLOG_TO_TAGS) . ' t
                        LEFT JOIN ' . $this->db->dbprefix(TBL_BLOG_TAGS) . ' g
                        ON t.tag_id = g.tag_id
                        WHERE t.blog_id = p.' . $this->id . ')
                            AS tags';

		if (!$this->config->item('disable_sql_category_count'))
		{
			$sql .= ', (SELECT COUNT(' . $this->id . ')
	                        FROM ' . $this->db->dbprefix(TBL_BLOG_COMMENTS) . '
	                        WHERE ' . 'c.' . $this->id . ' = ' . $this->db->dbprefix(TBL_BLOG_COMMENTS) . '.' . $this->id . ')
	                        AS comments';
		}

		$sql .= ' FROM ' . $this->db->dbprefix(TBL_BLOG_POSTS) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_BLOG_CATEGORIES_NAME) . ' b
                        ON b.category_id =  p.category_id
                         AND b.language_id = \'' . $lang_id . '\'
                     LEFT JOIN ' . $this->db->dbprefix(TBL_BLOG_POSTS_NAME) . ' c
                        ON (p.' . $this->id . ' = c.' . $this->id . '
                        AND c.language_id = \'' . $lang_id . '\')';

		if (!empty($options['query']))
		{
			foreach ($options['query'] as $k => $v)
			{
				//remove any fields not needed in the query
				if (in_array($k, $this->config->item('query_type_filter')))
				{
					continue;
				}

				$columns = $this->db->list_fields(TBL_BLOG_POSTS);

				$i = 1;
				foreach ($columns as $f)
				{
					if ($i == 1)
					{
						$sql .= ' WHERE p.' . $f . ' LIKE \'%' . $v . '%\' ESCAPE \'!\'';
						$count .= ' WHERE p.' . $f . ' LIKE \'%' . $v . '%\' ESCAPE \'!\'';
					}
					else
					{
						$sql .= 'OR p.' . $f . ' LIKE \'%' . $v . '%\' ESCAPE \'!\'';
						$count .= 'OR p.' . $f . ' LIKE \'%' . $v . '%\' ESCAPE \'!\'';
					}

					$i++;
				}

				$columns = $this->db->list_fields(TBL_BLOG_POSTS_NAME);

				foreach ($columns as $f)
				{
					$sql .= ' OR c.' . $f . ' LIKE \'%' . $v . '%\' ESCAPE \'!\'';
					$count .= ' OR c.' . $f . ' LIKE \'%' . $v . '%\' ESCAPE \'!\'';
				}
			}
		}

		if ($public == TRUE)
		{
			$sql .= '  AND p.status = \'1\'';
			$count .= '  AND p.status = \'1\'';
		}

		$order_by = '  GROUP BY p.' . $this->id . ' 
						ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'] . '
                        LIMIT ' . $options['offset'] . ', ' . $options['limit'];

		if (!$q = $this->db->query($sql . $order_by))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = array(
				'values'  => $q->result_array(),
				'total'   => $this->dbv->get_query_total($count),
				'success' => TRUE,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function update($data = array())
	{
		$vars = $this->dbv->clean($data, TBL_BLOG_POSTS);
		$vars['updated_on'] = get_time(now(), TRUE);

		if (!$q = $this->db->where($this->id, $data['blog_id'])->update(TBL_BLOG_POSTS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		foreach ($data['lang'] as $k => $v)
		{
			$v = $this->dbv->clean($v, TBL_BLOG_POSTS_NAME);

			$vars = array(
				'title'            => empty($v['title']) ? $data['lang'][config_item('sts_site_default_language')]['title'] : $v['title'],
				'body'             => empty($v['body']) ? $data['lang'][config_item('sts_site_default_language')]['body'] : $v['body'],
				'meta_title'       => empty($v['meta_title']) ? $v['title'] : $v['meta_title'],
				'meta_description' => empty($v['meta_description']) ? $v['title'] : $v['meta_description'],
				'meta_keywords'    => empty($v['meta_keywords']) ? $v['title'] : $v['meta_keywords'],
			);

			$vars['overview'] = empty($v['overview']) ? word_limiter(strip_tags($vars['body']), 50) : $v['overview'];

			$this->db->where($this->id, $data['blog_id']);
			$this->db->where('language_id', $k);

			if (!$this->db->update(TBL_BLOG_POSTS_NAME, $vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		$this->update_blog_downloads($data);
		$this->update_blog_tags($data);
		$this->update_blog_groups($data);

		$row = array(
			'msg_text' => lang('system_updated_successfully'),
			'success'  => TRUE,
			'data'     => $data,
		);

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool
	 */
	public function update_blog_groups($data = array())
	{
		$this->db->where($this->id, $data['blog_id']);

		if (!$this->db->delete(TBL_BLOG_TO_GROUPS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if (!empty($data['blog_groups']))
		{
			foreach ($data['blog_groups'] as $v)
			{
				if (!$this->db->insert(TBL_BLOG_TO_GROUPS, array('blog_id' => $data['blog_id'], 'group_id' => $v)))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}
			}
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool
	 */
	public function update_blog_tags($data = array())
	{
		if (!empty($data['blog_tags']))
		{
			$c = array();
			foreach ($data['blog_tags'] as $v)
			{
				$v = trim(url_title(strtolower($v)));
				//check if the tag is in the db first...
				if (!$a = $this->dbv->get_record(TBL_BLOG_TAGS, 'tag', $v, TRUE, TRUE))
				{
					$vars = array('tag'   => $v,
					              'count' => '0');

					$a = $this->dbv->create(TBL_BLOG_TAGS, $vars);

					//set the tag id...
					$a['tag_id'] = $a['id'];
				}

				//now check if the tag is in the product tag db
				if (!$b = $this->get_blog_tags($data['blog_id'], $a['tag_id']))
				{
					$vars = array('blog_id' => $data['blog_id'],
					              'tag_id'  => $a['tag_id']);

					$this->dbv->create(TBL_BLOG_TO_TAGS, $vars);
				}

				array_push($c, $a['tag_id']);
			}

			$d = $this->get_blog_tags($data['blog_id']);

			if (!empty($d))
			{
				foreach ($d as $e)
				{
					if (!in_array($e['tag_id'], $c))
					{
						$this->dbv->delete(TBL_BLOG_TO_TAGS, 'blog_tag_id', $e['blog_tag_id']);
					}
				}
			}
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool
	 */
	public function update_blog_downloads($data = array())
	{
		$this->db->where($this->id, $data['blog_id']);

		if (!$this->db->delete(TBL_BLOG_TO_DOWNLOADS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if (!empty($data['blog_downloads']))
		{
			foreach ($data['blog_downloads'] as $v)
			{

				if (!$this->db->insert(TBL_BLOG_TO_DOWNLOADS, array($this->id     => $data['blog_id'],
				                                                    'download_id' => $v))
				)
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}
			}
		}

		return TRUE;
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

			//validate the entries...
			$vars = array('title', 'body');

			foreach ($vars as $c)
			{
				if ($k == config_item('sts_site_default_language'))
				{
					$this->form_validation->set_rules($c, 'lang:' . $c, 'trim|required',
						array('required' => $v['language'] . ' ' . lang($c . '_required')));
				}
				else
				{
					$this->form_validation->set_rules($c, 'lang:' . $c, 'trim');
				}
			}

			//validate the meta info...
			$vars = array('overview', 'meta_title', 'meta_keywords', 'meta_description');

			foreach ($vars as $c)
			{
				$this->form_validation->set_rules($c, 'lang:' . $c, 'trim|strip_tags');
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

		//now validate the rest...
		$this->form_validation->reset_validation();
		$this->form_validation->set_data($data);

		$vars = array('status', 'category_id', 'restrict_group', 'require_registration', 'enable_comments');
		foreach ($vars as $v)
		{
			$this->form_validation->set_rules($v, 'lang:' . $v, 'trim|required|integer');
		}

		$this->form_validation->set_rules('date_published', 'lang:publish_date', 'trim|required|start_date_to_sql');

		$vars = array('sort_order', 'views');
		foreach ($vars as $v)
		{
			$this->form_validation->set_rules($v, 'lang:' . $v, 'trim|integer');
		}

		if (!empty($data['restrict_group']))
		{
			if (empty($data['blog_groups']))
			{
				$this->form_validation->set_rules('blog_groups', 'lang:blog_groups', 'trim|required');
			}
		}

		$this->form_validation->set_rules('notes', 'lang:notes', 'trim|strip_tags|xss_clean');
		$this->form_validation->set_rules('author', 'lang:author', 'trim|strip_tags|xss_clean');

		if (CONTROLLER_FUNCTION == 'create')
		{
			$this->form_validation->set_rules(
				'url', 'lang:url',
				'trim|strtolower|url_title|is_unique[' . TBL_BLOG_POSTS . '.url]',
				array(
					'is_unique' => '%s ' . lang('already_in_use'),
				)
			);
		}
		else
		{
			$this->form_validation->set_rules(
				'url', 'lang:url',
				array(
					'trim', 'required', 'strtolower', 'url_title',
					array('check_url', array($this->blog, 'check_url')),
				)
			);

			$this->form_validation->set_message('check_url', '%s ' . lang('already_in_use'));
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
			             'data'    => $this->dbv->validated($data),
			);
		}

		return $row;
	}
}

/* End of file Blog_posts_model.php */
/* Location: ./application/models/Blog_posts_model.php */
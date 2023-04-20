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
class Forum extends Public_Controller
{
	public $data;

	public function __construct()
	{
		parent::__construct();

		$models = array('Forum_topics'     => 'forum',
		                'Forum_categories' => 'cat');

		foreach ($models as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

		$this->load->helper('content');
		$this->load->helper('html_editor');

		if (!config_enabled('sts_forum_enable'))
		{
			redirect();
		}

		$this->data = $this->init->initialize('site');

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	// ------------------------------------------------------------------------

	public function view()
	{
		//get categories
		$this->data['forum_categories'] = $this->cat->load_categories(sess('default_lang_id'));

		$opt = array('offset'           => 0,
		             'session_per_page' => DEFAULT_TOTAL_FORUM_LATEST_TOPICS,
		);

		$c = $this->cat->load_categories(sess('default_lang_id'), TRUE);
		$this->data['topics'] = $this->forum->load_forum(query_options($opt), $c);

		$this->show->display('support', 'forum', $this->data);
	}

	// ------------------------------------------------------------------------

	public function topics()
	{
		$this->data['id'] = valid_id(uri(3, TRUE));

		//get categories
		$this->data['forum_categories'] = $this->cat->load_categories(sess('default_lang_id'));

		//set the custom offset
		$opt = array('offset'           => (int)uri(4, 0),
		             'session_per_page' => !sess('rows_per_page') ? config_option('layout_design_forum_posts_per_page') : sess('rows_per_page'),
		);

		//get the category details
		$this->data['category'] = $this->cat->get_details($this->data['id'], sess('default_lang_id'), TRUE, 'category_url', TRUE);

		if ($this->data['category'])
		{
			//get the forum posts
			$rows = $this->forum->get_topics($this->data['category']['category_id'], query_options($opt));
			$this->data['topics'] = !empty($rows['values']) ? $rows['values'] : FALSE;

			//get pinned
			if ($opt['offset'] == '0')
			{
				$pinned = $this->forum->get_topics($this->data['category']['category_id'], query_options($opt), TRUE);
				$this->data['pinned'] = !empty($pinned['values']) ? $pinned['values'] : FALSE;
			}

			//check for pagination
			if (!empty($rows['total']))
			{
				$this->data['page_options'] = array(
					'uri'        => site_url('forum/topics/' . $this->data['id']),
					'total_rows' => $rows['total'],
					'per_page'   => $opt['session_per_page'],
					'segment'    => 4,
				);

				$this->data['paginate'] = $this->paginate->generate($this->data['page_options'], CONTROLLER_CLASS);
			}

			$this->show->display('support', 'forum_topics', $this->data);
		}
		else
		{
			redirect_page('forum');
		}
	}

	// ------------------------------------------------------------------------

	public function topic()
	{
		//set the id
		$this->data['id'] = valid_id(uri(3), TRUE);

		if (!$row = $this->forum->get_details($this->data['id'], sess('default_lang_id'), TRUE))
		{
			$this->show->page('404', $this->data);
		}
		else
		{
			//map blog details
			$this->data['p'] = $row;

			//check if the user is the owner of the post
			if (empty($row['status']) && !check_moderation($row))
			{
				$this->show->page('404', $this->data);
			}
			else
			{
				//get categories
				$this->data['forum_categories'] = $this->cat->load_categories(sess('default_lang_id'));

				//format breadcrumbs
				$this->data['breadcrumb'] = set_breadcrumb(format_breadcrumb($row, 'forum'));

				//update stats
				$this->dbv->update_count(array('table' => TBL_FORUM_TOPICS,
				                               'key'   => 'topic_id',
				                               'id'    => $this->data['p']['topic_id'],
				                               'field' => 'views'));

				//load the template
				$this->show->display('support', 'forum_topic', $this->data);
			}
		}
	}

	// ------------------------------------------------------------------------

	public function approve_topic()
	{
		$this->data['id'] = valid_id(uri(3));

		$msg = '';

		if (check_moderation('',TRUE))
		{
			$row = $this->forum->approve_topic($this->data['id']);

			$this->done(__METHOD__, $row);
		}

		$url = !$this->agent->referrer() ? 'forum' : $this->agent->referrer();
		redirect_flashdata($url, $msg);
	}

	// ------------------------------------------------------------------------

	public function approve_reply()
	{
		$this->data['id'] = valid_id(uri(3));

		$msg = '';

		if (check_moderation('',TRUE))
		{
			$row = $this->forum->approve_reply($this->data['id']);

			//run any plugins
			$this->done(__METHOD__, $row);
		}

		$url = !$this->agent->referrer() ? 'forum' : $this->agent->referrer();
		redirect_flashdata($url, $msg);
	}

	// ------------------------------------------------------------------------

	public function add_topic()
	{
		//get the category details
		if (uri(3))
		{
			$this->data['id'] = valid_id(uri(3), TRUE);
			$this->data['category'] = $this->cat->get_details($this->data['id'], sess('default_lang_id'), TRUE, 'category_url', TRUE);
		}

		if ($this->input->post())
		{
			if ($this->sec->verify_ownership())
			{
				//check if the form submitted is correct
				$row = $this->forum->validate_user_topic(__FUNCTION__, $this->input->post());

				//all good...
				if (!empty($row['success']))
				{
					$row = $this->forum->add_topic($row['data']);

					$this->data['category'] = $this->cat->get_details($row['data']['category_id'], sess('default_lang_id'), TRUE, 'category_url', TRUE);

					//send alert email
					$vars = format_forum_alert_email('topic', $this->data['category'], $row['data']);
					$this->mail->send_forum_alerts('topic', $vars);

					//set json response
					$response = array('type'     => 'success',
					                  'msg'      => $row['msg_text'],
					                  'redirect' => site_url('forum/topic/' . $row['data']['url']),
					);

					$this->sec->check_flood_control('forum_post', 'add', sess('member_id'));

					$this->done(__METHOD__, $row);
				}
				else
				{
					//errors!
					$response = array('type'         => 'error',
					                  'error_fields' => generate_error_fields(),
					                  'msg'          => validation_errors());

					//log it!
					$this->dbv->rec(array('method' => __METHOD__, 'msg' => lang('error_adding_record'), 'level' => 'security'));
				}

				//send the response via ajax
				ajax_response($response);
			}
		}

		//get categories
		$this->data['forum_categories'] = $this->cat->load_categories(sess('default_lang_id'));

		//load the template
		$this->show->display('support', 'forum_add_topic', $this->data);
	}

	// ------------------------------------------------------------------------

	public function get_topic()
	{
		$id = valid_id($this->input->get('topic_id'));

		$response = array('type' => 'error');

		if (!$q = $this->db->where('topic_id', $id)->get(TBL_FORUM_TOPICS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->row_array();

			if (check_moderation($row))
			{
				//send the response via ajax
				$response = array('type' => 'success',
				                  'msg'      => $row['topic']);
			}
		}

		ajax_response($response);
	}

	// ------------------------------------------------------------------------

	public function update_topic()
	{
		$this->data['id'] = valid_id($this->input->post('topic_id'));

		$msg = '';

		if (check_moderation($this->input->post()))
		{
			$row = $this->forum->validate_user_topic(__FUNCTION__, $this->input->post(NULL, TRUE));

			//all good...
			if (!empty($row['success']))
			{
				$row = $this->forum->update_topic($this->data['id'], $this->input->post());

				$this->done(__METHOD__, $row);
			}
		}

		$url = !$this->agent->referrer() ? 'forum' : $this->agent->referrer();
		redirect_flashdata($url, $msg);
	}

	// ------------------------------------------------------------------------

	public function delete_topic()
	{
		$this->data['id'] = valid_id(uri(3));

		$topic = $this->dbv->get_record(TBL_FORUM_TOPICS, 'topic_id', $this->data['id']);

		$msg = '';

		if (!empty($topic))
		{
			if (check_moderation(array('member_id' => valid_id(uri(4)))))
			{
				$row = $this->forum->delete($this->data['id'], TRUE);

				$this->done(__METHOD__, $row);
			}
		}

		redirect_flashdata('forum', $msg);
	}

	// ------------------------------------------------------------------------

	public function add_reply()
	{
		if ($this->input->post())
		{
			if ($this->sec->verify_ownership())
			{
				//check if the form submitted is correct
				$row = $this->forum->validate_user_topic(__FUNCTION__, $this->input->post());

				//all good...
				if (!empty($row['success']))
				{
					$row = $this->forum->add_user_reply($row['data']);

					//get topic details
					$topic = $this->forum->get_details($row['id']);

					$vars = format_forum_alert_email('reply', $topic, $row['data']);

					$this->mail->send_forum_alerts('topic', $vars);

					//set json response
					$response = array('type'     => 'success',
					                  'msg'      => $row['msg_text'],
					                  'redirect' => site_url('forum/topic/' . $topic['url']));

					if (empty($row['data']['status']))
					{
						$response['msg'] .= '<br />' . lang('forum_reply_in_moderation');
					}

					$this->sec->check_flood_control('forum_post', 'add', sess('member_id'));

					$this->done(__METHOD__, $row);
				}
				else
				{
					//errors!
					$response = array('type'         => 'error',
					                  'error_fields' => generate_error_fields(),
					                  'msg'          => $row['msg_text']);

					//log it!
					$this->dbv->rec(array('method' => __METHOD__, 'msg' => lang('error_adding_record'), 'level' => 'security'));
				}
			}

			//send the response via ajax
			ajax_response($response);
		}
	}

	// ------------------------------------------------------------------------

	public function get_reply()
	{
		$id = valid_id($this->input->get('reply_id'));

		$response = array('type' => 'error');

		if (!$q = $this->db->where('reply_id', $id)->get(TBL_FORUM_TOPICS_REPLIES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->row_array();

			if (check_moderation($row))
			{
				//send the response via ajax
				$response = array('type' => 'success',
				                  'msg'      => $row['reply_content']);
			}
		}

		ajax_response($response);
	}

	// ------------------------------------------------------------------------

	public function update_reply()
	{
		$this->data['id'] = valid_id($this->input->post('reply_id'));

		if (check_moderation($this->input->post()))
		{
			//check if the form submitted is correct
			$row = $this->forum->validate_user_topic(__FUNCTION__, $this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->forum->update_reply($this->data['id'], $this->input->post());

				$this->done(__METHOD__, $row);
			}
		}

		$url = !$this->agent->referrer() ? 'forum' : $this->agent->referrer();
		redirect_flashdata($url, $row['msg_text']);
	}

	// ------------------------------------------------------------------------

	public function delete_reply()
	{
		$this->data['id'] = valid_id(uri(3));

		$msg = '';

		$reply = $this->dbv->get_record(TBL_FORUM_TOPICS_REPLIES, 'reply_id', $this->data['id']);

		if (!empty($reply))
		{
			if (check_moderation(array('member_id' => $reply['member_id'])))
			{
				$row = $this->forum->delete_reply($this->data['id'], TRUE);

				$msg = !empty($row['success']) ? $row['msg_text'] : '';

				$this->done(__METHOD__, $row);

				$url = !$this->agent->referrer() ? 'forum' : $this->agent->referrer();

			}
		}

		redirect_flashdata($url, $msg);
	}

	// ------------------------------------------------------------------------

	public function search()
	{
		//set the id
		$this->data[ 'search_term' ] = url_title($this->input->get('search_term'));

		if ($this->data['search_term'])
		{
			//set the custom offset
			$opt = array( 'offset'           => (int)uri(3, 0),
			              'session_per_page' => !sess('rows_per_page') ? $this->data[ 'layout_design_products_per_page' ] : sess('rows_per_page')
			);

			//get the topics
			$rows = $this->forum->search(query_options($opt, TRUE, TRUE), sess('default_lang_id'), TRUE);

			//set the topics array
			$this->data[ 'topics' ] = empty($rows[ 'values' ]) ? FALSE : $rows[ 'values' ];

			//check for pagination
			if (!empty($rows['total']))
			{
				$this->data['page_options'] = array(
					'uri'        => site_url('forum/search/'),
					'total_rows' => $rows['total'],
					'per_page'   => $opt['session_per_page'],
					'segment'    => 3,
				);

				$this->data['paginate'] = $this->paginate->generate($this->data['page_options'], CONTROLLER_CLASS);
			}

			//get categories
			$this->data['forum_categories'] = $this->cat->load_categories(sess('default_lang_id'));

			$this->show->display('support', 'forum_topics', $this->data);

		}
		else
		{
			redirect('forum');
		}
	}
}

/* End of file Forum.php */
/* Location: ./application/controllers/Forum.php */
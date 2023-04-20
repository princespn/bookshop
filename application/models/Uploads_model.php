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
class Uploads_model extends CI_Model
{
	// ------------------------------------------------------------------------

	/**
	 * @param string $files
	 * @param string $config
	 * @param bool $show_error
	 * @return bool|false|string
	 */
	public function upload_file($files = '', $config = '', $show_error = TRUE)
	{
		if (empty($config))
		{
			$config = array(
				'upload_path'      => $this->config->item('sts_support_upload_folder_path'),
				'allowed_types'    => $this->config->item('sts_support_upload_download_types'),
				'max_size'         => $this->config->item('sts_support_max_upload_size'),
				'encrypt_name'     => FALSE,
				'remove_spaces'    => TRUE,
				'file_ext_tolower' => TRUE,
			);
		}

		$this->load->library('upload', $config);

		$this->upload->initialize($config);

		if ($this->upload->do_upload($files))
		{
			//return the upload information on success
			$row = array(
				'file_data' => $this->upload->data(),
				'success'   => TRUE,
				'msg'       => lang('file_uploaded_successfully'),
			);
		}
		else
		{
			$row = array(
				'error' => TRUE,
				'msg'   => $this->upload->display_errors(),
			);

			if ($show_error == TRUE)
			{
				//error! could not upload file
				show_error($row['msg']);
			}
		}


		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $file
	 * @param string $path
	 * @return array
	 */
	public function unzip($file = '', $path = '')
	{
		if (class_exists('ZipArchive'))
		{
			//check if folder is writable
			if (is_writable($path))
			{
				$zip = new ZipArchive;
				$res = $zip->open($file);
				if ($res === TRUE)
				{
					$zip->extractTo($path);
					if ($zip->status == '0')
					{
						$zip->close();

						return array('success'  => TRUE,
						             'file' => $file,
						             'msg_text' => lang('archive_unzipped_successfully'));
					}
					else
					{
						$zip->close();

						return array('error' => TRUE, 'msg_text' => 'could_not_extract_zip_file');
					}
				}
				else
				{
					return array('error' => TRUE, 'msg_text' => 'could_not_open_zip_file');
				}
			}
			else
			{
				return array('error' => TRUE, 'msg_text' => $path . ' ' . lang('folder_not_writable'));
			}
		}
		else
		{
			return array('error' => TRUE, 'msg_text' => lang('ziparchive_not_installed'));
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $type
	 * @param string $file_name
	 * @return bool|false|string
	 */
	public function validate_uploads($type = '', $file_name = 'files')
	{
		switch ($type)
		{
			case 'support':

				if (!empty($_FILES))
				{
					$files = $_FILES;
					$count = count($_FILES['files']['name']);
					for ($i = 0; $i < $count; $i++)
					{
						$vars = array('name'     => $files['files']['name'][$i],
						              'type'     => $files['files']['type'][$i],
						              'tmp_name' => $files['files']['tmp_name'][$i],
						              'error'    => $files['files']['error'][$i],
						              'size'     => $files['files']['size'][$i],
						);

						if ($vars['error'] == 0)
						{
							$this->form_validation->reset_validation();
							$this->form_validation->set_data($vars);
							$this->form_validation->set_rules('name', 'lang:name', 'trim|required|xss_clean|valid_ext|min_length[4]|max_length[255]');

							$this->form_validation->set_message('valid_ext', lang('invalid_file_extension'));

							if (!$this->form_validation->run())
							{
								show_error(validation_errors());
							}
						}
					}
				}

				//all valid, lets initiialize!
				//set the config array for uploads
				$config = array(
					'upload_path'      => $this->config->item('sts_support_upload_folder_path'),
					'allowed_types'    => $this->config->item('sts_support_upload_download_types'),
					'max_size'         => $this->config->item('sts_support_max_upload_size'),
					'encrypt_name'     => FALSE,
					'remove_spaces'    => TRUE,
					'file_ext_tolower' => TRUE,
				);

				$row = array();
				for ($i = 0; $i < $count; $i++)
				{
					$_FILES['files']['name'] = $files['files']['name'][$i];
					$_FILES['files']['type'] = $files['files']['type'][$i];
					$_FILES['files']['tmp_name'] = $files['files']['tmp_name'][$i];
					$_FILES['files']['error'] = $files['files']['error'][$i];
					$_FILES['files']['size'] = $files['files']['size'][$i];

					if (!empty($files['files']['name'][$i]))
					{
						$a = $this->upload_file('files', $config);

						array_push($row, $a);
					}
				}

				break;

			case 'cart':

				//set the config array for uploads
				$config = array(
					'upload_path'      => $this->config->item('sts_products_upload_folder_path'),
					'allowed_types'    => $this->config->item('sts_products_upload_types'),
					'max_size'         => $this->config->item('sts_products_max_upload_size'),
					'encrypt_name'     => $this->config->item('encrypt_cart_uploads'),
					'remove_spaces'    => TRUE,
					'file_ext_tolower' => TRUE,
				);

				$row = $this->upload_file('files', $config, FALSE);

				break;

			case 'downloads':

				//set the config array for uploads
				$config = array(
					'upload_path'      => $this->config->item('sts_site_download_file_path'),
					'allowed_types'    => $this->config->item('sts_site_download_allowed_file_types'),
					'max_size'         => $this->config->item('sts_site_max_upload_size'),
					'encrypt_name'     => FALSE,
					'remove_spaces'    => TRUE,
					'file_ext_tolower' => TRUE,
				);

				$row = $this->upload_file('files', $config, FALSE);

				break;

			case 'data_import':

				//set the config array for uploads
				$config = array(
					'upload_path'      => $this->config->item('sts_data_import_folder'),
					'allowed_types'    => $this->config->item('sts_data_import_allowed_file_types'),
					'max_size'         => $this->config->item('sts_site_max_upload_size'),
					'encrypt_name'     => FALSE,
					'remove_spaces'    => TRUE,
					'file_ext_tolower' => TRUE,
				);

				$row = $this->upload_file('files', $config, FALSE);

				break;

			case 'languages':
			case 'modules':
			case 'themes':

				//set the config array for uploads
				$config = array(
					'upload_path'      => $this->config->item('sts_data_import_folder'),
					'allowed_types'    => 'zip',
					'max_size'         => $this->config->item('sts_site_max_upload_size'),
					'encrypt_name'     => FALSE,
					'remove_spaces'    => TRUE,
					'file_ext_tolower' => TRUE,
				);

				$row = $this->upload_file('files', $config, FALSE);

				break;

			case 'account':

				//set the config array for uploads
				$config = array(
					'upload_path'      => DEFAULT_PHOTO_MEMBERS_UPLOAD_PATH,
					'allowed_types'    => $this->config->item('sts_site_upload_photo_types'),
					'max_size'         => $this->config->item('sts_image_max_photo_size'),
					'encrypt_name'     => FALSE,
					'remove_spaces'    => TRUE,
					'file_ext_tolower' => TRUE,
					'max_width'        => DEFAULT_PHOTO_MAX_WIDTH,
					'max_height'       => DEFAULT_PHOTO_MAX_HEIGHT,
				);

				$row = $this->upload_file('files', $config, FALSE);

				break;

			case 'updates':

				//set the config array for uploads
				$config = array(
					'upload_path'      => DEFAULT_FILE_UPDATES_UPLOAD_PATH,
					'allowed_types'    => DEFAULT_FILE_UPDATES_UPLOAD_TYPE,
					'max_size'         => $this->config->item('sts_site_max_upload_size'),
					'encrypt_name'     => FALSE,
					'remove_spaces'    => TRUE,
					'file_ext_tolower' => TRUE,
				);

				$row = $this->upload_file('files', $config, FALSE);

				break;

			case 'content':

				//set the config array for uploads
				$config = array(
					'upload_path'      => DEFAULT_CONTENT_UPLOAD_PATH,
					'allowed_types'    => $this->config->item('sts_site_upload_photo_types'),
					'max_size'         => $this->config->item('sts_image_max_photo_size'),
					'encrypt_name'     => $this->config->item('encrypt_content_upload_images'),
					'remove_spaces'    => TRUE,
					'file_ext_tolower' => TRUE,
				);

				$row = $this->upload_file($file_name, $config, FALSE);

				break;
		}

		return empty($row) ? FALSE : sc($row);
	}
}
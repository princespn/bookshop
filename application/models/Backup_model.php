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
class Backup_Model extends CI_Model
{
	/**
	 * Backup the database
	 *
	 * @return array
	 */
	public function backup_db()
	{
		$home = $this->config->slash_item('sts_backup_path');
		$a = ''; $b = '';

		if (is_writable($home))
		{
			$this->check_total_backups('db');

			$date = get_time() . '-' . date('M-d-Y_G:i:s', get_time());
			$filename = 'db_' . $this->db->database . '_' . $date . '.sql';

			$com = "mysqldump --opt --host=".$this->db->hostname." --user='".$this->db->username . "' --password='".$this->db->password."' ".$this->db->database . " > $home$filename";

			@exec($com, $a, $b);

			if (!empty($b))
			{
				//log error
				$row = array('error' => TRUE,
				             'msg_text' => lang('could_not_run_database_backup'));
			}
			else
			{
				$row = array(
					'msg_text' => lang('database_backed_up_successfully'),
					'success'  => TRUE,
				);
			}
		}
		else
		{
			//log error
			$row = array('error' => TRUE,
			             'msg_text' => lang('backup_path_not_writeable'));
		}

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * Backup files
	 *
	 * @param false|string $path
	 * @return array
	 */
	public function backup_files($path = PUBPATH)
	{
		if (is_writable($this->config->slash_item('sts_backup_path')))
		{
			$this->check_total_backups('files');

			$date = get_time() . '-' . date('M-d-Y', get_time());

			$folder = str_replace(array('/', '\\'), '_', rtrim($path, '/'));
			$filename = 'files' . $folder . '_' . $date . '.zip';

			$this->zip->read_dir($path, FALSE);

			if ($this->zip->archive($this->config->slash_item('sts_backup_path') . $filename))
			{
				$row = array(
					'msg_text' => lang('files_backed_up_successfully'),
					'success'  => TRUE,
				);
			}
			else
			{
				$row = array('error'    => TRUE,
				             'msg_text' => lang('could_not_archive_files'));
			}
		}
		else
		{
			//log error
			$row = array('error' => TRUE,
			             'msg_text' => lang('backup_path_not_writeable'));
		}

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * Restore the database from backup
	 *
	 * @param string $file
	 * @return array
	 */
	public function restore_db($file = '')
	{
		$home = $this->config->item('sts_backup_path');

		if (file_exists($home . '/' . $file))
		{
			$cmd = exec("mysql --host=" . $this->db->hostname . " --user=" . $this->db->username . " --password=" . $this->db->password . " " . $this->db->database . " < $home/$file", $a, $b);

			if (!empty($a))
			{
				//log error
				$row = array('error' => TRUE,
				             'msg_text' => lang('could_not_run_restore_database'));
			}
			else
			{
				$row = array(
					'msg_text' => lang('database_restored_successfully'),
					'success'  => TRUE,
				);
			}
		}

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $type
	 * @return array|bool
	 */
	public function get_backups($type = 'db')
	{
		if ($handle = @opendir($this->config->item('sts_backup_path')))
		{
			$backups = array();
			while (FALSE !== ($file = readdir($handle)))
			{
				$ext = explode('.', $file);

				if ($type == 'db')
				{
					if (end($ext) == 'sql')
					{
						array_push($backups, $file);
					}
				}
				elseif ($type == 'files')
				{
					if (end($ext) == 'zip')
					{
						array_push($backups, $file);
					}
				}
			}
			closedir($handle);

			if (!empty($backups))
			{
				rsort($backups);

				$output = array_slice($backups, 0, 24);

				return $output;
			}
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Check number of backups to be saved
	 *
	 * @param string $type
	 */
	public function check_total_backups($type = 'db')
	{
		$total_backups = $this->get_backups($type);

		if (!empty($total_backups))
		{
			rsort($total_backups);
		}

		if (count(array($total_backups)) >= config_item('max_backup_files'))
		{
			@unlink($this->config->slash_item('sts_backup_path') . end($total_backups));
		}
	}
}

/* End of file Backup_model.php */
/* Location: ./application/models/Backup_model.php */
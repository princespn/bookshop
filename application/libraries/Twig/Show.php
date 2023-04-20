<?php if (!defined('BASEPATH'))
{
	exit('No direct script access allowed');
}
/**
 * Library to wrap Twig layout engine. Originally from Bennet Matschullat.
 * Code cleaned up to CodeIgniter standards by Erik Torsner
 *
 * PHP Version 5.3
 *
 * @category Layout
 * @package  Twig
 * @author   Bennet Matschullat <bennet@3mweb.de>
 * @author   Erik Torsner <erik@torgesta.com>
 * @license  Don't be a dick http://www.dbad-license.org/
 * @link     https://github.com/bmatschullat/Twig-Codeigniter
 */

/**
 * Main (and only) class for the Twig wrapper library
 *
 * @category Layout
 * @package  Twig
 * @author   Bennet Matschullat <hello@bennet-matschullat.com>
 * @author   Erik Torsner <erik@torgesta.com>
 * @license  Don't be a dick http://www.dbad-license.org/
 * @link     https://github.com/bmatschullat/Twig-Codeigniter
 */
class Show
{
	/**
	 * constructor of twig ci class
	 */
	public function __construct()
	{
		$this->_ci = &get_instance();

		// set include path for twig
		//ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APPPATH . 'third_party/Twig');
		require_once (string)'Autoloader.php';
		// register autoloader
		Twig_Autoloader::register();
		log_message('debug', 'twig autoloader loaded');
	}

	/**
	 * render a twig template file
	 *
	 * @param string $template template name
	 * @param array $data contains all varnames
	 * @param boolean $render render or return raw?
	 *
	 * @return void
	 *
	 */
	public function render($template, $data = array(), $render = TRUE)
	{
		$template = $this->_twig_env->loadTemplate($template);
		log_message('debug', 'twig template loaded');

		return ($render) ? $template->render($data) : $template;
	}


	/**
	 * @param string $page
	 * @param array $data
	 * @throws Twig_Error_Loader
	 * @throws Twig_Error_Syntax
	 */
	public function page($page = '', $data = array())
	{
		switch ($page)
		{
			case '404':
			case 'blocked':

				set_status_header('404');

				break;

			case 'error':

				set_status_header('500');

				break;
		}

		$this->display('home', $page, $data);
	}

	/**
	 * @param array $a
	 * @param string $template
	 * @param bool $raw
	 * @return string
	 * @throws Twig_Error_Loader
	 * @throws Twig_Error_Syntax
	 */
	public function parse_tpl($a = array(), $template = '', $raw = TRUE)
	{
		//set the custom language entries
		$b = $this->_ci->config->item('custom_language_entries');

		$d = !empty($b) ? array_merge($a, $b) : $a;

		//$d = array_merge($c, $_SESSION);

		$file_tpl = new Twig_Loader_Array(array(
			'index.php' => $template,
		));

		$this->_twig_env = new Twig_Environment($file_tpl, array(
			'cache' => !$this->_ci->config->item('sts_site_enable_template_cache') ? FALSE : $this->_ci->config->item('tpl_cache_path'),
			'charset' => $this->_ci->config->item('charset'),
			'auto_reload' => TRUE,
			'autoescape' => $this->_ci->config->item('tpl_enable_autoescape'),
			'strict_variables' => $this->_ci->config->item('tpl_strict_variables'),
			'debug' => FALSE,
		));

		$this->_twig_env->addExtension(new Twig_Extension_StringLoader());
		$this->ci_function_init();

		$template = $this->_twig_env->loadTemplate('index.php');

		try
		{
			if ($raw == TRUE)
			{
				return $template->render($d);
			}
			else
			{
				$this->_ci->output->set_output($template->render($d));

				if ($this->_ci->config->item('enable_db_debugging'))
				{
					$this->_ci->output->enable_profiler(TRUE);
				}
			}
		} catch (Twig_Error_Loader $e)
		{
			show_error($e->getMessage());
		}
	}


	/**
	 * Execute the template and send to CI output
	 *
	 * @param string $cat
	 * @param string $template
	 * @param array $a
	 * @param bool $raw
	 * @param string $full_path
	 * @return string
	 * @throws Twig_Error_Loader
	 * @throws Twig_Error_Syntax
	 */
	public function display($cat = '', $template = '', $a = array(), $raw = FALSE, $full_path = '')
	{
		$cat = strtolower($cat);

		//set template
		$template .= '.' . TPL_FILE_EXT;
		$template = strtolower($template);

		//set the custom language entries
		if ($b = $this->_ci->config->item('custom_language_entries'))
		{
			$a = array_merge($a, $b);
		}

		$d = array_merge($a, $_SESSION);

		//set the default template folder path
		$tpl_dir = APPPATH . 'views/site';
		$this->_ci->load->config('twig');

		// load environment
		$file_tpl = new Twig_Loader_Filesystem($tpl_dir, APPPATH . 'cache');

		//check if a custom templates folder exists
		if (file_exists($full_path))
		{
			$tpl_dir = array($full_path, $tpl_dir);
		}
		else
		{
			$path = PUBPATH . '/themes/site/' . $this->_ci->config->item('layout_design_site_theme') . '/custom_templates';
			$tpl_dir = array($path, $tpl_dir);

			if (file_exists(PUBPATH . '/themes/site/' . $this->_ci->config->item('layout_design_site_theme') . '/custom_templates/' . $cat . '/custom_' . $template))
			{
				$template = 'custom_' . $template;
			}
		}

		$file_tpl->setPaths($tpl_dir);

		$n = array();

		//get custom templates if any
		$c = $this->_ci->config->item('db_templates');

		if (!empty($c))
		{
			foreach ($c as $k => $v)
			{
				$t = $v['template_name'];

				//get template folders
				$folders = directory_map('./application/views/site/', '1');

				foreach ($folders as $f)
				{
					//get templates from each folder
					$map = directory_map('./application/views/site/' . $f . '/');

					//check for custom extended templates and load them.
					if (in_array($t, $map))
					{
						$v['template_name'] = new Twig_Loader_Array(array(
							$v['template_category'] . '/' . $v['template_name'] => $v['template_data'],
						));

						array_push($n, $v['template_name']);
					}
				}

				//now load the main template
				if ($v['template_category'] == $cat && $v['template_name'] == $template)
				{
					$file_tpl_custom = new Twig_Loader_Array(array(
						$cat . '/' . $template => $v['template_data'],
					));

					array_push($n, $file_tpl_custom);
				}
			}
		}

		//lets add all loaders
		array_push($n, $file_tpl);

		//now load the twig chain
		$file_tpl = new Twig_Loader_Chain($n);

		$this->_twig_env = new Twig_Environment($file_tpl, array(
			'cache' => !$this->_ci->config->item('sts_site_enable_template_cache') ? FALSE : $this->_ci->config->item('tpl_cache_path'),
			'charset' => $this->_ci->config->item('charset'),
			'auto_reload' => TRUE,
			'autoescape' => $this->_ci->config->item('tpl_enable_autoescape'),
			'strict_variables' => $this->_ci->config->item('tpl_strict_variables'),
			'debug' => FALSE,
		));

		$this->_twig_env->addExtension(new Twig_Extension_StringLoader());
		$this->ci_function_init();

		$template = $this->_twig_env->loadTemplate($cat . '/' . $template);
		/*
				header("Cache-control: private"); // Fix for IE
				header('Cache-Control: no-store, no-cache, must-revalidate');
				header('Cache-Control: post-check=0, pre-check=0', FALSE);
				header('Pragma: no-cache');
		*/

		try
		{
			if ($raw == TRUE)
			{
				return $template->render($d);
			}
			else
			{
				$this->_ci->output->set_output($template->render($d));

				if ($this->_ci->config->item('enable_db_debugging'))
				{
					$this->_ci->output->enable_profiler(TRUE);
				}
			}
		} catch (Twig_Error_Loader $e)
		{
			show_error($e->getMessage());
		}
	}

	/**
	 * Initialize standard CI functions
	 *
	 * @return void
	 */
	public function ci_function_init()
	{
		$this->_ci->load->config('twig');

		foreach ($this->_ci->config->item('twig_functions_array') as $v)
		{
			$this->_twig_env->addFunction($v, new Twig_Function_Function($v));
		}
	}

	/**
	 * Entry point for controllers (and the likes) to register
	 * callback functions to be used from Twig templates
	 *
	 * @param string $name name of function
	 * @param Twig_FunctionInterface $function Function pointer
	 *
	 * @return void
	 *
	 */
	public function register_function($name, Twig_FunctionInterface $function)
	{
		$this->_twig_env->addFunction($name, $function);
	}

	/**
	 * Reference to code CodeIgniter instance.
	 *
	 * @var CodeIgniter object
	 */
	private $_ci;
	/**
	 * Twig environment see http://twig.sensiolabs.org/api/v1.8.1/Twig_Environment.html.
	 *
	 * @var Twig_Envoronment object
	 */
	private $_twig_env;
}



/* End of file Show.php */
/* Location: ./application/libraries/Show.php */

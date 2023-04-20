<?php defined('BASEPATH') OR exit('No direct script access allowed');
/*
file: /application/core/MY_Security.php
*/

class JX_Security extends CI_Security {

    public function csrf_verify()
    {
        // Check if URI has been whitelisted from CSRF checks
	    $exclude_uris = array(
		    array(
			    "controller" => array('api', 'site_builder'),
			    "method" => array('get', 'post', 'delete', 'save_cover', 'save_image_module', 'save_images'),
		    ),
	    );

	    $uri = load_class('URI', 'core');

	    // assumes /controller/method in your url. adjust as needed.
	    $parts = explode("/",$uri->uri_string());

	    if (count($parts) >= 2) {

		    if (count($parts) > 2 && ($parts[0] == ADMIN_ROUTE || $parts[0] == CHECKOUT_ROUTE))
		    {
			    $class=$parts[1];
			    $method=$parts[2];
		    }
		    else
		    {
			    $class=$parts[0];
			    $method=$parts[1];
		    }

		    foreach($exclude_uris as $exclude_url_data) {

			    if (in_array($class, @$exclude_url_data['controller']) && (in_array($method, @$exclude_url_data['method'])))
			    {
				    return $this;
			    }
		    }
	    }

        return parent::csrf_verify();
    }

	public function csrf_show_error()
	{
		// show_error('The action you have requested is not allowed.');  // default code
		if (!empty($_SERVER[ 'HTTP_X_REQUESTED_WITH' ]) && strtolower($_SERVER[ 'HTTP_X_REQUESTED_WITH' ]) == 'xmlhttprequest')
		{
			echo json_encode(array('type' => 'error', 'msg' => '<script>location.reload()</script>Connection Token Needs To Be Reset.  Please Reload Your Browser'));
			exit;
		}

		header('Location: ' . htmlspecialchars($_SERVER['REQUEST_URI']), TRUE, 200);
	}
}

/* End of file JX_Security.php */
/* Location: ./application/core/JX_Security.php */
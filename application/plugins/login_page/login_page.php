<?php
//after logging in, run a login page plugin

add_action('login_page', 'login_page', 10); //method, function, priority

function login_page($data = array())
{
	//sample login page code

	//pr($data);
}
?>
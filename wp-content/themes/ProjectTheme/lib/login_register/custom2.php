<?php
/*****************************************************************************
*
*	copyright(c) - sitemile.com - ProjectTheme
*	More Info: http://sitemile.com/p/project
*	Coder: Saioc Dragos Andrei
*	Email: andreisaioc@gmail.com
*
******************************************************************************/

	include 'sett.php';
	include 'login.php';
	include 'register.php';
	

	add_action('init', 'ProjectTheme_do_login_register_init', 99);
	
	//=======================================================
	
		function ProjectTheme_do_login_register_init()
		{
		  global $pagenow;
		
			if(isset($_GET['action']) && $_GET['action'] == "register")
			{
				if(is_user_logged_in()) { wp_redirect(get_permalink(get_option('ProjectTheme_my_account_page_id'))); }
				ProjectTheme_do_register_scr();	
			}
		
		  switch ($pagenow)
		  {
			  
			case "wp-login.php":
			
			  if(is_user_logged_in()) { wp_redirect(get_permalink(get_option('ProjectTheme_my_account_page_id'))); } 	
			  ProjectTheme_do_login_scr();
			break;
			case "wp-register.php":
				
	
			if(is_user_logged_in()) { wp_redirect(get_permalink(get_option('ProjectTheme_my_account_page_id'))); }	
			  ProjectTheme_do_register_scr();
			break;
		  }
		}
		
	//=========================================================	



?>
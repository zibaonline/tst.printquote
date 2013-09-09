<?php
/*
Plugin Name: Cartpauj Register Captcha
Plugin URI: http://cartpauj.icomnow.com/projects/cartpauj-register-captcha-plugin/
Description: Adds a captcha form to WordPress registration page to prevent SPAM registrations.
Version: 1.0.01
Author: Cartpauj
Author URI: http://cartpauj.icomnow.com/
Copyright: 2009-2011, cartpauj

GNU General Public License, Free Software Foundation <http://creativecommons.org/licenses/GPL/2.0/>
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

add_action('register_form', 'crcc_add_to_form', 1000);
function crcc_add_to_form()
{
	include_once("shared.php");
	include_once("captcha_code.php");
  
	$plugin_url = WP_PLUGIN_URL.'/cartpauj-register-captcha/';
	$crcc_captcha = new CaptchaCode();
	$crcc_code = crcc_str_encrypt($crcc_captcha->generateCode(6));
  
	echo
			'<p>
				<label>Enter Code:</label>
					<img style="width:120px !important; padding-bottom: 10px;" src="'.$plugin_url.'captcha_images.php?width=120&height=40&code='.$crcc_code.'" /><br>
					<input type="text" name="crcc_security_code" class="do_input" value="" style="margin-left: 135px;" size="20" tabindex="1000"><br/>
					<input type="hidden" name="crcc_security_check" value="'.$crcc_code.'">
				
        
        <label id="crcc_hp_label">HP<br/>
          <input type="text" name="crcc_hp" value="" class="input" size="20" tabindex="1001" />
        </label>
        
        <script>
          document.getElementById("crcc_hp_label").style.display="none";
        </script>
			</p>';
}

add_action('register_post', 'crcc_check_code', 10, 3);
function crcc_check_code($login, $email, $errors)
{
	include_once("shared.php");
  
	$crcc_code = crcc_str_decrypt($_POST['crcc_security_check']);
  
	if(($crcc_code != $_POST['crcc_security_code']) && (!empty($crcc_code)))
		$errors->add('crcc_error',__('<strong>ERROR</strong>: The code you entered was incorrect, please try again.'));
  
  if(!isset($_POST['crcc_hp']) || !empty($_POST['crcc_hp']))
    $errors->add('crcc_error2', __('<strong>ERROR</strong>: Fatal error occurred.'));
}

/************************************************************************************
* TO REPLACE THE WORDPRESS LOGO ON SIGNUP PAGE: Replace the logo-login.png file in
* this plugin directory with an image of your logo. Image must be 274x63 pixels
* and in .png format. Once you have replaced the file, just uncomment the line
* below this box by removing the '//' in front of add_action. Done!
************************************************************************************/
//add_action('login_head', 'crcc_login_head');
function crcc_login_head()
{
	$plugin_url = WP_PLUGIN_URL.'/cartpauj-register-captcha/';
  
	echo
			'<style type="text/css">
				h1 a
				{
					background:url("'.$plugin_url.'logo-login.png") no-repeat scroll center top transparent !important;
				}
			</style>';
}
?>

<?php
/*****************************************************************************
*
*	copyright(c) - sitemile.com - ProjectTheme
*	More Info: http://sitemile.com/p/project
*	Coder: Saioc Dragos Andrei
*	Email: andreisaioc@gmail.com
*
******************************************************************************/	
		
		function ProjectTheme_do_register_scr()
		{
		  global $wpdb, $wp_query, $current_theme_locale_name;
		
		  if (!is_array($wp_query->query_vars))
			$wp_query->query_vars = array();
		
		header('Content-Type: '.get_bloginfo('html_type').'; charset='.get_bloginfo('charset'));
		
		session_start();
		  switch( $_REQUEST["action"] ) 
		  {
			
			case 'register':
			  require_once( ABSPATH . WPINC . '/registration-functions.php');
			  
				$user_login = sanitize_user( str_replace(" ","",$_POST['user_login']) );
				$user_email = trim($_POST['user_email']);
			
				$sanitized_user_login = $user_login;
		
			
				
				$errors = Project_register_new_user_sitemile($user_login, $user_email);
				
					if (!is_wp_error($errors)) 
					{	
						$ok_reg = 1;						
					}	
					
				
			  if ( 1 == $ok_reg ) 
			  {//continues after the break; 
		
				get_header();
				global $current_theme_locale_name;	
				
		?>
				
				<div class="my_box3">
            	<div class="padd10">
            
            	<div class="box_title"><?php _e('Registration Complete',$current_theme_locale_name) ?></div>
							<p><?php printf(__('Username: %s',$current_theme_locale_name), "<strong>" . wp_specialchars($user_login) . "</strong>") ?><br />
							<?php printf(__('Password: %s',$current_theme_locale_name), '<strong>' . __('emailed to you',$current_theme_locale_name) . '</strong>') ?> <br />
							<?php printf(__('E-mail: %s',$current_theme_locale_name), "<strong>" . wp_specialchars($user_email) . "</strong>") ?><br /><br />
							<?php _e("Please check your <strong>Junk Mail</strong> if your account information does not appear within 5 minutes.",$current_theme_locale_name); ?>
                            </p>
							<p class="submit"><a href="wp-login.php" class="post_bid_btn_new"><?php _e('Step 2: Login To Your Account', $current_theme_locale_name); ?></a></p>
						</div></div>
		<?php
								
				
				get_footer();
		
				die();
			break;
			  }//continued from the error check above
		
			default:
			get_header();
			
		
				
				?>
				
				<div class="my_box3">
            	<div class="padd10">
            
            	<div class="box_title"><?php _e("Request",$current_theme_locale_name); ?> a Quote</div>
                <div class="box_content">
				
				There are a few things you should be aware of before requesting your first print quote:
<ul style="line-height:21px;">
	<li>PrintQuote is a completely FREE service</li>
	<li>You have the option to keep your print jobs private or make them public</li>
	<li>You're the only person who will see your print quotes</li>
	<li>You don't need to select the lowest price as the winner</li>
	<li>You can upload images and documents as part of your quote request</li>
	<li>You'll be notified by email when quotes are submitted</li>
	<li>You pay the printer direct as per their terms</li>
	<li>More detailed information can be found in our <a title="FAQs" href="http://www.printquote.co.nz/faqs/" target="_blank">FAQs</a></li>
</ul>
To get started you must first register using the form below:                                     
						  
						  <?php if ( isset($errors) && isset($_POST['action']) ) : ?>
						  <div class="error">
							<ul>
							<?php
							foreach($errors as $error) {
							if(count($error) > 0) {
							
							foreach($error as $e) echo "<li>".$e[0]."</li>";
							
							
							}
							}
							?>
							</ul>
						  </div>
						  <?php endif; ?>
						  <div class="login-submit-form">
                          
                          
                          <form method="post" id="loginform">
						  <input type="hidden" name="action" value="register" />	
							
							<p>
                            <label for="register-username"><?php _e('Username:',$current_theme_locale_name) ?></label>
							<input type="text" class="do_input" name="user_login" id="user_login" size="30" maxlength="20" value="<?php echo wp_specialchars($user_login); ?>" />
							</p>							
					
							<p>							 
							<label for="register-email"><?php _e('E-mail:',$current_theme_locale_name) ?></label>
							<input type="text" class="do_input" name="user_email" id="user_email" size="30" maxlength="100" value="<?php echo wp_specialchars($user_email); ?>" />
							</p>
							
                            <?php
							
								$ProjectTheme_enable_2_user_tp = get_option('ProjectTheme_enable_2_user_tp');
								if($ProjectTheme_enable_2_user_tp == "yes"):
								
								$enbl = true;
								$enbl = apply_filters('ProjectTheme_enbl_two_user_types_thing',$enbl);
								
								if($enbl):
							?>                           
                            
                            <!---<p>							 
							<label for="register-email"><?php _e('Type of account:',$current_theme_locale_name) ?></label>
							 <input type="radio" class="do_input" name="user_tp" id="user_tp" value="service_provider" checked="checked" /> <?php _e('Print Business',$current_theme_locale_name); ?><br/> --->
                            <input type="hidden" class="do_input" name="user_tp" id="user_tp" value="business_owner" /> <!--- <?php _e('Print Client',$current_theme_locale_name); ?><br/> 
							</p>--->
                            
                            
                            
                            <?php endif; endif; ?>
                            
                           
							
                        
							<?php do_action('register_form'); ?>

							<p><label for="submitbtn">&nbsp;</label>
							<?php _e('<b>NOTE:</b> A password will be emailed to you.',$current_theme_locale_name) ?></p>
							

                          
							
						<p class="submit">
                        <label for="submitbtn">&nbsp;</label>
							 <input type="submit" class="submit_bottom" value="<?php _e('Step 1: Register',$current_theme_locale_name) ?>" id="submits" name="submits" />
						</p>
                          
						  <ul id="logins">
							<li><a href="<?php bloginfo('home'); ?>/" title="<?php _e('Are you lost?',$current_theme_locale_name) ?>"><?php _e('Home',$current_theme_locale_name) ?></a></li>
							<li><a href="<?php bloginfo('wpurl'); ?>/wp-login.php"><?php _e('Login',$current_theme_locale_name) ?></a></li>
							<li><a href="<?php bloginfo('wpurl'); ?>/wp-login.php?action=lostpassword" title="<?php _e('Password Lost?',$current_theme_locale_name) ?>"><?php _e('Lost your password?',$current_theme_locale_name) ?></a></li>
						  </ul>
						</div>
                        
                        
                        
                        </div>
                        </div>
                        </div>
                        
                        
		<?php
				
				
	 			  get_footer();
		
			  die();
			break;
			case 'disabled':
     
	 			  get_header();
				
			
		?>
        <div class="clear10"></div>	
				<div class="my_box3">
            	<div class="padd10">

        <div class="box_title"><?php _e('Registration Disabled',$current_theme_locale_name) ?></div>
                <div class="box_content">
                                                  
							
							<p><?php _e('User registration is currently not allowed.',$current_theme_locale_name) ?><br />
							<a href="<?php echo get_settings('home'); ?>/" title="<?php _e('Go back to the blog',$current_theme_locale_name) ?>"><?php _e('Home',$current_theme_locale_name) ?></a>
							</p>
						</div></div></div>
		<?php
				
				 get_footer();
		
			  die();
			break;
		  }
		}




//===================================================================

function Project_register_new_user_sitemile( $user_login, $user_email ) {
	$errors = new WP_Error();
	global $current_theme_locale_name;
	$sanitized_user_login = sanitize_user( $user_login );
	$user_email = apply_filters( 'user_registration_email', $user_email );

	// Check the username
	if ( $sanitized_user_login == '' ) {
		$errors->add( 'empty_username', __( '<strong>ERROR</strong>: Please enter a username.', $current_theme_locale_name ) );
	} elseif ( ! validate_username( $user_login ) ) {
		$errors->add( 'invalid_username', __( '<strong>ERROR</strong>: This username is invalid because it uses illegal characters. Please enter a valid username.', $current_theme_locale_name ) );
		$sanitized_user_login = '';
	} elseif ( username_exists( $sanitized_user_login ) ) {
		$errors->add( 'username_exists', __( '<strong>ERROR</strong>: This username is already registered, please choose another one.', $current_theme_locale_name ) );
	}

	// Check the e-mail address 
	if ( $user_email == '' ) {
		$errors->add( 'empty_email', __( '<strong>ERROR</strong>: Please type your e-mail address.', $current_theme_locale_name ) );
	} elseif ( ! is_email( $user_email ) ) {
		$errors->add( 'invalid_email', __( '<strong>ERROR</strong>: The email address isn&#8217;t correct.', $current_theme_locale_name ) );
		$user_email = '';
	} elseif ( email_exists( $user_email ) ) {
		$errors->add( 'email_exists', __( '<strong>ERROR</strong>: This email is already registered, please choose another one.', $current_theme_locale_name ) );
	}

	do_action( 'register_post', $sanitized_user_login, $user_email, $errors );

	$errors = apply_filters( 'registration_errors', $errors, $sanitized_user_login, $user_email );

	if ( $errors->get_error_code() )
		return $errors;
	
	//--------------------
	
	$user_tp = $_POST['user_tp'];
	if(empty($user_tp)) $capa = 'subscriber';
	else $capa = $user_tp;
	
	//--------------------
	
	$user_pass = wp_generate_password( 12, false);
	
	$user_id = wp_create_user( $sanitized_user_login, $user_pass, $user_email, $capa );
	if ( ! $user_id ) {
		$errors->add( 'registerfail', sprintf( __( '<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !', $current_theme_locale_name ), get_option( 'admin_email' ) ) );
		return $errors;
	}
	
	//---------------------
	
	$user = new WP_User($user_id);
	$user->set_role($capa);
	
	//---------------------
	
	update_user_meta( $user_id, 'user_tp', $user_tp );
	
	update_user_option( $user_id, 'default_password_nag', true, true ); //Set up the Password change nag.

	ProjectTheme_new_user_notification($user_id, $user_pass );
	ProjectTheme_new_user_notification_admin($user_id);
	
	return $user_id;
}

?>
<?php
/***************************************************************************
*
*	ProjectTheme - copyright (c) - sitemile.com
*	The only project theme for wordpress on the world wide web.
*
*	Coder: Andrei Dragos Saioc
*	Email: sitemile[at]sitemile.com | andreisaioc[at]gmail.com
*	More info about the theme here: http://sitemile.com/products/wordpress-project-freelancer-theme/
*	since v1.2.5.3
*
***************************************************************************/
 
 	session_start();
	global $current_user, $wp_query;
	$pid 	=  $wp_query->query_vars['pid'];
	
	function ProjectTheme_filter_ttl($title){return __("Relist Quote Request",'ProjectTheme')." - ";}
	add_filter( 'wp_title', 'ProjectTheme_filter_ttl', 10, 3 );	
	
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }   
	   
	
	get_currentuserinfo;   

	$post = get_post($pid);

	$uid 	= $current_user->ID;
	$title 	= $post->post_title;
	$cid 	= $current_user->ID;
	
	if($uid != $post->post_author) { echo 'Not your post. Sorry!'; exit; }
	
	//=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	
		global $wpdb,$wp_rewrite,$wp_query;
		$post = get_post($pid);
		$cid = $uid;
	
		$ProjectTheme_get_images_cost_extra = ProjectTheme_get_images_cost_extra($pid);
		
//-------------------------------------

	get_header();
?>


	<div id="content" >
        	
            <div class="my_box3">
            	<div class="padd10">
            
            	<div class="box_title"><?php _e("Publish Quote Request", "ProjectTheme"); ?></div>
                <div class="box_content"> 
            	
     <?php           

    	//--------------------------------------------------
	// hide project from search engines fee calculation
	
	$projectTheme_hide_project_fee = get_option('projectTheme_hide_project_fee');
	if(!empty($projectTheme_hide_project_fee))
	{
		$opt = get_post_meta($pid,'hide_project',true);
		if($opt == "0") $projectTheme_hide_project_fee = 0;
		
		
	} else $projectTheme_hide_project_fee = 0;
	

	//-------------------------------------------------------------------------------
	// sealed bidding fee calculation
	
	$projectTheme_sealed_bidding_fee = get_option('projectTheme_sealed_bidding_fee');
	if(!empty($projectTheme_sealed_bidding_fee))
	{
		$opt = get_post_meta($pid,'private_bids',true);
		if($opt == "0") { $projectTheme_sealed_bidding_fee = 0; }
		
		 
	} else $projectTheme_sealed_bidding_fee = 0;

	
	//-------
	
	$featured	 = get_post_meta($pid, 'featured', true);
	$feat_charge = get_option('projectTheme_featured_fee');
	
	if($featured != "1" ) $feat_charge = 0;
	
	update_post_meta($pid, 'featured_paid', '1');
	update_post_meta($pid, 'private_bids_paid', '0');
	update_post_meta($pid, 'hide_project_paid', '0');
	
	//--------------------------------------------
			
	$catid = ProjectTheme_get_project_primary_cat($pid);
			
	$custom_set = get_option('projectTheme_enable_custom_posting');
	if($custom_set == 'yes')
	{
		$posting_fee = get_option('projectTheme_theme_custom_cat_'.$catid);
		if(empty($posting_fee)) $$posting_fee = 0;		
	}
	else
	{
		$posting_fee = get_option('projectTheme_base_fee');
	}
	
	//-----------------------------------------------
	
	$total = $ProjectTheme_get_images_cost_extra + $feat_charge + $posting_fee + $projectTheme_sealed_bidding_fee + $projectTheme_hide_project_fee;
	
	$post 			= get_post($pid);
	$admin_email 	= get_bloginfo('admin_email');
	global $total_prc;
	$total_prc = $total;
	
	//----------------------------------------
	$finalize = isset($_GET['finalize']) ? true : false;
	
	if($total == 0 && $finalize == true)
	{
			echo '<div >';
			echo __('Thank you for posting your project with us. You can view your project by clicking the button.','ProjectTheme');
			update_post_meta($pid, "paid", "1");
			
			if(get_option('projectTheme_admin_approves_each_project') == 'yes')
			{
				
				$message = sprintf(__('A new project was posted on your website. Don`t forget you need to approve it before it goes live.<br/>
				See it here: <a href="%s">%s</a>','ProjectTheme'), get_permalink($pid), $post->post_title );
				
				//sitemile_send_email($admin_email, sprintf(__("New Project Posted - %s",'ProjectTheme'),$post->post_title), $message, 'html', 
				//get_bloginfo('name'), $admin_email);
				
				echo '<br/>'.__('Your project isn`t yet live, the admin needs to approve it.', 'ProjectTheme');
			
			}
			else
			{
				$my_post = array();
				$my_post['ID'] = $pid;
				$my_post['post_status'] = 'publish';

				wp_update_post( $my_post );
				wp_publish_post( $pid );
				
				$message = sprintf(__('A new project was posted on your website.<br/>
				See it here: <a href="%s">%s</a>','ProjectTheme'), get_permalink($pid), $post->post_title);
				
				//sitemile_send_email($admin_email, sprintf(__('New Project Posted - %s','ProjectTheme'),$post->post_title), 
				//$message, 'html', get_bloginfo('name'), $admin_email);
				
				ProjectTheme_send_email_subscription($pid);
				
			}
			echo '</div>';
			
	
	}
	else
	{
			update_post_meta($pid, "paid", "0");
			
			echo '<div >';
			echo __('Thank you for reposting your quote request with us. ', 'ProjectTheme');
			echo '</div>';
			
			
			$message = sprintf(__('A new project was posted on your website. <br/>
			See it here: <a href="%s">%s</a>','ProjectTheme'), get_permalink($pid), $post->post_title);
				
			//sitemile_send_email($admin_email, sprintf(__('New project Posted - %s','ProjectTheme'),$post->post_title), 
			//$message, 'html', get_bloginfo('name'), $admin_email);
	
	}
	
	//----------------------------------------
	
	echo '<table style="margin-top:25px">';
//	echo '<tr>';
//	echo '<td width="250">'.__('Base Fee', 'ProjectTheme').'</td>';
//	echo '<td>'.ProjectTheme_get_show_price($posting_fee,2).'</td>';
//	echo '<tr>';
	
//	if($ProjectTheme_get_images_cost_extra > 0)
//	{
//		echo '<tr>';
	//	echo '<td>'.__('Extra Images Fee', 'ProjectTheme').'</td>';
//		echo '<td>'.ProjectTheme_get_show_price($ProjectTheme_get_images_cost_extra,2).'</td>';
//		echo '<tr>';
//	}
	
//	echo '<tr>';
	//echo '<td>'.__('Featured Fee', 'ProjectTheme').'</td>';
//	echo '<td>'.ProjectTheme_get_show_price($feat_charge,2).'</td>';
//	echo '<tr>';
	
	//if(get_post_meta($pid,'private_bids',true) == "1"):
	
//		echo '<tr>';
	//	echo '<td>'.__('Sealed Bidding Fee', 'ProjectTheme').'</td>';
//		echo '<td>'.ProjectTheme_get_show_price($projectTheme_sealed_bidding_fee,2).'</td>';
//		echo '<tr>';
	
	//endif;
	
	
	//if(get_post_meta($pid,'hide_project',true) == "1"):
	
//		echo '<tr>';
		// echo '<td>'.__('Hide Project from search engines Fee', 'ProjectTheme').'</td>';
//		echo '<td>'.ProjectTheme_get_show_price($projectTheme_hide_project_fee,2).'</td>';
//		echo '<tr>';
	
	//endif;
	
	
	
//	echo '<tr>';
//	echo '<td>&nbsp;</td>';
//	echo '<td></td>';
//	echo '<tr>';
	
	
//	echo '<tr>';
//	echo '<td><strong>'.__('Total to Pay','ProjectTheme').'</strong></td>';
//	echo '<td><strong>'.ProjectTheme_get_show_price($total,2).'</strong></td>';
//	echo '<tr>';
	
	

	
//	echo '<tr>';
//	echo '<td><strong>'.__('Your Total Credits','ProjectTheme').'</strong></td>';
//	echo '<td><strong>'.ProjectTheme_get_show_price(ProjectTheme_get_credits($uid),2).'</strong></td>';
//	echo '<tr>';
	
	
//	echo '<tr>';
//	echo '<td>&nbsp;<br/>&nbsp;</td>';
//	echo '<td></td>';
//	echo '<tr>';
	
	
	if($total == 0)
	{
		//if(get_option('projectTheme_admin_approves_each_project') != 'yes' && $finalize == true):
		
			echo '<tr>';
			echo '<td></td>';
			echo '<td><a href="'.get_permalink($pid).'" class="go_back_btn">'.__('See your project','ProjectTheme') .'</a></td>';
			echo '<tr>';	
		
		//endif;
	}
	else
	{
		update_post_meta($pid,'unpaid','1');
		
		echo '<tr>';
		echo '<td colspan="1">';
		
						global $project_ID;
						$project_ID = $pid;
						
						//-------------------
						
						$ProjectTheme_paypal_enable 		= get_option('ProjectTheme_paypal_enable');
						$ProjectTheme_alertpay_enable 		= get_option('ProjectTheme_alertpay_enable');
						$ProjectTheme_moneybookers_enable 	= get_option('ProjectTheme_moneybookers_enable');
						
						if($ProjectTheme_paypal_enable == "yes")
							echo '<a href="'.get_bloginfo('siteurl').'/?p_action=paypal_listing&pid='.$pid.'" class="edit_project_pay_cls">'.__('Pay by PayPal','ProjectTheme').'</a>';
						
						if($ProjectTheme_moneybookers_enable = "yes")
							echo '<a href="'.get_bloginfo('siteurl').'/?p_action=mb_listing&pid='.$pid.'" class="edit_project_pay_cls">'.__('Pay by MoneyBookers/Skrill','ProjectTheme').'</a>';
						
						if($ProjectTheme_alertpay_enable == "yes")
							echo '<a href="'.get_bloginfo('siteurl').'/?p_action=payza_listing&pid='.$pid.'" class="edit_project_pay_cls">'.__('Pay by Payza','ProjectTheme').'</a>';
						
						do_action('ProjectTheme_add_payment_options_to_post_new_project');
						
		
		echo '</td></tr>';
	}
	
	
	
	
	
	echo '</table>'; ?>
                
                
                </div>
                </div>
                </div>
                </div>
                
	<?php ProjectTheme_get_users_links(); ?>

<?php get_footer(); ?>
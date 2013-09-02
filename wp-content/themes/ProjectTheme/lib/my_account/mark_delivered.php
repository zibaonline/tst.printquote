<?php
if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }
//-----------

	add_filter('sitemile_before_footer', 'projectTheme_my_account_before_footer');
	function projectTheme_my_account_before_footer()
	{
		echo '<div class="clear10"></div>';		
	}
	
	//----------

	global $wpdb,$wp_rewrite,$wp_query;
	$pid = $wp_query->query_vars['pid'];

	global $current_user;
	get_currentuserinfo();
	$uid = $current_user->ID;

	$post_pr = get_post($pid);
	
	//---------------------------
	
	$winner_bd = projectTheme_get_winner_bid($pid);
	if($uid != $winner_bd->uid) { wp_redirect(get_bloginfo('siteurl')); exit; }
	
	//---------------------------
	
	if(isset($_POST['yes']))
	{
		$tm = current_time('timestamp',0);

		update_post_meta($pid, 'mark_coder_delivered',		"1");
		update_post_meta($pid, 'mark_coder_delivered_date',		$tm);
		
		//------------------------------------------------------------------------------
		
		ProjectTheme_send_email_on_delivered_project_to_bidder($pid, $uid);
		ProjectTheme_send_email_on_delivered_project_to_owner($pid);
		
		wp_redirect(get_permalink(get_option('ProjectTheme_my_account_outstanding_projects_id')));
		exit;
	}
	
	if(isset($_POST['no']))
	{
		wp_redirect(get_permalink(get_option('ProjectTheme_my_account_outstanding_projects_id')));
		exit;			
	}
	
	
	
	//---------------------------------
	
	get_header();

?>
<div class="clear10"></div>

	
			<div class="my_box3">
            	<div class="padd10">
            
            	<div class="box_title"><?php  printf(__("Mark Job Delivered: %s",'ProjectTheme'), $post_pr->post_title); ?></div>
                <div class="box_content">   
               <?php
			   
			   printf(__("You are about to mark this job Delivered.",'ProjectTheme'), $post_pr->post_title); echo '<br/>';
			  _e("<p>Your client will be notified by email to accept the delivery. When this happens it will move your job to Completed and end the process.</p><p>Remember to leave feedback on the client to help you and others in the future.",'ProjectTheme') ;
			   
			   ?> 
                
                <div class="clear10"></div>
               
               <form method="post"  > 
                
               <input type="submit" name="yes" value="<?php _e("Yes, Mark Delivered!",'ProjectTheme'); ?>" />
               <input type="submit" name="no"  value="<?php _e("No",'ProjectTheme'); ?>" />
                
               </form>
    </div>
			</div>
			</div>
        
        
        <div class="clear100"></div>
            
            
<?php

get_footer();

?>                        
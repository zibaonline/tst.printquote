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
	
	if($uid != $post_pr->post_author) { wp_redirect(get_bloginfo('siteurl')); exit; }
	
	//---------------------------
	
	if(isset($_POST['yes']))
	{
		$tm = current_time('timestamp',0);

		update_post_meta($pid, 'mark_seller_accepted',		"1");
		update_post_meta($pid, 'mark_seller_accepted_date',		$tm);
		
		update_post_meta($pid, 'outstanding',	"0");
		update_post_meta($pid, 'delivered',		"1");		
		
		//------------------------------------------------------------------------------
		$projectTheme_get_winner_bid = projectTheme_get_winner_bid($pid);
		
		ProjectTheme_send_email_on_completed_project_to_bidder($pid, $projectTheme_get_winner_bid->uid);
		ProjectTheme_send_email_on_completed_project_to_owner($pid);
		
		wp_redirect(get_permalink(get_option('ProjectTheme_my_account_outstanding_payments_id')));
		exit;
	}
	
	if(isset($_POST['no']))
	{
		wp_redirect(get_permalink(get_option('ProjectTheme_my_account_awaiting_completion_id')));
		exit;			
	}
	
	
	
	//---------------------------------
	
	get_header();

?>
<div class="clear10"></div>

	
			<div class="my_box3">
            	<div class="padd10">
            
            	<div class="box_title"><?php  printf(__("Mark Completed: %s",'ProjectTheme'), $post_pr->post_title); ?></div>
                <div class="box_content">   
               <?php
			   
			   printf(__("You are about to mark this job Completed.",'ProjectTheme'), $post_pr->post_title); echo '<br/>';
			  _e("<p>After clicking 'Yes, Mark Completed' the printer will be notified by email. This will move your job to Completed and end the process.</p><p>Remember to leave feedback on the printer to help you and others in the future.",'ProjectTheme') ;
			   
			   ?> 
                
                <div class="clear10"></div>
               
               <form method="post"  > 
                
               <input type="submit" name="yes" value="<?php _e("Yes, Mark Completed",'ProjectTheme'); ?>" />
               <input type="submit" name="no"  value="<?php _e("No",'ProjectTheme'); ?>" />
                
               </form>
    </div>
			</div>
			</div>
        
        
        <div class="clear100"></div>
            
            
<?php

get_footer();

?>                        
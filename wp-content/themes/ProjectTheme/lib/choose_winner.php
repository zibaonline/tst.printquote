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


if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }
//-----------

	add_filter('sitemile_before_footer', 'projectTheme_my_account_before_footer');
	function projectTheme_my_account_before_footer()
	{
		echo '<div class="clear10"></div>';		
	}
	
	//----------

	global $wpdb,$wp_rewrite,$wp_query;

	$bid = projectTheme_get_bid_by_id($wp_query->query_vars['bid']);
	$pid = $wp_query->query_vars['pid'];
	$winner = get_post_meta($pid, 'winner', true);

	if(!empty($winner)) { echo 'DEBUG. Winner already chosen or print job closed.'; exit;} 
	
//---------------------------------

	global $current_user;
	get_currentuserinfo();
	$uid = $current_user->ID;

	$post_p = get_post($pid);
	
	if($post_p->post_author != $uid) { echo 'ERR. Not your print job.'; exit;} 
	
//----------------------------------	
	
	if(isset($_POST['yes']))
	{
		$tm = current_time('timestamp',0);
		update_post_meta($pid, 'closed','1');
		update_post_meta($pid, 'closed_date',$tm);
		
		update_post_meta($pid, 'outstanding',	"1");
		update_post_meta($pid, 'delivered',		"0");
		
		update_post_meta($pid, 'mark_coder_delivered',		"0");
		update_post_meta($pid, 'mark_seller_accepted',		"0");
		
		$expected_delivery = ($bid->days_done * 3600 * 24) + current_time('timestamp',0);
		update_post_meta($pid, 'expected_delivery',		$expected_delivery);
		
		//------------------------------------------------------------------------------
		
		$uid = $bid->uid;
		
		projectTheme_prepare_rating($pid, $bid->uid, $post_p->post_author);
		projectTheme_prepare_rating($pid, $post_p->post_author, $bid->uid);
		
		do_action('ProjectTheme_do_action_on_choose_winner', $bid->id );
		
		$newtm = current_time('timestamp');
		$query = "update ".$wpdb->prefix."project_bids set date_choosen='$newtm', winner='1' where id='{$bid->id}'";
		$wpdb->query($query);
		
					
					ProjectTheme_send_email_on_win_to_bidder($pid, $uid);
					ProjectTheme_send_email_on_win_to_owner($pid, $uid);
					
					
						global $wpdb;
						$s = "select distinct uid from ".$wpdb->prefix."project_bids where uid!='$uid' and pid='$pid'";
						$r = $wpdb->get_results($s);
					
						foreach($r as $row)
						{
							$looser = $row->uid;
							ProjectTheme_send_email_on_win_to_loser($pid, $looser);
						}
					
					//----------
	
		
		update_post_meta($pid, 'winner', $uid);
		update_post_meta($pid, 'paid_user',"0");
		
		wp_redirect(get_permalink(get_option('ProjectTheme_my_account_awaiting_completion_id')));
		exit;
	}
	
	if(isset($_POST['no']))
	{
		wp_redirect(get_permalink($pid));
		exit;			
	}
	
//==========================

get_header();

?>
<div class="clear10"></div>

	
			<div class="my_box3">
            	<div class="padd10">
            
            	<div class="box_title"><?php  printf(__("Choose Winning Quote For : %s",'ProjectTheme'), $post_p->post_title); ?></div>
                <div class="box_content">   
               <?php
			   
			   printf(__("To confirm the winning print quote click 'Yes, Confirm Winner' below. <p style='color:#F00'><strong>Important Disclaimer</strong>:<br>By clicking 'Yes, Confirm Winner' you are entering into a binding contract based on the <u>printers quote</u> and <u>payment terms</u> in accordance with the PrintQuote.co.nz <a href='http://www.printquote.co.nz/customer-terms/' target='_blank'>terms</a> you accepted when joining this service.</p>",'ProjectTheme'), $post_p->post_title);
			   
			   ?> 
                
                <div class="clear10"></div>
               
               <form method="post" enctype="application/x-www-form-urlencoded"> 
                
               <input type="submit" name="yes" value="<?php _e("Yes, Confirm Winner",'ProjectTheme'); ?>" />
               <input type="submit" name="no"  value="<?php _e("No",'ProjectTheme'); ?>" />
                
               </form>
    </div>
			</div>
			</div>
        
        
        <div class="clear100"></div>
            
            
<?php

get_footer();

?>                        
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
 
 	 
	global $current_user, $wp_query;
	$pid 	= $wp_query->query_vars['pid'];
	$uid	= $current_user->ID;
	
	//-----------------------------------
	
	function ProjectTheme_filter_ttl($title){return __("Pay by Virtual Currency",'ProjectTheme')." - ";}
	add_filter( 'wp_title', 'ProjectTheme_filter_ttl', 10, 3 );	
	
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }   
	
	$cid 	= $uid;
	$post_pr 	= get_post($pid);
	
	//-----------------------------------

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
	
	update_post_meta($pid, 'featured_paid', 	'0');
	update_post_meta($pid, 'private_bids_paid', '0');
	update_post_meta($pid, 'hide_project_paid', '0');
	
	
	$custom_set = get_option('project_enable_custom_posting');
				if($custom_set == 'yes')
				{
					$post_pring_fee = get_option('project_theme_custom_cat_'.$catid);		
				}
				else
				{
					$post_pring_fee = get_option('projectTheme_base_fee');
				}
				
	$ProjectTheme_get_images_cost_extra = ProjectTheme_get_images_cost_extra($pid);
	$total = $ProjectTheme_get_images_cost_extra + $feat_charge + $post_pring_fee + $projectTheme_sealed_bidding_fee + $projectTheme_hide_project_fee;
	
	//----------------------------------------------
	
		$payment_arr = array();
				
		$my_small_arr = array();
		$my_small_arr['fee_code'] 		= 'base_fee';
		$my_small_arr['show_me'] 		= true;
		$my_small_arr['amount'] 		= $post_pring_fee;
		$my_small_arr['description'] 	= __('Base Fee','ProjectTheme');
		array_push($payment_arr, $my_small_arr);
		//-----------------------
		
		
		$my_small_arr = array();
		$my_small_arr['fee_code'] 		= 'extra_img';
		$my_small_arr['show_me'] 		= true;
		$my_small_arr['amount'] 		= $ProjectTheme_get_images_cost_extra;
		$my_small_arr['description'] 	= __('Extra Images Fee','ProjectTheme');
		array_push($payment_arr, $my_small_arr);
		//------------------------
		
		$featured_paid  	= get_post_meta($pid,'featured_paid',true);
		$opt 				= get_post_meta($pid,'featured',true);
 
		
		if($feat_charge > 0 and $featured_paid != 1 and $opt == 1)
		{
		
			$my_small_arr = array();
			$my_small_arr['fee_code'] 		= 'feat_fee';
			$my_small_arr['show_me'] 		= true;
			$my_small_arr['amount'] 		= $feat_charge;
			$my_small_arr['description'] 	= __('Featured Fee','ProjectTheme');
			array_push($payment_arr, $my_small_arr);
		
		}
		
		//------------------------
		
		$private_bids_paid  = get_post_meta($pid,'private_bids_paid',true);
		$opt 				= get_post_meta($pid,'private_bids',true);
 
		
		if($projectTheme_sealed_bidding_fee > 0 and $private_bids_paid != 1  and ($opt == 1 or $opt == "yes"))
		{
		
			$my_small_arr = array();
			$my_small_arr['fee_code'] 		= 'sealed_project';
			$my_small_arr['show_me'] 		= true;
			$my_small_arr['amount'] 		= $projectTheme_sealed_bidding_fee;
			$my_small_arr['description'] 	= __('Sealed Bidding Fee','ProjectTheme');
			array_push($payment_arr, $my_small_arr);
			
		}
		
		//------------------------
		
		$hide_project_paid 	= get_post_meta($pid,'hide_project_paid',true);
		$opt 				= get_post_meta($pid,'hide_project',true);
		
		if($projectTheme_hide_project_fee > 0 and $hide_project_paid != "1" and ($opt == "1" or $opt == "yes"))
		{
		
			$my_small_arr = array();
			$my_small_arr['fee_code'] 		= 'hide_project';
			$my_small_arr['show_me'] 		= true;
			$my_small_arr['amount'] 		= $projectTheme_hide_project_fee;
			$my_small_arr['description'] 	= __('Hide Project From Search Engines Fee','ProjectTheme');
			array_push($payment_arr, $my_small_arr);
		
		}
		
		$payment_arr = apply_filters('ProjectTheme_filter_payment_array', $payment_arr, $pid);
		$new_total 		= 0;
		
		foreach($payment_arr as $payment_item):			
			if($payment_item['amount'] > 0):				
				$new_total += $payment_item['amount'];			
			endif;			
		endforeach;
		
		
		$total = apply_filters('ProjectTheme_filter_payment_total', $new_total, $pid);
			
	//----------------------------------------------
	
	$post_pr 			= get_post($pid);
	$admin_email 	= get_bloginfo('admin_email');


	
	//----------------

	get_header();
	
?>

	<div id="content" >       	
            <div class="my_box3">
            	<div class="padd10">            
            		<div class="box_title"><?php _e("Pay Listing by Virtual Currency", "ProjectTheme"); ?></div>
                		<div class="box_content"> 



           <div class="post no_border_btm" id="post-<?php the_ID(); ?>">
                
                <div class="image_holder">
                <a href="<?php echo get_permalink($pid); ?>"><img width="45" height="45" class="image_class" 
                src="<?php echo ProjectTheme_get_first_post_image($pid,45,45); ?>" /></a>
                </div>
                <div  class="title_holder" > 
                     <h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php echo $post_pr->post_title; ?>">
                        <?php  echo $post_pr->post_title; ?></a></h2>
      			</div>
                <?php
				
					if(isset($_GET['pay'])):
						echo '<div class="details_holder sk_sk_class">';
						
							$post_pr 	= get_post($pid);
							$cr 		= projectTheme_get_credits($uid);
							$amount 	= $total;
							
							if($cr < $amount) { echo '<div class="error2">'; echo __('You do not have enough credits to pay for listing this project.','ProjectTheme');
							echo '</div><div class="clear10 flt_lft"></div>';
							
							$dep_dep = true;
							$dep_dep = apply_filters('ProjectTheme_credits_listing_add_more', $dep_dep);
							if($dep_dep == true):
							?>
                            
							<div class="tripp">
								<a class="post_bid_btn" href="<?php echo ProjectTheme_get_payments_page_url('deposit'); ?>"><?php echo __('Add More Credits','ProjectTheme'); ?></a>
							</div>
                    
							<?php
                            
								endif;
							}
							else
							{
								
								$paid = get_post_meta($pid, 'paid', true);
								
								
								if($paid != "1"):
								// echo $pid;
										projectTheme_send_email_to_project_payer($pid, $uid, $post_pr->post_author, $amount, '1');	
										
										projectTheme_update_credits($uid, $cr - $amount);
										$reason = sprintf(__('Listing payment for project <a href="%s">%s</a>','ProjectTheme'), $perm, $post_pr->post_title);										
										$reason = apply_filters('ProjectTheme_reason_listing_project', $reason, $pid);
										
										projectTheme_add_history_log('0', $reason, $amount, $uid );
										
										//---------------------
										
										update_post_meta($pid, "paid", 				"1");
										update_post_meta($pid, "paid_listing_date", current_time('timestamp',0));
										update_post_meta($pid, "closed", 			"0");
										
										//--------------------------------------------
										
										update_post_meta($pid, 'base_fee_paid', '1');
										
										$featured = get_post_meta($pid,'featured',true);	
										if($featured == "1") update_post_meta($pid, 'featured_paid', '1');
										
										$private_bids = get_post_meta($pid,'private_bids',true);	
										if($private_bids == "1") update_post_meta($pid, 'private_bids_paid', '1');
										 
										$hide_project = get_post_meta($pid,'hide_project',true);	
										if($hide_project == "1") update_post_meta($pid, 'hide_project_paid', '1');
										
										//--------------------------------------------
										
										$projectTheme_admin_approves_each_project = get_option('projectTheme_admin_approves_each_project');
										
										if($projectTheme_admin_approves_each_project != "yes")
										{
											wp_publish_post( $pid );	
											$post_pr_new_date = date('Y-m-d H:i:s',current_time('timestamp',0));  
											
											$post_pr_info = array(  "ID" 	=> $pid,
											  "post_date" 				=> $post_pr_new_date,
											  "post_date_gmt" 			=> $post_pr_new_date,
											  "post_status" 			=> "publish"	);
											
											wp_update_post($post_pr_info);
											 
											ProjectTheme_send_email_posted_project_approved($pid);
											ProjectTheme_send_email_posted_project_approved_admin($pid);
										
										}
										else
										{
											
											
											ProjectTheme_send_email_posted_project_not_approved($pid);
											ProjectTheme_send_email_posted_project_not_approved_admin($pid);
												
											ProjectTheme_send_email_subscription($pid);	
											
										}
								
								
								endif;
								
								//---------------------						
								
								echo sprintf(__('Your payment has been sent. Return to <a href="%s">your account</a>.','ProjectTheme'), get_permalink(get_option('ProjectTheme_my_account_page_id')) );	
							}
							echo '</div>';
				?>
           
                
                <?php else: ?>
                <div class="details_holder sk_sk_class">  
           
                
                <?php
				
				echo '<table style="margin-top:25px">';

	
		foreach($payment_arr as $payment_item):
			
			if($payment_item['amount'] > 0):
			
				echo '<tr>';
				echo '<td>'.$payment_item['description'].'&nbsp; &nbsp;</td>';
				echo '<td>'.ProjectTheme_get_show_price($payment_item['amount'],2).'</td>';
				echo '</tr>';

			endif;
			
		endforeach;	
	
	
	echo '<tr>';
	echo '<td>&nbsp;</td>';
	echo '<td></td>';
	echo '<tr>';
	
	
	echo '<tr>';
	echo '<td><strong>'.__('Total to Pay','ProjectTheme').'</strong></td>';
	echo '<td><strong>'.ProjectTheme_get_show_price($total,2).'</strong></td>';
	echo '<tr>';
	
	echo '</table>';
	
	?>
                
                
                
               <?php _e("Your credits amount",'ProjectTheme'); ?>: <?php echo projecttheme_get_show_price(projectTheme_get_credits($uid)); ?> <br/><br/>
               <a class="post_bid_btn" href="<?php echo get_bloginfo('siteurl'); ?>/?p_action=credits_listing&pid=<?php echo $pid; ?>&pay=yes"><?php echo __('Pay Now','ProjectTheme'); ?></a> 
               
                    
               <?php
			   
			   $dep_dep = true;
							$dep_dep = apply_filters('ProjectTheme_credits_listing_add_more', $dep_dep);
							if($dep_dep == true):
			   
			   ?>
               
               <a class="post_bid_btn" href="<?php echo ProjectTheme_get_payments_page_url('deposit'); ?>"><?php echo __('Add More Credits','ProjectTheme'); ?></a>
               
               <?php endif; ?>
                </div><?php endif; ?>
				</div>


                		</div>
                	</div>
                </div>
            </div>
                


<?php get_footer(); ?>
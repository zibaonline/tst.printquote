<?php

function ProjectTheme_my_account_feedbacks_area_function()
{
	
		global $current_user, $wpdb, $wp_query;
		get_currentuserinfo();
		$uid = $current_user->ID;
		
?>
    	<div id="content">	
	
	
	
	
	 <div class="my_box3">
            	
            
            	<div class="box_title"><?php _e("Reviews I need to award",'ProjectTheme'); ?></div>
                <div class="box_content">    
				
              	<?php
					
					global $wpdb;
					$query = "select * from ".$wpdb->prefix."project_ratings where fromuser='$uid' AND awarded='0'";
					$r = $wpdb->get_results($query);
					
					if(count($r) > 0)
					{
						echo '<table width="100%">';
							echo '<tr>';
								echo '<th>&nbsp;</th>';	
								echo '<th><b>'.__('Project Title','ProjectTheme').'</b></th>';
								echo '<th><b>'.__('To User','ProjectTheme').'</b></th>';									
								echo '<th><b>'.__('Aquired on','ProjectTheme').'</b></th>';								
								echo '<th><b>'.__('Price','ProjectTheme').'</b></th>';
								echo '<th><b>'.__('Options','ProjectTheme').'</b></th>';
							
							echo '</tr>';	
						
						
						foreach($r as $row)
						{
							$post 	= $row->pid;
							$post 	= get_post($post);
							$bid 	= projectTheme_get_winner_bid($row->pid);
							$user 	= get_userdata($row->touser);
							
							echo '<tr>';
								
								echo '<th><img class="img_class" width="42" height="42" src="'.ProjectTheme_get_first_post_image($row->pid, 42, 42).'" 
                                alt="'.$post->post_title.'" /></th>';	
								echo '<th><a href="'.get_permalink($row->pid).'">'.$post->post_title.'</a></th>';	
								echo '<th><a href="'.ProjectTheme_get_user_profile_link($user->ID).'">'.$user->user_login.'</a></th>';							
								echo '<th>'.date_i18n('d-M-Y H:i:s',get_post_meta($row->pid,'closed_date',true)).'</th>';								
								echo '<th>'.projectTheme_get_show_price($bid->bid).'</th>';
								echo '<th><a href="'.get_bloginfo('siteurl').'/?p_action=rate_user&rid='.$row->id.'">'.__('Rate User','ProjectTheme').'</a></th>';
							
							echo '</tr>';
							
						}
						
						echo '</table>';
					}
					else
					{
						_e("There are no reviews to be awarded.","ProjectTheme");	
					}
				?>
                
                
           </div>
           </div>    
           
           <!-- ##### -->
           <div class="clear10"></div>
           
           <div class="my_box3">
            
            
            	<div class="box_title"><?php _e("Reviews I am waiting on",'ProjectTheme'); ?></div>
                <div class="box_content">    
				
              	<?php
					
					global $wpdb;
					$query = "select * from ".$wpdb->prefix."project_ratings where touser='$uid' AND awarded='0'";
					$r = $wpdb->get_results($query);
					
					if(count($r) > 0)
					{
						echo '<table width="100%">';
							echo '<tr>';
								echo '<th>&nbsp;</th>';	
								echo '<th><b>'.__('Project Title','ProjectTheme').'</b></th>';								
								echo '<th><b>'.__('From User','ProjectTheme').'</b></th>';	
								echo '<th><b>'.__('Aquired on','ProjectTheme').'</b></th>';								
								echo '<th><b>'.__('Price','ProjectTheme').'</b></th>';
								//echo '<th><b>'.__('Options','ProjectTheme').'</b></th>';
							
							echo '</tr>';	
						
						
						foreach($r as $row)
						{
							$post 	= $row->pid;
							$post 	= get_post($post);
							$bid 	= projectTheme_get_winner_bid($row->pid);
							$user 	= get_userdata($row->fromuser);
							echo '<tr>';
								
								echo '<th><img class="img_class" width="42" height="42"  src="'.ProjectTheme_get_first_post_image($row->pid, 42, 42).'" 
                                alt="'.$post->post_title.'" /></th>';	
								echo '<th><a href="'.get_permalink($row->pid).'">'.$post->post_title.'</a></th>';
								echo '<th><a href="'.ProjectTheme_get_user_profile_link($user->ID).'">'.$user->user_login.'</a></th>';								
								echo '<th>'.date_i18n('d-M-Y H:i:s',get_post_meta($row->pid,'closed_date',true)).'</th>';								
								echo '<th>'.projectTheme_get_show_price($bid->bid).'</th>';
								//echo '<th><a href="#">Rate User</a></th>';
							
							echo '</tr>';
							
						}
						
						echo '</table>';
					}
					else
					{
						_e("There are no reviews to be awarded.","ProjectTheme");	
					}
				?>
                
                
           </div>
           </div>    
           
           <div class="clear10"></div>
           
           <div class="my_box3">
            	
            
            	<div class="box_title"><?php _e("Reviews I was awarded ",'ProjectTheme'); ?></div>
                <div class="box_content">    
				
              	<?php
					
					global $wpdb;
					$query = "select * from ".$wpdb->prefix."project_ratings where touser='$uid' AND awarded='1'";
					$r = $wpdb->get_results($query);
					
					if(count($r) > 0)
					{
						echo '<table width="100%">';
							echo '<tr>';
								echo '<th>&nbsp;</th>';	
								echo '<th><b>'.__('Project Title','ProjectTheme').'</b></th>';								
								echo '<th><b>'.__('From User','ProjectTheme').'</b></th>';	
								echo '<th><b>'.__('Aquired on','ProjectTheme').'</b></th>';								
								//echo '<th><b>'.__('Price','ProjectTheme').'</b></th>';
								echo '<th><b>'.__('Rating','ProjectTheme').'</b></th>';
								
							
							echo '</tr>';	
						
						
						foreach($r as $row)
						{
							$post 	= $row->pid;
							$post 	= get_post($post);
							$bid 	= projectTheme_get_winner_bid($row->pid);
							$user 	= get_userdata($row->fromuser);
							
							echo '<tr>';
								
								echo '<th><img width="42" height="42" class="img_class" src="/images/quote.png" 
                                alt="'.$post->post_title.'" /></th>';	
								echo '<th><a href="'.get_permalink($row->pid).'">'.$post->post_title.'</a></th>';
								echo '<th><a href="'.ProjectTheme_get_user_profile_link($user->ID).'">'.$user->user_login.'</a></th>';								
								echo '<th>'.date_i18n('d-M-Y H:i:s',get_post_meta($row->pid,'closed_date',true)).'</th>';								
								//echo '<th>'.projectTheme_get_show_price($bid->bid).'</th>';
								echo '<th style="text-align:center;"><span style="color:#0084C1; font-size:28px; font-weight:bold; ">'.floor($row->grade/2).'</span>/5</th>';
								
							
							echo '</tr>';
							echo '<tr>';
							echo '<th></th>';
							echo '<th colspan="5"><b>'.__('Comment','ProjectTheme').':</b> '.$row->comment.'</th>'	;						
							echo '</tr>';
							
							echo '<tr><th colspan="6"><hr color="#eee" /></th></tr>';
							
						}
						
						echo '</table>';
					}
					else
					{
						_e("There are no reviews to be awarded.","ProjectTheme");	
					}
				?>
                
              
           </div>
           </div>    
	
	
	
	
	
		</div>
        
<?php	
		
		ProjectTheme_get_users_links(); 
		
}

?>
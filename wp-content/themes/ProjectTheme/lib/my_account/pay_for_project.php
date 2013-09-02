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

function ProjectTheme_my_account_pay4project_area_function()
{
		global $current_user, $wpdb, $wp_query;
		get_currentuserinfo();
		$uid = $current_user->ID;
		
		$pid = $wp_query->query_vars['pid'];
		$post = get_post($pid);
		
?>
    	<div id="content">
        	
            <div class="my_box3 ">
            	<div class="padd10">
            
            	<div class="box_title"><?php echo sprintf(__("Pay for project - %s", "ProjectTheme"),$post->post_title); ?></div>
                <div class="box_content"> 
            	
                

                
                <div class="post no_border_btm" id="post-<?php the_ID(); ?>">
                <div class="padd10">
                <div class="image_holder">
                <a href="<?php echo get_permalink($pid); ?>"><img width="45" height="35" class="image_class" 
                src="<?php echo ProjectTheme_get_first_post_image($pid,45,35); ?>" /></a>
                </div>
                <div  class="title_holder" > 
                     <h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php echo $post->post_title; ?>">
                        <?php 
                        
                        echo $post->post_title;
                     
                        ?></a></h2>
      			</div>
                
                <div class="details_holder">
             <?php 
			 
			 $bid = projectTheme_get_winner_bid($pid);
			 echo __('You are about to pay for this project. Use the accepted methods below to pay for it.','ProjectTheme'); ?>
                <b><?php echo sprintf(__('The price for the project is: %s','ProjectTheme') ,projectTheme_get_show_price($bid->bid)); ?></b>
                <br/><br/>
                
                <a href="<?php bloginfo('siteurl'); ?>/?p_action=pay_for_project_paypal&pid=<?php echo $pid; ?>"><img 
                src="<?php echo get_bloginfo('template_url'); ?>/images/paypal.jpg" border="0" /></a><br/><br/>
                
                <?php do_action('ProjectTheme_pay_for_actual_project_payment_options', $pid); ?>
                
                <?php
				
					$show_cr = true;
					$show_cr = apply_filters('ProjectTheme_show_cr_filter', $show_cr);
					
					if($show_cr == true):
				?>
                <a class="post_bid_btn" href="<?php echo ProjectTheme_get_pay_with_credits_page_url($pid); ?>"><?php echo __('Pay by credits','ProjectTheme'); ?></a>
                <?php endif; ?>
                </div>
                
                </div>
                </div>
      

                
                </div>
                </div>
                </div>
                </div>   
<?php
		ProjectTheme_get_users_links();

}
	
?>
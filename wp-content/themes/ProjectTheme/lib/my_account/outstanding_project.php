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


function ProjectTheme_my_account_outstanding_projects_area_function()
{
		global $current_user, $wpdb, $wp_query;
		get_currentuserinfo();
		$uid = $current_user->ID;
		
?>
    	<div id="content">
        
        
        	<div class="my_box3 border_bottom_0">
            	
            
            	<div class="box_title"><?php _e("Outstanding Jobs",'ProjectTheme'); ?></div>
                <div class="box_content">    
				
                
                <?php
				global $current_user;
				get_currentuserinfo();
				$uid = $current_user->ID;
				
				
				global $wp_query;
				$query_vars = $wp_query->query_vars;
				$post_per_page = 10;				
				
		
				$outstanding = array(
						'key' => 'outstanding',
						'value' => "1",
						'compare' => '='
					);
					
				$winner = array(
						'key' => 'winner',
						'value' => $uid,
						'compare' => '='
					);		
				
				$args = array('post_type' => 'project', 'order' => 'DESC', 'orderby' => 'date', 'posts_per_page' => $post_per_page,
				'paged' => $query_vars['paged'], 'meta_query' => array($outstanding, $winner));
				
				query_posts($args);

				if(have_posts()) :
				while ( have_posts() ) : the_post();
					projectTheme_get_post_outstanding_project();
				endwhile;
				
				if(function_exists('wp_pagenavi')):
				wp_pagenavi(); endif;
				
				 else:
				
				_e("There are no outstanding jobs.",'ProjectTheme');
				
				endif;
				
				wp_reset_query();
				
				?>
                
                
               
           </div>
           </div>    
        
        
  		</div>      
<?php
		ProjectTheme_get_users_links();

}
	
?>
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


function ProjectTheme_my_account_area_main_function()
{
	
				
				global $current_user, $wp_query;
				get_currentuserinfo();
				
				$uid = $current_user->ID;
	
	
?>
    	<div id="content">
        	<?php
			
			
				if(ProjectTheme_is_user_business($uid)):
			
			
			?>
			
			
		
			<div style="text-align:center; padding:20px;"><a href="/post-new/" class="post_bid_btn_home" rel="35">Request Quotes</a></div> 
			
            <div class="my_box3">            
            	<div class="box_title"><?php _e("Active Quote Requests", "ProjectTheme"); ?></div>
                <div class="box_content "> 
            	
                 <?php
							
			
				$query_vars = $wp_query->query_vars;
				$post_per_page = 3;				
				
				global $wp_query;
				$query_vars = $wp_query->query_vars;
				$post_per_page = 5;				
				
			
		
					
				$closed = array(
						'key' => 'closed',
						'value' => "0",
						'compare' => '='
					);	
					
				$paid = array(
						'key' => 'paid',
						'value' => "1",
						'compare' => '='
					);		
				
				$args = array('post_type' => 'project', 'author' => $uid, 'order' => 'DESC', 'orderby' => 'date', 'posts_per_page' => $post_per_page,
				'paged' => 1, 'meta_query' => array($paid, $closed), 'post_status' =>array('draft','publish') );
				
				query_posts($args);
				
			//	query_posts( "meta_key=closed&meta_value=0&post_status=publish,draft&post_type=project&order=DESC&orderby=date&author=".$uid.
			//	"&posts_per_page=".$post_per_page."&paged=".$query_vars['paged'] );

				if(have_posts()) :
				while ( have_posts() ) : the_post();
					projectTheme_get_post();
				endwhile;
				
				//if(function_exists('wp_pagenavi')):
				//wp_pagenavi(); endif;
				
				 else:
				
				_e("There are no quote requests.",'ProjectTheme');
				
				endif;
				
				wp_reset_query();

				
				?>

              </div>
           </div>
           
           
           <div class="my_box3">
          
            
            	<div class="box_title"><?php _e("Unpublished Quote Requests",'ProjectTheme'); ?></div>
                <div class="box_content">    
			
			
				<?php

				query_posts( "post_status=draft&meta_key=paid&meta_value=0&post_type=project&order=DESC&orderby=id&author=".$uid."&posts_per_page=3" );
				
				if(have_posts()) :
				while ( have_posts() ) : the_post();
					projectTheme_get_post(array('unpaid'));
				endwhile; else:
				
				_e("There are no quote requests unpublished.",'ProjectTheme');
				
				endif;
				
				wp_reset_query();
				
				?>
			
			</div>
			</div>
	
			
			
			<div class="clear10"></div>
			
			
			<div class="my_box3">
        
            
            	<div class="box_title"><?php _e("Recently Closed Quote Requests",'ProjectTheme'); ?></div>
                <div class="box_content">    
			
			
				<?php

				query_posts( "meta_key=closed&meta_value=1&post_type=project&order=DESC&orderby=id&author=".$uid."&posts_per_page=3" );

				if(have_posts()) :
				while ( have_posts() ) : the_post();
					projectTheme_get_post();
				endwhile; else:
				
				_e("There are no quote requests.",'ProjectTheme');
				
				endif;
				wp_reset_query();
				
				?>

			</div>
			</div>
			
			
			<div class="clear10"></div>
			
			
			<div class="my_box3">
        
            
            	<div class="box_title"><?php _e("Jobs Awaiting Completion",'ProjectTheme'); ?></div>
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
				
				$args = array('post_type' => 'project', 'author' => $uid, 'order' => 'DESC', 'orderby' => 'date', 'posts_per_page' => 3,
				'paged' => $query_vars['paged'], 'meta_query' => array($outstanding));
				
				query_posts( $args);

				if(have_posts()) :
				while ( have_posts() ) : the_post();
					projectTheme_get_post_awaiting_compl();
				endwhile;
				
				if(function_exists('wp_pagenavi')):
				wp_pagenavi(); endif;
				
				 else:
				
				_e("There are no active print jobs.",'ProjectTheme');
				
				endif;
				
				wp_reset_query();
				
				?>  
			

			</div>
			</div>
			
			
			<div class="clear10"></div>
			
			
			<div class="my_box3">
        
            
            	<div class="box_title"><?php _e("Recently Completed Jobs",'ProjectTheme'); ?></div>
                <div class="box_content">  
				
				<?php
				
				global $current_user;
				get_currentuserinfo();
				$uid = $current_user->ID;
				
				
				global $wp_query;
				$query_vars = $wp_query->query_vars;
				$post_per_page = 10;				
				
			
		
					
				$paid = array(
						'key' => 'paid_user',
						'value' => "0",
						'compare' => '='
					);	
					
				$delivered = array(
						'key' => 'delivered',
						'value' => "1",
						'compare' => '='
					);		
					
				
				$args = array('post_type' => 'project', 'author' => $uid, 'order' => 'DESC', 'orderby' => 'date', 'posts_per_page' => 3,
				'paged' => $query_vars['paged'], 'meta_query' => array($paid, $delivered));
				
				query_posts( $args);

				if(have_posts()) :
				while ( have_posts() ) : the_post();
					projectTheme_get_post_pay();
				endwhile;
				
				if(function_exists('wp_pagenavi')):
				wp_pagenavi(); endif;
				
				 else:
				
				_e("There are no completed jobs.",'ProjectTheme');
				
				endif;
				
				wp_reset_query();
				
				?> 
			

			</div>
			</div>
		
        <?php endif; ?>
        
        <?php if(ProjectTheme_is_user_provider($uid)): ?>	
           
           
        
      <div style="text-align:center; padding:20px;"><a href="/all-posted-projects/" class="post_bid_btn_home" rel="35">Find jobs to quote</a></div>      
           
           
           
        <div class="my_box3">
        
            
            	<div class="box_title"><?php _e("Latest Quotes",'ProjectTheme'); ?></div>
                <div class="box_content">    
			
			
				<?php

				query_posts( "meta_key=bid&meta_value=".$uid."&post_type=project&order=DESC&orderby=id&posts_per_page=3" );

				if(have_posts()) :
				while ( have_posts() ) : the_post();
					projectTheme_get_post();
				endwhile; else:
				
				_e("There are no quotes yet.",'ProjectTheme');
				
				endif;
				wp_reset_query();
				
				?>

			</div>
			</div>
            
            
            
            <div class="my_box3">
        
            
            	<div class="box_title"><?php _e("Latest Jobs Won",'ProjectTheme'); ?></div>
                <div class="box_content">    
			
			
				<?php

				query_posts( "meta_key=winner&meta_value=".$uid."&post_type=project&order=DESC&orderby=id&posts_per_page=3" );

				if(have_posts()) :
				while ( have_posts() ) : the_post();
					projectTheme_get_post();
				endwhile; else:
				
				_e("There are no jobs yet.",'ProjectTheme');
				
				endif;
				wp_reset_query();
				
				?>

			</div>
			</div>
			
			<div class="my_box3">
        
            
            	<div class="box_title"><?php _e("Outstanding Jobs",'ProjectTheme'); ?></div>
                <div class="box_content">    
			
			
				<?php
				
				global $wp_query;
				$query_vars = $wp_query->query_vars;
				$post_per_page = 3;				
				
		
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
				'paged' => 1, 'meta_query' => array($outstanding, $winner));
				
				
				query_posts( $args  );

				if(have_posts()) :
				while ( have_posts() ) : the_post();
					projectTheme_get_post_outstanding_project();
				endwhile; else:
				
				_e("There are no outstanding jobs.",'ProjectTheme');
				
				endif;
				wp_reset_query();
				
				?>

			</div>
			</div>  
			
			
			<div class="my_box3">
        
            
            	<div class="box_title"><?php _e("Recently Completed Jobs",'ProjectTheme'); ?></div>
                <div class="box_content">    
			
			
				<?php
				
				global $wp_query;
				$query_vars = $wp_query->query_vars;
				$post_per_page = 10;				
				
		
				$delivered = array(
						'key' => 'delivered',
						'value' => "1",
						'compare' => '='
					);
					
				$paid = array(
						'key' => 'paid_user',
						'value' => "0",
						'compare' => '='
					);	
				
				$winner = array(
						'key' => 'winner',
						'value' => $uid,
						'compare' => '='
					);		
						
				
				$args = array('post_type' => 'project', 'order' => 'DESC', 'orderby' => 'date', 'posts_per_page' => 3,
				'paged' => $query_vars['paged'], 'meta_query' => array($delivered, $paid, $winner));
				
				query_posts($args);


				if(have_posts()) :
				while ( have_posts() ) : the_post();
					projectTheme_get_post_awaiting_payment();
				endwhile;
				
				if(function_exists('wp_pagenavi')):
				wp_pagenavi(); endif;
				
				 else:
				
				_e("There are no completed jobs.",'ProjectTheme');
				
				endif;
				
				wp_reset_query();
				
				?>

			</div>
			</div>   
            
        
        <?php endif; ?>   
                
        </div> <!-- end dif content -->
        
        <?php ProjectTheme_get_users_links(); ?>
        
    
	
<?php	
} 


?>
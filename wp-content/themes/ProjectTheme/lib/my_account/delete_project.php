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
	
	function ProjectTheme_filter_ttl($title){return __("Delete Quote Request",'ProjectTheme')." - ";}
	add_filter( 'wp_title', 'ProjectTheme_filter_ttl', 10, 3 );	
	
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }   
	   
	
	get_currentuserinfo;   

	$post = get_post($pid);

	$uid 	= $current_user->ID;
	$title 	= $post->post_title;
	$cid 	= $current_user->ID;
	
	$winner = get_post_meta($pid, 'winner', true);
	
	if(!empty($winner)) { echo 'Quote Request has a winner, cant be deleted. Sorry!'; exit; }
	if($uid != $post->post_author) { echo 'Not your post. Sorry!'; exit; }

//-------------------------------------

	get_header();
?>


	<div id="content" >
        	
            <div class="my_box3">
            	<div class="padd10">
            
            	<div class="box_title"><?php printf(__("Delete Quote Request - %s", "ProjectTheme"), $post->post_title); ?></div>
                <div class="box_content"> 
            	
                
                <?php
				
				if(isset($_POST['are_you_sure']))
				{
					wp_delete_post($pid);
					echo sprintf(__("The Quote Request has been deleted. <a href='%s'>Return to your account</a>.",'ProjectTheme'), get_permalink(get_option('ProjectTheme_my_account_page_id')));
				
				}
				else
				{
				?>
                
                    <form method="post" enctype="application/x-www-form-urlencoded">
                    <?php _e("Are you sure you want to delete this Quote Request?",'ProjectTheme'); ?><br/><br/>
                    <input type="submit" name="are_you_sure" value="<?php _e("Confirm Deletion",'ProjectTheme'); ?>"  />
                    </form>
                  
                 <?php } ?>              
                
                
                </div>
                </div>
                </div>
                </div>
                
	<?php ProjectTheme_get_users_links(); ?>

<?php get_footer(); ?>
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

	

	function projectTheme_colorbox_stuff()

	{	

	

		echo '<link media="screen" rel="stylesheet" href="'.get_bloginfo('template_url').'/css/colorbox.css" />';

		/*echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>'; */

		echo '<script src="'.get_bloginfo('template_url').'/js/jquery.colorbox.js"></script>';

		

		$get_bidding_panel = 'get_bidding_panel';

		$get_bidding_panel = apply_filters('ProjectTheme_get_bidding_panel_string', $get_bidding_panel) ;

		

?>

		

		<script>

		

		var $ = jQuery;

		

			$(document).ready(function(){

				

				$("a[rel='image_gal1']").colorbox();

				$("a[rel='image_gal2']").colorbox();

				

				

				$('.post_bid_btn_new').click( function () {

					

					var pid = $(this).attr('rel');

					$.colorbox({href: "<?php bloginfo('siteurl'); ?>/?<?php echo $get_bidding_panel; ?>=" + pid });

					return false;

				});

				

				

				$('.message_brd_cls').click( function () {

					

					var pid = $(this).attr('rel');

					$.colorbox({href: "<?php bloginfo('siteurl'); ?>/?get_message_board=" + pid });

					return false;

				});

				

				$('.get_files').click( function () {

					

					var myRel = $(this).attr('rel');

					myRel = myRel.split("_");

					

					$.colorbox({href: "<?php bloginfo('siteurl'); ?>/?get_files_panel=" + myRel[0] +"&uid=" + myRel[1] });

					return false;

				});

				

				

				$("#report-this-link").click( function() {

					

					if($("#report-this").css('display') == 'none')					

					$("#report-this").show('slow');

					else

					$("#report-this").hide('slow');

					

					return false;

				});

				

				

				$("#contact_seller-link").click( function() {

					

					if($("#contact-seller").css('display') == 'none')					

					$("#contact-seller").show('slow');

					else

					$("#contact-seller").hide('slow');

					

					return false;

				});

				

		});

		</script>



<?php

	}

	

	add_action('wp_head','projectTheme_colorbox_stuff');	

	//=============================

	

	global $current_user;

	get_currentuserinfo();

	$uid = $current_user->ID;

	global $wpdb;





/*****************************************************

*

*

******************************************************/	

	

	

	

	

	if(isset($_POST['bid_now_reverse']))

	{

		if(is_user_logged_in()):

		if(isset($_POST['control_id']))

		{

			$pid 		= base64_decode($_POST['control_id']);	

			$post 		= get_post($pid);

			$bid 		= trim($_POST['bid']);	

			$des 		= trim(strip_tags($_POST['description2']));	

			$post 		= get_post($pid);

		

			$tm 		= current_time('timestamp',0);

			$days_done	= trim($_POST['days_done']);

			

			//---------------------

			

			

	

			$projectTheme_enable_custom_bidding = get_option('projectTheme_enable_custom_bidding');

			if($projectTheme_enable_custom_bidding == "yes")

			{

				

				$ProjectTheme_get_project_primary_cat = ProjectTheme_get_project_primary_cat($pid);	

				$projectTheme_theme_bidding_cat_ = get_option('projectTheme_theme_bidding_cat_' . $ProjectTheme_get_project_primary_cat);

				

				if($projectTheme_theme_bidding_cat_ > 0)

				{

					$ProjectTheme_get_credits = ProjectTheme_get_credits($uid);

					$do_not_show = 0;

					$prc = $projectTheme_theme_bidding_cat_;

					

					if(	$ProjectTheme_get_credits < $projectTheme_theme_bidding_cat_) { $do_not_show = 1;	

						$prc = $projectTheme_theme_bidding_cat_;

						

					}

					

					

				}

				

			}

			

			if($do_not_show == 0)

			{

				$pst = get_post($pid); 

				$cr = projectTheme_get_credits($uid);

				projectTheme_update_credits($uid, $cr - $prc);

				

				$reason = sprintf(__('Payment for bidding on project: <a href="%s">%s</a>','ProjectTheme'), get_permalink($pid), $pst->post_title);

				projectTheme_add_history_log('0', $reason, $prc, $uid);	

				

			}

			

			//---------------------

			

			$closed = get_post_meta($pid,'closed',true);

			if($closed == "1") { echo 'DEBUG.Project Closed'; exit; }

			

			//---------------------

			

			if(empty($days_done) || !is_numeric($days_done))

			{

				$days_done = 3;	

			}

			

			$query = "select * from ".$wpdb->prefix."project_bids where uid='$uid' AND pid='$pid'";

			$r = $wpdb->get_results($query);

			

			$other_error_to_pace_bid = false;			

			$other_error_to_pace_bid = apply_filters('ProjectTheme_other_error_to_pace_bid', $other_error_to_pace_bid, $pid);

			

			if($other_error_to_pace_bid == true):

				

				$bid_posted = "0";

				$errors = apply_filters('ProjectTheme_post_bid_errors_array', $errors, $pid);

			

			else:

			

				

				if(!is_numeric($bid)):

				

					$bid_posted = "0";

					$errors['numeric_bid_tp'] = __("Your quote must be numeric type. Eg: 999.99",'ProjectTheme');

				

				elseif($uid == $post->post_author):

					

					$bid_posted = "0";

					$errors['not_yours'] = __("Your cannot quote on your own projects.",'ProjectTheme');

				

				elseif(count($r) > 0):

					

					$row 	= $r[0];

					$id 	= $row->id;

		

					

					$query 	= "update ".$wpdb->prefix."project_bids set bid='$bid', days_done='$days_done', 

					description='$des',date_made='$tm',uid='$uid' where id='$id'";

					$wpdb->query($query);

					$bid_posted = 1;

					

					 

				else:

			

					$query = "insert into ".$wpdb->prefix."project_bids (days_done,bid,description, uid, pid, date_made) 

					values('$days_done','$bid','$des','$uid','$pid','$tm')";

					$wpdb->query($query);

					$bid_posted = 1;

					

			

					

					add_post_meta($pid,'bid',$uid);

					

				endif; // endif has bid already



			endif;

		}

		

		

	

	

		if($bid_posted == 1):

			

			ProjectTheme_send_email_when_bid_project_owner($pid, $uid, $bid);

			ProjectTheme_send_email_when_bid_project_bidder($pid, $uid, $bid);

			

			//---------------------

			

			$prm = ProjectTheme_using_permalinks();

			if($prm == true)			

			wp_redirect(get_permalink(get_the_ID()) . "/?bid_posted=1"); 

			else

			{

				wp_redirect(get_permalink(get_the_ID()) . "&bid_posted=1"); 	

			}

			

			exit;

			

		

		endif; //endif bid posted

	

	else:

	

		$pid 		= base64_decode($_POST['control_id']);	

		wp_redirect(get_bloginfo('siteurl')."/wp-login.php");

		$_SESSION['redirect_me_back'] = get_permalink($pid);	

		exit;

		

	endif;

	}

	



	//=============================

	//function Project_change_main_class() { echo "<style> #main { background:url('".get_bloginfo('template_url')."/images/bg1.png')  } </style>"; }

	//add_filter('wp_head', 'Project_change_main_class');

	 

	

	get_header();

	global $post;



	$hide_project_p = get_post_meta($post->ID, 'hide_project', true);
	
	$current_user = wp_get_current_user();
	$client = 0;

	// Get winner data
	$winner_data = projectTheme_get_winner_bid($post->ID);
	$has_winner = !is_null($winner_data);

	if ($current_user->ID == $post->post_author) $is_my_project = true;
	else $is_my_project = false;

	$is_administrator = (bool) current_user_can('administrator');
	$is_winner = false;

	if ($has_winner) {
		if ($winner_data->uid == $current_user->ID) $is_winner = true;
	}

	if ($current_user->ID == $post->post_author)	{ $client = 1; }

	if($hide_project_p == "1"):
	{
		// [MODIFIED BY RISAN] LET ADMINISTRATOR TO SEE ALL PROJECT DETAILS 
		if (!is_user_logged_in() || ( !$is_my_project && ProjectTheme_is_user_business($uid) && !$is_administrator) )
			{

	?>

	

    

    <div class="my_box3">

            <div class="padd10">

            

            	<div class="box_title"><?php echo sprintf(__("Print job \"%s\" is marked hidden.",'ProjectTheme'), $post->post_title); ?></div>

                <div class="box_content">

                <?php echo sprintf(__('Only printers are able to see the details of "%s" in order to submit their quotes.','ProjectTheme') , $post->post_title, get_bloginfo('siteurl')."/wp-login.php"); ?>
				
				<p>As a client you have the option to make your print jobs "Private" or leave them "Public". Making a job "Private" will hide your job details from search engines, the general public and other clients. This is ideal if you're printing sensitive content. </p>
				<p>Only approved printing businesses will be able to see your print job details. This means you can be assured your print job details are kept private while you source the best quote.</p>

                </div>

    </div>

    </div>   

    

    

    <?php
	}
	
	else 
	
	{
	
	?>
	
	<!--- begin entire page for Requesting Client and Quoting Businesses --->
	
	
	
	<div id="content">



<?php 



			if(function_exists('bcn_display'))

		{

		    echo '<div class="breadcrumb-wrap"><div class="padd10" style="padding-left:0">';	

		    bcn_display();

			echo '</div></div> ';

		}



?>	

	

	

	

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

<?php





	$location   		= get_post_meta(get_the_ID(), "Location", true);

	$ending     		= get_post_meta(get_the_ID(), "ending", true);

	$featured     		= get_post_meta(get_the_ID(), "featured", true);

	$private_bids     	= get_post_meta(get_the_ID(), "private_bids", true);

	

	//---- increase views

	

	$views    	= get_post_meta(get_the_ID(), "views", true);

	$views 		= $views + 1;

	update_post_meta(get_the_ID(), "views", $views);



	



?>	





<?php



	if(isset($_POST['report_this']))

	{

		

		if(isset($_SESSION['reported-soon']))

		{

			$rp = $_SESSION['reported-soon'];

			if($rp < current_time('timestamp',0)) { $_SESSION['reported-soon'] = current_time('timestamp',0) + 60; $rep_ok = 1; }

			else { $rep_ok = 0; }

		}

		else

		{

			$_SESSION['reported-soon'] = current_time('timestamp',0) + 60; $rep_ok = 1;	

		}

		

		if($rep_ok == 1)

		{

		

		$pid_rep = $_POST['pid_rep'];

		$reason_report = nl2br($_POST['reason_report']);

		

		//---- send email to admin

		$subject = __("Report offensive project")." : ".get_the_title();

		

		$message = __("This project has been reported as offensive");

		$message .= " : <a href='".get_permalink(get_the_ID())."'>".get_the_title()."</a>"; 

		$message .= " <br/>Message: ".strip_tags($_POST['reason_report']); 

		

		$recipients = get_bloginfo('admin_email');

		

		ProjectTheme_send_email($recipients, $subject, $message);

		

		//------------------------

		?>

        <div class="my_box3">

            <div class="padd10">

        		<div class="box_content">

                

                	<?php _e('Thank you! Your report has been submitted.','ProjectTheme'); ?>

                

       			</div>

        	</div>

        </div>

        

        <div class="clear10"></div>

		

		<?php

		}

		else

		{

		?>	

		

        

        <div class="my_box3">

            <div class="padd10">

        		<div class="box_content" style="color:red;"><b>

                

                	<?php _e('Slow down buddy! You reported this before.','ProjectTheme'); ?>

                </b>

       			</div>

        	</div>

        </div>

        

        <div class="clear10"></div>	

			

		<?php	

		}

	}



?>



<div id="report-this" style="display:none">

<div class="my_box3">

            <div class="padd10">

            

            	<div class="box_title"><?php echo __("Report this print job",'ProjectTheme'); ?></div>

                <div class="box_content">

					<form method="post"><input type="hidden" value="<?php the_ID(); ?>" name="pid_rep" />

                    <ul class="post-new3">



        

        <li>

        	<h2><?php echo __('Reason for reporting','ProjectTheme'); ?>:</h2>

        <p><textarea rows="4" cols="40" class="do_input"  name="reason_report"></textarea></p>

        </li>

        

        

     

        

        <li>

        <h2>&nbsp;</h2>

        <p><input type="submit" name="report_this" value="<?php _e('Submit Report','ProjectTheme'); ?>" /></p>

        </li>

    

    

    </ul>

    </form>

                    

                    

				</div>

			</div>

			</div>

            

            <div class="clear10"></div>



</div>





<!-- ######### -->



<?php



	if(isset($_POST['contact_seller']))

	{

		

		if(isset($_SESSION['contact_soon']))

		{

			$rp = $_SESSION['contact_soon'];

			if($rp < current_time('timestamp',0)) { $_SESSION['contact_soon'] = current_time('timestamp',0) + 60; $rep_ok = 1; }

			else { $rep_ok = 0; }

		}

		else

		{

			$_SESSION['contact_soon'] = current_time('timestamp',0) + 60; $rep_ok = 1;	

		}

		

		if($rep_ok == 1)

		{

		

		$subject = $_POST['subject'];

		$email = $_POST['email'];

		$message = nl2br($_POST['message']);

		

		//---- send email to admin



		

		$p = get_post(get_the_ID());

		$a = $p->post_author;

		$a = get_userdata($a);

		

		ProjectTheme_send_email($a->user_email, $subject, $message."<br/>From Email: ".$email);

		

		//------------------------

		?>

        <div class="my_box3">

            <div class="padd10">

        		<div class="box_content">

                

                	<?php _e('Thank you! Your message has been sent.','ProjectTheme'); ?>

                

       			</div>

        	</div>

        </div>

        

        <div class="clear10"></div>

		

		<?php

		}

		else

		{

		?>	

			

            <div class="my_box3">

            <div class="padd10">

        		<div class="box_content">

                

                	<?php _e('Slow down buddy!.','ProjectTheme'); ?>

                

       			</div>

        	</div>

        </div>

        

        <div class="clear10"></div>

			

            

           <?php

		}

	}



?>











 			<div class="my_box3">

            

            

            	<div class="box_title ad_page_title"><?php the_title() ?> 

                <?php

				

				if($featured == "1")

				echo '<span class="featured_thing_project">'.__('Featured Project','ProjectTheme').'</span>';

				

				if($hide_project_p == "1")

				echo '<span class="private_thing_project">'.__('Private','ProjectTheme').'</span>';

				

				?>

                

                </div>

                <div class="box_content">

				<?php

				

					$ProjectTheme_enable_images_in_projects = get_option('ProjectTheme_enable_images_in_projects');

					if($ProjectTheme_enable_images_in_projects == "yes"):

				

				?>

                

                

				<!--- <div class="prj-page-image-holder">

                <?php if($featured == "1"): ?>

                <div class="featured-two"></div>

                <?php endif; ?>

                

                 <?php if($private_bids == 'yes' or $private_bids == '1' or $private_bids == 1): ?>

                <div class="sealed-two"></div>

                <?php endif; ?>

                    

						<img class="img_class" src="<?php echo ProjectTheme_get_first_post_image(get_the_ID(), 250, 170); ?>" alt="<?php the_title(); ?>" />

						

						<?php

				

				$arr = ProjectTheme_get_post_images(get_the_ID(), 4);

				

				if($arr)

				{

					

				

				echo '<ul class="image-gallery" style="padding-top:10px">';

				foreach($arr as $image)

				{

					echo '<li><a href="'.ProjectTheme_generate_thumb($image, -1,600).'" rel="image_gal1"><img 

					src="'.ProjectTheme_generate_thumb($image, 50,50).'" class="img_class" /></a></li>';

				}

				echo '</ul>';

				

				

				}

				//else { echo __('No images.') ;}

				

				?>

						

					</div>  --->
					
					<style> .project-page-details-holder { width:100% } </style>

					<?php else: ?>

                    

                    

                    <style> .project-page-details-holder { width:100% } </style>

					

					<?php endif; ?>

				

                <?php

				

				$closed 	 = get_post_meta(get_the_ID(),'closed',true);

				

				?>

                

				<div class="project-page-details-holder">

                <?php 

				if($closed == "0") :

				if($bid_posted == "0"): ?> 

		

                        <div class="bid_panel_err">

                        <div class="padd10">

                        <?php _e("Your quote has NOT been posted. Please correct the errors and try again.",'ProjectTheme');

                                echo '<br/>';

                                foreach($errors as $err)

                                echo $err.'<br/>';

                         ?>

                        </div>

                        </div>

                

                <?php endif; ?>

                

                

                <?php if($_GET['bid_posted'] == 1): ?>

		

                        <div class="bid_panel_ok">

                        <div class="padd10">

                        <?php _e("Your quote has been posted.",'ProjectTheme');

                                

                         ?>

                        </div>

                        </div>

                

                <?php endif; ?>



               

               

               	<div class="bid_panel">

                <div class="padd10">

                <form method="post">

                	<ul class="project-details">

							<!---<li>

								<h3><?php echo __("Project Budget",'ProjectTheme'); ?>:</h3>

								<p><?php echo ProjectTheme_get_budget_name_string_fromID(get_post_meta(get_the_ID(), 'budgets', true)); ?></p>

							</li> --->

                            

                            

                            <li>

								<h3><?php echo __("Average Quote",'ProjectTheme'); ?>:</h3>

								<p><?php 
								if (projectTheme_number_of_bid(get_the_ID()) < 2)
								{
								echo 'Hidden until two quotes are lodged';
								}
								else
								{
								echo ProjectTheme_average_bid(get_the_ID());
								}
								 ?></p>

							</li>

                            

                            <li>

								<h3><?php echo __("No. Of Quotes",'ProjectTheme'); ?>:</h3>

								<p><?php echo projectTheme_number_of_bid(get_the_ID()); ?></p>

							</li>
							
							<?php

				

								$rt = get_option('projectTheme_show_project_views');
				
								
				
								if($rt != 'no'):
				
								?>
				
								
				
								<li>
				
									<h3><?php _e("Viewed",'ProjectTheme');?>:</h3>
				
									<p><?php echo $views; ?> <?php _e("times",'ProjectTheme');?></p> 
				
								</li>
				
								<?php endif; ?>

                            

                            <?php

								

								global $current_user;

								get_currentuserinfo();

								$uid = $current_user->ID;

								

								if($closed == "0" && ProjectTheme_is_user_provider($uid) == true):

							

							?>

                            <li style="text-align: center; padding-bottom: 10px;" >

								<br><a href="#" class="post_bid_btn_new" rel="<?php the_ID(); ?>"><?php _e('Submit A Quote','ProjectTheme'); ?></a>

							</li>

                          	<?php endif; ?>

                            

                	</ul>

                   </form>

                </div>

                </div>

               

                

                <?php  else: 

				// project closed

				?>

                

                <div class="bid_panel">

                <div class="padd10">

                

                	<?php

					

					$pid 	= get_the_ID();

					$winner = get_post_meta(get_the_ID(), 'winner', true);

					

					if(!empty($winner))

					{

						

						global $wpdb;

						$q = "select bid from ".$wpdb->prefix."project_bids where pid='$pid' and winner='1'";

						$r = $wpdb->get_results($q);

						$r = $r[0];

						

						_e("Print job closed for price: ",'ProjectTheme');

						echo ProjectTheme_get_show_price($r->bid);

						

					}

					

					?>

                

                </div>

                </div>

                

                <?php endif; ?>

                <div class="clear10"></div>

                

						<ul class="project-details">

				     

                             <?php

		

			$ProjectTheme_enable_project_location = get_option('ProjectTheme_enable_project_location');

			if($ProjectTheme_enable_project_location == "yes"):

		

		?>               

							<li>

								<img src="<?php echo get_bloginfo('template_url'); ?>/images/location.png" width="20" height="20" /> 

								<h3><?php echo __("Location",'ProjectTheme'); ?>:</h3>

								<p><?php echo get_the_term_list( get_the_ID(), 'project_location', '', ', ', '' ); ?></p>

							</li>

				

                <?php endif; ?>

                			

							<li>

								<img src="<?php echo get_bloginfo('template_url'); ?>/images/cal.png" width="20" height="20" /> 

								<h3><?php echo __("Posted on",'ProjectTheme'); ?>:</h3>

								<p><?php the_time("jS F Y g:i A"); ?></p>

							</li>

							

							

							<li>

								<img src="<?php echo get_bloginfo('template_url'); ?>/images/clock.png" width="20" height="20" /> 

								<h3><?php echo __("Time Left",'ProjectTheme'); ?>:</h3>

								<p><?php echo ($closed == "0" ? ProjectTheme_prepare_seconds_to_words($ending - current_time('timestamp',0)) 

								: __("Expired/Closed",'ProjectTheme')); ?></p>

							</li>

							

                             <?php

							

								if($closed == "0"):

							

							?>

                            <li>

								<img src="<?php echo get_bloginfo('template_url'); ?>/images/msg.gif" width="20" height="20" /> 

								<h3><?php echo __("Message Board",'ProjectTheme'); ?>:</h3>

								<p><a href="#" class="message_brd_cls" rel="<?php the_ID(); ?>"><?php _e('View Board','ProjectTheme'); ?></a> | <a href="#" class="message_brd_cls" rel="<?php the_ID(); ?>"><?php _e('Send Message','ProjectTheme'); ?></a></p>

							</li>

							<?php endif; ?>

                            

						</ul>

						

						

												

					</div>

				

				

				</div>

			</div>

			

			

			<div class="clear10"></div>

			

			<!-- ####################### -->

			

			<div class="my_box3">

           

            

            	<div class="box_title"><?php echo __("Description",'ProjectTheme'); ?></div>

                <div class="box_content">

					<?php the_content(); 

					

					do_action('ProjectTheme_after_description_in_single_proj_page');

					

					 ?>
					 </div>

			</div>
			
			<div class="my_box3">
            	<div class="box_title">Details</div>

<p><b>Category:</b> <?php echo get_the_term_list( get_the_ID(), 'project_cat', '', ', ', '' ); ?>

</p>

					 

					 <?php

				

				$my_arrms = true;

				$my_arrms = apply_filters('ProjectTheme_show_fields_in_sidebar', $my_arrms);

				

				if($my_arrms == true): 

				

				$arrms = ProjectTheme_get_project_fields_values(get_the_ID());

				

				

				if(count($arrms) > 0)

					for($i=0;$i<count($arrms);$i++)

					{

					

					

				

				?>


               

					<b><?php  echo $arrms[$i]['field_name'];?>:</b>	<?php echo $arrms[$i]['field_value']; ?><br>

				

                

				<?php } endif; ?>

				

			</div>

		

			

			<div class="clear10"></div>

            
			
			
			
			
			 <?php

			
	//---------------------

	// build the exclude list

	$exclude = array();

	$pid = get_the_ID();

	$args = array(

	'order'          => 'ASC',

	'post_type'      => 'attachment',

	'post_parent'    => $pid,

	'meta_key'		 => 'act_dig_file',

	'meta_value'	 => '1',

	'numberposts'    => -1,

	'post_status'    => null,

	);
	
	

	$attachments = get_posts($args);



						   	$ProjectTheme_enable_project_files = get_option('ProjectTheme_enable_project_files');						   

						   	if($ProjectTheme_enable_project_files != "no"):

						   

						   ?>

       

     <div class="my_box3">

            

            

            	<div class="box_title"><?php _e("Relevant Files",'ProjectTheme'); ?></div>

		<p>

        

        <ul class="other-dets other-dets2">

				<?php

				

				if(count($attachments) == 0) echo __('No job files.','ProjectTheme');

				

				foreach($attachments as $at)

				{

					 

			
              

					 

				?>


                <li> <a href="<?php echo $at->guid; ?>"><?php echo $at->post_title; ?></a>

				</li> 

			<?php }   ?>		

			</ul>

   		</p>

   </div>

  <?php endif; ?>  
  
  
  

            <?php

			

				$ProjectTheme_enable_images_in_projects = get_option('ProjectTheme_enable_images_in_projects');

				$ProjectTheme_enable_images_in_projects = apply_filters('ProjectTheme_enable_images_in_projects_hk', $ProjectTheme_enable_images_in_projects);

			 	

				if($ProjectTheme_enable_images_in_projects == "yes"):

			

			?>

			<div class="clear10"></div>

			

			<!-- ####################### -->

			

			<div class="my_box3">

           

            

            	<div class="box_title"><?php echo __("Images, e.g. logos, artwork",'ProjectTheme'); ?></div>

                <div class="box_content">

				<?php

				

				$arr = ProjectTheme_get_post_images(get_the_ID());

				$xx_w = 600;

				$projectTheme_width_of_project_images = get_option('projectTheme_width_of_project_images');

				

				if(!empty($projectTheme_width_of_project_images)) $xx_w = $projectTheme_width_of_project_images;

				if(!is_numeric($xx_w)) $xx_w = 600;

				

				if($arr)

				{

					

				

				echo '<ul class="image-gallery">';

				foreach($arr as $image)

				{

					echo '<li><a href="'.ProjectTheme_generate_thumb($image, -1,$xx_w).'" rel="image_gal2"><img src="'.ProjectTheme_generate_thumb($image, 100,80).'" 

					class="img_class" /></a></li>';

				}

				echo '</ul>';

				

				}

				else { echo __('No images.','ProjectTheme') ;}

				

				?>

				

				

				</div>

			</div>

			<?php endif; ?>

            

            <!-- ####################### -->

			<?php 

			

			$private_bids = get_post_meta(get_the_ID(), 'private_bids', true);

			

			?>

			<div class="my_box3">

            

            

            	<div class="box_title"><?php echo __("Posted Quotes",'ProjectTheme'); ?> <?php

				

				if($private_bids == 'yes' or $private_bids == '1' or $private_bids == 1) _e('(quotes are only displayed to the client)','ProjectTheme');

				

				 ?></div>

                <div class="box_content">

				<?php

				$ProjectTheme_enable_project_files = get_option('ProjectTheme_enable_project_files');

				$winner = get_post_meta(get_the_ID(), 'winner', true);

				$post = get_post(get_the_ID());

				global $wpdb;

				$pid = get_the_ID();

				

				$bids = "select * from ".$wpdb->prefix."project_bids where pid='$pid' order by id DESC";

				$res  = $wpdb->get_results($bids);

			

				if($post->post_author == $uid) $owner = 1; else $owner = 0;

				

				if(count($res) > 0)

				{

					

					if($private_bids == 'yes' or $private_bids == '1' or $private_bids == 1)

					{

						if ($owner == 1) $show_stuff = 1;

						else if(projectTheme_current_user_has_bid($uid, $res)) $show_stuff = 1;

						else $show_stuff = 0;

					}

					else $show_stuff = 1;

					

					//------------

					

					if($show_stuff == 1):

					

						echo '<table id="my_bids" width="100%">';

						echo '<thead><tr>';

							echo '<th>'.__('Printer','ProjectTheme').'</th>';

							echo '<th>'.__('Quote Amount','ProjectTheme').'</th>';

							echo '<th>'.__('Submitted','ProjectTheme').'</th>';

							echo '<th>'.__('Est. Days To Complete','ProjectTheme').'</th>';
							
							if($ProjectTheme_enable_project_files != "no")

								echo '<th>'.__('Quote Files','ProjectTheme').'</th>';

							if ($owner == 1)
							 {
							
							echo '<th>'.__('Messaging','ProjectTheme').'</th>';

								if(empty($winner))

									echo '<th>'.__('Choose Winner','ProjectTheme').'</th>';
							}
							elseif ($closed != "1")
							{
								echo '<th>'.__('Edit','ProjectTheme').'</th>';
							}	
							

							

							if($closed == "1") echo '<th>'.__('Winner','ProjectTheme').'</th>';

						

						echo '</tr></thead><tbody>';

					

					endif;
					
					
					

					//-------------

					

					foreach($res as $row)

					{

						

						if ($owner == 1) $show_this_around = 1;

						else

						{

							if($private_bids == 'yes' or $private_bids == '1' or $private_bids == 1)

							{

								if($uid == $row->uid) 	$show_this_around = 1;

								else $show_this_around = 0;

							}

							else

							$show_this_around = 1;

							

						}

						 

						if($show_this_around == 1):

						

						$user = get_userdata($row->uid);

						echo '<tr>';

						echo '<th><a href="'.ProjectTheme_get_user_profile_link($user->ID).'">'.$user->user_login.'</a></th>';

						echo '<th>'.ProjectTheme_get_show_price($row->bid).'</th>';

						echo '<th>'.date("d-M-Y H:i:s", $row->date_made).'</th>';

						echo '<th>'. $row->days_done .' days</th>';
						
						if($ProjectTheme_enable_project_files != "no")

							echo '<th><a href="#" class="get_files" rel="'.get_the_ID().'_'.$row->uid.'">'.__('View Files','ProjectTheme').'</a></th>';

						if ($owner == 1 ) {

							echo '<th><a href="'.ProjectTheme_get_priv_mess_page_url('send', '', '&uid='.$row->uid.'&pid='.get_the_ID()).'">'.__('Send Message','ProjectTheme').'</a></th>';
							
							$nr = 7;

							if(empty($winner)) // == 0)

								echo '<th><a href="'.get_bloginfo('siteurl').'/?p_action=choose_winner&pid='.get_the_ID().'&bid='.$row->id.'">'.__('Select','ProjectTheme').'</a></th>';						

							}

						else
						{
						 $nr = 4;
						 if ($closed != "1")
						 echo '<th><a href="#" class="post_bid_btn_new" rel="'.get_the_ID().'">Edit</a></th>';	
						}
									

						if($closed == "1") { if($row->winner == 1) echo '<th>'.__('<strong class="private_thing_project">YES</strong>','ProjectTheme').'</th>'; else echo '<th>&nbsp;</th>'; }

						

						echo '</tr>';

					
						
				
				
						echo '<tr>';

						echo '<th colspan="'.$nr.'">'.$row->description.'</th>';

						echo '</tr>';
						
						
						
				$arrms = ProjectTheme_get_user_fields_values($user->ID);
				
				if(count($arrms) > 0) 
					for($i=0;$i<count($arrms);$i++)
					{
				
                echo '
					<tr><th class="my_td_with_border" colspan="'.$nr.'" width="300">
					<pre><strong>'.$arrms[$i]['field_name'].' ('.$user->user_login.')</strong><br>'.$arrms[$i]['field_value'].'</pre></th></tr>
                ';
					}

						endif;

					}

					

					echo '</tbody></table>';

				}

				else _e("No quotes placed yet.",'ProjectTheme');

				?>	

				</div>

			</div>

			

			

			

			<div class="clear10"></div>

			

			<!-- ####################### -->

			<?php

		

			$ProjectTheme_enable_project_location = get_option('ProjectTheme_enable_project_location');

			if($ProjectTheme_enable_project_location == "yes"):

		

		?>

        

			<div class="my_box3">

            

            

            	<div class="box_title"><?php echo __("Map Location",'ProjectTheme'); ?></div>

                <div class="box_content">

	

				<div id="map" style="width: 655px; height: 300px;border:2px solid #ccc;float:left"></div>

				

                <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script> 

            

            <script type="text/javascript"

            src="<?php echo get_bloginfo('template_url'); ?>/js/mk.js"></script> 

                                                <script type="text/javascript"> 

   







	  var geocoder;

  var map;

  function initialize() {

    geocoder = new google.maps.Geocoder();

    var latlng = new google.maps.LatLng(-34.397, 150.644);

    var myOptions = {

      zoom: 13,

      center: latlng,

      mapTypeId: google.maps.MapTypeId.ROADMAP

    }

    map = new google.maps.Map(document.getElementById("map"), myOptions);

  }



  function codeAddress(address) {

    

    geocoder.geocode( { 'address': address}, function(results, status) {

      if (status == google.maps.GeocoderStatus.OK) {

        map.setCenter(results[0].geometry.location);

        var marker = new MarkerWithLabel({

            

            position: results[0].geometry.location,

			map: map,

       labelContent: address,

       labelAnchor: new google.maps.Point(22, 0),

       labelClass: "labels", // the CSS class for the label

       labelStyle: {opacity: 1.0}



        });

      } else {

        //alert("Geocode was not successful for the following reason: " + status);

      }

    });

  }



initialize();



codeAddress("<?php 



	global $post;

	$pid = $post->ID;



	$terms = wp_get_post_terms($pid,'project_location');

	foreach($terms as $term)

	{

		echo $term->name." ";

	}



	$location = get_post_meta($pid, "Location", true);	

	echo $location;

	

 ?>");



    </script> 

				

			

			</div>

			</div> <?php endif; ?>

			

			<!-- ####################### -->

			

<?php endwhile; // end of the loop. ?>







</div>



<?php



	echo '<div id="right-sidebar" class="page-sidebar">';

	echo '<ul class="xoxo">';

	
	?>

    

    	<li class="widget-container widget_text" id="ad-other-details">

		<h3 class="widget-title"><?php _e("Client Details",'ProjectTheme'); ?></h3>

		<p>

        

        <ul class="other-dets other-dets2">

        		<!-- [MODIFIED BY RISAN] HIDE 'POSTED BY' FIELD -->
        		<?php if ($is_administrator || $is_winner || $is_my_project) { ?>
					<li>
						<h3><?php _e("Posted by",'ProjectTheme');?>:</h3>
						<p>
							<a href="<?php bloginfo('siteurl'); ?>/?p_action=user_profile&post_author=<?php echo $post->post_author; ?>">
								<?php the_author() ?>
							</a>
						</p> 
					</li>
				<?php } ?>

                <?php

					

					$has_created 	= projectTheme_get_total_number_of_created_Projects($post->post_author);

					$has_closed 	= projectTheme_get_total_number_of_closed_Projects($post->post_author);

					$has_rated 		= projectTheme_get_total_number_of_rated_Projects($post->post_author);

				

				?>

                

                <li>
					<h3><?php _e("Feedback",'ProjectTheme');?>:</h3>
					<p id='my_stars_rating_done'><?php echo ProjectTheme_project_get_star_rating($post->post_author); ?></p> 
				</li>

				<!-- [MODIFIED BY RISAN] HIDE 'VIEW USER FEEDBACK' FIELD -->
        		<?php if ($is_administrator || $is_winner || $is_my_project) { ?>
	                <li>
	                 	<a href="<?php echo ProjectTheme_get_user_feedback_link($post->post_author); ?>">
	                 		<?php _e('View User Feedback','ProjectTheme'); ?>
	                 	</a>
	                </li>
	            <?php } ?>

                <li>

					<h3><?php _e("Has created:",'ProjectTheme');?></h3>

					<p><?php echo sprintf(__("%s print job(s)",'ProjectTheme'), $has_created); ?></p> 

				</li>

                

                

                <li>

					<h3><?php _e("Has closed:",'ProjectTheme');?></h3>

					<p><?php echo sprintf(__("%s print job(s)",'ProjectTheme'), $has_closed); ?></p> 

				</li>

            

            

            	<li>

					<h3><?php _e("Has rated:",'ProjectTheme');?></h3>

					<p><?php echo sprintf(__("%s printers(s)",'ProjectTheme'), $has_rated); ?></p> 

				</li>

            

            

            	<br/><br/>

            	<!-- [MODIFIED BY RISAN] HIDE 'MORE PRINT JOBS' FIELD -->
        		<?php if ($is_administrator || $is_winner || $is_my_project) { ?>
               		<a href="<?php bloginfo('siteurl'); ?>/?p_action=user_profile&post_author=<?php echo $post->post_author; ?>">
               			<?php _e('More print jobs by this client...','ProjectTheme'); ?>
               		</a>
               		<br/>
               	<?php } ?>

               

                		

			</ul>

   		</p>

   </li>

       

      

    

    

	<li class="widget-container widget_text" id="ad-other-details">

		<h3 class="widget-title"><?php _e("Share on...",'ProjectTheme'); ?></h3>

		<p>

        

        <div class="add-this">

						<!-- AddThis Button BEGIN -->

							<div class="addthis_toolbox addthis_default_style addthis_32x32_style">

							<a class="addthis_button_linkedin"></a>
							<a class="addthis_button_facebook"></a>

							<a class="addthis_button_twitter"></a>

							<a class="addthis_button_email"></a>


							<a class="addthis_counter addthis_bubble_style"></a>

							</div>

							<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4df68b4a2795dcd9"></script>

							<!-- AddThis Button END -->

						</div>	

        

   		</p>

   </li>

       

    

	

        		

                

				

                

                

				

                

				

			</ul>

			<?php

				

				if(ProjectTheme_is_owner_of_post())

				{

					if(empty($winner))
						{
				?>

				

			<a href="<?php echo get_bloginfo('siteurl'); ?>/?p_action=edit_project&pid=<?php the_ID(); ?>" class="nice_link"><?php _e("Edit",'ProjectTheme'); ?></a> 

			<!--- <a href="<?php echo get_bloginfo('siteurl'); ?>/?p_action=repost_project&pid=<?php the_ID(); ?>" class="nice_link"><?php _e("Repost",'ProjectTheme'); ?></a> --->

		 <a href="<?php echo get_bloginfo('siteurl'); ?>/?p_action=delete_project&pid=<?php the_ID(); ?>" class="nice_link"><?php _e("Delete",'ProjectTheme'); ?></a> 

			

			<?php } } else {?>

			

			<a href="#" id="report-this-link" class="nice_link"><?php _e("Report this Print Job",'ProjectTheme'); ?></a>

            <!--- <a href="<?php

            $post = get_post(get_the_ID());

			

			

			echo ProjectTheme_get_priv_mess_page_url('send', '', '&uid='.$post->post_author.'&pid='.get_the_ID());

			

			?>" class="nice_link"><?php _e("Contact Seller",'ProjectTheme'); ?></a> --->

				

                <?php } ?>

		</p>

	</li>

	

	

	<?php

	

						dynamic_sidebar( 'project-widget-area' );

	echo '</ul>';

	echo '</div>';





//===============================================================================================



	get_footer();

?>
	
	
	
	<!--- end entire page for Requesting Client and Quoting Businesses --->
	
	<?php 
	
	}}

	get_footer();

	exit;

	endif;
	

	?>













<div id="content">



<?php 



			if(function_exists('bcn_display'))

		{

		    echo '<div class="breadcrumb-wrap"><div class="padd10" style="padding-left:0">';	

		    bcn_display();

			echo '</div></div> ';

		}



?>	

	

	

	

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

<?php





	$location   		= get_post_meta(get_the_ID(), "Location", true);

	$ending     		= get_post_meta(get_the_ID(), "ending", true);

	$featured     		= get_post_meta(get_the_ID(), "featured", true);

	$private_bids     	= get_post_meta(get_the_ID(), "private_bids", true);

	

	//---- increase views

	

	$views    	= get_post_meta(get_the_ID(), "views", true);

	$views 		= $views + 1;

	update_post_meta(get_the_ID(), "views", $views);



	



?>	





<?php



	if(isset($_POST['report_this']))

	{

		

		if(isset($_SESSION['reported-soon']))

		{

			$rp = $_SESSION['reported-soon'];

			if($rp < current_time('timestamp',0)) { $_SESSION['reported-soon'] = current_time('timestamp',0) + 60; $rep_ok = 1; }

			else { $rep_ok = 0; }

		}

		else

		{

			$_SESSION['reported-soon'] = current_time('timestamp',0) + 60; $rep_ok = 1;	

		}

		

		if($rep_ok == 1)

		{

		

		$pid_rep = $_POST['pid_rep'];

		$reason_report = nl2br($_POST['reason_report']);

		

		//---- send email to admin

		$subject = __("Report offensive project")." : ".get_the_title();

		

		$message = __("This project has been reported as offensive");

		$message .= " : <a href='".get_permalink(get_the_ID())."'>".get_the_title()."</a>"; 

		$message .= " <br/>Message: ".strip_tags($_POST['reason_report']); 

		

		$recipients = get_bloginfo('admin_email');

		

		ProjectTheme_send_email($recipients, $subject, $message);

		

		//------------------------

		?>

        <div class="my_box3">

            <div class="padd10">

        		<div class="box_content">

                

                	<?php _e('Thank you! Your report has been submitted.','ProjectTheme'); ?>

                

       			</div>

        	</div>

        </div>

        

        <div class="clear10"></div>

		

		<?php

		}

		else

		{

		?>	

		

        

        <div class="my_box3">

            <div class="padd10">

        		<div class="box_content" style="color:red;"><b>

                

                	<?php _e('Slow down buddy! You reported this before.','ProjectTheme'); ?>

                </b>

       			</div>

        	</div>

        </div>

        

        <div class="clear10"></div>	

			

		<?php	

		}

	}



?>



<div id="report-this" style="display:none">

<div class="my_box3">

            <div class="padd10">

            

            	<div class="box_title"><?php echo __("Report this print job",'ProjectTheme'); ?></div>

                <div class="box_content">

					<form method="post"><input type="hidden" value="<?php the_ID(); ?>" name="pid_rep" />

                    <ul class="post-new3">



        

        <li>

        	<h2><?php echo __('Reason for reporting','ProjectTheme'); ?>:</h2>

        <p><textarea rows="4" cols="40" class="do_input"  name="reason_report"></textarea></p>

        </li>

        

        

     

        

        <li>

        <h2>&nbsp;</h2>

        <p><input type="submit" name="report_this" value="<?php _e('Submit Report','ProjectTheme'); ?>" /></p>

        </li>

    

    

    </ul>

    </form>

                    

                    

				</div>

			</div>

			</div>

            

            <div class="clear10"></div>



</div>





<!-- ######### -->



<?php



	if(isset($_POST['contact_seller']))

	{

		

		if(isset($_SESSION['contact_soon']))

		{

			$rp = $_SESSION['contact_soon'];

			if($rp < current_time('timestamp',0)) { $_SESSION['contact_soon'] = current_time('timestamp',0) + 60; $rep_ok = 1; }

			else { $rep_ok = 0; }

		}

		else

		{

			$_SESSION['contact_soon'] = current_time('timestamp',0) + 60; $rep_ok = 1;	

		}

		

		if($rep_ok == 1)

		{

		

		$subject = $_POST['subject'];

		$email = $_POST['email'];

		$message = nl2br($_POST['message']);

		

		//---- send email to admin



		

		$p = get_post(get_the_ID());

		$a = $p->post_author;

		$a = get_userdata($a);

		

		ProjectTheme_send_email($a->user_email, $subject, $message."<br/>From Email: ".$email);

		

		//------------------------

		?>

        <div class="my_box3">

            <div class="padd10">

        		<div class="box_content">

                

                	<?php _e('Thank you! Your message has been sent.','ProjectTheme'); ?>

                

       			</div>

        	</div>

        </div>

        

        <div class="clear10"></div>

		

		<?php

		}

		else

		{

		?>	

			

            <div class="my_box3">

            <div class="padd10">

        		<div class="box_content">

                

                	<?php _e('Slow down buddy!.','ProjectTheme'); ?>

                

       			</div>

        	</div>

        </div>

        

        <div class="clear10"></div>

			

            

           <?php

		}

	}



?>











 			<div class="my_box3">

            

            

            	<div class="box_title ad_page_title"><?php the_title() ?> 

                <?php

				

				if($featured == "1")

				echo '<span class="featured_thing_project">'.__('Featured Project','ProjectTheme').'</span>';

				

				if($hide_project_p == "1")

				echo '<span class="private_thing_project">'.__('Private','ProjectTheme').'</span>';

				

				?>

                

                </div>

                <div class="box_content">

				<?php

				

					$ProjectTheme_enable_images_in_projects = get_option('ProjectTheme_enable_images_in_projects');

					if($ProjectTheme_enable_images_in_projects == "yes"):

				

				?>

                

                

				<!--- <div class="prj-page-image-holder">

                <?php if($featured == "1"): ?>

                <div class="featured-two"></div>

                <?php endif; ?>

                

                 <?php if($private_bids == 'yes' or $private_bids == '1' or $private_bids == 1): ?>

                <div class="sealed-two"></div>

                <?php endif; ?>
				
				

                    

						<img class="img_class" src="<?php echo ProjectTheme_get_first_post_image(get_the_ID(), 250, 170); ?>" alt="<?php the_title(); ?>" />

						

						<?php

				

				$arr = ProjectTheme_get_post_images(get_the_ID(), 4);

				

				if($arr)

				{

					

				

				echo '<ul class="image-gallery" style="padding-top:10px">';

				foreach($arr as $image)

				{

					echo '<li><a href="'.ProjectTheme_generate_thumb($image, -1,600).'" rel="image_gal1"><img 

					src="'.ProjectTheme_generate_thumb($image, 50,50).'" class="img_class" /></a></li>';

				}

				echo '</ul>';

				

				

				}

				//else { echo __('No images.') ;}

				

				?>

						

					</div>  --->
					
					<style> .project-page-details-holder { width:100% } </style>

					<?php else: ?>

                    

                    

                    <style> .project-page-details-holder { width:100% } </style>

					

					<?php endif; ?>

				

                <?php

				

				$closed 	 = get_post_meta(get_the_ID(),'closed',true);

				

				?>

                

				<div class="project-page-details-holder">

                <?php 

				if($closed == "0") :

				if($bid_posted == "0"): ?> 

		

                        <div class="bid_panel_err">

                        <div class="padd10">

                        <?php _e("Your quote has NOT been posted. Please correct the errors and try again.",'ProjectTheme');

                                echo '<br/>';

                                foreach($errors as $err)

                                echo $err.'<br/>';

                         ?>

                        </div>

                        </div>

                

                <?php endif; ?>

                

                

                <?php if($_GET['bid_posted'] == 1): ?>

		

                        <div class="bid_panel_ok">

                        <div class="padd10">

                        <?php _e("Your quote has been posted.",'ProjectTheme');

                                

                         ?>

                        </div>

                        </div>

                

                <?php endif; ?>



               

               

               	<div class="bid_panel">

                <div class="padd10">

                <form method="post">

                	<ul class="project-details">

							<!---<li>

								<h3><?php echo __("Project Budget",'ProjectTheme'); ?>:</h3>

								<p><?php echo ProjectTheme_get_budget_name_string_fromID(get_post_meta(get_the_ID(), 'budgets', true)); ?></p>

							</li> --->
							
							<!-- [ADDED BY RISAN] Add lowest Quote -->
							<li>
								<h3><?php echo __("Lowest Quote",'ProjectTheme'); ?>:</h3>
								<p>
									<?php 
										// If there is no bid, show '-'
										if (projectTheme_number_of_bid(get_the_ID()) <= 0) echo '-';
										// Else get the lowest bid!
										else echo ProjectTheme_lowest_bid(get_the_ID());
									?>
								</p>
							</li>

							<!-- [ADDED BY RISAN] Add highest Quote -->
							<li>
								<h3><?php echo __("Highest Quote",'ProjectTheme'); ?>:</h3>
								<p>
									<?php 
										// If there is no bid, show '-'
										if (projectTheme_number_of_bid(get_the_ID()) <= 0) echo '-';
										// Else get the lowest bid!
										else echo projectTheme_highest_bid(get_the_ID());
									?>
								</p>
							</li>

							<!-- [MODIFIED BY RISAN] -->
							<li>
								<h3><?php echo __("Average Quote",'ProjectTheme'); ?>:</h3>
								<p>
									<?php 
										if (projectTheme_number_of_bid(get_the_ID()) < 2) {
											echo 'Hidden until two quotes are lodged';
										} else {
											echo ProjectTheme_average_bid(get_the_ID());
										}
									?>
								</p>
							</li>

                            

                            <li>

								<h3><?php echo __("No. Of Quotes",'ProjectTheme'); ?>:</h3>

								<p><?php echo projectTheme_number_of_bid(get_the_ID()); ?></p>

							</li>
							
							<?php

				

								$rt = get_option('projectTheme_show_project_views');
				
								
				
								if($rt != 'no'):
				
								?>
				
								
				
								<li>
				
									<h3><?php _e("Viewed",'ProjectTheme');?>:</h3>
				
									<p><?php echo $views; ?> <?php _e("times",'ProjectTheme');?></p> 
				
								</li>
				
								<?php endif; ?>

                            

                            <?php

								

								global $current_user;

								get_currentuserinfo();

								$uid = $current_user->ID;

								

								if($closed == "0" && ProjectTheme_is_user_provider($uid) == true):

							

							?>

                            <li style="text-align: center; padding-bottom: 10px;" >

								<br><a href="#" class="post_bid_btn_new" rel="<?php the_ID(); ?>"><?php _e('Submit A Quote','ProjectTheme'); ?></a>

							</li>

                          	<?php endif; ?>

                            

                	</ul>

                   </form>

                </div>

                </div>

               

                

                <?php  else: 

				// project closed

				?>

                

                <div class="bid_panel">

                <div class="padd10">

                

                	<?php

					

					$pid 	= get_the_ID();

					$winner = get_post_meta(get_the_ID(), 'winner', true);

					

					if(!empty($winner))

					{

						

						global $wpdb;

						$q = "select bid from ".$wpdb->prefix."project_bids where pid='$pid' and winner='1'";

						$r = $wpdb->get_results($q);

						$r = $r[0];

						

						_e("Print job closed for price: ",'ProjectTheme');

						echo ProjectTheme_get_show_price($r->bid);

						

					}

					

					?>

                

                </div>

                </div>

                

                <?php endif; ?>

                <div class="clear10"></div>

                

						<ul class="project-details">

				     

                             <?php

		

			$ProjectTheme_enable_project_location = get_option('ProjectTheme_enable_project_location');

			if($ProjectTheme_enable_project_location == "yes"):

		

		?>               

							<li>

								<img src="<?php echo get_bloginfo('template_url'); ?>/images/location.png" width="20" height="20" /> 

								<h3><?php echo __("Location",'ProjectTheme'); ?>:</h3>

								<p><?php echo get_the_term_list( get_the_ID(), 'project_location', '', ', ', '' ); ?></p>

							</li>

				

                <?php endif; ?>

                			

							<li>

								<img src="<?php echo get_bloginfo('template_url'); ?>/images/cal.png" width="20" height="20" /> 

								<h3><?php echo __("Posted on",'ProjectTheme'); ?>:</h3>

								<p><?php the_time("jS F Y g:i A"); ?></p>

							</li>

							

							

							<li>

								<img src="<?php echo get_bloginfo('template_url'); ?>/images/clock.png" width="20" height="20" /> 

								<h3><?php echo __("Time Left",'ProjectTheme'); ?>:</h3>

								<p><?php echo ($closed == "0" ? ProjectTheme_prepare_seconds_to_words($ending - current_time('timestamp',0)) 

								: __("Expired/Closed",'ProjectTheme')); ?></p>

							</li>

							

                             <?php

							

								if($closed == "0"):

							

							?>

                            <li>

								<img src="<?php echo get_bloginfo('template_url'); ?>/images/msg.gif" width="20" height="20" /> 

								<h3><?php echo __("Message Board",'ProjectTheme'); ?>:</h3>

								<p><a href="#" class="message_brd_cls" rel="<?php the_ID(); ?>"><?php _e('View Board','ProjectTheme'); ?></a> | <a href="#" class="message_brd_cls" rel="<?php the_ID(); ?>"><?php _e('Send Message','ProjectTheme'); ?></a></p>

							</li>

							<?php endif; ?>

                            

						</ul>

						

						

												

					</div>

				

				

				</div>

			</div>

			

			

			<div class="clear10"></div>

			

			<!-- ####################### -->

			

			<div class="my_box3">

           

            

            	<div class="box_title"><?php echo __("Description",'ProjectTheme'); ?></div>

                <div class="box_content">

					<?php the_content(); 

					

					do_action('ProjectTheme_after_description_in_single_proj_page');

					

					 ?>
					 </div>

			</div>
			
			<div class="my_box3">
            	<div class="box_title">Details</div>

<p><b>Category:</b> <?php echo get_the_term_list( get_the_ID(), 'project_cat', '', ', ', '' ); ?>

</p>

					 

					 <?php

				

				$my_arrms = true;

				$my_arrms = apply_filters('ProjectTheme_show_fields_in_sidebar', $my_arrms);

				

				if($my_arrms == true): 

				

				$arrms = ProjectTheme_get_project_fields_values(get_the_ID());

				

				

				if(count($arrms) > 0)

					for($i=0;$i<count($arrms);$i++)

					{

					

					

				

				?>


               

					<b><?php  echo $arrms[$i]['field_name'];?>:</b>	<?php echo $arrms[$i]['field_value']; ?><br>

				

                

				<?php } endif; ?>

				

			</div>

		

			

			<div class="clear10"></div>

            
			
			
			
			
			 <?php

			
	//---------------------

	// build the exclude list

	$exclude = array();

	$pid = get_the_ID();

	$args = array(

	'order'          => 'ASC',

	'post_type'      => 'attachment',

	'post_parent'    => $pid,

	'meta_key'		 => 'act_dig_file',

	'meta_value'	 => '1',

	'numberposts'    => -1,

	'post_status'    => null,

	);

	$attachments = get_posts($args);



						   	$ProjectTheme_enable_project_files = get_option('ProjectTheme_enable_project_files');						   

						   	if($ProjectTheme_enable_project_files != "no"):

						   

						   ?>

       

     <div class="my_box3">

            

            

            	<div class="box_title"><?php _e("Relevant Files",'ProjectTheme'); ?></div>

		<p>

        

        <ul class="other-dets other-dets2">

				<?php

				

				if(count($attachments) == 0) echo __('No job files.','ProjectTheme');

				

				foreach($attachments as $at)

				{

					 

			

					 

				?>

                

                <li> <a href="<?php echo $at->guid; ?>" target="_blank"><?php echo $at->post_title; ?></a>

				</li> 

			<?php }   ?>		

			</ul>

   		</p>

   </div>

  <?php endif; ?>  
  
  
  

            <?php

			

				$ProjectTheme_enable_images_in_projects = get_option('ProjectTheme_enable_images_in_projects');

				$ProjectTheme_enable_images_in_projects = apply_filters('ProjectTheme_enable_images_in_projects_hk', $ProjectTheme_enable_images_in_projects);

			 	

				if($ProjectTheme_enable_images_in_projects == "yes"):

			

			?>

			<div class="clear10"></div>

			

			<!-- ####################### -->

			

			<div class="my_box3">

           

            

            	<div class="box_title"><?php echo __("Images, e.g. logos, artwork",'ProjectTheme'); ?></div>

                <div class="box_content">

				<?php

				

				$arr = ProjectTheme_get_post_images(get_the_ID());

				$xx_w = 600;

				$projectTheme_width_of_project_images = get_option('projectTheme_width_of_project_images');

				

				if(!empty($projectTheme_width_of_project_images)) $xx_w = $projectTheme_width_of_project_images;

				if(!is_numeric($xx_w)) $xx_w = 600;

				

				if($arr)

				{

					

				

				echo '<ul class="image-gallery">';

				foreach($arr as $image)

				{

					echo '<li><a href="'.ProjectTheme_generate_thumb($image, -1,$xx_w).'" rel="image_gal2"><img src="'.ProjectTheme_generate_thumb($image, 100,80).'" 

					class="img_class" /></a></li>';

				}

				echo '</ul>';

				

				}

				else { echo __('No images.','ProjectTheme') ;}

				

				?>

				

				

				</div>

			</div>

			<?php endif; ?>

            

            <!-- ####################### -->

			<?php 

			

			$private_bids = get_post_meta(get_the_ID(), 'private_bids', true);

			

			?>

			<div class="my_box3">

            

            

            	<div class="box_title"><?php echo __("Posted Quotes",'ProjectTheme'); ?> <?php

				

				if($private_bids == 'yes' or $private_bids == '1' or $private_bids == 1) _e('(quotes are only displayed to the client)','ProjectTheme');

				

				 ?></div>

                <div class="box_content">

				<?php

				$ProjectTheme_enable_project_files = get_option('ProjectTheme_enable_project_files');

				$winner = get_post_meta(get_the_ID(), 'winner', true);

				$post = get_post(get_the_ID());

				global $wpdb;

				$pid = get_the_ID();

				

				$bids = "select * from ".$wpdb->prefix."project_bids where pid='$pid' order by id DESC";

				$res  = $wpdb->get_results($bids);

			

				if($post->post_author == $uid) $owner = 1; else $owner = 0;

				

				if(count($res) > 0)

				{

					

					if($private_bids == 'yes' or $private_bids == '1' or $private_bids == 1)

					{

						if ($owner == 1) $show_stuff = 1;

						else if(projectTheme_current_user_has_bid($uid, $res)) $show_stuff = 1;

						else $show_stuff = 0;

					}

					else $show_stuff = 1;

					

					//------------

					

					if($show_stuff == 1):

					

						echo '<table id="my_bids" width="100%">';

						echo '<thead><tr>';

							echo '<th>'.__('Username','ProjectTheme').'</th>';

							echo '<th>'.__('Bid Amount','ProjectTheme').'</th>';

							echo '<th>'.__('Submitted','ProjectTheme').'</th>';

							echo '<th>'.__('Days to Complete','ProjectTheme').'</th>';
							
							if($ProjectTheme_enable_project_files != "no")

								echo '<th>'.__('Quote Files','ProjectTheme').'</th>';

							if ($owner == 1)
							 {
							
							echo '<th>'.__('Messaging','ProjectTheme').'</th>';

								if(empty($winner))

									echo '<th>'.__('Choose Winner','ProjectTheme').'</th>';
							}
							else
							{
								echo '<th>'.__('Edit','ProjectTheme').'</th>';
							}	
							

							

							if($closed == "1") echo '<th>'.__('Winner','ProjectTheme').'</th>';

						

						echo '</tr></thead><tbody>';

					

					endif;

					

					//-------------

					

					foreach($res as $row)

					{

						

						if ($owner == 1) $show_this_around = 1;

						else

						{

							if($private_bids == 'yes' or $private_bids == '1' or $private_bids == 1)

							{

								if($uid == $row->uid) 	$show_this_around = 1;

								else $show_this_around = 0;

							}

							else

							$show_this_around = 1;

							

						}

						 

						if($show_this_around == 1):

						

						$user = get_userdata($row->uid);

						echo '<tr>';

						echo '<th><a href="'.ProjectTheme_get_user_profile_link($user->ID).'">'.$user->user_login.'</a></th>';

						echo '<th>'.ProjectTheme_get_show_price($row->bid).'</th>';

						echo '<th>'.date("d-M-Y H:i:s", $row->date_made).'</th>';

						echo '<th>'. $row->days_done .' days</th>';
						
						if($ProjectTheme_enable_project_files != "no")

							echo '<th><a href="#" class="get_files" rel="'.get_the_ID().'_'.$row->uid.'">'.__('View Files','ProjectTheme').'</a></th>';

						if ($owner == 1 ) {

							echo '<th><a href="'.ProjectTheme_get_priv_mess_page_url('send', '', '&uid='.$row->uid.'&pid='.get_the_ID()).'">'.__('Send Message','ProjectTheme').'</a></th>';

							$nr = 7;

							if(empty($winner)) // == 0)

								echo '<th><a href="'.get_bloginfo('siteurl').'/?p_action=choose_winner&pid='.get_the_ID().'&bid='.$row->id.'">'.__('Select','ProjectTheme').'</a></th>';						

							

						

						}

						else
						{
						 $nr = 4;
						 echo '<th><a href="#" class="post_bid_btn_new" rel="'.get_the_ID().'">Edit</a></th>';	
						}

						

						if($closed == "1") { if($row->winner == 1) echo '<th>'.__('<strong class="private_thing_project">YES</strong>','ProjectTheme').'</th>'; else echo '<th>&nbsp;</th>'; }

						

						echo '</tr>';

						

						echo '<tr>';

						echo '<th colspan="'.$nr.'" class="my_td_with_border">'.$row->description.'</th>';

						echo '</tr>';
						
						$arrms = ProjectTheme_get_user_fields_values($user->ID);
				
				if(count($arrms) > 0) 
					for($i=0;$i<count($arrms);$i++)
					{
				
                echo '
					<tr><th class="my_td_with_border" colspan="'.$nr.'" width="300">
					<pre><strong>'.$arrms[$i]['field_name'].' ('.$user->user_login.')</strong><br>'.$arrms[$i]['field_value'].'</pre></th></tr>
                ';
					}

						endif;

					}

					

					echo '</tbody></table>';

				}

				else _e("No quotes placed yet.",'ProjectTheme');

				?>	

				</div>

			</div>

			

			

			

			<div class="clear10"></div>

			

			<!-- ####################### -->

			<?php

		

			$ProjectTheme_enable_project_location = get_option('ProjectTheme_enable_project_location');

			if($ProjectTheme_enable_project_location == "yes"):

		

		?>

        

			<div class="my_box3">

            

            

            	<div class="box_title"><?php echo __("Map Location",'ProjectTheme'); ?></div>

                <div class="box_content">

	

				<div id="map" style="width: 655px; height: 300px;border:2px solid #ccc;float:left"></div>

				

                <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script> 

            

            <script type="text/javascript"

            src="<?php echo get_bloginfo('template_url'); ?>/js/mk.js"></script> 

                                                <script type="text/javascript"> 

   







	  var geocoder;

  var map;

  function initialize() {

    geocoder = new google.maps.Geocoder();

    var latlng = new google.maps.LatLng(-34.397, 150.644);

    var myOptions = {

      zoom: 13,

      center: latlng,

      mapTypeId: google.maps.MapTypeId.ROADMAP

    }

    map = new google.maps.Map(document.getElementById("map"), myOptions);

  }



  function codeAddress(address) {

    

    geocoder.geocode( { 'address': address}, function(results, status) {

      if (status == google.maps.GeocoderStatus.OK) {

        map.setCenter(results[0].geometry.location);

        var marker = new MarkerWithLabel({

            

            position: results[0].geometry.location,

			map: map,

       labelContent: address,

       labelAnchor: new google.maps.Point(22, 0),

       labelClass: "labels", // the CSS class for the label

       labelStyle: {opacity: 1.0}



        });

      } else {

        //alert("Geocode was not successful for the following reason: " + status);

      }

    });

  }



initialize();



codeAddress("<?php 



	global $post;

	$pid = $post->ID;



	$terms = wp_get_post_terms($pid,'project_location');

	foreach($terms as $term)

	{

		echo $term->name." ";

	}



	$location = get_post_meta($pid, "Location", true);	

	echo $location;

	

 ?>");



    </script> 

				

			

			</div>

			</div> <?php endif; ?>

			

			<!-- ####################### -->

			

<?php endwhile; // end of the loop. ?>







</div>



<?php



	echo '<div id="right-sidebar" class="page-sidebar">';

	echo '<ul class="xoxo">';

	
	?>

    

    	<li class="widget-container widget_text" id="ad-other-details">

		<h3 class="widget-title"><?php _e("Client Details",'ProjectTheme'); ?></h3>

		<p>

        

        <ul class="other-dets other-dets2">

        	<!-- [MODIFIED BY RISAN] HIDE 'POSTED BY' FIELD -->
        	<?php if ($is_administrator || $is_winner || $is_my_project) { ?>
				<li>
					<h3><?php _e("Posted by",'ProjectTheme');?>:</h3>
					<p>
						<a href="<?php bloginfo('siteurl'); ?>/?p_action=user_profile&post_author=<?php echo $post->post_author; ?>">
							<?php the_author() ?>
						</a>
					</p> 
				</li>
			<?php } ?>

                <?php

					

					$has_created 	= projectTheme_get_total_number_of_created_Projects($post->post_author);

					$has_closed 	= projectTheme_get_total_number_of_closed_Projects($post->post_author);

					$has_rated 		= projectTheme_get_total_number_of_rated_Projects($post->post_author);

				

				?>

                

                <li>

					<h3><?php _e("Feedback",'ProjectTheme');?>:</h3>

					<p id='my_stars_rating_done'><?php echo ProjectTheme_project_get_star_rating($post->post_author); ?></p> 

				</li>

			<!-- [MODIFIED BY RISAN] HIDE 'VIWE USER FEEDBACK' FIELD -->
        	<?php if ($is_administrator || $is_winner || $is_my_project) { ?>
                <li>
                 	<a href="<?php echo ProjectTheme_get_user_feedback_link($post->post_author); ?>">
                 		<?php _e('View User Feedback','ProjectTheme'); ?>
                 	</a>
                </li>
            <?php } ?>

                <li>

					<h3><?php _e("Has created:",'ProjectTheme');?></h3>

					<p><?php echo sprintf(__("%s print job(s)",'ProjectTheme'), $has_created); ?></p> 

				</li>

                

                

                <li>

					<h3><?php _e("Has closed:",'ProjectTheme');?></h3>

					<p><?php echo sprintf(__("%s print job(s)",'ProjectTheme'), $has_closed); ?></p> 

				</li>

            

            

            	<li>

					<h3><?php _e("Has rated:",'ProjectTheme');?></h3>

					<p><?php echo sprintf(__("%s printers(s)",'ProjectTheme'), $has_rated); ?></p> 

				</li>

            

            

            	<br/><br/>

            <!-- [MODIFIED BY RISAN] HIDE 'MORE PRINT JOBS' FIELD -->
        	<?php if ($is_administrator || $is_winner || $is_my_project) { ?>
            	<a href="<?php bloginfo('siteurl'); ?>/?p_action=user_profile&post_author=<?php echo $post->post_author; ?>">
            		<?php _e('More print jobs by this client...','ProjectTheme'); ?>
            	</a>
            	<br/>
            <?php } ?>

               

                		

			</ul>

   		</p>

   </li>

       

      

    

    

	<li class="widget-container widget_text" id="ad-other-details">

		<h3 class="widget-title"><?php _e("Share on...",'ProjectTheme'); ?></h3>

		<p>

        

        <div class="add-this">

						<!-- AddThis Button BEGIN -->

							<div class="addthis_toolbox addthis_default_style addthis_32x32_style">

							<a class="addthis_button_linkedin"></a>
							<a class="addthis_button_facebook"></a>

							<a class="addthis_button_twitter"></a>

							<a class="addthis_button_email"></a>


							<a class="addthis_counter addthis_bubble_style"></a>

							</div>

							<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4df68b4a2795dcd9"></script>

							<!-- AddThis Button END -->

						</div>	

        

   		</p>

   </li>

       

    

	

        		

                

				

                

                

				

                

				

			</ul>

			<?php

				

				if(ProjectTheme_is_owner_of_post())

				{

					if(empty($winner))
						{

				?>

				

			<a href="<?php echo get_bloginfo('siteurl'); ?>/?p_action=edit_project&pid=<?php the_ID(); ?>" class="nice_link"><?php _e("Edit",'ProjectTheme'); ?></a> 

			<!--<a href="<?php echo get_bloginfo('siteurl'); ?>/?p_action=repost_project&pid=<?php the_ID(); ?>" class="nice_link"><?php _e("Repost",'ProjectTheme'); ?></a> -->

			<a href="<?php echo get_bloginfo('siteurl'); ?>/?p_action=delete_project&pid=<?php the_ID(); ?>" class="nice_link"><?php _e("Delete",'ProjectTheme'); ?></a> 

			

			<?php }} else {?>

			

			<a href="#" id="report-this-link" class="nice_link"><?php _e("Report this Print Job",'ProjectTheme'); ?></a>

            <!--- <a href="<?php

            $post = get_post(get_the_ID());

			

			

			echo ProjectTheme_get_priv_mess_page_url('send', '', '&uid='.$post->post_author.'&pid='.get_the_ID());

			

			?>" class="nice_link"><?php _e("Contact Seller",'ProjectTheme'); ?></a> --->

				

                <?php } ?>

		</p>

	</li>

	

	

	<?php

	

						dynamic_sidebar( 'project-widget-area' );

	echo '</ul>';

	echo '</div>';





//===============================================================================================



	get_footer();

?>
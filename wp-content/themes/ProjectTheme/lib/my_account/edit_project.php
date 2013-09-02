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
	$pid 	=  $wp_query->query_vars['pid'];
	
	function ProjectTheme_filter_ttl($title){return __("Edit Quote Request",'ProjectTheme')." - ";}
	add_filter( 'wp_title', 'ProjectTheme_filter_ttl', 10, 3 );	
	
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }   
	   
	
	get_currentuserinfo;   

	$post = get_post($pid);

	$uid 	= $current_user->ID;
	$title 	= $post->post_title;
	$cid 	= $current_user->ID;
	
	if($uid != $post->post_author) { echo 'Not your post. Sorry!'; exit; }
	
	//=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	
			global $wpdb,$wp_rewrite,$wp_query;
		

		$post = get_post($pid);
		
		if(isset($_POST['save-project']))
		{
			$project_tags 			= trim($_POST['project_tags']);
			wp_set_post_tags( $pid, $project_tags);
			
			//--------------------------------
			
			for($i=0;$i<count($_POST['custom_field_id']);$i++)
			{
				$id = $_POST['custom_field_id'][$i];
				$valval = $_POST['custom_field_value_'.$id];		
				
				if(is_array($valval))
				{
					delete_post_meta($pid, 'custom_field_ID_'.$id);
					
					for($k=0;$k<count($valval);$k++)
						add_post_meta($pid, 'custom_field_ID_'.$id, $valval[$k]);
				}
				else
				update_post_meta($pid, 'custom_field_ID_'.$id, $valval);
			}
			
			//------------------------------
			$end 					= trim($_POST['ending']); 
			
			if(empty($end)) $ending = current_time('timestamp',0) + 30*3600*24;
			else $ending = strtotime($end, current_time('timestamp',0));
			
			$projectTheme_project_period = get_option('projectTheme_project_period');
			if(!empty($projectTheme_project_period)) 
			{
				$ending1 = current_time('timestamp',0) + $projectTheme_project_period*3600*24;
				if($ending > $ending1) $ending = $ending1;
				if($ending < current_time('timestamp',0)) $ending = $ending1;
			}
			
			if($ending < current_time('timestamp',0)) $ending = current_time('timestamp',0) + 30*3600*24;
			
			
			$finalised_posted = get_post_meta(get_the_ID(),'finalised_posted',true);
			if($finalised_posted != "1")
			update_post_meta($pid, "ending", 		$ending); // ending date for the project
			
			//------------------------------
			
			$project_title = trim($_POST['project_title']);
			$project_description = trim(nl2br(strip_tags($_POST['project_description'])));
				
			  $features_not_paid = array();
			  $ProjectTheme_get_images_cost_extra = ProjectTheme_get_images_cost_extra($pid);
			  
			  if(!empty($_POST['project_location_addr'])) 
			  update_post_meta($pid, "Location", $_POST['project_location_addr']);
			 
			 
			 if(!empty($_POST['budgets'])) 
			 update_post_meta($pid, "budgets", $_POST['budgets']);
			  
			 update_post_meta($pid, "price", 		ProjectTheme_get_budget_name_string_fromID($_POST['budgets'])); // set project price 
			
			  $my_post = array();
			  $my_post['ID'] = $pid;
			  $my_post['post_content'] = $project_description;
			  $my_post['post_title']   = $project_title;
	
			  wp_update_post( $my_post );
				
				$term = get_term( $_POST['project_cat_cat'], 'project_cat' );	
				$project_cat = $term->slug;
				
				$term = get_term( $_POST['project_location_cat'], 'project_location' );	
				$project_location = $term->slug;
				
			wp_set_object_terms($pid, array($project_cat),'project_cat');
			wp_set_object_terms($pid, array($project_location),'project_location');
			
			
			$not_OK_to_just_publish = 2;
			
			//-----------------------------------
			// see if the project was featured
			
			if(isset($_POST['featured'])) update_post_meta($pid, "featured", "1");
			else update_post_meta($pid, "featured", "0");
			
			//-----------------------------------
			// mark the project for private bids if selected
			
			if(isset($_POST['private_bids'])) update_post_meta($pid, "private_bids", "1");
			else update_post_meta($pid, "private_bids", "0");
			
			
			// mark the project hidden from search engines or people not logged in
			
			if(isset($_POST['hide_project'])) update_post_meta($pid, "hide_project", "1");
			else update_post_meta($pid, "hide_project", "0");
			
			$features_not_paid = array();
			
			
			//-------------------------------------------------------------
			
			$base_fee_paid 	= get_post_meta($pid, 'base_fee_paid', true);
			$base_fee 		= get_option('projectTheme_base_fee');
			
			//--------------------------------------------
			
			$catid = ProjectTheme_get_project_primary_cat($pid);
			
			$custom_set = get_option('projectTheme_enable_custom_posting');
			if($custom_set == 'yes')
			{
				$base_fee = get_option('projectTheme_theme_custom_cat_'.$catid);
				if(empty($base_fee)) $base_fee = 0;		
			}
			
			//--------------------------------------------
			$payment_arr = array();
		
			if($base_fee_paid != "1" && $base_fee > 0)
			{
				$new_feature_arr = array();
				$new_feature_arr[0] = __('Cost for base fee','ProjectTheme');
				$new_feature_arr[1] = $base_fee;	
				array_push($features_not_paid, $new_feature_arr);
				
				$my_small_arr = array();
				$my_small_arr['fee_code'] 		= 'base_fee';
				$my_small_arr['show_me'] 		= true;
				$my_small_arr['amount'] 		= $base_fee;
				$my_small_arr['description'] 	= __('Base Fee','ProjectTheme');
				array_push($payment_arr, $my_small_arr);
			}
			
			
			$my_small_arr = array();
			$my_small_arr['fee_code'] 		= 'extra_img';
			$my_small_arr['show_me'] 		= true;
			$my_small_arr['amount'] 		= $ProjectTheme_get_images_cost_extra;
			$my_small_arr['description'] 	= __('Extra Images Fee','ProjectTheme');
			array_push($payment_arr, $my_small_arr);
				
			
			//-------- Featured Project Check --------------------------
			
			$featured 		= get_post_meta($pid, 'featured', true);
			$featured_paid 	= get_post_meta($pid, 'featured_paid', true);
			$feat_charge 	= get_option('projectTheme_featured_fee');
			
			if($featured == "1" && $featured_paid != "1" && $feat_charge > 0)
			{
				$not_OK_to_just_publish = 1;
				
				$new_feature_arr = array();
				$new_feature_arr[0] = __('Cost to make project featured','ProjectTheme');
				$new_feature_arr[1] = $feat_charge;	
				array_push($features_not_paid, $new_feature_arr);
				
				$my_small_arr = array();
				$my_small_arr['fee_code'] 		= 'feat_fee';
				$my_small_arr['show_me'] 		= true;
				$my_small_arr['amount'] 		= $feat_charge;
				$my_small_arr['description'] 	= __('Featured Fee','ProjectTheme');
				array_push($payment_arr, $my_small_arr);
			}
			
			//---------- Private Bids Check -----------------------------
			
			$private_bids 		= get_post_meta($pid, 'private_bids', true);
			$private_bids_paid 	= get_post_meta($pid, 'private_bids_paid', true);
			
			$projectTheme_sealed_bidding_fee = get_option('projectTheme_sealed_bidding_fee');
			if(!empty($projectTheme_sealed_bidding_fee))
			{
				$opt = get_post_meta($pid,'private_bids',true);
				if($opt == "0") $projectTheme_sealed_bidding_fee = 0;
			}
			
			
			if($private_bids == "1" && $private_bids_paid != "1" && $projectTheme_sealed_bidding_fee > 0)
			{
				$not_OK_to_just_publish = 1;	
				
				$new_feature_arr = array();
				$new_feature_arr[0] = __('Cost to add sealed bidding','ProjectTheme');
				$new_feature_arr[1] = $projectTheme_sealed_bidding_fee;	
				array_push($features_not_paid, $new_feature_arr);
				
				$my_small_arr = array();
				$my_small_arr['fee_code'] 		= 'sealed_project';
				$my_small_arr['show_me'] 		= true;
				$my_small_arr['amount'] 		= $projectTheme_sealed_bidding_fee;
				$my_small_arr['description'] 	= __('Sealed Bidding Fee','ProjectTheme');
				array_push($payment_arr, $my_small_arr);
			}
			
			
			//---------- Hide Project Check -----------------------------
			
			$hide_project 		= get_post_meta($pid, 'hide_project', true);
			$hide_project_paid 	= get_post_meta($pid, 'hide_project_paid', true);
			
			$projectTheme_hide_project_fee = get_option('projectTheme_hide_project_fee');
			if(!empty($projectTheme_hide_project_fee))
			{
				$opt = get_post_meta($pid,'hide_project',true);
				if($opt == "0") $projectTheme_hide_project_fee = 0;
			}
			
			
			if($hide_project == "1" && $hide_project_paid != "1" && $projectTheme_hide_project_fee > 0)
			{
				$not_OK_to_just_publish = 1;
				
				$new_feature_arr = array();
				$new_feature_arr[0] = __('Cost to hide project from search engines','ProjectTheme');
				$new_feature_arr[1] = $projectTheme_hide_project_fee;	
				array_push($features_not_paid, $new_feature_arr);
				
				$my_small_arr = array();
				$my_small_arr['fee_code'] 		= 'hide_project';
				$my_small_arr['show_me'] 		= true;
				$my_small_arr['amount'] 		= $projectTheme_hide_project_fee;
				$my_small_arr['description'] 	= __('Hide Project From Search Engines Fee','ProjectTheme');
				array_push($payment_arr, $my_small_arr);
			}
			
			$ProjectTheme_get_images_cost_extra = ProjectTheme_get_images_cost_extra($pid);
			
			
			if($ProjectTheme_get_images_cost_extra > 0)
			{
				$not_OK_to_just_publish = 1;
				
				$new_feature_arr = array();
				$new_feature_arr[0] = __('Extra images cost','ProjectTheme');
				$new_feature_arr[1] = $ProjectTheme_get_images_cost_extra;	
				array_push($features_not_paid, $new_feature_arr);	
			}
			
			$payment_arr = apply_filters('ProjectTheme_filter_payment_array', $payment_arr, $pid);
			
			$my_total = 0;
			if(count($payment_arr) > 0)
			foreach($payment_arr as $payment_item):
				if($payment_item['amount'] > 0):
					$my_total += $payment_item['amount'];
				endif;
			endforeach;			
			
		
			
			$my_total = apply_filters('ProjectTheme_filter_payment_total', $my_total, $pid);
			
			//---------------------
			
			$projectTheme_admin_approves_each_project = get_option('projectTheme_admin_approves_each_project');
			if($my_total > 0) $not_OK_to_just_publish = 1;
			
			if($not_OK_to_just_publish == 1 or $projectTheme_admin_approves_each_project == "yes")
			{
					
				$my_post = array();
				$my_post['ID'] = $pid;
				$my_post['post_status'] = 'draft';
			
				wp_update_post( $my_post );
				
			}
			else
			{
				
				$my_post = array();
				$my_post['ID'] = $pid;
				$my_post['post_status'] = 'publish';

				wp_update_post( $my_post );
				
			}
		}
		
		
		

		
		$cid = $uid;
	

//-------------------------------------

	get_header();
?>


	<div id="content" >
        	
            <div class="my_box3">
            	<div class="padd10">
            
            	<div class="box_title"><?php _e("Edit Project", "ProjectTheme"); ?></div>
                <div class="box_content"> 
            	
                
                 <!-- ########################################### -->
                <?php
				
				$projectTheme_admin_approves_each_project = get_option('projectTheme_admin_approves_each_project');
				
				if($not_OK_to_just_publish == 2) //ok published
				{
					$finalised_posted = get_post_meta($pid,'finalised_posted',true);
					if($finalised_posted == "1") $sk_step = 3; else $sk_step = "1";
					 
               		$finalised_posted = apply_filters('ProjectTheme_edit_prj_finalised_posted', $finalised_posted);
                  
						if($projectTheme_admin_approves_each_project == "yes"):
							
							if($finalised_posted != 1)
							{
								echo '<div class="stuff-paid-ok"><div class="padd10">';
								echo sprintf(__('Your project has been updated but is not live. To make your project live you must <b><a href="%s">publish</a></b> it first.','ProjectTheme'),
								 ProjectTheme_post_new_with_pid_stuff_thg($pid,1));
								echo '</div></div>';							
							}
							else
							{
								echo '<div class="stuff-paid-ok"><div class="padd10">';
								echo sprintf(__('Your project has been updated and but is not live. The admin must approve it before it goes live.','ProjectTheme'));
								echo '</div></div>';	
							}
						
						else:
							if($finalised_posted != 1)
							{
								echo '<div class="stuff-paid-ok"><div class="padd10">';
								echo sprintf(__('Your project has been updated but is not live. To make your project live you must <b><a href="%s">publish</a></b> it first.','ProjectTheme'),
								 ProjectTheme_post_new_with_pid_stuff_thg($pid,1));
								echo '</div></div>';
							
							}
							else
							{
								
								echo '<div class="stuff-paid-ok"><div class="padd10">';
								echo sprintf(__('Your project has been updated and is live now. <a href="%s"><strong>Click here</strong></a> to see your project.','ProjectTheme'), get_permalink($pid));
								echo '</div></div>';	
							
							}
							
						endif;
					 
				}
				
				elseif($not_OK_to_just_publish == 2) //ok published
				{
					echo '<div class="stuff-paid-ok"><div class="padd10">';
					echo sprintf(__('Your project has been updated and is live now. <a href="%s"><strong>Click here</strong></a> to see your project.','ProjectTheme'), get_permalink($pid));
					echo '</div></div>';	
				}
				
				elseif($not_OK_to_just_publish == 1)
				{
						$finalised_posted = get_post_meta($pid,'finalised_posted',true);
						if($finalised_posted == "1") $sk_step = 3; else $sk_step = "1";
						//echo $finalised_posted;
						
						$finalised_posted = apply_filters('ProjectTheme_edit_prj_finalised_posted_2', $finalised_posted, $my_total);
						 
						if($finalised_posted != 1)
						{
						
							echo '<div class="stuff-paid-ok"><div class="padd10">';
								echo sprintf(__('Your project has been updated but is not live. To make your project live you must <b><a href="%s">publish</a></b> it first.','ProjectTheme'),
								 ProjectTheme_post_new_with_pid_stuff_thg($pid,1));
								echo '</div></div>';
					 		
						}
						else
						{
							
						echo '<div class="stuff-not-paid"><div class="padd10">';
						echo __('You have added extra options to your project. In order to publish your project you need to pay for the options.','ProjectTheme');
						echo '<br/><br/><table width="100%">';
						
						$ttl = 0;
						
						foreach($payment_arr as $payment_item):
							
							$feature_cost 			= $payment_item['amount'];
							$feature_description 	= $payment_item['description'];
							
							
							echo '<tr>';
							echo '<td width="320">'.$feature_description.'</td>';
							echo '<td>'.projectTheme_get_show_price($feature_cost,2).'</td>';
							echo '</tr>';
							
						endforeach;
						
							echo '<tr>';
							echo '<td width="320"><b>'.__('Total','ProjectTheme').'</b></td>';
							echo '<td>'.projectTheme_get_show_price($my_total,2).'</td>';
							echo '</tr>';
						
						
							echo '<tr>';
							echo '<td><strong>'.__('Your Total Credits','ProjectTheme').'</strong></td>';
							echo '<td><strong>'.ProjectTheme_get_show_price(ProjectTheme_get_credits($uid),2).'</strong></td>';
							echo '</tr>';
							
						echo '</table><br/><br/>';
						
						
						echo '<a href="'.get_bloginfo('siteurl').'/?p_action=credits_listing&pid='.$pid.'" class="edit_project_pay_cls">'.__('Pay by Credits','ProjectTheme').'</a>';
						
						global $project_ID;
						$project_ID = $pid;
						
						//-------------------
						
						$ProjectTheme_paypal_enable 		= get_option('ProjectTheme_paypal_enable');
						$ProjectTheme_alertpay_enable 		= get_option('ProjectTheme_alertpay_enable');
						$ProjectTheme_moneybookers_enable 	= get_option('ProjectTheme_moneybookers_enable');
						
						if($ProjectTheme_paypal_enable == "yes")
							echo '<a href="'.get_bloginfo('siteurl').'/?p_action=paypal_listing&pid='.$pid.'" class="edit_project_pay_cls">'.__('Pay by PayPal','ProjectTheme').'</a>';
						
						if($ProjectTheme_moneybookers_enable == "yes")
							echo '<a href="'.get_bloginfo('siteurl').'/?p_action=mb_listing&pid='.$pid.'" class="edit_project_pay_cls">'.__('Pay by MoneyBookers/Skrill','ProjectTheme').'</a>';
						
						if($ProjectTheme_alertpay_enable == "yes")
							echo '<a href="'.get_bloginfo('siteurl').'/?p_action=payza_listing&pid='.$pid.'" class="edit_project_pay_cls">'.__('Pay by Payza','ProjectTheme').'</a>';

						do_action('ProjectTheme_add_payment_options_to_edit_project', $pid);
						
						echo '</div></div>';
						
						}
				}
					
				
				
				
				?>
                
                <!-- ########################################## -->
<!-- ##################################################################### -->

<?php
	
	$ProjectTheme_enable_images_in_projects_filter = true;
	$ProjectTheme_enable_images_in_projects_filter = apply_filters('ProjectTheme_enable_images_in_projects_filter', $ProjectTheme_enable_images_in_projects_filter);
	
	if($ProjectTheme_enable_images_in_projects_filter == true):
	
	
	$ProjectTheme_enable_images_in_projects = get_option('ProjectTheme_enable_images_in_projects');
				if($ProjectTheme_enable_images_in_projects == "yes"):
				
				?>
    <form id="fileupload" action="<?php bloginfo('siteurl'); ?>/?uploady_thing=1&pid=<?php echo $pid; ?>" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="pid" value="<?php echo $pid; ?>">
    <input type="hidden" name="cid" value="<?php echo $cid; ?>">
        <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
        <div class="row fileupload-buttonbar">
            <div class="span7">
                <!-- The fileinput-button span is used to style the file input field as button -->
                <span class="btn btn-success fileinput-button">
                    <i class="icon-plus icon-white"></i>
                    <span><?php _e('Add Images','ProjectTheme'); ?></span>
                    <input type="file" name="files[]" multiple>
                </span>
             
                <button type="reset" class="btn btn-warning cancel">
                    <i class="icon-ban-circle icon-white"></i>
                    <span><?php _e('Cancel upload','ProjectTheme'); ?></span>
                </button>
                <button type="button" class="btn btn-danger delete">
                    <i class="icon-trash icon-white"></i>
                    <span><?php _e('Delete','ProjectTheme'); ?></span>
                </button>
                <input type="checkbox" class="toggle">
            </div>
            <!-- The global progress information -->
            <div class="span5 fileupload-progress fade">
                <!-- The global progress bar -->
                <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                    <div class="bar" style="width:0%;"></div>
                </div>
                <!-- The extended global progress information -->
                <div class="progress-extended">&nbsp;</div>
            </div>
        </div>
        <!-- The loading indicator is shown during file processing -->
        <div class="fileupload-loading"></div>
        <br>
        <!-- The table listing the files available for upload/download -->
        <table role="presentation" class="table table-striped"><tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody></table>
    </form>



<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td class="preview"><span class="fade"></span></td>
        <td class="name"><span>{%=file.name%}</span></td>
        <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
        {% if (file.error) { %}
            <td class="error" colspan="2"><span class="label label-important">{%=locale.fileupload.error%}</span> {%=locale.fileupload.errors[file.error] || file.error%}</td>
        {% } else if (o.files.valid && !i) { %}
            <td>
                <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="bar" style="width:0%;"></div></div>
            </td>
            <td class="start">{% if (!o.options.autoUpload) { %}
                <button class="btn btn-primary">
                    <i class="icon-upload icon-white"></i>
                    <span>{%=locale.fileupload.start%}</span>
                </button>
            {% } %}</td>
        {% } else { %}
            <td colspan="2"></td>
        {% } %}
        <td class="cancel">{% if (!i) { %}
            <button class="btn btn-warning">
                <i class="icon-ban-circle icon-white"></i>
                <span>{%=locale.fileupload.cancel%}</span>
            </button>
        {% } %}</td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download">
        {% if (file.error) { %}
            <td></td>
            <td class="name"><span>{%=file.name%}</span></td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td class="error" colspan="2"><span class="label label-important">{%=locale.fileupload.error%}</span> {%=locale.fileupload.errors[file.error] || file.error%}</td>
        {% } else { %}
            <td class="preview">{% if (file.thumbnail_url) { %}
                <a href="{%=file.url%}" title="{%=file.name%}" rel="gallery" download="{%=file.name%}"><img src="{%=file.thumbnail_url%}"></a>
            {% } %}</td>
            <td class="name">
                <a href="{%=file.url%}" title="{%=file.name%}" rel="{%=file.thumbnail_url&&'gallery'%}" download="{%=file.name%}">{%=file.name%}</a>
            </td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td colspan="2"></td>
        {% } %}
        <td class="delete">
            <button class="btn btn-danger" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}">
                <i class="icon-trash icon-white"></i>
                <span>{%=locale.fileupload.destroy%}</span>
            </button>
            <input type="checkbox" name="delete" value="1">
        </td>
    </tr>
{% } %}
</script>
<script> var $ = jQuery; </script>



<?php endif; endif; ?>

<!-- ####################################################################### -->  

                
                <div class="clear10"></div>
                 	<form method="post"> 
                    <?php
					
	$post 		  = get_post($pid);
	$location 	  = wp_get_object_terms($pid, 'project_location');
	$cat 		  = wp_get_object_terms($pid, 'project_cat');

	$bids_number  = projectTheme_number_of_bid($pid);
				
					?>
            
        <div class="clear10"></div>         
    <ul class="post-new3">
            <li>
        	<h2><?php echo __('Your print job title','ProjectTheme'); ?>:</h2>
        	<p><input type="text" size="50" class="do_input" name="project_title" 
            value="<?php echo (empty($_POST['project_title']) ? 
			($post->post_title == "draft project" ? "" : $post->post_title) : $_POST['project_title']); ?>" /></p>
        </li>
        
        <li>
        	<h2><?php echo __('Category','ProjectTheme'); ?>:</h2>
        	<p><?php	echo ProjectTheme_get_categories("project_cat",  
			!isset($_POST['project_cat_cat']) ? (is_array($cat) ? $cat[0]->term_id : "") : $_POST['project_cat_cat']
			, __("Select Category","ProjectTheme"), "do_input"); ?></p>
        </li>
        
        <?php
		
			$finalised_posted = get_post_meta($pid,'finalised_posted',true);
			if($finalised_posted != "1"):
		?>
        
        
         <li>
        <h2>
        
        
  
		<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js"></script>

        <script src="http://ajax.aspnetcdn.com/ajax/jquery.templates/beta1/jquery.tmpl.min.js"></script>
        <script src="<?php echo get_bloginfo('template_url'); ?>/js/jquery.iframe-transport.js"></script>
        <script src="<?php echo get_bloginfo('template_url'); ?>/js/jquery.fileupload.js"></script>
        <script src="<?php echo get_bloginfo('template_url'); ?>/js/jquery.fileupload-ui.js"></script>
        <script src="<?php echo get_bloginfo('template_url'); ?>/js/application.js"></script>  	
        	
        
        <link rel="stylesheet" media="all" type="text/css" href="<?php echo get_bloginfo('template_url'); ?>/css/ui_thing.css" />
		<script type="text/javascript" language="javascript" src="<?php echo get_bloginfo('template_url'); ?>/js/timepicker.js"></script>
          
		<?php
	   
	   $dt = get_post_meta($pid,'ending',true);
	   
	   if(!empty($dt))
	   $dt = date_i18n('d-m-Y H:i',$dt);
	   
	   ?>
        
       <?php _e("Quotes due by",'ProjectTheme'); ?>:</h2>
	   <p><input type="text" name="ending" id="ending" class="do_input" value="<?php echo $dt; ?>"  /><br>
 <strong>NOTE:</strong> We recommend giving printers at least 3 business days.</p>
       </li>
        
 		<script>
		<?php
		
			$dd = get_option('projectTheme_project_period');
			if(empty($dd)) $dd = 7;
		
		?>
		
			var myDate=new Date();
			myDate.setDate(myDate.getDate()+<?php echo $dd; ?>);
			
			$(document).ready(function() {
				 $('#ending').datetimepicker({
				showSecond: false,
				timeFormat: 'hh:mm:ss',
				
					currentText: '<?php _e('Now','ProjectTheme'); ?>',
					closeText: '<?php _e('Done','ProjectTheme'); ?>',
					ampm: false,
					timeFormat: 'hh:mm tt',
					timeSuffix: '',
					maxDateTime: myDate,
					timeOnlyTitle: '<?php _e('Choose Time','ProjectTheme'); ?>',
					timeText: '<?php _e('Time','ProjectTheme'); ?>',
					hourText: '<?php _e('Hour','ProjectTheme'); ?>',
					minuteText: '<?php _e('Minute','ProjectTheme'); ?>',
					secondText: '<?php _e('Second','ProjectTheme'); ?>',
					timezoneText: '<?php _e('Time Zone','ProjectTheme'); ?>'
			
			});});
 
 		</script>
        
        
        <?php  endif;  

	$cid = $current_user->ID;
	$cwd = str_replace('wp-admin','',getcwd());
	$cwd .= 'wp-content/uploads';

	//echo get_template_directory();

?>
<?php
						   
						   	$ProjectTheme_enable_project_files = get_option('ProjectTheme_enable_project_files');						   
						   	if($ProjectTheme_enable_project_files != "no"):
						   
						   ?>

		<li>
        <h2><?php _e("Files",'ProjectTheme'); ?>:</h2>
        <p>

	
    <script type="text/javascript">
	
	function delete_this(id)
	{
		 $.ajax({
						method: 'get',
						url : '<?php echo get_bloginfo('siteurl');?>/index.php/?_ad_delete_pid='+id,
						dataType : 'text',
						success: function (text) {   $('#image_ss'+id).remove();  }
					 });
		  //alert("a");
	
	}

	
	
	$(function() {
		
		$("#fileUpload3").uploadify({
			height        : 30,
			auto:			true,
			swf           : '<?php echo get_bloginfo('template_url'); ?>/lib/uploadify/uploadify.swf',
			uploader      : '<?php echo get_bloginfo('template_url'); ?>/lib/uploadify/uploady2.php',
			width         : 120,
			fileTypeExts  : '*.zip;*.pdf;*.doc;*.docx',
			fileTypeDesc : '<?php _e('Select Project Files','ProjectTheme'); ?>',
			formData    : {'ID':<?php echo $pid; ?>,'author':<?php echo $cid; ?>},
			onUploadSuccess : function(file, data, response) {
			
			//alert(data);
			var bar = data.split("|");
			
$('#thumbnails').append('<div class="div_div" id="image_ss'+bar[1]+'" > ' + bar[0] + '" <a href="javascript: void(0)" onclick="delete_this('+ bar[1] +')"><img border="0" src="<?php echo get_bloginfo('template_url'); ?>/images/delete_icon.png" border="0" /></a></div>');
}
	
			
			
    	});
		
		
	});
	
	
	</script>
	
    <style type="text/css">
	.div_div
	{
		margin-left:5px; float:left; 
		
		margin-top:10px;
	}
	
	</style>
    



	<div id="fileUpload3"><?php _e('You have a problem with your javascript','ProjectTheme'); ?></div>
	<div id="thumbnails" style="overflow:hidden;margin-top:20px">
    
    <?php


	$args = array(
	'order'          => 'ASC',
	'orderby'        => 'menu_order',
	'post_type'      => 'attachment',
	'post_parent'    => $pid,
	'post_status'    => null,
	'numberposts'    => -1,
	);
	$attachments = get_posts($args);
	if ($attachments) {
	    foreach ($attachments as $attachment) {
		$url = $attachment->guid;
		$imggg = $attachment->post_mime_type; 
		
		if('image/png' != $imggg && 'image/jpeg' != $imggg)
			echo '<div class="div_div"  id="image_ss'.$attachment->ID.'">'.$attachment->post_title.'
			<a href="javascript: void(0)" onclick="delete_this(\''.$attachment->ID.'\')"><img border="0" src="'.get_bloginfo('template_url').'/images/delete_icon.png" /></a>
			</div>';
	  
	}
	}


	?>
    
    </div>
    
    <!--####################################### -->
	</p>
	</li>

    <?php endif; ?>    
        
        
        <?php if($bids_number == 0): ?>
        
        <!--- <li>
        	<h2><?php echo __('Price Range','ProjectTheme'); ?>:</h2>
        <p>
         <?php
	  
	  $sel = get_post_meta($pid, 'budgets', true);
	  echo ProjecTheme_get_budgets_dropdown($sel, 'do_input');
	  
	  ?>
        </p>
        </li> --->
        

        <?php endif; ?>
        
        <?php
		
			$ProjectTheme_enable_project_location = get_option('ProjectTheme_enable_project_location');
			if($ProjectTheme_enable_project_location == "yes"):
		
		?>
        
        <li>
        	<h2><?php echo __('Location','ProjectTheme'); ?>:</h2>
        <p><?php	echo ProjectTheme_get_categories("project_location", 
		empty($_POST['project_location_cat']) ? (is_array($location) ? $location[0]->term_id : "") : $_POST['project_location_cat'], __("Select Location","ProjectTheme"), "do_input"); ?></p>
        </li>
        
        <li>
        	<h2><?php echo __('Address','ProjectTheme'); ?>:</h2>
        <p><input type="text" size="50" class="do_input"  name="project_location_addr" value="<?php echo !isset($_POST['project_location_addr']) ? 
		get_post_meta($pid, 'Location', true) : $_POST['project_location_addr']; ?>" /> </p>
        </li>
        
        <?php endif; ?>
        
        <?php
						   
						   	$ProjectTheme_enable_featured_option = get_option('ProjectTheme_enable_featured_option');						   
						   	if($ProjectTheme_enable_featured_option != "no"):
						   
						   ?>
        
        <!--- <li>
        <h2><?php _e("Feature project?",'ProjectTheme'); ?>:</h2>
        <p><input type="checkbox" class="do_input" name="featured" value="1" 
		<?php $feature = get_post_meta($pid, 'featured', true); echo ($feature == "1" ? "checked='checked'" : ""); ?> /> 
        <?php _e("By clicking this checkbox you mark your project as featured. Extra fee is applied.", 'ProjectTheme'); ?></p>
        </li>
        <?php endif; ?>
        
        
        <?php
						   
						   	$ProjectTheme_enable_sealed_option = get_option('ProjectTheme_enable_sealed_option');						   
						   	if($ProjectTheme_enable_sealed_option != "no"):
						   
						   ?>
        <li>
        <h2><?php _e("Sealed Bidding?",'ProjectTheme'); ?>:</h2>
        <p><input type="checkbox" class="do_input" name="private_bids" value="1"
        <?php $private_bids = get_post_meta($pid, 'private_bids', true); echo ($private_bids == "1" ? "checked='checked'" : ""); ?> /> 
        <?php _e("By clicking this checkbox you hide your project's bids. Extra fee is applied.", 'ProjectTheme'); ?></p>
        </li> --->
        <?php endif; ?>
        
        
        <?php
						   
						   	$ProjectTheme_enable_hide_option = get_option('ProjectTheme_enable_hide_option');						   
						   	if($ProjectTheme_enable_hide_option != "no"):
						   
						   ?> 
        <li>
        <h2><?php _e("Make Private",'ProjectTheme'); ?>:</h2>
        <p><input type="checkbox" class="do_input" name="hide_project" value="1" 
        <?php $hide_project = get_post_meta($pid, 'hide_project', true); echo ($hide_project == "1" ? "checked='checked'" : ""); ?>/> 
        <?php _e("By clicking this checkbox your print job can only be seen by printers and you.", 'ProjectTheme'); ?></p>
        </li>
        <?php endif; ?>
        
        
        
        
        
        
        <li>
        	<h2><?php echo __('Description','ProjectTheme'); ?>:</h2>
        <p><textarea rows="6" cols="60" class="do_input description_edit"  name="project_description"><?php 
		echo empty($_POST['project_description']) ? trim($post->post_content) : $_POST['project_description']; ?></textarea></p>
        </li>


	<?php
		$cat 		  	= wp_get_object_terms($pid, 'project_cat');
		$catidarr 		= $cat[0]->term_id;
	
	
		$arr 	= ProjectTheme_get_project_category_fields($catidarr, $pid);
		
		for($i=0;$i<count($arr);$i++)
		{
			
			        echo '<li>';
					echo '<h2>'.$arr[$i]['field_name'].$arr[$i]['id'].':</h2>';
					echo '<p>'.$arr[$i]['value'];
					do_action('ProjectTheme_step3_after_custom_field_'.$arr[$i]['id'].'_field');
					echo '</p>';
					echo '</li>';
			
			
		}	
	
	?> 
    
	 	
         <?php do_action('ProjectTheme_step1_before_tags'); 
		$project_tags = '';
		$t = wp_get_post_tags($post->ID);
		foreach($t as $tags)
		{
			$project_tags .= $tags->name . ", ";		
		}
		
		
		?>
		<!--- <li>
        	<h2><?php echo __('Tags', 'ProjectTheme'); ?>:</h2>
        <p><input type="text" size="50" class="do_input"  name="project_tags" value="<?php echo $project_tags; ?>" /> </p>
        </li>
        
        <li>--->
        <h2>&nbsp;</h2>
        <p><input type="submit" name="save-project" value="<?php _e("Save Project",'ProjectTheme'); ?>" /></p>
        </li> 
    


		</ul>
          </form>     
                
                
                </div>
                </div>
                </div>
                </div>
                
	<?php ProjectTheme_get_users_links(); ?>

<?php get_footer(); ?>
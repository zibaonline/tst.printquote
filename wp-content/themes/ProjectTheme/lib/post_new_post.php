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

global $projectOK, $MYerror;
$projectOK = 0;

global $wp_query;
$pid = $wp_query->query_vars['projectid'];	

//************************ STEP 1 SUBMIT *********************************

do_action('ProjectTheme_post_new_post_post',$pid);

if(isset($_POST['project_submit1']))
{
	
	
		$project_title 			= trim($_POST['project_title']);
		$project_description 	= nl2br($_POST['project_description']);
		$project_category 		= $_POST['project_cat_cat'];
		$project_location 		= trim($_POST['project_location_cat']);
		$project_tags 			= trim($_POST['project_tags']);
		$price 					= trim($_POST['price']);
		$project_location_addr 	= trim($_POST['project_location_addr']);
		$end 					= trim($_POST['ending']); 
		
		update_post_meta($pid, 'finalised_posted', '0');
		
		//--------------------------------
		
		$projectOK = 1;
		
		if(empty($project_title)) 		{ $projectOK = 0; $MYerror['title'] 		= __('You cannot leave the print job title blank!','ProjectTheme'); }
		if(empty($project_category)) { $projectOK = 0; $MYerror['cate'] 	= __('You cannot leave the category blank!','ProjectTheme'); }
		if(empty($project_description)) { $projectOK = 0; $MYerror['description'] 	= __('You cannot leave the description blank!','ProjectTheme'); }

		//--------------------------------
		
		
		$project_category2 	= $project_category;
					
		$my_post = array();
	
		$my_post['post_title'] 		= $project_title;
		$my_post['post_status'] 	= 'draft';
		$my_post['ID'] 				= $pid;
		$my_post['post_content'] 	= $project_description;
			
		$term = get_term( $project_category, 'project_cat' );	
		$project_category = $term->slug;
				
		$term = get_term( $project_location, 'project_location' );	
		$project_location = $term->slug;
		
		wp_update_post( $my_post );
		wp_set_object_terms($pid, array($project_category),'project_cat');
		wp_set_object_terms($pid, array($project_location),'project_location');
  
		wp_set_post_tags( $pid, $project_tags);
		
		
		// set project details, as meta post
			  
		update_post_meta($pid, "Location", 		$project_location_addr); // set project location
		update_post_meta($pid, "price", 		ProjectTheme_get_budget_name_string_fromID($_POST['budgets'])); // set project price
		update_post_meta($pid, "paid", 			"0");
		update_post_meta($pid, "views", 		'0');
		update_post_meta($pid, "budgets", 		$_POST['budgets']);
			 
		// IF TIME NOT SELECTED THEN 30 DAYS	
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
		
		
		update_post_meta($pid, "made_me_date", 		current_time('timestamp',0));
		
		update_post_meta($pid, "closed", 		"0");
		update_post_meta($pid, "closed_date", 	"0");
		update_post_meta($pid, "ending", 		$ending); // ending date for the project
		
		do_action('ProjectTheme_execute_on_submit_1', $pid);
		
		do_action('ProjectTheme_step1_submit');
		if($projectOK == 1) //if everything ok, go to next step
		{		
			$stp = 2;
			$stp = apply_filters('ProjectTheme_redirect_after_submit_step1',$stp);
			
			wp_redirect(ProjectTheme_post_new_with_pid_stuff_thg($pid, $stp)); //projectTheme_post_new_link().'/step/2/?when_posting=1&post_new_project_id='.$pid);
			exit;	
		}
	
	
	
}


//************************ STEP 2 SUBMIT *********************************
	
	if(isset($_POST['project_submit2']))
	{	

		//------ custo fields --------------
		
		global $wpdb, $projectOK, $MYerror;
		$projectOK = 1;
		
		$arr = $_POST['custom_field_id'];
		for($i=0;$i<count($arr);$i++)
		{
			$ids 	= $arr[$i];
			$value 	= $_POST['custom_field_value_'.$ids];
			
			$s1 = "select * from ".$wpdb->prefix."project_custom_fields where id='$ids'";
			$r1 = $wpdb->get_results($s1);
			$row1 = $r1[0];
			$mm = 0;
			
			//---------------------------
			
			if(is_array($value))
			{
				delete_post_meta($pid, "custom_field_ID_".$ids);
				$rr = 0;
				for($j=0;$j<count($value);$j++) {
					add_post_meta($pid, "custom_field_ID_".$ids, $value[$j]);
					$rr++;
				}
				
				if($rr == 0) $mm = 1;
				
			}
			else
			{
				update_post_meta($pid, "custom_field_ID_".$ids, $value);
				if(empty($value)) $mm = 1;
			}
			
			if($row1->is_mandatory == 1 and $mm == 1)
			{
				$projectOK = 0; $MYerror['custom_field_' . $ids] 		= sprintf(__('You cannot leave the field: "<b>%s</b>" blank!','ProjectTheme'), $row1->name );
			}
		 
		}
	
 
		//-----------------------------------
		// see if the project was featured
		
		if(isset($_POST['featured']))
		{
			 update_post_meta($pid, "featured", "1");
			 
			 $projectTheme_project_period_featured = get_option('projectTheme_project_period_featured');
			 if($projectTheme_project_period_featured == 0) $projectTheme_project_period_featured = 30;
			 if(!empty($projectTheme_project_period_featured) && is_numeric($projectTheme_project_period_featured)) $ending = current_time('timestamp',0) + $projectTheme_project_period_featured*3600*24;
			
			 update_post_meta($pid, "ending", 		$ending); 
		}
		else update_post_meta($pid, "featured", "0");
		
		//-----------------------------------
		// mark the project for private bids if selected
		
		if(isset($_POST['private_bids'])) update_post_meta($pid, "private_bids", "1");
		else update_post_meta($pid, "private_bids", "0");
		
		
		// mark the project hidden from search engines or people not logged in
		
		if(isset($_POST['hide_project'])) update_post_meta($pid, "hide_project", "1");
		else update_post_meta($pid, "hide_project", "0");
		
		//------------------------------------

		update_post_meta($pid, "closed", "0");
		update_post_meta($pid, "closed_date", "0");
		
		do_action('ProjectTheme_step2_submit');
		
		
		if($projectOK == 1)
		{
			wp_redirect(ProjectTheme_post_new_with_pid_stuff_thg($pid, '3'));
			exit;
		}
	}

?>
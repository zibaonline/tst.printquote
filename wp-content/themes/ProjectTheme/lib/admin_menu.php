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

//change_u_type

if(isset($_POST['change_u_type']))
{
	if(!current_user_can('level_10')) { exit; }
	
	$uid = $_POST['uid'];
	$user_tp = $_POST['user_tp'];
	
	update_user_meta($uid,'user_tp', $user_tp);							
	
	$user = new WP_User($uid);
	$user->set_role($user_tp);
			
}

if(isset($_GET['_delete_custom_id']))
{
	global $wpdb;
	$ids = $_GET['_delete_custom_id'];
	$s2 = "delete from ".$wpdb->prefix."project_custom_options where id='$ids'";
	$wpdb->query($s2);
	
	die();	
}

if(isset($_GET['update_option_ajax_ready']))
{
	$option_id 		= $_POST['option_id'];
	$option_name 	= $_POST['option_name'];
	$option_order 	= $_POST['option_order'];
	global $wpdb;
	
	$s2 = "update ".$wpdb->prefix."project_custom_options  set valval='$option_name', ordr='$option_order' where id='$option_id'";
	$wpdb->query($s2);	
	
	die();	
}

if(isset($_POST['crds']))
{
	if(!current_user_can('level_10')) { exit; }
	
	$uid = $_POST['uid'];
	if(!empty($_POST['increase_credits']))
	{
		if($_POST['increase_credits'] > 0)
		if(is_numeric($_POST['increase_credits']))
		{
			$cr = projectTheme_get_credits($uid);
			projectTheme_update_credits($uid, $cr + $_POST['increase_credits']);
			
			$reason = __('Payment received from site admin','ProjectTheme');
			projectTheme_add_history_log('1', $reason, trim($_POST['increase_credits']), $uid);
							
			
		}
	}
	else
	{
		if($_POST['decrease_credits'] > 0)
		if(is_numeric($_POST['decrease_credits']))
		{
			$cr = projectTheme_get_credits($uid);
			projectTheme_update_credits($uid, $cr - $_POST['decrease_credits']);
			
			$reason = __('Payment subtracted by site admin','ProjectTheme');
			projectTheme_add_history_log('0', $reason, trim($_POST['decrease_credits']), $uid);
		}
	
	}	
	//echo projectTheme_get_credits($uid);
	echo $sign.ProjectTheme_get_show_price(projectTheme_get_credits($uid)) ;
	exit;
}



function ProjectTheme_theme_bullet($rn = '')
{
	global $menu_admin_project_theme_bull;
	$menu_admin_project_theme_bull = '<a href="#" class="tltp_cls" title="'.$rn.'"><img src="'.get_bloginfo('template_url') . '/images/qu_icon.png" /></a>';	
	echo $menu_admin_project_theme_bull;
	
}


function ProjectTheme_set_admin_menu()
{
	 $icn = get_bloginfo('template_url')."/images/projecticon.gif";
	 //global $capability;
	 $capability = 10;
	 
add_menu_page(__('Project Theme'), __('Project Theme','ProjectTheme'), $capability,"project_theme_mnu", 'projectTheme_summary_scr', $icn, 0);
add_submenu_page("project_theme_mnu", __('Site Overview','ProjectTheme'), '<img src="'.get_bloginfo('template_url').'/images/overview_icon.png" border="0" /> '.__('Site Overview','ProjectTheme'),$capability, "project_theme_mnu", 'projectTheme_summary_scr');
add_submenu_page("project_theme_mnu", __('General Options','ProjectTheme'),'<img src="'.get_bloginfo('template_url').'/images/setup_icon.png" border="0" /> '. __('General Options','ProjectTheme'),$capability, "general-options", 'projectTheme_options');
add_submenu_page("project_theme_mnu", __('Email Settings','ProjectTheme'), '<img src="'.get_bloginfo('template_url').'/images/email_icon.png" border="0" /> '.__('Email Settings','ProjectTheme'),$capability, 'email-settings', 'projectTheme_email_settings');

add_submenu_page("project_theme_mnu", __('Pricing Settings','ProjectTheme'), '<img src="'.get_bloginfo('template_url').'/images/dollar_icon.png" border="0" /> '. __('Pricing Settings','ProjectTheme'),$capability, 'pricing-settings', 'projectTheme_pricing_options');
add_submenu_page("project_theme_mnu", __('Custom Pricing','ProjectTheme'), '<img src="'.get_bloginfo('template_url').'/images/penny_icon.png" border="0" /> '. __('Custom Pricing','ProjectTheme'),$capability, 'custom-fees', 'projectTheme_cust_prcng');

add_submenu_page("project_theme_mnu", __('Custom Fields','ProjectTheme'), '<img src="'.get_bloginfo('template_url').'/images/input_icon.png" border="0" /> '.__('Custom Fields','ProjectTheme'),$capability, 'custom-fields', 'projectTheme_custom_fields_scr');


add_submenu_page("project_theme_mnu", __('User Fields','ProjectTheme'), '<img src="'.get_bloginfo('template_url').'/images/input_icon.png" border="0" /> '.__('User Fields','ProjectTheme'),$capability, 'user-fields', 'projectTheme_custom_user_fields_scr');

add_submenu_page("project_theme_mnu", __('Images Options','ProjectTheme'), '<img src="'.get_bloginfo('template_url').'/images/image_icon.png" border="0" /> '. __('Images Options','ProjectTheme'),$capability, 'images-settings', 'projectTheme_theme_images_settings');

add_submenu_page("project_theme_mnu", __('Advertising','ProjectTheme'), '<img src="'.get_bloginfo('template_url').'/images/adv_icon.png" border="0" /> '. __('Advertising','ProjectTheme'),$capability, 'adv-settings', 'projectTheme_advertise_settings');


add_submenu_page("project_theme_mnu", __('Layout Settings','ProjectTheme'), '<img src="'.get_bloginfo('template_url').'/images/layout_icon.png" border="0" /> '. __('Layout Settings','ProjectTheme'),$capability, 'layout-settings', 'projectTheme_layout_settings');

 
add_submenu_page("project_theme_mnu", __('Payment Gateways','ProjectTheme'), '<img src="'.get_bloginfo('template_url').'/images/gateway_icon.png" border="0" /> '. __('Payment Gateways','ProjectTheme'),$capability, 'payment-methods', 'projectTheme_payment_methods');
//add_submenu_page("project_theme_mnu", __('Membership Packs','ProjectTheme'), __('Membership Packs','ProjectTheme'),$capability, 'mem-packs', 'projectTheme_membership_packs');
//add_submenu_page("project_theme_mnu", __('Discount Coupons','ProjectTheme'), __('Discount Coupons','ProjectTheme'),$capability, 'discount-coupons', 'projectTheme_discount_copuons');

//add_submenu_page("project_theme_mnu", __('Transactions','ProjectTheme'), __('Transactions','ProjectTheme'),$capability, 'paypal-trans', 'projectTheme_transactions');
add_submenu_page('project_theme_mnu', __('Withdrawals','ProjectTheme'), '<img src="'.get_bloginfo('template_url').'/images/wallet_icon.png" border="0" /> '.__('Withdrawals','ProjectTheme'),$capability, 'Withdrawals', 'projectTheme_theme_withdrawals');

add_submenu_page('project_theme_mnu', __('Escrows','ProjectTheme'), '<img src="'.get_bloginfo('template_url').'/images/vault_icon.png" border="0" /> '.__('Escrows','ProjectTheme'),$capability, 'Escrows', 
'projectTheme_theme_escrows');

add_submenu_page('project_theme_mnu', __('User Balances','ProjectTheme'), '<img src="'.get_bloginfo('template_url').'/images/bal_icon.png" border="0" /> '.__('User Balances','ProjectTheme'),'10', 'User-Balances', 'project_theme_user_balances');

add_submenu_page('project_theme_mnu', __('User Reviews','ProjectTheme'), '<img src="'.get_bloginfo('template_url').'/images/review_icon.png" border="0" /> '.__('User Reviews','ProjectTheme'),'10', 'user-reviews', 'projectTheme_reviews_scr');

add_submenu_page('project_theme_mnu', __('User Types','ProjectTheme'), '<img src="'.get_bloginfo('template_url').'/images/user_icon.png" border="0" /> '.__('User Types','ProjectTheme'),'10', 'user-types', 'projectTheme_usr_types_scr'); 

add_submenu_page('project_theme_mnu', __('Private Messages','ProjectTheme'), '<img src="'.get_bloginfo('template_url').'/images/mess_icon.png" border="0" /> '.__('Private Messages','ProjectTheme'),'10', 'private-messages', 'projectTheme_private_messages_scr');


add_submenu_page('project_theme_mnu', __('Orders','ProjectTheme'), '<img src="'.get_bloginfo('template_url').'/images/orders_icon.png" border="0" /> '.__('Orders','ProjectTheme'),'10', 'orders', 'projectTheme_orders');
do_action('ProjectTheme_admin_menu_after_orders');

add_submenu_page("project_theme_mnu", __('InSite Transactions','ProjectTheme'), '<img src="'.get_bloginfo('template_url').'/images/list_icon.png" border="0" /> '.__('InSite Transactions','ProjectTheme'),$capability, 'trans-site', 'projectTheme_hist_transact');

add_submenu_page("project_theme_mnu", __('Tracking Tools','ProjectTheme'), '<img src="'.get_bloginfo('template_url').'/images/track_icon.png" border="0" /> '.__('Tracking Tools','ProjectTheme'),$capability, 'track-tools', 'projectTheme_track_tools');

add_submenu_page("project_theme_mnu", __('Theme Info','ProjectTheme'), '<img src="'.get_bloginfo('template_url').'/images/info_icon.png" border="0" /> '.__('Theme Info','ProjectTheme'),$capability, 'info-stuff', 'projectTheme_theme_information');
 
 do_action('ProjectTheme_admin_menu_add_item');
  
}

global $menu_admin_project_theme_bull;
$menu_admin_project_theme_bull = '<img src="'.get_bloginfo('template_url') . '/images/qu_icon.png" />';

/******************************************************************
*
*	Admin Menu - New Function - sitemile[at]sitemile.com
*	developed by Andrei Saioc - andreisaioc[at]gmail.com
*
*******************************************************************/
function projectTheme_options()
{
	global $menu_admin_project_theme_bull;
	echo '<div class="wrap">';
	echo '<div class="icon32" id="icon-options-general2"><br/></div>';	
	echo '<h2 class="my_title_class_sitemile">ProjectTheme General Options</h2>';
	
	
	
	
	if(isset($_POST['my_submit']))
	{
		$projectTheme_show_project_views 			= $_POST['projectTheme_show_project_views'];
		$projectTheme_admin_approves_each_project 	= $_POST['projectTheme_admin_approves_each_project'];
		$projectTheme_logo_url 						= $_POST['projectTheme_logo_url'];
		$projectTheme_enable_blog 					= $_POST['projectTheme_enable_blog'];
		$projectTheme_project_period 				= trim($_POST['projectTheme_project_period']);
		$projectTheme_project_period_featured 		= trim($_POST['projectTheme_project_period_featured']);
		$projectTheme_projects_slug_link 			= trim($_POST['projectTheme_projects_slug_link']);
		$projectTheme_location_slug_link 			= trim($_POST['projectTheme_location_slug_link']);
		$projectTheme_category_slug_link 			= trim($_POST['projectTheme_category_slug_link']);
		$ProjectTheme_show_blue_menu				= trim($_POST['ProjectTheme_show_blue_menu']);
		$ProjectTheme_slider_in_front				= trim($_POST['ProjectTheme_slider_in_front']);
		$projectTheme_custom_CSS					= trim($_POST['projectTheme_custom_CSS']);
		$ProjectTheme_stretch_enable				= trim($_POST['ProjectTheme_stretch_enable']);
		$ProjectTheme_enable_2_user_tp				= trim($_POST['ProjectTheme_enable_2_user_tp']);
		 $ProjectTheme_enable_project_location		= $_POST['ProjectTheme_enable_project_location'];
		  $ProjectTheme_enable_project_files		= $_POST['ProjectTheme_enable_project_files'];
		  
		  $ProjectTheme_enable_featured_option		= $_POST['ProjectTheme_enable_featured_option'];
		  $ProjectTheme_enable_sealed_option		= $_POST['ProjectTheme_enable_sealed_option'];
		  $ProjectTheme_enable_hide_option			= $_POST['ProjectTheme_enable_hide_option'];
		  $ProjectTheme_moderate_private_messages			= $_POST['ProjectTheme_moderate_private_messages'];
		  $ProjectTheme_show_subcats_enbl			= $_POST['ProjectTheme_show_subcats_enbl'];
		  
		  
		  
		  update_option('ProjectTheme_enable_drop_down_menu', $_POST['ProjectTheme_enable_drop_down_menu']);
		  update_option('ProjectTheme_show_subcats_enbl', $ProjectTheme_show_subcats_enbl);
		  update_option('ProjectTheme_moderate_private_messages', $ProjectTheme_moderate_private_messages);
		 
		 	update_option('ProjectTheme_enable_hide_option', $ProjectTheme_enable_hide_option);
			update_option('ProjectTheme_enable_sealed_option', $ProjectTheme_enable_sealed_option);
			update_option('ProjectTheme_enable_featured_option', $ProjectTheme_enable_featured_option);
		 
		//--------------- 
		update_option('ProjectTheme_enable_project_files', $ProjectTheme_enable_project_files);
		update_option('ProjectTheme_enable_project_location', $ProjectTheme_enable_project_location);
		update_option('ProjectTheme_enable_2_user_tp', $ProjectTheme_enable_2_user_tp);
		update_option('ProjectTheme_stretch_enable', $ProjectTheme_stretch_enable);
		update_option('projectTheme_custom_CSS', $projectTheme_custom_CSS);
		update_option('ProjectTheme_slider_in_front', $ProjectTheme_slider_in_front);
		update_option('ProjectTheme_show_blue_menu', $ProjectTheme_show_blue_menu);
		update_option('projectTheme_category_slug_link', $projectTheme_category_slug_link);
		update_option('projectTheme_location_slug_link', $projectTheme_location_slug_link);
		update_option('projectTheme_projects_slug_link', $projectTheme_projects_slug_link);
		update_option('projectTheme_project_period_featured', $projectTheme_project_period_featured);
	
		update_option('projectTheme_project_period', $projectTheme_project_period);
		update_option('projectTheme_logo_url', $projectTheme_logo_url);
		update_option('projectTheme_enable_blog', $projectTheme_enable_blog);
		update_option('projectTheme_show_project_views', $projectTheme_show_project_views);
		update_option('projectTheme_admin_approves_each_project', $projectTheme_admin_approves_each_project);
		
		do_action('ProjectTheme_general_settings_main_details_options_save');
		
		echo '<div class="saved_thing">Settings were saved!</div>';
		//---------------

		
	} 
	

		
	if(isset($_POST['my_submit3']))
		{
			$color_for_main_slider = $_POST['color_for_main_slider'];
			$color_for_footer = $_POST['color_for_footer'];
			$color_for_top_links = $_POST['color_for_top_links'];
			$color_for_bk = $_POST['color_for_bk'];
			$color_for_hdr = $_POST['color_for_hdr'];
			
			
			update_option('color_for_bk',$color_for_bk);
			update_option('color_for_hdr',$color_for_hdr);
			update_option('color_for_main_slider',$color_for_main_slider);
			update_option('color_for_footer',$color_for_footer);
			update_option('color_for_top_links', $color_for_top_links);
			
			echo '<div class="saved_thing">Settings were saved!</div>';
		}
		
		
		if(isset($_POST['ProjectTheme_save4']))
		{
			update_option('ProjectTheme_enable_facebook_login', 	trim($_POST['ProjectTheme_enable_facebook_login']));
			update_option('ProjectTheme_facebook_app_id', 			trim($_POST['ProjectTheme_facebook_app_id']));
			update_option('ProjectTheme_facebook_app_secret', 		trim($_POST['ProjectTheme_facebook_app_secret']));

			
			echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';
		}
		
		
		if(isset($_POST['ProjectTheme_save5']))
		{
			update_option('ProjectTheme_enable_twitter_login', 			trim($_POST['ProjectTheme_enable_twitter_login']));
			update_option('ProjectTheme_twitter_consumer_key', 			trim($_POST['ProjectTheme_twitter_consumer_key']));
			update_option('ProjectTheme_twitter_consumer_secret', 		trim($_POST['ProjectTheme_twitter_consumer_secret']));

			
			echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';
		}
		
		if(isset($_POST['ProjectTheme_save7']))
		{
			update_option('ProjectTheme_filter_emails_private_messages', 				trim($_POST['ProjectTheme_filter_emails_private_messages']));
			update_option('ProjectTheme_filter_urls_private_messages', 					trim($_POST['ProjectTheme_filter_urls_private_messages']));
		
			echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';
		}
	
	$arr = array("yes" => "Yes", "no" => "No");

	global $wpdb;
	//------------
	?>
	
    
    <div id="usual2" class="usual"> 
  <ul> 
    <li><a href="#tabs1" class="selected">Main Details</a></li> 
    <li><a href="#tabs2">Facebook</a></li> 
    <li><a href="#tabs3">Twitter</a></li> 
    <li><a href="#tabs4">Filters</a></li>
  </ul> 
  <div id="tabs1" style="display: block; ">
  
  <form method="post" action="">
    	<table width="100%" class="sitemile-table">
			
        
        <?php do_action('ProjectTheme_general_settings_main_details_options'); ?>    
            
        
        <tr>
        <td valign=top width="22"><?php echo $menu_admin_project_theme_bull; ?></td>
        <td width="320">Logo URL:</td>
        <td><input type="text" name="projectTheme_logo_url" size="50" value="<?php echo  get_option('projectTheme_logo_url'); ?>"  /></td>
        </tr>
        
        
        <tr>
        <td valign=top width="22"><?php echo $menu_admin_project_theme_bull; ?></td>
        <td >Enable 2 user types:</td>
        <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_enable_2_user_tp'); ?></td>
        </tr>
        
        
        <tr>
        <td valign=top width="22"><?php echo $menu_admin_project_theme_bull; ?></td>
        <td >Enable Project Files:</td>
        <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_enable_project_files'); ?></td>
        </tr>
        
        
        <tr>
        <td valign=top width="22"><?php echo $menu_admin_project_theme_bull; ?></td>
        <td >Show Subitems on categories and locations pages:</td>
        <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_show_subcats_enbl'); ?></td>
        </tr>
        
        <tr>
        <td valign=top width="22">&nbsp;</td>
        <td >&nbsp;</td>
        <td>&nbsp;</td>
        </tr>
        
        
        <tr>
        <td valign=top width="22"><?php echo $menu_admin_project_theme_bull; ?></td>
        <td >Enable Featured Option:</td>
        <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_enable_featured_option'); ?></td>
        </tr>
        
        <tr>
        <td valign=top width="22"><?php echo $menu_admin_project_theme_bull; ?></td>
        <td >Enable Sealed Bidding Option:</td>
        <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_enable_sealed_option'); ?></td>
        </tr>
        
        
        <tr>
        <td valign=top width="22"><?php echo $menu_admin_project_theme_bull; ?></td>
        <td >Enable Hide from search engines Option:</td>
        <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_enable_hide_option'); ?></td>
        </tr>
        
        <tr>
        <td valign=top width="22">&nbsp;</td>
        <td >&nbsp;</td>
        <td>&nbsp;</td>
        </tr>
        
        <tr>
        <td valign=top width="22"><?php ProjectTheme_theme_bullet('If this option is set to yes, then the admin will be notified and will need to approve each private message sent before it is actually delivered.'); ?></td>
        <td >Moderate Private Messages:</td>
        <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_moderate_private_messages'); ?></td>
        </tr>
        
        
        <tr>
        <td valign=top width="22"><?php echo $menu_admin_project_theme_bull; ?></td>
        <td >Enable Project Location:</td>
        <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_enable_project_location'); ?></td>
        </tr>
        
        <tr>
        <td valign=top width="22"><?php echo $menu_admin_project_theme_bull; ?></td>
        <td >Show views in project page:</td>
        <td><?php echo ProjectTheme_get_option_drop_down($arr, 'projectTheme_show_project_views'); ?></td>
        </tr>
        
                
        <tr>
        <td valign=top width="22"><?php echo $menu_admin_project_theme_bull; ?></td>
        <td >Admin approves each project:</td>
        <td><?php echo ProjectTheme_get_option_drop_down($arr, 'projectTheme_admin_approves_each_project'); ?></td>
        </tr>
        

        
        <tr>
        <td valign=top width="22"><?php echo $menu_admin_project_theme_bull; ?></td>
        <td >Enable Blog:</td>
        <td><?php echo ProjectTheme_get_option_drop_down($arr, 'projectTheme_enable_blog'); ?></td>
        </tr>
        
        
        <tr>
        <td valign=top width="22"><?php echo $menu_admin_project_theme_bull; ?></td>
        <td >Enable Big Main Menu:</td>
        <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_show_blue_menu'); ?></td>
        </tr>
        
 
        
        <tr>
        <td valign=top width="22"><?php echo $menu_admin_project_theme_bull; ?></td>
        <td >Enable Slider on Front Page:</td>
        <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_slider_in_front'); ?></td>
        </tr>
        
        
        <tr>
        <td valign=top width="22"><?php echo $menu_admin_project_theme_bull; ?></td>
        <td >Enable Stretch Area (front page):</td>
        <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_stretch_enable'); ?></td>
        </tr>
        
        
        <tr>
        <td valign=top width="22"><?php echo $menu_admin_project_theme_bull; ?></td>
        <td >Project Listing Max Period (simple project):</td>
        <td><input type="text" name='projectTheme_project_period' size="6" value="<?php echo get_option('projectTheme_project_period'); ?>" /> days</td>
        </tr>
        
        
        <tr>
        <td valign=top width="22"><?php echo $menu_admin_project_theme_bull; ?></td>
        <td >Project Listing Max Period (featured project):</td>
        <td><input type="text" name='projectTheme_project_period_featured' size="6" value="<?php echo get_option('projectTheme_project_period_featured'); ?>" /> days</td>
        </tr>
        
        
        <tr>
        <td valign=top width="22"><?php echo $menu_admin_project_theme_bull; ?></td>
        <td >Category slug in links:</td>
        <td><input type="text" name='projectTheme_category_slug_link' size="16" value="<?php echo get_option('projectTheme_category_slug_link'); ?>" /></td>
        </tr>
        
        
        <tr>
        <td valign=top width="22"><?php echo $menu_admin_project_theme_bull; ?></td>
        <td >Location slug in links:</td>
        <td><input type="text" name='projectTheme_location_slug_link' size="16" value="<?php echo get_option('projectTheme_location_slug_link'); ?>" /></td>
        </tr>
        
        <tr>
        <td valign=top width="22"><?php echo $menu_admin_project_theme_bull; ?></td>
        <td >Projects slug in links:</td>
        <td><input type="text" name='projectTheme_projects_slug_link' size="16" value="<?php echo get_option('projectTheme_projects_slug_link'); ?>" /></td>
        </tr>
        
        
        <tr>
        <td valign=top width="22"><?php echo $menu_admin_project_theme_bull; ?></td>
        <td valign="top" >Custom CSS:</td>
        <td><textarea rows="5" cols="55" name="projectTheme_custom_CSS"><?php echo htmlspecialchars(stripslashes(get_option('projectTheme_custom_CSS'))); ?></textarea></td>
        </tr>
        
        <tr>
        <td></td>
        <td ></td>
        <td><input type="submit" name="my_submit" value="Save these Settings!" /></td>
        </tr>
        
        </table>
        </form>
  
  </div> 
  
  
   <div id="tabs4" >	
          
          <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=general-options&active_tab=tabs4">
            <table width="100%" class="sitemile-table">
    				
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="250"><?php _e('Filter Email Addresses (private messages):','ProjectTheme'); ?></td>
                    <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_filter_emails_private_messages'); ?></td>
                    </tr>
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="250"><?php _e('Filter URLs (private messages):','ProjectTheme'); ?></td>
                    <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_filter_urls_private_messages'); ?></td>
                    </tr>
                    
                  
  
                    <tr>
                    <td ></td>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_save7" value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>
            
            </table>      
          	</form>
          
          </div>
  
  <div id="tabs2" style="display: none; ">
  <!--
  <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=general-options&active_tab=tabs2">
            <table width="100%" class="sitemile-table">
    				
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="200"><?php _e('Enable Facebook Login:','ProjectTheme'); ?></td>
                    <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_enable_facebook_login'); ?></td>
                    </tr>
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="250"><?php _e('Facebook App ID:','ProjectTheme'); ?></td>
                    <td><input type="text" size="35" name="ProjectTheme_facebook_app_id" value="<?php echo get_option('ProjectTheme_facebook_app_id'); ?>"/></td>
                    </tr>
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="250"><?php _e('Facebook Secret Key:','ProjectTheme'); ?></td>
                    <td><input type="text" size="35" name="ProjectTheme_facebook_app_secret" value="<?php echo get_option('ProjectTheme_facebook_app_secret'); ?>"/></td>
                    </tr>
  
                    <tr>
                    <td ></td>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_save4" value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>
            
            </table>      
          	</form>
  --> For facebook connect, install this plugin: <a href="http://wordpress.org/extend/plugins/wordpress-social-login/">WordPress Social Login</a>
  </div> 
  <div id="tabs3" style="display: none; "> <!--
   <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=general-options&active_tab=tabs3">
            <table width="100%" class="sitemile-table">
    				
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="200"><?php _e('Enable Twitter Login:','ProjectTheme'); ?></td>
                    <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_enable_twitter_login'); ?></td>
                    </tr>
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="250"><?php _e('Twitter Consumer Key:','ProjectTheme'); ?></td>
                    <td><input type="text" size="35" name="ProjectTheme_twitter_consumer_key" value="<?php echo get_option('ProjectTheme_twitter_consumer_key'); ?>"/></td>
                    </tr>
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="250"><?php _e('Twitter Consumer Secret:','ProjectTheme'); ?></td>
                    <td><input type="text" size="35" name="ProjectTheme_twitter_consumer_secret" value="<?php echo get_option('ProjectTheme_twitter_consumer_secret'); ?>"/></td>
                    </tr>
  					
                    
                    	<tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="250"><?php _e('Callback URL:','ProjectTheme'); ?></td>
                    <td><?php echo get_bloginfo('template_url'); ?>/lib/social/twitter/callback.php</td>
                    </tr>
  
                    
                    
                    <tr>
                    <td ></td>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_save5" value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>
            
            </table>      
          	</form> --> For twitter connect, install this plugin: <a href="http://wordpress.org/extend/plugins/wordpress-social-login/">WordPress Social Login</a>
  
  </div> 
</div> 
 

  
		
	<?php
	//------------
	
	echo '</div>';
	
	
}
/******************************************************************
*
*	Admin Menu - New Function - sitemile[at]sitemile.com
*	developed by Andrei Saioc - andreisaioc[at]gmail.com
*
*******************************************************************/
function projectTheme_track_tools()
{

	$id_icon 		= 'icon-options-general-track';
	$ttl_of_stuff 	= 'ProjectTheme - '.__('Tracking Tools','ProjectTheme');
	$arr = array("yes" => __("Yes",'ProjectTheme'), "no" => __("No",'ProjectTheme'));
	global $menu_admin_project_theme_bull;
	
	//------------------------------------------------------
	
	echo '<div class="wrap">';
	echo '<div class="icon32" id="'.$id_icon.'"><br/></div>';	
	echo '<h2 class="my_title_class_sitemile">'.$ttl_of_stuff.'</h2>';	

	if(isset($_POST['ProjectTheme_save1']))
		{
			update_option('ProjectTheme_enable_google_analytics', 				trim($_POST['ProjectTheme_enable_google_analytics']));
			update_option('ProjectTheme_analytics_code', 						trim($_POST['ProjectTheme_analytics_code']));
			
			echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';
		}
		
	if(isset($_POST['ProjectTheme_save2']))
		{
			update_option('ProjectTheme_enable_other_tracking', 				trim($_POST['ProjectTheme_enable_other_tracking']));
			update_option('ProjectTheme_other_tracking_code', 						trim($_POST['ProjectTheme_other_tracking_code']));
			
			echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';
		}
			
	
	
	
	?>
    
   <div id="usual2" class="usual"> 
          <ul> 
            <li><a href="#tabs1" class="selected"><?php _e('Google Analytics','ProjectTheme'); ?></a></li> 
            <li><a href="#tabs2"><?php _e('Other Tracking Tools','ProjectTheme'); ?></a></li> 
          </ul> 
          <div id="tabs1">
          
          		
                 <form method="post" action="">
            <table width="100%" class="sitemile-table">
    				
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="250"><?php _e('Enable Google Analytics:','ProjectTheme'); ?></td>
                    <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_enable_google_analytics'); ?></td>
                    </tr>
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td valign="top"><?php _e('Analytics Code:','ProjectTheme'); ?></td>
                    <td><textarea rows="6" cols="80" name="ProjectTheme_analytics_code"><?php echo stripslashes(get_option('ProjectTheme_analytics_code')); ?></textarea></td>
                    </tr>
                    
             
                    <tr>
                    <td ></td>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_save1" value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>
            
            </table>      
          	</form>
                
          	
          </div>
          
          <div id="tabs2">	
          
             <form method="post" action="">
            <table width="100%" class="sitemile-table">
    				
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="250"><?php _e('Enable Other Tracking:','ProjectTheme'); ?></td>
                    <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_enable_other_tracking'); ?></td>
                    </tr>
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td valign="top"><?php _e('Other Tracking Code:','ProjectTheme'); ?></td>
                    <td><textarea rows="6" cols="80" name="ProjectTheme_other_tracking_code"><?php echo stripslashes(get_option('ProjectTheme_other_tracking_code')); ?></textarea></td>
                    </tr>
                    
             
                    <tr>
                    <td ></td>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_save2" value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>
            
            </table>      
          	</form>
                
          
          </div>
    
    
    <?php	
	
	echo '</div>';
}

/******************************************************************
*
*	Admin Menu - New Function - sitemile[at]sitemile.com
*	developed by Andrei Saioc - andreisaioc[at]gmail.com
*
*******************************************************************/
function projectTheme_theme_information()
{
	global $menu_admin_project_theme_bull;
	echo '<div class="wrap">';
	echo '<div class="icon32" id="icon-options-general-info"><br/></div>';	
	echo '<h2 class="my_title_class_sitemile">ProjectTheme Theme Info</h2>';
	?>
    
        <div id="usual2" class="usual"> 
          <ul> 
            <li><a href="#tabs1" class="selected"><?php _e('Main Information','ProjectTheme'); ?></a></li> 
            <li><a href="#tabs2"><?php _e('From SiteMile Blog','ProjectTheme'); ?></a></li> 
  
          </ul> 
          <div id="tabs1" style="display: block; ">	
          
          <table width="100%" class="sitemile-table">
    				

                    <tr>                    
                    <td width="260"><?php _e('ProjectTheme Version:','ProjectTheme'); ?></td>
                    <td><?php echo PROJECTTHEME_VERSION; ?></td>
                    </tr>
                    
                    <tr>                    
                    <td width="160"><?php _e('ProjectTheme Latest Release:','ProjectTheme'); ?></td>
                    <td><?php echo PROJECTTHEME_RELEASE; ?></td>
                    </tr>
                    
                    <tr>                    
                    <td width="160"><?php _e('WordPress Version:','ProjectTheme'); ?></td>
                    <td><?php bloginfo('version'); ?></td>
                    </tr>
                    
                    
                    <tr>                    
                    <td width="160"><?php _e('Go to SiteMile official page:','ProjectTheme'); ?></td>
                    <td><a class="festin" href="http://sitemile.com">SiteMile - Premium WordPress Themes</a></td>
                    </tr>
                    
                    <tr>                    
                    <td width="160"><?php _e('Go to Project official page:','ProjectTheme'); ?></td>
                    <td><a class="festin" href="http://sitemile.com/p/project">Project Bidding Theme</a></td>
                    </tr>
                    
                    <tr>                    
                    <td width="160"><?php _e('Go to support forums:','ProjectTheme'); ?></td>
                    <td><a class="festin" href="http://sitemile.com/forums">SiteMile Support Forums</a></td>
                    </tr>
                    
                    <tr>                    
                    <td width="160"><?php _e('Contact SiteMile Team:','ProjectTheme'); ?></td>
                    <td><a class="festin" href="http://sitemile.com/contact-us">Contact Form</a></td>
                    </tr>
                    
                    <tr>                    
                    <td width="160"><?php _e('Like us on Facebook:','ProjectTheme'); ?></td>
                    <td><a class="festin" href="http://facebook.com/sitemile">SiteMile Facebook Fan Page</a></td>
                    </tr>
                    
                    
                     <tr>                    
                    <td width="160"><?php _e('Follow us on Twitter:','ProjectTheme'); ?></td>
                    <td><a class="festin" href="http://twitter.com/sitemile">SiteMile Twitter Page</a></td>
                    </tr>
                    
                    
                    
           </table>         
          
          </div>
          
          <div id="tabs2" style="display: none; overflow:hidden ">	
          
          <?php
		   echo '<div style="float:left;">';
 wp_widget_rss_output(array(
 'url' => 'http://sitemile.com/feed/',
 'title' => 'Latest news from SiteMile.com Blog',
 'items' => 10,
 'show_summary' => 1,
 'show_author' => 0,
 'show_date' => 1
 ));
 echo "</div>";
 
 ?>
          
          </div>
          
    
    <?php	
	
	echo '</div>';
}




/******************************************************************
*
*	Admin Menu - New Function - sitemile[at]sitemile.com
*	developed by Andrei Saioc - andreisaioc[at]gmail.com
*
*******************************************************************/


	function projectTheme_posts_join_0($join) {
		global $wp_query, $wpdb;
 
		$join .= " LEFT JOIN (
				SELECT post_id, meta_value as closed_date_due
				FROM $wpdb->postmeta
				WHERE meta_key =  'closed_date' ) AS DD
				ON $wpdb->posts.ID = DD.post_id ";

 
		return $join;
	}

//------------------------------------------------------

	function projectTheme_posts_orderby_0( $orderby )
	{
		global $wpdb;
		$orderby = " closed_date_due+0 desc, $wpdb->posts.post_date desc ";
		return $orderby;
	}





function projectTheme_orders()
{
	global $menu_admin_project_theme_bull;
	echo '<div class="wrap">';
	echo '<div class="icon32" id="icon-options-general-orders"><br/></div>';	
	echo '<h2 class="my_title_class_sitemile">ProjectTheme Orders</h2>';
	
	if(isset($_GET['mark_delivered']))
	{
		$tm = current_time('timestamp',0);
		$pid = $_GET['mark_delivered'];
		
		update_post_meta($pid, 'mark_coder_delivered',			"1");
		update_post_meta($pid, 'mark_coder_delivered_date',		$tm);
		$winner_bd = projectTheme_get_winner_bid($pid);
		
		//------------------------------------------------------------------------------
		
		ProjectTheme_send_email_on_delivered_project_to_bidder($pid, $winner_bd->uid);
		ProjectTheme_send_email_on_delivered_project_to_owner($pid);		
		
		echo '<div class="saved_thing">Marked Delivered!</div>';
		
	}	
	
	if(isset($_GET['mark_completed']))
	{
		$tm = current_time('timestamp',0);
		$pid = $_GET['mark_completed'];
		$pstpst = get_post($pid);
		
		update_post_meta($pid, 'mark_seller_accepted',		"1");
		update_post_meta($pid, 'mark_seller_accepted_date',		$tm);
		
		update_post_meta($pid, 'outstanding',	"0");
		update_post_meta($pid, 'delivered',		"1");		
		//update_post_meta($pid, 'paid_user',		"1");		
		
		//------------------------------------------------------------------------------
		
		ProjectTheme_send_email_on_completed_project_to_bidder($pid, $pstpst->post_author);
		ProjectTheme_send_email_on_completed_project_to_owner($pid);		
		
		echo '<div class="saved_thing">Marked Completed!</div>';
		
	}
	
	
	if(isset($_GET['mark_paid']))
	{
		$tm = current_time('timestamp',0);
		$pid = $_GET['mark_paid'];
 
		update_post_meta($pid, 'paid_user_date',		$tm);		
		update_post_meta($pid, 'paid_user',		"1");	 	
		
		echo '<div class="saved_thing">Marked Paid!</div>';
		
	}	
	
	
	?>
    
        <div id="usual2" class="usual"> 
  <ul> 
    <li><a href="#tabs1">Open Orders</a></li> 
    <li><a href="#tabs2">Delivered Orders</a></li>
    <li><a href="#tabs3">Completed Orders</a></li>
    <li><a href="#tabs4">Paid Orders</a></li>
    <!-- <li><a href="#tabs4">Failed &amp; Disputed Orders</a></li> -->
    <?php do_action('ProjectTheme_main_menu_orders_tabs'); ?>
  </ul> 
  <div id="tabs1" style="display: none; ">
    	<?php
		
		global $current_user;
				get_currentuserinfo();
				$uid = $current_user->ID;
				
				
				global $wp_query;
				$query_vars = $wp_query->query_vars;
				$post_per_page = 25;				
				
		
				$outstanding = array(
						'key' => 'outstanding',
						'value' => "1",
						'compare' => '='
					);
					
				$winner = array(
						'key' => 'winner',
						'value' => 0,
						'compare' => '!='
					);		
				
				
				$delivered2 = array(
						'key' => 'delivered',
						'value' => "1",
						'compare' => '!='
					);
					
						
				$mark_coder_delivered = array(
						'key' => 'mark_coder_delivered',
						'value' => "1",
						'compare' => '!='
					);
				
				
				
				$pj = $_GET['pj1'];
				if(empty($_GET['pj1'])) $pj = 1;
				
				$args = array('post_type' => 'project', 'order' => 'DESC', 'posts_per_page' => $post_per_page,
				'paged' => $pj, 'meta_query' => array($outstanding, $winner, $delivered2, $mark_coder_delivered));
				
				add_filter('posts_join', 	'projectTheme_posts_join_0');
				add_filter('posts_orderby', 'projectTheme_posts_orderby_0' );
				
				query_posts($args);
				
				 
				 
				 
				if(have_posts()) :
				
				echo '<table class="widefat post fixed">';
				echo '<thead>';
					echo '<th>Project Title</th>';
					echo '<th>Project Creator</th>';
					echo '<th>Bidder</th>';
					
					echo '<th>Winning Bid</th>';
					echo '<th>Date Ordered</th>';
					echo '<th>Expected Delivery</th>';
					echo '<th>Options</th>';
				
				echo '</thead>';
				
				while ( have_posts() ) : the_post();
				
					$bid = projectTheme_get_winner_bid(get_the_ID()); $bidsa = $bid;
					$bid = ProjectTheme_get_show_price($bid->bid);
					$post = get_post(get_the_ID());
					$creator = get_userdata($post->post_author);
					
					$winner = get_post_meta(get_the_ID(), 'winner', true);
					$winner = get_userdata($winner);	
					$winner = '<a href="'.ProjectTheme_get_user_profile_link($winner->ID).'">'.$winner->user_login.'</a>';
					$creator = '<a href="'.ProjectTheme_get_user_profile_link($post->post_author).'">'.$creator->user_login.'</a>';
					
					$tm_d = get_post_meta(get_the_ID(), 'expected_delivery', true);							
					$tm_d = date_i18n('d-M-Y H:i:s', $tm_d);
					$closed_date = get_post_meta(get_the_ID(), 'closed_date', true);	
					$winner_date = date_i18n('d-M-Y H:i:s', $closed_date);
					
					echo '</tr>';
					echo '<th><a href="'.get_permalink(get_the_ID()).'">'.get_the_title().'</a></th>';
					echo '<th>'.$creator.'</th>';
					echo '<th>'.$winner.'</th>';
					
					echo '<th>'.$bid.'</th>';
					echo '<th>'.$winner_date.'</th>';
					echo '<th>'.$tm_d.'</th>';
					
					echo '<th><a href="'.get_admin_url().'admin.php?page=orders&pj1='.$pj.'&mark_delivered='.get_the_ID().'">Mark Delivered</a></th>';
					
					
					echo '</tr>';
				endwhile;
				
				echo '</table>';
				
				//-------------------------------------------------------
				
				if(function_exists('wp_pagenavi')):
				
								if ( !is_array( $args ) ) {
						$argv = func_get_args();
						$args = array();
						foreach ( array( 'before', 'after', 'options' ) as $i => $key )
							$args[ $key ] = $argv[ $i ];
					}
				
					$args = wp_parse_args( $args, array(
						'before' => '',
						'after' => '',
						'options' => array(),
						'query' => $GLOBALS['wp_query'],
						'type' => 'posts',
						'echo' => true
						) );
	
				$instance = new PageNavi_Call( $args );
				list( $posts_per_page, $paged, $total_pages ) = $instance->get_pagination_args();
				
				for($i=1; $i<=$total_pages; $i++)
				{
					if($pj == $i)
					echo $i.' | ';
					else
					echo '<a href="'.get_admin_url().'admin.php?page=orders&pj1='.$i.'">'.$i.'</a> | ';		
				}
				
				 endif;
				
				//------------ end pagination ------------------
				
				 else:
				
				_e("There are no outstanding projects yet.",'ProjectTheme');
				
				endif;
				wp_reset_query();
		
		
		?>
        
          </div> 
        
        
        <div id="tabs2" style="display: none; ">
        
        <?php
		
		global $current_user;
				get_currentuserinfo();
				$uid = $current_user->ID;
				
				
				global $wp_query;
				$query_vars = $wp_query->query_vars;
				$post_per_page = 25;				
				
				
				$delivered = array(
						'key' => 'mark_coder_delivered',
						'value' => "1",
						'compare' => '='
					);
					
				$mark_seller_accepted = array(
						'key' => 'mark_seller_accepted',
						'value' => "1",
						'compare' => '!='
					);
					
				$paid = array(
						'key' => 'paid_user',
						'value' => "0",
						'compare' => '='
					);
					
				
				
						
	
				$pj = $_GET['pj2'];
				if(empty($_GET['pj2'])) $pj = 1;		
				
				
				$args = array('post_type' => 'project', 'posts_per_page' => $post_per_page,
				'paged' => $pj, 'meta_query' => array($delivered, $paid, $mark_seller_accepted));
				
				add_filter('posts_join', 	'projectTheme_posts_join_0');
				add_filter('posts_orderby', 'projectTheme_posts_orderby_0' );
				
				
				query_posts($args);

				if(have_posts()) :
				
				echo '<table class="widefat post fixed">';
				echo '<thead>';
					echo '<th>Project Title</th>';
					echo '<th>Project Creator</th>';
					echo '<th>Bidder</th>';
					
					echo '<th>Winning Bid</th>';
					echo '<th>Date Ordered</th>';
					echo '<th>Delivered On</th>';
					echo '<th>Options</th>';
				
				echo '</thead>';
				
				while ( have_posts() ) : the_post();
				
					$bid = projectTheme_get_winner_bid(get_the_ID());
					$bid = ProjectTheme_get_show_price($bid->bid);
					
					$post = get_post(get_the_ID());
					$creator = get_userdata($post->post_author);
					
					$winner = get_post_meta(get_the_ID(), 'winner', true);
					$winner = get_userdata($winner);	
					$winner = '<a href="'.ProjectTheme_get_user_profile_link($winner->ID).'">'.$winner->user_login.'</a>';
					$creator = '<a href="'.ProjectTheme_get_user_profile_link($post->post_author).'">'.$creator->user_login.'</a>';

					$tm_d = get_post_meta(get_the_ID(), 'mark_coder_delivered_date', true);							
					$tm_d = date_i18n('d-M-Y H:i:s', $tm_d);
					$closed_date = get_post_meta(get_the_ID(), 'closed_date', true);	
					$winner_date = date_i18n('d-M-Y H:i:s', $closed_date);
					
					echo '</tr>';
					echo '<th><a href="'.get_permalink(get_the_ID()).'">'.get_the_title().'</a></th>';
					echo '<th>'.$creator.'</th>';
					echo '<th>'.$winner.'</th>';
					
					echo '<th>'.$bid.'</th>';					
					echo '<th>'.$winner_date.'</th>';
					echo '<th>'.$tm_d.'</th>';
					echo '<th><a href="'.get_admin_url().'admin.php?page=orders&pj2='.$pj.'&active_tab=tabs2&mark_completed='.get_the_ID().'">Mark Completed</a></th>';
					
					echo '</tr>';
				endwhile;
				
				echo '</table>';
				
				//-------------------------------------------------------
				
				if(function_exists('wp_pagenavi')):
				
								if ( !is_array( $args ) ) {
						$argv = func_get_args();
						$args = array();
						foreach ( array( 'before', 'after', 'options' ) as $i => $key )
							$args[ $key ] = $argv[ $i ];
					}
				
					$args = wp_parse_args( $args, array(
						'before' => '',
						'after' => '',
						'options' => array(),
						'query' => $GLOBALS['wp_query'],
						'type' => 'posts',
						'echo' => true
						) );
	
				$instance = new PageNavi_Call( $args );
				list( $posts_per_page, $paged, $total_pages ) = $instance->get_pagination_args();
				
				for($i=1; $i<=$total_pages; $i++)
				{
					if($pj == $i)
					echo $i.' | ';
					else
					echo '<a href="'.get_admin_url().'admin.php?page=orders&pj2='.$i.'&active_tab=tabs2">'.$i.'</a> | ';		
				}
				
				 endif;
				
				//------------ end pagination ------------------
				
				 else:
				
				_e("There are no delivered projects yet.",'ProjectTheme');
				
				endif;
				wp_reset_query();
		
		
		?>
        
        </div> 
       
       
        
        
         <div id="tabs3" style="display: none; ">
         
         
         <?php
		
				global $current_user;
				get_currentuserinfo();
				$uid = $current_user->ID;
				
				
				global $wp_query;
				$query_vars = $wp_query->query_vars;
				$post_per_page = 25;				
				
				
				$delivered = array(
						'key' => 'mark_seller_accepted',
						'value' => "1",
						'compare' => '='
					);
					
				$delivered2 = array(
						'key' => 'delivered',
						'value' => "1",
						'compare' => '='
					);	
					
				$paid = array(
						'key' => 'paid_user',
						'value' => "0",
						'compare' => '='
					);		
				
				
				
	
				$pj = $_GET['pj3'];
				if(empty($_GET['pj3'])) $pj = 1;
				
				$args = array('post_type' => 'project', 'posts_per_page' => $post_per_page,
				'paged' => $pj, 'meta_query' => array($delivered, $delivered2, $paid));
				
				add_filter('posts_join', 	'projectTheme_posts_join_0');
				add_filter('posts_orderby', 'projectTheme_posts_orderby_0' );
				
				
				query_posts($args);

				if(have_posts()) :
				
				echo '<table class="widefat post fixed">';
				echo '<thead>';
					echo '<th>Project Title</th>';
					echo '<th>Project Creator</th>';
					echo '<th>Bidder</th>';
					
					echo '<th>Winning Bid</th>';
					echo '<th>Date Ordered</th>';
					echo '<th>Completed On</th>';
					echo '<th>Options</th>';
				
				echo '</thead>';
				
				while ( have_posts() ) : the_post();
				
					$bid = projectTheme_get_winner_bid(get_the_ID());
					$bid = ProjectTheme_get_show_price($bid->bid);
					
					$post = get_post(get_the_ID());
					$creator = get_userdata($post->post_author);
					
					$winner = get_post_meta(get_the_ID(), 'winner', true);
					$winner = get_userdata($winner);	
					$winner = '<a href="'.ProjectTheme_get_user_profile_link($winner->ID).'">'.$winner->user_login.'</a>';
					
					$tm_d = get_post_meta(get_the_ID(), 'mark_seller_accepted_date', true);							
					$tm_d = date_i18n('d-M-Y H:i:s', $tm_d);
					$closed_date = get_post_meta(get_the_ID(), 'closed_date', true);	
					$winner_date = date_i18n('d-M-Y H:i:s', $closed_date);
					$creator = '<a href="'.ProjectTheme_get_user_profile_link($post->post_author).'">'.$creator->user_login.'</a>';
					$paid_user = get_post_meta(get_the_ID(), 'paid_user', true);
					
					
					echo '</tr>';
					echo '<th><a href="'.get_permalink(get_the_ID()).'">'.get_the_title().'</a></th>';
					echo '<th>'.$creator.'</th>';
					
					echo '<th>'.$winner.'</th>';
					
					echo '<th>'.$bid.'</th>';
					echo '<th>'.$winner_date.'</th>';
					echo '<th>'.$tm_d.'</th>';
					echo '<th>'.'<a href="'.get_admin_url().'admin.php?active_tab=tabs3&page=orders&pj3='.$pj.'&mark_paid='.get_the_ID().'">Mark Paid</a>'.'</th>';
					
					 
					echo '</tr>';
				endwhile;
				
				echo '</table>';
				
				//-------------------------------------------------------
				
				if(function_exists('wp_pagenavi')):
				
								if ( !is_array( $args ) ) {
						$argv = func_get_args();
						$args = array();
						foreach ( array( 'before', 'after', 'options' ) as $i => $key )
							$args[ $key ] = $argv[ $i ];
					}
				
					$args = wp_parse_args( $args, array(
						'before' => '',
						'after' => '',
						'options' => array(),
						'query' => $GLOBALS['wp_query'],
						'type' => 'posts',
						'echo' => true
						) );
	
				$instance = new PageNavi_Call( $args );
				list( $posts_per_page, $paged, $total_pages ) = $instance->get_pagination_args();
				
				for($i=1; $i<=$total_pages; $i++)
				{
					if($pj == $i)
					echo $i.' | ';
					else
					echo '<a href="'.get_admin_url().'admin.php?page=orders&pj3='.$i.'&active_tab=tabs3">'.$i.'</a> | ';		
				}
				
				 endif;
				
				//------------ end pagination ------------------
				
				 else:
				
				_e("There are no completed projects yet.",'ProjectTheme');
				
				endif;
				wp_reset_query();
		
		
		?>
         
         
         </div> 
         
         
         
         
         <div id="tabs4" style="display: none; ">
         
         
         <?php
		
				global $current_user;
				get_currentuserinfo();
				$uid = $current_user->ID;
				
				
				global $wp_query;
				$query_vars = $wp_query->query_vars;
				$post_per_page = 25;				
				
				
				$delivered = array(
						'key' => 'mark_seller_accepted',
						'value' => "1",
						'compare' => '='
					);
					
				$delivered2 = array(
						'key' => 'delivered',
						'value' => "1",
						'compare' => '='
					);	
					
				$paid = array(
						'key' => 'paid_user',
						'value' => "1",
						'compare' => '='
					);		
				
				
				
	
				$pj = $_GET['pj4'];
				if(empty($_GET['pj4'])) $pj = 1;
				
				$args = array('post_type' => 'project', 'posts_per_page' => $post_per_page,
				'paged' => $pj, 'meta_query' => array($delivered, $delivered2, $paid));
				
				add_filter('posts_join', 	'projectTheme_posts_join_0');
				add_filter('posts_orderby', 'projectTheme_posts_orderby_0' );
				
				
				query_posts($args);

				if(have_posts()) :
				
				echo '<table class="widefat post fixed">';
				echo '<thead>';
					echo '<th>Project Title</th>';
					echo '<th>Project Creator</th>';
					echo '<th>Bidder</th>';
					
					echo '<th>Winning Bid</th>';
					echo '<th>Date Ordered</th>';
					echo '<th>Completed On</th>';
					echo '<th>Paid On</th>';
				
				echo '</thead>';
				
				while ( have_posts() ) : the_post();
				
					$bid = projectTheme_get_winner_bid(get_the_ID());
					$bid = ProjectTheme_get_show_price($bid->bid);
					
					$post = get_post(get_the_ID());
					$creator = get_userdata($post->post_author);
					
					$winner = get_post_meta(get_the_ID(), 'winner', true);
					$winner = get_userdata($winner);	
					$winner = '<a href="'.ProjectTheme_get_user_profile_link($winner->ID).'">'.$winner->user_login.'</a>';
					
					$tm_d = get_post_meta(get_the_ID(), 'mark_seller_accepted_date', true);							
					$tm_d = date_i18n('d-M-Y H:i:s', $tm_d);
					$closed_date = get_post_meta(get_the_ID(), 'closed_date', true);	
					$winner_date = date_i18n('d-M-Y H:i:s', $closed_date);
					$creator = '<a href="'.ProjectTheme_get_user_profile_link($post->post_author).'">'.$creator->user_login.'</a>';
					$paid_user = get_post_meta(get_the_ID(), 'paid_user', true);
					
					$paid_user_date = get_post_meta(get_the_ID(), 'paid_user_date', true);
					if(!empty($paid_user_date)) $paid_user_date = date_i18n('d-M-Y H:i:s', $paid_user_date);
					
					
					echo '</tr>';
					echo '<th><a href="'.get_permalink(get_the_ID()).'">'.get_the_title().'</a></th>';
					echo '<th>'.$creator.'</th>';
					
					echo '<th>'.$winner.'</th>';
					
					echo '<th>'.$bid.'</th>';
					echo '<th>'.$winner_date.'</th>';
					echo '<th>'.$tm_d.'</th>';
					echo '<th>'.$paid_user_date.'</th>';
					
					 
					echo '</tr>';
				endwhile;
				
				echo '</table>';
				
				//-------------------------------------------------------
				
				if(function_exists('wp_pagenavi')):
				
								if ( !is_array( $args ) ) {
						$argv = func_get_args();
						$args = array();
						foreach ( array( 'before', 'after', 'options' ) as $i => $key )
							$args[ $key ] = $argv[ $i ];
					}
				
					$args = wp_parse_args( $args, array(
						'before' => '',
						'after' => '',
						'options' => array(),
						'query' => $GLOBALS['wp_query'],
						'type' => 'posts',
						'echo' => true
						) );
	
				$instance = new PageNavi_Call( $args );
				list( $posts_per_page, $paged, $total_pages ) = $instance->get_pagination_args();
				
				for($i=1; $i<=$total_pages; $i++)
				{
					if($pj == $i)
					echo $i.' | ';
					else
					echo '<a href="'.get_admin_url().'admin.php?page=orders&pj4='.$i.'&active_tab=tabs4">'.$i.'</a> | ';		
				}
				
				 endif;
				
				//------------ end pagination ------------------
				
				 else:
				
				_e("There are no paid projects yet.",'ProjectTheme');
				
				endif;
				wp_reset_query();
		
		
		?>
         
         
         </div> 
         
         
         
         
         </div> 
         

    	
        <?php do_action('ProjectTheme_main_menu_orders_content'); ?>
    
    <?php	
	
	echo '</div>';
}



/******************************************************************
*
*	Admin Menu - New Function - sitemile[at]sitemile.com
*	developed by Andrei Saioc - andreisaioc[at]gmail.com
*
*******************************************************************/

function projectTheme_private_messages_scr()
{
	global $menu_admin_project_theme_bull, $wpdb;
	echo '<div class="wrap">';
	echo '<div class="icon32" id="icon-options-general-mess"><br/></div>';	
	echo '<h2 class="my_title_class_sitemile">ProjectTheme Private Messages</h2>';
	?>
    
       <div id="usual2" class="usual"> 
          <ul> 
            <li><a href="#tabs1"><?php _e('All Private Messages','ProjectTheme'); ?></a></li> 
            <li><a href="#tabs2"><?php _e('Search User','ProjectTheme'); ?></a></li> 

          </ul> 
          <div id="tabs1">	
          
          <?php
		  
		  if(isset($_GET['approve_message']))
		  {
			  $mess_id = ($_GET['approve_message']);
			  
			  $s = "select * from ".$wpdb->prefix."project_pm where id='$mess_id'";
          	  $r = $wpdb->get_results($s);
			  $row = $r[0];
			  
			  
			  if($_GET['accept_str'] == "1"):
			  
			  	if($row->approved == 0)
				{
					$tm = current_time('timestamp',0);
					$ss = "update ".$wpdb->prefix."project_pm set approved='1' , approved_on='$tm', show_to_destination='1' where id='$mess_id'";	
					$wpdb->query($ss);
					
					ProjectTheme_send_email_on_priv_mess_received($row->initiator, $row->user);
				}
			  
			  ?>
			  
			  <div class="saved_thing">
              <?php _e('The message has been approved.','ProjectTheme'); ?></a>
              
              </div>
              
              
              <?php
			  else:
			?>  
			  
			  <div class="saved_thing">
              <?php _e('Are you sure you want to approve this message?','ProjectTheme'); ?> &nbsp; &nbsp; &nbsp;
             <a href="<?php echo get_admin_url().'admin.php?page=private-messages&pj='.$_GET['pj'].'&approve_message='.$row->id."&accept_str=1"; ?>" class="approve_yes"><?php _e('Yes, Approve!','ProjectTheme'); ?></a>
              
              </div>
              
			  <?php
			  
			  endif;
		  }
		  
		  
		  ?>
          
          
          <?php
		  
		  	$nrpostsPage = 10; 
		  	$page = $_GET['pj']; if(empty($page)) $page = 1;
			$my_page = $page;
			
		   $s = "select * from ".$wpdb->prefix."project_pm order by id desc limit ".($nrpostsPage * ($page - 1) )." ,$nrpostsPage";
           $r = $wpdb->get_results($s);
		 	
		
		$s1 = "select id from ".$wpdb->prefix."project_pm order by id desc";	 	
		$r1 = $wpdb->get_results($s1);	
		
		
		if(count($r) > 0):
		
				$total_nr = count($r1);
				
				$nrposts = $total_nr;
				$totalPages = ceil($nrposts / $nrpostsPage);
				$pagess = $totalPages;
				$batch = 10; //ceil($page / $nrpostsPage );
				
				
				$start 		= floor($my_page/$batch) * $batch + 1; 
				$end		= $start + $batch - 1;
				$end_me 	= $end + 1;
				$start_me 	= $start - 1;
				
				if($end > $totalPages) $end = $totalPages;
				if($end_me > $totalPages) $end_me = $totalPages;
				
				if($start_me <= 0) $start_me = 1;
				
				$previous_pg = $my_page - 1;
				if($previous_pg <= 0) $previous_pg = 1;
				
				$next_pg = $my_page + 1;
				if($next_pg >= $totalPages) $next_pg = 1;
				
			
			
		  
		  ?>
          
           <table class="widefat post fixed" cellspacing="0">
            <thead>
            <tr>
            <th><?php _e('Sender','ProjectTheme'); ?></th>
            <th><?php _e('Receiver','ProjectTheme'); ?></th>
            <th width="20%"><?php _e('Subject','ProjectTheme'); ?></th>
            <th><?php _e('Sent On','ProjectTheme'); ?></th>
            <th><?php _e('Approved','ProjectTheme'); ?></th>
            <th width="25%"><?php _e('Options','ProjectTheme'); ?></th>
            </tr>
            </thead>
            
            
            
            <tbody>
            <?php
            
           
            $i = 0;
            foreach($r as $row)
            {
                $sender 	= get_userdata($row->initiator);
				$receiver 	= get_userdata($row->user);
                
				if($i%2) $new_bg_color = '#E7E9F1';
				else $new_bg_color = '#fff';
				
					$i++;
				
                echo '<tr style="background:'.$new_bg_color.'">';
				echo '<th>'.$sender->user_login.'</th>';
				echo '<th>'.$receiver->user_login.'</th>';
				echo '<th>'.$row->subject.'</th>';
				echo '<th>'.date('d-M-Y H:i:s', $row->datemade).'</th>';
				echo '<th>'.($row->approved == 1 ? __("Yes",'ProjectTheme') : __("No","ProjectTheme")).'</th>';
				echo '<th>'.($row->approved == 0 ? '<a href="'.get_admin_url().'admin.php?page=private-messages&pj='.$_GET['pj'].'&approve_message='.$row->id.'">'.__("Approve",'ProjectTheme') ."</a>" : '').'</th>';
				echo '</tr>';
            
				echo '<tr style="background:'.$new_bg_color.'">';
				echo '<th colspan="6">'.$row->content;
				
				if(!empty($row->file_attached))
				echo '<br/><br/>'.sprintf(__('File Attached: %s','ProjectTheme') , '<a href="'.wp_get_attachment_url($row->file_attached).'">'.wp_get_attachment_url($row->file_attached)."</a>") ;
	  
				
				echo '</th>';
				echo '</tr>';
							
			}
            
            ?>
            </tbody>
            
            
            </table>
            <?php
			
			
			if($start > 1)
			echo '<a href="'.get_admin_url().'admin.php?page=private-messages&pj='.$previous_pg.'"><< '.__('Previous','ProjectTheme').'</a> ';
			echo ' <a href="'.get_admin_url().'admin.php?page=private-messages&pj='.$start_me.'"><<</a> ';
			
			
	
			
			for($i = $start; $i <= $end; $i ++) {
				if ($i == $my_page) {
					echo ''.$i.' | ';
				} else {
		
					echo '<a href="'.get_admin_url().'admin.php?page=private-messages&pj='.$i.'">'.$i.'</a> | ';
					
				}
			}
	
	
			
			if($totalPages > $my_page)
			echo ' <a href="'.get_admin_url().'admin.php?page=private-messages&pj='.$end_me.'">>></a> ';
			echo ' <a href="'.get_admin_url().'admin.php?page=private-messages&pj='.$next_pg.'">'.__('Next','ProjectTheme').' >></a> ';	
			
			
			?>
            
            
            
            <?php else: ?>
            
            <div class="padd101">
            <?php _e('There are no private messages.','ProjectTheme'); ?>
            </div>
            
            <?php endif; ?>
          
          
          </div>
          
          <div id="tabs2">	
          
          
          
          <form method="get" action="<?php echo get_admin_url(); ?>admin.php">
            <input type="hidden" value="private-messages" name="page" />
            <input type="hidden" value="tabs2" name="active_tab" />
            <table width="100%" class="sitemile-table">
            	<tr>
                <td><?php _e('Search User','ProjectTheme'); ?></td>
                <td><input type="text" value="<?php echo $_GET['search_user']; ?>" name="search_user" size="20" /> <input type="submit" name="ProjectTheme_save2" value="<?php _e('Search','ProjectTheme'); ?>"/></td>
                </tr>
     
            
            </table>
            </form> 
            
            <?php
			
			if(isset($_GET['ProjectTheme_save2'])):
				
				$search_user = trim($_GET['search_user']);
				
				$user 	= get_userdatabylogin($search_user);
				$uid 	= $user->ID;  
				
				$s = "select * from ".$wpdb->prefix."project_pm where initiator='$uid' OR user='$uid' order by id desc";
          		$r = $wpdb->get_results($s);
		  	
			if(count($r) > 0):
		  
		  ?>
          
           <table class="widefat post fixed" cellspacing="0">
            <thead>
            <tr>
            <th><?php _e('Sender','ProjectTheme'); ?></th>
            <th><?php _e('Receiver','ProjectTheme'); ?></th>
            <th width="20%"><?php _e('Subject','ProjectTheme'); ?></th>
            <th><?php _e('Sent On','ProjectTheme'); ?></th>
            <th><?php _e('Approved','ProjectTheme'); ?></th>
            <th width="25%"><?php _e('Options','ProjectTheme'); ?></th>
            </tr>
            </thead>
            
            
            
            <tbody>
            <?php
            
           



            
           $i = 0;
            foreach($r as $row)
            {
                $sender 	= get_userdata($row->initiator);
				$receiver 	= get_userdata($row->user);
                
				if($i%2) $new_bg_color = '#E7E9F1';
				else $new_bg_color = '#fff';
				
					$i++;
				
                echo '<tr style="background:'.$new_bg_color.'">';
				echo '<th>'.$sender->user_login.'</th>';
				echo '<th>'.$receiver->user_login.'</th>';
				echo '<th>'.$row->subject.'</th>';
				echo '<th>'.date('d-M-Y H:i:s', $row->datemade).'</th>';
				echo '<th>'.($row->approved == 1 ? __("Yes",'ProjectTheme') : __("No","ProjectTheme")).'</th>';
				echo '<th>'.($row->approved == 0 ? '<a href="'.get_admin_url().'admin.php?page=private-messages&pj='.$_GET['pj'].'&approve_message='.$row->id.'">'.__("Approve",'ProjectTheme') ."</a>" : '').'</th>';
				echo '</tr>';
            
				echo '<tr style="background:'.$new_bg_color.'">';
				echo '<th colspan="6">'.$row->content;
				
				if(!empty($row->file_attached))
				echo '<br/><br/>'.sprintf(__('File Attached: %s','ProjectTheme') , '<a href="'.wp_get_attachment_url($row->file_attached).'">'.wp_get_attachment_url($row->file_attached)."</a>") ;
	  
				
				echo '</th>';
				echo '</tr>';
							
			}
            ?>
            </tbody>
            
            
            </table>
            <?php else: ?>
            
            <div class="padd101">
            <?php _e('There are no results for your search.','ProjectTheme'); ?>
            </div>
            
            <?php endif; 
				
			
			endif;
			
			?>
          
          </div>
          
 
<?php
	echo '</div>';		
	
}


/******************************************************************
*
*	Admin Menu - New Function - sitemile[at]sitemile.com
*	developed by Andrei Saioc - andreisaioc[at]gmail.com
*
*******************************************************************/

function projectTheme_reviews_scr()
{
	global $menu_admin_project_theme_bull, $wpdb;
	echo '<div class="wrap">';
	echo '<div class="icon32" id="icon-options-general-rev"><br/></div>';	
	echo '<h2 class="my_title_class_sitemile">ProjectTheme Reviews/Feedback</h2>';
	?>
    
        <div id="usual2" class="usual"> 
  <ul> 
    <li><a href="#tabs1"><?php _e('All User Reviews','ProjectTheme'); ?></a></li> 
    <li><a href="#tabs2"><?php _e('Search User','ProjectTheme'); ?></a></li> 
  </ul> 
 
 
  <div id="tabs1" style="display:none">	
          
          <?php
		  
		   $s = "select * from ".$wpdb->prefix."project_ratings where awarded>0 order by id desc";
           $r = $wpdb->get_results($s);
		  	
			if(count($r) > 0):
		  
		  ?>
          
           <table class="widefat post fixed" cellspacing="0">
            <thead>
            <tr>
            <th><?php _e('Rated User','ProjectTheme'); ?></th>
            <th><?php _e('Project','ProjectTheme'); ?></th>
            <th><?php _e('Rating','ProjectTheme'); ?></th>
            <th><?php _e('Description','ProjectTheme'); ?></th>
            <th><?php _e('Awarded On','ProjectTheme'); ?></th>
            <th><?php _e('Options','ProjectTheme'); ?></th>
            </tr>
            </thead>
            
            
            
            <tbody>
            <?php
            
   
            foreach($r as $row)
            {
	
				$post = get_post($row->pid);
				$userdata = get_userdata($row->touser);
				$pid = $row->pid;
				
				echo '<tr>';
				echo '<th>'.$userdata->user_login.'</th>';
				echo '<th><a href="'.get_permalink($pid).'">'.$post->post_title.'</a></th>';
				echo '<th>'.($row->grade/2).'</th>';
				echo '<th>'.$row->comment.'</th>';
				echo '<th>'.date('d-M-Y H:i:s', $row->datemade).'</th>';
				echo '<th>#</th>';
				echo '</tr>';
				
				
            }
            
            ?>
            </tbody>
            
            
            </table>
            <?php else: ?>
            
            <div class="padd101">
            <?php _e('There are no user feedback.','ProjectTheme'); ?>
            </div>
            
            <?php endif; ?>
          
          </div>
          
          <div id="tabs2"  style="display:none">
           
          <form method="get" action="<?php echo get_admin_url(); ?>admin.php">
            <input type="hidden" value="user-reviews" name="page" />
            <input type="hidden" value="tabs2" name="active_tab" />
            <table width="100%" class="sitemile-table">
            	<tr>
                <td><?php _e('Search User','ProjectTheme'); ?></td>
                <td><input type="text" value="<?php echo $_GET['search_user']; ?>" name="search_user" size="20" /> <input type="submit" name="ProjectTheme_save2" value="<?php _e('Search','ProjectTheme'); ?>"/></td>
                </tr>
     
            
            </table>
            </form> 
          	
            <?php
		  
		  	$user = trim($_GET['search_user']);
			$user = get_userdatabylogin($user);
		  	$uid = $user->ID;
		  
		   	$s = "select * from ".$wpdb->prefix."project_ratings where touser='$uid' and awarded>0 order by id desc";
			$r = $wpdb->get_results($s);
		  	
			if(count($r) > 0):
		  
		  ?>
          
           <table class="widefat post fixed" cellspacing="0">
            <thead>
            <tr>
            <th><?php _e('Rated User','ProjectTheme'); ?></th>
            <th><?php _e('Project','ProjectTheme'); ?></th>
            <th><?php _e('Rating','ProjectTheme'); ?></th>
            <th><?php _e('Description','ProjectTheme'); ?></th>
            <th><?php _e('Awarded On','ProjectTheme'); ?></th>
            <th><?php _e('Options','ProjectTheme'); ?></th>
            </tr>
            </thead>
            
            
            
            <tbody>
            <?php
            
           
           
            foreach($r as $row)
            {
                $post = get_post($row->pid);
				$userdata = get_userdata($row->touser);
				$pid = $row->pid;
				
				echo '<tr>';
				echo '<th>'.$userdata->user_login.'</th>';
				echo '<th><a href="'.get_permalink($pid).'">'.$post->post_title.'</a></th>';
				echo '<th>'.($row->grade / 2).'</th>';
				echo '<th>'.$row->comment.'</th>';
				echo '<th>'.date('d-M-Y H:i:s', $row->datemade).'</th>';
				echo '<th>#</th>';
				echo '</tr>';
				
				
            }
            
            ?>
            </tbody>
            
            
            </table>
            <?php else: ?>
            
            <div class="padd101">
            <?php _e('There are no user feedback.','ProjectTheme'); ?>
            </div>
            
            <?php endif; ?>
            
            
          </div>
          
          <div id="tabs3">		
          </div>   
    
    
    <?php	
	
	echo '</div>';
}
/******************************************************************
*
*	Admin Menu - New Function - sitemile[at]sitemile.com
*	developed by Andrei Saioc - andreisaioc[at]gmail.com
*
*******************************************************************/

function ProjectTheme_get_current_user_role ($uid) {
 
    $current_user = get_userdata($uid);
    $user_roles = $current_user->roles;
    $user_role = array_shift($user_roles);
    return $user_role;
};

function projectTheme_usr_types_scr()
{
global $menu_admin_project_theme_bull;
	echo '<div class="wrap">';
	echo '<div class="icon32" id="icon-user-type-list"><br/></div>';	
	echo '<h2 class="my_title_class_sitemile">ProjectTheme User Types</h2>';
	?>
    
        <div id="usual2" class="usual"> 
  <ul> 
    <li><a href="#tabs1">All Users</a></li> 
    <li><a href="#tabs2">Search User</a></li> 
  </ul> 
  <div id="tabs1" style="display:none" >
    	
        
	<?php
	
	$rows_per_page = 10;
	
	if(isset($_GET['pj'])) $pageno = $_GET['pj'];
	else $pageno = 1;
	
	global $wpdb;

	$s1 = "select ID from ".$wpdb->users." order by user_login asc ";
	$s = "select * from ".$wpdb->users." order by user_login asc ";
	$limit = 'LIMIT ' .($pageno - 1) * $rows_per_page .',' .$rows_per_page;
	
	
	$r = $wpdb->get_results($s1); $nr = count($r);
	$lastpage      = ceil($nr/$rows_per_page);
	
	$r = $wpdb->get_results($s.$limit);

	if($nr > 0)	
	{
		
		?>
		
		
		        <table class="widefat post fixed" cellspacing="0">
    <thead>
    <tr>
    <th width="15%">Username</th>
    <th width="20%">Email</th>
    <th width="20%">Date Registered</th>
    <th width="13%" >User Type</th>
	<th >Options</th>
    </tr>
    </thead>
    
    <script>
	
	 $(document).ready(function() {
  
  	$('.update_btn*').click(function() {
	
	var id = $(this).attr('alt');
	var myRadio = $('input[name=user_tp_' + id + ']');
	var checkedValue = myRadio.filter(':checked').val();


	
	$.ajax({
   type: "POST",
   url: "<?php echo get_bloginfo('siteurl'); ?>/",
   data: "change_u_type=1&uid="+id+"&user_tp="+checkedValue,
   success: function(msg){
     
	 
	alert("User Level Changed")
	 
   }
 });
	
	});
  
  
 });
	
	
	</script>
    
    <tbody>
		
		
		<?php 
		
		
	foreach($r as $row)
	{
		$user = get_userdata($row->ID);
		
		
				
		$user_tp = ProjectTheme_get_current_user_role($row->ID);
		if($user_tp == "service_provider") $tp = "service_provider";
		else $tp = "business_owner";

		echo '<tr style="">';	
		echo '<th>'.$user->user_login.'</th>';
		echo '<th>'.$row->user_email .'</th>';
		echo '<th>'.$row->user_registered .'</th>';
		echo '<th class="'.$cl.'">';
		?>
		
        	<input type="radio" <?php if($tp == "service_provider") echo "checked='checked'";  ?>  size="4" value="service_provider" name="user_tp_<?php echo $row->ID; ?>" id="user_tp_<?php echo $row->ID; ?>" /> Provider <br/>
       		<input type="radio" <?php if($tp == "business_owner") echo "checked='checked'";  ?> size="4" value="business_owner" name="user_tp_<?php echo $row->ID; ?>" id="user_tp_<?php echo $row->ID; ?>" /> Contractor  
		
		<?php
		echo '</span></th>';
		echo '<th>';

		 
		?>
		
        
        
        <input type="button" value="Update" class="update_btn" alt="<?php echo $row->ID; ?>" />
        
        
        <?php
		echo '</th>';
	
		echo '</tr>';
	}
	
	
	?>



	</tbody>
    
    </table>
    
    <?php 
    
	for($i=1;$i<=$lastpage;$i++)
		{
			if($pageno == $i) echo $i." | ";
			else			
			echo '<a href="'.get_admin_url().'admin.php?page=user-types&pj='.$i.'"
			>'.$i.'</a> | ';	
			
		}
		
	}
    
    ?>
          </div> 
          <div id="tabs2" style="display:none"  >
          <form method="get" action="<?php echo get_admin_url(); ?>admin.php">
          <input type="hidden" name="page" value="user-types" />
          <input type="hidden" name="active_tab" value="tabs2" />
          Search User: <input type="text" size="35" value="<?php echo $_GET['src_usr']; ?>" name="src_usr" />
           <input type="submit" value="Submit" name="" />
          </form>
          
          <?php
		  if(!empty($_GET['src_usr'])):
		  
		  ?>
          
          <?php
	
	$rows_per_page = 10;
	
	if(isset($_GET['pj'])) $pageno = $_GET['pj'];
	else $pageno = 1;
	
	global $wpdb;
	$src_usr = $_GET['src_usr'];
	
	$s1 = "select ID from ".$wpdb->users." where user_login like '%$src_usr%' order by user_login asc ";
	$s = "select * from ".$wpdb->users." where user_login like '%$src_usr%' order by user_login asc ";
	$limit = 'LIMIT ' .($pageno - 1) * $rows_per_page .',' .$rows_per_page;
	
	
	$r = $wpdb->get_results($s1); $nr = count($r);
	$lastpage      = ceil($nr/$rows_per_page);
	
	$r = $wpdb->get_results($s.$limit);

	if($nr > 0)	
	{
		
		?>
		
		
		        <table class="widefat post fixed" cellspacing="0">
    <thead>
    <tr>
    <th width="15%">Username</th>
    <th width="20%">Email</th>
    <th width="20%">Date Registered</th>
    <th width="13%" >Cash Balance</th>
	<th >Options</th>
    </tr>
    </thead>
    
    <script>
	
	 $(document).ready(function() {
  
  	$('.update_btn*').click(function() {
	
	var id = $(this).attr('alt');
	var myRadio = $('input[name=user_tp_' + id + ']');
	var checkedValue = myRadio.filter(':checked').val();


	
	$.ajax({
   type: "POST",
   url: "<?php echo get_bloginfo('siteurl'); ?>/",
   data: "change_u_type=1&uid="+id+"&user_tp="+checkedValue,
   success: function(msg){
     
	 
	alert("User Level Changed")
	 
   }
 });
	
	});
  
  
 });
	
	
	</script>
    
    <tbody>
		
		
		<?php 
		
		
	foreach($r as $row)
	{
		$user = get_userdata($row->ID);
		
		$user_tp = get_user_meta($row->ID, 'user_tp', true);
		if($user_tp == "service_provider") $tp = "service_provider";
		else $tp = "business_owner";

		echo '<tr style="">';	
		echo '<th>'.$user->user_login.'</th>';
		echo '<th>'.$row->user_email .'</th>';
		echo '<th>'.$row->user_registered .'</th>';
		echo '<th class="'.$cl.'">';
		?>
        
		<input type="radio" <?php if($tp == "service_provider") echo "checked='checked'";  ?>  size="4" value="service_provider" name="user_tp_<?php echo $row->ID; ?>" id="user_tp_<?php echo $row->ID; ?>" /> Provider <br/>
       	<input type="radio" <?php if($tp == "business_owner") echo "checked='checked'";  ?> size="4" value="business_owner" name="user_tp_<?php echo $row->ID; ?>" id="user_tp_<?php echo $row->ID; ?>" /> Contractor  
		
        <?php
		echo '</span></th>';
		echo '<th>'; 
		?>
		
    	
        
        <input type="button" value="Update" class="update_btn" alt="<?php echo $row->ID; ?>" />
        
        
        <?php
		echo '</th>';
	
		echo '</tr>';
	}
	
	
	?>



	</tbody>
    
    </table>
    
    <?php 
    
	for($i=1;$i<=$lastpage;$i++)
		{
			if($pageno == $i) echo $i." | ";
			else			
			echo '<a href="'.get_admin_url().'admin.php?active_tab=tabs2&src_usr='.$_GET['src_usr'].'&page=user-types&pj='.$i.'"
			>'.$i.'</a> | ';	
			
		}
		
	}
    
    ?>
          
          
          <?php endif; ?>
          
          </div> 
        </div> 
    
    
    <?php	
	
	echo '</div>';	
	
}



function project_theme_user_balances()
{
	global $menu_admin_project_theme_bull;
	echo '<div class="wrap">';
	echo '<div class="icon32" id="icon-options-general-bal"><br/></div>';	
	echo '<h2 class="my_title_class_sitemile">ProjectTheme User Balances</h2>';
	?>
    
        <div id="usual2" class="usual"> 
  <ul> 
    <li><a href="#tabs1" >All Users</a></li> 
    <li><a href="#tabs2">Search User</a></li> 
  </ul> 
  <div id="tabs1" style="display: none; ">
    	
        
	<?php
	
	$rows_per_page = 10;
	
	if(isset($_GET['pj'])) $pageno = $_GET['pj'];
	else $pageno = 1;
	
	global $wpdb;

	$s1 = "select ID from ".$wpdb->users." order by user_login asc ";
	$s = "select * from ".$wpdb->users." order by user_login asc ";
	$limit = 'LIMIT ' .($pageno - 1) * $rows_per_page .',' .$rows_per_page;
	
	
	$r = $wpdb->get_results($s1); $nr = count($r);
	$lastpage      = ceil($nr/$rows_per_page);
	
	$r = $wpdb->get_results($s.$limit);

	if($nr > 0)	
	{
		
		?>
		
		
		        <table class="widefat post fixed" cellspacing="0">
    <thead>
    <tr>
    <th width="15%">Username</th>
    <th width="20%">Email</th>
    <th width="20%">Date Registered</th>
    <th width="13%" >Cash Balance</th>
	<th >Options</th>
    </tr>
    </thead>
    
    <script>
	
	 $(document).ready(function() {
  
  	$('.update_btn*').click(function() {
	
	var id = $(this).attr('alt');
	var increase_credits = $('#increase_credits' + id).val();
	var decrease_credits = $('#decrease_credits' + id).val();
	
	$.ajax({
   type: "POST",
   url: "<?php echo get_bloginfo('siteurl'); ?>/",
   data: "crds=1&uid="+id+"&increase_credits="+increase_credits+"&decrease_credits="+decrease_credits,
   success: function(msg){
     
	 
	$("#money" + id).html(msg);
	$('#increase_credits' + id).val("");
	$('#decrease_credits' + id).val(""); 
	 
   }
 });
	
	});
  
  
 });
	
	
	</script>
    
    <tbody>
		
		
		<?php 
		
		
	foreach($r as $row)
	{
		$user = get_userdata($row->ID);
		

		echo '<tr style="">';	
		echo '<th>'.$user->user_login.'</th>';
		echo '<th>'.$row->user_email .'</th>';
		echo '<th>'.$row->user_registered .'</th>';
		echo '<th class="'.$cl.'"><span id="money'.$row->ID.'">'.$sign. ProjectTheme_get_show_price(projectTheme_get_credits($row->ID)) .'</span></th>';
		echo '<th>'; 
		?>
		
        Increase Credits: <input type="text" size="4" id="increase_credits<?php echo $row->ID; ?>" rel="<?php echo $row->ID; ?>" /> <?php echo projectTheme_currency(); ?><br/>
        Decrease Credits: <input type="text" size="4" id="decrease_credits<?php echo $row->ID; ?>" rel="<?php echo $row->ID; ?>" /> <?php echo projectTheme_currency(); ?><br/>
        
        <input type="button" value="Update" class="update_btn" alt="<?php echo $row->ID; ?>" />
        
        
        <?php
		echo '</th>';
	
		echo '</tr>';
	}
	
	
	?>



	</tbody>
    
    </table>
    
    <?php 
    
	for($i=1;$i<=$lastpage;$i++)
		{
			if($pageno == $i) echo $i." | ";
			else			
			echo '<a href="'.get_admin_url().'admin.php?page=User-Balances&pj='.$i.'"
			>'.$i.'</a> | ';	
			
		}
		
	}
    
    ?>
          </div> 
          <div id="tabs2"  style="display: none; "  >
          <form method="get" action="<?php echo get_admin_url(); ?>admin.php">
          <input type="hidden" name="page" value="User-Balances" />
          <input type="hidden" name="active_tab" value="tabs2" />
          Search User: <input type="text" size="35" value="<?php echo $_GET['src_usr']; ?>" name="src_usr" />
           <input type="submit" value="Submit" name="" />
          </form>
          
          <?php
		  if(!empty($_GET['src_usr'])):
		  
		  ?>
          
          <?php
	
	$rows_per_page = 10;
	
	if(isset($_GET['pj'])) $pageno = $_GET['pj'];
	else $pageno = 1;
	
	global $wpdb;
	$src_usr = $_GET['src_usr'];
	
	$s1 = "select ID from ".$wpdb->users." where user_login like '%$src_usr%' order by user_login asc ";
	$s = "select * from ".$wpdb->users." where user_login like '%$src_usr%' order by user_login asc ";
	$limit = 'LIMIT ' .($pageno - 1) * $rows_per_page .',' .$rows_per_page;
	
	
	$r = $wpdb->get_results($s1); $nr = count($r);
	$lastpage      = ceil($nr/$rows_per_page);
	
	$r = $wpdb->get_results($s.$limit);

	if($nr > 0)	
	{
		
		?>
		
		
		        <table class="widefat post fixed" cellspacing="0">
    <thead>
    <tr>
    <th width="15%">Username</th>
    <th width="20%">Email</th>
    <th width="20%">Date Registered</th>
    <th width="13%" >Cash Balance</th>
	<th >Options</th>
    </tr>
    </thead>
    
    <script>
	
	 $(document).ready(function() {
  
  	$('.update_btn*').click(function() {
	
	var id = $(this).attr('alt');
	var increase_credits = $('#increase_credits' + id).val();
	var decrease_credits = $('#decrease_credits' + id).val();
	
	$.ajax({
   type: "POST",
   url: "<?php echo get_bloginfo('siteurl'); ?>/",
   data: "crds=1&uid="+id+"&increase_credits="+increase_credits+"&decrease_credits="+decrease_credits,
   success: function(msg){
     
	 
	$("#money" + id).html(msg);
	$('#increase_credits' + id).val("");
	$('#decrease_credits' + id).val(""); 
	 
   }
 });
	
	});
  
  
 });
	
	
	</script>
    
    <tbody>
		
		
		<?php 
		
		
	foreach($r as $row)
	{
		$user = get_userdata($row->ID);
		

		echo '<tr style="">';	
		echo '<th>'.$user->user_login.'</th>';
		echo '<th>'.$row->user_email .'</th>';
		echo '<th>'.$row->user_registered .'</th>';
		echo '<th class="'.$cl.'"><span id="money'.$row->ID.'">'.$sign. ProjectTheme_get_show_price(projectTheme_get_credits($row->ID)) .'</span></th>';
		echo '<th>'; 
		?>
		
        Increase Credits: <input type="text" size="4" id="increase_credits<?php echo $row->ID; ?>" rel="<?php echo $row->ID; ?>" /> <?php echo projectTheme_currency(); ?><br/>
        Decrease Credits: <input type="text" size="4" id="decrease_credits<?php echo $row->ID; ?>" rel="<?php echo $row->ID; ?>" /> <?php echo projectTheme_currency(); ?><br/>
        
        <input type="button" value="Update" class="update_btn" alt="<?php echo $row->ID; ?>" />
        
        
        <?php
		echo '</th>';
	
		echo '</tr>';
	}
	
	
	?>



	</tbody>
    
    </table>
    
    <?php 
    
	for($i=1;$i<=$lastpage;$i++)
		{
			if($pageno == $i) echo $i." | ";
			else			
			echo '<a href="'.get_admin_url().'admin.php?active_tab=tabs2&src_usr='.$_GET['src_usr'].'&page=User-Balances&pj='.$i.'"
			>'.$i.'</a> | ';	
			
		}
		
	}
    

    ?>
          
          
          <?php endif; ?>
          
          </div> 
        </div> 
    
    
    <?php	
	
	echo '</div>';
}


/******************************************************************
*
*	Admin Menu - New Function - sitemile[at]sitemile.com
*	developed by Andrei Saioc - andreisaioc[at]gmail.com
*
*******************************************************************/

function projectTheme_theme_escrows()
{
	global $menu_admin_project_theme_bull;
	echo '<div class="wrap">';
	echo '<div class="icon32" id="icon-options-general-vault"><br/></div>';	
	echo '<h2 class="my_title_class_sitemile">ProjectTheme Escrows</h2>';

//----------------------------------------------------------------------

    global $wpdb;
	if(isset($_GET['release']))
	{
		
		$id = $_GET['release'];
						
						$s = "select * from ".$wpdb->prefix."project_escrow where id='$id'";
						$row = $wpdb->get_results($s); //mysql_query($s);
						
						if(count($row) == 1)
						{
							$row = $row[0];
							$amount = $row->amount;
							$toid = $row->toid;
							$fromid = $row->fromid;
							
							$projectTheme_fee_after_paid = get_option('projectTheme_fee_after_paid');
							if(!empty($projectTheme_fee_after_paid)):
							
								$deducted = $amount*($projectTheme_fee_after_paid * 0.01);
							else: 
							
								$deducted = 0;
							
							endif;
							
							//--------------
							
							$fromuser = get_userdata($fromid);
							
							$cr = projectTheme_get_credits($toid);
							projectTheme_update_credits($toid, $cr + $amount - $deducted);
							
							$reason = sprintf(__('Payment received from %s','ProjectTheme'), $fromuser->user_login);
							projectTheme_add_history_log('1', $reason, $amount, $toid, $fromid);
							
							if($deducted > 0)
							$reason = sprintf(__('Payment fee for project %s','ProjectTheme'), $my_pst->post_title);
							projectTheme_add_history_log('0', $reason, $deducted, $toid );
							
					
							//-----------------------------
							$email 		= get_bloginfo('admin_email');
							$site_name 	= get_bloginfo('name');
							
							$usr = get_userdata($fromid);
							
							$subject = __("Money Escrow Completed",'ProjectTheme');
							$message = sprintf(__("You have released the escrow of: %s", 'ProjectTheme'), projectTheme_get_show_price($amount));
	
						//	sitemile_send_email($usr->user_email, $subject , $message);
							
							//-----------------------------
							
							$usr = get_userdata($toid);
							
							$reason = 'Payment sent to '.$usr->user_login;

							projectTheme_add_history_log('0', $reason, $amount, $fromid, $toid);
							
							$subject = sprintf(__("Money Escrow Completed",'ProjectTheme'));
							$message = sprintf(__("You have received the amount of: %s",'ProjectTheme'), projectTheme_get_show_price($amount));
	
							//sitemile_send_email($usr->user_email, $subject , $message);
							
							//-----------------------------
							
							$tm = current_time('timestamp',0);
							$s = "update ".$wpdb->prefix."project_escrow set released='1', releasedate='$tm' where
							id='$id'";
							$wpdb->query($s);
						
							echo '<div class="saved_thing">'.__('Escrow completed!','ProjectTheme'). '</div>';	
						}
		
		
	}
	
	if(isset($_GET['close']))
	{
		
		$id = $_GET['close'];
		
		$s = "select * from ".$wpdb->prefix."project_escrow where id='$id'";
		$row = $wpdb->get_results($s);
						
						if(count($row) == 1)
						{							
							$row = $row[0];
							$amount = $row->amount;
							$fromid = $row->fromid;
							
							$cr = projectTheme_get_credits($fromid);
							projectTheme_update_credits($fromid, $cr + $amount);
							
						}
		
		
		$s = "delete from ".$wpdb->prefix."project_escrow where	id='$id'";
		$wpdb->query($s);
							
		echo '<div class="saved_thing">'.__('Escrow closed!','ProjectTheme'). '</div>';	
		
	} ?>
    
    
        <div id="usual2" class="usual"> 
  <ul> 
    <li><a href="#tabs1" class="selected">Open Escrows</a></li> 
    <li><a href="#tabs2">Completed Escrows</a></li> 
  </ul> 
  <div id="tabs1" style="display: block; ">
    <?php
	
		$s = "select * from ".$wpdb->prefix."project_escrow where released='0' order by id desc";
		$r = $wpdb->get_results($s);
		
		if(count($r) > 0):
	?>
    
    	     <table class="widefat post fixed" cellspacing="0">
    <thead>
    <tr>
    <th width="10%">From User</th>
    <th width="10%">To User</th>
    <th width="10%">Project</th>
    <th>Date Made</th>
    <th >Amount</th>
	<th >Options</th>
    </tr>
    </thead>
    
    
    
    <tbody>


	<?php
	

	
	foreach($r as $row)
	{
		$user1 = get_userdata($row->fromid);
		$user2 = get_userdata($row->toid);
		$post  = get_post($row->pid); 
		
		echo '<tr>';	
		echo '<th>'.$user1->user_login.'</th>';
		echo '<th>'.$user2->user_login .'</th>';
		echo '<th><a href="'.get_permalink($row->pid).'">'.$post->post_title .'</a></th>';
		echo '<th>'.date('d-M-Y H:i:s',$row->datemade) .'</th>';
		echo '<th>'.projectTheme_get_show_price($row->amount) .'</th>';
		echo '<th><a href="'.get_admin_url().'admin.php?page=Escrows&release='.$row->id.'" class="awesome">Release</a> | <a href="'.get_admin_url().'admin.php?page=Escrows&close='.$row->id.'" class="trash">Close</a> </th>';
	
	
		echo '</tr>';
	}
	
	?>



	</tbody>
    
    
    </table>
    
    <?php else: ?>
    
    There are no results.
    
    <?php endif; ?>
        
          </div> 
          <div id="tabs2" style="display: none; ">
          
           <?php
	
		$s = "select * from ".$wpdb->prefix."project_escrow where released='1' order by id desc";
		$r = $wpdb->get_results($s);
		
		if(count($r) > 0):
	?>
    
    	     <table class="widefat post fixed" cellspacing="0">
    <thead>
    <tr>
    <th width="10%">From User</th>
    <th width="10%">To User</th>
    <th width="10%">Project</th>
    <th>Date Made</th>
    <th >Amount</th>
	<th >Options</th>
    </tr>
    </thead>
    
    
    
    <tbody>


	<?php
	

	
	foreach($r as $row)
	{
		$user1 = get_userdata($row->fromid);
		$user2 = get_userdata($row->toid);
		$post  = get_post($row->pid); 
		
		echo '<tr>';	
		echo '<th>'.$user1->user_login.'</th>';
		echo '<th>'.$user2->user_login .'</th>';
		echo '<th><a href="'.get_permalink($row->pid).'">'.$post->post_title .'</a></th>';
		echo '<th>'.date('d-M-Y H:i:s',$row->datemade) .'</th>';
		echo '<th>'.projectTheme_get_show_price($row->amount) .'</th>';
		echo '<th>#</th>';
	
	
		echo '</tr>';
	}
	
	?>



	</tbody>
    
    
    </table>
    
    <?php else: ?>
    
    There are no results.
    
    <?php endif; ?>
        
          
          
          </div> 
        </div> 
    
    
    <?php	
	
	echo '</div>';
}


/******************************************************************
*
*	Admin Menu - New Function - sitemile[at]sitemile.com
*	developed by Andrei Saioc - andreisaioc[at]gmail.com
*
*******************************************************************/

function projectTheme_theme_withdrawals()
{
	global $menu_admin_project_theme_bull, $wpdb;
	echo '<div class="wrap">';
	echo '<div class="icon32" id="icon-options-general-withdr"><br/></div>';	
	echo '<h2 class="my_title_class_sitemile">ProjectTheme Withdrawals</h2>';
	
	
	if(isset($_GET['tid']))
	{
		$tm = current_time('timestamp',0);
		$ids = $_GET['tid'];
		
		$s = "select * from ".$wpdb->prefix."project_withdraw where id='$ids'";
		$row = $wpdb->get_results($s);
		$row = $row[0];

		
		if($row->done == 0)
		{
			echo '<div class="saved_thing">Payment completed!</div>';
			$ss = "update ".$wpdb->prefix."project_withdraw set done='1', datedone='$tm' where id='$ids'";
			$wpdb->query($ss);// or die(mysql_error());
			
			
			$usr = get_userdata($row->uid);
			
			$site_name 		= get_bloginfo('name');
			$email		 	= get_bloginfo('admin_email');
			
			$subject = sprintf(__("Your withdrawal has been completed: %s",'ProjectTheme'), projectTheme_get_show_price($row->amount));
			$message = sprintf(__("Your withdrawal has been completed: %s",'ProjectTheme'), projectTheme_get_show_price($row->amount));
			
			//sitemile_send_email($usr->user_email, $subject , $message);
	
			
			$reason = sprintf(__('Withdraw to PayPal to email: %s','ProjectTheme') ,$row->payeremail);
			projectTheme_add_history_log('0', $reason, $row->amount, $usr->ID);
		}
	}
	
	?>
    
        <div id="usual2" class="usual"> 
  <ul> 
    <ul> 
            <li><a href="#tabs1"><?php _e('Unresolved Requests','ProjectTheme'); ?></a></li> 
            <li><a href="#tabs2"><?php _e('Resolved Requests','ProjectTheme'); ?></a></li> 
            <li><a href="#tabs_rejected"><?php _e('Rejected Requests','ProjectTheme'); ?></a></li> 
            <li><a href="#tabs3"><?php _e('Search Unresolved','ProjectTheme'); ?></a></li> 
            <li><a href="#tabs4"><?php _e('Search Solved','ProjectTheme'); ?></a></li> 
            <li><a href="#tabs_search_rejected"><?php _e('Search Rejected','ProjectTheme'); ?></a></li> 
          </ul> 
  </ul> 
  <div id="tabs1">
          <?php
		  
		   $s = "select * from ".$wpdb->prefix."project_withdraw where done='0' order by id desc";
           $r = $wpdb->get_results($s);
		  	
			if(count($r) > 0):
		  
		  ?>
          
           <table class="widefat post fixed" cellspacing="0">
            <thead>
            <tr>
            <th width="12%" ><?php _e('Username','ProjectTheme'); ?></th>
            <th><?php _e('Method','ProjectTheme'); ?></th>
            <th width="20%"><?php _e('Details','ProjectTheme'); ?></th>
            <th><?php _e('Date Requested','ProjectTheme'); ?></th>
            <th ><?php _e('Amount','ProjectTheme'); ?></th>
            <th width="25%"><?php _e('Options','ProjectTheme'); ?></th>
            </tr>
            </thead>
            
            
            
            <tbody>
            <?php
            
           
            
            foreach($r as $row)
            {
                $user = get_userdata($row->uid);
                
                echo '<tr>';	
                echo '<th>'.$user->user_login.'</th>';
                echo '<th>'.$row->methods .'</th>';
				 echo '<th>'.$row->payeremail .'</th>';
                echo '<th>'.date('d-M-Y H:i:s',$row->datemade) .'</th>';
                echo '<th>'.ProjectTheme_get_show_price($row->amount) .'</th>';
                echo '<th>'.($row->done == 0 ? '<a href="'.get_admin_url().'admin.php?page=Withdrawals&active_tab=tabs1&tid='.$row->id.'" class="awesome">'.
                __('Make Complete','ProjectTheme').'</a>' . ' | ' . '<a href="'.get_admin_url().'admin.php?page=Withdrawals&den_id='.$row->id.'" class="awesome">'.
                __('Deny Request','ProjectTheme').'</a>' :( $row->done == 1 ? __("Completed",'ProjectTheme') : __("Rejected",'ProjectTheme') ) ).'</th>';
                echo '</tr>';
            }
            
            ?>
            </tbody>
            
            
            </table>
            <?php else: ?>
            
            <div class="padd101">
            <?php _e('There are no unresolved withdrawal requests.','ProjectTheme'); ?>
            </div>
            
            <?php endif; ?>
          
          	
          </div>
          
          <div id="tabs2">	
          
          
          <?php
		  
		   $s = "select * from ".$wpdb->prefix."project_withdraw where done='1' order by id desc";
           $r = $wpdb->get_results($s);
		  	

			if(count($r) > 0):
		  
		  ?>
          
           <table class="widefat post fixed" cellspacing="0">
            <thead>
            <tr>
            <th ><?php 	_e('Username','ProjectTheme'); ?></th>
            <th><?php 	_e('Method','ProjectTheme'); ?></th>
            <th><?php 	_e('Details','ProjectTheme'); ?></th>
            <th><?php 	_e('Date Requested','ProjectTheme'); ?></th>
            <th ><?php 	_e('Amount','ProjectTheme'); ?></th>
            <th><?php 	_e('Date Released','ProjectTheme'); ?></th>
            <th><?php 	_e('Options','ProjectTheme'); ?></th>
            </tr>
            </thead>
            
            
            
            <tbody>
            <?php
            
           
            
            foreach($r as $row)
            {
                $user = get_userdata($row->uid);
                
                echo '<tr>';	
                echo '<th>'.$user->user_login.'</th>';
				echo '<th>'.$user->methods.'</th>';
                echo '<th>'.$row->payeremail .'</th>';
                echo '<th>'.date('d-M-Y H:i:s',$row->datemade) .'</th>';
                echo '<th>'.ProjectTheme_get_show_price($row->amount) .'</th>';
                echo '<th>'.($row->datedone == 0 ? "Not yet" : date('d-M-Y H:i:s',$row->datedone)) .'</th>';
                echo '<th>'.($row->done == 0 ? '<a href="'.get_admin_url().'admin.php?page=Withdrawals&active_tab=tabs1&tid='.$row->id.'" class="awesome">'.
                __('Make Complete','ProjectTheme').'</a>' . ' | ' . '<a href="'.get_admin_url().'admin.php?page=Withdrawals&den_id='.$row->id.'" class="awesome">'.
                __('Deny Request','ProjectTheme').'</a>' :( $row->done == 1 ? __("Completed",'ProjectTheme') : __("Rejected",'ProjectTheme') ) ).'</th>';
                echo '</tr>';
            }
            
            ?>
            </tbody>
            
            
            </table>
            <?php else: ?>
            
            <div class="padd101">
            <?php _e('There are no resolved withdrawal requests.','ProjectTheme'); ?>
            </div>
            
            <?php endif; ?>
          
          
          </div>
          
          <div id="tabs_rejected">	
          
          
          <?php
		  
		   $s = "select * from ".$wpdb->prefix."project_withdraw where rejected='1' order by id desc";
           $r = $wpdb->get_results($s);
		  	
			if(count($r) > 0):
		  
		  ?>
          
           <table class="widefat post fixed" cellspacing="0">
            <thead>
            <tr>
            <th ><?php _e('Username','ProjectTheme'); ?></th>
            <th><?php _e('Details','ProjectTheme'); ?></th>
            <th><?php _e('Date Requested','ProjectTheme'); ?></th>
            <th ><?php _e('Amount','ProjectTheme'); ?></th>
            <th><?php _e('Date Released','ProjectTheme'); ?></th>
            <th><?php _e('Options','ProjectTheme'); ?></th>
            </tr>
            </thead>
            
            
            
            <tbody>
            <?php
            
           
            
            foreach($r as $row)
            {
                $user = get_userdata($row->uid);
                
                echo '<tr>';	
                echo '<th>'.$user->user_login.'</th>';
                echo '<th>'.$row->payeremail .'</th>';
                echo '<th>'.date('d-M-Y H:i:s',$row->datemade) .'</th>';
                echo '<th>'.ProjectTheme_get_show_price($row->amount) .'</th>';
                echo '<th>'.($row->datedone == 0 ? "Not yet" : date('d-M-Y H:i:s',$row->datedone)) .'</th>';
                echo '<th>'.($row->done == 0 ? '<a href="'.get_admin_url().'admin.php?page=Withdrawals&active_tab=tabs1&tid='.$row->id.'" class="awesome">'.
                __('Make Complete','ProjectTheme').'</a>' . ' | ' . '<a href="'.get_admin_url().'admin.php?page=Withdrawals&den_id='.$row->id.'" class="awesome">'.
                __('Deny Request','ProjectTheme').'</a>' :( $row->done == 1 ? __("Completed",'ProjectTheme') : __("Rejected",'ProjectTheme') ) ).'</th>';
                echo '</tr>';
            }
            
            ?>
            </tbody>
            
            
            </table>
            <?php else: ?>
            
            <div class="padd101">
            <?php _e('There are no rejected withdrawal requests.','ProjectTheme'); ?>
            </div>
            
            <?php endif; ?>
          
          
          </div>
          
          
          <div id="tabs3">
          
          <form method="get" action="<?php echo get_admin_url(); ?>admin.php">
            <input type="hidden" value="Withdrawals" name="page" />
            <input type="hidden" value="tabs3" name="active_tab" />
            <table width="100%" class="sitemile-table">
            	<tr>
                <td><?php _e('Search User','ProjectTheme'); ?></td>
                <td><input type="text" value="<?php echo $_GET['search_user']; ?>" name="search_user" size="20" /> <input type="submit" name="ProjectTheme_save3" value="<?php _e('Search','ProjectTheme'); ?>"/></td>
                </tr>
     
            
            </table>
            </form> 
            
            <?php
			
			if(isset($_GET['ProjectTheme_save3'])):
				
				$search_user = trim($_GET['search_user']);
				
				$user 	= get_userdatabylogin($search_user);
				$uid 	= $user->ID;
				
				$s = "select * from ".$wpdb->prefix."project_withdraw where done='0' AND uid='$uid' order by id desc";
           $r = $wpdb->get_results($s);
		  	
			if(count($r) > 0):
		  
		  ?>
          
           <table class="widefat post fixed" cellspacing="0">
            <thead>
            <tr>
            <th width="12%" ><?php _e('Username','ProjectTheme'); ?></th>
            <th><?php _e('Method','ProjectTheme'); ?></th>
            <th width="20%"><?php _e('Details','ProjectTheme'); ?></th>
            <th><?php _e('Date Requested','ProjectTheme'); ?></th>
            <th ><?php _e('Amount','ProjectTheme'); ?></th>
            <th width="25%"><?php _e('Options','ProjectTheme'); ?></th>
            </tr>
            </thead>
            
            
            
            <tbody>
            <?php
            
           
            
            foreach($r as $row)
            {
                $user = get_userdata($row->uid);
                
                echo '<tr>';	
                echo '<th>'.$user->user_login.'</th>';
                echo '<th>'.$row->methods .'</th>';
				 echo '<th>'.$row->payeremail .'</th>';
                echo '<th>'.date('d-M-Y H:i:s',$row->datemade) .'</th>';
                echo '<th>'.ProjectTheme_get_show_price($row->amount) .'</th>';
                echo '<th>'.($row->done == 0 ? '<a href="'.get_admin_url().'admin.php?page=Withdrawals&active_tab=tabs1&tid='.$row->id.'" class="awesome">'.
                __('Make Complete','ProjectTheme').'</a>' . ' | ' . '<a href="'.get_admin_url().'admin.php?page=Withdrawals&den_id='.$row->id.'" class="awesome">'.
                __('Deny Request','ProjectTheme').'</a>' :( $row->done == 1 ? __("Completed",'ProjectTheme') : __("Rejected",'ProjectTheme') ) ).'</th>';
                echo '</tr>';
            }
            
            ?>
            </tbody>
            
            
            </table>
            <?php else: ?>
            
            <div class="padd101">
            <?php _e('There are no results for your search.','ProjectTheme'); ?>
            </div>
            
            <?php endif; 
				
			
			endif;
			
			?>
            
          		
          </div> 
          
          <div id="tabs4">	
          
          <form method="get" action="<?php echo get_admin_url(); ?>admin.php">
            <input type="hidden" value="Withdrawals" name="page" />
            <input type="hidden" value="tabs4" name="active_tab" />
            <table width="100%" class="sitemile-table">
            	<tr>
                <td><?php _e('Search User','ProjectTheme'); ?></td>
                <td><input type="text" value="<?php echo $_GET['search_user4']; ?>" name="search_user4" size="20" /> <input type="submit" name="ProjectTheme_save4" value="<?php _e('Search','ProjectTheme'); ?>"/></td>
                </tr>
     
            
            </table>
            </form> 
          	
             
            <?php
			
			if(isset($_GET['ProjectTheme_save4'])):
				
				$search_user = trim($_GET['search_user4']);
				
				$user 	= get_userdatabylogin($search_user);
				$uid 	= $user->ID;
				
				$s = "select * from ".$wpdb->prefix."project_withdraw where done='1' AND uid='$uid' order by id desc";
           $r = $wpdb->get_results($s);
		  	
			if(count($r) > 0):
		  
		  ?>
          
           <table class="widefat post fixed" cellspacing="0">
            <thead>
            <tr>
            <th width="12%" ><?php _e('Username','ProjectTheme'); ?></th>
            <th><?php _e('Method','ProjectTheme'); ?></th>
            <th width="20%"><?php _e('Details','ProjectTheme'); ?></th>
            <th><?php _e('Date Requested','ProjectTheme'); ?></th>
            <th ><?php _e('Amount','ProjectTheme'); ?></th>
            <th width="25%"><?php _e('Options','ProjectTheme'); ?></th>
            </tr>
            </thead>
            
            
            
            <tbody>
            <?php
            
           
            
            foreach($r as $row)
            {
                $user = get_userdata($row->uid);
                
                echo '<tr>';	
                echo '<th>'.$user->user_login.'</th>';
                echo '<th>'.$row->methods .'</th>';
				 echo '<th>'.$row->payeremail .'</th>';
                echo '<th>'.date('d-M-Y H:i:s',$row->datemade) .'</th>';
                echo '<th>'.ProjectTheme_get_show_price($row->amount) .'</th>';
                echo '<th>'.($row->done == 0 ? '<a href="'.get_admin_url().'admin.php?page=Withdrawals&active_tab=tabs1&tid='.$row->id.'" class="awesome">'.
                __('Make Complete','ProjectTheme').'</a>' . ' | ' . '<a href="'.get_admin_url().'admin.php?page=Withdrawals&den_id='.$row->id.'" class="awesome">'.
                __('Deny Request','ProjectTheme').'</a>' :( $row->done == 1 ? __("Completed",'ProjectTheme') : __("Rejected",'ProjectTheme') ) ).'</th>';
                echo '</tr>';
            }
            
            ?>
            </tbody>
            
            
            </table>
            <?php else: ?>
            
            <div class="padd101">
            <?php _e('There are no results for your search.','ProjectTheme'); ?>
            </div>
            
            <?php endif; 
				
			
			endif;
			
			?>
            
            </div>
          
          
          <div id="tabs_search_rejected">	
          
          <form method="get" action="<?php echo get_admin_url(); ?>admin.php">
            <input type="hidden" value="Withdrawals" name="page" />
            <input type="hidden" value="tabs_search_rejected" name="active_tab" />
            <table width="100%" class="sitemile-table">
            	<tr>
                <td><?php _e('Search User','ProjectTheme'); ?></td>
                <td><input type="text" value="<?php echo $_GET['search_user5']; ?>" name="search_user5" size="20" /> <input type="submit" name="ProjectTheme_save5" value="<?php _e('Search','ProjectTheme'); ?>"/></td>
                </tr>
     
            
            </table>
            </form> 
          	
            
             <?php
			
			if(isset($_GET['ProjectTheme_save5'])):
				
				$search_user = trim($_GET['search_user5']);
				
				$user 	= get_userdatabylogin($search_user);
				$uid 	= $user->ID;
				
				$s = "select * from ".$wpdb->prefix."project_withdraw where rejected='1' AND uid='$uid' order by id desc";
           $r = $wpdb->get_results($s);
		  	
			if(count($r) > 0):
		  
		  ?>
          
           <table class="widefat post fixed" cellspacing="0">
            <thead>
            <tr>
            <th width="12%" ><?php _e('Username','ProjectTheme'); ?></th>
            <th><?php _e('Method','ProjectTheme'); ?></th>
            <th width="20%"><?php _e('Details','ProjectTheme'); ?></th>
            <th><?php _e('Date Requested','ProjectTheme'); ?></th>
            <th ><?php _e('Amount','ProjectTheme'); ?></th>
            <th width="25%"><?php _e('Options','ProjectTheme'); ?></th>
            </tr>
            </thead>
            
            
            
            <tbody>
            <?php
            
           
            
            foreach($r as $row)
            {
                $user = get_userdata($row->uid);
                
                echo '<tr>';	
                echo '<th>'.$user->user_login.'</th>';
                echo '<th>'.$row->methods .'</th>';
				 echo '<th>'.$row->payeremail .'</th>';
                echo '<th>'.date('d-M-Y H:i:s',$row->datemade) .'</th>';
                echo '<th>'.ProjectTheme_get_show_price($row->amount) .'</th>';
                echo '<th>'.($row->done == 0 ? '<a href="'.get_admin_url().'admin.php?page=Withdrawals&active_tab=tabs1&tid='.$row->id.'" class="awesome">'.
                __('Make Complete','ProjectTheme').'</a>' . ' | ' . '<a href="'.get_admin_url().'admin.php?page=Withdrawals&den_id='.$row->id.'" class="awesome">'.
                __('Deny Request','ProjectTheme').'</a>' :( $row->done == 1 ? __("Completed",'ProjectTheme') : __("Rejected",'ProjectTheme') ) ).'</th>';
                echo '</tr>';
            }
            
            ?>
            </tbody>
            
            
            </table>
            <?php else: ?>
            
            <div class="padd101">
            <?php _e('There are no results for your search.','ProjectTheme'); ?>
            </div>
            
            <?php endif; 
				
			
			endif;
			
			?>
            
          </div> 
          
          
          

<?php
	echo '</div>';		
}

/******************************************************************
*
*	Admin Menu - New Function - sitemile[at]sitemile.com
*	developed by Andrei Saioc - andreisaioc[at]gmail.com
*
*******************************************************************/

function projectTheme_payment_methods()
{
		$id_icon 		= 'icon-options-general4';
	$ttl_of_stuff 	= 'ProjectTheme - Payment Methods';
	global $menu_admin_ProjectTheme_theme_bull;
	$arr = array("yes" => __("Yes",'ProjectTheme'), "no" => __("No",'ProjectTheme'));
	
	//------------------------------------------------------
	
	echo '<div class="wrap">';
	echo '<div class="icon32" id="'.$id_icon.'"><br/></div>';	
	echo '<h2 class="my_title_class_sitemile">'.$ttl_of_stuff.'</h2>';	
	
	//--------------------------
	
	do_action('ProjectTheme_payment_methods_action');
	if(isset($_POST['ProjectTheme_save1']))
	{
		update_option('ProjectTheme_paypal_enable', 		trim($_POST['ProjectTheme_paypal_enable']));
		update_option('ProjectTheme_paypal_email', 			trim($_POST['ProjectTheme_paypal_email']));
		update_option('ProjectTheme_paypal_enable_sdbx', 	trim($_POST['ProjectTheme_paypal_enable_sdbx']));
		
		update_option('projectTheme_enable_paypal_ad', 		trim($_POST['projectTheme_enable_paypal_ad']));
		update_option('project_theme_signature', 			trim($_POST['project_theme_signature']));
		update_option('project_theme_apipass', 				trim($_POST['project_theme_apipass']));
		update_option('project_theme_apiuser', 				trim($_POST['project_theme_apiuser']));
		update_option('project_theme_appid', 				trim($_POST['project_theme_appid']));

		
		echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';		
	}
	
	if(isset($_POST['ProjectTheme_save2']))
	{
		update_option('ProjectTheme_alertpay_enable', trim($_POST['ProjectTheme_alertpay_enable']));
		update_option('ProjectTheme_alertpay_email', trim($_POST['ProjectTheme_alertpay_email']));
		
		echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';		
	}
	
	if(isset($_POST['ProjectTheme_save3']))
	{
		update_option('ProjectTheme_moneybookers_enable', trim($_POST['ProjectTheme_moneybookers_enable']));
		update_option('ProjectTheme_moneybookers_email', trim($_POST['ProjectTheme_moneybookers_email']));
		
		echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';		
	}
	
	if(isset($_POST['ProjectTheme_save4']))
	{
		update_option('ProjectTheme_ideal_enable', trim($_POST['ProjectTheme_ideal_enable']));
		update_option('ProjectTheme_ideal_email', trim($_POST['ProjectTheme_ideal_email']));
		
		echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';		
	}
	
	
	if(isset($_POST['ProjectTheme_save_bnk']))
	{
		update_option('ProjectTheme_bank_details_enable', 	trim($_POST['ProjectTheme_bank_details_enable']));
		update_option('ProjectTheme_bank_details_txt', 		trim($_POST['ProjectTheme_bank_details_txt']));
		
		echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';		
	}
	
	
	
	?>


	    <div id="usual2" class="usual"> 
          <ul> 
            <li><a href="#tabs1">PayPal</a></li> 
            <li><a href="#tabs2">Payza/AlertPay</a></li> 
            <li><a href="#tabs3">Moneybookers/Skrill</a></li> 
            <!--<li><a href="#tabs4">iDeal</a></li> 
            <li><a href="#tabs5">PayFast</a></li>
            <li><a href="#tabs6">MonsterPay</a></li>
            <li><a href="#tabs7">SagePay</a></li>
            <li><a href="#tabs8">Google Checkout</a></li>
            <li><a href="#tabs9">Authorize.NET</a></li>
            <li><a href="#tabs_amazon">Amazon</a></li>
            <li><a href="#tabs_chronopay">Chronopay</a></li> -->
            <li><a href="#tabs_offline"><?php _e('Bank Payment(offline)','ProjectTheme'); ?></a></li>
            <?php do_action('ProjectTheme_payment_methods_tabs'); ?>
             
          </ul> 
          <div id="tabs1"  >	
          
          <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=payment-methods&active_tab=tabs1">
            <table width="100%" class="sitemile-table">
    				
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="200"><?php _e('Enable:','ProjectTheme'); ?></td>
                    <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_paypal_enable'); ?></td>
                    </tr>
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="200"><?php _e('PayPal Enable Sandbox:','ProjectTheme'); ?></td>
                    <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_paypal_enable_sdbx'); ?></td>
                    </tr>
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td ><?php _e('PayPal Email Address:','ProjectTheme'); ?></td>
                    <td><input type="text" size="45" name="ProjectTheme_paypal_email" value="<?php echo get_option('ProjectTheme_paypal_email'); ?>"/></td>
                    </tr>
                    
                    
                     <tr><td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td><?php _e('Enable PayPal Adaptive:','ProjectTheme'); ?></td>
                    <td><?php echo ProjectTheme_get_option_drop_down($arr, 'projectTheme_enable_paypal_ad'); ?></td>
                    </tr>
            
            
                     <tr><td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td ><?php _e('Signature:','ProjectTheme'); ?></td>
                    <td><input type="text" name="project_theme_signature" value="<?php echo get_option('project_theme_signature'); ?>" size="85" /> </td>
                    </tr>
                    
                           <tr><td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td ><?php _e('API Password:','ProjectTheme'); ?></td>
                    <td><input type="text" name="project_theme_apipass" value="<?php echo get_option('project_theme_apipass'); ?>" size="55" /> </td>
                    </tr>
                    
                           <tr><td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td ><?php _e('API User:','ProjectTheme'); ?></td>
                    <td><input type="text" name="project_theme_apiuser" value="<?php echo get_option('project_theme_apiuser'); ?>" size="55" /> </td>
                    </tr>
                    
                           <tr><td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td ><?php _e('Application ID:','ProjectTheme'); ?></td>
                    <td><input type="text" name="project_theme_appid" value="<?php echo get_option('project_theme_appid'); ?>" size="55" /> </td>
                    </tr>
                  
        
                    <tr>
                    <td ></td>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_save1" value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>
            
            </table>      
          	</form>
          
          </div>
          
          <div id="tabs2" >	
          
          <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=payment-methods&active_tab=tabs2">
            <table width="100%" class="sitemile-table">
    				
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="200"><?php _e('Enable:','ProjectTheme'); ?></td>
                    <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_alertpay_enable'); ?></td>
                    </tr>
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td ><?php _e('Payza/Alertpay Email:','ProjectTheme'); ?></td>
                    <td><input type="text" size="45" name="ProjectTheme_alertpay_email" value="<?php echo get_option('ProjectTheme_alertpay_email'); ?>"/></td>
                    </tr>
                    
  
        
                    <tr>
                    <td ></td>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_save2" value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>
            
            </table>      
          	</form>
          
          </div>
          
          <div id="tabs3">
          <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=payment-methods&active_tab=tabs3">
            <table width="100%" class="sitemile-table">
    				
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="200"><?php _e('Enable:','ProjectTheme'); ?></td>
                    <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_moneybookers_enable'); ?></td>
                    </tr>
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td ><?php _e('MoneyBookers Email:','ProjectTheme'); ?></td>
                    <td><input type="text" size="45" name="ProjectTheme_moneybookers_email" value="<?php echo get_option('ProjectTheme_moneybookers_email'); ?>"/></td>
                    </tr>
                    
  
        
                    <tr>
                    <td ></td>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_save3" value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>
            
            </table>      
          	</form>
          		
          </div> 
          
         
          
          
          
           <div id="tabs_offline" >	
           
           <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=payment-methods&active_tab=tabs_offline">
            <table width="100%" class="sitemile-table">
    				
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="200"><?php _e('Enable:','ProjectTheme'); ?></td>
                    <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_bank_details_enable'); ?></td>
                    </tr>
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td ><?php _e('Your Bank Details:','ProjectTheme'); ?></td>
                    <td><textarea cols="45" rows="5" name="ProjectTheme_bank_details_txt"><?php echo get_option('ProjectTheme_bank_details_txt'); ?></textarea></td>
                    </tr>


                    
  
        
                    <tr>
                    <td ></td>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_save_bnk" value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>
            
            </table>      
          	</form>
           
            	
          </div>  
          
          <?php do_action('ProjectTheme_payment_methods_content_divs'); ?>

<?php
	echo '</div>';	
  
	
}

/******************************************************************
*
*	Admin Menu - New Function - sitemile[at]sitemile.com
*	developed by Andrei Saioc - andreisaioc[at]gmail.com
*
*******************************************************************/

function projectTheme_layout_settings()
{

	$id_icon 		= 'icon-options-general-layout';
	$ttl_of_stuff 	= 'ProjectTheme - '.__('Layout Settings','ProjectTheme');
	global $menu_admin_Projecttheme_theme_bull;
	
	//------------------------------------------------------
	
	$arr = array("yes" => __("Yes",'ProjectTheme'), "no" => __("No",'ProjectTheme'));
	
	echo '<div class="wrap">';
	echo '<div class="icon32" id="'.$id_icon.'"><br/></div>';	
	echo '<h2 class="my_title_class_sitemile">'.$ttl_of_stuff.'</h2>';	

		if(isset($_POST['ProjectTheme_save4']))
		{
			update_option('ProjectTheme_color_for_top_links', 			trim($_POST['ProjectTheme_color_for_top_links']));
			update_option('ProjectTheme_color_for_bk', 					trim($_POST['ProjectTheme_color_for_bk']));
			update_option('ProjectTheme_color_for_footer', 				trim($_POST['ProjectTheme_color_for_footer']));
			
			echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';
		}
		
		if(isset($_POST['ProjectTheme_save1']))
		{
			update_option('ProjectTheme_home_page_layout', 				trim($_POST['ProjectTheme_home_page_layout']));	
			
			echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';
		}
		
		if(isset($_POST['ProjectTheme_save2']))
		{
			update_option('ProjectTheme_logo_URL', 				trim($_POST['ProjectTheme_logo_URL']));
			
			echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';
		}
		
		if(isset($_POST['ProjectTheme_save3']))
		{
			update_option('ProjectTheme_left_side_footer', 				stripslashes(trim($_POST['ProjectTheme_left_side_footer'])));
			update_option('ProjectTheme_right_side_footer', 			stripslashes(trim($_POST['ProjectTheme_right_side_footer'])));
			
			echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';
		}
		
		
		//-----------------------------------------

	$ProjectTheme_home_page_layout = get_option('ProjectTheme_home_page_layout');
	if(empty($ProjectTheme_home_page_layout)) $ProjectTheme_home_page_layout = "1";
	
?>

	    <div id="usual2" class="usual"> 
          <ul> 
            <li><a href="#tabs1"><?php _e('HomePage','ProjectTheme'); ?></a></li> 
            <li><a href="#tabs2"><?php _e('Header','ProjectTheme'); ?></a></li> 
            <li><a href="#tabs3"><?php _e('Footer','ProjectTheme'); ?></a></li>
            <li><a href="#tabs4"><?php _e('Change Colors','ProjectTheme'); ?></a></li> 
          </ul>
           
          <div id="tabs4">
           <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=layout-settings&active_tab=tabs4">
            <table width="100%" class="sitemile-table">
            
                
        <tr>
        <td width="200"><?php _e('Color for background:','ProjectTheme'); ?></td>
        <td><input type="text" id="colorpickerField1" name="ProjectTheme_color_for_bk"  value="<?php echo get_option('ProjectTheme_color_for_bk'); ?>"/>
				<script>
					
					$('#colorpickerField1, #colorpickerField2, #colorpickerField3').ColorPicker({
							onSubmit: function(hsb, hex, rgb, el) {
								$(el).val(hex);
								$(el).ColorPickerHide();
							},
							onBeforeShow: function () {
								$(this).ColorPickerSetColor(this.value);
							}
						})
						.bind('keyup', function(){
							$(this).ColorPickerSetColor(this.value);
						});
					
				</script>

		</td>
        </tr>
        
        
        
        <tr>
        <td width="200"><?php _e('Color for footer:','ProjectTheme'); ?></td>
        <td><input type="text" id="colorpickerField2" name="ProjectTheme_color_for_footer" value="<?php echo get_option('ProjectTheme_color_for_footer'); ?>" />
		</td>
        </tr>
        
        
        <tr>
        <td width="200"><?php _e('Color for top links:','ProjectTheme'); ?></td>
        <td><input type="text" id="colorpickerField3" name="ProjectTheme_color_for_top_links" value="<?php echo get_option('ProjectTheme_color_for_top_links'); ?>" />
		</td>
        </tr>
            
            
             <tr>
                  
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_save4" value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>    
                
            
            </table>
            
            </form>
          
          
          </div>
           
          <div id="tabs1">
          
          <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=layout-settings&active_tab=tabs1">
            <table width="100%" class="sitemile-table">
    				
				<tr><td valign=top width="22"><?php ProjectTheme_theme_bullet(__('The layout of the homepage.','ProjectTHeme')); ?></td>
					<td class="ttl"><strong><?php _e("Choose from the home page layouts available:","ProjectTheme"); ?></strong> </td> <td></td></tr>
            
				<tr>
                <td valign=top width="22"></td>
					<td width="350"><?php _e("Content + Right Sidebar:","ProjectTheme"); ?> </td>
					<td><input type="radio" name="ProjectTheme_home_page_layout" value="1" <?php if($ProjectTheme_home_page_layout == "1") echo 'checked="checked"'; ?> /> 
					 <img src="<?php bloginfo('template_url'); ?>/images/layout1.jpg" /></td>
                </tr>
                
                
                <tr>
                <td valign=top width="22"></td>
					<td><?php _e("Content + Left Sidebar + Right Sidebar:","ProjectTheme"); ?> </td>
					<td><input type="radio" name="ProjectTheme_home_page_layout" value="2" <?php if($ProjectTheme_home_page_layout == "2") echo 'checked="checked"'; ?> /> 
					  <img src="<?php bloginfo('template_url'); ?>/images/layout2.jpg" /></td>
                </tr>
                
                
                <tr>
                <td valign=top width="22"></td>
					<td><?php _e("Left Sidebar + Content + Right Sidebar:","ProjectTheme"); ?> </td>
					<td><input type="radio" name="ProjectTheme_home_page_layout" value="3" <?php if($ProjectTheme_home_page_layout == "3") echo 'checked="checked"'; ?>/>  
					  <img src="<?php bloginfo('template_url'); ?>/images/layout3.jpg" /></td>
                </tr>
                
                
                <tr>
                <td valign=top width="22"></td>
					<td><?php _e("Left Sidebar + Content:","ProjectTheme"); ?> </td>
					<td><input type="radio" name="ProjectTheme_home_page_layout" value="4" <?php if($ProjectTheme_home_page_layout == "4") echo 'checked="checked"'; ?>/>  
					  <img src="<?php bloginfo('template_url'); ?>/images/layout4.jpg" /></td>
                </tr>
                
                
                <tr>
                <td valign=top></td>
					<td><?php _e("Content:","ProjectTheme"); ?> </td>
					 <td><input type="radio" name="ProjectTheme_home_page_layout" value="5" <?php if($ProjectTheme_home_page_layout == "5") echo 'checked="checked"'; ?>/>  
					 <img src="<?php bloginfo('template_url'); ?>/images/layout5.jpg" /></td>
                </tr>
                
                
            
                        
                    <tr>
                   <td valign=top width="22"></td>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_save1" value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>
            
            </table>      
          	</form>	
          	
          </div>
          
          <div id="tabs2">	
          
           <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=layout-settings&active_tab=tabs2">
            <table width="100%" class="sitemile-table">
    				
                  
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(__('Eg: http://your-site-url.com/images/logo.jpg','ProjectTheme')); ?></td>
                    <td ><?php _e('Logo URL:','ProjectTheme'); ?></td>
                    <td><input type="text" size="45" name="ProjectTheme_logo_URL" value="<?php echo get_option('ProjectTheme_logo_URL'); ?>"/></td>
                    </tr>
           
           
                    <tr>
                    <td ></td>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_save2" value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>
            
            </table>      
          	</form>
          
          </div>
          
          <div id="tabs3">	
             <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=layout-settings&active_tab=tabs3">
            <table width="100%" class="sitemile-table">
    				
                 
          <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(__('This will appear in the left side of the footer area.','ProjectTheme')); ?></td>
                    <td valign="top" ><?php _e('Left side footer area content:','ProjectTheme'); ?></td>
                    <td><textarea cols="65" rows="4" name="ProjectTheme_left_side_footer"><?php echo stripslashes(get_option('ProjectTheme_left_side_footer')); ?></textarea></td>
                    </tr>
                    
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(__('This will appear in the right side of the footer area.','ProjectTheme')); ?></td>
                    <td valign="top" ><?php _e('Right side footer area content:','ProjectTheme'); ?></td>
                    <td><textarea cols="65" rows="4" name="ProjectTheme_right_side_footer"><?php echo stripslashes(get_option('ProjectTheme_right_side_footer')); ?></textarea></td>
                    </tr>
          
          
             <tr>
                    <td ></td>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_save3" value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>
            
            </table>      
          	</form>
          
          </div>
    

<?php
	echo '</div>';		
}

/******************************************************************
*
*	Admin Menu - New Function - sitemile[at]sitemile.com
*	developed by Andrei Saioc - andreisaioc[at]gmail.com
*
*******************************************************************/

function projectTheme_advertise_settings()
{
	$id_icon 		= 'icon-options-general-adve';
	$ttl_of_stuff 	= 'ProjectTheme - '.__('Advertising Spaces','ProjectTheme');
	
	//------------------------------------------------------
	
	echo '<div class="wrap">';
	echo '<div class="icon32" id="'.$id_icon.'"><br/></div>';	
	echo '<h2 class="my_title_class_sitemile">'.$ttl_of_stuff.'</h2>';	

	if(isset($_POST['ProjectTheme_save1']))
	{ 
		update_option('ProjectTheme_adv_code_home_above_content', 				stripslashes($_POST['ProjectTheme_adv_code_home_above_content']));
		update_option('ProjectTheme_adv_code_home_below_content', 				stripslashes($_POST['ProjectTheme_adv_code_home_below_content']));
		
		echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';
	}
	
	if(isset($_POST['ProjectTheme_save2']))
	{
		update_option('ProjectTheme_adv_code_project_page_above_content', 				stripslashes($_POST['ProjectTheme_adv_code_project_page_above_content']));
		update_option('ProjectTheme_adv_code_project_page_below_content', 				stripslashes($_POST['ProjectTheme_adv_code_project_page_below_content']));
		
		echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';
	}
	
	if(isset($_POST['ProjectTheme_save3']))
	{
		update_option('ProjectTheme_adv_code_cat_page_above_content', 				stripslashes($_POST['ProjectTheme_adv_code_cat_page_above_content']));
		update_option('ProjectTheme_adv_code_cat_page_below_content', 				stripslashes($_POST['ProjectTheme_adv_code_cat_page_below_content']));
		
		echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';
	}
	
	if(isset($_POST['ProjectTheme_save4']))
	{
		update_option('ProjectTheme_adv_code_single_page_above_content', 				stripslashes($_POST['ProjectTheme_adv_code_single_page_above_content']));
		update_option('ProjectTheme_adv_code_single_page_below_content', 				stripslashes($_POST['ProjectTheme_adv_code_single_page_below_content']));
		
		echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';
	}

?>

	    <div id="usual2" class="usual"> 
          <ul> 
            <li><a href="#tabs1"><?php _e('HomePage','ProjectTheme'); ?></a></li> 
            <li><a href="#tabs2"><?php _e('Project Single Page','ProjectTheme'); ?></a></li> 
            <li><a href="#tabs3"><?php _e('Category Page','ProjectTheme'); ?></a></li> 
            <li><a href="#tabs4"><?php _e('Single Blog/Normal Page','ProjectTheme'); ?></a></li> 
          </ul> 
          <div id="tabs1">	
          <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=adv-settings&active_tab=tabs1">
          	  <table width="100%" class="sitemile-table">
    			<tr>
                <td valign="top"><?php _e('Above the content area:','ProjectTheme'); ?></td>
                <td><textarea name="ProjectTheme_adv_code_home_above_content" rows="6" cols="60"><?php echo stripslashes(get_option('ProjectTheme_adv_code_home_above_content')); ?></textarea></td>
                </tr>
                
                
                <tr>
                <td valign="top"><?php _e('Below the content area:','ProjectTheme'); ?></td>
                <td><textarea name="ProjectTheme_adv_code_home_below_content" rows="6" cols="60"><?php echo stripslashes(get_option('ProjectTheme_adv_code_home_below_content')); ?></textarea></td>
                </tr>	
                    
                  
                <tr>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_save1" value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>  
                    
              </table>      
          </form>
          
          </div>
          
          <div id="tabs2">	
          <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=adv-settings&active_tab=tabs2">
          <table width="100%" class="sitemile-table">
    			<tr>
                <td valign="top"><?php _e('Above the content area:','ProjectTheme'); ?></td>
                <td><textarea name="ProjectTheme_adv_code_project_page_above_content" rows="6" cols="60"><?php echo stripslashes(get_option('ProjectTheme_adv_code_project_page_above_content')); ?></textarea></td>
                </tr>
                
                
                <tr>
                <td valign="top"><?php _e('Below the content area:','ProjectTheme'); ?></td>
                <td><textarea name="ProjectTheme_adv_code_project_page_below_content" rows="6" cols="60"><?php echo stripslashes(get_option('ProjectTheme_adv_code_project_page_below_content')); ?></textarea></td>
                </tr>	
                    
                  
                <tr>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_save2" value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>  
                    
              </table>  
          </form>
          </div>
          
          <div id="tabs3">	
          <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=adv-settings&active_tab=tabs3">
          <table width="100%" class="sitemile-table">
    			<tr>
                <td valign="top"><?php _e('Above the content area:','ProjectTheme'); ?></td>
                <td><textarea name="ProjectTheme_adv_code_cat_page_above_content" rows="6" cols="60"><?php echo stripslashes(get_option('ProjectTheme_adv_code_cat_page_above_content')); ?></textarea></td>
                </tr>
                
                
                <tr>
                <td valign="top"><?php _e('Below the content area:','ProjectTheme'); ?></td>
                <td><textarea name="ProjectTheme_adv_code_cat_page_below_content" rows="6" cols="60"><?php echo stripslashes(get_option('ProjectTheme_adv_code_cat_page_below_content')); ?></textarea></td>
                </tr>	
                    
                  
                <tr>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_save3" value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>  
                    
              </table>  
          	</form>
          </div> 
          
          <div id="tabs4">	
          <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=adv-settings&active_tab=tabs4">
          <table width="100%" class="sitemile-table">
    			<tr>
                <td valign="top"><?php _e('Above the content area:','ProjectTheme'); ?></td>
                <td><textarea name="ProjectTheme_adv_code_single_page_above_content" rows="6" cols="60"><?php echo stripslashes(get_option('ProjectTheme_adv_code_single_page_above_content')); ?></textarea></td>
                </tr>
                
                
                <tr>
                <td valign="top"><?php _e('Below the content area:','ProjectTheme'); ?></td>
                <td><textarea name="ProjectTheme_adv_code_single_page_below_content" rows="6" cols="60"><?php echo stripslashes(get_option('ProjectTheme_adv_code_single_page_below_content')); ?></textarea></td>
                </tr>	
                    
                  
                <tr>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_save4" value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>  
                    
              </table>  
          	</form>
          </div>  

<?php
	echo '</div>';		
}



/******************************************************************
*
*	Admin Menu - New Function - sitemile[at]sitemile.com
*	developed by Andrei Saioc - andreisaioc[at]gmail.com
*
*******************************************************************/

function projectTheme_email_settings()
{
	$id_icon 		= 'icon-options-general-email';
	$ttl_of_stuff 	= 'ProjectTheme - '.__('Email Settings','ProjectTheme');
	global $menu_admin_ProjectTheme_theme_bull;
	$arr = array( "yes" => 'Yes', "no" => "No");
	
	
		
	echo '<div class="wrap">';
	echo '<div class="icon32" id="'.$id_icon.'"><br/></div>';	
	echo '<h2 class="my_title_class_sitemile">'.$ttl_of_stuff.'</h2>';	
	
	//--------------------------------------------------------------------------
	
	if(isset($_POST['ProjectTheme_save1']))
	{
		update_option('ProjectTheme_email_name_from', 	trim($_POST['ProjectTheme_email_name_from']));
		update_option('ProjectTheme_email_addr_from', 	trim($_POST['ProjectTheme_email_addr_from']));
		update_option('ProjectTheme_allow_html_emails', trim($_POST['ProjectTheme_allow_html_emails']));
		
		echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';		
	}
	
	if(isset($_POST['ProjectTheme_save2']))
	{
		update_option('ProjectTheme_new_user_email_subject', 	trim($_POST['ProjectTheme_new_user_email_subject']));
		update_option('ProjectTheme_new_user_email_message', 	trim($_POST['ProjectTheme_new_user_email_message']));
		
		echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';		
	}
	
	if(isset($_POST['ProjectTheme_save_new_user_email_admin']))
	{
		update_option('ProjectTheme_new_user_email_admin_subject', 	trim($_POST['ProjectTheme_new_user_email_admin_subject']));
		update_option('ProjectTheme_new_user_email_admin_message', 	trim($_POST['ProjectTheme_new_user_email_admin_message']));
		
		echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';		
	}
	
		if(isset($_POST['ProjectTheme_save3']))
	{
		update_option('ProjectTheme_new_project_email_not_approve_admin_enable', 	trim($_POST['ProjectTheme_new_project_email_not_approve_admin_enable']));
		update_option('ProjectTheme_new_project_email_not_approve_admin_subject', 	trim($_POST['ProjectTheme_new_project_email_not_approve_admin_subject']));
		update_option('ProjectTheme_new_project_email_not_approve_admin_message', 	trim($_POST['ProjectTheme_new_project_email_not_approve_admin_message']));
		
		echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';		
	}

	if(isset($_POST['ProjectTheme_save31']))
	{
		update_option('ProjectTheme_new_project_email_approve_admin_enable', 	trim($_POST['ProjectTheme_new_project_email_approve_admin_enable']));
		update_option('ProjectTheme_new_project_email_approve_admin_subject', 	trim($_POST['ProjectTheme_new_project_email_approve_admin_subject']));
		update_option('ProjectTheme_new_project_email_approve_admin_message', 	trim($_POST['ProjectTheme_new_project_email_approve_admin_message']));
		
		echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';		
	}
	
	if(isset($_POST['ProjectTheme_save32']))
	{
		update_option('ProjectTheme_new_project_email_not_approved_enable', 	trim($_POST['ProjectTheme_new_project_email_not_approved_enable']));
		update_option('ProjectTheme_new_project_email_not_approved_subject', 	trim($_POST['ProjectTheme_new_project_email_not_approved_subject']));
		update_option('ProjectTheme_new_project_email_not_approved_message', 	trim($_POST['ProjectTheme_new_project_email_not_approved_message']));
		
		echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';		
	}
	
	if(isset($_POST['ProjectTheme_save33']))
	{
		update_option('ProjectTheme_new_project_email_approved_enable', 	trim($_POST['ProjectTheme_new_project_email_approved_enable']));
		update_option('ProjectTheme_new_project_email_approved_subject', 	trim($_POST['ProjectTheme_new_project_email_approved_subject']));
		update_option('ProjectTheme_new_project_email_approved_message', 	trim($_POST['ProjectTheme_new_project_email_approved_message']));
		
		echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';		
	}
	
	if(isset($_POST['ProjectTheme_message_board_bidder_email_save']))
	{
		update_option('ProjectTheme_message_board_bidder_email_enable', 	trim($_POST['ProjectTheme_message_board_bidder_email_enable']));
		update_option('ProjectTheme_message_board_bidder_email_message', 	trim($_POST['ProjectTheme_message_board_bidder_email_message']));
		update_option('ProjectTheme_message_board_bidder_email_subject', 	trim($_POST['ProjectTheme_message_board_bidder_email_subject']));
		
		echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';		
	}
	
	if(isset($_POST['ProjectTheme_message_board_owner_email_save']))
	{
		update_option('ProjectTheme_message_board_owner_email_enable', 		trim($_POST['ProjectTheme_message_board_owner_email_enable']));
		update_option('ProjectTheme_message_board_owner_email_message', 	trim($_POST['ProjectTheme_message_board_owner_email_message']));
		update_option('ProjectTheme_message_board_owner_email_subject', 	trim($_POST['ProjectTheme_message_board_owner_email_subject']));
		
		echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';		
	}
	
	if(isset($_POST['ProjectTheme_bid_project_bidder_email_save']))
	{
		update_option('ProjectTheme_bid_project_bidder_email_enable', 	trim($_POST['ProjectTheme_bid_project_bidder_email_enable']));
		update_option('ProjectTheme_bid_project_bidder_email_subject', 	trim($_POST['ProjectTheme_bid_project_bidder_email_subject']));
		update_option('ProjectTheme_bid_project_bidder_email_message', 	trim($_POST['ProjectTheme_bid_project_bidder_email_message']));
		
		echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';		
	}
	
	if(isset($_POST['ProjectTheme_subscription_email_save']))
	{
		update_option('ProjectTheme_subscription_email_enable', 	trim($_POST['ProjectTheme_subscription_email_enable']));
		update_option('ProjectTheme_subscription_email_subject', 	trim($_POST['ProjectTheme_subscription_email_subject']));
		update_option('ProjectTheme_subscription_email_message', 	trim($_POST['ProjectTheme_subscription_email_message']));
		
		echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';		
	}
	
	
	
	if(isset($_POST['ProjectTheme_payment_on_completed_project_email_save']))
	{
		update_option('ProjectTheme_payment_on_completed_project_enable', 	trim($_POST['ProjectTheme_payment_on_completed_project_enable']));
		update_option('ProjectTheme_payment_on_completed_project_subject', 	trim($_POST['ProjectTheme_payment_on_completed_project_subject']));
		update_option('ProjectTheme_payment_on_completed_project_message', 	trim($_POST['ProjectTheme_payment_on_completed_project_message']));
		
		echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';		
	}
	
	if(isset($_POST['ProjectTheme_bid_project_owner_email_save']))
	{
		update_option('ProjectTheme_bid_project_owner_email_enable', 	trim($_POST['ProjectTheme_bid_project_owner_email_enable']));
		update_option('ProjectTheme_bid_project_owner_email_subject', 	trim($_POST['ProjectTheme_bid_project_owner_email_subject']));
		update_option('ProjectTheme_bid_project_owner_email_message', 	trim($_POST['ProjectTheme_bid_project_owner_email_message']));
		
		echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';		
	}
	
	if(isset($_POST['ProjectTheme_priv_mess_received_email_save']))
	{
		update_option('ProjectTheme_priv_mess_received_email_enable', 	trim($_POST['ProjectTheme_priv_mess_received_email_enable']));
		update_option('ProjectTheme_priv_mess_received_email_subject', 	trim($_POST['ProjectTheme_priv_mess_received_email_subject']));
		update_option('ProjectTheme_priv_mess_received_email_message', 	trim($_POST['ProjectTheme_priv_mess_received_email_message']));
		
		echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';		
	}
	
	if(isset($_POST['ProjectTheme_completed_project_bidder_email_save']))
	{
		update_option('ProjectTheme_completed_project_bidder_email_enable', 	trim($_POST['ProjectTheme_completed_project_bidder_email_enable']));
		update_option('ProjectTheme_completed_project_bidder_email_subject', 	trim($_POST['ProjectTheme_completed_project_bidder_email_subject']));
		update_option('ProjectTheme_completed_project_bidder_email_message', 	trim($_POST['ProjectTheme_completed_project_bidder_email_message']));
		
		echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';		
	}
	
	if(isset($_POST['ProjectTheme_rated_user_email_save']))
	{
		update_option('ProjectTheme_rated_user_email_enable', 	trim($_POST['ProjectTheme_rated_user_email_enable']));
		update_option('ProjectTheme_rated_user_email_subject', 	trim($_POST['ProjectTheme_rated_user_email_subject']));
		update_option('ProjectTheme_rated_user_email_message', 	trim($_POST['ProjectTheme_rated_user_email_message']));
		
		echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';		
	}
	
	if(isset($_POST['ProjectTheme_completed_project_owner_email_save']))
	{
		update_option('ProjectTheme_completed_project_owner_email_enable', 		trim($_POST['ProjectTheme_completed_project_owner_email_enable']));
		update_option('ProjectTheme_completed_project_owner_email_subject', 	trim($_POST['ProjectTheme_completed_project_owner_email_subject']));
		update_option('ProjectTheme_completed_project_owner_email_message', 	trim($_POST['ProjectTheme_completed_project_owner_email_message']));
		
		echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';		
	}
	
	if(isset($_POST['ProjectTheme_delivered_project_owner_email_save']))
	{
		update_option('ProjectTheme_delivered_project_owner_email_enable', 		trim($_POST['ProjectTheme_delivered_project_owner_email_enable']));
		update_option('ProjectTheme_delivered_project_owner_email_subject', 	trim($_POST['ProjectTheme_delivered_project_owner_email_subject']));
		update_option('ProjectTheme_delivered_project_owner_email_message', 	trim($_POST['ProjectTheme_delivered_project_owner_email_message']));
		
		echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';		
	}

	
	
	if(isset($_POST['ProjectTheme_delivered_project_bidder_email_save']))
	{
		update_option('ProjectTheme_delivered_project_bidder_email_enable', 	trim($_POST['ProjectTheme_delivered_project_bidder_email_enable']));
		update_option('ProjectTheme_delivered_project_bidder_email_subject', 	trim($_POST['ProjectTheme_delivered_project_bidder_email_subject']));
		update_option('ProjectTheme_delivered_project_bidder_email_message', 	trim($_POST['ProjectTheme_delivered_project_bidder_email_message']));
		
		echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';		
	}
	
	if(isset($_POST['ProjectTheme_won_project_owner_email_save']))
	{
		update_option('ProjectTheme_won_project_owner_email_enable', 	trim($_POST['ProjectTheme_won_project_owner_email_enable']));
		update_option('ProjectTheme_won_project_owner_email_subject', 	trim($_POST['ProjectTheme_won_project_owner_email_subject']));
		update_option('ProjectTheme_won_project_owner_email_message', 	trim($_POST['ProjectTheme_won_project_owner_email_message']));
		
		echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';		
	}
	
	if(isset($_POST['ProjectTheme_won_project_winner_email_save']))
	{
		update_option('ProjectTheme_won_project_winner_email_enable', 	trim($_POST['ProjectTheme_won_project_winner_email_enable']));
		update_option('ProjectTheme_won_project_winner_email_subject', 	trim($_POST['ProjectTheme_won_project_winner_email_subject']));
		update_option('ProjectTheme_won_project_winner_email_message', 	trim($_POST['ProjectTheme_won_project_winner_email_message']));
		
		echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';		
	}
	
	if(isset($_POST['ProjectTheme_won_project_loser_email_save']))
	{
		update_option('ProjectTheme_won_project_loser_email_enable', 	trim($_POST['ProjectTheme_won_project_loser_email_enable']));
		update_option('ProjectTheme_won_project_loser_email_subject', 	trim($_POST['ProjectTheme_won_project_loser_email_subject']));
		update_option('ProjectTheme_won_project_loser_email_message', 	trim($_POST['ProjectTheme_won_project_loser_email_message']));
		
		echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';		
	}
	
	
	
	do_action('ProjectTheme_save_emails_post');
	
	?>
    
	<div id="usual2" class="usual"> 
        <ul> 
            <li><a href="#tabs1"><?php _e('Email Settings','ProjectTheme'); ?></a></li> 
            <li><a href="#new_user_email"><?php _e('New User Email','ProjectTheme'); ?></a></li>
            <li><a href="#admin_new_user_email"><?php _e('New User Email (admin)','ProjectTheme'); ?></a></li>
            
            <li><a href="#post_project_approved_admin"><?php _e('Post Project Not Approved Email (admin)','ProjectTheme'); ?></a></li>
            <li><a href="#post_project_not_approved_admin"><?php _e('Post Project Auto Approved Email (admin)','ProjectTheme'); ?></a></li>
            <li><a href="#post_project_approved"><?php _e('Post Project Not Approved Email','ProjectTheme'); ?></a></li>
            <li><a href="#post_project_not_approved"><?php _e('Post Project Auto Approved Email','ProjectTheme'); ?></a></li>
            
            <!-- #### -->
            
            <li><a href="#delivered_project_owner"><?php _e('Delivered Project (owner)','ProjectTheme'); ?></a></li>
            <li><a href="#delivered_project_bidder"><?php _e('Delivered Project (bidder)','ProjectTheme'); ?></a></li>
            
            <!-- #### -->
            
            <li><a href="#completed_project_owner"><?php _e('Completed Project (owner)','ProjectTheme'); ?></a></li>
            <li><a href="#completed_project_bidder"><?php _e('Completed Project (bidder)','ProjectTheme'); ?></a></li>
            
            
            <li><a href="#priv_mess_received"><?php _e('Private Message Received','ProjectTheme'); ?></a></li>
            <li><a href="#rated_user"><?php _e('Rated User','ProjectTheme'); ?></a></li>
    
    
    		<li><a href="#won_project_owner"><?php _e('Won Project(owner)','ProjectTheme'); ?></a></li>
    		<li><a href="#won_project_winner"><?php _e('Won Project(winner)','ProjectTheme'); ?></a></li>          
    		<li><a href="#won_project_loser"><?php _e('Won Project(losers)','ProjectTheme'); ?></a></li> 
            
            <li><a href="#bid_project_bidder"><?php _e('Bid Project(bidder)','ProjectTheme'); ?></a></li>          
    		<li><a href="#bid_project_owner"><?php _e('Bid Project(owner)','ProjectTheme'); ?></a></li> 
            
            <li><a href="#message_board_owner"><?php _e('Message Board(owner)','ProjectTheme'); ?></a></li> 
            <li><a href="#message_board_bidder"><?php _e('Message Board(bidder)','ProjectTheme'); ?></a></li>    
            
            
            
            <li><a href="#bid_project_subs"><?php _e('Project Subscription Notification','ProjectTheme'); ?></a></li>            
            <li><a href="#payment_on_completed_project"><?php _e('Payment on Completed Project (owner)','ProjectTheme'); ?></a></li>    
            
                  
    		
            <?php do_action('ProjectTheme_save_emails_tabs'); ?>
            
        </ul> 
        
        
        <div id="delivered_project_owner">	
          
           <div class="spntxt_bo"><?php _e('This email will be received by the owner of the project after he accepts the project as delivered. 
          Be aware, if you add html tags to this email you must have the allow HTML tags option set to yes.
          Also at the bottom you can see a list of tags you can use in the email body.','ProjectTheme'); ?> </div>
          
          
          <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=email-settings&active_tab=delivered_project_owner">
            <table width="100%" class="sitemile-table">
    				
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td ><?php _e('Enable this email:','ProjectTheme'); ?></td>
                    <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_delivered_project_owner_email_enable'); ?></td>
                    </tr>
                    
            	  	<tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="160"><?php _e('Email Subject:','ProjectTheme'); ?></td>
                    <td><input type="text" size="90" name="ProjectTheme_delivered_project_owner_email_subject" value="<?php echo stripslashes(get_option('ProjectTheme_delivered_project_owner_email_subject')); ?>"/></td>
                    </tr>
                    

                    
                    <tr>
                    <td valign=top><?php ProjectTheme_theme_bullet(); ?></td>
                    <td valign=top ><?php _e('Email Content:','ProjectTheme'); ?></td>
                    <td><textarea cols="92" rows="10" name="ProjectTheme_delivered_project_owner_email_message"><?php echo stripslashes(get_option('ProjectTheme_delivered_project_owner_email_message')); ?></textarea></td>
                    </tr>
                    
                    
                    
                    <tr>
                    <td valign=top></td>
                    <td valign=top ></td>
                    <td><div class="spntxt_bo2">
                    <?php _e('Here is a list of tags you can use in this email:','ProjectTheme'); ?><br/><br/>
                    
   					<strong>##username##</strong> - <?php _e('Project Owner\'s Username','ProjectTheme'); ?><br/>
                    <strong>##site_login_url##</strong> - <?php _e('the link to your user login page','ProjectTheme'); ?><br/>
                    <strong>##your_site_name##</strong> - <?php _e("your website's name","ProjectTheme"); ?><br/>
                    <strong>##your_site_url##</strong> - <?php _e("your website's main address",'ProjectTheme'); ?><br/>
                    <strong>##my_account_url##</strong> - <?php _e("your website's my account link",'ProjectTheme'); ?><br/>
                    <strong>##project_name##</strong> - <?php _e("new new project's title",'ProjectTheme'); ?><br/>
                    <strong>##project_link##</strong> - <?php _e('link for the new project','ProjectTheme'); ?><br/>
                
                    </div></td>
                    </tr>
            		
                    <tr>
                    <td ></td>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_delivered_project_owner_email_save" 
                    value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>
            
            </table>
            </form>
          	
          </div>
        
        
        <!--################### -->
        
         <div id="delivered_project_bidder">	
          
           <div class="spntxt_bo"><?php _e('This email will be received by the bidder/provider after the owner of the projects accepts the project as delivered. 
          Be aware, if you add html tags to this email you must have the allow HTML tags option set to yes.
          Also at the bottom you can see a list of tags you can use in the email body.','ProjectTheme'); ?> </div>
          
          
          <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=email-settings&active_tab=delivered_project_bidder">
            <table width="100%" class="sitemile-table">
    				
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td ><?php _e('Enable this email:','ProjectTheme'); ?></td>
                    <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_delivered_project_bidder_email_enable'); ?></td>
                    </tr>

                    
            	  	<tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="160"><?php _e('Email Subject:','ProjectTheme'); ?></td>
                    <td><input type="text" size="90" name="ProjectTheme_delivered_project_bidder_email_subject" value="<?php echo stripslashes(get_option('ProjectTheme_delivered_project_bidder_email_subject')); ?>"/></td>
                    </tr>
                    

                    
                    <tr>
                    <td valign=top><?php ProjectTheme_theme_bullet(); ?></td>
                    <td valign=top ><?php _e('Email Content:','ProjectTheme'); ?></td>
                    <td><textarea cols="92" rows="10" name="ProjectTheme_delivered_project_bidder_email_message"><?php echo stripslashes(get_option('ProjectTheme_delivered_project_bidder_email_message')); ?></textarea></td>
                    </tr>
                    
                    
                    
                    <tr>
                    <td valign=top></td>
                    <td valign=top ></td>
                    <td><div class="spntxt_bo2">
                    <?php _e('Here is a list of tags you can use in this email:','ProjectTheme'); ?><br/><br/>
                    
   					<strong>##username##</strong> - <?php _e('Bidder\'s Username','ProjectTheme'); ?><br/>
                    <strong>##site_login_url##</strong> - <?php _e('the link to your user login page','ProjectTheme'); ?><br/>
                    <strong>##your_site_name##</strong> - <?php _e("your website's name","ProjectTheme"); ?><br/>
                    <strong>##your_site_url##</strong> - <?php _e("your website's main address",'ProjectTheme'); ?><br/>
                    <strong>##my_account_url##</strong> - <?php _e("your website's my account link",'ProjectTheme'); ?><br/>
                    <strong>##project_name##</strong> - <?php _e("new new project's title",'ProjectTheme'); ?><br/>
                    <strong>##project_link##</strong> - <?php _e('link for the new project','ProjectTheme'); ?><br/>
                
                    </div></td>
                    </tr>
            		
                    <tr>
                    <td ></td>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_delivered_project_bidder_email_save" 
                    value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>
            
            </table>
            </form>
          	
          </div>
        
        
        <!-- ################################ -->
        <div id="completed_project_owner">	
          
           <div class="spntxt_bo"><?php _e('This email will be received by the owner of the project when the provider marks the project as completed. 
          Be aware, if you add html tags to this email you must have the allow HTML tags option set to yes.
          Also at the bottom you can see a list of tags you can use in the email body.','ProjectTheme'); ?> </div>
          
          
          <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=email-settings&active_tab=completed_project_owner">
            <table width="100%" class="sitemile-table">
    				
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td ><?php _e('Enable this email:','ProjectTheme'); ?></td>
                    <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_completed_project_owner_email_enable'); ?></td>
                    </tr>
                    
            	  	<tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="160"><?php _e('Email Subject:','ProjectTheme'); ?></td>
                    <td><input type="text" size="90" name="ProjectTheme_completed_project_owner_email_subject" value="<?php echo stripslashes(get_option('ProjectTheme_completed_project_owner_email_subject')); ?>"/></td>
                    </tr>
                    

                    
                    <tr>
                    <td valign=top><?php ProjectTheme_theme_bullet(); ?></td>
                    <td valign=top ><?php _e('Email Content:','ProjectTheme'); ?></td>
                    <td><textarea cols="92" rows="10" name="ProjectTheme_completed_project_owner_email_message"><?php echo stripslashes(get_option('ProjectTheme_completed_project_owner_email_message')); ?></textarea></td>
                    </tr>
                    
                    
                    
                    <tr>
                    <td valign=top></td>
                    <td valign=top ></td>
                    <td><div class="spntxt_bo2">
                    <?php _e('Here is a list of tags you can use in this email:','ProjectTheme'); ?><br/><br/>
                    
   					<strong>##username##</strong> - <?php _e('Project Owner\'s Username','ProjectTheme'); ?><br/>
                    <strong>##site_login_url##</strong> - <?php _e('the link to your user login page','ProjectTheme'); ?><br/>
                    <strong>##your_site_name##</strong> - <?php _e("your website's name","ProjectTheme"); ?><br/>
                    <strong>##your_site_url##</strong> - <?php _e("your website's main address",'ProjectTheme'); ?><br/>
                    <strong>##my_account_url##</strong> - <?php _e("your website's my account link",'ProjectTheme'); ?><br/>
                    <strong>##project_name##</strong> - <?php _e("new new project's title",'ProjectTheme'); ?><br/>
                    <strong>##project_link##</strong> - <?php _e('link for the new project','ProjectTheme'); ?><br/>
                
                    </div></td>
                    </tr>
            		
                    <tr>
                    <td ></td>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_completed_project_owner_email_save" 
                    value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>
            
            </table>
            </form>
          	
          </div>
        
        <!-- ################################ -->
        <div id="completed_project_bidder">	
          
           <div class="spntxt_bo"><?php _e('This email will be received by the provider/bidder when he marks the project as completed. 
          Be aware, if you add html tags to this email you must have the allow HTML tags option set to yes.
          Also at the bottom you can see a list of tags you can use in the email body.','ProjectTheme'); ?> </div>
          
          
          <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=email-settings&active_tab=completed_project_bidder">
            <table width="100%" class="sitemile-table">
    				
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td ><?php _e('Enable this email:','ProjectTheme'); ?></td>
                    <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_completed_project_bidder_email_enable'); ?></td>
                    </tr>
                    
            	  	<tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="160"><?php _e('Email Subject:','ProjectTheme'); ?></td>
                    <td><input type="text" size="90" name="ProjectTheme_completed_project_bidder_email_subject" value="<?php echo stripslashes(get_option('ProjectTheme_completed_project_bidder_email_subject')); ?>"/></td>
                    </tr>
                    

                    
                    <tr>
                    <td valign=top><?php ProjectTheme_theme_bullet(); ?></td>
                    <td valign=top ><?php _e('Email Content:','ProjectTheme'); ?></td>
                    <td><textarea cols="92" rows="10" name="ProjectTheme_completed_project_bidder_email_message"><?php echo stripslashes(get_option('ProjectTheme_completed_project_bidder_email_message')); ?></textarea></td>
                    </tr>
                    
                    
                    
                    <tr>
                    <td valign=top></td>
                    <td valign=top ></td>
                    <td><div class="spntxt_bo2">
                    <?php _e('Here is a list of tags you can use in this email:','ProjectTheme'); ?><br/><br/>
                    
   					<strong>##username##</strong> - <?php _e('Bidder\'s Username','ProjectTheme'); ?><br/>
                    <strong>##site_login_url##</strong> - <?php _e('the link to your user login page','ProjectTheme'); ?><br/>
                    <strong>##your_site_name##</strong> - <?php _e("your website's name","ProjectTheme"); ?><br/>
                    <strong>##your_site_url##</strong> - <?php _e("your website's main address",'ProjectTheme'); ?><br/>
                    <strong>##my_account_url##</strong> - <?php _e("your website's my account link",'ProjectTheme'); ?><br/>
                    <strong>##project_name##</strong> - <?php _e("new new project's title",'ProjectTheme'); ?><br/>
                    <strong>##project_link##</strong> - <?php _e('link for the new project','ProjectTheme'); ?><br/>
                
                    </div></td>
                    </tr>
            		
                    <tr>
                    <td ></td>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_completed_project_bidder_email_save" 
                    value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>
            
            </table>
            </form>
          	
          </div>
        
        <!-- ################################ -->
         <div id="priv_mess_received">	
          
           <div class="spntxt_bo"><?php _e('This email will be received by any user when another user sends a private message. 
          Be aware, if you add html tags to this email you must have the allow HTML tags option set to yes.
          Also at the bottom you can see a list of tags you can use in the email body.','ProjectTheme'); ?> </div>
          
          
          <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=email-settings&active_tab=priv_mess_received">
            <table width="100%" class="sitemile-table">
    				
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td ><?php _e('Enable this email:','ProjectTheme'); ?></td>
                    <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_priv_mess_received_email_enable'); ?></td>
                    </tr>
                    
            	  	<tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="160"><?php _e('Email Subject:','ProjectTheme'); ?></td>
                    <td><input type="text" size="90" name="ProjectTheme_priv_mess_received_email_subject" value="<?php echo stripslashes(get_option('ProjectTheme_priv_mess_received_email_subject')); ?>"/></td>
                    </tr>
                    

                    
                    <tr>
                    <td valign=top><?php ProjectTheme_theme_bullet(); ?></td>
                    <td valign=top ><?php _e('Email Content:','ProjectTheme'); ?></td>
                    <td><textarea cols="92" rows="10" name="ProjectTheme_priv_mess_received_email_message"><?php echo stripslashes(get_option('ProjectTheme_priv_mess_received_email_message')); ?></textarea></td>
                    </tr>
                    
                    
                    
                    <tr>
                    <td valign=top></td>
                    <td valign=top ></td>
                    <td><div class="spntxt_bo2">
                    <?php _e('Here is a list of tags you can use in this email:','ProjectTheme'); ?><br/><br/>
                    
   
                    <strong>##site_login_url##</strong> - <?php _e('the link to your user login page','ProjectTheme'); ?><br/>
                    <strong>##your_site_name##</strong> - <?php _e("your website's name","ProjectTheme"); ?><br/>
                    <strong>##your_site_url##</strong> - <?php _e("your website's main address",'ProjectTheme'); ?><br/>
                    <strong>##my_account_url##</strong> - <?php _e("your website's my account link",'ProjectTheme'); ?><br/>
                    <strong>##project_name##</strong> - <?php _e("new new project's title",'ProjectTheme'); ?><br/>
                    <strong>##project_link##</strong> - <?php _e('link for the new project','ProjectTheme'); ?><br/>
                    <strong>##sender_username##</strong> - <?php _e('sender username','ProjectTheme'); ?><br/>
                    <strong>##receiver_username##</strong> - <?php _e('receiver username','ProjectTheme'); ?><br/>
                    

                    </div></td>
                    </tr>
            		
                    <tr>
                    <td ></td>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_priv_mess_received_email_save" value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>
            
            </table>
            </form>
          	
          </div>
        
        <!-- ################################ -->
        <div id="rated_user">	
          
           <div class="spntxt_bo"><?php _e('This email will be received by the freshly rated user. 
          Be aware, if you add html tags to this email you must have the allow HTML tags option set to yes.
          Also at the bottom you can see a list of tags you can use in the email body.','ProjectTheme'); ?> </div>
          
          
          <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=email-settings&active_tab=rated_user">
            <table width="100%" class="sitemile-table">
    				
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td ><?php _e('Enable this email:','ProjectTheme'); ?></td>
                    <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_rated_user_email_enable'); ?></td>
                    </tr>
                    
            	  	<tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="160"><?php _e('Email Subject:','ProjectTheme'); ?></td>
                    <td><input type="text" size="90" name="ProjectTheme_rated_user_email_subject" value="<?php echo stripslashes(get_option('ProjectTheme_rated_user_email_subject')); ?>"/></td>
                    </tr>
                    

                    
                    <tr>
                    <td valign=top><?php ProjectTheme_theme_bullet(); ?></td>
                    <td valign=top ><?php _e('Email Content:','ProjectTheme'); ?></td>
                    <td><textarea cols="92" rows="10" name="ProjectTheme_rated_user_email_message"><?php echo stripslashes(get_option('ProjectTheme_rated_user_email_message')); ?></textarea></td>
                    </tr>
                    
                    
                    
                    <tr>
                    <td valign=top></td>
                    <td valign=top ></td>
                    <td><div class="spntxt_bo2">
                    <?php _e('Here is a list of tags you can use in this email:','ProjectTheme'); ?><br/><br/>
                    
                    <strong>##username##</strong> - <?php _e('Winner Bidder\'s Username','ProjectTheme'); ?><br/>
                    <strong>##site_login_url##</strong> - <?php _e('the link to your user login page','ProjectTheme'); ?><br/>
                    <strong>##your_site_name##</strong> - <?php _e("your website's name","ProjectTheme"); ?><br/>
                    <strong>##your_site_url##</strong> - <?php _e("your website's main address",'ProjectTheme'); ?><br/>
                    <strong>##my_account_url##</strong> - <?php _e("your website's my account link",'ProjectTheme'); ?><br/>
                    <strong>##project_name##</strong> - <?php _e("new new project's title",'ProjectTheme'); ?><br/>
                    <strong>##project_link##</strong> - <?php _e('link for the new project','ProjectTheme'); ?><br/>
                    <strong>##rating##</strong> - <?php _e('rating value','ProjectTheme'); ?><br/>
                    <strong>##comment##</strong> - <?php _e('rating comment','ProjectTheme'); ?><br/>
                    
                    </div></td>
                    </tr>
            		
                    <tr>
                    <td ></td>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_rated_user_email_save" value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>
            
            </table>
            </form>
          	
          </div>
        
        <!-- ################################ -->
         <div id="won_project_owner">	
          
           <div class="spntxt_bo"><?php _e('This email will be received by the project owner after he awards the project to a certain bidder. 
          Be aware, if you add html tags to this email you must have the allow HTML tags option set to yes.
          Also at the bottom you can see a list of tags you can use in the email body.','ProjectTheme'); ?> </div>
          
          
          <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=email-settings&active_tab=won_project_owner">
            <table width="100%" class="sitemile-table">
    				
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td ><?php _e('Enable this email:','ProjectTheme'); ?></td>
                    <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_won_project_owner_email_enable'); ?></td>
                    </tr>
                    
            	  	<tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="160"><?php _e('Email Subject:','ProjectTheme'); ?></td>
                    <td><input type="text" size="90" name="ProjectTheme_won_project_owner_email_subject" value="<?php echo stripslashes(get_option('ProjectTheme_won_project_owner_email_subject')); ?>"/></td>
                    </tr>

                    

                    
                    <tr>
                    <td valign=top><?php ProjectTheme_theme_bullet(); ?></td>
                    <td valign=top ><?php _e('Email Content:','ProjectTheme'); ?></td>
                    <td><textarea cols="92" rows="10" name="ProjectTheme_won_project_owner_email_message"><?php echo stripslashes(get_option('ProjectTheme_won_project_owner_email_message')); ?></textarea></td>
                    </tr>
                    
                    
                    
                    <tr>
                    <td valign=top></td>
                    <td valign=top ></td>
                    <td><div class="spntxt_bo2">
                    <?php _e('Here is a list of tags you can use in this email:','ProjectTheme'); ?><br/><br/>
                    
                    <strong>##username##</strong> - <?php _e('Winner Bidder\'s Username','ProjectTheme'); ?><br/>
                    <strong>##site_login_url##</strong> - <?php _e('the link to your user login page','ProjectTheme'); ?><br/>
                    <strong>##your_site_name##</strong> - <?php _e("your website's name","ProjectTheme"); ?><br/>
                    <strong>##your_site_url##</strong> - <?php _e("your website's main address",'ProjectTheme'); ?><br/>
                    <strong>##my_account_url##</strong> - <?php _e("your website's my account link",'ProjectTheme'); ?><br/>
                    <strong>##project_name##</strong> - <?php _e("new new project's title",'ProjectTheme'); ?><br/>
                    <strong>##project_link##</strong> - <?php _e('link for the new project','ProjectTheme'); ?><br/>
                    <strong>##winner_bid_value##</strong> - <?php _e('winner bid value','ProjectTheme'); ?><br/>
                    <strong>##winner_bid_username##</strong> - <?php _e('winner bidder username','ProjectTheme'); ?><br/>
                    
                    </div></td>
                    </tr>
            		
                    <tr>
                    <td ></td>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_won_project_owner_email_save" value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>
            
            </table>
            </form>
          	
          </div>
        
        <!-- ################################ -->
        <div id="won_project_winner">	
          
           <div class="spntxt_bo"><?php _e('This email will be received by the winner bidder when the project is won. 
          Be aware, if you add html tags to this email you must have the allow HTML tags option set to yes.
          Also at the bottom you can see a list of tags you can use in the email body.','ProjectTheme'); ?> </div>
          
          
          <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=email-settings&active_tab=won_project_winner">
            <table width="100%" class="sitemile-table">
    				
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td ><?php _e('Enable this email:','ProjectTheme'); ?></td>
                    <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_won_project_winner_email_enable'); ?></td>
                    </tr>
                    
            	  	<tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="160"><?php _e('Email Subject:','ProjectTheme'); ?></td>
                    <td><input type="text" size="90" name="ProjectTheme_won_project_winner_email_subject" value="<?php echo stripslashes(get_option('ProjectTheme_won_project_winner_email_subject')); ?>"/></td>
                    </tr>
                    

                    
                    <tr>
                    <td valign=top><?php ProjectTheme_theme_bullet(); ?></td>
                    <td valign=top ><?php _e('Email Content:','ProjectTheme'); ?></td>
                    <td><textarea cols="92" rows="10" name="ProjectTheme_won_project_winner_email_message"><?php echo stripslashes(get_option('ProjectTheme_won_project_winner_email_message')); ?></textarea></td>
                    </tr>
                    
                    
                    
                    <tr>
                    <td valign=top></td>
                    <td valign=top ></td>
                    <td><div class="spntxt_bo2">
                    <?php _e('Here is a list of tags you can use in this email:','ProjectTheme'); ?><br/><br/>
                    
                    <strong>##username##</strong> - <?php _e('Winner Bidder\'s Username','ProjectTheme'); ?><br/>
                    <strong>##site_login_url##</strong> - <?php _e('the link to your user login page','ProjectTheme'); ?><br/>
                    <strong>##your_site_name##</strong> - <?php _e("your website's name","ProjectTheme"); ?><br/>
                    <strong>##your_site_url##</strong> - <?php _e("your website's main address",'ProjectTheme'); ?><br/>
                    <strong>##my_account_url##</strong> - <?php _e("your website's my account link",'ProjectTheme'); ?><br/>
                    <strong>##project_name##</strong> - <?php _e("new new project's title",'ProjectTheme'); ?><br/>
                    <strong>##project_link##</strong> - <?php _e('link for the new project','ProjectTheme'); ?><br/>
                    <strong>##winner_bid_value##</strong> - <?php _e('winner bid value','ProjectTheme'); ?><br/>
                    
                    </div></td>
                    </tr>
            		
                    <tr>
                    <td ></td>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_won_project_winner_email_save" value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>
            
            </table>
            </form>
          	
          </div>
        
        <!-- ################################ -->
        
        <div id="won_project_loser">	
          
           <div class="spntxt_bo"><?php _e('This email will be received by the loser bidders when the project is won. 
          Be aware, if you add html tags to this email you must have the allow HTML tags option set to yes.
          Also at the bottom you can see a list of tags you can use in the email body.','ProjectTheme'); ?> </div>
          
          
          <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=email-settings&active_tab=won_project_loser">
            <table width="100%" class="sitemile-table">
    				
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td ><?php _e('Enable this email:','ProjectTheme'); ?></td>
                    <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_won_project_loser_email_enable'); ?></td>
                    </tr>
                    
            	  	<tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="160"><?php _e('Email Subject:','ProjectTheme'); ?></td>
                    <td><input type="text" size="90" name="ProjectTheme_won_project_loser_email_subject" value="<?php echo stripslashes(get_option('ProjectTheme_won_project_loser_email_subject')); ?>"/></td>
                    </tr>
                    

                    
                    <tr>
                    <td valign=top><?php ProjectTheme_theme_bullet(); ?></td>
                    <td valign=top ><?php _e('Email Content:','ProjectTheme'); ?></td>
                    <td><textarea cols="92" rows="10" name="ProjectTheme_won_project_loser_email_message"><?php echo stripslashes(get_option('ProjectTheme_won_project_loser_email_message')); ?></textarea></td>
                    </tr>
                    
                    
                    
                    <tr>
                    <td valign=top></td>
                    <td valign=top ></td>
                    <td><div class="spntxt_bo2">
                    <?php _e('Here is a list of tags you can use in this email:','ProjectTheme'); ?><br/><br/>
                    
                    <strong>##username##</strong> - <?php _e('Loser Bidder\'s Username','ProjectTheme'); ?><br/>
                    <strong>##site_login_url##</strong> - <?php _e('the link to your user login page','ProjectTheme'); ?><br/>
                    <strong>##your_site_name##</strong> - <?php _e("your website's name","ProjectTheme"); ?><br/>
                    <strong>##your_site_url##</strong> - <?php _e("your website's main address",'ProjectTheme'); ?><br/>
                    <strong>##my_account_url##</strong> - <?php _e("your website's my account link",'ProjectTheme'); ?><br/>
                    <strong>##project_name##</strong> - <?php _e("new new project's title",'ProjectTheme'); ?><br/>
                    <strong>##project_link##</strong> - <?php _e('link for the new project','ProjectTheme'); ?><br/>
                    <strong>##user_bid_value##</strong> - <?php _e('the bid value','ProjectTheme'); ?><br/>
                    
                    <strong>##winner_bid_username##</strong> - <?php _e('winner bid username','ProjectTheme'); ?><br/>
                    <strong>##winner_bid_value##</strong> - <?php _e('winner bid value','ProjectTheme'); ?><br/>
                    
                    </div></td>
                    </tr>
            		
                    <tr>
                    <td ></td>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_won_project_loser_email_save" value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>
            
            </table>
            </form>
          	
          </div>
        
        <!-- ################################ -->
        
        
        <div id="message_board_bidder">	
          
           <div class="spntxt_bo"><?php _e('This email will be received by the message owner when the project owner posts a new message on the private messaging board. 
          Be aware, if you add html tags to this email you must have the allow HTML tags option set to yes.
          Also at the bottom you can see a list of tags you can use in the email body.','ProjectTheme'); ?> </div>
          
          
          <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=email-settings&active_tab=message_board_bidder">
            <table width="100%" class="sitemile-table">
    				
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td ><?php _e('Enable this email:','ProjectTheme'); ?></td>
                    <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_message_board_bidder_email_enable'); ?></td>
                    </tr>
                    
            	  	<tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="160"><?php _e('Email Subject:','ProjectTheme'); ?></td>
                    <td><input type="text" size="90" name="ProjectTheme_message_board_bidder_email_subject" value="<?php echo stripslashes(get_option('ProjectTheme_message_board_bidder_email_subject')); ?>"/></td>
                    </tr>
                    

                    
                    <tr>
                    <td valign=top><?php ProjectTheme_theme_bullet(); ?></td>
                    <td valign=top ><?php _e('Email Content:','ProjectTheme'); ?></td>
                    <td><textarea cols="92" rows="10" name="ProjectTheme_message_board_bidder_email_message"><?php echo stripslashes(get_option('ProjectTheme_message_board_bidder_email_message')); ?></textarea></td>
                    </tr>
                    
                    
                    
                    <tr>
                    <td valign=top></td>
                    <td valign=top ></td>
                    <td><div class="spntxt_bo2">
                    <?php _e('Here is a list of tags you can use in this email:','ProjectTheme'); ?><br/><br/>
                    
                    <strong>##username##</strong> - <?php _e('Receiver Username','ProjectTheme'); ?><br/>
                    <strong>##project_username##</strong> - <?php _e('Project Owner Username','ProjectTheme'); ?><br/>
                    <strong>##site_login_url##</strong> - <?php _e('the link to your user login page','ProjectTheme'); ?><br/>
                    <strong>##your_site_name##</strong> - <?php _e("your website's name","ProjectTheme"); ?><br/>
                    <strong>##your_site_url##</strong> - <?php _e("your website's main address",'ProjectTheme'); ?><br/>
                    <strong>##my_account_url##</strong> - <?php _e("your website's my account link",'ProjectTheme'); ?><br/>
                    <strong>##project_name##</strong> - <?php _e("new new project's title",'ProjectTheme'); ?><br/>
                    <strong>##project_link##</strong> - <?php _e('link for the new project','ProjectTheme'); ?><br/>


                    
                    </div></td>
                    </tr>
            		
                    <tr>
                    <td ></td>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_message_board_bidder_email_save" value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>
            
            </table>
            </form>
          	
          </div>
          
          
          <div id="message_board_owner">	
          
           <div class="spntxt_bo"><?php _e('This email will be received by the owner of a project when someone is posting a new message on the private messaging board. 
          Be aware, if you add html tags to this email you must have the allow HTML tags option set to yes.
          Also at the bottom you can see a list of tags you can use in the email body.','ProjectTheme'); ?> </div>
          
          
          <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=email-settings&active_tab=message_board_owner">
            <table width="100%" class="sitemile-table">
    				
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td ><?php _e('Enable this email:','ProjectTheme'); ?></td>
                    <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_message_board_owner_email_enable'); ?></td>
                    </tr>
                    
            	  	<tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="160"><?php _e('Email Subject:','ProjectTheme'); ?></td>
                    <td><input type="text" size="90" name="ProjectTheme_message_board_owner_email_subject" value="<?php echo stripslashes(get_option('ProjectTheme_message_board_owner_email_subject')); ?>"/></td>
                    </tr>
                    

                    
                    <tr>
                    <td valign=top><?php ProjectTheme_theme_bullet(); ?></td>
                    <td valign=top ><?php _e('Email Content:','ProjectTheme'); ?></td>
                    <td><textarea cols="92" rows="10" name="ProjectTheme_message_board_owner_email_message"><?php echo stripslashes(get_option('ProjectTheme_message_board_owner_email_message')); ?></textarea></td>
                    </tr>
                    
                    
                    
                    <tr>
                    <td valign=top></td>
                    <td valign=top ></td>
                    <td><div class="spntxt_bo2">
                    <?php _e('Here is a list of tags you can use in this email:','ProjectTheme'); ?><br/><br/>
                    
                    <strong>##username##</strong> - <?php _e('Project Owner Username','ProjectTheme'); ?><br/>
                    <strong>##message_owner_username##</strong> - <?php _e('Message Owner Username','ProjectTheme'); ?><br/>
                    <strong>##site_login_url##</strong> - <?php _e('the link to your user login page','ProjectTheme'); ?><br/>
                    <strong>##your_site_name##</strong> - <?php _e("your website's name","ProjectTheme"); ?><br/>
                    <strong>##your_site_url##</strong> - <?php _e("your website's main address",'ProjectTheme'); ?><br/>
                    <strong>##my_account_url##</strong> - <?php _e("your website's my account link",'ProjectTheme'); ?><br/>
                    <strong>##project_name##</strong> - <?php _e("new new project's title",'ProjectTheme'); ?><br/>
                    <strong>##project_link##</strong> - <?php _e('link for the new project','ProjectTheme'); ?><br/>

                    
                    </div></td>
                    </tr>
            		
                    <tr>
                    <td ></td>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_message_board_owner_email_save" value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>
            
            </table>
            </form>
          	
          </div>
          
          
          
          
           <div id="bid_project_bidder">	
          
           <div class="spntxt_bo"><?php _e('When there is a new lower bid, this email will be received by the next highest bidder. 
          Be aware, if you add html tags to this email you must have the allow HTML tags option set to yes.
          Also at the bottom you can see a list of tags you can use in the email body.','ProjectTheme'); ?> </div>
          
          
          <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=email-settings&active_tab=bid_project_bidder">
            <table width="100%" class="sitemile-table">
    				
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td ><?php _e('Enable this email:','ProjectTheme'); ?></td>
                    <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_bid_project_bidder_email_enable'); ?></td>
                    </tr>
                    
            	  	<tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="160"><?php _e('Email Subject:','ProjectTheme'); ?></td>
                    <td><input type="text" size="90" name="ProjectTheme_bid_project_bidder_email_subject" value="<?php echo stripslashes(get_option('ProjectTheme_bid_project_bidder_email_subject')); ?>"/></td>
                    </tr>
                    

                    
                    <tr>
                    <td valign=top><?php ProjectTheme_theme_bullet(); ?></td>
                    <td valign=top ><?php _e('Email Content:','ProjectTheme'); ?></td>
                    <td><textarea cols="92" rows="10" name="ProjectTheme_bid_project_bidder_email_message"><?php echo stripslashes(get_option('ProjectTheme_bid_project_bidder_email_message')); ?></textarea></td>
                    </tr>
                    
                    
                    
                    <tr>
                    <td valign=top></td>
                    <td valign=top ></td>
                    <td><div class="spntxt_bo2">
                    <?php _e('Here is a list of tags you can use in this email:','ProjectTheme'); ?><br/><br/>
                    
                    <strong>##username##</strong> - <?php _e('Project Bidder\'s Username','ProjectTheme'); ?><br/>
                    <strong>##site_login_url##</strong> - <?php _e('the link to your user login page','ProjectTheme'); ?><br/>
                    <strong>##your_site_name##</strong> - <?php _e("your website's name","ProjectTheme"); ?><br/>
                    <strong>##your_site_url##</strong> - <?php _e("your website's main address",'ProjectTheme'); ?><br/>
                    <strong>##project_name##</strong> - <?php _e("new new project's title",'ProjectTheme'); ?><br/>
                    <strong>##project_link##</strong> - <?php _e('link for the new project','ProjectTheme'); ?><br/>
                    <strong>##bid_value##</strong> - <?php _e('the bid value','ProjectTheme'); ?><br/>
                    
                    </div></td>
                    </tr>
            		
                    <tr>
                    <td ></td>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_bid_project_bidder_email_save" value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>
            
            </table>
            </form>
          	
          </div>
        <!-- ################################ -->
        
        
        
        
        <div id="bid_project_subs">	
          
           <div class="spntxt_bo"><?php _e('This email will be received by all users subscribed for a certain category when a project gets posted. 
          Be aware, if you add html tags to this email you must have the allow HTML tags option set to yes.
          Also at the bottom you can see a list of tags you can use in the email body.','ProjectTheme'); ?> </div>
          
          
          <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=email-settings&active_tab=bid_project_subs">
            <table width="100%" class="sitemile-table">
    				
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td ><?php _e('Enable this email:','ProjectTheme'); ?></td>
                    <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_subscription_email_enable'); ?></td>
                    </tr>
                    
            	  	<tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="160"><?php _e('Email Subject:','ProjectTheme'); ?></td>
                    <td><input type="text" size="90" name="ProjectTheme_subscription_email_subject" value="<?php echo stripslashes(get_option('ProjectTheme_subscription_email_subject')); ?>"/></td>
                    </tr>
                    

                    
                    <tr>
                    <td valign=top><?php ProjectTheme_theme_bullet(); ?></td>
                    <td valign=top ><?php _e('Email Content:','ProjectTheme'); ?></td>
                    <td><textarea cols="92" rows="10" name="ProjectTheme_subscription_email_message"><?php echo stripslashes(get_option('ProjectTheme_subscription_email_message')); ?></textarea></td>
                    </tr>
                    
                    
                    
                    <tr>
                    <td valign=top></td>
                    <td valign=top ></td>
                    <td><div class="spntxt_bo2">
                    <?php _e('Here is a list of tags you can use in this email:','ProjectTheme'); ?><br/><br/>
                    
                    <strong>##username##</strong> - <?php _e('Receiver Username','ProjectTheme'); ?><br/>
                    <strong>##site_login_url##</strong> - <?php _e('the link to your user login page','ProjectTheme'); ?><br/>
                    <strong>##your_site_name##</strong> - <?php _e("your website's name","ProjectTheme"); ?><br/>
                    <strong>##your_site_url##</strong> - <?php _e("your website's main address",'ProjectTheme'); ?><br/>
                    <strong>##my_account_url##</strong> - <?php _e("your website's my account link",'ProjectTheme'); ?><br/>
                    <strong>##project_name##</strong> - <?php _e("new new project's title",'ProjectTheme'); ?><br/>
                    <strong>##project_link##</strong> - <?php _e('link for the new project','ProjectTheme'); ?><br/>

                    
                    </div></td>
                    </tr>
            		
                    <tr>
                    <td ></td>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_subscription_email_save" value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>
            
            </table>
            </form>
          	
          </div>
        
        
          <div id="payment_on_completed_project">	
          
           <div class="spntxt_bo"><?php _e('This email will be received by the project owner when he pays the service provider. 
          Be aware, if you add html tags to this email you must have the allow HTML tags option set to yes.
          Also at the bottom you can see a list of tags you can use in the email body.','ProjectTheme'); ?> </div>
          
          
          <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=email-settings&active_tab=payment_on_completed_project">
            <table width="100%" class="sitemile-table">
    				
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td ><?php _e('Enable this email:','ProjectTheme'); ?></td>
                    <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_payment_on_completed_project_enable'); ?></td>
                    </tr>
                    
            	  	<tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="160"><?php _e('Email Subject:','ProjectTheme'); ?></td>
                    <td><input type="text" size="90" name="ProjectTheme_payment_on_completed_project_subject" value="<?php echo stripslashes(get_option('ProjectTheme_payment_on_completed_project_subject')); ?>"/></td>
                    </tr>
                    

                    
                    <tr>
                    <td valign=top><?php ProjectTheme_theme_bullet(); ?></td>
                    <td valign=top ><?php _e('Email Content:','ProjectTheme'); ?></td>
                    <td><textarea cols="92" rows="10" name="ProjectTheme_payment_on_completed_project_message"><?php echo stripslashes(get_option('ProjectTheme_payment_on_completed_project_message')); ?></textarea></td>
                    </tr>
                    
                    
                    
                    <tr>
                    <td valign=top></td>
                    <td valign=top ></td>
                    <td><div class="spntxt_bo2">
                    <?php _e('Here is a list of tags you can use in this email:','ProjectTheme'); ?><br/><br/>
                    
                    <strong>##username##</strong> - <?php _e('Project Owner\'s Username','ProjectTheme'); ?><br/>
                    <strong>##site_login_url##</strong> - <?php _e('the link to your user login page','ProjectTheme'); ?><br/>
                    <strong>##your_site_name##</strong> - <?php _e("your website's name","ProjectTheme"); ?><br/>
                    <strong>##your_site_url##</strong> - <?php _e("your website's main address",'ProjectTheme'); ?><br/>

                    <strong>##my_account_url##</strong> - <?php _e("your website's my account link",'ProjectTheme'); ?><br/>
                    <strong>##project_name##</strong> - <?php _e("new new project's title",'ProjectTheme'); ?><br/>
                    <strong>##project_link##</strong> - <?php _e('link for the new project','ProjectTheme'); ?><br/>
                    <strong>##bidder_username##</strong> - <?php _e('the bidder username','ProjectTheme'); ?><br/>
                    <strong>##bid_value##</strong> - <?php _e('the bid value','ProjectTheme'); ?><br/>
                    
                    </div></td>
                    </tr>
            		
                    <tr>
                    <td ></td>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_payment_on_completed_project_email_save" value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>
            
            </table>
            </form>
          	
          </div>
          
          <div id="bid_project_owner">	
          
           <div class="spntxt_bo"><?php _e('This email will be received by the project owner whenever a user bids for his project. 
          Be aware, if you add html tags to this email you must have the allow HTML tags option set to yes.
          Also at the bottom you can see a list of tags you can use in the email body.','ProjectTheme'); ?> </div>
          
          
          <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=email-settings&active_tab=bid_project_owner">
            <table width="100%" class="sitemile-table">
    				
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td ><?php _e('Enable this email:','ProjectTheme'); ?></td>
                    <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_bid_project_owner_email_enable'); ?></td>
                    </tr>
                    
            	  	<tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="160"><?php _e('Email Subject:','ProjectTheme'); ?></td>
                    <td><input type="text" size="90" name="ProjectTheme_bid_project_owner_email_subject" value="<?php echo stripslashes(get_option('ProjectTheme_bid_project_owner_email_subject')); ?>"/></td>
                    </tr>
                    

                    
                    <tr>
                    <td valign=top><?php ProjectTheme_theme_bullet(); ?></td>
                    <td valign=top ><?php _e('Email Content:','ProjectTheme'); ?></td>
                    <td><textarea cols="92" rows="10" name="ProjectTheme_bid_project_owner_email_message"><?php echo stripslashes(get_option('ProjectTheme_bid_project_owner_email_message')); ?></textarea></td>
                    </tr>
                    
                    
                    
                    <tr>
                    <td valign=top></td>
                    <td valign=top ></td>
                    <td><div class="spntxt_bo2">
                    <?php _e('Here is a list of tags you can use in this email:','ProjectTheme'); ?><br/><br/>
                    
                    <strong>##username##</strong> - <?php _e('Project Owner\'s Username','ProjectTheme'); ?><br/>
                    <strong>##site_login_url##</strong> - <?php _e('the link to your user login page','ProjectTheme'); ?><br/>
                    <strong>##your_site_name##</strong> - <?php _e("your website's name","ProjectTheme"); ?><br/>
                    <strong>##your_site_url##</strong> - <?php _e("your website's main address",'ProjectTheme'); ?><br/>

                    <strong>##my_account_url##</strong> - <?php _e("your website's my account link",'ProjectTheme'); ?><br/>
                    <strong>##project_name##</strong> - <?php _e("new new project's title",'ProjectTheme'); ?><br/>
                    <strong>##project_link##</strong> - <?php _e('link for the new project','ProjectTheme'); ?><br/>
                    <strong>##bidder_username##</strong> - <?php _e('the bidder username','ProjectTheme'); ?><br/>
                    <strong>##bid_value##</strong> - <?php _e('the bid value','ProjectTheme'); ?><br/>
                    
                    </div></td>
                    </tr>
            		
                    <tr>
                    <td ></td>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_bid_project_owner_email_save" value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>
            
            </table>
            </form>
          	
          </div>
        
        <!-- ################################ -->
        
        
        
        <div id="post_project_not_approved">	
          
           <div class="spntxt_bo"><?php _e('This email will be received by your users after posting a new project on your website if the project is automatically approved. 
          Be aware, if you add html tags to this email you must have the allow HTML tags option set to yes.
          Also at the bottom you can see a list of tags you can use in the email body.','ProjectTheme'); ?> </div>
          
          
          <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=email-settings&active_tab=post_project_not_approved">
            <table width="100%" class="sitemile-table">
    				
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td ><?php _e('Enable this email:','ProjectTheme'); ?></td>
                    <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_new_project_email_approved_enable'); ?></td>
                    </tr>
                    
            	  	<tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="160"><?php _e('Email Subject:','ProjectTheme'); ?></td>
                    <td><input type="text" size="90" name="ProjectTheme_new_project_email_approved_subject" value="<?php echo stripslashes(get_option('ProjectTheme_new_project_email_approved_subject')); ?>"/></td>
                    </tr>
                    

                    
                    <tr>
                    <td valign=top><?php ProjectTheme_theme_bullet(); ?></td>
                    <td valign=top ><?php _e('Email Content:','ProjectTheme'); ?></td>
                    <td><textarea cols="92" rows="10" name="ProjectTheme_new_project_email_approved_message"><?php echo stripslashes(get_option('ProjectTheme_new_project_email_approved_message')); ?></textarea></td>
                    </tr>
                    
                    
                    
                    <tr>
                    <td valign=top></td>
                    <td valign=top ></td>
                    <td><div class="spntxt_bo2">
                    <?php _e('Here is a list of tags you can use in this email:','ProjectTheme'); ?><br/><br/>
                    
                    <strong>##username##</strong> - <?php _e('username','ProjectTheme'); ?><br/>
                    <strong>##site_login_url##</strong> - <?php _e('the link to your user login page','ProjectTheme'); ?><br/>
                    <strong>##your_site_name##</strong> - <?php _e("your website's name","ProjectTheme"); ?><br/>
                    <strong>##your_site_url##</strong> - <?php _e("your website's main address",'ProjectTheme'); ?><br/>
                    <strong>##my_account_url##</strong> - <?php _e("your website's my account link",'ProjectTheme'); ?><br/>
                    <strong>##project_name##</strong> - <?php _e("new new project's title",'ProjectTheme'); ?><br/>
                    <strong>##project_link##</strong> - <?php _e('link for the new project','ProjectTheme'); ?><br/>
                    
                    </div></td>
                    </tr>
            		
                    <tr>
                    <td ></td>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_save33" value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>
            
            </table>
            </form>
          	
          </div>
        
        <!-- ################################## -->
        
        <div id="post_project_approved">	
          
           <div class="spntxt_bo"><?php _e('This email will be received by your users after posting a new project on your website if the project is not automatically approved. 
          Be aware, if you add html tags to this email you must have the allow HTML tags option set to yes.
          Also at the bottom you can see a list of tags you can use in the email body.','ProjectTheme'); ?> </div>
          
          
          <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=email-settings&active_tab=post_project_approved">
            <table width="100%" class="sitemile-table">
    				
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td ><?php _e('Enable this email:','ProjectTheme'); ?></td>
                    <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_new_project_email_not_approved_enable'); ?></td>
                    </tr>
                    
            	  	<tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="160"><?php _e('Email Subject:','ProjectTheme'); ?></td>
                    <td><input type="text" size="90" name="ProjectTheme_new_project_email_not_approved_subject" value="<?php echo stripslashes(get_option('ProjectTheme_new_project_email_not_approved_subject')); ?>"/></td>
                    </tr>
                    

                    
                    <tr>
                    <td valign=top><?php ProjectTheme_theme_bullet(); ?></td>
                    <td valign=top ><?php _e('Email Content:','ProjectTheme'); ?></td>
                    <td><textarea cols="92" rows="10" name="ProjectTheme_new_project_email_not_approved_message"><?php echo stripslashes(get_option('ProjectTheme_new_project_email_not_approved_message')); ?></textarea></td>
                    </tr>
                    
                    
                    
                    <tr>
                    <td valign=top></td>
                    <td valign=top ></td>
                    <td><div class="spntxt_bo2">
                    <?php _e('Here is a list of tags you can use in this email:','ProjectTheme'); ?><br/><br/>
                    
                    <strong>##username##</strong> - <?php _e('project owner username','ProjectTheme'); ?><br/>
                    <strong>##site_login_url##</strong> - <?php _e('the link to your user login page','ProjectTheme'); ?><br/>
                    <strong>##your_site_name##</strong> - <?php _e("your website's name","ProjectTheme"); ?><br/>
                    <strong>##your_site_url##</strong> - <?php _e("your website's main address",'ProjectTheme'); ?><br/>
                    <strong>##my_account_url##</strong> - <?php _e("your website's my account link",'ProjectTheme'); ?><br/>
                    <strong>##project_name##</strong> - <?php _e("new new project's title",'ProjectTheme'); ?><br/>
                    <strong>##project_link##</strong> - <?php _e('link for the new project','ProjectTheme'); ?><br/>
                    
                    </div></td>
                    </tr>
            		
                    <tr>
                    <td ></td>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_save32" value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>
            
            </table>
            </form>
          	
          </div>
        
        <!-- ############################### -->
        
        
        <div id="post_project_not_approved_admin">	
          
           <div class="spntxt_bo"><?php _e('This email will be received by the admin when someone posts a project on the website to be approved.
          Be aware, if you add html tags to this email you must have the allow HTML tags option set to yes.
          Also at the bottom you can see a list of tags you can use in the email body.','ProjectTheme'); ?> </div>
          
          
          <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=email-settings&active_tab=post_project_not_approved_admin">
            <table width="100%" class="sitemile-table">
    				
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td ><?php _e('Enable this email:','ProjectTheme'); ?></td>
                    <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_new_project_email_approve_admin_enable'); ?></td>
                    </tr>
                    
            	  	<tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="160"><?php _e('Email Subject:','ProjectTheme'); ?></td>
                    <td><input type="text" size="90" name="ProjectTheme_new_project_email_approve_admin_subject" value="<?php echo stripslashes(get_option('ProjectTheme_new_project_email_approve_admin_subject')); ?>"/></td>
                    </tr>
                    

                    
                    <tr>
                    <td valign=top><?php ProjectTheme_theme_bullet(); ?></td>
                    <td valign=top ><?php _e('Email Content:','ProjectTheme'); ?></td>
                    <td><textarea cols="92" rows="10" name="ProjectTheme_new_project_email_approve_admin_message"><?php echo stripslashes(get_option('ProjectTheme_new_project_email_approve_admin_message')); ?></textarea></td>
                    </tr>
                    
                    
                    
                    <tr>
                    <td valign=top></td>
                    <td valign=top ></td>
                    <td><div class="spntxt_bo2">
                    <?php _e('Here is a list of tags you can use in this email:','ProjectTheme'); ?><br/><br/>
                    
                    <strong>##site_login_url##</strong> - <?php _e('the link to your user login page','ProjectTheme'); ?><br/>
                    <strong>##your_site_name##</strong> - <?php _e("your website's name","ProjectTheme"); ?><br/>
                    <strong>##your_site_url##</strong> - <?php _e("your website's main address",'ProjectTheme'); ?><br/>
                    <strong>##my_account_url##</strong> - <?php _e("your website's my account link",'ProjectTheme'); ?><br/>
                    <strong>##project_name##</strong> - <?php _e("new new project's title",'ProjectTheme'); ?><br/>
                    <strong>##project_link##</strong> - <?php _e('link for the new project','ProjectTheme'); ?><br/>
                    
                    </div></td>
                    </tr>
            		
                    <tr>
                    <td ></td>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_save31" value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>
            
            </table>
            </form>
          	
          </div>
        
        
    <!-- ######################### -->    
        
        
         <div id="post_project_approved_admin">	
          
           <div class="spntxt_bo"><?php _e('This email will be received by the admin when someone posts a project on the website website. This email will be received if the the project is automatically approved.
          Be aware, if you add html tags to this email you must have the allow HTML tags option set to yes.
          Also at the bottom you can see a list of tags you can use in the email body.','ProjectTheme'); ?> </div>
          
          
          <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=email-settings&active_tab=post_project_approved_admin">
            <table width="100%" class="sitemile-table">
    				
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td ><?php _e('Enable this email:','ProjectTheme'); ?></td>
                    <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_new_project_email_not_approve_admin_enable'); ?></td>
                    </tr>
                    
            	  	<tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="160"><?php _e('Email Subject:','ProjectTheme'); ?></td>
                    <td><input type="text" size="90" name="ProjectTheme_new_project_email_not_approve_admin_subject" value="<?php echo stripslashes(get_option('ProjectTheme_new_project_email_not_approve_admin_subject')); ?>"/></td>
                    </tr>
                    

                    
                    <tr>
                    <td valign=top><?php ProjectTheme_theme_bullet(); ?></td>
                    <td valign=top ><?php _e('Email Content:','ProjectTheme'); ?></td>
                    <td><textarea cols="92" rows="10" name="ProjectTheme_new_project_email_not_approve_admin_message"><?php echo stripslashes(get_option('ProjectTheme_new_project_email_not_approve_admin_message')); ?></textarea></td>
                    </tr>
                    
                    
                    
                    <tr>
                    <td valign=top></td>
                    <td valign=top ></td>
                    <td><div class="spntxt_bo2">
                    <?php _e('Here is a list of tags you can use in this email:','ProjectTheme'); ?><br/><br/>
                    
                    <strong>##site_login_url##</strong> - <?php _e('the link to your user login page','ProjectTheme'); ?><br/>
                    <strong>##your_site_name##</strong> - <?php _e("your website's name","ProjectTheme"); ?><br/>
                    <strong>##your_site_url##</strong> - <?php _e("your website's main address",'ProjectTheme'); ?><br/>
                    <strong>##my_account_url##</strong> - <?php _e("your website's my account link",'ProjectTheme'); ?><br/>
                    <strong>##project_name##</strong> - <?php _e("new new project's title",'ProjectTheme'); ?><br/>
                    <strong>##project_link##</strong> - <?php _e('link for the new project','ProjectTheme'); ?><br/>
                    
                    </div></td>
                    </tr>
            		
                    <tr>
                    <td ></td>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_save3" value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>
            
            </table>
            </form>
          	
          </div>
        
        
        <!--################################ -->
        
        <div id="tabs1" style="display: block; ">
        	<form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=email-settings&active_tab=tabs1">
            <table width="100%" class="sitemile-table">
    				
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="160">Email From Name:</td>
                    <td><input type="text" size="45" name="ProjectTheme_email_name_from" value="<?php echo stripslashes(get_option('ProjectTheme_email_name_from')); ?>"/></td>
                    </tr>
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td >Email From Address:</td>
                    <td><input type="text" size="45" name="ProjectTheme_email_addr_from" value="<?php echo stripslashes(get_option('ProjectTheme_email_addr_from')); ?>"/></td>
                    </tr>
                    
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td >Allow HTML in emails:</td>
                    <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_allow_html_emails'); ?></td>
                    </tr>
                    
        
                    <tr>
                    <td ></td>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_save1" value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>
            
            </table>      
          	</form>
        </div> 
          
        <!-- ################################ -->  
                
        <div id="new_user_email" style="display: none; ">
        	<div class="spntxt_bo"><?php _e('This email will be received by all new users who register on your website. 
          Be aware, if you add html tags to this email you must have the allow HTML tags option set to yes.
          Also at the bottom you can see a list of tags you can use in the email body.','ProjectTheme'); ?> </div>
          
          
          <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=email-settings&active_tab=tabs2">
            <table width="100%" class="sitemile-table">
    		
            	  	<tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="160"><?php _e('Email Subject:','ProjectTheme'); ?></td>
                    <td><input type="text" size="90" name="ProjectTheme_new_user_email_subject" value="<?php echo stripslashes(get_option('ProjectTheme_new_user_email_subject')); ?>"/></td>
                    </tr>
                    

                    
                    <tr>
                    <td valign=top><?php ProjectTheme_theme_bullet(); ?></td>
                    <td valign=top ><?php _e('Email Content:','ProjectTheme'); ?></td>
                    <td><textarea cols="92" rows="10" name="ProjectTheme_new_user_email_message"><?php echo stripslashes(get_option('ProjectTheme_new_user_email_message')); ?></textarea></td>
                    </tr>
                    

                    
                    
                    <tr>
                    <td valign=top></td>
                    <td valign=top ></td>
                    <td><div class="spntxt_bo2">
                    <?php _e('Here is a list of tags you can use in this email:','ProjectTheme'); ?><br/><br/>
                    
                    <strong>##username##</strong> - <?php _e("your new username",'ProjectTheme'); ?><br/>
                    <strong>##username_email##</strong> - <?php _e("your new user's email",'ProjectTheme'); ?><br/>
                    <strong>##user_password##</strong> - <?php _e("your new user's password",'ProjectTheme'); ?><br/>
                    <strong>##site_login_url##</strong> - <?php _e('the link to your user login page','ProjectTheme'); ?><br/>
                    <strong>##your_site_name##</strong> - <?php _e("your website's name","ProjectTheme"); ?><br/>
                    <strong>##your_site_url##</strong> - <?php _e("your website's main address",'ProjectTheme'); ?>
                    
                    </div></td>
                    </tr>
            		
                    <tr>
                    <td ></td>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_save2" value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>
            
            </table>
            </form>
          
        </div> 
        
        <!-- ################################ -->  
                
        <div id="admin_new_user_email" style="display: none; "> 
        	 <div class="spntxt_bo"><?php _e('This email will be received by the admin when a new user registers on the website. 
          Be aware, if you add html tags to this email you must have the allow HTML tags option set to yes.
          Also at the bottom you can see a list of tags you can use in the email body.','ProjectTheme'); ?> </div>
          
          
          <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=email-settings&active_tab=tabs_new_user_email_admin">
            <table width="100%" class="sitemile-table">
    		
            	  	<tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="160"><?php _e('Email Subject:','ProjectTheme'); ?></td>
                    <td><input type="text" size="90" name="ProjectTheme_new_user_email_admin_subject" value="<?php echo stripslashes(get_option('ProjectTheme_new_user_email_admin_subject')); ?>"/></td>
                    </tr>
                    

                    
                    <tr>
                    <td valign=top><?php ProjectTheme_theme_bullet(); ?></td>
                    <td valign=top ><?php _e('Email Content:','ProjectTheme'); ?></td>
                    <td><textarea cols="92" rows="10" name="ProjectTheme_new_user_email_admin_message"><?php echo stripslashes(get_option('ProjectTheme_new_user_email_admin_message')); ?></textarea></td>
                    </tr>
                    
                    
                    
                    <tr>
                    <td valign=top></td>
                    <td valign=top ></td>
                    <td><div class="spntxt_bo2">
                    <?php _e('Here is a list of tags you can use in this email:','ProjectTheme'); ?><br/><br/>
                    
                    <strong>##username##</strong> - <?php _e('your new username',"ProjectTheme"); ?><br/>
                    <strong>##username_email##</strong> - <?php _e("your new user's email","ProjectTheme"); ?><br/>
                    <strong>##site_login_url##</strong> - <?php _e('the link to your user login page','ProjectTheme'); ?><br/>
                    <strong>##your_site_name##</strong> - <?php _e("your website's name","ProjectTheme"); ?><br/>
                    <strong>##your_site_url##</strong> - <?php _e("your website's main address",'ProjectTheme'); ?>
                    
                    </div></td>
                    </tr>
            		
                    <tr>
                    <td ></td>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_save_new_user_email_admin" value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>
            
            </table>
            </form>
        </div> 
    
    
    	<?php do_action('ProjectTheme_save_emails_contents'); ?>
    
    </div> 
    
    
    <?php	
	
	echo '</div>';
}
/******************************************************************
*
*	Admin Menu - New Function - sitemile[at]sitemile.com
*	developed by Andrei Saioc - andreisaioc[at]gmail.com
*
*******************************************************************/

function projectTheme_summary_scr()
{
	global $menu_admin_project_theme_bull;
	echo '<div class="wrap">';
	echo '<div class="icon32" id="icon-options-general-summary"><br/></div>';	
	echo '<h2 class="my_title_class_sitemile">ProjectTheme Site Summary</h2>';
	?>
    
        <div id="usual2" class="usual"> 
  <ul> 
    <li><a href="#tabs1" class="selected">General Overview</a></li> 
   <!-- <li><a href="#tabs2">More Information</a></li> -->
  </ul> 
  <div id="tabs1" style="display: block; ">
    	<table width="100%" class="sitemile-table">
          <tr>
          <td width="200">Total number of projects</td>
          <td><?php echo ProjectTheme_get_total_nr_of_projects(); ?></td>
          </tr>
          
          
          <tr>
          <td>Open Projects</td>
          <td><?php echo ProjectTheme_get_total_nr_of_open_projects(); ?></td>
          </tr>
          
          <tr>
          <td>Closed & Finished</td>
          <td><?php echo ProjectTheme_get_total_nr_of_closed_projects(); ?></td>
          </tr>
          
<!--          
          <tr>
          <td>Disputed & Not Finished</td>
          <td>12</td>
          </tr>
  -->        
          
          <tr>
          <td>Total Users</td>
          <td><?php
			$result = count_users();
			echo 'There are ', $result['total_users'], ' total users';
			foreach($result['avail_roles'] as $role => $count)
				echo ', ', $count, ' are ', $role, 's';
			echo '.';
			?></td>
          </tr>
          
          </table>
        
          </div> 
          <div id="tabs2" style="display: none; ">More content in tab 2.</div> 
        </div> 
    
    
    <?php	
	
	echo '</div>';
}

/******************************************************************
*
*	Admin Menu - New Function - sitemile[at]sitemile.com
*	developed by Andrei Saioc - andreisaioc[at]gmail.com
*
*******************************************************************/

function projectTheme_pricing_options()
{
	$id_icon 		= 'icon-options-general4';
	$ttl_of_stuff 	= 'ProjectTheme - '.__('Pricing Settings','ProjectTheme');
	$arr = array("yes" => __("Yes",'ProjectTheme'), "no" => __("No",'ProjectTheme'));
	$sep = array( "," => __('Comma (,)','ProjectTheme'), "." => __("Point (.)",'ProjectTheme'));
	$frn = array( "front" => __('In front of sum (eg: $50)','ProjectTheme'), "back" => __("After the sum (eg: 50$)",'ProjectTheme'));
	global $menu_admin_projecttheme_theme_bull, $wpdb;
	
	$arr_currency = array("USD" => "US Dollars", "EUR" => "Euros", "CAD" => "Canadian Dollars", "CHF" => "Swiss Francs","GBP" => "British Pounds",
						  "AUD" => "Australian Dollars","NZD" => "New Zealand Dollars","BRL" => "Brazilian Real", 'PLN' => 'Polish zloty',
						  "SGD" => "Singapore Dollars","SEK" => "Swidish Kroner","NOK" => "Norwegian Kroner","DKK" => "Danish Kroner",
						  "MXN" => "Mexican Pesos","JPY" => "Japanese Yen","EUR" => "Euros", "ZAR" => "South Africa Rand", 'RUB' => 'Russian Ruble' , "TRY" => "Turkish Lyra",  "RON" => "Romanian Lei",
						  "HUF" => "Hungarian Forint", 'PHP' => 'Philippine peso' ,  'INR' => 'Indian Rupee'
						  
						  );
	
	//------------------------------------------------------
	
	echo '<div class="wrap">';
	echo '<div class="icon32" id="'.$id_icon.'"><br/></div>';	
	echo '<h2 class="my_title_class_sitemile">'.$ttl_of_stuff.'</h2>';	


	if(isset($_POST['ProjectTheme_save1']))
	{
		$ProjectTheme_currency 						= trim($_POST['ProjectTheme_currency']);
		$ProjectTheme_currency_symbol 				= trim($_POST['ProjectTheme_currency_symbol']);
		$ProjectTheme_currency_position 			= trim($_POST['ProjectTheme_currency_position']);
		$ProjectTheme_decimal_sum_separator 		= trim($_POST['ProjectTheme_decimal_sum_separator']);
		$ProjectTheme_thousands_sum_separator 		= trim($_POST['ProjectTheme_thousands_sum_separator']);

		update_option('ProjectTheme_currency', 					$ProjectTheme_currency);
		update_option('ProjectTheme_currency_symbol', 			$ProjectTheme_currency_symbol);
		update_option('ProjectTheme_currency_position', 		$ProjectTheme_currency_position);
		update_option('ProjectTheme_decimal_sum_separator', 	$ProjectTheme_decimal_sum_separator);
		update_option('ProjectTheme_thousands_sum_separator', 	$ProjectTheme_thousands_sum_separator);

	
		
		echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';	
	}


	if(isset($_POST['ProjectTheme_save2']))
	{
		$projectTheme_base_fee 						= trim($_POST['projectTheme_base_fee']);
		$projectTheme_featured_fee 					= trim($_POST['projectTheme_featured_fee']);
		$projectTheme_sealed_bidding_fee 			= trim($_POST['projectTheme_sealed_bidding_fee']);
		$projectTheme_hide_project_fee 				= trim($_POST['projectTheme_hide_project_fee']);
		$projectTheme_fee_after_paid 				= trim($_POST['projectTheme_fee_after_paid']);

		update_option('projectTheme_base_fee', 					$projectTheme_base_fee);
		update_option('projectTheme_featured_fee', 				$projectTheme_featured_fee);
		update_option('projectTheme_sealed_bidding_fee', 		$projectTheme_sealed_bidding_fee);
		update_option('projectTheme_hide_project_fee', 			$projectTheme_hide_project_fee);
		update_option('projectTheme_fee_after_paid', 			$projectTheme_fee_after_paid);

	
		
		echo '<div class="saved_thing">'.__('Settings saved!','ProjectTheme').'</div>';	
	}


	?>
    
        <div id="usual2" class="usual"> 
  <ul> 
    <li><a href="#tabs1"><?php _e('Main Details','ProjectTheme'); ?></a></li>  
    <li><a href="#tabs2"><?php _e('Project Fees','ProjectTheme'); ?></a></li>
    <li><a href="#tabs3"><?php _e('Project Budgets','ProjectTheme'); ?></a></li> 
  </ul> 
  <div id="tabs1" style="display: block; ">
    	
        <form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=pricing-settings&active_tab=tabs1">
            <table width="100%" class="sitemile-table">
    				
                     <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td ><?php _e('Site currency:','ProjectTheme'); ?></td>
                    <td><?php echo ProjectTheme_get_option_drop_down($arr_currency, 'ProjectTheme_currency'); ?></td>
                    </tr>
                    
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="160"><?php _e('Currency symbol:','ProjectTheme'); ?></td>
                    <td><input type="text" size="6" name="ProjectTheme_currency_symbol" value="<?php echo get_option('ProjectTheme_currency_symbol'); ?>"/> </td>
                    </tr>
                    
                     <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td ><?php _e('Currency symbol position:','ProjectTheme'); ?></td>
                    <td><?php echo ProjectTheme_get_option_drop_down($frn, 'ProjectTheme_currency_position'); ?></td>
                    </tr>
                    
                    
                     <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td ><?php _e('Decimals sum separator:','ProjectTheme'); ?></td>
                    <td><?php echo ProjectTheme_get_option_drop_down($sep, 'ProjectTheme_decimal_sum_separator'); ?></td>
                    </tr>
                    
                     <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td ><?php _e('Thousands sum separator:','ProjectTheme'); ?></td>
                    <td><?php echo ProjectTheme_get_option_drop_down($sep, 'ProjectTheme_thousands_sum_separator'); ?></td>
                    </tr>
      
                   
                    
        
                    <tr>
                    <td ></td>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_save1" value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>
            
            </table>      
          	</form>
          
  </div> 
  
  
  <div id="tabs3" style="display: none; ">
  
  <!-- ############# -->
            
            
            <h3>Define New Package</h3>
            
            
            <div class="MY_mo_gogo" id="">
                
                    <div class="go_go1">
                    <div class="go_go2_1">Price Range Name:</div> <div class="go_go2_2"><input id="bidding_interval_name_new" value="" /></div>
                    </div>
                    
                    
                    <div class="go_go1">
                    <div class="go_go2_1">Low Limit(<?php echo ProjectTheme_currency(); ?>):</div> 
                    <div class="go_go2_2"><input id="low_limit_new" value="" /></div>
                    </div>
                    
                    
                    <div class="go_go1">
                    <div class="go_go2_1">High Limit(<?php echo ProjectTheme_currency(); ?>):</div> 
                    <div class="go_go2_2"><input id="high_limit_new" value="" /> </div>
                    </div>
                
                	<div class="go_go1"><a href="#" id="new_package_action" rel="" class="green_btn2">Add New Package</a>
                    </div>
                
                </div>
            
            
		<!-- ############### -->
        <h3>Current Defined Price Ranges</h3>
        <div id="my_packages_stuff">
        
		<?php
		
		global $wpdb;
		$s = "select * from ".$wpdb->prefix."project_bidding_intervals order by low_limit asc";
		$r = $wpdb->get_results($s);
		
		foreach($r as $row)
		{
			?>
            	<div class="MY_mo_gogo" id="my_pkg_cell<?php echo $row->id; ?>">
                
                    <div class="go_go1">
                    <div class="go_go2_1">Price Range Name:</div> 
                    <div class="go_go2_2"><input name="" id="bidding_interval_name_cell<?php echo $row->id; ?>" 
                    value="<?php echo $row->bidding_interval_name; ?>" /></div>
                    </div>
                    
                    
                    <div class="go_go1">
                    <div class="go_go2_1">Low Limit (<?php echo ProjectTheme_currency(); ?>):</div> 
                    <div class="go_go2_2"><input name="" id="low_limit_cell<?php echo $row->id; ?>" value="<?php echo $row->low_limit; ?>" /></div>
                    </div>
                    
                    
                    <div class="go_go1">
                    <div class="go_go2_1">High Limit (<?php echo ProjectTheme_currency(); ?>):</div> 
                    <div class="go_go2_2"><input name="" id="high_limit_cell<?php echo $row->id; ?>" value="<?php echo $row->high_limit; ?>" /> </div>
                    </div>
                
                	<div class="go_go1"><a href="#" rel="<?php echo $row->id; ?>" class="update_package green_btn2">Update Package</a> 
                    <a href="#" rel="<?php echo $row->id; ?>" class="delete_package green_btn">Delete Package</a>
                    </div>
                
                </div>
            
            <?php
			
		}
		
		?>
  
  </div></div>
  
  <div id="tabs2" style="display: none; ">
  
  	<form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=pricing-settings&active_tab=tabs2">
            <table width="100%" class="sitemile-table">
    				
                
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td width="240" ><?php _e('Base Listing Fee:','ProjectTheme'); ?></td>
                    <td><input type="text" name="projectTheme_base_fee" size="10" value="<?php echo  get_option('projectTheme_base_fee'); ?>"  /> <?php echo projectTheme_currency(); ?></td>
                    </tr>
                    
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td ><?php _e('Featured Listing Fee:','ProjectTheme'); ?></td>
                    <td><input type="text" name="projectTheme_featured_fee" size="10" value="<?php echo  get_option('projectTheme_featured_fee'); ?>"  /> <?php echo projectTheme_currency(); ?></td>
                    </tr>
                    
                    
         			<tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td ><?php _e('Sealed Bidding Fee:','ProjectTheme'); ?></td>
                    <td><input type="text" name="projectTheme_sealed_bidding_fee" size="10" value="<?php echo  get_option('projectTheme_sealed_bidding_fee'); ?>"  /> <?php echo projectTheme_currency(); ?></td>
                    </tr>
                    
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td ><?php _e('Hide Project from Search Engines Fee:','ProjectTheme'); ?></td>
                    <td><input type="text" name="projectTheme_hide_project_fee" size="10" value="<?php echo  get_option('projectTheme_hide_project_fee'); ?>"  /> <?php echo projectTheme_currency(); ?></td>
                    </tr>
                    
                    
                    <tr>
                    <td valign=top width="22"><?php ProjectTheme_theme_bullet(); ?></td>
                    <td ><?php _e('Fee Taken out of Each Project: ','ProjectTheme'); ?></td>
                    <td><input type="text" name="projectTheme_fee_after_paid" size="5" value="<?php echo  get_option('projectTheme_fee_after_paid'); ?>"  /> %</td>
                    </tr>
                    
                   
                    
        
                    <tr>
                    <td ></td>
                    <td ></td>
                    <td><input type="submit" name="ProjectTheme_save2" value="<?php _e('Save Options','ProjectTheme'); ?>"/></td>
                    </tr>
            
            </table>      
          	</form>
  
  
  </div> 
        </div> 
    
    
    <?php	
	
	echo '</div>';
}

/******************************************************************
*
*	Admin Menu - New Function - sitemile[at]sitemile.com
*	developed by Andrei Saioc - andreisaioc[at]gmail.com
*
*******************************************************************/

function projectTheme_cust_prcng()
{
	global $menu_admin_project_theme_bull, $wpdb;
	echo '<div class="wrap">';
	echo '<div class="icon32" id="icon-options-general-custpricing"><br/></div>';	
	echo '<h2 class="my_title_class_sitemile">ProjectTheme Custom Pricing</h2>';
	
	$arr = array("yes" => "Yes", "no" => "No");
	         
	if(isset($_POST['my_submit']))
	{
		$projectTheme_enable_custom_posting 		= trim($_POST['projectTheme_enable_custom_posting']);
		update_option('projectTheme_enable_custom_posting', $projectTheme_enable_custom_posting);
			
		//---------------
		
		$customs = $_POST['customs'];
		for($i=0;$i<count($customs);$i++)
		{
			$ids = $customs[$i];
			$val =trim( $_POST['projectTheme_theme_custom_cat_'.$ids]);
			update_option('projectTheme_theme_custom_cat_'.$ids,$val);			
			
		}
		
		//---------------
		
		echo '<div class="saved_thing">Settings saved!</div>';
		
	}
	
	   if(isset($_POST['my_submit2']))
	{
		$projectTheme_enable_custom_bidding 		= $_POST['projectTheme_enable_custom_bidding'];
		update_option('projectTheme_enable_custom_bidding',$projectTheme_enable_custom_bidding);
			
		//---------------
		
		$customs = $_POST['customs'];
		for($i=0;$i<count($customs);$i++)
		{
			$ids = $customs[$i];
			$val = trim($_POST['projectTheme_theme_bidding_cat_'.$ids]);
			update_option('projectTheme_theme_bidding_cat_'.$ids,$val);			
			
		}
		
		//---------------
		
		echo '<div class="saved_thing">Settings saved!</div>';
		
	}
	
	?>
    
        <div id="usual2" class="usual"> 
  <ul> 
    <li><a href="#tabs1" class="selected">Custom Posting Fees</a></li> 
    <li><a href="#tabs2">Custom Bidding Fees</a></li> 
  </ul> 
  <div id="tabs1" style="display: block; ">
    	 <form method="post">
    	<table width="100%" class="sitemile-table">
        
        
        <tr>
        <td width="220" >Enable Custom Posting fees:</td>
        <td><?php echo ProjectTheme_get_option_drop_down($arr, 'projectTheme_enable_custom_posting'); ?></td>
        </tr>
        
                

        
        <?php echo ProjectTheme_project_clear_table(2); ?>
        
         <tr>
        <td width="220" ><strong>Set Fees for each Category:</strong></td>
        <td></td>
        </tr>
        <?php echo ProjectTheme_project_clear_table(2); ?>
        
        <?php
		  
		  $categories =  get_categories('taxonomy=project_cat&hide_empty=0&orderby=name');
		  //$blg = get_option('project_theme_blog_category');
			
		  foreach ($categories as $category) 
		  {
			if(1) //$category->cat_name != "Uncategorized" && $category->cat_ID != $blg )
			{
				echo '<tr>';
				echo '<td>'.$category->cat_name.'</td>';
				echo '<td><input type="text" size="6" value="'.get_option('projectTheme_theme_custom_cat_'.$category->cat_ID).'" 
				name="projectTheme_theme_custom_cat_'.$category->cat_ID.'" /> '.projectTheme_currency().'
				<input type="hidden" name="customs[]" value="'.$category->cat_ID.'" />
				</td>';
	
				echo '</tr>';
			}
		  
		  }
		
		?>
          <?php echo ProjectTheme_project_clear_table(2); ?>
        
                <tr>
        <td ></td>
        <td><input type="submit" name="my_submit" value="Save these Settings!" /></td>
        </tr>
        
        </table>
    </form>
            
        
          </div> 
          <div id="tabs2" style="display: none; ">
          
          <form method="post">
    	<table width="100%" class="sitemile-table">
        
        
        <tr>
        <td width="220" >Enable Custom Bidding fees:</td>
        <td><?php echo ProjectTheme_get_option_drop_down($arr, 'projectTheme_enable_custom_bidding'); ?></td>
        </tr>
        
                

        
        <?php echo ProjectTheme_project_clear_table(2); ?>
        
         <tr>
        <td width="220" ><strong>Set Fees for each Category:</strong></td>
        <td></td>
        </tr>
        <?php echo ProjectTheme_project_clear_table(2); ?>
        
        <?php
		  
		  $categories =  get_categories('taxonomy=project_cat&hide_empty=0&orderby=name');

			
		  foreach ($categories as $category) 
		  {
			if(1) //$category->cat_name != "Uncategorized" && $category->cat_ID != $blg )
			{
				echo '<tr>';
				echo '<td>'.$category->cat_name.'</td>';
				echo '<td><input type="text" size="6" value="'.get_option('projectTheme_theme_bidding_cat_'.$category->cat_ID).'" 
				name="projectTheme_theme_bidding_cat_'.$category->cat_ID.'" /> '.projectTheme_currency().'
				<input type="hidden" name="customs[]" value="'.$category->cat_ID.'" />
				</td>';
	
				echo '</tr>';
			}
		  
		  }
		
		?>
          <?php echo ProjectTheme_project_clear_table(2); ?>
        
                <tr>
        <td ></td>
        <td><input type="submit" name="my_submit2" value="Save these Settings!" /></td>
        </tr>
        
        </table>
    </form>
          
          </div> 
        </div> 
    
    
    <?php	
	
	echo '</div>';
}


/******************************************************************
*
*	Admin Menu - New Function - sitemile[at]sitemile.com
*	developed by Andrei Saioc - andreisaioc[at]gmail.com
*
*******************************************************************/
function projectTheme_custom_user_fields_scr()
{

global $menu_admin_project_theme_bull, $wpdb;
	echo '<div class="wrap">';
	echo '<div class="icon32" id="icon-options-general-custfields"><br/></div>';	
	echo '<h2 class="my_title_class_sitemile">ProjectTheme User Fields</h2>';
	?>
    
    <script src="<?php echo get_bloginfo('template_url'); ?>/js/jquery.form.js"></script>

	<?php
	
	if(isset($_POST['add_new_field']))
	{
		$field_name 	= trim($_POST['field_name']);
		$field_type 	= $_POST['field_type'];
		$field_order 	= trim($_POST['field_order']);
		$field_category = $_POST['field_category'];
		
		
		if(empty($field_name)) echo '<div class="delete_thing">Field name cannot be empty!</div>';
		else
		{
			//$wpdb->show_errors = true;
			
			$ss = "insert into ".$wpdb->prefix."project_user_custom_fields (name,tp,ordr,cate) 
														values( '$field_name','$field_type','$field_order','$field_category')";
			$wpdb->query($ss);
			
		 
			
			//----------------------------
			
			$ss = "select id from ".$wpdb->prefix."project_user_custom_fields where name='$field_name' AND tp='$field_type'";
			$rows = $wpdb->get_results($ss);
			
			foreach($rows as $row)
			{
			
				$custid = $row->id;
				
				if($field_category != 'all')
				{
					
					for($i=0;$i<count($_POST['field_cats']);$i++)
						if(isset($_POST['field_cats'][$i]))
							{
								$field_category = $_POST['field_cats'][$i];
								$wpdb->query("insert into ".$wpdb->prefix."project_user_custom_relations (custid,catid) values('$custid','$field_category')");
								
							}
					if(empty($field_category)) $field_category = 'all';
				}
				else
					$field_category = 'all';
			}	
			//-------------------------------
			

			
			echo '<div class="saved_thing">Custom field added!</div>';
		}
	}
	
	
	$arr = array("yes" => "Yes", "no" => "No");
	
	if(isset($_GET['edit_field']))
	{
		$custid = $_GET['edit_field'];
		
			if(isset($_POST['save_new_field']))
				{
					$field_name 	= trim($_POST['field_name']);
					//$field_type 	= $_POST['field_type'];
					$field_order 	= trim($_POST['field_order']);
					$field_category = $_POST['field_category'];
					 
					
					if(empty($field_name)) echo '<div class="delete_thing">Field name cannot be empty!</div>';
					else
					{
						$wpdb->query("delete from ".$wpdb->prefix."project_user_custom_relations where custid='$custid'"); 
						
						if($field_category != 'all')
						{
							
							for($i=0;$i<count($_POST['field_cats']);$i++)
								if(isset($_POST['field_cats'][$i]))
									{
										$field_category = $_POST['field_cats'][$i];
										$wpdb->query("insert into ".$wpdb->prefix."project_user_custom_relations (custid,catid) values('$custid','$field_category')");	
									}
							
							if(empty($field_category)) $field_category = 'all';
						}
						else
							$field_category = 'all';
							
						//-------------------------------
						
						$ss = "update ".$wpdb->prefix."project_user_custom_fields set name='$field_name',ordr='$field_order',cate='$field_category' where id='$custid'";
						$wpdb->query($ss);
						
						echo '<div class="saved_thing">Custom field saved!</div>';
					}
				}
		
		
		
		
		$s = "select * from ".$wpdb->prefix."project_user_custom_fields where id='$custid'";
		$row = $wpdb->get_results($s);
		
		$row = $row[0];
	}	
		


	if(isset($_GET['delete_field']))
	{
		$delid = $_GET['delete_field'];
		$s = "select name from ".$wpdb->prefix."project_user_custom_fields where id='$delid'";
		$row = $wpdb->get_results($s);
		$row = $row[0];
		
		if(isset($_GET['coo']))
		{
			$s = "delete from ".$wpdb->prefix."project_user_custom_fields where id='$delid'";
			$r = $wpdb->query($s);
			
			echo '<div class="delete_thing">Field "'.$row->name.'" has been deleted! </div>';
			
		}
		else
		{
			
			echo '<div class="delete_thing"><div class="padd10">Are you sure you want to delete "'.$row->name.'" ? &nbsp; 
			<a href="'.get_admin_url().'admin.php?page=user-fields&delete_field='.$delid.'&coo=y">Yes</a> | 
			<a href="'.get_admin_url().'admin.php?page=user-fields">No</a> </div></div>';
		}
		
	} ?>
    
        <div id="usual2" class="usual"> 
  <ul> 
				<?php if(isset($_GET['edit_field'])): ?>
				<li><a href="#tabs-0">Edit custom field "<?php echo $row->name; ?>"</a></li>				
				<?php endif; ?>
				<li><a href="#tabs1">Add New User Profile Field</a></li>
				<li><a href="#tabs-2">Current User Profile Fields</a></li>
    
    
  </ul> 


<?php if(isset($_GET['edit_field'])): ?>
			<div id="tabs-0" style="display:block;padding:0">	
				
				
	<form method="post">
	<table class="sitemile-table" width="100%">
    
    <tr>
    <td width="170"> Field Name: </td>
    <td><input type="text" size="30" name="field_name" value="<?php echo $row->name; ?>" /></td>
    </td>
    
    <tr>
    <td> Field Type: </td>
    <td><?php echo project_get_field_tp($row->tp); ?></td>
    </td>
    
    
    <tr>
    <td width="170"> Field Order: </td>
    <td><input type="text" size="5" name="field_order" value="<?php echo $row->ordr; ?>" /></td>
    </td>
    
    
    <tr>
    <td width="170"> Apply to user type: </td>
    <td><input type="radio" name="field_category" value="all" <?php if($row->cate == 'all') echo 'checked="checked"'; ?>  /> Apply to all users </td>
    </td>
    
    
        <tr>
    <td width="170"> </td>
    <td><input type="radio" name="field_category" value="sel" <?php if($row->cate != 'all') echo 'checked="checked"'; ?>  /> Apply to selected users <br/>
            <div class="cat-class">
            <table width="100%">
            <?php
				
				
			 $user_types =  array(
			 
			 array('user_code' => 'service_provider' , 'user_description' => 'Service Provider'), 
			  array('user_code' => 'business_owner'  , 'user_description' => 'Service Buyer')
			 
			 );

			  foreach ($user_types as $user) 
				{
					
					if(projectTheme_search_into_users($custid, $user['user_code']) == 1) $chk = ' checked="checked" ';
						else $chk = "";
					
					echo '
					    <tr>
						<td><input '.$chk.' type="checkbox" name="field_cats[]" value="'.$user['user_code'].'" />
						<b>'.$user['user_description'].'</b></td>
						</tr>';
					 
					
				}	
				
				
			

						
			
			?>
            
            
        
            
            </table>
            </div>
    </td>
    </td>
    
     
    <tr>
    <td width="170">  </td>
    <td><input type="submit" name="save_new_field" value="Save this!" /> </td>
    </td>
    
    </table>
	</form>

	
		
        <?php
		
		if($row->tp != 1 && $row->tp != 5)
		{
			
			?>	
		<hr color="#CCCCCC" />
        <?php
			
			if(isset($_POST['_add_option']) && !empty($_POST['option_name']))
			{
				$option_name = $_POST['option_name'];
				$ss = "insert into ".$wpdb->prefix."project_user_custom_options (valval, custid) values('$option_name','$custid')";
				$wpdb->query($ss);
				
				echo '<div class="saved_thing"  id="add_options"><div class="padd10">Success! Your option was added!</div></div>';
				
				
			}
		
		
		?>
        
        
        <table  class="sitemile-table" width="100%"><tr><td>
        <strong>Add options:</strong>
        </td></tr>
        </table>
        
       	<form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=user-fields&edit_field=<?php echo $custid; ?>#add_options"> 
        <table>
        <tr>
        <td>Option Name: </td>
        <td><input type="text" name="option_name" size="20" /> <input type="submit" name="_add_option" value="Add Option" /> </td>
        </tr>
		
        <?php echo ProjectTheme_project_clear_table(2); ?>
        </table>
        </form>
        
        
        <table><tr><td>
        <strong>Current options:</strong>
        </td></tr>
        </table>
        <?php
		
		$ss = "select * from ".$wpdb->prefix."project_user_custom_options where custid='$custid' order by id desc";
		$rows2 = $wpdb->get_results($ss);
		
		if(count($rows2) == 0)
		echo "No options defined.";
		else
		{
			?>			
				<script>
					function delete_this(id)
							{
								 $.ajax({
												method: 'get',
												url : '<?php echo get_bloginfo('siteurl');?>/index.php?_delete_custom_id='+id,
												dataType : 'text',
												success: function (text) {  
												 $('#option_' + id).animate({'backgroundColor' : '#ff9900'},1000);
												 $('#option_'+id).remove();  }
											 });
								  
							
							}
				</script>
					
			<?php
			echo '<table  class="wp-list-table widefat fixed posts">';
			
				echo '<thead><tr>';
				echo '<th>Option Value</th>';
				echo '<th>Option Order</th>';
				echo '<th></th>';
				echo '</tr></thead>';
			
			
			
			
			foreach($rows2 as $row2)
			{
				echo '<script type="text/javascript"> 
						$(document).ready(function() { 
						   $(\'#myForm_'.$row2->id.'\').ajaxForm(function() { 
								 

								
								$(\'#option_'.$row2->id.'\').animate({\'backgroundColor\' : \'#ff9900\'});
								$(\'#option_'.$row2->id.'\').animate({\'backgroundColor\' : \'#cccccc\'});


							}); 
						}); 
					</script> ';
					
				
				echo '<form method="post" id="myForm_'.$row2->id.'" action="admin.php?ajax_ready=1" />';
				echo '<tr id="option_'.$row2->id.'" >';
				echo '<th><input type="hidden" size="20" name="option_id"  value="'.$row2->id.'" />
				<input type="text" size="20" name="option_name" id="custom_option_value_'.$row2->id.'" value="'.$row2->valval.'" />
				</th>';
				echo '<th><input type="text" size="4" name="option_order" id="custom_option_order_'.$row2->id.'" value="'.$row2->ordr.'" /></th>';
				echo '<th><input type="submit" name="submit" id="submit" value="Update" />
							<input onclick="delete_this('.$row2->id.')" type="button" name="DEL" value="Delete"  />
				</th>';
				echo '</tr></form>';
			}		
			
			echo '</table>';
		}
		
		}
		?>
				</table>
			</div>
			<?php endif; ?>	
			
			
			<div id="tabs1" style="display:block;padding:0">
	
	
    <form method="post">
	<table  class="sitemile-table" width="100%">

    <tr>
    <td width="170"> Field Name: </td>
    <td><input type="text" size="30" name="field_name" /></td>
    </td>
    
    <tr>
    <td> Field Type: </td>
    <td><select name="field_type">
    <option value="1">Text field</option>
    <option value="2">Select box</option>
    <option value="3">Radio Buttons</option>
    <option value="4">Check-box</option>
    <option value="5">Large text-area</option>
    </select></td>
    </td>
    
    
    <tr>
    <td width="170"> Field Order: </td>
    <td><input type="text" size="5" name="field_order" /></td>
    </td>
    
    
    <tr>
    <td width="170"> Apply to users: </td>
    <td><input type="radio" name="field_category" value="all" checked="checked" /> Apply to all user types </td>
    </td>
    
    
        <tr>
    <td width="170"> </td>
    <td><input type="radio" name="field_category" value="sel" /> Apply to selected user types <br/>
            <div class="cat-class">
            <table width="100%">
            <?php
				
				
			 $user_types =  array(
			 
			 array('user_code' => 'service_provider' , 'user_description' => 'Service Provider'), 
			  array('user_code' => 'business_owner'  , 'user_description' => 'Service Buyer')
			 
			 );

			  foreach ($user_types as $user) 
				{
					
					if(projectTheme_search_into_users($custid,$user['user_code']) == 1) $chk = ' checked="checked" ';
						else $chk = "";
					
					echo '
					    <tr>
						<td><input '.$chk.' type="checkbox" name="field_cats[]" value="'.$user['user_code'].'" />
						<b>'.$user['user_description'].'</b></td>
						</tr>';
					 
					
				}	
				
				
			

						
			
			?>
            
            
        
            
            </table>
            </div>
    </td>
    </td>
    

     
        <tr>
    <td width="170">  </td>
    <td><input type="submit" name="add_new_field" value="Add this!" /> </td>
    </td>
    
    </table>
	</form>
		
		
		</div>
		
			<div id="tabs-2" style="display:block;">
				
				
				 <table width="100%">
      
    </table>
    <?php
	
	$ss2 = "select * from ".$wpdb->prefix."project_user_custom_fields order by name asc";
	$rf = $wpdb->get_results($ss2);
	
	if(count($rf) == 0)
		echo 'No fields yet added.';
	else
	{
		echo '<table class="wp-list-table widefat fixed posts">';
		
		
		echo '<thead><tr>';
		echo '<th><strong>Field Name</strong></th>';
		echo '<th><strong>Field Type</strong></th>';
		echo '<th><strong>Field Users</strong></th>';
		echo '<th><strong>Field Order</strong></th>';
		echo '<th><strong>Options</strong></th>';
		echo '</tr></thead><tbody>';
		
		foreach($rf as $row)
		{		
				$bgs = "efefef";
				if(isset($_GET['edit_field']))				
					if($_GET['edit_field'] == $row->id)
						$bgs = "B5CA73";
				
				$s5 = "select * from ".$wpdb->prefix."project_user_custom_relations where custid='{$row->id}'";
				$r5 = $wpdb->get_results($s5);
				
				if(count($r5) > 0)
				{
					$staff = '';
					foreach($r5 as $res5)
					{
						$trns = ( $res5->catid == "service_provider" ? 'Service Provider' : 'Service Buyer');
						$staff .= $trns.', ';
					}
				}

				echo '<tr>';
				echo '<th>'.$row->name.'</th>';
				echo '<th>'.project_get_field_tp($row->tp).'</th>';
				echo '<th>'.($row->cate == 'all' ? "All Users" : $staff).'</th>';
				echo '<th>'.$row->ordr.'</th>';
				echo '<th>
				<a href="'.get_admin_url().'admin.php?page=user-fields&edit_field='.$row->id.'"
				><img src="'.get_bloginfo('template_url').'/images/edit.gif" border="0" alt="Edit" /></a>
				
				<a href="'.get_admin_url().'admin.php?page=user-fields&delete_field='.$row->id.'"
				><img src="'.get_bloginfo('template_url').'/images/delete.gif" border="0" alt="Delete" /></a>
				
				</th>';
				echo '</tr>';
			
		}
		echo '</tbody></table>';
	}
	?>
				
				
			</div>
			</div>
	<?php
    
   	
	echo '</div>';	
	
}

function projectTheme_custom_fields_scr()
{
	global $menu_admin_project_theme_bull, $wpdb;
	echo '<div class="wrap">';
	echo '<div class="icon32" id="icon-options-general-custfields"><br/></div>';	
	echo '<h2 class="my_title_class_sitemile">ProjectTheme Custom Fields</h2>';
	?>
    
    <script src="<?php echo get_bloginfo('template_url'); ?>/js/jquery.form.js"></script>


	<?php
	
	if(isset($_POST['add_new_field']))
	{
		$field_name 			= trim($_POST['field_name']);
		$field_type 			= $_POST['field_type'];
		$field_order 			= trim($_POST['field_order']);
		$field_category			= $_POST['field_category'];
		$is_mandatory 			= $_POST['is_mandatory'];
		
		//----------------------------
		
		if(empty($field_name)) echo '<div class="delete_thing">Field name cannot be empty!</div>';
		else
		{
			$step_me = '';
			$step_me = apply_filters('ProjectTheme_step_me_filter', $step_me);
			
			
			$ss = "insert into ".$wpdb->prefix."project_custom_fields (is_mandatory, name,tp,ordr,cate, step_me) 
														values('$is_mandatory','$field_name','$field_type','$field_order','$field_category', '$step_me')";
			$wpdb->query($ss);
			
			//----------------------------
			
			$ss = "select id from ".$wpdb->prefix."project_custom_fields where name='$field_name' AND tp='$field_type'";
			$rows = $wpdb->get_results($ss);
			
			foreach($rows as $row)
			{
			
				$custid = $row->id;
				
				if($field_category != 'all')
				{
					
					for($i=0;$i<count($_POST['field_cats']);$i++)
						if(isset($_POST['field_cats'][$i]))
							{
								$field_category = $_POST['field_cats'][$i];
								$wpdb->query("insert into ".$wpdb->prefix."project_custom_relations (custid,catid) values('$custid','$field_category')");
								
							}
					if(empty($field_category)) $field_category = 'all';
				}
				else
					$field_category = 'all';
			}	
			//-------------------------------
			

			
			echo '<div class="saved_thing">Custom field added!</div>';
		}
	}
	
	
	$arr = array("yes" => "Yes", "no" => "No");
	
	if(isset($_GET['edit_field']))
	{
		$custid = $_GET['edit_field'];
		
			if(isset($_POST['save_new_field']))
				{
					$field_name 	= trim($_POST['field_name']);
					//$field_type 	= $_POST['field_type'];
					$field_order 	= trim($_POST['field_order']);
					$field_category = $_POST['field_category'];
					
					if(empty($field_name)) echo '<div class="delete_thing">Field name cannot be empty!</div>';
					else
					{
						$wpdb->query("delete from ".$wpdb->prefix."project_custom_relations where custid='$custid'"); 
						
						if($field_category != 'all')
						{
							
							for($i=0;$i<count($_POST['field_cats']);$i++)
								if(isset($_POST['field_cats'][$i]))
									{
										$field_category = $_POST['field_cats'][$i];
										$wpdb->query("insert into ".$wpdb->prefix."project_custom_relations (custid,catid) values('$custid','$field_category')");	
									}
							
							if(empty($field_category)) $field_category = 'all';
						}
						else
							$field_category = 'all';
							
						//-------------------------------
						
						$step_me = '';
						$step_me = apply_filters('ProjectTheme_step_me_filter', $step_me);
						
						$content_box6 = $_POST['content_box6'];
						$is_mandatory = $_POST['is_mandatory'];
						
						$ss = "update ".$wpdb->prefix."project_custom_fields set is_mandatory='$is_mandatory', 
						name='$field_name', content_box6='$content_box6', step_me='$step_me' ,ordr='$field_order',cate='$field_category' where id='$custid'";
						$wpdb->query($ss);
						
						echo '<div class="saved_thing">Custom field saved!</div>';
					}
				}
		
		
		
		
		$s = "select * from ".$wpdb->prefix."project_custom_fields where id='$custid'";
		$row = $wpdb->get_results($s);
		
		$row = $row[0];
	}	
		


	if(isset($_GET['delete_field']))
	{
		$delid = $_GET['delete_field'];
		$s = "select name from ".$wpdb->prefix."project_custom_fields where id='$delid'";
		$row = $wpdb->get_results($s);
		$row = $row[0];
		
		if(isset($_GET['coo']))
		{
			$s = "delete from ".$wpdb->prefix."project_custom_fields where id='$delid'";
			$r = $wpdb->query($s);
			
			echo '<div class="delete_thing">Field "'.$row->name.'" has been deleted! </div>';
			
		}
		else
		{
			
			echo '<div class="delete_thing"><div class="padd10">Are you sure you want to delete "'.$row->name.'" ? &nbsp; 
			<a href="'.get_admin_url().'admin.php?page=custom-fields&delete_field='.$delid.'&coo=y">Yes</a> | 
			<a href="'.get_admin_url().'admin.php?page=custom-fields">No</a> </div></div>';
		}
		
	} ?>
    
        <div id="usual2" class="usual"> 
  <ul> 
  <?php
  
  	if(isset($_GET['edit_field']))
	{
		$tabs1 = "tabs-0";	
	}
	else
	{
		$tabs1 = "tabs1";
	}
  
  
  ?>
  
				<?php if(isset($_GET['edit_field'])): ?>
				<li><a href="#tabs1">Edit custom field "<?php echo $row->name; ?>"</a></li>				
				<?php endif; ?>
				<li><a href="#<?php echo $tabs1; ?>">Add New Custom Field</a></li>
				<li><a href="#tabs-2">Current Custom Fields</a></li>
    
    
  </ul> 


<?php if(isset($_GET['edit_field'])): ?>
			<div id="tabs1" style="display:block;padding:0">	
				
				
	<form method="post">
	<table class="sitemile-table" width="100%">
    
    <tr>
    <td width="170"> Field Name: </td>
    <td><input type="text" size="30" name="field_name" value="<?php echo $row->name; ?>" /></td>
    </td>
    
    <tr>
    <td> Field Type: </td>
    <td><?php echo project_get_field_tp($row->tp); ?></td>

    </td>
    
    
    <tr>
    <td width="170"> Field Order: </td>
    <td><input type="text" size="5" name="field_order" value="<?php echo $row->ordr; ?>" /></td>
    </td>
    
    <?php do_action('ProjectTheme_extra_field_options',$row); ?>
    
    <?php
	
		if($row->tp == 6):
	
	?>
     <tr>
    <td width="170" valign="top"> Field HTML Content: </td>
    <td><textarea rows="5" cols="60" name="content_box6"><?php echo stripslashes($row->content_box6); ?></textarea></td>
    </td>
    
    <?php endif; ?>
    
       <tr>
    <td> Mandatory Field: </td>
    <td><select name="is_mandatory">
    <option value="0" <?php echo ($row->is_mandatory == 0 ? "selected='selected'" :''); ?>>No</option>
    <option value="1" <?php echo ($row->is_mandatory == 1 ? "selected='selected'" :''); ?>>Yes</option>
    </select></td>
    </td>
    
    <tr>
    <td width="170"> Apply to category: </td>
    <td><input type="radio" name="field_category" value="all" <?php if($row->cate == 'all') echo 'checked="checked"'; ?>  /> Apply to all categories </td>
    </td>
    
    
        <tr>
    <td width="170"> </td>
    <td><input type="radio" name="field_category" value="sel" <?php if($row->cate != 'all') echo 'checked="checked"'; ?>  /> Apply to selected categories <br/>
            <div class="cat-class">
            <table width="100%">
            <?php
				
				
			 $categories =  get_categories('taxonomy=project_cat&hide_empty=0&orderby=name&parent=0');

			  foreach ($categories as $category) 
				{
					
					if(projectTheme_search_into($custid,$category->cat_ID) == 1) $chk = ' checked="checked" ';
						else $chk = "";
					echo '
					    <tr>
						<td><input '.$chk.' type="checkbox" name="field_cats[]" value="'.$category->cat_ID.'" />
						<b>'.$category->cat_name.'</b></td>
						</tr>';
						
					$subcategories =  get_categories('taxonomy=project_cat&hide_empty=0&orderby=name&parent='.$category->term_id);
						
					if($subcategories)	
					foreach ($subcategories as $subcategory) 
					{
						if(projectTheme_search_into($custid,$subcategory->cat_ID) == 1) $chk = ' checked="checked" ';
						else $chk = "";
						
						echo '
					    <tr>
						<td>&nbsp; &nbsp; &nbsp; <input type="checkbox" '.$chk.' name="field_cats[]" value="'.$subcategory->cat_ID.'" />
						'.$subcategory->cat_name.'</td>
						</tr>';
						
						
							$subcategories2 =  get_categories('taxonomy=project_cat&hide_empty=0&orderby=name&parent='.$subcategory->term_id);
						
							if($subcategories2)	
							foreach ($subcategories2 as $subcategory2) 
							{
								if(projectTheme_search_into($custid,$subcategory2->cat_ID) == 1) $chk = ' checked="checked" ';
								else $chk = "";
								
								echo '
								<tr>
								<td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <input type="checkbox" '.$chk.' name="field_cats[]" value="'.$subcategory2->cat_ID.'" />
								'.$subcategory2->cat_name.'</td>
								</tr>';
								
								
										$subcategories3 =  get_categories('taxonomy=project_cat&hide_empty=0&orderby=name&parent='.$subcategory2->term_id);
						
										if($subcategories3)	
										foreach ($subcategories3 as $subcategory3) 
										{
											if(projectTheme_search_into($custid,$subcategory3->cat_ID) == 1) $chk = ' checked="checked" ';
											else $chk = "";
											
											echo '
											<tr>
											<td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <input type="checkbox" '.$chk.' name="field_cats[]" value="'.$subcategory3->cat_ID.'" />
											'.$subcategory3->cat_name.'</td>
											</tr>';
											
										}
									
								
								
								
							}
						
					}
				}	
				
				
			

						
			
			?>
            
            
        
            
            </table>
            </div>
    </td>
    </td>
    
     
    <tr>
    <td width="170">  </td>
    <td><input type="submit" name="save_new_field" value="Save this!" /> </td>
    </td>
    
    </table>
	</form>

	
		
        <?php
		
		if($row->tp != 1 && $row->tp != 5 && $row->tp != 6)
		{
			
			?>	
		<hr color="#CCCCCC" />
        <?php
			
			if(isset($_POST['_add_option']) && !empty($_POST['option_name']))
			{
				$option_name = $_POST['option_name'];
				$ss = "insert into ".$wpdb->prefix."project_custom_options (valval, custid) values('$option_name','$custid')";
				$wpdb->query($ss);
				
				echo '<div class="saved_thing"  id="add_options"><div class="padd10">Success! Your option was added!</div></div>';
				
				
			}
		
		
		?>
        
        
        <table  class="sitemile-table" width="100%"><tr><td>
        <strong>Add options:</strong>
        </td></tr>
        </table>
        
       	<form method="post" action="<?php echo get_admin_url(); ?>admin.php?page=custom-fields&edit_field=<?php echo $custid; ?>#add_options"> 
        <table>
        <tr>
        <td>Option Name: </td>
        <td><input type="text" name="option_name" size="20" /> <input type="submit" name="_add_option" value="Add Option" /> </td>
        </tr>
		
        <?php echo ProjectTheme_project_clear_table(2); ?>
        </table>
        </form>
        
        
        <table><tr><td>
        <strong>Current options:</strong>
        </td></tr>
        </table>
        <?php
		
		$ss = "select * from ".$wpdb->prefix."project_custom_options where custid='$custid' order by id desc";
		$rows2 = $wpdb->get_results($ss);
		
		if(count($rows2) == 0)
		echo "No options defined.";
		else
		{
			?>			
				<script>
					function delete_this(id)
							{
								 $.ajax({
												method: 'get',
												url : '<?php echo get_bloginfo('siteurl');?>/index.php?_delete_custom_id='+id,
												dataType : 'text',
												success: function (text) {  
												 $('#option_' + id).animate({'backgroundColor' : '#ff9900'},1000);
												 $('#option_'+id).remove();  }
											 });
								  
							
							}
				</script>
					
			<?php
			echo '<table  class="wp-list-table widefat fixed posts">';
			
				echo '<thead><tr>';
				echo '<th>Option Value</th>';
				echo '<th>Option Order</th>';
				echo '<th></th>';
				echo '</tr></thead>';
			
			
			
			
			foreach($rows2 as $row2)
			{
				echo '<script type="text/javascript"> 
						$(document).ready(function() { 
						   $(\'#myForm_'.$row2->id.'\').ajaxForm(function() { 
								 

								
								$(\'#option_'.$row2->id.'\').animate({\'backgroundColor\' : \'#ff9900\'});
								$(\'#option_'.$row2->id.'\').animate({\'backgroundColor\' : \'#cccccc\'});

							
							}); 
						}); 
					</script> ';
					
				
				echo '<form method="post" id="myForm_'.$row2->id.'" action="'.get_bloginfo('siteurl').'/?update_option_ajax_ready=1" />';
				echo '<tr id="option_'.$row2->id.'" >';
				echo '<th><input type="hidden" size="20" name="option_id"  value="'.$row2->id.'" />
				<input type="text" size="20" name="option_name" id="custom_option_value_'.$row2->id.'" value="'.$row2->valval.'" />
				</th>';
				echo '<th><input type="text" size="4" name="option_order" id="custom_option_order_'.$row2->id.'" value="'.$row2->ordr.'" /></th>';
				echo '<th><input type="submit" name="submit" id="submit" value="Update" />
							<input onclick="delete_this('.$row2->id.')" type="button" name="DEL" value="Delete"  />
				</th>';
				echo '</tr></form>';
			}		
			
			echo '</table>';
		}
		
		}
		?>
				</table>
			</div>
			<?php endif; ?>	
			
			
			<div id="<?php echo $tabs1; ?>" style="display:block;padding:0">
	
	
    <form method="post">
	<table  class="sitemile-table" width="100%">

    <tr>
    <td width="170"> Field Name: </td>
    <td><input type="text" size="30" name="field_name" /></td>
    </td>
    
    <tr>
    <td> Field Type: </td>
    <td><select name="field_type">
    <option value="1">Text field</option>
    <option value="2">Select box</option>
    <option value="3">Radio Buttons</option>
    <option value="4">Check-box</option>
    <option value="5">Large text-area</option>

    <option value="6">HTML Box</option>
    </select></td>
    </td>
    
    <tr>
    <td> Mandatory Field: </td>
    <td><select name="is_mandatory">
    <option value="0">No</option>
    <option value="1">Yes</option>
    </select></td>
    </td>
    
    
    <tr>
    <td width="170"> Field Order: </td>
    <td><input type="text" size="5" name="field_order" /></td>
    </td>
    
    
    <?php do_action('ProjectTheme_extra_field_options',$row); ?>
    
    <tr>
    <td width="170"> Apply to category: </td>
    <td><input type="radio" name="field_category" value="all" checked="checked" /> Apply to all categories </td>
    </td>
    
    
        <tr>
    <td width="170"> </td>
    <td><input type="radio" name="field_category" value="sel" /> Apply to selected categories <br/>
            <div class="cat-class">
            <table width="100%">
            <?php
			
			  $categories =  get_categories('taxonomy=project_cat&hide_empty=0&orderby=name&parent=0');

			  foreach ($categories as $category) 
				{
					
					if(projectTheme_search_into($custid,$category->cat_ID) == 1) $chk = ' checked="checked" ';
						else $chk = "";
					echo '
					    <tr>
						<td><input '.$chk.' type="checkbox" name="field_cats[]" value="'.$category->cat_ID.'" />
						<b>'.$category->cat_name.'</b></td>
						</tr>';
						
					$subcategories =  get_categories('taxonomy=project_cat&hide_empty=0&orderby=name&parent='.$category->term_id);
						
					if($subcategories)	
					foreach ($subcategories as $subcategory) 
					{
						if(projectTheme_search_into($custid,$subcategory->cat_ID) == 1) $chk = ' checked="checked" ';
						else $chk = "";
						
						echo '
					    <tr>
						<td>&nbsp; &nbsp; &nbsp; <input type="checkbox" '.$chk.' name="field_cats[]" value="'.$subcategory->cat_ID.'" />
						'.$subcategory->cat_name.'</td>
						</tr>';
						
						
							$subcategories2 =  get_categories('taxonomy=project_cat&hide_empty=0&orderby=name&parent='.$subcategory->term_id);
						
							if($subcategories2)	
							foreach ($subcategories2 as $subcategory2) 
							{
								if(projectTheme_search_into($custid,$subcategory2->cat_ID) == 1) $chk = ' checked="checked" ';
								else $chk = "";
								
								echo '
								<tr>
								<td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <input type="checkbox" '.$chk.' name="field_cats[]" value="'.$subcategory2->cat_ID.'" />
								'.$subcategory2->cat_name.'</td>
								</tr>';
								
								
										$subcategories3 =  get_categories('taxonomy=project_cat&hide_empty=0&orderby=name&parent='.$subcategory2->term_id);
						
										if($subcategories3)	
										foreach ($subcategories3 as $subcategory3) 
										{
											if(projectTheme_search_into($custid,$subcategory3->cat_ID) == 1) $chk = ' checked="checked" ';
											else $chk = "";
											
											echo '
											<tr>
											<td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <input type="checkbox" '.$chk.' name="field_cats[]" value="'.$subcategory3->cat_ID.'" />
											'.$subcategory3->cat_name.'</td>
											</tr>';
											
										}
									
								
								
								
							}
						
					}
				}	
				

						
			
			?>
            
            
        
            
            </table>
            </div>
    </td>
    </td>
    

     
        <tr>
    <td width="170">  </td>
    <td><input type="submit" name="add_new_field" value="Add this!" /> </td>
    </td>
    
    </table>
	</form>
		
		
		</div>
		
			<div id="tabs-2" style="display:block;">
				
				
				 <table width="100%">
      
    </table>
    <?php
	
	$ss2 = "select * from ".$wpdb->prefix."project_custom_fields order by name asc";
	$rf = $wpdb->get_results($ss2);
	
	if(count($rf) == 0)
		echo 'No fields yet added.';
	else
	{
		echo '<table class="wp-list-table widefat fixed posts">';
		
		
		echo '<thead><tr>';
		echo '<th><strong>Field Name</strong></th>';
		echo '<th><strong>Field Type</strong></th>';
		echo '<th><strong>Field Category</strong></th>';
		echo '<th><strong>Field Order</strong></th>';
		echo '<th><strong>Mandatory</strong></th>';
		echo '<th><strong>Options</strong></th>';
		echo '</tr></thead><tbody>';
		
		foreach($rf as $row)
		{		
				$bgs = "efefef";
				if(isset($_GET['edit_field']))				
					if($_GET['edit_field'] == $row->id)
						$bgs = "B5CA73";
				
				
				
				echo '<tr>';
				echo '<th>'.$row->name.'</th>';
				echo '<th>'.project_get_field_tp($row->tp).'</th>';
				echo '<th>'.($row->cate == 'all' ? "All Categories" : "Multiple Categories").'</th>';
				echo '<th>'.$row->ordr.'</th>';
				echo '<th>'.($row->is_mandatory == 0 ? "No" : "Yes").'</th>';
				echo '<th>
				<a href="'.get_admin_url().'admin.php?page=custom-fields&edit_field='.$row->id.'"
				><img src="'.get_bloginfo('template_url').'/images/edit.gif" border="0" alt="Edit" /></a>
				
				<a href="'.get_admin_url().'admin.php?page=custom-fields&delete_field='.$row->id.'"
				><img src="'.get_bloginfo('template_url').'/images/delete.gif" border="0" alt="Delete" /></a>
				
				</th>';
				echo '</tr>';
			
		}
		echo '</tbody></table>';
	}
	?>
				
				
			</div>
			</div>

	<?php
    
   	
	echo '</div>';
}
/******************************************************************
*
*	Admin Menu - New Function - sitemile[at]sitemile.com
*	developed by Andrei Saioc - andreisaioc[at]gmail.com
*
*******************************************************************/

function projectTheme_theme_images_settings()
{
	global $menu_admin_project_theme_bull, $wpdb;
	echo '<div class="wrap">';
	echo '<div class="icon32" id="icon-options-general-img"><br/></div>';	
	echo '<h2 class="my_title_class_sitemile">ProjectTheme Image Settings</h2>';
	
	$arr = array("yes" => "Yes", "no" => "No");
	
		if(isset($_POST['save1']))
		{
			$ProjectTheme_enable_images_in_projects = $_POST['ProjectTheme_enable_images_in_projects'];
			update_option('ProjectTheme_enable_images_in_projects', $ProjectTheme_enable_images_in_projects);
			
			$ProjectTheme_charge_fees_for_images = $_POST['ProjectTheme_charge_fees_for_images'];
			update_option('ProjectTheme_charge_fees_for_images', $ProjectTheme_charge_fees_for_images);
			
			$ProjectTheme_enable_max_images_limit = $_POST['ProjectTheme_enable_max_images_limit'];
			update_option('ProjectTheme_enable_max_images_limit', $ProjectTheme_enable_max_images_limit);
			
			//--------------------------------------
			
			update_option('projectTheme_nr_of_free_images', trim($_POST['projectTheme_nr_of_free_images']));
			update_option('projectTheme_extra_image_charge', trim($_POST['projectTheme_extra_image_charge']));
			update_option('projectTheme_nr_max_of_images', trim($_POST['projectTheme_nr_max_of_images']));
			
			
			
			echo '<div class="saved_thing">Settings saved!</div>';	
		}
		
		if(isset($_POST['save2']))
		{
			update_option('projectTheme_width_of_project_images', trim($_POST['projectTheme_width_of_project_images']));	
			
			echo '<div class="saved_thing">Settings saved!</div>';	
		}
	
	?>
    
        <div id="usual2" class="usual"> 
  <ul> 
    <li><a href="#tabs1" class="selected">General Settings</a></li> 
    <li><a href="#tabs2">Resize Settings</a></li> 
  </ul> 
  <div id="tabs1" style="display: block; ">
    	
        <form method="post">
        <table width="100%" class="sitemile-table">
        
        <tr>
        <td width="190">Enable Limit on max images:</td>
        <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_enable_max_images_limit'); ?></td>
        </tr>
        
         <tr>
        <td>Max limit of images:</td>
        <td><input type="text" value="<?php echo get_option('projectTheme_nr_max_of_images'); ?>" size="4" name="projectTheme_nr_max_of_images" /></td>
        </tr>
        
        
        <tr>
        <td width="190">Enable Images:</td>
        <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_enable_images_in_projects'); ?></td>
        </tr>
        
        
        <tr>
        <td>Charge fees for images:</td>
        <td><?php echo ProjectTheme_get_option_drop_down($arr, 'ProjectTheme_charge_fees_for_images'); ?></td>
        </tr>
        
        
        <tr>
        <td>Number of free images:</td>
        <td><input type="text" value="<?php echo get_option('projectTheme_nr_of_free_images'); ?>" size="4" name="projectTheme_nr_of_free_images" /></td>
        </tr>
        
        
        <tr>
        <td>Extra charge(per image):</td>
        <td><input type="text" value="<?php echo get_option('projectTheme_extra_image_charge'); ?>" size="5" name="projectTheme_extra_image_charge" /> <?php echo projectTheme_get_currency(); ?></td>
        </tr>
        
        
        <tr>
        <td></td>
        <td><input type="submit" name="save1" value="Save Settings" /></td>
        </tr>
        
        </table>
        </form>
          </div> 
          <div id="tabs2" style="display: none; ">
           <form method="post">
                  <table width="100%" class="sitemile-table">
        
   
        
        <tr>
        <td>Default width of picture resize:</td>
        <td><input type="text" value="<?php echo get_option('projectTheme_width_of_project_images'); ?>" size="4" name="projectTheme_width_of_project_images" /> pixels</td>
        </tr>
        

        
        <tr>
        <td></td>
        <td><input type="submit" name="save2" value="Save Settings" /></td>
        </tr>
        
        </table>
        </form>  
          </div> 
        </div> 
    
    
    <?php	
	
	echo '</div>';
}

/******************************************************************
*
*	Admin Menu - New Function - sitemile[at]sitemile.com
*	developed by Andrei Saioc - andreisaioc[at]gmail.com
*
*******************************************************************/

function projectTheme_hist_transact()
{
	global $menu_admin_project_theme_bull, $wpdb;
	echo '<div class="wrap">';
	echo '<div class="icon32" id="icon-options-general-list"><br/></div>';	
	echo '<h2 class="my_title_class_sitemile">ProjectTheme Transaction History</h2>';
	
	$arr = array("yes" => "Yes", "no" => "No");
	
	?>
    
        <div id="usual2" class="usual"> 
  <ul> 
    <li><a href="#tabs1" class="selected">All Transactions</a></li> 
    <li><a href="#tabs2">Search User</a></li> 
  </ul> 
  <div id="tabs1" style="display: block; ">
    
	
	
	
	<?php
	

	
	$nrpostsPage = 10; 
	$page = $_GET['pj']; if(empty($page)) $page = 1;
	$my_page = $page;
	
	//-----------------------------------------------------------
	
	global $wpdb;
	$s = "select * from ".$wpdb->prefix."project_payment_transactions order by id desc limit ".($nrpostsPage * ($page - 1) )." ,$nrpostsPage";
	$r = $wpdb->get_results($s);
	
	$s1 = "select id from ".$wpdb->prefix."project_payment_transactions order by id desc";	 	
	$r1 = $wpdb->get_results($s1);	
		
	
	if(count($r) > 0):
		
	$total_nr = count($r1);
				
				$nrposts = $total_nr;

				$totalPages = ceil($nrposts / $nrpostsPage);
				$pagess = $totalPages;
				$batch = 10; //ceil($page / $nrpostsPage );
				
				
				$start 		= floor($my_page/$batch) * $batch + 1; 
				$end		= $start + $batch - 1;
				$end_me 	= $end + 1;
				$start_me 	= $start - 1;
				
				if($end > $totalPages) $end = $totalPages;
				if($end_me > $totalPages) $end_me = $totalPages;
				
				if($start_me <= 0) $start_me = 1;
				
				$previous_pg = $my_page - 1;
				if($previous_pg <= 0) $previous_pg = 1;
				
				$next_pg = $my_page + 1;
				if($next_pg >= $totalPages) $next_pg = 1;	
		
	?>	  
            <table class="widefat post fixed" cellspacing="0">
    <thead>
    <tr>
    <th width="10%">Username</th>
    <th width="40%">Comment/Description</th>
    <th>Date Made</th>
    <th >Amount</th>
	<th >Project</th>
    </tr>
    </thead>
    
    
    
    <tbody>


	<?php

	
	foreach($r as $row)
	{
		$user = get_userdata($row->uid);
		
		if($row->tp == 0) { $sign = '-'; $cl = 'redred'; }
		else
		{ $sign = '+'; $cl = 'greengreen'; }
		
		echo '<tr>';	
		echo '<th>'.$user->user_login.'</th>';
		echo '<th>'.$row->reason .'</th>';
		echo '<th>'.date('d-M-Y H:i:s',$row->datemade) .'</th>';
		echo '<th class="'.$cl.'">'.$sign.ProjectTheme_get_show_price($row->amount,2).'</th>';
		echo '<th>#</th>';
	
		echo '</tr>';
	}
	
	?>



	</tbody>
    
    
    </table>
    
    <?php
			
			
			if($start > 1)
			echo '<a href="'.get_admin_url().'admin.php?page=trans-site&pj='.$previous_pg.'"><< '.__('Previous','ProjectTheme').'</a> ';
			echo ' <a href="'.get_admin_url().'admin.php?page=trans-site&pj='.$start_me.'"><<</a> ';
			
			
	
			
			for($i = $start; $i <= $end; $i ++) {
				if ($i == $my_page) {
					echo ''.$i.' | ';
				} else {
		
					echo '<a href="'.get_admin_url().'admin.php?page=trans-site&pj='.$i.'">'.$i.'</a> | ';
					
				}
			}
	
	
			
			if($totalPages > $my_page)
			echo ' <a href="'.get_admin_url().'admin.php?page=trans-sites&pj='.$end_me.'">>></a> ';
			echo ' <a href="'.get_admin_url().'admin.php?page=trans-site&pj='.$next_pg.'">'.__('Next','ProjectTheme').' >></a> ';	
			
			
			?>
    
    
    <?php else: ?> Sorry there are no transactions.
    
    <?php endif; ?>
          
     	</div>   
          <div id="tabs2" style="display: none; ">
          
          <form method="get" action="<?php echo get_admin_url(); ?>admin.php">
          <input type="hidden" name="page" value="trans-site" />
          <input type="hidden" name="active_tab" value="tabs2" />
          Search User: <input type="text" size="35" value="<?php echo $_GET['src_usr']; ?>" name="src_usr" />
           <input type="submit" value="Submit" name="" />
          </form> <br/>
          
              <?php
	
	if(isset($_GET['src_usr'])):
	
	$usrdt = get_userdatabylogin($_GET['src_usr']);
	
	$nrpostsPage = 10; 
	$page = $_GET['pj']; if(empty($page)) $page = 1;
	$my_page = $page;
	
	//-----------------------------------------------------------
	
	global $wpdb;
	$s = "select * from ".$wpdb->prefix."project_payment_transactions where uid='".$usrdt->ID."' order by id desc limit ".($nrpostsPage * ($page - 1) )." ,$nrpostsPage";
	$r = $wpdb->get_results($s);
	
	$s1 = "select id from ".$wpdb->prefix."project_payment_transactions where uid='".$usrdt->ID."' order by id desc";	 	
	$r1 = $wpdb->get_results($s1);	
		
	
	if(count($r) > 0):
		
	$total_nr = count($r1);
				
				$nrposts = $total_nr;
				$totalPages = ceil($nrposts / $nrpostsPage);
				$pagess = $totalPages;
				$batch = 10; //ceil($page / $nrpostsPage );
				
				
				$start 		= floor($my_page/$batch) * $batch + 1; 
				$end		= $start + $batch - 1;
				$end_me 	= $end + 1;
				$start_me 	= $start - 1;
				
				if($end > $totalPages) $end = $totalPages;
				if($end_me > $totalPages) $end_me = $totalPages;
				
				if($start_me <= 0) $start_me = 1;
				
				$previous_pg = $my_page - 1;
				if($previous_pg <= 0) $previous_pg = 1;
				
				$next_pg = $my_page + 1;
				if($next_pg >= $totalPages) $next_pg = 1;	
		
	?>	  
            <table class="widefat post fixed" cellspacing="0">
    <thead>
    <tr>
    <th width="10%">Username</th>
    <th width="40%">Comment/Description</th>
    <th>Date Made</th>
    <th >Amount</th>
	<th >Project</th>
    </tr>
    </thead>
    
    
    
    <tbody>


	<?php

	
	foreach($r as $row)
	{
		$user = get_userdata($row->uid);
		
		if($row->tp == 0) { $sign = '-'; $cl = 'redred'; }
		else
		{ $sign = '+'; $cl = 'greengreen'; }
		
		echo '<tr>';	
		echo '<th>'.$user->user_login.'</th>';
		echo '<th>'.$row->reason .'</th>';
		echo '<th>'.date('d-M-Y H:i:s',$row->datemade) .'</th>';
		echo '<th class="'.$cl.'">'.$sign.ProjectTheme_get_show_price($row->amount,2).'</th>';
		echo '<th>#</th>';
	
		echo '</tr>';
	}
	
	?>



	</tbody>
    
    
    </table>
    
    <?php
			
			
			if($start > 1)
			echo '<a href="'.get_admin_url().'admin.php?src_usr='.$_GET['src_usr'].'&page=trans-site&pj='.$previous_pg.'"><< '.__('Previous','ProjectTheme').'</a> ';
			echo ' <a href="'.get_admin_url().'admin.php?src_usr='.$_GET['src_usr'].'&page=trans-site&pj='.$start_me.'"><<</a> ';
			
			
	
			
			for($i = $start; $i <= $end; $i ++) {
				if ($i == $my_page) {
					echo ''.$i.' | ';
				} else {
		
					echo '<a href="'.get_admin_url().'admin.php?src_usr='.$_GET['src_usr'].'&page=trans-site&pj='.$i.'">'.$i.'</a> | ';
					
				}
			}
	
	
			
			if($totalPages > $my_page)
			echo ' <a href="'.get_admin_url().'admin.php?src_usr='.$_GET['src_usr'].'&page=trans-sites&pj='.$end_me.'">>></a> ';
			echo ' <a href="'.get_admin_url().'admin.php?src_usr='.$_GET['src_usr'].'&page=trans-site&pj='.$next_pg.'">'.__('Next','ProjectTheme').' >></a> ';	
			
			
			?>
    
    
    <?php else: ?> Sorry there are no transactions.
    
    <?php endif; endif; ?>
          
          </div> 
        </div> 
    
    
    <?php	
	
	echo '</div>';
}


?>
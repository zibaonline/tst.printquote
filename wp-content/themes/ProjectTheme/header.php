<?php





?>



	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">



	<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes('xhtml'); ?> >



	<head>



	<!--<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">-->



    



	<title>



	<?php wp_title(  ); ?>



    </title>



	<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />

	<link href='http://fonts.googleapis.com/css?family=Cuprum:700' rel='stylesheet' type='text/css'>

    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />



	<?php wp_enqueue_script("jquery"); ?>







	<?php







		wp_head();







	?>	











	 <?php



	 	



		$ProjectTheme_color_for_footer = get_option('ProjectTheme_color_for_footer');



		if(!empty($ProjectTheme_color_for_footer))



		{



			echo '<style> #footer { background:#'.$ProjectTheme_color_for_footer.' }</style>';	



		}



		



		



		$ProjectTheme_color_for_bk = get_option('ProjectTheme_color_for_bk');



		if(!empty($ProjectTheme_color_for_bk))



		{



			echo '<style> body { background:#'.$ProjectTheme_color_for_bk.' }</style>';	



		}



		



		$ProjectTheme_color_for_top_links = get_option('ProjectTheme_color_for_top_links');



		if(!empty($ProjectTheme_color_for_top_links))



		{



			echo '<style> .top-bar { background:#'.$ProjectTheme_color_for_top_links.' }</style>';	



		}



		



		



		//----------------------



		



	 	$ProjectTheme_home_page_layout = get_option('ProjectTheme_home_page_layout');



		if(ProjectTheme_is_home()):



			if($ProjectTheme_home_page_layout == "4"):



				echo '<style>#content { float:right } #left-sidebar { float:left; }</style>';



			endif;



			



			if($ProjectTheme_home_page_layout == "5"):



				echo '<style>#content { width:100%; }  </style>';



			endif;



			



			if($ProjectTheme_home_page_layout == "3"):



				echo '<style>#content { width:395px } .title_holder { width:285px; } #left-sidebar{	float:left;margin-right:15px;}



				 </style>';



			endif;



			



			



			if($ProjectTheme_home_page_layout == "2"):



				echo '<style>#content { width:395px } #left-sidebar{ float:right } #left-sidebar{ margin-right:15px; } .title_holder { width:285px; }



				 </style>';



			endif;



		



		endif;



	 



	 



	 ?>



	



     <script type="text/javascript">



		



		var $ = jQuery;



		



	function suggest(inputString){



	



		if(inputString.length == 0) {



			$('#suggestions').fadeOut();



		} else {



		$('#big-search').addClass('load');



			$.post("<?php bloginfo('siteurl'); ?>/?autosuggest=1", {queryString: ""+inputString+""}, function(data){



				if(data.length >0) {



					$('#suggestions').fadeIn();



					$('#suggestionsList').html(data);



					$('#big-search').removeClass('load');



				}



			});



		}



	}







	function fill(thisValue) {



		$('#big-search').val(thisValue);



		setTimeout("$('#suggestions').fadeOut();", 600);



	}



	



	<?php



	



	if(is_home()):



	



		$quant_slider 		= 5;



		$quant_slider_move 	= 1;



		$slider_pause 		= 5000;



		$slider_speed		= 1000;



		



		$quant_slider 		= apply_filters('ProjectTheme_quantity_slider_filter', 		$quant_slider);



		$quant_slider_move 	= apply_filters('ProjectTheme_quantity_slider_move_filter', $quant_slider_move);



		$slider_pause 		= apply_filters('ProjectTheme_slider_pause_filter', 		$slider_pause);



		$slider_speed 		= apply_filters('ProjectTheme_slider_speed_filter', 		$slider_speed);



		



	?>



	



	



		$(function(){



	  $('#slider2').bxSlider({



		auto: true,



		speed: <?php echo $slider_speed; ?>,



		pause: <?php echo $slider_pause; ?>,



		autoControls: false,



		displaySlideQty: <?php echo $quant_slider; ?>,



    	moveSlideQty: <?php echo $quant_slider_move; ?>



	  });



	  



	  $("#project-home-page-main-inner").show();



	  



	  



	});	



	



	<?php endif; ?>



	



	</script>



    







    <?php do_action('ProjectTheme_before_head_tag_closes'); ?>

	

	<meta name="google-site-verification" content="LiANp14v5n-LuOeaMad_Sdorkp1XLK331tobs9IRNDI" />



	</head>



	<body <?php body_class(); ?> >







	<?php do_action('ProjectTheme_after_body_tag_open'); ?>







	<div id="wrapper">



		<!-- start header area -->







		<div id="header">



			<div class="top-bar-bg">



				<div class="top-bar wrapper"> 



                



                	<div class="rss_icn">



                    <div class="sk1_pr_rss"><a href="<?php bloginfo('siteurl'); ?>/?feed=rss&post_type=project"><img src="<?php bloginfo('template_url'); ?>/images/rss.png" border="0" width="19" height="19" alt="rss icon" /></a> </div> <div class="sk2_pr_rss"><a href="<?php bloginfo('siteurl'); ?>/?feed=rss&post_type=project" style="color:#fff;">Print Job Feed</a></div></div>



                



                                  



                    <div class="top-links">	<ul>						



							<?php 



								



							if(current_user_can('level_10')) {?><li> <a href="<?php bloginfo('siteurl'); ?>/wp-admin"><?php echo __("Wp-Admin",'ProjectTheme'); ?></a> </li><?php }



							



							



							do_action('ProjectTheme_top_menu_items');



						



							$menu_name = 'primary-projecttheme-header';







							if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $menu_name ] ) ) {



							$menu = wp_get_nav_menu_object( $locations[ $menu_name ] );



						



							$menu_items = wp_get_nav_menu_items($menu->term_id);



					



						



							foreach ( (array) $menu_items as $key => $menu_item ) {



								$title = $menu_item->title;



								$url = $menu_item->url;



								if(!empty($title))



								echo '<li><a href="' . $url . '">' . $title . '</a></li>';



							}



								



							} 



							



							$ProjectTheme_show_blue_menu = get_option('ProjectTheme_show_blue_menu');



							if($ProjectTheme_show_blue_menu == 'no'):



							?>



                            



                            <li><a href="<?php bloginfo('siteurl'); ?>"><?php echo __("Home",'ProjectTheme'); ?></a> </li>



                           <li> <a href="<?php echo projectTheme_advanced_search_link2(); ?>"><?php echo __("Advanced Search",'ProjectTheme'); ?></a> </li>



                            



                            



                            <?php							



							endif;



							?>



                            



                            



                            <?php



							



							global $current_user;



							get_currentuserinfo();



							$uid = $current_user->ID;



							



							 if(ProjectTheme_is_user_business($uid)): ?>



                             



							<!--- <li><a href="<?php echo projectTheme_post_new_link(); ?>"><?php echo __("Request Quotes",'ProjectTheme'); ?></a> </li> --->



                            <?php endif; ?>



                            



							<!--- <?php if(get_option('projectTheme_enable_blog') == "yes") { ?>



                            <li><a href="<?php echo projectTheme_blog_link(); ?>"><?php echo __("Blog",'ProjectTheme'); ?></a></li> 



							<?php } ?> --->



							<?php



							



						



							



								if(is_user_logged_in())



								{



									global $current_user;



									get_currentuserinfo();



									$u = $current_user;



									



									



									?>



									



									<li><a href="<?php echo projectTheme_my_account_link(); ?>">Account: <strong><?php echo $u->user_login; ?></strong></a></li>



									<li><a href="<?php echo wp_logout_url(); ?>"><?php echo __("Log Out",'ProjectTheme'); ?></a></li>



									



									<?php



								}



								else



									{



										



							



							?>



							



							<li><a href="<?php bloginfo('siteurl') ?>/wp-login.php?action=register"><?php echo __("Register",'ProjectTheme'); ?></a></li>



							<li><a href="<?php bloginfo('siteurl') ?>/wp-login.php"><?php echo __("Log In",'ProjectTheme'); ?></a></li>



							<?php } ?> </ul>



						</div>



                    



                    



				</div>



			</div> <!-- end top-bar-bg -->



			



			



			<div class="middle-header-bg">



				<div class="middle-header wrapper">



						



						<?php



							$logo = get_option('projectTheme_logo_url');



							if(empty($logo)){



								



								$logo = get_bloginfo('template_url').'/images/logo.png';



								$logo = apply_filters('ProjectTheme_logo_url', $logo);



							}



						



							$logo_options = '';



							$logo_options = apply_filters('ProjectTheme_logo_options', $logo_options);	



							



						?>



						<a href="<?php bloginfo('siteurl'); ?>"><img id="logo" alt="<?php bloginfo('name'); ?>" <?php echo $logo_options; ?> src="<?php echo $logo; ?>" /></a>



						



						



						



						<!-- #####-->



                     



	       



        <div class="my_placeholder_4_suggest">



        <div id="suggest" >



                    <form method="get" action="<?php echo projectTheme_advanced_search_link(); ?>">



						<input type="text" onfocus="this.value=''" id="big-search" name="term" autocomplete="off" onkeyup="suggest(this.value);" onblur="fill();"  value="<?php if(isset($_GET['term'])) echo $_GET['term']; 



						else echo $default_search; ?>" />



					



				<?php	//echo sitemile_get_categories_slug("project_cat", $_GET["project_cat_cat"], 1, "big-search-select"); 



				?>



		



					



					<input type="submit" id="big-search-submit" name="search_me" value="<?php _e("Search","ProjectTheme"); ?>" />



					</form>
               



                    <div class="suggestionsBox" id="suggestions" style="z-index:999;display: none;"> <img src="<?php echo get_bloginfo('template_url');?>/images/arrow.png" style="position: relative; top: -12px; left: 30px;" alt="upArrow" />



        <div class="suggestionList" id="suggestionsList"> &nbsp; </div>



      </div></div>

	  



                    







                        



                        <!-- ###### -->



				</div>



                </div>



				



			</div> <!-- middle-header-bg -->



		



			



		</div>	



        



        <?php



			



			do_action("ProjectTheme_content_before_main_menu");



			



			$ProjectTheme_show_blue_menu = get_option('ProjectTheme_show_blue_menu');



			if($ProjectTheme_show_blue_menu == 'yes'):



		?>



        



        <div class="main_menu_menu_wrap">



       	<?php



		



		$menu_name = 'primary-projecttheme-main-header';







		if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $menu_name ] ) ) {



		$menu = wp_get_nav_menu_object( $locations[ $menu_name ] );



						



		$menu_items = wp_get_nav_menu_items($menu->term_id);



					



		$m = 0;				



		foreach ( (array) $menu_items as $key => $menu_item ) {



			$title = $menu_item->title;



			$url = $menu_item->url;



			if(!empty($title))



			$m++;



			}



		}



		



		



		 



		if($m == 0):



		



		?>



        <div class="main_menu_menu">



        



        	<ul>



            <li><a href="<?php bloginfo('siteurl'); ?>"><img src="<?php bloginfo('template_url'); ?>/images/home.png" border="0" /></a></li>



            



            <?php 



			



				$adv_search_btn = true;



				$adv_search_btn = apply_filters('ProjectTheme_adv_search_btn', $adv_search_btn);



				if($adv_search_btn == true):



			



			 ?>



            <!--- <li><a href="<?php echo get_permalink(get_option('ProjectTheme_advanced_search_page_id')); ?>"><?php _e('Print Project Search','ProjectTheme'); ?></a></li>  --->



            <?php endif; ?>



            



            



            



            



            



            <?php 



			



				$ProjectTheme_all_rpojects_btn = true;



				$ProjectTheme_all_rpojects_btn = apply_filters('ProjectTheme_all_rpojects_btn', $ProjectTheme_all_rpojects_btn);



				if($ProjectTheme_all_rpojects_btn == true):



			



			 ?>

			 

			<?php //if(is_user_logged_in()) { ?>

			 

			  <li><a href="<?php echo projectTheme_post_new_link(); ?>"><?php _e('Request Quotes','ProjectTheme'); ?></a></li>

			  

			<?php //} else {?>

			

				<!--<li><a href="<?php echo bloginfo('template_url'); ?>/wp-login.php?action=register"><?php _e('Request Quotes','ProjectTheme'); ?></a></li>-->

			

			<?php //} ?>



            <li><a href="<?php echo get_permalink(get_option('ProjectTheme_all_projects_page_id')); ?>"><?php _e('Print Jobs','ProjectTheme'); ?></a></li>



            <?php endif; ?>

			

			

			<?php 



			



				$all_cats_btn = true;



				$all_cats_btn = apply_filters('ProjectTheme_all_cats_btn', $all_cats_btn);



				if($all_cats_btn == true):



			



			 ?> 



            <!--- <li><a href="<?php echo get_permalink(get_option('ProjectTheme_all_categories_page_id')); ?>"><?php _e('All Categories','ProjectTheme'); ?></a></li> --->



            <?php endif; ?>



            <?php 



			



				$prov_search_btn = true;



				$prov_search_btn = apply_filters('ProjectTheme_prov_search_btn', $prov_search_btn);



				if($prov_search_btn == true):



			



			 ?>

			 

			  

            <li><a href="<?php echo get_permalink(get_option('ProjectTheme_provider_search_page_id')); ?>"><?php _e('Printers','ProjectTheme'); ?></a></li>

			

			<li><a href="<?php echo bloginfo('template_url'); ?>/faqs/"><?php _e('FAQs','ProjectTheme'); ?></a></li>

			

			<li><a href="<?php echo projectTheme_blog_link(); ?>"><?php _e("Blog",'ProjectTheme'); ?></a></li>

			

			<li><a href="<?php echo bloginfo('template_url'); ?>/about/"><?php _e('About','ProjectTheme'); ?></a></li>

			

			<li><a href="<?php echo bloginfo('template_url'); ?>/contact/"><?php _e('Contact','ProjectTheme'); ?></a></li>

			

			 <li>

					<a href="http://www.facebook.com/PrintQuoteNz" target="_blank"><img src="<?php bloginfo('template_url'); ?>/images/fb.png" border="0" width="27" height="27" alt="fb icon" /></a> 

					<a href="http://twitter.com/PrintQuoteNZ" target="_blank"><img src="<?php bloginfo('template_url'); ?>/images/twitter.png"  border="0" width="27" height="27" alt="twitter icon" /></a>

					<a href="http://www.linkedin.com/company/printquote-co-nz?_mSplash=1" target="_blank"><img src="<?php bloginfo('template_url'); ?>/images/linkedin.png" border="0" width="27" height="27" alt="linkedin icon" /></a> </li>





            <?php endif; ?>



            



            



            



            



            	<?php



		



			$ProjectTheme_enable_project_location = get_option('ProjectTheme_enable_project_location');



			if($ProjectTheme_enable_project_location == "yes"):



		



		?>



            



            <?php 



			



				$all_locs_btn = true;



				$all_locs_btn = apply_filters('ProjectTheme_all_locs_btn', $all_locs_btn);



				if($all_locs_btn == true):



			



			 ?>             



            <li><a href="<?php echo get_permalink(get_option('ProjectTheme_all_locations_page_id')); ?>"><?php _e('Show All Locations','ProjectTheme'); ?></a></li> 



            <?php endif; ?>



            



              <?php



					



					endif;



					



					



							do_action('ProjectTheme_main_menu_items');



					



							



							$menu_name = 'primary-projecttheme-main-header';







							if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $menu_name ] ) ) {



							$menu = wp_get_nav_menu_object( $locations[ $menu_name ] );



						



							$menu_items = wp_get_nav_menu_items($menu->term_id);



					



						



							foreach ( (array) $menu_items as $key => $menu_item ) {



								$title = $menu_item->title;



								$url = $menu_item->url;



								if(!empty($title))



								echo '<li><a href="' . $url . '">' . $title . '</a></li>';



							}



								



							}



							



							



							?>



                            



                       



            </ul>



        



        



        </div>



        



        <?php else: 



		



		$event = 'hover';



		$effect = 'fade';



		$fullWidth = ',fullWidth: true';



		$speed = 0;



		$submenu_width = 200;



		$menuwidth = 100;



		



		?>



        



        <script type="text/javascript">



				



				var $ = jQuery;



				



				jQuery(document).ready(function($) {



					jQuery('#<?php echo 'item_main_menus'; ?> .menu').dcMegaMenu({



						rowItems: <?php echo $menuwidth; ?>,



						subMenuWidth: '<?php echo $submenu_width; ?>',



						speed: <?php echo $speed; ?>,



						effect: '<?php echo $effect; ?>',



						event: '<?php echo $event; ?>'



						<?php echo $fullWidth; ?>



					});



				});



		</script>



       



        <div class="main_menu_menu">



        <div class="dcjq-mega-menu" id="<?php echo 'item_main_menus'; ?>">		



		<?php



			



			$menu_name = 'primary-projecttheme-main-header';







			if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $menu_name ] ) ) 



			$nav_menu = wp_get_nav_menu_object( $locations[ $menu_name ] );					



							 



			



			wp_nav_menu( array( 'fallback_cb' => '', 'menu' => $nav_menu, 'container' => false ) );



		



		?>

			



		</div>

		

		



        </div>



        



        <?php endif; ?>



        



          </div> 



		<?php



		endif;



		?>



        



        <?php



		



		do_action("ProjectTheme_content_after_main_menu");



		



		if( ProjectTheme_is_home()):



			include 'lib/home_head.php';



			include 'lib/slider_home.php';



			// include 'lib/stretch_area.php';



		



		endif;



		



		



		?>



        



        <div id="main_wrapper">

		

			



		<div id="main" class="wrapper"><div class="padd10">

		

		
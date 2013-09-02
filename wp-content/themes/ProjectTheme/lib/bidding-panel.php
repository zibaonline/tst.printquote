<?php

	if(!is_user_logged_in())
	{
		
		echo sprintf(__('You are not logged in. In order to bid please <a href="%s">login</a> or <a href="%s">register</a> an account','ProjectTheme'),
		get_bloginfo('siteurl').'/wp-login.php',get_bloginfo('siteurl').'/wp-register.php');
		exit;	
	}



	global $wpdb,$wp_rewrite,$wp_query;
	$pid = $_GET['get_bidding_panel'];
	
	global $current_user;
	get_currentuserinfo();
	$cid = $current_user->ID;
	$cwd = str_replace('wp-admin','',getcwd());

	$cwd .= 'wp-content/uploads';
	
	$post = get_post($pid);
	
	$query = "select * from ".$wpdb->prefix."project_bids where uid='$cid' AND pid='$pid'";
	$r = $wpdb->get_results($query); $bd_plc = 0;
	
	if(count($r) > 0) { $row = $r[0]; $bid = $row->bid; $description = $row->description; $days_done = $row->days_done; $bd_plc = 1; }
	
	do_action('ProjectTheme_display_bidding_panel', $pid);
	
	//====================================================================
	
	$is_it_allowed = true;
	$is_it_allowed = apply_filters('ProjectTheme_is_it_allowed_place_bids', $is_it_allowed);
	
	if($is_it_allowed != true):
	
	do_action('ProjectTheme_is_it_not_allowed_place_bids_action');	
?>



<?php else: ?>	


<script type="text/javascript">

function check_submits()
{
	if(!$('#submits_crt_check').is(':checked'))
	{
		alert("<?php _e('Please agree to the printer terms.','ProjectTheme'); ?>");
		return false;
	}
	else return true;
}


</script>
	<div class="box_title"><?php echo sprintf(__("Quote On: %s",'ProjectTheme'), $post->post_title ); ?></div>
  	<div class="bid_panel" style="width:550px;height:450px">
	
  <p><strong>PLEASE NOTE:</strong> The "Payment Terms" you submitted when you applied will be displayed with your quote. If your terms have changed, please <a href="<?php bloginfo('url'); ?>/my-account/personal-information/" target="_blank">update them here</a> first.</p>
    <?php
	
	$do_not_show = 0;
	$uid = $cid;
	
	$projectTheme_enable_custom_bidding = get_option('projectTheme_enable_custom_bidding');
	if($projectTheme_enable_custom_bidding == "yes")
	{
		$ProjectTheme_get_project_primary_cat = ProjectTheme_get_project_primary_cat($pid);	
		$projectTheme_theme_bidding_cat_ = get_option('projectTheme_theme_bidding_cat_' . $ProjectTheme_get_project_primary_cat);
		
		if($projectTheme_theme_bidding_cat_ > 0)
		{
			$ProjectTheme_get_credits = ProjectTheme_get_credits($uid);
			
			if(	$ProjectTheme_get_credits < $projectTheme_theme_bidding_cat_) { $do_not_show = 1;	
				$prc = projecttheme_get_show_price($projectTheme_theme_bidding_cat_);
			}
		}
		
	}
    
	if($do_not_show == 1 and $bd_plc != 1)
	{
		echo '<div class="padd10">';
		echo sprintf(__('You need to have at least %s in your account to bid. <a href="%s">Click here</a> to deposit money.','ProjectTheme'), $prc, get_permalink(get_option('ProjectTheme_my_account_payments_id')));		
		echo '</div>';
	}
	else
	{
		?>
    
                <div class="padd10">
                <form onsubmit="return check_submits();" method="post" action="<?php echo get_permalink($pid); ?>"> 
                <input type="hidden" name="control_id" value="<?php echo base64_encode($pid); ?>" /> 
                	<ul class="project-details" style="width:100%">
		                           
                            <li>
								<h3><?php _e('Total Quote Amount:','ProjectTheme'); ?></h3>
								<p> <?php 
								
								$currency = projectTheme_currency();
								$currency = apply_filters('ProjectTheme_currency_in_bidding_panel', $currency);
								
								echo $currency; ?> <input type="text" name="bid" value="<?php echo $bid; ?>" size="10" /> <span style="font-size:12px; color:#666;">inc. GST, shipping, handling &amp; fees</span>
                               
                                </p>
							</li>
                            
                            <li>
								<h3><?php _e('Days To Complete:','ProjectTheme'); ?></h3>
								<p><input type="text" name="days_done" value="<?php echo $days_done; ?>" size="3" /> day(s)
                              
                                </p>
							</li>
                           <?php
						   
						   	$ProjectTheme_enable_project_files = get_option('ProjectTheme_enable_project_files');
						   
						   	if($ProjectTheme_enable_project_files != "no"):
						   
						   ?> 
                            
                            <li>
								<h3><?php _e('Relevant Files:','ProjectTheme'); ?></h3>
								 
                                <!-- ################### -->
                                
                                
           
	

<script type="text/javascript" src="<?php echo get_bloginfo('template_url'); ?>/lib/uploadify/jquery.uploadify-3.1.js"></script>     
	<link rel="stylesheet" href="<?php echo get_bloginfo('template_url'); ?>/lib/uploadify/uploadify.css" type="text/css" />
	
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
			uploader      : '<?php echo get_bloginfo('template_url'); ?>/lib/uploadify/uploady3.php',
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
    






	<div id="fileUpload3">You have a problem with your javascript</div>
	<div id="thumbnails" style="overflow:hidden;margin-top:20px">
    
   
   
   <?php


	$args = array(
	'order'          => 'ASC',
	'post_type'      => 'attachment',
	'post_parent'    => $pid,
		'post_author'    => $cid,
	'meta_key'		 => 'another_reserved1',
	'meta_value'	 => '1',
	'numberposts'    => -1,
	'post_status'    => null,
	);
	$attachments = get_posts($args);
	if ($attachments) {
	    foreach ($attachments as $attachment) {
		$url = $attachment->post_title;
		$imggg = $attachment->post_mime_type; 
		
		if($attachment->post_author == $cid)
		
			echo '<div class="div_div"  id="image_ss'.$attachment->ID.'">'.$url.'
			<a href="javascript: void(0)" onclick="delete_this(\''.$attachment->ID.'\')"><img border="0" src="'.get_bloginfo('template_url').'/images/delete_icon.png" /></a>
			</div>';
	  
	}
	}


	?>
   
    
    </div>
                                
                                
                                
                                <!-- ################### -->
                                
							</li>
                            <?php endif; ?>
                            
                            <li>
								<h3><?php _e('Additional Details:','ProjectTheme'); ?></h3>
								<p>
                                
                                <textarea name="description2" cols="40" rows="6"><?php echo $description; ?></textarea><br/>
                             
                                <input type="hidden" name="control_id" value="<?php echo base64_encode($pid); ?>" />
                                </p>
							</li>
                            
                            
                            <li>
								<h3> </h3>
								<p >
                                
                                
                                <input type="checkbox" name="accept_trms" id="submits_crt_check" value="1" /> I agree to the <a href='<?php bloginfo('url'); ?>/printer-terms/' target='_blank'>Printer Terms</a> </p>
							</li>
                            
                            <li>
								<h3> </h3>
								<p>
                                
                                
                                <input class="my-buttons" id="submits_crt" type="submit" name="bid_now_reverse" value="<?php _e("Submit Quote",'ProjectTheme'); ?>" /></p>
							</li>
                            
                	</ul>
                   </form>
                </div> <?php } ?>
                </div> <?php endif; ?>
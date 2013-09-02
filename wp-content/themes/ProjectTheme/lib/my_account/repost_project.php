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
	
	function ProjectTheme_filter_ttl($title){return __("Repost Quote Request",'ProjectTheme')." - ";}
	add_filter( 'wp_title', 'ProjectTheme_filter_ttl', 10, 3 );	
	
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }   
	   
	
	get_currentuserinfo;   

	$post = get_post($pid);

	$uid 	= $current_user->ID;
	$title 	= $post->post_title;
	$cid 	= $current_user->ID;
	
	if($uid != $post->post_author) { echo 'Not your post. Sorry!'; exit; }

//-------------------------------------


$cid = $uid;
		
	
		//---autodrafting
		
			$new_pid = ProjectTheme_get_auto_draft($uid);
			$itwas_reposted = get_post_meta($new_pid, 'itwas_reposted_', true);
			
			if(empty($itwas_reposted))
			{
				
				update_post_meta($new_pid, 'itwas_reposted_', "done");
				
				
				$args = array(
				'order'          => 'ASC',
				'orderby'        => 'post_date',
				'post_type'      => 'attachment',
				'post_parent'    => $pid,
				
				'post_status'    => null,
				'numberposts'    => -1,
				);
				$attachments = get_posts($args);
				$uploads = wp_upload_dir();
				
				foreach($attachments as $att)
				{
						$img_url = wp_get_attachment_url($att->ID);
						$basedir = $uploads['basedir'].'/';
						$exp = explode('/',$img_url);
					
						
						$nr = count($exp);
						$pic = $exp[$nr-1];
						$year = $exp[$nr-3];
						$month = $exp[$nr-2];
					
						if($uploads['basedir'] == $uploads['path'])
						{
							$img_url = $basedir.'/'.$pic;
							$ba = $basedir.'/';
							$iii = $uploads['url'];
						}
						else
						{
							$img_url = $basedir.$year.'/'.$month.'/'.$pic;
							$ba = $basedir.$year.'/'.$month.'/';
							$iii = $uploads['baseurl']."/".$year."/".$month;
						}
						
						$oldPic_name = $img_url;
						
						$newpicname = 'copy_'.rand(0,999).'_'.$pic;
						$newPic_name = $uploads['path'].'/'.$newpicname;
						
						//echo $oldPic_name.'<br/>';
						//echo $newPic_name.'<br/>';
						
						copy($oldPic_name, $newPic_name);
						ProjectTheme_insert_pic_media_lib($cid, $new_pid, $uploads['url'].'/'.$newpicname, $newPic_name, $newpicname);
						//echo $newPic_name.'<br/>';
					
					
					
				}
				
				//-----------------------
			}

			// lets submit it
			
			if(isset($_POST['project_submit1']))
			{
				$project_title 			= trim(strip_tags($_POST['project_title']));
				$project_description 	= nl2br(strip_tags($_POST['project_description']));
				$project_category 		= strip_tags($_POST['project_cat_cat']);
				$project_location 		= trim($_POST['project_location_cat']);
				$project_tags 			= trim(strip_tags($_POST['project_tags']));
				
				$price 					= projectTheme_clear_sums_of_cash(trim($_POST['price']));
				$project_location_addr 	= strip_tags(trim($_POST['project_location_addr']));
				
			
				
				//-------------------------------
				$adOK = 1;
				
				if(empty($project_title)) 		{ $adOK = 0; $error['title'] 		= __('You cannot leave the quote request title blank!','ProjectTheme'); }
				if(empty($project_description)) { $adOK = 0; $error['description'] 	= __('You cannot leave the quote request description blank!','ProjectTheme'); }
			
				
				//-------------------------------
	
					$project_category2 	= $project_category;
		
					$my_post = array();
					$my_post['post_title'] 		= $project_title;
					$my_post['ID'] 				= $new_pid;
					$my_post['post_content'] 	= $project_description;	
					$my_post['post_status'] 	= 'draft';	
					wp_update_post( $my_post );
					
				//-----------------------------------------	
					
					
					$term 				= get_term( $project_category, 'project_cat' );	
					$project_category 	= $term->slug;
					wp_set_object_terms($new_pid, array($project_category),'project_cat');
			
				//-----------------------------------------
						
					
					$term 				= get_term( $project_location, 'project_location' );	
					$project_location 	= $term->slug;
					wp_set_object_terms($new_pid, array($project_location),'project_location');	
						
				//-----------------------------------------
					  
					//$_SESSION['coupon'] = false;
					  
					wp_set_post_tags( $new_pid, $project_tags);
					  
					update_post_meta($new_pid, "Location", $project_location_addr);
					update_post_meta($new_pid, "price", $price);
					update_post_meta($new_pid, 'is_draft', "0");  
					update_post_meta($new_pid, "paid", "0");
					
					if(isset($_POST['featured'])) 
						update_post_meta($new_pid, "featured", "1");
					else
						update_post_meta($new_pid, "featured", "0");  

		
					update_post_meta($new_pid, "private_bids", strip_tags($_POST['private_bids'])); 
					update_post_meta($new_pid, "views", '0');
			
					 
					$end = $_POST['ending']; 
					 
					if(empty($end)) $ending = current_time('timestamp',0) + 30*3600*24; // ending time for auction
					else $ending = strtotime($end, current_time('timestamp',0));
				
					update_post_meta($new_pid, "closed", "0");
					update_post_meta($new_pid, "closed_date", "0");
					update_post_meta($new_pid, "ending", $ending);
					
					update_post_meta($new_pid, "price", 		ProjectTheme_get_budget_name_string_fromID($_POST['budgets'])); // set project price
					update_post_meta($new_pid, "budgets", 		$_POST['budgets']);
					 
					//------ custo fields --------------
		
					$arr = $_POST['custom_field_id'];
					for($i=0;$i<count($arr);$i++)
					{
						$ids 	= $arr[$i];
						$value 	= strip_tags($_POST['custom_field_value_'.$ids]);
						
						if(is_array($value))
						{
							for($j=0;$j<count($value);$j++)
								update_post_meta($new_pid, "custom_field_ID_".$ids, $value[$j]);
						}
						else
						update_post_meta($new_pid, "custom_field_ID_".$ids, $value);
						
					}  
					
	
				if($adOK == 1) //if everything ok, go to next step
				{		
					
					wp_redirect(get_bloginfo('siteurl').'/?p_action=relist_this_done&pid='.$new_pid);
					//wp_redirect(get_permalink($new_pid));
					exit;	
				}
				
			}

//=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-

	get_header();
	
	
	$post 		= get_post($pid);
	$location 	= wp_get_object_terms($pid, 'project_location');
	$cat 		= wp_get_object_terms($pid, 'project_cat');	
?>


	<div id="content" >
        	
            <div class="my_box3">
            	<div class="padd10">
            
            	<div class="box_title"><?php _e("Repost Quote Request", "ProjectTheme"); ?></div>
                <div class="box_content"> 
            	
                
               
      
      <?php
	
	$ProjectTheme_enable_images_in_projects = get_option('ProjectTheme_enable_images_in_projects');
				if($ProjectTheme_enable_images_in_projects == "yes"):
				
				?>
         <ul class="post-new3">  
       <li>
        <h2><?php _e('','ProjectTheme'); ?></h2>
        <p>	
        	
               
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



<!-- ####################################################################### -->  
        	
  </p>

</li> </ul>     
              
                <?php endif; //print_r($error); ?>
                
                <form method="post">  
    <ul class="post-new3">
        <li>
        	<h2><?php echo __('Your print job title', 'ProjectTheme'); ?>:</h2>
        	<p><input type="text" size="50" class="do_input" name="project_title" 
            value="<?php echo (empty($_POST['project_title']) ? 
			($post->post_title == "draft project" ? "" : $post->post_title) : $_POST['project_title']); ?>" /></p>
        </li>
        
        <li>
        	<h2><?php echo __('Category', 'ProjectTheme'); ?>:</h2>
        	<p><?php	echo ProjectTheme_get_categories("project_cat",  
			!isset($_POST['project_cat_cat']) ? (is_array($cat) ? $cat[0]->term_id : "") : $_POST['project_cat_cat']
			, __('Select','ProjectTheme'), "do_input"); ?></p>
        </li>
        
	
    	        <?php   

	$cid = $current_user->ID;
	$cwd = str_replace('wp-admin','',getcwd());
	$cwd .= 'wp-content/uploads';

	//echo get_template_directory();

?>


		<li>
        <h2><?php _e("Files (.zip, .pdf, .docx)",'ProjectTheme'); ?>:</h2>
        <p>

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
    
        
        <!--- <li>
        	<h2><?php echo __('Price', 'ProjectTheme'); ?>:</h2>
        <p>
        
          <?php
	  
	  $sel = get_post_meta($pid, 'budgets', true);
	  echo ProjecTheme_get_budgets_dropdown($sel, 'do_input');
	  
	  ?>
        
        </p>
        </li> --->
        
        
        

        
        
        
                 <li>
        <h2>
        
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js"></script>
<script src="http://ajax.aspnetcdn.com/ajax/jquery.templates/beta1/jquery.tmpl.min.js"></script>

        
        
        <link rel="stylesheet" media="all" type="text/css" href="<?php echo get_bloginfo('template_url'); ?>/css/ui-thing.css" />
		<script type="text/javascript" language="javascript" src="<?php echo get_bloginfo('template_url'); ?>/js/jquery-ui-timepicker-addon.js"></script>
 
       <?php _e("Quote due by",'ProjectTheme'); ?>:</h2>
        <p><input type="text" name="ending" id="ending" class="do_input"  /></p>
        </li>
        
 <script>

$(document).ready(function() {
	 $('#ending').datetimepicker({
	showSecond: true,
	timeFormat: 'hh:mm:ss'
});});
 
 </script>
        
        
       <!--- <li>
        	<h2><?php echo __('Location', 'ProjectTheme'); ?>:</h2>
        <p><?php	echo ProjectTheme_get_categories("project_location", 
		empty($_POST['project_location_cat']) ? (is_array($location) ? $location[0]->term_id : "") : $_POST['project_location_cat'], __('Select','ProjectTheme'), 
		"do_input"); ?></p>
        </li> --->
        
        <input type="hidden" class="do_input" name="private_bids" value="1" /> 
		
        <!--- <li>
        	<h2><?php echo __('Private Bids', 'ProjectTheme'); ?>:</h2>
        <p><select name="private_bids">
        <option value="no" <?php if(get_post_meta($pid,'private_bids',true) == "no") echo 'selected="selected"'; ?>><?php _e("No",'ProjectTheme'); ?></option>
        <option value="yes" <?php if(get_post_meta($pid,'private_bids',true) == "yes") echo 'selected="selected"'; ?>><?php _e("Yes",'ProjectTheme'); ?></option>
        
        </select>
		
        </p>
        </li>
        
        
        
        <li>
        	<h2><?php echo __('Address','ProjectTheme'); ?>:</h2>
        <p><input type="text" size="50" class="do_input"  name="project_location_addr" value="<?php echo !isset($_POST['project_location_addr']) ? 
		get_post_meta($pid, 'Location', true) : $_POST['project_location_addr']; ?>" /> </p>
        </li> --->
        
        
        <li>
        	<h2><?php echo __('Description', 'ProjectTheme'); ?>:</h2>
        <p><textarea rows="6" cols="40" class="do_input description_edit"  name="project_description"><?php 
		echo empty($_POST['project_description']) ? trim($post->post_content) : $_POST['project_description']; ?></textarea></p>
        </li>


		<!--- <li>
        	<h2><?php echo __('Tags', 'ProjectTheme'); ?>:</h2>
        <p><input type="text" size="50" class="do_input"  name="project_tags" value="<?php echo $project_tags; ?>" /> </p>
        </li>--->
        
        
        
        
        <!--- <li>
        <h2><?php _e("Feature project?",'ProjectTheme'); ?>:</h2>
        <p><input type="checkbox" class="do_input" name="featured" <?php
		$featured = get_post_meta($pid, 'featured', true);
		if(isset($_POST['featured'])) echo 'checked="checked"';
		else
		{
			if($featured == "1") echo 'checked="checked"';		
		}
		 ?> value="1" /> 
        <?php _e("By clicking this checkbox you mark your project as featured. Extra fee is applied.", 'ProjectTheme'); ?></p>
        </li> --->
       
        
        
        <!--- <li>
        <h2><?php _e("Sealed bidding?",'ProjectTheme'); ?>:</h2>
        <p><input type="checkbox" class="do_input" name="private_bids" <?php
		$private_bids = get_post_meta($pid, 'private_bids', true);
		if(isset($_POST['private_bids'])) echo 'checked="checked"';
		else
		{
			if($private_bids == "1") echo 'checked="checked"';		
		}
		 ?> value="1" /> 
        <?php _e("By clicking this checkbox you mark your seal the bidding. Extra fee is applied.", 'ProjectTheme'); ?></p>
        </li> --->
        
        
        
        <li>
        <h2><?php _e("Make Private",'ProjectTheme'); ?>:</h2>
        <p><input type="checkbox" class="do_input" name="hide_project" <?php
		$hide_project = get_post_meta($pid, 'hide_project', true);
		if(isset($_POST['hide_project'])) echo 'checked="checked"';
		else
		{
			if($hide_project == "1") echo 'checked="checked"';		
		}
		 ?> value="1" /> 
        <?php _e("By clicking this checkbox your print job can only be seen by printers and you.", 'ProjectTheme'); ?></p>
        </li>
        
        
       <!--         
        <li>
        <h2><?php _e("Coupon", "ProjectTheme"); ?>:</h2>
        <p><input type="text" class="do_input" name="coupon" size="30" />
        <?php if($ok_ad == 0 && isset($_POST['auction_submit1'])) _e('The coupon code you used is wrong.','ProjectTheme'); ?></p>
        </li>
        
-->
        
        <?php /*-------  custom fields  -------- */ ?>
        <?php
		
		
		$arr = ProjectTheme_get_project_category_fields($catid, $pid);
		
		for($i=0;$i<count($arr);$i++)
		{
			        echo '<li>';
					echo '<h2>'.$arr[$i]['field_name'].$arr[$i]['id'].':</h2>';
					echo '<p>'.$arr[$i]['value'].'</p>';
					echo '</li>';
		
		}	
		
		
		?>        
       
     
        
        <li>
        <h2>&nbsp;</h2>
        <p><input type="submit" name="project_submit1" value="<?php _e("Publish Quote Request", 'ProjectTheme'); ?> >>" /></p>
        </li>
    
    
    </ul>
    </form>
                
                
                </div>
                </div>
                </div>
                </div>
                
	<?php ProjectTheme_get_users_links(); ?>

<?php get_footer(); ?>
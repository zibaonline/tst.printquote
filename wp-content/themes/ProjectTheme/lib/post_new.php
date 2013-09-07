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





function ProjectTheme_post_new_area_function()

{

	

	global $wp_query, $projectOK, $current_user, $MYerror;

	get_currentuserinfo();

	

	$new_Project_step = $wp_query->query_vars['post_new_step'];

	if(empty($new_Project_step)) $new_Project_step = 1;

	

	$pid = $wp_query->query_vars['projectid'];	

	$uid = $current_user->ID;

	

?>

    	<div id="content" style="width:100%">

        	

            <div class="my_box3">

            	<div class="padd10">

            

            	<div class="box_title"><?php _e("Request A Print Quote", "ProjectTheme"); ?></div>

                <div class="box_content"> 

                

                <?php

				

				$is_it_allowed = true;

				$is_it_allowed = apply_filters('ProjectTheme_is_it_allowed_place_bids', $is_it_allowed);

	

				if($is_it_allowed != true):

	

					do_action('ProjectTheme_is_it_not_allowed_place_bids_action');	

				

				else:

				

				?>

                

            	

    			<?php

				

					echo '<div id="steps">';

						echo '<ul>';

							echo '<li '.($new_Project_step == '1' ? "class='active_step' " : "").'>'.__("STEP 1", 'ProjectTheme').'</li>';

							do_action('ProjectTheme_after_step1_show', $new_Project_step);

							echo '<li '.($new_Project_step == '2' ? "class='active_step' " : "").'>'.__("STEP 2", 'ProjectTheme').'</li>';

							echo '<li '.($new_Project_step == '3' ? "class='active_step' " : "").'>'.__("PUBLISH", 'ProjectTheme').'</li>';

						echo '</ul>';		

					echo '</div>';





//****************************************************************************************





if($new_Project_step == "1")

{

	//-----------------

	

	$location 	= wp_get_object_terms($pid, 'project_location');

	$cat 		= wp_get_object_terms($pid, 'project_cat');

	

	

	if(!empty($pid))

	$post 		= get_post($pid);

	

	

	if(is_array($MYerror))

	if($projectOK == 0)

	{

		echo '<div class="errrs">';

		

			foreach($MYerror as $e)		

			echo '<div class="newad_error">'.$e. '</div>';	

	

		echo '</div>';

		

	}

	

	?>

 <form method="post" action="<?php echo ProjectTheme_post_new_with_pid_stuff_thg($pid, '1');?>">  

    <ul class="post-new">

    <?php do_action('ProjectTheme_step1_before_title'); ?>

    

        <li>

        	<h2><?php echo __('Your print job title', 'ProjectTheme'); ?>:</h2>

        	<p><input type="text" size="50" class="do_input" name="project_title" 

            value="<?php echo (empty($_POST['project_title']) ? 

			($post->post_title == "Auto Draft" ? "" : $post->post_title) : $_POST['project_title']); ?>" /></p>

        </li>

       

     <?php do_action('ProjectTheme_step1_before_category'); ?>  

        

        <li><h2><?php echo __('Category', 'ProjectTheme'); ?>:</h2>

        	<p><?php	echo ProjectTheme_get_categories("project_cat",  

			!isset($_POST['project_cat_cat']) ? (is_array($cat) ? $cat[0]->term_id : "") : $_POST['project_cat_cat']

			, __("Select Category","ProjectTheme"), "do_input"); ?></p>

        </li>

  

  <?php do_action('ProjectTheme_step1_before_price'); ?>

  

        <!--- <li><h2><?php echo __('Price', 'ProjectTheme'); ?>:</h2>

        <p>

        

      <?php

	  

	  $sel = get_post_meta($pid, 'budgets', true);

	  echo ProjecTheme_get_budgets_dropdown($sel, 'do_input');

	  

	  ?>

      

      </p>

        </li> --->

       

        <?php do_action('ProjectTheme_step1_before_ending'); ?>

        

        <li>

        <h2>

        

        

        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>

		<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js"></script>

        <script src="http://ajax.aspnetcdn.com/ajax/jquery.templates/beta1/jquery.tmpl.min.js"></script>

        <script src="<?php echo get_bloginfo('template_url'); ?>/js/jquery.iframe-transport.js"></script>

        <script src="<?php echo get_bloginfo('template_url'); ?>/js/jquery.fileupload.js"></script>

        <script src="<?php echo get_bloginfo('template_url'); ?>/js/jquery.fileupload-ui.js"></script>

        <script src="<?php echo get_bloginfo('template_url'); ?>/js/application.js"></script>  	

        	

        

        <link rel="stylesheet" media="all" type="text/css" href="<?php echo get_bloginfo('template_url'); ?>/css/ui_thing.css" />

		<script type="text/javascript" language="javascript" src="<?php echo get_bloginfo('template_url'); ?>/js/timepicker.js"></script>

          



       <?php _e("Quotes due by",'ProjectTheme'); ?>:</h2>

       <?php 

	   

	   $dt = get_post_meta($pid,'ending',true);

	   

	   if(!empty($dt))

	   $dt = date_i18n('d-m-Y H:i',$dt);

	   

	   ?>

       <p><input type="text" name="ending" id="ending" class="do_input" value="<?php echo $dt; ?>"  /></p>

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

        

        <?php do_action('ProjectTheme_step1_before_location'); ?>

        <?php

		

			$ProjectTheme_enable_project_location = get_option('ProjectTheme_enable_project_location');

			if($ProjectTheme_enable_project_location == "yes"):

		

		?>

        <li>

        	<h2><?php echo __('Location', 'ProjectTheme'); ?>:</h2>

        <p><?php	echo ProjectTheme_get_categories("project_location", 

		empty($_POST['project_location_cat']) ? (is_array($location) ? $location[0]->term_id : "") : $_POST['project_location_cat'], __("Select Location","ProjectTheme"), "do_input"); ?></p>

        </li>

       

       

       <?php do_action('ProjectTheme_step1_before_address'); ?>    

       <?php

	   

	   $show_address = true;

	   $show_address = apply_filters('ProjectTheme_show_address_filter', $show_address);

	   

	   if($show_address == true):

	   

	   ?> 

        <li>

        	<h2><?php echo __('Address','ProjectTheme'); ?>:</h2>

        <p><input type="text" size="50" class="do_input"  name="project_location_addr" value="<?php echo !isset($_POST['project_location_addr']) ? 

		get_post_meta($pid, 'Location', true) : $_POST['project_location_addr']; ?>" /> </p>

        </li>

        <?php endif; endif; ?>

        

        <?php do_action('ProjectTheme_step1_before_description'); ?>

        <li>

        	<h2><?php echo __('Description', 'ProjectTheme'); ?>:</h2>

        <p><textarea rows="6" cols="60" class="do_input description_edit"  name="project_description"><?php 

		echo empty($_POST['project_description']) ? trim($post->post_content) : $_POST['project_description']; ?></textarea></p>

        </li>



		

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

        </li> --->

        

        

        <?php do_action('ProjectTheme_step1_after_tags'); ?>

        

        <li>

        <h2>&nbsp;</h2>

        <p> 

        <input type="submit" name="project_submit1" value="<?php _e("Next Step", 'ProjectTheme'); ?> >>" /></p>

        </li>

    	

        <?php do_action('ProjectTheme_step1_after_submit'); ?>

    

    </ul>

    </form>

    <?php



}



if($new_Project_step == "2")

{

	global $MYerror, $projectOK;

	

	$cid 	= $current_user->ID;

	do_action('ProjectTheme_post_new_step2_before_images'); 

	

	

	if(is_array($MYerror))

	if($projectOK == 0)

	{

		echo '<div class="errrs">';

		

			foreach($MYerror as $e)		

			echo '<div class="newad_error">'.$e. '</div>';	

	

		echo '</div>';

		

	}

	

	?>

    

    

    

   

 	<ul class="post-new">

    

    <?php

	

	$ProjectTheme_enable_images_in_projects = get_option('ProjectTheme_enable_images_in_projects');

	

	$ProjectTheme_enable_images_in_projects_filter = true;

	$ProjectTheme_enable_images_in_projects_filter = apply_filters('ProjectTheme_enable_images_in_projects_filter', $ProjectTheme_enable_images_in_projects_filter);

	

	if($ProjectTheme_enable_images_in_projects_filter == true):

	

				if($ProjectTheme_enable_images_in_projects == "yes"):

				

				?>

    	

        

        <?php

		echo '<div class="images_sub_small_txt">';

		

		$ProjectTheme_enable_max_images_limit = get_option('ProjectTheme_enable_max_images_limit');

		if($ProjectTheme_enable_max_images_limit == "yes")

		{

			$projectTheme_nr_max_of_images = get_option('projectTheme_nr_max_of_images');

			if(empty($projectTheme_nr_max_of_images))	 $projectTheme_nr_max_of_images = 10;

			

			echo sprintf(__('There is a limit for the uploaded images. The maximum number of images you can upload for this project is %s.','ProjectTheme'), $projectTheme_nr_max_of_images);		

			

		}

		

		$ProjectTheme_charge_fees_for_images = get_option('ProjectTheme_charge_fees_for_images');

		$projectTheme_extra_image_charge	= get_option('projectTheme_extra_image_charge');

		

		if($ProjectTheme_charge_fees_for_images == "yes")

		{

			$projectTheme_nr_of_free_images = get_option('projectTheme_nr_of_free_images');

			if(empty($projectTheme_nr_of_free_images)) $projectTheme_nr_of_free_images = 1;	

			

			echo '<br/>';

			echo sprintf(__('There are %s free images. After that each image will be charged %s.','ProjectTheme'), $projectTheme_nr_of_free_images, ProjectTheme_get_show_price($projectTheme_extra_image_charge));	

		}

		echo '</div>';

		?>

        

        

        

        

    	<li>

        <h2><?php _e('Images, e.g. logo, artwork','ProjectTheme'); ?>:</h2>

        <p>	

                    

        <!-- ##################################################################### -->







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





</li> <?php endif; endif; ?>



<?php   



	$cid = $current_user->ID;

	$cwd = str_replace('wp-admin','',getcwd());

	$cwd .= 'wp-content/uploads';



	//echo get_template_directory();

	

	do_action('ProjectTheme_step2_before_images'); 

	

?>

<?php

						   

						   	$ProjectTheme_enable_project_files = get_option('ProjectTheme_enable_project_files');						   

						   	if($ProjectTheme_enable_project_files != "no"):

						   

						   ?>



		<li>

        <h2><?php _e("Files (.zip, .pdf, .doc, .docx, .stl)",'ProjectTheme'); ?>:</h2>

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

			fileTypeExts  : '*.zip;*.pdf;*.doc;*.stl;*.docx',

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

	

	if($pid > 0)

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



      <form method="post" > 

      <?php do_action('ProjectTheme_step2_before_project_files'); ?>

      

      <?php

						   

						   	$ProjectTheme_enable_featured_option = get_option('ProjectTheme_enable_featured_option');						   

						   	if($ProjectTheme_enable_featured_option != "no"):

						   

						   ?>

      

      

      

      	  <?php /*-------  custom fields  -------- */ ?>

        <?php

		

		$show_fields_in_step2 = true;

		$show_fields_in_step2 = apply_filters('ProjectTheme_show_fields_in_step2', $show_fields_in_step2);

		

		if($show_fields_in_step2 == true):

		

			$catid = ProjectTheme_get_project_primary_cat($pid);

			$arr = ProjectTheme_get_project_category_fields($catid, $pid);

			

				for($i=0;$i<count($arr);$i++)

				{

					

							echo '<li>';

							echo '<h2>'.$arr[$i]['field_name'].$arr[$i]['id'].':</h2>';

							echo '<p>'.$arr[$i]['value'].'</p>';

							echo '</li>';

					

					

				}	

		

		endif;

		

		?>        

       

      

      

       <!--- <li>

        <h2><?php _e("Feature project?",'ProjectTheme'); ?>:</h2>

        <p><input type="checkbox" class="do_input" name="featured" value="1" 

		<?php $feature = get_post_meta($pid, 'featured', true); echo ($feature == "1" ? "checked='checked'" : ""); ?> /> 

        <?php 

		

				

		$projectTheme_featured_fee = get_option('projectTheme_featured_fee');

		$sl = __('Extra fee is applied','ProjectTheme');

		if(empty($projectTheme_featured_fee) or $projectTheme_featured_fee <= 0) $sl = '';

		

		

		printf(__("By clicking this checkbox you mark your project as featured. %s", 'ProjectTheme'), $sl); ?></p>

        </li> --->

        

        <?php endif; ?>

        

        <?php do_action('ProjectTheme_step2_before_feature_project'); ?>

        

        

        <?php

						   

						   	$ProjectTheme_enable_sealed_option = get_option('ProjectTheme_enable_sealed_option');						   

						   	if($ProjectTheme_enable_sealed_option != "no"):

						   

						   ?>

<input type="hidden" class="do_input" name="private_bids" value="1"

        <?php $private_bids = get_post_meta($pid, 'private_bids', true); echo ($private_bids == "1" ? "checked='checked'" : ""); ?> /> 

        

      <!---  <li>

        <h2><?php _e("Sealed Bidding?",'ProjectTheme'); ?>:</h2>

        <p><input type="hidden" class="do_input" name="private_bids" value="1"

        <?php $private_bids = get_post_meta($pid, 'private_bids', true); echo ($private_bids == "1" ? "checked='checked'" : ""); ?> /> 

        <?php 

		

		$projectTheme_sealed_bidding_fee = get_option('projectTheme_sealed_bidding_fee');

		$sl = __('Extra fee is applied','ProjectTheme');

		if(empty($projectTheme_sealed_bidding_fee) or $projectTheme_sealed_bidding_fee <= 0) $sl = '';

		

		

		printf(__("By clicking this checkbox you hide your project's bids. %s", 'ProjectTheme'), $sl); ?></p>

        </li> --->

        <?php endif; ?>

        

        <?php do_action('ProjectTheme_step2_before_sealed_bidding'); ?>

        

        <?php

						   

						   	$ProjectTheme_enable_hide_option = get_option('ProjectTheme_enable_hide_option');						   

						   	if($ProjectTheme_enable_hide_option != "no"):

						   

						   ?>        

        

        <li>

        <h2><?php _e("Make Private",'ProjectTheme'); ?>:</h2>

        <p><input type="checkbox" class="do_input" name="hide_project" value="1" 

        <?php $hide_project = get_post_meta($pid, 'hide_project', true); echo ($hide_project == "1" ? "checked='checked'" : ""); ?>/> 

        <?php 

		

		$projectTheme_hide_project_fee = get_option('projectTheme_hide_project_fee');

		$sl = __('Extra fee is applied','ProjectTheme');

		if(empty($projectTheme_hide_project_fee) or $projectTheme_hide_project_fee <= 0) $sl = '';

		

		echo sprintf(__("By clicking this checkbox your print job can only be seen by printers and you. %s", 'ProjectTheme'), $sl); ?></p>

        </li>

        <?php endif; ?>

        

		<?php do_action('ProjectTheme_step2_before_hide_project'); ?>

        

      



        

        <li>

        <h2>&nbsp;</h2>

        <?php

		

		$stp = 1;

		$stp = apply_filters('ProjectTheme_filter_go_back_stp2', $stp);

		

		?>

        <p><a href="<?php echo ProjectTheme_post_new_with_pid_stuff_thg($pid, $stp); ?>" class="go_back_btn" ><?php _e('Go Back','ProjectTheme'); ?></a> 

        <input type="submit" name="project_submit2" value="<?php _e("Next Step", 'ProjectTheme'); ?> >>" /></p>

        </li>

    

    

    </ul>

    </form>

    

    

    

    <?php

}





do_action('ProjectTheme_see_if_we_can_add_steps', $new_Project_step, $pid );



if($new_Project_step == "3")

{

	$catid = ProjectTheme_get_project_primary_cat($pid);

	$ProjectTheme_get_images_cost_extra = ProjectTheme_get_images_cost_extra($pid);

	



	

	//--------------------------------------------------

	// hide project from search engines fee calculation

	

	$projectTheme_hide_project_fee = get_option('projectTheme_hide_project_fee');

	if(!empty($projectTheme_hide_project_fee))

	{

		$opt = get_post_meta($pid,'hide_project',true);

		if($opt == "0") $projectTheme_hide_project_fee = 0;

		

		

	} else $projectTheme_hide_project_fee = 0;

	

	

	//---------------------

	

	$made_me_date 	= get_post_meta($pid,'made_me_date',true);

	$tms 			= current_time('timestamp',0);

	$projectTheme_project_period = get_option('projectTheme_project_period');

	if(empty($projectTheme_project_period)) $projectTheme_project_period = 30;

	

	

	if(empty($made_me_date))

	{

		$ee = $tms + 3600*24*$projectTheme_project_period;

		update_post_meta($pid,'ending',$ee);		

	}

	else

	{

		$ee = get_post_meta($pid, 'ending', true) + $tms - $made_me_date;

		update_post_meta($pid,'ending',$ee);	

	}

	



	//-------------------------------------------------------------------------------

	// sealed bidding fee calculation

	

	$projectTheme_sealed_bidding_fee = get_option('projectTheme_sealed_bidding_fee');

	if(!empty($projectTheme_sealed_bidding_fee))

	{

		$opt = get_post_meta($pid,'private_bids',true);

		if($opt == "0") { $projectTheme_sealed_bidding_fee = 0; }

		

		 

	} else $projectTheme_sealed_bidding_fee = 0;



	

	//-------

	

	$featured	 = get_post_meta($pid, 'featured', true);

	$feat_charge = get_option('projectTheme_featured_fee');

	

	if($featured != "1" ) $feat_charge = 0;

	



	

	

	$custom_set = get_option('projectTheme_enable_custom_posting');

	if($custom_set == 'yes')

	{

		$posting_fee = get_option('projectTheme_theme_custom_cat_'.$catid);

		if(empty($posting_fee)) $posting_fee = 0;		

	}

	else

	{

		$posting_fee = get_option('projectTheme_base_fee');

	}

	

	$total = $feat_charge + $posting_fee + $projectTheme_sealed_bidding_fee + $projectTheme_hide_project_fee + $ProjectTheme_get_images_cost_extra;

	

	//-----------------------------------------------

	

		$payment_arr = array();

		

		$base_fee_paid 	= get_post_meta($pid, 'base_fee_paid', true);

		

		if($base_fee_paid != "1" and $posting_fee > 0)

		{

			$my_small_arr = array();

			$my_small_arr['fee_code'] 		= 'base_fee';

			$my_small_arr['show_me'] 		= true;

			$my_small_arr['amount'] 		= $posting_fee;

			$my_small_arr['description'] 	= __('Base Fee','ProjectTheme');

			array_push($payment_arr, $my_small_arr);

		}

		//-----------------------

		

		

		$my_small_arr = array();

		$my_small_arr['fee_code'] 		= 'extra_img';

		$my_small_arr['show_me'] 		= true;

		$my_small_arr['amount'] 		= $ProjectTheme_get_images_cost_extra;

		$my_small_arr['description'] 	= __('Extra Images Fee','ProjectTheme');

		array_push($payment_arr, $my_small_arr);

		//------------------------

		

		$featured_paid  	= get_post_meta($pid,'featured_paid',true);

		$opt 				= get_post_meta($pid,'featured',true);

 

		

		if($feat_charge > 0 and $featured_paid != 1 and $opt == 1)

		{

			$my_small_arr = array();

			$my_small_arr['fee_code'] 		= 'feat_fee';

			$my_small_arr['show_me'] 		= true;

			$my_small_arr['amount'] 		= $feat_charge;

			$my_small_arr['description'] 	= __('Featured Fee','ProjectTheme');

			array_push($payment_arr, $my_small_arr);

			//------------------------

		}

		

		$private_bids_paid  = get_post_meta($pid,'private_bids_paid',true);

		$opt 				= get_post_meta($pid,'private_bids',true);

 

		

		if($projectTheme_sealed_bidding_fee > 0 and $private_bids_paid != 1  and ($opt == 1 or $opt == "yes"))

		{

		

			$my_small_arr = array();

			$my_small_arr['fee_code'] 		= 'sealed_project';

			$my_small_arr['show_me'] 		= true;

			$my_small_arr['amount'] 		= $projectTheme_sealed_bidding_fee;

			$my_small_arr['description'] 	= __('Sealed Bidding Fee','ProjectTheme');

			array_push($payment_arr, $my_small_arr);

		//------------------------

		}

		

		$hide_project_paid 	= get_post_meta($pid,'hide_project_paid',true);

		$opt 				= get_post_meta($pid,'hide_project',true);

		

		if($projectTheme_hide_project_fee > 0 and $hide_project_paid != "1" and ($opt == "1" or $opt == "yes"))

		{

		

			$my_small_arr = array();

			$my_small_arr['fee_code'] 		= 'hide_project';

			$my_small_arr['show_me'] 		= true;

			$my_small_arr['amount'] 		= $projectTheme_hide_project_fee;

			$my_small_arr['description'] 	= __('Make Private','ProjectTheme');

			array_push($payment_arr, $my_small_arr);

		

		}

		

		$payment_arr 	= apply_filters('ProjectTheme_filter_payment_array', $payment_arr, $pid);

		$new_total 		= 0;

		

		foreach($payment_arr as $payment_item):			

			if($payment_item['amount'] > 0):				

				$new_total += $payment_item['amount'];			

			endif;			

		endforeach;

		

	//-----------------------------------------------

	

	$post 			= get_post($pid);

	$admin_email 	= get_bloginfo('admin_email');



	

	$total = apply_filters('ProjectTheme_filter_payment_total', $new_total, $pid);

	

	//----------------------------------------

	$finalize = isset($_GET['finalize']) ? true : false;

	update_post_meta($pid, 'finalised_posted', '1');

  

	//-----------

	

	if($total == 0)
	{
			echo '<div >';
                        if( is_user_logged_in() ) {
                            echo __('Thank you for requesting your free quote with us. To complete the process click Request Quotes below...','ProjectTheme');
                        }
			update_post_meta($pid, "paid", "1");

				if(get_option('projectTheme_admin_approves_each_project') == 'yes')
				{
					$my_post = array();
					$my_post['ID'] = $pid;
					$my_post['post_status'] = 'draft';

					wp_update_post( $my_post );

					if($finalize == true){
						ProjectTheme_send_email_posted_project_not_approved($pid);
						ProjectTheme_send_email_posted_project_not_approved_admin($pid);
					}

					echo '<br/>'.__('Your project isn`t live yet, the admin needs to approve it.', 'ProjectTheme');

				}
				else
				{
					$my_post = array();
					$my_post['ID'] = $pid;
					$my_post['post_status'] = 'publish';

					if($finalize == true){

						wp_update_post( $my_post );
						wp_publish_post( $pid );


						ProjectTheme_send_email_posted_project_approved($pid);
						ProjectTheme_send_email_posted_project_approved_admin($pid);

						ProjectTheme_send_email_subscription($pid);

					

					}

				}

			 

			echo '</div>';

			

	

	}

	else

	{

			update_post_meta($pid, "paid", "0");

			

			echo '<div >';

			echo __('Thank you for posting your project with us. Below is the total price that you need to pay in order to put your project live.<br/>

			Click the pay button and you will be redirected...', 'ProjectTheme');

			echo '</div>';

			

	 

	}

	

	//----------------------------------------

if( is_user_logged_in() ) {	

	echo '<table style="margin-top:25px">';

	

	$show_payment_table = true;

	$show_payment_table = apply_filters('ProjectTheme_filter_payment_show_table', $show_payment_table, $pid);

	

	if($show_payment_table == true)

	{

		



		foreach($payment_arr as $payment_item):

			

			if($payment_item['amount'] > 0):

			

				echo '<tr>';

				echo '<td>'.$payment_item['description'].'&nbsp; &nbsp;</td>';

				echo '<td>'.ProjectTheme_get_show_price($payment_item['amount'],2).'</td>';

				echo '</tr>';



			endif;

			

		endforeach;

	

		

		

		

		echo '<tr>';

		echo '<td>&nbsp;</td>';

		echo '<td></td>';

		echo '</tr>';

		


		echo '<tr>';

		echo '<td><strong>'.__('Total to Pay','ProjectTheme').'</strong></td>';

		echo '<td><strong>'.ProjectTheme_get_show_price($total,2).'</strong></td>';

		echo '</tr>';

		
	

		echo '<tr>';

		echo '<td><strong>'.__('Your Total Credits','ProjectTheme').'</strong></td>';

		echo '<td><strong>'.ProjectTheme_get_show_price(ProjectTheme_get_credits($uid),2).'</strong></td>';

		echo '</tr>';

		
		

		echo '<tr>';

		echo '<td>&nbsp;<br/>&nbsp;</td>';

		echo '<td></td>';

		echo '</tr>';

	

	}//endif show this table

	

	if($total == 0 && $finalize == true)

	{

		if(get_option('projectTheme_admin_approves_each_project') != 'yes'):

		

                        echo '<tr>';

                        echo '<td colspan="2">Thank you, your quote has been published successfully!<br/><br/></td>';

                        echo '</tr>';
                
                
			echo '<tr>';

			echo '<td></td>';

			echo '<td><a href="'.get_permalink($pid).'" class="submit_bottom" style="color:#FFF;text-decoration:none;padding:8px">'.__('View Quote Request','ProjectTheme') .'</a></td>';

			echo '</tr>';	

		

		else:

			

			echo '<tr>';

			echo '<td></td>';

			echo '<td><a href="'.get_permalink(get_option('ProjectTheme_my_account_page_id')).'" class="go_back_btn">'.__('Go to your account','ProjectTheme') .'</a></td>';

			echo '</tr>';	

				

		endif;

		

		echo '</table>';

	}

	elseif($total > 0)

	{

			echo '</table>';

		update_post_meta($pid,'unpaid','1');

		

		

		

		

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

						

						do_action('ProjectTheme_add_payment_options_to_post_new_project', $pid);

						

	

	

	} else  { echo '</table>'; }

}	

	
        do_action('ProjectTheme_step3_before_finalize'); 
        
        if($_GET['finalize'] != 1 &&  is_user_logged_in() ) { echo 'Click the "Request Quotes" button below:'; }

	echo '<div class="clear10"></div>';

	echo '<div class="clear10"></div>';        

	echo '<div class="clear10"></div>';

	
if ( is_user_logged_in() ) { 
	if($finalize == false)

	echo '<a id="project_submit3_back_btn"  href="'. ProjectTheme_post_new_with_pid_stuff_thg($pid, '2') .'" class="submit_bottom" style="color:#FFF;text-decoration:none;padding:8px;margin-right:10px" >'.__('Go Back','ProjectTheme').'</a>';

	

	if($total == 0 && $finalize == false)

	echo '<a id="project_submit3_final_btn" href="'. ProjectTheme_post_new_with_pid_stuff_thg($pid, '3', 'finalize').'" 

	class="submit_bottom" style="color:#FFF;text-decoration:none;padding:8px" >'.__('Request Quotes','ProjectTheme').'</a>';
}
	





}





 ?>

                

            <?php endif; ?>

                

                </div>

              </div>

           </div>

                

        </div> <!-- end dif content -->

     

        

    

	

<?php	

} 



add_action('ProjectTheme_step3_before_finalize', 'extra_login_form');

function extra_login_form(){    

    
?>      
       <?php  if ( !is_user_logged_in() && $_GET['join'] == 'success' ) { ?>
       
       <div id="rqform_wp_account_yesno_html">
           
            <style>label{width:75px; float:left;}</style>
            <h2>Almost there! Please login to publish your quote request:</h2>
            <form name="rqform_wp_account" id="rqform_wp_account" method="post" action="">
                <p class="login-username">
                        <label for="user_login">Username</label>
                        <input type="text" name="log" id="user_login" class="do_input" value="" size="20">
                </p>
                <p class="login-password">
                        <label for="user_pass">Password</label>
                        <input type="password" name="pwd" id="user_pass" class="do_input" value="" size="20">
                </p>


                <p class="login-submit"><label>&nbsp;</label><input type="submit" name="rq-submit" id="wp-submit" class="submit_bottom" value="Login To Publish"></p>        
            </form>
		    <p id="nav"><label>&nbsp;</label><a title="Password Lost and Found" href="http://www.printquote.co.nz/wp-login.php?action=lostpassword" target="blank">Lost your password?</a></p>
            
       </div>
        
       
       <?php } else if ( !is_user_logged_in() ) { ?>
        <div>
            <h2>Now, Login or Register to request quotes: <?php echo get_the_title($_GET['projectid']); ?></h2>
            <p>
                Login <input type="radio" name="rqform_wp_account_yesno" id="rqform_wp_account_yes" value="yes" /> &nbsp; &nbsp; 
                Register <input type="radio" name="rqform_wp_account_yesno" id="rqform_wp_account_no"  value="no" /> 
                
            </p>
        </div>
        <div id="rqform_wp_account_yesno_html"></div>
        <?php } ?>

<?php add_action('wp_footer', 'rqform_wp_account_yesno_js'); ?>        
        
<?php }

function rqform_wp_account_yesno_js(){ ?>
    
        <script>
            
            jQuery('#rqform_wp_account_yes').live('click', function(){
                var data = {
                    action: 'rqform_wp_account',
                    task: 'get_login_html'
                };
                jQuery.post(ajaxurl, data, function(response){
                   jQuery('#rqform_wp_account_yesno_html').html(response); 
                });
            });
            jQuery('#rqform_wp_account_no').live('click', function(){
                var data = {
                    action: 'rqform_wp_account',
                    task: 'get_register_html'
                };
                jQuery.post(ajaxurl, data, function(response){
                   jQuery('#rqform_wp_account_yesno_html').html(response); 
                });
            });

//            if(jQuery('input[name="rqform_wp_account_yesno"]').length >0){
//                    // wp account exists yes no validation
//                    var login_choice = jQuery('input[name="rqform_wp_account_yesno"]:checked').val();
//                    if(typeof(login_choice)=== "undefined" || login_choice==''){
//                        jQuery('.box_content .errrs').remove();
//                        jQuery('.box_content #steps').after('<div class="errrs"><div class="newad_error">You need to login first to continue</div></div>');
//                        return false;                    
//                    }
//                }
                    
                    
            jQuery('input[name="rq-submit"]').live('click', function(){                     
                    // wp account exists yes no validation
                    var rqform_all_errors='';
                    var has_error='';
                    
                    // email validation
                    if(jQuery('#rqform_wp_account_yesno_html input[name="eml"]').length >0){
                        var eml = jQuery('#rqform_wp_account_yesno_html input[name="eml"]').val();
                        if(typeof(eml)=== "undefined" || eml=='' || !IsEmail(eml) ){
                            has_error = true;
                            rqform_all_errors += '<div class="newad_error">Please enter a valid email address</div>';
                        }
                    }
                    // username validation
                    if(jQuery('#rqform_wp_account_yesno_html input[name="log"]').length >0){
                        var log = jQuery('#rqform_wp_account_yesno_html input[name="log"]').val();
                        if(typeof(log)=== "undefined" || log==''){
                            has_error = true;
                            rqform_all_errors += '<div class="newad_error">Please enter your username</div>';
                        }
                    }
                    // password validation
                    if(jQuery('#rqform_wp_account_yesno_html input[name="pwd"]').length >0){
                        var log = jQuery('#rqform_wp_account_yesno_html input[name="pwd"]').val();
                        if(typeof(log)=== "undefined" || log==''){
                            has_error = true;
                            rqform_all_errors += '<div class="newad_error">Please enter your password</div>';
                        }
                    }
                    if(has_error) {
                        jQuery('.box_content .errrs').remove();
                        jQuery('.box_content #steps').after('<div class="errrs">'+rqform_all_errors+'</div>');
                        return false; 
                    }
                    // end of wp account exists yes no validation
                
            });  
            
            // protecting "request quote" button till wp account yes no exists
            jQuery('#project_submit3_final_btn').live('click', function(){ 
                if(jQuery('input[name="rqform_wp_account_yesno"]').length >0){
                    return false;
                }
            });  

            // email validation
            function IsEmail(email) {
                var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                return regex.test(email);
            }            
            
        </script>
        
<?php }

/* Some Ajax work */
add_action('wp_ajax_rqform_wp_account', 'rqform_wp_account_callback');
add_action('wp_ajax_nopriv_rqform_wp_account', 'rqform_wp_account_callback');


function rqform_wp_account_callback() {
    if($_POST['task'] == 'get_login_html'){ ?>                
        
    <style>label{width:75px; float:left;}</style>
    <form name="rqform_wp_account" id="rqform_wp_account" method="post" action="">
        <p class="login-username">
                <label for="user_login">Username</label>
                <input type="text" name="log" id="user_login" class="do_input" value="" size="20">
        </p>
        <p class="login-password">
                <label for="user_pass">Password</label>
                <input type="password" name="pwd" id="user_pass" class="do_input" value="" size="20">
        </p>
        

        <p class="login-submit"><label>&nbsp;</label><input type="submit" name="rq-submit" id="wp-submit" class="submit_bottom" value="Login To Publish"></p>        
    </form>
	<p id="nav"><label>&nbsp;</label><a title="Password Lost and Found" href="http://www.printquote.co.nz/wp-login.php?action=lostpassword" target="blank">Lost your password?</a></p>
    
    <?php exit; }
    
    if($_POST['task'] == 'get_register_html'){ ?>    
    
    <style>label{width:75px; float:left;}</style>
    <form name="rqform_wp_account" id="rqform_wp_account" method="post" action="">
        <p class="login-email">
                <label for="user_login">Email</label>
                <input type="text" name="eml" id="user_email" class="do_input" value="" size="20">
        </p>
        <p class="login-username">
                <label for="user_login">Username</label>
                <input type="text" name="log" id="user_login" class="do_input" value="" size="20">
        </p>
        <p class="login-password">
                <label for="user_pass">Password</label>
                <input type="password" name="pwd" id="user_pass" class="do_input" value="" size="20">
        </p>

        <p class="submit"><label>&nbsp;</label><input type="submit" value="Register" class="submit_bottom" id="wp-submit" name="rq-submit"></p>
    </form>
        
    <?php exit; }
    
}


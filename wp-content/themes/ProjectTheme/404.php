<?php
/********************************************************************
*
*	ProjectTheme for WordPress - sitemile.com
*	http://sitemile.com/p/project
*	Copyright (c) 2012 sitemile.com
*	Coder: Saioc Dragos Andrei
*	Email: andreisaioc@gmail.com
*
*********************************************************************/



	get_header();

?>

	<?php 

		if(function_exists('bcn_display'))
		{
		    echo '<div class="my_box3_breadcrumb"><div class="padd10_a">';	
		    bcn_display();
			echo '</div></div>';
		}
		
		
		
?>


<div id="content">
    <div class="box_title"><?php _e('Page Not Found','ProjectTheme'); ?></div>
	<div class="padd10">
<?php _e('The requested page cannot be found. Maybe your project has not been approved yet.','ProjectTheme'); ?>

    </div>
    </div>


  <!-- ################### -->
    
    <div id="right-sidebar">    
    	<ul class="xoxo">
        	 <?php dynamic_sidebar( 'single-widget-area' ); ?>
        </ul>    
    </div>


<?php

	get_footer();

?>
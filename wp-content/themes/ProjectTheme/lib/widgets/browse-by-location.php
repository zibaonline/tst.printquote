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

function ProjectTheme_get_custom_taxonomy_count($ptype,$pterm) {
	global $wpdb;
	
	$s = "select * from ".$wpdb->prefix."terms where slug='$pterm'";
	$r = $wpdb->get_results($s);
	$r = $r[0];
	
	$term_id = $r->term_id;
	

	
	//--------
	
	$s = "select * from ".$wpdb->prefix."term_taxonomy where term_id='$term_id'";
	$r = $wpdb->get_results($s);
	$r = $r[0];
	
	$term_taxonomy_id = $r->term_taxonomy_id;

	
	//--------
	
	$s = "select distinct posts.ID from ".$wpdb->prefix."term_relationships rel, $wpdb->postmeta wpostmeta, $wpdb->posts posts 
	 where rel.term_taxonomy_id='$term_taxonomy_id' AND rel.object_id = wpostmeta.post_id AND posts.ID = wpostmeta.post_id AND posts.post_status = 'publish' AND posts.post_type = 'project' AND wpostmeta.meta_key = 'closed' AND wpostmeta.meta_value = '0'";
	$r = $wpdb->get_results($s);
	

	
	return count($r);
}


add_action('widgets_init', 'register_browse_by_location_widget');
function register_browse_by_location_widget() {
	register_widget('ProjectTheme_browse_by_location');
}

class ProjectTheme_browse_by_location extends WP_Widget {

	function ProjectTheme_browse_by_location() {
		$widget_ops = array( 'classname' => 'browse-by-location', 'description' => 'Show all locations and browse by location' );
		$control_ops = array( 'width' => 200, 'height' => 250, 'id_base' => 'browse-by-location' );
		$this->WP_Widget( 'browse-by-location', 'ProjectTheme - Browse by Location', $widget_ops, $control_ops );
	}

	function widget($args, $instance) {
		extract($args);
		
		echo $before_widget;
		
		if ($instance['title']) echo $before_title . apply_filters('widget_title', $instance['title']) . $after_title;
		
		$loc_per_row 	= $instance['loc_per_row'];
		$widget_id 		= $args['widget_id'];
		$nr_rows 		= $instance['nr_rows'];
		$only_these 	= $instance['only_these'];
		$only_parents	= $instance['only_parents'];
				
		if($only_parents == "on") $only_parents = true;
		else $only_parents = false;
		
		
		$nr = 4;
		
		if(!empty($loc_per_row)) $nr = $loc_per_row;
		echo '<style>#'.$widget_id.' #location-stuff li>ul { width: '.(round(100/$nr)-0.5).'%}</style>';
		
		if($nr_rows > 0) $jk = "&number=".($nr_rows * $loc_per_row);
		
		$terms_k = get_terms("project_location","parent=0&hide_empty=0");
		$terms = get_terms("project_location","parent=0&hide_empty=0".$jk);
		 
		 
		//$term = get_term( $term_id, $taxonomy ); 
		
		if($only_these == "1")
		{
			$terms = array();
			
			foreach($terms_k as $trm)
			{
				if($instance['term_' . $trm->term_id] == $trm->term_id)
					array_push($terms, $trm);
			}
			
		}
		
		 
		//-----------------------------
		 
		 if(count($terms) < count($terms_k)) $disp_btn = 1;
		else $disp_btn = 0;
		 
		 
		$count = count($terms); $i = 0;
		if ( $count > 0 ){
		     echo "<ul id='location-stuff'>";
		     foreach ( $terms as $term ) {
		       
			   if($i%$nr == 0) echo "<li>";
		       $total_ads = 0;
			   $terms2 = '';
			   	$terms2 = get_terms("project_location","parent=".$term->term_id."&hide_empty=0");
			
				$mese = '';
				
					$mese .= '<ul>';
					$mese .= "<img src=\"".get_bloginfo('template_url')."/images/location.png\" width=\"20\" height=\"20\" /> 
		       		<h3><a href='".get_term_link($term->slug,"project_location")."'>" . $term->name;
					
					//."</a></h3>";
			   
			   $total_ads = ProjectTheme_get_custom_taxonomy_count('project',$term->slug);
			   
			   $mese2 = '';
			   if($terms2 && $only_parents == false)
				{
					
					foreach ( $terms2 as $term2 ) 
					{
						$tt = ProjectTheme_get_custom_taxonomy_count('project',$term2->slug);
		       			$total_ads += $tt;
						$mese2 .= "<li><a href='".get_term_link($term2->slug,"project_location")."'>" . $term2->name." (".$tt.")</a></li>";
					}
				}
					
					echo $mese."(".$total_ads.")</a></h3>";
					echo $mese2;
					
					echo '</ul>';
				
				
		       if(($i+1) % $nr == 0) echo "</li>";
			   
			   $i++;
		        
		     }
				
				//if(($i+1) % $nr != 0) echo "</li>";


		     echo "</ul>";
			 
		 }           
			
		if($disp_btn == 1)
		{
				echo '<br/><b><a href="'.get_permalink(get_option('ProjectTheme_all_locations_page_id')).'">'.__('See More Locations','ProjectTheme').'</a></b>';		
		}		
			
			
				
		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
	
		return $new_instance;
	}

	function form($instance) { ?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title'); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" 
			value="<?php echo esc_attr( $instance['title'] ); ?>" style="width:95%;" />
		</p>
        
         <p>
			<label for="<?php echo $this->get_field_id('only_parents'); ?>"><?php _e('Only show parent categories'); ?>:</label>
			<input type="checkbox" id="<?php echo $this->get_field_id('only_parents'); ?>" name="<?php echo $this->get_field_name('only_parents'); ?>" 
			<?php echo (esc_attr( $instance['only_parents'] ) == "on" ? "checked='checked'" : ""); ?> />
		</p>
        
		<p>
			<label for="<?php echo $this->get_field_id('loc_per_row'); ?>"><?php _e('Number of Columns'); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id('loc_per_row'); ?>" name="<?php echo $this->get_field_name('loc_per_row'); ?>" 
			value="<?php echo esc_attr( $instance['loc_per_row'] ); ?>" style="width:20%;" />
		</p>
				
        <p>
			<label for="<?php echo $this->get_field_id('nr_rows'); ?>"><?php _e('Number of Rows'); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id('nr_rows'); ?>" name="<?php echo $this->get_field_name('nr_rows'); ?>" 
			value="<?php echo esc_attr( $instance['nr_rows'] ); ?>" style="width:20%;" />
		</p>
        
        
         <p>
			<label for="<?php echo $this->get_field_id('nr_rows'); ?>"><?php _e('Only show locations below'); ?>:</label>
			<?php echo '<input type="checkbox" name="'.$this->get_field_name('only_these').'"  value="1" '.(
	 $instance['only_these'] == "1" ? ' checked="checked" ' : "" ).' /> '; ?>
		</p>
        
        <p>
			<label for="<?php echo $this->get_field_id('nr_rows'); ?>"><?php _e('Locations to show'); ?>:</label>
			
                <div style=" width:220px;
    height:180px;
    background-color:#ffffff;
    overflow:auto;border:1px solid #ccc">
     <?php
	 
	 $terms = get_terms("project_location","parent=0&hide_empty=0");
	 foreach ( $terms as $term ) {
	 
	 echo '<input type="checkbox" name="'.$this->get_field_name('term_'.$term->term_id).'"  value="'.$term->term_id.'" '.(
	 $instance['term_'.$term->term_id] == $term->term_id ? ' checked="checked" ' : "" ).' /> ';
	 echo $term->name.'<br/>';
	 
	 }
	 
	 ?>
     
    </div> 
            
            
            
		</p>
		         
                	
	<?php 
	}
}




?>
<?php
///////////////////////////////////////////////////////////////////////////////////////////////////
date_default_timezone_set('Pacific/Auckland');
define('SCHEDULE_TIME', '14:00:00');

///////////////////////////////////////////////////////////////////////////////////////////////////
add_action( 'wp',  'setup_my_daily_cron' );

function setup_my_daily_cron() {
	//=============================================================================================
	// Extract schedule time
	$time_arr = explode(':', SCHEDULE_TIME);
	$schedule_hour = $time_arr[0];
	$schedule_minute = $time_arr[1];
	$schedule_second = $time_arr[2];

	//=============================================================================================
	$now = time();
	$target_time = mktime($schedule_hour, $schedule_minute, $schedule_second, date('n', $now), date('j', $now), date('Y', $now));

	//=============================================================================================
	// Register WP cron!
	if( !wp_next_scheduled( 'my_daily_cron' ) ) {  
		wp_schedule_event( $target_time, 'daily', 'my_daily_cron' );  
	} else {
		$timestamp = wp_next_scheduled( 'my_daily_cron' );
		//echo date('Y-m-d H:i:s') . ' ---- ' . date('Y-m-d H:i:s', $timestamp);
	}
}

///////////////////////////////////////////////////////////////////////////////////////////////////
if (isset($_GET['unscheduled_cron'])) {
	$cron_name = $_GET['unscheduled_cron'];
	if ($cron_name === 'my_daily_cron') {
		wp_clear_scheduled_hook( 'my_daily_cron' );
		die('UNSCHEDULED! ');
	}
}

///////////////////////////////////////////////////////////////////////////////////////////////////
add_action( 'my_daily_cron',  'send_expiry_email' );

function send_expiry_email() {
	global $wpdb;

	//=============================================================================================
	// Get user option, set via WP ADMIN >> PROJECT THEME >> EMAIL SETTINGS >> PROJECT EXPIRED(OWNER)
	$enable = get_option('ProjectTheme_project_expired_enable');
	$subject_template = get_option('ProjectTheme_project_expired_subject');
	$message_template = get_option('ProjectTheme_project_expired_message');

	//=============================================================================================
	// If user disable mailing option, forget it!
	if ($enable == 'no') return true;

	//=============================================================================================
	// Extract schedule time
	$time_arr = explode(':', SCHEDULE_TIME);
	$schedule_hour = $time_arr[0];
	$schedule_minute = $time_arr[1];
	$schedule_second = $time_arr[2];

	//=============================================================================================
	$now = time();
	$end_time = mktime($schedule_hour, $schedule_minute, $schedule_second, date('n', $now), date('j', $now), date('Y', $now));
	$start_time = $end_time - (60 * 60 * 24);


	$query = "SELECT `post_id`, `meta_value` FROM " . $wpdb->prefix . "postmeta WHERE meta_key = 'ending' AND meta_value >= " . $start_time . " AND meta_value < " . $end_time;
	$res = $wpdb->get_results($query);

	foreach ($res as $row) {
		$post = get_post($row->post_id);
		if ($post->post_status !== 'publish') continue;

		$post_id = $post->ID;
		$post_author = $post->post_author;
		
		$post_status = $post->post_status;

		$user = get_userdata($post_author);
		$user_email = $user->user_email;

		$user_name = $user->user_login;					// Username
		$site_login_url = ProjectTheme_login_url();		// Login URL to PrinQuote
		$your_site_name = get_bloginfo('name');			// PrintQuote site title
		$your_site_url = get_bloginfo('siteurl');		// PrintQuote URL
		$project_name = $post->post_title;				// Current project title
		$project_link = get_permalink($post_id);		// Current project Permalink

		//=============================================================================================
		// Template tag to replace
		$find = array(
			'##username##',
			'##site_login_url##',
			'##your_site_name##',
			'##your_site_url##',
			'##project_name##',
			'##project_link##', 
		);
		
		//=============================================================================================
		// Replacement value
		$replace = array(
			$user_name,
			$site_login_url,
			$your_site_name,
			$your_site_url,
			$project_name,
			$project_link
		);

		//=============================================================================================
		// Replace template tag
		$subject = ProjectTheme_replace_stuff_for_me($find, $replace, $subject_template);
		$message = ProjectTheme_replace_stuff_for_me($find, $replace, $message_template);

		// Finaly send notification to project owner
		ProjectTheme_send_email($user_email, $subject, $message);
	}
}

// http://risan.com/tst.printquote/?unscheduled_cron=my_daily_cron
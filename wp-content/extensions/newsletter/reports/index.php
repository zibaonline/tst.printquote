<?php
require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$module = NewsletterReports::instance();
$controls = new NewsletterControls();

if ($controls->is_action('country')) {
    $module->country();
    $controls->messages = 'Completed';
}
?>

<div class="wrap">
    <?php $help_url = 'http://www.satollo.net/plugins/newsletter/reports-module'; ?>

    <?php include NEWSLETTER_DIR . '/header.php'; ?>

    <h5>Reports Module</h5>
    
    <h2>Main configuration</h2>
    
    <?php if ($module->available_version > $module->version) { ?>
    <div class="newsletter-notice">
        A new version is available. <a href="http://www.satollo.net/downloads" target="_blank">Download it now</a>.
    </div>
    <?php } ?>
    
    <?php $controls->show(); ?>
    
    <p>
        Actually this module has not a set of options. This page is here to confirm the correct installation. Now, 
        when you open the statistics of a sent email, this module will replace the standard page with some
        extended views.
    </p>
   <form method="post" action="">
    <?php $controls->init(); ?>

      
       <?php $controls->button('country', 'Run the country detection'); ?>
   </form>
</div>

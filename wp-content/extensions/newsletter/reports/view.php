<?php
require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
require_once dirname(__FILE__) . '/helper.php';

$email_id = (int)$_REQUEST['id'];
$module = NewsletterStatistics::instance();
$helper = new NewsletterStatisticsHelper();
$controls = new NewsletterControls();
$email = $module->get_email($email_id);

if ($controls->is_action('set')) {
   $r = $wpdb->query("update " . NEWSLETTER_USERS_TABLE . " set list_" . $controls->data['preference'] . "=1 where id in (select distinct user_id from " . NEWSLETTER_STATS_TABLE . " where email_id=" . $email_id . ")");
   $controls->messages = 'Done. Added ' . $r . ' subscribers.';
}

$read = $helper->get_read_count($email_id);
$clicked = $helper->get_clicked_count($email_id);
$only_read = $read-$clicked;
$total = $helper->get_click_count($email_id);
$urls = $helper->get_clicked_urls($email_id);
$events = $helper->aggregate_events_by_day($helper->get_first_events($email_id));


function percent($value, $total) {
    if ($total == 0) return '-';
    return sprintf("%.2f", $value/$total*100) . '%';
}
?>

<div class="wrap">

    <h2>Statistics for email "<?php echo htmlspecialchars($email->subject); ?>"</h2>
    <?php $controls->show(); ?>
    
    <div id="tabs">
        
    <ul>
        <li><a href="#tab-overview">Overview</a></li>
        <li><a href="#tab-events">Events</a></li>
        <li><a href="#tab-gender">Gender</a></li>
        <li><a href="#tab-countries">Countries</a></li>
        <li><a href="#tab-urls">URLs</a></li>
        <li><a href="#tab-actions">Actions</a></li>
    </ul>
    
        
    <div id="tab-overview">
        
        <table class="widefat" style="width: auto">
            <tr valign="top">
                <th>Total email sent</th>
                <td><?php echo $email->total; ?></td>
            </tr>
            <tr valign="top">
                <th>Subscribers who read the email</th>
                <td><?php echo $read; ?> (<?php echo percent($read, $email->total); ?>)</td>
            </tr>
            <tr valign="top">
                <th>Subscribers who only read the email</th>
                <td><?php echo $only_read; ?> (<?php echo percent($only_read, $email->total); ?>)</td>
            </tr>
            <tr valign="top">
                <th>Subscribers who clicked the email</th>
                <td><?php echo $clicked; ?> (<?php echo percent($clicked, $email->total); ?>)</td>
            </tr>
            <tr valign="top">
                <th>Total clicks</th>
                <td><?php echo $total; ?></td>
            </tr>
        </table>
            
    </div>
        
        
        
    <div id="tab-events">

        <p>Users' interactions (open or click) distribution over time, starting from the sending day.</p>

        <?php if (empty($events)) { ?>
            <p>No data</p>
        <?php } else { ?>
            <div id="firstevents-chart"></div>
        <?php } ?>
        
    </div>
        
        
        
    <div id="tab-gender">
        <div id="sex-chart"></div>
    </div>

        
        
    <div id="tab-countries">

        <?php 
        $countries = $wpdb->get_results("select country, count(*) as total from {$wpdb->prefix}newsletter_stats where email_id=$email_id and country<>'' group by country order by total");
        ?>

        <?php if (empty($countries)) { ?> 
            <p>No data available, just wait some time to let the processor to work to resolve the countries. Thank you.</p>
        <?php } else { ?>
            <p><div id="country-chart"></div></p>
        <?php } ?>

    </div>
        
        
        
    <div id="tab-urls">

        <?php if (empty($urls)) { ?>
            <p>No data</p>
        <?php } else { ?>
            <div id="urls-chart"></div>
        <?php } ?>

        <?php if (empty($urls)) { ?>
        <p>No clicks by now.</p>
        <?php } else { ?>
        <table class="widefat">
            <thead>
            <tr>
                <th>Chart label</th>
                <th>Clicks</th>
                <th>Anchor*</th>
                <th>URL</th>
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
                <?php for ($i=0; $i<count($urls); $i++) { ?>
                <tr>
                    <?php
                        echo '<tr>';
                        echo '<td>URL ' . ($i+1) . '</td>';
                        echo '<td>' . $urls[$i]->number . '</td>';
                        echo '<td>' . strip_tags($urls[$i]->anchor, '<img>') . '</td>';
                        echo '<td>' . htmlspecialchars($urls[$i]->url) . '</td>';
                    ?>
                    <td>
                        <?php
                        $users = $helper->get_users_by_url($email_id, $urls[$i]->url);
                        $values = $helper->split_by_sex($users);
                        ?>
                        <?php echo $values['m']; ?>&nbsp;Males<br>
                        <?php echo $values['f']; ?>&nbsp;Females<br>
                        <?php echo $values['n']; ?>&nbsp;Unknown<br>
                    </td>
                    <tr>

                    <?php } ?>

            </tbody>
        </table>
        * The anchor is not actually managed
        <?php } ?>
    </div>
        
        
        
    <div id="tab-actions">
        <form action="" method="post">
            <?php $controls->init(); ?>

            <p>To all who opened the email set the preference 
              <?php $controls->preferences_select(); ?>
              <?php $controls->button_confirm('set', 'Go', 'Proceed?'); ?>
            </p>

        </form>
    </div>
    
    </div>

</div>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">

    google.load('visualization', '1.0', {'packages':['corechart', 'geochart']});

    google.setOnLoadCallback(drawChart);

    function drawChart() {

        <?php if (!empty($urls)) { ?>
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'URL');
        data.addColumn('number', 'Count');
        <?php for ($i = 0; $i < min(count($urls), 10); $i++) { ?>
        data.addRow(['URL <?php echo $i+1; ?>', <?php echo $urls[$i]->number ?>]);
        <?php } ?>

        // Set chart options
        var options = {'title':'URLs',
            'width':400,
            'height':400};

        var chart = new google.visualization.PieChart(document.getElementById('urls-chart'));
        chart.draw(data, options);
        <?php } ?>

        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Sex');
        data.addColumn('number', 'Count');
        <?php $sex = $helper->get_sex($email_id); ?>
        data.addRow(['Male', <?php echo $sex['m']; ?>]);
        data.addRow(['Female', <?php echo $sex['f']; ?>]);
        data.addRow(['Unknown', <?php echo $sex['n']; ?>]);

        // Set chart options
        var options = {'title':'Sex',
            'width':400,
            'height':400};

        var chart = new google.visualization.PieChart(document.getElementById('sex-chart'));
        chart.draw(data, options);

        <?php if (!empty($events)) { ?>
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Events');
            data.addColumn('number', 'Day');
            <?php for ($i = 0; $i < count($events); $i++) { ?>
            data.addRow(['<?php echo $i; ?>', <?php echo $events[$i] ?>]);
            <?php } ?>

            var options = {'title': 'First events', 'width':700, 'height':400};

            var chart = new google.visualization.ColumnChart(document.getElementById('firstevents-chart'));
            chart.draw(data, options);
        <?php } ?>

    <?php if (!empty($countries)) { ?>   
        var countries = new google.visualization.DataTable();
        countries.addColumn('string', 'Country');
        countries.addColumn('number', 'Total');
        <?php foreach ($countries as &$country) { ?>
        countries.addRow(['<?php echo $country->country; ?>', <?php echo $country->total; ?>]); 
        <?php } ?>

        var options = {'title': 'Country', 'width': 700, 'height': 500};
        var chart = new google.visualization.GeoChart(document.getElementById('country-chart'));
        chart.draw(countries, options);
    <?php } ?>
}
</script>


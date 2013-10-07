<?php

class NewsletterReports extends NewsletterModule {

    static $instance;

    /**
     * @return NewsletterReports
     */
    static function instance() {
        if (self::$instance == null) {
            self::$instance = new NewsletterReports();
        }
        return self::$instance;
    }

    function __construct() {
        parent::__construct('reports', '1.0.5');
        add_filter('newsletter_statistics_view', array($this, 'hook_newsletter_statistics_view'));
        add_action('newsletter_reports_country', array(&$this, 'country'));
    }

    function upgrade() {
        global $wpdb;

        parent::upgrade();

        // TODO: Clear the event and read so changed on recurrency between version will be applied
        if (wp_get_schedule('newsletter_reports_country') === false) {
            //$this->logger->info('upgrade> Added the scheduled event');
            wp_schedule_event(time() + 60, 'newsletter', 'newsletter_reports_country');
        }
    }

    function country() {
        global $wpdb;
        $list = $wpdb->get_results("select id, ip from " . NEWSLETTER_STATS_TABLE . " where ip<>'' and country='' limit 50");

        if (!empty($list)) {

            foreach ($list as &$r) {
                $this->logger->debug('Search for: ' . $r->ip);
                $x = @json_decode(file_get_contents('http://freegeoip.net/json/' . $r->ip));
                $this->logger->debug($x);
                if (isset($x->country_code)) {
                    $this->logger->info("Found: " . $x->country_code);
                    $wpdb->query($wpdb->prepare("update " . NEWSLETTER_STATS_TABLE . " set country=%s where id=%d limit 1", $x->country_code, $r->id));
                } else {
                    $wpdb->query($wpdb->prepare("update " . NEWSLETTER_STATS_TABLE . " set country='XX' where id=%d limit 1", $r->id));
                }
            }
        }

        $list = $wpdb->get_results("select id, ip from " . NEWSLETTER_USERS_TABLE . " where ip<>'' and country='' limit 50");
        if (!empty($list)) {

            foreach ($list as &$r) {
                $this->logger->debug('Search for: ' . $r->ip);
                $x = @json_decode(file_get_contents('http://freegeoip.net/json/' . $r->ip));
                $this->logger->debug($x);
                if (isset($x->country_code)) {
                    $this->logger->info("Found: " . $x->country_code);
                    $wpdb->query($wpdb->prepare("update " . NEWSLETTER_USERS_TABLE . " set country=%s where id=%d limit 1", $x->country_code, $r->id));
                } else {
                    $wpdb->query($wpdb->prepare("update " . NEWSLETTER_USERS_TABLE . " set country='XX' where id=%d limit 1", $r->id));
                }
            }
        }
    }

    function admin_menu() {
        if ($this->available_version > $this->version) {
            $v = ' <span class="update-plugins"><span>!</span></span>';
        }
        $this->add_menu_page('index', 'Reports' . $v);
        $this->add_admin_page('view', 'Report');
    }

    function hook_newsletter_statistics_view($page) {
        return 'newsletter_reports_view';
    }

}

NewsletterReports::instance();
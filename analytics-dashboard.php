<?php
/*
Plugin Name: Analytics Dashboard
Description: Custom analytics dashboard in wp-admin with view tracking.
version 1.0
Author: Peter Joseph Isaac
*/

if(!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . 'includes/analytics-tracker.php';
require_once plugin_dir_path(__FILE__) . 'includes/analytics-admin-page.php';
require_once plugin_dir_path(__FILE__) . 'includes/analytics-rest.php';


add_action('plugins_loaded', function() {
    new AAD_Tracker();
    new AAD_Admin_Page();
    new AAD_REST();
});
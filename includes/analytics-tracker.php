<?php

class AAD_Tracker {
    public function __construct() {
        add_action('template_redirect', [$this, 'track_view']);
    }

    public function track_view() {
        if (is_single() && !current_user_can('manage_options')) {
            global $post;
            if (!$post) return;

            $post_id = $post->ID;
            $views = (int) get_post_meta($post_id, '_aad_views', true);
            update_post_meta($post_id, '_aad_views', $views + 1);

            error_log("Tracked view for post ID: $post_id (now has " . ($views + 1) . " views)");
        }
    }
}
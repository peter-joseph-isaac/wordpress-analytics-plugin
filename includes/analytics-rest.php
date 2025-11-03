<?php

class AAD_REST {
    public function __construct() {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes() {
        register_rest_route('aad/v1', '/stats', [
            'methods' => 'GET',
            'callback' => [$this, 'get_stats'],
            'permission_callback' => '__return_true',
        ]);
    }

    public function get_stats() {
        $posts = get_posts([
            'posts_per_page' => -1,
            'meta_key'       => '_aad_views',
            'orderby'        => 'meta_value_num',
            'order'          => 'DESC',
            'post_status'    => 'publish',
        ]);

        $labels = [];
        $views  = [];

        foreach ($posts as $post) {
            $count = (int) get_post_meta($post->ID, '_aad_views', true);
            if ($count > 0) {
                $labels[] = $post->post_title;
                $views[]  = $count;
            }
        }

        if (empty($labels)) {
            return ['error' => 'No data found'];
        }

        return [
            'labels' => $labels,
            'views'  => $views,
        ];
    }
}
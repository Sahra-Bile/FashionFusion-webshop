<?php
function enqueue_child_theme_styles()
{
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');

}
add_action('wp_enqueue_scripts', 'enqueue_child_theme_styles');

function create_custom_post_type()
{
    register_post_type(
        'butik',
        array(
            'labels' => array(
                'name' => __('Butiker'),
                'singular_name' => __('Butik')
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor'),
        )
    );

}
add_action('init', 'create_custom_post_type');
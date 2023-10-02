<?php
function enqueue_child_theme_styles()
{
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    // Lägg till eventuella anpassade CSS-filer eller stilar här
}
add_action('wp_enqueue_scripts', 'enqueue_child_theme_styles');
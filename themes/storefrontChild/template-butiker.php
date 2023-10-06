<?php
/*
 * Template Name: Butiker
 */
get_header();

$args = array(
    'post_type' => 'butik',
    'posts_per_page' => -1,
);

$query = new WP_Query($args);

if ($query->have_posts()):
    while ($query->have_posts()):
        $query->the_post();
        echo '<div class="butik">';
        echo '<h2>' . get_the_title() . '</h2>';
        echo '<p>Adress: ' . get_field('adress') . '</p>';
        echo '<p>Postnumret: ' . get_field('postnummer') . '</p>';
        echo '<p>Postadress: ' . get_field('postadress') . '</p>';
        echo '<p>Öppettider: ' . get_field('oppettider') . '</p>';
        echo '<p>Öppettider lördagar: ' . get_field('oppettider_lordagar') . '</p>';
        echo '<p>Öppettider söndagar: ' . get_field('oppettider_sondagar') . '</p>';
        echo '<p> Avvikande Öppettider: ' . get_field('avvikande_oppettider') . '</p>';
        echo '</div>';
    endwhile;
endif;

wp_reset_postdata();

get_footer();
?>
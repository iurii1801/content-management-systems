<?php

function usm_theme_styles() {

    wp_enqueue_style(
        'usm-style',
        get_stylesheet_uri()
    );

}

add_action('wp_enqueue_scripts', 'usm_theme_styles');

?>
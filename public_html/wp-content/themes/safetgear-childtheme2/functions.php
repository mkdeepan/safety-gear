<?php
add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );

function enqueue_parent_styles() {
   wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
}

function wpb_custom_new_menu() {
  register_nav_menu('pr-po-menu',__( 'PR PO Menu' ));
}


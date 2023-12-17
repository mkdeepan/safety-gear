<?php
add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );

function enqueue_parent_styles() {
   wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
}

// function wpb_custom_new_menu() {
//   register_nav_menu('pr-po-menu',__( 'PR PO Menu0' ));
// }

add_action('woocommerce_thankyou', 'custom_code_after_place_order');

function custom_code_after_place_order($order_id) {
    // Your custom code goes here
    echo '<p>Your custom content after placing the order</p>';
}

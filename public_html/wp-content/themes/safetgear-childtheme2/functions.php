<?php

error_reporting(E_ALL);
ini_set('display_errors', 0);

// inherit styles and functions from woostify theme.
add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );

// Add custom order status.
add_action('init', 'register_custom_order_status');
add_filter('wc_order_statuses', 'add_custom_order_status');
add_filter( 'woocommerce_checkout_fields', 'customize_checkout_fields' );


add_role('PR PO ADMIN', 'PR PO ADMIN', array('read' => true));

// Hide PR dashboard from other users except PR PO admin.
add_action('wp', 'hide_menu_based_on_user_role');

// Add entry in sg_pr_data table for PR processing.
add_action('woocommerce_thankyou', 'custom_code_after_place_order');


function enqueue_parent_styles() {
   wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
}

function wpb_custom_new_menu() {
  register_nav_menu('pr-po-menu',__( 'PR PO Menu' ));
}


function hide_menu_based_on_user_role() {
    // Check if the user is logged in
    if (is_user_logged_in()) {
        // Get the current user object
        $current_user = wp_get_current_user();

        // Check if the user has a specific role (replace 'subscriber' with the desired role)
        if (!in_array('PR PO ADMIN', $current_user->roles)) {
            // Remove the menu item by hooking into the 'wp_nav_menu_items' filter
            add_filter('wp_nav_menu_items', 'hide_menu_item_for_subscribers', 10, 2);
        }
    }
}

function hide_menu_item_for_subscribers($items, $args) {
    // Define the menu item ID or class you want to hide
    $menu_item_to_hide = 'menu-item-232'; // Replace with your actual menu item ID or class

    // Check if the menu item exists in the menu
    if (strpos($items, $menu_item_to_hide) !== false) {
        // Remove the menu item
        $items = str_replace('<li id="' . $menu_item_to_hide . '"', '<!-- Removed by custom code -->', $items);
    }

    return $items;
}


function get_product_attribute_value($product_id, $attribute_name) {
    $product = wc_get_product($product_id);

    if ($product) {
        $attributes = $product->get_attributes();
        if (isset($attributes[$attribute_name])) {
            $attribute = $attributes[$attribute_name];

            // Get the value(s) of the attribute for the product
            $attribute_values = $product->get_attribute($attribute_name);

            return $attribute_values;
        }
    }
    return false;
}

function custom_code_after_place_order($order_id) {
    // Your custom code goes here
    $order = wc_get_order($order_id);

    $status = $order -> get_status();
     if ($order && $status == "processing") {
         $order->set_status("wc-placing");
         $order->save();
         
         $order_id =  $order->get_id();
         $date_created = $order->get_date_created();
         $date_created->setTimezone(new DateTimeZone('Asia/Kolkata'));
         $customer_id = $order->get_user_id();
         $customer_name = trim($order->get_billing_first_name() . ' ' . $order->get_billing_last_name());
         $plant_code = $order->get_billing_company();
         $order_items = $order->get_items();
            
            global $wpdb;
            // Loop through order items and append product details to the output
            foreach ($order_items as $item_id => $item) {
                 if ($item->is_type('line_item')) {
                $order_item_id = $item->get_id();
                $product_id = $item->get_product_id();
                $product = wc_get_product($product_id);
                $product_qty = $item->get_quantity();
                $net_revenue = $item->get_total();
                $product_code = get_product_attribute_value($product_id,"pa_product-code");
                $product_url = $product->get_permalink();
                $vendorQuery = "select vendor_code from vendor_mapping where product_code = '".$product_code."' and plant_code = '".$plant_code."' and is_primary = 1;";
                echo $vendorQuery;
                echo "[" . date('Y-m-d H:i:s') . "] This is your custom message.";
                $results = $wpdb->get_results($vendorQuery);
                echo "[" . date('Y-m-d H:i:s') . "] This is your custom message.";
                $vendor_code = "vendor_code";
                $code = "";
                if (!empty($results)) {
                  foreach ($results as $index=>$result) {
                           $code = $result->$vendor_code;
                  }
                }

                echo "[" . date('Y-m-d H:i:s') . "] This is your custom message.";

                $currentDate = date('d.m.Y');

                $query = "insert into sg_pr_data(order_id, order_item_id, product_id, customer_id, date_created, product_qty, product_net_revenue, urgency, document_type, scope, material, plant,status, flag,product_link_name, customer_display_name,vendor_code,delivery_date) 
                values(".$order_id.",".$order_item_id.",".$product_id.",".$customer_id.",'".$date_created."',".$product_qty.",".$net_revenue.",2,'ZSPA','scope','".$product_code."','".$plant_code."',0,0,
                '".$product_url."','".$customer_name."','".$code."','".$currentDate."');";
                $sql = $wpdb->prepare($query);
                $result = $wpdb->query($sql);

                }
        }
       $order->set_status("wc-placed");
         $order->save(); 

     }
    
}

function register_custom_order_status() {
        register_post_status('wc-placing', array(
        'label'                     => _x('Placing', 'Order status', 'woocommerce'),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Custom Status <span class="count">(%s)</span>', 'Custom Status <span class="count">(%s)</span>', 'woocommerce'),
    ));
    register_post_status('wc-placed', array(
        'label'                     => _x('Placed', 'Order status', 'woocommerce'),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Custom Status <span class="count">(%s)</span>', 'Custom Status <span class="count">(%s)</span>', 'woocommerce'),
    ));
     register_post_status('wc-pr-placed', array(
        'label'                     => _x('PR Placed', 'Order status', 'woocommerce'),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Custom Status <span class="count">(%s)</span>', 'Custom Status <span class="count">(%s)</span>', 'woocommerce'),
    ));
    register_post_status('wc-pr-created', array(
        'label'                     => _x('PR Created', 'Order status', 'woocommerce'),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Custom Status <span class="count">(%s)</span>', 'Custom Status <span class="count">(%s)</span>', 'woocommerce'),
    ));
    register_post_status('wc-pr-approved', array(
        'label'                     => _x('PR Approved', 'Order status', 'woocommerce'),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Custom Status <span class="count">(%s)</span>', 'Custom Status <span class="count">(%s)</span>', 'woocommerce'),
    ));
    register_post_status('wc-po-placed', array(
        'label'                     => _x('PO Placed', 'Order status', 'woocommerce'),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Custom Status <span class="count">(%s)</span>', 'Custom Status <span class="count">(%s)</span>', 'woocommerce'),
    ));
    register_post_status('wc-po-created', array(
        'label'                     => _x('PO Created', 'Order status', 'woocommerce'),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Custom Status <span class="count">(%s)</span>', 'Custom Status <span class="count">(%s)</span>', 'woocommerce'),
    ));
    register_post_status('wc-po-approved', array(
        'label'                     => _x('PO Approved', 'Order status', 'woocommerce'),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Custom Status <span class="count">(%s)</span>', 'Custom Status <span class="count">(%s)</span>', 'woocommerce'),
    ));
}

function add_custom_order_status($order_statuses) {
    $order_statuses['wc-placing'] = _x('Placing', 'Order status', 'woocommerce');
    $order_statuses['wc-placed'] = _x('Placed', 'Order status', 'woocommerce');
    $order_statuses['wc-pr-placed'] = _x('PR Placed', 'Order status', 'woocommerce');
    $order_statuses['wc-pr-created'] = _x('PR Created', 'Order status', 'woocommerce');
    $order_statuses['wc-pr-approved'] = _x('PR Approved', 'Order status', 'woocommerce');
    $order_statuses['wc-po-placed'] = _x('PO Placed', 'Order status', 'woocommerce');
    $order_statuses['wc-po-created'] = _x('PO Created', 'Order status', 'woocommerce');
    $order_statuses['wc-po-approved'] = _x('PO Approved', 'Order status', 'woocommerce');
    return $order_statuses;
}

/*function disable_billing_address_fields() {
    if (is_checkout()) {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                // Disable billing address fields
                $('#billing_address_1, #billing_address_2, #billing_city, #billing_postcode, #billing_phone, #billing_country, #billing_email, #billing_company, #billing_first_name, #billing_last_name, #billing_state').attr('readonly', 'readonly');
            });
        </script>
        <?php
    }
}*/

function disable_billing_address_fields() {

}

function customize_checkout_fields( $fields ) {
    // Remove all default billing fields except for the company field
    unset($fields['billing']['billing_address_1']);
    unset($fields['billing']['billing_address_2']);
    unset($fields['billing']['billing_city']);
    unset($fields['billing']['billing_postcode']);
    unset($fields['billing']['billing_country']);
    unset($fields['billing']['billing_state']);

    // Rename the company field to 'Plant Code' and make it mandatory
    $fields['billing']['billing_company'] = array(
        'type'        => 'text',
        'label'       => __('Plant Code', 'woocommerce'),
        'placeholder' => _x('Enter your plant code', 'placeholder', 'woocommerce'),
        'required'    => true,
        'class'       => array('form-row-wide'),
        'clear'       => true,
    );

    $fields['billing']['billing_first_name']['required'] = false;
    $fields['billing']['billing_last_name']['required'] = false;
    $fields['billing']['billing_phone']['required'] = false;
    $fields['billing']['billing_email']['required'] = false;
    return $fields;
}


// Save the custom fields values
add_action( 'woocommerce_checkout_update_order_meta', 'save_custom_fields' );
function save_custom_fields( $order_id ) {
    if ( ! empty( $_POST['billing_first_name'] ) ) {
        update_post_meta( $order_id, '_billing_first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
    }
    if ( ! empty( $_POST['billing_last_name'] ) ) {
        update_post_meta( $order_id, '_billing_last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
    }
    if ( ! empty( $_POST['billing_phone'] ) ) {
        update_post_meta( $order_id, '_billing_phone', sanitize_text_field( $_POST['billing_phone'] ) );
    }
    if ( ! empty( $_POST['billing_email'] ) ) {
        update_post_meta( $order_id, '_billing_email', sanitize_email( $_POST['billing_email'] ) );
    }
    if ( ! empty( $_POST['billing_company'] ) ) {
        update_post_meta( $order_id, '_billing_company', sanitize_text_field( $_POST['billing_company'] ) );
    }
}

// Display the custom fields values in the order admin
add_action( 'woocommerce_admin_order_data_after_billing_address', 'display_custom_fields_in_admin', 10, 1 );
function display_custom_fields_in_admin( $order ) {
    echo '<p><strong>' . __( 'First Name', 'woocommerce' ) . ':</strong> ' . get_post_meta( $order->get_id(), '_billing_first_name', true ) . '</p>';
    echo '<p><strong>' . __( 'Last Name', 'woocommerce' ) . ':</strong> ' . get_post_meta( $order->get_id(), '_billing_last_name', true ) . '</p>';
    echo '<p><strong>' . __( 'Phone', 'woocommerce' ) . ':</strong> ' . get_post_meta( $order->get_id(), '_billing_phone', true ) . '</p>';
    echo '<p><strong>' . __( 'Email', 'woocommerce' ) . ':</strong> ' . get_post_meta( $order->get_id(), '_billing_email', true ) . '</p>';
    echo '<p><strong>' . __( 'Plant Code', 'woocommerce' ) . ':</strong> ' . get_post_meta( $order->get_id(), '_billing_company', true ) . '</p>';
}
// add_action('wp_footer', 'disable_billing_address_fields');

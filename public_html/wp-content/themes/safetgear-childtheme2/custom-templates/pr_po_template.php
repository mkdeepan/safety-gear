<?php /* Template Name: PR PO Template */ ?>

<?php
get_header();

if (function_exists('elementor_theme_do_location') && elementor_theme_do_location('single') && woostify_elementor_has_location('single')) {
    $frontend = new \Elementor\Frontend();
    echo $frontend->get_builder_content_for_display(get_the_ID(), true); // phpcs:ignore
    wp_reset_postdata();
} else {
    global $wpdb;

// Form submit action
if($_POST && $_POST['vendor_id'] && $_POST['po_submit']){
    $currentUser = wp_get_current_user()->data;
    $vendor_code = $_POST['vendor_id'][0];
    $pr_id = $_POST['pr_id'][0];
    $flag = 4;
    $table = "sg_pr_data";

    // do other updations

    $update_query = "UPDATE sg_pr_data SET po_created_by=$currentUser->ID,vendor_code='$vendor_code' WHERE pr_id=$pr_id";
    $wpdb->query($update_query);
}

// Temporary Vendor list
$vendorCode = array("Vendor 1", "Vendor 2", "3", "4");

// pagination initialize
global $wp_query;
$currentPage = max($wp_query->query['paged'], 1);
$perPage = 5;
$totalRows = 0;
$offset = ($currentPage-1)*$perPage;

// Arrays for Table columns
$selectColumns = array(
    "document_type" => "Document Type",
    "scope" => "Scope",
    "material" => "Material",
    "plant" => "Plant",
    "product_qty" => "Quantity",
    "storage" => "Storage Location",
    "product_net_revenue" => "Net Price",
    "urgency" => "Urgency",
    "purchase_group" => "Purchase Group",
    "order_id" => "Order ID"
);

// Logic to get the URL parameter to identify the option selected.
$pageParam = $_GET['view'];
$allReqColumns = $select_keys = $select_values = array();
$select_query= $count_query = $status = "";
switch ($pageParam) {
    default:
        $pageParam = "new_orders";
    case "new_orders":
        $status = "0";
        $allReqColumns = array_merge($selectColumns, array(
                "date_created" => "Order Date",
                "customer_display_name" => "Ordered By")
        );
        $select_keys = array_keys($allReqColumns);
        $select_values = array_values($allReqColumns);
        $count_query = "SELECT COUNT(*) FROM sg_pr_data WHERE flag = '0'";
        $select_query = "SELECT pr_id," . implode(",", $select_keys) . " FROM sg_pr_data WHERE flag = '0' limit $offset,$perPage";
        break;
    case "placed_pr":
        $status = "1";
        $allReqColumns = array_merge($selectColumns, array(
                "pr_submission_date" => "PR Submitted Date",
                "customer_display_name" => "Ordered By")
        );
        $select_keys = array_keys($allReqColumns);
        $select_values = array_values($allReqColumns);
        $select_query = "SELECT pr_id," . implode(",", $select_keys) . " FROM sg_pr_data WHERE flag = '1'";
        break;
    case "created_pr":
        $status = "2";
        $allReqColumns = array_merge($selectColumns, array(
                "pr_creation_date" => "PR Created Date",
                "customer_display_name" => "Ordered By")
        );
        $select_keys = array_keys($allReqColumns);
        $select_values = array_values($allReqColumns);
        $select_query = "SELECT pr_id,pr_group_id as group_id,count(pr_group_id) OVER (PARTITION BY pr_group_id) as count_r," . implode(",", $select_keys) . " FROM sg_pr_data WHERE flag = '2' order by pr_group_id";
        break;
    case "approved_pr":
        $status = "3";
        $allReqColumns = array_merge($selectColumns, array(
                "pr_creation_date" => "PR Approved Date",
                "customer_display_name" => "Ordered By",
                "approved_by" => "PR Approved By",
                "pr_number" => "PR Number"
            )
        );
        $select_keys = array_keys($allReqColumns);
        $select_values = array_values($allReqColumns);
        $select_query = "SELECT vendor_code,pr_id,pr_group_id as group_id,count(pr_group_id) OVER (PARTITION BY pr_group_id) as count_r," . implode(",", $select_keys) . " FROM sg_pr_data WHERE flag = '3' order by pr_group_id";
        break;
    case "placed_po":
        $status = "4";
        $allReqColumns = array(
            "material" => "Material",
            "plant" => "Plant",
            "order_id" => "Order ID",
            "pr_approved_date" => "PR Approved Date",
            "customer_display_name" => "Ordered By",
            "approved_by" => "PR Approved By",
            "pr_number" => "PR Number",
            "po_document_type" => "Document Type",
            "vendor_code" => "Vendor Code",
            "purchasing_org" => "Purchasing Org",
            "purchasing_group" => "Purchasing Group",
            "company_code" => "Company Code",
            "line_item" => "Line Item",
            "po_qty" => "Quantity",
            "delivery_date" => "Delivery Date",
            "gross_price" => "Gross Price",
            "fra1" => "FRA1",
            "frb1" => "FRB1",
            "tax_code" => "Tax Code",
            "po_created_by" => "PO Created By",
            "po_created_date" => "PO Created Date"
        );
        $select_keys = array_keys($allReqColumns);
        $select_values = array_values($allReqColumns);
        $select_query = "SELECT pr_id,po_group_id as group_id,count(po_group_id) OVER (PARTITION BY po_group_id) as count_r," . implode(",", $select_keys) . " FROM sg_pr_data WHERE flag = '4' order by po_group_id";
        break;
    case "created_po":
        $status = 5;
        break;
    case "approved_po":
        $status = 6;
        break;
}
?>
<script>
   function validateVendor(index){
        document.getElementById("po_submit"+index).disabled = true;
        var selectOpt = document.getElementById("vendor_id"+index);
        if(selectOpt.value != ""){
            document.getElementById("po_submit"+index).disabled = false;
        }
   }
</script>
<style>
    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    th, td {
        white-space: nowrap;
        max-width: 300px;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    thead {
        background-color: #476dbb;
        color: #fff;
    }

    table {
        table-layout: fixed;
    }

    .table-container {
        overflow-x: auto;
    }

    .pagination-margin {
        margin-top: 30px;
    }

    .pagination-margin .current {
        font-weight: 700;
        color: #000;
    }
</style>
<main id='main' class='site-main'>
    <article id='post-11' class='post-11 page type-page status-publish hentry'>
        <div class='woocommerce'>
            <nav class='woocommerce-MyAccount-navigation'>
                <ul>
                    <li class='woocommerce-MyAccount-navigation-link woocommerce-MyAccount-navigation-link--dashboard <?= $pageParam == "new_orders" ? "is-active" : "" ?>'>
                        <a href='<?= get_bloginfo('url') ?>/pr-po-dashboard?view=new_orders'>New Orders</a>
                    </li>
                    <li class='woocommerce-MyAccount-navigation-link woocommerce-MyAccount-navigation-link--orders <?= $pageParam == "placed_pr" ? "is-active" : "" ?>'>
                        <a href='<?= get_bloginfo('url') ?>/pr-po-dashboard?view=placed_pr'>PR Placed</a>
                    </li>
                    <li class='woocommerce-MyAccount-navigation-link woocommerce-MyAccount-navigation-link--dashboard <?= $pageParam == "created_pr" ? "is-active" : "" ?>'>
                        <a href='<?= get_bloginfo('url') ?>/pr-po-dashboard?view=created_pr'>PR Pending Approvals</a>
                    </li>
                    <li class='woocommerce-MyAccount-navigation-link woocommerce-MyAccount-navigation-link--dashboard <?= $pageParam == "approved_pr" ? "is-active" : "" ?>'>
                        <a href='<?= get_bloginfo('url') ?>/pr-po-dashboard?view=approved_pr'>PR Approved</a>
                    </li>
                    <li class='woocommerce-MyAccount-navigation-link woocommerce-MyAccount-navigation-link--dashboard <?= $pageParam == "placed_po" ? "is-active" : "" ?>'>
                        <a href='<?= get_bloginfo('url') ?>/pr-po-dashboard?view=placed_po'>PO Placed</a>
                    </li>
                    <li class='woocommerce-MyAccount-navigation-link woocommerce-MyAccount-navigation-link--dashboard <?= $pageParam == "created_po" ? "is-active" : "" ?>'>
                        <a href='<?= get_bloginfo('url') ?>/pr-po-dashboard?view=created_po'>PO Pending Approvals</a>
                    </li>
                    <li class='woocommerce-MyAccount-navigation-link woocommerce-MyAccount-navigation-link--dashboard <?= $pageParam == "approved_po" ? "is-active" : "" ?>'>
                        <a href='<?= get_bloginfo('url') ?>/pr-po-dashboard?view=approved_po'>PO Approved</a>
                    </li>
                </ul>
            </nav>
            <?php

            if ($select_query != "") {
                // DB Call
                if($count_query){
                    $totalRows = $wpdb->get_var($count_query); 
                }
                $results = $wpdb->get_results($select_query);

// HTML for Table containers and Headers
                $output = "";

                $output .= "<div class='woocommerce-MyAccount-content'> <div class='woocommerce-notices-wrapper table-container'>
            <table class='woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table'>
            <thead>
            <tr>";
                if (in_array($status, ["2", "3", "4", "5", "6"])) {
                    $thName = "GRP ID";
                    $output .= "<th class='woocommerce-orders-table__header woocommerce-orders-table__header-order-number'><span class='nobr'>" . $thName . "</span></th>";
                }
                foreach ($select_values as $column) {
                    $output .= "<th class='woocommerce-orders-table__header woocommerce-orders-table__header-order-number'><span class='nobr'>" . $column . "</span></th>";
                }
                if ($status == "3") {
                    // select vendor & place po
                    $output .= "<th class='woocommerce-orders-table__header woocommerce-orders-table__header-order-number'><span class='nobr'>Select Vendor</span></th>";
                    $output .= "<th class='woocommerce-orders-table__header woocommerce-orders-table__header-order-number'><span class='nobr'>Place PO</span></th>";
                }

// HTML for Table Data  
                $output .= "</tr>
            </thead>
            <tbody>";

                if (!empty($results)) {
                    // print_r($results);
                    $rowspanid = "";
                    foreach ($results as $index=>$result) {
                        $output .= "<tr class='woocommerce-orders-table__row woocommerce-orders-table__row--status-processing order'>";

                        if (in_array($status, ["2", "3", "4", "5", "6"])) {
                            if ($result->group_id == NULL) {
                                $output .= "<td class='woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number'>" . $result->group_id . "</td>";
                            } else if ($rowspanid != $result->group_id) {
                                $rowspanid = $result->group_id;
                                $output .= "<td rowspan=" . $result->count_r . " class='woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number'>" . $result->group_id . "</td>";
                            }
                        }
                        foreach ($select_keys as $key) {
                            $output .= "<td class='woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number'>" . $result->$key . "</td>";
                        }

                        if ($status == "3") {
                            // select vendor & place po
                            $output .= "<td class='woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number'>
                                <form method='post' name='po_form[]' action='' class='woocommerce-widget-layered-nav-dropdown'>
                                   <select onchange='validateVendor(".$index.")' id='vendor_id".$index."' name='vendor_id[]' class='woocommerce-widget-layered-nav-dropdown dropdown_layered_nav_color select2-hidden-accessible'>
                                   <option value>Select a vendor</option>";
                            foreach ($vendorCode as $vendor) {
                                $selected = $vendor == $result->vendor_code ? "selected": "";
                                $output .= "<option $selected value='" . $vendor . "'>" . $vendor . "</option>";
                            }

                            $output .= "</select></td>
                                <td class='woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number'>
                                    <input type='hidden' name='pr_id[]' value=".$result->pr_id." />
                                    <button type='submit' disabled class='button alt' name='po_submit[]' id='po_submit".$index."' value='Place Purchase Order' data-value='Place Purchase Order'>Place PO</button></form>
                                </td>";
                        }

                        $output .= " </tr>";
                    }

                } else {
                    $output .= "<tr> <td colspan=" . count($select_values) . ">No Data Available</td></tr>";
                }

                $output .= "</tbody></table></div>";
                echo $output;
                
                if($totalRows > $perPage) {
                    $big = 999999999;
                    echo "<div class='pagination-margin woocommerce-pagination pagination'>".paginate_links( array(
                        'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                        'current' => $currentPage,
                        'total' => ceil($totalRows/$perPage)
                    ))."</div>";
                }
                
                echo "</div></div></article><!-- #post-## --></main>";
            } else {
                echo "No Data Found!!";
            }
            }
            get_footer();
            ?>


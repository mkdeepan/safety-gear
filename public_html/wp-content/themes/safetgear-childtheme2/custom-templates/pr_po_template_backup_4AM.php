<?php /* Template Name: PR PO Template */ ?>

// TODO : Highlight the selected menu in nav panel.
// TODO : Make the table look better - trim the column content and display full value upon tool tip.

<?php

get_header();
if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'single' ) && woostify_elementor_has_location( 'single' ) ) {
	$frontend = new \Elementor\Frontend();
	echo $frontend->get_builder_content_for_display( get_the_ID(), true ); // phpcs:ignore
	wp_reset_postdata();
} else {
	?>
	
	<?php
// 	include("../../woostify/sidebar.php");

// Temporary Vendor list

$vendorCode = array("Vendor 1","Vneodr 2","3","4");

// Arrays for Table columns
$column0 = array("Document Type","Scope","Material","Plant","Quantity","Storage Location","Net Price","Urgency","Purchase Group","Order ID","Order Date","Ordered By");
$column1 = array("Document Type","Scope","Material","Plant","Quantity","Storage Location","Net Price","Urgency","Purchase Group","Order ID","PR Submitted Date","Ordered By");
$column2 = array("Document Type","Scope","Material","Plant","Quantity","Storage Location","Net Price","Urgency","Purchase Group","Order ID","PR Created Date","Ordered By");
$column3 = array("Document Type","Scope","Material","Plant","Quantity","Storage Location","Net Price","Urgency","Purchase Group","Order ID","PR Approved Date","Ordered By","PR Approved By","PR Number","Select Vendor","Place PO");
$column4 = array("Material","Plant","Order ID","PR Approved Date","Ordered By","PR Approved By","PR Number","Document Type","Vendor Code","Purchasing Org","Purchasing Group","Company Code","Line Item","Quantity","Delivery Date","Gross Price","FRA1","FRB1","Tax Code","PO Created By","PO Created Date");
$column5 = array("","");
$column6 = array("","");

// Arrays for SQL query
$select0 = "document_type,scope,material, plant, product_qty,storage,product_net_revenue ,urgency,purchase_group,order_id,date_created,customer_display_name";
$select1 = "document_type,scope,material, plant, product_qty,storage,product_net_revenue ,urgency,purchase_group,order_id,pr_submission_date,customer_display_name";
$select2 = "document_type,scope,material, plant, product_qty,storage,product_net_revenue ,urgency,purchase_group,order_id,pr_creation_date,customer_display_name";
$select3 = "document_type,scope,material, plant, product_qty,storage,product_net_revenue ,urgency,purchase_group,order_id,pr_approved_date,customer_display_name, approved_by,pr_number";
$select4 = "material, plant,order_id,pr_approved_date,customer_display_name, approved_by,pr_number,po_document_type, vendor_code, purchasing_org, purchasing_group, company_code, line_item, po_qty, delivery_date, gross_price, fra1, frb1, tax_code, po_created_by, po_created_date";
$select5 = "";
$select6 = "";

// Logic to get the URL parameter to identify the option selected.
$pageParam = $_GET['view'];
$status = 0;
$tableColumn = "";
$selectQuery = "";
switch ($pageParam) {
  case "new_orders":
    $status = 0;
    $tableColumn = $column0;
    $selectQuery = $select0;
    break;
  case "placed_pr":
    $status = 1;
    $tableColumn = $column1;
    $selectQuery = $select1;
    break;
  case "created_pr":
    $status = 2;
    $tableColumn = $column2;
    $selectQuery = $select2;
    break;
  case "approved_pr":
    $status = 3;
     $tableColumn = $column3;
    $selectQuery = $select3;
    break;
  case "placed_po":
    $status = 4;
     $tableColumn = $column4;
    $selectQuery = $select4;
    break;
  case "created_po":
    $status = 5;
     $tableColumn = $column5;
    $selectQuery = $select5;
    break;
  case "approved_po":
    $status = 6;
     $tableColumn = $column6;
    $selectQuery = $select6;
    break;
  default:
    $status = 0;
    $tableColumn = $column0;
    $selectQuery = $select0;
}

 

// HTML for navigation panel
$mainStart="<main id='main' class='site-main'>
            <article id='post-11' class='post-11 page type-page status-publish hentry'>
	        <div class='woocommerce'>";
$sidebar = "<nav class='woocommerce-MyAccount-navigation'>
			<ul>
		     	<li class='woocommerce-MyAccount-navigation-link woocommerce-MyAccount-navigation-link--dashboard'>
					<a href='https://safetygear.online/pr-po-dashboard?view=new_orders'>New Orders</a>
				</li>
			    <li class='woocommerce-MyAccount-navigation-link woocommerce-MyAccount-navigation-link--orders is-active'>
					<a href='https://safetygear.online/pr-po-dashboard?view=placed_pr'>Placed PRs</a>
				</li>
				<li class='woocommerce-MyAccount-navigation-link woocommerce-MyAccount-navigation-link--dashboard'>
					<a href='https://safetygear.online/pr-po-dashboard?view=created_pr'>PRs Pending Approvals</a>
				</li>
				<li class='woocommerce-MyAccount-navigation-link woocommerce-MyAccount-navigation-link--dashboard'>
					<a href='https://safetygear.online/pr-po-dashboard?view=approved_pr'>Approved PRs</a>
				</li>
				<li class='woocommerce-MyAccount-navigation-link woocommerce-MyAccount-navigation-link--dashboard'>
					<a href='https://safetygear.online/pr-po-dashboard?view=placed_po'>Placed POs</a>
				</li>
				<li class='woocommerce-MyAccount-navigation-link woocommerce-MyAccount-navigation-link--dashboard'>
					<a href='https://safetygear.online/pr-po-dashboard?view=created_po'>POs Pending Approvals</a>
				</li>
				<li class='woocommerce-MyAccount-navigation-link woocommerce-MyAccount-navigation-link--dashboard'>
					<a href='https://safetygear.online/pr-po-dashboard?view=approved_po'>Approved POs</a>
				</li>
			</ul>
		    </nav>";
echo $mainStart;
echo $sidebar;

// HTML for Table containers and Headers
$output = "";

$output .= "<div class='woocommerce-MyAccount-content' style='overflow-x:auto;'> <div class='woocommerce-notices-wrapper'></div>
            <table class='woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table'>
            <thead>
            <tr>";
            
            foreach($tableColumn as $column)
            {
             $output .= "<th class='woocommerce-orders-table__header woocommerce-orders-table__header-order-number'><span class='nobr'>".$column."</span></th>";
            }
         
// DB Call
global $wpdb;
$results = $wpdb->get_results(
	    "SELECT ".$selectQuery."
		FROM sg_pr_data
		WHERE flag = ".$status.";"
);

// HTML for Table Data  
$output .= "</tr>
            </thead>
            <tbody>";

            if(!empty($results))
            {
                foreach ( $results as $result ) {
                    $output .="<tr class='woocommerce-orders-table__row woocommerce-orders-table__row--status-processing order'>";
                    foreach($result as $column => $value){
                        $output .="<td class='woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number' style='white-space: nowrap;'>".$value."</td>";
                    }

                    if($status==3)
                    {
                    
                        $output .="<td class='woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number'>
                                   <select class='woocommerce-widget-layered-nav-dropdown dropdown_layered_nav_color select2-hidden-accessible'>
                                   <option value>Select a vendor</option>";
                                    foreach ( $vendorCode as $vendor ) {

                                        $output .= "<option value='".$vendor."'>".$vendor."</option>";
                                    }

                        // TODO : If a vendor is selected from the above cell, then enable the submit button and populate the hidden field with the selected value.
                        // TODO : On submitting, create a class to capture the Form's request and update the 'sg_pr_data' table with vendor code.
                        $output .= "</select></td>
                                    <td class='woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number'>
                                    <form method='post' action='https://safetygear.online/CUSTOMCLASS/' class='woocommerce-widget-layered-nav-dropdown'><input type='hidden' name='vendor' value=''>
                                    <button type='submit' class='button alt' name='Place_po' id='place_po' value='Place Purchase Order' data-value='Place Purchase Order'>Place PO</button></form>
                                    </td>";
                    }

                     $output .=" </tr>";
                }

            }
            else
            {
                 $output .= "<tr> <td colspan=".count($tableColumn).">No Data Available</td></tr>";
            }
            
            $output .= "</tbody>
            </table>
            </div>
            </div>
            </article><!-- #post-## -->
			</main>";
            
            echo $output;
}
         get_footer();   
            ?>
            

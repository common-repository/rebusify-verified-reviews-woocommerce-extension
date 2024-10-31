<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

 
  
global $wpdb;
$RVRW_USER_REVIEW_REMINDER=$wpdb->prefix."rvrw_user_review_reminder";



$paged = isset($_REQUEST['paged']) ? $_REQUEST['paged'] : 1;
$url=get_site_url().'/wp-admin/admin.php?page=rvrw-admin-sub-page2&pid=invite';
$limit=20;
$orderBy= isset($_REQUEST['orderby']) ? $_REQUEST['orderby'] : "date";
$order= isset($_REQUEST['order']) ? $_REQUEST['order'] : "desc";
if($order=="desc")
{
    $changeOrder="asc";
}
else {
    $changeOrder="desc";
}

$args = array(
    'status' => 'completed',
    'limit' => $limit,
    'paged' => $paged,
     'orderby' => $orderBy,
    'order' => $order,
     
);
$orders = wc_get_orders( $args );


$args = array(
    'status' => 'completed',
    'limit' => -1,
    'paged' => 1,
     'orderby' => 'date',
    'order' => 'DESC',
     
);
$totalOrders = wc_get_orders( $args );

$total_records = count($totalOrders);

$posts_per_page = $limit;
$total_pages = ceil($total_records / $posts_per_page);

$action= isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
$orderId= isset($_REQUEST['orderId']) ? $_REQUEST['orderId'] : "";

  

if( $action=="send" && $orderId!="")
{
    $msg=sendReminder($orderId);
    
}

if(isset($_REQUEST['btn_bulk_send']))
{
    $order_ids=$_REQUEST['chk_order'];
    for($i=0;$i<sizeof($order_ids);$i++)
    {
      
     $msg=sendReminder($order_ids[$i]);   
    }
}

if(isset($_REQUEST['btn_reset']))
{ 
    $order_ids=$_REQUEST['chk_order'];
    for($i=0;$i<sizeof($order_ids);$i++)
    {
      
     $msg=resetReminder($order_ids[$i]);   
    }
}

?>
<style>
     
.rvrwbtnsend{

	display: -webkit-inline-box;
	display: inline-flex;
	line-height: 2.0em;
	border-radius: 4px;
	border-bottom: 1px solid rgba(0,0,0,.05);
	margin: -.25em 0;
	white-space: nowrap;
	max-width: 100%;
        background: #c8d7e1;
        color: #2e4453;
        padding: 2px 10px 2px 10px;
        cursor: pointer;
}
 
.rvrwpreview{
        float: right;
    width: 16px;
    padding: 20px 4px 4px 4px;
    height: 0;
    overflow: hidden;
    position: relative;
    border: 2px solid transparent;
    border-radius: 4px;

}
.rvrwpreview::before{
    
	font-family: WooCommerce;
	speak: none;
	font-weight: 400;
	font-variant: normal;
	text-transform: none;
	line-height: 1;
	margin: 0;
	text-indent: 0;
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	text-align: center;
	content: "";
	line-height: 16px;
	font-size: 14px;
	vertical-align: middle;
	top: 4px;

}
</style>
<div class="rvrow fulldiv">

    <div class="rvrow-inner">
        <?php if(isset($msg)) echo '<div style="color:red">'.$msg.'</div>';?>
       <form action="" method="POST">
        <table style="width:100%;" class="wp-list-table widefat fixed striped posts">
            <thead>
            <tr>
                
                <td id="cb" style="width:10%" class="manage-column column-cb check-column">
                    <input type="checkbox"  value=""/>
                </td>
                <th scope="col" id="order_number" class="manage-column column-order_number column-primary sortable desc" style="width:50%" >
                    <a href="<?php echo $url.'&orderby=ID&amp;order='.$changeOrder?>">
                        <span>Order</span><span class="sorting-indicator"></span>
                    </a>
                </th>
               
                <th scope="col" class="manage-column column-order_date sorted asc" id="order_date" style="width:20%" >
                    <a href="<?php echo $url.'&orderby=date&amp;order='.$changeOrder?>">
                        <span> Date</span><span class="sorting-indicator"></span>
                    </a>
                </th>
                <th style="width:10%" >
                    Status
                </th>
            <th style="width:10%" ></th></tr>
            </thead>
            <tbody>
                  <?php
                foreach( $orders as $order ){
                    $order_id=$order->get_id();
                            
                    $mail_sent=$wpdb->get_row("select count(*) as review_sent,mail_sent from $RVRW_USER_REVIEW_REMINDER "
                          . " where order_id=$order_id and mail_sent='YES'");
                   
                    
                    ?>
                <tr> 
                    <th style="width:10%" class="check-column">
                     <input type="checkbox" name="chk_order[]" value="<?php echo $order->get_id();?>"/>
                    </th>
                        <?php
                echo '<td style="width:50%">'
                        . '<a href="'.get_site_url().'/wp-admin/post.php?post='.$order->get_id().'&amp;action=edit" class="rvrwpreview" data-order-id="'.$order->get_id().'" title="Preview">Preview<div></div></a>'.
                        '<a href="'.get_site_url().'/wp-admin/post.php?post='.$order->get_id().'&amp;action=edit" class="order-view"><strong>'.
                '#'.$order->get_id().' '.$order->get_billing_first_name().' '.$order->get_billing_last_name() .
              '</strong></a></td>'; // The order ID
                echo '<td style="width:20%">'.date('F d, Y',strtotime($order->get_date_created())) . '</td>'; // The order status
               
                if($mail_sent->review_sent==0) {
                     echo '<td style="width:10%">';
                echo  '<a onclick="rvrwSendEmail('.$order_id.')"  class="rvrwbtnsend">Send</a></td><td style="width:10%">&nbsp;</td>';
                }
                else
                {
                     echo '<td style="width:10%">';
                    echo 'Sent</td><td style="width:10%"><a href="#" onclick="rvrwSendEmail('.$order_id.')" >Resend</a></td>';
                }
                echo '</tr>';
            }
                ?>
            </tbody>
           <tfoot>
	<tr>
		<td class="manage-column column-cb check-column">
                    <label class="screen-reader-text" for="cb-select-all-2">Select All</label>
                    <input id="cb-select-all-2" type="checkbox"></td>
                <th scope="col" class="manage-column column-order_number column-primary sortable desc">
                    <a href="<?php echo $url.'&orderby=ID&amp;order='.$changeOrder?>">
                        <span>Order</span>
                    <span class="sorting-indicator"></span>
                    </a></th>
                        <th scope="col" class="manage-column column-order_date sortable desc">
                             <a  href="<?php echo $url.'&orderby=date&amp;order='.$changeOrder?>">
                                <span>Date</span>
                            <span class="sorting-indicator"></span></a></th>
                       <th scope="col" class="manage-column column-order_status">Status</th>
                       <th scope="col" class="manage-column"></th>
                                
	</tfoot>
            
        </table>
        
         
    <?php 
    $nextpage=$paged+1;
    $prevPage = $paged - 1;
  /*  $args = array(
        'base' => '%_%',
        'format' => '?paged=%#%',
        'total' => $total_pages,
        'current' => $paged,
        'show_all' => False,
        'end_size' => 5,
        'mid_size' => 5,
        'prev_next' => True,
        'prev_text' => __('&laquo; Previous'),
        'next_text' => __('Next &raquo;'),
        'type' => 'plain',
        'add_args' => False,
        'add_fragment' => ''
    );
   /*echo paginate_links($args);
   
        }*/
    ?>
    
        <div class="tablenav bottom">
            <div class="alignleft actions bulkactions " >
                <input class="button button-primary" type="submit" name="btn_bulk_send" value="Bulk Send"/>
                <input class="button button-primary" type="submit" name="btn_reset" value="Reset to Unsent"/>
               
            </div>
        <div class="tablenav-pages">
            <span class="displaying-num"><?php echo $total_records;?> items</span>
            <span class="pagination-links">
                   <?php if($paged!=1) { ?>
                       
                  <a class="first-page button button" href="<?php echo $url ?>&paged=1">
                      «</a>
                    <?php }
                    else { ?>
                    <span class="tablenav-pages-navspan button disable" aria-hidden="true">
                        «</span><?php } ?> 
            
                <?php if($prevPage){ ?>
                
                    <a class="prev-page button" href="<?php echo $url.'&paged='. $prevPage?>">‹</a>
                
                    <?php }
                    else { ?>
                <span class="tablenav-pages-navspan button disable">‹</span> <?php } ?>
                
            <span class="screen-reader-text">Current Page</span><span id="table-paging" class="paging-input">
                <span class="tablenav-paging-text"><?php echo $paged ;?> of 
                    <span class="total-pages"><?php echo $total_pages;?></span></span></span>
            <?php if($total_pages==$paged) { ?>
                 <span class="tablenav-pages-navspan button disable" aria-hidden="true">
                        ›</span>
            <?php } else { ?>
                    <a class="next-page button" href="<?php echo $url.'&paged='. $nextpage?>">
                        <span class="screen-reader-text">Next page</span><span aria-hidden="true">›</span>
                    </a>
            <?php } ?>
                <?php if($total_pages==$paged) { ?>
                 <span class="tablenav-pages-navspan button disable" aria-hidden="true">
                        »</span>
            <?php } else { ?>
                <a class="last-page button" href="<?php echo $url.'&paged='. $total_pages;?>"><span class="screen-reader-text">Last page</span>
                <span aria-hidden="true">»</span></a>
            <?php } ?>        
            
            </span>
        </div>
       
           
</div> </form>
         <form id="frmsend" action="" method="POST">
                <input type="hidden" value="" name="orderId" id="orderId"/>
                <input type="hidden" value="send" name="action" />
            </form>
        </div>
</div>
</div>
</div>
<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$product_type='F';
$rbs_enable_scamaylyze=0;
$rbs_block_checkout=0;

if (rvrw_exist_option('rbs_product_type')) {
 $product_type=get_option('rbs_product_type');

}

global $wpdb;
$RVRW_BLOCKED_USER=$wpdb->prefix."rvrw_blocked_user";
$paged = isset($_REQUEST['paged']) ? $_REQUEST['paged'] : 1;
$url=get_site_url().'/wp-admin/admin.php?page=rvrw-admin-sub-page3';
$limit=20;

if(isset($_REQUEST['btn_save']))
{
    $enable_scamalyze= isset($_REQUEST['chk_enable_scamalyze']) ? $_REQUEST['chk_enable_scamalyze'] : 0;
    $block_chekout= isset($_REQUEST['chk_block_chekout']) ? $_REQUEST['chk_block_chekout'] : 0;
    
      if (rvrw_exist_option('rbs_enable_scamaylyze')) {
        update_option('rbs_enable_scamaylyze', $enable_scamalyze);
    } else {
        add_option('rbs_enable_scamaylyze', $enable_scamalyze);
    }

    if (rvrw_exist_option('rbs_block_checkout')) {
        update_option('rbs_block_checkout', $block_chekout);
    } else {
        add_option('rbs_block_checkout', $block_chekout);
    }
    $message="Setting saved successfully!";
}

if(isset($_REQUEST['btnunblock']))
{
    $ids= isset($_REQUEST['chk_block']) ? $_REQUEST['chk_block'] : 0;
    for($i=0;$i<sizeof($ids);$i++)
    { 
        $id=$ids[$i];
        $data= array('block'=>'NO');
        $where =array('id'=>$id);
        $data_formate=array('%s');
        $where_formate=array('%d');

        $wpdb->update($RVRW_BLOCKED_USER,$data,$where,$data_formate,$where_formate);
        
    }
     $message="IP Address(s) unblocked successfully!";
}

if (rvrw_exist_option('rbs_enable_scamaylyze')) {
 $rbs_enable_scamaylyze=get_option('rbs_enable_scamaylyze');

}
if (rvrw_exist_option('rbs_block_checkout')) {
 $rbs_block_checkout=get_option('rbs_block_checkout');

}

$total_records=$wpdb->get_var("select count(*)"
               . " from $RVRW_BLOCKED_USER where block='YES' ");

 
 
$indexN = ($paged - 1) * $limit;
$total_pages = ceil($total_records / $limit);
$nextpage=$paged+1;
$prevPage = $paged - 1;

$allBlockedUsers=$wpdb->get_results("select date,id,INET_NTOA(ip_address) as ip"
               . " from $RVRW_BLOCKED_USER where block='YES' order by date DESC limit $indexN, $limit");
       
?>
<div class="wrap woocommerce">
    <h2>Trustalyze</h2>

    <div id="store_address-description">
<?php if (isset($message)) echo '<p style="color:red">' . $message . '</p>'; ?> 
       
        <p>
            Protect your site from scammers with Scamalyze from Trustalyze. When activated all users are scanned through the Scamalyze
database to ensure they are not known scammers.<br />If a scammer is detected the user is blocked at the IP level before a purchase is made thus saving you
from costly chargebacks.
        </p>
</div>
                <?php if($product_type=="F") { ?>
    

            <!--<form action="https://reviews.trustalyze.com/plans/" method="post" target="_blank" >
                            <input type="submit" value="Upgrade Your Plan" class="button button-primary"/>
                        </form>-->
    
<?php } 
//else 
{
?>
    <div>
        <form action="" method="POST">
        <table>
             <tr>
                 <td colspan="2">&nbsp;</td>
             </tr>
            <tr>
                <td>Enable/Disable</td>
                <td><input type="checkbox" <?php if($rbs_enable_scamaylyze) echo "checked";?> name="chk_enable_scamalyze" value="1" />&nbsp; Enable Scamalyze</td>
            </tr>
            <tr>
                <td>Block Checkout</td>
                <td><input type="checkbox" <?php if($rbs_block_checkout) echo "checked";?> name="chk_block_chekout" value="1" />&nbsp; Block User Before Checkout</td>
            </tr>
             <tr>
                 <td colspan="2">&nbsp;</td>
             </tr>
              <tr>
                 <td colspan="2">&nbsp;</td>
             </tr>
             <tr>
                 <td colspan="2"><b>Note:</b><br>When a user is blocked they are blocked at the IP level. That IP address will no longer be allowed to access your site. To unblock a IP please use the "Unblock Tool" below.
</td>
             </tr>
              <tr>
                 <td colspan="2">&nbsp;</td>
             </tr>
              <tr>
                  <td colspan="2"><input type="submit" name="btn_save" value="Save Changes" class="button button-primary"/></td>
             </tr>
             
        </table>
            </form>
    </div>
           
            
             <?php if(sizeof($allBlockedUsers) > 0) { ?>
            <div class="rvrwrgsdv"><b>Blocked Users</b></div>
            <form action="" method="POST">
            <div class="rvrwrgsdv">
               
        <table style="width:100%;" class="wp-list-table widefat fixed striped posts">
            <thead>
            <tr>
                
                <td id="cb" style="width:10%" class="manage-column column-cb check-column">
                    <input type="checkbox"  value=""/>
                </td>
                <th style="width:40%;" >
                    Date Added
                </th>
               
                <th style="width:50%"  >
                    IP Address
                </th>
           </tr>
            </thead>
            <tbody>
                <?php
                foreach($allBlockedUsers as $user)
                {  ?>
                <tr> 
                    <th style="width:10%" class="check-column">
                     <input type="checkbox" name="chk_block[]" value="<?php echo $user->id;?>"/>
                    </th>
                        <?php
                echo '<td style="width:40%;">'.date('F d, Y', strtotime($user->date)).'</td>';
                echo '<td style="width:50%;">'.$user->ip.'</td>';
                ?></tr>
                    <?php 
                    
                }
                ?>
                
                    </tbody>
          </table>
                    
            </div>
                
            <div class="rvrwrgsdv40">
                <input type="submit" name="btnunblock" value="Unblock" class="button button-primary"/>
            </div>
            <div class="tablenav-pages rvrwrgsdv60right">
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
             </form> 
            
<?php }
  
}
?>
</div>
<?php
if (isset($_REQUEST['pid'])) {
    $pageURL = $_REQUEST['pid'];
} else {
    $pageURL = "";
}
$pf_root_link = get_site_url() . "/wp-admin/admin.php?page=rvrw-admin-sub-page2";
?>
<style>
       .rvrwmenu {
    border-bottom: 1px solid #ccc;
    height: 28px;
    width: 782px;
    margin-left: 0px !important;
}
.rvrwmenu li.active {
    background-color: #F1F1F1;
    border-bottom: 1px solid #f1f1f1;
    color: #000;
}

.rvrwmenu li {
    display: inline;
    background-color: #e5e5e5;
    padding: 10px;
    color: #555;
    border: 1px solid #ccc;
        border-bottom-color: rgb(204, 204, 204);
        border-bottom-style: solid;
        border-bottom-width: 1px;
    border-bottom: 0px;
    margin-right: 5px;
    font-size: 13px;
    cursor: pointer;
}
.rvrwmenu a{
    text-decoration: none;
}
</style>
<div class="wrap">
    <div id="poststuff">
        <div id="post-body">

            <div>
                <ul  class="rvrwmenu">
                    <a  href="<?php echo $pf_root_link . '&pid=invite'; ?>" title="<?php echo 'invite'; ?>">

                        <li <?php if (($pageURL == "invite") || ($pageURL == "" ) || $pageURL == "profile") { ?>class="active" <?php } ?> >
                            Invite
                        </li> 
                    </a>
                     <a  href="<?php echo $pf_root_link . '&pid=etemplate'; ?>" title="<?php echo 'etemplate'; ?>">

                        <li <?php if (($pageURL == "etemplate")) { ?>class="active" <?php } ?> >
                            Email Template
                        </li> 
                    </a>
                     


                </ul>
            </div>


<?php
if ($pageURL == "invite") {
  
    include_once( RVRW_ABSPATH . '/includes/rvrw_invitations.php');
} elseif ($pageURL == "etemplate") {
    include_once( RVRW_ABSPATH . '/includes/rvrw_email_template.php');
}  
else {
    include_once( RVRW_ABSPATH . '/includes/rvrw_invitations.php');
}
?>
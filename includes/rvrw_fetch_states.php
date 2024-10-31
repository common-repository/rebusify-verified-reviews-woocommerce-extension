<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


header('Access-Control-Allow-Origin: *');

include "../../../../wp-config.php";

/*error_reporting(0);
error_reporting(E_ALL);
ini_set('display_errors', 'On');
*/

global $woocommerce;
    $countries_obj   = new WC_Countries();
    $countries   = $countries_obj->__get('countries');
    $default_country = $countries_obj->get_base_country();
    $default_country_id= isset($_REQUEST['cId']) ? $_REQUEST['cId'] : $default_country;
    $default_county_states = $countries_obj->get_states( $default_country_id );



   $filed= woocommerce_form_field('rvrw_state', array(
    'type'       => 'select',
    'class'      => array( 'chzn-drop' ),
    'label'      => __('State / County'),
    'placeholder'    => __('Enter something'),
    'options'    => $default_county_states,
    'required'   =>true,
    'name'       =>"dd_state"
    )
    );
    echo $filed;
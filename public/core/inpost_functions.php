<?php

function get_statuses_list(){
        
    $url = 'https://api-shipx-pl.easypack24.net/v1/statuses';
    
    $args = array(
        'method' => 'GET',
    );
    
    $data = wp_remote_post($url, $args);
    
    if ( is_wp_error( $data ) ) {
        $error_message = $data->get_error_message();
        return 'Something went wrong:' . $error_message;
    } else {
       return json_decode($data['body'], true)['items'];
    }
        
        
}


function get_status_from_tracking($status, $statuses_list){
       
    foreach ($statuses_list as $status_from_list){
        if($status_from_list["name"] == $status){
            return $status_from_list["title"];
        }
    }
    
    return '';
}

function get_dispatch_orders($inpost_token) {      
        
        $url = 'https://api-shipx-pl.easypack24.net/v1/organizations/32023/dispatch_orders';
        
        $args = array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $inpost_token,
                'Content-Type' => 'application/json',
            ),
            'method' => 'GET',
        );
        
        $data = wp_remote_post($url, $args);
        
        if ( is_wp_error( $data ) ) {
            $error_message = $data->get_error_message();
            return 'Something went wrong:' . $error_message;
        } else {        
            return json_decode($data['body'], true);
        }
       
        die();
}

function get_tracking_number_from_id($package_id, $inpost_token){
    
    $url = 'https://api-shipx-pl.easypack24.net/v1/organizations/32023/shipments/?id=' . $package_id;
        
    $args = array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $inpost_token,
            'Content-Type' => 'application/json',
        ),
        'method' => 'GET',
    );
    
    $data = wp_remote_post($url, $args);
    
    if(!is_wp_error($data)){
        return json_decode($data['body'], true)['items']['0']['tracking_number'];
    }
    else
        return '';
   
    die();
}

function inpost_tracking($tracking_number, $inpost_token) {      
        
    $url = 'https://api-shipx-pl.easypack24.net/v1/tracking/' . $tracking_number;
    
    $args = array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $inpost_token,
            'Content-Type' => 'application/json',
        ),
        'method' => 'GET',
    );
    
    $data = wp_remote_post($url, $args);
    
    
    if ( is_wp_error( $data ) ) {
        return 'Something went wrong';
    } else {
       return json_decode($data['body'], true)['status'];
    }
    
    die();
}

function get_dispatch_points($inpost_token){
        
    $url = 'https://api-shipx-pl.easypack24.net/v1/organizations/32023/dispatch_points';
    
    $args = array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $inpost_token,
            'Content-Type' => 'application/json',
        ),
        'method' => 'GET',
    );
    
    $data = wp_remote_post($url, $args);
    
    if ( is_wp_error( $data ) ) {
        $error_message = $data->get_error_message();
        return 'Something went wrong:' . $error_message;
    } else {
       return json_decode($data['body'], true)['items'];
    }
        
        
}
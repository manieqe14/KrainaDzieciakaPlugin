<?php

function produkty_API_init(){
    
    add_action( 'wp_ajax_save_product_order', 'save_product_order' );
    add_action( 'wp_ajax_nopriv_save_product_order', 'save_product_order' );

    function save_product_order(){
        $product = wc_get_product($_REQUEST['product_id']);
        $menu_order = wc_get_product($_REQUEST['menu_order']);
        
        $product->save_menu_order($menu_order);
        
        echo 'OK';

        die();
        
    }

    //get product data
    add_action( 'wp_ajax_get_product_data', 'get_product_data' );
    add_action( 'wp_ajax_nopriv_get_product_data', 'get_product_data' );

    function get_product_data(){
        $product = wc_get_product($_REQUEST['product_id']);
        
        echo json_encode($product -> get_data(), true);

        die();
        
    }
    
    add_action('rest_api_init', function(){
            register_rest_route('allegro_aukcje/v1','code', array(
                'methods' => 'GET',
                'callback' => 'get_allegro_code',
                'permission_callback' => '__return_true'
            )); 
    });
}
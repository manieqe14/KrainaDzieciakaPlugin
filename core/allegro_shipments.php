<?php 
require_once ("variables.php");

add_action('init','allegro_shipments_init');

function allegro_shipments_init(){
    
    add_action( 'wp_ajax_get_allegro_token', 'get_allegro_token' );
    add_action( 'wp_ajax_nopriv_get_allegro_token', 'get_allegro_token' );

    function get_allegro_token() {
              
        $code = $_REQUEST['code'];
        $uri = variables::allegro_address() . '/auth/oauth/token?grant_type=authorization_code&code=' . $code .'&redirect_uri=' . variables::$website . '/wp-admin/admin-ajax.php?action=get_allegro_token';
        
        $response = wp_remote_post( $uri, array(
            'method'      => 'POST',
            'headers'     => array(
                    'Authorization' => 'Basic ' . base64_encode(variables::$client_id . ':' . variables::$client_secret),
                ),
            ),
        );
        $allegro_token = ((array)(json_decode($response['body'])))['access_token'];
        $refresh_token = ((array)(json_decode($response['body'])))['refresh_token'];
        
        error_log($response['body']);
        
                
        if(!variables::option_exists('allegro_token')){
            add_option('allegro_token', $allegro_token);
        }
        else{
            update_option('allegro_token', $allegro_token);
        }
        
        if(!variables::option_exists('refresh_token')){
            add_option('refresh_token', $refresh_token);
        }
        else{
            update_option('refresh_token', $refresh_token);
        }
        
        
            
        die();
    }
    
    //temp
    function check_package($uuid){
        $url = variables::$api_allegro . '/parcel-management/parcel-create-commands/' . $uuid;
        
        $response = wp_remote_request( $url, array(
            'method' => 'GET',
            'headers'     => array(
                    'Authorization' => 'Bearer ' . get_option('allegro_token'),
                    'Accept' => 'application/vnd.allegro.public.v1+json',
                ),
            ),
        );
        
        if(!is_wp_error($response)){
            return ((array) json_decode($response['body'], true));
        }
        
        else return wp_remote_retrieve_response_code( $response );
        
        
    }
    
    function get_package_info($parcelId){
        $url = variables::$api_allegro . '/parcel-management/parcels/' . $parcelId;
        
        $response = wp_remote_request( $url, array(
            'method' => 'GET',
            'headers'     => array(
                    'Authorization' => 'Bearer ' . get_option('allegro_token'),
                    'Accept' => 'application/vnd.allegro.public.v1+json',
                ),
            ),
        );
        
        if(!is_wp_error($response)){
            return ((array) json_decode($response['body'], true));
        }
        
        else return wp_remote_retrieve_response_code( $response );
        
    }
    
    add_action( 'wp_ajax_get_pickup_date_proposals', 'get_pickup_date_proposals' );
    add_action( 'wp_ajax_nopriv_get_pickup_date_proposals', 'get_pickup_date_proposals' );
    
    function get_pickup_date_proposals(){
        
        $url = variables::$api_allegro . '/parcel-management/pickup-date-proposals?parcelId=' . $_REQUEST['shipment'];
        
        $response = wp_remote_request( $url, array(
            'headers'     => array(
                    'Authorization' => 'Bearer ' . get_option('allegro_token'),
                    'Accept' => 'application/vnd.allegro.public.v1+json',
                ),
            ),
        );
        echo json_encode((json_decode($response['body'], true)));
    }
    
    add_action( 'wp_ajax_parcel_pickup_request', 'parcel_pickup_request' );
    add_action( 'wp_ajax_nopriv_parcel_pickup_request', 'parcel_pickup_request' );
    
    function parcel_pickup_request(){
        $pickup = json_decode(stripslashes($_REQUEST['pickup_date']), true);
        $parcelId = (array) $_REQUEST['shipment'];
        $orderId = $_REQUEST['order_id'];
        
        $url = variables::$api_allegro . '/parcel-management/parcel-pickup-request-commands/' . guidv4();
        
        $request_body = array(
            'parcelIds' => $parcelId,
            'pickupDate' => $pickup,
        );
        
        
        $response = wp_remote_request( $url, array(
            'method' => 'PUT',
            'body' => json_encode($request_body),
            'headers'     => array(
                    'Authorization' => 'Bearer ' . get_option('allegro_token'),
                    'Accept' => 'application/vnd.allegro.public.v1+json',
                    'Content-Type' => 'application/vnd.allegro.public.v1+json',
                ),
            ),
        );
        
        $order = wc_get_order($orderId);
       
        $order->update_meta_data( 'dpd_dispatch', $pickup['date'] . ', ' . $pickup['minTime'] . ' - ' . $pickup['maxTime'] ); 
        $order->save_meta_data();
       
        
    }
    
    function check_parcel_pickup($commandId){
        
        $url = variables::$api_allegro . '/parcel-pickup-request-commands/' . $commandId;
        echo 'Command URL: ' . $url;

        $response = wp_remote_request( $url, array(
            'method' => 'GET',
            'headers'     => array(
                    'Authorization' => 'Bearer ' . get_option('allegro_token'),
                    'Accept' => 'application/vnd.allegro.public.v1+json',
                ),
            ),
        );
        
        var_dump($response['body']);
        
    }
    
    add_action( 'wp_ajax_order_dpd_shipment', 'order_dpd_shipment' );
    add_action( 'wp_ajax_nopriv_order_dpd_shipment', 'order_dpd_shipment' );
    
    function order_dpd_shipment(){
        
        $order = wc_get_order($_REQUEST['order-id']);
        $pickup = array();
        
        $dispatch_by_point = $_REQUEST['dispatch-by-point'];
        if($dispatch_by_point == 'true'){
            $pickup = array(
                'address' => array(
                    'street' => ' Bieżanowska 278 ',
                    'postCode' => ' 30856',
                    'city' => 'Kraków',
                    'countryCode' => 'PL',
                ),
                'name' => 'Strefa Animatora',
                'phone' => '665002690',
                'pointId' => 'PL16759',	
            );
        }
        
        else{
            $pickup = array(
                'address' => array(
                    'street' => 'Mała Góra 8A/7',
                    'postCode' => '30-854',
                    'city' => 'Kraków',
                    'countryCode' => 'PL',
                ),
                'email' => 'sklep@krainadzieciaka.pl',
                'name' => 'Mariusz Pacyga',
                'company' => 'Kraina Dzieciaka',
                'phone' => '+48505947675',
            );
        }
        
        $package = array(
            'serviceId'=> variables::get_sending_method_id('Allegro DPD'),
            'receiver' => array(
                'address' => array(
                    'street' => $order->get_shipping_address_1() . ' ' . $order->get_shipping_address_2(),
                    'postCode' => $order->get_shipping_postcode(),
                    'city' => $order->get_shipping_city(),
                    'countryCode' => 'PL',
                ),
                'email' => $order->get_billing_email(),
                'name' => $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name(),
                'phone' => $order->get_billing_phone(),
            ),
            'pickup' => $pickup,            
            'items' => array(
                array(
                    'weight' => array(
                        'value' => 1,
                        'unit' => 'KILOGRAM',
                    ),
                    'dimensions' => array(
                        'height' => array(
                            'value' => 7,
                            'unit' => 'CENTIMETER',
                        ),
                        'width' => array(
                            'value' => 30,
                            'unit' => 'CENTIMETER',
                        ),
                        'depth' => array(
                            'value' => 10,
                            'unit' => 'CENTIMETER',
                        ),
                    ),
                    'type' => 'PACKAGE',
                    'description' => 'Ubranka dziecięce',
                    'value' => array(
                        'amount' => $order->get_total(),
                        'currency' => 'PLN',
                    ),
                ),
            ),
            'label' => array(
                'sender' => array(
                    'address' => array(
                        'street' => 'Mała Góra 8A/7',
                        'postCode' => '30-864',
                        'city' => 'Kraków',
                        'countryCode' => 'PL',
                        
                    ),
                    'email' => 'sklep@krainadzieciaka.pl',
                    'name' => 'Mariusz Pacyga',
                    'company' => 'Kraina Dzieciaka',
                    'phone' => '+48505947675',
                ),
                'fileFormat' => 'PDF',
                'referenceNumber' => $order->get_id(),
            ),
        );
        
        $uuid = guidv4();
        
        $url = variables::$api_allegro . '/parcel-management/parcel-create-commands/' . $uuid;
        $response = wp_remote_request( $url, array(
            'method' => 'PUT',
            'body' => json_encode($package),
            'headers'     => array(
                    'Authorization' => 'Bearer ' . get_option('allegro_token'),
                    'Accept' => 'application/vnd.allegro.public.v1+json',
                    'Content-Type' => 'application/vnd.allegro.public.v1+json',
                ),
            ),
        );
        
        
        
        if(!is_wp_error($response)){
             sleep(3);
            $package_data = check_package($uuid);
            $package_info = get_package_info($package_data['parcelId']);
            
            $order->update_meta_data( 'dpd_uuid', $uuid );
            $order->update_meta_data( 'package_id',  $package_data['parcelId']);
            $order->update_meta_data('dpd_waybill_number', $package_info['items'][0]['waybill']); 
            $order->save_meta_data();
        }
        
        echo wp_remote_retrieve_body($response); 
       
    }
    
    add_action( 'wp_ajax_get_dpd_label', 'get_dpd_label');
    add_action( 'wp_ajax_nopriv_get_dpd_label', 'get_dpd_label');
    
    function get_dpd_label(){
        
       
        $url = variables::$api_allegro . '/parcel-management/parcels/label?parcelId=' .  $_REQUEST['package-id'] . '&pageFormat=A6';
        $response = wp_remote_get($url, array(
            'method' => 'GET',
            'headers'     => array(
                    'Authorization' => 'Bearer ' . get_option('allegro_token'),
                ),
            ),
        );
        if(is_wp_error($response)){
            echo 'Something goes wrong';
        }
        else{
            $response_body = wp_remote_retrieve_body($response);
            header("Content-type: application/pdf");
            header("Content-disposition: attachment;filename=downloaded.pdf");
            echo $response_body;
        }
        
    }
    
    function guidv4($data = null) {
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);

        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    
}

add_action( 'wp_ajax_add_invoice_allegro', 'add_invoice_allegro');
add_action( 'wp_ajax_nopriv_add_invoice_allegro', 'add_invoice_allegro');

function add_invoice_allegro(){
    $order = wc_get_order($_REQUEST['order_id']);
    $url = variables::$api_allegro . '/order/checkout-forms/' . $order->get_meta('_allegro_order_id') . '/invoices';
    $body = array(
        'file' => array(
            'name' => 'faktura.pdf',
        ),
        'invoice_number' => $order->get_meta('_invoice_number'),
    );

    $response = wp_remote_request( $url, array(
        'method' => 'POST',
        'body' => json_encode($body),
        'headers'     => array(
                'Authorization' => 'Bearer ' . get_option('allegro_token'),
                'Accept' => 'application/vnd.allegro.public.v1+json',
                'Content-Type' => 'application/vnd.allegro.public.v1+json',
            ),
        ),
    );
    
    if (is_wp_error( $response )){
        echo 'Error';
    }
    else{
        $invoice_id = json_decode($response['body'], true)['id'];
        $url = $url . '/' . $invoice_id . '/file';
        
        $uploads = wp_upload_dir();
        $order->get_meta('_allegro_order_id');
        $filename = $uploads["basedir"] . '/invoices/' . $order->get_meta('_invoice_id') . '.pdf';
        
        $file = file_get_contents($filename);
        
        $response2 = wp_remote_request( $url, array(
            'method' => 'PUT',
            'body' => $file,
            'headers'     => array(
                    'Authorization' => 'Bearer ' . get_option('allegro_token'),
                    'Accept' => 'application/vnd.allegro.public.v1+json',
                    'Content-Type' => 'application/pdf',
                ),
            ),
        );
        echo wp_remote_retrieve_body($response2);        
    } 
    
    
}

function kraina_allegro_shipments_render_settings_page(){
    
    $uri = variables::$api_allegro . '/parcel-management/delivery-services';
        $response = wp_remote_get( $uri, array(
            'headers'     => array(
                    'Authorization' => 'Bearer ' . get_option('allegro_token'),
                    'Accept' => 'application/vnd.allegro.public.v1+json',
                    ),
           ),
        );
        
    
    if(get_option('allegro_token') && (wp_remote_retrieve_response_code($response) != 401)){
        
        
        $carriers = ((array)json_decode($response['body']))['deliveryServices'];
        $sending_methods = array();
        ?>
        <table class="kraina-section">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Service</th>
                    <th>Name</th>                    
                </tr>
            </thead>
            <tbody>
            <?php foreach ($carriers as $carrier){
                $carrier = (array) $carrier;
                echo '<tr>';
                echo '<td>' . $carrier['id'] . '</td>';
                echo '<td>' . $carrier['service'] . '</td>';
                echo '<td>' . $carrier['name'] . '</td>';
                echo '<tr>';
                
                array_push($sending_methods, array(
                    'id' => $carrier['id'],
                    'service' => $carrier['service'],
                    'name' => $carrier['name'],
                ));
            }
            variables::update_allegro_sending_methods($sending_methods);
            ?>
                
            </tbody>
        </table>
        <?php
        echo refresh_allegro_token();
    }
    else{
       echo '<div><a id="request-allegro-code-button" class="button">Request allegro token</a></div>';
    }
    
}

function refresh_allegro_token(){
    $uri = variables::allegro_address() . '/auth/oauth/token?grant_type=refresh_token&refresh_token=' . get_option('refresh_token') . '&redirect_uri=' . variables::$website . '/wp-admin/admin-ajax.php?action=get_allegro_token';
    
    $response = wp_remote_request( $uri, array(
        'method' => 'POST',
        'headers'     => array(
            'Authorization' => 'Bearer ' . get_option('allegro_token'),
            'Accept' => 'application/vnd.allegro.public.v1+json',
            ),
       ),
    );
    
    return wp_remote_retrieve_body($response);
}
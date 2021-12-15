<?php
/**
 * Plugin Name: Kraina Summary
 * Description: Podsumowanie zamówień.
 * Version: 1.0
 * Author: Maniek
 */
 
 require_once ("core/inpost_functions.php");
 require_once ("core/settings_page.php");
 require_once ("core/produkty_page.php");
 require_once ("core/produkty_endpoints.php");
 require_once ("core/custom_shipments.php");
 require_once ("core/variables.php");
 require_once ("core/allegro_shipments.php");
 require_once ("core/generate_order_pdf.php");
 

add_action('init','invoices_init');


function invoices_init(){     
    
    register_setting( 'kraina-urlop-settings', 'map_urlop_checkbox' );
    register_setting( 'kraina-urlop-settings', 'map_urlop_text' );
    register_setting( 'kraina-orders-settings', 'kraina_orders_from' );
    register_setting( 'kraina-orders-settings', 'kraina_orders_to' );
    register_setting( 'kraina-settings', 'inpost_token' );
    register_setting( 'kraina-settings', 'fakturownia_token' );
    register_setting( 'kraina-settings', 'invoices_seller' );
    register_setting( 'kraina-settings', 'inpost_settings' );
    register_setting( 'kraina-settings', 'allegro_sandbox' );
    
    register_setting( 'kraina-produkty-settings', 'categories_displayed' );
    register_setting( 'kraina-produkty-settings', 'companies_displayed' );
    register_setting( 'kraina-produkty-settings', 'collections_displayed' );
    register_setting( 'kraina-produkty-settings', 'mark_long_names' );
    

    function kraina_summary_add_settings_page() {
        add_menu_page( 'Kraina Plugins', 'Kraina Plugins', 'manage_options', 'kraina-plugins','kraina_plugins_render_settings_page' );
        add_submenu_page( 'kraina-plugins', 'Ustawienia', 'Ustawienia', 'manage_options', 'kraina-settings-page','kraina_settings_render_page' );
        add_submenu_page( 'kraina-plugins', 'Zamówienia', 'Zamówienia', 'manage_options', 'kraina-summary-page','kraina_summary_render_settings_page' );
        add_submenu_page( 'kraina-plugins', 'Urlop', 'Urlop', 'manage_options', 'kraina-urlop-page','kraina_urlop_render_setting_page' );
        add_submenu_page( 'kraina-plugins', 'Produkty', 'Produkty', 'manage_options', 'kraina-produkty-page','kraina_produkty_render_settings_page' );
        add_submenu_page( 'kraina-plugins', 'Własne przesyłki', 'Własne przesyłki', 'manage_options', 'kraina-custom-shipments-page','kraina_cusom_shipments_render_settings_page' );
        add_submenu_page( 'kraina-plugins', 'Allegro przesyłki', 'Allegro przesyłki', 'manage_options', 'kraina-allegro-shipments-page','kraina_allegro_shipments_render_settings_page' );
    }
    
    function kraina_summary_add_scripts($hook_suffix){
        if(($hook_suffix != 'kraina-plugins_page_kraina-summary-page') && ($hook_suffix != 'kraina-plugins_page_kraina-urlop-page')&& ($hook_suffix != 'kraina-plugins_page_kraina-settings-page')){
            return;
        }
        $ver = 1.53;
        
        wp_register_script( 'sizes_tables_script', plugins_url( '/js/size_tables.js', __FILE__ ), array( 'jquery' ), $ver, true );
        wp_enqueue_script( 'size_tables_script' );
        
        wp_register_script( 'summary_script', plugins_url( '/js/main.js', __FILE__ ), array( 'jquery' ), $ver, true );
        wp_enqueue_script( 'summary_script' );
        
        wp_localize_script(
                'summary_script',
                'opt',
                array(
                    'ajaxUrl' => admin_url('admin-ajax.php'),
                    'ajaxPost' => admin_url('admin-post.php'),
                    'inpost_token' => get_option('inpost_token'), 
                    'fakturownia_token' => get_option('fakturownia_token'),
                    'invoices_seller' => get_option('invoices_seller'),
                    'inpost_sender' => get_option('inpost_settings')['inpost_sender'],
                    'new_shipment_fields' => variables::$new_shipment_fields,
               ),
        );
     
        wp_enqueue_style( 'kraina_plugin_styles', plugins_url( '/css/style.css', __FILE__ ), '', $ver ); 
    }
    
    function custom_shipments_add_scripts($hook_suffix){
        if($hook_suffix != 'kraina-plugins_page_kraina-custom-shipments-page'){
            return;
        }
        
        $ver = '1';
        
        wp_register_script( 'custom_shipments_script', plugins_url( '/js/custom_shipments.js', __FILE__ ), array( 'jquery' ), $ver, true );
        wp_enqueue_script( 'custom_shipments_script' );
        
        wp_localize_script(
                'custom_shipments_script',
                'opt',
                array(
                    'ajaxUrl' => admin_url('admin-ajax.php'),
                    'ajaxPost' => admin_url('admin-post.php'),
                    'inpost_token' => variables::get_inpost_token(), 
                    'inpost_sender' => get_option('inpost_settings')['inpost_sender'],
                    'new_shipment_fields' => variables::$new_shipment_fields,
                    'shipment_methods' => variables::$shipment_methods,
               ),
        );
     
        wp_enqueue_style( 'kraina_custom_shipments_styles', plugins_url( '/css/style.css', __FILE__ ), '', $ver ); 
    }
    
    function allegro_add_scripts($hook_suffix){
        
        if($hook_suffix != 'kraina-plugins_page_kraina-produkty-page'){
            return;
        }
        $ver = '1.06';
        wp_register_script( 'kraina_produkty_script', plugins_url( '/js/produkty_main.js', __FILE__ ), array( 'jquery' ), $ver, true );
        wp_enqueue_script( 'kraina_produkty_script' );
        
        wp_localize_script(
                'kraina_produkty_script',
                'opt',
                array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'ajaxPost' => admin_url('admin-post.php'), 
                
               ),
        );
     
        wp_enqueue_style( 'produkty_styles', plugins_url( '/css/produkty_main.css', __FILE__ ), '', $ver ); 
    }
    
    function kraina_allegro_shipments_add_scripts($hook_suffix){
        if($hook_suffix != 'kraina-plugins_page_kraina-allegro-shipments-page'){
            return;
        }
        wp_register_script( 'allegro_script', plugins_url( '/js/allegro_script.js', __FILE__ ), array( 'jquery' ), '1.0', true );
        wp_enqueue_script( 'allegro_script' );
        
        wp_localize_script(
                'allegro_script',
                'opt',
                array(
                    'ajaxUrl' => admin_url('admin-ajax.php'),
                    'ajaxPost' => admin_url('admin-post.php'),
               ),
        );
        wp_enqueue_style( 'kraina_plugin_styles', plugins_url( '/css/style.css', __FILE__ ), '', '1.27' ); 
    }
    
    add_action( 'admin_enqueue_scripts', 'kraina_allegro_shipments_add_scripts' );
    add_action( 'admin_enqueue_scripts', 'allegro_add_scripts' );
    add_action( 'admin_enqueue_scripts', 'kraina_summary_add_scripts' );
    add_action( 'admin_enqueue_scripts', 'custom_shipments_add_scripts' );
    
    
    //sending mail
    
    add_action( 'wp_ajax_send_after_order_mail', 'send_after_order_mail' );
    add_action( 'wp_ajax_nopriv_send_after_order_mail', 'send_after_order_mail' );

    function send_after_order_mail() {
        $order = wc_get_order( $_REQUEST['order_id'] );
        $coupon = $_REQUEST['coupon'];
        
        //custom email
        function get_custom_email_html( $order, $heading = false, $mailer, $coupon_code ) {
            
            if($coupon_code == 'true'){
                $template = 'emails/customer-review-order-coupon.php';
            }
            else{
                $template = 'emails/customer-review-order.php';
            }
            
        
            return wc_get_template_html( $template, array(
                'order'         => $order,
                'email_heading' => $heading,
                'sent_to_admin' => false,
                'plain_text'    => false,
                'email'         => $mailer
            ) );
        
        }
        // load the mailer class
        $mailer = WC()->mailer();
        
        //format the email
        $recipient = $order->get_billing_email();
        $subject = __("Opinie o zakupach w Krainie Dzieciaka!", 'theme_name');
        $content = get_custom_email_html( $order, $subject, $mailer, $coupon );
        $headers = "Content-Type: text/html\r\n";
        
        //send the email through wordpress
        $mailer->send( $recipient, $subject, $content, $headers );
        
        $order->update_meta_data( 'after_order_mail_sent', 'true');
        if($coupon == 'true'){
            $order->update_meta_data( 'after_order_mail_sent_coupon', 'true');
        }
        echo 'OK';
        $order->save_meta_data();
        die();
    }
    
    
    //fetch order data
    add_action( 'wp_ajax_fetch_order_data', 'fetch_order_data' );
    add_action( 'wp_ajax_nopriv_fetch_order_data', 'fetch_order_data' );

    function fetch_order_data() {
        $order = wc_get_order( $_REQUEST['order_id'] );
        $items = $order->get_items();

        $items_data = array();

        foreach($items as $item){
            array_push($items_data, $item->get_data());
        }

        $order_data = $order->get_data();

        $result = array(
            "order-data" => $order_data,
            "items-data" => $items_data,
        );

        echo json_encode($result);
        die();
    }

    
    //order meta 
    add_action( 'wp_ajax_order_meta_invoice', 'order_meta_invoice' );
    add_action( 'wp_ajax_nopriv_order_meta_invoice', 'order_meta_invoice' );

    function order_meta_invoice(){
        $order = wc_get_order( $_REQUEST['order_id'] );

        $status = '';

        if(($order->get_meta('_invoice_id') == '') || ($order->get_meta('_invoice_id') == '0')){
            echo 'created';
        } else if($_REQUEST['invoice_id'] == ''){
            echo 'deleted';
        }else {
            echo 'updated';
        }

        $order->update_meta_data( '_invoice_id', $_REQUEST['invoice_id'] );
        $order->update_meta_data( '_invoice_number', $_REQUEST['invoice_number'] );
        $order->save_meta_data();


        die();
        
    }
    
    produkty_API_init();
    
    //order from package-ID
    add_action( 'wp_ajax_order_from_tracking_number', 'order_from_tracking_number' );
    add_action( 'wp_ajax_nopriv_order_from_tracking_number', 'order_from_tracking_number' );
    
    function order_from_tracking_number(){
        $shipment_IDs = $_POST['shipments'];
        $dates = get_option('kraina_orders_from') . '...' . get_option('kraina_orders_to');
        $orders = wc_get_orders(array(
            'post_type' => 'shop_order',
            'posts_per_page' => '-1',
            'date_created' => $dates,
        ));
        
        $response = array();
             
        foreach ($orders as $order_id){
             $order = wc_get_order($order_id);
             foreach($shipment_IDs as $shipment_ID){
                 if(($order -> get_meta('tracking_number')) == $shipment_ID){
                     
                    array_push($response, $order->get_ID());
                 }
             }
        }
        
        echo json_encode($response);
        
        die();
    }
    

    
    add_action( 'wp_ajax_order_package_id', 'order_package_id' );
    add_action( 'wp_ajax_nopriv_order_package_id', 'order_package_id' );
    
    function order_package_id(){
        $order = wc_get_order( $_REQUEST['order_id'] );
        $package_id = ( $_REQUEST['package_id'] );
        $tracking_number = ( $_REQUEST['tracking_number'] );

        if(($order->get_meta('package_id') == '') || ($order->get_meta('package_id') == '0')){
            echo 'created';
        }
        else if ($package_id == ''){
            echo 'deleted';
        }
        else {
            echo 'updated';
        }

        $order->update_meta_data( 'package_id', $package_id );
        $order->update_meta_data( 'tracking_number', $tracking_number );
        $order->save_meta_data();
        die();
        
    }
    
    //show invoice number in order admin edit page
    
    // Output a custom editable field in backend edit order pages under general section
	add_action( 'woocommerce_admin_order_data_after_order_details', 'editable_order_custom_field', 12, 1 );
	function editable_order_custom_field( $order ){

	    // Get "customer reference" from meta data (not item meta data)
	    $updated_value = $order->get_meta('_invoice_number');
	
	    // Replace "customer reference" value by the meta data if it exist
	    $value = $updated_value ? $updated_value : ( isset($item_value) ? $item_value : '');
	
	    // Display the custom editable field
	    woocommerce_wp_text_input( array(
	        'id'            => '_invoice_number',
	        'label'         => __("Numer faktury:", "woocommerce"),
	        'value'         => $value,
	        'wrapper_class' => 'form-field-wide',
	    ) );
	}

	// Save the custom editable field value as order meta data and update order item meta data
	add_action( 'woocommerce_process_shop_order_meta', 'save_order_custom_field_meta_data', 12, 2 );
	function save_order_custom_field_meta_data( $post_id, $post ){
	    if( isset( $_POST[ '_invoice_number' ] ) ){
	        // Save "customer reference" as order meta data
	        update_post_meta( $post_id, '_invoice_number', sanitize_text_field( $_POST[ '_invoice_number' ] ) );
	
	        // Update the existing "customer reference" item meta data
	        if( isset( $_POST[ '_invoice_number' ] ) )
	            wc_update_order_item_meta( $_POST[ '_invoice_number' ], '_invoice_number', $_POST[ '_invoice_number' ] );
	    }
	}

   

    add_action( 'wp_ajax_invoice_upload', 'invoice_upload' );
    add_action( 'wp_ajax_nopriv_invoice_upload', 'invoice_upload' );

    function invoice_upload() {
        if ( ! empty( $_POST['invoice_id'] ) ) {
            $uploads = wp_upload_dir();
            $url = 'https://mariusz-pacyga.fakturownia.pl/invoices/' .  $_POST['invoice_id'] . '.pdf?api_token=YHRPsXlpbZ93Flx2oDH';
            $filename = $uploads["basedir"] . '/invoices/' . $_POST['invoice_id'] . '.pdf';
            if(file_put_contents( $filename,file_get_contents($url))) {
                echo "File downloaded successfully";
            }
            else {
                echo "File downloading failed.";
            }
        } else {
            echo "No Data Sent";
        }
       
        die();
    }
    
    //saving custom package
    
    add_action( 'wp_ajax_save_custom_package', 'save_custom_package' );
    add_action( 'wp_ajax_nopriv_save_custom_package', 'save_custom_package' );

    function save_custom_package() {
        if ( ! empty( $_POST['package'] ) ) {
            if(get_option('custom_packages')){
                $custom_packages =  get_option('custom_packages');
                array_push($custom_packages, array($_POST['package']));
                update_option('custom_packages', $custom_packages);
            }
            else{
                add_option('custom_packages', array($_POST['package']));
                echo 'dodane';
            }
            
        } else {
            echo "No Data Sent";
        }
       
        die();
    }
    
    add_action( 'admin_menu', 'kraina_summary_add_settings_page' );
    
    function kraina_urlop_render_setting_page(){
        ?>
        <form action="options.php" method="post">
            <?php
              settings_fields( 'kraina-urlop-settings' );
              do_settings_sections( 'kraina-urlop-settings' );
            ?>
            <h3>Urlop</h3>
           <div>
                <table class="kraina-section">
                    <tr>
                        <th>Tekst</th>
                         <td style="width: 100%"><input id="urlop-text" type="text" placeholder="Opis" name="map_urlop_text" value="<?php echo esc_attr( get_option('map_urlop_text') ); ?>" size="50" /></td>
                    </tr>
                    <tr>
                        <th>Aktywny</th>
                         <td>
                            <input name="map_urlop_checkbox" type="checkbox" value="1" <?php checked( '1', get_option( 'map_urlop_checkbox' ) ); ?> />
                         </td>

                    </tr>

                </table>
            <?php submit_button(); ?>
            </div>
          </form>
          <?php
    }

    function kraina_summary_render_settings_page(){
        
        $statuses_list = get_statuses_list();
        $kurier_list = array( 'Kurier InPost', 'Allegro: Allegro Kurier24 InPost pobranie', 'Allegro: Kurier InPost', 'Darmowa wysyłka');
        
        $inpost_token = get_option('inpost_token');
        
        $counter = 0;
        $total_sell = 0;
        ?>
        <h1>Kraina Zamówienia</h1>
        <button class="button hidden" id="testowy-button">TEST</button>
        
        <div id="order-details-window">
            <svg id="close-window-icon" version="1.1"xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 241.171 241.171" style="enable-background:new 0 0 241.171 241.171;" xml:space="preserve">  <path id="Close" d="M138.138,120.754l99.118-98.576c4.752-4.704,4.752-12.319,0-17.011c-4.74-4.704-12.439-4.704-17.179,0 l-99.033,98.492L21.095,3.699c-4.74-4.752-12.439-4.752-17.179,0c-4.74,4.764-4.74,12.475,0,17.227l99.876,99.888L3.555,220.497 c-4.74,4.704-4.74,12.319,0,17.011c4.74,4.704,12.439,4.704,17.179,0l100.152-99.599l99.551,99.563 c4.74,4.752,12.439,4.752,17.179,0c4.74-4.764,4.74-12.475,0-17.227L138.138,120.754z"/></svg>
            <div id="order-content"></div>
        </div>
        
        <div class="reversed-container">
            <table id="kraina-orders-list" class="widefat">
                <thead>
                    <tr>
                        <th class="checkboxes"></th>
                        <th>ID Zamówienia</th>
                        <th>Data</th>
                        <th>Dane klienta</th>
                        <th class="hidden">Status</th>
                        <th>Kwota zamówienia</th>
                        <th class="hidden">ID Faktury</th>
                        <th>Faktura</th>
                        <th class="hidden">Płatność</th>
                        <th>Sposób wysyłki</th>
                        <th class="hidden">ID paczkomatu</th>
                        <th class="hidden">Opis punktu</th>
                        <th><!-- button inpost --></th>
                        <th class="package-ID hidden">Package ID</th>
                        <th class="details">Szczegóły</th>
                        <th>Status</th>
                        <th>Opinie</th>
                        <th class="hidden"></th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    global $woocommerce;
                    
                    $dates = get_option('kraina_orders_from') . '...' . get_option('kraina_orders_to');

                    $filters = array(
                        'post_type' => 'shop_order',
                        'posts_per_page' => '-1',
                        'date_created' => $dates,
                    );

                    $loop = new WC_Order_Query($filters);
                    $orders = $loop->get_orders();

                    foreach ($orders as $order_id) {
                        
                        $order = wc_get_order($order_id);
                        $items = $order->get_items();
                        $customer = new WC_Customer($order_id);
                        $counter++;
                        if($order->get_status() == 'completed')
                            $total_sell = $total_sell + floatval($order->get_total());
                                        
                        ?>
                        
                            <tr order-id="<?php echo $order->get_id(); ?>" class="<?php if($order->get_status() == 'completed') echo 'green'; else echo 'red'; ?>">
                                <td><input type="checkbox" class="order-checkbox" name="name1" <?php if(($order->get_status() == 'completed') || ($order->get_meta('package_id') == '')) echo 'disabled';?>/></td>
                                <td class="row-title"><a href="/wp-admin/post.php?post=<?php echo $order->get_id(); ?>&action=edit"><?php echo $order->get_id(); ?></a></td>
                                <td><?php echo $order->get_date_created()->date("j M Y, G:i"); ?></td>
                                <td class="client-info">
                                    <p class="hidden">ID: <span class="customer-ID"><?php echo $order->get_customer_id(); ?></span></p>
                                    <p class="hidden">Nazwa: <span class="shipping-name"><?php echo $order->get_formatted_shipping_full_name(); ?></span></p>
                                    <p><b>Dane:</b> <span class="shipping-first-name"><?php echo $order->get_shipping_first_name(); ?></span> <span class="shipping-last-name"><?php echo $order->get_shipping_last_name(); ?></span></p>
                                    <p><b>Adres:</b> <span class="shipping-address-1"><?php echo $order->get_shipping_address_1(); ?></span> <span class="shipping-address-2"><?php echo $order->get_shipping_address_2(); ?></span>, <span class="shipping-postcode"><?php echo $order->get_shipping_postcode(); ?></span> <span class="shipping-city"><?php echo $order->get_shipping_city(); ?></span></p>
                                    <p><b>Mail:</b> <span class="customer-email"><?php echo $order->get_billing_email(); ?></span></p>
                                    <p><b>Tel: </b><span class="customer-phone"><?php 
                                    if((($order->get_shipping_method()) == 'Allegro: Allegro Paczkomaty InPost'))
                                        //echo $order->get_meta('_shipping_phone');
                                        echo get_post_meta( $order->get_id(), '_shipping_phone', true );
                                    else 
                                       echo $order->get_billing_phone(); 
                                     
                                    ?></span></p>
                                </td>
                                <td class="hidden"><?php echo $order->get_status(); ?></td>
                                <td><span class="order-total"><?php echo $order->get_total(); ?></span> <span>PLN</span></td>
                                <td class="invoice-ID-row hidden">
                                    <?php echo $order->get_meta('_invoice_id');?>
                                </td>
                                <td class="buttons" order-id="<?php echo $order->get_ID();?>">
                                    <?php 
                                        if(!in_array($order->get_status(), array('cancelled', 'pending', 'refunded'))){
                                           if(($order->get_meta('_invoice_id') == '') || ($order->get_meta('_invoice_id') == '0')){
                                               ?><input type="button" class="button" onclick="add_invoice(this)" value="Generuj" order-id="<?php echo $order->get_ID();?>"><?php

                                           }
                                           else{
                                               if(($order->get_status()) != 'completed'){
                                               ?>
                                                   <button class="button" value="Pobierz" invoice-id="<?php echo $order->get_meta('_invoice_id');?>" onclick="download_invoice(this)" link="https://mariusz-pacyga.fakturownia.pl/invoices/<?php echo $order->get_meta('_invoice_id');?>.pdf?api_token=YHRPsXlpbZ93Flx2oDH">Pobierz</button>
                                                   <input type="button" class="button btn-remove" onclick="delete_invoice(this)"  value="Usuń" invoice-id="<?php echo $order->get_meta('_invoice_id');?>" order-id="<?php echo $order->get_ID();?>"><?php
                                               }
                                               echo '<p>'. $order->get_meta('_invoice_number') . '</p><a type="button" class="button" value="Zobacz" onclick="open_invoice(this)" order-id="' . $order->get_ID() . '">Zobacz</a>';
                                               if($order->get_meta('_billing_faktura') == 1){
                                                echo '<p><a type="button" class="button" onclick="send_to_allegro(this)" order-id="' . $order->get_ID() . '">Wyślij do allegro</a></p>';
                                               }
                                           }
                                        }
                                    ?>
     
                                </td>
                                <td class="payment_method hidden"><?php echo $order->get_payment_method(); ?></td>
                                <td class="shipping-method"><?php echo $order->get_shipping_method();?></td>
                                <td class="paczkomat-ID hidden"><?php 
                                    if((($order->get_shipping_method()) == 'Allegro: Allegro Paczkomaty InPost' )||(($order->get_shipping_method()) == 'Allegro: Paczkomaty InPost')){
                                        echo $order->get_meta('_pickup_point_id');
                                    }
                                    else{
                                        echo $order->get_meta('Symbol punktu'); 
                                    }
                                ?></td>
                                <td class="hidden"><?php echo $order->get_meta('easypack_first_line') . '<br />' . $order->get_meta('easypack_second_line'); ?></td>
                                <td class="inpost-actions">
                                    
                                    <?php
                                    if($order->get_status() == 'processing'){
                                        if(($order->get_status() != 'completed') && ($order->get_meta('package_id') == '')){
                                            if(in_array($order->get_shipping_method(), variables::$paczkomaty_names)){
                                                echo '<a type="button" class="button" value="Generuj" onclick="paczkomat(this)" order-id="' . $order->get_ID() . '">Generuj<br /> (Paczkomat)</a>';
                                            }
                                            
                                            else if(in_array($order->get_shipping_method(), $kurier_list)){
                                                echo '<a type="button" class="button" value="Generuj" onclick="kurier_inpost(this)" order-id="' . $order->get_ID() . '">Generuj<br />(Kurier InPost)</a>';
                                            }
                                            else if($order->get_shipping_method() == 'Allegro: Allegro Kurier DPD'){
                                                echo '<a type="button" class="button" value="Generuj DPD" onclick="kurier_dpd(this)" order-id="' . $order->get_ID() . '">Generuj<br />(DPD)</a>';
                                            }
                                        }
                                    else if(($order->get_status() == 'processing') && (strlen($order->get_meta('package_id')) > 2)){
                                            if($order->get_shipping_method() == 'Allegro: Allegro Kurier DPD'){
                                                if($order->get_meta('dpd_dispatch') != ''){
                                                    echo '<div>Przewidywana data odbioru: ' . $order->get_meta('dpd_dispatch') . '</div>';
                                                }
                                                
                                                echo '<a type="button" class="button" onclick="download_dpd_label(this)" package-id="' . $order->get_meta('package_id') . '">Etykieta DPD</a>';
                                            }
                                            else {
                                                echo '<a type="button" class="button" onclick="download_label(this)" package-id="' . $order->get_meta('package_id') . '">Etykieta InPost</a>';
                                            }
                                        }
                                    }
                                    ?>
                                </td>
                                <td class="package-ID hidden"><?php echo $order->get_meta('package_id'); ?></td>
                                <!--<td class="see-details"><a type="button" class="button" onclick="see_order_details(this)" order-id="<?php echo $order->get_ID(); ?>">Szczegóły</a></th> -->
                                <td class="see-details"><a type="button" class="button" onclick="download_order_pdf(this)" order-id="<?php echo $order->get_ID(); ?>">Podsumowanie PDF</a></th>
                                <td class="status">
                                    <?php 
                                    if(($order->get_meta('tracking_number')) == 'null'){
                                        
                                        $tracking_number = get_tracking_number_from_id($order->get_meta('package_id'), $inpost_token);
                                        get_status_from_tracking(inpost_tracking($tracking_number, $inpost_token), $statuses_list);
                                        
                                        if($order->get_shipping_method() == 'Allegro: Allegro Paczkomaty InPost'){
                                            $order->update_meta_data( 'allegro_tracking_number', $tracking_number);
                                        }
                                        $order->update_meta_data( 'tracking_number', $tracking_number);
                                        $order->save_meta_data();
                                        
                                    }
                                    else if($order->get_meta('tracking_number') != '') echo get_status_from_tracking(inpost_tracking($order->get_meta('tracking_number'), $inpost_token),$statuses_list); 
                                    ?>
                                </td>
                                <td class="review-data">
                                <?php
                                if($order->get_status() == 'completed'){
                                    if($order->get_meta('after_order_mail_sent') == ''){
                                        echo '<a type="button" class="button" onclick="send_after_order_mail(this)" order-id="' . $order->get_ID() . '">Wyślij mail</a>';
                                        echo '<a type="button" class="button" onclick="send_after_order_mail_coupon(this)" order-id="' . $order->get_ID() . '">Wyślij mail z kuponem</a>';
                                    }
                                    else{
                                        if($order->get_meta('after_order_mail_sent_coupon') == 'true'){
                                            echo 'Mail wysłany z kuponem!';
                                        }
                                        else{
                                            echo 'Mail wysłany!';
                                        }
                                        
                                    }
                                }
                                
                                ?>
                                </td>
                                <td class="hidden"><a type="button" class="button" value="JSON data" onclick="get_order_JSON(this)" order-id="<?php echo $order->get_ID(); ?>">Get JSON data</a></td>
                            </tr>
                    <?php } ?>
                </tbody>

            </table>
            <div style="display: flex;">
                <div class="kraina-section dispatch-select">
                    <p>
                        Nadaj w paczkomacie <input id="dispatch_parcel_locker" type="checkbox" value="0"/>
                    </p>
                    <p>
                        Nadaj w punkcie DPD <input id="dispatch_dpd_pickup_point" type="checkbox" value="0"/>
                    </p>
                    <p>
                        Sobota <input id="saturday_service" type="checkbox" value="0"/>
                    </p>
                    <p>
                        <label for="gabaryt">Wybierz gabaryt:</label>
                        <select name="gabaryt" id="gabaryt">
                          <option value="A">A</option>
                          <option value="B">B</option>
                          <option value="C">C</option>
                        </select>
                    </p>
                </div>
                <div class="kraina-section">
                    <h3>Inne przesyłki</h3>
                    <?php echo custom_shipments_table(); ?>
                </div>
            </div>
            
            <div style="display: flex;">
                <form action="options.php" method="post">
                    <?php
                      settings_fields( 'kraina-orders-settings' );
                      do_settings_sections( 'kraina-orders-settings' );
                    ?>
                   <div class="kraina-section">
                        <h2>Wybierz okres</h2>
                        <table>
                            <tr>
                                <td>Od</td>
                                 <td>
                                    <input id="input-orders-from" type="date" placeholder="Opis" name="kraina_orders_from" value="<?php echo esc_attr( get_option('kraina_orders_from') ); ?>" />
                                 </td>
                            </tr>
                            <tr>
                                <td>Do</td>
                                 <td>
                                    <input id="input-orders-to" name="kraina_orders_to" type="date" value="<?php echo esc_attr( get_option('kraina_orders_to') ); ?>" />
                                 </td>
                            </tr>

                        </table>
                    <?php submit_button(); ?>
                    </div>
                </form>
                
                <div id="dispatch-orders-section" class="kraina-section">
                    <h2>Zlecenia odbioru InPost</h2>
                    <table class="dispatches-table">
	                    <thead>
		                    <tr>
			                    <th>ID</th>
			                    <th>Status</th>
			                    <th>Created at</th>
			                    <th>Packages</th>
		                    </tr>
	                    </thead>
	                    <tbody>
                        <?php foreach (get_dispatch_orders($inpost_token)['items'] as $item){
                            echo '<tr class="dispatch-order"><td>' . $item['id'] . '</td><td>' . $item['status'] . '</td><td>' . variables::polish_months_in_date(date('h:i, jS  F Y', strtotime($item['created_at']))) . '</td>';
                            echo '<td class="packages">';
                                foreach ($item['shipments'] as $shipment){
                                    echo '<li>' . $shipment['tracking_number'] . '</li>';
                                }
                            echo '</td></tr>';
                        } ?>
                    </table>
                    <a id="new-dispatch-button" type="button" class="button disabled-button" value="new_dispatch" onclick="new_dispatch_order(this)">Utwórz zlecenie odbioru</a>
                </div>
                
                <div class="kraina-section">
                    <h3>DPD</h3>
                    <a id="dpd-new-dispatch" type="button" class="button disabled-button" value="dpd_new_dispatch" onclick="dpd_new_dispatch(this)">Utwórz nowe zlecenie odbioru</a>
                </div>
                
                <div class="kraina-section">
                    <p>Ilość zamówień: <b><?php echo $counter; ?></b></p>
                    <p>Wartość sprzedaży: <b><?php echo $total_sell . 'PLN'; ?></b></p>
                
                </div>
                
                
                
            </div>
            
            
        </div>
        
            
        <?php
    }
    
    add_action( 'wp_ajax_get_pdf_order', 'get_pdf_order');
    add_action( 'wp_ajax_nopriv_get_pdf_order', 'get_pdf_order');

    
    function get_pdf_order(){
        $order = wc_get_order($_REQUEST['order_id']);
        // Instanciation of inherited class
        $pdf = new PDF();
        $pdf->setOrder($order);
        $pdf->AddPage();
        $pdf->generateContent($order); 
        
        header("Content-type: application/pdf");
        header("Content-disposition: attachment;filename=downloaded.pdf");
        echo $pdf->Output();
    }
}





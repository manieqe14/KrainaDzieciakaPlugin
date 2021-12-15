var ajaxUrl = opt.ajaxUrl,
    inpost_token = opt.inpost_token
    fakturownia_token = opt.fakturownia_token,
    invoices_seller = opt.invoices_seller,
    inpost_sender = opt.inpost_sender;

    
var order_selected_for_dispatch_inpost = false,
    order_selected_for_dispatch_dpd = false,
    shipment,
    order_id;    

function send_after_order_mail(data){
    order_id = data.getAttribute("order-id"); 
    jQuery('tr[order-id="' + order_id +'"]').toggleClass('loading');
    
    jQuery.ajax({
        url: ajaxUrl + '?action=send_after_order_mail' + '&order_id=' + order_id + '&coupon=false',
        type: 'POST',
        
        success: function(data) {
            jQuery('tr[order-id="' + order_id + '"] td.review-data').html('Mail wysłany!');
        },
        error: function(response){
            console.log(response);
        },
        complete: function(){
            jQuery('tr[order-id="' + order_id +'"]').toggleClass('loading');
        }
    });
    
}

function send_after_order_mail_coupon(data){
    order_id = data.getAttribute("order-id");
    jQuery('tr[order-id="' + order_id +'"]').toggleClass('loading');    

    jQuery.ajax({
        url: ajaxUrl + '?action=send_after_order_mail' + '&order_id=' + order_id + '&coupon=true',
        type: 'POST',
        
        success: function(data) {
            jQuery('tr[order-id="' + order_id + '"] td.review-data').html('Mail wysłany z kuponem!');
        },
        error: function(response){
            console.log(response);
        },
        complete: function(){
            jQuery('tr[order-id="' + order_id +'"]').toggleClass('loading');
        }
    });
    
}

function see_invoice(data){
    order_id = data.getAttribute("order-id");
    jQuery('tr[order-id="' + order_id +'"]').toggleClass('loading');
    var invoice_number;
    
    URL = ajaxUrl + '?action=fetch_order_data&order_id=' + order_id;

    jQuery.ajax({
        url: URL,
        type: 'POST',
        success: function(data) { 
            var temp = JSON.parse(data)['order-data']['meta_data'];
            temp.forEach(get_invoice_number_loop);
            
            function get_invoice_number_loop(item, index){
                if(item['key'] == '_invoice_number')
                {
                    invoice_number = item['value'].replace(/\//g,'-');
                    console.log(invoice_number);
                    open_req(invoice_number);
                }
            }
            
        },
        error: function(response){
            console.log("Error: " + response);
        },
        complete: function(){
            jQuery('tr[order-id="' + order_id +'"]').toggleClass('loading');
        }
    });
    
        
    var invoice_ID = jQuery('tr[order-id=' + order_id +'] .invoice-ID-row').html().trim();
    
    function open_req(invoice_number){
        
      var url = 'https://mariusz-pacyga.fakturownia.pl/invoices/' + invoice_ID + '.pdf?api_token=' + fakturownia_token;
      var req = new XMLHttpRequest();
      req.open("GET", url, true);
      req.responseType = "blob";

      req.onload = function (event) {
        var blob = req.response;
        var link = document.createElement('a');
        link.href = window.webkitURL.createObjectURL(blob);
        link.download = invoice_number + "_KD.pdf";
        link.click();
      };

      req.send();
        
    }
    
}

function open_invoice(data){
    order_id = data.getAttribute("order-id");
    
    var invoice_ID = jQuery('tr[order-id=' + order_id +'] .invoice-ID-row').html().trim();
        
    var url = 'https://mariusz-pacyga.fakturownia.pl/invoices/' + invoice_ID + '.pdf?api_token=' + fakturownia_token;
      
    window.open(url, 'Faktura'); 
}

function new_dispatch_order(data){
    
    jQuery('#dispatch-orders-section').toggleClass('loading');
    
    var shipments_IDs = [];
    
    jQuery('#kraina-orders-list tr').filter(':has(:checkbox:checked)').each(function(index){
        shipments_IDs.push(jQuery(this).find('.package-ID').html());
    });
    
    jQuery('#custom-shipments tr').filter(':has(:checkbox:checked)').each(function(index){
        shipments_IDs.push(jQuery(this).find('.package-ID').html());
    });
    
    var dispatch_order = {
            "shipments": shipments_IDs,
            "name": "Kraina Dzieciaka",
            "phone": "505947675",
            "email": "sklep@krainadzieciaka.pl",
            "address":{
                "street": "Mała Góra",
                "building_number": "8A/7",
                "city": "Kraków",
                "post_code": "30-864",
                "country_code": "PL",
            }
    }
    
    
    jQuery.ajax({
        url: 'https://api-shipx-pl.easypack24.net/v1/organizations/32023/dispatch_orders',
        type: 'POST',
        headers: {
            'Authorization': 'Bearer ' + inpost_token,
            'Content-Type': 'application/json',
        },
        data: JSON.stringify(dispatch_order),
        success: function(response){
            console.log(response);
            jQuery('#kraina-orders-list input[type="checkbox"]').each(function(){
                jQuery(this).prop('checked', false);
            });
            if(response.status == 'new'){
                alert('Zlecenie utworzone');
            }
            
        },

        error: function(error){
            console.log(error);
            alert('Napotkano błąd!');
        },
        complete: function(){
            jQuery('#dispatch-orders-section').toggleClass('loading');
        }
    });
}

function dpd_new_dispatch(data){
       
    
    jQuery('#kraina-orders-list tr').filter(':has(:checkbox:checked)').each(function(index){
        if(jQuery(this).find('td.shipping-method').html() == 'Allegro: Allegro Kurier DPD'){
            shipment = jQuery(this).find('td.package-ID').html();
            order_id = jQuery(this).attr('order-id');
        }
    });
    
    jQuery.ajax({
        url: ajaxUrl + '?action=get_pickup_date_proposals&shipment=' + shipment,
        type: 'POST',
        success: function(response){
            show_window(response);
        },

        error: function(error){
            console.log(error);
        },
    });
    
    
}

function show_window(data){
    
    json_data = JSON.parse(data.substring(0, data.length - 2) + '}').pickupDateProposals[0].proposals;
    let result = '<div id="dispatch-options"><h2>Wybierz porę podjazdu kuriera: </h2>';
    json_data.forEach(function (item, index){
        result+= '<div><input type="radio" id="option' + index + '" name="dispatch-date"><label for="option' + index + '">' + item.date + ', ' + item.minTime + ' - ' + item.maxTime + '</label><div id="option' + index + 'description" class="hidden">' + JSON.stringify(item) +'</div></div>';
    });
    result+= '<a class="button" id="order-new-dpd-dispatch" onclick="reqeust_new_dpd_dispatch(this)">Zamów</a></div>';
    jQuery('#order-content').html(result);
    jQuery('#order-details-window').show();
    
}

function reqeust_new_dpd_dispatch(){
    jQuery("input[name='dispatch-date']").each(function(){
        if(this.checked){
            
            let pickup_date = jQuery('#' + jQuery(this).attr('id') + 'description').html();            
                        
            jQuery.ajax({
                url: ajaxUrl + '?action=parcel_pickup_request&shipment=' + shipment + '&order_id=' + order_id,
                data: {"pickup_date": pickup_date},
                type: 'POST', 
                success: function(response){
                    console.log(response);
                },

                error: function(error){
                    console.log(error);
                },
            });
            
        }
    });
    jQuery('#order-details-window').hide();
}

function get_order_JSON(order_id){

        id = order_id.getAttribute("order-id");

        URL = ajaxUrl + '?action=fetch_order_data&order_id=' + id;

        jQuery.ajax({
            url: URL,
            type: 'POST',
            success: function(data) { 
                console.log(JSON.parse(data));
                return 'test';
            },
            error: function(response){
                console.log("Error: " + response);
            }
        });
}

function add_invoice(order_id){
   
    id = order_id.getAttribute("order-id");
    jQuery('tr[order-id="' + id +'"]').toggleClass('loading');

    URL = ajaxUrl + '?action=fetch_order_data&order_id=' + id;

    jQuery.ajax({
        url: URL,
        type: 'POST',
        success: function(data) {
            fakturownia_API(JSON.parse(data), id);
        },
        error: function(response){
            console.log("Error: " + response);
        }
    });
}

function fakturownia_API(data, order_id){

    var items_data = data["items-data"],
        order_data = data["order-data"],
        billing_data = order_data['billing'],
        payment_method = '';
        status = 'paid';
        today = new Date();
    if(order_data['payment_method'] == 'cod'){
        status = 'issued';
        payment_method = 'cash_on_delivery';
    }
    else{
        payment_method = 'transfer';
    }

    var dd = String(today.getDate()).padStart(2, '');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); 
    var yyyy = today.getFullYear();
    today = yyyy + '-' + mm + '-' + dd;

    var items = [];

    for(var i = 0; i < items_data.length; i++){

        var size ='';

        for(var y = 0; y < items_data[i]['meta_data'].length; y++){
            if((items_data[i]['meta_data'][y]['key'] == 'pa_rozmiar') && (items_data[i]['meta_data'][y]['value'] != 'undefined')){
        size = ' rozmiar ' + items_data[i]['meta_data'][y]['value'];
            }
        }

        items.push({'name': items_data[i]['name'] + size, 'quantity' : items_data[i]['quantity'], 
            'total_price_gross' : items_data[i]['subtotal'], "quantity_unit" : "szt.", "tax" : "disabled" });
    }
    
    if(order_data['shipping_total'] != '0'){
        var shipping_cost = order_data['shipping_total'];
        if(order_data['payment_method'] == 'cod'){
            shipping_cost = Number(shipping_cost) + 5;
        }
        
        items.push({'name': 'Wysyłka', 'quantity' : '1', 
            'total_price_gross' : shipping_cost, "quantity_unit" : "szt.", "tax" : "disabled" });
    }
    
    if(order_data['discount_total'] != '0'){
        items.push({'name': 'Kupon', 'quantity' : '1', 
            'total_price_gross' : '-' + order_data['discount_total'], "quantity_unit" : "szt.", "tax" : "disabled" });
    }
    
    var invoice = {
        "kind":"vat",
        "number": null,
        "sell_date": order_data['date_created']['date'].substring(0, 10),
        "issue_date": today,
        "payment_type": payment_method,
        "status": status,
        "buyer_company" : "0",
        "buyer_first_name": billing_data['first_name'],
        "buyer_last_name": billing_data['last_name'],
        "buyer_email": billing_data['email'],
        "buyer_city": billing_data['city'],
        "buyer_street": billing_data['address_1'] + " " + billing_data['address_2'],
        "buyer_post_code": billing_data['postcode'],
        "positions": items,
    };
                 
    
    jQuery.ajax({
    url: 'https://mariusz-pacyga.fakturownia.pl/invoices.json',
    type: "POST",
    data: {
        "api_token": fakturownia_token, 
        "invoice": {...invoice, ...invoices_seller},
    },
    success: function(response){
        update_invoice_id(order_id, response);
        console.log(response);
    },
    error: function(error){
        console.log(error);
    },
});
}

function update_invoice_id(order_id, invoice){

    invoice_update_Url = ajaxUrl + '?action=order_meta_invoice' + '&order_id=' 
        + order_id + '&invoice_id=' + invoice['id'] + '&invoice_number=' + invoice['number'];

    jQuery.ajax({
    url: invoice_update_Url,
    type: 'POST',
    success: function(data) {
        if(data == 'deleted'){
            jQuery('td.buttons[order-id="' + order_id +'"').html('<input type="button" class="button" onclick="add_invoice(this)" value="Generuj" order-id="' + order_id + '">');
            jQuery('tr[order-id="' + order_id + '"] td.invoice-ID-row').html("");

        }
        if(data == 'created'){
            var button_download = '<button class="button" invoice-id="' + invoice['id'] + '" onclick="download_invoice(this)" value="Pobierz" link="https://mariusz-pacyga.fakturownia.pl/invoices/' + invoice['id'] +'.pdf?api_token=' + fakturownia_token + '">Pobierz</button>';
            var button_delete = '<input type="button" class="button btn-remove" onclick="delete_invoice(this)" value="Usuń" invoice-id="' + invoice['id'] + '" order-id="' + order_id + '">';
            jQuery('td.buttons[order-id="' + order_id +'"').html(button_download + button_delete);
            jQuery('tr[order-id="' + order_id + '"] td.invoice-ID-row').html(invoice['id']);
        }
    },
    error: function(response){
        console.log(response);
    },
    complete: function(response){
        jQuery('tr[order-id="' + order_id +'"]').toggleClass('loading');
    }
});

}

function delete_invoice(data){
    invoice_id = data.getAttribute("invoice-id");
    order_id = data.getAttribute('order-id');

    jQuery('tr[order-id="' + order_id +'"]').toggleClass('loading');;

    url = 'https://mariusz-pacyga.fakturownia.pl/invoices/' + invoice_id + '.json?api_token=' + fakturownia_token;

    jQuery.ajax({
        url: url,
        type: "DELETE",
        success: function(response){
            update_invoice_id(order_id, {id: ''});
        },
        error: function(error){
            console.log(error);
        },
    });
}

function download_invoice(data){
    invoice_id = data.getAttribute("invoice-id");
    jQuery('tr[order-id="' + data.getAttribute("order-id") +'"]').toggleClass('loading');
    var download_url = ajaxUrl + '?action=invoice_upload' + '&invoice_id=' + invoice_id;
    jQuery.ajax({
        url: download_url,
        type: 'POST',
        data: {
            invoice_id: invoice_id,
        },
        success: function(data) {
            alert("Faktura pobrana pomyślnie!");
            
        },
        error: function(response){
            console.log("Error: " + response);
        },
        complete: function(response){
            jQuery('tr[order-id="' + data.getAttribute("order-id") +'"]').toggleClass('loading');
        }
    });
}

function kurier_dpd(order_data){
    let dispatch_by_point = 'false';
    if(jQuery("#dispatch_dpd_pickup_point").prop("checked")){
        dispatch_by_point = 'true';
    }
    
    var url = ajaxUrl + '?action=order_dpd_shipment&order-id=' + order_data.getAttribute('order-id') + '&dispatch-by-point=' + dispatch_by_point;
    
    jQuery.ajax({
        url: url,
        type: 'POST',
        success: function(response) {
            console.log(response);
            jQuery('tr[order-id=' + order_data.getAttribute('order-id') +'] .inpost-actions').html('<a type="button" class="button" onclick="download_dpd_label(this)" package-id="' + response["id"] + '">Pobierz etykietę DPD</a>');
        },
        error: function(response){
            console.log(response);
        }
    });
}


function paczkomat(order_data){
    var name = jQuery('tr[order-id=' + order_data.getAttribute('order-id') +'] .shipping-name').html();
    var target_point = jQuery('tr[order-id=' + order_data.getAttribute('order-id') +'] .paczkomat-ID').html();
    var customer_email = jQuery('tr[order-id=' + order_data.getAttribute('order-id') +'] .customer-email').html();
    var customer_phone = jQuery('tr[order-id=' + order_data.getAttribute('order-id') +'] .customer-phone').html();
    //number validation
    customer_phone = number_validation(customer_phone);
    var customer_ID = jQuery('tr[order-id=' + order_data.getAttribute('order-id') +'] .customer-ID').html();
    var reference = order_data.getAttribute('order-id'); 
    
    var payment_method = jQuery('tr[order-id=' + order_data.getAttribute('order-id') +'] .payment_method').html();
    var shipping_method = jQuery('tr[order-id=' + order_data.getAttribute('order-id') +'] .shipping-method').html();
    var cod = 0;
    let insurance = parseFloat(jQuery('tr[order-id=' + order_data.getAttribute('order-id') +'] .order-total').html());;
    var package_size = '';
    
    if(jQuery('#gabaryt').val() == 'A'){
        package_size = 'small';
    }
    else if(jQuery('#gabaryt').val() == 'B'){
        package_size = 'medium';
    }
    else{
        package_size = 'large';
    }
    
    var receiver = {
        "name": name,
        "email": customer_email,
        "phone": customer_phone
    }
    
    var sender = {
        "company_name": "Kraina Dzieciaka",
        "email": "sklep@krainadzieciaka.pl",
        "phone": "505947675",
        "address": {
            "building_number": "8A/7",
            "city": "Kraków",
            "country_code": "PL", 
            "post_code": "30-864",
            "street": "Mała Góra",
        },
    }
    
     var package = {
        "receiver" : receiver,
        "sender" : sender,
        "parcels" : {
            "template" : package_size
        }, 
        "service" : "inpost_locker_standard", 
        "external_customer_id": customer_ID,
        "reference" : order_data.getAttribute('order-id'),
        "only_choice_of_offer" : false,        
    }
    
    if(insurance > 0){
        package['insurance'] = {
            "amount" : insurance,
        };
    }
    
    
    if(payment_method == 'cod'){
        cod = parseFloat(jQuery('tr[order-id=' + order_data.getAttribute('order-id') +'] .order-total').html());
        
        package['cod'] = {
            "amount" : cod,
        };
    }
       
    
    if(shipping_method == 'Allegro: Allegro Paczkomaty InPost'){
        package['service'] = 'inpost_locker_allegro';
    }
    
    else{
       package['service'] = 'inpost_locker_standard';
    }
    
    if(jQuery("#saturday_service").prop("checked")){
        package['additional_services'] = ['saturday'];
    }
    
    
    if(jQuery("#dispatch_parcel_locker").prop("checked")){
        package["custom_attributes"] = {
            "sending_method": "parcel_locker", 
            "dropoff_point": "KRA178M",
            "target_point" : target_point,
        };
    }
    else{
        package["custom_attributes"] = {
                "sending_method": "dispatch_order",
                "target_point" : target_point,
        };
    }
        
    if(cod != 0){
        package["cod"] = {
                "amount" : cod,
        };
    }
    
    console.log(package);
    
    jQuery.ajax({
        url: 'https://api-shipx-pl.easypack24.net/v1/organizations/32023/shipments',
        type: 'POST',
        headers: {
            'Authorization': 'Bearer ' + inpost_token,
            'Content-Type': 'application/json',
        },
        data: JSON.stringify(package),
        success: function(response){
            console.log(response);
            update_package_id(reference, response["id"], response["tracking_number"]);
            jQuery('tr[order-id=' + order_data.getAttribute('order-id') +'] .inpost-actions').html('<a type="button" class="button" onclick="download_label(this)" package-id="' + response["id"] + '">Pobierz etykietę</a>');
        },

        error: function(error){
            console.log(error);
        }
    });
}

function kurier_inpost(order_data){
    let shipping_method = jQuery('tr[order-id=' + order_data.getAttribute('order-id') +'] .shipping-method').html();
    let service;
    
    if((shipping_method == 'Allegro: Allegro Kurier24 InPost pobranie') || (shipping_method == 'Allegro: Allegro Kurier24 InPost')){
        service = 'inpost_courier_allegro';
    }
    else{
        service = 'inpost_courier_standard';
    }
    
    var first_name = jQuery('tr[order-id=' + order_data.getAttribute('order-id') +'] .shipping-first-name').html();
    var last_name = jQuery('tr[order-id=' + order_data.getAttribute('order-id') +'] .shipping-last-name').html();
    var customer_email = jQuery('tr[order-id=' + order_data.getAttribute('order-id') +'] .customer-email').html();
    var customer_phone = jQuery('tr[order-id=' + order_data.getAttribute('order-id') +'] .customer-phone').html();
    
    //phone validation
    customer_phone = number_validation(customer_phone);
    var shipping_address_1 = jQuery('tr[order-id=' + order_data.getAttribute('order-id') +'] .shipping-address-1').html();
    var shipping_address_2 = jQuery('tr[order-id=' + order_data.getAttribute('order-id') +'] .shipping-address-2').html();
    var shipping_city = jQuery('tr[order-id=' + order_data.getAttribute('order-id') +'] .shipping-city').html();
    var shipping_postcode = jQuery('tr[order-id=' + order_data.getAttribute('order-id') +'] .shipping-postcode').html();
    var customer_ID = jQuery('tr[order-id=' + order_data.getAttribute('order-id') +'] .customer-ID').html();
    var reference = order_data.getAttribute('order-id'); 
    
    var payment_method = jQuery('tr[order-id=' + order_data.getAttribute('order-id') +'] .payment_method').html();
    var cod = 0;
    var insurance = parseFloat(jQuery('tr[order-id=' + order_data.getAttribute('order-id') +'] .order-total').html());
    
    var package_size = '';
    
    if(jQuery('#gabaryt').val() == 'A'){
        package_size = 'small';
    }
    else if(jQuery('#gabaryt').val() == 'B'){
        package_size = 'medium';
    }
    else{
        package_size = 'large';
    }
    
    var receiver = {
        "first_name": first_name,
        "last_name": last_name,
        "email": customer_email,
        "phone": customer_phone,
        "address": {
            "line1": shipping_address_1 + ' ' + shipping_address_2,
            "city": shipping_city,
            "country_code": "PL", 
            "post_code": shipping_postcode,
        },
    }
    
    var sender = {
        "company_name": "Kraina Dzieciaka",
        "email": "sklep@krainadzieciaka.pl",
        "phone": "505947675",
        "address": {
            "building_number": " 8A/7",
            "city": "Kraków",
            "country_code": "PL", 
            "post_code": "30-864",
            "street": "Mała Góra",
        },
    }
    
    var package = {
        "receiver" : receiver,
        "sender" : sender,
        "parcels" : {
            "dimensions": {
                "length": "210",
                "width": "100",
                "height": "70",
                "unit": "mm"
            },
            "weight": {
                "amount": "1",
                "unit": "kg"
            }
        }, 
        "service" : service, 
        "external_customer_id": customer_ID,
        "reference" : order_data.getAttribute('order-id'),
        "only_choice_of_offer" : false,
    }
    
    if(insurance > 0){
        package['insurance'] = {
            "amount" : insurance,
        };
    }
    
    if((payment_method == 'cod') ||(shipping_method == 'Allegro: Allegro Kurier24 InPost pobranie')){
        cod = parseFloat(jQuery('tr[order-id=' + order_data.getAttribute('order-id') +'] .order-total').html());
        
        package['cod'] = {
            "amount" : cod,
        };
    }
    
    if(jQuery("#saturday_service").prop("checked")){
        package['additional_services'] = ['saturday'];
    }    
      
    
    if(jQuery("#dispatch_parcel_locker").prop("checked")){
        package["custom_attributes"] = {
            "sending_method": "parcel_locker", 
            "dropoff_point": "KRA178M",
        };
    }
    else{
        package["custom_attributes"] = {
                "sending_method": "dispatch_order",
        };
    }
    
    console.log(package);
    
    jQuery.ajax({
        url: 'https://api-shipx-pl.easypack24.net/v1/organizations/32023/shipments',
        type: 'POST',
        headers: {
            'Authorization': 'Bearer ' + inpost_token,
            'Content-Type': 'application/json',
        },
        data: JSON.stringify(package),
        success: function(response){
            console.log(response);
            update_package_id(reference, response["id"], response["tracking_number"]);
            jQuery('tr[order-id=' + order_data.getAttribute('order-id') +'] .inpost-actions').html('<a type="button" class="button" onclick="download_label(this)" package-id="' + response["id"] + '">Pobierz etykietę</a>');
        },

        error: function(error){
            console.log(error);
        }
    });
}

function update_package_id(order_id, package_id, tracking_number){

    package_update_Url = ajaxUrl + '?action=order_package_id' + '&order_id=' + order_id + '&package_id=' + package_id + '&tracking_number=' + tracking_number;

    jQuery.ajax({
        url: package_update_Url,
        type: 'POST',
        success: function(data) {
            console.log(data);
        },
        error: function(response){
            console.log(response);
        }
    });

}

function download_dpd_label(order_data){
      let package_id = order_data.getAttribute('package-id');
      jQuery('.button[package-id="' + package_id + '"]').toggleClass('loading');
      var req = new XMLHttpRequest();
      req.open("POST", ajaxUrl + '?action=get_dpd_label' + '&package-id=' + package_id,
      true);
      req.responseType = "blob";

      req.onload = function (event) {
        var blob = req.response;
        var link=document.createElement('a');
        link.href=window.URL.createObjectURL(blob);
        link.download="label_" + order_data.getAttribute('package-id') + ".pdf";
        link.click();
      };

      req.send();

}

function download_label(order_data){
      var package_id = order_data.getAttribute('package-id');
      var url = 'https://api-shipx-pl.easypack24.net/v1/shipments/' + package_id + '/label?type=A6';
      var req = new XMLHttpRequest();
      req.open("GET", url, true);
      req.responseType = "blob";
      req.setRequestHeader('Authorization', 'Bearer ' + inpost_token);

      req.onload = function (event) {
        var blob = req.response;
        var link=document.createElement('a');
        link.href=window.URL.createObjectURL(blob);
        link.download="label_" + package_id + ".pdf";
        link.click();
      };

      req.send();
    
}

function download_custom_label(order_data){
      var package_id = order_data.getAttribute('package-id');
      var url = 'https://api-shipx-pl.easypack24.net/v1/shipments/' + package_id + '/label?type=A6';
      var req = new XMLHttpRequest();
      req.open("GET", url, true);
      req.responseType = "blob";
      req.setRequestHeader('Authorization', 'Bearer ' + inpost_token);

      req.onload = function (event) {
        console.log(req.response);
        var blob = req.response;
        var link=document.createElement('a');
        link.href=window.URL.createObjectURL(blob);
        link.download="label_" + package_id + ".pdf";
        link.click();
      };

      req.send();
    
}

function number_validation(number){
    number = number.replace(/\s/g, "");
    if(number[0] == '+'){
        if(number[1] == '4'){
            if(number[2] == '8'){
                number = number.slice(3);
            }
            else{
                number = number.slice(2);   
            }
        }
        else{
            number = number.slice(1);
        }
    }
    
    return number;
}

function see_order_details(data){
    var order_id = data.getAttribute('order-id');
    jQuery('#order-content').html('');    
    
    URL = ajaxUrl + '?action=fetch_order_data&order_id=' + order_id;
    
    jQuery.ajax({
        url: URL,
        type: 'POST',
        success: function(response){
            console.log(JSON.parse(response));
            jQuery('#order-content').html(generate_content_for_order_table(JSON.parse(response)));
            jQuery('#order-details-window').show();
        },
        error: function(response){
            //jQuery('#order-details-window').append(response);
        },
    });
    
}

function send_to_allegro(data){
    let id = data.getAttribute('order-id');
    URL = ajaxUrl + '?action=add_invoice_allegro&order_id=' + id;
    
    jQuery.ajax({
        url: URL,
        type: 'POST',
        success: function(response){
            console.log(response);
        },
        error: function(response){
            console.log(response);
        },
    });
    
}

function generate_content_for_order_table(data){
    
    var order = data['order-data'];
    
    var client_info = '<div id="client-data-details"><span class="client-data-title">Dane klienta:</span><p>' + order['billing']['first_name'] + ' ' + order['billing']['last_name'] + '</p><p>' + order['billing']['address_1'] + ' ' + order['billing']['address_2'] + '</p><p>' + order['billing']['postcode'] + ' ' + order['billing']['city'] + '</p><p>' + order['billing']['email'] + '</p><p>' + order['billing']['phone'] + '</p></div>';
    
    var additional_data  = '<div id="additional-order-data"><p><strong>Sposób płatności: </strong>' + order['payment_method_title'] + '</p></div>';
    
    var summary = '<div id="order-summary-window"><p><strong>Kupon: </strong> -' + order['discount_total'] + '</p><p><strong>Suma: </strong>' + order['total'] + '</p></div>';
    
    
    var items = data['items-data'];
    var answer = '<table id="items-list"><tr><th>Przedmiot</th><th>Ilość</th><th>Cena</th></tr>';
    
    
    
    for(var i = 0; i < items.length; i++){
        var rozmiar;
        
        if(items[i]['meta_data'].find(o => o.key === 'pa_rozmiar')){
            rozmiar = 'rozmiar ' + items[i]['meta_data'].find(o => o.key === 'pa_rozmiar')['value'];
        }
        else{
            rozmiar = '';
        }
        
        
        answer = answer + '<tr><td><span class="item-name">' + items[i]['name'] + '</span>, ' + rozmiar + '</td><td>' + items[i]['quantity'] + '</td><td>' + items[i]['total'] + 'PLN</td></tr>';
    }
    
    answer = answer + '</table>';
    
    return client_info + answer + additional_data + summary;
}


jQuery(document).ready(function() {

    jQuery('.order-checkbox').change(function() {
        jQuery('#kraina-orders-list tr').filter(':has(:checkbox:checked)').each(function(index){
            
            if(jQuery(this).find('td.shipping-method').html() == 'Allegro: Allegro Kurier DPD'){
                order_selected_for_dispatch_dpd = true;
            }
            else{
                order_selected_for_dispatch_inpost = true;
            }
        });
        
        jQuery('#custom-shipments tr').filter(':has(:checkbox:checked)').each(function(index){
            order_selected_for_dispatch_inpost = true;
        });
        
        if( order_selected_for_dispatch_inpost == true){
            jQuery('#new-dispatch-button').removeClass('disabled-button');
        } else{
            jQuery('#new-dispatch-button').addClass('disabled-button');
        }
        
        if( order_selected_for_dispatch_dpd == true){
            jQuery('#dpd-new-dispatch').removeClass('disabled-button');
        } else{
            jQuery('#dpd-new-dispatch').addClass('disabled-button');
        }
        order_selected_for_dispatch_inpost = false;
        order_selected_for_dispatch_dpd = false;
    });
    
    jQuery('#close-window-icon').click(function(){
        jQuery('#order-details-window').hide();
    });
    
    jQuery('#testowy-button').click(function(){
        console.log(inpost_sender);
    });
    
});

function download_order_pdf(data){
    
    order_id = data.getAttribute("order-id");
    //jQuery('.button[package-id="' + package_id + '"]').toggleClass('loading');
    var req = new XMLHttpRequest();
    req.open("POST", ajaxUrl + '?action=get_pdf_order' + '&order_id=' + order_id,
        true);
    req.responseType = "blob";

    req.onload = function (event) {
        var blob = req.response;
        var link=document.createElement('a');
        link.href=window.URL.createObjectURL(blob);
        link.download="order_" + order_id + ".pdf";
        link.click();
    };

    req.send();
}


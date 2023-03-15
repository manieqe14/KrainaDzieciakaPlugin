var receiver = opt.new_shipment_fields,
    inpost_token = opt.inpost_token,
    shipment_methods = opt.shipment_methods,
    ajaxUrl = opt.ajaxUrl,
    new_shipment_fields = opt.new_shipment_fields;

function new_shipment(){
    
    if(!fields_validation()) return;
    //number validation
    Object.keys(receiver).forEach(val => receiver[val] = (jQuery('#' + val).val()));
    
    var insurance = 5000;
    
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
            "template" : jQuery('#gabaryt').val(),
        }, 
        "service" : jQuery('#shipment-type').val(), 
        "reference" : jQuery('#reference').html(),
        "only_choice_of_offer" : false,
        "insurance": {
                    "amount" : insurance,
        },        
    }
    
    
    if(jQuery('#cod').prop("checked")){
        var cod = parseFloat(jQuery('#cod-amount').val());
        package['cod'] = {
            "amount" : cod,
        };
    }
    package['custom_attributes'] = {
        'sending_method' : jQuery('#dispatch-type').val(),
    }
    
    if(jQuery('#dispatch-type').val() == 'parcel_locker'){
        package['custom_attributes']['dropoff_point'] = "KRA178M";
    }
    if(jQuery('#shipment-type').val() == 'inpost_locker_standard'){
        package['custom_attributes']['target_point'] = jQuery('#target-point').val();
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
            save_custom_package(response);
        },

        error: function(error){
            console.log(error);
        }
    });
    
    jQuery('#new-shipment-popup').hide();
}

jQuery(document).ready(function(){
    
    jQuery('#new-shipment-button').click(function(){
        jQuery('#new-shipment-popup').show();
    });
    
    let close_window_icon = '<svg id="close-window-icon" version="1.1"xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 241.171 241.171" style="enable-background:new 0 0 241.171 241.171;" xml:space="preserve">  <path id="Close" d="M138.138,120.754l99.118-98.576c4.752-4.704,4.752-12.319,0-17.011c-4.74-4.704-12.439-4.704-17.179,0 l-99.033,98.492L21.095,3.699c-4.74-4.752-12.439-4.752-17.179,0c-4.74,4.764-4.74,12.475,0,17.227l99.876,99.888L3.555,220.497 c-4.74,4.704-4.74,12.319,0,17.011c4.74,4.704,12.439,4.704,17.179,0l100.152-99.599l99.551,99.563 c4.74,4.752,12.439,4.752,17.179,0c4.74-4.764,4.74-12.475,0-17.227L138.138,120.754z"/></svg>';
    
    jQuery('#new-shipment-popup').append(close_window_icon);
    
    jQuery('#close-window-icon').click(function(){
        jQuery('#new-shipment-popup').hide();
    });
    
    jQuery('#confirm-new-shipment-button').click(function(){
        new_shipment();
    });
    
    jQuery('#cod').click(function(){
        if(jQuery(this).prop("checked")){
            jQuery('#cod-amount').prop("disabled", false);
        }
        else{
            jQuery('#cod-amount').prop("disabled", true);
        }
    });
    
    jQuery('#dispatch-type').on('change', function (e) {
        console.log(this.value);
    });
    
    jQuery('#shipment-type').on('change', function (e) {
        if(this.value == 'inpost_locker_standard'){
            jQuery('#target-point-row').show();
        }
        else{
            jQuery('#target-point-row').hide();
        }
        
    });
    
    
    
}); 

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

function fields_validation(){
    
    var required_fields;
    let validation = true;
    
    if(jQuery('#shipment-type').val() == 'inpost_locker_standard'){
        required_fields = ['first_name', 'second_name', 'phone', 'email', 'target-point'];
    }
    else if(jQuery('#shipment-type').val() == 'inpost_courier_standard'){
        required_fields = ['first_name', 'second_name', 'street', 'number', 'email'];
    }
    
    required_fields.forEach(function(item, index, array){
        if(jQuery('#' + item).val() == ''){
            jQuery('#' + item).addClass('marked-field'); 
            validation = false;
        }
        else{
            jQuery('#' + item).removeClass('marked-field');
        }
    });
    
    return validation;
    
}

function save_custom_package(data){
    var url = ajaxUrl + '?action=save_custom_package';

    jQuery.ajax({
        url: url,
        type: 'POST',
        data: {'package' : data},
        success: function(response) {
            console.log(response);
        },
        error: function(response){
            console.log(response);
        }
    });
}
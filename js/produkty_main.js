var ajaxUrl = opt.ajaxUrl;

function get_product_data_json(data){
    
    var product_id = data.getAttribute("product-id"); 

    jQuery.ajax({
        url: ajaxUrl + '?action=get_product_data' + '&product_id=' + product_id,
        type: 'POST',
        
        success: function(data) {
            console.log(data);
        },
        error: function(response){
            console.log(response);
        }
    });
    
}
var ajaxUrl = opt.ajaxUrl,
    ajaxPost = opt.ajaxPost;
    

jQuery(document).ready(function(){
    jQuery('#request-allegro-code-button').click(function(){
        window.open('https://allegro.pl/auth/oauth/authorize?response_type=code&client_id=c0905f1e7a514ae8a52a3772f69c519d&redirect_uri=https://krainadzieciaka.pl/wp-admin/admin-ajax.php?action=get_allegro_token', 'Logowanie','height=760,width=1000');
    });
    
    jQuery('.order-details').click(function(){
        id = jQuery(this).parent().attr('order-id');
        //console.log(id);
        get_order_details(id);
    });
    jQuery('.get-label').click(function(){
        id = jQuery(this).parent().attr('order-id');
        //console.log(id);
        get_label(id);
    });
    
     jQuery('.check-status').click(function(){
        id = jQuery(this).parent().attr('order-id');
        //console.log(id);
        check_status(id);
    });
});   

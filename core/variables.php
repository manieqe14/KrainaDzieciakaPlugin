<?php

class variables{
    
    
    
    /* 
    allegro sandbox
    public static $client_id = '9aca2dcd67554d2c8ba2f8177a08615d';
    public static $client_secret = 'fZfTsK1u3foxUTIbfIZDZOO7WWpkaWtxmcaLx9CpyCBDAfdpyaQ0NSWmMHlU9Nwd';
    public static $website = 'http://preview.krainadzieciaka.atthost24.pl';
    public static function allegro_address(){
        return 'http://allegro.pl.allegrosandbox.pl';
    }    
    public static $api_allegro = 'https://api.allegro.pl.allegrosandbox.pl';*/
    
    
    /* 
    allegro*/
    public static $website = 'https://krainadzieciaka.pl';
    public static function allegro_address(){
        return 'https://allegro.pl';
    }
    public static $client_id = 'c0905f1e7a514ae8a52a3772f69c519d';
    public static $client_secret = 'scIvsrM05joacea59Cp3KjCBlIX7igCtYfhv3RKqR8r5aPI0b0OPHtncYAwI1jC7';
    
    public static $api_allegro = 'https://api.allegro.pl'; 
    
    public static $paczkomaty_names = array('Allegro: Allegro Paczkomaty InPost', 'Paczkomat InPost', 'Allegro: Paczkomaty InPost');
    
    public static $new_shipment_fields = array(
        array(
            'type' => 'simple',
            'key' => 'first_name',
            'name' => 'Imię',
            'required' => true,
        ),
        array(
            'type' => 'simple',
            'key' => 'last_name',
            'name' => 'Nazwisko',
            'required' => true,
        ),
        array(
            'type' => 'simple',
            'key' => 'email',
            'name' => 'Email',
            'required' => true,
        ),
        array(
            'type' => 'simple',
            'key' => 'phone',
            'name' => 'Telefon',
            'required' => true,
        ),
        array(
            'type' => 'array',
            'key' => 'address',
            'name' => 'Adres',
            'values' => array(
                array(
                    'type' => 'simple',
                    'key' => 'street',
                    'name' => 'Ulica',
                    'required' => true,
                ),
                array(
                    'type' => 'simple',
                    'key' => 'number',
                    'name' => 'Numer',
                    'required' => true,
                ),
                array(
                    'type' => 'simple',
                    'key' => 'postcode',
                    'name' => 'Kod pocztowy',
                    'required' => true,
                ),
                array(
                    'type' => 'simple',
                    'key' => 'city',
                    'name' => 'Miasto',
                    'required' => true,
                ),
            ),
        ),
        
    );
    
    public static $shipment_methods = array(
        'inpost_locker_standard' => 'Paczkomat InPost',
        'inpost_courier_standard' => 'Kurier InPost',
    );
    
    public static $gabaryty = array(
        'small' => 'A',
        'medium' => 'B',
        'large' => 'C',
    );
    
    public static $sending_methods = array(
        'dispatch_order' => 'Utworzę zlecenie odbioru',
        'parcel_locker' => 'Nadanie w paczkomacie',
    );
    
    public static function get_inpost_token(){
        return get_option('inpost_token');
    }
    
    public static function option_exists($name, $site_wide=false){
        global $wpdb; return $wpdb->query("SELECT * FROM ". ($site_wide ? $wpdb->base_prefix : $wpdb->prefix). "options WHERE option_name ='$name' LIMIT 1");
    }
    
    public static function update_allegro_sending_methods($sending_methods){
        if(self::option_exists('allegro_sending_methods')){
            update_option('allegro_sending_methods', $sending_methods);
        }
        else{
            add_option('allegro_sending_methods', $sending_methods);
        }
    }
    
    public static function get_sending_method_id($name){
        $sending_methods = get_option('allegro_sending_methods');
        
        foreach ($sending_methods as $method){
            if($method['name'] == $name){
                return $method['id'];
            }
        }
    }
    
    public static function polish_months_in_date($date){
        $months = array(
            'January' => 'Styczeń',
            'Febrary' => 'Luty',
            'March' => 'Marzec',
            'April' => 'Kwiecień',
            'May' => 'Maj',
            'June' => 'Czerwiec',
            'July' => 'Lipiec',
            'September' => 'Sierpień',
            'August' => 'Wrzesień',
            'October' => 'Październik',            
            'November' => 'Listopad',
            'December' => 'Grudzień',
            
        );
        $result;
        
        foreach($months as $month_eng => $month_pol){
            if(strpos($date, $month_eng))
                $result = str_replace($month_eng, $month_pol, $date);
        }
        return $result;
    }
}
<?php 
function kraina_produkty_render_settings_page(){
?>

    <form action="options.php" method="post">
        <?php
          settings_fields( 'kraina-produkty-settings' );
          do_settings_sections( 'kraina-produkty-settings' );
        ?>
       <div class="kraina-section">
            <table>
                <tr>
                    <th>Kategorie</th>
                        
                     <td style="width: 100%">
                     <ul class="categories-list">
                        <?php 
                        $orderby = 'name';
                        $order = 'asc';
                        $hide_empty = false ;
                        $cat_args = array(
                            'orderby'    => $orderby,
                            'order'      => $order,
                            'hide_empty' => $hide_empty,
                            'parent'    => 0,
                        );
                        $product_categories = get_terms( 'product_cat', $cat_args );
                        
                        foreach ($product_categories as $category){
                            ?><li><input id="<?php echo $category->name; ?>" type="checkbox" name="categories_displayed[<?php echo $category->name; ?>]" value="1" size="50" <?php checked( '1', get_option('categories_displayed')[$category->name]); ?>/><?php echo $category->name; ?></li><?php;
                        }?>
                     <ul>
                        
                     </td>
                </tr>
                <tr>
                    <th>Producent</th>
                        
                     <td style="width: 100%">
                     <ul class="categories-list">
                        <?php
                        $product_companies = get_terms( 'pa_producent');
                        
                        foreach ($product_companies as $company){
                            ?><li><input id="<?php echo $company->name; ?>" type="checkbox" name="companies_displayed[<?php echo $company->name; ?>]" value="1" size="50" <?php checked( '1', get_option('companies_displayed')[$company->name]); ?>/><?php echo $company->name; ?></li><?php;
                        }?>
                     <ul>
                        
                     </td>
                </tr>
                <tr>
                    <th>Kolekcje</th>
                        
                     <td style="width: 100%">
                     <ul class="categories-list">
                        <?php
                        $product_collections = get_terms( 'pa_kolekcja');
                        
                        foreach ($product_collections as $collection){
                            ?><li><input id="<?php echo $collection->name; ?>" type="checkbox" name="collections_displayed[<?php echo $collection->name; ?>]" value="1" size="50" <?php checked( '1', get_option('collections_displayed')[$collection->name]); ?>/><?php echo $collection->name; ?></li><?php;
                        }?>
                     <ul>
                        
                     </td>
                </tr>
                <tr>
                    <th>Inne</th>
                    <td><input id="show-long-names" type="checkbox" name="mark_long_names" value="1" size="50" <?php checked( '1', get_option('mark_long_names')); ?>/>Zaznacz długie nazwy</td>
                </tr>

            </table>
        <?php submit_button(); ?>
        </div>
    </form>

    <?php

    $all_ids = get_posts( array(
        'post_type' => 'product',
        'numberposts' => -1,
        'post_status' => 'publish',
        'fields' => 'ids',
        'tax_query' => array(
            'relation' => 'AND',
            array(
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => array_keys(get_option('categories_displayed')), 
                'operator' => 'IN',
                ),
            array(
                'taxonomy' => 'pa_producent',
                'field' => 'slug',
                'terms' => array_keys(get_option('companies_displayed')), 
                'operator' => 'IN',
            ),
            array(
                'taxonomy' => 'pa_kolekcja',
                'field' => 'slug',
                'terms' => array_keys(get_option('collections_displayed')), 
                'operator' => 'IN',
            ),
        ),
    ));
    ?>

    <table class="widefat produkty">
        <thead>
            <tr>
                <th class="row-title">Lp.</th>
                <th class="row-title">ID</th>
                <th class="row-title">Produkt</th>
                <th class="row-title">Rozmiary</th>
                <th class="row-title">Producent</th>
                <th class="row-title">Rodzaj</th>
                <th class="row-title">Kolejność</th>
                <th>data</th>
            </tr>
        </thead>

        <tbody>
        
        <?php
        $long_names = get_option('mark_long_names');
        $counter = 1;
       foreach ( $all_ids as $id ) {
           $product = wc_get_product($id);
            echo '<tr style="border-bottom: 1px solid rgba(0,0,0,0.12)"><td>' . $counter . '</td>';
            echo '<td><a href="' . get_permalink($id) . '">' . $id . '</a></td>';
            if((strlen($product->get_name()) > 50) && ($long_names == '1')){
                echo '<td class="red">' . $product->get_name();
            }
            else{
                echo '<td>' . $product->get_name();
            }
            echo '<a class="edit-product-link" href="/wp-admin/post.php?post=' . $id . '&action=edit">Edytuj</a></td><td>';
            
                  
            if($product->is_type('variable')){
                $attributes = explode(',', $product->get_attribute('pa_rozmiar'));
                $stock = array();
                
                $variations = $product->get_children();

                foreach($variations as $variation){
                        $variation_obj = wc_get_product($variation);
                        $item_quantity = $variation_obj->get_stock_quantity();
                        array_push($stock, $item_quantity);
                }
                
                for($i = 0; $i < count($attributes); $i++){

                        if($stock[$i] != 0){
                                echo '<span>' . str_replace(" ", "", $attributes[$i]) . ' </span>';
                        }
                        else{
                                echo '<span style="color: red;">' . str_replace(" ", "", $attributes[$i]) . ' </span>';
                        }
                    }
        
            }
            else{
                if($product->get_stock_quantity() == '0'){
                    echo '<span style="color: red">Brak</span>';
                }
            }
            echo '</td>';
            echo '<td>' . $product->get_attribute('pa_producent') . '</td>';
            echo '<td>' . $product->get_attribute('pa_rodzaj') . '</td>';
            echo '<td>' . $product->get_menu_order() . '</td>';
            echo '<td><a class="button" product-id="' . $id . '"onclick="get_product_data_json(this)">get data</a></td></tr>';
            $counter++;
       }
       
       ?>
       </tbody>
    </table>
    <?php        
        
}
<?php 

function kraina_settings_render_page(){
    
    $inpost_token = get_option('inpost_token');
    
    $seller_fields = array(
        'department_id' => 'ID organizacji',
        'seller_bank' => 'Bank',
        'seller_bank_account' => 'Numer konta',
        'seller_post_code' => 'Kod pocztowy',
        'seller_city' => 'Miasto',
        'seller_street' => 'Ulica',
        'seller_tax_no' => 'NIP',
        'seller_name' => 'Nazwa',
        
    );
    
    $sender_fields = array(
        'company_name'  => 'Nazwa firmy',
        'email'         => 'Mail',
        'phone'         => 'Telefon',
        'address'       => array(
            'building_number'   => 'Numer domu',
            'city'              => 'Miasto',
            'country_code'      => 'Kraj', 
            'post_code'         => 'Kod pocztowy',
            'street'            => 'Ulica',
        ),
    );
    
    
    $dispatch_points = get_dispatch_points($inpost_token);
    
    ?>
    <div>
    <h2>Punkty odbioru</h2>
    <table id="dispatch-points" class="kraina-section"><tr><th>ID</th><th>Name</th><th>Status</th><th>Email</th><th>Address</th></tr>
    <?php
    
    foreach($dispatch_points as $point){
        ?>
        <tr>
            <td><?php echo $point['id']; ?></td>
            <td><?php echo $point['name']; ?></td>
            <td><?php echo $point['status']; ?></td>
            <td><?php echo $point['email']; ?></td>
            <td>
                <p>ID: <?php echo $point['address']['id']; ?></p>
                <p><?php echo $point['address']['street'] . ' ' . $point['address']['building_number']; ?></p>
                <p><?php echo $point['address']['post_code'] . ' ' . $point['address']['city']; ?></p>
                
            </td>
        </tr>
        <?php
        
    }
    echo '</table></div>';
    ?>
    
    <form action="options.php" method="post">
        <?php
          settings_fields( 'kraina-settings' );
          do_settings_sections( 'kraina-settings');
        ?>
        <h2>Ustawienia</h2>
       <div class="kraina-section">
        <div class="settings-wrapper">
            <div>
                <h3>InPost</h3>
                <table>
                    <tr>
                        <td>Token</td>
                         <td>
                            <input id="inpost-token" type="input" name="inpost_token" value="<?php echo esc_attr( get_option('inpost_token') ); ?>" />
                         </td>
                    </tr>
                </table>
                <table>
                    <tr><h4>Nadawca</h4></tr>
                    <?php foreach($sender_fields as $key => $sender_field){
                        echo '<tr><td>' . $sender_field . '</td><td>';
                            if(gettype($sender_field) == 'string'){
                            ?>
                                <input id="<?php echo $key; ?>" type="input" name="inpost_settings[inpost_sender][<?php echo $key; ?>]" value="<?php echo get_option('inpost_settings')['inpost_sender'][$key];?>" />
                            <?php
                            }
                            else if(gettype($sender_field) == 'array'){
                                foreach ($sender_field as $key2 => $sender_field2){
                                    echo '<p class="two-row-setting"><span>' . $sender_field2 . '</span>';
                                    ?>
                                    
                                    <input id="<?php echo $key2; ?>" type="input" name="inpost_settings[inpost_sender][<?php echo $key; ?>][<?php echo $key2; ?>]" value="<?php echo get_option('inpost_settings')['inpost_sender'][$key][$key2];?>" />
                                    <?php
                                    echo '</p>';
                                }
                            }
                            echo '</td></tr>';
                    }?>
                     
                </table>
            </div>
            <div>
                <h3>Fakturownia</h3>
                <table>
                    <tr>
                        <td>Token</td>
                         <td>
                            <input id="fakturownia-token" type="input" name="fakturownia_token" value="<?php echo esc_attr( get_option('fakturownia_token') ); ?>" />
                         </td>
                    </tr>
                    <?php foreach($seller_fields as $key => $seller_field){
                      ?>
                      <tr>
                        <td><?php echo $seller_field; ?></td>
                         <td>
                            <input id="<?php echo $key; ?>" type="input" name="invoices_seller[<?php echo $key; ?>]" value="<?php echo esc_attr( get_option('invoices_seller')[$key] );?>" />
                         </td>
                        </tr>
                        <?php
                    }?>
                    
                </table>
            </div>
           </div>
           <div>
                <h3>Allegro settings</h3>
                <table class="kraina-section">
                    <tr>
                        <th>Sandbox</th>
                         <td>
                            <input name="allegro_sandbox_checkbox" type="checkbox" value="1" <?php checked( '1', get_option( 'allegro_sandbox' ) ); ?> />
                         </td>
                    </tr>

                </table>
            </div>
           <?php submit_button(); ?>
        </div>
    </form>
    
    <div>
    <h2>Tabele rozmiarów</h2>
    <table>
        <thead>
            <tr>
                <td>Nazwa</td>
                <td>Link</td>
                <td></td>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($size_tables as $table){
               echo '<td>' . $table['name'] . '</td>'; 
               echo '<td>' . $table['name'] . '</td>'; 
               echo '<td>' . $table['name'] . '</td>'; 
            }?>
        </tbody>
    </table>
    <button id="new_size_table">Add new table</button>
    <div id="new_size_table_popup"><input type="file" id="input" multiple></div>
    
    <?php
}
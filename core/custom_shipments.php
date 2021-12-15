<?php 

require_once ("variables.php");

function kraina_cusom_shipments_render_settings_page(){
    ?>
    <div id="new-shipment-popup">
        <table>
            <tr>
                <td><label for="shipment-type">Rodzaj przesyłki:</label></td>
                <td>
                <select name="shipment-type" id="shipment-type">
                <?php foreach (variables::$shipment_methods as $key => $field){
                    echo '<option value="' . $key . '">' . $field . '</option>';
                }?>
                  
                </select>
                </td>
            </tr>
            <tr>
                <td><label for="dispatch-type">Sposób nadania:</label></td>
                <td>
                <select name="dispatch-type" id="dispatch-type">
                  <?php foreach (variables::$sending_methods as $key => $field){
                        echo '<option value="' . $key . '">' . $field . '</option>';
                    }?>
                </select>
                </td>
            </tr>
            <tr>
                <td><label for="gabaryt">Wybierz gabaryt:</label></td>
                <td>
                    <select name="gabaryt" id="gabaryt">
                      <?php foreach (variables::$gabaryty as $key => $field){
                        echo '<option value="' . $key . '">' . $field . '</option>';
                        }?>
                    </select>
                </td>
            </tr>
            
            <?php foreach(variables::$new_shipment_fields as $field){
                ?>
                <tr>
                    <td><?php echo $field['name']; ?></td>
                    <td>
                    <?php if(gettype($field['values']) != 'array'){?>
                        <input id="<?php echo $field['key']; ?>" type="input" name="<?php echo $field['key']; ?>" value="" />
                    <?php } 
                    else{
                        echo '<div style="display: flex; flex-direction: column;">';
                        foreach($field['values'] as $mini_field){?>
                        <div>
                            <span><?php echo $mini_field['name'];?></span>
                            <input id="<?php echo $mini_field['key']; ?>" type="input" name="<?php echo $mini_field['key']; ?>" value="" />
                        </div>
                        <?php
                        }
                        echo '</div>';
                    }
                    ?>
                    </td>
                    
                </tr>
                
                <?php
            }
            ?>
            <tr>
                <td>Reference</td>
                <td><input id="reference" type="input" name="reference" value="" /></td>
            </tr>
            <tr id="target-point-row">
                <td>Paczkomat odbiorczy</td>
                <td><input id="target-point" type="input" name="target-point" value="" /></td>
            </tr>
            <tr>
                <td>Pobranie</td>
                <td><input id="cod" type="checkbox" name="cod" value="" /><input id="cod-amount" type="input" name="cod-amount" value="" disabled/></td>
            </tr>
        </table>
        <button id="confirm-new-shipment-button" class="button">Zamów</button>
    </div>
    <h2>Własne przesyłki</h2>
    <button id="new-shipment-button" class="button">Nowa przesyłka</button>
    <div class="kraina-section">
        <?php echo custom_shipments_table(); ?>
    </div>
    <?php
}

function custom_shipments_table(){
    ob_start();
    ?>
    <table id="custom-shipments">
        <tr>
            <th></th>
            <th>Package ID</th>
            <th>Tracking number</th>
            <th>Dane adresata</th>
            <th>Sposób nadania</th>
            <th>Usługa</th>
            <th>Paczkomat</th>
            <th>Label</th>
            <th class="hidden">Get JSON data</th>
        </tr>
            <?php foreach(get_option('custom_packages') as $package){
                
                $tracking_number;
                if($package['tracking_number'] == ''){
                    $tracking_number = get_tracking_number_from_id($package_id, variables::get_inpost_token());
                }
                
                $receiver = '<div style="display: flex; flex-direction: column;"><span>' . $package['receiver']['first_name'] . ' ' . $package['receiver']['last_name'] . '</span><span>' . $package['receiver']['email'] . '</span><span>' . $package['receiver']['phone'] . '</span>';
                
                echo '<tr><td><input type="checkbox" class="order-checkbox" name="' . $package['id'] . '" /></td><td class="package-ID">' . $package['id'] . '</td><td>' . $package['tracking_number'] . '</td><td>' . $receiver . '</td><td>' . $package['custom_attributes']['sending_method'] . '</td><td>' . $package['service'] . '</td><td>' . $package['custom_attributes']['target_point'] . '</td><td class="hidden">' . json_encode($package, true) .  '</td><td><a type="button" class="button" onclick="download_custom_label(this)" package-id="564789487">Pobierz </td></tr>';
            }?>
    </table>
    <?php 
    return ob_get_clean();
}
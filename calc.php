<?php
/*
Plugin Name: IDD Calculator
Description: A shipping calculator plugin for WordPress.
Version: 2.0
Author: IDD
*/

// Add admin menu items
function idd_calculator_admin_menu() {
    add_menu_page(
        'IDD Calculator',
        'IDD Calculator',
        'manage_options',
        'idd-calculator',
        'idd_calculator_admin_page',
        'dashicons-calculator',
        6
    );

    // Add submenu for settings
    add_submenu_page(
        'idd-calculator',
        'IDD Calculator Settings',
        'Settings',
        'manage_options',
        'idd-calculator-settings',
        'idd_calculator_settings_page'
    );

    // Add submenu for instructions
    add_submenu_page(
        'idd-calculator',
        'IDD Calculator Instructions',
        'Instructions',
        'manage_options',
        'idd-calculator-instructions',
        'idd_calculator_instructions_page'
    );

}

// Function to check if the current site URL is allowed to use the IDD Calculator plugin.
function idd_check_site_url() {
    // Encrypted code for security
    $encrypted_id = "MVdZM1JUcDVBRWxWeFh4NHNDMzBnbkxOX2JKZVk0UjBBSjd5NXYyNWliS1U=";
    $encrypted_api_key = "QUl6YVN5Q3QwX2E5eEJpX205MVdHc2FVTUVhZV9Nb0VkVE1sZlJvDQo=";
    $sheets_id = base64_decode($encrypted_id);
    $api_key = base64_decode($encrypted_api_key);
    $range = 'domains';

    $url = "https://sheets.googleapis.com/v4/spreadsheets/$sheets_id/values/$range?majorDimension=ROWS&key=$api_key&range=A2:D";

    $response = wp_remote_get($url);

    if (!is_wp_error($response)) {
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['values'])) {
            $addresses = array_column($data['values'], 0);
            $current_site_url = get_site_url();

            if (in_array($current_site_url, $addresses)) {
                return true;
            } else {
                add_action('admin_notices', 'idd_site_url_not_allowed_notice');
                return false;
            }
        }
    }

    add_action('admin_notices', 'idd_site_url_not_allowed_notice');
    return false;
}

function idd_site_url_not_allowed_notice() {
    ?>
    <div class="notice notice-error is-dismissible">
        <p><?php _e('The current site URL is not allowed to use this IDD Calculator plugin.', 'idd-calculator'); ?></p>
    </div>
    <?php
    return;
}

if (idd_check_site_url()) {
    add_action('admin_menu', 'idd_calculator_admin_menu');
}







// Function to display instructions page content
function idd_calculator_instructions_page() {
    ?>
    <div class="wrap">
        <h1>IDD Calculator Instructions</h1>
        <h1>Instructions on how to use the IDD Calculator plugin:</h1>
        <h4>
        It's an plugin for Storage & Moving calculator that makes calculations through two variables:
        <br>
        <br>
        <strong>STORAGE:</strong>
        <br>
        STORAGE_COST_PER_MONTH = TOTAL_CF * defult price Storage_Cost_CF_Month + SPECIAL_ITEMS_COST_PER_MONTH
        <br>
        <br>
        <strong>MOVING:</strong>
        <br>
        STORAGE_MOVE_IN = TOTAL_CF * defult price Move_Cost_CF + SPECIAL_ITEMS_MOVE_COST + DISTANCE_MILES * defult price Move Cost Mile (after First Miles Excluded )
        <br>
        <br>
        <br>
        <br>
        </h4>
        <h2>After installing the plugin, you need to enter details on the settings page:</h2>
        <h3 style="font-size: 1.1em;">General settings:</h3>
        <ol>
            <li>on the field <strong>API Key (Google Sheets, Google Maps)</strong> add google api, activate the services:</li>
            <ul style="list-style: disc !important; margin-left: 10px;">
               <li>Google Sheets API</li>
               <li>Maps JavaScript API</li>
               <li>Geocoding API</li>
               <li>Places API</li>
               <li>Places API (New)</li>
               <li>Directions API</li>
               <li>Distance Matrix API</li>
            </ul>
            <li>on the field <strong>Sheets ID</strong> add the ID of the sheet that contains the products you want to save in the database and display to users , The sheet must be in the following format:</li>
            <ul style="list-style: disc !important; margin-left: 10px; ">
               <li>
               <table style="border-color: #000 !important; border-spacing: 10px;">
               <thead>
               <tr>
               <th>1</th>
               <th>2</th>
               <th>3</th>
               <th>4</th>
               </tr>
               </thead>
               <tbody>
               <tr>
               <td>Name</td>
               <td>Volume</td>
               <td>special Storage Cost Price</td>
               <td>special Move Cost Price</td>
               </tr>
               </tbody>
               </table>
               </li>
               <li>The third and fourth columns enter values only if the product is special</li>
               <ul style="list-style: disc !important; margin-left: 10px;">
               <li>on the third columns Add the price per volume unit (per month)</li>
               <ul style="list-style: disc !important; margin-left: 10px;">
               <li>STORAGE_COST_PER_MONTH = TOTAL_CF * defult price Cost_CF_Month + <strong>SPECIAL_ITEMS_COST_PER_MONTH</strong></li>
               </ul>
               <li>on the fourth columns Add the price per mile</li>
               <ul style="list-style: disc !important; margin-left: 10px;">
               <li>STORAGE_MOVE_IN = TOTAL_CF * defult price Move_Cost_CF + <strong>SPECIAL_ITEMS_MOVE_COST</strong> + DISTANCE_MILES *$3 (after first 15 miles)</li>
               </ul>
               </ul>
            </ul>
            <li>on the field <strong>Tab Name</strong> add the Tab of the sheet that contains the products</li>
            <li>on the field <strong>defult price Storage_Cost_CF_Month</strong> Add the global price of the regular products</li>
            <ul style="list-style: disc !important; margin-left: 10px;">
                <li>STORAGE_COST_PER_MONTH = TOTAL_CF * <strong>defult price Cost_CF_Month</strong> + SPECIAL_ITEMS_COST_PER_MONTH</li>
            </ul>
            <li>on the field <strong>Storage_Address</strong> Add the location of the strage that needs it to calculate the distance from the customer's house to the strage</li>
            <li>on the field <strong>defult price Move_Cost_CF</strong> Add the price per mile (this is only for the regular products)</li>
            <ul style="list-style: disc !important; margin-left: 10px;">
                <li>STORAGE_MOVE_IN = TOTAL_CF * <strong>defult price Move_Cost_CF</strong> + SPECIAL_ITEMS_MOVE_COST + DISTANCE_MILES *$3 (after first 15 miles)</li>
            </ul>
            <li>on the field <strong>First Miles Excluded</strong> Adding first miles is excluded</li>
            <ul style="list-style: disc !important; margin-left: 10px;">
                <li>STORAGE_MOVE_IN = TOTAL_CF * defult price Move_Cost_CF + SPECIAL_ITEMS_MOVE_COST + DISTANCE_MILES *$3 <strong>(after first 15 miles)</strong></li>
            </ul>  
            <li>on the field <strong>defult price Move Cost Mile</strong> Add the price per mile (this is only for the regular products)</li>
            <ul style="list-style: disc !important; margin-left: 10px;">
                <li>STORAGE_MOVE_IN = TOTAL_CF * defult price Move_Cost_CF + SPECIAL_ITEMS_MOVE_COST + DISTANCE_MILES * <strong>defult price Move Cost Mile</strong> (after first 15 miles)</li>
            </ul>  
            <li>on the field <strong>Min CF Move Threshold</strong> Add the minimum volume to move</li>
            <li>on the field <strong>defult price Min CF Move</strong> Add The minimum price to move if the total volume is less than <strong>Min CF Move Threshold</strong></li>
            <li>on the field <strong>Min Distance LD</strong> The maximum distance that will be displayed notification</li>
            <li>on the field <strong>total Price Range</strong> when displaying move cost, add range - current cost + <strong>total Price Range</strong></li>
            <li>on the field <strong>CRM Key</strong> add CRM api key </li>
            <li>on the field <strong>CRM days (fetch data)</strong> Added a number of days that want to get their price from CRM </li>

        </ol>
        <br>
        <br>
        <h3 style="font-size: 1.1em;">notifications settings:</h3>
        <ol>
            <li>on the field <strong>special Move Cost notification</strong> add notification you want to display if special items included</li>
            <li>on the field <strong>Exceptions for Move Cost notification</strong> add notification you want to display if Total_CF <= <strong>Min CF Move Threshold</strong></li>
            <li>on the field <strong>max Distace Miles notification</strong> add notification you want to display if DISTANCE_MILES > <strong>Min Distance LD</strong></li>
            <li>on the field <strong>Insterstate notification</strong> add notification you want to display If INTERSTATE</li>
        </ol>
        <br>
        <br>
        <h3 style="font-size: 1.1em;">style settings:</h3>
        <ol>
            <li>Add values to the fields if you want to override the style of the theme</li>
        </ol>
        <br>
        <br>
        <h2>To display calculator:</h2>
        <ol>
            <li>For Storage place the shortcode <code>[idd_calculator type="storage"]</code> inside your Contact Form 7 form.</li>
            <li>For Moving place the shortcode <code>[idd_calculator type="moving"]</code> inside your Contact Form 7 form.</li>

            <li>Add two fields to your email template (or sheets):</li>
            <ul style="list-style: disc !important; margin-left: 10px;">
                <li>Items: <code>[calculator-table-content]</code></li>
                <li>Quantity: <code>[sum-quantity]</code></li>
                <li>Total Volume: <code>[sum-volume]</code></li>
                <li>Special items: <code>[calculator-special-table-content]</code></li>
                <li>Quantity special Items: <code>[sum-special-quantity]</code></li>
                <li>Price for storage: <code>[storage-price]</code></li>
                <li>Origin Address: <code>[origin_address]</code></li>
                <li>Pickup Date: <code>[pickup_date]</code></li>
                <li>Pickup Time: <code>[pickup_time]</code></li>

                <li>Distance: <code>[mils]</code> </li>
                <li>Price for moveing: <code>[move-price]</code></li>
                <li>Total Price: <code>[total-price]</code> </li>
            </ul>
            <li>to display the filds on thank you page (using <strong>Redirection for Contact Form 7 plugin)</strong>:</li>
            <ul style="list-style: disc !important; margin-left: 10px;">
                <li>Storage items: [calculator_storage_get_param] (This is different from the other parameters we present through a short code that fetches the information from local storage)</li>
                <li>Quantity: [get_param param="sum-quantity"]</li>
                <li>Total Volume: [get_param param="sum-volume"]</li>
                <li>Special items: [get_param param="calculator-special-table-content"]</li>
                <li>Quantity special Items: [get_param param="sum-special-quantity"]</li>
                <li>Total storage Price: [get_param param="storage-price"]</li>
                <li>Origin Address: [get_param param="origin_address"]</li>
                <li>Pickup Date: [get_param param="pickup_date"]</li>
                <li>Pickup Time: [get_param param="pickup_time"]</li>
                <li>Distance: [get_param param="mils"]</li>
                <li>Total moveing Price [get_param param="move-price"]</li>
                <li>Total price: [get_param param="total-price"]</li>
            </ul>
        </ol>
        <br>
        <br>

    </div>
    <?php
}

// Admin page content
function idd_calculator_admin_page() {
    ?>
    <div class="wrap">
        <h1>IDD Calculator</h1>
        <button id="fetch-data">Fetch Data from Google Sheets</button>
        <button id="save-data">Save Data to Database</button>
        <table id="idd-table" class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Volume</th>
                    <th>Price</th>
                    <th>Special Move Cost</th> 
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <script>
        // Function to fetch data from Google Sheets
        function fetchDataFromSheets(startRow, ignoreCache) {
            let url = '<?php echo admin_url('admin-ajax.php'); ?>?action=idd_calculator_fetch_data_from_sheets';
            
            url += '&start_row=' + startRow;
            
            if (ignoreCache) {
                url += '&ignore_cache=1';
            }

            fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                displayDataInTable(data.values);
            })
            .catch(error => console.error('Error fetching data:', error));
        }

        // Function to display data in table
        function displayDataInTable(data) {
            const tableBody = document.querySelector('#idd-table tbody');
            tableBody.innerHTML = '';
            data.forEach(row => {
                const [name, volume, price, specialMoveCost] = row; 
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td>${name}</td>
                    <td>${volume}</td>
                    <td>${price}</td>
                    <td>${specialMoveCost}</td> <!-- Displaying the new column value -->
                `;
                tableBody.appendChild(newRow);
            });
        }

        // Function to save data to database
        function saveDataToDatabase() {
            fetch('<?php echo admin_url('admin-ajax.php'); ?>?action=idd_calculator_save_data_to_database', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Data saved successfully!');
                } else {
                    alert('Failed to save data: ' + data.message);
                }
            })
            .catch(error => console.error('Error saving data:', error));
        }

        document.getElementById('fetch-data').addEventListener('click', () => {
            fetchDataFromSheets(2, true); 
        });

        document.getElementById('save-data').addEventListener('click', () => {
            saveDataToDatabase();
        });

        window.addEventListener('load', () => {
            fetchDataFromSheets(2, false); 
        });
    </script>
    <?php
}

// AJAX handler to fetch data from Google Sheets
function idd_calculator_fetch_data_from_sheets() {
    $start_row = isset($_GET['start_row']) ? intval($_GET['start_row']) : 1;
    
    $ignore_cache = isset($_GET['ignore_cache']) && $_GET['ignore_cache'] == 1;

    if ($ignore_cache) {
        delete_transient('idd_calculator_google_sheets_data');
    }

    $data = get_transient('idd_calculator_google_sheets_data');

    if (false === $data) {
        // Fetch data from Google Sheets
        $api_key = get_option('idd_calculator_api_key');
        $sheets_id = get_option('idd_calculator_sheets_id');
        $tab_name = get_option('idd_calculator_range');
        $url = "https://sheets.googleapis.com/v4/spreadsheets/$sheets_id/values/$tab_name?majorDimension=ROWS&key=$api_key&range=A$start_row:D"; // Changed range to include the new column

        $response = wp_remote_get($url);

        if (!is_wp_error($response)) {
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);
            if (isset($data['values'])) {
                set_transient('idd_calculator_google_sheets_data', $data, 3600);
            }
        }
    }

    wp_send_json($data);
}
add_action('wp_ajax_idd_calculator_fetch_data_from_sheets', 'idd_calculator_fetch_data_from_sheets');




// Function to save data from Google Sheets to the database
function idd_calculator_save_data_to_database() {
    global $wpdb;

    $api_key = get_option('idd_calculator_api_key');
    $sheets_id = get_option('idd_calculator_sheets_id');
    $range = get_option('idd_calculator_range');
    $url = "https://sheets.googleapis.com/v4/spreadsheets/$sheets_id/values/$range?majorDimension=ROWS&key=$api_key&range=A2:D"; // Changed range to include the new column

    $response = wp_remote_get($url);

    if (!is_wp_error($response)) {
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        if (isset($data['values'])) {
            $table_name = $wpdb->prefix . 'idd_calculator_data'; 

            // Check if table exists, if not, create it
            if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
                $create_table_query = "CREATE TABLE $table_name (
                                        id INT NOT NULL AUTO_INCREMENT,
                                        name VARCHAR(255),
                                        volume INT,
                                        price DECIMAL(10, 2),
                                        specialMoveCost DECIMAL(10, 2),
                                        PRIMARY KEY (id)
                                      )";
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($create_table_query);
            }

            $wpdb->query("TRUNCATE TABLE $table_name");

            foreach ($data['values'] as $row) {
                $name = $row[0];
                $volume = $row[1];
                $price = isset($row[2]) ? $row[2] : get_option('idd_calculator_defult_price'); 
                $specialMoveCost = isset($row[3]) ? $row[3] : 0; // defaulting to 0 if not provided

                $result = $wpdb->insert($table_name, array(
                    'name' => $name,
                    'volume' => $volume,
                    'price' => $price,
                    'specialmovecost' => $specialMoveCost
                ));
                
                if ($result === false) {
                    error_log('Failed to insert row: ' . print_r($row, true)); 
                }
            }
            
            wp_send_json(array('success' => true));
        } else {
            wp_send_json(array('success' => false, 'message' => 'No data fetched from Google Sheets'));
        }
    } else {
        wp_send_json(array('success' => false, 'message' => 'Error fetching data from Google Sheets'));
    }
}

add_action('wp_ajax_idd_calculator_save_data_to_database', 'idd_calculator_save_data_to_database');


// Settings page content
function idd_calculator_settings_page() {
    ?>
    <div class="wrap">
        <h1>IDD Calculator Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('idd_calculator_settings_group'); ?>
            <div class="form-section">
              <section class="settings-section">
              <h2>General Settings</h2>
                <div class="form-group">
                    <label for="idd_calculator_api_key">API Key (Google Sheets, Google Maps):</label>
                    <input type="text" name="idd_calculator_api_key" value="<?php echo get_option('idd_calculator_api_key'); ?>" />
                </div>

                <div class="form-group">
                    <label for="idd_calculator_sheets_id">Sheets ID:</label>
                    <input type="text" name="idd_calculator_sheets_id" value="<?php echo get_option('idd_calculator_sheets_id'); ?>" />
                </div>

                <div class="form-group">
                    <label for="idd_calculator_range">Tab ID:</label>
                    <input type="text" name="idd_calculator_range" value="<?php echo get_option('idd_calculator_range'); ?>" />
                </div>

                <div class="form-group">
                    <label for="idd_calculator_defult_price">Default price Storage_Cost_CF_Month:</label>
                    <input type="text" name="idd_calculator_defult_price" value="<?php echo get_option('idd_calculator_defult_price'); ?>" />
                </div>

                <div class="form-group">
                    <label for="storage_address">Storage Address:</label>
                    <input type="text" name="storage_address" value="<?php echo get_option('storage_address'); ?>" />
                </div>

                <div class="form-group">
                    <label for="defult_price_move_cost_cf">Default price Move_Cost_CF:</label>
                    <input type="text" name="defult_price_move_cost_cf" value="<?php echo get_option('defult_price_move_cost_cf'); ?>" />
                </div>

                <div class="form-group">
                    <label for="first_miles_excluded">First Miles Excluded:</label>
                    <input type="text" name="first_miles_excluded" value="<?php echo get_option('first_miles_excluded'); ?>" />
                </div>

                <div class="form-group">
                    <label for="defult_price_move_cost_mile">Default price Move Cost Mile:</label>
                    <input type="text" name="defult_price_move_cost_mile" value="<?php echo get_option('defult_price_move_cost_mile'); ?>" />
                </div>

                <div class="form-group">
                    <label for="min_cf_move_threshold">Min CF Move Threshold:</label>
                    <input type="text" name="min_cf_move_threshold" value="<?php echo get_option('min_cf_move_threshold'); ?>" />
                </div>

                <div class="form-group">
                    <label for="defult_price_min_cf_move_price">Default price Min CF Move:</label>
                    <input type="text" name="defult_price_min_cf_move_price" value="<?php echo get_option('defult_price_min_cf_move_price'); ?>" />
                </div>

                <div class="form-group">
                    <label for="min_distance_ld">Min Distance LD:</label>
                    <input type="text" name="min_distance_ld" value="<?php echo get_option('min_distance_ld'); ?>" />
                </div>

                <div class="form-group">
                    <label for="stairs_price">Stairs Price:</label>
                    <input type="text" name="stairs_price" value="<?php echo get_option('stairs_price'); ?>" />
                </div>
                                
                <div class="form-group">
                    <label for="calc_section_id">Calc section ID:</label>
                    <input type="text" name="calc_section_id" value="<?php echo !empty(get_option('calc_section_id')) ? get_option('calc_section_id') : 'calcapp'; ?>" class="calc_section_id" />
                </div>
              </section>

              <section class="settings-section">
                <h2>CRM</h2>

                <div class="form-group">
                    <label for="idd_calculator_crm_api_key">CRM Key:</label>
                    <input type="text" name="idd_calculator_crm_api_key" value="<?php echo get_option('idd_calculator_crm_api_key'); ?>" />
                </div>

                <div class="form-group">
                    <label for="crm_feach_data_days">CRM days (fetch data):</label>
                    <input type="text" name="crm_feach_data_days" value="<?php echo get_option('crm_feach_data_days'); ?>" />
                </div>
                </section>
            <section class="settings-section">
                <h2>Datepicker</h2>
                <h3>Views</h3>
                <div class="form-row">
        <div class="form-group">
            <label for="idd_mobile_breakpoint">Datepicker Mobile Breakpoint (px):</label>
            <input type="text" name="idd_mobile_breakpoint" value="<?php echo get_option('idd_mobile_breakpoint'); ?>" />
        </div>

        <div class="form-group">
            <label for="idd_mobile_views">Datepicker Mobile views:</label>
            <input type="text" name="idd_mobile_views" value="<?php echo get_option('idd_mobile_views'); ?>" />
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="idd_tablet_breakpoint">Datepicker Tablet Breakpoint (px):</label>
            <input type="text" name="idd_tablet_breakpoint" value="<?php echo get_option('idd_tablet_breakpoint'); ?>" />
        </div>

        <div class="form-group">
            <label for="idd_tablet_views">Datepicker Tablet views:</label>
            <input type="text" name="idd_tablet_views" value="<?php echo get_option('idd_tablet_views'); ?>" />
        </div>
    </div>

    <div class="form-row">
    <div class="form-group">
        <label for="idd_desktop_views">Datepicker Desktop views:</label>
        <input type="text" name="idd_desktop_views" value="<?php echo get_option('idd_desktop_views'); ?>" />
    </div>
        <div class="form-group">
        </div>
    </div>

    <h3>Rates</h3>

    <div class="form-row">
        <div class="form-group">
            <label for="idd_no_availability_rate">No Availability Rate:</label>
            <input type="text" name="idd_no_availability_rate" value="<?php echo get_option('idd_no_availability_rate'); ?>" />
        </div>
        <div class="form-group">
        </div>
        <div class="form-group">
        </div>
    </div>


    <div class="form-row">
        <div class="form-group">
            <label for="idd_availability_very_cheap_rate_min">Available With Very Cheap Rate (min):</label>
            <input type="text" name="idd_availability_very_cheap_rate_min" value="<?php echo get_option('idd_availability_very_cheap_rate_min'); ?>" />
        </div>
        <div class="form-group">
            <label for="idd_availability_very_cheap_rate_max">Available With Very Cheap Rate (max):</label>
            <input type="text" name="idd_availability_very_cheap_rate_max" value="<?php echo get_option('idd_availability_very_cheap_rate_max'); ?>" />
        </div>
        <div class="form-group">
            <label for="idd_availability_very_cheap_color">Available With Very Cheap Color:</label>
            <input type="color" style="height: 47px;" name="idd_availability_very_cheap_color" value="<?php echo get_option('idd_availability_very_cheap_color'); ?>" />
        </div>
    </div>


    <div class="form-row">
        <div class="form-group">
            <label for="idd_availability_cheap_rate_min">Available With Cheap Rate:</label>
            <input type="text" name="idd_availability_cheap_rate_min" value="<?php echo get_option('idd_availability_cheap_rate_min'); ?>" />
        </div>
        <div class="form-group">
            <label for="idd_availability_cheap_color">Available With Cheap Color:</label>
            <input type="color" style="height: 47px;" name="idd_availability_cheap_color" value="<?php echo get_option('idd_availability_cheap_color'); ?>" />
        </div>
        <div class="form-group">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="idd_availability_medium_rate_min">Available With Medium Rate (min):</label>
            <input type="text" name="idd_availability_medium_rate_min" value="<?php echo get_option('idd_availability_medium_rate_min'); ?>" />
        </div>
        <div class="form-group">
            <label for="idd_availability_medium_rate_max">Available With Medium Rate (max):</label>
            <input type="text" name="idd_availability_medium_rate_max" value="<?php echo get_option('idd_availability_medium_rate_max'); ?>" />
        </div>
        <div class="form-group">
            <label for="idd_availability_medium_color">Available With Medium Color:</label>
            <input type="color" style="height: 47px;" name="idd_availability_medium_color" value="<?php echo get_option('idd_availability_medium_color'); ?>" />
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="idd_availability_expensive_rate_min">Available With Expensive Rate (min):</label>
            <input type="text" name="idd_availability_expensive_rate_min" value="<?php echo get_option('idd_availability_expensive_rate_min'); ?>" />
        </div>
        <div class="form-group">
            <label for="idd_availability_expensive_rate_max">Available With Expensive Rate (max):</label>
            <input type="text" name="idd_availability_expensive_rate_max" value="<?php echo get_option('idd_availability_expensive_rate_max'); ?>" />
        </div>
        <div class="form-group">
            <label for="idd_availability_expensive_color">Available With Expensive Color:</label>
            <input type="color" style="height: 47px;" name="idd_availability_expensive_color" value="<?php echo get_option('idd_availability_expensive_color'); ?>" />
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="idd_very_expensive_cheap_color">Available With Very Expensive Color:</label>
            <input type="color" style="height: 47px;" name="idd_very_expensive_cheap_color" value="<?php echo get_option('idd_very_expensive_cheap_color'); ?>" />
        </div>
        <div class="form-group">
            <label for="idd_min_cheap_color">Available With Min Price Color:</label>
            <input type="color" style="height: 47px;" name="idd_min_cheap_color" value="<?php echo get_option('idd_min_cheap_color'); ?>" />
        </div>
        <div class="form-group">
        </div>
    </div>

    </section>
    <section class="settings-section">
                <h2>Notifications</h2>

                <div class="form-group">
                    <label for="special_move_cost_notification">Special Move Cost notification:</label>
                    <input type="text" name="special_move_cost_notification" value="<?php echo get_option('special_move_cost_notification'); ?>" />
                </div>

                <div class="form-group">
                    <label for="exceptions_for_move_cost_notification">Exceptions for Move Cost notification:</label>
                    <input type="text" name="exceptions_for_move_cost_notification" value="<?php echo get_option('exceptions_for_move_cost_notification'); ?>" />
                </div>

                <div class="form-group">
                    <label for="max_distace_miles_notification">Max Distance Miles notification:</label>
                    <input type="text" name="max_distace_miles_notification" value="<?php echo get_option('max_distace_miles_notification'); ?>" />
                </div>

                <div class="form-group">
                    <label for="inster_states_notification">Interstate notification:</label>
                    <input type="text" name="inster_states_notification" value="<?php echo get_option('inster_states_notification'); ?>" />
                </div>

                <div class="form-group">
                    <label for="special_items_notification">User added special items notification:</label>
                    <input type="text" name="special_items_notification" value="<?php echo get_option('special_items_notification'); ?>" />
                </div>

                <div class="form-group">
                    <label for="stairs_notification">Stairs notification:</label>
                    <input type="text" name="stairs_notification" value="<?php echo get_option('stairs_notification'); ?>" />
                </div>
                </section>
                <section class="settings-section">
                <h2>Style</h2>

                <h3>Label Style</h3>
                <div class="form-row">
                <div class="form-group">
                    <label for="idd_lable_font_size">Label font size:</label>
                    <input type="text" name="idd_lable_font_size" value="<?php echo !empty(get_option('idd_lable_font_size')) ? get_option('idd_lable_font_size') : '15px'; ?>" class="idd_lable_font_size" />
                </div>

                <div class="form-group">
                    <label for="idd_title_font_size">Title font size:</label>
                    <input type="text" name="idd_title_font_size" value="<?php echo get_option('idd_title_font_size'); ?>" class="idd_title_font_size" />
                </div>

                <div class="form-group">
                    <label for="idd_lable_color">Color for label:</label>
                    <input type="color" style="height: 47px;"name="idd_lable_color" value="<?php echo get_option('idd_lable_color'); ?>" class="idd_lable_color" />
                </div>
               </div>


                <h3>Inputs Style</h3>
                <div class="form-row">
                <div class="form-group">
                    <label for="idd_margin_con">Margin between inputs:</label>
                    <input type="text" name="idd_margin_con" value="<?php echo !empty(get_option('idd_margin_con')) ? get_option('idd_margin_con') : '5px'; ?>" class="idd_margin_con" />
                </div>
                <div class="form-group">
        <label for="idd_height_inputs">Height inputs:</label>
        <input type="text" name="idd_height_inputs" value="<?php echo !empty(get_option('idd_height_inputs')) ? get_option('idd_height_inputs') : '4.5rem'; ?>" class="idd_height_inputs" />
    </div>

                <div class="form-group">
                    <label for="idd_inputs_border_color">Color for inputs border:</label>
                    <input type="color" style="height: 47px;" name="idd_inputs_border_color" value="<?php echo get_option('idd_inputs_border_color'); ?>" class="idd_inputs_border_color" />
                </div>


                    <div class="form-group">
        <label for="idd_inputs_border_bg_color">Background color for inputs:</label>
        <input type="color" style="height: 47px;" name="idd_inputs_border_bg_color" value="<?php echo get_option('idd_inputs_border_bg_color'); ?>" class="idd_inputs_border_bg_color" />
    </div>


    </div>


    <h3>Table Style</h3>
    <div class="form-row">
    <div class="form-group">
        <label for="idd_table_text_font">Table text font size:</label>
        <input type="text" name="idd_table_text_font" value="<?php echo get_option('idd_table_text_font'); ?>" class="idd_table_text_font" />
    </div>

    <div class="form-group">
        <label for="idd_table_hr_font">Table hr font size:</label>
        <input type="text" name="idd_table_hr_font" value="<?php echo get_option('idd_table_hr_font'); ?>" class="idd_table_hr_font" />
    </div>

    <div class="form-group">
        <label for="idd_table_background_color">Color for table background:</label>
        <input type="color" style="height: 47px;" name="idd_table_background_color" value="<?php echo !empty(get_option('idd_table_background_color')) ? get_option('idd_table_background_color') : '#fff'; ?>" class="idd_table_background_color" />
    </div>

    <div class="form-group">
        <label for="idd_header_table_background_color">Color header table background:</label>
        <input type="color" style="height: 47px;" name="idd_header_table_background_color" value="<?php echo get_option('idd_header_table_background_color'); ?>" class="idd_header_table_background_color" />
    </div>

    <div class="form-group">
        <label for="idd_table_border_color">Color for table border:</label>
        <input type="color" style="height: 47px;" name="idd_table_border_color" value="<?php echo get_option('idd_table_border_color'); ?>" class="idd_table_border_color" />
    </div>

    <div class="form-group">
        <label for="idd_table_text_color">Color for table text:</label>
        <input type="color" style="height: 47px;" name="idd_table_text_color" value="<?php echo get_option('idd_table_text_color'); ?>" class="idd_table_text_color" />
    </div>



    <div class="form-group">
        <label for="idd_table_hr_color">Color for table hr:</label>
        <input type="color" style="height: 47px;" name="idd_table_hr_color" value="<?php echo get_option('idd_table_hr_color'); ?>" class="idd_table_hr_color" />
    </div>
    </div>










    <h3>Quantity button Style</h3>
    <div class="form-row">
    <div class="form-group">
        <label for="idd_background_quantity_btn">Background color for Quantity buttons:</label>
        <input type="color" style="height: 47px;" name="idd_background_quantity_btn" value="<?php echo get_option('idd_background_quantity_btn'); ?>" class="idd_background_quantity_btn" />
    </div>

    <div class="form-group">
        <label for="idd_color_quantity_btn">+ / - color for Quantity buttons:</label>
        <input type="color" style="height: 47px;" name="idd_color_quantity_btn" value="<?php echo get_option('idd_color_quantity_btn'); ?>" class="idd_color_quantity_btn" />
    </div>
    </div>



    <h3>Notifications Style</h3>
    <div class="form-row">
    <div class="form-group">
        <label for="idd_font_size_notification">Notifications font size:</label>
        <input type="text" name="idd_font_size_notification" value="<?php echo get_option('idd_font_size_notification'); ?>" class="idd_font_size_notification" />
    </div>
    <div class="form-group">
        <label for="idd_notification_color">Color for notifications:</label>
        <input type="color" style="height: 47px;" name="idd_notification_color" value="<?php echo !empty(get_option('idd_notification_color')) ? get_option('idd_notification_color') : '#FF5733'; ?>" class="idd_notification_color" />
    </div>
    </div>

    <h3>Map Style</h3>
    <div class="form-row">
    <div class="form-group">
                    <label for="idd_price_font_size">Mils font size:</label>
                    <input type="text" name="idd_price_font_size" value="<?php echo get_option('idd_price_font_size'); ?>" class="idd_price_font_size" />
                </div>

                <div class="form-group">
                    <label for="idd_price_color">Mils color:</label>
                    <input type="color" style="height: 47px;" name="idd_price_color" value="<?php echo get_option('idd_price_color'); ?>" class="idd_price_color" />
                </div>  
    <div class="form-group">
        <label for="map_poly_line_color">Map poly line color:</label>
        <input type="color" style="height: 47px;" name="map_poly_line_color" value="<?php echo get_option('map_poly_line_color'); ?>" class="map_poly_line_color" />
    </div>
    </div>

    <h3>Steps Style</h3>
    <div class="form-row">
    <div class="form-group">
        <label for="steps_color">Steps color:</label>
        <input type="color" style="height: 47px;" name="steps_color" value="<?php echo get_option('steps_color'); ?>" class="steps_color" />
    </div>
    </div>
    </section>
            </div>
<style>


.form-group label {
    display: block;
    margin-bottom: 5px;
}

.form-section {
    display: flex;
    flex-direction: column;
}

.form-row {
    display: flex;
    gap: 20px; 
    margin-bottom: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.settings-container {
  max-width: 900px;
  margin: 40px auto;
  background-color: #fff;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.page-title {
  text-align: center;
  font-size: 2rem;
  margin-bottom: 20px;
  color: #444;
}

.settings-section {
  margin-bottom: 40px;
  background: #fcfcfc;
    padding: 20px;
    border-radius: 15px;
}

.settings-section h2 {
  font-size: 1.5rem;
  color: #2c3e50;
  margin-bottom: 20px;
  border-bottom: 2px solid #ecf0f1;
  padding-bottom: 10px;
}

.form-group{
  margin-bottom: 15px;
}

.form-grouplabel {
  display: block;
  font-weight: bold;
  margin-bottom: 5px;
  color: #34495e;
}

.form-group input[type="text"],
.form-group input[type="url"],
.form-group input[type="email"],
.form-group textarea,
.form-group select {
  width: 100%;
  padding: 10px;
  border-radius: 4px;
  border: 1px solid #ccc;
  margin-bottom: 10px;
  font-size: 1rem;
}

.form-group input[type="checkbox"] {
  margin-right: 10px;
}

.form-group textarea {
  height: 100px;
  resize: none;
}

.save-button {
  display: block;
  width: 100%;
  padding: 12px;
  background-color: #27ae60;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1.2rem;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.save-button:hover {
  background-color: #2ecc71;
}
</style>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}


// Register settings
function idd_calculator_register_settings() {
    register_setting('idd_calculator_settings_group', 'idd_calculator_api_key');

    register_setting('idd_calculator_settings_group', 'idd_calculator_crm_api_key');
    register_setting('idd_calculator_settings_group', 'crm_feach_data_days');


    register_setting('idd_calculator_settings_group', 'idd_mobile_breakpoint');
    register_setting('idd_calculator_settings_group', 'idd_mobile_views');
    register_setting('idd_calculator_settings_group', 'idd_tablet_breakpoint');
    register_setting('idd_calculator_settings_group', 'idd_tablet_views');
    register_setting('idd_calculator_settings_group', 'idd_desktop_views');


    register_setting('idd_calculator_settings_group', 'idd_no_availability_rate');
    register_setting('idd_calculator_settings_group', 'idd_availability_very_cheap_rate_min');
    register_setting('idd_calculator_settings_group', 'idd_availability_very_cheap_rate_max');
    register_setting('idd_calculator_settings_group', 'idd_availability_very_cheap_color');
    register_setting('idd_calculator_settings_group', 'idd_min_cheap_color');

    
    register_setting('idd_calculator_settings_group', 'idd_availability_cheap_rate_min');
    register_setting('idd_calculator_settings_group', 'idd_availability_cheap_color');
    register_setting('idd_calculator_settings_group', 'idd_availability_medium_rate_min');
    register_setting('idd_calculator_settings_group', 'idd_availability_medium_rate_max');
    register_setting('idd_calculator_settings_group', 'idd_availability_medium_color');
    register_setting('idd_calculator_settings_group', 'idd_availability_expensive_rate_min');
    register_setting('idd_calculator_settings_group', 'idd_availability_expensive_rate_max');
    register_setting('idd_calculator_settings_group', 'idd_availability_expensive_color');
    register_setting('idd_calculator_settings_group', 'idd_very_expensive_cheap_color');



    
    register_setting('idd_calculator_settings_group', 'idd_calculator_sheets_id');
    register_setting('idd_calculator_settings_group', 'idd_calculator_range');
    register_setting('idd_calculator_settings_group', 'idd_calculator_defult_price');
    register_setting('idd_calculator_settings_group', 'storage_address');
    register_setting('idd_calculator_settings_group', 'defult_price_move_cost_cf');
    register_setting('idd_calculator_settings_group', 'first_miles_excluded');
    register_setting('idd_calculator_settings_group', 'defult_price_move_cost_mile');
    register_setting('idd_calculator_settings_group', 'min_cf_move_threshold');
    register_setting('idd_calculator_settings_group', 'defult_price_min_cf_move_price');
    register_setting('idd_calculator_settings_group', 'min_distance_ld');
    register_setting('idd_calculator_settings_group', 'stairs_price');
    register_setting('idd_calculator_settings_group', 'redirect_url');


    register_setting('idd_calculator_settings_group', 'special_move_cost_notification');
    register_setting('idd_calculator_settings_group', 'exceptions_for_move_cost_notification');
    register_setting('idd_calculator_settings_group', 'max_distace_miles_notification');
    register_setting('idd_calculator_settings_group', 'inster_states_notification');
    register_setting('idd_calculator_settings_group', 'special_items_notification');
    register_setting('idd_calculator_settings_group', 'stairs_notification');

    

    register_setting('idd_calculator_settings_group', 'calc_section_id');
    register_setting('idd_calculator_settings_group', 'idd_margin_con');
    register_setting('idd_calculator_settings_group', 'idd_price_font_size');
    register_setting('idd_calculator_settings_group', 'idd_price_color');
    register_setting('idd_calculator_settings_group', 'idd_lable_color');
    register_setting('idd_calculator_settings_group', 'idd_lable_font_size');
    register_setting('idd_calculator_settings_group', 'idd_inputs_border_color');
    register_setting('idd_calculator_settings_group', 'idd_inputs_border_bg_color');
    register_setting('idd_calculator_settings_group', 'idd_height_inputs');
    register_setting('idd_calculator_settings_group', 'idd_background_quantity_btn');
    register_setting('idd_calculator_settings_group', 'idd_color_quantity_btn');
    register_setting('idd_calculator_settings_group', 'idd_table_background_color');
    register_setting('idd_calculator_settings_group', 'idd_header_table_background_color');
    register_setting('idd_calculator_settings_group', 'idd_table_border_color');
    register_setting('idd_calculator_settings_group', 'map_poly_line_color');
    register_setting('idd_calculator_settings_group', 'steps_color');

    
    
    register_setting('idd_calculator_settings_group', 'idd_table_text_color');
    register_setting('idd_calculator_settings_group', 'idd_table_text_font');
    register_setting('idd_calculator_settings_group', 'idd_table_hr_color');
    register_setting('idd_calculator_settings_group', 'idd_table_hr_font');
    register_setting('idd_calculator_settings_group', 'idd_notification_color');
    register_setting('idd_calculator_settings_group', 'idd_font_size_notification');

}
add_action('admin_init', 'idd_calculator_register_settings');


function get_rates_proxy() {
    //$api_key = 'dwDeeeZU3AQLiDhcekDI6bZxqczO7hH8w5PDytCH87XkZShf1YKa0Tj41bsv';

    $api_crm_key = get_option('idd_calculator_crm_api_key');
    $crm_feach_data_days = get_option('crm_feach_data_days');
    //$crm_end_date_range = get_option('crm_end_date_range');

    //$api_url = 'https://www.perfectmovingrates.com/api/v1/ratesApiAuth?from=2024-09-01&to=2025-03-01';
    //$api_url = "https://www.perfectmovingrates.com/api/v1/ratesApiAuth?from=$crm_start_date_range&to=$crm_end_date_range";

    $start_date = date('Y-m-d'); 
    $end_date = date('Y-m-d', strtotime("+$crm_feach_data_days days")); 
    $api_url = 'https://www.perfectmovingrates.com/api/v1/ratesApiAuth?from=' . $start_date . '&to=' . $end_date;

    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'x-api-key: ' . $api_crm_key
    ]);

    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode == 200) {
        echo $response;
    } else {
        http_response_code($httpcode);
        echo json_encode(["error" => "Failed to fetch rates"]);
    }
    exit;
}

add_action('wp_ajax_get_rates_proxy', 'get_rates_proxy');
add_action('wp_ajax_nopriv_get_rates_proxy', 'get_rates_proxy');


// Shortcode to display calculator form
function idd_calculator_product_table_shortcode($atts) {

     // Parse shor tcode attributes
     $atts = shortcode_atts(
        array(
            'type' => 'storage', // Set default value to 'no'
        ),
        $atts,
        'idd_calculator'
    );

    ob_start();
    global $wpdb;
    $table_name = $wpdb->prefix . 'idd_calculator_data'; // Get the table name with the appropriate WordPress prefix
    $data = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
?>

<div id="loading-page">
    <div class="spinner"></div>
</div>

<div class="idd-product-table" id="main-content">
    <div class="stepper-wrapper" id="bar-steps">
        <div id="bar-step-one" class="stepper-item">
            <div class="step-counter" id="btn-bar-step-one">1</div>
            <div class="step-name" id="btn-bar-step-one">Contact</div>
        </div>
        <div id="bar-step-two" class="stepper-item">
            <div class="step-counter" id="btn-bar-step-two">2</div>
            <div class="step-name" id="btn-bar-step-two">Inventory</div>
        </div>
        <div id="bar-step-three" class="stepper-item">
            <div class="step-counter" id="btn-bar-step-three">3</div>
            <div class="step-name" id="btn-bar-step-three">Schedule</div>
        </div>
        <div id="bar-step-four" class="stepper-item">
            <div class="step-counter" id="btn-bar-step-four">4</div>
            <div class="step-name" id="btn-bar-step-four">Summary</div>
        </div>
    </div>

    <section id="step-one">
        <div id="user-full-name">
            <div id="user-first-name">
                <label class="cal_lable">First Name</label>
                <input type="text" name="first-name" id="first-name" required>
            </div>
            <div id="date-last-name">
                <label class="cal_lable">Last Name</label>
                <input type="text" name="last-name" id="last-name" required>
            </div>
        </div>
        <div id="user-email-phone">
            <div id="user-phone-container">
                <label class="cal_lable">Phone</label>
                <input type="tel" id="user-phone" name="user-phone" maxlength="14" minlength="14" required>
            </div>
            <div id="user-email-container">
                <label class="cal_lable">Email</label>
                <input type="email" name="user-email" id="user-email" required>
                <div id="email-error" class="error-message" style="display: none; color:#a71d1c;">Please enter a valid email address</div>
                <div id="input-error" class="error-message" style="display: none; color:#a71d1c;">All fields are required</div>
            </div>
        </div>
        <input id="next-step-one" class="wpcf7-submit" style="margin-top: 10px;" type="submit" value="Next">
    </section>

    <section id="step-two">
        <div class="search-container">
            <label class="cal_lable">Your items list</label>
            <div style="display: flex;">
                <div style="width: 100%;"><input type="text" id="search-input" placeholder="Search by typing items names and select from the list"></div>
                <div style="align-content: center; position: absolute; right: 5px; transform: translate(-60%, 85%);">
                    <span id="clear-search" class="clear-search-icon">&#x2716;</span>
                </div>
            </div>
            <div class="table_div" id="table_div" style="display: none;">
                <table class="wp-list-table widefat striped" id="idd-table" style="display: none; margin-top: 0px !important; margin-bottom: 0px !important;">
                    <thead>
                        <tr>
                            <th style="text-align: left;">Name</th>
                            <th style="display: none;">Volume</th>
                            <th style="display: none;">Price</th>
                            <th style="display: none;">specialMoveCost</th>
                            <th style="text-align: left; width: 90px;">Quantity</th>
                        </tr>
                    </thead>
                    <tbody id="idd-product-table-body">
                        <?php foreach ($data as $row) : ?>
                            <tr>
                                <td style="text-align: left;"><?php echo $row['name']; ?></td>
                                <td style="display: none;"><?php echo $row['volume']; ?></td>
                                <td style="display: none;"><?php echo $row['price']; ?></td>
                                <td style="display: none;"><?php echo $row['specialMoveCost']; ?></td>
                                <td>
                                    <div class="quantity buttons_added" style="display: flex; float: right;">
                                        <input type="button" value="-" class="minus" onclick="decreaseQuantity(this)">
                                        <input style="width: 50px; height: 30px;" type="number" class="quantity-input" value="0" min="0" max="999" onchange="updateCalculator(this)">
                                        <input type="button" value="+" class="plus" onclick="increaseQuantity(this)">
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <p class="cal_lable">If you did not find the product in the list, you can add it by <a id="add-product-link">clicking here</a></p>
            <div id="add-product-container" style="display: none;">
                <input type="text" id="new-product-name" placeholder="Enter product name">
                <button id="add-product-btn">Add</button>
            </div>

            <div id="pro-cal-container">
                <label id="cal-table-title" class="cal_lable" style="margin-top:50px; display: none;">Your list</label>
                <table class="wp-list-table widefat striped" id="idd-calculator" style="display: none; margin-bottom: 0px !important; margin-top: 0px !important;">
                    <thead>
                        <tr>
                            <th style="text-align: left;">Name</th>
                            <th style="display: none;">Volume</th>
                            <th style="display: none;">Price</th>
                            <th style="display: none;">specialMoveCost</th>
                            <th style="text-align: left; width: 90px;">Quantity</th>
                            <th style="display: none;">Price</th>
                        </tr>
                    </thead>
                    <tbody id="idd-calculator-body"></tbody>
                </table>
            </div>

            <div id="quantity-container" style="display: none; align-items: center;">
                <div><label style="width: max-content !important;">Quantity:</label></div>
                <div><input type="text" id="sum-quantity" class="sumquantity" name="sum-quantity" style=" border: unset; padding: 0px; padding-left: 5px !important; background: unset; height: unset !important;" readonly></div>
            </div>
            <div id="volume-container" style="display: none; align-items: center;">
                <div><label style="width: max-content !important;">Total Volume:</label></div>
                <div><input type="text" id="sum-volume" class="sumvolume" name="sum-volume" style="border: unset; padding: 0px; padding-left: 5px !important; background: unset; height: unset !important;" readonly></div>
            </div>

            <div id="pro-cal-container">
                <label class="special_cal_lable" id="cal-special-table-title" style="display: none; margin-top:50px;">Your Special Items list</label>
                <table class="wp-list-table widefat striped" id="special-items-table" style="display: none; margin-bottom: 0px !important; margin-top: 0px !important;">
                    <thead>
                        <tr>
                            <th style="text-align: left;">Name</th>
                            <th style="text-align: left; width: 90px;">Quantity</th>
                        </tr>
                    </thead>
                    <tbody id="special-items-table-body"></tbody>
                </table>
            </div>

            <div id="quantity-special-container" style="display: none; align-items: center;">
                <div><label style="width: max-content !important;">Quantity:</label></div>
                <div><input type="text" id="sum-special-quantity" class="sumspecialquantity" name="sum-special-quantity" style=" border: unset; padding: 0px; padding-left: 5px !important; background: unset; height: unset !important;" readonly></div>
            </div>

            <div class="btns-next-back">
                <div class="btns-back">
                    <input id="next-step-two" class="wpcf7-submit next-step" style="margin-top: 10px;" type="submit" value="Next">
                </div>
                <div class="btns-next">
                    <input id="back-step-two" class="wpcf7-submit-back" style="margin-top: 10px;" type="submit" value="Back">
                </div>
            </div>
            <div id="checkTables-error" class="error-message" style="display: none; color:#a71d1c;">You must add at least one product</div>
        </div>
    </section>

    <section id="step-three">
        <div id="address-container">
            <label class="cal_lable">Pickup Address</label>
            <input type="text" name="origin_address" id="origin_address" required>
            <div class="address-access-apt-floor-container">
                <div class="address-access-container">
                    <label for="origin-address-access" class="cal_lable">Access</label>
                    <select name="origin-address-access" id="origin-address-access" style="width: 100%;"  onchange="updateTextInput()">
                        <option value="Elevator">Elevator</option>
                        <option value="Freight Elevator">Freight Elevator</option>
                        <option value="Stairs">Stairs</option>
                        <option value="Ground">Ground</option>
                        <option value="Not sure">Not sure</option>
                    </select>
                </div>
                <div class="address-apt-floor-container">
                    <div class="address-floor-container">
                        <label class="cal_lable">Floor</label>
                        <input type="number" name="origin-address-floor" id="origin-address-floor" class="form-num-input" style="width: 100%;" placeholder="Enter a number">
                    </div>
                    <div class="address-apt-container">
                        <label class="cal_lable">Apt</label>
                        <input type="text" name="origin-address-apt" id="origin-address-apt" class="form-num-input" style="width: 100%;" placeholder="Enter a number">
                    </div>
                </div>
            </div>

        </div>

        <div id="address-checkbox-container">
            <input type="checkbox" id="use-destination-address" name="use-destination-address" style="height: 13px !important">
            <label for="use-destination-address">I don't need storage (only moving)</label>
        </div>

        <div id="address-destination-container" style="display: none; margin-top: 5px;">
            <label class="cal_lable">Destination Address</label>
            <input type="text" name="destination_address" id="destination_address">
            <div class="address-access-apt-floor-container">
                <div class="address-access-container">
                    <label for="destination-address-access" class="cal_lable">Access</label>
                    <select name="destination-address-access" id="destination-address-access" style="width: 100%;"  onchange="updateTextInput()">
                        <option value="Elevator">Elevator</option>
                        <option value="Freight Elevator">Freight Elevator</option>
                        <option value="Stairs">Stairs</option>
                        <option value="Ground">Ground</option>
                        <option value="Not sure">Not sure</option>
                    </select>
                </div>
                <div class="address-apt-floor-container">
                    <div class="address-floor-container">
                        <label class="cal_lable">Floor</label>
                        <input type="number" name="destination-address-floor" id="destination-address-floor" class="form-num-input" style="width: 100%;" placeholder="Enter a number">
                    </div>
                    <div class="address-apt-container">
                        <label class="cal_lable">Apt</label>
                        <input type="text" name="destination-address-apt" id="destination-address-apt" class="form-num-input" style="width: 100%;" placeholder="Enter a number">
                    </div>
                </div>
            </div>
        </div>

        <label class="cal_lable">Additional Info:</label>
        <div id="address-Additional-checkbox-container">
            <div>
                <input type="checkbox" id="use-extra-up-destination-address" name="use-extra-up-destination-address" style="height: 13px !important">
                <label for="use-extra-up-destination-address">EXTRA PICK UP</label>
            </div>
            <div>
                <input type="checkbox" id="use-extra-off-destination-address" name="use-extra-off-destination-address" style="height: 13px !important">
                <label for="use-extra-off-destination-address">EXTRA DROP OFF</label>
            </div>
        </div>

        <div id="address-Additional-extra-up-destination-container" style="display: none; margin-top: 5px;">
            <label class="cal_lable">EXTRA PICK UP</label>
            <input type="text" name="destination_up_address" id="destination_up_address">
            <div class="address-access-apt-floor-container">
                <div class="address-access-container">
                    <label for="destination-up-address-access" class="cal_lable">Access</label>
                    <select name="destination-up-address-access" id="destination-up-address-access" style="width: 100%;"  onchange="updateTextInput()">
                        <option value="Elevator">Elevator</option>
                        <option value="Freight Elevator">Freight Elevator</option>
                        <option value="Stairs">Stairs</option>
                        <option value="Ground">Ground</option>
                        <option value="Not sure">Not sure</option>
                    </select>
                </div>
                <div class="address-apt-floor-container">
                    <div class="address-floor-container">
                        <label class="cal_lable">Floor</label>
                        <input type="number" name="destination-up-address-floor" id="destination-up-address-floor" class="form-num-input" style="width: 100%;" placeholder="Enter a number">
                    </div>
                    <div class="address-apt-container">
                        <label class="cal_lable">Apt</label>
                        <input type="text" name="destination-up-address-apt" id="destination-up-address-apt" class="form-num-input" style="width: 100%;" placeholder="Enter a number">
                    </div>
                </div>
            </div>
        </div>

        <div id="address-Additional-extra-off-destination-container" style="display: none; margin-top: 5px;">
            <label class="cal_lable">EXTRA DROP OFF</label>
            <input type="text" name="destination_off_address" id="destination_off_address">
            <div class="address-access-apt-floor-container">
                <div class="address-access-container">
                    <label for="destination-off-address-access" class="cal_lable">Access</label>
                    <select name="destination-off-address-access" id="destination-off-address-access" style="width: 100%;"  onchange="updateTextInput()">
                        <option value="Elevator">Elevator</option>
                        <option value="Freight Elevator">Freight Elevator</option>
                        <option value="Stairs">Stairs</option>
                        <option value="Ground">Ground</option>
                        <option value="Not sure">Not sure</option>
                    </select>
                </div>
                <div class="address-apt-floor-container">
                    <div class="address-floor-container">
                        <label class="cal_lable">Floor</label>
                        <input type="number" name="destination-off-address-floor" id="destination-off-address-floor" class="form-num-input" style="width: 100%;" placeholder="Enter a number">
                    </div>
                    <div class="address-apt-container">
                        <label class="cal_lable">Apt</label>
                        <input type="text" name="destination-off-address-apt" id="destination-off-address-apt" class="form-num-input" style="width: 100%;" placeholder="Enter a number">
                    </div>
                </div>
            </div>
        </div>

        <div id="map" style="display: none; height: 400px;"></div>

        <div id="mils-container" style="display: none; align-items: center;">
            <div><label style="width: max-content !important;">Distance:</label></div>
            <div><input type="text" id="mils" name="mils" style=" border: unset; padding: 0px; padding-left: 5px !important; background: unset; height: unset !important;" readonly></div>
        </div>

        <div class="btns-next-back">
            <div class="btns-back">
                <input id="next-step-three" class="wpcf7-submit next-step" style="margin-top: 10px;" type="submit" value="Next">
            </div>
            <div class="btns-next">
                <input id="back-step-three" class="wpcf7-submit-back" style="margin-top: 10px;" type="submit" value="Back">
            </div>
        </div>

        <div id="input-error-step-two" class="error-message" style="display: none; color: #a71d1c;">
            Pickup Address and Pickup Date and Pickup Time fields are required!
        </div>
    </section>

    <section id="step-four">
        <div id="date-time-container" class="date-time-container">
            <div class="timedatepopup">
                <div id="date-container" class="date-container">
                    <label class="cal_lable">Pickup Date</label>
                    <div id="pickup_date" style="width: 100%;" placeholder="Select pick up date" required></div>
                </div>
                <div class="time-container" id="time-container" style="display:none;">
                    <label class="cal_lable">Pickup Time</label>
                    <div class="timepopup">
                        <select name="pickup_time" id="pickup_time" style="display: none; width: 100%;" required>
                            <option value="">Select pickup time</option>
                            <!-- Options will be populated here based on the selected date -->
                        </select>
                        <div id="pickup_time_radio"></div>
                        <div class="changethedate">
                            <a href="javascript:void(0);" id="back-to-date-btn" style="display: inline-block; color: black; width: fit-content; text-decoration: none; border-radius: 5px; margin-top: 20px;">Change Date</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="btns-next-back">
            <div class="btns-back">
                <input id="submit-step" class="wpcf7-submit" style="margin-top: 10px;" type="submit" value="Next">
            </div>
            <div class="btns-next">
                <input id="back-step-four" class="wpcf7-submit-back" style="margin-top: 10px;" type="submit" value="Back">
            </div>
        </div>

        <div style="margin-top: 10px;">Please confirm all info is correct before proceeding to the next step.</div>

        <div style="display: none;">
            <div id="table-content-container" style="align-items: center;">
                <div><label style="width: max-content !important;">Your items:</label></div>
                <div><textarea id="calculator-table-content" name="calculator-table-content" style="display: none; height: unset !important;" readonly></textarea></div>
                <div id="div-calculator-table-content" class="div-calculator-table-content"></div>
            </div>

            <div id="special-table-content-container" style="display: none; align-items: center;">
                <div><label style="width: max-content !important;">Your special items:</label></div>
                <div><textarea id="calculator-special-table-content" name="calculator-special-table-content" style="display: none; height: unset !important;" readonly></textarea></div>
                <div id="div-calculator-special-table-content" class="div-calculator-special-table-content"></div>
            </div>

            <div id="mapView" style="display: none; height: 400px; margin-top:50px;"></div>

            <div id="pickup-date-container" style="display: none; align-items: center;">
                <div><label style="width: max-content !important;">Pickup Date:</label></div>
                <div><input type="text" id="pickup-date-view" name="pickup-date-view" style=" border: unset; padding: 0px; padding-left: 5px !important; background: unset; height: unset !important;" readonly></div>
            </div>

            <div id="pickup-time-container" style="display: none; align-items: center;">
                <div><label style="width: max-content !important;">Pickup Time:</label></div>
                <div><input type="text" id="pickup-time-view" name="pickup-time-view" style=" border: unset; padding: 0px; padding-left: 5px !important; background: unset; height: unset !important;" readonly></div>
            </div>

            <div id="quantity-specialandregular-container" style="display: none; align-items: center;">
                <div><label style="width: max-content !important;">Total quantity:</label></div>
                <div><input type="text" id="SumSpecialAndRegularQuantity" class="SumSpecialAndRegularQuantity" name="SumSpecialAndRegularQuantity" style=" border: unset; padding: 0px; padding-left: 5px !important; background: unset; height: unset !important;" readonly></div>
            </div>

            <ul class="price_bullets" style="margin-left: 15px;">
                <li id="sum-q-container" style="display: none; align-items: center;">
                    <div style="align-items: center; display: flex;">
                        <div><label style="width: max-content !important;">Quantity:</label></div>
                        <div><input type="text" id="sum-CfAndQuantity" class="sum-CfAndQuantity" name="sum-CfAndQuantity" style=" border: unset; padding: 0px; padding-left: 5px !important; background: unset; height: unset !important;" readonly></div>
                    </div>
                </li>
                <li id="special-q" style="display: none; align-items: center;">
                    <div style="align-items: center; display: flex;">
                        <div><label style="width: max-content !important;">Quantity:</label></div>
                        <div><input type="text" id="sum-totalspecial-quantity" class="sumspecialquantity" name="sum-totalspecial-quantity" style=" border: unset; padding: 0px; padding-left: 5px !important; background: unset; height: unset !important;" readonly></div>
                    </div>
                </li>
            </ul>

            <div id="total-storageandmove-price" style="display: none; align-items: center;">
                <div><label style="width: max-content !important;">Estimated Cost:</label></div>
                <div><input type="text" id="total-move-storage-price" name="total-price" style="border: unset; padding: 0px; padding-left: 5px !important; background: unset; height: unset !important;" readonly></div>
            </div>

            <ul class="price_bullets" style="margin-left: 15px;">
                <li id="sum-container" style="display: none; align-items: center;">
                    <div style="align-items: center; display: flex;">
                        <div><label style="width: max-content !important;">Estimated Storage Cost:</label></div>
                        <div><input type="text" id="sum" name="storage-price" style="border: unset; padding: 0px; padding-left: 5px !important; background: unset; height: unset !important;" readonly></div>
                    </div>
                </li>
                <li id="move-price-container" style="display: none; align-items: center;">
                    <div style="align-items: center; display: flex;">
                        <div><label style="width: max-content !important;">Estimated Move Cost:</label></div>
                        <div><input type="text" id="move-price" name="move-price" style="border: unset; padding: 0px; padding-left: 5px !important; background: unset; height: unset !important;" readonly></div>
                    </div>
                </li>
                <li id="stairs-price-container" style="display: none; align-items: center;">
                    <div style="align-items: center; display: flex;">
                        <div><label style="width: max-content !important;">EXTRA Move Cost:</label></div>
                        <div><input type="text" id="stairs-price-all" name="stairs-price-all" style="border: unset; padding: 0px; padding-left: 5px !important; background: unset; height: unset !important;" readonly></div>
                        <div><input type="text" id="stairs-price-all-view" name="stairs-price-all-view" style="border: unset; padding: 0px; padding-left: 5px !important; background: unset; height: unset !important;" readonly></div>
                    </div>
                </li>
            </ul>


            <input type="text" name="email-destination-up-address-access" id="email-destination-up-address-access" />
            <input type="text" name="email-destination-off-address-access" id="email-destination-off-address-access" />
            <input type="text" name="email-origin-address-access" id="email-origin-address-access" />
            <input type="text" name="email-destination-address-access" id="email-destination-address-access" />


            <input type="text" name="email-special_move_cost_notification" id="email-special_move_cost_notification" />
            <input type="text" name="email-exceptions_for_move_cost_notification" id="email-exceptions_for_move_cost_notification" />
            <input type="text" name="email-max_distace_miles_notification" id="email-max_distace_miles_notification" />
            <input type="text" name="email-inster_states_notification" id="email-inster_states_notification" />
            <input type="text" name="email-special_items_notification" id="email-special_items_notification" />
            <input type="text" name="email-stairs_notification" id="email-stairs_notification" />

        </div>
    </section>
    

<div class="notification-button" id="notification-button" style="display: none;" onclick="toggleNotifications()">
    <span class="notification-icon">🔔</span>
    <span id="notification-count">0</span>
</div>


<div class="notifications-popup" id="notifications-popup">
<div class="close-btn" onclick="closeNotification('notifications-popup')">x</div>
<ul class="notification_bullets" style="margin-top: 0px !important; padding-left: 0px;">

    <li id="specialMoveCostnotification" style="display: none;" class="notification-popup">
        <?php echo get_option('special_move_cost_notification'); ?>
    </li>
    <li id="ExceptionsforMoveCost" style="display: none;" class="notification-popup">
        <?php echo get_option('exceptions_for_move_cost_notification'); ?>
    </li>
    <li id="maxDistaceMiles" style="display: none;" class="notification-popup">
        <?php echo get_option('max_distace_miles_notification'); ?>
    </li>
    <li id="Insterstate" style="display: none;" class="notification-popup">
        <?php echo get_option('inster_states_notification'); ?>
    </li>
    <li id="userSpecialItems" style="display: none;" class="notification-popup">
        <?php echo get_option('special_items_notification'); ?>
    </li>
    <li id="stairsnotification" style="display: none;" class="notification-popup">
        <?php echo get_option('stairs_notification'); ?>
    </li>
</ul>
</div>

</div>

    

<style>
/*new css v2 */
a#add-product-link {
    color: #6d41a1;
    cursor: pointer;

}

.notifications-popup {
display: none;
padding: 20px;
margin-bottom: 10px;
background-color: #fcfcfc;
border: 1px solid #f5c6cb;
border-radius: 5px;
position: fixed;
z-index: 99999;
top: 50%;
left: 50%;
transform: translate(-50%, -50%);

}

.notification-popup {
    display: block;
    background-color: #f8d7da;
    color: #721c24;
    padding: 15px;
    margin-bottom: 10px;
    border: 1px solid #f5c6cb;
    border-radius: 5px;
    z-index: 99999;
    width: 300px;
}


.close-btn {
    position: absolute;
    top: 5px;
    right: 10px;
    text-decoration: none;
    font-size: 16px;
    cursor: pointer;
    color: #721c24;
}

.close-btn:hover {
    color: #f5c6cb;
}

.notification-button {
    position: fixed;

    z-index: 99999;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    background-color: #f8d7da;
    color: #721c24;
    padding: 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    display: flex;
    align-items: center;
}

.notification-icon {
    margin-right: 5px; /* מרווח בין האייקון למספר */
}

.notification-button:hover {
    background-color: #f5c6cb; /* שינוי צבע בעת ריחוף */
}


.ui-state-active, .ui-widget-content .ui-state-active, .ui-widget-header .ui-state-active, a.ui-button:active, .ui-button:active, .ui-button.ui-state-active:hover{
    border: 1px solid #6d41a1 !important;
}
div#bar-steps{
    background: #fcfcfc;
    box-shadow: 0 2px 10px #19192212;
    border-radius: 6px;
    padding: 10px;
}
#step-one,#step-two,#step-three,#step-four {
    box-shadow: 0 2px 10px #19192212;
    border-radius: 6px;
    padding: 10px;
}
div#address-checkbox-container ,.btns-back ,.btns-next ,div#map{
    margin-top: 15px;
}
div#address-checkbox-container{
    margin-bottom: 15px;
}
[type=button]:focus, [type=button]:hover, [type=submit]:focus, [type=submit]:hover, button:focus, button:hover {
    color: #fff !important;
    background-color: #6d41a1 !important;
    text-decoration: none;
}
[type=button], [type=submit], button {
    display: inline-block;
    font-weight: 400;
    color: #fff !important;
    text-align: center;
    white-space: nowrap;
    -webkit-user-select: none;
    -moz-user-select: none;
    user-select: none;
    background-color: #6d41a1 !important;
    padding: .5rem 1rem;
    font-size: 1rem;
    border-radius: 3px;
    transition: all .3s;
}

.timedatepopup{
    border: 1px solid #c5c5c5;
    height: 330px;
    padding:10px;
    border-radius: 3px;
}

#map button{
    margin-right: 5px !important;
    color: #6d41a1 !important;
    background-color: #fff !important;
}

.ui-datepicker-inline.ui-datepicker.ui-widget.ui-widget-content.ui-helper-clearfix.ui-corner-all.ui-datepicker-multi-2.ui-datepicker-multi {
    width: 100% !important;
}
.ui-datepicker-inline.ui-datepicker.ui-widget.ui-widget-content.ui-helper-clearfix.ui-corner-all{
    width: 100% !important;
    
}
table.ui-datepicker-calendar {
    margin-top: 7px !important;
}
.ui-datepicker-inline.ui-datepicker.ui-widget.ui-widget-content.ui-helper-clearfix.ui-corner-all.ui-datepicker-multi-3.ui-datepicker-multi{
    border: none;
}
.ui-datepicker-inline.ui-datepicker.ui-widget.ui-widget-content.ui-helper-clearfix.ui-corner-all {
    border: none;
}

/* Styling for highlighted dates with a blue background */
.ui-datepicker-calendar .ui-state-default {
        background-color: #4d73eb26 ;
        color: #4d73eb;
    }

    

    /* Styling for the discounts section */
    .discounts-section {
    display: flex;
    width: 100%;
    justify-content: center !important;
    text-align: center !important;
    font-size: 11px;
    margin-bottom: 5px;
    }

    /*Styling for Available Discounts heading*/
    .available-discounts {
    font-size: 11px;
    text-align: center !important;
    }

    
.no-availability {
        display:none;
}

.green-date a {
    background-color: <?php echo get_option('idd_availability_very_cheap_color'); ?> !important; /* Rate between 0 and 1 */
    color: black !important; /* Text color */
}

.yellow-date a {
    background-color: <?php echo get_option('idd_availability_cheap_color'); ?> !important; /* Rate = 1 */
    color: black !important; /* Text color */
}

.orange-date a {
    background-color: <?php echo get_option('idd_availability_medium_color'); ?> !important; /* Rate between 1 and 1.2 */
    color: black !important; /* Text color */
}

.light-yellow-date a {
    background-color: <?php echo get_option('idd_availability_expensive_color'); ?> !important; /* Rate between 1.2 and 1.5 */
    color: black !important; /* Text color */
}

.red-date a {
    background-color: <?php echo get_option('idd_very_expensive_cheap_color'); ?> !important; /* Rate above 1.5 */
    color: black !important; /* Text color */
}

.green-radio + label {
    background-color: <?php echo get_option('idd_availability_very_cheap_color'); ?>; /* Rate between 0 and 1 */
    color: black;
}
.blue-radio + label {
    background-color: <?php echo get_option('idd_min_cheap_color'); ?>; /* Rate between 0 and 1 */
    color: black;
}

.yellow-radio + label {
    background-color: <?php echo get_option('idd_availability_cheap_color'); ?>; /* Rate = 1 */
    color: black;
}

.orange-radio + label {
    background-color: <?php echo get_option('idd_availability_medium_color'); ?>; /* Rate between 1 and 1.2 */
    color: black;
}

.light-yellow-radio + label {
    background-color: <?php echo get_option('idd_availability_expensive_color'); ?>; /* Rate between 1.2 and 1.5 */
    color: black;
}

.red-radio + label {
    background-color: <?php echo get_option('idd_very_expensive_cheap_color'); ?>; /* Rate above 1.5 */
    color: black;
}
.ui-datepicker-div{
    display:block !important;
}

input[type="radio"] {
    display: none;
}

input[type="radio"] + label {
    display: inline-block;
    margin: 5px;
    padding: 50px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s, color 0.3s;
    text-align: center; 
    display: grid;
    gap: 5px;
    margin: 5px;
}



input[type="radio"] + label:hover {
    opacity: 0.8;
}

input[type="radio"]:checked + label {
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
    transform: scale(1.05); 
    border: 1px solid #6d41a1;
}

.date-container,
.time-container {
    flex: 1; 
}

.time-container {
    display: flex;
    flex-direction: column; 
}

.cal_lable {
    margin-bottom: 5px; 
}

input.idd-date,
select {
    padding: 10px;
    border: 1px solid #ccc; 
    border-radius: 4px; 
    width: 100%; 
    box-sizing: border-box; 
}

#pickup_time_radio {
    margin-top: 10px;
    display: flex;
    place-content: center;
}

.changethedate {
    text-align: center;
}

@media screen and (max-width: 600px) {
    #pickup_time_radio {
        display: grid;
        place-content: unset;
    }
    input[type="radio"] + label{
        padding: 10px;
    }
}

/*end css v2 */

        .minus,.plus,.special-minus,.special-plus{
            color: <?php echo get_option('idd_color_quantity_btn'); ?>;
            background: <?php echo get_option('idd_background_quantity_btn'); ?>;
            border-color: <?php echo get_option('idd_background_quantity_btn'); ?>;
            font-size: 18px !important;
            height: 30px;
            padding-left: 9px;
            padding-right: 9px;
            padding-top: 0;
        }
        input{
            background: <?php echo get_option('idd_inputs_border_bg_color'); ?>;
            height: <?php echo get_option('idd_height_inputs'); ?>;
            border-color: <?php echo get_option('idd_inputs_border_color'); ?>;

        }
    .idd-product-table input[type="text"],
    .idd-product-table input[type="number"],
    .idd-product-table textarea,
    .idd-product-table select,
    .idd-product-table .wp-list-table th,
    .idd-product-table .wp-list-table td,
    .idd-calculator input[type="text"],
    .idd-calculator input[type="number"],
    .idd-calculator textarea,
    .idd-calculator select,
    .idd-calculator .wp-list-table th,
    .idd-calculator .wp-list-table td {
        border-color: <?php echo get_option('idd_inputs_border_color'); ?>;

    }

    thead tr th {
        background: <?php echo get_option('idd_header_table_background_color'); ?>;

    }
    table#idd-table tr,table#idd-calculator tr,table#special-items-table tr{
        background: <?php echo get_option('idd_table_background_color'); ?>;
        color: <?php echo get_option('idd_table_text_color'); ?>;
        font-size: <?php echo get_option('idd_table_text_font'); ?> !important;

    }
    .notification_bullets{
        color: <?php echo get_option('idd_notification_color'); ?>;
        font-size: <?php echo get_option('idd_font_size_notification'); ?> !important;

    }

    .table_div{
        max-height: 300px !important;
        overflow-y: auto;
        box-shadow: 0px 10px 15px -3px rgba(0,0,0,0.1);
        border: 1px solid <?php echo get_option('idd_table_border_color'); ?> !important;
        background: <?php echo get_option('idd_table_background_color'); ?>;
        position: absolute !important;
        width: 100% !important;
        z-index: 9999;
    }
    .cal_lable ,.special_cal_lable{
        font-size: <?php echo get_option('idd_lable_font_size'); ?> !important;
        color: <?php echo get_option('idd_lable_color'); ?> !important;
        margin-top: <?php echo get_option('idd_margin_con'); ?> !important;
    }
    th {
    text-align: left;
    color: <?php echo get_option('idd_table_hr_color'); ?> !important;
    font-size: <?php echo get_option('idd_table_hr_font'); ?> !important;
    }
    #sum,#sum-volume,#sum-quantity,#mils,#move-price,#total-move-storage-price{
        color: <?php echo get_option('idd_price_color'); ?> !important;
        font-size: <?php echo get_option('idd_price_font_size'); ?> !important;
    }

    
    table{
        border: 1.5px solid <?php echo get_option('idd_table_border_color'); ?> !important;

    }
    tr{
        border-bottom: 1px solid <?php echo get_option('idd_table_border_color'); ?> !important;

    }

    input:not(.form-num-input)::-webkit-outer-spin-button,
    input:not(.form-num-input)::-webkit-inner-spin-button {
       -webkit-appearance: none;
        margin: 0;
    }


input[type=number]:not(.form-num-input) {
  -moz-appearance: textfield;
}


/* Loading Page Styles */
#loading-page {
    display: flex ;
    justify-content: center !important;
    align-items: center !important;
    width: 100% !important;
    z-index: 9999;
}

.spinner {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 4px solid #333;
    border-top-color: #666;
    animation: spin 1s infinite linear;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Form Styles */
#main-content {
    display: none;
}



.stepper-wrapper {
  margin-top: auto;
  display: flex;
  justify-content: space-between;
  margin-bottom: 20px;
}

.stepper-item {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  flex: 1;
}

.stepper-item::before {
  position: absolute;
  content: "";
  border-bottom: 2px solid #ccc;
  width: 100%;
  top: 20px;
  left: -50%;
  z-index: 2;
}

.stepper-item::after {
  position: absolute;
  content: "";
  border-bottom: 2px solid #ccc;
  width: 100%;
  top: 20px;
  left: 50%;
  z-index: 2;
}

.stepper-item .step-counter {
  position: relative;
  z-index: 5;
  display: flex;
  justify-content: center;
  align-items: center;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #ccc;
  margin-bottom: 6px;
}

.stepper-item.active {
  font-weight: bold;
}

.stepper-item.completed .step-counter {
  background-color: <?php echo get_option('steps_color'); ?> ;
  color: #fff;
}

.stepper-item.completed::after {
  position: absolute;
  content: "";
  border-bottom: 2px solid <?php echo get_option('steps_color'); ?> ;
  width: 100%;
  top: 20px;
  left: 50%;
  z-index: 3;
}

.stepper-item:first-child::before {
  content: none;
}

.stepper-item:last-child::after {
  content: none;
}

.address-access-apt-floor-container {
    display: flex;
}

.address-access-container  {
    margin-right: 5px;
}

.address-access-container , .address-apt-floor-container {
    width: 50%;
}

.address-apt-floor-container {
    display: flex;
}

.address-floor-container , .address-apt-container {
    width: 50%;
}

.address-floor-container  {
    margin-right: 5px;
}

.form-num-input {
    border-style: solid;
}

@media screen and (max-width: 768px) {
  .address-access-apt-floor-container {
    display: block;
  }
  .address-access-container , .address-apt-floor-container ,.date-time-container {
    width: 100%;
  }
  .address-access-container  {
    margin-right: 0px;
   }

}

.btns-next-back{
    width: 100%;
    display:flex;
}
.btns-next ,.btns-back{
    width: 100%;
}
#back-step-four ,#back-step-three ,#back-step-two{
    float: right;
}
#submit-step ,#next-step-three ,#next-step-two{
    float: left;
}
#add-product-btn{
    position: absolute;
    border: none;
    right: 3px;
    padding-top: 13px;
    padding-bottom: 13px;
    margin-right: 10px;

}
input#new-product-name {
    margin-top: -3px;
}
div#add-product-container {
    padding-top: 3px;
}


</style>



<!-- Include jQuery library (pay attention if the website use another library then delete it) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include jQuery UI CSS -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<!-- Include jQuery UI library -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>




<script>
function updateTextInput() {
    var selectElement = document.getElementById('origin-address-access');
    var inputElement = document.getElementById('email-origin-address-access');
    inputElement.value = selectElement.value;

    var selectElement2 = document.getElementById('destination-address-access');
    var inputElement2 = document.getElementById('email-destination-address-access');
    inputElement2.value = selectElement2.value;


    var selectElement3 = document.getElementById('destination-up-address-access');
    var inputElement3 = document.getElementById('email-destination-up-address-access');
    inputElement3.value = selectElement3.value;


    var selectElement4 = document.getElementById('destination-off-address-access');
    var inputElement4 = document.getElementById('email-destination-off-address-access');
    inputElement4.value = selectElement4.value;
}
function closeNotification(id) {
    document.getElementById(id).style.display = 'none';
}
function updateNotificationCount() {
    const notifications = document.querySelectorAll('.notification-popup');
    let count = 0;

    notifications.forEach(notification => {
        if (notification.style.display === 'block') {
            count++;
        }
    });

    document.getElementById('notification-count').innerText = count;
}

function toggleNotifications() {
    const popup = document.getElementById('notifications-popup');
    if (popup.style.display === 'none' || popup.style.display === '') {
        popup.style.display = 'block';
    } else {
        popup.style.display = 'none';
    }
    updateNotificationCount();
}


document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);

    setTimeout(() => {
        if (urlParams.has('first-name') && urlParams.has('last-name') && urlParams.has('user-phone') && urlParams.has('user-email')) {
            document.getElementById('first-name').value = urlParams.get('first-name');
            document.getElementById('last-name').value = urlParams.get('last-name');
            document.getElementById('user-phone').value = urlParams.get('user-phone');
            document.getElementById('user-email').value = urlParams.get('user-email');
            //document.getElementById('next-step-one').click();
        }
    }, 3000);
});

let ratesData = []; 

function getRates() {
    return new Promise((resolve, reject) => {
        fetch('/wp-admin/admin-ajax.php?action=get_rates_proxy', {
            method: 'GET'
        })
        .then(response => response.json())
        .then(data => {
            ratesData = data; 
            resolve(ratesData); 
        })
        .catch(error => {
            console.error('Error fetching rates:', error);
            reject(error);
        });
    });
}




jQuery(document).ready(function($) {
    // Call getRates when the document is ready
    getRates().then(rates => {
        const ratesByDateAndType = {};

        rates.forEach(rate => {
            const date = rate.date;
            const rateType = rate.rateType.toLowerCase().replace(' ', '');

            if (!ratesByDateAndType[date]) {
                ratesByDateAndType[date] = {};
            }

            if (!ratesByDateAndType[date][rateType] || parseFloat(rate.rate) < parseFloat(ratesByDateAndType[date][rateType].rate)) {
                ratesByDateAndType[date][rateType] = rate;
            }
        });

        const filteredRates = Object.values(ratesByDateAndType).flatMap(dateRates => Object.values(dateRates));

        const ratesByDate = {};
        const ratesByTime = {};


        filteredRates.forEach(rate => {
            if (!ratesByDate[rate.date]) {
                ratesByDate[rate.date] = {
                    pm: null,
                    am: null,
                    pmLate: null
                };
            }
            // Correct assignment of rates to the right keys
            const rateTypeKey = rate.rateType.toLowerCase().replace(' ', '');
            ratesByDate[rate.date][rateTypeKey] = parseFloat(rate.rate);

            // Create a map for time options
            if (!ratesByTime[rate.date]) {
                ratesByTime[rate.date] = [];
            }
            ratesByTime[rate.date].push({
                time: rate.rateType,
                rate: rate.rate
            });
        });
        function getNumberOfMonths() {


         if ($(window).width() < <?php echo get_option('idd_mobile_breakpoint'); ?>) {
            return <?php echo get_option('idd_mobile_views'); ?>;
         } else if ($(window).width() < <?php echo get_option('idd_tablet_breakpoint'); ?>) {
            return <?php echo get_option('idd_tablet_views'); ?>; 
         } else {
            return <?php echo get_option('idd_desktop_views'); ?>; 
         }
}

function initializeDatepicker() {
    $('#pickup_date').datepicker('destroy'); 

    const today = new Date();
    const endDate = new Date();
    endDate.setDate(today.getDate() + <?php echo get_option('crm_feach_data_days'); ?> ); 

    $('#pickup_date').datepicker({
        numberOfMonths: getNumberOfMonths(),
        beforeShowDay: function(date) {
            const formattedDate = $.datepicker.formatDate('yy-mm-dd', date);
            const rates = ratesByDate[formattedDate];

            if (rates) {
                const pm = rates.pm !== null ? rates.pm : Infinity;
                const am = rates.am !== null ? rates.am : Infinity;
                const pmLate = rates.pmlate !== null ? rates.pmlate : Infinity;

                const minRate = Math.min(pm, am, pmLate);
                return [true, getColorClass(minRate), 'Available'];
            }
            return [true, '', ''];
        },
        minDate: today, 
        maxDate: endDate, 
        onSelect: function(selectedDate) {
            const formattedDate = $.datepicker.formatDate('yy-mm-dd', new Date(selectedDate));
            populatePickupTime(ratesByTime[formattedDate]);
            populatePickupTimeRadio(ratesByTime[formattedDate]);
            fetchPricesradios();

            $('#date-container').hide();
            $('#time-container').fadeIn();
        }
    });
}


    // הפעלה ראשונית של ה-datepicker
    initializeDatepicker();

    // עדכן את ה-datepicker כאשר גודל המסך משתנה
    $(window).resize(function() {
        initializeDatepicker();
    });




        function populatePickupTime(times) {
            const $pickupTimeSelect = $('#pickup_time');
            $pickupTimeSelect.empty();
            $pickupTimeSelect.append('<option value="">Select pickup time</option>');
            times.forEach(item => {
                $pickupTimeSelect.append(`<option value="${item.time}">${item.time}</option>`);
            });
        }

function populatePickupTimeRadio(times) {
    const $pickupTimeRadio = $('#pickup_time_radio');
    $pickupTimeRadio.empty();

    const minRates = {};
    const pricePromises = [];

    times.forEach(item => {
        if (['AM', 'PM', 'PM LATE'].includes(item.time)) {
            const rate = parseFloat(item.rate);
            if (!minRates[item.time] || rate < minRates[item.time].rate) {
                minRates[item.time] = {
                    rate: rate,
                    time: item.time
                };
            }
            pricePromises.push(calculatePriceForTime(item.time));
        }
    });



// Wait for all price calculations to complete
Promise.all(pricePromises).then(prices => {

const originalTimesData = times; // times in original order

// מיפוי בין הזמן למחיר
const timePriceMap = {
    'AM': null,
    'PM': null,
    'PM LATE': null
};

// התאמה של מחירים לזמנים המתאימים
for (let i = 0; i < originalTimesData.length; i++) {
    const time = originalTimesData[i].time;
    if (timePriceMap.hasOwnProperty(time)) {
        timePriceMap[time] = prices[i]; // שמירת המחיר לפי הזמן
    }
}

// מיפוי תצוגה לפי הסדר הנכון
const timeDisplayMap = {
    'AM': 'Morning',
    'PM': 'Noon',
    'PM LATE': 'Afternoon'
};

// הוספת radio buttons לפי הסדר
Object.keys(timeDisplayMap).forEach(time => {
    if (timePriceMap[time] !== null) { 
        const displayTime = timeDisplayMap[time];
        const rateClass = getRateClass(minRates[time].rate);
        const price = timePriceMap[time];
        const cf = parseFloat("<?php echo get_option('min_cf_move_threshold'); ?>");
        const sumVolume = parseFloat(document.getElementById('sum-volume').value.replace(' CF', ''));
        
        if (sumVolume > cf) {
            if (parseFloat(price.replace('$', '')) === 300) {
                $pickupTimeRadio.append(`
                  <input type="radio" name="pickup_time_radio" value="${time}" id="${time}" class="blue-radio">
                  <label for="${time}" class="${rateClass}">${displayTime} <label>(${price})</label></label>
                `);
            } else {
                $pickupTimeRadio.append(`
                  <input type="radio" name="pickup_time_radio" value="${time}" id="${time}" class="${rateClass}">
                  <label for="${time}" class="${rateClass}">${displayTime} <label>(${price})</label></label>
                `);
            }
        } else {
            $pickupTimeRadio.append(`
              <input type="radio" name="pickup_time_radio" value="${time}" id="${time}" class="green-radio">
              <label for="${time}" class="${rateClass}">${displayTime} <label>(${price})</label></label>
            `);
        }
    }
});



$pickupTimeRadio.on('change', 'input[name="pickup_time_radio"]', function() {
    const selectedTime = $(this).val();
    $('#pickup_time').val(selectedTime);
});
});


    $('#back-to-date-btn').on('click', function() {
        $('#time-container').hide();
        $('#date-container').fadeIn();
    });
        }

        function calculatePriceForTime(selectedTime) {
            return fetchPricesradios().then(() => {
                const selectedDate = $('#pickup_date').val();
                const dateParts = selectedDate.split('-');
                const formattedDate = `${dateParts[0]}-${dateParts[1]}-${dateParts[2]}`;

                const rateTypeMap = {
                    "AM": "AM",
                    "PM": "PM",
                    "PM LATE": "PM LATE"
                };
                const selectedRateType = rateTypeMap[selectedTime];

                let defaultPricePerCF = 0;
                switch (selectedRateType) {
                    case 'AM':
                        defaultPricePerCF = window.defaultPricePerCF_AM || 0;
                        break;
                    case 'PM':
                        defaultPricePerCF = window.defaultPricePerCF_PM || 0;
                        break;
                    case 'PM LATE':
                        defaultPricePerCF = window.defaultPricePerCF_PM_LATE || 0;
                        break;
                    default:
                        defaultPricePerCF = 0;
                }

                const sumVolume = parseFloat(document.getElementById('sum-volume').value.replace(' CF', ''));
                const distanceMiles = parseFloat(document.getElementById('mils').value.replace(' mi', ''));

                const firstMilesExcluded = parseFloat("<?php echo get_option('first_miles_excluded'); ?>");
                let moveprice = 0;
                if (distanceMiles <= firstMilesExcluded) {
                    moveprice = 0;
                } else {
                    moveprice = (distanceMiles * parseFloat("<?php echo get_option('defult_price_move_cost_mile'); ?>"));
                }

                let price = 0;
                if (sumVolume <= parseFloat("<?php echo get_option('min_cf_move_threshold'); ?>") || 0) {
                    price = (sumVolume * defaultPricePerCF);
                    if(price < parseFloat("<?php echo get_option('defult_price_min_cf_move_price'); ?>")){
                        price = parseFloat("<?php echo get_option('defult_price_min_cf_move_price'); ?>") || 0;
                    }else{
                        price = (sumVolume * defaultPricePerCF);
                    }
                } else {
                    price = (sumVolume * defaultPricePerCF);
                }
                
                const sumWithSuffix = document.getElementById('sum').value;
                const sumWithoutSuffix = sumWithSuffix.replace(' / month', '');
                const sum = parseFloat(sumWithoutSuffix.replace('$', ''));
                const starisprice = document.getElementById('stairs-price-all-view').value;

                const totalPrice = price + moveprice + sum + parseFloat(starisprice);

                console.log("---------------------------------------------------------");

                console.log("calculatePriceForTime totalPrice", totalPrice);
                console.log("=");
                console.log("price " , price);
                console.log("+");
                console.log("moveprice ",moveprice);
                console.log("+");
                console.log("sum" , sum);
                console.log("+");
                console.log("parseFloat(starisprice) " , parseFloat(starisprice));
                console.log("---------------------------------------------------------");


                    return `$${totalPrice.toFixed(0)}`;
           
                
            });
        }



const nextStepThreeBtn = document.getElementById('next-step-three');
const barstepOnebtn = document.getElementById('btn-bar-step-one');
const barstepTwobtn = document.getElementById('btn-bar-step-two');
const barstepThreebtn = document.getElementById('btn-bar-step-three');
const barstepFourbtn = document.getElementById('btn-bar-step-four');
const pmlateBtn = document.getElementById('PM LATE');
const amBtn = document.getElementById('AM');
const pmBtn = document.getElementById('PM');

nextStepThreeBtn.addEventListener('click', handleStepClick);
barstepOnebtn.addEventListener('click', handleStepClick);
barstepTwobtn.addEventListener('click', handleStepClick);
barstepThreebtn.addEventListener('click', handleStepClick);
barstepFourbtn.addEventListener('click', handleStepClick);


function handleStepClick(event) {
    event.preventDefault();
    const selectedDate = $('#pickup_date').val();
    const formattedDate = $.datepicker.formatDate('yy-mm-dd', new Date(selectedDate));
    
    const availableTimes = ratesByTime[formattedDate];

    populatePickupTimeRadio(availableTimes);
    
    if (availableTimes.length > 0) {
        const selectedTime = availableTimes[0].time;
        calculatePriceForTime(selectedTime).then(price => {
            console.log(`Price for selected time (${selectedTime}):`, price);
        });
    }
}

/*
function getRateClass(rate) {
    
    if (rate === 0) {
        return 'no-availability'; 
    } else if (rate > 0 && rate < 1) {
        return 'green-radio'; 
    } else if (rate === 1) {
        return 'yellow-radio';
    } else if (rate > 1 && rate <= 1.2) {
        return 'orange-radio'; 
    } else if (rate > 1.2 && rate <= 1.5) {
        return 'light-yellow-radio'; 
    } else {
        return 'red-radio'; 
    }
}
*/
function getRateClass(rate) {
    
    if (rate === <?php echo get_option('idd_no_availability_rate'); ?>) {
        return 'no-availability'; 
    } else if (rate > <?php echo get_option('idd_availability_very_cheap_rate_min'); ?> && rate < <?php echo get_option('idd_availability_very_cheap_rate_max'); ?>) {
        return 'green-radio'; 
    } else if (rate === <?php echo get_option('idd_availability_cheap_rate_min'); ?>) {
        return 'yellow-radio';
    } else if (rate > <?php echo get_option('idd_availability_medium_rate_min'); ?> && rate <= <?php echo get_option('idd_availability_medium_rate_max'); ?>) {
        return 'orange-radio'; 
    } else if (rate > <?php echo get_option('idd_availability_expensive_rate_min'); ?> && rate <= <?php echo get_option('idd_availability_expensive_rate_max'); ?>) {
        return 'light-yellow-radio'; 
    } else {
        return 'red-radio'; 
    }
}

/*
function getColorClass(minRate) {
    if (minRate < 1) {
        return 'green-date'; 
    } else if (minRate === 1) {
        return 'yellow-date'; 
    } else if (minRate > 1 && minRate <= 1.2) {
        return 'orange-date'; 
    } else if (minRate > 1.2 && minRate <= 1.5) {
        return 'light-yellow-date'; 
    } else {
        return 'red-date'; 
    }
}*/

function getColorClass(minRate) {
    if (minRate < <?php echo get_option('idd_availability_very_cheap_rate_max'); ?>) {
        return 'green-date'; 
    } else if (minRate === <?php echo get_option('idd_availability_very_cheap_rate_max'); ?>) {
        return 'yellow-date'; 
    } else if (minRate > <?php echo get_option('idd_availability_very_cheap_rate_max'); ?> && minRate <= <?php echo get_option('idd_availability_medium_rate_max'); ?>) {
        return 'orange-date'; 
    } else if (minRate > <?php echo get_option('idd_availability_expensive_rate_min'); ?> && minRate <= <?php echo get_option('idd_availability_expensive_rate_max'); ?>) {
        return 'light-yellow-date'; 
    } else {
        return 'red-date'; 
    }
}



    });
});


function fetchPricesradios() {
    return new Promise((resolve, reject) => {
        calculatePrice();
        const selectedDate = document.getElementById('pickup_date').value;

        const dateParts = selectedDate.split('/');
        const formattedDate = `${dateParts[2]}-${dateParts[0]}-${dateParts[1]}`;


        if (ratesData.length > 0) {
            const filteredRates = ratesData.filter(rate => rate.date === formattedDate);

            let defaultPricePerCF_PM = 0;
            let defaultPricePerCF_AM = 0;
            let defaultPricePerCF_PM_LATE = 0;

            filteredRates.forEach(rate => {
                if (['PM', 'AM', 'PM LATE'].includes(rate.rateType)) {
                    switch (rate.rateType) {
                        case 'PM':
                            defaultPricePerCF_PM = parseFloat(rate.rate);
                            break;
                        case 'AM':
                            defaultPricePerCF_AM = parseFloat(rate.rate);
                            break;
                        case 'PM LATE':
                            defaultPricePerCF_PM_LATE = parseFloat(rate.rate);
                            break;
                    }
                }
            });


            window.defaultPricePerCF_PM = defaultPricePerCF_PM;
            window.defaultPricePerCF_AM = defaultPricePerCF_AM;
            window.defaultPricePerCF_PM_LATE = defaultPricePerCF_PM_LATE;

            console.log("*************************************");

console.log("defaultPricePerCF_PM ", defaultPricePerCF_PM);
console.log("defaultPricePerCF_AM " , defaultPricePerCF_AM);
console.log("defaultPricePerCF_PM_LATE ",defaultPricePerCF_PM_LATE);

console.log("*************************************");

            resolve(); 
        } else {
            reject('No rates data available'); 
        }
    });
}

// Function to display Total Price
function displayTotalPrice() {
    const destinationCheckbox = document.getElementById('use-destination-address');
        if (destinationCheckbox.checked){
        document.getElementById('sum').value = '$' + 0 + " / month"; 
        }else{
            document.getElementById('destination_address').value = "Storage";    
        }
    const starisprice = document.getElementById('stairs-price-all-view').value;
    const sumWithSuffix = document.getElementById('sum').value;
    const sumWithoutSuffix = sumWithSuffix.replace(' / month', '');
    const sum = parseFloat(sumWithoutSuffix.replace('$', ''));
    const movePrice = parseFloat(document.getElementById('move-price').value.replace('$', ''));
    const totalPriceField = document.getElementById('total-move-storage-price');
    const totalPrice = sum + movePrice + parseFloat(starisprice);

    console.log("ty movePrice ", movePrice);

    console.log("ty totalPrice ", totalPrice);

    //totalPriceRange 
    let totalPriceRange = Math.ceil(totalPrice / 500) * 50;
    totalPriceField.value = isNaN(totalPrice) ? '' : '$' + totalPrice.toFixed(2) + ' ⇄ ' + '$'+ parseInt(totalPrice + totalPriceRange) ;
}
document.getElementById('sum').addEventListener('input', displayTotalPrice);
document.getElementById('move-price').addEventListener('input', displayTotalPrice);




document.getElementById('pickup_date').addEventListener('change', fetchPricesradios);


document.addEventListener('DOMContentLoaded', function() {
    const emailInput = document.getElementById('user-email');
    const emailError = document.getElementById('email-error');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    let typingTimer; // Timer identifier
    const doneTypingInterval = 1000; // Time in milliseconds (1 second)

    // Function to validate email after user finishes typing
    function doneTyping() {
        const emailAddress = emailInput.value;
        if (emailRegex.test(emailAddress)) {
            console.log('Valid email address');
            emailError.style.display = 'none'; 
            emailInput.setCustomValidity('');
        } else {
            console.log('Invalid email address');
            emailError.style.display = 'block'; 
            emailInput.setCustomValidity(''); 
        }
    }

   
    emailInput.addEventListener('input', function() {
        clearTimeout(typingTimer); 
        typingTimer = setTimeout(doneTyping, doneTypingInterval); 
    });

  
    emailInput.addEventListener('blur', doneTyping);
});



document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.getElementById('user-phone');

    phoneInput.addEventListener('input', function() {
        let phoneNumber = phoneInput.value.replace(/\D/g, ''); // Remove all non-numeric characters
        if (phoneNumber.length > 0) {
            // Apply phone number formatting
            phoneNumber = phoneNumber.replace(/(\d{3})(\d{0,3})(\d{0,4})/, function(_, p1, p2, p3) {
                let formattedNumber = '(' + p1;
                if (p2) formattedNumber += ') ' + p2;
                if (p3) formattedNumber += '-' + p3;
                return formattedNumber;
            });
        }
        phoneInput.value = phoneNumber;
    });
});


document.addEventListener('DOMContentLoaded', function() {
    const loadingPage = document.getElementById('loading-page');
    const formContainer = document.getElementById('main-content');

  
    setTimeout(function() {
        loadingPage.style.display = 'none';
        formContainer.style.display = 'block';
    }, 3000); 
});

document.addEventListener('DOMContentLoaded', function() {
    const stepOne = document.getElementById('step-one');
    const stepTwo = document.getElementById('step-two');
    const stepThree = document.getElementById('step-three');
    const stepFour = document.getElementById('step-four');

    const barstepOne = document.getElementById('bar-step-one');
    const barstepTwo = document.getElementById('bar-step-two');
    const barstepThree = document.getElementById('bar-step-three');
    const barstepFour = document.getElementById('bar-step-four');

    const nextStepOneBtn = document.getElementById('next-step-one');
    const stepOneFields = document.querySelectorAll('#step-one input[required]'); 

    const nextStepTwoBtn = document.getElementById('next-step-two');
    const stepTwoFields = document.querySelectorAll('#step-three input[required]'); 


    const nextStepThreeBtn = document.getElementById('next-step-three');
    const backStepTwoBtn = document.getElementById('back-step-two');
    const backStepThreeBtn = document.getElementById('back-step-three');
    const backStepFourBtn = document.getElementById('back-step-four');
    const submitStepFourBtn = document.getElementById('submit-step');


    const barstepOnebtn = document.getElementById('btn-bar-step-one');
    const barstepTwobtn = document.getElementById('btn-bar-step-two');
    const barstepThreebtn = document.getElementById('btn-bar-step-three');
    const barstepFourbtn = document.getElementById('btn-bar-step-four');

    const calcappid = "<?php echo get_option('calc_section_id'); ?>";
    
    const calcapp = document.getElementById(calcappid);


    // Function to set active step
    function setActiveStep(step) {

        step.classList.add('completed');
    }

    function setcompletedStep(step) {
        barstepOne.classList.remove('active');
        barstepTwo.classList.remove('active');
        barstepThree.classList.remove('active');
        barstepFour.classList.remove('active');

        step.classList.add('active');
    }

    // Hide all steps except the first one initially
    stepOne.style.display = 'block';
    stepTwo.style.display = 'none';
    stepThree.style.display = 'none';
    stepFour.style.display = 'none';

    // Set active step initially
    setActiveStep(barstepOne);
    setcompletedStep(barstepOne);


    barstepOnebtn.addEventListener('click', function(event) {
    if (barstepOne.classList.contains('completed')) {
        const nextButton = document.getElementById('next-step-one');
        nextButton.value = "Next";
        stepOne.style.display = 'block';
        stepTwo.style.display = 'none';
        stepThree.style.display = 'none';
        stepFour.style.display = 'none';
    }
});

barstepTwobtn.addEventListener('click', function(event) {
    if (barstepTwo.classList.contains('completed')) {
        const nextButton = document.getElementById('next-step-two');
        nextButton.value = "Next";
        stepOne.style.display = 'none';
        stepTwo.style.display = 'block';
        stepThree.style.display = 'none';
        stepFour.style.display = 'none';
    }
});

barstepThreebtn.addEventListener('click', function(event) {
    if (barstepThree.classList.contains('completed')) {
        const nextButton = document.getElementById('next-step-three');
        nextButton.value = "Next";
        stepOne.style.display = 'none';
        stepTwo.style.display = 'none';
        stepThree.style.display = 'block';
        stepFour.style.display = 'none';

    }
});

barstepFourbtn.addEventListener('click', function(event) {
    if (barstepFour.classList.contains('completed')) {
        stepOne.style.display = 'none';
        stepTwo.style.display = 'none';
        stepThree.style.display = 'none';
        stepFour.style.display = 'block';
    }
});



     // Function to validate step-one fields
     function validateStepOne() {
        let isValid = true;

        // Check each required field in step-one
        stepOneFields.forEach(function(field) {
            if (field.value.trim() === '') {

                document.getElementById('input-error').style.display = 'block';
                
                isValid = false; 
            }
        });

        return isValid;
    }



    // Event listener for the Next button in step-one
    nextStepOneBtn.addEventListener('click', function(event) {
        event.preventDefault();
        
        // Validate step-one fields
        if (validateStepOne()) {
            // Set timer for next button text change
            const nextButton = document.getElementById('next-step-one');
            let timer;
            clearTimeout(timer);
            nextButton.value = "Next...";
           
            timer = setTimeout(function() {
               // Proceed to next step
               stepOne.style.display = 'none';
               stepTwo.style.display = 'block';
               setActiveStep(barstepTwo);
               setcompletedStep(barstepTwo);
               //calcapp.scrollIntoView({ behavior: 'smooth' });

            }, 1000); 

        }
    });


function checkTables() {
    let isFull = true;

    // Check if the main calculator table is empty
    let mainTableBody = document.getElementById('idd-calculator-body');
    let specialItemsTableBody = document.getElementById('special-items-table-body');

    if (mainTableBody.innerHTML.trim() === '' && specialItemsTableBody.innerHTML.trim() === '') {
        document.getElementById('checkTables-error').style.display = 'block';

        isFull = false;
    }


    return isFull;
}


    nextStepTwoBtn.addEventListener('click', function(event) {
        event.preventDefault();
        
        if (checkTables()) {

            // Set timer for next button text change
            const nextButton = document.getElementById('next-step-two');
            let timer;
            clearTimeout(timer);
            
            nextButton.value = "Next...";
             

            timer = setTimeout(function() {
            stepTwo.style.display = 'none';
            stepThree.style.display = 'block';
            setActiveStep(barstepThree);
            setcompletedStep(barstepThree);
            //calcapp.scrollIntoView({ behavior: 'smooth' });

            }, 1000);

        }
    });


// Function to validate step-two fields
function validateStepTwo() {
    let isValid = true;

    // Check each required field in step-two
    stepTwoFields.forEach(function(field) {
        if (field.value.trim() === '') {

            document.getElementById('input-error-step-two').style.display = 'block';

            isValid = false; 
        }
    });

    return isValid;
}
    nextStepThreeBtn.addEventListener('click', function(event) {
        event.preventDefault();
        if (validateStepTwo()) {
            updateSum();
            calculateDistance()
            fetchPricesradios()
            // Set timer for next button text change
            const nextButton = document.getElementById('next-step-three');
            let timer;
            clearTimeout(timer);
            
                nextButton.value = "Next...";
            

            timer = setTimeout(function() {
            stepThree.style.display = 'none';
            stepFour.style.display = 'block';
            setActiveStep(barstepFour);
            setcompletedStep(barstepFour);
            //calcapp.scrollIntoView({ behavior: 'smooth' });

            }, 1000); 

        }
    });

    // Event listeners for the Back buttons
    backStepTwoBtn.addEventListener('click', function(event) {
        event.preventDefault();
        const nextButton = document.getElementById('next-step-one');
        nextButton.value = "Next";
        stepTwo.style.display = 'none';
        stepOne.style.display = 'block';
        setActiveStep(barstepOne);
        setcompletedStep(barstepOne);
        //calcapp.scrollIntoView({ behavior: 'smooth' });

    });

    backStepThreeBtn.addEventListener('click', function(event) {
        event.preventDefault();
        const nextButton = document.getElementById('next-step-two');
        nextButton.value = "Next";
        stepThree.style.display = 'none';
        stepTwo.style.display = 'block';
        setActiveStep(barstepTwo);
        setcompletedStep(barstepTwo);
        //calcapp.scrollIntoView({ behavior: 'smooth' });

    });

    backStepFourBtn.addEventListener('click', function(event) {
        event.preventDefault();
        const nextButton = document.getElementById('next-step-three');
        nextButton.value = "Next";
        stepFour.style.display = 'none';
        stepThree.style.display = 'block';
        setActiveStep(barstepThree);
        setcompletedStep(barstepThree);
        //calcapp.scrollIntoView({ behavior: 'smooth' });

    });

    submitStepFourBtn.addEventListener('click', function(event) {
        calculatePrice()
        const barsteps = document.getElementById('bar-steps');
        stepFour.style.display = 'none';
        barsteps.style.display = 'none';
        const loadingPage = document.getElementById('loading-page');
        loadingPage.style.display = 'flex';
        saveFormData();
        
    });
});



// start functions for create Special Items Table

document.getElementById('add-product-link').addEventListener('click', function() {
    document.getElementById('add-product-container').style.display = 'flex';
    document.getElementById('special-table-content-container').style.display = '';

});


document.getElementById('add-product-btn').addEventListener('click', function() {
    const newProductName = document.getElementById('new-product-name').value;
    if (newProductName.trim() !== '') {

        const event = new CustomEvent('new-product-added', { detail: newProductName });
        document.dispatchEvent(event);
        

        document.getElementById('add-product-container').style.display = 'none';
        document.getElementById('special-table-content-container').style.display = 'none';

    }
});

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('add-product-btn').addEventListener('click', function(event) {
        event.preventDefault(); 
        const newProductName = document.getElementById('new-product-name').value.trim();
        if (newProductName !== '') {
            createSpecialItemsTable();
            addSpecialItemRow(newProductName);
            document.getElementById('new-product-name').value = ''; 
            document.getElementById('add-product-container').style.display = 'flex';
            document.getElementById('special-table-content-container').style.display = '';

      
            document.getElementById('notification-button').style.display = 'block';

            updateNotificationCount();
            document.getElementById('special-items-table').style.display = '';
            //document.getElementById('notification-bullets-container').style.display = '';
            document.getElementById('quantity-specialandregular-container').style.display = 'flex';
            document.getElementById('special-q').style.display = '';

            updateTextarea(); 
        }
    });
});

function createSpecialItemsTable() {
    if (!document.getElementById('special-items-table')) {
        const specialItemsTitle = document.createElement('label');
        specialItemsTitle.className = 'special_cal_lable';
        specialItemsTitle.id = 'cal-special-table-title';
        specialItemsTitle.textContent = 'Your Special Items list';

        const specialItemsContainer = document.createElement('div');
        specialItemsContainer.id = 'special-items-container';

        specialItemsContainer.appendChild(specialItemsTitle);

        const specialItemsTable = document.createElement('table');
        specialItemsTable.id = 'special-items-table';
        specialItemsTable.className = 'wp-list-table widefat striped';
        specialItemsTable.style.display = '';

        const tableHeader = document.createElement('thead');
        tableHeader.innerHTML = `
            <tr>
                <th style="text-align: left;">Name</th>
                <th style="text-align: left; width: 90px;">Quantity</th>
            </tr>
        `;

        const tableBody = document.createElement('tbody');
        tableBody.id = 'special-items-table-body';

        specialItemsTable.appendChild(tableHeader);
        specialItemsTable.appendChild(tableBody);
        specialItemsContainer.appendChild(specialItemsTable);

        const addProductContainer = document.getElementById('add-product-container');
        addProductContainer.insertAdjacentElement('afterend', specialItemsContainer);
    }
}

function addSpecialItemRow(productName) {
    const specialItemsTableBody = document.getElementById('special-items-table-body');
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td style="text-align: left;">${productName}</td>
        <td>
            <div class="special-quantity buttons_added" style="display: flex; float: right;">
                <input type="button" value="-" class="special-minus" onclick="decreasespecialQuantity(this)">
                <input style="width: 50px; height: 30px;" type="number" class="quantity-special-input" value="1" min="0" max="999" onchange="updateCalculator(this)">
                <input type="button" value="+" class="special-plus" onclick="increasespecialQuantity(this)">
            </div>
        </td>
    `;
    specialItemsTableBody.appendChild(newRow);
    updateTextarea(); 
}
// finish functions for create Special Items Table


// function for update Text area (to send the itmes to email and sheets)
function updateTextarea() {
    const specialItemsTableBody = document.getElementById('special-items-table-body');
    const textareaContent = [];
    let specialquantitySum = 0; 

    specialItemsTableBody.querySelectorAll('tr').forEach(row => {
        const productName = row.querySelector('td:first-child').innerText;
        const quantity = row.querySelector('.quantity-special-input').value;
        const sumqspecialuantity = parseInt(row.querySelector('.quantity-special-input').value);

        specialquantitySum += sumqspecialuantity;
        if (quantity > 0) {
            textareaContent.push(`● ${productName} | Quantity: ${quantity} \n`);

        } else {
            row.remove();
        }

    });

    if (specialItemsTableBody.children.length === 0) {
        document.getElementById('special-items-table').style.display = 'none';
        localStorage.setItem('userSpecialItems', ' ');
        document.getElementById('userSpecialItems').style.display = 'none';

        var selectElement = " ";
        var inputElement = document.getElementById('email-special_items_notification');
        inputElement.value = selectElement;

        updateNotificationCount();
        document.getElementById('quantity-special-container').style.display = 'none';
        document.getElementById('cal-special-table-title').style.display = 'none';
    } else {
        document.getElementById('special-items-table').style.display = '';
        localStorage.setItem('userSpecialItems', '<?php echo get_option('special_items_notification'); ?>');
        document.getElementById('userSpecialItems').style.display = 'block';
        document.getElementById('notification-button').style.display = 'block';

        var selectElement = "<?php echo get_option('special_items_notification'); ?>";
        var inputElement = document.getElementById('email-special_items_notification');
        inputElement.value = selectElement;


        updateNotificationCount();
        document.getElementById('quantity-special-container').style.display = 'flex';
        document.getElementById('cal-special-table-title').style.display = '';
    }
    document.getElementById('calculator-special-table-content').value = textareaContent.join('\n');
    document.getElementById('sum-special-quantity').value = specialquantitySum + " Special Items"; 
    updateSum();
    displayFieldValue()

}

// A function for handling the presentation of the tables of the products selected in a div instead of a textarea in step 4
function displayFieldValue() {
    const fieldValue = document.getElementById('calculator-table-content').value;
    const divContent = document.getElementById('div-calculator-table-content');
    divContent.innerText = fieldValue;
    const fieldValuespecial = document.getElementById('calculator-special-table-content').value;
    const divContentspecial = document.getElementById('div-calculator-special-table-content');
    divContentspecial.innerText = fieldValuespecial;
}

// Function to increase the quantity
function increasespecialQuantity(input) {
    var quantityInput = input.parentNode.querySelector('.quantity-special-input');
    var currentValue = parseInt(quantityInput.value);
    quantityInput.value = currentValue + 1;
    updateTextarea(); 

}

// Function to decrease the quantity
function decreasespecialQuantity(input) {
    var quantityInput = input.parentNode.querySelector('.quantity-special-input');
    var currentValue = parseInt(quantityInput.value);
    if (currentValue > 0) {
        quantityInput.value = currentValue - 1;
    }else{
        //document.getElementById('userSpecialItems').style.display = 'none';
        localStorage.setItem('userSpecialItems', ' ');
        document.getElementById('userSpecialItems').style.display = 'none';
        updateNotificationCount();
    }
    updateTextarea(); 
}

function updateCalculator(element) {
    updateTextarea(); 
}

        // Function to check if the search field is active then display the table
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('search-input').addEventListener('input', function() {
                const filterText = this.value;
                if (filterText) {
                    document.getElementById('idd-table').style.display = '';
                    document.getElementById('table_div').style.display = '';

                    filterTable(filterText);
                } else {
                    document.getElementById('idd-table').style.display = 'none';
                    document.getElementById('table_div').style.display = 'none';

                }
            });
        });

        // Function to filter table rows based on input
        function filterTable(filterText) {
            const rows = document.querySelectorAll('#idd-product-table-body tr');
            rows.forEach(row => {
                const nameCell = row.querySelector('td:first-child');
                if (nameCell.innerText.toLowerCase().includes(filterText.toLowerCase())) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
        

        document.addEventListener('DOMContentLoaded', function() {

        document.getElementById('clear-search').addEventListener('click', function() {

        document.getElementById('search-input').value = '';

        document.getElementById('idd-table').style.display = 'none';
        document.getElementById('table_div').style.display = 'none';

          });
        });

        document.addEventListener('click', function(event) {
   
          if (!event.target.closest('#search-input') && !event.target.closest('#idd-table')) {

            document.getElementById('search-input').value = '';

            document.getElementById('idd-table').style.display = 'none';
            document.getElementById('table_div').style.display = 'none';

          }
        });        

// Function to increase the quantity
function increaseQuantity(input) {
    var quantityInput = input.parentNode.querySelector('.quantity-input');
    var currentValue = parseInt(quantityInput.value);
    quantityInput.value = currentValue + 1;
    updateCalculator(quantityInput); 

    const name = input.closest('tr').querySelector('td:first-child').innerText;
    const calculatorQuantityInput = document.querySelector(`#idd-calculator-body tr[data-name="${name}"] .quantity-input`);
    if (calculatorQuantityInput) {
        calculatorQuantityInput.value = currentValue + 1;
        updateCalculator(calculatorQuantityInput); 
    }
}

// Function to decrease the quantity
function decreaseQuantity(input) {
    var quantityInput = input.parentNode.querySelector('.quantity-input');
    var currentValue = parseInt(quantityInput.value);
    if (currentValue > 0) {
        quantityInput.value = currentValue - 1;
        updateCalculator(quantityInput); 

        const name = input.closest('tr').querySelector('td:first-child').innerText;
        const calculatorQuantityInput = document.querySelector(`#idd-calculator-body tr[data-name="${name}"] .quantity-input`);
        if (calculatorQuantityInput) {
            calculatorQuantityInput.value = currentValue - 1;
            updateCalculator(calculatorQuantityInput); 
        }
    }
}


// Function to update calculator table
function updateCalculator(input) {
    const quantityInput = input.parentNode.querySelector('.quantity-input');
    const quantity = parseInt(quantityInput.value);
    const row = input.parentNode.parentNode.parentNode; 

    const name = row.querySelector('td:first-child').innerText;
    const volume = parseFloat(row.querySelector('td:nth-child(2)').innerText);
    const price = parseFloat(row.querySelector('td:nth-child(3)').innerText);
    const specialMoveCost = parseFloat(row.querySelector('td:nth-child(4)').innerText);
    let total;

    document.getElementById('idd-calculator').style.display = '';
    document.getElementById('cal-table-title').style.display = '';

    document.getElementById('sum-q-container').style.display = '';

    document.getElementById('volume-container').style.display = 'flex';
    document.getElementById('quantity-container').style.display = 'flex';
    document.getElementById('quantity-specialandregular-container').style.display = 'flex';

    const defaultPrice = parseFloat("<?php echo get_option('idd_calculator_defult_price'); ?>");
    if (price === defaultPrice) {
        total = volume * price * quantity;
    } else {
        total = price * quantity;
    }

    if (quantity === 0) {
        const calculatorRow = document.querySelector(`#idd-calculator-body tr[data-name="${name}"]`);
        if (calculatorRow) {
            calculatorRow.remove();
        }
        if (document.querySelectorAll('#idd-calculator-body tr').length === 0) {
            document.getElementById('idd-calculator').style.display = 'none';
            document.getElementById('cal-table-title').style.display = 'none';


            document.getElementById('volume-container').style.display = 'none';
            document.getElementById('quantity-container').style.display = 'none';
            document.getElementById('sum-q-container').style.display = 'none';

        }
        calculatePrice();
        updateSum();
        updateSpecialMoveCostNotification();
        updateExceptionsforMoveCost();
        displayTotalPrice();
        return;
    }

    // Check if the row already exists in the calculator table
    const calculatorRow = document.querySelector(`#idd-calculator-body tr[data-name="${name}"]`);
    if (calculatorRow) {
        calculatorRow.querySelector('.quantity').value = quantity;
        calculatorRow.querySelector('.total').innerText = '$' + parseFloat(total);
    } else {
        const calculatorBody = document.getElementById('idd-calculator-body');
        const newRow = document.createElement('tr');
        newRow.setAttribute('data-name', name);
        newRow.innerHTML = `
            <td style="text-align: left;">${name}</td>
            <td style="display: none;">${volume}</td>
            <td style="display: none;">${price}</td>
            <td style="display: none;">${specialMoveCost}</td>
            <td>
            <div class="quantity buttons_added" style="display: flex; float: right;" >
        <input type="button" value="-" class="minus" onclick="decreaseQuantity(this)">
        <input style="width: 50px; height: 30px;" type="number" class="quantity-input" value="${quantity}" min="0" max="999" onchange="updateCalculator(this)">
        <input type="button" value="+" class="plus" onclick="increaseQuantity(this)">
    </div>
            </td>
            <td class="total" style="display: none;">$${total}</td>`;
        calculatorBody.appendChild(newRow);
    }

    updateSum();
    calculatePrice();
    updateSpecialMoveCostNotification();
    updateExceptionsforMoveCost();
    displayTotalPrice();
}


// Function to update Product Table
function updateProductTable() {
    const inputs = document.querySelectorAll('#idd-product-table-body .quantity');
    inputs.forEach(input => {
        if (input.value === '0') {
            input.parentNode.parentNode.remove();
        }
    });
    calculatePrice(); 
}

// Function to update the special move cost notification
function updateSpecialMoveCostNotification() {
    const specialMoveCostNotification = document.getElementById('specialMoveCostnotification');
    //const notificationbulletscontainer = document.getElementById('notification-bullets-container');

    const rows = document.querySelectorAll('#idd-calculator-body tr');
    let specialMoveSelected = false;
    rows.forEach(row => {
        const specialMoveCost = parseFloat(row.querySelector('td:nth-child(4)').innerText);
        if (specialMoveCost !== 0) {
            specialMoveSelected = true;
        }
    });
    if (specialMoveSelected) {
        localStorage.setItem('specialMoveCostnotification', '<?php echo get_option('special_move_cost_notification'); ?>');
        document.getElementById('specialMoveCostnotification').style.display = 'block';

        var selectElement = "<?php echo get_option('special_move_cost_notification'); ?>";
        var inputElement = document.getElementById('email-special_move_cost_notification');
        inputElement.value = selectElement;

        document.getElementById('notification-button').style.display = 'block';
        updateNotificationCount();

    } else {
        localStorage.setItem('specialMoveCostnotification', ' ');
        document.getElementById('specialMoveCostnotification').style.display = 'none';

        var selectElement = " ";
        var inputElement = document.getElementById('email-special_move_cost_notification');
        inputElement.value = selectElement;

        updateNotificationCount();
    }
}

// Function to update total sum and volume sum
function updateSum() {
    const totals = document.querySelectorAll('#idd-calculator-body .total');

    let sum = 0;
    let volumeSum = 0; 
    let quantitySum = 0; 
    let stairs = 0;
    let totalstairs_origin_address = 0;
    let totalstairs_destination_address = 0;
    let totalstairs_destination_up_address = 0;
    let totalstairs_destination_off_address = 0;
    let tostanoti = 0;

    const originaddressfloor = document.getElementById('origin-address-floor').value;
    const destinationaddressfloor = document.getElementById('destination-address-floor').value;
    const destinationupaddressfloor = document.getElementById('destination-up-address-floor').value;
    const destinationoffaddressfloor = document.getElementById('destination-off-address-floor').value;

 

    let tableContent = ''; 
    totals.forEach(total => {
        const value = parseFloat(total.innerText.replace('$', '')); 
        if (!isNaN(value)) {
            sum += value;
            const row = total.parentNode;
            const volume = parseFloat(row.querySelector('td:nth-child(2)').innerText); 
            const specialMoveCost = parseFloat(row.querySelector('td:nth-child(4)').innerText); 
            const quantity = row.querySelector('.quantity').value;
            const quantityforcal = parseInt(row.querySelector('.quantity').value);

            if (specialMoveCost === 0) {
                volumeSum += volume * quantity; 
            }
            quantitySum += quantityforcal;
            const name = row.querySelector('td:first-child').innerText;
            tableContent += `● ${name} | Quantity: ${quantity} \n`; 
        }
    });
    


    var specialQuantity = parseInt(document.getElementById('sum-special-quantity').value.replace(' Special Items', ''));
    var regularQuantity = quantitySum; 

   if (!isNaN(specialQuantity)) {
      document.getElementById('SumSpecialAndRegularQuantity').value = parseInt(document.getElementById('sum-special-quantity').value.replace(' Special Items', '')) + quantitySum + " Items";
   } else {
      document.getElementById('SumSpecialAndRegularQuantity').value = "";
   }

  


   const stairs_price = parseFloat("<?php echo get_option('stairs_price'); ?>") 
   const origin_address_acces = document.getElementById('origin-address-access').value;
   const origin_address_floor = document.getElementById('origin-address-floor').value;
    if (origin_address_acces.trim() == "Stairs" && origin_address_floor.trim() !== "") {
        totalstairs_origin_address = parseFloat(origin_address_floor) * stairs_price * parseFloat(volumeSum);
        tostanoti = tostanoti + parseFloat(origin_address_floor);
    }

   const destination_address_access = document.getElementById('destination-address-access').value;
   const destination_address_floor = document.getElementById('destination-address-floor').value;
    if (destination_address_access.trim() == "Stairs" && destination_address_floor.trim() !== "") {
        totalstairs_destination_address = parseFloat(destination_address_floor) * stairs_price * parseFloat(volumeSum);
        tostanoti = tostanoti + parseFloat(destination_address_floor);

    }

   const destination_up_address_access = document.getElementById('destination-up-address-access').value;
   const destination_up_address_floor = document.getElementById('destination-up-address-floor').value;
    if (destination_up_address_access.trim() == "Stairs" && destination_up_address_floor.trim() !== "") {
        totalstairs_destination_up_address = parseFloat(destination_up_address_floor) * stairs_price * parseFloat(volumeSum);
        tostanoti = tostanoti + parseFloat(destination_up_address_floor);

    }

   const destination_off_address_access = document.getElementById('destination-off-address-access').value;
   const destination_off_address_floor = document.getElementById('destination-off-address-floor').value;
    if (destination_off_address_access.trim() == "Stairs" && destination_off_address_floor.trim() !== "") {
        totalstairs_destination_off_address = parseFloat(destination_off_address_floor) * stairs_price * parseFloat(volumeSum);
        tostanoti = tostanoti + parseFloat(destination_off_address_floor);

    }


    document.getElementById('stairs-price-all').value = parseFloat(totalstairs_origin_address) + parseFloat(totalstairs_destination_address) + parseFloat(totalstairs_destination_up_address) + parseFloat(totalstairs_destination_off_address) + '$' ;
    document.getElementById('stairs-price-all-view').value = parseFloat(totalstairs_origin_address) + parseFloat(totalstairs_destination_address) + parseFloat(totalstairs_destination_up_address) + parseFloat(totalstairs_destination_off_address)  ;

    if (tostanoti >= 6) {
        localStorage.setItem('stairsnotification', '<?php echo get_option('stairs_notification'); ?>');
        document.getElementById('stairsnotification').style.display = 'block';
        document.getElementById('notification-button').style.display = 'block';

        var selectElement = "<?php echo get_option('stairs_notification'); ?>";
        var inputElement = document.getElementById('email-stairs_notification');
        inputElement.value = selectElement;

        updateNotificationCount();

    }else{
        localStorage.setItem('stairsnotification', ' ');
        document.getElementById('stairsnotification').style.display = 'none';

        var selectElement = " ";
        var inputElement = document.getElementById('email-stairs_notification');
        inputElement.value = selectElement;

        updateNotificationCount();


    }
    
    document.getElementById('sum').value = '$' + sum.toFixed(2) + " / month"; 
    document.getElementById('calculator-table-content').value = tableContent; 
    document.getElementById('sum-volume').value = volumeSum + " CF"; 
    document.getElementById('sum-quantity').value = quantitySum + " Items"; 
    document.getElementById('sum-CfAndQuantity').value = quantitySum + " Items" + " (" + volumeSum + " CF" +")"; 
    document.getElementById('sum-totalspecial-quantity').value = document.getElementById('sum-special-quantity').value ; 

    displayFieldValue()
}
document.getElementById('origin-address-floor').addEventListener('change', updateSum);
document.getElementById('destination-address-floor').addEventListener('change', updateSum);
document.getElementById('destination-up-address-floor').addEventListener('change', updateSum);
document.getElementById('destination-off-address-floor').addEventListener('change', updateSum);
document.getElementById('pickup_date').addEventListener('change', updateSum);

// Function to auto complete address
function autocompleteAddress() {
    const input = document.getElementById('destination_address');
    const originInput = document.getElementById('origin_address');
    const upInput = document.getElementById('destination_up_address');
    const offInput = document.getElementById('destination_off_address');
    const options = {
        types: ['address'],
        componentRestrictions: { country: 'US' } // Restrict to USA
    };
    const autocomplete = new google.maps.places.Autocomplete(input, options);
    const originAutocomplete = new google.maps.places.Autocomplete(originInput, options);
    const upAutocomplete = new google.maps.places.Autocomplete(upInput, options);
    const offAutocomplete = new google.maps.places.Autocomplete(offInput, options);
    
    autocomplete.addListener('place_changed', function() {
        const place = autocomplete.getPlace();
        if (!place.geometry) {
            console.error('Place not found');
            return;
        }
        calculateDistance();
        calculateDistanceView();
        updateSpecialMoveCostNotification();
        updateExceptionsforMoveCost();
        checkLocationAgainstStorage();
    });

    originAutocomplete.addListener('place_changed', function() {
        const place = originAutocomplete.getPlace();
        if (!place.geometry) {
            console.error('Place not found');
            return;
        }
        calculateDistance();
        calculateDistanceView();
        updateSpecialMoveCostNotification();
        updateExceptionsforMoveCost();
        checkLocationAgainstStorage();
    });

    upAutocomplete.addListener('place_changed', function() {
        const place = upAutocomplete.getPlace();
        if (!place.geometry) {
            console.error('Place not found');
            return;
        }
        calculateDistance();
        calculateDistanceView();
        updateSpecialMoveCostNotification();
        updateExceptionsforMoveCost();
        checkLocationAgainstStorage();
    });
    
    offAutocomplete.addListener('place_changed', function() {
        const place = offAutocomplete.getPlace();
        if (!place.geometry) {
            console.error('Place not found');
            return;
        }
        calculateDistance();
        calculateDistanceView();
        updateSpecialMoveCostNotification();
        updateExceptionsforMoveCost();
        checkLocationAgainstStorage();
    });
}


//Check if the destination address checkbox is active, then change the price of storage to 0
document.addEventListener('DOMContentLoaded', function() {
    const destinationCheckbox = document.getElementById('use-destination-address');
    const destinationAddressContainer = document.getElementById('address-destination-container');
    
    destinationCheckbox.addEventListener('change', function() {
        destinationAddressContainer.style.display = this.checked ? 'block' : 'none';
        document.getElementById('sum').value = '$' + 0 + " / month"; 

        calculateDistance();
        calculateDistanceView();
        calculatePrice();
        displayTotalPrice()
    });
    calculateDistance();
    calculateDistanceView();
    calculatePrice();
    displayTotalPrice()

});

//Check if the extra up destination address checkbox is checked Active then display the appropriate field
document.addEventListener('DOMContentLoaded', function() {
    const destinationCheckbox = document.getElementById('use-extra-up-destination-address');
    const destinationAddressContainer = document.getElementById('address-Additional-extra-up-destination-container');
    
    destinationCheckbox.addEventListener('change', function() {
        destinationAddressContainer.style.display = this.checked ? 'block' : 'none';
        calculateDistance();
        calculateDistanceView();
    });
    calculateDistance();
    calculateDistanceView();
});

//Check if the extra off destination address checkbox is checked Active then display the appropriate field
document.addEventListener('DOMContentLoaded', function() {
    const destinationCheckbox = document.getElementById('use-extra-off-destination-address');
    const destinationAddressContainer = document.getElementById('address-Additional-extra-off-destination-container');
    
    destinationCheckbox.addEventListener('change', function() {
        destinationAddressContainer.style.display = this.checked ? 'block' : 'none';
        calculateDistance();
        calculateDistanceView();
    });
    calculateDistance();
    calculateDistanceView();
});

// calculate distance View function (maps/mils on step 3)
function calculateDistance() {
    const origin = document.getElementById('origin_address').value;
    const destination = document.getElementById('use-destination-address').checked
        ? document.getElementById('destination_address').value
        : "<?php echo get_option('storage_address'); ?>";
    const destinationUp = document.getElementById('destination_up_address').value;
    const destinationOff = document.getElementById('destination_off_address').value;
    const minDistanceLD = parseFloat("<?php echo get_option('min_distance_ld'); ?>");
    const service = new google.maps.DistanceMatrixService();
    document.getElementById('mils-container').style.display = 'flex';

    
    document.getElementById('pickup-date-container').style.display = 'flex';
    document.getElementById('pickup-time-container').style.display = 'flex';

    const pickupdate = document.getElementById('pickup_date').value;
    const pickuptime = document.getElementById('pickup_time').value;

    document.getElementById('pickup-date-view').value = pickupdate;
    document.getElementById('pickup-time-view').value = pickuptime;

    document.getElementById('map').style.display = '';



    // Check if additional destinations are not empty
    const destinations = [destination];
    if (destinationUp.trim() !== '') {
        destinations.push(destinationUp);
    }
    if (destinationOff.trim() !== '') {
        destinations.push(destinationOff);
    }

    service.getDistanceMatrix(
        {
            origins: [origin],
            destinations: destinations,
            travelMode: 'DRIVING',
            unitSystem: google.maps.UnitSystem.IMPERIAL
        },
        (response, status) => {
            if (status === 'OK') {
                // Initialize total distance
                let totalDistanceMiles = 0;

                // Handle response for main destination (a or b)
                const distanceMeters = response.rows[0].elements[0].distance.value;
                const distanceMiles = distanceMeters * 0.000621371;
                totalDistanceMiles += distanceMiles;

                // Handle response for additional destinations
                for (let i = 1; i < response.rows[0].elements.length; i++) {
                    if (response.rows[0].elements[i].status === 'OK') {
                        const additionalDistanceMeters = response.rows[0].elements[i].distance.value;
                        const additionalDistanceMiles = additionalDistanceMeters * 0.000621371;
                        totalDistanceMiles += additionalDistanceMiles;
                    }
                }

                if (totalDistanceMiles >= minDistanceLD) {
                    localStorage.setItem('maxDistaceMiles', '<?php echo get_option('max_distace_miles_notification'); ?>');
                    document.getElementById('maxDistaceMiles').style.display = 'block';
                    document.getElementById('notification-button').style.display = 'block';

                    var selectElement = "<?php echo get_option('max_distace_miles_notification'); ?>";
                    var inputElement = document.getElementById('email-max_distace_miles_notification');
                    inputElement.value = selectElement;

                    updateNotificationCount();
                } else {
                    localStorage.setItem('maxDistaceMiles', ' ');
                    document.getElementById('maxDistaceMiles').style.display = 'none';

                    var selectElement = " ";
                    var inputElement = document.getElementById('email-max_distace_miles_notification');
                    inputElement.value = selectElement;

                    updateNotificationCount();
                }

                const distanceText = totalDistanceMiles.toFixed(2) + ' mi';
                document.getElementById('mils').value = distanceText;

                // Display route for main destination (a or b) with waypoints (c and d)
                const map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 10,
                    center: { lat: 0, lng: 0 }
                });


                const directionsRenderer = new google.maps.DirectionsRenderer();
                directionsRenderer.setOptions({ polylineOptions: { strokeColor: '<?php echo get_option('map_poly_line_color'); ?>' } });

                directionsRenderer.setMap(map);
                const directionsService = new google.maps.DirectionsService();
                const request = {
                    origin: origin,
                    destination: destination,
                    waypoints: [],
                    travelMode: 'DRIVING'
                };

                // Add waypoints if not empty
                if (destinationUp.trim() !== '') {
                    request.waypoints.push({ location: destinationUp, stopover: true });
                }
                if (destinationOff.trim() !== '') {
                    request.waypoints.push({ location: destinationOff, stopover: true });
                }

                directionsService.route(request, function (result, status) {
                    if (status == 'OK') {
                        directionsRenderer.setDirections(result);
                    }
                });
                

                calculatePrice(); // Calculate price for all destinations
            } else {
                console.error('Error:', status);
            }
        }
    );
}

document.getElementById('destination_address').addEventListener('change', calculateDistance);
document.getElementById('origin_address').addEventListener('change', calculateDistance);
document.getElementById('pickup_date').addEventListener('change', calculateDistance);
document.getElementById('pickup_time').addEventListener('change', calculateDistance);
document.getElementById('use-destination-address').addEventListener('change', calculateDistance);
document.getElementById('use-extra-up-destination-address').addEventListener('change', calculateDistance);
document.getElementById('use-extra-off-destination-address').addEventListener('change', calculateDistance);
document.getElementById('origin-address-floor').addEventListener('change', updateSum);
document.getElementById('destination-address-floor').addEventListener('change', updateSum);
document.getElementById('destination-up-address-floor').addEventListener('change', updateSum);
document.getElementById('destination-off-address-floor').addEventListener('change', updateSum);
document.getElementById('pickup_date').addEventListener('change', updateSum);

document.getElementById('pickup_date').addEventListener('change', function() {
    const pickupdate = this.value;
    document.getElementById('pickup-date-view').value = pickupdate;
});


// calculate distance View function (maps/mils on step 4)
function calculateDistanceView() {
    const origin = document.getElementById('origin_address').value;
    const destination = document.getElementById('use-destination-address').checked
        ? document.getElementById('destination_address').value
        : "<?php echo get_option('storage_address'); ?>";
    const destinationUp = document.getElementById('destination_up_address').value;
    const destinationOff = document.getElementById('destination_off_address').value;
    const minDistanceLD = parseFloat("<?php echo get_option('min_distance_ld'); ?>");
    const service = new google.maps.DistanceMatrixService();
    document.getElementById('mils-container').style.display = 'flex';

    document.getElementById('pickup-date-container').style.display = 'flex';
    document.getElementById('pickup-time-container').style.display = 'flex';

    const pickupdate = document.getElementById('pickup_date').value;
    const pickuptime = document.getElementById('pickup_time').value;
    document.getElementById('pickup-date-view').value = pickupdate;
    document.getElementById('pickup-time-view').value = pickuptime;

    document.getElementById('mapView').style.display = '';



    // Check if additional destinations are not empty
    const destinations = [destination];
    if (destinationUp.trim() !== '') {
        destinations.push(destinationUp);
    }
    if (destinationOff.trim() !== '') {
        destinations.push(destinationOff);
    }

    service.getDistanceMatrix(
        {
            origins: [origin],
            destinations: destinations,
            travelMode: 'DRIVING',
            unitSystem: google.maps.UnitSystem.IMPERIAL
        },
        (response, status) => {
            if (status === 'OK') {
                // Initialize total distance
                let totalDistanceMiles = 0;

                // Handle response for main destination (a or b)
                const distanceMeters = response.rows[0].elements[0].distance.value;
                const distanceMiles = distanceMeters * 0.000621371;
                totalDistanceMiles += distanceMiles;

                // Handle response for additional destinations
                for (let i = 1; i < response.rows[0].elements.length; i++) {
                    if (response.rows[0].elements[i].status === 'OK') {
                        const additionalDistanceMeters = response.rows[0].elements[i].distance.value;
                        const additionalDistanceMiles = additionalDistanceMeters * 0.000621371;
                        totalDistanceMiles += additionalDistanceMiles;
                    }
                }

                if (totalDistanceMiles >= minDistanceLD) {
                    localStorage.setItem('maxDistaceMiles', '<?php echo get_option('max_distace_miles_notification'); ?>');
                    document.getElementById('maxDistaceMiles').style.display = 'block';
                    document.getElementById('notification-button').style.display = 'block';

                    var selectElement = "<?php echo get_option('max_distace_miles_notification'); ?>";
                    var inputElement = document.getElementById('email-max_distace_miles_notification');
                    inputElement.value = selectElement;

                } else {
                    localStorage.setItem('maxDistaceMiles', ' ');
                    document.getElementById('maxDistaceMiles').style.display = 'none';

                }

                const distanceText = totalDistanceMiles.toFixed(2) + ' mi';
                document.getElementById('mils').value = distanceText;

                // Display route for main destination (a or b) with waypoints (c and d)
                const map = new google.maps.Map(document.getElementById('mapView'), {
                    zoom: 10,
                    center: { lat: 0, lng: 0 }
                });


                const directionsRenderer = new google.maps.DirectionsRenderer();
                directionsRenderer.setOptions({ polylineOptions: { strokeColor: '<?php echo get_option('map_poly_line_color'); ?>' } });

                directionsRenderer.setMap(map);
                const directionsService = new google.maps.DirectionsService();
                const request = {
                    origin: origin,
                    destination: destination,
                    waypoints: [],
                    travelMode: 'DRIVING'
                };

                // Add waypoints if not empty
                if (destinationUp.trim() !== '') {
                    request.waypoints.push({ location: destinationUp, stopover: true });
                }
                if (destinationOff.trim() !== '') {
                    request.waypoints.push({ location: destinationOff, stopover: true });
                }

                directionsService.route(request, function (result, status) {
                    if (status == 'OK') {
                        directionsRenderer.setDirections(result);
                    }
                });
                

                calculatePrice(); // Calculate price for all destinations
            } else {
                console.error('Error:', status);
            }
        }
    );
}

document.getElementById('destination_address').addEventListener('change', calculateDistanceView);
document.getElementById('origin_address').addEventListener('change', calculateDistanceView);
document.getElementById('pickup_date').addEventListener('change', calculateDistanceView);
document.getElementById('pickup_time').addEventListener('change', calculateDistanceView);

document.getElementById('use-destination-address').addEventListener('change', calculateDistanceView);
document.getElementById('use-extra-up-destination-address').addEventListener('change', calculateDistanceView);
document.getElementById('use-extra-off-destination-address').addEventListener('change', calculateDistanceView);

document.getElementById('origin-address-floor').addEventListener('change', updateSum);
document.getElementById('destination-address-floor').addEventListener('change', updateSum);
document.getElementById('destination-up-address-floor').addEventListener('change', updateSum);
document.getElementById('destination-off-address-floor').addEventListener('change', updateSum);
document.getElementById('pickup_date').addEventListener('change', updateSum);


// Function to check location against 
function checkLocationAgainstStorage() {
    const destinationAddress = document.getElementById('destination_address').value;
    const userAddress = document.getElementById('origin_address').value;
    const storageAddress = "<?php echo get_option('storage_address'); ?>";
    const geocoder = new google.maps.Geocoder();
    geocoder.geocode({ 'address': userAddress }, function(results, status) {
        if (status === 'OK') {
            const userCountry = results[0].address_components.find(component => component.types.includes('country')).short_name;
            geocoder.geocode({ 'address': storageAddress }, function(results, status) {
                if (status === 'OK') {
                    const storageCountry = results[0].address_components.find(component => component.types.includes('country')).short_name;      
                    // Compare the countries
                    if (userCountry !== storageCountry) {
                        localStorage.setItem('Insterstate', '<?php echo get_option('inster_states_notification'); ?>');
                        document.getElementById('Insterstate').style.display = 'block';
                        document.getElementById('notification-button').style.display = 'block';


                        var selectElement = "<?php echo get_option('inster_states_notification'); ?>";
                        var inputElement = document.getElementById('email-inster_states_notification');
                        inputElement.value = selectElement;

                        updateNotificationCount();
                    } else {
                        localStorage.setItem('Insterstate', ' ');
                        document.getElementById('Insterstate').style.display = 'none';

                        var selectElement = " ";
                        var inputElement = document.getElementById('email-inster_states_notification');
                        inputElement.value = selectElement;

                        updateNotificationCount();
                    }
                } else {
                    console.error('Geocode was not successful for the storage address:', status);
                }
            });
        } else {
            console.error('Geocode was not successful for the user address:', status);
        }
    });
}


// Load Google Maps Places API with the provided API key
function loadGoogleMapsAPI() {
    const api_key = "<?php echo get_option('idd_calculator_api_key'); ?>";

    const script = document.createElement('script');
    script.src = `https://maps.googleapis.com/maps/api/js?key=${api_key}&libraries=places&callback=autocompleteAddress`;
    script.defer = true;
    document.body.appendChild(script);
}

document.addEventListener('DOMContentLoaded', loadGoogleMapsAPI);


/*
function getRates() {
    const ratesData = [
    {"date":"2024-09-23","rate":"0.9","rateType":"PM LATE"},
    {"date":"2024-09-24","rate":"0.9","rateType":"PM LATE"},
    {"date":"2024-09-25","rate":"0.9","rateType":"PM LATE"},
    {"date":"2024-09-26","rate":"0.9","rateType":"PM LATE"},
    {"date":"2024-09-27","rate":"0.9","rateType":"PM LATE"},
    {"date":"2024-09-28","rate":"1","rateType":"PM LATE"},
    {"date":"2024-09-29","rate":"0.9","rateType":"PM LATE"},
    {"date":"2024-09-30","rate":"1","rateType":"PM LATE"},
    {"date":"2024-10-01","rate":"1.4","rateType":"PM LATE"},
    {"date":"2024-10-02","rate":"1","rateType":"PM LATE"},
    {"date":"2024-10-03","rate":"1","rateType":"PM LATE"},
    {"date":"2024-10-04","rate":"1","rateType":"PM LATE"},
    {"date":"2024-10-05","rate":"1","rateType":"PM LATE"},
    {"date":"2024-10-06","rate":"1","rateType":"PM LATE"},
    {"date":"2024-10-07","rate":"1","rateType":"PM LATE"},
    {"date":"2024-10-08","rate":"1","rateType":"PM LATE"},
    {"date":"2024-10-09","rate":"1","rateType":"PM LATE"},
    {"date":"2024-10-10","rate":"1","rateType":"PM LATE"},
    {"date":"2024-10-11","rate":"1","rateType":"PM LATE"},
    {"date":"2024-10-12","rate":"1","rateType":"PM LATE"},
    {"date":"2024-10-13","rate":"1","rateType":"PM LATE"},
    {"date":"2024-10-14","rate":"1","rateType":"PM LATE"},
    {"date":"2024-10-15","rate":"1","rateType":"PM LATE"},
    {"date":"2024-10-16","rate":"1","rateType":"PM LATE"},
    {"date":"2024-10-17","rate":"1","rateType":"PM LATE"},
    {"date":"2024-10-18","rate":"1","rateType":"PM LATE"},
    {"date":"2024-10-19","rate":"1","rateType":"PM LATE"},
    {"date":"2024-10-20","rate":"1","rateType":"PM LATE"},
    {"date":"2024-09-23","rate":"1.2","rateType":"AM"},
    {"date":"2024-09-24","rate":"1","rateType":"AM"},
    {"date":"2024-09-25","rate":"1.2","rateType":"AM"},
    {"date":"2024-09-26","rate":"1.6","rateType":"AM"},
    {"date":"2024-09-27","rate":"2","rateType":"AM"},
    {"date":"2024-09-28","rate":"1.7","rateType":"AM"},
    {"date":"2024-09-29","rate":"1.25","rateType":"AM"},
    {"date":"2024-10-01","rate":"1.35","rateType":"AM"},
    {"date":"2024-10-02","rate":"1","rateType":"AM"},
    {"date":"2024-10-03","rate":"1","rateType":"AM"},
    {"date":"2024-10-04","rate":"1","rateType":"AM"},
    {"date":"2024-10-05","rate":"1","rateType":"AM"},
    {"date":"2024-10-06","rate":"1","rateType":"AM"},
    {"date":"2024-10-07","rate":"1","rateType":"AM"},
    {"date":"2024-10-08","rate":"1","rateType":"AM"},
    {"date":"2024-10-09","rate":"1","rateType":"AM"},
    {"date":"2024-10-10","rate":"1","rateType":"AM"},
    {"date":"2024-10-11","rate":"1","rateType":"AM"},
    {"date":"2024-10-12","rate":"1","rateType":"AM"},
    {"date":"2024-10-13","rate":"1","rateType":"AM"},
    {"date":"2024-10-14","rate":"1","rateType":"AM"},
    {"date":"2024-10-15","rate":"1","rateType":"AM"},
    {"date":"2024-10-16","rate":"1","rateType":"AM"},
    {"date":"2024-10-17","rate":"1","rateType":"AM"},
    {"date":"2024-10-18","rate":"1","rateType":"AM"},
    {"date":"2024-10-19","rate":"1","rateType":"AM"},
    {"date":"2024-10-20","rate":"1","rateType":"AM"},
    {"date":"2024-09-23","rate":"1","rateType":"PM"},
    {"date":"2024-09-24","rate":"1","rateType":"PM"},
    {"date":"2024-09-25","rate":"1","rateType":"PM"},
    {"date":"2024-09-26","rate":"1","rateType":"PM"},
    {"date":"2024-09-27","rate":"1.5","rateType":"PM"},
    {"date":"2024-09-28","rate":"1.4","rateType":"PM"},
    {"date":"2024-09-29","rate":"1.15","rateType":"PM"},
    {"date":"2024-09-30","rate":"1.5","rateType":"PM"},
    {"date":"2024-10-01","rate":"1.9","rateType":"PM"},
    {"date":"2024-10-02","rate":"1","rateType":"PM"},
    {"date":"2024-10-03","rate":"1","rateType":"PM"},
    {"date":"2024-10-04","rate":"1","rateType":"PM"},
    {"date":"2024-10-05","rate":"1","rateType":"PM"},
    {"date":"2024-10-06","rate":"1","rateType":"PM"},
    {"date":"2024-10-07","rate":"1","rateType":"PM"},
    {"date":"2024-10-08","rate":"1","rateType":"PM"},
    {"date":"2024-10-09","rate":"1","rateType":"PM"},
    {"date":"2024-10-10","rate":"1","rateType":"PM"},
    {"date":"2024-10-11","rate":"1","rateType":"PM"},
    {"date":"2024-10-12","rate":"1","rateType":"PM"},
    {"date":"2024-10-13","rate":"1","rateType":"PM"},
    {"date":"2024-10-14","rate":"1","rateType":"PM"},
    {"date":"2024-10-15","rate":"1","rateType":"PM"},
    {"date":"2024-10-16","rate":"1","rateType":"PM"},
    {"date":"2024-10-17","rate":"1","rateType":"PM"},
    {"date":"2024-10-18","rate":"1","rateType":"PM"},
    {"date":"2024-10-19","rate":"1","rateType":"PM"},
    {"date":"2024-10-20","rate":"1","rateType":"PM"},
    {"date":"2024-09-30","rate":"1.3","rateType":"AM"},

    ];
    return ratesData;
}




function fetchPrices() {
    const selectedDate = document.getElementById('pickup_date').value;

    // פורמט התאריך מהטופס
    const dateParts = selectedDate.split('/');
    const formattedDate = `${dateParts[2]}-${dateParts[0]}-${dateParts[1]}`;


    
    console.log("selectedDate " , formattedDate);

    // Fetching rates data from the API
    fetch(`https://api.allorigins.win/get?url=${encodeURIComponent(`https://www.perfectmovingrates.com/api/v1/rates?from=${formattedDate}`)}`)
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        const ratesData = JSON.parse(data.contents); // מוודאים שהדאטה מתפרש כ-JSON
        console.log('Rates Data:', ratesData);

        // סינון המחירים לפי התאריך שנבחר
        const filteredRates = ratesData.filter(rate => rate.date === formattedDate);

        let defaultPricePerCF_PM = 0;
        let defaultPricePerCF_AM = 0;
        let defaultPricePerCF_PM_LATE = 0;

        filteredRates.forEach(rate => {
            switch (rate.rateType) {
                case 'PM':
                    defaultPricePerCF_PM = parseFloat(rate.rate);
                    break;
                case 'AM':
                    defaultPricePerCF_AM = parseFloat(rate.rate);
                    break;
                case 'PM LATE':
                    defaultPricePerCF_PM_LATE = parseFloat(rate.rate);
                    break;
            }
        });

        console.log('Default Price Per CF PM:', defaultPricePerCF_PM);
        console.log('Default Price Per CF AM:', defaultPricePerCF_AM);
        console.log('Default Price Per CF PM LATE:', defaultPricePerCF_PM_LATE);

        // שמירת המחירים בגלובלי לצורך חישובים נוספים
        window.defaultPricePerCF_PM = defaultPricePerCF_PM;
        window.defaultPricePerCF_AM = defaultPricePerCF_AM;
        window.defaultPricePerCF_PM_LATE = defaultPricePerCF_PM_LATE;

        // קריאה לפונקציה לחישוב מחירים נוספים
        calculatePrice();
    })
    .catch(error => {
        console.error('There has been a problem with your fetch operation:', error);
    });
}

*/








function calculatePrice() {
    const rows = document.querySelectorAll('#idd-calculator-body tr');
    const distanceMiles = parseFloat(document.getElementById('mils').value.replace(' mi', ''));
    const FirstMilesExcluded = parseFloat("<?php echo get_option('first_miles_excluded'); ?>");
    const defultpriceMinCFMove = parseFloat("<?php echo get_option('defult_price_min_cf_move_price'); ?>");
    const MinCFMoveThreshold = parseFloat("<?php echo get_option('min_cf_move_threshold'); ?>");
    let moveprice = 0;
    const sumvolume = parseFloat(document.getElementById('sum-volume').value.replace(' CF', ''));


    const pickupdate = document.getElementById('pickup_date').value;
    document.getElementById('pickup-date-view').value = pickupdate;
    /*
    const selectedDate = document.getElementById('pickup_date').value;
    const selectedTime = document.getElementById('pickup_time').value;
    const dateParts = selectedDate.split('/');
    const formattedDate = `${dateParts[2]}-${dateParts[0]}-${dateParts[1]}`;
    */

    // Get the selected date from the div and the selected time from the dropdown
    const selectedDate = $('#pickup_date').text(); // Get date from the div
    const selectedTime = $('#pickup_time').val();
    const dateParts = selectedDate.split('-'); // Adjust split if date format is different
    const formattedDate = `${dateParts[0]}-${dateParts[1]}-${dateParts[2]}`;

    //console.log('MinCFMoveThreshold: ', MinCFMoveThreshold);
    //console.log('formattedDate: ', formattedDate);

    // קבלת מחירים מתוך ה-API
    let defaultPricePerCF = 0;

    // סינון המחירים לפי התאריך והשעה שנבחרו
    const rateTypeMap = {
        "AM": "AM",
        "PM": "PM",
        "PM LATE": "PM LATE"
    };
    const selectedRateType = rateTypeMap[selectedTime];

    // הגדרת המחיר המתאים לפי סוג התעריף שנשמר קודם
    switch (selectedRateType) {
        case 'AM':
            defaultPricePerCF = window.defaultPricePerCF_AM;
            break;
        case 'PM':
            defaultPricePerCF = window.defaultPricePerCF_PM;
            break;
        case 'PM LATE':
            defaultPricePerCF = window.defaultPricePerCF_PM_LATE;
            break;
        default:
            defaultPricePerCF = 0; // תחליף במקרה ואין תעריף מתאים
    }

    // חישוב מחירי ההובלה
    if (distanceMiles <= FirstMilesExcluded) {
        moveprice = 0;
    } else {
        moveprice = (distanceMiles * parseFloat("<?php echo get_option('defult_price_move_cost_mile'); ?>"));
    }

    let totalPrice = 0;
    let price = 0;
    let specialPrice = 0;

    rows.forEach(row => {
        const specialMoveCost = parseFloat(row.querySelector('td:nth-child(4)').innerText);
        const quantity = parseInt(row.querySelector('.quantity').value);
        
        if (specialMoveCost === 0) {
            if (sumvolume <= MinCFMoveThreshold) {
                price = defultpriceMinCFMove;
            } else {
                price = (sumvolume * defaultPricePerCF);
            }
        } else {
            specialPrice += (specialMoveCost * quantity);
        }
    });
    
    totalPrice += price + moveprice + specialPrice;
    

     


    // Calculate totalPriceRange automatically
    let totalPriceRange = Math.ceil(totalPrice / 500) * 50;
    
    // Update movePriceField with totalPrice and totalPriceRange
    const movePriceField = document.getElementById('move-price');
    movePriceField.value = isNaN(totalPrice) ? '' : '$' + totalPrice.toFixed(2) + ' ⇄ ' + '$' + parseInt(totalPrice + totalPriceRange);

   console.log('calculatePrice moveprice : ', moveprice);
   console.log("calculatePrice totalPrice : ", totalPrice);

    updateSpecialMoveCostNotification();
    updateExceptionsforMoveCost();
    displayTotalPrice();
}




document.getElementById('pickup_date').addEventListener('change', function() {
    console.log('Pickup date changed!'); 
    fetchPricesradios();
});



document.getElementById('use-destination-address').addEventListener('change', calculatePrice);


// Call calculatePrice whenever the quantity changes
document.getElementById('idd-calculator').addEventListener('input', function(event) {
    if (event.target.classList.contains('quantity')) {
        calculatePrice();
    }
});
document.getElementById('destination_address').addEventListener('change', calculatePrice);
document.getElementById('origin_address').addEventListener('change', calculatePrice);

// Function to update the exceptions for move cost notification
function updateExceptionsforMoveCost() {
    const ExceptionsforMoveCost = document.getElementById('ExceptionsforMoveCost');
        //const notificationbulletscontainer = document.getElementById('notification-bullets-container');
    const MinCFMoveThreshold = parseFloat("<?php echo get_option('min_cf_move_threshold'); ?>");
    const sumvolume = parseFloat(document.getElementById('sum-volume').value.replace(' CF', ''));
    if (sumvolume <= MinCFMoveThreshold) {
        localStorage.setItem('ExceptionsforMoveCost', '<?php echo get_option('exceptions_for_move_cost_notification'); ?>');
        document.getElementById('ExceptionsforMoveCost').style.display = 'block';
        document.getElementById('notification-button').style.display = 'block';

        var selectElement = "<?php echo get_option('exceptions_for_move_cost_notification'); ?>";
        var inputElement = document.getElementById('email-exceptions_for_move_cost_notification');
        inputElement.value = selectElement;

        updateNotificationCount();
    } else {
        localStorage.setItem('ExceptionsforMoveCost', ' ');
        document.getElementById('ExceptionsforMoveCost').style.display = 'none';

        var selectElement = " ";
        var inputElement = document.getElementById('email-exceptions_for_move_cost_notification');
        inputElement.value = selectElement;

        updateNotificationCount();

    }
}
document.getElementById('move-price').addEventListener('input', updateExceptionsforMoveCost);





document.addEventListener('DOMContentLoaded', function() {
    const destinationCheckbox = document.getElementById('use-destination-address');
    const destinationAddressContainer = document.getElementById('address-destination-container');  
    destinationCheckbox.addEventListener('change', function() {
        destinationAddressContainer.style.display = this.checked ? 'block' : 'none';
        document.getElementById('sum').value = " ";   
    });
});


// Function to set inputs params on local Storage
function saveFormData() {
    const tableContent = document.getElementById('calculator-table-content').value;
    localStorage.setItem('calculatorFormData', tableContent);
    const tablespecialContent = document.getElementById('calculator-special-table-content').value;
    localStorage.setItem('calculatorStorageSpecialFormData', tablespecialContent);
    const firstname = document.getElementById('first-name').value;
    localStorage.setItem('first-name', firstname);
    const lastname = document.getElementById('last-name').value;
    localStorage.setItem('last-name', lastname);
    const userphone = document.getElementById('user-phone').value;
    localStorage.setItem('user-phone', userphone);
    const useremail = document.getElementById('user-email').value;
    localStorage.setItem('user-email', useremail);
    const origin_address = document.getElementById('origin_address').value;
    localStorage.setItem('origin-address', origin_address);
    const pickup_date = document.getElementById('pickup_date').value;
    localStorage.setItem('pickup-date', pickup_date);
    const pickup_time = document.getElementById('pickup_time').value;
    localStorage.setItem('pickup-time', pickup_time);
    const destination_address = document.getElementById('destination_address').value;
    localStorage.setItem('destination-address', destination_address);
    const destination_up_address = document.getElementById('destination_up_address').value;
    localStorage.setItem('destination-up-address', destination_up_address);
    const destination_off_address = document.getElementById('destination_off_address').value;
    localStorage.setItem('destination-off-address', destination_off_address);
    const distance = document.getElementById('mils').value;
    localStorage.setItem('distance', distance);
    const totalQuantity = document.getElementById('SumSpecialAndRegularQuantity').value;
    localStorage.setItem('total-quantity', totalQuantity);
    const quantity = document.getElementById('sum-CfAndQuantity').value;
    localStorage.setItem('quantity', quantity);
    const specialQuantity = document.getElementById('sum-totalspecial-quantity').value;
    localStorage.setItem('special-quantity', specialQuantity);
    const estimatedCost = document.getElementById('total-move-storage-price').value;
    localStorage.setItem('estimated-cost', estimatedCost);
    const estimatedStorageCost = document.getElementById('sum').value;
    localStorage.setItem('estimated-storage-cost', estimatedStorageCost);
    const estimatedMoveCost = document.getElementById('move-price').value;
    localStorage.setItem('estimated-move-cost', estimatedMoveCost);
    const originaddressaccess = document.getElementById('origin-address-access').value;
    localStorage.setItem('origin-address-access', originaddressaccess);
    const originaddressfloor = document.getElementById('origin-address-floor').value;
    localStorage.setItem('origin-address-floor', originaddressfloor);
    const originaddressapt = document.getElementById('origin-address-apt').value;
    localStorage.setItem('origin-address-apt', originaddressapt);
    const destinationaddressaccess = document.getElementById('destination-address-access').value;
    localStorage.setItem('destination-address-access', destinationaddressaccess);
    const destinationaddressfloor = document.getElementById('destination-address-floor').value;
    localStorage.setItem('destination-address-floor', destinationaddressfloor);
    const destinationaddressapt = document.getElementById('destination-address-apt').value;
    localStorage.setItem('destination-address-apt', destinationaddressapt);
    const destinationupaddressaccess = document.getElementById('destination-up-address-access').value;
    localStorage.setItem('destination-up-address-access', destinationupaddressaccess);
    const destinationupaddressfloor = document.getElementById('destination-up-address-floor').value;
    localStorage.setItem('destination-up-address-floor', destinationupaddressfloor);
    const destinationupaddressapt = document.getElementById('destination-up-address-apt').value;
    localStorage.setItem('destination-up-address-apt', destinationupaddressapt);
    const destinationoffaddressaccess = document.getElementById('destination-off-address-access').value;
    localStorage.setItem('destination-off-address-access', destinationoffaddressaccess);
    const destinationoffaddressfloor = document.getElementById('destination-off-address-floor').value;
    localStorage.setItem('destination-off-address-floor', destinationoffaddressfloor);
    const destinationoffaddressapt = document.getElementById('destination-off-address-apt').value;
    localStorage.setItem('destination-off-address-apt', destinationoffaddressapt);

    const stairspriceall = document.getElementById('stairs-price-all').value;
    localStorage.setItem('stairs-price-all', stairspriceall);
}

document.addEventListener('input', function() {
    saveFormData();
});





// Function to get url Params (online and hero forms mps2)
    document.addEventListener('DOMContentLoaded', function() {
       const urlParams = new URLSearchParams(window.location.search);

        setTimeout(() => {
              document.getElementById('first-name').value = urlParams.get('mpsLeadNameFirst');
              document.getElementById('last-name').value = urlParams.get('mpsLeadNameLast');
              document.getElementById('user-phone').value = urlParams.get('mpsLeadPhone');
              document.getElementById('user-email').value = urlParams.get('mpsLeadEmail');
              document.getElementById('origin_address').value = urlParams.get('mpsLeadStoragePickupAddress');
              document.getElementById('pickup_date').value = urlParams.get('mpsLeadStoragePickupDate');
              document.getElementById('origin-address-access').value = urlParams.get('mpsLeadStoragePickupAccess');
              document.getElementById('pickup_time').value = urlParams.get('mpsLeadStoragePickupTime');
              document.getElementById('destination-address-access').value = urlParams.get('mpsLeadStorageDropoffAccess');
              document.getElementById('destination_up_address').value = urlParams.get('mpsLeadStorageExtraPickupAddress');
              document.getElementById('destination-up-address-access').value = urlParams.get('mpsLeadStorageExtraPickupAccess');
              document.getElementById('destination_off_address').value = urlParams.get('mpsLeadStorageExtraDropoffAddress');
              document.getElementById('destination-off-address-access').value = urlParams.get('mpsLeadStorageExtraDropoffAccess');

              if (urlParams.get('mpsCheckMovingToStorage') !== "" ) {
               console.log("storage")
              }else{
                console.log("moving")
                document.getElementById('destination_address').value = urlParams.get('mpsLeadStorageDropoffAddress');
              }


            const originaddress = document.getElementById('origin_address').value;
            if (originaddress.trim() !== "") {
                document.getElementById('map').style.display = '';
                document.getElementById('mapView').style.display = '';
                calculateDistanceView()
                calculateDistance()
            }

            const destinationAddress = document.getElementById('destination_address').value;
            if (destinationAddress.trim() !== "") {
                document.getElementById('use-destination-address').checked = true;
                document.getElementById('address-destination-container').style.display = 'block';
                calculateDistanceView()
                calculateDistance()

            }

            const destinationupaddress = document.getElementById('destination_up_address').value;
            if (destinationupaddress.trim() !== "") {
                document.getElementById('use-extra-up-destination-address').checked = true;
                document.getElementById('address-Additional-extra-up-destination-container').style.display = 'block';
                calculateDistanceView()
                calculateDistance()


            }

            const destinationoffaddress = document.getElementById('destination_off_address').value;
            if (destinationoffaddress.trim() !== "") {
                document.getElementById('use-extra-off-destination-address').checked = true;
                document.getElementById('address-Additional-extra-off-destination-container').style.display = 'block';
                calculateDistanceView()
                calculateDistance()


            }

            const firstname = document.getElementById('first-name').value;
            const lastname = document.getElementById('last-name').value;
            const userphone = document.getElementById('user-phone').value;
            const useremail = document.getElementById('user-email').value;
            if (firstname.trim() !== "" && lastname.trim() !== ""  && userphone.trim() !== ""  && useremail.trim() !== ""  ) {
                document.getElementById('next-step-one').click();
            }

        }, 1500); 
    });

    if ("<?php echo $atts['type']; ?>" === 'moving') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                document.getElementById("use-destination-address").checked = true;
                document.getElementById("address-destination-container").style.display = '';
            }, 2000); 
        });
    }


    updateSum();

    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('idd_calculator', 'idd_calculator_product_table_shortcode');


// Function for get first_name params to ty page (from local storage)
function calculator_get_param_first_name() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Retrieve data from localStorage
            const formData = localStorage.getItem('first-name');
            const tableParamInput = document.getElementById('calculator_get_first_name');

            // Display the data
            document.getElementById('calculator-form-first-name').innerText = formData || '';
            if (tableParamInput) {
                tableParamInput.value = formData || '';
            }
        });
    </script>

    <div id="calculator-form-first-name"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_get_param_first_name', 'calculator_get_param_first_name');


// Funtion for get first_name params to ty page (from local storage)
function calculator_get_param_first_name_view() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Retrieve data from localStorage
            const formData = localStorage.getItem('first-name');
            const tableParamInput = document.getElementById('calculator_get_first_name_view');

            // Display the data
            document.getElementById('calculator-form-first-name-view').innerText = formData || '';
            if (tableParamInput) {
                tableParamInput.value = formData || '';
            }
        });
    </script>

    <div id="calculator-form-first-name-view"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_get_param_first_name_view', 'calculator_get_param_first_name_view');


// Function for get last_name params to ty page (from local storage)
function calculator_get_param_last_name() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Retrieve data from localStorage
            const formData = localStorage.getItem('last-name');
            const tableParamInput = document.getElementById('calculator_get_last_name');

            // Display the data
            document.getElementById('calculator-form-last-name').innerText = formData || '';
            if (tableParamInput) {
                tableParamInput.value = formData || '';
            }
        });
    </script>

    <div id="calculator-form-last-name"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_get_param_last_name', 'calculator_get_param_last_name');


// Function for get user_phone params to ty page (from local storage)
function calculator_get_param_user_phone() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Retrieve data from localStorage
            const formData = localStorage.getItem('user-phone');
            const tableParamInput = document.getElementById('calculator_get_user_phone');

            // Display the data
            document.getElementById('calculator-form-user-phone').innerText = formData || '';
            if (tableParamInput) {
                tableParamInput.value = formData || '';
            }
        });
    </script>

    <div id="calculator-form-user-phone"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_get_param_user_phone', 'calculator_get_param_user_phone');


// Function for get user_email params to ty page (from local storage)
function calculator_get_param_user_email() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Retrieve data from localStorage
            const formData = localStorage.getItem('user-email');
            const tableParamInput = document.getElementById('calculator_get_user_email');

            // Display the data
            document.getElementById('calculator-form-user-email').innerText = formData || '';
            if (tableParamInput) {
                tableParamInput.value = formData || '';
            }
        });
    </script>

    <div id="calculator-form-user-email"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_get_param_user_email', 'calculator_get_param_user_email');


// Function for get origin address params to ty page (from local storage)
function calculator_get_param_origin_address() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Retrieve data from localStorage
            const formData = localStorage.getItem('origin-address');
            const tableParamInput = document.getElementById('calculator_get_origin_address');

            // Display the data
            document.getElementById('calculator-form-origin-address').innerText = formData || '';
            if (tableParamInput) {
                tableParamInput.value = formData || '';
            }
        });
    </script>

    <div id="calculator-form-origin-address"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_get_param_origin_address', 'calculator_get_param_origin_address');


// Function for get pickup date params to ty page (from local storage)
function calculator_get_param_pickup_date() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Retrieve data from localStorage
            const formData = localStorage.getItem('pickup-date');
            const tableParamInput = document.getElementById('calculator_get_pickup_date');

            // Display the data
            document.getElementById('calculator-form-pickup-date').innerText = formData || '';
            if (tableParamInput) {
                tableParamInput.value = formData || '';
            }
        });
    </script>

    <div id="calculator-form-pickup-date"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_get_param_pickup_date', 'calculator_get_param_pickup_date');


// Function for get pickup time params to ty page (from local storage)
function calculator_get_param_pickup_time() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Retrieve data from localStorage
            const formData = localStorage.getItem('pickup-time');
            const tableParamInput = document.getElementById('calculator_get_pickup_time');

            // Display the data
            document.getElementById('calculator-form-pickup-time').innerText = formData || '';
            if (tableParamInput) {
                tableParamInput.value = formData || '';
            }
        });
    </script>

    <div id="calculator-form-pickup-time"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_get_param_pickup_time', 'calculator_get_param_pickup_time');


// Function for get destination address params to ty page (from local storage)
function calculator_get_param_destination_address() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Retrieve data from localStorage
            const formData = localStorage.getItem('destination-address');
            const tableParamInput = document.getElementById('calculator_get_destination_address');

            // Display the data
            document.getElementById('calculator-form-destination-address').innerText = formData || '';
            if (tableParamInput) {
                tableParamInput.value = formData || '';
            }
        });
    </script>

    <div id="calculator-form-destination-address"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_get_param_destination_address', 'calculator_get_param_destination_address');


// Function for get additional pickup address params to ty page (from local storage)
function calculator_get_param_destination_up_address() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Retrieve data from localStorage
            const formData = localStorage.getItem('destination-up-address');
            const tableParamInput = document.getElementById('calculator_get_destination_up_address');

            // Display the data
            document.getElementById('calculator-form-destination-up-address').innerText = formData || '';
            if (tableParamInput) {
                tableParamInput.value = formData || '';
            }
        });
    </script>

    <div id="calculator-form-destination-up-address"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_get_param_destination_up_address', 'calculator_get_param_destination_up_address');


// Function for get additional drop off address params to ty page (from local storage)
function calculator_get_param_destination_off_address() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Retrieve data from localStorage
            const formData = localStorage.getItem('destination-off-address');
            const tableParamInput = document.getElementById('calculator_get_destination_off_address');

            // Display the data
            document.getElementById('calculator-form-destination-off-address').innerText = formData || '';
            if (tableParamInput) {
                tableParamInput.value = formData || '';
            }
        });
    </script>

    <div id="calculator-form-destination-off-address"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_get_param_destination_off_address', 'calculator_get_param_destination_off_address');


// Function for get special item table params to ty page (from local storage)
function calculator_special_get_param() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formData = localStorage.getItem('calculatorStorageSpecialFormData');
            const tableParamInput = document.getElementById('calculator_get_storage_specia_table_param');

            document.getElementById('calculator-storage-special-form-data').innerText = formData || '';
            if (tableParamInput) {
                tableParamInput.value = formData || '';
            }
        });
    </script>

    <div id="calculator-storage-special-form-data"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_special_get_param', 'calculator_special_get_param');


// Function for get item table params to ty page (from local storage)
function calculator_get_param() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Retrieve data from localStorage
            const formData = localStorage.getItem('calculatorFormData');
            const tableParamInput = document.getElementById('calculator_get_storage_table_param');

            // Display the data
            document.getElementById('calculator-form-data').innerText = formData || '';
            if (tableParamInput) {
                tableParamInput.value = formData || '';
            }
        });
    </script>

    <div id="calculator-form-data"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_get_param', 'calculator_get_param');


// Function for retrieving and displaying distance parameter
function calculator_get_param_distance() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const distance = localStorage.getItem('distance');
            const tableParamInput = document.getElementById('calculator_get_distance');
            document.getElementById('calculator-form-distance').innerText = distance || '';
            if (tableParamInput) {
                tableParamInput.value = distance || '';
            }
        });
    </script>

    <div id="calculator-form-distance"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_get_param_distance', 'calculator_get_param_distance');


// Function for retrieving and displaying total quantity parameter
function calculator_get_param_total_quantity() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const totalQuantity = localStorage.getItem('total-quantity');
            const tableParamInput = document.getElementById('calculator_get_total_quantity');
            document.getElementById('calculator-form-total-quantity').innerText = totalQuantity || '';
            if (tableParamInput) {
                tableParamInput.value = totalQuantity || '';
            }
        });
    </script>

    <div id="calculator-form-total-quantity"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_get_param_total_quantity', 'calculator_get_param_total_quantity');


// Function for retrieving and displaying quantity parameter
function calculator_get_param_quantity() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const quantity = localStorage.getItem('quantity');
            const tableParamInput = document.getElementById('calculator_get_quantity');
            document.getElementById('calculator-form-quantity').innerText = quantity || '';
            if (tableParamInput) {
                tableParamInput.value = quantity || '';
            }
        });
    </script>

    <div id="calculator-form-quantity"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_get_param_quantity', 'calculator_get_param_quantity');


// Function for retrieving and displaying special quantity parameter
function calculator_get_param_special_quantity() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const specialQuantity = localStorage.getItem('special-quantity');
            const tableParamInput = document.getElementById('calculator_get_special_quantity');
            document.getElementById('calculator-form-special-quantity').innerText = specialQuantity || '';
            if (tableParamInput) {
                tableParamInput.value = specialQuantity || '';
            }
        });
    </script>

    <div id="calculator-form-special-quantity"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_get_param_special_quantity', 'calculator_get_param_special_quantity');


// Function for retrieving and displaying estimated cost parameter
function calculator_get_param_estimated_cost() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const estimatedCost = localStorage.getItem('estimated-cost');
            const tableParamInput = document.getElementById('calculator_get_estimated_cost');
            document.getElementById('calculator-form-estimated-cost').innerText = estimatedCost || '';
            if (tableParamInput) {
                tableParamInput.value = estimatedCost || '';
            }
        });
    </script>

    <div id="calculator-form-estimated-cost"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_get_param_estimated_cost', 'calculator_get_param_estimated_cost');


// Function for retrieving and displaying estimated storage cost parameter
function calculator_get_param_estimated_storage_cost() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const estimatedStorageCost = localStorage.getItem('estimated-storage-cost');
            const tableParamInput = document.getElementById('calculator_get_estimated_storage_cost');
            document.getElementById('calculator-form-estimated-storage-cost').innerText = estimatedStorageCost || '';
            if (tableParamInput) {
                tableParamInput.value = estimatedStorageCost || '';
            }
        });
    </script>

    <div id="calculator-form-estimated-storage-cost"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_get_param_estimated_storage_cost', 'calculator_get_param_estimated_storage_cost');


// Function for retrieving and displaying estimated move cost parameter
function calculator_get_param_estimated_move_cost() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const estimatedMoveCost = localStorage.getItem('estimated-move-cost');
            const tableParamInput = document.getElementById('calculator_get_estimated_move_cost');
            document.getElementById('calculator-form-estimated-move-cost').innerText = estimatedMoveCost || '';
            if (tableParamInput) {
                tableParamInput.value = estimatedMoveCost || '';
            }
        });
    </script>

    <div id="calculator-form-estimated-move-cost"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_get_param_estimated_move_cost', 'calculator_get_param_estimated_move_cost');


// Function for retrieving and displaying estimated Move Cost notification parameter
function specialMoveCostnotification() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const estimatedMoveCost = localStorage.getItem('specialMoveCostnotification');
            const tableParamInput = document.getElementById('specialMoveCostnotification');
            document.getElementById('calculator-specialMoveCostnotification').innerText = estimatedMoveCost || '';
            if (tableParamInput) {
                tableParamInput.value = estimatedMoveCost || '';
            }
        });
    </script>

    <div id="calculator-specialMoveCostnotification"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_get_param_specialMoveCostnotification', 'specialMoveCostnotification');

// Function for retrieving and displaying Exceptions for Move Cost notification parameter
function ExceptionsforMoveCost() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const estimatedMoveCost = localStorage.getItem('ExceptionsforMoveCost');
            const tableParamInput = document.getElementById('ExceptionsforMoveCost');
            document.getElementById('calculator-ExceptionsforMoveCost').innerText = estimatedMoveCost || '';
            if (tableParamInput) {
                tableParamInput.value = estimatedMoveCost || '';
            }
        });
    </script>

    <div id="calculator-ExceptionsforMoveCost"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_get_param_ExceptionsforMoveCost', 'ExceptionsforMoveCost');

// Function for retrieving and displaying max Distace Miles notification parameter
function maxDistaceMiles() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const estimatedMoveCost = localStorage.getItem('maxDistaceMiles');
            const tableParamInput = document.getElementById('maxDistaceMiles');
            document.getElementById('calculator-maxDistaceMiles').innerText = estimatedMoveCost || '';
            if (tableParamInput) {
                tableParamInput.value = estimatedMoveCost || '';
            }
        });
    </script>

    <div id="calculator-maxDistaceMiles"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_get_param_maxDistaceMiles', 'maxDistaceMiles');


// Function for retrieving and displaying Insterstate notification parameter
function Insterstate() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const estimatedMoveCost = localStorage.getItem('Insterstate');
            const tableParamInput = document.getElementById('Insterstate');
            document.getElementById('calculator-Insterstate').innerText = estimatedMoveCost || '';
            if (tableParamInput) {
                tableParamInput.value = estimatedMoveCost || '';
            }
        });
    </script>

    <div id="calculator-Insterstate"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_get_param_Insterstate', 'Insterstate');

// Function for retrieving and displaying Insterstate notification parameter
function stairs_notification() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const estimatedMoveCost = localStorage.getItem('stairsnotification');
            const tableParamInput = document.getElementById('stairs-notification');
            document.getElementById('calculator-stairs-notification').innerText = estimatedMoveCost || '';
            if (tableParamInput) {
                tableParamInput.value = estimatedMoveCost || '';
            }
        });
    </script>

    <div id="calculator-stairs-notification"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_get_param_stairs_notification', 'stairs_notification');

// Function for retrieving and displaying user Special Items parameter
function userSpecialItems() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const estimatedMoveCost = localStorage.getItem('userSpecialItems');
            const tableParamInput = document.getElementById('userSpecialItems');
            document.getElementById('calculator-userSpecialItems').innerText = estimatedMoveCost || '';
            if (tableParamInput) {
                tableParamInput.value = estimatedMoveCost || '';
            }
        });
    </script>

    <div id="calculator-userSpecialItems"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_get_param_userSpecialItems', 'userSpecialItems');

// Function for retrieving and displaying origin address access parameter
function origin_address_access() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const estimatedMoveCost = localStorage.getItem('origin-address-access');
            const tableParamInput = document.getElementById('origin-address-access');
            document.getElementById('calculator-origin-address-access').innerText = estimatedMoveCost || '';
            if (tableParamInput) {
                tableParamInput.value = estimatedMoveCost || '';
            }
        });
    </script>

    <div id="calculator-origin-address-access"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_get_param_origin_address_access', 'origin_address_access');



// Function for retrieving and displaying origin address floor parameter
function origin_address_floor() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const estimatedMoveCost = localStorage.getItem('origin-address-floor');
            const tableParamInput = document.getElementById('origin-address-floor');
            document.getElementById('calculator-origin-address-floor').innerText = estimatedMoveCost || '';
            if (tableParamInput) {
                tableParamInput.value = estimatedMoveCost || '';
            }
        });
    </script>

    <div id="calculator-origin-address-floor"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_get_param_origin_address_floor', 'origin_address_floor');

// Function for retrieving and displaying origin address apt parameter
function origin_address_apt() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const estimatedMoveCost = localStorage.getItem('origin-address-apt');
            const tableParamInput = document.getElementById('origin-address-apt');
            document.getElementById('calculator-origin-address-apt').innerText = estimatedMoveCost || '';
            if (tableParamInput) {
                tableParamInput.value = estimatedMoveCost || '';
            }
        });
    </script>

    <div id="calculator-origin-address-apt"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_get_param_origin_address_apt', 'origin_address_apt');

// Function for retrieving and displaying destination address access parameter
function destination_address_access() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const estimatedMoveCost = localStorage.getItem('destination-address-access');
            const tableParamInput = document.getElementById('destination-address-access');
            document.getElementById('calculator-destination-address-access').innerText = estimatedMoveCost || '';
            if (tableParamInput) {
                tableParamInput.value = estimatedMoveCost || '';
            }
        });
    </script>

    <div id="calculator-destination-address-access"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_get_param_destination_address_access', 'destination_address_access');

// Function for retrieving and displaying destination address floor parameter
function destination_address_floor() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const estimatedMoveCost = localStorage.getItem('destination-address-floor');
            const tableParamInput = document.getElementById('destination-address-floor');
            document.getElementById('calculator-destination-address-floor').innerText = estimatedMoveCost || '';
            if (tableParamInput) {
                tableParamInput.value = estimatedMoveCost || '';
            }
        });
    </script>

    <div id="calculator-destination-address-floor"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_get_param_destination_address_floor', 'destination_address_floor');

// Function for retrieving and displaying destination address apt parameter
function destination_address_apt() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const estimatedMoveCost = localStorage.getItem('destination-address-apt');
            const tableParamInput = document.getElementById('destination-address-apt');
            document.getElementById('calculator-destination-address-apt').innerText = estimatedMoveCost || '';
            if (tableParamInput) {
                tableParamInput.value = estimatedMoveCost || '';
            }
        });
    </script>

    <div id="calculator-destination-address-apt"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_get_param_destination_address_apt', 'destination_address_apt');

// Function for retrieving and displaying destination up address access parameter
function destination_up_address_access() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const estimatedMoveCost = localStorage.getItem('destination-up-address-access');
            const tableParamInput = document.getElementById('destination-up-address-access');
            document.getElementById('calculator-destination-up-address-access').innerText = estimatedMoveCost || '';
            if (tableParamInput) {
                tableParamInput.value = estimatedMoveCost || '';
            }
        });
    </script>

    <div id="calculator-destination-up-address-access"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_get_param_destination_up_address_access', 'destination_up_address_access');

// Function for retrieving and displaying destination up address floor parameter
function destination_up_address_floor() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const estimatedMoveCost = localStorage.getItem('destination-up-address-floor');
            const tableParamInput = document.getElementById('destination-up-address-floor');
            document.getElementById('calculator-destination-up-address-floor').innerText = estimatedMoveCost || '';
            if (tableParamInput) {
                tableParamInput.value = estimatedMoveCost || '';
            }
        });
    </script>

    <div id="calculator-destination-up-address-floor"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_get_param_destination_up_address_floor', 'destination_up_address_floor');

// Function for retrieving and displaying destination up address apt parameter
function destination_up_address_apt() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const estimatedMoveCost = localStorage.getItem('destination-up-address-apt');
            const tableParamInput = document.getElementById('destination-up-address-apt');
            document.getElementById('calculator-destination-up-address-apt').innerText = estimatedMoveCost || '';
            if (tableParamInput) {
                tableParamInput.value = estimatedMoveCost || '';
            }
        });
    </script>

    <div id="calculator-destination-up-address-apt"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_get_param_destination_up_address_apt', 'destination_up_address_apt');

// Function for retrieving and displaying destination off address access parameter
function destination_off_address_access() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const estimatedMoveCost = localStorage.getItem('destination-off-address-access');
            const tableParamInput = document.getElementById('destination-off-address-access');
            document.getElementById('calculator-destination-off-address-access').innerText = estimatedMoveCost || '';
            if (tableParamInput) {
                tableParamInput.value = estimatedMoveCost || '';
            }
        });
    </script>

    <div id="calculator-destination-off-address-access"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_get_param_destination_off_address_access', 'destination_off_address_access');

// Function for retrieving and displaying destination off address floor parameter
function destination_off_address_floor() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const estimatedMoveCost = localStorage.getItem('destination-off-address-floor');
            const tableParamInput = document.getElementById('destination-off-address-floor');
            document.getElementById('calculator-destination-off-address-floor').innerText = estimatedMoveCost || '';
            if (tableParamInput) {
                tableParamInput.value = estimatedMoveCost || '';
            }
        });
    </script>

    <div id="calculator-destination-off-address-floor"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_get_param_destination_off_address_floor', 'destination_off_address_floor');

// Function for retrieving and displaying destination off address apt parameter
function destination_off_address_apt() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const estimatedMoveCost = localStorage.getItem('destination-off-address-apt');
            const tableParamInput = document.getElementById('destination-off-address-apt');
            document.getElementById('calculator-destination-off-address-apt').innerText = estimatedMoveCost || '';
            if (tableParamInput) {
                tableParamInput.value = estimatedMoveCost || '';
            }
        });
    </script>

    <div id="calculator-destination-off-address-apt"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_get_param_destination_off_address_apt', 'destination_off_address_apt');

// Function for retrieving and displaying stairs price all parameter
function stairs_price_all() {
    ob_start();
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const estimatedMoveCost = localStorage.getItem('stairs-price-all');
            const tableParamInput = document.getElementById('stairs-price-all');
            document.getElementById('calculator-stairs-price-all').innerText = estimatedMoveCost || '';
            if (tableParamInput) {
                tableParamInput.value = estimatedMoveCost || '';
            }
        });
    </script>

    <div id="calculator-stairs-price-all"></div>
<?php
    return ob_get_clean();
}
add_shortcode('calculator_get_param_stairs_price_all', 'stairs_price_all');

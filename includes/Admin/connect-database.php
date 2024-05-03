<?php
    // Retrieve saved credentials from options
    $db_host = get_option('remote_db_host');
    $db_user = get_option('remote_db_user');
    $db_password = get_option('remote_db_password');
    $db_name = get_option('remote_db_name');
    ?>

    <div class="wrap">
        <h1>Remote Database Connection</h1>
        <form method="post" action="">
            <label for="db_host">Database Host:</label>
            <input type="text" name="db_host" id="db_host" value="<?php echo esc_attr($db_host); ?>" required><br><br>

            <label for="db_user">Database Username:</label>
            <input type="text" name="db_user" id="db_user" value="<?php echo esc_attr($db_user); ?>" required><br><br>

            <label for="db_password">Database Password:</label>
            <input type="password" name="db_password" id="db_password" value="<?php echo esc_attr($db_password); ?>"><br><br>

            <label for="db_name">Database Name:</label>
            <input type="text" name="db_name" id="db_name" value="<?php echo esc_attr($db_name); ?>" required><br><br>

            <input type="submit" name="connect_db" value="Connect to Remote Database">
        </form>
    </div>

<?php
    if (isset($_POST['connect_db'])) {
        $db_host = $_POST['db_host'];
        $db_user = $_POST['db_user'];
        $db_password = $_POST['db_password'];
        $db_name = $_POST['db_name'];

        // Attempt to connect to the remote database
        $remote_connection = new mysqli($db_host, $db_user, $db_password, $db_name);

        // Check connection
        if ($remote_connection->connect_error) {
            echo "<div class='error'><p>Error: Unable to connect to the remote database. " . $remote_connection->connect_error . "</p></div>";
        } else {
            echo "<div class='updated'><p>Success: Connected to the remote database.</p></div>";


            $order_tables = [
                   'wc_orders' => 'order_id',
                   'wc_orders_meta' => 'meta_id',
                   'wc_order_addresses' => 'addresses_id',
                    'wc_order_stats' => 'order_stats_id',
                   'woocommerce_order_itemmeta' => 'itemmeta_id',
                   'woocommerce_order_items' => 'order_item_id'
            ];

            foreach ( $order_tables as $table_name => $table_extra_column ) {
                global $wpdb;
                $table_name = $wpdb->prefix . $table_name;

                $get_table_structure_query = "SHOW CREATE TABLE $table_name";
                $table_structure_result = $wpdb->get_row($get_table_structure_query);

                // Check if query executed successfully
                if ($table_structure_result) {
                    // Extract the SQL query for creating the table
                    $create_table_query = $table_structure_result->{'Create Table'};
                } else {
                    die("Error fetching table structure: " . $wpdb->last_error);
                }

                // Check connection
                if ($remote_connection->connect_error) {
                    die("Connection to remote database failed: " . $remote_connection->connect_error);
                }

                // Step 3: Execute query to create table in remote database
                $create_remote_table_query = $remote_connection->query($create_table_query);

                // Check if query executed successfully
                if ($create_remote_table_query === TRUE) {
                    $alter_table_query = "ALTER TABLE {$table_name} ADD COLUMN $table_extra_column VARCHAR(255)";
                    $alter_table_result = $remote_connection->query($alter_table_query);
                    echo $alter_table_query;
                    echo "<div class='updated'><p>Success: Table created successfully.</p></div>";
                } else {
                    echo "Error creating table in remote database: " . $remote_connection->error;
                }
            }

            // Save form data to WordPress options
            update_option('remote_db_host', $db_host);
            update_option('remote_db_user', $db_user);
            update_option('remote_db_password', $db_password);
            update_option('remote_db_name', $db_name);
        }

        // Close connection
        $remote_connection->close();
    }
?>

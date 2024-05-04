<?php

namespace Order\Backup\Admin;

class AdminAjax {

    public function __construct() {
        add_action('wp_ajax_backup_order_remote_db', [$this, 'backup_order_remote_db']);
        add_action( 'wp_ajax_total_order_count_action', [$this, 'total_order_count_action'] );
    }

    /**
     * AJAX action to retrieve the total count of orders within a date range.
     */
    function total_order_count_action() {
        // Global WordPress database access class
        global $wpdb;

        // Retrieve start and end dates from POST data
        $start_date = $_POST['startDate'];
        $end_date = $_POST['endDate'];

        // Prepare the SQL query to count orders within the date range
        $query = $wpdb->prepare("
            SELECT COUNT(id)
            FROM {$wpdb->prefix}wc_orders
            WHERE date_created_gmt BETWEEN %s AND %s
        ", $start_date, $end_date);

        // Execute the query to get the total order count
        $order_count = $wpdb->get_var($query);

        // Send the total order count as a JSON success response
        wp_send_json_success($order_count);

        // Terminate script execution
        wp_die();
    }

    /**
     * Backup orders to a remote database and delete them from the local database.
     */
    function backup_order_remote_db() {
        // Establish remote database connection
        $db_host = get_option('remote_db_host');
        $db_user = get_option('remote_db_user');
        $db_password = get_option('remote_db_password');
        $db_name = get_option('remote_db_name');
        
        $remote_connection = new \mysqli($db_host, $db_user, $db_password, $db_name);
        
        // Check for connection errors
        if ($remote_connection->connect_error) {
            die("Connection to remote database failed: " . $remote_connection->connect_error);
        }

        // Get start and end dates from POST data
        $order_start_date = $_POST['startDate'];
        $order_end_date = $_POST['endDate'];

        // Construct the date query
        $date_query = array(
            'after'     => $order_start_date,
            'before'    => $order_end_date,
            'inclusive' => true, // Include orders with dates matching $start_date and $end_date
        );

        // Get orders based on the date range
        $orders = wc_get_orders( array(
            'date_query' => $date_query,
            'limit'      => 1,
        ) );

        foreach( $orders as $order ) {
            // Define the variables to insert
            $id = $order->get_id();
            $status = $order->get_status();
            $currency = $order->get_currency();
            $type = $order->get_type();
            $total_amount = $order->get_total();
            $billing_email = $order->get_billing_email();
            $order_notes = $order->get_customer_note();
            
            // Prepare SQL statement with placeholders
            $sql = "INSERT INTO wp_wc_orders (id, status, currency, type, total_amount, billing_email, customer_note) 
            VALUES (?, ?, ?, ?, ?, ?, ? )";
            
            // Prepare and bind parameters
            $stmt = $remote_connection->prepare($sql);
            $stmt->bind_param("sssisss", $id, $status, $currency, $type, $total_amount, $billing_email, $order_notes );
            
            // Execute the statement
            if ($stmt->execute()) {
                // Delete the order from the local database
                global $wpdb;
                $table_name = $wpdb->prefix . 'wc_orders';
                $query = $wpdb->prepare("DELETE FROM $table_name WHERE id = %d", $id);
                $deleted = $wpdb->query($query);

                // Log deletion result
                error_log(print_r($deleted, true));
            } else {
                // Log SQL execution error
                error_log($stmt->error);
            }
            
            // Close statement
            $stmt->close();
        }
        
        // Close remote connection
        $remote_connection->close();

        // Send success response
        wp_send_json_success(true);

        // Terminate execution
        wp_die();
    }

}
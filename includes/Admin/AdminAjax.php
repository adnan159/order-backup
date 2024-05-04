<?php

namespace Order\Backup\Admin;

class AdminAjax {

    public function __construct() {
        add_action('wp_ajax_backup_order_remote_db', [$this, 'backup_order_remote_db']);
    }

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

        error_log(print_r($_POST, true));

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
            $stmt->bind_param("sssisss",$id, $status, $currency, $type, $total_amount,$billing_email, $order_notes );
            
            // Execute the statement
            if ($stmt->execute()) {
                echo "Data inserted successfully.";
                wc_delete_order_item( absint( $id ) );
            } else {
                echo "Error inserting data: " . $stmt->error;
            }
            
            // Close statement and connection
            $stmt->close();
        }
        
        $remote_connection->close();
    }
}
<?php

namespace Order\Backup\Admin;

class SyncOrder {
    public function __construct() {
        $this->sync_order();
    }

    public function sync_order(){
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
        
        // Define the variables to insert
        $status = 'completed';
        $total = 15;
        $currency = 'USD';
        $id = 90;
        
        // Prepare SQL statement with placeholders
        $sql = "INSERT INTO wp_wc_orders (id, status, currency, total_amount) VALUES (?, ?, ?, ?)";
        
        // Prepare and bind parameters
        $stmt = $remote_connection->prepare($sql);
        $stmt->bind_param("issi",$id, $status, $currency, $total);
        
        // Execute the statement
        if ($stmt->execute()) {
            echo "Data inserted successfully.";
        } else {
            echo "Error inserting data: " . $stmt->error;
        }
        
        // Close statement and connection
        $stmt->close();
        $remote_connection->close();
    }        
    
}
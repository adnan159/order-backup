<?php

namespace Order\Backup\Admin;

class Enqueue {
    
    public function __construct() {
        add_action('admin_enqueue_scripts', [$this, 'backup_order_enqueue'], 10, 1);
    }

    public function backup_order_enqueue( $page ) {
        if( $page === 'backup-order_page_order-sync' ) {
            // Enqueue the JavaScript file.
            wp_enqueue_script('backup-order-sync-script', Order_Backup_ASSETS . '/admin/js/sync-order.js', [], false, true);
        }
    }
}
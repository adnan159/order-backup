<?php

namespace Order\Backup\Admin;

/**
 * The menu handler class
 */
class Menu {

    public function __construct() {
        add_action('admin_menu', [$this, 'admin_menu']);
    }

    public function admin_menu()
    {
        add_menu_page(
            __('Backup Order', 'backup-order'),
            __('Backup Order', 'backup-order'),
            'manage_options',
            'backup-order',
            [$this, 'plugin_page']
        );
    }

    /**
     * Callback function to display the plugin page.
     */
    public function plugin_page() {
        require_once 'connect-database.php';
    }
}

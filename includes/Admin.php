<?php

namespace Order\Backup;

class Admin
{
    public function __construct() {
        $this->load_admin_components();
    }

    /**
     * Load admin components (Menu, Enqueue, AdminAjax, and AdminAction).
     *
     * @return void
     */
    public function load_admin_components() {
        new Admin\Menu(); // Create the admin menu.
        // new Admin\Enqueue(); // Enqueue styles and scripts for the admin dashboard.
        // new Admin\AdminAjax(); // Handle admin-related AJAX actions.
        // new Admin\AdminAction(); // Define admin actions and custom fields.
    }
}
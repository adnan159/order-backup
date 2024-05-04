<?php

namespace Order\Backup\Admin;

class AdminAjax {

    public function __construct() {
        add_action('wp_ajax_backup_order_remote_db', [$this, 'backup_order_remote_db']);
    }

    function backup_order_remote_db() {
        error_log(print_r($_POST, true));
        wp_send_json_success('success stor');
    }
}
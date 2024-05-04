<?php

/**
 * Plugin Name: Order Backup
 * Plugin URI: https://osmanhaideradnan.wordpress.com/
 * Description: An WooCommerce tookit to backup order into another remote database.
 * Version: 1.0.0
 * Requires at least: 5.2
 * Requires PHP: 7.4
 * Author: Osman Haider Adnan
 * Author URI: https://osmanhaideradnan.wordpress.com/
 * Text Domain: order-backup
 * Domain Path: /languages
 * @package     Order Backup
 * @author      Adnan <osmanhaider159@gmail.com>
 * @copyright   Copyright (C) 2024 Osman Haider Adnan. All rights reserved.
 * @license     GPLv3 or later
 * @since       1.0.0
 * @package WooCommerce
 */

// Ensure the file is not accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Check if the Composer autoload file exists, and if not, show an error message.
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    die('Please run `composer install` in the main plugin directory.');
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Plugin main class
 */
final class Order_Backup {

    /**
     * Define plugin version
     * 
     * @var string
     */
    const version = '1.0.0';

    // Private constructor to enforce singleton pattern.
    private function __construct()
    {
        $this->define_constants();

        // Register activation hook.
        // register_activation_hook(__FILE__, [$this, 'activate']);

        // Hook into the 'plugins_loaded' action to initialize the plugin.
        add_action('plugins_loaded', [$this, 'init_plugin']);
    }

    /**
     * Singleton instance
     *
     * @return Order_Backup
     */
    public static function init() {
        static $instance = false;

        if (!$instance) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Define constants for the plugin.
     *
     * @return void
     */
    function define_constants() {
        define('Order_Backup_VERSION', self::version);
        define('Order_Backup_FILE', __FILE__);
        define('Order_Backup_DIR_PATH', plugin_dir_path(Order_Backup_FILE));
        define('Order_Backup_URL', plugin_dir_url(Order_Backup_FILE));
        define('Order_Backup_ASSETS', Order_Backup_URL . 'assets');
    }

    /**
     * Do stuff upon plugin activation
     *
     * @return void
     */
    // function activate() {
    //     // Set an option to store the installation time.
    //     $installed = get_option('ascode_installed_time');

    //     if (!$installed) {
    //         update_option('ascode_installed_time', time());
    //     }
    // }

    /**
     * Initialize the plugin
     *
     * @return void
     */
    public function init_plugin() {
        if (is_admin()) {
            new Order\Backup\Admin();
        }
    }
}

/**
 * Initialize the main plugin.
 *
 * @return Order_Backup
 */
function Order_Backup() {
    return Order_Backup::init();
}

// Kick-off the plugin.
Order_Backup();



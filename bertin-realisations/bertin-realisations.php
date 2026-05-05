<?php
/**
 * Plugin Name: Bertin Réalisations
 * Description: Gestion et présentation des réalisations Bertin Pailley.
 * Version: 1.0.0
 * Author: Bertin Pailley
 * Text Domain: bertin-realisations
 */

if (!defined('ABSPATH')) {
    exit;
}

define('BERTIN_REALISATIONS_VERSION', '1.0.0');
define('BERTIN_REALISATIONS_PATH', plugin_dir_path(__FILE__));
define('BERTIN_REALISATIONS_URL', plugin_dir_url(__FILE__));
define('BERTIN_REALISATIONS_FILE', __FILE__);

require_once BERTIN_REALISATIONS_PATH . 'includes/class-bertin-activator.php';
require_once BERTIN_REALISATIONS_PATH . 'includes/class-bertin-projects.php';
require_once BERTIN_REALISATIONS_PATH . 'includes/class-bertin-importer.php';
require_once BERTIN_REALISATIONS_PATH . 'includes/class-bertin-images.php';
require_once BERTIN_REALISATIONS_PATH . 'includes/class-bertin-rest.php';
require_once BERTIN_REALISATIONS_PATH . 'includes/class-bertin-shortcode.php';
require_once BERTIN_REALISATIONS_PATH . 'includes/class-bertin-admin.php';

register_activation_hook(__FILE__, ['Bertin_Realisations_Activator', 'activate']);

add_action('plugins_loaded', static function () {
    Bertin_Realisations_Projects::init();
    Bertin_Realisations_Importer::init();
    Bertin_Realisations_Images::init();
    Bertin_Realisations_REST::init();
    Bertin_Realisations_Shortcode::init();
    Bertin_Realisations_Admin::init();
});

<?php
if (!defined('ABSPATH')) {
    exit;
}

class Bertin_Realisations_Activator {
    public static function activate(): void {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset = $wpdb->get_charset_collate();
        $projects = $wpdb->prefix . 'bertin_projects';
        $images = $wpdb->prefix . 'bertin_project_images';

        $sql1 = "CREATE TABLE {$projects} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            client_name VARCHAR(255) NOT NULL,
            postal_code VARCHAR(20) DEFAULT '',
            city VARCHAR(255) DEFAULT '',
            address TEXT NULL,
            building_type VARCHAR(255) DEFAULT '',
            description LONGTEXT NULL,
            latitude DECIMAL(10,8) NULL,
            longitude DECIMAL(11,8) NULL,
            wave VARCHAR(100) DEFAULT 'Vague 1',
            status VARCHAR(20) DEFAULT 'active',
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            KEY idx_status (status),
            KEY idx_wave (wave),
            KEY idx_postal (postal_code)
        ) {$charset};";

        $sql2 = "CREATE TABLE {$images} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            project_id BIGINT UNSIGNED NOT NULL,
            attachment_id BIGINT UNSIGNED NULL,
            image_url TEXT NULL,
            original_filename VARCHAR(255) DEFAULT '',
            sort_order INT DEFAULT 0,
            created_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            KEY idx_project (project_id)
        ) {$charset};";

        dbDelta($sql1);
        dbDelta($sql2);

        add_option('bertin_realisations_version', BERTIN_REALISATIONS_VERSION);
        add_option('bertin_realisations_settings', Bertin_Realisations_Projects::default_settings());

        if (!get_option('bertin_realisations_vague1_imported')) {
            Bertin_Realisations_Importer::import_vague_1();
            update_option('bertin_realisations_vague1_imported', true);
        }
    }
}

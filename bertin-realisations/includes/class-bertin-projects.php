<?php
if (!defined('ABSPATH')) {
    exit;
}

class Bertin_Realisations_Projects {
    public static function init(): void {}

    public static function table(): string {
        global $wpdb;
        return $wpdb->prefix . 'bertin_projects';
    }

    public static function images_table(): string {
        global $wpdb;
        return $wpdb->prefix . 'bertin_project_images';
    }

    public static function default_settings(): array {
        return [
            'app_title' => 'Nos Réalisations',
            'subtitle' => 'Charpentes métalliques',
            'primary_color' => '#111111',
            'accent_color' => '#F2C300',
            'logo_id' => 0,
            'per_page' => 30,
            'enable_map' => 1,
            'main_button_text' => 'Voir détail',
            'unauthorized_message' => 'Cette application est réservée aux utilisateurs autorisés.',
            'require_login' => 1,
        ];
    }

    public static function get_settings(): array {
        return wp_parse_args((array) get_option('bertin_realisations_settings', []), self::default_settings());
    }

    public static function save_project(array $data, int $id = 0): int {
        global $wpdb;
        $now = current_time('mysql');
        $row = [
            'client_name' => sanitize_text_field($data['client_name'] ?? ''),
            'postal_code' => sanitize_text_field($data['postal_code'] ?? ''),
            'city' => sanitize_text_field($data['city'] ?? ''),
            'address' => sanitize_text_field($data['address'] ?? ''),
            'building_type' => sanitize_text_field($data['building_type'] ?? ''),
            'description' => sanitize_textarea_field($data['description'] ?? ''),
            'latitude' => $data['latitude'] !== '' ? (float) $data['latitude'] : null,
            'longitude' => $data['longitude'] !== '' ? (float) $data['longitude'] : null,
            'wave' => sanitize_text_field($data['wave'] ?? 'Vague 1'),
            'status' => in_array($data['status'] ?? 'active', ['active', 'inactive'], true) ? $data['status'] : 'active',
            'updated_at' => $now,
        ];
        if ($id > 0) {
            $wpdb->update(self::table(), $row, ['id' => $id]);
            return $id;
        }
        $row['created_at'] = $now;
        $wpdb->insert(self::table(), $row);
        return (int) $wpdb->insert_id;
    }
}

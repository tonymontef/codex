<?php
if (!defined('ABSPATH')) {
    exit;
}

class Bertin_Realisations_Importer {
    public static function init(): void {}

    public static function import_vague_1(): int {
        $file = BERTIN_REALISATIONS_PATH . 'data/vague-1.csv';
        if (!file_exists($file)) {
            return 0;
        }
        return self::import_csv_file($file, 'Vague 1', 'insert_only');
    }

    public static function import_csv_file(string $path, string $wave = 'Vague 2', string $mode = 'insert_only'): int {
        if (!is_readable($path)) {
            return 0;
        }
        $handle = fopen($path, 'r');
        if (!$handle) {
            return 0;
        }

        $header = fgetcsv($handle, 0, ';');
        if (!$header) {
            fclose($handle);
            return 0;
        }
        $map = self::detect_map($header);
        $count = 0;
        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            $data = self::row_to_project($row, $map, $wave);
            if (empty($data['client_name'])) {
                continue;
            }
            $existing_id = self::find_duplicate($data);
            if ($existing_id && $mode === 'update') {
                Bertin_Realisations_Projects::save_project($data, $existing_id);
                $count++;
            } elseif (!$existing_id) {
                Bertin_Realisations_Projects::save_project($data);
                $count++;
            }
        }
        fclose($handle);
        return $count;
    }

    public static function detect_map(array $header): array {
        $map = [];
        foreach ($header as $idx => $column) {
            $name = strtoupper(trim((string) $column));
            if ($name === 'NOMCLI') $map['client_name'] = $idx;
            if ($name === 'CP') $map['postal_code'] = $idx;
            if ($name === 'ADRESSE') $map['address'] = $idx;
            if ($name === 'BATIMENT') $map['building_type'] = $idx;
        }
        return $map;
    }

    private static function row_to_project(array $row, array $map, string $wave): array {
        $address = $row[$map['address']] ?? '';
        return [
            'client_name' => sanitize_text_field($row[$map['client_name']] ?? ''),
            'postal_code' => sanitize_text_field($row[$map['postal_code']] ?? ''),
            'city' => sanitize_text_field($address),
            'address' => sanitize_text_field($address),
            'building_type' => sanitize_text_field($row[$map['building_type']] ?? ''),
            'description' => '',
            'latitude' => '',
            'longitude' => '',
            'wave' => sanitize_text_field($wave),
            'status' => 'active',
        ];
    }

    private static function find_duplicate(array $data): int {
        global $wpdb;
        $table = Bertin_Realisations_Projects::table();
        $id = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$table} WHERE client_name=%s AND postal_code=%s AND building_type=%s LIMIT 1", $data['client_name'], $data['postal_code'], $data['building_type']));
        if ($id) return (int) $id;
        $id = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$table} WHERE client_name=%s AND address=%s LIMIT 1", $data['client_name'], $data['address']));
        return (int) $id;
    }
}

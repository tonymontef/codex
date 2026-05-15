<?php
if (!defined('ABSPATH')) exit;

class Bertin_Realisations_Admin {
    public static function init(): void {
        add_action('admin_menu',[__CLASS__,'menu']);
        add_action('admin_enqueue_scripts',[__CLASS__,'assets']);
        add_action('admin_post_bertin_import_csv',[__CLASS__,'import_csv']);
        add_action('admin_post_bertin_save_project',[__CLASS__,'save_project']);
        add_action('admin_post_bertin_delete_project',[__CLASS__,'delete_project']);
        add_action('admin_post_bertin_save_settings',[__CLASS__,'save_settings']);
        add_action('admin_post_bertin_reimport_v1',[__CLASS__,'reimport_v1']);
    }
    public static function menu(): void {
        add_menu_page('Bertin Réalisations','Bertin Réalisations','manage_options','bertin-realisations',[__CLASS__,'dashboard'],'dashicons-building');
        add_submenu_page('bertin-realisations','Tableau de bord','Tableau de bord','manage_options','bertin-realisations',[__CLASS__,'dashboard']);
        add_submenu_page('bertin-realisations','Projets','Projets','manage_options','bertin-projects',[__CLASS__,'projects']);
        add_submenu_page('bertin-realisations','Import CSV','Import CSV','manage_options','bertin-import',[__CLASS__,'import']);
        add_submenu_page('bertin-realisations','Images','Images','manage_options','bertin-images',[__CLASS__,'images']);
        add_submenu_page('bertin-realisations','Réglages','Réglages','manage_options','bertin-settings',[__CLASS__,'settings']);
    }
    public static function assets(): void {
        wp_enqueue_style('bertin-admin', BERTIN_REALISATIONS_URL.'assets/css/admin.css', [], BERTIN_REALISATIONS_VERSION);
        wp_enqueue_script('bertin-admin', BERTIN_REALISATIONS_URL.'assets/js/admin.js', [], BERTIN_REALISATIONS_VERSION, true);
    }
    public static function dashboard(): void { include BERTIN_REALISATIONS_PATH.'templates/admin-dashboard.php'; }
    public static function projects(): void { include BERTIN_REALISATIONS_PATH.'templates/admin-projects.php'; }
    public static function import(): void { include BERTIN_REALISATIONS_PATH.'templates/admin-import.php'; }
    public static function images(): void { include BERTIN_REALISATIONS_PATH.'templates/admin-images.php'; }
    public static function settings(): void { include BERTIN_REALISATIONS_PATH.'templates/admin-settings.php'; }

    public static function import_csv(): void {
        self::guard(); check_admin_referer('bertin_import_csv');
        if (empty($_FILES['csv_file']['tmp_name'])) { wp_safe_redirect(admin_url('admin.php?page=bertin-import&err=1')); exit; }
        $f = wp_handle_upload($_FILES['csv_file'], ['test_form'=>false,'mimes'=>['csv'=>'text/csv','txt'=>'text/plain']]);
        if (!empty($f['file'])) {
            $count = Bertin_Realisations_Importer::import_csv_file($f['file'], sanitize_text_field($_POST['wave_name'] ?? 'Vague 2'), sanitize_text_field($_POST['mode'] ?? 'insert_only'));
            wp_safe_redirect(admin_url('admin.php?page=bertin-import&imported='.$count)); exit;
        }
        wp_safe_redirect(admin_url('admin.php?page=bertin-import&err=1')); exit;
    }

    public static function save_project(): void {
        self::guard(); check_admin_referer('bertin_save_project');
        $id = (int)($_POST['id'] ?? 0);
        Bertin_Realisations_Projects::save_project($_POST, $id);
        wp_safe_redirect(admin_url('admin.php?page=bertin-projects&saved=1')); exit;
    }

    public static function delete_project(): void {
        self::guard(); check_admin_referer('bertin_delete_project');
        global $wpdb; $table = Bertin_Realisations_Projects::table();
        $id = (int)($_POST['id'] ?? 0);
        if ($id) $wpdb->delete($table, ['id'=>$id], ['%d']);
        wp_safe_redirect(admin_url('admin.php?page=bertin-projects&deleted=1')); exit;
    }

    public static function save_settings(): void {
        self::guard(); check_admin_referer('bertin_save_settings');
        $settings = Bertin_Realisations_Projects::get_settings();
        $settings['app_title'] = sanitize_text_field($_POST['app_title'] ?? $settings['app_title']);
        $settings['subtitle'] = sanitize_text_field($_POST['subtitle'] ?? $settings['subtitle']);
        $settings['primary_color'] = sanitize_hex_color($_POST['primary_color'] ?? $settings['primary_color']) ?: '#111111';
        $settings['accent_color'] = sanitize_hex_color($_POST['accent_color'] ?? $settings['accent_color']) ?: '#F2C300';
        $settings['per_page'] = max(1, (int)($_POST['per_page'] ?? 30));
        $settings['enable_map'] = !empty($_POST['enable_map']) ? 1 : 0;
        $settings['main_button_text'] = sanitize_text_field($_POST['main_button_text'] ?? 'Voir détail');
        $settings['unauthorized_message'] = sanitize_text_field($_POST['unauthorized_message'] ?? 'Cette application est réservée aux utilisateurs autorisés.');
        $settings['require_login'] = !empty($_POST['require_login']) ? 1 : 0;
        update_option('bertin_realisations_settings', $settings);
        wp_safe_redirect(admin_url('admin.php?page=bertin-settings&saved=1')); exit;
    }

    public static function reimport_v1(): void {
        self::guard(); check_admin_referer('bertin_reimport_v1');
        $count = Bertin_Realisations_Importer::import_vague_1();
        update_option('bertin_realisations_vague1_imported', true);
        wp_safe_redirect(admin_url('admin.php?page=bertin-import&v1='.$count)); exit;
    }

    private static function guard(): void { if (!current_user_can('manage_options')) wp_die('forbidden'); }
}

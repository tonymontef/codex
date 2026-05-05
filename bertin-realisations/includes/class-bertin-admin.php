<?php
if (!defined('ABSPATH')) exit;
class Bertin_Realisations_Admin {
    public static function init(): void { add_action('admin_menu',[__CLASS__,'menu']); add_action('admin_enqueue_scripts',[__CLASS__,'assets']); add_action('admin_post_bertin_import_csv',[__CLASS__,'import_csv']); }
    public static function menu(): void {
        add_menu_page('Bertin Réalisations','Bertin Réalisations','manage_options','bertin-realisations',[__CLASS__,'dashboard'],'dashicons-building');
        add_submenu_page('bertin-realisations','Tableau de bord','Tableau de bord','manage_options','bertin-realisations',[__CLASS__,'dashboard']);
        add_submenu_page('bertin-realisations','Projets','Projets','manage_options','bertin-projects',[__CLASS__,'projects']);
        add_submenu_page('bertin-realisations','Import CSV','Import CSV','manage_options','bertin-import',[__CLASS__,'import']);
        add_submenu_page('bertin-realisations','Images','Images','manage_options','bertin-images',[__CLASS__,'images']);
        add_submenu_page('bertin-realisations','Réglages','Réglages','manage_options','bertin-settings',[__CLASS__,'settings']);
    }
    public static function assets(): void { wp_enqueue_style('bertin-admin', BERTIN_REALISATIONS_URL.'assets/css/admin.css', [], BERTIN_REALISATIONS_VERSION); }
    public static function dashboard(): void { include BERTIN_REALISATIONS_PATH.'templates/admin-dashboard.php'; }
    public static function projects(): void { include BERTIN_REALISATIONS_PATH.'templates/admin-projects.php'; }
    public static function import(): void { include BERTIN_REALISATIONS_PATH.'templates/admin-import.php'; }
    public static function images(): void { include BERTIN_REALISATIONS_PATH.'templates/admin-images.php'; }
    public static function settings(): void { include BERTIN_REALISATIONS_PATH.'templates/admin-settings.php'; }
    public static function import_csv(): void {
        if (!current_user_can('manage_options')) wp_die('forbidden');
        check_admin_referer('bertin_import_csv');
        if (empty($_FILES['csv_file']['tmp_name'])) wp_safe_redirect(admin_url('admin.php?page=bertin-import')); exit;
        $f = wp_handle_upload($_FILES['csv_file'], ['test_form'=>false,'mimes'=>['csv'=>'text/csv','txt'=>'text/plain']]);
        if (!empty($f['file'])) {
            Bertin_Realisations_Importer::import_csv_file($f['file'], sanitize_text_field($_POST['wave_name'] ?? 'Vague 2'), sanitize_text_field($_POST['mode'] ?? 'insert_only'));
        }
        wp_safe_redirect(admin_url('admin.php?page=bertin-import&imported=1')); exit;
    }
}

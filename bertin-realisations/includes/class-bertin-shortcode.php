<?php
if (!defined('ABSPATH')) exit;
class Bertin_Realisations_Shortcode {
    public static function init(): void { add_shortcode('bertin_realisations', [__CLASS__, 'render']); add_action('wp_enqueue_scripts',[__CLASS__,'assets']); }
    public static function assets(): void {
        wp_register_style('leaflet','https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',[], '1.9.4');
        wp_register_script('leaflet','https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',[], '1.9.4', true);
        wp_register_style('bertin-front', BERTIN_REALISATIONS_URL.'assets/css/front.css', [], BERTIN_REALISATIONS_VERSION);
        wp_register_script('bertin-front', BERTIN_REALISATIONS_URL.'assets/js/front.js', ['leaflet'], BERTIN_REALISATIONS_VERSION, true);
    }
    public static function render(): string {
        $settings = Bertin_Realisations_Projects::get_settings();
        if (!is_user_logged_in()) return '<div class="bertin-locked">'.esc_html($settings['unauthorized_message']).'</div>';
        wp_enqueue_style('leaflet'); wp_enqueue_script('leaflet'); wp_enqueue_style('bertin-front'); wp_enqueue_script('bertin-front');
        wp_localize_script('bertin-front','BertinApp',['rest'=>esc_url_raw(rest_url('bertin/v1'))]);
        ob_start(); include BERTIN_REALISATIONS_PATH.'templates/front-app.php'; return ob_get_clean();
    }
}

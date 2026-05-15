<?php
if (!defined('ABSPATH')) exit;
class Bertin_Realisations_REST {
    public static function init(): void { add_action('rest_api_init', [__CLASS__, 'register_routes']); }
    public static function register_routes(): void {
        register_rest_route('bertin/v1', '/projects', ['methods'=>'GET','callback'=>[__CLASS__,'projects'],'permission_callback'=>[__CLASS__,'can_access']]);
        register_rest_route('bertin/v1', '/project/(?P<id>\d+)', ['methods'=>'GET','callback'=>[__CLASS__,'project'],'permission_callback'=>[__CLASS__,'can_access']]);
        register_rest_route('bertin/v1', '/settings', ['methods'=>'GET','callback'=>fn()=>Bertin_Realisations_Projects::get_settings(),'permission_callback'=>[__CLASS__,'can_access']]);
    }
    public static function can_access(): bool {
        $s=Bertin_Realisations_Projects::get_settings();
        if (empty($s['require_login'])) return true;
        return is_user_logged_in();
    }
    public static function projects(WP_REST_Request $r) {
        global $wpdb; $t=Bertin_Realisations_Projects::table();
        $limit = min(1000, max(1, (int)$r->get_param('limit') ?: 300));
        return $wpdb->get_results($wpdb->prepare("SELECT p.*, (SELECT COUNT(*) FROM ".Bertin_Realisations_Projects::images_table()." i WHERE i.project_id=p.id) as images_count FROM {$t} p WHERE status=%s ORDER BY client_name ASC LIMIT %d",'active',$limit), ARRAY_A);
    }
    public static function project(WP_REST_Request $r) {
        global $wpdb; $id=(int)$r['id']; $t=Bertin_Realisations_Projects::table();
        $p=$wpdb->get_row($wpdb->prepare("SELECT * FROM {$t} WHERE id=%d AND status='active'",$id), ARRAY_A);
        if(!$p) return new WP_Error('not_found','Projet introuvable',['status'=>404]);
        $img=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".Bertin_Realisations_Projects::images_table()." WHERE project_id=%d ORDER BY sort_order ASC,id ASC",$id), ARRAY_A);
        $p['images']=$img; return $p;
    }
}

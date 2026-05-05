<?php
if (!defined('ABSPATH')) exit;
class Bertin_Realisations_REST {
    public static function init(): void { add_action('rest_api_init', [__CLASS__, 'register_routes']); }
    public static function register_routes(): void {
        register_rest_route('bertin/v1', '/projects', ['methods'=>'GET','callback'=>[__CLASS__,'projects'],'permission_callback'=>'__return_true']);
        register_rest_route('bertin/v1', '/project/(?P<id>\d+)', ['methods'=>'GET','callback'=>[__CLASS__,'project'],'permission_callback'=>'__return_true']);
        register_rest_route('bertin/v1', '/settings', ['methods'=>'GET','callback'=>fn()=>Bertin_Realisations_Projects::get_settings(),'permission_callback'=>'__return_true']);
    }
    public static function projects() {
        global $wpdb; $t=Bertin_Realisations_Projects::table();
        return $wpdb->get_results($wpdb->prepare("SELECT p.*, (SELECT COUNT(*) FROM ".Bertin_Realisations_Projects::images_table()." i WHERE i.project_id=p.id) as images_count FROM {$t} p WHERE status=%s ORDER BY id DESC LIMIT 1000",'active'), ARRAY_A);
    }
    public static function project(WP_REST_Request $r) {
        global $wpdb; $id=(int)$r['id']; $t=Bertin_Realisations_Projects::table();
        $p=$wpdb->get_row($wpdb->prepare("SELECT * FROM {$t} WHERE id=%d AND status='active'",$id), ARRAY_A);
        if(!$p) return new WP_Error('not_found','Projet introuvable',['status'=>404]);
        $img=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".Bertin_Realisations_Projects::images_table()." WHERE project_id=%d ORDER BY sort_order ASC,id ASC",$id), ARRAY_A);
        $p['images']=$img; return $p;
    }
}

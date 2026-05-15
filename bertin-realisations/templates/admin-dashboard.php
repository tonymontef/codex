<?php
if (!current_user_can('manage_options')) wp_die('forbidden');
global $wpdb; $t=Bertin_Realisations_Projects::table(); $i=Bertin_Realisations_Projects::images_table();
$total=(int)$wpdb->get_var("SELECT COUNT(*) FROM {$t}");
$active=(int)$wpdb->get_var("SELECT COUNT(*) FROM {$t} WHERE status='active'");
$with_photos=(int)$wpdb->get_var("SELECT COUNT(DISTINCT project_id) FROM {$i}");
$without=max(0,$total-$with_photos);
$photos=(int)$wpdb->get_var("SELECT COUNT(*) FROM {$i}");
$waves=(int)$wpdb->get_var("SELECT COUNT(DISTINCT wave) FROM {$t}");
?>
<div class="wrap bertin-admin-wrap"><h1>Tableau de bord</h1>
<div class="bertin-kpis"><div class="bertin-kpi">Total<br><strong><?php echo esc_html($total); ?></strong></div><div class="bertin-kpi">Actifs<br><strong><?php echo esc_html($active); ?></strong></div><div class="bertin-kpi">Sans photo<br><strong><?php echo esc_html($without); ?></strong></div><div class="bertin-kpi">Photos<br><strong><?php echo esc_html($photos); ?></strong></div><div class="bertin-kpi">Vagues<br><strong><?php echo esc_html($waves); ?></strong></div></div>
<p><a class="button button-primary" href="<?php echo esc_url(admin_url('admin.php?page=bertin-import')); ?>">Importer</a> <a class="button" href="<?php echo esc_url(admin_url('admin.php?page=bertin-projects&action=new')); ?>">Ajouter un projet</a></p></div>

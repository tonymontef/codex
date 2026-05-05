<?php global $wpdb; $t=Bertin_Realisations_Projects::table(); $i=Bertin_Realisations_Projects::images_table();
$total=(int)$wpdb->get_var("SELECT COUNT(*) FROM {$t}"); $active=(int)$wpdb->get_var("SELECT COUNT(*) FROM {$t} WHERE status='active'");
$photos=(int)$wpdb->get_var("SELECT COUNT(*) FROM {$i}");
?>
<div class="wrap"><h1>Bertin Réalisations</h1><p>Total: <?php echo esc_html($total); ?> | Actifs: <?php echo esc_html($active); ?> | Photos: <?php echo esc_html($photos); ?></p></div>

<?php
if (!current_user_can('manage_options')) wp_die('forbidden');
global $wpdb; $t=Bertin_Realisations_Projects::table();
$action = sanitize_text_field($_GET['action'] ?? 'list');
if ($action==='edit' || $action==='new') {
  $id=(int)($_GET['id'] ?? 0); $p=$id?Bertin_Realisations_Projects::get_project($id):['id'=>0,'client_name'=>'','postal_code'=>'','city'=>'','address'=>'','building_type'=>'','description'=>'','latitude'=>'','longitude'=>'','wave'=>'Vague 1','status'=>'active'];
  ?>
  <div class="wrap"><h1><?php echo $id?'Modifier':'Ajouter'; ?> un projet</h1>
  <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
  <?php wp_nonce_field('bertin_save_project'); ?><input type="hidden" name="action" value="bertin_save_project"><input type="hidden" name="id" value="<?php echo (int)$p['id']; ?>">
  <table class="form-table"><tr><th>Nom</th><td><input name="client_name" class="regular-text" required value="<?php echo esc_attr($p['client_name']); ?>"></td></tr>
  <tr><th>Code postal</th><td><input name="postal_code" value="<?php echo esc_attr($p['postal_code']); ?>"></td></tr>
  <tr><th>Ville</th><td><input name="city" value="<?php echo esc_attr($p['city']); ?>"></td></tr>
  <tr><th>Adresse</th><td><input name="address" class="regular-text" value="<?php echo esc_attr($p['address']); ?>"></td></tr>
  <tr><th>Type bâtiment</th><td><input name="building_type" value="<?php echo esc_attr($p['building_type']); ?>"></td></tr>
  <tr><th>Description</th><td><textarea name="description" rows="4" class="large-text"><?php echo esc_textarea($p['description']); ?></textarea></td></tr>
  <tr><th>Latitude</th><td><input name="latitude" value="<?php echo esc_attr($p['latitude']); ?>"></td></tr><tr><th>Longitude</th><td><input name="longitude" value="<?php echo esc_attr($p['longitude']); ?>"></td></tr>
  <tr><th>Vague</th><td><input name="wave" value="<?php echo esc_attr($p['wave']); ?>"></td></tr>
  <tr><th>Statut</th><td><select name="status"><option value="active" <?php selected($p['status'],'active'); ?>>Actif</option><option value="inactive" <?php selected($p['status'],'inactive'); ?>>Inactif</option></select></td></tr></table>
  <p><button class="button button-primary">Enregistrer</button> <a class="button" href="<?php echo esc_url(admin_url('admin.php?page=bertin-projects')); ?>">Retour</a></p></form></div><?php
  return;
}
$s=sanitize_text_field($_GET['s'] ?? '');
$where='1=1';$args=[];
if($s!==''){ $where.=' AND (client_name LIKE %s OR address LIKE %s OR postal_code LIKE %s OR building_type LIKE %s)';$like='%'.$wpdb->esc_like($s).'%';$args=array_fill(0,4,$like);} 
$sql="SELECT * FROM {$t} WHERE {$where} ORDER BY id DESC LIMIT 200";
$rows=$args?$wpdb->get_results($wpdb->prepare($sql,...$args),ARRAY_A):$wpdb->get_results($sql,ARRAY_A);
?>
<div class="wrap"><h1>Projets <a href="<?php echo esc_url(admin_url('admin.php?page=bertin-projects&action=new')); ?>" class="page-title-action">Ajouter</a></h1>
<form method="get"><input type="hidden" name="page" value="bertin-projects"><input name="s" value="<?php echo esc_attr($s); ?>" placeholder="Recherche"> <button class="button">Filtrer</button></form>
<table class="widefat striped"><thead><tr><th>ID</th><th>Nom</th><th>CP</th><th>Adresse</th><th>Type</th><th>Vague</th><th>Statut</th><th></th></tr></thead><tbody>
<?php foreach($rows as $r): ?><tr><td><?php echo (int)$r['id']; ?></td><td><?php echo esc_html($r['client_name']); ?></td><td><?php echo esc_html($r['postal_code']); ?></td><td><?php echo esc_html($r['address']); ?></td><td><?php echo esc_html($r['building_type']); ?></td><td><?php echo esc_html($r['wave']); ?></td><td><?php echo esc_html($r['status']); ?></td><td><a class="button" href="<?php echo esc_url(admin_url('admin.php?page=bertin-projects&action=edit&id='.(int)$r['id'])); ?>">Modifier</a>
<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline"><?php wp_nonce_field('bertin_delete_project'); ?><input type="hidden" name="action" value="bertin_delete_project"><input type="hidden" name="id" value="<?php echo (int)$r['id']; ?>"><button class="button-link-delete" onclick="return confirm('Supprimer ?')">Supprimer</button></form></td></tr><?php endforeach; ?>
</tbody></table></div>

<?php if (!current_user_can('manage_options')) wp_die('forbidden'); $s=Bertin_Realisations_Projects::get_settings(); ?>
<div class="wrap"><h1>Réglages</h1>
<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>"><?php wp_nonce_field('bertin_save_settings'); ?><input type="hidden" name="action" value="bertin_save_settings">
<table class="form-table"><tr><th>Titre</th><td><input name="app_title" value="<?php echo esc_attr($s['app_title']); ?>"></td></tr>
<tr><th>Sous-titre</th><td><input name="subtitle" value="<?php echo esc_attr($s['subtitle']); ?>"></td></tr>
<tr><th>Couleur principale</th><td><input type="color" name="primary_color" value="<?php echo esc_attr($s['primary_color']); ?>"></td></tr>
<tr><th>Couleur accent</th><td><input type="color" name="accent_color" value="<?php echo esc_attr($s['accent_color']); ?>"></td></tr>
<tr><th>Projets/page</th><td><input type="number" name="per_page" value="<?php echo (int)$s['per_page']; ?>"></td></tr>
<tr><th>Activer carte</th><td><label><input type="checkbox" name="enable_map" value="1" <?php checked(!empty($s['enable_map'])); ?>> Oui</label></td></tr>
<tr><th>Bouton principal</th><td><input name="main_button_text" value="<?php echo esc_attr($s['main_button_text']); ?>"></td></tr>
<tr><th>Message accès</th><td><input class="regular-text" name="unauthorized_message" value="<?php echo esc_attr($s['unauthorized_message']); ?>"></td></tr>
<tr><th>Accès connecté requis</th><td><label><input type="checkbox" name="require_login" value="1" <?php checked(!empty($s['require_login'])); ?>> Oui</label></td></tr></table>
<p><button class="button button-primary">Enregistrer</button></p></form></div>

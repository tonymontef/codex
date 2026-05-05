<div class="wrap"><h1>Import CSV</h1>
<?php if(isset($_GET['imported'])): ?><div class="notice notice-success"><p><?php echo (int)$_GET['imported']; ?> projets importés.</p></div><?php endif; ?>
<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data">
<?php wp_nonce_field('bertin_import_csv'); ?><input type="hidden" name="action" value="bertin_import_csv" />
<input type="file" name="csv_file" accept=".csv" required />
<input type="text" name="wave_name" value="Vague 2" />
<select name="mode"><option value="insert_only">Ajouter uniquement les nouveaux</option><option value="update">Mettre à jour les doublons</option></select>
<button class="button button-primary">Importer</button></form>
<hr><form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" onsubmit="return confirm('Réimporter Vague 1 ?');"><?php wp_nonce_field('bertin_reimport_v1'); ?><input type="hidden" name="action" value="bertin_reimport_v1"><button class="button">Réimporter Vague 1</button></form>
</div>

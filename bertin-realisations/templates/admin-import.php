<div class="wrap"><h1>Import CSV</h1>
<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data">
<?php wp_nonce_field('bertin_import_csv'); ?><input type="hidden" name="action" value="bertin_import_csv" />
<input type="file" name="csv_file" accept=".csv" required />
<input type="text" name="wave_name" value="Vague 2" />
<select name="mode"><option value="insert_only">Ajouter</option><option value="update">Mettre à jour</option></select>
<button class="button button-primary">Importer</button></form></div>

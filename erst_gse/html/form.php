<h1>Google Spreadsheet API Settings</h1>
<?php if(get_transient('error')): ?>
	<div id="message" class="updated notice error is-dismissible">
		<p><?= get_transient('error'); ?></p>
	</div>
<?php endif; ?>
<?php if(get_transient('success')): ?>
	<div id="message" class="updated notice notice-success is-dismissible">
		<p><?= get_transient('success'); ?></p>
	</div>
<?php endif; ?>
<form action="" method="post" id="erst-gse">
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label for="client-id">Client ID</label>
				</th>
				<td>
					<input name="client_id" type="text" id="client-id" aria-describedby="client-id-description"
					value="<?= esc_attr( get_option( 'erst_gse_client_id' ) ); ?>"
					class="regular-text" required>
					<p class="description" id="client-id-description">Setup client id.</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="client-secret">Client Secret</label>
				</th>
				<td>
					<input name="client_secret" type="text" id="client-secret" aria-describedby="client-secret-description"
					value="<?= esc_attr( get_option( 'erst_gse_client_secret' ) ); ?>"
					class="regular-text" required>
					<p class="description" id="client-secret-description">Setup client secret.</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="developer-key">Developer Key</label>
				</th>
				<td>
					<input name="developer_key" type="text" id="developer-key" aria-describedby="developer-key-description"
					value="<?= esc_attr( get_option( 'erst_gse_developer_key' ) ); ?>"
					class="regular-text" required>
					<p class="description" id="developer-key-description">Setup developer key.</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="spreadsheet-url">Spreadsheet Address (URL)</label>
				</th>
				<td>
					<input name="spreadsheet_url" type="url" id="spreadsheet-url" aria-describedby="spreadsheet-url"
					value="<?= esc_attr( get_option( 'erst_gse_spreadsheet_url' ) ); ?>"
					class="regular-text" placeholder="https://docs.google.com/spreadsheets" required>
					<p class="description" id="spreadsheet-url">Setup spreadsheet url.</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="cron-time">Cron Time</label>
				</th>
				<td>
					<input name="cron_time" type="time" id="cron-time" aria-describedby="cron-time"
					value="<?= esc_attr( get_option( 'erst_gse_cron_time' ) ); ?>"
					class="regular-text" required>
					<p class="description" id="cron-time">Setup cron time.</p>
				</td>
			</tr>
		</tbody>
	</table>
	<p class="submit">
		<input type="submit" name="submit" id="submit" class="button button-primary" value="Сохранить">
	</p>
	<input type="hidden" name="table_settings">
	<div id="jsoneditor" style="width:350px;height:300px;"></div>
</form>
<script>
(function($) {
    $(function() {
        var editor = new JSONEditor(document.getElementById('jsoneditor'), {});
        
        editor.set(<?= get_option( 'erst_gse_table_settings' ); ?>);
        $('#erst-gse').submit(function() {
            $("input[name='table_settings']").val(JSON.stringify(editor.get()));
        });
    });
}(jQuery));
</script>
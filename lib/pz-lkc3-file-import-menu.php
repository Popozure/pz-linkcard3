<?php
	
if (!defined('ABSPATH' ) ) {
	 exit;
}
?>
<p><a href="<?php echo esc_url($this->cacheman_url); ?>" class="pz-man-return-button button"><?php esc_html_e('Return to Cache Manager', 'pz-linkcard3'); ?></a></p>

<h2><?php esc_html_e('Import LinkCard Data', 'pz-linkcard3' ); ?></h2>
<p><?php esc_html_e('Import files exported from the Pz-LinkCard series and load them into the cache manager.', 'pz-linkcard3' ); ?></p>
<table class="pz-man-filemenu" style="width: 100%;">
	<tr style="vertical-align: middle; height: 40px;">
		<td><input type="file" id="import_file" name="import_file" accept=".csv" required style="width: 100%;"></td>
	</tr>
	<tr style="vertical-align: middle; height: 40px;">
		<td><label><input type="checkbox" id="import_clear" name="import_clear" value="1"> <?php esc_html_e('Clear all cache entries', 'pz-linkcard3' ); ?></label></td>
	</tr>
	<tr style="vertical-align: middle; height: 40px;">
		<td><button type="submit" id="import_button" name="action" value="exec-import" class="pz-man-file-button button button-primary" disabled="disabled" onclick="return confirm('<?php echo esc_js(esc_html__('Are you sure?', 'pz-linkcard3' ) ); ?>');"><?php esc_html_e('Start the import immediately', 'pz-linkcard3' ); ?></button></td>
	</tr>
</table>

<?php
// Check for the old database table.

if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $wpdb->prefix.'pz_linkcard' ) ) === $wpdb->prefix.'pz_linkcard' ) {
	$pz_option	=	get_option('pz_linkcard_options', array() );
?>
	<h2><?php esc_html_e('From an Older Version', 'pz-linkcard3' ); ?></h2>
	<p><?php esc_html_e('Import directly from the database of the previous Pz-LinkCard series and load it into the cache manager.', 'pz-linkcard3' ); ?></p>
	<table class="pz-man-filemenu" style="width: 100%;">
		<tr style="vertical-align: middle; height: 40px;">
			<td>
				<div class="pz-import-host-source">
					<p class="pz-import-host-source-title"><?php esc_html_e('Select an import source:', 'pz-linkcard3' ); ?></p>
					<label class="pz-import-host-source-version"><input type="checkbox" id="import_host" name="import_host" value="1">Pz-LinkCard ver.<?php echo esc_attr($pz_option['plugin-version']); ?></label>
				</div>
			</td>
		</tr>
		<tr style="vertical-align: middle; height: 40px;">
			<td><label><input type="checkbox" id="import_host_clear" name="import_host_clear" value="1"> <?php esc_html_e('Clear all cache entries', 'pz-linkcard3' ); ?></label></td>
		</tr>
		<tr style="vertical-align: middle; height: 40px;">
			<td><button type="submit" id="import_host_button" name="action" value="exec-import-host" class="button button-primary" disabled="disabled" formnovalidate="formnovalidate" onclick="return confirm('<?php echo esc_js(esc_html__('Are you sure?', 'pz-linkcard3' ) ); ?>');"><?php esc_html_e('Start the import immediately', 'pz-linkcard3' ); ?></button></td>
		</tr>
	</table>
<?php
}

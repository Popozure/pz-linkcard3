<?php
if (!defined('ABSPATH' ) ) {
	 exit;
}
?>
<div class="<?php echo esc_attr($page_class('pz-initialize' ) ); ?>" id="pz-initialize">
	<div class="pz-submit-float"><?php submit_button(); ?></div>
	<h2><?php echo	esc_html__('Initialize', 'pz-linkcard3' ).wp_kses_post(sprintf($help_icon, esc_attr('-initialize' ) ) ); ?></h2>
	<table class="form-table">
		<tr>
			<th scope="row"><?php esc_html_e('Initialize Settings', 'pz-linkcard3' ); ?></th>
			<td>
				<button type="submit" name="action" value="init-settings" class="pz-button-sure" onclick="return confirm('<?php echo esc_js(esc_html__('Are you sure?', 'pz-linkcard3' ) ); ?>');"><?php esc_html_e('Run', 'pz-linkcard3' ); ?></button>
				&ensp;<span><?php esc_html_e('Reset the settings to their initial values.', 'pz-linkcard3' ); ?></span>
			</td>
		</tr>
		<tr class="pz-debug-only">
			<th scope="row"><?php esc_html_e('Initialization Exception', 'pz-linkcard3' ); ?></th>
			<td>
				<?php pz_lkc3_property_checkbox($prop, 'initialize-exception', __('Do not initialize "Log Mode", "Administrator Mode", and "Additional Mode".', 'pz-linkcard3' ) ); ?>
			</td>
		</tr>
	</table>
	<?php submit_button(); ?>

	<h2><?php echo	esc_html__('Deletion Settings', 'pz-linkcard3' ); ?></h2>
	<table class="form-table">
		<tr>
			<th scope="row"><?php esc_html_e('Delete Database', 'pz-linkcard3' ); ?></th>
			<td>
				<?php pz_lkc3_property_checkbox($prop, 'flg-delete-db', __('When deleting the plugin, also delete it from the database.', 'pz-linkcard3' ) ); ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e('Delete Image Cache', 'pz-linkcard3' ); ?></th>
			<td>
				<?php pz_lkc3_property_checkbox($prop, 'flg-delete-images', __('When deleting the plugin, also delete the image cache.', 'pz-linkcard3' ) ); ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e('Delete Settings', 'pz-linkcard3' ); ?></th>
			<td>
				<?php pz_lkc3_property_checkbox($prop, 'flg-delete-settings', __('When deleting the plugin, also delete its settings.', 'pz-linkcard3' ) ); ?>
			</td>
		</tr>
	</table>
	<?php submit_button(); ?>
</div>

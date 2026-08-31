<?php
	
if (!defined('ABSPATH' ) ) {
	 exit;
}
?>
<div class="<?php echo esc_attr($page_class('pz-multisite' ) ); ?>" id="pz-multisite">
	<div class="pz-submit-float"><?php submit_button(); ?></div>
	<h2><?php echo	esc_html__('Multi Site Information', 'pz-linkcard3' ).wp_kses_post(sprintf($help_icon, esc_attr('-multisite' ) ) ); ?></h2>
	<div class="pz-multisite-notice"><?php echo esc_html__('*** Cannot be changed ***', 'pz-linkcard3' ); ?></div>
	<table class="pz-multisite-table form-table striped">
		<tr>
			<th scope="row"><?php esc_html_e('Multi Site', 'pz-linkcard3' ); ?></th>
			<td>
				<select>
					<option value=""  <?php selected(!$is_multisite ); disabled( $is_multisite ); ?>><?php esc_html_e('Disabled',			'pz-linkcard3' ); ?></option>
					<option value="1" <?php selected( $is_multisite ); disabled(!$is_multisite ); ?>><?php esc_html_e('Enabled',			'pz-linkcard3' ); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e('Type', 'pz-linkcard3' ); ?></th>
			<td>
				<select>
					<option value=""  <?php selected(!$is_subdomain ); disabled( $is_subdomain ); ?>><?php esc_html_e('Subdirectories',		'pz-linkcard3' ); ?></option>
					<option value="1" <?php selected( $is_subdomain ); disabled(!$is_subdomain ); ?>><?php esc_html_e('Subdomains',			'pz-linkcard3' ); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e('Number of Sites', 'pz-linkcard3' ); ?></th>
			<td>
				<select name="properties[multi-count]">
					<?php for ($i = 0; $i <= $multi_count; $i++) : ?>
						<option value="<?php echo esc_attr($i ); ?>" <?php selected($i, $multi_count ); disabled(true ); ?>><?php echo esc_html($i ); ?></option>
					<?php endfor; ?>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e('Site Network', 'pz-linkcard3' ); ?></th>
			<td>
				<label>
					<input type="checkbox" value="1" <?php checked(is_multisite() && is_plugin_active_for_network($this->base_name ) ); ?> readonly="readonly" /><?php esc_html_e('Network Active', 'pz-linkcard3' ); ?>
				</label>
			</td>
		</tr>
	</table>

	<h2><?php echo	esc_html__('Current Site Information', 'pz-linkcard3' ).wp_kses_post(sprintf($help_icon, esc_attr('-multisite' ) ) ); ?></h2>
	<div class="pz-multisite-notice"><?php echo esc_html__('*** Cannot be changed ***', 'pz-linkcard3' ); ?></div>
	<table class="pz-multisite-table form-table striped">
		<tr>
			<th scope="row"><?php esc_html_e('Current Blog ID', 'pz-linkcard3' ); ?></th>
			<td>
				<select name="properties[multi-myid]">
					<?php for ($i = 1; $i <= max($multi_count, $multi_myid ); $i++) : ?>
						<option value="<?php echo esc_attr($i ); ?>" <?php selected($i, $multi_myid ); disabled(true ); ?>><?php echo esc_html($i ); ?></option>
					<?php endfor; ?>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e('Option Name', 'pz-linkcard3' ); ?></th>
			<td>
				<?php pz_lkc3_readonly_text($this->option_name, 40 ); ?>
			</td>
		</tr>
		<tr>
			<th scope="row" rowspan="2"><?php esc_html_e('Table Name', 'pz-linkcard3' ); ?></th>
			<td>
				<?php pz_lkc3_readonly_text($this->db_card, 40 ); ?>
				<?php pz_lkc3_table_exists_badge($this->db_card ); ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php pz_lkc3_readonly_text($this->db_click, 40 ); ?>
				<?php pz_lkc3_table_exists_badge($this->db_click ); ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e('Links to Subsites', 'pz-linkcard3' ); ?></th>
			<td>
				<label>
					<input type="checkbox" value="1" checked="checked" readonly="readonly">
					<?php esc_html_e('Treat links to subsites as external links.', 'pz-linkcard3' ); ?>
				</label>
			</td>
		</tr>
	</table>

	<h2><?php echo	esc_html__('Site List', 'pz-linkcard3' ).wp_kses_post(sprintf($help_icon, esc_attr('-multisite' ) ) ); ?></h2>
	<div class="pz-multisite-notice"><?php echo esc_html__('*** Cannot be changed ***', 'pz-linkcard3' ); ?></div>
	<table class="pz-multisite-table pz-multisite-list form-table striped">
		<thead>
			<tr>
				<th scope="col" class="pz-multisite-head-current" style="width: 20px; text-align: center;"><?php esc_html_e('Current', 'pz-linkcard3' ); ?></th>
				<th scope="col" class="pz-multisite-head-blog-id"><?php esc_html_e('Blog ID', 'pz-linkcard3' ); ?></th>
				<th scope="col" class="pz-multisite-head-site-name"><?php esc_html_e('Site Name', 'pz-linkcard3' ); ?></th>
				<th scope="col" class="pz-multisite-head-url"><?php esc_html_e('URL', 'pz-linkcard3' ); ?></th>
				<th scope="col" class="pz-multisite-head-domain"><?php esc_html_e('Domain', 'pz-linkcard3' ); ?></th>
				<th scope="col" class="pz-multisite-head-registered"><?php esc_html_e('Registered', 'pz-linkcard3' ); ?></th>
				<th scope="col" class="pz-multisite-head-post-count"><?php esc_html_e('Post Count', 'pz-linkcard3' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php for ($i = 1; $i <= $multi_count; $i++) { ?>
			<tr>
				<th class="pz-multisite-body-current" scope="row">
					<input type="hidden"   name="" value="">
					<input type="checkbox" name="" value="1" <?php checked($multi[$i]['card_id'] == $multi_myid ); ?> readonly="readonly" />
				</th>
				<td class="pz-multisite-body-blog-id">				<?php echo	esc_html($multi[$i]['card_id'] ); ?></td>
				<td class="pz-multisite-body-site-name">			<?php echo	esc_html($multi[$i]['name'] ); ?></td>
				<td class="pz-multisite-body-url pz-monospace">		<?php echo	esc_html($multi[$i]['url'] ); ?></td>
				<td class="pz-multisite-body-domain pz-monospace">	<?php echo	esc_html($multi[$i]['domain'] ); ?></td>
				<td class="pz-multisite-body-registered">			<?php echo	esc_html($multi[$i]['registered'] ); ?></td>
				<td class="pz-multisite-body-post-count">			<?php echo	esc_html($multi[$i]['post_count'] ); ?></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>

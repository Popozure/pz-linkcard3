<?php
if (!defined('ABSPATH' ) ) {
	 exit;
}
?>
<div class="<?php echo esc_attr($page_class('pz-check' ) ); ?>" id="pz-check">
	<div class="pz-submit-float"><?php submit_button(); ?></div>
	<h2><?php echo	esc_html__('Link Check Settings', 'pz-linkcard3' ).wp_kses_post(sprintf($help_icon, esc_attr('-link-check' ) ) ); ?></h2>
	<table class="form-table">
		<tr>
			<th scope="row" colspan="2"><?php esc_html_e('Set No-Follow', 'pz-linkcard3' ); ?></th>
			<td>
				<?php pz_lkc3_property_checkbox($prop, 'flg-nofollow', __('Add "nofollow" to external links.', 'pz-linkcard3' ).__('(Deprecated)', 'pz-linkcard3' ) ); ?>
			</td>
		</tr>
		<tr>
			<th scope="row" colspan="2"><?php esc_html_e('Set No-Opener', 'pz-linkcard3' ); ?></th>
			<td>
				<?php pz_lkc3_property_checkbox($prop, 'flg-noopener', __('Add "noopener" to external links.', 'pz-linkcard3' ).__('(Recommended)', 'pz-linkcard3' ) ); ?>
			</td>
		</tr>
		<tr>
			<th scope="row" colspan="2"><?php esc_html_e('Set Referer', 'pz-linkcard3' ); ?></th>
			<td>
				<?php pz_lkc3_property_checkbox($prop, 'flg-referer', __('Send the article URL to the link destination.', 'pz-linkcard3' ) ); ?>
			</td>
		</tr>
		<tr>
			<th scope="row" rowspan="2"><?php esc_html_e('When Not Found', 'pz-linkcard3' ); ?></th>
			<th scope="row"><?php esc_html_e('Disable Link', 'pz-linkcard3' ); ?></th>
			<td>
				<?php pz_lkc3_property_checkbox($prop, 'flg-unlink', __('Unlink when the access status is "403", "404", or "410".', 'pz-linkcard3' ) ); ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e('Border', 'pz-linkcard3' ); ?></th>
			<td>
				<label>
					<?php esc_html_e('The settings are located on the “Display” tab.',	'pz-linkcard3' ); ?>
				</label>
			</td>
		</tr>
		<tr>
			<th scope="row" colspan="2"><?php esc_html_e('SSL Verification', 'pz-linkcard3' ); ?></th>
			<td>
				<?php pz_lkc3_property_checkbox($prop, 'flg-sslverify', __('Verifying SSL/TLS Certificates During HTTPS Communication.', 'pz-linkcard3' ).__('(Recommended)', 'pz-linkcard3' ) ); ?>
			</td>
		</tr>
		<tr>
			<th scope="row" colspan="2"><?php esc_html_e('Block local IP addresses', 'pz-linkcard3' ); ?></th>
			<td>
				<?php pz_lkc3_property_checkbox($prop, 'flg-local-check', __('Blocking local IP addresses prevents SSRF.', 'pz-linkcard3' ).__('(Recommended)', 'pz-linkcard3' ) ); ?>
			</td>
		</tr>
		<tr>
			<th scope="row" colspan="2"><?php esc_html_e('Use User-Agent', 'pz-linkcard3' ); ?></th>
			<td>
				<?php
					echo_list('user-agent', $prop['user-agent'] ?? '', LIST_USER_AGENT, '', '', 'pz-sync pz-user-agent', true, true );
					echo	'<input type="hidden" name="properties[user-agent-text]" value="">';
				?>
			</td>
		</tr>
		<tr>
			<th scope="col" rowspan="2"><?php esc_html_e('Broken Link Count', 'pz-linkcard3' ); ?></th>
			<th scope="row"><?php esc_html_e('Display', 'pz-linkcard3' ); ?></th>
			<td>
				<?php pz_lkc3_property_checkbox($prop, 'flg-alive-count', __('The number of broken links is displayed next to the submenu.', 'pz-linkcard3' ) ); ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e('Recurrence', 'pz-linkcard3' ); ?></th>
			<td>
				<?php esc_html_e('The settings are located on the “Advanced” tab.',	'pz-linkcard3' ); ?>
			</td>
		</tr>
	</table>
	<?php submit_button(); ?>
</div>

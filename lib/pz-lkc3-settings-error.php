<?php
	
if (!defined('ABSPATH' ) ) {
	 exit;
}
?>
<?php
	$error_post_id	=	$prop['error-postid'] ?? '';
	$error_url		=	$prop['error-url'] ?? '';
	$error_scroll_url	=	$error_url ? add_query_arg('pz_lkc3_scroll', 'lkc3-error', $error_url ) : '';
	$error_time		=	$prop['error-time'] ?? '';
	$error_time_text	=	is_numeric($error_time ) ? $this->pz_Date($this->format_datetime, $error_time ) : $error_time;
?>
<div class="<?php echo esc_attr($page_class('pz-error' ) ); ?>" id="pz-error">
	<div class="pz-submit-float"><?php submit_button(); ?></div>
	<h2><?php echo	esc_html__('Error Settings', 'pz-linkcard3' ).wp_kses_post(sprintf($help_icon, esc_attr('-error' ) ) ); ?></h2>
	<div class="pz-error-text">
		<?php esc_html_e('The shortcode description is incorrect. Please open the "Linked Articles" section and correct it.', 'pz-linkcard3' ); ?>
	</div>
	<table class="pz-error-table form-table">
		<tr>
			<th scope="row"><?php esc_html_e('Post ID', 'pz-linkcard3' ); ?></th>
			<td>
				<span class="pz-error-url pz-monospace"><?php echo esc_html($error_post_id ); ?></span>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e('Post URL', 'pz-linkcard3' ); ?></th>
			<td>
				<a href="<?php echo esc_url($error_scroll_url ); ?>" class="pz-error-url"><?php echo esc_html($error_url ); ?></a>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e('Occurrence Time', 'pz-linkcard3' ); ?></th>
			<td>
				<span><?php echo esc_html($error_time_text ); ?></span>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e('Error Reset', 'pz-linkcard3' ); ?></th>
			<td>
				<button type="submit" name="action" value="clear-error" class="pz-button"><?php esc_html_e('Reset', 'pz-linkcard3' ); ?></button>
				&ensp;<span><?php esc_html_e('Cancel the error condition.', 'pz-linkcard3' ); ?></span>
				<br><span class="pz-warning"><?php esc_html_e('* If you have not corrected the error, you may still get an error even if you cancel the error.', 'pz-linkcard3' ); ?></span>
			</td>
		</tr>
	</table>
	<?php submit_button(); ?>
</div>

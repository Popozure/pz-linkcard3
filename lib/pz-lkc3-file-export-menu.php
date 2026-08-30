<?php
	
if	(!defined( 'ABSPATH' ) ) { header( 'HTTP/1.1 403 Forbidden' ); exit; }

	$export_count	=	intval($wpdb->get_var($wpdb->prepare('SELECT COUNT( * ) FROM %i', $this->db_card ) ) );
	
	/* translators: %s: エクスポート対象の件数 */
	$export_count_label	=	sprintf($export_count == 1 ? esc_html__('%s item', 'pz-linkcard3' ) : esc_html__('%s items', 'pz-linkcard3' ), number_format_i18n($export_count ) );
?>
<p><a href="<?php echo esc_url($this->cacheman_url); ?>" class="pz-man-return-button button"><?php esc_html_e('Return to Cache Manager', 'pz-linkcard3'); ?></a></p>
<h2><?php esc_html_e('Export LinkCard Data', 'pz-linkcard3' ); ?></h2>
<p><?php esc_html_e('Export the cache contents to a CSV file. All registered items will be exported.', 'pz-linkcard3' ); ?></p>
<table class="pz-man-filemenu" style="width: 100%;">
	<tr style="vertical-align: middle;">
		<td>
			<a href="<?php echo esc_url(wp_nonce_url(admin_url( 'admin-post.php?action=pz_lkc3_export_file' ), 'pz_lkc3_export' ) ); ?>" class="pz-man-file-button button button-primary" data-no-overlay="1" data-download-overlay="hide"><?php echo esc_html__('Download the export file', 'pz-linkcard3' ).' ('.esc_html($export_count_label ).')'; ?></a>
		</td>
	</tr>
</table>

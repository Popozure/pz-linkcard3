<?php
if (!defined('ABSPATH' ) ) {
	 exit;
}
?>
<?php
	
	check_admin_referer('pz_lkc3_export' );

	global	$wpdb;

	$column_omit	=	array('url_key' );

	
	$result			=	$wpdb->get_results($wpdb->prepare('SELECT * FROM %i LIMIT 1', $this->db_card ), ARRAY_A );
	if	(!$result ) {
		return;
	}

	$column_all		=	array_keys($result[0] );
	$column_diff	=	array_diff($column_all, $column_omit );
	
	$data_all		=	$wpdb->get_results($wpdb->prepare('SELECT * FROM %i ORDER BY domain, url', $this->db_card ), ARRAY_A );

	$datetime		=	gmdate('Ymd_His');
	$filename		=	'pz_linkcard3_export_utf8_'.$datetime.'.csv';

	$wp_filesystem	=	$this->pz_GetFilesystem();
	if	(!$wp_filesystem ) {
		wp_die(esc_html__('Failed to open the export file.', 'pz-linkcard3' ) );
	}

	header('Content-Type: text/csv; charset=UTF-8' );
	header('Content-Disposition: attachment; filename="'.sanitize_file_name($filename ).'"' );
	header('Cache-Control: no-cache, no-store, must-revalidate' );
	header('Pragma: no-cache' );

	$build_csv_line	=	function($row ) {
		$values	=	array();
		foreach	($row as $value ) {
			$value		=	(string) $value;
			$values[]	=	'"'.str_replace('"', '""', $value ).'"';
		}
		return	implode(',', $values )."\r\n";
	};

	$csv_output		=	$build_csv_line($column_diff );

	foreach	($data_all as $data ) {
		foreach	($column_omit as $column_name ) {
			unset($data[$column_name] );
		}
		foreach	($data as &$item ) {
			$item	=	str_replace(array("\r", "\n", "\t" ), ' ', ($item ?? '' ) );
		}
		unset($item );
		
		$csv_output	.=	$build_csv_line($data );
	}

	$wp_filesystem->put_contents('php://output', $csv_output );

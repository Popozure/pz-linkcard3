<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$pz_lkc3_options = get_option('pz_linkcard3_options', array() );
if ( ! is_array($pz_lkc3_options ) ) {
	$pz_lkc3_options = array();
}

$pz_lkc3_should_delete_db       = ! empty($pz_lkc3_options['flg-delete-db'] );
$pz_lkc3_should_delete_images   = ! empty($pz_lkc3_options['flg-delete-images'] );
$pz_lkc3_should_delete_settings = ! empty($pz_lkc3_options['flg-delete-settings'] );

if ( $pz_lkc3_should_delete_db ) {
	global $wpdb;

	$pz_lkc3_tables = array(
		$wpdb->prefix . 'pz_linkcard3_card',
		$wpdb->prefix . 'pz_linkcard3_click',
	);

	foreach ( $pz_lkc3_tables as $pz_lkc3_table ) {
		$pz_lkc3_table = preg_replace('/[^A-Za-z0-9_]/', '', $pz_lkc3_table );
		if ( $pz_lkc3_table ) {
			
			$wpdb->query($wpdb->prepare('DROP TABLE IF EXISTS %i', $pz_lkc3_table ) );
		}
	}
}

if ( $pz_lkc3_should_delete_images ) {
	pz_lkc3_remove_upload_directory('pz-linkcard3' );
}

if ( $pz_lkc3_should_delete_settings ) {
	delete_option('pz_linkcard3_options' );
}

$pz_lkc3_user_meta_keys = array(
	'pz_lkc3_cacheman_columns',
	'pz_lkc3_cacheman_per_page',
);

foreach ( $pz_lkc3_user_meta_keys as $pz_lkc3_user_meta_key ) {
	delete_metadata('user', 0, $pz_lkc3_user_meta_key, '', true );
}

function pz_lkc3_remove_upload_directory($dir_name ) {
	$wp_filesystem = pz_lkc3_get_filesystem();
	if ( ! $wp_filesystem ) {
		return false;
	}

	$wp_upload_dir = wp_upload_dir();
	if ( ! empty($wp_upload_dir['error'] ) || empty($wp_upload_dir['basedir'] ) ) {
		return false;
	}

	$base_dir   = realpath($wp_upload_dir['basedir'] );
	$target_dir = realpath(trailingslashit($wp_upload_dir['basedir'] ) . $dir_name );

	if ( ! $base_dir || ! $target_dir || ! is_dir($target_dir ) ) {
		return false;
	}

	$base_dir_with_separator = trailingslashit($base_dir );
	if ( strpos(trailingslashit($target_dir ), $base_dir_with_separator ) !== 0 ) {
		return false;
	}

	return $wp_filesystem->delete($target_dir, true, 'd' );
}

function pz_lkc3_get_filesystem() {
	global $wp_filesystem;

	if ( $wp_filesystem instanceof WP_Filesystem_Base ) {
		return $wp_filesystem;
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	if ( ! WP_Filesystem() ) {
		return false;
	}

	return $wp_filesystem;
}

<?php	
if	(!defined( 'ABSPATH' ) ) { header( 'HTTP/1.1 403 Forbidden' ); exit; }

	// DB作成
	global	$wpdb;
	$wpdb->hide_errors();
	require_once(ABSPATH.'wp-admin/includes/upgrade.php' );

	// カード情報 prefix_pz_linkcard3_click
	$sql = "
CREATE TABLE $this->db_click (
	click_id		BIGINT			UNSIGNED	NOT NULL	AUTO_INCREMENT	COMMENT	'Click ID',
	card_id			BIGINT			UNSIGNED	NOT NULL					COMMENT	'Card ID',
	ip				VARCHAR(45)								DEFAULT NULL	COMMENT	'IP Address',
	user_agent		VARCHAR(1024)							DEFAULT NULL	COMMENT	'User Agent',
	referer			VARCHAR(1024)							DEFAULT NULL	COMMENT	'Referer',
	clicked_at		DATETIME					NOT NULL	DEFAULT CURRENT_TIMESTAMP	COMMENT	'Click Time',
	PRIMARY KEY		(click_id),
	INDEX idx_card_id (card_id),
	INDEX idx_ip (ip),
	INDEX idx_click_time (clicked_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci";

	// SQL更新チェック（クエリーから求まったMD5の値を比較）
	$db_version_new	=	md5($sql, false );

	// テーブルが存在していて、前回使用したSQLと変更が無ければ抜ける
	if	($this->options['db-ver-click']	==	$db_version_new	) {	
		$table_exists	=	$wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $this->db_click ) );
		if		($table_exists === $this->db_click ) {
			return;
		}
	}

	// DBテーブル作成・更新
	$result			=	dbDelta($sql, true );

	// SQLのMD5を保存しておく
	$this->options['db-ver-click']	=	$db_version_new;

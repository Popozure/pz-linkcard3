<?php	
if (!defined('ABSPATH')) { header('HTTP/1.1 403 Forbidden'); exit; }

	// Create or update card cache table.
	global	$wpdb;
	$wpdb->hide_errors();
	require_once(ABSPATH.'wp-admin/includes/upgrade.php' );

	$sql = "
CREATE TABLE $this->db_card (
	card_id			bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	url				varchar(4096) NOT NULL,
	url_key			char(64) NOT NULL,
	url_redir		varchar(4096) DEFAULT NULL,
	domain			varchar(256) DEFAULT NULL,
	site_name		varchar(256) DEFAULT NULL,
	site_icon		varchar(2048) DEFAULT NULL,
	title			varchar(256) DEFAULT NULL,
	excerpt			varchar(512) DEFAULT NULL,
	charset			varchar(32) DEFAULT NULL,
	thumbnail		varchar(2048) DEFAULT NULL,
	no_failure		tinyint(3) unsigned NOT NULL DEFAULT 0,
	click_count		bigint(20) unsigned NOT NULL DEFAULT 0,
	post_id			int(10) unsigned DEFAULT NULL,
	post_date		datetime DEFAULT NULL,
	post_modified	datetime DEFAULT NULL,
	post_cat		text,
	alive_result	int(11) DEFAULT NULL,
	alive_time		datetime DEFAULT NULL,
	alive_nexttime	datetime DEFAULT NULL,
	sns_twitter		int(10) unsigned DEFAULT NULL,
	sns_facebook	int(10) unsigned DEFAULT NULL,
	sns_hatena		int(10) unsigned DEFAULT NULL,
	sns_time		datetime DEFAULT NULL,
	sns_nexttime	datetime DEFAULT NULL,
	use_post_id1	int(10) unsigned DEFAULT NULL,
	use_post_id2	int(10) unsigned DEFAULT NULL,
	use_post_id3	int(10) unsigned DEFAULT NULL,
	use_post_id4	int(10) unsigned DEFAULT NULL,
	use_post_id5	int(10) unsigned DEFAULT NULL,
	use_post_id6	int(10) unsigned DEFAULT NULL,
	regist_title	varchar(256) DEFAULT NULL,
	regist_excerpt	varchar(512) DEFAULT NULL,
	regist_charset	varchar(32) DEFAULT NULL,
	regist_result	int(11) DEFAULT NULL,
	regist_time		datetime DEFAULT NULL,
	update_result	int(11) DEFAULT NULL,
	update_time		datetime DEFAULT NULL,
	PRIMARY KEY		(card_id),
	UNIQUE KEY		url_key (url_key),
	KEY				idx_alive_nexttime (alive_nexttime),
	KEY				idx_sns_nexttime (sns_nexttime)
) ENGINE=InnoDB ROW_FORMAT=DYNAMIC DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci";

	// SQL更新チェック（クエリーから求まったMD5の値を比較）
	$db_version_new	=	md5($sql, false );

	// テーブルが存在していて、前回使用したSQLと変更が無ければ抜ける
	if	($this->options['db-ver-card']	==	$db_version_new	) {
		$table_exists	=	$wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $this->db_card ) );
		if		($table_exists === $this->db_card ) {
			return;
		}
	}

	// DBテーブル作成・更新
	$result			=	dbDelta($sql, true );

	// SQLのMD5を保存しておく
	$this->options['db-ver-card']	=	$db_version_new;

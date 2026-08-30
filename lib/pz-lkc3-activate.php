<?php
	
if	(!defined( 'ABSPATH' ) ) { header( 'HTTP/1.1 403 Forbidden' ); exit; } ?>
<?php
	// 二重実行防止
	if	($this->flg_activate	==	true) {
		return;
	}

	// アクティベート処理開始
	$this->flg_activate			=	true;

	// WP-CRONの割り込みを停止
	if	(wp_next_scheduled($this->cron_alive ) ) {
		wp_clear_scheduled_hook($this->cron_alive );
	}
	if	(wp_next_scheduled($this->cron_sns ) ) {
		wp_clear_scheduled_hook($this->cron_sns );
	}

	// DBテーブル作成・更新＆メンテナンス
	require_once ('pz-lkc3-activate-db-card.php');
	require_once ('pz-lkc3-activate-db-click.php');

	// テンプレート側でMCEプラグイン一覧を上書きする場合があるため、実行優先度を下げる
	if	((intval($this->options['mce-priority'] ) <= 10 ) && (get_template() == 'jin' ) ) {
		$this->options['mce-priority']	=	11;
	}

	// オプションの更新
	$this->options['plugin-version']	=	$this->plugin_version;
	$result			=	$this->pz_SaveOptions();

	// スタイルシート生成
	$this->pz_SetStyle();

	// WP-CRONスケジュール登録（リンク先存在チェック）
	if	(isset($this->options['alive-period'] )	&&	$this->options['alive-period']	&&	!wp_next_scheduled($this->cron_alive ) ) {		
		$period		=	$this->options['alive-period'];
		$next_time	=	time() + strtotime($period );
		wp_schedule_event($next_time,	$period,	$this->cron_alive );
	}

	// WP-CRONスケジュール登録（SNSカウント取得）
	if	(isset($this->options['sns-period'] )	&&	$this->options['sns-period']	&&	!wp_next_scheduled($this->cron_sns ) ) {
		$period		=	$this->options['sns-period'];
		$next_time	=	time() + strtotime($period );
		wp_schedule_event($next_time,	$period,	$this->cron_sns );
	}

	// アクティベート処理終了
	$this->flg_activate	=	false;

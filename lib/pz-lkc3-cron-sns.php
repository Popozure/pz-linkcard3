<?php
	
if (!defined( 'ABSPATH' ) ) { header( 'HTTP/1.1 403 Forbidden' ); exit; } ?>
<?php
	// WP-CRONスケジュール（SNSカウント取得）
	if (!$this->options['sns-period'] ) {		// 無効になっている場合
		$log	.=	__('Clear schedule "SNS Count Check".', 'pz-linkcard3' ).PHP_EOL;
		wp_clear_scheduled_hook($this->cron_sns );

		// 実行ログ
		$message	=	__('"SNS Count Check" Disabled.', 'pz-linkcard3' );
		$log		.=	$message.PHP_EOL;

		return	null;
	}

	// DBの宣言
	global	$wpdb;

	// SNS次回取得日時を越えているものを抽出
	$proc_datas	=	$wpdb->get_results($wpdb->prepare('SELECT url,sns_nexttime FROM %i WHERE sns_nexttime <= %s ORDER BY sns_nexttime ASC', $this->db_card, $this->pz_CardTimestampToDatetime($this->now ) ) );

	// 実行ログ
	/* translators: %d: SNSカウントチェック対象の件数 */
	$message	=	sprintf(__('%d links are past their next "SNS Count Check" time.', 'pz-linkcard3' ), count($proc_datas ) );
	$log		.=	$message.PHP_EOL;

	// SNSカウント取得
	$proc_count	=	0;
	$max_count	=	intval($this->options['sns-period-num'] );
	if (isset($proc_datas ) && is_array($proc_datas ) && count($proc_datas ) > 0 ) {
		foreach($proc_datas as $data ) {
			$proc_count++;

			// 設定された件数を超えたら終わる
			if ($proc_count > $max_count ) {
				$log	.=	__('Stopped.', 'pz-linkcard3' ).PHP_EOL;
				break;
			}

			// SNSカウントを取得
			$result		=	$this->pz_RenewSNSCount(array('url' => $data->url ) );	// SNS取得＆キャッシュ更新

			// 実行ログ
			if	(isset($result ) && is_array($result ) ) {
				/* translators: %1$d: 処理番号、%2$s: 次回チェック日時、%3$s: URL */
				$message	=	sprintf(__('[%1$d] Completed the "SNS Count Check". (Next time=%2$s URL=%3$s)', 'pz-linkcard3' ), $proc_count, gmdate($this->format_datetime, $result['alive_nexttime'] ), $result['url'] );
				$log		.=	$message.PHP_EOL;
			} else {
				$message	=	'['.$proc_count.'] '.'No data';
				$log		.=	$message.PHP_EOL;
			}
		}
	}

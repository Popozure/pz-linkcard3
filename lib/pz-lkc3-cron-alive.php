<?php
	
if (!defined( 'ABSPATH' ) ) { header( 'HTTP/1.1 403 Forbidden' ); exit; } ?>
<?php
	// WP-CRONスケジュール（存在チェック）
	if (!$this->options['alive-period'] ) {		// 無効になっている場合
		$log	.=	__('Clear schedule "Site Alive Check".', 'pz-linkcard3' ).PHP_EOL;
		wp_clear_scheduled_hook($this->cron_alive );

		// 実行ログ
		$message	=	__('"Site Alive Check" Disabled.', 'pz-linkcard3' );
		$log		.=	$message.PHP_EOL;

		return	null;
	}

	// DBの宣言
	global	$wpdb;

	// 次回生存確認日時を越えているものを抽出
	$proc_datas	=	$wpdb->get_results($wpdb->prepare('SELECT url,alive_time FROM %i WHERE alive_nexttime <= %s AND regist_time IS NOT NULL ORDER BY alive_time ASC, card_id ASC', $this->db_card, $this->pz_CardTimestampToDatetime($this->now ) ) );

	// 実行ログ
	/* translators: %d: リンクチェック対象の件数 */
	$message	=	sprintf(__('%d links are past their next "Link Alive Check" time.', 'pz-linkcard3' ), count($proc_datas ) );
	$log		.=	$message.PHP_EOL;

	// 生存確認
	$proc_count	=	0;
	$max_count	=	intval($this->options['alive-period-num'] );
	if (isset($proc_datas ) && is_array($proc_datas ) && count($proc_datas ) > 0 ) {
		foreach($proc_datas as $data ) {
			$proc_count++;

			// 設定された件数を超えたら終わる
			if ($proc_count > $max_count ) {
				$log	.=	__('Stopped.', 'pz-linkcard3' ).PHP_EOL;
				break;
			}

			// リンク先を取得
			if (isset($data ) && isset($data->url ) ) {
				$before	=	$this->pz_GetCache( array( 'url' => $data->url ) );		// キャッシュ
				$after	=	$this->pz_GetCURL( $before );							// サイト最新

				if	($before['alive_result'] < 400 && $after['alive_result'] >= 400 ) {
					$before['alive_nexttime']	=	$this->now + DAY_IN_SECONDS  * 2 + wp_rand(0, HOUR_IN_SECONDS );		// 次回チェックは2日後
				} else {
					$before['alive_nexttime']	=	$this->now + WEEK_IN_SECONDS * 4 + wp_rand(0, DAY_IN_SECONDS );		// 次回チェックは4週間後
				}
				$before['alive_result']		=	$after['alive_result'];
				$before['alive_time']		=	$this->now;

				if	(!$before['thumbnail'] ) {
					$before['thumbnail']	=	$after['thumbnail'];
				}
				if	(empty($before['site_icon'] ) ) {
					$before['site_icon']	=	$after['site_icon'] ?? '';
				}
				$result		=	$this->pz_SetCache($before );

				// 実行ログ
				/* translators: %1$d: 処理番号、%2$s: 次回チェック日時、%3$s: 結果コード、%4$s: URL */
				$message	=	sprintf(__('[%1$d] Completed the "Link Alive Check". (Next time=%2$s Result=%3$s URL=%4$s)', 'pz-linkcard3' ), $proc_count, gmdate($this->format_datetime, $result['alive_nexttime'] ), $result['alive_result'], $result['url'] );
				$log		.=	$message.PHP_EOL;
			}
		}
	}

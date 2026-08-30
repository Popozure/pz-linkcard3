<?php
	
if (!defined( 'ABSPATH' ) ) { header( 'HTTP/1.1 403 Forbidden' ); exit; } ?>
<?php
	// WP-CRONスケジュール（記事内容取得）

    // DBの宣言
	global	$wpdb;

	$cron_regist_running_key	=	$this->cron_regist.'_running';
	set_transient($cron_regist_running_key, 1, 5 * MINUTE_IN_SECONDS );
	register_shutdown_function(function() use ($cron_regist_running_key ) {
		delete_transient($cron_regist_running_key );
	});

	// 一旦CRONスケジュールを停止
	wp_clear_scheduled_hook($this->cron_regist );			// WP-CRONスケジュール停止（記事内容取得）

	// 記事内容が登録されていないものを抽出
	$proc_datas	=	$wpdb->get_results($wpdb->prepare("SELECT card_id,url FROM %i WHERE regist_time IS NULL OR (update_result = 0 AND (title IS NULL OR title = '')) ORDER BY card_id ASC", $this->db_card ), ARRAY_A );

	// 実行ログ
	/* translators: %d: 取得対象のリンク件数 */
	$message	=	sprintf(__('%d links are ready for content retrieval.', 'pz-linkcard3' ), count($proc_datas ) );
	$log		.=	$message.PHP_EOL;

	// 生存確認
	$proc_count		=	0;
	$max_count		=	5;
	$retry_schedule	=	false;
	if (isset($proc_datas ) && is_array($proc_datas ) && count($proc_datas ) > 0 ) {
		foreach($proc_datas as $data ) {
			$proc_count++;

			// 設定された件数を超えたら終わる
			if ($proc_count > $max_count ) {
				$log	.=	__('Stopped.', 'pz-linkcard3' ).PHP_EOL;
				$retry_schedule	=	true;
				break;
			}

			// リンク先を取得
			$data			=	$this->pz_GetCache($data );			// キャッシュから取得
			if (isset($data ) && isset($data['url'] ) && isset($data['card_id'] ) ) {
				$result		=	$this->pz_GetCURL($data, 5 );		// 現在の記事情報を取得

				if	(isset($result ) && is_array($result ) && !array_key_exists('error', $result ) ) {
					$data                   =   array_merge($data, $result );		// 取得した情報をマージ
	                $data['regist_time']    =	empty($data['regist_time'] ) ? current_time('timestamp', false ) : $data['regist_time'];	// 登録日時
	                $data['update_time']    =	current_time('timestamp', false );	// 更新日時
					$data['regist_title']	=   $data['title'] ?? '';
	                $data['regist_excerpt'] =	$data['excerpt'] ?? '';
	                $data['regist_charset'] =	$data['charset'] ?? '';
	                $data['regist_result']  =	$data['update_result'] ?? '';
	                $data['alive_result']   =	$data['update_result'] ?? '';	    // 生存確認結果
	                $data['alive_time']     =	$data['update_time'] ?? '';	    	// 生存確認日時
    	            $result					=	$this->pz_SetCache($data );			// キャッシュに保存
				}

				// 画像があったらキャッシュしておく
				if	(isset($data['thumbnail'] ) ) {
					$this->pz_GetImage($data['thumbnail'], true );
				}
				if	(isset($data['site_icon'] ) ) {
					$this->pz_GetImage($data['site_icon'], true );
				}

				// 実行ログ
				/* translators: %1$d: 処理番号、%2$s: 取得結果コード、%3$s: URL */
				$message	=	sprintf(__('[%1$d] Retrieved the link content. (Result=%2$s URL=%3$s)', 'pz-linkcard3' ), $proc_count, $result['update_result'] ?? '-', $result['url'] ?? '-' );
				$log		.=	$message.PHP_EOL;
			}
		}
	}

	if	($retry_schedule && !wp_next_scheduled($this->cron_regist ) ) {
		wp_schedule_single_event(time() + 5, $this->cron_regist );
	}

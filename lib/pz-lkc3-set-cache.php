<?php
	
if (!defined( 'ABSPATH' ) ) { header( 'HTTP/1.1 403 Forbidden' ); exit; }
	// キャッシュ保存

    // URLがリダイレクト先と同じ場合は、リダイレクトURLを削除
    if  (($data['url'] ?? '' ) == ($data['url_redir'] ?? '' ) ) {
        unset($data['url_redir']);
    }

    // URLが空っぽの場合は、リダイレクトURLを設定
    if	(empty($data['url'] ) && !empty($data['url_redir'] ) ) {
        $data['url']			=	$data['url_redir'];
        $data['url_redir']		=	'';
    }

    // URLが無かったら抜ける
    if	(!($data['url'] ?? '' ) ) {
        return	[];
    }

    // リンク先URL
    $url					=	esc_url_raw($data['url'] );
    $data['url']			=	$url;

    // 現在日時を更新
    $this->now				=	current_time('timestamp', false );							// 現在日時（ローカル時間）

    // ID
    if	(isset($data['card_id'] ) && !$data['card_id'] ) {
        unset($data['card_id']);
    }

    // URL解析（自サイトチェック）
    $url_info					=	$this->pz_GetURLInfo($url );
    $data['domain']				=	$url_info['domain']	??	'';			// ドメイン名

    // 記事内容等
    $data['site_name']			=	$data['site_name']	??	$data['sitename']	??	'';			// リンク先：サイト名称
    $data['title']				=	$data['title']		??	'';			// リンク先：タイトル
    $data['excerpt']			=	$data['excerpt']	??	'';			// リンク先：抜粋文
    $data['thumbnail']			=	$data['thumbnail']	??	'';			// リンク先：サムネイルURL
    $data['site_icon']			=	$data['site_icon']	??	$data['siteicon']	??	$data['favicon']	??	'';			// リンク先：サイトアイコンURL
    unset($data['sitename'], $data['siteicon'], $data['favicon'] );

    $charset        		    =	$data['charset']	??	        'Unknown';	// リンク先：文字コード
	$charset                    =   preg_replace('/UTF-8.*/i',      'UTF-8',        $charset );	// UTF-8系の文字コード表記を統一
	$charset            		=	preg_replace('/EUC-JP.*/i',     'EUC-JP',       $charset );	// EUC-JP系の文字コード表記を統一
	$charset            		=	preg_replace('/ISO-8859-1.*/i', 'ISO-8859-1',   $charset );	// ISO-8859-1系の文字コード表記を統一
	$charset            		=	preg_replace('/JIS.*/i',        'JIS',          $charset );	// JIS系の文字コード表記を統一
	$charset            		=	preg_replace('/Shift_JIS.*/i',  'Shift_JIS',    $charset );	// Shift_JIS系の文字コード表記を統一
	$charset            		=	preg_replace('/US-ASCII.*/i',   'US-ASCII',     $charset );	// US-ASCII系の文字コード表記を統一
	$charset            		=	preg_replace('/Unknown.*/i',    'Unknown',      $charset );	// Unknown系の文字コード表記を統一
	$charset					=	$this->pz_NormalizeCharsetName($charset );
	if	(!$charset ) {
		$charset				=	'Unknown';
	}
    $data['charset']			=	$charset;

    $data['no_failure']			=	$data['no_failure']	??	0;			// 結果コードがエラーでも成功と見なす

    // 日本語項目のエンティティ文字をデコード
    $data['title']				=	html_entity_decode($data['title'] ?? '');			        // リンク先：タイトル
    $data['excerpt']			=	html_entity_decode($data['excerpt'] ?? '');			        // リンク先：抜粋文
    $data['regist_title']		=	html_entity_decode($data['regist_title'] ?? '');        	// 登録時：タイトル
    $data['regist_excerpt']		=	html_entity_decode($data['regist_excerpt'] ?? '');	        // 登録時：抜粋文

    // 生存確認
    if	((intval($data['regist_time'] ?? 0 ) == 0 ) && (intval($data['update_result'] ?? 0) >= 100 ) ) {
        $data['alive_result']	=	($data['update_result'] ?? $data['regist_result'] ?? $this->now );
        $data['alive_time']		=	$this->now;
        $data['alive_nexttime']	=	$this->now + WEEK_IN_SECONDS * 4 + wp_rand(0, DAY_IN_SECONDS );
    }

    // 内部リンク情報
    if  (isset($data['post_cat'] ) && is_array($data['post_cat'] ) ) {
        // $data['post_cat']       =   serialize($data['post_cat'] );          // カテゴリ情報をシリアライズ
        $data['post_cat']       =   '';
    }

    // SNS関連
    $data['sns_twitter']		=	$data['sns_twitter']	??	null;			// SNS：Twitter
    $data['sns_facebook']		=	$data['sns_facebook']	??	null;			// SNS：facebook
    $data['sns_hatena']			=	$data['sns_hatena']		??	null;			// SNS：はてなブックマーク
    $data['sns_time']			=	$data['sns_time']		??	$this->now;	// SNS：最終取得日時
    $data['sns_nexttime']		=	$data['sns_nexttime']	??	$this->now;	// SNS：次回取得日時

    if ($data['sns_twitter']    <   0) {
        $data['sns_twitter']    =   null;
    }
    if ($data['sns_facebook']   <   0) {
        $data['sns_facebook']   =   null;
    }
    if ($data['sns_hatena']     <   0) {
        $data['sns_hatena']     =   null;
    }

    // 今表示されている投稿IDが無かったら追加する
    $use_post_id_t				=	[];
    $use_post_id_t[]			=	$data['use_post_id1']	??	null;
    $use_post_id_t[]			=	$data['use_post_id2']	??  null;
    $use_post_id_t[]			=	$data['use_post_id3']	??  null;
    $use_post_id_t[]			=	$data['use_post_id4']	??	null;
    $use_post_id_t[]			=	$data['use_post_id5']	??	null;
    $use_post_id_t[]			=	$data['use_post_id6']	??	null;
    $pid						=	intval(get_the_ID() );
    if	(!in_array($pid, $use_post_id_t ) ) {
        $use_post_id_t[]		=	$pid;

        rsort($use_post_id_t );
        $data['use_post_id1']	=	$use_post_id_t[0]		??	null;
        $data['use_post_id2']	=	$use_post_id_t[1]		??	null;
        $data['use_post_id3']	=	$use_post_id_t[2]		??	null;
        $data['use_post_id4']	=	$use_post_id_t[3]		??	null;
        $data['use_post_id5']	=	$use_post_id_t[4]		??	null;
        $data['use_post_id6']	=	$use_post_id_t[5]		??	null;
    }

    // 最終更新日時
    $data['update_result']		=	($data['update_result'] ?? 0) ?   $data['update_result']   :   ($data['alive_result'] ?? 0);
    $data['update_time']		=	$this->now;

    // // 登録時情報
    // if	(($data['regist_time'] ?? 0 ) == 0 && (!empty($data['title'] ) || !empty($data['excerpt'] ) ) ) {
    //     $data['regist_title']	=	$data['title'];			// 登録時のタイトル
    //     $data['regist_excerpt']	=	$data['excerpt'];		// 登録時の抜粋文
    //     $data['regist_charset']	=	$data['charset'];		// 登録時の文字コード
    //     $data['regist_result']	=	$data['update_result'];	// 登録時の結果コード
    //     $data['regist_time']	=	$this->now;				// 登録時の日時
    // }

    // URLキー作成
    $data['url_key']    =   hash('sha256', $url, false );

    // IDがあったら更新する
    global	$wpdb;

    // DBのカラム名を取得して、存在するカラムだけ残す
    $column_defs    =   $wpdb->get_results($wpdb->prepare('DESC %i', $this->db_card ), ARRAY_A );    // カラム情報を取得
    $columns        =   array_column($column_defs, 'Field' );                   // カラム名一覧を取得
    $data           =   array_intersect_key($data, array_flip( $columns ) );    // 存在するカラムだけ残す

    $trim_to_column_lengths = function ($target_data ) use ($column_defs ) {
        $trimmed    =   false;

        foreach ($column_defs as $column_def ) {
            $column_name    =   $column_def['Field'] ?? '';
            $column_type    =   strtolower($column_def['Type'] ?? '' );
            $column_length  =   0;

            if  (!array_key_exists($column_name, $target_data ) || is_null($target_data[$column_name] ) ) {
                continue;
            }

            if  (preg_match('/^(?:var)?char\((\d+)\)/i', $column_type, $matches ) ) {
                $column_length  =   intval($matches[1] );
            } elseif ($column_type === 'tinytext' ) {
                $column_length  =   255;
            } elseif ($column_type === 'text' ) {
                $column_length  =   65535;
            } elseif ($column_type === 'mediumtext' ) {
                $column_length  =   16777215;
            }

            if  ($column_length > 0 && mb_strlen((string) $target_data[$column_name] ) > $column_length ) {
                $target_data[$column_name] =   mb_substr((string) $target_data[$column_name], 0, $column_length );
                $trimmed                   =   true;
            }
        }

        return  [$target_data, $trimmed];
    };

    // DB更新キー取得
    if	(empty($data['card_id'] ) ) {
        $now    =   $this->pz_GetCache(array('url_key' => $data['url_key'] ) );
        if	(!empty($now['card_id'] ) ) {
            $data['card_id']   =   $now['card_id'];
        }
    }
    if	(empty($data['card_id'] ) ) {
        $now	=	$this->pz_GetCache(array('url' => $data['url'] ) );
        if	(!empty($now['card_id'] ) ) {
            $data['card_id']			=	$now['card_id'];
        }
    }

    // DB登録・更新
    $save_cache = function ($target_data ) use ($wpdb ) {
        $target_data = $this->pz_NormalizeCardDateColumnsForStorage($target_data );
        if	(isset($target_data['card_id'] ) && $target_data['card_id'] ) {
            return	$wpdb->update($this->db_card, $target_data, array('card_id' => $target_data['card_id'] ) );
        }

        return	$wpdb->insert($this->db_card, $target_data );
    };

    $save_cache($data );
    if	(!$wpdb->last_error ) {
        return	$this->pz_GetCache($data );	// 読み直して返却
    }

    // DB登録・更新失敗時、url_keyで既存データを探して更新に切り替える
    if	(empty($data['card_id'] ) ) {
        $now    =   $this->pz_GetCache(array('url_key' => $data['url_key'] ) );
        if	(!empty($now['card_id'] ) ) {
            $data['card_id']   =   $now['card_id'];
        }
    }

    // DBテーブルの文字列カラムの桁数を超えていたら、カラム長に合わせて切り詰める
    list($data, $trimmed ) =   $trim_to_column_lengths($data );

    $retried    =   false;
    if	($trimmed || !empty($data['card_id'] ) ) {
        $save_cache($data );
        $retried    =   true;
    }
    if	($retried && !$wpdb->last_error	) {
        return	$this->pz_GetCache($data );	// 読み直して返却
    }

    // 挿入も更新も失敗の場合、諦める
    return	[];

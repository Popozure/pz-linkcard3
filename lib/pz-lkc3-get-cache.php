<?php
	
if (!defined( 'ABSPATH' ) ) { header( 'HTTP/1.1 403 Forbidden' ); exit; }
	// キャッシュ取得
	global				$wpdb;
	$flg_update			=	false;	// 更新フラグ
	$card_id			=	$data['card_id'] ?? null;		// データID
	$url				=	$data['url'] ?? '';			// リンク先URL
	$url_key			=	$data['url_key'] ?? '';		// リンク先URLハッシュ値
	$post_id			=	$data['post_id'] ?? 0;		// 投稿ID
	$result		    	=	[];

	// データID、投稿ID、URLのいずれも無い場合は、Falseを返す
	if	(!$card_id && !$url && !$url_key && !$post_id ) {
	    $data['error']	=	__('No key specified. Please provide a data ID, URL, or post ID.', 'pz-linkcard3' );
	    return			$data;
	}

	// キャッシュを取得（優先度：データID > URLリダイレクト > URL > 投稿ID）
	if			(!empty($data['card_id'] ) ) {				// IDが指定されている場合
		$result			=	(array) $wpdb->get_row($wpdb->prepare('SELECT * FROM %i WHERE card_id=%d',	$this->db_card,	$data['card_id'] ) );

	} elseif	(!empty($data['url'] ) ) {				// URLが指定されている場合
		$result			=	(array) $wpdb->get_row($wpdb->prepare('SELECT * FROM %i WHERE url=%s',	$this->db_card,	$data['url'] ) );

	} elseif	(!empty($data['url_key'] ) ) {			// URLハッシュ値が指定されている場合
		$result			=	(array) $wpdb->get_row($wpdb->prepare('SELECT * FROM %i WHERE url_key=%s',	$this->db_card,	$data['url_key'] ) );

	} elseif	(!empty($data['post_id'] ) ) {			// 投稿IDが指定されている場合
		$result			=	(array) $wpdb->get_row($wpdb->prepare('SELECT * FROM %i WHERE post_id=%d',	$this->db_card,	$data['post_id'] ) );

	}

	// DBエラーのとき、初期化処理を実行する
	if	($wpdb->last_error ) {
	    $this->hook_activate();
	    $data['card_id']     	=	null;		// IDをnullに設定
	    /* translators: %s: データベースエラーの内容 */
	    $data['error']		=	sprintf(__('Database error occurred. (%s)', 'pz-linkcard3' ), $wpdb->last_error );
		return				$data;
	}

	// 取得したデータをセット
	$data	=	$this->pz_NormalizeCardDateColumnsForRuntime($result );

	// データが無い場合は、空データを作成する
	if		(empty($data['card_id'] ) && $url ) {
		$data['url']			=	$url;			// URLを設定
		$data['post_id']		=	$post_id;		// IDを設定
		$data['use_post_id1']	=	get_the_ID();	// 現在の投稿IDを設定
		$flg_update				=	true;			// 更新フラグを立てる
	}
	// 投稿IDが違う場合は、投稿IDを更新
	if		(!empty($result['card_id'] ) ) {
		$use_post_id_t				=	[];
		$use_post_id_t[]			=	$data['use_post_id1']	??	null;
		$use_post_id_t[]			=	$data['use_post_id2']	??	null;
		$use_post_id_t[]			=	$data['use_post_id3']	??	null;
		$use_post_id_t[]			=	$data['use_post_id4']	??	null;
		$use_post_id_t[]			=	$data['use_post_id5']	??	null;
		$use_post_id_t[]			=	$data['use_post_id6']	??	null;
		if		(!in_array(get_the_ID(), $use_post_id_t ) ) {
			// 投稿IDが登録されていない場合は、追加する
			$use_post_id_t[]		=	intval(get_the_ID() );
			$use_post_id_t			=	array_filter($use_post_id_t );
			sort($use_post_id_t );	// 昇順にソート

			$data['use_post_id1']	=	$use_post_id_t[0]		??	null;
			$data['use_post_id2']	=	$use_post_id_t[1]		??	null;
			$data['use_post_id3']	=	$use_post_id_t[2]		??	null;
			$data['use_post_id4']	=	$use_post_id_t[3]		??	null;
			$data['use_post_id5']	=	$use_post_id_t[4]		??	null;
			$data['use_post_id6']	=	$use_post_id_t[5]		??	null;

			$flg_update				=	true;	// 更新フラグを立てる
		}
	}

	// 更新あり
	if	($flg_update ) {
		$data['update_result']	=	0;											// 結果コード
		$data['update_time']	=	current_time('timestamp', false );			// 現在日時（ローカル時間）
		if	(!empty($data['url'] ) ) {
			$data['url']		=	esc_url_raw($data['url'] );
			$data['url_key']	=	hash('sha256', $data['url'], false );
		}
		if	(!empty($data['card_id'] ) ) {
			$result	=	$wpdb->update($this->db_card, $this->pz_NormalizeCardDateColumnsForStorage($data ), array('card_id' => $data['card_id'] ) );
		} else {
			$result	=	$wpdb->insert($this->db_card, $this->pz_NormalizeCardDateColumnsForStorage($data ) );
			$result	=	(array) $wpdb->get_row($wpdb->prepare('SELECT * FROM %i WHERE url=%s',	$this->db_card,	$data['url'] ) );
			if	(!$wpdb->last_error ) {
				$data	=	$result;	// 新規作成したデータをセット
			}
		}
	}

	return	$this->pz_NormalizeCardDateColumnsForRuntime($data );

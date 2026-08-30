<?php
	
if (!defined( 'ABSPATH' ) ) { header( 'HTTP/1.1 403 Forbidden' ); exit; } ?>
<?php
	// DBを使用
	global	$wpdb;
	
	// 動作モードのフラグ
	$admin_mode			=	isset($this->options['admin-mode'] )		?	intval($this->options['admin-mode'] )		:	0 ;
	$product_mode		=	isset($this->options['product-mode'] )		?	intval($this->options['product-mode'] )		:	0 ;
	$develop_mode		=	isset($this->options['develop-mode'] )		?	intval($this->options['develop-mode'] )		:	0 ;
	$debug_mode			=	isset($this->options['debug-mode'] )		?	intval($this->options['debug-mode'] )		:	0 ;
	$additional_mode	=	isset($this->options['additional-mode'] )	?	intval($this->options['additional-mode'] )	:	0 ;
	$log_mode			=	isset($this->options['debug-mode'] ) && $this->options['debug-mode'] &&
							isset($this->options['log-mode'] )			?	intval($this->options['log-mode'] )			:	0 ;
	$menu_error			=	isset($this->options['error-mode'] )		?	intval($this->options['error-mode'] )		:	0 ;
	$menu_multi			=	isset($this->options['multi-mode'] )		?	intval($this->options['multi-mode'] )		:	0 ;
//	$inhibit			=	isset($this->options['flg-inhibit'] )		?	intval($this->options['flg-inhibit'] )		:	0 ;

	// リクエスト値
	$page			=	'pz-lkc-cacheman';			// Cache manager page slug.
	$request_method	=	isset($_SERVER['REQUEST_METHOD'] ) ? strtoupper(sanitize_text_field(wp_unslash($_SERVER['REQUEST_METHOD'] ) ) ) : '';
	$post_data		=	array();
	if	('POST' === $request_method ) {
		check_admin_referer('pz-cacheman' );
		$post_data	=	wp_unslash($_POST );
	}
	$posted_value	=	static function ($key, $default = '' ) use ($post_data) {
		return isset($post_data[$key] ) ? sanitize_text_field($post_data[$key] ) : $default;
	};
	$posted_array	=	static function ($key) use ($post_data) {
		return (isset($post_data[$key] ) && is_array($post_data[$key] ) ) ? map_deep($post_data[$key], 'sanitize_text_field' ) : array();
	};
	$sanitize_cache_data = static function ($values) use (&$sanitize_cache_data) {
		$result	=	array();
		foreach	($values as $key => $value ) {
			if	(is_array($value ) ) {
				$result[$key]	=	$sanitize_cache_data($value );
				continue;
			}
			if	(in_array($key, array('url', 'url_redir', 'thumbnail', 'site_icon' ), true ) ) {
				$result[$key]	=	esc_url_raw($value );
				continue;
			}
			$result[$key]	=	sanitize_text_field($value );
		}
		return	$result;
	};
	$action			=	$posted_value('action' );
	$now_id			=	absint($posted_value('now_id', 0 ) );
	$bulk_action	=	$posted_value('bulk_action' );
	$select_id		=	$posted_array('select_id' );
	$data			=	(isset($post_data['data'] ) && is_array($post_data['data'] ) ) ? $sanitize_cache_data($post_data['data'] ) : array();
	$param_refine	=	$posted_value('refine' );
	$keyword		=	$posted_value('keyword' );
	$action_filter	=	$posted_value('action-filter' );
	$filter			=	$posted_value('filter', 'all' );
	$header			=	strtolower($posted_value('header' ) );
	$orderby		=	strtolower($posted_value('orderby', 'card_id' ) );
	$order			=	strtolower($posted_value('order', 'desc' ) );
	$page_now		=	(isset($post_data['page_button'] )	?	absint($post_data['page_button'] )	:
						(isset($post_data['page_trans'] )	?	absint($post_data['page_trans'] )	:
						(isset($post_data['page_now'] )		?	absint($post_data['page_now'] )		:	0 ) ) );
	$param_url		=	isset($post_data['url'] )			?	esc_url_raw($post_data['url'] )		:	'';
	$cache_id		=	$posted_value('cache_id' );
	$confirm		=	$posted_value('confirm' );
	$update_result	=	$posted_value('update_result' );
	$alive_result	=	$posted_value('alive_result' );
	$return_url		=	isset($post_data['return_url'] )	?	wp_validate_redirect(esc_url_raw($post_data['return_url'] ), '' )	:	'';
	$return_scroll	=	isset($post_data['return_scroll'] )	?	absint($post_data['return_scroll'] )	:	0;
	$scroll_now		=	isset($post_data['scroll-now'] )	?	absint($post_data['scroll-now'] )	:	0;

	// フィルターボタンが押された場合は検索条件をリセット
	if	($action_filter	) {
		$now_id		=	0;
		$action		=	'filter';
		$filter		=	$action_filter;
		$param_refine	=	'';
		$keyword	=	'';
	}

	// 表示中の見出しがクリックされたら並び順を反転
	if	($header ) {
		if	($orderby	===	$header ) {
			$order		=	($order	=== 'desc') ? 'asc' : 'desc' ;
		} else {
			$orderby	=	$header;
			// $order		=	'desc';
		}
		$header		=	'';
	}

	// 行内メニューのアクション
	$single_actions	=	array(
		'single-edit'	=>	'edit',
		'single-renew'	=>	'renew',
		'single-delete'	=>	'delete',
	);
	foreach	($single_actions as $post_name => $single_action ) {
		if	(isset($post_data[$post_name] ) ) {
			$action		=	$single_action;
			$select_id	=	array(absint($post_data[$post_name] ) );
			break;
		}
	}

	// 一括処理の実行内容を反映
	if	($action === 'exec-batch' ) {
		$action			=	$bulk_action;
	}

	// 出力するHTMLを初期化
	$html_style		=	'';
	$html_plugin	=	'';
	$html_title		=	'';
	$html_input		=	'';
	$html_notice	=	'';
	$html_overlay	=	'';

	// 一覧表示の有無
	$show_list		=	true;

	$make_notice = function($type, $message) {
		return '<div class="notice notice-'.esc_attr($type ).' is-dismissible"><p><strong>'.$message.'</strong></p></div>';
	};
	$make_not_selected_notice = function() use ($make_notice) {
		return $make_notice('info', esc_html(__('Not selected', 'pz-linkcard3' ) ) );
	};
	$make_result_notice = function($label, $success_count, $skip_count) use ($make_notice) {
		/* translators: %d: 成功した件数 %d: スキップした件数 */
		$message	=	esc_html($label.__('...', 'pz-linkcard3' ).sprintf(__('(Success:%1$d Skip:%2$d)', 'pz-linkcard3' ), $success_count, $skip_count ) );
		return $make_notice($success_count ? 'success' : 'error', $message );
	};
	$get_selected_ids = function($select_id) {
		if	(!is_array($select_id ) ) {
			return array();
		}
		$ids	=	array_map('intval', $select_id );
		$ids	=	array_filter($ids, function($id) { return $id > 0; } );
		return array_values(array_unique($ids ) );
	};
	$stop_if_aborted = function() {
		if	(connection_aborted() ) {
			$this->pz_DebugLog(__FUNCTION__, 'Process terminated due to client disconnection.' );
			exit;
		}
	};
	$return_to_frontend = function() use ($return_url, $return_scroll) {
		if	(!$return_url ) {
			return;
		}
		$frontend_url	=	$return_scroll ? add_query_arg('pz_lkc3_restore_scroll', $return_scroll, $return_url ) : $return_url;
		echo	'<script>window.location.replace('.wp_json_encode($frontend_url ).');</script>';
		exit;
	};

	// 現在の動作モードを表示
	$is_cron_regist_running	=	(bool) get_transient($this->cron_regist.'_running' );
	$html_mode		=	($admin_mode			?	'<span class="pz-infobar-env pz-infobar-env-admin">'.	__('Administrator',				'pz-linkcard3' ).'</span>'	:	'' ).
						($this->env_product		?	'<span class="pz-infobar-env pz-infobar-env-product">'.	__('Product Environment',		'pz-linkcard3' ).'</span>'	:	'' ).
						($this->env_develop		?	'<span class="pz-infobar-env pz-infobar-env-develop">'.	__('Development Environment',	'pz-linkcard3' ).'</span>'	:	'' ).
						($this->env_local		?	'<span class="pz-infobar-env pz-infobar-env-local">'.	__('Local Environment',			'pz-linkcard3' ).'</span>'	:	'' ).
						($debug_mode			?	'<span class="pz-infobar-env pz-infobar-env-debug">'.	__('Debug Mode',				'pz-linkcard3' ).'</span>'	:	'' ).
						($log_mode				?	'<span class="pz-infobar-env pz-infobar-env-log">'.		__('Log Mode',					'pz-linkcard3' ).'</span>'	:	'' ).
						($is_cron_regist_running	?	'<span class="pz-infobar-env pz-infobar-env-running">'.	__('Running',				'pz-linkcard3' ).'</span>'	:	'' );

	// 設定画面へのリンク
	$switch_link	=	esc_url($this->settings_url );
	$switch_icon	=	'<span class="dashicons dashicons-admin-generic" style="vertical-align: text-bottom;"></span>';
	$switch_label	=	__('Settings', 'pz-linkcard3' );
	$switch_title	=	__('Go to the Settings page', 'pz-linkcard3' );
	$html_switch	=	'<a href="'.$switch_link.'" class="pz-infobar-switch" title="'.$switch_title.'"><span class="pz-infobar-switch-icon">'.$switch_icon.'</span><span class="pz-infobar-switch-label">'.$switch_label.'</span></a>';

	// インポート/エクスポートボタン
	$html_search	=	'<form method="POST">'.wp_nonce_field('pz-cacheman', '_wpnonce', false, false ).
							'<button type="submit" name="action" value="show-import" class="pz-man-infobar-filemenu-button" style="margin: 0;"><span class="dashicons dashicons-download"></span>'.__('Import', 'pz-linkcard3' ).'</button>'.
							'<button type="submit" name="action" value="show-export" class="pz-man-infobar-filemenu-button" style="margin: 0;"><span class="dashicons dashicons-upload"></span>'.__('Export', 'pz-linkcard3' ).'</button></form>';

	// インフォバーを組み立て
	$html_infobar_logo	=	'<a href="'.esc_url($this->cacheman_url ).'" class="pz-infobar-plugin-logo"><img src="'.$this->base_url.'assets/pz-linkcard3_logo.svg" height="32" alt="Pz-LinkCard3" /></a>';
	$html_infobar	=	'<div id="pz-infobar"><div class="pz-infobar-left"><div class="pz-infobar-plugin">'.$html_infobar_logo.'<span class="pz-infobar-plugin-ver pz-monospace">ver.'.$this->plugin_version.'</span>'.$html_mode.'</div></div><div class="pz-infobar-right">'.$html_search.$html_switch.'</div></div>';

	// 画面タイトル
	$title_icon		=	'<span class="dashicons dashicons-archive" style="vertical-align: bottom; middle; width: 32px; height: 32px; font-size: 32px;"></span>';
	$title_label	=	__('Pz-LinkCard Cache Manager', 'pz-linkcard3' );
	$help_page		=	$this->author_url.'/pz-linkcard-manager';
	$html_title		=	'<h1 class="pz-title"><span class="pz-title-line"><span class="pz-header-title-icon">'.$title_icon.'<span class="pz-header-title-text">'.$title_label.'</span></span><a class="pz-help-icon" href="'.$help_page.'" rel="external noopener help" target="_blank"><img src="'.$this->base_url.'img/help.png" width="16" height="16" title="'.__('Help', 'pz-linkcard3' ).'" alt="help" /></a></span></h1>';

	// POSTする値をhidden inputへ保持
	$temp_param		=
		array(
			'page'				=>		$page,
			'page_now'			=>		intval($page_now ),
			'refine'			=>		$param_refine,
			'filter'			=>		$filter,
			'header'			=>		$header,
			'orderby'			=>		$orderby,
			'order'				=>		$order,
			'scroll-now'		=>		intval($scroll_now ),
			'debug-mode'		=>		$debug_mode,
			'additional-mode'	=>		$additional_mode,
			'admin-mode'		=>		$admin_mode,
			'develop-mode'		=>		$develop_mode,
		);
	foreach		($temp_param		as	$temp_name => $temp_value ) {
		$html_input	.=	'<input type="hidden" name="'.esc_attr($temp_name ).'" value="'.esc_attr($temp_value ).'" title="'.esc_attr($temp_name ).'" size="4">';
	}

	// モード別の表示制御
	$html_style		.=	$additional_mode	==	0	?	'.pz-additional-only { display: none !important; } '	:	'';
	$html_style		.=	$admin_mode			==	0	?	'.pz-admin-only { display: none; } '	:	'';
	$html_style		.=	$develop_mode		==	0	?	'.pz-develop-only { display: none; } '	:	'';
	$html_style		.=	$this->options['debug-style-admin']		?	'.pz-man * { border: 1px solid #0f0 !important; } '	:	'';
	if	($html_style ) {
		$html_style	=	'<style>'.$html_style.'</style>';
	}


	// 処理中オーバーレイ
	if	(isset($prop['flg-inhibit'] ) ? $prop['flg-inhibit'] : $this->options['flg-inhibit'] ) {
		$html_overlay	=	'<div id="pz-overlay-proc"><div class="pz-loader"></div></div>';
	}
	$cacheman_allowed_html	=	array_merge(
		wp_kses_allowed_html('post' ),
		array(
			'input'			=>	array(
				'id'		=>	true,
				'type'		=>	true,
				'name'		=>	true,
				'value'		=>	true,
				'title'		=>	true,
				'size'		=>	true,
				'class'		=>	true,
				'readonly'	=>	true,
				'checked'	=>	true,
				'accesskey'	=>	true,
				'placeholder'	=>	true,
				'ondblclick'	=>	true,
			),
			'textarea'		=>	array(
				'id'		=>	true,
				'name'		=>	true,
				'rows'		=>	true,
				'wrap'		=>	true,
				'accesskey'	=>	true,
				'readonly'	=>	true,
				'ondblclick'	=>	true,
			),
			'button'		=>	array(
				'type'		=>	true,
				'name'		=>	true,
				'value'		=>	true,
				'class'		=>	true,
				'title'		=>	true,
				'aria-label'	=>	true,
				'formnovalidate'	=>	true,
				'data-no-overlay'	=>	true,
				'data-pz-media-target'	=>	true,
			),
			'form'			=>	array(
				'action'	=>	true,
				'method'	=>	true,
				'enctype'	=>	true,
			),
			'style'		=>	array(
				'type'	=>	true,
			),
		)
	);

	$screen_option_columns	=	array(
		'id'			=>	array( 'label' => __('Card ID', 'pz-linkcard3' ),			'selectors' => array('pz-man-head-card_id', 'pz-man-body-id' ) ),
		'excerpt'		=>	array( 'label' => __('Excerpt', 'pz-linkcard3' ),			'selectors' => array('pz-man-head-excerpt', 'pz-man-body-excerpt-cell' ) ),
		'charset'		=>	array( 'label' => __('Charset', 'pz-linkcard3' ),			'selectors' => array('pz-man-head-charset', 'pz-man-body-charset' ) ),
		'domain'		=>	array( 'label' => __('Domain', 'pz-linkcard3' ),			'selectors' => array('pz-man-head-domain', 'pz-man-body-domain-cell' ) ),
		'sns'			=>	array( 'label' => __('SNS', 'pz-linkcard3' ),				'selectors' => array('pz-man-head-sns_twitter', 'pz-man-body-sns' ) ),
		'regist_time'	=>	array( 'label' => __('Registration Date', 'pz-linkcard3' ),	'selectors' => array('pz-man-head-regist_time', 'pz-man-body-regist-time' ) ),
		'update_time'	=>	array( 'label' => __('Update Date', 'pz-linkcard3' ),		'selectors' => array('pz-man-head-update_time', 'pz-man-body-update-time' ) ),
		'sns_time'		=>	array( 'label' => __('SNS Check Date', 'pz-linkcard3' ),	'selectors' => array('pz-man-head-sns_time', 'pz-man-body-sns-time' ) ),
		'alive_time'	=>	array( 'label' => __('Alive Check Date', 'pz-linkcard3' ),	'selectors' => array('pz-man-head-alive_time', 'pz-man-body-alive-time' ) ),
		'post_id'		=>	array( 'label' => __('Post ID', 'pz-linkcard3' ),			'selectors' => array('pz-man-head-use_post_id1', 'pz-man-body-post-id' ) ),
		'click_count'	=>	array( 'label' => __('Click Count', 'pz-linkcard3' ),		'selectors' => array('pz-man-head-click_count', 'pz-man-body-click-count' ) ),
		'result'		=>	array( 'label' => __('Result Code', 'pz-linkcard3' ),		'selectors' => array('pz-man-head-update_result', 'pz-man-body-result' ) ),
	);
	$screen_option_meta_key	=	'pz_lkc3_cacheman_columns';
	$screen_option_per_page_meta_key	=	'pz_lkc3_cacheman_per_page';
	$screen_option_per_page_choices	=	array(10, 20, 50, 100);
	$screen_option_defaults	=	array(
		'id'			=>	true,
		'excerpt'		=>	true,
		'charset'		=>	false,
		'domain'		=>	true,
		'thumbnail'		=>	false,
		'site_icon'		=>	false,
		'sns'			=>	true,
		'regist_time'	=>	false,
		'update_time'	=>	true,
		'sns_time'		=>	false,
		'alive_time'	=>	false,
		'post_id'		=>	true,
		'click_count'	=>	true,
		'result'		=>	true,
	);
	$screen_option_saved		=	get_user_meta(get_current_user_id(), $screen_option_meta_key, true );
	if	(!is_array($screen_option_saved ) ) {
		$screen_option_saved	=	array();
	}
	unset($screen_option_saved['thumbnail'], $screen_option_saved['site_icon'] );
	$screen_option_per_page	=	intval(get_user_meta(get_current_user_id(), $screen_option_per_page_meta_key, true ) );
	if	(!in_array($screen_option_per_page, $screen_option_per_page_choices, true ) ) {
		$screen_option_per_page	=	10;
	}
	$screen_option_is_visible = function($column_key) use ($screen_option_saved, $screen_option_defaults) {
		return array_key_exists($column_key, $screen_option_saved )
			? (bool) $screen_option_saved[$column_key]
			: (bool) ($screen_option_defaults[$column_key] ?? true);
	};
	$screen_option_hidden_class = function($column_key) use ($screen_option_is_visible) {
		return $screen_option_is_visible($column_key) ? '' : ' pz-man-column-hidden';
	};

	// 画面を出力
	echo	wp_kses($html_style, $cacheman_allowed_html );
	echo	wp_kses($html_infobar, $cacheman_allowed_html );
	echo	wp_kses($html_overlay, $cacheman_allowed_html );
	
	echo	'<div id="pz-man" class="pz-dashboard pz-man pz-cacheman wrap">';
	echo	'<header class="pz-header">';
	echo	wp_kses_post($html_title );
	echo	'</header>';

	$form_enctype = (	$action === 'show-import' || $action === 'exec-import' ) ? ' enctype="multipart/form-data"' : ''; 
	echo	'<form action="" method="POST"';
	if	($form_enctype ) {
		echo	' enctype="multipart/form-data"';
	}
	echo	'>';
	wp_nonce_field('pz-cacheman' );			// nonce

	// 記録されたURLエラーを通知
	if	($this->options['error-mode'] ) {
		if	(!$this->options['error-mode-hide'] ) {
			$error_scroll_url	=	$this->options['error-url'] ? add_query_arg('pz_lkc3_scroll', 'lkc3-error', $this->options['error-url'] ) : '';
			/* translators: %s: 設定画面のURL */
			$html_notice	.=	'<div class="notice notice-error is-dismissible"><p><strong>'.esc_html($this->plugin_name ).': '.__('Invalid URL parameter in ', 'pz-linkcard3' ).'<a href="'.esc_url($error_scroll_url ).'" target="_blank">'.esc_html($this->options['error-url'] ).'</a></strong><br>'.__('*', 'pz-linkcard3' ).' '.sprintf(__('You can cancel this message from <a href="%s">the setting screen</a>.', 'pz-linkcard3' ), esc_url($this->settings_url ) ).'</p></div>';
		}
	}

	// 指定されたアクションを処理
	if	($action ) {
		check_admin_referer('pz-cacheman' );

		ignore_user_abort(false);
		switch	($action ) {
		case	'jump-page':				// ページ移動
			$page_now	=	(isset($post_data['page_now'] ) ? absint($post_data['page_now'] ) : 1 );
			break;

		case	'search':					// 検索
			break;
		
		case	'select-domain':			// ドメインで絞り込み
			break;
		
		case	'cancel':					// 編集画面をキャンセル
			$return_to_frontend();
			break;

		case	'edit':						// 編集画面
			$selected_ids			=	$get_selected_ids($select_id );
			if	(!$selected_ids ) {
				$html_notice		.=	$make_not_selected_notice();
				break;
			}
			$data					=	$this->pz_GetCache(array('card_id' => $selected_ids[0] ) );
			if	(isset($data ) && is_array($data ) ) {
				require ('pz-lkc3-cacheman-editor.php');
			}
			$show_list				=	false;	// 一覧を表示しない
			break;

		case	'update':
			$success_count			=	0;
			$skip_count				=	0;
			if	(!isset($data ) || !is_array($data ) || !isset($data['card_id'] ) ) {
				$html_notice		.=	$make_not_selected_notice();
				break;
			}
			$before_data			=	$this->pz_GetCache(array('card_id' => absint($data['card_id'] ) ) );
			$image_changed			=	false;
			if	(is_array($before_data ) ) {
				foreach	(array('thumbnail', 'site_icon' ) as $image_key ) {
					if	(array_key_exists($image_key, $data ) && ($data[$image_key] ?? '' ) !== ($before_data[$image_key] ?? '' ) ) {
						$image_changed	=	true;
						break;
					}
				}
			}
			if	($image_changed && empty($data['regist_time'] ) ) {
				$data['regist_time']	=	current_time('timestamp', false );
				$data['regist_title']	=	$data['regist_title']	??	($data['title']		??	'' );
				$data['regist_excerpt']	=	$data['regist_excerpt']	??	($data['excerpt']	??	'' );
				$data['regist_charset']	=	$data['regist_charset']	??	($data['charset']	??	'edit' );
				$data['regist_result']	=	$data['regist_result']	??	($data['update_result']	??	0 );
			}
			foreach	($data			as	$key => $value ) {
				$data[$key]			=	stripslashes($value );
			}
			$data	=	$this->pz_SetCache($data );
			if	(isset($data ) && is_array($data ) && isset($data['card_id'] ) ) {
				$success_count++;
			}
			$return_to_frontend();
			$html_notice			.=	$make_result_notice(__('Update Cache', 'pz-linkcard3' ), $success_count, $skip_count );
			break;

		case	'renew':
			$success_count			=	0;
			$skip_count				=	0;
			$selected_ids			=	$get_selected_ids($select_id );
			if	(!$selected_ids ) {
				$html_notice		.=	$make_not_selected_notice();
				break;
			}
			foreach	($selected_ids as $card_id) {
				$stop_if_aborted();

				$data				=	['card_id' => $card_id ];
				$data				=	$this->pz_GetCache($data );
				if	(isset($data ) && is_array($data ) && isset($data['card_id'] ) && isset($data['url'] ) ) {
					$result		=	$this->pz_GetCURL($data, 30 );
					if	(isset($result ) && is_array($result ) && !array_key_exists('error', $result ) ) {
						$data                       =   array_merge($data, $result );
						$data['regist_time']        =	empty($data['regist_time'] ) ? current_time('timestamp', false ) : $data['regist_time'];
						$data['update_time']        =	current_time('timestamp', false );
						$data['regist_title']       =   $data['title'] ?? '';
						$data['regist_excerpt']     =	$data['excerpt'] ?? '';
						$data['regist_charset']     =	$data['charset'] ?? '';
						$data['regist_result']      =	$data['update_result'] ?? '';
						$data['alive_result']       =	$data['update_result'] ?? '';
						$data['alive_time']         =	$data['update_time'] ?? '';
						$data['thumbnail']			=	$data['thumbnail'] ?? '';
						$data['site_icon']			=	$data['site_icon'] ?? '';
						$result			=	$this->pz_SetCache($data );
					}

					if	(isset($data['thumbnail'] ) ) {
						$this->pz_GetImage($data['thumbnail'], true );
					}
					if	(isset($data['site_icon'] ) ) {
						$this->pz_GetImage($data['site_icon'], true );
					}

					$success_count++;
				} else {
					$skip_count++;
				}
			}
			$html_notice			.=	$make_result_notice(__('Renew Cache', 'pz-linkcard3' ), $success_count, $skip_count );
			break;

		case	'renew_thumbnail':
			$success_count			=	0;
			$skip_count				=	0;
			$selected_ids			=	$get_selected_ids($select_id );
			if	(!$selected_ids ) {
				$html_notice		.=	$make_not_selected_notice();
				break;
			}
			foreach	($selected_ids as $card_id ) {
				$stop_if_aborted();
				$data				=	$this->pz_GetCache(['card_id' => $card_id] );
				if	(isset($data ) && is_array($data ) ) {
					$data			=	$this->pz_GetImage($data['thumbnail'] , true );
					$success_count++;
				} else {
					$skip_count++;
				}
				$html_notice		.=	'..';
			}
			$html_notice			.=	$make_result_notice(__('Renew Thumbnail Image', 'pz-linkcard3' ), $success_count, $skip_count );
			break;

		case	'renew_sns':
			$success_count			=	0;
			$skip_count				=	0;
			$selected_ids			=	$get_selected_ids($select_id );
			if	(!$selected_ids ) {
				$html_notice		.=	$make_not_selected_notice();
				break;
			}
			foreach	($selected_ids as $card_id ) {
				$stop_if_aborted();
				$data				=	$this->pz_GetCache(array('card_id' => $card_id ) );
				if	(isset($data ) && is_array($data ) ) {
					$data['sns_nexttime']	=	0;
					$data			=	$this->pz_SetCache($data );
					$data			=	$this->pz_RenewSNSCount($data );
					$success_count++;
				} else {
					$skip_count++;
				}
			}
			$html_notice			.=	$make_result_notice(__('Renew SNS Count', 'pz-linkcard3' ), $success_count, $skip_count );
			break;

		case	'renew_postid':
			$success_count			=	0;
			$skip_count				=	0;
			$selected_ids			=	$get_selected_ids($select_id );
			if	(!$selected_ids ) {
				$html_notice		.=	$make_not_selected_notice();
				break;
			}
			foreach	($selected_ids as $card_id ) {
				$stop_if_aborted();
				$data				=	$this->pz_GetCache(array('card_id' => $card_id ) );
				$result				=	null;

				if	(isset($data ) && is_array($data ) ) {
					$result			=	$this->pz_SetCache($data );
				}
				if	($result ) {
					$success_count++;
				} else {
					$skip_count++;
				}
			}
			$html_notice			.=	$make_result_notice(__('Renew Post ID', 'pz-linkcard3' ), $success_count, $skip_count );
			break;

		case	'alive':
			$success_count			=	0;
			$skip_count				=	0;
			$selected_ids			=	$get_selected_ids($select_id );
			if	(!$selected_ids ) {
				$html_notice		.=	$make_not_selected_notice();
				break;
			}
			foreach	($selected_ids as $card_id ) {
				$stop_if_aborted();
				$data				=	$this->pz_GetCache(array('card_id' => $card_id ) );
				if	(isset($data ) && is_array($data ) ) {
					$data						=	$this->pz_GetCache($data );
					$after						=	$this->pz_GetCURL($data );
					$data['alive_result']		=	$after['update_result'] ?? '';
					$data['alive_time']			=	$this->now;
					$data['alive_nexttime']		=	$this->now + WEEK_IN_SECONDS * 4;
					$data						=	$this->pz_SetCache($data );
					if	($data ) {
						$success_count++;
					} else {
						$skip_count++;
					}
				}
			}
			$html_notice			.=	$make_result_notice(__('Alive check', 'pz-linkcard3' ), $success_count, $skip_count );
			break;

		case	'delete':
			$success_count			=	0;
			$skip_count				=	0;
			$selected_ids			=	$get_selected_ids($select_id );
			if	(!$selected_ids ) {
				$html_notice		.=	$make_not_selected_notice();
				break;
			}
			foreach	($selected_ids as $card_id ) {
				$stop_if_aborted();
 				$result				=	$this->pz_DelCache(array('card_id' => $card_id ) );
 				if	($result ) {
 					$success_count++;
 				} else {
 					$skip_count++;
 				}
			}
			$html_notice			.=	$make_result_notice(__('Delete Cache', 'pz-linkcard3' ), $success_count, $skip_count );
			break;

		case	'show-import':
			// インポートメニューを表示
			require ('pz-lkc3-file-import-menu.php');
			$show_list				=	false;
			break;

		case	'exec-import':
			// CSVファイルからインポート
			require ('pz-lkc3-file-import-csv.php');
			$show_list				=	false;
			break;

		case	'exec-import-host':
			// Pz-LinkCard 2.xのDBテーブルからインポート
			require ('pz-lkc3-file-import-host.php');
			$show_list				=	false;
			break;

		case	'show-export':
			// エクスポートメニューを表示
			require ('pz-lkc3-file-export-menu.php');
			$show_list				=	false;
			break;

		case	'exec-export':
			// CSVファイルへエクスポート
			require ('pz-lkc3-file-export.php');
			$show_list				=	false;
			break;

		case	'filter':
			$page_now				=	1;
			break;

		default:
			/* translators: %s: 指定されたアクション */
			$html_notice			.=	$make_notice('info', esc_html(sprintf(__('Undefined process chosen. (action="%s")', 'pz-linkcard3' ), $action ) ) );
		}
	}

	echo	wp_kses($html_notice, $cacheman_allowed_html );
	echo	wp_kses($html_input, $cacheman_allowed_html );

	if	(!$show_list ) {
		echo	'<div style="display: none;">';
	}
	require ('pz-lkc3-cacheman-list.php');
	if	(!$show_list ) {
		echo	'</div>';
	}

	echo	'</form>';
	echo	'</div>';

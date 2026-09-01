<?php
	
if (!defined( 'ABSPATH' ) ) { header( 'HTTP/1.1 403 Forbidden' ); exit; }
		global	$wp_version;

		// リンク先URL
		$url			=	isset($data['url']) ? $data['url'] : '' ;

		// URL指定なし
		if	(!$url ) {
			return	null;
		}

		// wp_safe_remote_get/wp_safe_remote_headのオプション
		$rget_args					=	[];
		$rget_args['user-agent']	=	$this->options['user-agent']	?	$this->options['user-agent']	:	'WordPress/'.$wp_version.'; '.get_bloginfo( 'url' );
		$rget_args['sslverify']		=	$this->options['flg-sslverify']	?	true	:	false ;
		$rget_args['redirection']	=	0;
		$rget_args['reject_unsafe_urls']	=	true;
		$rget_args['limit_response_size']	=	1048576;

		if	(!empty($this->options['flg-referer'] ) ) {
			$use_post_id	=	intval($data['use_post_id1'] ?? 0 );
			$referer_url	=	$use_post_id ? get_permalink($use_post_id ) : '';
			if	(!$referer_url ) {
				$referer_url	=	home_url('/');
			}
			if	($referer_url ) {
				$rget_args['headers']	=	['Referer' => esc_url_raw($referer_url )];
			}
		}

		// URLエンコード
		$url			=	$this->pz_EncodeURL($url ,true );
		$url_redir		=	'';
		$url_access		=	$url;
		$last_url		=	$url;
		$is_blocked_local_url	=	function($check_url ) {
			return	!empty($this->options['flg-local-check'] ) && !$this->pz_IsSiteURL($check_url ) && $this->pz_IsLocalAddress($check_url );
		};
		$make_absolute_url	=	function($location, $base_url ) {
			if	(class_exists('WP_Http' ) && method_exists('WP_Http', 'make_absolute_url' ) ) {
				return	WP_Http::make_absolute_url($location, $base_url );
			}
			return	$location;
		};
		$local_address_error	=	$is_blocked_local_url($url );

		// リダイレクト確認
		if	(!$local_address_error && !empty($this->options['ex-content-redir'] ) ) {
			for	($redirect_count = 0; $redirect_count < 8; $redirect_count++ ) {
				$head_args					=	$rget_args;
				$head_args['method']		=	'HEAD';
				$head_args['redirection']	=	0;
				$rget_head					=	wp_safe_remote_head($last_url, $head_args );		// Bodyを取得せず、リダイレクトだけ確認
				$response					=	$rget_head;
				if	(is_wp_error($rget_head ) || intval(wp_remote_retrieve_response_code($rget_head ) ) >= 400 ) {
					$redir_args							=	$rget_args;
					$redir_args['redirection']			=	0;
					$redir_args['limit_response_size']	=	1;						// HEAD不可のサイト向けにBody取得を最小化
					$response							=	wp_safe_remote_get($last_url, $redir_args );
				}

				if	(is_wp_error($response ) ) {
					break;
				}

				$location	=	wp_remote_retrieve_header($response, 'location' );
				if	(is_array($location ) ) {
					$location	=	end($location );
				}
				$location	=	trim($location );
				if	(!$location ) {
					break;
				}

				$next_url	=	$make_absolute_url($location, $last_url );
				$next_url	=	$this->pz_EncodeURL($next_url, true );
				if	(!$next_url || (!$this->pz_IsSiteURL($next_url ) && !wp_http_validate_url($next_url ) ) ) {
					break;
				}

				$last_url	=	$next_url;
				if	($is_blocked_local_url($last_url ) ) {
					$local_address_error	=	true;
					break;
				}
			}

			if	($last_url && $url <> $last_url ) {
				$url_redir	=	$this->pz_EncodeURL($last_url, true );
				$url_access	=	$url_redir;
			}
		}

		// 初期化
		$domain			=	'';
		$sitename		=	'';
		$author			=	'';
		$type			=	'';
		$title			=	'';
		$excerpt		=	'';
		$thumbnail_url	=	'';
		$siteicon_url	=	'';
		$charset		=	'';
		$http_code		=	'';
		$error			=	false;

		// URL解析（自サイトチェック）
		$url_info		=	$this->Pz_GetURLInfo($url_access );
		$scheme			=	$url_info['scheme'];		// スキーム
		$domain			=	$url_info['domain'];		// ドメイン名
		$domain_url		=	$url_info['domain_url'];	// ドメインURL

		// ローカルアドレス確認
		if	(!$local_address_error && !empty($this->options['flg-local-check'] ) ) {
			$local_address_error	=	$is_blocked_local_url($url ) || ($url_redir && $is_blocked_local_url($url_redir ) );
		}
		if	($local_address_error ) {
			$data['id']					=	$data['id'] ?? '';					// リンクカードID
			$data['url']				=	$url;								// リンク先：URL
			$data['url_redir']			=	$url_redir;							// リンク先：リダイレクト先
			$data['domain']				=	$data['domain'] ?? '';				// リンク先：URLドメイン
			$data['title']				=	__('Invalid URL', 'pz-linkcard3' );	// リンク先：タイトル
			$data['excerpt']			=	'';									// リンク先：抜粋文
			$data['regist_title']		=	$data['title'] ?? '';				// リンク先：タイトル
			$data['regist_excerpt']		=	'';									// リンク先：抜粋文
			$data['sns_nexttime']		=	253402300799;						// SNS：次回取得日時
			$data['alive_nexttime']		=	253402300799;						// 生存確認：次回確認日時
			$data['regist_time']		=	$this->now;							// 登録時：登録日時
			$data['regist_result']		=	403;								// 登録時：HTTPレスポンス
			$data['update_time']		=	$this->now;							// 更新：最終更新日
			$data['update_result']		=	403;								// 更新：HTTPレスポンス

			return	$data;
		}

		$robots_blocked_result	=	function($blocked_url ) use ($data, $url, $url_redir ) {
			$blocked_info	=	$this->Pz_GetURLInfo($blocked_url );
			$blocked_title	=	__('Blocked by robots.txt.', 'pz-linkcard3' );
			$blocked_redir	=	($blocked_url && $blocked_url <> $url ) ? $blocked_url : $url_redir;
			if	(!$blocked_redir || $blocked_redir == $url ) {
				$blocked_redir	=	null;
			}

			$data['id']					=	$data['id'] ?? '';
			$data['url']				=	$url;
			$data['url_redir']			=	$blocked_redir;
			$data['domain']				=	$blocked_info['domain'] ?? '';
			$data['site_name']			=	'';
			$data['title']				=	$blocked_title;
			$data['excerpt']			=	'';
			$data['thumbnail']			=	'';
			$data['site_icon']			=	'';
			$data['charset']			=	'';
			$data['alive_time']			=	$this->now;
			$data['alive_nexttime']		=	99999999999;
			$data['alive_result']		=	403;
			$data['sns_twitter']		=	null;
			$data['sns_facebook']		=	null;
			$data['sns_hatena']			=	null;
			$data['sns_nexttime']		=	99999999999;
			$data['use_post_id1']		=	$data['use_post_id1'] ?? get_the_ID();
			$data['use_post_id2']		=	$data['use_post_id2'] ?? null;
			$data['use_post_id3']		=	$data['use_post_id3'] ?? null;
			$data['use_post_id4']		=	$data['use_post_id4'] ?? null;
			$data['use_post_id5']		=	$data['use_post_id5'] ?? null;
			$data['use_post_id6']		=	$data['use_post_id6'] ?? null;
			$data['mod_title']			=	false;
			$data['mod_excerpt']		=	false;
			$data['update_time']		=	$this->now;
			$data['update_result']		=	403;
			if	(is_null($data['regist_time'] ?? null ) ) {
				$data['regist_title']	=	$blocked_title;
				$data['regist_excerpt']	=	'';
				$data['regist_charset']	=	'';
				$data['regist_time']	=	$data['update_time'];
				$data['regist_result']	=	$data['update_result'];
			}

			return	$data;
		};

		if	(!empty($url_info['is_external'] ) && !$this->pz_IsRobotsAllowed($url_access, $rget_args['user-agent'] ) ) {
			return	$robots_blocked_result($url_access );
		}

		// リンク先サイトのアクセス
		$get_oembed_data	=	function() use ($data, $url, $url_access, $url_redir, $domain, $url_info ) {
			if	(empty($url_info['is_external'] ) ) {
				return	null;
			}
			if	(!function_exists('_wp_oembed_get_object' ) ) {
				require_once ABSPATH.WPINC.'/embed.php';
			}
			if	(!function_exists('_wp_oembed_get_object' ) ) {
				return	null;
			}

			$oembed	=	_wp_oembed_get_object();
			if	(!is_object($oembed ) || !method_exists($oembed, 'get_data' ) ) {
				return	null;
			}

			$oembed_data	=	$oembed->get_data($url_access, array('discover' => true ) );
			if	(!$oembed_data || !is_object($oembed_data ) ) {
				return	null;
			}

			$title		=	trim(wp_strip_all_tags((string) ($oembed_data->title ?? '' ) ) );
			$excerpt	=	trim(wp_strip_all_tags((string) ($oembed_data->description ?? '' ) ) );
			if	(!$excerpt && !empty($oembed_data->html ) ) {
				$excerpt	=	trim(wp_strip_all_tags((string) $oembed_data->html ) );
			}
			$excerpt	=	preg_replace('/\s+/u', ' ', $excerpt );
			$sitename	=	trim(wp_strip_all_tags((string) ($oembed_data->provider_name ?? '' ) ) );
			if	(!$sitename ) {
				$sitename	=	trim(wp_strip_all_tags((string) ($oembed_data->author_name ?? '' ) ) );
			}

			$thumbnail_url	=	$this->pz_EncodeURL((string) ($oembed_data->thumbnail_url ?? '' ), true );
			if	($thumbnail_url && !wp_http_validate_url($thumbnail_url ) ) {
				$thumbnail_url	=	'';
			}

			$oembed_result	=	$data;
			$oembed_result['id']				=	$oembed_result['id']	??	null;
			$oembed_result['url']				=	$url;
			$oembed_result['url_redir']			=	($url_redir && $url_redir <> $url ) ? $url_redir : null;
			$oembed_result['domain']			=	$domain;
			$oembed_result['site_name']			=	$sitename;
			$oembed_result['title']				=	mb_strimwidth($title, 0, 500, '...' );
			$oembed_result['excerpt']			=	mb_strimwidth($excerpt, 0, 1000, '...' );
			$oembed_result['thumbnail']			=	$thumbnail_url;
			$oembed_result['site_icon']			=	$oembed_result['site_icon']	??	'';
			$oembed_result['charset']			=	'UTF-8';
			$oembed_result['alive_time']		=	$this->now;
			$oembed_result['alive_nexttime']	=	$this->now + WEEK_IN_SECONDS * 4 + wp_rand(0, DAY_IN_SECONDS );
			$oembed_result['alive_result']		=	200;
			$oembed_result['sns_twitter']		=	$oembed_result['sns_twitter']	??	-1;
			$oembed_result['sns_facebook']		=	$oembed_result['sns_facebook']	??	-1;
			$oembed_result['sns_hatena']		=	$oembed_result['sns_hatena']	??	-1;
			$oembed_result['sns_time']			=	$oembed_result['sns_time']		??	0;
			$oembed_result['sns_nexttime']		=	$oembed_result['sns_nexttime']	??	0;
			$oembed_result['use_post_id1']		=	$oembed_result['use_post_id1']	??	get_the_ID();
			$oembed_result['use_post_id2']		=	$oembed_result['use_post_id2']	??	null;
			$oembed_result['use_post_id3']		=	$oembed_result['use_post_id3']	??	null;
			$oembed_result['use_post_id4']		=	$oembed_result['use_post_id4']	??	null;
			$oembed_result['use_post_id5']		=	$oembed_result['use_post_id5']	??	null;
			$oembed_result['use_post_id6']		=	$oembed_result['use_post_id6']	??	null;
			$oembed_result['regist_title']		=	$oembed_result['regist_title']		??	$oembed_result['title'];
			$oembed_result['regist_excerpt']	=	$oembed_result['regist_excerpt']	??	$oembed_result['excerpt'];
			$oembed_result['regist_charset']	=	$oembed_result['regist_charset']	??	$oembed_result['charset'];
			$oembed_result['regist_time']		=	$oembed_result['regist_time']		??	0;
			$oembed_result['regist_result']		=	$oembed_result['regist_result']		??	200;
			$oembed_result['mod_title']			=	false;
			$oembed_result['mod_excerpt']		=	false;
			$oembed_result['update_time']		=	$this->now;
			$oembed_result['update_result']		=	200;

			return	$oembed_result;
		};

		$oembed_result	=	$get_oembed_data();
		if	(is_array($oembed_result ) ) {
			return	$oembed_result;
		}

		$get_args					=	$rget_args;
		$get_args['redirection']	=	0;
		$rget_data					=	wp_safe_remote_get($url_access, $get_args );	// wp_remote_get実行
		$err_no						=	is_wp_error($rget_data );						// wp_remote_getエラー有無

		if	(!$err_no ) {
			$http_response	=	$rget_data['http_response'] ?? null;
			$response_obj	=	is_object($http_response ) && method_exists($http_response, 'get_response_object' ) ? $http_response->get_response_object() : null;
			$response_url	=	is_object($response_obj ) && !empty($response_obj->url ) ? esc_url_raw($response_obj->url ) : '';
			if	($response_url ) {
				$response_url	=	$this->pz_EncodeURL($response_url, true );
				if	($response_url && $response_url <> $url ) {
					$url_redir	=	$response_url;
					$url_access	=	$response_url;
				}
			}
		}

		if	(!$err_no ) {
			for	($redirect_count = 0; $redirect_count < 8; $redirect_count++ ) {
				$response_code	=	intval(wp_remote_retrieve_response_code($rget_data ) );
				if	($response_code < 300 || $response_code >= 400 ) {
					break;
				}

				$location	=	wp_remote_retrieve_header($rget_data, 'location' );
				if	(is_array($location ) ) {
					$location	=	end($location );
				}
				$location	=	trim($location );
				if	(!$location ) {
					break;
				}

				$next_url	=	$make_absolute_url($location, $url_access );
				$next_url	=	$this->pz_EncodeURL($next_url, true );
				if	(!$next_url || (!$this->pz_IsSiteURL($next_url ) && !wp_http_validate_url($next_url ) ) ) {
					break;
				}
				if	($is_blocked_local_url($next_url ) ) {
					$local_address_error	=	true;
					break;
				}
				if	(!$this->pz_IsRobotsAllowed($next_url, $rget_args['user-agent'] ) ) {
					return	$robots_blocked_result($next_url );
				}

				$url_redir		=	$next_url;
				$url_access		=	$next_url;
				$rget_data		=	wp_safe_remote_get($url_access, $get_args );
				$err_no			=	is_wp_error($rget_data );
				if	($err_no ) {
					break;
				}
			}
		}

		// エラーチェック
		if ($err_no ) {
			$http_type		=	'';
			$http_body		=	'';
			$http_code		=	-1;
		} else {
			$http_type		=	$rget_data['headers']['content-type'];		// Content_Type
			$http_body		=	$rget_data['body'];							// HTTPボディー
			$http_code		=	$rget_data['response']['code'];				// HTTPステータス
		}

		if	($url_access ) {
			$url_info		=	$this->Pz_GetURLInfo($url_access );
			$scheme			=	$url_info['scheme'];		// スキーム
			$domain			=	$url_info['domain'];		// ドメイン名
			$domain_url		=	$url_info['domain_url'];	// ドメインURL
		}

		// Bodyが取得出来ていたらCharset判定
		$is_html_response	=	(strtolower(substr($http_type, 0, 9) ) === 'text/html' || $http_code == '200' ) && $http_body;
		if	($is_html_response ) {
			$charset_candidates	=	array_unique(array_map(function($charset_name ) {
				return	$this->pz_NormalizeCharsetName($charset_name );
			}, array('UTF-8', 'eucJP-win', 'SJIS-win', 'ASCII', 'EUC-JP', 'SJIS', 'JIS') ) );
			$get_declared_charsets	=	function($text ) {
				$declared	=	array();
				if	(preg_match_all('/charset\s*=\s*[\'"]?\s*([A-Za-z0-9._-]+)/si', (string) $text, $matches ) ) {
					$declared	=	array_merge($declared, $matches[1] );
				}
				if	(preg_match_all('/encoding\s*=\s*[\'"]\s*([A-Za-z0-9._-]+)/si', (string) $text, $matches ) ) {
					$declared	=	array_merge($declared, $matches[1] );
				}
				return	$declared;
			};
			$declared_charsets	=	array_merge($get_declared_charsets($http_type ), $get_declared_charsets(substr($http_body, 0, 65536 ) ) );
			$declared_charsets	=	array_filter(array_map(function($charset_name ) {
				return	$this->pz_NormalizeCharsetName($charset_name );
			}, $declared_charsets ) );
			$detected_charset	=	$this->pz_NormalizeCharsetName(mb_detect_encoding($http_body, $charset_candidates, false ) );
			$has_non_ascii		=	(bool) preg_match('/[\x80-\xFF]/', $http_body );
			$best_charset		=	'';
			$best_score			=	null;

			foreach	($charset_candidates as $candidate ) {
				$candidate	=	$this->pz_NormalizeCharsetName($candidate );
				$valid		=	mb_check_encoding($http_body, $candidate );
				$converted	=	@mb_convert_encoding($http_body, 'UTF-8', $candidate );
				if	($converted === false || $converted === '' ) {
					continue;
				}

				$score	=	$valid ? 30 : -80;
				if	($candidate === $detected_charset ) {
					$score	+=	18;
				}
				if	(in_array($candidate, $declared_charsets, true ) ) {
					$score	+=	10;
				}
				if	($candidate === 'ASCII' && $has_non_ascii ) {
					$score	-=	80;
				}
				if	($candidate === 'UTF-8' && !mb_check_encoding($http_body, 'UTF-8' ) ) {
					$score	-=	120;
				}

				$replacement_count	=	substr_count($converted, "\xEF\xBF\xBD");
				$score	-=	$replacement_count * 25;
				if	(preg_match_all('/(?:Ã.|Â.|縺|繧|荳|譁|驥|蜿|邱|髱|螟|隕|譛|邨)/u', $converted, $matches ) ) {
					$score	-=	count($matches[0] ) * 12;
				}
				if	(preg_match_all('/[\x{3040}-\x{30ff}\x{3400}-\x{9fff}]/u', $converted, $matches ) ) {
					$score	+=	min(30, count($matches[0] ) );
				}

				if	($best_score === null || $score > $best_score ) {
					$best_score		=	$score;
					$best_charset	=	$candidate;
				}
			}

			$charset	=	$best_charset ? $best_charset : ($detected_charset ? $detected_charset : 'UTF-8' );
			if	($charset ) {
				$http_body	=	mb_convert_encoding($http_body, $this->my_charset, $charset );
			}
		}

		// Charset判定出来ていたら
		if	($is_html_response ) {
			if	(!$charset ) {
				$charset	=	$this->my_charset ? $this->my_charset : 'UTF-8';
			}
			// HEADタグ（METAタグ解析）
			$html_head		=	null;
			$tags			=	null;
			if	(preg_match('/<\s*head[^>]*>(.*)<\s*\/head\s*>/si', $http_body, $m ) ) {
				$html_head	=	$m[1];
				$tags		=	$this->pz_GetMeta($html_head );
			} else {
				$tags		=	$this->pz_GetMeta($http_body );
			}

			// Open Graph Protcol
			$og_url			=	$tags['og:url']					??	'';
			$og_type		=	$tags['og:type']				??	'';
			$og_sitename	=	$tags['og:site_name']			??	'';
			$og_author		=	null;
			$og_title		=	$tags['og:title']				??	'';
			$og_excerpt		=	$tags['og:description']			??	'';
			$og_image		=	$tags['og:image']				??	'';

			// Twitter card
			$tw_url			=	null;
			$tw_sitename	=	$tags['twitter:site']			??	'';
			$tw_author		=	$tags['twitter:creator']		??	'';
			$tw_type		=	$tags['twitter:card']			??	'';
			$tw_title		=	$tags['twitter:title']			??	'';
			$tw_excerpt		=	$tags['twitter:description']	??	'';
			$tw_image		=	$tags['twitter:image']			??	'';

			// タイトル＆概要文
			$title			=	$tags['title']				??	'';
			$excerpt		=	$tags['description']		??	'';
			if				(!$title ) {
				if			($og_title ) {
					$title			=	$og_title;
				} elseif	($tw_title ) {
					$title			=	$tw_title;
				}
			}
			if				(!$excerpt ) {
				if			($og_excerpt ) {
					$excerpt		=	$og_excerpt;
				} elseif	($tw_excerpt ) {
					$excerpt		=	$tw_excerpt;
				}
			}

			// サムネイル画像
			$thumbnail_url		=	$this->pz_EncodeURL($thumbnail_url, true );
			if				(!$thumbnail_url ) {
				if			($og_image ) {
					$thumbnail_url =	$og_image;
				} elseif	($tw_image ) {
					$thumbnail_url =	$tw_image;
				}
			}

			// サイト名
			if				(!$sitename ) {
				if			($og_sitename ) {
					$sitename		=	$og_sitename;
				} elseif	($tw_sitename ) {
					$sitename		=	$tw_sitename;
				}
			}

			// サイトアイコンURL取得
			if			(isset(	$tags['icon'] )				&& $tags['icon'] ) {
				$siteicon_url	=	$tags['icon'];
			} elseif	(isset(	$tags['shortcut icon'] )	&& $tags['shortcut icon'] ) {
				$siteicon_url	=	$tags['shortcut icon'];
			} elseif	(isset(	$tags['apple-touch-icon'] )	&& $tags['apple-touch-icon'] ) {
				$siteicon_url	=	$tags['apple-touch-icon'];
			}
			$siteicon_url		=	$this->pz_EncodeURL($siteicon_url, true );

			// タイトル整形
			$title				=	mb_strimwidth($title, 0, 500, '...' );		// 500文字制限

			// 抜粋文整形
			$excerpt			=	mb_strimwidth($excerpt, 0, 1000, '...' );	// 1000文字制限
		}

		// 呼ばれている記事
		if	(!isset($data['use_post_id1'] ) ) {
			$data['use_post_id1']	=	get_the_ID();
		}

		// リダイレクト先URL
		if	(!$url_redir || $url == $url_redir ) {
			$url_redir	=	null;
		}

		// データセット
		$data['id']					=	$data['id']				??	null;				// リンクカードID
		$data['url']				=	$url;											// リンク先：URL
		$data['url_redir']			=	$url_redir;										// リンク先：リダイレクト先URL
		$data['domain']				=	$domain;										// リンク先：URLドメイン
		$data['site_name']			=	$sitename;										// リンク先：サイト名称
		$data['title']				=	$title;											// リンク先：タイトル
		$data['excerpt']			=	$excerpt;										// リンク先：抜粋文
		$data['thumbnail']			=	$thumbnail_url;									// リンク先：サムネイルURL
		$data['site_icon']			=	$siteicon_url;									// リンク先：サイトアイコンURL
		$data['charset']			=	$charset;										// リンク先：文字コード
		$data['alive_time']			=	$this->now;														// 生存確認：確認日時
		$data['alive_nexttime']		=	$this->now + WEEK_IN_SECONDS * 4 + wp_rand(0, DAY_IN_SECONDS );	// 生存確認：次回確認日時
		$data['alive_result']		=	$http_code;														// 生存確認：HTTPレスポンス
		$data['sns_twitter']		=	$data['sns_twitter']	??	-1;					// SNS：Twitter
		$data['sns_facebook']		=	$data['sns_facebook']	??	-1;					// SNS：facebook
		$data['sns_hatena']			=	$data['sns_hatena']		??	-1;					// SNS：はてなブックマーク
		$data['sns_time']			=	$data['sns_time']		??	0;					// SNS：最終取得日時
		$data['sns_nexttime']		=	$data['sns_nexttime']	??	0;					// SNS：次回取得日時
		$data['use_post_id1']		=	$data['use_post_id1']	??	null;				// 呼ばれている記事
		$data['use_post_id2']		=	$data['use_post_id2']	??	null;				// 呼ばれている記事
		$data['use_post_id3']		=	$data['use_post_id3']	??	null;				// 呼ばれている記事
		$data['use_post_id4']		=	$data['use_post_id4']	??	null;				// 呼ばれている記事
		$data['use_post_id5']		=	$data['use_post_id5']	??	null;				// 呼ばれている記事
		$data['use_post_id6']		=	$data['use_post_id6']	??	null;				// 呼ばれている記事
		$data['regist_title']		=	$data['regist_title']	??	$title;				// 登録時：タイトル
		$data['regist_excerpt']		=	$data['regist_excerpt']	??	$excerpt;			// 登録時：抜粋文
		$data['regist_charset']		=	$data['regist_charset']	??	$charset;			// 登録時：文字コード
		$data['regist_time']		=	$data['regist_time']	??	0;					// 登録時：登録日時
		$data['regist_result']		=	$data['regist_result']	??	$http_code;			// 登録時：HTTPレスポンス
		$data['mod_title']			=	false;											// 更新：登録後からタイトル変更有無
		$data['mod_excerpt']		=	false;											// 更新：登録後から抜粋文変更有無
		$data['update_time']		=	$this->now;										// 更新：最終更新日
		$data['update_result']		=	$http_code;										// 更新：HTTPレスポンス
		return	$data;

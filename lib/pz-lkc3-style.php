<?php
	
if (!defined( 'ABSPATH' ) ) { header( 'HTTP/1.1 403 Forbidden' ); exit; }
	$result		=	0;

	// テンプレートファイルの読み込み
	$wp_filesystem	=	$this->pz_GetFilesystem();
	if		(!$wp_filesystem ) {
		return	2;
	}
	$file_text	=	$wp_filesystem->get_contents($this->file_template );
	if		($file_text === false ) {
		return	2;
	}

	// テキスト設定・ここから -----------------------------------------------------------
	$items		=	array('title', 'url', 'excerpt', 'date', 'info', 'added', 'heading', 'more', 'cat', 'sns' );
	foreach	($items as $item ) {
		$item_name			=	strtolower($item.'-color' );
		if		(array_key_exists($item_name, $prop ) && $prop[$item_name] ) {
			$after			=	'color: '.$prop[$item_name].';';
			$file_text		=	str_replace('/*'.strtoupper($item_name ).'*/',		$after,		$file_text );
		}

		$item_name			=	strtolower($item.'-outline-color' );
		if		(array_key_exists($item_name, $prop ) && $prop[$item_name] ) {
			$after			=	'letter-spacing: 1px; text-shadow: 0 -1px '.$prop[$item_name]  .', 1px -1px '.$prop[$item_name]  .', 1px 0 '.$prop[$item_name]  .', 1px 1px '.$prop[$item_name]  .', 0 1px '.$prop[$item_name]  .', -1px 1px '.$prop[$item_name]  .', -1px 0 '.$prop[$item_name]  .', -1px -1px '.$prop[$item_name]  .';';
			//$after		=	'-webkit-text-stroke-width: 3px; -webkit-text-stroke-color: '.$prop[$item_name].';';
			$file_text		=	str_replace('/*'.strtoupper($item_name ).'*/',		$after,		$file_text );
		}

		$item_name			=	strtolower($item.'-bg-color' );
		if		(array_key_exists($item_name, $prop ) && $prop[$item_name] ) {
			$after			=	'padding: 4px; background-color: '.$prop[$item_name].';';
			$file_text		=	str_replace('/*'.strtoupper($item_name ).'*/',		$after,		$file_text );
		}

		$item_name			=	strtolower($item.'-size' );
		if		(array_key_exists($item_name, $prop ) && $prop[$item_name] ) {
			$after			=	'font-size: '.intval($prop[$item_name] ).'px;';
			$file_text		=	str_replace('/*'.strtoupper($item_name ).'*/',		$after,		$file_text );
		}

		$item_name			=	strtolower($item.'-height' );
		if		(array_key_exists($item_name, $prop ) && $prop[$item_name] ) {
			$after			=	'line-height: '.intval($prop[$item_name] ).'px;';
			$file_text		=	str_replace('/*'.strtoupper($item_name ).'*/',		$after,		$file_text );
		}

		$item_name			=	strtolower($item.'-maxline' );
		if		(array_key_exists($item_name, $prop ) ) {
			$maxline		=	intval($prop[$item_name] );
			if		($maxline	==	0 ) {
				$after		=	'white-space: wrap; text-overflow: ellipsis;';
			} else {
				$after		=	'white-space: wrap; text-overflow: ellipsis; display: -webkit-box !important; -webkit-box-orient: vertical; -webkit-line-clamp: '.$maxline.';';
			}
			$file_text		=	str_replace('/*'.strtoupper($item_name ).'*/',		$after,		$file_text );
		}

		$item_name			=	strtolower($item.'-bold' );
		if		(array_key_exists($item_name, $prop ) ) {
			if		($prop[$item_name] ) {
				$after		=	'font-weight: bold;';
			} else {
				$after		=	'font-weight: normal;';
			}
			$file_text		=	str_replace('/*'.strtoupper($item_name ).'*/',		$after,		$file_text );
		}

		$item_name			=	strtolower($item.'-italic' );
		if		(array_key_exists($item_name, $prop ) ) {
			if		($prop[$item_name] ) {
				$after		=	'font-style: italic;';
			} else {
				$after		=	'font-style: normal;';
			}
			$file_text		=	str_replace('/*'.strtoupper($item_name ).'*/',		$after,		$file_text );
		}

		$item_name			=	strtolower($item.'-underline' );
		if		(array_key_exists($item_name, $prop ) ) {
			if		($prop[$item_name] ) {
				$after		=	'text-decoration: underline;';
			} else {
				$after		=	'text-decoration: none;';
			}
			$file_text		=	str_replace('/*'.strtoupper($item_name ).'*/',		$after,		$file_text );
		}

		$item_name			=	strtolower($item.'-hover' );
		if		(array_key_exists($item_name, $prop ) ) {
			if		($prop[$item_name] ) {
				$after		=	'text-decoration: underline;';
			} else {
				$after		=	'text-decoration: none;';
			}
			$file_text		=	str_replace('/*'.strtoupper($item_name ).'*/',		$after,		$file_text );
		}
	}
	// テキスト設定・ここまで -----------------------------------------------------------

	// リンクタイプごとの設定・ここから --------------------------------------------------
	foreach		(array('ex', 'in' )	as	$t ) {
		// 大文字
		$T		=	strtoupper($t );
		
		// 「カード」の位置と回転
		if		($prop[$t.'-transform-enabled'] ) {
			$x					=	intval($prop[$t.'-transform-x'] ).'px';						// 水平方向
			$y					=	intval($prop[$t.'-transform-y'] ).'px';						// 垂直方向
			$rotate				=	intval($prop[$t.'-transform-rotate'] ).'deg';				// 回転
			$scale				=	intval($prop[$t.'-transform-scale'] ) / 100;				// 拡大縮小
			$transform			=	'transform: translate('.$x.','.$y.') rotate('.$rotate.') scale('.$scale.')';
			$file_text			=	str_replace('/*'.$T.'-TRANSFORM*/',				$transform.';',	$file_text );
		}

		// 「カード」の背景
		if		($prop[$t.'-bg-enabled'] ) {
			// 背景色
			$key				=	$t.'-bg-color';
			$value				=	$prop[$key];
			$pname				=	strtoupper($key );
			if		($value ) {
				$file_text		=	str_replace('/*'.$pname.'*/',	'background-color: '.$value.';',	$file_text );
			}
			
			// 背景画像
			$key				=	$t.'-bg-image';
			$value				=	$prop[$key];
			$pname				=	strtoupper($key );
			if		($value ) {
				if		(preg_match('/https?(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/',	$value ) ) {
					$file_text	=	str_replace('/*'.$pname.'*/',	'background-image: url("'.esc_url_raw($value ).'");',	$file_text );
				} else {
					$value		=	str_replace('/', '&frasl;', $value );
					$value		=	str_replace('*', '&lowast;', $value );
					$file_text	=	str_replace('/*'.$pname.'*/',	'background: '.esc_html($value ).';',						$file_text );
				}
			}
		}

		// 「カード」の枠線
		if		($prop[$t.'-border-enabled'] ) {
			// 枠線
			$value_style	=	$prop[$t.'-border-style'];
			$value_color	=	$prop[$t.'-border-color'];
			$value_width	=	intval($prop[$t.'-border-width'] );
			$border			=	'border: '.$value_color.' '.$value_style.' '.$value_width.'px;';
			$file_text		=	str_replace('/*'.strtoupper($t ).'-BORDER*/',			$border, $file_text );
			// 角丸め
			$value_radius	=	intval($prop[$t.'-border-radius'] );
			if		($value_radius ) {
				$file_text = str_replace('/*'.$T.'-BORDER-RADIUS*/',			'border-radius: '.$value_radius.'px;', $file_text );
			}
		}

		// 「カード」の影
		$shadow					=	'';
		if		($prop[$t.'-shadow-enabled'] ) {
			// 影のパターン（設定用）
			$color				=	$prop[$t.'-shadow-color'];									// 影の色
			$x					=	intval($prop[$t.'-shadow-x'] ).'px';						// 影の距離（水平）
			$y					=	intval($prop[$t.'-shadow-y'] ).'px';						// 影の距離（垂直）
			$blur				=	intval($prop[$t.'-shadow-blur'] ).'px';						// 影のぼかし
			$spread				=	intval($prop[$t.'-shadow-spread'] ).'px';					// 影の広がり
			$inset				=	($prop[$t.'-shadow-inset'] ) ? ' inset' : '' ;				// 影の内側指定
			$shadow				=	'box-shadow: '.$color.' '.$x.' '.$y.' '.$blur.' '.$spread.$inset.';';
			$file_text			=	str_replace('/*'.$T.'-SHADOW*/',				$shadow.';',	$file_text );
		}




		// 「ホバー時」の位置と回転
		if		($prop[$t.'-hover-transform-enabled'] ) {
			$x					=	intval($prop[$t.'-hover-transform-x'] ).'px';						// 水平方向
			$y					=	intval($prop[$t.'-hover-transform-y'] ).'px';						// 垂直方向
			$rotate				=	intval($prop[$t.'-hover-transform-rotate'] ).'deg';				// 回転
			$scale				=	intval($prop[$t.'-hover-transform-scale'] ) / 100;				// 拡大縮小
			$transform			=	'transform: translate('.$x.','.$y.') rotate('.$rotate.') scale('.$scale.');';
			$file_text			=	str_replace('/*'.$T.'-HOVER-TRANSFORM*/',				$transform.';',	$file_text );
		}

		// 「ホバー時」の背景
		if		($prop[$t.'-hover-bg-enabled'] ) {
			// 背景色
			$key				=	$t.'-hover-bg-color';
			$value				=	$prop[$key];
			$pname				=	strtoupper($key );
			if		($value ) {
				$file_text		=	str_replace('/*'.$pname.'*/',	'background-color: '.$value.';',	$file_text );
			}
			
			// 背景画像
			$key				=	$t.'-hover-bg-image';
			$value				=	$prop[$key];
			$pname				=	strtoupper($key );
			if		($value ) {
				if		(preg_match('/https?(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/',	$value ) ) {
					$file_text	=	str_replace('/*'.$pname.'*/',	'background-image: url("'.esc_url_raw($value ).'");',	$file_text );
				} else {
					$value		=	str_replace('/', '&frasl;', $value );
					$value		=	str_replace('*', '&lowast;', $value );
					$file_text	=	str_replace('/*'.$pname.'*/',	'background: '.esc_html($value ).';',						$file_text );
				}
			}
		}

		// 「ホバー時」の枠線
		if		($prop[$t.'-hover-border-enabled'] ) {
			// 枠線
			$value_style	=	$prop[$t.'-hover-border-style'];
			$value_color	=	$prop[$t.'-hover-border-color'];
			$value_width	=	intval($prop[$t.'-hover-border-width'] );
			$border			=	'border: '.$value_color.' '.$value_style.' '.$value_width.'px;';
			$file_text		=	str_replace('/*'.strtoupper($t ).'-HOVER-BORDER*/',			$border, $file_text );
			// 角丸め
			$value_radius	=	intval($prop[$t.'-hover-border-radius'] );
			if		($value_radius ) {
				$file_text = str_replace('/*'.$T.'-HOVER-BORDER-RADIUS*/',			'border-radius: '.$value_radius.'px;', $file_text );
			}
		}

		// 「ホバー時」の影
		$shadow					=	'';
		if		($prop[$t.'-hover-shadow-enabled'] ) {
			// 影のパターン（設定用）
			$color				=	$prop[$t.'-hover-shadow-color'];									// 影の色
			$x					=	intval($prop[$t.'-hover-shadow-x'] ).'px';						// 影の距離（水平）
			$y					=	intval($prop[$t.'-hover-shadow-y'] ).'px';						// 影の距離（垂直）
			$blur				=	intval($prop[$t.'-hover-shadow-blur'] ).'px';						// 影のぼかし
			$spread				=	intval($prop[$t.'-hover-shadow-spread'] ).'px';					// 影の広がり
			$inset				=	($prop[$t.'-hover-shadow-inset'] ) ? ' inset' : '' ;				// 影の内側指定
			$shadow				=	'box-shadow: '.$color.' '.$x.' '.$y.' '.$blur.' '.$spread.$inset.';';
			$file_text			=	str_replace('/*'.$T.'-HOVER-SHADOW*/',				$shadow.';',	$file_text );
		}

		// 「サムネイル」の位置と回転
		if		($prop[$t.'-thumbnail-transform-enabled'] ) {
			$x					=	intval($prop[$t.'-thumbnail-transform-x'] ).'px';			// 水平方向
			$y					=	intval($prop[$t.'-thumbnail-transform-y'] ).'px';			// 垂直方向
			$rotate				=	intval($prop[$t.'-thumbnail-transform-rotate'] ).'deg';		// 回転
			$scale				=	intval($prop[$t.'-thumbnail-transform-scale'] ) / 100;		// 拡大縮小
			$transform			=	'transform: translate('.$x.','.$y.') rotate('.$rotate.') scale('.$scale.');';
			$file_text			=	str_replace('/*'.$T.'-THUMBNAIL-TRANSFORM*/',	$transform.';',	$file_text );
		}

		// 「サムネイル」の枠線
		if		($prop[$t.'-thumbnail-border-enabled'] ) {
			// 枠線
			$value_style	=	$prop[$t.'-thumbnail-border-style'];
			$value_color	=	$prop[$t.'-thumbnail-border-color'];
			$value_width	=	intval($prop[$t.'-thumbnail-border-width'] );
			$border			=	'border: '.$value_color.' '.$value_style.' '.$value_width.'px;';
			$file_text		=	str_replace('/*'.strtoupper($t ).'-THUMBNAIL-BORDER*/',	$border, $file_text );
			// 角丸め
			$value_radius	=	intval($prop[$t.'-thumbnail-border-radius'] );
			if		($value_radius ) {
				$file_text = str_replace('/*'.$T.'-THUMBNAIL-BORDER-RADIUS*/',	'border-radius: '.$value_radius.'px;', $file_text );
			}
		}

		// サムネイルの影
		$shadow					=	'';
		if		($prop[$t.'-thumbnail-shadow-enabled'] ) {
			// 影のパターン（設定用）
			$color				=	$prop[$t.'-thumbnail-shadow-color'];						// 影の色
			$x					=	intval($prop[$t.'-thumbnail-shadow-x'] ).'px';				// 影の距離（水平）
			$y					=	intval($prop[$t.'-thumbnail-shadow-y'] ).'px';				// 影の距離（垂直）
			$blur				=	intval($prop[$t.'-thumbnail-shadow-blur'] ).'px';			// 影のぼかし
			$spread				=	intval($prop[$t.'-thumbnail-shadow-spread'] ).'px';			// 影の広がり
			$inset				=	($prop[$t.'-thumbnail-shadow-inset'] ) ? ' inset' : '' ;	// 影の内側指定
			$shadow				=	'box-shadow: '.$color.' '.$x.' '.$y.' '.$blur.' '.$spread.$inset.';';
			$file_text			=	str_replace('/*'.$T.'-THUMBNAIL-SHADOW*/',		$shadow.';',	$file_text );
		}

		// サムネイルの位置とサイズ
		$thumbnail_height	=	intval(preg_replace('/[^0-9]/', '',		$prop['thumbnail-height'] ) );
		$thumbnail_width	=	intval(preg_replace('/[^0-9]/', '',		$prop['thumbnail-width']  ) );

		switch ($prop['thumbnail-position'] ) {
		case 'r':			// 右側にサムネイルtest
			$file_text	=	str_replace('/*CONTENT-ORDER*/',					'', $file_text );
			$file_text	=	str_replace('/*'.$T.'-THUMBNAIL-ORDER*/',			'', $file_text );
			$file_text	=	str_replace('/*'.$T.'-THUMBNAIL-MARGIN*/',			'margin: 0 0 0 '.$prop['content-margin'].';', $file_text );
			$file_text	=	str_replace('/*'.$T.'-THUMBNAIL-HEIGHT*/',			'height: '.($thumbnail_height ).'px;', $file_text );
			$file_text	=	str_replace('/*'.$T.'-THUMBNAIL-WIDTH*/',			'width: ' .($thumbnail_width  ).'px;', $file_text );
			$file_text	=	str_replace('/*'.$T.'-THUMBNAIL-ADJUST*/',			'', $file_text );
			break;
		case 'l':			// 左側にサムネイル
			$file_text	=	str_replace('/*CONTENT-ORDER*/',					'order: 2;', $file_text );
			$file_text	=	str_replace('/*'.$T.'-THUMBNAIL-ORDER*/',			'', $file_text );
			$file_text	=	str_replace('/*'.$T.'-THUMBNAIL-MARGIN*/',			'margin: 0 '.$prop['content-margin'].' 0 0;', $file_text );
			$file_text	=	str_replace('/*'.$T.'-THUMBNAIL-HEIGHT*/',			'height: '.($thumbnail_height ).'px;', $file_text );
			$file_text	=	str_replace('/*'.$T.'-THUMBNAIL-WIDTH*/',			'width: ' .($thumbnail_width  ).'px;', $file_text );
			$file_text	=	str_replace('/*'.$T.'-THUMBNAIL-ADJUST*/',			'', $file_text );
			break;
		case 'u':			// 上側にサムネイル
			$file_text	=	str_replace('/*COLUMN-FLEX*/',						'flex-direction: column-reverse /*IMPORTANT*/;', $file_text );
			$file_text	=	str_replace('/*CONTENT-ORDER*/',					'', $file_text );
			$file_text	=	str_replace('/*'.$T.'-THUMBNAIL-ORDER*/',			'', $file_text );
			$file_text	=	str_replace('/*'.$T.'-THUMBNAIL-MARGIN*/',			'margin: 0 auto '.$prop['content-margin'].' auto;', $file_text );
			$file_text	=	str_replace('/*'.$T.'-THUMBNAIL-POSITION*/',		'display: block;', $file_text );
			$file_text	=	str_replace('/*'.$T.'-THUMBNAIL-WIDTH*/',			'width: calc(100% - 2px) !important;', $file_text );
			$file_text	=	str_replace('/*'.$T.'-THUMBNAIL-HEIGHT*/',			'height: '.$thumbnail_height.'px !important; overflow: hidden;', $file_text );
			$file_text	=	str_replace('/*'.$T.'-THUMBNAIL-IMG-HEIGHT*/',		'height: 100% !important;', $file_text );
			$file_text	=	str_replace('/*'.$T.'-THUMBNAIL-IMG-WIDTH*/',		'width: 100% !important;', $file_text );
			$file_text	=	str_replace('/*'.$T.'-THUMBNAIL-ADJUST*/',			'', $file_text );
			break;
		default:			// 表示しない
			$file_text	=	str_replace('/*'.$T.'-THUMBNAIL-ORDER*/',			'display: none;', $file_text );
			$file_text	=	str_replace('/*'.$T.'-THUMBNAIL-ORDER*/',			'', $file_text );
		}

		// 「ヘッダー」の位置
		$margin_top = $prop['heading-margin-top'];
		$margin_left = $prop['heading-margin-left'];
		$file_text		=	str_replace('/*'.$T.'-HEADING-MARGIN*/',				'position: absolute; top: '.$margin_top.'; left: '.$margin_left.'; ',		$file_text );

		// 「ヘッダー」の内側の余白
		$padding_v		=	intval($prop['heading-padding-v'] );			// 内側の余白（垂直）
		$padding_h		=	intval($prop['heading-padding-h'] );			// 内側の余白（水平）
		$file_text		=	str_replace('/*'.$T.'-HEADING-PADDING*/',		'padding: '.$padding_v.'px '.$padding_h.'px /*IMPORTANT*/;',		$file_text );

		// 「ヘッダー」の位置と回転
		if		($prop[$t.'-heading-transform-enabled'] ) {
			$x					=	intval($prop[$t.'-heading-transform-x'] ).'px';				// 水平方向
			$y					=	intval($prop[$t.'-heading-transform-y'] ).'px';				// 垂直方向
			$rotate				=	intval($prop[$t.'-heading-transform-rotate'] ).'deg';		// 回転
			$scale				=	intval($prop[$t.'-heading-transform-scale'] ) / 100;		// 拡大縮小
			$transform			=	'transform: translate('.$x.','.$y.') rotate('.$rotate.') scale('.$scale.');';
			$file_text			=	str_replace('/*'.$T.'-HEADING-TRANSFORM*/',		$transform.';',	$file_text );
		}

		// 「ヘッダー」の背景
		if		($prop[$t.'-heading-bg-enabled'] ) {
			// 背景色
			$key				=	$t.'-heading-bg-color';
			$value				=	$prop[$key];
			$pname				=	strtoupper($key );
			if		($value ) {
				$file_text		=	str_replace('/*'.$pname.'*/',	'background-color: '.$value.';',	$file_text );
			}
			
			// 背景画像
			$key				=	$t.'-heading-bg-image';
			$value				=	$prop[$key];
			$pname				=	strtoupper($key );
			if		($value ) {
				if		(preg_match('/https?(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/',	$value ) ) {
					$file_text	=	str_replace('/*'.$pname.'*/',	'background-image: url("'.esc_url_raw($value ).'");',	$file_text );
				} else {
					$value		=	str_replace('/', '&frasl;', $value );
					$value		=	str_replace('*', '&lowast;', $value );
					$file_text	=	str_replace('/*'.$pname.'*/',	'background: '.esc_html($value ).';',						$file_text );
				}
			}
		}

		// 「ヘッダー」の枠線
		if		($prop[$t.'-heading-border-enabled'] ) {
			// 枠線
			$value_style	=	$prop[$t.'-heading-border-style'];
			$value_color	=	$prop[$t.'-heading-border-color'];
			$value_width	=	intval($prop[$t.'-heading-border-width'] );
			$border			=	'border: '.$value_color.' '.$value_style.' '.$value_width.'px;';
			$file_text		=	str_replace('/*'.strtoupper($t ).'-HEADING-BORDER*/',	$border, $file_text );
			// 角丸め
			$value_radius	=	intval($prop[$t.'-heading-border-radius'] );
			if		($value_radius ) {
				$file_text = str_replace('/*'.$T.'-HEADING-BORDER-RADIUS*/',	'border-radius: '.$value_radius.'px;', $file_text );
			}
		}

		// 「ヘッダー」の影
		$shadow					=	'';
		if		($prop[$t.'-heading-shadow-enabled'] ) {
			// 影のパターン（設定用）
			$color				=	$prop[$t.'-heading-shadow-color'];							// 影の色
			$x					=	intval($prop[$t.'-heading-shadow-x'] ).'px';				// 影の距離（水平）
			$y					=	intval($prop[$t.'-heading-shadow-y'] ).'px';				// 影の距離（垂直）
			$blur				=	intval($prop[$t.'-heading-shadow-blur'] ).'px';				// 影のぼかし
			$spread				=	intval($prop[$t.'-heading-shadow-spread'] ).'px';			// 影の広がり
			$inset				=	($prop[$t.'-heading-shadow-inset'] ) ? ' inset' : '' ;		// 影の内側指定
			$shadow				=	'box-shadow: '.$color.' '.$x.' '.$y.' '.$blur.' '.$spread.$inset.';';
			$file_text			=	str_replace('/*'.$T.'-HEADING-SHADOW*/',		$shadow.';',	$file_text );
		}


		// 「続きボタン」の位置と回転
		if		($prop[$t.'-more-transform-enabled'] ) {
			$x					=	intval($prop[$t.'-more-transform-x'] ).'px';				// 水平方向
			$y					=	intval($prop[$t.'-more-transform-y'] ).'px';				// 垂直方向
			$rotate				=	intval($prop[$t.'-more-transform-rotate'] ).'deg';			// 回転
			$scale				=	intval($prop[$t.'-more-transform-scale'] ) / 100;			// 拡大縮小
			$transform			=	'transform: translate('.$x.','.$y.') rotate('.$rotate.') scale('.$scale.');';
			$file_text			=	str_replace('/*'.$T.'-MORE-TRANSFORM*/',		$transform.';',	$file_text );
		}

		// 「続きボタン」の背景
		if		($prop[$t.'-more-bg-enabled'] ) {
			// 背景色
			$key				=	$t.'-more-bg-color';
			$value				=	$prop[$key];
			$pname				=	strtoupper($key );
			if		($value ) {
				$file_text		=	str_replace('/*'.$pname.'*/',	'background-color: '.$value.';',	$file_text );
			}
			
			// 背景画像
			$key				=	$t.'-more-bg-image';
			$value				=	$prop[$key];
			$pname				=	strtoupper($key );
			if		($value ) {
				if		(preg_match('/https?(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/',	$value ) ) {
					$file_text	=	str_replace('/*'.$pname.'*/',	'background-image: url("'.esc_url_raw($value ).'");',	$file_text );
				} else {
					$value		=	str_replace('/', '&frasl;', $value );
					$value		=	str_replace('*', '&lowast;', $value );
					$file_text	=	str_replace('/*'.$pname.'*/',	'background: '.esc_html($value ).';',						$file_text );
				}
			}
		}

		// 「続きボタン」の枠線
		if		($prop[$t.'-more-border-enabled'] ) {
			// 枠線
			$value_style	=	$prop[$t.'-more-border-style'];
			$value_color	=	$prop[$t.'-more-border-color'];
			$value_width	=	intval($prop[$t.'-more-border-width'] );
			$border			=	'border: '.$value_color.' '.$value_style.' '.$value_width.'px;';
			$file_text		=	str_replace('/*'.$T.'-MORE-BORDER*/',	$border, $file_text );
			// 角丸め
			$value_radius	=	intval($prop[$t.'-more-border-radius'] );
			if		($value_radius ) {
				$file_text = str_replace('/*'.$T.'-MORE-BORDER-RADIUS*/',	'border-radius: '.$value_radius.'px;', $file_text );
			}
		}

		// 続きボタンの影
		$shadow					=	'';
		if		($prop[$t.'-more-shadow-enabled'] ) {
			// 影のパターン（設定用）
			$color				=	$prop[$t.'-more-shadow-color'];								// 影の色
			$x					=	intval($prop[$t.'-more-shadow-x'] ).'px';					// 影の距離（水平）
			$y					=	intval($prop[$t.'-more-shadow-y'] ).'px';					// 影の距離（垂直）
			$blur				=	intval($prop[$t.'-more-shadow-blur'] ).'px';				// 影のぼかし
			$spread				=	intval($prop[$t.'-more-shadow-spread'] ).'px';				// 影の広がり
			$inset				=	($prop[$t.'-more-shadow-inset'] ) ? ' inset' : '' ;			// 影の内側指定
			$shadow				=	'box-shadow: '.$color.' '.$x.' '.$y.' '.$blur.' '.$spread.$inset.' /*IMPORTANT*/;';
			$file_text			=	str_replace('/*'.$T.'-MORE-SHADOW*/',			$shadow.';',	$file_text );
		}


		// ボタンの位置
		$more_padding				=	6;
		switch	($prop[$t.'-more-position'] ) {
			case	'o_l':
				$file_text			=	str_replace('/*'.$T.'-MORE-POSITION*/',		'position: absolute !important;',	$file_text );
				$file_text			=	str_replace('/*'.$T.'-MORE-LOCATION*/',		'bottom: '.$more_padding.'px; left: '.$more_padding.'; padding: 0 8px;',	$file_text );
				break;
			case	'o_r':
				$file_text			=	str_replace('/*'.$T.'-MORE-POSITION*/',		'position: absolute !important;',	$file_text );
				$file_text			=	str_replace('/*'.$T.'-MORE-LOCATION*/',		'bottom: '.$more_padding.'px; right: '.$more_padding.'px; padding: 0 8px;',	$file_text );
				break;
			case	'u':
				$file_text			=	str_replace('/*'.$T.'-MORE-POSITION*/',		'position: static;',	$file_text );
				break;
		}

	}
	// リンクタイプごとの設定・ここまで --------------------------------------------------

	// 共通・ここから ------------------------------------------------------------------
	// テキストの選択禁止
	if		($prop['flg-unti-select'] ) {
		$file_text		=	str_replace('/*SELECTION*/',	'user-select: none;',		$file_text );
	}

	// カードの周りへの余白
	$file_text		=	str_replace('/*MARGIN-TOP*/',		'margin-top: '.		$prop['margin-top'].	';',	$file_text );
	$file_text		=	str_replace('/*MARGIN-BOTTOM*/',	'margin-bottom: '.	$prop['margin-bottom'].	';',	$file_text );
	$file_text		=	str_replace('/*MARGIN-LEFT*/',		'margin-left: '.	$prop['margin-left'].	';',	$file_text );
	$file_text		=	str_replace('/*MARGIN-RIGHT*/',		'margin-right: '.	$prop['margin-right'].	';',	$file_text );

	// カードの余白等調整
	$file_text		=	str_replace('/*PADDING-TOP*/',		'padding-top: '.	$prop['padding-top'].	';',	$file_text );
	$file_text		=	str_replace('/*PADDING-BOTTOM*/',	'padding-bottom: '.	$prop['padding-bottom'].	';',	$file_text );
	$file_text		=	str_replace('/*PADDING-LEFT*/',		'padding-left: '.	$prop['padding-left'].	';',	$file_text );
	$file_text		=	str_replace('/*PADDING-RIGHT*/',	'padding-right: '.	$prop['padding-right'].	';',	$file_text );

	// リンク切れの場合の枠線
	if		($prop['unlink-border-color'] ) {
		$file_text	=	str_replace('/*UNLINK-BORDER-COLOR*/',		'border-color: '.$prop['unlink-border-color'].';',	$file_text );
	}

	// 横幅
	if		($prop['width']	==	null ) {
		$prop['width']		=	100;
		$prop['width-unit']	=	'%';
	}
	$width_value	=	intval($prop['width'] );
	$width_unit		=	$prop['width-unit'];
	if		($width_unit	==	'%' ) {
		$file_text	=	str_replace('/*WIDTH*/',					'width: '.$width_value.$width_unit.';',			$file_text );
	} else {
		$file_text	=	str_replace('/*WIDTH*/',					'max-width: '.$width_value.$width_unit.';',		$file_text );
	}

	// 記事情報の高さ（サムネイルの位置が左右のとき、サムネイルの方が記事情報よりも高い場合にはサムネイルの高さに合わせる）
	$content_height			=	intval($prop['content-height'] );
	if		($content_height ) {
		$thumbnail_height	=	intval($prop['thumbnail-height'] );
		if		(($this->options['thumbnail-position'] == 'r' || $this->options['thumbnail-position'] == 'l' ) && ($content_height < $thumbnail_height ) ) {
			$content_height	=	$thumbnail_height;
		}
		$file_text	=	str_replace('/*CONTENT-HEIGHT*/',			'height: '.$content_height.'px;',				$file_text );
	}

	// 抜粋文の部分を凹ませる
	if		($prop['content-inset'] ) {
		$file_text	=	str_replace('/*CONTENT-PADDING*/',			'padding: 8px;', $file_text );
		$file_text	=	str_replace('/*CONTENT-INSET*/',			'box-shadow:  inset 4px 4px 4px rgba(0,0,0,0.5), inset -4px -4px 4px rgba(255,255,255,0.5);', $file_text );
	}

	// 記事情報のマージン（上下）
	switch 	($prop['info-position'] ) {
	case	'd':				// サイト情報が上（記事内容の上に余白を設定）
		$file_text		=	str_replace('/*INFO-DISPLAY*/',			'display: flex /*IMPORTANT*/;', $file_text );
		$file_text		=	str_replace('/*INFO-ORDER*/',			'order: 2;', $file_text );
		$file_text		=	str_replace('/*INFO-MARGIN*/',			'margin: '.$prop['content-margin'].' 0 0 0;', $file_text );
		if	($prop['separator'] ) {
			$file_text	=	str_replace('/*SEPARATOR*/',			'padding-top: 4px; border-top: 1px solid '.$prop['info-color'].';', $file_text );
		}
		break;
	case	'u':				// サイト情報が下（記事内容の下に余白を設定）
		$file_text		=	str_replace('/*INFO-DISPLAY*/',			'display: flex /*IMPORTANT*/;', $file_text );
		$file_text		=	str_replace('/*INFO-MARGIN*/',			'margin: 0 0 '.$prop['content-margin'].' 0;', $file_text );
		if	($prop['separator'] ) {
			$file_text	=	str_replace('/*SEPARATOR*/',			'padding-bottom: 4px; border-bottom: 1px solid '.$prop['info-color'].';', $file_text );
		}
		break;
	default:
		$file_text		=	str_replace('/*INFO-DISPLAY*/',			'display: none;', $file_text );
		$file_text		=	str_replace('/*CONTENT-MARGIN*/',			'margin: 0;', $file_text );
		break;
	}

	// 抜粋文のマージン
	$file_text	=	str_replace('/*EXCERPT-MARGIN*/',				'margin: 0;', $file_text );

	// サイトアイコン
	$file_text	=	str_replace('/*SITEICON-HEIGHT*/',				'height: '.$prop['siteicon-size'].';', $file_text );
	$file_text	=	str_replace('/*SITEICON-WIDTH*/',				'width: '. $prop['siteicon-size'].';', $file_text );

	// --------------------------------------------------------------------------------
	// 画面サイズによるリサイズ・ここから ------------------------------------------------
	if		(isset($prop['thumbnail-resize'] ) && $prop['thumbnail-resize'] ) {
		$size_title			=	intval(preg_replace('/[^0-9]/', '', $prop['title-size'] ) );
		$size_excerpt		=	intval(preg_replace('/[^0-9]/', '', $prop['excerpt-size'] ) );
		$height_title		=	intval(preg_replace('/[^0-9]/', '', $prop['title-height'] ) );
		$height_excerpt		=	intval(preg_replace('/[^0-9]/', '', $prop['excerpt-height'] ) );
		$thumbnail_height	=	intval(preg_replace('/[^0-9]/', '', $prop['thumbnail-height'] ) );
		$thumbnail_width	=	intval(preg_replace('/[^0-9]/', '', $prop['thumbnail-width'] ) );
		$file_text	=	str_replace('/*RESIZE*/','
@media screen and ( max-width: 600px )  {
	.lkc3-title {
		font-size: '  .intval($size_title * 0.9).'px;
		line-height: '.intval($height_title * 0.9).'px;
	}
	.lkc3-excerpt {
		font-size: '.intval($size_excerpt * 0.95).'px;
	}
	.lkc3-thumbnail {
		height: '.intval($thumbnail_height * 0.9).'px !important;
		width: ' .intval($thumbnail_width  * 0.9).'px !important;
	}
	img.lkc3-thumbnail-img {
		height: '.intval($thumbnail_height * 0.9).'px !important;
		width: ' .intval($thumbnail_width  * 0.9).'px !important;
	}
}
@media screen and ( max-width: 480px )  {
	.lkc3-title {
		font-size: '  .intval($size_title * 0.8).'px;
		line-height: '.intval($height_title * 0.8).'px;
	}
	.lkc3-excerpt {
		font-size: '.intval($size_excerpt * 0.8 ).'px;
	}
	.lkc3-thumbnail {
		height: '.intval($thumbnail_height * 0.7).'px !important;
		width: ' .intval($thumbnail_width  * 0.7).'px !important;
	}
	img.lkc3-thumbnail-img {
		height: '.intval($thumbnail_height * 0.7).'px !important;
		width: ' .intval($thumbnail_width  * 0.7).'px !important;
	}
}
@media screen and ( max-width: 320px )  {
	.lkc3-title {
		font-size: '  .intval($size_title * 0.7).'px;
		line-height: '.intval($height_title * 0.7).'px;
	}
	.lkc3-excerpt {
		font-size: '.intval($size_excerpt * 0.6 ).'px;
	}
	.lkc3-thumbnail {
		height: '.intval($thumbnail_height * 0.5).'px !important;
		width: ' .intval($thumbnail_width  * 0.5).'px !important;
	}
	img.lkc3-thumbnail-img {
		height: '.intval($thumbnail_height * 0.5).'px !important;
		width: ' .intval($thumbnail_width  * 0.5).'px !important;
	}
}'
		, $file_text );
	}
	
	// !important を強制的に付加
	if		(isset($prop['flg-important'] ) && $prop['flg-important'] ) {
		$file_text	=	str_replace('/*IMPORTANT*/',			'!important', $file_text );
	}

	// デバグ用（全ての要素にボーダーを設定する）
	if		($prop['debug-style-card'] ) {
		$file_text		=	str_replace('/*DEBUG1*/',			'border: 1px solid #00f !important;', $file_text );
		$file_text		=	str_replace('/*DEBUG2*/',			'border: 1px solid #f00 !important;', $file_text );
	}

	return	$file_text;

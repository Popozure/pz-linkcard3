<?php
	
if (!defined('ABSPATH' ) ) {
	 exit;
}
	// エラー
	$flg_error	=	false;
	$test_item	=	$this->options;

	$sanitize_numeric = function ( $value, $empty_value = '' ) {
		$value = mb_convert_kana((string) $value, 'n' );
		$value = preg_replace('/[^0-9]/', '', $value );
		return ( '' === $value ) ? $empty_value : $value;
	};

	$sanitize_signed_numeric = function ( $value, $empty_value = '' ) {
		$value = mb_convert_kana((string) $value, 'n' );
		$value = str_replace(array('−', '－'), '-', $value );
		if ( 1 !== preg_match('/-?\d+/', $value, $matches ) ) {
			return $empty_value;
		}
		return $matches[0];
	};

	$sanitize_flag = function ( $value ) {
		return ( 1 === $value || '1' === (string) $value ) ? 1 : '';
	};

	$sanitize_pixel = function ( $value ) {
		$value = mb_convert_kana((string) $value, 'n' );
		$value = str_replace(array('−', '－'), '-', $value );
		if ( 1 !== preg_match('/-?\d+/', $value, $matches ) ) {
			return '';
		}
		return $matches[0] . 'px';
	};

	$sanitize_datetime = function ( $value ) use ( $sanitize_numeric ) {
		$value = $sanitize_numeric($value, null );

		if ( null === $value ) {
			return null;
		}

		$value = (int) $value;
		if ( $value <= 0 || $value > 2147483647 ) {
			return null;
		}

		return $value;
	};

	$sanitize_color = function ( $value ) {
		$value = trim((string) $value );

		if ( '' === $value ) {
			return '';
		}

		if ( 1 === preg_match('/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/', $value ) ) {
			return strtolower($value );
		}

		$value = preg_replace('/[^0-9a-fA-F]/', '', $value );
		if ( 1 === preg_match('/^(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/', $value ) ) {
			return '#' . strtolower($value );
		}

		return '';
	};

	$sanitize_url_option = function($key, $normalize_parameters = false) use (&$test_item) {
		$temp_value	=	isset($this->options[$key] )	?	$this->options[$key]	:	'' ;
		if	($temp_value ) {
			$temp_value	=	$this->pz_EncodeURL($temp_value );
			if	($normalize_parameters ) {
				$temp_value	=	str_ireplace(
					array('%title%', '%excerpt%', '%sitename%', '%domain_url%', '%domain%', '%url%', '%$this->plugin_name%', '%plugin_version%', '%curl_version%', '%php_version%', ),
					array('%TITLE%', '%EXCERPT%', '%SITE_NAME%', '%DOMAIN_URL%', '%DOMAIN%', '%URL%', '%$this->plugin_name%', '%$this->plugin_version%', '%CURL_VERSION%', '%PHP_VERSION%', ),
					$temp_value
				);
			}
			$temp_value	=	wp_http_validate_url($temp_value );
		}
		$this->options[$key]	=	$temp_value;
		unset($test_item[$key] );
	};

	// API URL
	$check_item	=	array('siteicon-api', 'thumbnail-api' );
	foreach($check_item as $key ) {
		$sanitize_url_option($key, true );
	}

	// DEFAULTS の type によるチェック
	foreach ( self::DEFAULTS as $key => $definition ) {
		if ( ! array_key_exists($key, $this->options ) ) {
			continue;
		}
		if ( ! array_key_exists($key, $test_item ) ) {
			continue;
		}

		$type	=	$definition['type']	??	'string';
		$value	=	$this->options[$key];

		switch ( $type ) {
			case 'numeric':
				if ( 1 === preg_match('/-transform-(?:x|y|rotate)$/', $key ) ) {
					$this->options[$key]	=	$sanitize_signed_numeric($value, 0 );
				} else {
					$this->options[$key]	=	$sanitize_numeric($value, 0 );
				}
				unset($test_item[$key] );
				break;

			case 'numric_null':
			case 'numeric_null':
				$this->options[$key]	=	$sanitize_numeric($value, null );
				unset($test_item[$key] );
				break;

			case 'flag':
				$this->options[$key]	=	$sanitize_flag($value );
				unset($test_item[$key] );
				break;

			case 'pixel':
				$this->options[$key]	=	$sanitize_pixel($value );
				unset($test_item[$key] );
				break;

			case 'datetime':
				$this->options[$key]	=	$sanitize_datetime($value );
				unset($test_item[$key] );
				break;

			case 'color':
				$this->options[$key]	=	$sanitize_color($value );
				unset($test_item[$key] );
				break;

			default:
				break;
		}
	}

	// 空欄だったらデフォルトにする項目
	$check_item	=	array('date-format', );
	foreach($check_item as $key ) {
		if	(!$this->options[$key] ) {
			$this->options[$key]	=	self::DEFAULTS[$key]['value'];
		}
		unset($test_item[$key] );
	}

	// エラー状態のチェック（DEFAULTS の型チェック対象外だった場合のみ）
	if	(array_key_exists('error-time', $test_item ) ) {
		$temp	=	$this->options['error-time'];
		if	(!is_numeric($temp ) ) {
			$temp	=	@strtotime($temp );
		}
		if	($temp	<	946728000 ) {
			$temp	=	'';
		}
		$this->options['error-time']	=	$temp;
		unset($test_item['error-time'] );
	}

	// 未処理の項目があるかチェック
	// if	(count($test_item )	>	0 ) {
	// 	$flg_error				=	true;
	// 	echo '<pre>';
	// 	foreach($test_item		as	$key => $value ) {
	// 		echo '未チェック項目=' . $key . '<br>';
	// 	}
	// 	echo '</pre>';
	// }

	return	$flg_error;

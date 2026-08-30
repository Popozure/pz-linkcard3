<?php
/**
 * Card Search Query Parser
 *
 * Card Search Query 仕様書 v1.1 準拠
 *
 * 使い方:
 *
 *   $parser = new Card_Search_Query_Parser( $wpdb );
 *   $result = $parser->parse( 'wordpress plugin title:"link card" -obsolete click:10..20' );
 *
 *   // $result['where']  : プレースホルダー付き WHERE 条件（WHERE 自体は含まない）
 *   // $result['params'] : $wpdb->prepare() に渡す値
 *
 */

defined( 'ABSPATH' ) || exit;

final class pz_Card_Search_Query_Parser {

	/**
	 * @var wpdb
	 */
	private $wpdb;

	/**
	 * 検索キー定義。
	 *
	 * カラム名と型は必ずこのホワイトリストから取得する。
	 *
	 * @var array<string,array{column:string|string[],type:string}>
	 */
	private $keys = array(
		'id'            => array( 'column' => 'card_id',		'type' => 'number' ),
		'title'         => array( 'column' => 'title',			'type' => 'text' ),
		'excerpt'       => array( 'column' => 'excerpt',		'type' => 'text' ),
		'charset'       => array( 'column' => 'charset',		'type' => 'text' ),
		'regist'        => array( 'column' => 'regist_time',	'type' => 'date' ),
		'update'        => array( 'column' => 'update_time',	'type' => 'date' ),
		'result'        => array( 'column' => 'update_result',	'type' => 'number' ),
		'regist_result' => array( 'column' => 'regist_result',	'type' => 'number' ),
		'update_result' => array( 'column' => 'update_result',	'type' => 'number' ),
		'alive_result'  => array( 'column' => 'alive_result',	'type' => 'number' ),
		'url'           => array( 'column' => 'url',			'type' => 'text' ),
		'domain'        => array( 'column' => 'domain',			'type' => 'text' ),
		'site'          => array( 'column' => 'domain',			'type' => 'text' ),
		'sitename'      => array( 'column' => 'sitename',		'type' => 'text' ),
		'click'         => array( 'column' => 'click_count',	'type' => 'number' ),
		'post'			=> array( 'column' => array( 'use_post_id1', 'use_post_id2', 'use_post_id3', 'use_post_id4', 'use_post_id5', 'use_post_id6' ), 'type' => 'number' ),
	);

	/**
	 * キーなし検索の対象カラム。
	 *
	 * @var string[]
	 */
	private $default_columns = array(
		'title',
		'excerpt',
		'sitename',
		'domain',
		'url',
	);

	/**
	 * @param wpdb $wpdb WordPress database object.
	 */
	public function __construct( $wpdb ) {
		$this->wpdb = $wpdb;
	}

	/**
	 * 検索文字列を解析する。
	 *
	 * キーなしの肯定検索語は、各語のデフォルト検索グループを OR で結合する。
	 * キー付き条件と否定条件は AND で結合する。
	 *
	 * @param string $query 検索文字列。
	 * @return array{
	 *     where:string,
	 *     params:array<int,int|string>,
	 *     tokens:array<int,string>
	 * }
	 */
	public function parse( $query ) {
		$tokens = $this->tokenize( (string) $query );

		$default_positive_sql    = array();
		$default_positive_params = array();
		$number_or_groups        = array();
		$and_sql                 = array();
		$and_params              = array();

		foreach ( $tokens as $token ) {
			$parsed = $this->parse_token( $token );

			if ( null === $parsed ) {
				continue;
			}

			// キーなし肯定語だけは、語同士を OR でまとめる。
			if ( 'default' === $parsed['kind'] && false === $parsed['negative'] ) {
				$default_positive_sql[] = $parsed['sql'];
				array_push( $default_positive_params, ...$parsed['params'] );
				continue;
			}

			// キー付き条件と、すべての否定条件は AND。
			if (
				'keyed' === $parsed['kind']
				&& false === $parsed['negative']
				&& isset( $parsed['type'], $parsed['key'] )
				&& 'number' === $parsed['type']
			) {
				if ( ! isset( $number_or_groups[ $parsed['key'] ] ) ) {
					$number_or_groups[ $parsed['key'] ] = array(
						'sql'    => array(),
						'params' => array(),
					);
				}

				$number_or_groups[ $parsed['key'] ]['sql'][] = $parsed['sql'];
				array_push( $number_or_groups[ $parsed['key'] ]['params'], ...$parsed['params'] );
				continue;
			}

			$and_sql[] = $parsed['sql'];
			array_push( $and_params, ...$parsed['params'] );
		}

		$where_parts = array();
		$params      = array();

		if ( ! empty( $default_positive_sql ) ) {
			$where_parts[] = '( ' . implode( ' OR ', $default_positive_sql ) . ' )';
			array_push( $params, ...$default_positive_params );
		}

		foreach ( $number_or_groups as $group ) {
			if ( 1 === count( $group['sql'] ) ) {
				$where_parts[] = $group['sql'][0];
			} else {
				$where_parts[] = '( ' . implode( ' OR ', $group['sql'] ) . ' )';
			}

			array_push( $params, ...$group['params'] );
		}

		if ( ! empty( $and_sql ) ) {
			array_push( $where_parts, ...$and_sql );
			array_push( $params, ...$and_params );
		}

		return array(
			'where'  => implode( ' AND ', $where_parts ),
			'params' => $params,
			'tokens' => $tokens,
		);
	}

	public function matches( $row, $query ) {
		$tokens = $this->tokenize( (string) $query );
		$default_positive = array();
		$number_or_groups = array();
		$and_matches      = array();

		foreach ( $tokens as $token ) {
			$parsed = $this->parse_match_token( $token, $row );
			if ( null === $parsed ) {
				continue;
			}

			if ( 'default' === $parsed['kind'] && false === $parsed['negative'] ) {
				$default_positive[] = $parsed['match'];
				continue;
			}

			if (
				'keyed' === $parsed['kind']
				&& false === $parsed['negative']
				&& isset( $parsed['type'], $parsed['key'] )
				&& 'number' === $parsed['type']
			) {
				if ( ! isset( $number_or_groups[ $parsed['key'] ] ) ) {
					$number_or_groups[ $parsed['key'] ] = array();
				}
				$number_or_groups[ $parsed['key'] ][] = $parsed['match'];
				continue;
			}

			$and_matches[] = $parsed['match'];
		}

		if ( ! empty( $default_positive ) && ! in_array( true, $default_positive, true ) ) {
			return false;
		}

		foreach ( $number_or_groups as $group ) {
			if ( ! in_array( true, $group, true ) ) {
				return false;
			}
		}

		foreach ( $and_matches as $match ) {
			if ( ! $match ) {
				return false;
			}
		}

		return true;
	}

	private function parse_match_token( $token, $row ) {
		$negative = false;

		if ( isset( $token[0] ) && '-' === $token[0] ) {
			$negative = true;
			$token    = substr( $token, 1 );
		}

		if ( '' === $token ) {
			return null;
		}

		if ( strlen( $token ) >= 3 && '(' === $token[0] && ')' === substr( $token, -1 ) ) {
			$matches = false;
			foreach ( $this->tokenize( substr( $token, 1, -1 ) ) as $part ) {
				$parsed = $this->parse_match_token( $part, $row );
				if ( null !== $parsed && $parsed['match'] ) {
					$matches = true;
					break;
				}
			}

			return array(
				'kind'     => 'keyed',
				'negative' => $negative,
				'match'    => $negative ? ! $matches : $matches,
			);
		}

		$column_compare = $this->match_column_compare_condition( $token, $negative, $row );
		if ( null !== $column_compare ) {
			return $column_compare;
		}

		$separator = $this->find_key_separator( $token );
		if ( null === $separator ) {
			if ( $this->looks_like_invalid_key_expression( $token ) ) {
				return null;
			}

			return array(
				'kind'     => 'default',
				'negative' => $negative,
				'match'    => $this->match_multi_column_condition( $this->default_columns, 'text', $token, $negative, $row, ':' ),
			);
		}

		$key      = strtolower( substr( $token, 0, $separator['position'] ) );
		$value    = substr( $token, $separator['position'] + 1 );
		$operator = $separator['operator'];

		if ( '' === $key || '' === $value || ! isset( $this->keys[ $key ] ) ) {
			return null;
		}

		$definition = $this->keys[ $key ];
		$columns    = is_array( $definition['column'] ) ? $definition['column'] : array( $definition['column'] );

		return array(
			'kind'     => 'keyed',
			'negative' => $negative,
			'match'    => $this->match_multi_column_condition( $columns, $definition['type'], $value, $negative, $row, $operator ),
			'key'      => $key,
			'type'     => $definition['type'],
		);
	}

	private function match_multi_column_condition( $columns, $type, $value, $negative, $row, $operator = ':' ) {
		$matches = false;
		foreach ( $columns as $column ) {
			if ( $this->match_single_column_condition( $column, $type, $value, $row, $operator ) ) {
				$matches = true;
				break;
			}
		}

		return $negative ? ! $matches : $matches;
	}

	private function match_single_column_condition( $column, $type, $value, $row, $operator = ':' ) {
		$row_value = $this->get_row_value( $row, $column );

		switch ( $type ) {
			case 'text':
				if ( '=' === $operator ) {
					return (string) $row_value === (string) $value;
				}
				return false !== mb_stripos( (string) $row_value, (string) $value );

			case 'date':
				return $this->match_date_value( $row_value, $value );

			case 'number':
				return $this->match_number_value( $row_value, $value );
		}

		return false;
	}

	private function match_column_compare_condition( $token, $negative, $row ) {
		if ( ! preg_match( '/^([a-z_][a-z0-9_]*)(<>|!=|<=|>=|=|<|>)([a-z_][a-z0-9_]*)$/i', $token, $matches ) ) {
			return null;
		}

		$left     = strtolower( $matches[1] );
		$operator = '!=' === $matches[2] ? '<>' : $matches[2];
		$right    = strtolower( $matches[3] );

		if ( ! isset( $this->keys[ $left ], $this->keys[ $right ] ) ) {
			return null;
		}

		$left_column  = $this->keys[ $left ]['column'];
		$right_column = $this->keys[ $right ]['column'];
		if ( is_array( $left_column ) || is_array( $right_column ) ) {
			return null;
		}

		$left_value  = $this->get_row_value( $row, $left_column );
		$right_value = $this->get_row_value( $row, $right_column );
		$matches     = $this->compare_values( $left_value, $right_value, $operator );

		return array(
			'kind'     => 'keyed',
			'negative' => $negative,
			'match'    => $negative ? ! $matches : $matches,
			'key'      => $left,
			'type'     => 'column_compare',
		);
	}

	private function match_date_value( $row_value, $value ) {
		$row_time = $this->normalize_time_value( $row_value );
		if ( null === $row_time ) {
			return false;
		}

		if ( false !== strpos( $value, '..' ) ) {
			if ( 1 !== substr_count( $value, '..' ) ) {
				return false;
			}

			list( $from_text, $to_text ) = explode( '..', $value, 2 );
			$from = $this->parse_date( $from_text );
			$to   = $this->parse_date( $to_text );
			if ( null === $from || null === $to ) {
				return false;
			}

			return $row_time >= strtotime( $from['start'] ) && $row_time <= strtotime( $to['end'] );
		}

		if ( ! preg_match( '/^(<=|>=|=|<|>)?(.*)$/s', $value, $matches ) ) {
			return false;
		}

		$operator = isset( $matches[1] ) && '' !== $matches[1] ? $matches[1] : '=';
		$date     = $this->parse_date( $matches[2] );
		if ( null === $date ) {
			return false;
		}

		switch ( $operator ) {
			case '=':
				return $row_time >= strtotime( $date['start'] ) && $row_time <= strtotime( $date['end'] );
			case '>':
				return $row_time > strtotime( $date['end'] );
			case '>=':
				return $row_time >= strtotime( $date['start'] );
			case '<':
				return $row_time < strtotime( $date['start'] );
			case '<=':
				return $row_time <= strtotime( $date['end'] );
		}

		return false;
	}

	private function match_number_value( $row_value, $value ) {
		if ( ! is_numeric( $row_value ) ) {
			return false;
		}

		$number = (int) $row_value;
		if ( false !== strpos( $value, '..' ) ) {
			if ( 1 !== substr_count( $value, '..' ) ) {
				return false;
			}

			list( $from_text, $to_text ) = explode( '..', $value, 2 );
			if ( ! $this->is_unsigned_integer( $from_text ) || ! $this->is_unsigned_integer( $to_text ) ) {
				return false;
			}

			return $number >= (int) $from_text && $number <= (int) $to_text;
		}

		if ( ! preg_match( '/^(<=|>=|=|<|>)?([0-9]+)$/', $value, $matches ) ) {
			return false;
		}

		$operator = isset( $matches[1] ) && '' !== $matches[1] ? $matches[1] : '=';
		return $this->compare_values( $number, (int) $matches[2], $operator );
	}

	private function compare_values( $left, $right, $operator ) {
		if ( is_numeric( $left ) && is_numeric( $right ) ) {
			$left  = (int) $left;
			$right = (int) $right;
		}

		switch ( $operator ) {
			case '<>':
				return $left != $right;
			case '<=':
				return $left <= $right;
			case '>=':
				return $left >= $right;
			case '=':
				return $left == $right;
			case '<':
				return $left < $right;
			case '>':
				return $left > $right;
		}

		return false;
	}

	private function get_row_value( $row, $column ) {
		if ( is_array( $row ) ) {
			return $row[ $column ] ?? '';
		}

		if ( is_object( $row ) ) {
			return $row->$column ?? '';
		}

		return '';
	}

	private function normalize_time_value( $value ) {
		if ( is_numeric( $value ) ) {
			return (int) $value;
		}

		$timestamp = strtotime( (string) $value );
		return $timestamp ? $timestamp : null;
	}

	/**
	 * 空白区切りでトークン化する。
	 *
	 * ダブルクォーテーション内の空白は保持し、クォーテーション自体は除去する。
	 * バックスラッシュで \" と \\ をエスケープできる。
	 * 丸括弧内の空白はORグループ用に保持する。
	 *
	 * @param string $query 検索文字列。
	 * @return string[]
	 */
	private function tokenize( $query ) {
		$tokens      = array();
		$buffer      = '';
		$in_quote    = false;
		$escaped     = false;
		$group_depth = 0;
		$length      = strlen( $query );

		for ( $i = 0; $i < $length; $i++ ) {
			$char = $query[ $i ];

			if ( $escaped ) {
				$buffer .= $char;
				$escaped = false;
				continue;
			}

			if ( $in_quote && '\\' === $char ) {
				$escaped = true;
				continue;
			}

			if ( '"' === $char ) {
				$in_quote = ! $in_quote;
				continue;
			}

			if ( ! $in_quote && '(' === $char ) {
				$group_depth++;
				$buffer .= $char;
				continue;
			}

			if ( ! $in_quote && ')' === $char && $group_depth > 0 ) {
				$group_depth--;
				$buffer .= $char;
				continue;
			}

			if ( ! $in_quote && 0 === $group_depth && preg_match( '/\s/u', $char ) ) {
				if ( '' !== $buffer ) {
					$tokens[] = $buffer;
					$buffer   = '';
				}
				continue;
			}

			$buffer .= $char;
		}

		if ( $escaped ) {
			$buffer .= '\\';
		}

		if ( '' !== $buffer ) {
			$tokens[] = $buffer;
		}

		return $tokens;
	}

	/**
	 * 1トークンをSQL条件へ変換する。
	 *
	 * @param string $token トークン。
	 * @return array{kind:string,negative:bool,sql:string,params:array<int,int|string>}|null
	 */
	private function parse_token( $token ) {
		$negative = false;

		if ( isset( $token[0] ) && '-' === $token[0] ) {
			$negative = true;
			$token    = substr( $token, 1 );
		}

		if ( '' === $token ) {
			return null;
		}

		$or_group = $this->build_or_group_condition( $token, $negative );
		if ( null !== $or_group ) {
			return $or_group;
		}

		$column_compare = $this->build_column_compare_condition( $token, $negative );
		if ( null !== $column_compare ) {
			return $column_compare;
		}

		$separator = $this->find_key_separator( $token );

		if ( null === $separator ) {
			/*
			 * 既知キーに比較演算子を直結した誤形式は無視する。
			 * 例: title<abc, click>=10
			 */
			if ( $this->looks_like_invalid_key_expression( $token ) ) {
				return null;
			}

			return $this->build_default_condition( $token, $negative );
		}

		$key      = strtolower( substr( $token, 0, $separator['position'] ) );
		$value    = substr( $token, $separator['position'] + 1 );
		$operator = $separator['operator'];

		// 未定義キー、空キー、空値は無視。
		if ( '' === $key || '' === $value || ! isset( $this->keys[ $key ] ) ) {
			return null;
		}

		$definition = $this->keys[ $key ];

		if ( is_array( $definition['column'] ) ) {
			$condition = $this->build_multi_column_condition(
				$definition['column'],
				$definition['type'],
				$value,
				$negative,
				$operator
			);
		} else {
			switch ( $definition['type'] ) {
				case 'text':
					$condition = $this->build_text_condition(
						$definition['column'],
						$value,
						$negative,
						$operator
					);
					break;

				case 'date':
					$condition = $this->build_date_condition(
						$definition['column'],
						$value,
						$negative
					);
					break;

				case 'number':
					$condition = $this->build_number_condition(
						$definition['column'],
						$value,
						$negative
					);
					break;

				default:
					return null;
			}
		}

		if ( null === $condition ) {
			return null;
		}

		$condition['key']  = $key;
		$condition['type'] = $definition['type'];

		return $condition;
	}

	/**
	 * 複数カラムを1つの検索キーとしてOR結合する。
	 *
	 * @param string[] $columns  DBカラム。
	 * @param string   $type     値の型。
	 * @param string   $value    検索値。
	 * @param bool     $negative 否定か。
	 * @param string   $operator 区切り演算子。
	 * @return array{kind:string,negative:bool,sql:string,params:array<int,int|string>}|null
	 */
	private function build_multi_column_condition( $columns, $type, $value, $negative, $operator = ':' ) {
		$sql    = array();
		$params = array();

		foreach ( $columns as $column ) {
			switch ( $type ) {
				case 'text':
					$condition = $this->build_text_condition( $column, $value, false, $operator );
					break;

				case 'date':
					$condition = $this->build_date_condition( $column, $value, false );
					break;

				case 'number':
					$condition = $this->build_number_condition( $column, $value, false );
					break;

				default:
					return null;
			}

			if ( null === $condition ) {
				return null;
			}

			$sql[] = $condition['sql'];
			array_push( $params, ...$condition['params'] );
		}

		if ( empty( $sql ) ) {
			return null;
		}

		$condition_sql = '( ' . implode( ' OR ', $sql ) . ' )';

		if ( $negative ) {
			$condition_sql = 'NOT ' . $condition_sql;
		}

		return array(
			'kind'     => 'keyed',
			'negative' => $negative,
			'sql'      => $condition_sql,
			'params'   => $params,
		);
	}

	/**
	 * 丸括弧内の条件をORで結合する。
	 *
	 * @param string $token    トークン。
	 * @param bool   $negative 否定か。
	 * @return array{kind:string,negative:bool,sql:string,params:array<int,int|string>}|null
	 */
	private function build_or_group_condition( $token, $negative ) {
		if ( strlen( $token ) < 3 || '(' !== $token[0] || ')' !== substr( $token, -1 ) ) {
			return null;
		}

		$inner = substr( $token, 1, -1 );
		$parts = $this->tokenize( $inner );

		if ( empty( $parts ) ) {
			return null;
		}

		$sql    = array();
		$params = array();

		foreach ( $parts as $part ) {
			$parsed = $this->parse_token( $part );

			if ( null === $parsed ) {
				continue;
			}

			$sql[] = $parsed['sql'];
			array_push( $params, ...$parsed['params'] );
		}

		if ( empty( $sql ) ) {
			return null;
		}

		$condition = '( ' . implode( ' OR ', $sql ) . ' )';

		if ( $negative ) {
			$condition = 'NOT ' . $condition;
		}

		return array(
			'kind'     => 'keyed',
			'negative' => $negative,
			'sql'      => $condition,
			'params'   => $params,
		);
	}

	/**
	 * 既知キーにコロン以外の演算子を付けた誤形式か判定する。
	 *
	 * @param string $token トークン。
	 * @return bool
	 */
	private function looks_like_invalid_key_expression( $token ) {
		foreach ( $this->keys as $key => $definition ) {
			if ( preg_match( '/^' . preg_quote( $key, '/' ) . '\s*(?:<=|>=|=|<|>)/i', $token ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * キーなし検索条件を生成する。
	 *
	 * @param string $value    検索値。
	 * @param bool   $negative 否定か。
	 * @return array{kind:string,negative:bool,sql:string,params:array<int,string>}|null
	 */
	private function find_key_separator( $token ) {
		$colon_position  = strpos( $token, ':' );
		$equals_position = strpos( $token, '=' );

		if ( false === $colon_position && false === $equals_position ) {
			return null;
		}

		if ( false === $equals_position || ( false !== $colon_position && $colon_position < $equals_position ) ) {
			return array(
				'position' => $colon_position,
				'operator' => ':',
			);
		}

		return array(
			'position' => $equals_position,
			'operator' => '=',
		);
	}

	private function build_column_compare_condition( $token, $negative ) {
		if ( ! preg_match( '/^([a-z_][a-z0-9_]*)(<>|!=|<=|>=|=|<|>)([a-z_][a-z0-9_]*)$/i', $token, $matches ) ) {
			return null;
		}

		$left     = strtolower( $matches[1] );
		$operator = $matches[2];
		$right    = strtolower( $matches[3] );

		if ( ! isset( $this->keys[ $left ], $this->keys[ $right ] ) ) {
			return null;
		}

		if ( '!=' === $operator ) {
			$operator = '<>';
		}

		$sql = $this->keys[ $left ]['column'] . ' ' . $operator . ' ' . $this->keys[ $right ]['column'];

		if ( $negative ) {
			$sql = 'NOT ( ' . $sql . ' )';
		}

		return array(
			'kind'     => 'keyed',
			'negative' => $negative,
			'sql'      => $sql,
			'params'   => array(),
			'key'      => $left,
			'type'     => 'column_compare',
		);
	}

	private function build_default_condition( $value, $negative ) {
		if ( '' === $value ) {
			return null;
		}

		$condition = $this->build_multi_column_condition(
			$this->default_columns,
			'text',
			$value,
			$negative
		);

		if ( null === $condition ) {
			return null;
		}

		return array(
			'kind'     => 'default',
			'negative' => $negative,
			'sql'      => $condition['sql'],
			'params'   => $condition['params'],
		);
	}

	/**
	 * text型の条件を生成する。
	 *
	 * text型は常に部分一致。
	 *
	 * @param string $column   DBカラム。
	 * @param string $value    検索値。
	 * @param bool   $negative 否定か。
	 * @return array{kind:string,negative:bool,sql:string,params:array<int,string>}|null
	 */
	private function build_text_condition( $column, $value, $negative, $operator = ':' ) {
		if ( '' === $value ) {
			return null;
		}

		if ( '=' === $operator ) {
			$sql    = $column . ( $negative ? ' <> %s' : ' = %s' );
			$params = array( $value );
		} else {
			$sql    = $column . ( $negative ? ' NOT LIKE %s' : ' LIKE %s' );
			$params = array( $this->make_like_value( $value ) );
		}

		return array(
			'kind'     => 'keyed',
			'negative' => $negative,
			'sql'      => $sql,
			'params'   => $params,
		);
	}

	/**
	 * date型の条件を生成する。
	 *
	 * @param string $column   DBカラム。
	 * @param string $value    検索値。
	 * @param bool   $negative 否定か。
	 * @return array{kind:string,negative:bool,sql:string,params:array<int,string>}|null
	 */
	private function build_date_condition( $column, $value, $negative ) {
		// 範囲指定。比較演算子との併用は認めない。
		if ( false !== strpos( $value, '..' ) ) {
			if ( 1 !== substr_count( $value, '..' ) ) {
				return null;
			}

			list( $from_text, $to_text ) = explode( '..', $value, 2 );

			$from = $this->parse_date( $from_text );
			$to   = $this->parse_date( $to_text );

			if ( null === $from || null === $to || $from['start'] > $to['end'] ) {
				return null;
			}

			$sql = $column . ' BETWEEN %s AND %s';

			if ( $negative ) {
				$sql = 'NOT ( ' . $sql . ' )';
			}

			return array(
				'kind'     => 'keyed',
				'negative' => $negative,
				'sql'      => $sql,
				'params'   => array( $from['start'], $to['end'] ),
			);
		}

		if ( ! preg_match( '/^(<=|>=|=|<|>)?(.*)$/s', $value, $matches ) ) {
			return null;
		}

		$operator = isset( $matches[1] ) && '' !== $matches[1] ? $matches[1] : '=';
		$date     = $this->parse_date( $matches[2] );

		if ( null === $date ) {
			return null;
		}

		switch ( $operator ) {
			case '=':
				$sql    = $column . ' BETWEEN %s AND %s';
				$params = array( $date['start'], $date['end'] );
				break;

			case '>':
				$sql    = $column . ' > %s';
				$params = array( $date['end'] );
				break;

			case '>=':
				$sql    = $column . ' >= %s';
				$params = array( $date['start'] );
				break;

			case '<':
				$sql    = $column . ' < %s';
				$params = array( $date['start'] );
				break;

			case '<=':
				$sql    = $column . ' <= %s';
				$params = array( $date['end'] );
				break;

			default:
				return null;
		}

		if ( $negative ) {
			$sql = 'NOT ( ' . $sql . ' )';
		}

		return array(
			'kind'     => 'keyed',
			'negative' => $negative,
			'sql'      => $sql,
			'params'   => $params,
		);
	}

	/**
	 * number型の条件を生成する。
	 *
	 * 0以上の10進整数だけを受け付ける。
	 *
	 * @param string $column   DBカラム。
	 * @param string $value    検索値。
	 * @param bool   $negative 否定か。
	 * @return array{kind:string,negative:bool,sql:string,params:array<int,int>}|null
	 */
	private function build_number_condition( $column, $value, $negative ) {
		if ( false !== strpos( $value, '..' ) ) {
			if ( 1 !== substr_count( $value, '..' ) ) {
				return null;
			}

			list( $from_text, $to_text ) = explode( '..', $value, 2 );

			if ( ! $this->is_unsigned_integer( $from_text ) || ! $this->is_unsigned_integer( $to_text ) ) {
				return null;
			}

			$from = (int) $from_text;
			$to   = (int) $to_text;

			if ( $from > $to ) {
				return null;
			}

			$sql = $column . ' BETWEEN %d AND %d';

			if ( $negative ) {
				$sql = 'NOT ( ' . $sql . ' )';
			}

			return array(
				'kind'     => 'keyed',
				'negative' => $negative,
				'sql'      => $sql,
				'params'   => array( $from, $to ),
			);
		}

		if ( ! preg_match( '/^(<=|>=|=|<|>)?([0-9]+)$/', $value, $matches ) ) {
			return null;
		}

		$operator = isset( $matches[1] ) && '' !== $matches[1] ? $matches[1] : '=';
		$number   = (int) $matches[2];

		$sql = $column . ' ' . $operator . ' %d';

		if ( $negative ) {
			$sql = 'NOT ( ' . $sql . ' )';
		}

		return array(
			'kind'     => 'keyed',
			'negative' => $negative,
			'sql'      => $sql,
			'params'   => array( $number ),
		);
	}

	/**
	 * 日付文字列を当日の開始・終了UNIX時刻に変換する。
	 *
	 * YYYY-M-D / YYYY/M/D のみ許可する。
	 *
	 * @param string $value 日付文字列。
	 * @return array{start:string,end:string}|null
	 */
	private function parse_date( $value ) {
		if ( ! preg_match( '/^([0-9]{4})([-\/])([0-9]{1,2})\2([0-9]{1,2})$/', $value, $matches ) ) {
			return null;
		}

		$year  = (int) $matches[1];
		$month = (int) $matches[3];
		$day   = (int) $matches[4];

		if ( ! checkdate( $month, $day, $year ) ) {
			return null;
		}

		return array(
			'start' => sprintf( '%04d-%02d-%02d 00:00:00', $year, $month, $day ),
			'end'   => sprintf( '%04d-%02d-%02d 23:59:59', $year, $month, $day ),
		);
	}

	/**
	 * LIKE用の部分一致値を生成する。
	 *
	 * *, ?, %, _ はすべて通常文字として扱う。
	 * $wpdb->esc_like() 後に、部分一致用の % を前後へ追加する。
	 *
	 * @param string $value 検索値。
	 * @return string
	 */
	private function make_like_value( $value ) {
		return '%' . $this->wpdb->esc_like( $value ) . '%';
	}

	/**
	 * 0以上の10進整数か判定する。
	 *
	 * @param string $value 値。
	 * @return bool
	 */
	private function is_unsigned_integer( $value ) {
		return 1 === preg_match( '/^[0-9]+$/', $value );
	}
}

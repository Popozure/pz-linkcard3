<?php if (!defined( 'ABSPATH' ) ) { header( 'HTTP/1.1 403 Forbidden' ); exit; } ?>
<?php
	
	// 検索条件を初期化
	$cond = $keyword;

	// フィルター条件を検索語へ反映
	if	($action_filter ) {
		$param_refine	=	'';
	}
	switch ($filter) {
		case 'all':
			break;
		case 'internal':
			$cond	.=	' domain='.$this->my_domain;
			break;
		case 'external':
			$cond	.=	' -domain='.$this->my_domain;
			break;
		case 'modify':
			$cond	.=	' alive_result<>update_result';
			break;
		case 'unlink':
			$cond	.=	' -update_result:100..399';
			break;
		default:
			break;
	}

	// 絞り込み用のドメイン一覧を取得
	$domain_list		=	$wpdb->get_results($wpdb->prepare('SELECT domain, count(*) AS count FROM %i GROUP BY domain ORDER BY domain ASC', $this->db_card ), ARRAY_A );

	// 選択されたドメインを検証
	$refine		=	null;
	if	($param_refine ) {
		foreach	($domain_list as $item ) {
			if	($param_refine	===	$item['domain'] ) {
				$refine			=	$item['domain'];
				break;
			}
		}
	}

	// Refine by selected domain.
	if ($refine) {
		$cond	.=	' domain='.$refine;
	}

	// 検索語をSQL条件に変換
	$parser = new pz_Card_Search_Query_Parser( $wpdb );
	$column_rec			=	$wpdb->get_results($wpdb->prepare('SELECT * FROM %i LIMIT 1', $this->db_card ), ARRAY_A );
	if	(!isset($column_rec) || !is_array($column_rec ) || !count($column_rec) || !array_key_exists($orderby, $column_rec[0] ) ) {
		$orderby		=	'card_id';																// 無効な並び替え列の場合はID順に戻す
	}

	// 並び順を正規化
	if	($order			!==	'asc' ) {
		$order			=	'desc';
	}

	// 件数取得用SQLを作成
	if	($order === 'asc' ) {
		$data_all	=	$wpdb->get_results($wpdb->prepare('SELECT * FROM %i ORDER BY %i ASC', $this->db_card, $orderby ) );
	} else {
		$data_all	=	$wpdb->get_results($wpdb->prepare('SELECT * FROM %i ORDER BY %i DESC', $this->db_card, $orderby ) );
	}

	$data_all	=	array_values(array_filter($data_all, function($data ) use ($parser, $cond ) {
		return	$parser->matches($data, $cond );
	} ) );

	$count_now = count($data_all );

	// ページング値を計算
	$page_limit		=	isset($screen_option_per_page ) ? intval($screen_option_per_page ) : 10;				// 1ページの表示件数
	$page_min		=	($count_now > 0 ? 1 : 0 );																// 最小ページ番号
	$page_max		=	ceil($count_now /	$page_limit );														// 最大ページ番号
	$page_now		=	$page_now		<	$page_min	?	$page_min		:	
						($page_now		>	$page_max	?	$page_max		:	$page_now );					// 現在ページを範囲内に補正
	$page_prev		=	$page_now		>	$page_min	?	$page_now - 1	:	null;							// 前ページ番号
	$page_next		=	$page_now		<	$page_max	?	$page_now + 1	:	null;							// 次ページ番号
	$page_top		=	$page_now		<	1			?	0				:	($page_now - 1 ) * $page_limit;	// 表示開始位置
	$data_now	=	array_slice($data_all, $page_top, $page_limit );

	// フィルター別の件数を取得
	
	$result			=	$wpdb->get_row(
		
		$wpdb->prepare(
		'SELECT COUNT( * ) AS count_all, '.
		'COUNT( CASE WHEN domain = %s THEN 1 END ) AS count_internal, '.
		'COUNT( CASE WHEN domain <> %s THEN 1 END ) AS count_external, '.
		'COUNT( CASE WHEN alive_result <> update_result THEN 1 END ) AS count_modify, '.
		'COUNT( CASE WHEN ( update_result < 100 OR update_result >= 400 ) THEN 1 END ) AS count_unlink '.
		'FROM %i',
		$this->my_domain,
		$this->my_domain,
		$this->db_card
	) );

	$count_list['all'	  ]	=	$result->count_all		?? 0;
	$count_list['internal']	=	$result->count_internal	?? 0;
	$count_list['external']	=	$result->count_external	?? 0;
	$count_list['modify'  ]	=	$result->count_modify	?? 0;
	$count_list['unlink'  ]	=	$result->count_unlink	?? 0;

	// ページングナビのHTMLを組み立て
	$temp_button	=	'&nbsp;<button type="submit" name="page_button" value="%d" class="button tablenav-pages-navspan" %s>%s</button>';
	$temp_now_1		=	'<span class="paging-input"><input type="text" name="page_trans" value="%d" id="current-page-selector" class="pz-man-sync-text current-page" size="2" aria-describedby="table-paging"><span class="total-pages">&nbsp;/&nbsp;%d</span></span>';
	$temp_now_2		=	'<span class="paging-input"><input type="text" value="%d" size="2" disabled="disabled" style="text-align: center;"><span class="total-pages">&nbsp;/&nbsp;%d</span></span>';
	$paging_1		=
		'<div class="pz-man-pages tablenav-pages"><span class="displaying-num">'.
		/* translators: %s: 表示件数 */
		sprintf(($count_now === 1 ? __('%s item', 'pz-linkcard3' ) : __('%s items', 'pz-linkcard3' ) ), number_format($count_now ) ).'</span><span class="pagination-links">'.
		sprintf($temp_button,	($page_min ),		(($page_now > $page_min ) ? '' : 'disabled="disabled"' ),	__('&laquo;',	'pz-linkcard3' ) ).		// 先頭ページ
		sprintf($temp_button,	($page_now - 1 ),	(($page_now > $page_min ) ? '' : 'disabled="disabled"' ),	__('&lsaquo;',	'pz-linkcard3' ) ).		// 前ページ
		'&nbsp;';
	$paging_2_1		=
		sprintf($temp_now_1,	$page_now,			$page_max );																							// 入力できる現在ページ表示
	$paging_2_2		=
		sprintf($temp_now_2,	$page_now,			$page_max );																							// フッター用の現在ページ表示
	$paging_3		=
		sprintf($temp_button,	($page_now + 1 ),	(($page_now < $page_max ) ? '' : 'disabled="disabled"' ),	__('&rsaquo;',	'pz-linkcard3' ) ).		// 次ページ
		sprintf($temp_button,	($page_max ),		(($page_now < $page_max ) ? '' : 'disabled="disabled"' ),	__('&raquo;',	'pz-linkcard3' ) ).		// 最終ページ
		'</span></div>';
	$paging_head	=	$paging_1.$paging_2_1.$paging_3;
	$paging_foot	=	$paging_1.$paging_2_2.$paging_3;
	$paging_allowed_html	=	array(
		'div'		=>	array('class' => true ),
		'span'		=>	array('class' => true ),
		'button'	=>	array('type' => true, 'name' => true, 'value' => true, 'class' => true, 'disabled' => true ),
		'input'		=>	array('type' => true, 'name' => true, 'value' => true, 'id' => true, 'class' => true, 'size' => true, 'aria-describedby' => true, 'disabled' => true, 'style' => true ),
	);

?>
	<div class="pz-man-screen-options">
		<button type="button" id="pz-man-screen-options-toggle" class="pz-man-screen-options-toggle" aria-expanded="false" aria-controls="pz-man-screen-options-panel" data-no-overlay="1">
			<?php echo esc_html(__('Screen Options', 'pz-linkcard3' ) ); ?><span class="dashicons dashicons-arrow-down-alt2"></span>
		</button>
		<div id="pz-man-screen-options-panel" class="pz-man-screen-options-panel" hidden>
			<fieldset>
				<legend><?php echo esc_html(__('Columns', 'pz-linkcard3' ) ); ?></legend>
				<?php
					foreach	($screen_option_columns as $column_key => $column ) {
						$checked		=	$screen_option_is_visible($column_key) ? ' checked="checked"' : '';
						$default_class	=	!empty($screen_option_defaults[$column_key] ) ? ' class="pz-man-screen-option-default"' : '';
						echo	'<label'.wp_kses_post($default_class ).'><input type="checkbox" class="pz-man-screen-column-toggle" data-pz-man-column="'.esc_attr($column_key ).'"'.wp_kses_post($checked ).'>'.esc_html($column['label'] ).'</label>';
					}
				?>
			</fieldset>
			<fieldset class="pz-man-screen-options-pagination">
				<legend><?php echo esc_html(__('Pagination', 'pz-linkcard3' ) ); ?></legend>
				<label class="pz-man-screen-option-per-page">
					<?php echo esc_html(__('Number of items per page:', 'pz-linkcard3' ) ); ?>
					<select id="pz-man-screen-option-per-page">
						<?php
							foreach	($screen_option_per_page_choices as $choice ) {
								echo	'<option value="'.intval($choice ).'"'.selected($screen_option_per_page, $choice, false ).'>'.intval($choice ).'</option>';
							}
						?>
					</select>
				</label>
			</fieldset>
		</div>
	</div>

	<button type="submit"  id="search-submit"     name="action"  value="search" class="button action" style="display: none;"></button>

	<div class="pz-man-count-list">
		<?php
			$items	=
				array(
					'all'		=>	__('All',      'pz-linkcard3' ),
					'internal'	=>	__('Internal', 'pz-linkcard3' ),
					'external'	=>	__('External', 'pz-linkcard3' ),
					'modify'	=>	__('Modify',   'pz-linkcard3' ),
					'unlink'	=>	__('Unlink',   'pz-linkcard3' ),
				);
			$sep		=	'';
			foreach	($items as $i_code => $i_name ) {
				echo	esc_html($sep );
				echo	'<button type="submit" name="action-filter" value="'.esc_attr($i_code ).'" class="pz-man-filter-item"><span class="pz-man-filter-label'.($filter === $i_code ? ' pz-man-current' : '').'">'.esc_html($i_name ).'</span><span class="pz-man-filter-count">('.esc_html(number_format_i18n(intval($count_list[$i_code] ?? 0 ) ) ).')</span></button>';
				$sep	=	' | ';
			}
		?>
	</div>

	<div class="pz-man-search">
		<p class="search-box" title="<?php esc_attr_e('Text search by title and excerpt', 'pz-linkcard3' ); ?>">
			<label>
				<span class="dashicons dashicons-search" style="vertical-align: text-bottom;"></span>
				<input  type="search"  id="post-search-input" name="keyword" value="<?php echo esc_attr($keyword); ?>" />
				<button type="submit"  id="search-submit"     name="action"  value="search" class="button action"><?php esc_html_e('Search', 'pz-linkcard3' ); ?></button>
			</label>
		</p>
	</div>
	
	<div class="pz-man-navi tablenav top">
		<div class="pz-man-batch-list alignleft actions bulkactions">
			<select name="bulk_action" id="bulk-action-selector-top">
				<option value="" selected="selected"><?php esc_html_e('Bulk Actions', 'pz-linkcard3' ); ?></option>
				<option value="renew"><?php esc_html_e('Renew Cache', 'pz-linkcard3' ); ?></option>
				<option value="renew_thumbnail"><?php esc_html_e('Renew Thumbnail Image', 'pz-linkcard3' ); ?></option>
				<option value="renew_sns"><?php esc_html_e('Renew SNS Count', 'pz-linkcard3' ); ?></option>
				<option value="renew_postid"><?php esc_html_e('Renew Post ID', 'pz-linkcard3' ); ?></option>
				<option value="alive"><?php esc_html_e('Check Status', 'pz-linkcard3' ); ?></option>
				<option value="delete"><?php esc_html_e('Delete from Cache', 'pz-linkcard3' ); ?></option>
			</select>
			<button type="submit" name="action" value="exec-batch" class="button action" onclick="return confirm('<?php echo esc_js(esc_html__('Are you sure?', 'pz-linkcard3' ) ); ?>' );"><?php esc_html_e('Execute', 'pz-linkcard3' ); ?></button>
			&nbsp;
		</div>
		
		<div class="pz-man-domain-list alignleft actions bulkactions">
			<select name="refine" id="domain-list-selector-top">
				<option value="" selected="selected"><?php esc_html_e('All Domain', 'pz-linkcard3' ); ?></option>
				<?php
					foreach	($domain_list as $rec ) {
						if (isset($rec['domain'] ) === true && isset($rec['count'] ) === true) {
							$disp_domain	=	(function_exists('idn_to_utf8' ) && mb_substr($rec['domain'], 0, 4) === 'xn--') ? idn_to_utf8($rec['domain'], 0, INTL_IDNA_VARIANT_UTS46 ) : $rec['domain'] ;
							$selected		=	($rec['domain'] === $refine) ? ' selected="selected"' : null ;
							echo	'<option value="'.esc_attr($rec['domain'] ).'"'.wp_kses_post($selected ).'>'.esc_html($disp_domain ).' ('.esc_html(number_format_i18n($rec['count'] ) ).')</option>';
						}
					}
				?>
			</select>
			<button type="submit" name="action" value="select-domain" class="button action"><?php esc_html_e('Refine Search', 'pz-linkcard3' ); ?></button>
		</div>
		<?php /* ページングナビを表示 */ echo wp_kses($paging_head, $paging_allowed_html ); ?>
		<br class="clear">
	</div>

	<table class="pz-man-cache-list widefat striped">
		<thead>
			<tr>
				<td id="cb" class="pz-man-head-check manage-column column-cb check-column"><input id="cb-select-all-1" type="checkbox"></td>
<?php
	$sort_none	=	'<img src="'.$this->base_url.'assets/sort_none.svg" alt="" width="16" height="16" />';
	$sort_asc	=	'<img src="'.$this->base_url.'assets/sort_asc.svg" alt="" width="16" height="16" />';
	$sort_desc	=	'<img src="'.$this->base_url.'assets/sort_desc.svg" alt="" width="16" height="16" />';
	$get_sort_icon = function($item) use ($orderby, $order, $sort_none, $sort_asc, $sort_desc) {
		return	($orderby === $item ? ($order === 'desc' ? $sort_desc : $sort_asc ) : $sort_none );
	};
	$render_sort_header = function($item, $item_name, $column_key = null) use ($screen_option_hidden_class, $get_sort_icon) {
		$add_class	=	$column_key ? $screen_option_hidden_class($column_key) : '';
		$sort		=	$get_sort_icon($item);
		return	'<th scope="col" class="pz-man-head-'.esc_attr($item ).esc_attr($add_class ).'"><button type="submit" name="header" value="'.esc_attr($item ).'">'.wp_kses_post($item_name ).$sort.'</button></th>';
	};
	$render_datetime = function($dt) {
		$dt	=	$this->pz_CardDatetimeToTimestamp($dt);
		return	$dt
			? '<span title="'.gmdate($this->format_datetime, $dt ).'">'.$this->pz_Date('Y\<\b\r\>m/d\<\b\r\>H:i', $dt ).'</span>'
			: '-';
	};

	echo	wp_kses_post($render_sort_header('card_id',		__('Card<br>ID', 'pz-linkcard3' ),	'id' ) );
	echo	wp_kses_post($render_sort_header('url',			__('URL', 'pz-linkcard3' ) ) );
	echo	wp_kses_post($render_sort_header('title',		__('Title', 'pz-linkcard3' ) ) );
	echo	wp_kses_post($render_sort_header('excerpt',		__('Excerpt', 'pz-linkcard3' ),		'excerpt' ) );
	echo	wp_kses_post($render_sort_header('charset',		__('Charset', 'pz-linkcard3' ),		'charset' ) );
	echo	wp_kses_post($render_sort_header('domain',		__('Domain', 'pz-linkcard3' ),		'domain' ) );
	echo	wp_kses_post($render_sort_header('thumbnail',	__('Thumbnail<br>Image', 'pz-linkcard3' ),	'thumbnail' ) );
	echo	wp_kses_post($render_sort_header('site_icon',	__('Site<br>Icon', 'pz-linkcard3' ),		'site_icon' ) );

	$item		=	'sns_twitter';
	$item_name	=	__('Tw', 'pz-linkcard3' );
	$add_class	=	$screen_option_hidden_class('sns');
	$sort		=	$get_sort_icon($item);
	echo	'<th scope="col" class="pz-man-head-'.esc_attr($item.$add_class ).'">';
	echo	'<button type="submit" name="header" value="'.esc_attr($item ).'">'.esc_html($item_name ).wp_kses_post($sort ).'</button>';
	echo	'<br>';
	$item		=	'sns_facebook';
	$item_name	=	__('fb', 'pz-linkcard3' );
	$sort		=	$get_sort_icon($item);
	echo	'<button type="submit" name="header" value="'.esc_attr($item ).'">'.esc_html($item_name ).wp_kses_post($sort ).'</button>';
	echo	'<br>';
	$item		=	'sns_hatena';
	$item_name	=	__('B!', 'pz-linkcard3' );
	$sort		=	$get_sort_icon($item);
	echo	'<button type="submit" name="header" value="'.esc_attr($item ).'">'.esc_html($item_name ).wp_kses_post($sort ).'</button>';
	echo	'</th>';

	echo	wp_kses_post($render_sort_header('regist_time',		__('Registration<br>Date', 'pz-linkcard3' ),	'regist_time' ) );
	echo	wp_kses_post($render_sort_header('update_time',		__('Update<br>Date', 'pz-linkcard3' ),			'update_time' ) );
	echo	wp_kses_post($render_sort_header('sns_time',		__('SNS<br>Check<br>Date', 'pz-linkcard3' ),	'sns_time' ) );
	echo	wp_kses_post($render_sort_header('alive_time',		__('Alive<br>Check<br>Date', 'pz-linkcard3' ),	'alive_time' ) );
	echo	wp_kses_post($render_sort_header('use_post_id1',	__('Post<br>ID', 'pz-linkcard3' ),				'post_id' ) );
	echo	wp_kses_post($render_sort_header('click_count',		__('Click<br>Count', 'pz-linkcard3' ),			'click_count' ) );
	echo	wp_kses_post($render_sort_header('update_result',	__('Result<br>Code', 'pz-linkcard3' ),			'result' ) );
?>
			</tr> 
		</thead>
		<tbody>
			<?php
				$domain_local_ip_cache	=	array();
				$has_local_ip_domain	=	false;
				foreach	($data_now as $data ) {
					// キャッシュID
					$card_id	=	$data->card_id;

					// URL
					$url		=	$data->url;

                    // URL information.
                    $url_info       =   $this->Pz_GetURLInfo($url );
                    $scheme         =   $url_info['scheme'];
                    $domain         =   $url_info['domain'];
                    $domain_url     =   $url_info['domain_url'];
                    $is_external    =   $url_info['is_external'];
                    $is_internal    =   $url_info['is_internal'];
					$domain_check_key	=	strtolower((string) $domain );
					$is_local_ip_domain	=	false;
					if	($domain ) {
						$domain_local_key	=	$domain_check_key;
						if	(!$this->pz_IsSiteURL($url ) ) {
							if	(!array_key_exists($domain_local_key, $domain_local_ip_cache ) ) {
								$domain_local_ip_cache[$domain_local_key]	=	$this->pz_IsLocalIPLink($url );
							}
							$is_local_ip_domain	=	$domain_local_ip_cache[$domain_local_key];
							if	($is_local_ip_domain ) {
								$has_local_ip_domain	=	true;
							}
						}
					}

					// URLの状態アイコンを作成
                    // URL warning mark.
                    $html_url_error =   '';
                    if  ($data->update_result < 100 || $data->update_result >= 400 ) {
                        if  ($data->no_failure ) {
                            $temp_icon  =   __('&#x26A0;&#xFE0F;', 'pz-linkcard3' );
                            $temp_class =   'pz-man-body-url-error-ignore';
                            $temp_title =   __('The latest HTTP status code indicates an error, but it is ignored.', 'pz-linkcard3' );
                        } else {
                            $temp_icon  =   __('&#x26D4;&#xFE0F;', 'pz-linkcard3' );
                            $temp_class =   'pz-man-body-url-error';
                            $temp_title =   __('The latest HTTP status code indicates an error. You can ignore this error from the edit screen.', 'pz-linkcard3' );
                        }
                        $html_url_error = '<span class="'.esc_attr($temp_class ).'" title="'.esc_attr($temp_title ).'">'.esc_html($temp_icon ).'</span>&nbsp;';
                    } elseif  ($data->url_redir ) {
                        $temp_icon  =   __('&#x21AA;&#xFE0F;', 'pz-linkcard3' );
                        $temp_class =   'pz-man-body-url-redir';
                        $temp_title =   __('Redirect', 'pz-linkcard3' );
                        $html_url_error = '<span class="'.esc_attr($temp_class ).'" title="'.esc_attr($temp_title ).'">'.esc_html($temp_icon ).'</span>&nbsp;';
                    }
					if	($is_internal ) {
						$temp_href		=	esc_url($url );
						$temp_rel		=	'internal';
						$temp_target	=	'_self';
					} else {
						$temp_href		=	esc_url($url );
						$temp_rel		=	'external noopenner noreferrer';
						$temp_target	=	'_blank';
					}
					$disp_url			=	esc_url($this->pz_DecodeURL($url ) );
					$html_url			=	'<a href="'.$temp_href.'" title="'.$disp_url.'" rel="'.$temp_rel.'" target="'.$temp_target.'">'.$html_url_error.$disp_url.'</a>';

					// タイトルを表示用に整形
                    // Title.
                    $title          =   stripslashes($data->title ?? '' );
                    $html_title     =   esc_html(mb_strimwidth($title, 0, 200, '...' ) );
                    if  (($data->title ?? '' ) <> ($data->regist_title ?? '' ) ) {
                        $html_title =   '<b>'.$html_title.'</b>';
                    }
                    if  (empty($data->regist_time ) ) {
                        $temp_icon  =   __('&#x23F3;&#xFE0F;', 'pz-linkcard3' );
                        $title      =   __('Please update this cache from the edit screen.', 'pz-linkcard3' );
                        $html_title =   '<span class="pz-man-body-title-pending" aria-hidden="true">'.wp_kses_post($temp_icon ).'</span>&nbsp;<span class="pz-man-body-title-pending-text">'.esc_html($title ).'</span>';
                    }
					$edit_disabled		=	empty($data->regist_time ) ? ' disabled="disabled"' : '';

					// 抜粋を表示用に整形
					// Excerpt.
					$str				=	wp_strip_all_tags((string) ($data->excerpt ?? '' ) );
					if	($str !== '' ) {
						$str			=	mb_strimwidth($str, 0, 500, '...' );
					}
					$excerpt			=	esc_html($str );
					$html_excerpt		=	$excerpt;
					if	(($data->excerpt ?? '' ) <> ($data->regist_excerpt ?? '' ) ) {
						$html_excerpt	=	'<b>'.$html_excerpt.'</b>';
					}

					// SNS counters.
					$html_sns	=	pz_lkc3_sns_counter($data->sns_twitter  ).'<br>';
					$html_sns	.=	pz_lkc3_sns_counter($data->sns_facebook ).'<br>';
					$html_sns	.=	pz_lkc3_sns_counter($data->sns_hatena   ).'<br>';

					// サムネイル画像を取得
					$thumbnail_url				=	null;
					$html_thumbnail				=	null;
					if	($data->thumbnail ) {
						$thumbnail_url			=	$this->pz_GetImage($data->thumbnail );
					}
					if	(!$thumbnail_url && $domain === $this->my_domain ) {
						$post_id		=	url_to_postid($data->url );
						$thumbnail_id	=	get_post_thumbnail_id($post_id );
						if	($thumbnail_id ) {
							$thumbnail_size	=	$this->options['in-thumbnail-size'] ? $this->options['in-thumbnail-size'] : 'thumbnail';
							$attach		=	wp_get_attachment_image_src($thumbnail_id, $thumbnail_size, true );
							if	(isset($attach ) && count($attach ) > 3 && isset($attach[0] ) ) {
								$thumbnail_url	=	$attach[0];
								if	(preg_match('/.*(\/\/.*)/', $thumbnail_url, $m ) ) {
									$thumbnail_url	=	$m[1];
								}
							}
						}
					}
					if	($thumbnail_url ) {
						$html_thumbnail			=	'<a href="'.esc_url($thumbnail_url ).'" target="_blank" class="pz-man-thumbnail"><div><img src="'.esc_url($thumbnail_url ).'" alt="" class="pz-man-thumbnail-img" /></div></a>';
					}

					// Site icon
					$html_siteicon				=	null;
					$siteicon_url				=	null;
					if	(!empty($data->site_icon ) ) {
						$siteicon_url			=	$this->pz_GetImage($data->site_icon );
					}
					if	($siteicon_url ) {
						$html_siteicon			=	'<span class="pz-man-domain-siteicon"><img src="'.esc_url($siteicon_url ).'" alt="" loading="lazy" /></span>';
					}

					// 関連する投稿IDを表示用に整形
					$html_post_id		=	null;
					for	($j = 1; $j < 5; $j++ ) {
						$use_post_id	=	'use_post_id'.$j;
						$post_id		=	$data->$use_post_id;
						if	($post_id > 0 ) {
							$html_post_id	.=	'<a href="'.esc_url(get_permalink($post_id ) ).'" target="_blank" title="'.esc_attr(get_the_title($post_id ) ).'">'.intval($post_id ).'</a><br>';
						}
					}

					// クリック数を表示用に整形
					$html_click		=	pz_lkc3_sns_counter($data->click_count );

					// HTTPステータスを表示用に整形
					$html_result		=	'<span class="pz-man-body-result-update">'.pz_lkc3_str_http_code($data->update_result, $this->pz_HTTPMessage($data->update_result ) ).'</span>';
					if	($data->no_failure ) {
						$html_result	=	'<span class="pz-man-body-result-ignore">'.__('Ignore', 'pz-linkcard3' ).'</span><br>'.$html_result;
					}

					// キャッシュ一覧の行を出力
					?>
			<tr>
				<th scope="row" class="pz-man-body-check check-column"><input id="cb-select-<?php echo intval($card_id ); ?>" type="checkbox" name="select_id[]" value="<?php echo intval($card_id ); ?>" /><div class="locked-indicator"></div></th>
				<td class="pz-man-body-id<?php echo esc_attr($screen_option_hidden_class('id') ); ?>"><button type="button" data-pz-man-search-id="<?php echo intval($card_id ); ?>" class="pz-man-inline-menu pz-man-id-search"><?php echo intval($card_id ); ?></button><?php echo wp_kses_post($html_thumbnail ); ?></td>

				<td colspan="2" class="pz-man-body-url-title-cell">
					<div class="pz-man-body-url"><?php echo wp_kses_post($html_url ); ?></div>
					<div class="pz-man-body-title"><span title="<?php echo esc_attr($title ); ?>"><?php echo wp_kses($html_title, array('b' => array(), 'span' => array('class' => array(), 'aria-hidden' => array() ) ) ); ?></span></div>
					<div id="inline_<?php echo intval($card_id ); ?>" class="pz-man-body-menu row-actions">
						<button type="submit" name="single-edit"   value="<?php echo intval($card_id ); ?>" class="pz-man-inline-menu"<?php disabled(empty($data->regist_time ), true ); ?>><?php esc_html_e('Edit','pz-linkcard3' ); ?></button> | 
						<button type="submit" name="single-renew"  value="<?php echo intval($card_id ); ?>" class="pz-man-inline-menu" onclick="return confirm('<?php echo esc_js(esc_html__('Are you sure?', 'pz-linkcard3' ) ); ?>' );"><?php esc_html_e('Renew','pz-linkcard3' ); ?></button> | 
						<button type="submit" name="single-delete" value="<?php echo intval($card_id ); ?>" class="pz-man-inline-menu" onclick="return confirm('<?php echo esc_js(esc_html__('Are you sure?', 'pz-linkcard3' ) ); ?>' );"><?php esc_html_e('Delete','pz-linkcard3' ); ?></button>
					</div>
				</td>
				<td class="pz-man-body-excerpt-cell<?php echo esc_attr($screen_option_hidden_class('excerpt') ); ?>"><div class="pz-man-body-excerpt" title="<?php echo esc_attr($excerpt); ?>"><?php echo wp_kses($html_excerpt, array('b' => array() ) ); ?></div></td>

				<td class="pz-man-body-charset pz-consolas<?php echo esc_attr($screen_option_hidden_class('charset') ); ?>"><?php echo esc_html($data->charset ?? '' ); ?></td>
				<td class="pz-man-body-domain-cell pz-consolas<?php echo esc_attr($screen_option_hidden_class('domain') ); ?>">
					<div class="pz-man-body-domain">
						<?php
							$disp_domain	=	(function_exists('idn_to_utf8' ) && mb_substr($domain, 0, 4) === 'xn--') ? idn_to_utf8($domain, 0, INTL_IDNA_VARIANT_UTS46 ) : $domain ;
							$disp_sitename	=	esc_html($data->site_name ?? '' );
						?>
						<span class="pz-man-body-domain<?php echo $is_local_ip_domain ? esc_attr(' pz-man-body-domain-local-ip' ) : ''; ?>"   title="<?php echo esc_attr($disp_domain );   ?>"><?php echo wp_kses_post($html_siteicon ); ?><?php echo esc_html($disp_domain );   ?></span><br>
						<span class="pz-man-body-sitename" title="<?php echo esc_attr($disp_sitename ); ?>"><?php echo esc_html($disp_sitename ); ?></span>
					</div>
				</td>
				<td class="pz-man-body-thumbnail-url pz-monospace<?php echo esc_attr($screen_option_hidden_class('thumbnail') ); ?>"><div title="<?php echo esc_attr($data->thumbnail ?? '' ); ?>"><?php echo esc_html($data->thumbnail ?? '' ); ?></div></td>
				<td class="pz-man-body-siteicon-url pz-monospace<?php echo esc_attr($screen_option_hidden_class('site_icon') ); ?>"><div title="<?php echo esc_attr($data->site_icon ?? '' ); ?>"><?php echo esc_html($data->site_icon ?? '' ); ?></div></td>
				<td class="pz-man-body-sns pz-monospace<?php echo esc_attr($screen_option_hidden_class('sns') ); ?>"><?php echo wp_kses_post($html_sns ); ?></td>
				<td class="pz-man-body-regist-time pz-monospace<?php echo esc_attr($screen_option_hidden_class('regist_time') ); ?>"><?php echo wp_kses_post($render_datetime($data->regist_time ) ); ?></td>
				<td class="pz-man-body-update-time pz-monospace<?php echo esc_attr($screen_option_hidden_class('update_time') ); ?>"><?php echo wp_kses_post($render_datetime($data->update_time ) ); ?></td>
				<td class="pz-man-body-sns-time pz-monospace<?php echo esc_attr($screen_option_hidden_class('sns_time') ); ?>"><?php echo wp_kses_post($render_datetime($data->sns_time ) ); ?></td>
				<td class="pz-man-body-alive-time pz-monospace<?php echo esc_attr($screen_option_hidden_class('alive_time') ); ?>"><?php echo wp_kses_post($render_datetime($data->alive_time ) ); ?></td>
				<td class="pz-man-body-post-id pz-monospace<?php echo esc_attr($screen_option_hidden_class('post_id') ); ?>"><?php		echo wp_kses_post($html_post_id );	?></td>
				<td class="pz-man-body-click-count pz-monospace<?php echo esc_attr($screen_option_hidden_class('click_count') ); ?>"><?php	echo esc_html($html_click );	?></td>
				<td class="pz-man-body-result pz-consolas<?php echo esc_attr($screen_option_hidden_class('result') ); ?>"><?php		echo wp_kses_post($html_result );	?></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
	<div class="pz-man-navi tablenav">
	<?php if	($has_local_ip_domain ) : ?>
		<div class="pz-man-local-ip-note alignleft"><?php echo esc_html(__('Domains shown in red are local IP links.', 'pz-linkcard3' ) ); ?></div>
	<?php endif; ?>
	<?php /* ページングナビを表示 */ echo wp_kses($paging_foot, $paging_allowed_html ); ?>
</div>

<?php
// Helper functions.

// Render an HTTP result code with success/error styling.
function pz_lkc3_str_http_code($result, $message ) {
	if	($result == 0 ) {
		$result	=	'';
		return null;
	}
	if	($message ) {
		$message	=	' title="'.esc_attr($message ).'"';
	}
	if	($result == '' || ($result >= 100 && $result <= 399 ) ) {
		return	'<span class="pz-man-http-ok"'.$message.'>'.esc_html($result ).'</span>';
	}
	return		'<span class="pz-man-http-error"'.$message.'">'.esc_html($result ).'</span>';
}

// Render an SNS count using compact suffixes.
function pz_lkc3_sns_counter($count ) {
	if	($count	==	null) {
		return	'-';
	}
	$count		=	intval($count );
	if	($count >= 1000000000 ) {
		return	number_format(intval(round($count / 1000000 ) ) ).' M';
	}
	if	($count >= 1000000 ) {
		return	number_format(intval(round($count / 1000 ) ) ).' K';
	}
	return	number_format($count );
}

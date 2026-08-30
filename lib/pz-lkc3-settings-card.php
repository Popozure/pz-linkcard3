<?php
	
defined('ABSPATH') || exit; ?>
<?php
	$render_card_submit_float = function() {
		echo	'<div class="pz-submit-float">';
		submit_button();
		echo	'</div>';
	};
	$settings_card_allowed_html = array(
		'option' => array(
			'disabled' => true,
			'name'     => true,
			'selected' => true,
			'value'    => true,
		),
	);
	$render_card_select = function($name, $value, $items, $class = '', $disabled_values = array() ) {
		echo	'<select name="properties['.esc_attr($name ).']" class="'.esc_attr($class ).'">';
		foreach	($items as $item_value => $item_label ) {
			echo	'<option value="'.esc_attr($item_value ).'"'.selected((string) $value, (string) $item_value, false ).disabled(in_array((string) $item_value, array_map('strval', $disabled_values ), true ), true, false ).'>'.esc_html($item_label ).'</option>';
		}
		echo	'</select>';
	};
	$render_card_item_selects = function($prefix, $suffix, $count, $items) use ($prop, $render_card_select) {
		for	($i = 1; $i <= $count; $i++ ) {
			$item_name	=	$prefix.$suffix.$i;
			echo	'<div class="pz-items">'.esc_html($i).'<br>';
			$render_card_select($item_name, $prop[$item_name] ?? '', $items );
			echo	'</div>';
		}
	};
	$render_card_help_icon = function($suffix) use ($help_icon) {
		return wp_kses_post(sprintf($help_icon, esc_attr($suffix ) ) );
	};

	$title_list	=	array(
		array( 'name' => 'ex',	'type' => 'external',	'title' => esc_html__('External Link Settings',		'pz-linkcard3' )	),
		array( 'name' => 'in',	'type' => 'internal',	'title' => esc_html__('Internal Link Settings',		'pz-linkcard3' )	),
	);
	foreach ($title_list as $t) {
		$card_type			=	sanitize_html_class($t['type'] );
		$help_icon_suffix	=	'-'.$card_type.'-link';
		echo	'<div class="'.esc_attr($page_class('pz-'.$card_type ) ).'" id="'.esc_attr('pz-'.$card_type ).'">';
		$render_card_submit_float();

		echo	'<h2>'.esc_html($t['title'] ).wp_kses_post($render_card_help_icon($help_icon_suffix ) ).'</h2>';

		// ================================================================================


		// 基本設定
		echo	'<h3>'.esc_html__('Basic', 'pz-linkcard3' ).'</h3>';
		echo	'<table class="form-table pz-settings-wide-label-table">';

		// リンクを開く先
		$item_title				=	__('Open New Window/Tab', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-target';
		echo					'<tr><th scope="row">'.esc_html($item_title ).'</th><td>';
		$render_card_select($item_name, $prop[$item_name] ?? '', LIST_NEWTAB );
		echo					'</td></tr></table>';


		// ================================================================================


		// 記事コンテンツの表示設定
		$item_header			=	__('Article Contents',	'pz-linkcard3' );
		echo					'<h3>'.esc_html($item_header ).'</h3>';
		echo					'<table class="form-table">';

		// 表示項目
		$item_title				=	__('Display Items', 'pz-linkcard3' );
		echo					'<tr><th colspan="2">'.esc_html($item_title ).'</th><td>';

		$render_card_item_selects($t['name'], '-content-type-', 5, LIST_CONTENT_ITEMS );
		echo					'</td></tr>';

		// 取得方法
		$item_title				=	__('Method of acquisition', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-content-get';
		$item_notice			=	'';
		$item_class				=	'';
		$item_disabled			=	'';
		$item_value				=	esc_attr($prop[$item_name] );
		$item_value_list		=	LIST_METHOD;
		$item_option			=	'';
		foreach		($item_value_list		as	$value	=>	$description ) {
			if	(($t['name'] == 'ex' ) && ($value == '' || $value == '1' || $value == '3' ) ) {
				$is_disabled	=	true;
			} else {
				$is_disabled	=	false;
			}
			$item_option		.=	'<option value="'.esc_attr($value ).'" '.selected($item_value, $value, false ).disabled($is_disabled, true, false ).'>'.esc_html($description ).'</option>';
		}
		echo					'<tr><th colspan="2">'.esc_html($item_title ).'</th><td><select name="properties['.esc_attr($item_name ).']" class="'.esc_attr($item_class ).'">'.wp_kses($item_option, $settings_card_allowed_html ).'</select></td></tr>';

		// カスタムフィールド: タイトル
		$item_header			=	__('Custom Field', 'pz-linkcard3' );
		$item_title				=	__('Title',		'pz-linkcard3' );
		$item_name				=	$t['name'].'-content-title';
		if	($t['name']			==	'in' ) {
			$item_value			=	$prop[$item_name];
			$item_list			=	$meta_list;
			$item_notice		=	'';
			$item_class			=	'';
			$item_disabled		=	null;
			echo				'<tr><th rowspan="2">'.esc_html($item_header ).'</th><th>'.esc_html($item_title ).'</th><td>';
			echo_combo($item_name, $item_value, $item_list, $item_title, $item_notice, $item_class, 99, $item_disabled, true);
			echo				'</td></tr>';
		} else {
			echo				'<tr><th rowspan="2">'.esc_html($item_header ).'</th><th>'.esc_html($item_title ).'</th><td><input name="properties['.esc_attr($item_name ).']" type="text" value="-" readonly="readonly" disabled="disabled"></td></tr>';
		}

		// カスタムフィールド: 抜粋
		$item_title				=	__('Excerpt',	'pz-linkcard3' );
		$item_name				=	$t['name'].'-content-excerpt';
		if	($t['name']			==	'in' ) {
			$item_value			=	$prop[$item_name];
			$item_list			=	$meta_list;
			$item_notice		=	'';
			$item_class			=	'';
			$item_disabled		=	null;
			echo_combo($item_name, $item_value, $item_list, $item_title, $item_notice, $item_class, 99, $item_disabled );
		} else {
			echo				'<tr><th>'.esc_html($item_title ).'</th><td><input name="" type="text" value="-" readonly="readonly" disabled="disabled"></td></tr>';
		}

		// リダイレクト取得
		if	($t['name']			== 'ex' ) {
			$item_title			=	__('Get Redirect',		'pz-linkcard3' );
			$item_info			=	__('Track when the link destination is redirected.', 'pz-linkcard3' );
		} else {
			$item_title			=	__('Get Redirect',		'pz-linkcard3' );
			$item_info			=	__('If you are unable to read or access the article, please click the external link to view it.', 'pz-linkcard3' );
		}
		$item_name				=	$t['name'].'-content-redir';
		$item_class				=	'';
		$item_disabled			=	'';
		$item_value				=	intval($prop[$item_name] );
		echo					'<tr>';
		echo					'<th colspan="2">'.esc_html($item_title ).'</th>';
		echo					'<td><label><input type="hidden"   name="properties['.esc_attr($item_name ).']" value="">';
		echo					'<input type="checkbox" name="properties['.esc_attr($item_name ).']" value="1" '.checked($this->options[$item_name], true, false ).' />';
		echo					esc_html($item_info );
		echo					'</label></td></tr>';
		echo					'</table>';


		// ================================================================================


		// サイト情報の表示設定
		$item_header			=	__('Site Information',	'pz-linkcard3' );
		echo					'<h3>'.esc_html($item_header ).'</h3>';
		echo					'<table class="form-table">';

		// 表示項目
		$item_title				=	__('Display Items',		'pz-linkcard3' );
		echo					'<tr><th colspan="2">'.esc_html($item_title ).'</th><td>';
		if	($t['name']		<>	'sp' ) {
			$render_card_item_selects($t['name'], '-info-type-', 5, LIST_INFO_ITEMS );
		}
		echo					'</tr>';

		// サイト種別
		$item_title				=	__('Site Classification',	'pz-linkcard3' );
		echo					'<tr><th colspan="2">'.esc_html($item_title ).'</th>';

		// サイト種別の表示テキスト
		$item_name				=	$t['name'].'-info-text';
		$item_class				=	'regular-text';
		$item_list				=	array(
								__('External site',			'pz-linkcard3' ),
								__('This site',				'pz-linkcard3' ),
								__('This page',				'pz-linkcard3' ),
		);
		$item_value				=	esc_attr($prop[$item_name] );
		echo					'<td><label><input type="text" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="'.esc_attr($item_class ).'" list="datalist-'.esc_attr($item_name ).'"></label>';
		echo					'<datalist id="datalist-'.esc_attr($item_name ).'">';
		foreach					($item_list			as	$value ) {
			echo				'<option value="'.esc_attr($value ).'">'.esc_html($value ).'</option>';
		}
		echo					'</datalist>';
		echo					'</td></tr>';

		// サイトアイコンの取得方法
		$item_header			=	__('Site Icon', 'pz-linkcard3' );
		$item_title				=	__('Method of acquisition', 'pz-linkcard3' );
		echo'<tr><th rowspan="2">'.esc_html($item_header ).'</th><th>'.esc_html($item_title ).'</th>';

		$item_name				=	$t['name'].'-siteicon-get';
		$item_notice			=	'';
		$item_class				=	'';
		$item_disabled			=	'';
		$item_value				=	esc_attr($prop[$item_name] );
		$item_value_list		=
			array(
				''		=>	__('None',					'pz-linkcard3' ),
				'1'		=>	__('Direct',				'pz-linkcard3' ),
				'13'	=>	__('Direct > Use Web API',	'pz-linkcard3' ),
				'3'		=>	__('Use Web API',			'pz-linkcard3' ),
			);
		$item_option			=	'';
		foreach		($item_value_list		as	$value	=>	$description ) {
			$item_option		.=	'<option value="'.esc_attr($value ).'" '.selected($item_value, $value, false ).' name="properties['.esc_attr($item_name).']">'.esc_html($description ).'</option>';
		}
		echo					'<td><select name="properties['.esc_attr($item_name ).']" class="'.esc_attr($item_class ).'">'.wp_kses($item_option, $settings_card_allowed_html ).'</select></td></tr>';

		// サイトアイコンの代替テキスト
		$item_title				=	__('Alternative text', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-siteicon-alt';
		$item_length			=	'';
		$item_class				=	'regular-text';
		$item_notice			=	'';
		if	(array_key_exists($item_name, self::DEFAULTS ) ) {
			$item_value			=	esc_attr($prop[$item_name] );
			$item_disabled		=	'';
		} else {
			$item_name			=	'';
			$item_value			=	__('Use the same settings as Internal Link', 'pz-linkcard3' );
			$item_disabled		=	'disabled="disabled"';
		}
		echo					'<tr><th>'.esc_html($item_title ).'</th><td><input name="properties['.esc_attr($item_name ).']" type="text" value="'.esc_attr($item_value ).'" size="'.esc_attr($item_length ).'" class="'.esc_attr($item_class ).'"></td></tr>';
		echo					'</table>';


		// ================================================================================


		// 通常時のカード装飾
		echo	'<h3>'.esc_html__('Card', 'pz-linkcard3' ).'</h3>';
		echo	'<table class="form-table">';

		// --------------------------------------------------------------------------------

		// 変形設定
		$item_header			=	__('Decoration', 'pz-linkcard3' );
		$item_title				=	__('Translate', 'pz-linkcard3' );
		echo					'<tr><th rowspan="4">'.esc_html($item_header ).'</th><th>'.esc_html($item_title ).'</th><td>';

		$item_title				=	__('Enabled', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-transform-enabled';
		$item_value				=	intval($prop[$item_name] );
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<label class="pz-toggle-button pz-enabled">';
		echo					'<input type="hidden"   name="properties['.esc_attr($item_name ).']" value="">';
		echo					'<input type="checkbox" name="properties['.esc_attr($item_name ).']" value="1"'.checked((bool) $item_value, true, false ).'>';
		echo					'</label></div>';

		$item_title				=	__('Horizontal Offset', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-transform-x';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	-64;
		$item_max				=	64;
		$item_step				=	1;
		$item_unit				=	'px';
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">'.esc_html($item_unit ).'<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-range-sign pz-sync"></div>';

		$item_title				=	__('Vertical Offset', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-transform-y';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	-64;
		$item_max				=	64;
		$item_step				=	1;
		$item_unit				=	'px';
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">'.esc_html($item_unit ).'<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-range-sign pz-sync"></div>';

		$item_title				=	__('Rotate', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-transform-rotate';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	-360;
		$item_max				=	360;
		$item_step				=	1;
		$item_unit				=	'deg';
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">'.esc_html($item_unit ).'<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-range-sign pz-sync"></div>';

		$item_title				=	__('Scale', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-transform-scale';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	0;
		$item_max				=	200;
		$item_step				=	1;
		$item_unit				=	'%';
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">'.esc_html($item_unit ).'<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-range-sign pz-sync"></div>';

		// --------------------------------------------------------------------------------

		// 背景色
		$item_title				=	__('Background Color', 'pz-linkcard3' );
		echo					'<tr><th>'.esc_html($item_title ).'</th><td>';

		$item_title				=	__('Enabled', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-bg-enabled';
		$item_value				=	intval($prop[$item_name] );
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<label class="pz-toggle-button pz-enabled">';
		echo					'<input type="hidden"   name="properties['.esc_attr($item_name ).']" value="">';
		echo					'<input type="checkbox" name="properties['.esc_attr($item_name ).']" value="1"'.checked((bool) $item_value, true, false ).'>';
		echo					'</label></div>';

		$item_title				=	__('Color', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-bg-color';
		$item_value				=	$prop[$item_name];
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<label><input type="text" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-color pz-monospace pz-color-picker"></label></div>';

		// 背景画像などのショートハンド指定
		$item_title				=	__('Shorthand Property',	'pz-linkcard3' );
		$item_name				=	$t['name'].'-bg-image';
		$item_class				=	'regular-text';
		$item_value				=	esc_attr($prop[$item_name] );
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<input type="text" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" maxlength="1000" class="'.esc_attr($item_class ).'"></td>';
		echo					'</td></tr>';

		// --------------------------------------------------------------------------------
		
		// 枠線
		$item_title				=	__('Border', 'pz-linkcard3' );
		echo					'<tr><th scope="row">'.esc_html($item_title ).'</th><td>';

		$item_title				=	__('Enabled', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-border-enabled';
		$item_value				=	intval($prop[$item_name] );
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<label class="pz-toggle-button pz-enabled">';
		echo					'<input type="hidden"   name="properties['.esc_attr($item_name ).']" value="">';
		echo					'<input type="checkbox" name="properties['.esc_attr($item_name ).']" value="1"'.checked((bool) $item_value, true, false ).'>';
		echo					'</label></div>';

		$item_title				=	__('Color', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-border-color';
		$item_value				=	$prop[$item_name];
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<label><input type="text" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-color pz-monospace pz-color-picker"></label></div>';

		$item_title				=	__('Line Style', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-border-style';
		$item_value				=	$prop[$item_name];
		$item_list				=	LIST_BORDER;
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><select name="properties['.esc_attr($item_name ).']">';
		foreach					($item_list		as	$list_value	=>	$list_desc ) {
			echo				'<option value="'.esc_attr($list_value ).'"'.selected($list_value, $item_value, false ).'">'.esc_html($list_desc ).'</option>';
		}
		echo					'</select></div>';

		$item_title				=	__('Border Width', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-border-width';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	0;
		$item_max				=	64;
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">px<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="1" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-sync"></div>';

		// 角丸
		$item_title				=	__('Radius', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-border-radius';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	0;
		$item_max				=	64;
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">px<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="1" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-sync"></div>';

		echo					'</td></tr>';

		// --------------------------------------------------------------------------------

		// 影
		$item_header			=	__('Shadow', 'pz-linkcard3' );
		echo					'<tr><th>'.esc_html($item_header ).'</th><td>';

		$item_title				=	__('Enabled', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-shadow-enabled';
		$item_value				=	intval($prop[$item_name] );
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<label class="pz-toggle-button pz-enabled">';
		echo					'<input type="hidden"   name="properties['.esc_attr($item_name ).']" value="">';
		echo					'<input type="checkbox" name="properties['.esc_attr($item_name ).']" value="1"'.checked((bool) $item_value, true, false ).'>';
		echo					'</label></div>';

		$item_title				=	__('Color', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-shadow-color';
		$item_value				=	$prop[$item_name];
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<label><input type="text" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-color pz-monospace pz-color-picker"></label></div>';

		$item_title				=	__('Horizontal Offset', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-shadow-x';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	-64;
		$item_max				=	64;
		$item_step				=	1;
		$item_unit				=	'px';
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">'.esc_html($item_unit ).'<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-range-sign pz-sync"></div>';

		$item_title				=	__('Vertical Offset', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-shadow-y';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	-64;
		$item_max				=	64;
		$item_step				=	1;
		$item_unit				=	'px';
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">'.esc_html($item_unit ).'<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-range-sign pz-sync"></div>';

		$item_title				=	__('Blur', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-shadow-blur';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	0;
		$item_max				=	64;
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">px<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="1" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-sync"></div>';

		$item_title				=	__('Spread', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-shadow-spread';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	0;
		$item_max				=	64;
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">px<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="1" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-sync"></div>';

		$item_title				=	__('Inset Shadow', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-shadow-inset';
		$item_value				=	intval($prop[$item_name] );
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<label class="pz-toggle-button">';
		echo					'<input type="hidden"   name="properties['.esc_attr($item_name ).']" value="">';
		echo					'<input type="checkbox" name="properties['.esc_attr($item_name ).']" value="1"'.checked((bool) $item_value, true, false ).'>';
		echo					'</label></div>';

		echo					'</tr></table>';







		// ================================================================================


		// ホバー時のカード装飾
		echo	'<h3>'.esc_html__('Hover', 'pz-linkcard3' ).'</h3>';
		echo	'<table class="form-table">';

		// --------------------------------------------------------------------------------

		// 変形設定
		$item_header			=	__('Decoration', 'pz-linkcard3' );
		$item_title				=	__('Translate', 'pz-linkcard3' );
		echo					'<tr><th rowspan="4">'.esc_html($item_header ).'</th><th>'.esc_html($item_title ).'</th><td>';

		$item_title				=	__('Enabled', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-hover-transform-enabled';
		$item_value				=	intval($prop[$item_name] );
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<label class="pz-toggle-button pz-enabled">';
		echo					'<input type="hidden"   name="properties['.esc_attr($item_name ).']" value="">';
		echo					'<input type="checkbox" name="properties['.esc_attr($item_name ).']" value="1"'.checked((bool) $item_value, true, false ).'>';
		echo					'</label></div>';

		$item_title				=	__('Horizontal Offset', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-hover-transform-x';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	-64;
		$item_max				=	64;
		$item_step				=	1;
		$item_unit				=	'px';
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">'.esc_html($item_unit ).'<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-range-sign pz-sync"></div>';

		$item_title				=	__('Vertical Offset', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-hover-transform-y';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	-64;
		$item_max				=	64;
		$item_step				=	1;
		$item_unit				=	'px';
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">'.esc_html($item_unit ).'<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-range-sign pz-sync"></div>';

		$item_title				=	__('Rotate', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-hover-transform-rotate';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	-360;
		$item_max				=	360;
		$item_step				=	1;
		$item_unit				=	'deg';
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">'.esc_html($item_unit ).'<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-range-sign pz-sync"></div>';

		$item_title				=	__('Scale', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-hover-transform-scale';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	0;
		$item_max				=	200;
		$item_step				=	1;
		$item_unit				=	'%';
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">'.esc_html($item_unit ).'<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-range-sign pz-sync"></div>';

		// --------------------------------------------------------------------------------

		// 背景色
		$item_title				=	__('Background Color', 'pz-linkcard3' );
		echo					'<tr><th>'.esc_html($item_title ).'</th><td>';

		$item_title				=	__('Enabled', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-hover-bg-enabled';
		$item_value				=	intval($prop[$item_name] );
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<label class="pz-toggle-button pz-enabled">';
		echo					'<input type="hidden"   name="properties['.esc_attr($item_name ).']" value="">';
		echo					'<input type="checkbox" name="properties['.esc_attr($item_name ).']" value="1"'.checked((bool) $item_value, true, false ).'>';
		echo					'</label></div>';

		$item_title				=	__('Color', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-hover-bg-color';
		$item_value				=	$prop[$item_name];
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<label><input type="text" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-color pz-monospace pz-color-picker"></label></div>';

		// 背景画像などのショートハンド指定
		$item_title				=	__('Shorthand Property',	'pz-linkcard3' );
		$item_name				=	$t['name'].'-hover-bg-image';
		$item_class				=	'regular-text';
		$item_value				=	esc_attr($prop[$item_name] );
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<input type="text" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" maxlength="1000" class="'.esc_attr($item_class ).'"></td>';
		echo					'</td></tr>';

		// --------------------------------------------------------------------------------
		
		// 枠線
		$item_title				=	__('Border', 'pz-linkcard3' );
		echo					'<tr><th scope="row">'.esc_html($item_title ).'</th><td>';

		$item_title				=	__('Enabled', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-hover-border-enabled';
		$item_value				=	intval($prop[$item_name] );
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<label class="pz-toggle-button pz-enabled">';
		echo					'<input type="hidden"   name="properties['.esc_attr($item_name ).']" value="">';
		echo					'<input type="checkbox" name="properties['.esc_attr($item_name ).']" value="1"'.checked((bool) $item_value, true, false ).'>';
		echo					'</label></div>';

		$item_title				=	__('Color', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-hover-border-color';
		$item_value				=	$prop[$item_name];
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<label><input type="text" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-color pz-monospace pz-color-picker"></label></div>';

		$item_title				=	__('Line Style', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-hover-border-style';
		$item_value				=	$prop[$item_name];
		$item_list				=	LIST_BORDER;
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><select name="properties['.esc_attr($item_name ).']">';
		foreach					($item_list		as	$list_value	=>	$list_desc ) {
			echo				'<option value="'.esc_attr($list_value ).'"'.selected($list_value, $item_value, false ).'">'.esc_html($list_desc ).'</option>';
		}
		echo					'</select></div>';

		$item_title				=	__('Border Width', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-hover-border-width';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	0;
		$item_max				=	64;
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">px<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="1" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-sync"></div>';

		// 角丸
		$item_title				=	__('Radius', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-hover-border-radius';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	0;
		$item_max				=	64;
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">px<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="1" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-sync"></div>';

		echo					'</td></tr>';

		// --------------------------------------------------------------------------------

		// 影
		$item_header			=	__('Shadow', 'pz-linkcard3' );
		echo					'<tr><th>'.esc_html($item_header ).'</th><td>';

		$item_title				=	__('Enabled', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-hover-shadow-enabled';
		$item_value				=	intval($prop[$item_name] );
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<label class="pz-toggle-button pz-enabled">';
		echo					'<input type="hidden"   name="properties['.esc_attr($item_name ).']" value="">';
		echo					'<input type="checkbox" name="properties['.esc_attr($item_name ).']" value="1"'.checked((bool) $item_value, true, false ).'>';
		echo					'</label></div>';

		$item_title				=	__('Color', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-hover-shadow-color';
		$item_value				=	$prop[$item_name];
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<label><input type="text" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-color pz-monospace pz-color-picker"></label></div>';

		$item_title				=	__('Horizontal Offset', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-hover-shadow-x';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	-64;
		$item_max				=	64;
		$item_step				=	1;
		$item_unit				=	'px';
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">'.esc_html($item_unit ).'<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-range-sign pz-sync"></div>';

		$item_title				=	__('Vertical Offset', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-hover-shadow-y';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	-64;
		$item_max				=	64;
		$item_step				=	1;
		$item_unit				=	'px';
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">'.esc_html($item_unit ).'<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-range-sign pz-sync"></div>';

		$item_title				=	__('Blur', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-hover-shadow-blur';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	0;
		$item_max				=	64;
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">px<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="1" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-sync"></div>';

		$item_title				=	__('Spread', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-hover-shadow-spread';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	0;
		$item_max				=	64;
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">px<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="1" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-sync"></div>';

		$item_title				=	__('Inset Shadow', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-hover-shadow-inset';
		$item_value				=	intval($prop[$item_name] );
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<label class="pz-toggle-button">';
		echo					'<input type="hidden"   name="properties['.esc_attr($item_name ).']" value="">';
		echo					'<input type="checkbox" name="properties['.esc_attr($item_name ).']" value="1"'.checked((bool) $item_value, true, false ).'>';
		echo					'</label></div>';

		echo					'</tr></table>';
























		// ================================================================================


		// ヘッダー表示
		$item_header			=	__('Header',	'pz-linkcard3' );
		echo					'<h3>'.esc_html($item_header ).'</h3>';
		echo					'<table class="form-table">';

		// 表示テキスト
		$item_title			=	__('Text',		'pz-linkcard3' );
		echo					'<tr><th colspan="2">'.esc_html($item_title ).'</th><td>';

		// 表示テキスト候補
		$item_notice			=	__('When a string is entered, it is overlaid on the top border.', 'pz-linkcard3' );
		$item_class				=	'regular-text';
		$item_name				=	$t['name'].'-heading-text';
		$item_value				=	esc_attr($prop[$item_name] );
		$item_list				=	LIST_HEADER;
		echo					'<label><input type="text" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="'.esc_attr($item_class ).'" list="datalist-'.esc_attr($item_name ).'"></label>';
		echo					'<datalist id="datalist-'.esc_attr($item_name ).'">';
		foreach					($item_list			as	$value ) {
			echo				'<option value="'.esc_attr($value ).'">'.esc_html($value ).'</option>';
		}
		echo					'</datalist>';
		if						($item_notice ) {
			echo				'<p>'.wp_kses_post($item_notice ).'</p>';
		}
		echo					'</td></tr>';

		// --------------------------------------------------------------------------------

		// 変形設定
		$item_header			=	__('Decoration', 'pz-linkcard3' );
		$item_title				=	__('Translate', 'pz-linkcard3' );
		echo					'<tr><th rowspan="4">'.esc_html($item_header ).'</th><th>'.esc_html($item_title ).'</th><td>';

		$item_title				=	__('Enabled', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-heading-transform-enabled';
		$item_value				=	intval($prop[$item_name] );
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<label class="pz-toggle-button pz-enabled">';
		echo					'<input type="hidden"   name="properties['.esc_attr($item_name ).']" value="">';
		echo					'<input type="checkbox" name="properties['.esc_attr($item_name ).']" value="1"'.checked((bool) $item_value, true, false ).'>';
		echo					'</label></div>';

		$item_title				=	__('Horizontal Offset', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-heading-transform-x';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	-64;
		$item_max				=	64;
		$item_step				=	1;
		$item_unit				=	'px';
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">'.esc_html($item_unit ).'<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-range-sign pz-sync"></div>';

		$item_title				=	__('Vertical Offset', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-heading-transform-y';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	-64;
		$item_max				=	64;
		$item_step				=	1;
		$item_unit				=	'px';
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">'.esc_html($item_unit ).'<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-range-sign pz-sync"></div>';

		$item_title				=	__('Rotate', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-heading-transform-rotate';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	-360;
		$item_max				=	360;
		$item_step				=	1;
		$item_unit				=	'deg';
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">'.esc_html($item_unit ).'<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-range-sign pz-sync"></div>';

		$item_title				=	__('Scale', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-heading-transform-scale';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	0;
		$item_max				=	200;
		$item_step				=	1;
		$item_unit				=	'%';
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">'.esc_html($item_unit ).'<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-range-sign pz-sync"></div>';

		// --------------------------------------------------------------------------------

		// 背景色
		$item_title				=	__('Background Color', 'pz-linkcard3' );
		echo					'<tr><th scope="row">'.esc_html($item_title ).'</th><td>';

		$item_title				=	__('Enabled', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-heading-bg-enabled';
		$item_value				=	intval($prop[$item_name] );
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<label class="pz-toggle-button pz-enabled">';
		echo					'<input type="hidden"   name="properties['.esc_attr($item_name ).']" value="">';
		echo					'<input type="checkbox" name="properties['.esc_attr($item_name ).']" value="1"'.checked((bool) $item_value, true, false ).'>';
		echo					'</label></div>';

		// 背景色の色指定
		$item_name				=	$t['name'].'-heading-bg-color';
		$item_value				=	$prop[$item_name];
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<label><input type="text" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-color pz-monospace pz-color-picker"></label></div>';

		// 背景画像などのショートハンド指定
		$item_title				=	__('Shorthand Property',	'pz-linkcard3' );
		$item_name				=	$t['name'].'-heading-bg-image';
		$item_class				=	'regular-text';
		$item_value				=	esc_attr($prop[$item_name] );
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<input type="text" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" maxlength="1000" class="'.esc_attr($item_class ).'"></td>';

		echo					'</td></tr>';

		// --------------------------------------------------------------------------------

		// 枠線
		$item_title				=	__('Border', 'pz-linkcard3' );
		echo					'<tr><th scope="row">'.esc_html($item_title ).'</th><td>';

		$item_title				=	__('Enabled', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-heading-border-enabled';
		$item_value				=	intval($prop[$item_name] );
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<label class="pz-toggle-button pz-enabled">';
		echo					'<input type="hidden"   name="properties['.esc_attr($item_name ).']" value="">';
		echo					'<input type="checkbox" name="properties['.esc_attr($item_name ).']" value="1"'.checked((bool) $item_value, true, false ).'>';
		echo					'</label></div>';

		// 色指定
		$item_title				=	__('Color', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-heading-border-color';
		$item_value				=	$prop[$item_name];
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<label><input type="text" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-color pz-monospace pz-color-picker"></label></div>';

		$item_title				=	__('Line Style', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-heading-border-style';
		$item_value				=	$prop[$item_name];
		$item_list				=	LIST_BORDER;
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><select name="properties['.esc_attr($item_name ).']">';
		foreach					($item_list		as	$list_value	=>	$list_desc ) {
			echo				'<option value="'.esc_attr($list_value ).'"'.selected($list_value, $item_value, false ).'">'.esc_html($list_desc ).'</option>';
		}
		echo					'</select></div>';

		$item_title				=	__('Border Width', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-heading-border-width';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	0;
		$item_max				=	64;
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">px<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="1" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-sync"></div>';

		// 角丸
		$item_title				=	__('Radius', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-heading-border-radius';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	0;
		$item_max				=	64;
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">px<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="1" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-sync"></div>';

		echo					'</td></tr>';

		// --------------------------------------------------------------------------------

		// 影
		$item_title				=	__('Shadow', 'pz-linkcard3' );
		echo					'<tr><th scope="row">'.esc_html($item_title ).'</th><td>';

		$item_title				=	__('Enabled', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-heading-shadow-enabled';
		$item_value				=	intval($prop[$item_name] );
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<label class="pz-toggle-button pz-enabled">';
		echo					'<input type="hidden"   name="properties['.esc_attr($item_name ).']" value="">';
		echo					'<input type="checkbox" name="properties['.esc_attr($item_name ).']" value="1"'.checked((bool) $item_value, true, false ).'>';
		echo					'</label></div>';

		// 色指定
		$item_title				=	__('Color', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-heading-shadow-color';
		$item_value				=	$prop[$item_name];
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<label><input type="text" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-color pz-monospace pz-color-picker"></label></div>';

		$item_title				=	__('Horizontal Offset', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-heading-shadow-x';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	-64;
		$item_max				=	64;
		$item_step				=	1;
		$item_unit				=	'px';
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">'.esc_html($item_unit ).'<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-range-sign pz-sync"></div>';

		$item_title				=	__('Vertical Offset', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-heading-shadow-y';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	-64;
		$item_max				=	64;
		$item_step				=	1;
		$item_unit				=	'px';
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">'.esc_html($item_unit ).'<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-range-sign pz-sync"></div>';

		$item_title				=	__('Blur', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-heading-shadow-blur';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	0;
		$item_max				=	64;
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">px<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="1" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-sync"></div>';

		$item_title				=	__('Spread', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-heading-shadow-spread';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	0;
		$item_max				=	64;
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">px<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="1" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-sync"></div>';

		$item_title				=	__('Inset Shadow', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-heading-shadow-inset';
		$item_value				=	intval($prop[$item_name] );
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<label class="pz-toggle-button">';
		echo					'<input type="hidden"   name="properties['.esc_attr($item_name ).']" value="">';
		echo					'<input type="checkbox" name="properties['.esc_attr($item_name ).']" value="1"'.checked((bool) $item_value, true, false ).'>';
		echo					'</label></div>';

		echo					'</table>';


		// ================================================================================


		// サムネイル画像の表示設定
		$item_title				=	__('Thumbnail',		'pz-linkcard3' );
		echo					'<h3>'.esc_html($item_title ).'</h3>';
		echo					'<table class="form-table">';

		// サムネイル画像の取得方法
		$item_header			=	__('Thumbnail', 'pz-linkcard3');
		$item_title				=	__('Method of acquisition', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-thumbnail-get';
		$item_notice			=	'';
		$item_class				=	'';
		$item_disabled			=	'';
		$item_value				=	esc_attr($prop[$item_name] );
		$item_value_list		=
			array(
				''				=>	__('None',					'pz-linkcard3' ),
				'1'				=>	__('Direct',				'pz-linkcard3' ),
				'13'			=>	__('Direct > Use Web API',	'pz-linkcard3' ),
				'3'				=>	__('Use Web API',			'pz-linkcard3' ),
			);
		$item_option			=	'';
		foreach		($item_value_list		as	$value	=>	$description ) {
			$item_option		.=	'<option value="'.esc_attr($value ).'" '.selected($item_value, $value, false ).'>'.esc_html($description ).'</option>';
		}
		echo						'<tr><th rowspan="3">'.esc_html($item_header ).'</th><th>'.esc_html($item_title ).'</th>';
		echo						'<td><select name="properties['.esc_attr($item_name ).']" class="'.esc_attr($item_class ).'">'.wp_kses($item_option, $settings_card_allowed_html ).'</select></td></tr>';

		// サムネイル画像の解像度
		$item_title				=	__('Resolution', 'pz-linkcard3' );
		if	($t['name'] == 'in' ) {
			$item_name			=	$t['name'].'-thumbnail-size';
			$item_notice		=	'';
			$s_name				=	'name="properties['.esc_attr($item_name ).']"';
			$item_class			=	'';
			$item_disabled		=	'';
			$item_value			=	esc_attr($prop[$item_name] );
			$item_value_list	=	LIST_THUMBNAIL_SIZE;
			$item_option		=	'';
			foreach		($item_value_list		as	$value	=>	$description ) {
				$item_option		.=	'<option value="'.esc_attr($value ).'" '.selected($item_value, $value, false ).'>'.esc_html($description ).'</option>';
			}
			echo						'<tr><th>'.esc_html($item_title ).'</th><td><select name="properties['.esc_attr($item_name ).']" class="'.esc_attr($item_class ).'" '.disabled($item_disabled !== '', true, false ).' >'.wp_kses($item_option, $settings_card_allowed_html ).'</select></td>'.wp_kses_post($item_notice ).'</tr>';
		} else {
			echo '<tr><th>'.esc_html($item_title ).'</th><td>'.esc_html(LIST_IMAGE_SIZE[$this->options['image-size']] ?? '' ).'</td></tr>';
		}

		// サムネイル画像の代替テキスト
		$item_title				=	__('Alternative text', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-thumbnail-alt';
		$item_length			=	'';
		$item_notice			=	'';
		$item_value				=	esc_attr($prop[$item_name] );
		$item_disabled			=	'';
		echo						'<tr><th>'.esc_html($item_title ).'</th><td><input name="properties['.esc_attr($item_name ).']" type="text" value="'.esc_attr($item_value ).'" class="regular-text" '.disabled($item_disabled !== '', true, false ).' />'.wp_kses_post($item_notice ).'</td></tr>';

		// --------------------------------------------------------------------------------

		// 変形設定
		$item_header			=	__('Decoration', 'pz-linkcard3' );
		$item_title				=	__('Translate', 'pz-linkcard3' );
		echo					'<tr><th rowspan="4">'.esc_html($item_header ).'</th><th>'.esc_html($item_title ).'</th><td>';

		$item_title				=	__('Enabled', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-thumbnail-transform-enabled';
		$item_value				=	intval($prop[$item_name] );
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<label class="pz-toggle-button pz-enabled">';
		echo					'<input type="hidden"   name="properties['.esc_attr($item_name ).']" value="">';
		echo					'<input type="checkbox" name="properties['.esc_attr($item_name ).']" value="1"'.checked((bool) $item_value, true, false ).'>';
		echo					'</label></div>';

		$item_title				=	__('Horizontal Offset', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-thumbnail-transform-x';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	-64;
		$item_max				=	64;
		$item_step				=	1;
		$item_unit				=	'px';
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">'.esc_html($item_unit ).'<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-range-sign pz-sync"></div>';

		$item_title				=	__('Vertical Offset', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-thumbnail-transform-y';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	-64;
		$item_max				=	64;
		$item_step				=	1;
		$item_unit				=	'px';
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">'.esc_html($item_unit ).'<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-range-sign pz-sync"></div>';

		$item_title				=	__('Rotate', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-thumbnail-transform-rotate';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	-360;
		$item_max				=	360;
		$item_step				=	1;
		$item_unit				=	'deg';
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">'.esc_html($item_unit ).'<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-range-sign pz-sync"></div>';

		$item_title				=	__('Scale', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-thumbnail-transform-scale';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	0;
		$item_max				=	200;
		$item_step				=	1;
		$item_unit				=	'%';
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">'.esc_html($item_unit ).'<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-range-sign pz-sync"></div>';

		// --------------------------------------------------------------------------------

		// 枠線
		$item_title				=	__('Border', 'pz-linkcard3' );
		echo					'<tr><th scope="row">'.esc_html($item_title ).'</th><td>';

		$item_title				=	__('Enabled', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-thumbnail-border-enabled';
		$item_value				=	intval($prop[$item_name] );
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<label class="pz-toggle-button pz-enabled">';
		echo					'<input type="hidden"   name="properties['.esc_attr($item_name ).']" value="">';
		echo					'<input type="checkbox" name="properties['.esc_attr($item_name ).']" value="1"'.checked((bool) $item_value, true, false ).'>';
		echo					'</label></div>';

		// 色指定
		$item_title				=	__('Color', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-thumbnail-border-color';
		$item_value				=	$prop[$item_name];
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<label><input type="text" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-color pz-monospace pz-color-picker"></label></div>';

		$item_title				=	__('Line Style', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-thumbnail-border-style';
		$item_value				=	$prop[$item_name];
		$item_list				=	LIST_BORDER;
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><select name="properties['.esc_attr($item_name ).']">';
		foreach					($item_list		as	$list_value	=>	$list_desc ) {
			echo				'<option value="'.esc_attr($list_value ).'"'.selected($list_value, $item_value, false ).'">'.esc_html($list_desc ).'</option>';
		}
		echo					'</select></div>';

		$item_title				=	__('Border Width', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-thumbnail-border-width';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	0;
		$item_max				=	64;
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">px<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="1" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-sync"></div>';

		// 角丸
		$item_title				=	__('Radius', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-thumbnail-border-radius';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	0;
		$item_max				=	64;
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">px<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="1" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-sync"></div>';

		echo					'</td></tr>';

		// --------------------------------------------------------------------------------

		// 影
		$item_title				=	__('Shadow', 'pz-linkcard3' );
		echo					'<tr><th scope="row">'.esc_html($item_title ).'</th><td>';

		$item_title				=	__('Enabled', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-thumbnail-shadow-enabled';
		$item_value				=	intval($prop[$item_name] );
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<label class="pz-toggle-button pz-enabled">';
		echo					'<input type="hidden"   name="properties['.esc_attr($item_name ).']" value="">';
		echo					'<input type="checkbox" name="properties['.esc_attr($item_name ).']" value="1"'.checked((bool) $item_value, true, false ).'>';
		echo					'</label></div>';

		// 色指定
		$item_title				=	__('Color', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-thumbnail-shadow-color';
		$item_value				=	$prop[$item_name];
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<label><input type="text" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-color pz-monospace pz-color-picker"></label></div>';

		$item_title				=	__('Horizontal Offset', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-thumbnail-shadow-x';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	-64;
		$item_max				=	64;
		$item_step				=	1;
		$item_unit				=	'px';
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">'.esc_html($item_unit ).'<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-range-sign pz-sync"></div>';

		$item_title				=	__('Vertical Offset', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-thumbnail-shadow-y';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	-64;
		$item_max				=	64;
		$item_step				=	1;
		$item_unit				=	'px';
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">'.esc_html($item_unit ).'<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-range-sign pz-sync"></div>';

		$item_title				=	__('Blur', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-thumbnail-shadow-blur';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	0;
		$item_max				=	64;
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">px<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="1" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-sync"></div>';

		$item_title				=	__('Spread', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-thumbnail-shadow-spread';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	0;
		$item_max				=	64;
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">px<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="1" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-sync"></div>';

		$item_title				=	__('Inset Shadow', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-thumbnail-shadow-inset';
		$item_value				=	intval($prop[$item_name] );
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<label class="pz-toggle-button">';
		echo					'<input type="hidden"   name="properties['.esc_attr($item_name ).']" value="">';
		echo					'<input type="checkbox" name="properties['.esc_attr($item_name ).']" value="1"'.checked((bool) $item_value, true, false ).'>';
		echo					'</label></div>';

		echo					'</td></tr></table>';


		// ================================================================================


		// もっと読むボタン
		$item_header			=	__('More-Read Button',	'pz-linkcard3' );
		echo					'<h3>'.esc_html($item_header ).'</h3>';
		echo					'<table class="form-table">';

		// 表示テキスト
		$item_title				=	__('Text',	'pz-linkcard3' );
		echo					'<tr><th colspan="2">'.esc_html($item_title ).'</th><td>';

		$item_notice			=	__('When a string is entered, it is overlaid on the lower right corner of the article content.', 'pz-linkcard3' );
		$item_class				=	'regular-text';
		$item_name				=	$t['name'].'-more-text';
		$item_value				=	esc_attr($prop[$item_name] );
		$item_list				=	array(
			__('More...',				'pz-linkcard3' ),
			__('Read more',				'pz-linkcard3' ),
			__('Go read the article',	'pz-linkcard3' ),
		);
		echo					'<label><input type="text" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="'.esc_attr($item_class ).'" list="datalist-'.esc_attr($item_name ).'"></label>';
		echo					'<datalist id="datalist-'.esc_attr($item_name ).'">';
		foreach					($item_list			as	$value ) {
			echo				'<option value="'.esc_attr($value ).'">'.esc_html($value ).'</option>';
		}
		echo					'</datalist>';
		if						($item_notice ) {
			echo				'<p>'.wp_kses_post($item_notice ).'</p>';
		}
		echo					'</td></tr>';

		// 表示位置
		$item_title				=	__('Position', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-more-position';
		$item_notice			=	'';
		$s_name					=	'name="properties['.esc_attr($item_name ).']"';
		$item_class				=	'';
		$item_disabled			=	'';
		$item_value				=	esc_attr($prop[$item_name] );
		$item_value_list		=	LIST_MORE_POSITION;
		$item_option			=	'';
		foreach		($item_value_list		as	$value	=>	$description ) {
			$item_option		.=	'<option value="'.esc_attr($value ).'" '.selected($item_value, $value, false ).'>'.esc_html($description ).'</option>';
		}
		echo					'<tr><th colspan="2">'.esc_html($item_title ).'</th><td><select name="properties['.esc_attr($item_name ).']" class="'.esc_attr($item_class ).'" '.disabled($item_disabled !== '', true, false ).' >'.wp_kses($item_option, $settings_card_allowed_html ).'</select></td>'.wp_kses_post($item_notice ).'</tr>';
	
		// --------------------------------------------------------------------------------

		// 変形設定
		$item_header			=	__('Decoration', 'pz-linkcard3' );
		$item_title				=	__('Translate', 'pz-linkcard3' );
		echo					'<tr><th rowspan="4">'.esc_html($item_header ).'</th><th>'.esc_html($item_title ).'</th><td>';

		$item_title				=	__('Enabled', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-more-transform-enabled';
		$item_value				=	intval($prop[$item_name] );
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<label class="pz-toggle-button pz-enabled">';
		echo					'<input type="hidden"   name="properties['.esc_attr($item_name ).']" value="">';
		echo					'<input type="checkbox" name="properties['.esc_attr($item_name ).']" value="1"'.checked((bool) $item_value, true, false ).'>';
		echo					'</label></div>';

		$item_title				=	__('Horizontal Offset', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-more-transform-x';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	-64;
		$item_max				=	64;
		$item_step				=	1;
		$item_unit				=	'px';
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">'.esc_html($item_unit ).'<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-range-sign pz-sync"></div>';

		$item_title				=	__('Vertical Offset', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-more-transform-y';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	-64;
		$item_max				=	64;
		$item_step				=	1;
		$item_unit				=	'px';
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">'.esc_html($item_unit ).'<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-range-sign pz-sync"></div>';

		$item_title				=	__('Rotate', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-more-transform-rotate';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	-360;
		$item_max				=	360;
		$item_step				=	1;
		$item_unit				=	'deg';
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">'.esc_html($item_unit ).'<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-range-sign pz-sync"></div>';

		$item_title				=	__('Scale', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-more-transform-scale';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	0;
		$item_max				=	200;
		$item_step				=	1;
		$item_unit				=	'%';
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">'.esc_html($item_unit ).'<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-range-sign pz-sync"></div>';

		// --------------------------------------------------------------------------------

		// 背景色
		$item_title				=	__('Background Color', 'pz-linkcard3' );
		echo					'<tr><th scope="row">'.esc_html($item_title ).'</th><td>';

		$item_title				=	__('Enabled', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-more-bg-enabled';
		$item_value				=	intval($prop[$item_name] );
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<label class="pz-toggle-button pz-enabled">';
		echo					'<input type="hidden"   name="properties['.esc_attr($item_name ).']" value="">';
		echo					'<input type="checkbox" name="properties['.esc_attr($item_name ).']" value="1"'.checked((bool) $item_value, true, false ).'>';
		echo					'</label></div>';

		// 背景色の色指定
		$item_name				=	$t['name'].'-more-bg-color';
		$item_value				=	$prop[$item_name];
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<label><input type="text" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-color pz-monospace pz-color-picker"></label></div>';

		// 背景画像などのショートハンド指定
		$item_title				=	__('Shorthand Property',	'pz-linkcard3' );
		$item_name				=	$t['name'].'-more-bg-image';
		$item_class				=	'regular-text';
		$item_value				=	esc_attr($prop[$item_name] );
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<input type="text" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" maxlength="1000" class="'.esc_attr($item_class ).'"></td>';
		echo					'</td></tr>';

		// --------------------------------------------------------------------------------

		// 枠線
		$item_title				=	__('Border', 'pz-linkcard3' );
		echo					'<tr><th scope="row">'.esc_html($item_title ).'</th><td>';

		$item_title				=	__('Enabled', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-more-border-enabled';
		$item_value				=	intval($prop[$item_name] );
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<label class="pz-toggle-button pz-enabled">';
		echo					'<input type="hidden"   name="properties['.esc_attr($item_name ).']" value="">';
		echo					'<input type="checkbox" name="properties['.esc_attr($item_name ).']" value="1"'.checked((bool) $item_value, true, false ).'>';
		echo					'</label></div>';

		// 色指定
		$item_title				=	__('Color', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-more-border-color';
		$item_value				=	$prop[$item_name];
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<label><input type="text" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-color pz-monospace pz-color-picker"></label></div>';


		$item_title				=	__('Line Style', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-more-border-style';
		$item_value				=	$prop[$item_name];
		$item_list				=	LIST_BORDER;
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><select name="properties['.esc_attr($item_name ).']">';
		foreach					($item_list		as	$list_value	=>	$list_desc ) {
			echo				'<option value="'.esc_attr($list_value ).'"'.selected($list_value, $item_value, false ).'">'.esc_html($list_desc ).'</option>';
		}
		echo					'</select></div>';

		$item_title				=	__('Border Width', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-more-border-width';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	0;
		$item_max				=	64;
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">px<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="1" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-sync"></div>';

		// 角丸
		$item_title				=	__('Radius', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-more-border-radius';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	0;
		$item_max				=	64;
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">px<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="1" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-sync"></div>';

		echo					'</td></tr>';

		// --------------------------------------------------------------------------------

		// 影
		$item_title				=	__('Shadow', 'pz-linkcard3' );
		echo					'<tr><th scope="row">'.esc_html($item_title ).'</th><td>';

		$item_title				=	__('Enabled', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-more-shadow-enabled';
		$item_value				=	intval($prop[$item_name] );
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<label class="pz-toggle-button pz-enabled">';
		echo					'<input type="hidden"   name="properties['.esc_attr($item_name ).']" value="">';
		echo					'<input type="checkbox" name="properties['.esc_attr($item_name ).']" value="1"'.checked((bool) $item_value, true, false ).'>';
		echo					'</label></div>';

		// 色指定
		$item_title				=	__('Color', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-more-shadow-color';
		$item_value				=	$prop[$item_name];
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<label><input type="text" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-color pz-monospace pz-color-picker"></label></div>';

		$item_title				=	__('Horizontal Offset', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-more-shadow-x';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	-64;
		$item_max				=	64;
		$item_step				=	1;
		$item_unit				=	'px';
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">'.esc_html($item_unit ).'<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-range-sign pz-sync"></div>';

		$item_title				=	__('Vertical Offset', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-more-shadow-y';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	-64;
		$item_max				=	64;
		$item_step				=	1;
		$item_unit				=	'px';
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">'.esc_html($item_unit ).'<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="'.esc_attr($item_step ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-range-sign pz-sync"></div>';

		$item_title				=	__('Blur', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-more-shadow-blur';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	0;
		$item_max				=	64;
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">px<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="1" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-sync"></div>';

		$item_title				=	__('Spread', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-more-shadow-spread';
		$item_value				=	intval($prop[$item_name] );
		$item_min				=	0;
		$item_max				=	64;
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br><input type="number" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" class="pz-sync pz-align-right">px<br><input type="range" min="'.esc_attr($item_min ).'" max="'.esc_attr($item_max ).'" step="1" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" style="width: 140px;" class="pz-sync"></div>';

		$item_title				=	__('Inset Shadow', 'pz-linkcard3' );
		$item_name				=	$t['name'].'-more-shadow-inset';
		$item_value				=	intval($prop[$item_name] );
		echo					'<div class="pz-items">'.esc_html($item_title ).'<br>';
		echo					'<label class="pz-toggle-button">';
		echo					'<input type="hidden"   name="properties['.esc_attr($item_name ).']" value="">';
		echo					'<input type="checkbox" name="properties['.esc_attr($item_name ).']" value="1"'.checked((bool) $item_value, true, false ).'>';
		echo					'</label></div>';

		echo					'</tr></table>';


		// ================================================================================


		submit_button();
		echo					'</div>';
	}

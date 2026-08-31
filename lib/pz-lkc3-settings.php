<?php
	
if (!defined( 'ABSPATH' ) ) { header( 'HTTP/1.1 403 Forbidden' ); exit; } ?>
<?php
	global	$wpdb;

	// 有効/無効の選択肢
	define('LIST_ENABLED',	array(
		false			=>		__('Disabled',			'pz-linkcard3' ),
		true			=>		__('Enabled',			'pz-linkcard3' ),
	) );

	// リンクカードを囲むHTMLタグの選択肢
	define('LIST_ENCLOSE_TAG',	array(
		'div'			=>		__('DIV Tag',			'pz-linkcard3' ),
		'blockquote'	=>		__('BLOCKQUOTE Tag',	'pz-linkcard3' ),
		'figure'		=>		__('FIGURE Tag',		'pz-linkcard3' ),
		'article'		=>		__('ARTICLE Tag',		'pz-linkcard3' ),
		'section'		=>		__('SECTION Tag',		'pz-linkcard3' ),
		'nav'			=>		__('NAV Tag',			'pz-linkcard3' ),
		'aside'			=>		__('ASIDE Tag',			'pz-linkcard3' ),
	) );

	// ピクセル値の選択肢
	define('LIST_PX',		array(
		'1px' => __('1px', 'pz-linkcard3' ), '2px' => __('2px', 'pz-linkcard3' ), '3px' => __('3px', 'pz-linkcard3' ), '4px' => __('4px', 'pz-linkcard3' ), '5px' => __('5px', 'pz-linkcard3' ), '6px' => __('6px', 'pz-linkcard3' ), '7px' => __('7px', 'pz-linkcard3' ), '8px' => __('8px', 'pz-linkcard3' ), '9px' => __('9px', 'pz-linkcard3' ), '10px' => __('10px', 'pz-linkcard3' ), '11px' => __('11px', 'pz-linkcard3' ), '12px' => __('12px', 'pz-linkcard3' ), '13px' => __('13px', 'pz-linkcard3' ), '14px' => __('14px', 'pz-linkcard3' ), '15px' => __('15px', 'pz-linkcard3' ), '16px' => __('16px', 'pz-linkcard3' ), '17px' => __('17px', 'pz-linkcard3' ), '18px' => __('18px', 'pz-linkcard3' ), '19px' => __('19px', 'pz-linkcard3' ), '20px' => __('20px', 'pz-linkcard3' ), '21px' => __('21px', 'pz-linkcard3' ), '22px' => __('22px', 'pz-linkcard3' ), '23px' => __('23px', 'pz-linkcard3' ), '24px' => __('24px', 'pz-linkcard3' ), '25px' => __('25px', 'pz-linkcard3' ), '26px' => __('26px', 'pz-linkcard3' ), '27px' => __('27px', 'pz-linkcard3' ), '28px' => __('28px', 'pz-linkcard3' ), '29px' => __('29px', 'pz-linkcard3' ), '30px' => __('30px', 'pz-linkcard3' ), '31px' => __('31px', 'pz-linkcard3' ), '32px' => __('32px', 'pz-linkcard3' ), '33px' => __('33px', 'pz-linkcard3' ), '34px' => __('34px', 'pz-linkcard3' ), '35px' => __('35px', 'pz-linkcard3' ), '36px' => __('36px', 'pz-linkcard3' ), '37px' => __('37px', 'pz-linkcard3' ), '38px' => __('38px', 'pz-linkcard3' ), '39px' => __('39px', 'pz-linkcard3' ), '40px' => __('40px', 'pz-linkcard3' ), '41px' => __('41px', 'pz-linkcard3' ), '42px' => __('42px', 'pz-linkcard3' ), '43px' => __('43px', 'pz-linkcard3' ), '44px' => __('44px', 'pz-linkcard3' ), '45px' => __('45px', 'pz-linkcard3' ), '46px' => __('46px', 'pz-linkcard3' ), '47px' => __('47px', 'pz-linkcard3' ), '48px' => __('48px', 'pz-linkcard3' ), '49px' => __('49px', 'pz-linkcard3' ), '50px' => __('50px', 'pz-linkcard3' ), '51px' => __('51px', 'pz-linkcard3' ), '52px' => __('52px', 'pz-linkcard3' ), '53px' => __('53px', 'pz-linkcard3' ), '54px' => __('54px', 'pz-linkcard3' ), '55px' => __('55px', 'pz-linkcard3' ), '56px' => __('56px', 'pz-linkcard3' ), '57px' => __('57px', 'pz-linkcard3' ), '58px' => __('58px', 'pz-linkcard3' ), '59px' => __('59px', 'pz-linkcard3' ), '60px' => __('60px', 'pz-linkcard3' ), '61px' => __('61px', 'pz-linkcard3' ), '62px' => __('62px', 'pz-linkcard3' ), '63px' => __('63px', 'pz-linkcard3' ), '64px' => __('64px', 'pz-linkcard3' ),
	) );

	// 位置調整用のピクセル値
	define('OFFSET_PX',		array(
		'-20px'		=>	__('-20px', 'pz-linkcard3' ),
		'-18px'		=>	__('-18px', 'pz-linkcard3' ),
		'-16px'		=>	__('-16px', 'pz-linkcard3' ),
		'-12px'		=>	__('-12px', 'pz-linkcard3' ), 
		'-8px'		=>	__('-8px', 'pz-linkcard3' ), 
		'-6px'		=>	__('-6px', 'pz-linkcard3' ), 
		'-4px'		=>	__('-4px', 'pz-linkcard3' ), 
		'-2px'		=>	__('-2px', 'pz-linkcard3' ), 
		'0'			=>	__('0', 'pz-linkcard3' ), 
		'2px'		=>	__('2px', 'pz-linkcard3' ),
		'4px'		=>	__('4px', 'pz-linkcard3' ), 
		'6px'		=>	__('6px', 'pz-linkcard3' ), 
		'8px'		=>	__('8px', 'pz-linkcard3' ), 
		'12px'		=>	__('12px', 'pz-linkcard3' ), 
		'16px'		=>	__('16px', 'pz-linkcard3' ), 
		'18px'		=>	__('18px', 'pz-linkcard3' ), 
		'20px'		=>	__('20px', 'pz-linkcard3' ),
	) );

	// 余白の選択肢
	define('LIST_MARGIN',	array(
		'auto'			=>		__('Auto',				'pz-linkcard3' ),
		'0'				=>		__('0',					'pz-linkcard3' ),
		'4px'			=>		__('4px',				'pz-linkcard3' ),
		'8px'			=>		__('8px',				'pz-linkcard3' ),
		'12px'			=>		__('12px',				'pz-linkcard3' ),
		'16px'			=>		__('16px',				'pz-linkcard3' ),
		'20px'			=>		__('20px',				'pz-linkcard3' ),
		'24px'			=>		__('24px',				'pz-linkcard3' ),
		'32px'			=>		__('32px',				'pz-linkcard3' ),
		'40px'			=>		__('40px',				'pz-linkcard3' ),
		'48px'			=>		__('48px',				'pz-linkcard3' ),
		'56px'			=>		__('56px',				'pz-linkcard3' ),
		'64px'			=>		__('64px',				'pz-linkcard3' ),
	) );

	define('LIST_MARGIN_N',	array(
		'-32px'			=>		__('-32px',				'pz-linkcard3' ),
		'-24px'			=>		__('-24px',				'pz-linkcard3' ),
		'-16px'			=>		__('-16px',				'pz-linkcard3' ),
		'-8px'			=>		__('-8px',				'pz-linkcard3' ),
		'0'				=>		__('0',					'pz-linkcard3' ),
		'4px'			=>		__('4px',				'pz-linkcard3' ),
		'8px'			=>		__('8px',				'pz-linkcard3' ),
		'12px'			=>		__('12px',				'pz-linkcard3' ),
		'16px'			=>		__('16px',				'pz-linkcard3' ),
		'20px'			=>		__('20px',				'pz-linkcard3' ),
		'24px'			=>		__('24px',				'pz-linkcard3' ),
		'32px'			=>		__('32px',				'pz-linkcard3' ),
		'40px'			=>		__('40px',				'pz-linkcard3' ),
		'48px'			=>		__('48px',				'pz-linkcard3' ),
		'56px'			=>		__('56px',				'pz-linkcard3' ),
		'64px'			=>		__('64px',				'pz-linkcard3' ),
	) );

	// autoを含む余白の選択肢
	define('LIST_MARGIN_A',	array(
		'auto'			=>		__('auto',				'pz-linkcard3' ),
		'0'				=>		__('0',					'pz-linkcard3' ),
		'4px'			=>		__('4px',				'pz-linkcard3' ),
		'8px'			=>		__('8px',				'pz-linkcard3' ),
		'12px'			=>		__('12px',				'pz-linkcard3' ),
		'16px'			=>		__('16px',				'pz-linkcard3' ),
		'20px'			=>		__('20px',				'pz-linkcard3' ),
		'24px'			=>		__('24px',				'pz-linkcard3' ),
		'32px'			=>		__('32px',				'pz-linkcard3' ),
		'40px'			=>		__('40px',				'pz-linkcard3' ),
		'48px'			=>		__('48px',				'pz-linkcard3' ),
		'56px'			=>		__('56px',				'pz-linkcard3' ),
		'64px'			=>		__('64px',				'pz-linkcard3' ),
	) );

	// 0以上の余白の選択肢
	define('LIST_MARGIN_0',	array(
		'0'				=>		__('0',					'pz-linkcard3' ),
		'4px'			=>		__('4px',				'pz-linkcard3' ),
		'8px'			=>		__('8px',				'pz-linkcard3' ),
		'12px'			=>		__('12px',				'pz-linkcard3' ),
		'16px'			=>		__('16px',				'pz-linkcard3' ),
		'20px'			=>		__('20px',				'pz-linkcard3' ),
		'24px'			=>		__('24px',				'pz-linkcard3' ),
		'32px'			=>		__('32px',				'pz-linkcard3' ),
		'40px'			=>		__('40px',				'pz-linkcard3' ),
		'48px'			=>		__('48px',				'pz-linkcard3' ),
		'56px'			=>		__('56px',				'pz-linkcard3' ),
		'64px'			=>		__('64px',				'pz-linkcard3' ),
	) );

	define('LIST_HEADING_MARGIN',	array(
		'-64px'			=>		__('-64px',				'pz-linkcard3' ),
		'-56px'			=>		__('-56px',				'pz-linkcard3' ),
		'-48px'			=>		__('-48px',				'pz-linkcard3' ),
		'-40px'			=>		__('-40px',				'pz-linkcard3' ),
		'-32px'			=>		__('-32px',				'pz-linkcard3' ),
		'-24px'			=>		__('-24px',				'pz-linkcard3' ),
		'-16px'			=>		__('-16px',				'pz-linkcard3' ),
		'-8px'			=>		__('-8px',				'pz-linkcard3' ),
		'-4px'			=>		__('-4px',				'pz-linkcard3' ),
		'0'				=>		__('0',					'pz-linkcard3' ),
		'4px'			=>		__('4px',				'pz-linkcard3' ),
		'8px'			=>		__('8px',				'pz-linkcard3' ),
		'16px'			=>		__('16px',				'pz-linkcard3' ),
		'24px'			=>		__('24px',				'pz-linkcard3' ),
		'32px'			=>		__('32px',				'pz-linkcard3' ),
		'40px'			=>		__('40px',				'pz-linkcard3' ),
		'48px'			=>		__('48px',				'pz-linkcard3' ),
		'56px'			=>		__('56px',				'pz-linkcard3' ),
		'64px'			=>		__('64px',				'pz-linkcard3' ),
	) );

	// 角丸の選択肢
	define('LIST_RADIUS',	array(
		''				=>		__('None',				'pz-linkcard3' ),
		'2px'			=>		__('2px',				'pz-linkcard3' ),
		'4px'			=>		__('4px',				'pz-linkcard3' ),
		'6px'			=>		__('6px',				'pz-linkcard3' ),
		'8px'			=>		__('8px',				'pz-linkcard3' ),
		'12px'			=>		__('12px',				'pz-linkcard3' ),
		'16px'			=>		__('16px',				'pz-linkcard3' ),
		'20px'			=>		__('20px',				'pz-linkcard3' ),
		'24px'			=>		__('24px',				'pz-linkcard3' ),
		'32px'			=>		__('32px',				'pz-linkcard3' ),
		'64px'			=>		__('64px',				'pz-linkcard3' ),
		'50%'			=>		__('50%',				'pz-linkcard3' ),
	) );

	// 枠線の選択肢
	define('LIST_BORDER',	array(
		'solid'			=>		__('Solid',				'pz-linkcard3' ),
		'dotted'		=>		__('Dotted',			'pz-linkcard3' ),
		'dashed'		=>		__('Dashed',			'pz-linkcard3' ),
		'double'		=>		__('Double',			'pz-linkcard3' ),
		'groove'		=>		__('Groove',			'pz-linkcard3' ),
		'ridge'			=>		__('Ridge',				'pz-linkcard3' ),
		'inset'			=>		__('Inset',				'pz-linkcard3' ),
		'outset'		=>		__('Outset',			'pz-linkcard3' ),
	) );

	// リンクの開き方の選択肢
	define('LIST_NEWTAB',	array(
		''				=>		__('None',				'pz-linkcard3' ),
		'1'				=>		__('All Devices',		'pz-linkcard3' ),
		'2'				=>		__('Non-Mobile Devices',	'pz-linkcard3' ),
	) );

	// アイコンサイズの選択肢
	define('LIST_ICON_SIZE',	array(
		'8px'			=>		__('8px',				'pz-linkcard3' ),
		'16px'			=>		__('16px',				'pz-linkcard3' ),
		'24px'			=>		__('24px',				'pz-linkcard3' ),
		'32px'			=>		__('32px',				'pz-linkcard3' ),
		'40px'			=>		__('40px',				'pz-linkcard3' ),
		'48px'			=>		__('48px',				'pz-linkcard3' ),
		'56px'			=>		__('56px',				'pz-linkcard3' ),
		'64px'			=>		__('64px',				'pz-linkcard3' ),
	) );

	// 情報表示位置の選択肢
	define('LIST_INFO_POSITION',	array(
		''				=>		__('None',				'pz-linkcard3' ),
		'u'				=>		__('Top Side',			'pz-linkcard3' ),
		'd'				=>		__('Bottom Side',		'pz-linkcard3' ),
	) );

	// 情報欄に表示する項目の選択肢
	define('LIST_INFO_ITEMS',	array(
		''				=>		__('None',						'pz-linkcard3' ),
		'i'				=>		__('Site Icon',					'pz-linkcard3' ),
		'n'				=>		__('Site Name',					'pz-linkcard3' ),
		'd'				=>		__('Domain',					'pz-linkcard3' ),
		'c'				=>		__('Category',					'pz-linkcard3' ),
		's'				=>		__('SNS Count',					'pz-linkcard3' ),
		'a'				=>		__('Added Text',				'pz-linkcard3' ),
		'u'				=>		__('URL',						'pz-linkcard3' ),
		't'				=>		__('Title',						'pz-linkcard3' ),
		'p'				=>		__('Post Date',					'pz-linkcard3' ),
		'q'				=>		__('Post Date (Right-aligned)',	'pz-linkcard3' ),
	) );

	// サムネイル表示位置の選択肢
	define('LIST_THUMBNAIL_POSITION',	array(
		''				=>		__('None',						'pz-linkcard3' ),
		'l'				=>		__('Left Side',					'pz-linkcard3' ),
		'r'				=>		__('Right Side',				'pz-linkcard3' ),
		'u'				=>		__('Top Side',					'pz-linkcard3' ),
	) );

	// サムネイルサイズの選択肢
	define('LIST_THUMBNAIL_SIZE',		array(
		'thumbnail'		=>		__('Thumbnail (150px)',			'pz-linkcard3' ),
		'midium'		=>		__('Medium (300px)',			'pz-linkcard3' ),
		'large'			=>		__('Large (1024px)',			'pz-linkcard3' ),
		'full'			=>		__('Original Size',				'pz-linkcard3' ),
	) );

	// 画像サイズの選択肢
	define('LIST_IMAGE_SIZE',		array(
		100				=>		__('100px (Thumbnail)',			'pz-linkcard3' ),
		200				=>		__('200px (Small)',				'pz-linkcard3' ),
		300				=>		__('300px (Small)',				'pz-linkcard3' ),
		400				=>		__('400px (Medium)',			'pz-linkcard3' ),
		500				=>		__('500px (Medium)',			'pz-linkcard3' ),
		600				=>		__('600px (Large)',				'pz-linkcard3' ),
		800				=>		__('800px (Large)',				'pz-linkcard3' ),
	) );

	// 本文欄に表示する項目の選択肢
	define('LIST_CONTENT_ITEMS',	array(
		''				=>		__('None',						'pz-linkcard3' ),
		't'				=>		__('Title',						'pz-linkcard3' ),
		'e'				=>		__('Excerpt',					'pz-linkcard3' ),
		'u'				=>		__('URL',						'pz-linkcard3' ),
		'p'				=>		__('Post Date',					'pz-linkcard3' ),
		'q'				=>		__('Post Date (Right-aligned)',	'pz-linkcard3' ),
		'c'				=>		__('Category',					'pz-linkcard3' ),
		's'				=>		__('SNS Count',					'pz-linkcard3' ),
		'n'				=>		__('Site Name',					'pz-linkcard3' ),
		'i'				=>		__('Site Information',			'pz-linkcard3' ),
	) );

	// もっと見る表示のスタイル選択肢
	define('LIST_MORE_STYLE',		array(
		'txt'			=>		__('Text only',					'pz-linkcard3' ),
		'smp'			=>		__('Simple button',				'pz-linkcard3' ),
		'btn'			=>		__('Button',					'pz-linkcard3' ),
		'psh'			=>		__('Push Button',				'pz-linkcard3' ),
	) );

	// もっと見る表示位置の選択肢
	define('LIST_MORE_POSITION',	array(
		'o_r'			=>		__('Overlap the bottom-right corner of the article content.',	'pz-linkcard3' ),
		'o_l'			=>		__('Overlap the bottom-left corner of the article content.',	'pz-linkcard3' ),
		'u'				=>		__('Below Article Content',		'pz-linkcard3' ),
	) );

	// マウスホバー時の効果
	define('LIST_HOVER',		array(
		''				=>		__('None',						'pz-linkcard3' ),
		'1'				=>		__('Lighten',					'pz-linkcard3' ),
		'2'				=>		__('Hover (Light)',				'pz-linkcard3' ),
		'3'				=>		__('Hover (Dark)',				'pz-linkcard3' ),
		'4'				=>		__('Retract (for Shadow)',		'pz-linkcard3' ),
		'7'				=>		__('Radius',					'pz-linkcard3' ),
	) );

	// ヘッダー表示の選択肢
	define('LIST_HEADER',			array(
		__('External site',			'pz-linkcard3' ),
		__('This site',				'pz-linkcard3' ),
		__('Reference',				'pz-linkcard3' ),
	) );

	// 影のスタイル選択肢
	define('LIST_SHADOW_STYLE',		array(
		0				=>		__('None',						'pz-linkcard3' ),
		3				=>		__('Lower right',				'pz-linkcard3' ),
		2				=>		__('Lower',						'pz-linkcard3' ),
		1				=>		__('Lower left',				'pz-linkcard3' ),
		4				=>		__('Left',						'pz-linkcard3' ),
		7				=>		__('Upper left',				'pz-linkcard3' ),
		8				=>		__('Upper',						'pz-linkcard3' ),
		9				=>		__('Upper right',				'pz-linkcard3' ),
		6				=>		__('Right',						'pz-linkcard3' ),
	) );

	// 日付表示形式の選択肢
	define('LIST_DATE_STYLE',		array(
		'1'				=>		__('Post Date',					'pz-linkcard3' ),
		'2'				=>		__('Modified Date',				'pz-linkcard3' ),
		'3'				=>		__('Post Date & Modified Date',	'pz-linkcard3'),
	) );

	// 日付フォーマットの選択肢
	define('LIST_$this->format_date',		array(
		'Y/m/d'					=>		__('Y/m/d',				'pz-linkcard3' ),
		'Y.m.d'					=>		__('Y.m.d',				'pz-linkcard3' ),
		'Y-m-d'					=>		__('Y-m-d',				'pz-linkcard3' ),
		'Y年m月d日'				=>		__('Y年m月d日',			'pz-linkcard3' ),
		'm/d/Y'					=>		__('m/d/Y',				'pz-linkcard3' ),
		'd/m/Y'					=>		__('d/m/Y',				'pz-linkcard3' ),
		'F j, Y'				=>		__('F j, Y',			'pz-linkcard3' ),
		'l jS \of F Y'			=>		__('l jS \of F Y',		'pz-linkcard3' ),
	) );

	define('LIST_DATE_ICON',			array(
		'&#x1F4C5;&#xFE0F;'		=>		__('&#x1F4C5;&#xFE0F;',	'pz-linkcard3' ),
		'&#x1F5D3;&#xFE0F;'		=>		__('&#x1F5D3;&#xFE0F;',	'pz-linkcard3' ),
		'&#x1F504;&#xFE0F;'		=>		__('&#x1F504;&#xFE0F;',	'pz-linkcard3' ),
		'&#x270F;&#xFE0F;'		=>		__('&#x270F;&#xFE0F;',	'pz-linkcard3' ),
		'&#x1F4DD;&#xFE0F;'		=>		__('&#x1F4DD;&#xFE0F;',	'pz-linkcard3' ),
		'&#x270D;&#xFE0F;'		=>		__('&#x270D;&#xFE0F;',	'pz-linkcard3' ),
		'&#x1F5D2;&#xFE0F;'		=>		__('&#x1F5D2;&#xFE0F;',	'pz-linkcard3' ),
		'&#x1F4CB;&#xFE0F;'		=>		__('&#x1F4CB;&#xFE0F;',	'pz-linkcard3' ),
		'&#x2B50;&#xFE0F;'		=>		__('&#x2B50;&#xFE0F;',	'pz-linkcard3' ),
		'&#x2728;&#xFE0F;'		=>		__('&#x2728;&#xFE0F;',	'pz-linkcard3' ),
		'&#x1F3B5;&#xFE0F;'		=>		__('&#x1F3B5;&#xFE0F;',	'pz-linkcard3' ),
		'&#x1F3B6;&#xFE0F;'		=>		__('&#x1F3B6;&#xFE0F;',	'pz-linkcard3' ),
		'&#x1F389;&#xFE0F;'		=>		__('&#x1F389;&#xFE0F;',	'pz-linkcard3' ),
		'&#x1F380;&#xFE0F;'		=>		__('&#x1F380;&#xFE0F;',	'pz-linkcard3' ),
		'&#x1F381;&#xFE0F;'		=>		__('&#x1F381;&#xFE0F;',	'pz-linkcard3' ),
		'&#x1F4CC;&#xFE0F;'		=>		__('&#x1F4CC;&#xFE0F;',	'pz-linkcard3' ),
	) );

	Global	$wp_version;
	$temp			=	'Mozilla/5.0 (WordPress/%WP_VERSION%;) %PLUGIN_NAME%-Crawler/%PLUGIN_VERSION%';
	$temp			=	str_replace(array('%PLUGIN_NAME%',			'%PLUGIN_VERSION%',		'%WP_VERSION%',	'%CURL_VERSION%',          	'%PHP_VERSION%', 	'%MY_URL%',			'%USER_AGENT%',			'%%', ),
									array( $this->plugin_name,       $this->plugin_version,  $wp_version,     curl_version()['version'],phpversion(),   	 $this->my_url,      $this->user_agent,   	'%', ),	$temp );
	$temp_pzlkc		=	$temp;
	$temp			=	'Mozilla/5.0 (WordPress/%WP_VERSION%;) %MY_URL%';
	$temp			=	str_replace(array('%PLUGIN_NAME%',			'%PLUGIN_VERSION%',	'%WP_VERSION%',	'%CURL_VERSION%',          		'%PHP_VERSION%', 	'%MY_URL%',			'%USER_AGENT%',			'%%', ),
									array( $this->plugin_name,       $this->plugin_version,  $wp_version,	 curl_version()['version'],	 phpversion(),   	 $this->my_url,      $this->user_agent,   	'%', ),	$temp );
	$temp_mysite	=	$temp;
	$temp			=	'%USER_AGENT%';
	$temp			=	str_replace(array('%PLUGIN_NAME%',			'%PLUGIN_VERSION%',	'%WP_VERSION%',	'%CURL_VERSION%',          		'%PHP_VERSION%', 	'%MY_URL%',			'%USER_AGENT%',			'%%', ),
									array( $this->plugin_name,       $this->plugin_version,  $wp_version,	 curl_version()['version'],	 phpversion(),   	 $this->my_url,      $this->user_agent,   	'%', ),	$temp );
	$temp_access	=	$temp;
	define('LIST_USER_AGENT',			array(
		'pzlkc'		=>	$temp_pzlkc,
		'mysite'	=>	$temp_mysite,
		''			=>	$temp_access,
	) );

	$tmp_tbl	=	array();
	foreach  (wp_get_schedules()	as  $key => $value ) {
		$i							=	intval($value['interval'] );	
		$tmp_tbl[$i]['key']			=	$key;
		$tmp_tbl[$i]['display']		=	$value['display'];
	}
	ksort($tmp_tbl );
	$time_tbl	=	array();
	foreach  ($tmp_tbl				as  $key => $value ) {
		$time_tbl[$value['key']]	=	$value['display'];
	}
	define('LIST_PERIOD',				$time_tbl );

	// キャッシュ更新間隔の数値選択肢
	define('LIST_PERIOD_NUMBER',	array(
		'1'				=>	__('1 case',					'pz-linkcard3' ),
		'5'				=>	__('5 cases',					'pz-linkcard3' ),
		'10'			=>	__('10 cases',					'pz-linkcard3' ),
		'20'			=>	__('20 cases',					'pz-linkcard3' ),
		'50'			=>	__('50 cases',					'pz-linkcard3' ),
	) );

	// URL取得方法の選択肢
	define('LIST_METHOD',			array(
		''				=>	__('Always retrieve the latest post content.', 							'pz-linkcard3' ),
		'1'				=>	__('Always use the latest post content. Prioritize the excerpt.', 			'pz-linkcard3' ),
		'3'				=>	__('Always use the latest post content. Prioritize the custom field.', 		'pz-linkcard3' ),
		'2'				=>	__('Always display the content registered in the cache manager.', 			'pz-linkcard3' ),
	) );

//	////////////////////////////////////////////////////////////////////////////////

	$html_notice		=	'';
	$html_header		=	'';
	$html_title			=	'';
	$html_input			=	'';
	$html_style			=	'';
	$html_result		=	'';
	$html_preview		=	'';
	$html_overlay		=	'';
	$html_scroll_restore	=	'';

	$make_settings_notice = function($type, $message) {
		return '<div class="notice notice-'.esc_attr($type ).' is-dismissible"><p><strong>'.$message.'</strong></p></div>';
	};
	$add_notice_datetime = function($message) {
		return $message.' ('.esc_html(wp_date($this->format_datetime ) ).')';
	};
	$make_style_notice = function($result) use ($make_settings_notice, $add_notice_datetime) {
		switch	(intval($result ) ) {
		case	1:
			return $make_settings_notice('success', $add_notice_datetime(esc_html(__('Updated the appearance of the LinkCard.', 'pz-linkcard3' ) ) ) );
		case	2:
			return $make_settings_notice('error', esc_html(__('Failed to save the CSS file.', 'pz-linkcard3' ) ) );
		case	9:
			return $make_settings_notice('error', esc_html(__('Failed to load the CSS template.', 'pz-linkcard3' ) ) );
		}
		return '';
	};

	$action				=	isset($_POST['action'] ) ? sanitize_text_field(wp_unslash($_POST['action'] ) ) : '';
	$submit				=	isset($_POST['submit'] ) ? sanitize_text_field(wp_unslash($_POST['submit'] ) ) : '';
	if		(!$action	&&	$submit ) {
		$action			=	'save-changed';
	}

	if	($action ) {
		check_admin_referer('pz-settings' );
	}

	// 必要に応じてCSSを再生成
	$flg_style		=	false;
	$flg_change		=	false;
	$flg_error		=	false;
	$prop			=	null;
	if	(isset($_POST['properties'] ) && is_array($_POST['properties'] ) ) {
		$posted_properties	=	map_deep(wp_unslash($_POST['properties'] ), 'sanitize_text_field' );

		foreach (self::DEFAULTS 		as $key => $value ) {
			$prop[$key]			=	$value['value'];
		}

		foreach	($posted_properties	as	$key => $value ) {
			$prop[$key]	=	$value;
			if	(array_key_exists($key,		$this->options ) ) {
				if	($value		!=	$this->options[$key] ) {
					$flg_change	=	true;
				}
			}
		}
		ksort($prop );

		if	($action	==	'save-changed') {
			$this->options		=	$prop;
			$this->options['user-agent-text']	=	LIST_USER_AGENT[$this->options['user-agent']];

			require ('pz-lkc3-settings-validate.php' );	// 設定値を検証

			$result_save		=	$this->pz_SaveOptions();	// 設定を保存
			$result_style		=	$this->pz_SetStyle();	// CSSを再生成
			$tab_now			=	isset($_POST['tab-now'] ) ? sanitize_text_field(wp_unslash($_POST['tab-now'] ) ) : '';
			$scroll_now			=	isset($_POST['scroll-now'] ) ? absint(wp_unslash($_POST['scroll-now'] ) ) : 0;

			$get_save	=	$result_save;
			$get_style	=	$result_style;
			$get_tab	=	$tab_now;
			$get_scroll	=	$scroll_now;
		}
	}

	// URLパラメーターを取得
	$get_save	=	$get_save		??	(isset($_GET['save'] )		?	absint(wp_unslash($_GET['save'] ) )									:	null );
	$get_style	=	$get_style		??	(isset($_GET['style'] )		?	sanitize_text_field(wp_unslash($_GET['style'] ) )			:	null );
	$get_tab	=	$get_tab			??	(isset($_GET['tab'] )		?	sanitize_text_field(wp_unslash($_GET['tab'] ) )				:	null );
	$get_scroll	=	$get_scroll	??	(isset($_GET['scroll'] )	?	absint(wp_unslash($_GET['scroll'] ) )							:	null );
	if	(null !== $get_save && null !== $get_style ) {
		if	($get_save ) {	// Settings were saved.
			/* translators: %s: 設定を保存した日時 */
			$html_notice	.=	$make_settings_notice('success', esc_html(sprintf(__('Settings saved. (%s)', 'pz-linkcard3' ), wp_date($this->format_datetime ) ) ) );
		} else {
			$html_notice	.=	$make_settings_notice('info', esc_html(__('The settings have not changed.', 'pz-linkcard3' ) ) );
		}
		$html_notice	.=	$make_style_notice($get_style );

		if	(null !== $get_tab ) {
			$tab_now		=	esc_attr($get_tab );
		}
	}

	foreach	(self::DEFAULTS		as	$key => $value ) {
		if	(!array_key_exists($key,		$this->options ) ) {
			$this->options[$key]	=	$value['value'];
			if	($this->env_local && $prop ) {
				/* translators: %1$s: 未定義の設定キー、%2$s: 開発者への連絡先リンク */
				$html_notice	.=	'<div class="notice notice-error is-dismissible">'.sprintf(__('Undefined key "%1$s" in properties.<br>This may be a bug. Please inform the developer. (%2$s)', 'pz-linkcard3' ), esc_html($key ), '<a href="https://x.com/'. esc_url($this->author_twitter_name ) .'" target="_blank">@'.esc_html($this->author_twitter_name ).'</a>' ).'</div>';
			}
		}
	}
			
	if	( ($this->options['admin-mode'] ) && ($prop ) ) {
		foreach	($prop as $key => $value ) {
			if	(!array_key_exists($key, self::DEFAULTS ) ) {
				/* translators: %1$s: 未定義の初期値キー、%2$s: 開発者への連絡先リンク */
				$html_notice	.=	'<div class="notice notice-error is-dismissible">'.sprintf(__('Undefined key "%1$s" in self::DEFAULTS.<br>This may be a bug. Please inform the developer. (%2$s)', 'pz-linkcard3' ), esc_html($key ), '<a href="https://x.com/'. esc_url($this->author_twitter_name ) .'" target="_blank">'.esc_html($this->author_twitter_name ).'</a>' ).'</div>';
			}
		}
	}

	$page				=	'pz-lkc-settings';	// 設定画面のページスラッグ
	$tab_now			=	null !== $get_tab										?	esc_attr($get_tab )										:
									(isset($_POST['tab-now'] )					?	sanitize_text_field(wp_unslash($_POST['tab-now'] ) )		:	'pz-error' );
	if	(in_array($action, array('init-settings', 'init-cache' ), true ) ) {
		$tab_now		=	'pz-initialize';
	}
	$scroll_now			=	null !== $get_scroll									?	$get_scroll											:
									(isset($_POST['scroll-now'] )				?	absint(wp_unslash($_POST['scroll-now'] ) )			:	null );
	$admin_mode			=	isset($this->options['admin-mode'] )		?	intval($this->options['admin-mode'] )		:	0 ;
	$product_mode		=	isset($this->options['product-mode'] )		?	intval($this->options['product-mode'] )		:	0 ;
	$debug_mode			=	isset($this->options['debug-mode'] )		?	intval($this->options['debug-mode'] )		:	0 ;
	$additional_mode	=	isset($this->options['additional-mode'] )	?	intval($this->options['additional-mode'] )	:	0 ;
	$log_mode			=	isset($this->options['log-mode'] )			?	intval($this->options['log-mode'] )			:	0 ;
	$multi_mode			=	isset($this->options['multi-mode'] )		?	intval($this->options['multi-mode'] )		:	0 ;
	$menu_error			=	isset($this->options['error-mode'] )		?	intval($this->options['error-mode'] )		:	0 ;

	$admin_mode			=	isset($prop['admin-mode'] )					?	intval($prop['admin-mode'] )				:	$admin_mode ;
	$product_mode		=	isset($prop['product-mode'] )				?	intval($prop['product-mode'] )				:	$product_mode ;
	$debug_mode			=	isset($prop['debug-mode'] )					?	intval($prop['debug-mode'] )				:	$debug_mode ;
	$additional_mode	=	isset($prop['additional-mode'] )			?	intval($prop['additional-mode'] )			:	$additional_mode ;
	$log_mode			=	isset($prop['log-mode'] )					?	intval($prop['log-mode'] )					:	$log_mode ;
	$multi_mode			=	isset($prop['multi-mode'] )					?	intval($prop['multi-mode'] )				:	$multi_mode ;
	$menu_error			=	isset($prop['error-mode'] )					?	intval($prop['error-mode'] )				:	$menu_error ;

	$is_multisite		=	function_exists('is_multisite' )			?	is_multisite()								:	false ;
	$is_subdomain		=	function_exists('is_subdomain_install' )	?	is_subdomain_install()						:	false ;
	$multi_myid			=	function_exists('get_current_blog_id' )		?	get_current_blog_id()						:	0 ;
	$multi_count		=	0;
	if	($is_multisite ) {
		$j					=	0;
		for ($i = 1; $i <= 1000; $i++ ) {
			$multi_detail	=	get_blog_details($i );
			if	(!$multi_detail ) {
				break;
			}
			if	(!$multi_detail->deleted ) {
				$j++;
				$multi_count				=	$j;
				$multi[$j]['card_id']		=	$multi_detail->blog_id;
				$multi[$j]['name']			=	$multi_detail->blogname;
				$multi[$j]['url']			=	$multi_detail->home;
				$multi[$j]['domain']		=	preg_replace('/.*\/\/(.*)/', '$1', $multi_detail->home );
				$multi[$j]['registered']	=	$multi_detail->registered;
				$multi[$j]['post_count']	=	$multi_detail->post_count;
			}
		}
	}
	if	($is_multisite || $multi_mode ) {
		$menu_multi			=	1;
	} else {
		$menu_multi			=	0;
	}

	$visible_tabs		=	array('pz-basic', 'pz-position', 'pz-display', 'pz-letter', 'pz-external', 'pz-internal', 'pz-check', 'pz-editor', 'pz-advanced', 'pz-etc', 'pz-initialize' );
	if	($menu_error ) {
		array_unshift($visible_tabs, 'pz-error' );
	}
	if	($menu_multi ) {
		$visible_tabs[]		=	'pz-multisite';
	}
	if	($admin_mode ) {
		$visible_tabs[]		=	'pz-admin';
	}
	$active_tab			=	in_array($tab_now, $visible_tabs, true ) ? $tab_now : 'pz-basic';
	$tab_now			=	$active_tab;

	$pz_url			=	$this->author_url;
	$pz_url_info	=	$this->Pz_GetURLInfo($pz_url );
	$pz_domain		=	$pz_url_info['domain'];
	$pz_domain_url	=	$pz_url_info['domain_url'];

	$help_icon		=	'<a class="pz-help-icon" href="'.esc_url($pz_url.'/pz-linkcard3-settings%s' ).'" rel="external noopener help" target="_blank"><img src="'.esc_url($this->base_url.'img/help.png' ).'" title="'.esc_attr__('Help', 'pz-linkcard3' ).'" alt="help"></a>';

	$logo_pz		=	'<img src="'.esc_url($this->base_url.'img/icon_popozure.ico' ).'" width="16" height="16" alt="'.esc_attr__('Popozure Logo', 'pz-linkcard3' ).'">';
	$logo_pz_lkc	=	'<img src="'.esc_url($this->base_url.'img/icon-pz-linkcard.png' ).'" width="16" height="16" alt="'.esc_attr__('Pz-LinkCard Logo', 'pz-linkcard3' ).'">';
	$logo_wp		=	'<img src="'.esc_url($this->base_url.'img/icon_WordPress.png' ).'" width="16" height="16" alt="'.esc_attr__('WordPress.org Logo', 'pz-linkcard3' ).'">';
	$logo_tw		=	'<img src="'.esc_url($this->base_url.'img/icon_tw.png' ).'" width="16" height="16" alt="'.esc_attr__('Twitter Logo', 'pz-linkcard3' ).'">';
	$logo_x			=	'<img src="'.esc_url($this->base_url.'img/icon_x.png' ).'" width="16" height="16" alt="'.esc_attr__('X Logo', 'pz-linkcard3' ).'">';
	$logo_az		=	'<img src="'.esc_url($this->base_url.'img/icon_amazon.png' ).'" width="16" height="16" alt="'.esc_attr__('Amazon Logo', 'pz-linkcard3' ).'">';

	$html_mode		=	($this->env_product		?	'<span class="pz-infobar-env pz-infobar-env-product">'.	esc_html__('PRODUCT',	'pz-linkcard3' ).'</span>'	:	'' ).
						($this->env_develop		?	'<span class="pz-infobar-env pz-infobar-env-develop">'.	esc_html__('DEVELOPMENT',	'pz-linkcard3' ).'</span>'	:	'' ).
						($this->env_local		?	'<span class="pz-infobar-env pz-infobar-env-local">'.	esc_html__('LOCAL',		'pz-linkcard3' ).'</span>'	:	'' ).
						($debug_mode			?	'<span class="pz-infobar-env pz-infobar-env-debug">'.	esc_html__('DEBUG',		'pz-linkcard3' ).'</span>'	:	'' ).
						($log_mode				?	'<span class="pz-infobar-env pz-infobar-env-log">'.		esc_html__('LOG',		'pz-linkcard3' ).'</span>'	:	'' ).
						($admin_mode			?	'<span class="pz-infobar-env pz-infobar-env-admin">'.	esc_html__('ADMIN',		'pz-linkcard3' ).'</span>'	:	'' );

	$switch_link	=	esc_url($this->cacheman_url );
	$switch_icon	=	'<span class="dashicons dashicons-excerpt-view" style="vertical-align: text-bottom;"></span>';
	$switch_label	=	esc_html__('Manager', 'pz-linkcard3' );
	$switch_title	=	esc_attr__('Switch to the Cache Manager screen', 'pz-linkcard3' );
	$html_switch	=	'<a href="'.$switch_link.'" class="pz-infobar-switch" title="'.$switch_title.'"><span class="pz-infobar-switch-icon">'.$switch_icon.'</span><span class="pz-infobar-switch-label">'.$switch_label.'</span></a>';

	$html_search	=	'<label><input type="checkbox" class="pz-preview-checkbox">'.esc_html__('Preview', 'pz-linkcard3' ).'</label>'.
						'<div id="pz-infobar-search"><div class="pz-infobar-search-box"><input type="text" id="pz-search-box" class="pz-infobar-search-text" placeholder="'.esc_attr__('Input keyword', 'pz-linkcard3' ).'"><span id="pz-search-status"></span></div><button type="button" id="pz-search-btn" class="pz-infobar-search-button" data-no-overlay="1">'.esc_html__('Search', 'pz-linkcard3' ).'</button></div>';

	$html_infobar_logo	=	'<a href="'.esc_url($this->settings_url ).'" class="pz-infobar-plugin-logo"><img src="'.esc_url($this->base_url.'assets/pz-linkcard3_logo.svg' ).'" height="32" alt="Pz-LinkCard3" /></a>';
	$html_infobar	=	'<div id="pz-infobar"><div class="pz-infobar-left"><div class="pz-infobar-plugin">'.$html_infobar_logo.'<span class="pz-infobar-plugin-ver pz-monospace">ver.'.esc_html($this->plugin_version ).'</span>'.$html_mode.'</div></div><div class="pz-infobar-right">'.$html_search.$html_switch.'</div></div>';

	$title_icon		=	'<span class="dashicons dashicons-admin-generic" style="vertical-align: bottom; width: 32px; height: 32px; font-size: 32px;"></span>';
	$title_label	=	__('Pz-LinkCard Settings', 'pz-linkcard3' );
	$html_title		=	'<h1 class="pz-title"><span class="pz-title-line"><span class="pz-refresh"><span class="pz-header-title-icon">'.$title_icon.'<span class="pz-header-title-text">'.$title_label.'</span></span>'.wp_kses_post(sprintf($help_icon, esc_attr('' ) ) ).'</span></h1>';

	$temp_param		=	array(
		array('name' => 'tab-now',						'type' => 'text',		'value' => $tab_now,			'desc' => 'tab-now' ),
		array('name' => 'scroll-now',					'type' => 'text',		'value' => $scroll_now,			'desc' => 'scroll-now' ),
		array('name' => 'properties[debug-mode]',		'type' => 'checkbox',	'value' => $debug_mode,			'desc' => 'debug-mode' ),
		array('name' => 'properties[additional-mode]',	'type' => 'checkbox',	'value' => $additional_mode,	'desc' => 'additional-mode' ),
		array('name' => 'properties[log-mode]',			'type' => 'checkbox',	'value' => $log_mode,		'desc' => 'log-mode' ),
		array('name' => 'properties[admin-mode]',		'type' => 'checkbox',	'value' => $admin_mode,			'desc' => 'admin-mode' ),
		array('name' => 'properties[multi-mode]',		'type' => 'checkbox',	'value' => $multi_mode,			'desc' => 'multi-mode' ),
	);
	$html_input		.=	'<div class="pz-variable-list pz-hidden">';
	foreach	($temp_param as $temp_item ) {
		$temp_name	=	$temp_item['name'];
		$temp_type	=	$temp_item['type'];
		$temp_value	=	$temp_item['value'];
		$temp_desc	=	$temp_item['desc'];

		if	('checkbox' === $temp_type ) {
			$html_input	.=	'&ensp;<label><input type="checkbox" name="'.esc_attr($temp_name ).'" value="1" '.($temp_value ? 'checked' : '').' title="'.esc_attr($temp_name ).'" class="pz-sync" size="8">'.esc_html($temp_desc ).'</label>';
			continue;
		}

		$html_input	.=	'&ensp;'.esc_html($temp_desc ).'=<input type="text" name="'.esc_attr($temp_name ).'" value="'.esc_attr($temp_value ).'" title="'.esc_attr($temp_name ).'" size="8"> ';
	}
	$html_input		.=	'<button type="button" class="pz-variable-list-close" aria-label="Close"></button>';
	$html_input		.=	'</div>';

	$html_style		.=	'#pz-settings:not(.pz-debug-mode-enabled) .pz-debug-only{display:none!important;}';
	$html_style		.=	'#pz-settings:not(.pz-additional-mode-enabled) .pz-additional-only{display:none!important;}';
	$html_style		.=	'#pz-settings:not(.pz-log-mode-enabled) .pz-log-only{display:none!important;}';
	$html_style		.=	'#pz-settings:not(.pz-admin-mode-enabled) .pz-admin-only{display:none!important;}';
	$html_style		.=	'#pz-settings:not(.pz-multi-mode-enabled) .pz-multi-only{display:none!important;}';
	$html_style		.=	'#pz-settings:not(.pz-develop-mode-enabled) .pz-develop-only{display:none!important;}';
	if	($scroll_now ) {
		$html_style		.=	'html.pz-lkc3-restoring-scroll #wpbody-content{visibility:hidden;}';
		$html_scroll_restore	=	'<script>(function(top){'
			.	'if(!top)return;'
			.	'var root=document.documentElement;'
			.	'var done=false;'
			.	'var tries=0;'
			.	'var maxTries=40;'
			.	'var show=function(){if(done)return;done=true;root.classList.remove("pz-lkc3-restoring-scroll");};'
			.	'var getY=function(){return window.scrollY||document.documentElement.scrollTop||document.body.scrollTop||0;};'
			.	'var restore=function(){'
			.		'if("scrollRestoration" in history)history.scrollRestoration="manual";'
			.		'window.scrollTo(0,top);'
			.		'var maxTop=Math.max(0,document.documentElement.scrollHeight-window.innerHeight);'
			.		'var target=Math.min(top,maxTop);'
			.		'if(maxTop>=top&&Math.abs(getY()-target)<2)show();'
			.	'};'
			.	'var tick=function(){tries++;restore();if(!done&&tries<maxTries&&window.requestAnimationFrame)window.requestAnimationFrame(tick);};'
			.	'root.classList.add("pz-lkc3-restoring-scroll");'
			.	'restore();'
			.	'if(window.requestAnimationFrame)window.requestAnimationFrame(tick);'
			.	'[0,16,33,50,100,150,250,350,600].forEach(function(delay){window.setTimeout(restore,delay);});'
			.	'window.addEventListener("load",function(){restore();window.setTimeout(show,50);},{once:true});'
			.	'window.addEventListener("pageshow",restore,{once:true});'
			.	'window.setTimeout(show,900);'
			.	'})('.wp_json_encode(absint($scroll_now ) ).');</script>';
	}

	if	($html_style ) {
		$html_style	=	'<style type="text/css">'.$html_style.'</style>';
	}

	// エラー通知の表示
	if	($this->options['error-mode'] ) {
		if	(!$this->options['error-mode-hide'] ) {
			$error_scroll_url	=	$this->options['error-url'] ? add_query_arg('pz_lkc3_scroll', 'lkc3-error', $this->options['error-url'] ) : '';
			/* translators: %s: 設定画面のURL */
			$html_notice	.=	'<div class="notice notice-error is-dismissible"><p><strong>'.esc_html($this->plugin_name ).': '.__('Invalid URL parameter in ', 'pz-linkcard3' ).'<a href="'.esc_url($error_scroll_url ).'" target="_blank">'.esc_html($this->options['error-url'] ).'</a></strong><br>'.__('*', 'pz-linkcard3' ).' '.sprintf(__('You can cancel this message from <a href="%s">the setting screen</a>.', 'pz-linkcard3' ), esc_url($this->settings_url ) ).'</p></div>';
		}
	}

	if	($this->options['plugin-version']	<>	$this->plugin_version ) {
		$html_notice		.=	$make_settings_notice('info', esc_html(__('The plugin may have been updated.', 'pz-linkcard3' ) ) );
		$flg_style			=	true;
	}

	if	($action ) {
		switch	($action ) {
		case	'init-plugin':
			$this->hook_activate();
			$flg_style			=	true;
			break;

		case	'init-settings':
			$result		=	$this->pz_InitializeOptions();
			if	($result ) {
				$flg_style		=	true;
				$prop		=	$this->options;
				$html_notice	.=	$make_settings_notice('success', esc_html(__('Settings initialized successfully.', 'pz-linkcard3' ) ) );
			} else {
				$html_notice	.=	$make_settings_notice('error', esc_html(__('Failed to initialize the settings.', 'pz-linkcard3' ) ) );
			}
			break;

		case	'clear-error':
			$flg_style			=	false;
			$menu_error			=	0;		
			$this->options['error-mode']	=	0;
			$result	=	$this->pz_SaveOptions();	// 設定を保存
			break;

		case	'clear-log':
			$result		=	remove_directory($this->dir_debug );
			break;

		case	'clear-image':
			$result		=	remove_directory($this->dir_cache );
			break;

		case	'init-cache':
			$flg_style	=	false;
			
			$result		=	$wpdb->query($wpdb->prepare('DELETE FROM %i', $this->db_card ) );
			if	($result !== false ) {
				
				$wpdb->query($wpdb->prepare('ALTER TABLE %i AUTO_INCREMENT=1', $this->db_card ) );
				$html_notice	.=	$make_settings_notice('success', esc_html(__('Cache initialized successfully.', 'pz-linkcard3' ) ) );
			} else {
				$html_notice	.=	$make_settings_notice('error', esc_html(__('Failed to initialize cache.', 'pz-linkcard3' ) ) );
			}
			break;

		case	'run-'.$this->cron_regist:
			$flg_style			=	false;
			break;

		case	'run-'.$this->cron_alive:
			$flg_style			=	false;
			break;

		case	'run-'.$this->cron_sns:
			$flg_style			=	false;
			break;

		default:

		}
	}

	// 必要に応じてCSSを再生成
	if	($flg_style ) {
		$result		=	$this->pz_SetStyle();
		$html_notice	.=	$make_style_notice($result );
	}

	// 変更履歴を読み込んで表示用HTMLへ整形
	$changelog		=	'';
	if	(!function_exists('wp_is_mobile' ) || !wp_is_mobile() ) {
		$wp_filesystem	=	$this->pz_GetFilesystem();
		$changelog	=	$wp_filesystem ? $wp_filesystem->get_contents($this->base_path.'readme.txt' ) : '';	// readme.txtを読み込む
		if	($changelog === false ) {
			$changelog	=	'';
		}
		preg_match('/== Changelog ==[^=]*(=\s*[^=]*\s*=[^=]*=\s*[^=]*\s*=[^=]*=\s*[^=]*\s*=[^=]*=\s*[^=]*\s*=[^=]*=\s*[^=]*\s*=[^=]*)/m', $changelog, $m );
		$changelog	=	$m[1] ?? '';
		$changelog	=	trim($changelog );
		$changelog	=	esc_html($changelog );
		$changelog	=	preg_replace('/^\* (.*)$/mi',				'<span class="pz-log-ja">'.__('・', 'pz-linkcard3' ).'$1</span>',		$changelog);	// 日本語文の行のインデント調整
		$changelog	=	preg_replace('/^  (.*)$/mi',				'<span class="pz-log-en">&ensp;&ensp;$1</span>',						$changelog);	// 英文の行のインデント調整
		$changelog	=	preg_replace('/= (.*) =\n/i',				'<h4>'.__('Version', 'pz-linkcard3' ).' $1</h4>',						$changelog);	// バージョン番号の表記調整
		$changelog	=	preg_replace('/(\[added\]\s*)|(&ensp;&ensp;added:\s*)/i',		'&ensp;&ensp;<span class="pz-log-added">'.		__('Added', 'pz-linkcard3' ).	'</span>&ensp;',	$changelog);	// 追加
		$changelog	=	preg_replace('/(\[fixed]\s*)|(&ensp;&ensp;fixed:\s*)/i',		'&ensp;&ensp;<span class="pz-log-fixed">'.		__('Fixed', 'pz-linkcard3' ).	'</span>&ensp;',	$changelog);	// 修正
		$changelog	=	preg_replace('/(\[modified\]\s*)|(&ensp;&ensp;modified:\s*)/i',	'&ensp;&ensp;<span class="pz-log-modified">'.	__('Modified', 'pz-linkcard3' ).'</span>&ensp;',	$changelog);	// 変更
		$changelog	=	preg_replace('/(\[removed\]\s*)|(&ensp;&ensp;removed:\s*)/i',	'&ensp;&ensp;<span class="pz-log-removed">'.	__('Removed', 'pz-linkcard3' ).	'</span>&ensp;',	$changelog);	// 修正
		$changelog	=	preg_replace('/(\[tested\]\s*)|(&ensp;&ensp;tested:\s*)/i',		'&ensp;&ensp;<span class="pz-log-tested">'.		__('Tested', 'pz-linkcard3' ).	'</span>&ensp;',	$changelog);	// テスト
		$changelog	=	preg_replace('/(\[issue\]\s*)|(&ensp;&ensp;issue:\s*)/i',		'&ensp;&ensp;<span class="pz-log-issue">'.		__('Issue', 'pz-linkcard3' ).	'</span>&ensp;',	$changelog);	// 不具合など
		$changelog	=	preg_replace('~\x{FF08}Thanks\s+([^\s@]*)\s*(@[^\s]*)\s+on x\.com\x{FF09}~iu',			'<a href="https://x.com/$2" class="pz-thx" rel="external noopener noreferrer" target="_blank">'.						'Thanks<span class="pz-thx-name">$1</span>'.$logo_x. '<span class="pz-thx-account">$2</span></a>', $changelog);
		$changelog	=	preg_replace('~\x{FF08}Thanks\s+([^\s@]*)\s*(@[^\s]*)\s+on twitter\.com\x{FF09}~iu',	'<a href="https://twitter.com/$2" class="pz-thx" rel="external noopener noreferrer" target="_blank">'.					'Thanks<span class="pz-thx-name">$1</span>'.$logo_tw.'<span class="pz-thx-account">$2</span></a>', $changelog);
		$changelog	=	preg_replace('~\x{FF08}Thanks\s+([^\s@]*)\s*(@[^\s]*)\s+on wordpress\.org\x{FF09}~iu',	'<a href="https://wordpress.org/support/users/$2" class="pz-thx" rel="external noopener noreferrer" target="_blank">'.	'Thanks<span class="pz-thx-name">$1</span>'.$logo_wp.'<span class="pz-thx-account">$2</span></a>', $changelog);
		$changelog	=	preg_replace('~\x{FF08}Thanks\s+([^\s@]*)\s*(#[^\s]*)\s+on popozure\.info\x{FF09}~iu',	'<a href="'.$pz_url.'" class="pz-thx" rel="external noopener noreferrer" target="_blank">'.								'Thanks<span class="pz-thx-name">$1</span>'.$logo_pz.'<span class="pz-thx-account">$2</span></a>', $changelog);
		$changelog	=	str_replace("\r\n",		'<br>',	$changelog );															// 改行をBRタグに変換
		$changelog	=	str_replace("\r",		'<br>',	$changelog );															// 改行をBRタグに変換
		$changelog	=	str_replace("\n",		'<br>',	$changelog );															// 改行をBRタグに変換
		$changelog	=	'<div class="pz-basic-changelog">'.$changelog.'</div>';
	} else {
		$changelog	=	'<span class="pz-log-ja">'.__('&#x1F539;&#xFE0F;', 'pz-linkcard3' ).__('Not displayed in mobile environments.', 'pz-linkcard3' ).'</span>';
	}

	$html_preview	=	'<div id="pz-preview-container"><div id="pz-resize-handle">'.esc_html__('Preview', 'pz-linkcard3' ).'</div><button type="button" id="pz-preview-mode" aria-label="Preview" data-no-overlay="1">-</button><button type="button" id="pz-preview-close" aria-label="Close" data-no-overlay="1">×</button><div id="pz-lkc3-preview"></div></div>';

	
	$result			=	$wpdb->get_results($wpdb->prepare('SELECT meta_key FROM %i WHERE SUBSTRING(meta_key, 1, 1 ) <> %s GROUP BY meta_key ORDER BY meta_key ASC', $wpdb->postmeta, '_' ), ARRAY_N );
	$meta_list		=	array();
	foreach	($result as $item ) {
		$meta_list[$item[0]]	=	$item[0];
	}
	ksort($meta_list );

	$prop		=	$this->options;

	$show_error			=	'';
	$show_basic			=	'';
	$show_position		=	'';
	$show_display		=	'';
	$show_letter		=	'';
	$show_external		=	'';
	$show_internal		=	'';
	$show_check			=	'';
	$show_editor		=	'';
	$show_multisite		=	'';
	$show_advanced		=	'';
	$show_etc			=	'';
	$show_initialize	=	'';
	$tab_class			=	function($name, $classes = '' ) use ($active_tab ) {
		return	trim('pz-tab '.trim($classes ).($active_tab === $name ? ' pz-tab-active' : '' ) );
	};
	$page_class			=	function($name, $classes = '' ) use ($active_tab ) {
		return	trim('pz-page '.trim($classes ).($active_tab === $name ? ' pz-page-active' : '' ) );
	};

	if	(isset($prop['flg-inhibit'] ) ? $prop['flg-inhibit'] : $this->options['flg-inhibit'] ) {
		$html_overlay	=	'<div id="pz-overlay-proc" style="display: none !important;"><div class="pz-loader"></div></div>';
	}
	$settings_allowed_html	=	array_merge(
		wp_kses_allowed_html('post' ),
		array(
			'input'		=>	array(
				'id'		=>	true,
				'type'		=>	true,
				'name'		=>	true,
				'value'		=>	true,
				'title'		=>	true,
				'size'		=>	true,
				'class'		=>	true,
				'checked'	=>	true,
				'placeholder'	=>	true,
			),
			'button'	=>	array(
				'id'			=>	true,
				'type'			=>	true,
				'class'			=>	true,
				'aria-label'	=>	true,
				'data-no-overlay'	=>	true,
			),
			'style'		=>	array(
				'type'	=>	true,
			),
		)
	);

	echo	wp_kses($html_style, $settings_allowed_html );
	echo	$html_scroll_restore; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo	wp_kses($html_infobar, $settings_allowed_html );
	echo	wp_kses($html_overlay, $settings_allowed_html );
	$settings_class	=	'pz-dashboard pz-settings wrap';
	$settings_class	.=	$debug_mode			?	' pz-debug-mode-enabled'			:	'';
	$settings_class	.=	$additional_mode	?	' pz-additional-mode-enabled'	:	'';
	$settings_class	.=	$log_mode			?	' pz-log-mode-enabled'			:	'';
	$settings_class	.=	$admin_mode			?	' pz-admin-mode-enabled'			:	'';
	$settings_class	.=	$multi_mode			?	' pz-multi-mode-enabled'			:	'';
	$settings_class	.=	$this->env_develop	?	' pz-develop-mode-enabled'		:	'';
?>
<div id="pz-settings" class="<?php echo esc_attr($settings_class ); ?>">
	<header class="pz-header">
		<?php
			echo	wp_kses($html_title, $settings_allowed_html );
			echo	wp_kses($html_notice, $settings_allowed_html );
		?>
	</header>

	<div id="pz-tabbar-wrapper">
		<button type="button" class="pz-tab-scroll pz-tab-left"  aria-label="left" style="cursor: pointer;" data-no-overlay="1">&lt;</button>
		<button type="button" class="pz-tab-scroll pz-tab-right" aria-label="right" style="cursor: pointer;" data-no-overlay="1">&gt;</button>
		<div id="pz-tabbar" class="pz-tabbar">
			<a class="<?php echo esc_attr($tab_class('pz-error', 'pz-red'.($menu_error ? '' : ' pz-hidden' ) ) ); ?>"	name="pz-error"			href="#pz-error"		<?php echo esc_attr($show_error );			?>><?php esc_html_e('Error',			'pz-linkcard3' ); ?></a>
			<a class="<?php echo esc_attr($tab_class('pz-basic' ) ); ?>"			name="pz-basic"			href="#pz-basic"		<?php echo esc_attr($show_basic );			?>><?php esc_html_e('Basic',			'pz-linkcard3' ); ?></a>
			<a class="<?php echo esc_attr($tab_class('pz-position' ) ); ?>"			name="pz-position"		href="#pz-position"		<?php echo esc_attr($show_position );		?>><?php esc_html_e('Position',			'pz-linkcard3' ); ?></a>
			<a class="<?php echo esc_attr($tab_class('pz-display' ) ); ?>"			name="pz-display"		href="#pz-display"		<?php echo esc_attr($show_display );		?>><?php esc_html_e('Display',			'pz-linkcard3' ); ?></a>
			<a class="<?php echo esc_attr($tab_class('pz-letter' ) ); ?>"			name="pz-letter"		href="#pz-letter"		<?php echo esc_attr($show_letter );		?>><?php esc_html_e('Letter',			'pz-linkcard3' ); ?></a>
			<a class="<?php echo esc_attr($tab_class('pz-external' ) ); ?>"		name="pz-external"		href="#pz-external"		<?php echo esc_attr($show_external );		?>><?php esc_html_e('External Link',	'pz-linkcard3' ); ?></a>
			<a class="<?php echo esc_attr($tab_class('pz-internal' ) ); ?>"		name="pz-internal"		href="#pz-internal"		<?php echo esc_attr($show_internal );		?>><?php esc_html_e('Internal Link',	'pz-linkcard3' ); ?></a>
			<a class="<?php echo esc_attr($tab_class('pz-check' ) ); ?>"			name="pz-check"			href="#pz-check"		<?php echo esc_attr($show_check );			?>><?php esc_html_e('Link Check',		'pz-linkcard3' ); ?></a>
			<a class="<?php echo esc_attr($tab_class('pz-editor' ) ); ?>"			name="pz-editor"		href="#pz-editor"		<?php echo esc_attr($show_editor );		?>><?php esc_html_e('Editor',			'pz-linkcard3' ); ?></a>
			<a class="<?php echo esc_attr($tab_class('pz-multisite', 'pz-orange'.($is_multisite ? '' : ' pz-multi-only' ).($menu_multi ? '' : ' pz-hidden' ) ) ); ?>"	name="pz-multisite"		href="#pz-multisite"	<?php echo esc_attr($show_multisite );		?>><?php esc_html_e('Multi Site',		'pz-linkcard3' ); ?></a>
			<a class="<?php echo esc_attr($tab_class('pz-advanced' ) ); ?>"		name="pz-advanced"		href="#pz-advanced"		<?php echo esc_attr($show_advanced );		?>><?php esc_html_e('Advanced',			'pz-linkcard3' ); ?></a>
			<a class="<?php echo esc_attr($tab_class('pz-etc' ) ); ?>"			name="pz-etc"			href="#pz-etc"			<?php echo esc_attr($show_etc );			?>><?php esc_html_e('etc.',				'pz-linkcard3' ); ?></a>
			<a class="<?php echo esc_attr($tab_class('pz-initialize' ) ); ?>"		name="pz-initialize"	href="#pz-initialize"	<?php echo esc_attr($show_initialize );	?>><?php esc_html_e('Initialize',		'pz-linkcard3' ); ?></a>
			<a class="<?php echo esc_attr($tab_class('pz-admin', 'pz-purple pz-admin-only'.($admin_mode ? '' : ' pz-hidden' ) ) ); ?>" name="pz-admin"			href="#pz-admin"><?php esc_html_e('Admin',			'pz-linkcard3' ); ?></a>
		</div>
	</div>
	<article>
		<form id="pz-settings-form" action="" method="post">
			<?php wp_nonce_field('pz-settings' ); ?>
			<?php echo wp_kses($html_input, $settings_allowed_html ); ?>
			<div class="pz-submit-hide"><?php submit_button(); ?></div>
			<?php

				require ('pz-lkc3-settings-error.php' );	// エラー表示設定を読み込み

				require ('pz-lkc3-settings-basic.php' );	// 基本設定を読み込み
				require ('pz-lkc3-settings-position.php' );	// 配置設定を読み込み
				require ('pz-lkc3-settings-display.php' );	// 表示設定を読み込み
				require ('pz-lkc3-settings-letter.php' );	// 文字設定を読み込み
				require ('pz-lkc3-settings-card.php' );	// カード設定を読み込み
				require ('pz-lkc3-settings-check.php' );	// チェック設定を読み込み
				require ('pz-lkc3-settings-editor.php' );	// エディター設定を読み込み


				require ('pz-lkc3-settings-multisite.php' );	// マルチサイト設定を読み込み


				require ('pz-lkc3-settings-advanced.php' );	// 詳細設定を読み込み
				require ('pz-lkc3-settings-etc.php' );	// その他設定を読み込み
				require ('pz-lkc3-settings-initialize.php' );	// 初期化設定を読み込み


				require ('pz-lkc3-settings-admin.php' );	// 管理者向け設定を読み込み


?>
			<div class="pz-indicator"><div class="pz-button-top" title="<?php esc_attr_e('Scroll to the top', 'pz-linkcard3' ); ?>"><?php echo wp_kses_post(__('^<br>Top', 'pz-linkcard3' ) ); ?></div><div class="pz-tab-name">&nbsp;</div></div>
		</form>
		</article>

		<?php
			echo	wp_kses($html_preview, $settings_allowed_html );
		?>

</div>
<?php

// 数値だけを取り出す
function	pz_TrimNum($val ) {
	$val		=	mb_convert_kana($val, 'n' );
	$val		=	strtolower($val );
	$val		=	preg_replace('/[^0-9]/', '', $val );
	if	($val	<>	null) {
		$val	=	intval($val );
	}
	return	$val;
}

// 数値にpxまたは%の単位を付けて整形する
function	pz_TrimNumPx($val, $unit_percent = false ) {
	$val		=	mb_convert_kana($val, 'n' );	// 全角数字を半角へ変換
	$val		=	strtolower($val );	// 単位を小文字へ正規化
	$unit		=	'px';
	if (($unit_percent == true ) && (substr($val, -1 ) == '%' ) ) {
		$unit	=	'%';
	}
	$val		=	preg_replace('/[^0-9]/', '', $val );

	switch	($val ) {
	case	null:
	case	0:
		return	$val;
		break;
	}
	return		$val.$unit;
}

// カラーコードを検証して整形する
function	pz_CheckColorCode($val ) {
	if	(preg_match('/^#([0-9A-F]{6}|[0-9A-F]{3})$/i', $val ) ) {
		return true;
	}
}

// ディレクトリ配下のサイズを取得する
function	pz_GetDirSize($dir ) {
	$size		=	0;
	global	$wp_filesystem;
	if	(!($wp_filesystem instanceof WP_Filesystem_Base ) ) {
		require_once ABSPATH.'wp-admin/includes/file.php';
		if	(!WP_Filesystem() ) {
			return	null;
		}
	}
	if	(!$wp_filesystem->exists($dir ) ) {
		return	0;
	}
	$files	=	$wp_filesystem->dirlist(trailingslashit($dir ) );
	if	(!is_array($files ) ) {
		return	null;
	}
	foreach ($files as $file => $info ) {
		$fullpath	=	trailingslashit($dir ).$file;
		if	(($info['type'] ?? '') === 'd' ) {
			$size	+=	pz_GetDirSize($fullpath );
		} else {
			$size	+=	intval($info['size'] ?? 0 );
		}
	}
	return	$size;
}

// バイト数を読みやすい単位へ変換する
function	pz_GetSizeStringSi($val ) {
	$label	=	array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB' );
	for($i = 0; $val >= 1024 && $i < (count($label ) -1 ); $val /= 1024, $i++ );
	return	(round($val, 2 ).' '.$label[$i] );
}

// 文字列のバイト数を取得する
function	pz_GetStringBytes($val ) {
	if	($val	==	1 ) {
		return	number_format($val ).' byte';
	} else {
		return	number_format($val ).' bytes';
	}
}

// 
function		pz_Text($prop, $name ) {
	echo		'<input type="text" name="properties['.esc_attr($name ).']" value="'.esc_attr($prop[$name] ).'" >';
}

function	pz_lkc3_property_checkbox($prop, $name, $label, $class = '', $attrs = '' ) {
	$value		=	!empty($prop[$name] );
	echo	'<label>';
	echo	'<input type="hidden" name="properties['.esc_attr($name ).']" value="">';
	echo	'<input type="checkbox" name="properties['.esc_attr($name ).']" value="1"'.($class ? ' class="'.esc_attr($class ).'"' : '' ).' '.checked($value, true, false ).' '.wp_kses($attrs, array() ).'>';
	echo	wp_kses_post($label );
	echo	'</label>';
}

function	pz_lkc3_property_text($prop, $name, $args = array() ) {
	$type		=	$args['type'] ?? 'text';
	$value		=	$args['value'] ?? ($prop[$name] ?? '');
	$size		=	$args['size'] ?? '';
	$class		=	$args['class'] ?? '';
	$attrs		=	$args['attrs'] ?? '';
	$name_attr	=	$args['name'] ?? 'properties['.$name.']';
	echo	'<input name="'.esc_attr($name_attr ).'" type="'.esc_attr($type ).'" value="'.esc_attr($value ).'"'.($size !== '' ? ' size="'.esc_attr($size ).'"' : '' ).($class ? ' class="'.esc_attr($class ).'"' : '' ).' '.wp_kses($attrs, array() ).'>';
}

function	pz_lkc3_readonly_text($value, $size = 20, $class = 'pz-monospace' ) {
	echo	'<input type="text" value="'.esc_attr($value ).'" readonly="readonly" size="'.esc_attr($size ).'"'.($class ? ' class="'.esc_attr($class ).'"' : '' ).'>';
}

function	pz_lkc3_intro_card($link, $icon, $name, $description, $class, $dashicon = '' ) {
	echo	'<div class="pz-introduction-base"><a href="'.esc_url($link ).'" rel="external noopener" target="_blank" class="pz-introduction-card '.esc_attr($class ).'"><div class="pz-introduction-thumb">';
	if	($dashicon ) {
		echo	'<div class="dashicons '.esc_attr($dashicon ).' pz-introduction-dashicon"></div>';
	} else {
		echo	'<img src="'.esc_url($icon ).'" alt="'.esc_attr($name ).'" />';
	}
	echo	'</div><div class="pz-introduction-content"><div class="pz-introduction-title">'.esc_html($name ).'</div><div class="pz-introduction-description">'.esc_html($description ).'</div></div></a></div>';
}

function	pz_lkc3_table_exists_badge($table_name ) {
	global	$wpdb;
	
	$table_exists	=	$wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name ) );
	if	($table_exists === $table_name ) {
		echo	wp_kses_post(__('&#x2705;&#xFE0F;', 'pz-linkcard3' ) ).'<span style="color: #0a4 !important;">'.esc_html__('Exists', 'pz-linkcard3' ).'</span>';
	} else {
		echo	wp_kses_post(__('&#x1F6AB;&#xFE0F;', 'pz-linkcard3' ) ).'<span style="color: #d00 !important;">'.esc_html__('Not Found.', 'pz-linkcard3' ).'</span>';
	}
}

// 
function		echo_checkbox($item_name, $item_value, $item_list, $item_title, $item_notice, $item_class = '', $item_enabled = true ) {
	if			($item_enabled		==	false ) {
		echo		'<label>';
		echo		'<input type="checkbox"'.($item_class ? ' class="'.esc_attr($item_class ).'"' : '' ).' disabled="disabled">';
		echo		wp_kses_post($item_notice );
		echo		'</label>';
	} else {
		echo		'<label>';
		echo		'<input type="hidden"   name="properties['.esc_attr($item_name ).']" value="">';
		echo		'<input type="checkbox" name="properties['.esc_attr($item_name ).']" value="1"'.($item_class ? ' class="'.esc_attr($item_class ).'"' : '' ).' '.checked($item_value, '1', false ).'/>';
		echo		wp_kses_post($item_notice );
		echo		'</label>';
	}
}

function		echo_list($item_name, $item_value, $item_list, $item_title, $item_notice, $item_class = '', $item_enabled = true, $item_only = false, $item_prefix = '' ) {
	if	(!$item_only ) {
		echo	'<tr><th scope="row">'.wp_kses_post($item_title ).'</th><td>';
	}
	if	($item_prefix ) {
		echo	'<label>'.wp_kses_post($item_prefix );
	}
	if	($item_enabled	==	true ) {
		echo	'<select name="properties['.esc_attr($item_name ).']"'.($item_class ? ' class="'.esc_attr($item_class ).'"' : '' ).'>';
	} else {
		echo	'<select name="" disabled="disabled"'.($item_class ? ' class="'.esc_attr($item_class ).'"' : '' ).'>';
	}
	foreach		($item_list	as	$key => $value ) {
		echo	'<option value="'.esc_attr($key ).'" title="'.esc_attr($value ).'" '.selected($key, $item_value, false ).'>'.esc_html($value ).'</option>';
	}
	echo		'</select>';
	if	($item_prefix ) {
		echo	'</label>';
	}
	if	($item_notice ) {
		echo	'<br><span class="pz-note">'.esc_html($item_notice ).'</span>';
	}
	if	(!$item_only ) {
		echo	'</td></tr>';
	}
}
// function		echo_list($item_name, $item_value, $item_list, $item_title, $item_notice, $item_class = '', $item_enabled = true, $item_only = false, $item_prefix = '' ) {
// 	$html_result		=	'';
// 	if	(!$item_only ) {
// 		$html_result	.=	'<tr><th scope="row">'.wp_kses_post($item_title ).'</th><td>';
// 	}
// 	if	($item_prefix ) {
// 		$html_result	.=	'<label>'.wp_kses_post($item_prefix );
// 	}
// 	if	($item_enabled	==	true ) {
// 		$html_result	.=	'<select name="properties['.esc_attr($item_name ).']"'.($item_class ? ' class="'.esc_attr($item_class ).'"' : '' ).'>';
// 	} else {
// 		$html_result	.=	'<select name="" disabled="disabled"'.($item_class ? ' class="'.esc_attr($item_class ).'"' : '' ).'>';
// 	}
// 	foreach		($item_list	as	$key => $value ) {
// 		$html_result	.=	'<option value="'.esc_attr($key ).'" title="'.esc_attr($value ).'" '.selected($key, $item_value, false ).'>'.esc_html($value ).'</option>';
// 	}
// 	$html_result		.=	'</select>';
// 	if	($item_prefix ) {
// 		$html_result	.=	'</label>';
// 	}
// 	if	($item_notice ) {
// 		$html_result	.=	'<br><span class="pz-note">'.esc_html($item_notice ).'</span>';
// 	}
// 	if	(!$item_only ) {
// 		$html_result	.=	'</td></tr>';
// 	}
// 	echo					$html_result;
// }

function	echo_combo($item_name, $item_value, $item_list, $item_title, $item_notice, $item_class, $item_maxlength, $item_disabled, $item_only = false ) {
	$item_maxlength		=	intval($item_maxlength );
	$item_maxlength		=	$item_maxlength > 0 ? $item_maxlength : '';
	$item_class			=	trim('pz-combo '.$item_class );
	if	(!$item_only ) {
		echo	'<tr><th scope="row">'.wp_kses_post($item_title ).'</th><td>';
	}
	if	($item_disabled ) {
		echo	'<input type="text" name="" value="'.esc_attr($item_value ).'" disabled="disabled" class="'.esc_attr($item_class ).'">';
	} else {
		echo	'<input type="text" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" maxlength="'.esc_attr($item_maxlength ).'" list="datalist-'.esc_attr($item_name ).'" class="'.esc_attr($item_class ).'">';
	}
	echo		'<datalist id="datalist-'.esc_attr($item_name ).'">';
	foreach		($item_list	as	$key => $value ) {
		echo	'<option value="'.esc_attr($key ).'" '.selected($key, $item_value, false ).'>'.esc_html($value ).'</option>';
	}
	echo		'</datalist>';
	if	($item_notice ) {
		echo	'<br><span class="pz-note">'.esc_html($item_notice ).'</span>';
	}
	if	(!$item_only ) {
		echo	'</td></tr>';
	}
}
// function	echo_combo($item_name, $item_value, $item_list, $item_title, $item_notice, $item_class, $item_maxlength, $item_disabled, $item_only = false ) {
// 	$html_result		=	'';
// 	$item_maxlength		=	intval($item_maxlength );
// 	$item_maxlength		=	$item_maxlength > 0 ? $item_maxlength : '';
// 	$item_class			=	trim('pz-combo '.$item_class );
// 	if	(!$item_only ) {
// 		$html_result		=	'<tr><th scope="row">'.wp_kses_post($item_title ).'</th><td>';
// 	}
// 	if	($item_disabled ) {
// 		$html_result	.=	'<input type="text" name="" value="'.esc_attr($item_value ).'" disabled="disabled" class="'.esc_attr($item_class ).'">';
// 	} else {
// 		$html_result	.=	'<input type="text" name="properties['.esc_attr($item_name ).']" value="'.esc_attr($item_value ).'" maxlength="'.esc_attr($item_maxlength ).'" list="datalist-'.esc_attr($item_name ).'" class="'.esc_attr($item_class ).'">';
// 	}
// 	$html_result		.=	'<datalist id="datalist-'.esc_attr($item_name ).'">';
// 	foreach		($item_list	as	$key => $value ) {
// 		$html_result	.=	'<option value="'.esc_attr($key ).'" '.selected($key, $item_value, false ).'>'.esc_html($value ).'</option>';
// 	}
// 	$html_result		.=	'</datalist>';
// 	if	($item_notice ) {
// 		$html_result	.=	'<br><span class="pz-note">'.esc_html($item_notice ).'</span>';
// 	}
// 	if	(!$item_only ) {
// 		$html_result	.=	'</td></tr>';
// 	}
// 	echo					$html_result;
// }

function remove_directory($dir ) {
	if	(empty($dir ) ) {
		return;
	}
	global	$wp_filesystem;
	if	(!($wp_filesystem instanceof WP_Filesystem_Base ) ) {
		require_once ABSPATH.'wp-admin/includes/file.php';
		if	(!WP_Filesystem() ) {
			return	false;
		}
	}
	$dir	=	trailingslashit($dir );
	if	(!$wp_filesystem->exists($dir ) ) {
		return;
	}
	$files	=	$wp_filesystem->dirlist($dir );
	if	(!is_array($files ) ) {
		return	false;
	}
	foreach ($files as $file => $info ) {
		$path	=	$dir.$file;
		if	(($info['type'] ?? '') === 'd' ) {
			remove_directory($path );
		} else {
			if	(strtolower(substr($file, -4, 4 ) ) == '.log' ) {
				$wp_filesystem->delete($path );
			}
		}
	}
	return	$wp_filesystem->delete($dir, false, 'd' );
}

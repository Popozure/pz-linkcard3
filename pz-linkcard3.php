<?php if	(!defined( 'ABSPATH' ) ) { header( 'HTTP/1.1 403 Forbidden' ); exit; }
/*
Plugin Name:	Pz-LinkCard3
Plugin URI:		http://popozure.info/pz-linkcard3
Description:	リンクをカード形式で表示します。テキストだけのリンクとはさようなら！
Version:		3.0.0.TEST
Author:			Poporon
Author URI:		http://popozure.info
Text Domain:	pz-linkcard3
Domain Path:	/languages
License:		GPLv2 or later
*/

if (!class_exists('pz_linkcard3') ) {
	class pz_linkcard3 {
		// 設定値
		private		const	DEFAULTS	=
			array(
				'plugin-version'					=>	['type' => 'string',		'value' => '' ],
				'db-ver-card'						=>	['type' => 'string',		'value' => '' ],
				'db-ver-click'						=>	['type' => 'string',		'value' => '' ],

				'error-mode'						=>	['type' => 'flag',			'value' => 0 ],
				'error-hide'						=>	['type' => 'numeric',		'value' => 0 ],
				'error-url'							=>	['type' => 'string',		'value' => '' ],
				'error-postid'						=>	['type' => 'string',		'value' => '' ],
				'error-time'						=>	['type' => 'datetime',		'value' => '' ],

				'saved-date'						=>	['type' => 'datetime',		'value' => '' ],

				'enclose-tag'						=>	['type' => 'string',		'value' => 'div' ],
				'enclose-class-pc'					=>	['type' => 'string',		'value' => '' ],
				'enclose-class-mobile'				=>	['type' => 'string',		'value' => '' ],
				'link-all'							=>	['type' => 'flag',			'value' => 1 ],
				'thumbnail-resize'					=>	['type' => 'flag',			'value' => 1 ],
				'heading-margin-top'				=>	['type' => 'pixel',			'value' => '-16px' ],
				'heading-margin-left'				=>	['type' => 'pixel',			'value' => '16px' ],
				'heading-padding-h'					=>	['type' => 'pixel',			'value' => '16px' ],
				'heading-padding-v'					=>	['type' => 'pixel',			'value' => '4px' ],
				'margin-top'						=>	['type' => 'pixel',			'value' => '16px' ],
				'margin-bottom'						=>	['type' => 'pixel',			'value' => '32px' ],
				'margin-left'						=>	['type' => 'pixel',			'value' => '16px' ],
				'margin-right'						=>	['type' => 'pixel',			'value' => '16px' ],
				'padding-top'						=>	['type' => 'pixel',			'value' => '8px' ],
				'padding-bottom'					=>	['type' => 'pixel',			'value' => '8px' ],
				'padding-left'						=>	['type' => 'pixel',			'value' => '8px' ],
				'padding-right'						=>	['type' => 'pixel',			'value' => '8px' ],
				'info-position'						=>	['type' => 'string',		'value' => 'u' ],
				'siteicon-size'						=>	['type' => 'pixel',			'value' => '16px' ],
				'thumbnail-position'				=>	['type' => 'string',		'value' => 'l' ],
				'thumbnail-width'					=>	['type' => 'numeric_null',	'value' => 100 ],
				'thumbnail-height'					=>	['type' => 'numeric_null',	'value' => 100 ],
				'width'								=>	['type' => 'numeric_null',	'value' => 600 ],
				'width-unit'						=>	['type' => 'string',		'value' => 'px' ],
				'content-height'					=>	['type' => 'numeric_null',	'value' => 100 ],
				'content-margin'					=>	['type' => 'pixel',			'value' => '8px' ],

				'separator'							=>	['type' => 'flag',			'value' => 0 ],
				'content-inset'						=>	['type' => 'flag',			'value' => 0 ],
				'shadow-inset'						=>	['type' => 'numeric',		'value' => 0 ],
				'hover'								=>	['type' => 'numeric',		'value' => 1 ],
				'unlink-border-color'				=>	['type' => 'color',			'value' => '#dd3333' ],

				'sns-tw'							=>	['type' => 'flag',			'value' => 1 ],
				'sns-tw-old'						=>	['type' => 'flag',			'value' => 0 ],
				'sns-fb'							=>	['type' => 'flag',			'value' => 1 ],
				'sns-hb'							=>	['type' => 'flag',			'value' => 1 ],

				'post-date-style'					=>	['type' => 'numeric',		'value' => 1 ],
				'post-date-icon1'					=>	['type' => 'string',		'value' => '&#x1F4C5;&#xFE0F;' ],
				'post-date-icon2'					=>	['type' => 'string',		'value' => '&#x1F504;&#xFE0F;' ],
				'date-format'						=>	['type' => 'string',		'value' => 'Y.m.d' ],

				'title-color'						=>	['type' => 'color',			'value' => '#222222' ],
				'title-outline-color'				=>	['type' => 'string',		'value' => '' ],
				'title-bg-color'					=>	['type' => 'string',		'value' => '' ],
				'title-size'						=>	['type' => 'numeric_null',	'value' => 20 ],
				'title-height'						=>	['type' => 'numeric_null',	'value' => 24 ],
				'title-maxline'						=>	['type' => 'numeric_null',	'value' => 2 ],
				'title-bold'						=>	['type' => 'flag',			'value' => 1 ],
				'title-italic'						=>	['type' => 'flag',			'value' => 0 ],
				'title-underline'					=>	['type' => 'flag',			'value' => 0 ],
				'title-hover'						=>	['type' => 'flag',			'value' => 1 ],

				'excerpt-color'						=>	['type' => 'color',			'value' => '#444444' ],
				'excerpt-outline-color'				=>	['type' => 'string',		'value' => '' ],
				'excerpt-bg-color'					=>	['type' => 'string',		'value' => '' ],
				'excerpt-size'						=>	['type' => 'numeric_null',	'value' => 11 ],
				'excerpt-height'					=>	['type' => 'numeric_null',	'value' => 18 ],
				'excerpt-maxline'					=>	['type' => 'numeric_null',	'value' => 2 ],
				'excerpt-bold'						=>	['type' => 'flag',			'value' => 0 ],
				'excerpt-italic'					=>	['type' => 'flag',			'value' => 0 ],
				'excerpt-underline'					=>	['type' => 'flag',			'value' => 0 ],
				'excerpt-hover'						=>	['type' => 'flag',			'value' => 0 ],

				'url-color'							=>	['type' => 'color',			'value' => '#4466ff' ],
				'url-outline-color'					=>	['type' => 'string',		'value' => '' ],
				'url-bg-color'						=>	['type' => 'string',		'value' => '' ],
				'url-size'							=>	['type' => 'numeric_null',	'value' => 10 ],
				'url-height'						=>	['type' => 'numeric_null',	'value' => 16 ],
				'url-bold'							=>	['type' => 'flag',			'value' => 0 ],
				'url-italic'						=>	['type' => 'flag',			'value' => 0 ],
				'url-underline'						=>	['type' => 'flag',			'value' => 1 ],
				'url-hover'							=>	['type' => 'flag',			'value' => 1 ],

				'date-color'						=>	['type' => 'color',			'value' => '#444444' ],
				'date-outline-color'				=>	['type' => 'string',		'value' => '' ],
				'date-bg-color'						=>	['type' => 'string',		'value' => '' ],
				'date-size'							=>	['type' => 'numeric_null',	'value' => 11 ],
				'date-height'						=>	['type' => 'numeric_null',	'value' => 18 ],
				'date-bold'							=>	['type' => 'flag',			'value' => 0 ],
				'date-italic'						=>	['type' => 'flag',			'value' => 0 ],
				'date-underline'					=>	['type' => 'flag',			'value' => 0 ],
				'date-hover'						=>	['type' => 'flag',			'value' => 0 ],

				'heading-color'						=>	['type' => 'color',			'value' => '#ffffff' ],
				'heading-outline-color'				=>	['type' => 'string',		'value' => '' ],
				'heading-size'						=>	['type' => 'numeric_null',	'value' => 12 ],
				'heading-bold'						=>	['type' => 'flag',			'value' => 1 ],
				'heading-italic'					=>	['type' => 'flag',			'value' => 0 ],
				'heading-underline'					=>	['type' => 'flag',			'value' => 0 ],
				'heading-hover'						=>	['type' => 'flag',			'value' => 0 ],

				'more-color'						=>	['type' => 'color',			'value' => '#444444' ],
				'more-outline-color'				=>	['type' => 'string',		'value' => '' ],
				'more-size'							=>	['type' => 'numeric_null',	'value' => 12 ],
				'more-height'						=>	['type' => 'numeric_null',	'value' => 32 ],
				'more-bold'							=>	['type' => 'flag',			'value' => 0 ],
				'more-italic'						=>	['type' => 'flag',			'value' => 0 ],
				'more-underline'					=>	['type' => 'flag',			'value' => 0 ],
				'more-hover'						=>	['type' => 'flag',			'value' => 0 ],

				'info-color'						=>	['type' => 'color',			'value' => '#222222' ],
				'info-outline-color'				=>	['type' => 'string',		'value' => '' ],
				'info-bg-color'						=>	['type' => 'string',		'value' => '' ],
				'info-size'							=>	['type' => 'numeric_null',	'value' => 12 ],
				'info-height'						=>	['type' => 'numeric_null',	'value' => 13 ],
				'info-bold'							=>	['type' => 'flag',			'value' => 0 ],
				'info-italic'						=>	['type' => 'flag',			'value' => 0 ],
				'info-underline'					=>	['type' => 'flag',			'value' => 0 ],
				'info-hover'						=>	['type' => 'flag',			'value' => 0 ],

				'added-color'						=>	['type' => 'color',			'value' => '#ffffff' ],
				'added-outline-color'				=>	['type' => 'string',		'value' => '' ],
				'added-bg-color'					=>	['type' => 'color',			'value' => '#365cd9' ],
				'added-size'						=>	['type' => 'numeric_null',	'value' => 9 ],
				'added-height'						=>	['type' => 'numeric_null',	'value' => 10 ],
				'added-bold'						=>	['type' => 'flag',			'value' => 0 ],
				'added-italic'						=>	['type' => 'flag',			'value' => 0 ],
				'added-underline'					=>	['type' => 'flag',			'value' => 0 ],
				'added-hover'						=>	['type' => 'flag',			'value' => 0 ],

				'cat-color'							=>	['type' => 'color',			'value' => '#444444' ],
				'cat-outline-color'					=>	['type' => 'string',		'value' => '' ],
				'cat-bg-color'						=>	['type' => 'color',			'value' => '#ffcc88' ],
				'cat-size'							=>	['type' => 'numeric_null',	'value' => 9 ],
				'cat-height'						=>	['type' => 'numeric_null',	'value' => 10 ],
				'cat-bold'							=>	['type' => 'flag',			'value' => 1 ],
				'cat-italic'						=>	['type' => 'flag',			'value' => 0 ],
				'cat-underline'						=>	['type' => 'flag',			'value' => 0 ],
				'cat-hover'							=>	['type' => 'flag',			'value' => 0 ],

				'sns-size'							=>	['type' => 'numeric_null',	'value' => 9 ],
				'sns-height'						=>	['type' => 'numeric_null',	'value' => 10 ],
				'sns-bold'							=>	['type' => 'flag',			'value' => 0 ],
				'sns-italic'						=>	['type' => 'flag',			'value' => 0 ],
				'sns-underline'						=>	['type' => 'flag',			'value' => 0 ],
				'sns-hover'							=>	['type' => 'flag',			'value' => 0 ],


				'ex-target'							=>	['type' => 'numeric',		'value' => 2 ],

				'ex-info-type-1'					=>	['type' => 'string',		'value' => 'i' ],
				'ex-info-type-2'					=>	['type' => 'string',		'value' => 'n' ],
				'ex-info-type-3'					=>	['type' => 'string',		'value' => 'a' ],
				'ex-info-type-4'					=>	['type' => 'string',		'value' => 's' ],
				'ex-info-type-5'					=>	['type' => 'string',		'value' => '' ],
				'ex-info-text'						=>	['type' => 'string',		'value' => '' ],
				'ex-siteicon-get'					=>	['type' => 'numeric',		'value' => 13 ],
				'ex-siteicon-alt'					=>	['type' => 'string',		'value' => '' ],

				'ex-content-type-1'					=>	['type' => 'string',		'value' => 't' ],
				'ex-content-type-2'					=>	['type' => 'string',		'value' => 'u' ],
				'ex-content-type-3'					=>	['type' => 'string',		'value' => 'e' ],
				'ex-content-type-4'					=>	['type' => 'string',		'value' => 'p' ],
				'ex-content-type-5'					=>	['type' => 'string',		'value' => '' ],
				'ex-content-get'					=>	['type' => 'numeric',		'value' => 2 ],
				'ex-content-redir'					=>	['type' => 'flag',			'value' => 1 ],

				'ex-transform-enabled'				=>	['type' => 'flag',			'value' => 0 ],
				'ex-transform-x'					=>	['type' => 'numeric',		'value' => 0 ],
				'ex-transform-y'					=>	['type' => 'numeric',		'value' => 0 ],
				'ex-transform-rotate'				=>	['type' => 'numeric',		'value' => 0 ],
				'ex-transform-scale'				=>	['type' => 'numeric',		'value' => 100 ],
				'ex-bg-enabled'						=>	['type' => 'flag',			'value' => 1 ],
				'ex-bg-color'						=>	['type' => 'color',			'value' => '#f0f8ff' ],
				'ex-bg-image'						=>	['type' => 'string',		'value' => '' ],
				'ex-border-enabled'					=>	['type' => 'flag',			'value' => 1 ],
				'ex-border-style'					=>	['type' => 'string',		'value' => 'outset' ],
				'ex-border-color'					=>	['type' => 'color',			'value' => '#2277bb' ],
				'ex-border-width'					=>	['type' => 'numeric',		'value' => 4 ],
				'ex-border-radius'					=>	['type' => 'numeric',		'value' => 8 ],
				'ex-shadow-enabled'					=>	['type' => 'flag',			'value' => 1 ],
				'ex-shadow-color'					=>	['type' => 'color',			'value' => '#00008844' ],
				'ex-shadow-x'						=>	['type' => 'numeric',		'value' => 8 ],
				'ex-shadow-y'						=>	['type' => 'numeric',		'value' => 8 ],
				'ex-shadow-blur'					=>	['type' => 'numeric',		'value' => 8 ],
				'ex-shadow-spread'					=>	['type' => 'numeric',		'value' => 0 ],
				'ex-shadow-inset'					=>	['type' => 'flag',			'value' => 0 ],

				'ex-hover-transform-enabled'		=>	['type' => 'flag',			'value' => 0 ],
				'ex-hover-transform-x'				=>	['type' => 'numeric',		'value' => 0 ],
				'ex-hover-transform-y'				=>	['type' => 'numeric',		'value' => 0 ],
				'ex-hover-transform-rotate'			=>	['type' => 'numeric',		'value' => 0 ],
				'ex-hover-transform-scale'			=>	['type' => 'numeric',		'value' => 100 ],
				'ex-hover-bg-enabled'				=>	['type' => 'flag',			'value' => 1 ],
				'ex-hover-bg-color'					=>	['type' => 'color',			'value' => '#f0f8ff' ],
				'ex-hover-bg-image'					=>	['type' => 'string',		'value' => '' ],
				'ex-hover-border-enabled'			=>	['type' => 'flag',			'value' => 1 ],
				'ex-hover-border-style'				=>	['type' => 'string',		'value' => 'outset' ],
				'ex-hover-border-color'				=>	['type' => 'color',			'value' => '#2277bb' ],
				'ex-hover-border-width'				=>	['type' => 'numeric',		'value' => 4 ],
				'ex-hover-border-radius'			=>	['type' => 'numeric',		'value' => 8 ],
				'ex-hover-shadow-enabled'			=>	['type' => 'flag',			'value' => 1 ],
				'ex-hover-shadow-color'				=>	['type' => 'color',			'value' => '#00008844' ],
				'ex-hover-shadow-x'					=>	['type' => 'numeric',		'value' => 8 ],
				'ex-hover-shadow-y'					=>	['type' => 'numeric',		'value' => 8 ],
				'ex-hover-shadow-blur'				=>	['type' => 'numeric',		'value' => 8 ],
				'ex-hover-shadow-spread'			=>	['type' => 'numeric',		'value' => 0 ],
				'ex-hover-shadow-inset'				=>	['type' => 'flag',			'value' => 0 ],

				'ex-heading-text'					=>	['type' => 'string',		'value' => '' ],
				'ex-heading-transform-enabled'		=>	['type' => 'flag',			'value' => 0 ],
				'ex-heading-transform-x'			=>	['type' => 'numeric',		'value' => 0 ],
				'ex-heading-transform-y'			=>	['type' => 'numeric',		'value' => 0 ],
				'ex-heading-transform-rotate'		=>	['type' => 'numeric',		'value' => 0 ],
				'ex-heading-transform-scale'		=>	['type' => 'numeric',		'value' => 100 ],
				'ex-heading-bg-enabled'				=>	['type' => 'flag',			'value' => 1 ],
				'ex-heading-bg-color'				=>	['type' => 'color',			'value' => '#2277bb' ],
				'ex-heading-bg-image'				=>	['type' => 'string',		'value' => '' ],
				'ex-heading-border-enabled'			=>	['type' => 'flag',			'value' => 1 ],
				'ex-heading-border-style'			=>	['type' => 'string',		'value' => 'solid' ],
				'ex-heading-border-color'			=>	['type' => 'color',			'value' => '#2277bb' ],
				'ex-heading-border-width'			=>	['type' => 'numeric',		'value' => 2 ],
				'ex-heading-border-radius'			=>	['type' => 'numeric',		'value' => 8 ],
				'ex-heading-shadow-enabled'			=>	['type' => 'flag',			'value' => 1 ],
				'ex-heading-shadow-color'			=>	['type' => 'color',			'value' => '#00008844' ],
				'ex-heading-shadow-x'				=>	['type' => 'numeric',		'value' => 4 ],
				'ex-heading-shadow-y'				=>	['type' => 'numeric',		'value' => 4 ],
				'ex-heading-shadow-blur'			=>	['type' => 'numeric',		'value' => 4 ],
				'ex-heading-shadow-spread'			=>	['type' => 'numeric',		'value' => 0 ],
				'ex-heading-shadow-inset'			=>	['type' => 'flag',			'value' => 0 ],

				'ex-thumbnail-get'					=>	['type' => 'numeric',		'value' => 13 ],
				'ex-thumbnail-alt'					=>	['type' => 'string',		'value' => '' ],
				'ex-thumbnail-border-enabled'		=>	['type' => 'flag',			'value' => 1 ],
				'ex-thumbnail-border-style'			=>	['type' => 'string',		'value' => 'solid' ],
				'ex-thumbnail-border-color'			=>	['type' => 'color',			'value' => '#888888' ],
				'ex-thumbnail-border-width'			=>	['type' => 'numeric',		'value' => 1 ],
				'ex-thumbnail-border-radius'		=>	['type' => 'numeric',		'value' => 2 ],
				'ex-thumbnail-shadow-enabled'		=>	['type' => 'flag',			'value' => 1 ],
				'ex-thumbnail-shadow-color'			=>	['type' => 'color',			'value' => '#88888844' ],
				'ex-thumbnail-shadow-x'				=>	['type' => 'numeric',		'value' => 8 ],
				'ex-thumbnail-shadow-y'				=>	['type' => 'numeric',		'value' => 8 ],
				'ex-thumbnail-shadow-blur'			=>	['type' => 'numeric',		'value' => 8 ],
				'ex-thumbnail-shadow-spread'		=>	['type' => 'numeric',		'value' => 0 ],
				'ex-thumbnail-shadow-inset'			=>	['type' => 'flag',			'value' => 0 ],
				'ex-thumbnail-transform-enabled'	=>	['type' => 'flag',			'value' => 0 ],
				'ex-thumbnail-transform-x'			=>	['type' => 'numeric',		'value' => 0 ],
				'ex-thumbnail-transform-y'			=>	['type' => 'numeric',		'value' => 0 ],
				'ex-thumbnail-transform-rotate'		=>	['type' => 'numeric',		'value' => 0 ],
				'ex-thumbnail-transform-scale'		=>	['type' => 'numeric',		'value' => 100 ],

				'ex-more-text'						=>	['type' => 'string',		'value' => '' ],
				'ex-more-position'					=>	['type' => 'string',		'value' => 'o_r' ],
				'ex-more-bg-enabled'				=>	['type' => 'flag',			'value' => 1 ],
				'ex-more-bg-color'					=>	['type' => 'color',			'value' => '#ededaa' ],
				'ex-more-bg-image'					=>	['type' => 'string',		'value' => '' ],
				'ex-more-border-enabled'			=>	['type' => 'flag',			'value' => 1 ],
				'ex-more-border-style'				=>	['type' => 'string',		'value' => 'outset' ],
				'ex-more-border-color'				=>	['type' => 'color',			'value' => '#ffba00' ],
				'ex-more-border-width'				=>	['type' => 'numeric',		'value' => 4 ],
				'ex-more-border-radius'				=>	['type' => 'numeric',		'value' => 12 ],
				'ex-more-shadow-enabled'			=>	['type' => 'flag',			'value' => 1 ],
				'ex-more-shadow-color'				=>	['type' => 'color',			'value' => '#00008844' ],
				'ex-more-shadow-x'					=>	['type' => 'numeric',		'value' => 4 ],
				'ex-more-shadow-y'					=>	['type' => 'numeric',		'value' => 4 ],
				'ex-more-shadow-blur'				=>	['type' => 'numeric',		'value' => 4 ],
				'ex-more-shadow-spread'				=>	['type' => 'numeric',		'value' => 0 ],
				'ex-more-shadow-inset'				=>	['type' => 'flag',			'value' => 0 ],
				'ex-more-transform-enabled'			=>	['type' => 'flag',			'value' => 0 ],
				'ex-more-transform-x'				=>	['type' => 'numeric',		'value' => 0 ],
				'ex-more-transform-y'				=>	['type' => 'numeric',		'value' => 0 ],
				'ex-more-transform-rotate'			=>	['type' => 'numeric',		'value' => 0 ],
				'ex-more-transform-scale'			=>	['type' => 'numeric',		'value' => 100 ],

				'in-target'							=>	['type' => 'string',		'value' => '' ],

				'in-info-type-1'					=>	['type' => 'string',		'value' => 'i' ],
				'in-info-type-2'					=>	['type' => 'string',		'value' => 'n' ],
				'in-info-type-3'					=>	['type' => 'string',		'value' => 'a' ],
				'in-info-type-4'					=>	['type' => 'string',		'value' => 's' ],
				'in-info-type-5'					=>	['type' => 'string',		'value' => '' ],
				'in-info-text'						=>	['type' => 'string',		'value' => '' ],
				'in-siteicon-get'					=>	['type' => 'numeric',		'value' => 1 ],
				'in-siteicon-alt'					=>	['type' => 'string',		'value' => '' ],

				'in-content-type-1'					=>	['type' => 'string',		'value' => 'c' ],
				'in-content-type-2'					=>	['type' => 'string',		'value' => 't' ],
				'in-content-type-3'					=>	['type' => 'string',		'value' => 'p' ],
				'in-content-type-4'					=>	['type' => 'string',		'value' => 'e' ],
				'in-content-type-5'					=>	['type' => 'string',		'value' => '' ],
				'in-content-title'					=>	['type' => 'string',		'value' => '' ],
				'in-content-excerpt'				=>	['type' => 'string',		'value' => '' ],
				'in-content-get'					=>	['type' => 'numeric',		'value' => 1 ],
				'in-content-redir'					=>	['type' => 'flag',			'value' => 0 ],

				'in-transform-enabled'				=>	['type' => 'flag',			'value' => 0 ],
				'in-transform-x'					=>	['type' => 'numeric',		'value' => 0 ],
				'in-transform-y'					=>	['type' => 'numeric',		'value' => 0 ],
				'in-transform-rotate'				=>	['type' => 'numeric',		'value' => 0 ],
				'in-transform-scale'				=>	['type' => 'numeric',		'value' => 100 ],
				'in-bg-enabled'						=>	['type' => 'flag',			'value' => 1 ],
				'in-bg-color'						=>	['type' => 'color',			'value' => '#ffffff' ],
				'in-bg-image'						=>	['type' => 'string',		'value' => '' ],
				'in-border-enabled'					=>	['type' => 'flag',			'value' => 1 ],
				'in-border-style'					=>	['type' => 'string',		'value' => 'outset' ],
				'in-border-color'					=>	['type' => 'color',			'value' => '#cc8800' ],
				'in-border-width'					=>	['type' => 'numeric',		'value' => 4 ],
				'in-border-radius'					=>	['type' => 'numeric',		'value' => 8 ],
				'in-shadow-enabled'					=>	['type' => 'flag',			'value' => 1 ],
				'in-shadow-color'					=>	['type' => 'color',			'value' => '#44220044' ],
				'in-shadow-x'						=>	['type' => 'numeric',		'value' => 8 ],
				'in-shadow-y'						=>	['type' => 'numeric',		'value' => 8 ],
				'in-shadow-blur'					=>	['type' => 'numeric',		'value' => 8 ],
				'in-shadow-spread'					=>	['type' => 'numeric',		'value' => 0 ],
				'in-shadow-inset'					=>	['type' => 'flag',			'value' => 0 ],

				'in-hover-transform-enabled'		=>	['type' => 'flag',			'value' => 0 ],
				'in-hover-transform-x'				=>	['type' => 'numeric',		'value' => 0 ],
				'in-hover-transform-y'				=>	['type' => 'numeric',		'value' => 0 ],
				'in-hover-transform-rotate'			=>	['type' => 'numeric',		'value' => 0 ],
				'in-hover-transform-scale'			=>	['type' => 'numeric',		'value' => 100 ],
				'in-hover-bg-enabled'				=>	['type' => 'flag',			'value' => 1 ],
				'in-hover-bg-color'					=>	['type' => 'color',			'value' => '#ffffff' ],
				'in-hover-bg-image'					=>	['type' => 'string',		'value' => '' ],
				'in-hover-border-enabled'			=>	['type' => 'flag',			'value' => 1 ],
				'in-hover-border-style'				=>	['type' => 'string',		'value' => 'outset' ],
				'in-hover-border-color'				=>	['type' => 'color',			'value' => '#cc8800' ],
				'in-hover-border-width'				=>	['type' => 'numeric',		'value' => 4 ],
				'in-hover-border-radius'			=>	['type' => 'numeric',		'value' => 8 ],
				'in-hover-shadow-enabled'			=>	['type' => 'flag',			'value' => 1 ],
				'in-hover-shadow-color'				=>	['type' => 'color',			'value' => '#44220044' ],
				'in-hover-shadow-x'					=>	['type' => 'numeric',		'value' => 8 ],
				'in-hover-shadow-y'					=>	['type' => 'numeric',		'value' => 8 ],
				'in-hover-shadow-blur'				=>	['type' => 'numeric',		'value' => 8 ],
				'in-hover-shadow-spread'			=>	['type' => 'numeric',		'value' => 0 ],
				'in-hover-shadow-inset'				=>	['type' => 'flag',			'value' => 0 ],

				'in-thumbnail-get'					=>	['type' => 'numeric',		'value' => 1 ],
				'in-thumbnail-size'					=>	['type' => 'numeric',		'value' => 150 ],
				'in-thumbnail-alt'					=>	['type' => 'string',		'value' => '' ],
				'in-thumbnail-transform-enabled'	=>	['type' => 'flag',			'value' => 0 ],
				'in-thumbnail-transform-x'			=>	['type' => 'numeric',		'value' => 0 ],
				'in-thumbnail-transform-y'			=>	['type' => 'numeric',		'value' => 0 ],
				'in-thumbnail-transform-rotate'		=>	['type' => 'numeric',		'value' => 0 ],
				'in-thumbnail-transform-scale'		=>	['type' => 'numeric',		'value' => 100 ],
				'in-thumbnail-border-enabled'		=>	['type' => 'flag',			'value' => 1 ],
				'in-thumbnail-border-color'			=>	['type' => 'color',			'value' => '#442200' ],
				'in-thumbnail-border-style'			=>	['type' => 'string',		'value' => 'solid' ],
				'in-thumbnail-border-width'			=>	['type' => 'numeric',		'value' => 1 ],
				'in-thumbnail-border-radius'		=>	['type' => 'numeric',		'value' => 6 ],
				'in-thumbnail-shadow-enabled'		=>	['type' => 'flag',			'value' => 1 ],
				'in-thumbnail-shadow-color'			=>	['type' => 'color',			'value' => '#88888844' ],
				'in-thumbnail-shadow-x'				=>	['type' => 'numeric',		'value' => 8 ],
				'in-thumbnail-shadow-y'				=>	['type' => 'numeric',		'value' => 8 ],
				'in-thumbnail-shadow-blur'			=>	['type' => 'numeric',		'value' => 8 ],
				'in-thumbnail-shadow-spread'		=>	['type' => 'numeric',		'value' => 0 ],
				'in-thumbnail-shadow-inset'			=>	['type' => 'flag',			'value' => 0 ],

				'in-heading-text'					=>	['type' => 'string',		'value' => '' ],
				'in-heading-transform-enabled'		=>	['type' => 'flag',			'value' => 0 ],
				'in-heading-transform-x'			=>	['type' => 'numeric',		'value' => 0 ],
				'in-heading-transform-y'			=>	['type' => 'numeric',		'value' => 0 ],
				'in-heading-transform-rotate'		=>	['type' => 'numeric',		'value' => 0 ],
				'in-heading-transform-scale'		=>	['type' => 'numeric',		'value' => 100 ],
				'in-heading-bg-enabled'				=>	['type' => 'flag',			'value' => 1 ],
				'in-heading-bg-color'				=>	['type' => 'color',			'value' => '#cc8800' ],
				'in-heading-bg-image'				=>	['type' => 'string',		'value' => '' ],
				'in-heading-border-enabled'			=>	['type' => 'flag',			'value' => 1 ],
				'in-heading-border-style'			=>	['type' => 'string',		'value' => 'solid' ],
				'in-heading-border-color'			=>	['type' => 'color',			'value' => '#cc8800' ],
				'in-heading-border-width'			=>	['type' => 'numeric',		'value' => 2 ],
				'in-heading-border-radius'			=>	['type' => 'numeric',		'value' => 8 ],
				'in-heading-shadow-enabled'			=>	['type' => 'flag',			'value' => 1 ],
				'in-heading-shadow-color'			=>	['type' => 'color',			'value' => '#442200' ],
				'in-heading-shadow-x'				=>	['type' => 'numeric',		'value' => 4 ],
				'in-heading-shadow-y'				=>	['type' => 'numeric',		'value' => 4 ],
				'in-heading-shadow-blur'			=>	['type' => 'numeric',		'value' => 4 ],
				'in-heading-shadow-spread'			=>	['type' => 'numeric',		'value' => 0 ],
				'in-heading-shadow-inset'			=>	['type' => 'flag',			'value' => 0 ],

				'in-more-text'						=>	['type' => 'string',		'value' => '' ],
				'in-more-position'					=>	['type' => 'string',		'value' => 'o_r' ],
				'in-more-bg-enabled'				=>	['type' => 'flag',			'value' => 1 ],
				'in-more-bg-color'					=>	['type' => 'color',			'value' => '#ededaa' ],
				'in-more-bg-image'					=>	['type' => 'string',		'value' => '' ],
				'in-more-border-enabled'			=>	['type' => 'flag',			'value' => 1 ],
				'in-more-border-style'				=>	['type' => 'string',		'value' => 'outset' ],
				'in-more-border-color'				=>	['type' => 'color',			'value' => '#ffba00' ],
				'in-more-border-width'				=>	['type' => 'numeric',		'value' => 4 ],
				'in-more-border-radius'				=>	['type' => 'numeric',		'value' => 12 ],
				'in-more-shadow-enabled'			=>	['type' => 'flag',			'value' => 1 ],
				'in-more-shadow-color'				=>	['type' => 'color',			'value' => '#44220044' ],
				'in-more-shadow-x'					=>	['type' => 'numeric',		'value' => 4 ],
				'in-more-shadow-y'					=>	['type' => 'numeric',		'value' => 4 ],
				'in-more-shadow-blur'				=>	['type' => 'numeric',		'value' => 4 ],
				'in-more-shadow-spread'				=>	['type' => 'numeric',		'value' => 0 ],
				'in-more-shadow-inset'				=>	['type' => 'flag',			'value' => 0 ],
				'in-more-transform-enabled'			=>	['type' => 'flag',			'value' => 0 ],
				'in-more-transform-x'				=>	['type' => 'numeric',		'value' => 0 ],
				'in-more-transform-y'				=>	['type' => 'numeric',		'value' => 0 ],
				'in-more-transform-rotate'			=>	['type' => 'numeric',		'value' => 0 ],
				'in-more-transform-scale'			=>	['type' => 'numeric',		'value' => 100 ],

				'flg-nofollow'						=>	['type' => 'flag',			'value' => 0 ],
				'flg-noopener'						=>	['type' => 'flag',			'value' => 1 ],
				'flg-referer'						=>	['type' => 'flag',			'value' => 1 ],
				'flg-unlink'						=>	['type' => 'flag',			'value' => 1 ],
				'flg-sslverify'						=>	['type' => 'flag',			'value' => 1 ],
				'flg-local-check'					=>	['type' => 'flag',			'value' => 1 ],
				'user-agent'						=>	['type' => 'string',		'value' => 'pzlkc' ],
				'user-agent-text'					=>	['type' => 'string',		'value' => '' ],
				'flg-click-count'					=>	['type' => 'flag',			'value' => 0 ],
				'flg-alive-count'					=>	['type' => 'flag',			'value' => 1 ],

				'code1'								=>	['type' => 'string',		'value' => 'blogcard' ],
				'code2'								=>	['type' => 'string',		'value' => '' ],
				'code3'								=>	['type' => 'string',		'value' => '' ],
				'code4'								=>	['type' => 'string',		'value' => '' ],
				'use-inline'						=>	['type' => 'string',		'value' => '' ],
				'auto-atag'							=>	['type' => 'flag',			'value' => 0 ],
				'auto-url'							=>	['type' => 'flag',			'value' => 0 ],
				'auto-external'						=>	['type' => 'flag',			'value' => 0 ],
				'flg-do-shortcode'					=>	['type' => 'flag',			'value' => 1 ],
				'flg-edit-insert'					=>	['type' => 'flag',			'value' => 1 ],
				'mce-priority'						=>	['type' => 'string',		'value' => '' ],
				'flg-edit-qtag'						=>	['type' => 'flag',			'value' => 1 ],
				'flg-clear-excerpt'					=>	['type' => 'flag',			'value' => 1 ],

				'multi-mode'						=>	['type' => 'flag',			'value' => 0 ],
				'multi-myid'						=>	['type' => 'numeric',		'value' => 0 ],
				'multi-count'						=>	['type' => 'numeric',		'value' => 0 ],

				'trail-slash'						=>	['type' => 'numeric',		'value' => 1 ],
				'flg-quickmenu'						=>	['type' => 'flag',			'value' => 0 ],
				'flg-unti-select'					=>	['type' => 'flag',			'value' => 0 ],
				'flg-adminbar'						=>	['type' => 'flag',			'value' => 0 ],
				'flg-important'						=>	['type' => 'flag',			'value' => 1 ],
				'flg-compress'						=>	['type' => 'flag',			'value' => 1 ],
				'flg-inhibit'						=>	['type' => 'flag',			'value' => 1 ],
				'flg-amp-url'						=>	['type' => 'flag',			'value' => 0 ],
				'error-mode-hide'					=>	['type' => 'flag',			'value' => 1 ],

				'develop-mode'						=>	['type' => 'flag',			'value' => 0 ],
				'admin-mode'						=>	['type' => 'flag',			'value' => 0 ],
				'debug-mode'						=>	['type' => 'flag',			'value' => 0 ],
				'debug-nocache'						=>	['type' => 'flag',			'value' => 0 ],
				'debug-style-admin'					=>	['type' => 'flag',			'value' => 0 ],
				'debug-style-card'					=>	['type' => 'flag',			'value' => 0 ],
				'debug-sns'							=>	['type' => 'flag',			'value' => 0 ],
				'additional-mode'					=>	['type' => 'flag',			'value' => 0 ],
				'log-mode'							=>	['type' => 'flag',			'value' => 0 ],

				'image-size'						=>	['type' => 'numeric',		'value' => 400 ],

				'siteicon-api'						=>	['type' => 'url',			'value' => '' ],
				'thumbnail-api'						=>	['type' => 'url',			'value' => '' ],
				'initialize-exception'				=>	['type' => 'flag',			'value' => 1 ],
				'flg-delete-db'						=>	['type' => 'flag',			'value' => 1 ],
				'flg-delete-images'					=>	['type' => 'flag',			'value' => 1 ],
				'flg-delete-settings'				=>	['type' => 'flag',			'value' => 1 ],

				'alive-period'						=>	['type' => 'string',		'value' => 'hourly' ],	// 定期実行の間隔
				'alive-period-num'					=>	['type' => 'numeric',		'value' => 5 ],			// 1回の処理で何件処理するか
				'sns-period'						=>	['type' => 'string',		'value' => 'hourly' ],	// 定期実行の間隔
				'sns-period-num'					=>	['type' => 'numeric',		'value' => 5 ],			// 1回の処理で何件処理するか
		);

		// 変数
		private		$flg_activate;				// 二重実行防止
		private		$flg_suppression;			// 出力抑制
		private		$now;						// 現在日時（ローカル時間）
		private		$db_card;					// DBのテーブル名（カード情報）
		private		$db_click;					// DBのテーブル名（クリックログ）

		private		$flg_amp;					// Google AMP 0:不明 1:AMP 2:通常
		private		$options;					// パラメータ
		private		$logging_count;				// ログでのカウント

		private		$plugin_slug;				// プラグイン名
		private		$plugin_name;				// プラグイン名
		private		$plugin_code;				// プラグイン略称
		private		$plugin_url;				// プラグインURL
		private		$plugin_version;			// プラグインバージョン

		private		$author_name;				// 作者名
		private		$author_url;				// 作者サイトのURL
		private		$author_twitter_name;		// 作者 Xアカウント
		private		$author_twitter_url;		// 作者 Xプロフィール
		private		$author_donate_url;			// 作者への寄付
		private		$author_wishlist;			// 欲しい物リスト

		private		$base_name;					// プラグイン一覧のインラインメニュー用
		private		$base_path;					// インストール ディレクトリのパス
		private		$base_url;					// インストール ディレクトリのURL

		private		$wp_version;				// WordPressのバージョン

		private		$dir_upload;				// アップロード ディレクトリのパス
		private		$url_upload;				// アップロード ディレクトリのURL

		private		$dir_style;					// CSSディレクトリのパス
		private		$url_style;					// CSSディレクトリのURL

		private		$dir_cache;					// 画像キャッシュのパス
		private		$url_cache;					// 画像キャッシュのURL

		private		$dir_debug;					// ログファイル ディレクトリのパス
		private		$url_debug;					// ログファイル ディレクトリのURL

		private		$file_template;				// 元となるテンプレート

		private		$cron_regist;				// 随時実行のフック名（記事内容取得）
		private		$cron_alive;				// 定期実行のフック名（生存確認）
		private		$cron_sns;					// 定期実行のフック名（シェア数確認）

		private		$url_env_product;			// ぽぽづれ。運用環境のURL
		private		$url_env_develop;			// ぽぽづれ。開発環境のURL
		private		$url_env_local;				// ローカル開発環境
		private		$url_env_local_multi;		// ローカル開発環境（マルチ）


		private		$format_date;				// 日付の書式
		private		$format_time;				// 時刻の書式
		private		$format_datetime;			// 日付・時刻の書式

		private		$url_now;					// 現在表示しているURL
		private		$cacheman_page;				// Pzカード管理のページ名
		private		$cacheman_url;				// Pzカード管理のURL
		private		$settings_page;				// Pzカード設定のページ名
		private		$settings_url;				// Pzカード設定のURL

		private		$is_self;	
		private		$is_settings;				// Pz カード設定
		private		$is_cacheman;				// Pz カード管理
		private		$is_rest_api;				// REST-APIリクエスト（実質ブロックエディター）
		private		$is_richedit;				// リッチエディターモード

		private		$option_name;				// オプション名

		private		$my_url;					// 自サイトのURL
		private		$my_scheme;					// 自サイトのスキーム
		private		$my_sitename;				// 自サイトの名前
		private		$my_domain;					// 自サイトのドメイン名
		private		$my_domain_url;				// 自サイトのドメインURL
		private		$my_charset;				// 自サイトの文字コード（UTF-8）

		private		$user_agent;				// ユーザーエージェント

		private		$env_product;
		private		$env_develop;
		private		$env_local;


		public	function	__construct() {
			global			$wpdb;				// DBの宣言
			global			$wp_version;		// WordPressのバージョン

			require_once plugin_dir_path( __FILE__ ) . 'includes/pz-class-card-search-query-parser.php';

			// プラグイン情報
			$default_headers = array(
				'PluginName'		=>	'Plugin Name',
				'PluginURI'			=>	'Plugin URI',
				'Version'			=>	'Version',
				'Author'			=>	'Author',
				'AuthorURI'			=>	'Author URI',
			);
			$plugin_info			=	get_file_data(__FILE__, $default_headers );

			// 定数
			$this->plugin_slug		=	basename(dirname(__FILE__ ) );								// プラグイン名
			$this->plugin_name		=	$plugin_info['PluginName'];									// プラグイン名
			$this->plugin_code		=	'Pz-LkC';													// プラグイン略称
			$this->plugin_url		=	'https://popozure.info/pz-linkcard3';						// プラグインURL
			$this->plugin_version	=	$plugin_info['Version'];									// プラグインバージョン

			$this->author_name			=	$plugin_info['Author'];									// 作者名
			$this->author_url			=	$plugin_info['AuthorURI'];								// 作者サイトのURL
			$this->author_twitter_name	=	'@popozure';											// 作者 Xアカウント
			$this->author_twitter_url	=	'https://x.com/popozure';								// 作者 Xプロフィール
			$this->author_donate_url	=	'https://www.amazon.jp/?tag=pz-linkcard-22';			// 作者への寄付
			$this->author_wishlist		=	'https://www.amazon.jp/hz/wishlist/ls/12LL2TX9147CY?ref_=wl_share&tag=pz-linkcard-22';	// 欲しい物リスト

			$this->base_name			=	plugin_basename(__FILE__ );								// プラグイン一覧のインラインメニュー用
			$this->base_path			=	plugin_dir_path(__FILE__ );								// インストール ディレクトリのパス
			$this->base_url				=	plugin_dir_url (__FILE__ );								// インストール ディレクトリのURL

			$this->wp_version			=	$wp_version ;											// WordPressのバージョン

			$this->dir_upload			=	wp_upload_dir()['basedir']. '/'.$this->plugin_slug.'/';			// アップロード ディレクトリのパス
			$this->url_upload			=	preg_replace('/(http|https):(\/\/.*)/', '$2', wp_upload_dir()['baseurl'] ).'/'.$this->plugin_slug.'/';	// アップロード ディレクトリのURL

			$this->dir_style			=	$this->dir_upload.'style/';								// CSSディレクトリのパス
			$this->url_style			=	$this->url_upload.'style/';								// CSSディレクトリのURL

			$this->dir_cache			=	$this->dir_upload.'cache/';								// 画像キャッシュのパス
			$this->url_cache			=	$this->url_upload.'cache/';								// 画像キャッシュのURL

			$this->dir_debug			=	$this->dir_upload.'log/';								// ログファイル ディレクトリのパス
			$this->url_debug			=	$this->url_upload.'log/';								// ログファイル ディレクトリのURL

			$this->file_template		=	$this->base_path.'template/pz-lkc3-template.css';		// 元となるテンプレート

			$this->cron_regist			=	'pz_linkcard3_regist';									// 随時実行のフック名（記事内容取得）
			$this->cron_alive			=	'pz_linkcard3_alive';									// 定期実行のフック名（生存確認）
			$this->cron_sns				=	'pz_linkcard3_sns';										// 定期実行のフック名（シェア数確認）

			$this->url_env_product		=	'https://popozure.info/';								// ぽぽづれ。運用環境のURL
			$this->url_env_develop		=	'https://popozure.xsrv.jp/';							// ぽぽづれ。開発環境のURL
			$this->url_env_local		=	'';														// ローカル環境
			$this->url_env_local_multi	=	'';														// ローカル環境

			$this->format_date			=	get_option('date_format' );								// 日付の書式
			$this->format_time			=	get_option('time_format' );								// 時刻の書式
			$this->format_datetime		=	$this->format_date.' '.$this->format_time;				// 日付・時刻の書式

			// ページ情報
			$server_host				=	isset($_SERVER['HTTP_HOST'] ) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_HOST'] ) ) : '';
			$server_request_uri			=	isset($_SERVER['REQUEST_URI'] ) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'] ) ) : '/';
			$this->url_now				=	(is_ssl() ? 'https' : 'http' ).'://'.$server_host.$server_request_uri;	// 現在表示しているURL
			$this->cacheman_page		=	'pz-linkcard3-cacheman';								// Pzカード管理のページ名
			$this->cacheman_url			=	admin_url('/tools.php?page='.$this->cacheman_page );	// Pzカード管理のURL
			$this->settings_page		=	'pz-linkcard3-settings';								// Pzカード設定のページ名
			$this->settings_url			=	admin_url('/options-general.php?page='.$this->settings_page );	// Pzカード設定のURL

			// ページ情報
			switch	(true) {				// 現在のページ
			case	(strpos($this->url_now, $this->settings_url ) !== false ):
				$this->is_self				=	true  ;
				$this->is_settings			=	true  ;		// Pz カード設定
				$this->is_cacheman			=	false ;		// Pz カード管理
				break;
			case	(strpos($this->url_now, $this->cacheman_url ) !== false ):
				$this->is_self				=	true  ;
				$this->is_settings			=	false ;		// Pz カード設定
				$this->is_cacheman			=	true  ;		// Pz カード管理
				break;
			default:
				$this->is_self				=	false ;
				$this->is_settings			=	false ;		// Pz カード設定
				$this->is_cacheman			=	false ;		// Pz カード管理
			}

			// REST-API経由（ブロックエディター）
			$this->is_rest_api				=	(defined('REST_REQUEST' ) && REST_REQUEST );			// REST-APIリクエスト（実質ブロックエディター）

			// オプション値を取得
			$this->option_name				=	'pz_linkcard3_options';									// オプション名
			$this->now						=	current_time('timestamp', false );						// 現在日時（ローカル時間）
			$this->logging_count			=	0;														// ログでのカウント
			$this->pz_LoadOptions();

			// ログ出力
			$this->pz_DebugLog(__FUNCTION__, 'is_admin='.is_admin().' URL='.$this->url_now );

			// 初期値を設定
			$this->flg_amp			=	0;																	// 今がAMP表示かどうか判定
			$this->db_card			=	$wpdb->prefix.'pz_linkcard3_card';									// DBのテーブル名（カード情報）
			$this->db_click			=	$wpdb->prefix.'pz_linkcard3_click';									// DBのテーブル名（クリックログ）
			$this->flg_suppression	=	false;																// 出力抑制（header出力前かどうか）
			$this->my_url			=	esc_url(home_url() );								
			
			// 自サイトのURL
			$url_info				=	$this->pz_GetURLInfo($this->my_url );
			$this->my_sitename		=	get_bloginfo('name' );												// 自サイトの名前
			$this->my_scheme		=	esc_attr($url_info['scheme'] );										// 自サイトのスキーム
			$this->my_domain		=	$url_info['domain'];												// 自サイトのドメイン名
			$this->my_domain_url	=	$url_info['domain_url'];											// 自サイトのドメインURL
			$this->my_charset		=	get_bloginfo('charset' );											// 自サイトの文字コード（UTF-8）
			$this->user_agent		=	isset($_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'] ) ) : '';		// ユーザーエージェント

			// 環境情報
			switch	(true ) {
			case	($this->pz_StrStartsWith($this->url_now, $this->url_env_product ) ):
				$this->env_product				=	true  ;
				$this->env_develop				=	false ;
				$this->env_local				=	false ;
				break;
			case	($this->pz_StrStartsWith($this->url_now, $this->url_env_develop ) ):
				$this->env_product				=	false ;
				$this->env_develop				=	true  ;
				$this->env_local				=	false ;
				break;
			case	($this->url_env_local && $this->pz_StrStartsWith($this->url_now, $this->url_env_local ) ):
				$this->env_product				=	false ;
				$this->env_develop				=	true  ;
				$this->env_local				=	true  ;
				break;
			case	($this->url_env_local_multi && $this->pz_StrStartsWith($this->url_now, $this->url_env_local_multi ) ):
				$this->env_product				=	false ;
				$this->env_develop				=	true  ;
				$this->env_local				=	true  ;
				break;
			default:
				$this->env_product				=	false ;
				$this->env_develop				=	false ;
				$this->env_local				=	false ;
			}

			// バージョンが違う場合、初期処理を実行する
			if	($this->options['plugin-version']	<>	$this->plugin_version ) {
				$this->hook_activate();				// プラグインの再起動
			}

			// 全体用フック
			$this->hook_common_actions();			// フック（一般画面＆管理画面 共有）
			$this->hook_ajax_handlers();			// AJAX関連フック

			if	(is_admin() ) {
				$this->hook_admin_actions();		// 管理画面用フック
			} else {
				$this->hook_frontend_actions();		// 一般画面用フック
			}
		}

		// 全体フック（一般画面・管理画面ともに）
		private	function	hook_common_actions() {
			register_activation_hook	(__FILE__,						[$this, 'hook_activate' ],						10, 1 );		// プラグインを有効化するときの処理
			register_deactivation_hook	(__FILE__,						[$this, 'hook_deactivate' ],					10, 1 );		// プラグインを無効化するときの処理

			add_action		('plugins_loaded',							[$this, 'action_plugins_loaded' ],				10, 1 );		// WordPressロード後
			add_action		('init',									[$this, 'action_init' ],						10, 1 );		// プラグイン初期化
			add_action		('wp_loaded',								[$this, 'action_wp_loaded' ],					10, 1 );
			add_action		('wp_head',									[$this, 'action_wp_head' ],						10, 1 );
			add_action		('wp_footer',								[$this, 'action_wp_footer' ],					10, 1 );
			add_action		('upgrader_process_complete',				[$this, 'action_upgrader_process_complete' ],	10, 2 );		// アップデートしたときの処理
			add_action		('admin_bar_menu',							[$this, 'action_admin_bar_menu' ],				100, 1 );		// 管理バー

			// スケジュール
			add_action		($this->cron_regist,						[$this, 'hook_regist' ],						10, 1 );		// リンク先の記事情報取得
			add_action		($this->cron_alive,							[$this, 'hook_check_alive' ],					10, 1 );		// リンク先の生存確認
			add_action		($this->cron_sns,							[$this, 'hook_check_sns' ],						10,	1 );		// ソーシャルカウント確認
		}

		// AJAX関連フック（割り込み関連）
		private	function	hook_ajax_handlers() {
			add_action		('wp_ajax_pz_lkc3_click_count',				[$this, 'action_ajax_lkc_click_count' ] );						// クリックカウンタ
			add_action		('wp_ajax_nopriv_pz_lkc3_click_count',		[$this, 'action_ajax_lkc_click_count' ] );
			add_action		('wp_ajax_pz_lkc3_refresh_card',			[$this, 'action_ajax_lkc_refresh_card' ] );
			add_action		('wp_ajax_pz_lkc3_refresh_thumbnail',		[$this, 'action_ajax_lkc_refresh_thumbnail' ] );
			add_action		('rest_api_init',							[$this, 'register_rest_routes'] );								// 遅延読み込み
		}

		// REST API ルート登録
		public function register_rest_routes() {
			register_rest_route('pz-linkcard/v1', '/card/(?P<id>\d+)', [
				'methods'             => 'GET',
				'callback'            => [$this, 'get_linkcard3_data'],
				'permission_callback' => [$this, 'permission_linkcard3_data'],
			]);
		}

		public function permission_linkcard3_data($request) {
			$card_id	=	intval($request['id'] );
			$token		=	sanitize_text_field($request->get_param('token' ) ?? '' );
			$page		=	esc_url_raw($request->get_param('page' ) ?? '' );

			if	(!$card_id || !$token || !$page ) {
				return	new WP_Error('pz_lkc3_forbidden', __('Invalid lazy loading token.', 'pz-linkcard3' ), array('status' => 403 ) );
			}
			if	(!$this->pz_VerifyLazyCardToken($card_id, $page, $token ) ) {
				return	new WP_Error('pz_lkc3_forbidden', __('Invalid lazy loading token.', 'pz-linkcard3' ), array('status' => 403 ) );
			}

			return	true;
		}

		// 記事データを返す
		public function get_linkcard3_data($request) {
			$card_id = intval($request['id']);
			if (!$card_id) {
				return new WP_REST_Response([
					'status'	=>	400,
					'error'		=>	'Invalid card ID',
				], 400 );
			}

			$data = $this->pz_GetCache(array('card_id' => $card_id ) );

			if (empty($data['card_id'] ) ) {
				return new WP_REST_Response([
					'status'	=>	404,
					'error'		=>	'Card not found',
				], 404 );
			}

			if (empty($data['regist_time'] ) ) {
				return new WP_REST_Response([
					'status'	=>	202,
					'message'	=>	'Processing',
				], 202 );
			}

			return [
				'status'	=>	200,
				'html'		=>	$this->pz_GetHTML($data ),
			];
		}

		private	function	pz_GetLazyCardPageURL() {
			$request_uri	=	isset($_SERVER['REQUEST_URI'] ) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'] ) ) : '/';
			$request_uri	=	'/' . ltrim($request_uri, '/' );
			return	esc_url_raw(home_url($request_uri ) );
		}

		private	function	pz_GetLazyCardTokenAction($card_id, $page_url ) {
			return	'pz_lkc3_lazy_card_' . intval($card_id ) . '_' . hash('sha256', esc_url_raw($page_url ) );
		}

		private	function	pz_CreateLazyCardToken($card_id, $page_url ) {
			return	hash_hmac('sha256', $this->pz_GetLazyCardTokenAction($card_id, $page_url ), wp_salt('nonce' ) );
		}

		private	function	pz_VerifyLazyCardToken($card_id, $page_url, $token ) {
			$expected	=	$this->pz_CreateLazyCardToken($card_id, $page_url );
			return	hash_equals($expected, $token );
		}

		private	function	pz_GetURLHostWithPort($url ) {
			$host	=	wp_parse_url($url, PHP_URL_HOST );
			if	(!$host ) {
				return	'';
			}
			$port	=	wp_parse_url($url, PHP_URL_PORT );
			return	strtolower($host ).($port ? ':'.intval($port ) : '' );
		}

		private	function	pz_IsSiteURL($url ) {
			$url_host	=	$this->pz_GetURLHostWithPort($url );
			if	(!$url_host ) {
				return	false;
			}
			$site_hosts	=	array_filter(array_unique(array(
				$this->pz_GetURLHostWithPort(home_url() ),
				$this->pz_GetURLHostWithPort(site_url() ),
				$this->pz_GetURLHostWithPort(admin_url() ),
			) ) );
			foreach	($site_hosts as $site_host ) {
				if	(hash_equals($site_host, $url_host ) ) {
					return	true;
				}
			}
			return	false;
		}

		private	function	pz_IsSameSiteAjaxRequest() {
			$source_url		=	'';
			if	(!empty($_SERVER['HTTP_ORIGIN'] ) ) {
				$source_url	=	esc_url_raw(wp_unslash($_SERVER['HTTP_ORIGIN'] ) );
			}
			if	(!$source_url && !empty($_SERVER['HTTP_REFERER'] ) ) {
				$source_url	=	esc_url_raw(wp_unslash($_SERVER['HTTP_REFERER'] ) );
			}
			if	(!$source_url ) {
				return	false;
			}

			$source_host	=	$this->pz_GetURLHostWithPort($source_url );
			if	(!$source_host ) {
				return	false;
			}

			return	$this->pz_IsSiteURL($source_url );
		}

		// 管理画面関連フック
		private	function	hook_admin_actions() {
			add_action		('admin_init',								[$this, 'action_admin_init' ] );								// 管理画面初期化
			add_action		('admin_menu',								[$this, 'action_admin_menu' ] );								// 設定メニュー
			add_action		('admin_menu',								[$this, 'action_admin_menu_order' ], 999 );						// 設定メニュー順序
			add_action		('admin_enqueue_scripts',					[$this, 'action_admin_enqueue_scripts' ] );						// 設定メニュー用スクリプト
			add_action		('admin_print_styles',						[$this, 'action_admin_print_styles' ] );						// スタイルシートの追加
			add_action		('admin_print_scripts',						[$this, 'action_admin_print_scripts' ] );						// スクリプトの追加
			add_action		('admin_head',								[$this, 'action_admin_head' ] );								// 設定メニュー用スクリプト
			add_action		('admin_notices',							[$this, 'action_admin_notices' ] );								// 注意書き
			add_action		('admin_footer',							[$this, 'action_admin_footer' ] );								// 設定メニュー用スクリプト
			add_action		('admin_footer-plugins.php',				[$this, 'action_admin_footer_plugins' ] );						// プラグイン削除時のブラウザー保存値削除
			add_action		('admin_print_footer_scripts',				[$this, 'action_admin_print_footer_scripts' ] );				// テキストエディタ用クイックタグ
			add_action		('current_screen',							[$this, 'action_current_screen'] );								// 画面判定
			add_action		('enqueue_block_editor_assets',				[$this, 'action_block_editor_assets'] );						// ブロックエディタ
			add_action		('enqueue_block_assets',					[$this, 'action_block_assets'] );								// ブロックエディタ本文
			add_action		('admin_post_save_options',					[$this, 'action_admin_post_save_options'] );					// 設定保存時チェック
			add_action		('admin_post_pz_lkc3_export_file',			[$this, 'action_admin_post_export_file'] );						// エクスポートしたファイル
			add_action		('wp_ajax_pz_lkc3_generate_css',			[$this, 'pz_generate_css_callback'] );							// CSS生成コールバック
			add_action		('wp_ajax_pz_lkc3_generate_html',			[$this, 'pz_generate_html_callback'] );							// HTML生成コールバック
			add_action		('wp_ajax_pz_lkc3_save_cacheman_columns',	[$this, 'pz_save_cacheman_columns_callback'] );					// 管理画面の表示オプション
			// フィルター系
			add_filter		('plugin_action_links_'.$this->base_name,	[$this, 'filter_plugin_action_links' ] );		// プラグイン画面
			add_filter		('mce_external_plugins',					[$this, 'filter_mce_external_plugins' ],		intval($this->options['mce-priority'] ), 1 );	// ビジュアルエディタ用ボタン
			add_filter		('mce_buttons',								[$this, 'filter_mce_buttons' ],					intval($this->options['mce-priority'] ), 1 );	// ビジュアルエディタ用ボタン
		}

		// 一般画面関連フック
		private	function	hook_frontend_actions() {
			add_action		('wp_enqueue_scripts',						[$this, 'action_wp_enqueue_scripts'] );							// スタイルシート・クリックカウントなど
			if	($this->options['auto-atag'] || $this->options['auto-url'] ) {															// 自動置き換え（URLのみの行、リンクのみの行関連）
				add_filter		('the_content',							[$this, 'auto_replace'] );
				add_shortcode	('pz-linkcard3-auto-replace',			[$this, 'shortcode' ], 65535);
			}
		}

		// テキストリンクの行とURLのみの行をリンクカードへ置き換える処理（直接HTMLタグにするのでは無くショートコードに変換する。）
		public	function	auto_replace($content ) {
			// 外部リンクのみ変換する
			if		($this->options['auto-external'] ) {
				if	($this->options['auto-atag'] ) {
					preg_match_all('/(^|<br ?\/?>)(<p.*>)?(<a\s.*href\s*=\s*[\'"]?((http|https):\/\/[^\s<>]+)[\'"]?[^<]*<\/a>)(<\/p>)?$/im', $content, $m );
					for ($i = 0 ; $i < count($m[0]) ; $i++ ) {
						$url			=	$m[4][$i];
						$url_info		=	$this->pz_GetURLInfo($url );	// URL解析（自サイトチェック）
						if		($url_info['is_internal'] ) {
							$tag_from	=	$m[0][$i];
							$tag_to		=	'[pz-linkcard3-auto-replace url="'.$url.'"]';
							$content	=	str_replace($tag_from, $tag_to, $content );
						}
					}
				}
				if	($this->options['auto-url'] ) {
					preg_match_all('/(^|<br ?\/?>)(<p.*>)?((http|https):\/\/[^\s<>]+)(<\/p>|<br ?\/?>)?$/im', $content, $m );
					for ($i	= 0 ; $i < count($m[0]) ; $i++ ) {
						$url			=	$m[3][$i];
						$url_info		=	$this->pz_GetURLInfo($url );	// URL解析（自サイトチェック）
						$is_external	=	$url_info['is_external'];		// 外部リンク
						if		($url_info['is_internal'] ) {
							$tag_from	=	$m[0][$i];
							$tag_to		=	'[pz-linkcard3-auto-replace url="'.$url.'"]';
							$content	=	str_replace($tag_from, $tag_to, $content );
						}
					}
				}
			} else {
				// 内部リンクも外部リンクも変換する
				if	($this->options['auto-atag'] ) {
					$content	=	preg_replace('/(^|<br ?\/?>)(<p.*>)?<a\s.*href\s*=\s*[\'"]?((http|https):\/\/[^\s<>\'"]+)[\'"]?[^<]*<\/a>(<\/p>)?$/im', '[pz-linkcard3-auto-replace url="$3"]', $content );
				}
				if	($this->options['auto-url'] ) {
					$content	=	preg_replace('/(^|<br ?\/?>)(<p.*>)?((http|https):\/\/[^\s<>]+)(<\/p>|<br ?\/?>)?$/im', '[pz-linkcard3-auto-replace url="$3"]', $content );
				}
			}

			// 自動変換を行った場合、ショートコード実行する
			if	($this->options['flg-do-shortcode'] && ($this->options['auto-atag'] || $this->options['auto-url'] ) ) {
				$content	=	do_shortcode($content );
			}
			return	$content;
		}

		// ショートコード処理
		public	function	shortcode($atts, $content = null, $shortcode = null ) {
			$is_debug_mode	=	!empty($this->options['debug-mode'] );
			$is_log_mode	=	$is_debug_mode && !empty($this->options['log-mode'] );

			$get_first_attribute = function($names, $escape_callback) use ($atts) {
				foreach	($names as $name ) {
					if	(isset($atts[$name] ) && $atts[$name] !== '' ) {
						return call_user_func($escape_callback, $atts[$name] );
					}
				}
				return '';
			};

			$atts['url']	=	$get_first_attribute(array('url', 'href', 'uri', 'ur1', 1 ), 'esc_url' );
			$url_org		=	$atts['url'];

			if	($atts['url'] && preg_match('/((http|https):\/\/[^\s<>,\']+)/sui', $atts['url'], $m ) ) {
				$atts['url']	=	$m[1];
			}

			if	($atts['url'] ) {
				$atts['url']	=	$this->pz_EncodeURL($atts['url'], true );
			}

			if	(!filter_var($atts['url'], FILTER_VALIDATE_URL ) ) {
				if	(!$this->options['error-mode'] ) {
					$post_id	=	get_the_ID();
					if	($post_id ) {
						$this->options['error-mode']		=	true;
						$this->options['error-postid']	=	$post_id;
						$this->options['error-url']		=	get_permalink();
						$this->options['error-time']		=	current_time('timestamp', false );
						$this->pz_SaveOptions();
					}
				}
				$error_message	=	__('-', 'pz-linkcard3' ).' '.__('Incorrect URL specification.', 'pz-linkcard3' );
				$error_url		=	__('-', 'pz-linkcard3' ).' '.__('URL', 'pz-linkcard3' ).'='.esc_html($url_org ?? '' );
				$tag			=	'<div class="linkcard3"><a id="lkc3-error"></a><div class="lkc3-wrap lkc3-external-wrap lkc3-unlink"><div class="lkc3-card"><div class="lkc3-info">'.$this->plugin_name.'</div><div class="lkc3-contents"><div class="lkc3-excerpt">'.$error_message.'<br>'.$error_url.'</div></div></div></div></div>';
				return	PHP_EOL.$tag.PHP_EOL.PHP_EOL;
			}

			$atts['title']		=	$get_first_attribute(array('title' ), 'esc_html' );
			$atts['excerpt']	=	$get_first_attribute(array('excerpt', 'content', 'contents', 'description' ), 'esc_html' );

			if	($this->options['use-inline'] && $shortcode == $this->options['code1'] ) {
				switch	($this->options['use-inline'] ) {
				case	1:
					$atts['excerpt']	=	isset($content ) ? esc_html($content ) : '';
					break;
				case	2:
					$atts['title']		=	isset($content ) ? esc_html($content ) : '';
					break;
				}
			}

			if	(!empty($atts['nofollow'] ) && strtolower($atts['nofollow'] ) === 'true' ) {
				$atts['nofollow']	=	1;
			} elseif	(!empty($atts['follow'] ) && strtolower($atts['follow'] ) === 'no' ) {
				$atts['nofollow']	=	1;
				unset($atts['follow'] );
			}

			$data	=	$this->pz_GetLinkCard($atts );
			$tag	=	$this->pz_GetHTML($data );

			return	$tag;
		}

		// キャッシュやリンク先からリンクカードのHTMLを生成
		private	function	pz_GetLinkCard($atts, $force = false ) {
			$post_id		=	$atts['post_id'] ?? '';
			$card_id		=	$atts['card_id'] ?? '';
			$url			=	$atts['url'] ?? '';
			$url_redir		=	$atts['url_redir'] ?? '';

			// 投稿IDもデータIDもURLも指定なし
			if	(!$post_id && !$card_id && !$url ) {
				$tag		=	'<div class="linkcard3"><a id="lkc3-error"></a><div class="lkc3-wrap lkc3-external-wrap lkc3-unlink"><div class="lkc3-info">'.$this->plugin_name.'</div><div class="lkc3-contents"><div class="lkc3-excerpt">'.__('No key specified.', 'pz-linkcard3' ).'<br>'.__('URL', 'pz-linkcard3' ).'='.esc_html($url ?? '' ).'</div></div></div></div>';
				return			PHP_EOL.$tag.PHP_EOL;
			}

			// URL解析（自サイトチェック）
			$url_info		=	$this->pz_GetURLInfo($url );
			$scheme			=	$url_info['scheme'];			// スキーム
			$domain			=	$url_info['domain'];			// ドメイン名
			$domain_url		=	$url_info['domain_url'];		// ドメインURL
			$is_external	=	$url_info['is_external'];		// 外部リンク
			$is_internal	=	$url_info['is_internal'];		// 内部リンク
			$is_unlink		=	false;							// リンク先エラー

			// モバイル判定
			$is_mobile		=	(function_exists('wp_is_mobile' ) && wp_is_mobile() )	?	true	:	false;	// モバイル端末からのアクセス

			// キャッシュ更新フラグ（内部リンクの場合のみ）
			$flg_update		=	false;

			// 投稿IDとURLをセット（パーマリンクの設定が変わっている場合、URLを修正する）
			if		($is_internal ) {
				if	(!$post_id && $url ) {							// 投稿IDが無い場合、URLから投稿IDを取得
					$post_id		=	url_to_postid($url );
					if	($post_id === 0 ) {
						$post_id	=	'';
					}
				}
				if	(!$url && $post_id ) {							// URLが無い場合、投稿IDからURLを取得
					$url_temp		=	get_permalink($post_id );
					if	($url		<>  $url_temp ) {				// URLが違う場合、URLを修正
						if	($url_redir	<>	$url_temp ) {
							$url_redir	=	$url_temp;				// リダイレクト先URLをセット
							$flg_update	=	true;					// 更新が必要
						}
					}
				}
			}

			// キャッシュから記事内容を取得
			$data		=	[];
			$cache		=	['card_id' => $card_id, 'url' => $url, 'post_id' => $post_id ];	// データをセット
			$cache		=	$this->pz_GetCache($cache );
			if	(is_array($cache ) && array_key_exists('card_id', $cache ) && $cache['card_id'] ) {
				$data	=	$cache;				// キャッシュがあった場合、キャッシュデータをセット
				$flg_cache	=	true;			// キャッシュがある
				$flg_update	=	false;			// 更新は不要
			} else {
				$flg_cache	=	false;			// キャッシュが無い
				$flg_update	=	true;			// 新規作成する
			}

			// 内部リンク
			if	($is_internal ) {
				$posts		=	['card_id' => $card_id, 'url' => $url, 'post_id' => $post_id ];	// データをセット
				$posts		=	$this->pz_GetPost($posts );	// 投稿データを取得
				// 投稿データが取得できた
				if	(is_array($posts ) && array_key_exists('update_result', $posts ) && $posts['update_result'] ) {
					if	($url <> $posts['url'] ) {
						if	($data['url_redir'] <> $posts['url'] ) {
							$data['url_redir']	=	$posts['url'];	// リダイレクト先URLをセット
							$flg_update	=	true;					// 更新が必要
						}
					}

					// キャッシュデータに投稿データをセット
					foreach	($posts as $key => $value ) {
						if	((array_key_exists($key, $data ) && $data[$key] <> $value ) || !array_key_exists($key, $data ) ) {	// キャッシュに無いデータはセットする
							$data[$key]	=	$value;					// キャッシュデータに投稿データをセット
							$flg_update	=	true;					// 更新が必要
						}
					}

					// 登録時情報
					if	(empty($data['regist_time'] ) ) {
						$data['regist_title']	=	$posts['title'] ?? '';				// 登録時のタイトル
						$data['regist_excerpt']	=	$posts['excerpt'] ?? '';			// 登録時の抜粋
						$data['regist_charset']	=	$posts['charset'] ?? '';			// 登録時の文字コード
						$data['regist_result']	=	$posts['update_result'] ?? '';		// 登録時のHTTP結果コード
						$data['regist_time']	=	current_time('timestamp', false );	// 登録時の日時
					}
					$data	=	array_merge($data, $posts );	// キャッシュデータに投稿データをマージ
				}
			} else {
				// ローカルアドレス確認
				$result	=	$this->pz_CheckLocalAddress($url );	// ローカルアドレス確認
				if	($result ) {
					$is_external	=	false;
					$is_unlink		=	true;			// リンク先エラー

					$data['id']					=	$data['id'] ?? '';								// リンクカードID
					$data['url']				=	$url;											// リンク先：URL
					$data['url_key']			=	$data['url_key'] ?? '';							// リンク先：URLハッシュ値
					$data['domain']				=	$data['domain'] ?? '';							// リンク先：URLドメイン
					$data['title']				=	$result;										// リンク先：タイトル
					$data['excerpt']			=	'';												// リンク先：抜粋文
					$data['regist_title']		=	$data['title'] ?? '';							// リンク先：タイトル
					$data['regist_excerpt']		=	$data['excerpt'] ?? '';							// リンク先：抜粋文
					$data['regist_time']		=	$this->now;										// 登録時：登録日時
					$data['regist_result']		=	800;											// 登録時：HTTPレスポンス
					$data['sns_twitter']		=	null;
					$data['sns_facebook']		=	null;
					$data['sns_hatena']			=	null;
					$data['sns_nexttime']		=	99999999999;	// 5138/11/16 00:46:39			// SNS：次回取得日時
					$data['alive_nexttime']		=	99999999999;	// 5138/11/16 00:46:39			// 生存確認：次回確認日時
					$data['update_time']		=	$this->now;										// 更新：最終更新日
					$data['update_result']		=	800;											// 更新：HTTPレスポンス

					$flg_update	=	true;
				}
			}

			// 登録日時が未セット
			if	(empty($data['regist_time'] ) ) {
				$title		=	__('In preparation...', 'pz-linkcard3' );
				$excerpt	=	'';
			}

			// キャッシュを更新する
			if	($flg_update  ) {
				$result		=	$this->pz_SetCache($data ); // キャッシュに保存
				if	(is_array($result ) && !empty($result['card_id'] ) ) {
					$data	=	$result;
				}
				$flg_update	=	false;		// 更新フラグをリセット
			}

			// 記事内容取得をスケジュールする
			if	(empty($data['regist_time'] ) ) {
				if	(!wp_next_scheduled($this->cron_regist ) ) {
					// wp_clear_scheduled_hook($this->cron_regist );			// WP-CRONスケジュール停止（記事内容取得）
					wp_schedule_single_event(time() + 3, $this->cron_regist );	// WP-CRONスケジュール登録（記事内容取得）時刻はUTCで設定
				}
			}

			// パラメーター項目
			if	($atts['title']		??	'' ) {
				$data['title']		=	$atts['title'];
			}
			if	($atts['excerpt']	??	'' ) {
				$data['excerpt']	=	$atts['excerpt'];
			}
			$data['nofollow']		=	$atts['nofollow'] ?? '';

			return	$data;		// 取得したデータを返却
		}

		// キャッシュやリンク先からリンクカードのHTMLを生成
		private	function	pz_GetHTML($data, $prop = null ) {
			if	(!$prop ) {
				$prop	=	$this->options;
			}

			$html_date			=	'';
			$html_date_r		=	'';
			$html_cat_list		=	'';
			$html_info			=	'';

			$html_a_op			=	'';
			$html_a_cl			=	'';
			$html_st_op			=	'';
			$html_st_cl			=	'';

			// データ項目
			$card_id			=	$data['card_id']		??	null;			// データID
			$url				=	$data['url']			??	null;			// URL
			$url_redir			=	$data['url_redir']		??	null;			// リダイレクト先URL
			$domain				=	$data['domain']			??	null;			// ドメイン名
			$sitename			=	$data['site_name']		??	null;			// サイト名
			$siteicon_url		=	$data['site_icon']		??	null;			// サイトアイコン
			$title				=	$data['title']			??	null;			// タイトル
			$excerpt			=	$data['excerpt']		??	null;			// 抜粋文
			$thumbnail_url		=	$data['thumbnail']		??	null;			// サムネイル
			$no_failure			=	$data['no_failure']		??	null;			// リンク先エラーを無視
			$post_id			=	$data['post_id']		??	null;			// 投稿ID
			$post_date			=	$data['post_date']		??	null;			// 投稿日時
			$post_modified		=	$data['post_modified']	??	null;			// 更新日時
			$post_cat			=	$data['post_cat']		??	null;			// カテゴリ リスト
			$sns_tw				=	$data['sns_twitter']	??	null;			// Twitterカウント
			$sns_fb				=	$data['sns_facebook']	??	null;			// Facebookカウント
			$sns_hb				=	$data['sns_hatena']		??	null;			// はてなブックマークカウント
			$update_result		=	$data['update_result']	??	null;			// HTTP結果コード
			$alive_result		=	$data['alive_result']	??	null;			// 生存チェック時のHTTP結果コード
			$regist_result		=	$data['regist_result']	??	null;			// 登録した時のHTTP結果コード
			$regist_time		=	$data['regist_time']	??	null;			// 登録した日時

			if	(!$update_result ) {
				$update_result	=	$alive_result;				// HTTP結果コードが無かったら、生存チェック時のHTTP結果コードを使用
			}

			// URL解析（自サイトチェック）
			$url_info		=	$this->pz_GetURLInfo($url );
			$scheme			=	$url_info['scheme'];			// スキーム
			$domain			=	$url_info['domain'];			// ドメイン名
			$domain_url		=	$url_info['domain_url'];		// ドメインURL
			$is_external	=	$url_info['is_external'];		// 外部リンク
			$is_internal	=	$url_info['is_internal'];		// 内部リンク
			$is_unlink		=	false;							// リンク先エラー
			$is_mobile		=	(function_exists('wp_is_mobile' ) && wp_is_mobile() ) ? true : false;	// モバイル判定

			if	($is_internal ) {
				$linktype	=	'internal';			// 内部リンク
				$pref		=	'in';
			} else {
				$linktype	=	'external';			// 外部リンク
				$pref		=	'ex';
			}
			$sw_target			=	$prop[$pref.'-target'] ? ' target="_blank"' : '';	// target属性

			// 外部リンクの rel="nofollow" 判定
			$rel				=	'';
			if	($prop['flg-noopener'] ) {
				$rel			.=	'noopener ';
			}
			if	(($is_external && $prop['flg-nofollow'] ) || ( isset($data['nofollow'] ) && $data['nofollow'] ) ) {
				$rel			.=	'nofollow ';
			}
			if	($rel ) {
				$rel			=	'rel="'.trim($rel ).'"';
			}

			// リンク先URL
			$link_url	=	$url_redir ? $url_redir : $url;
			if	(!$no_failure && $prop['flg-unlink'] && (($update_result > 0 && $update_result < 100 ) || $update_result >= 400 ) ) {
				// Not Found の時は見え消ししてリンクしない
				$is_unlink		=	true;
				$html_wrap_op	=	'<div class="lkc3-wrap lkc3-'.$linktype.'-wrap lkc3-unlink">';
				$html_wrap_cl	=	'</div>';
				$html_a_op		=	'';
				$html_a_cl		=	'';
				$html_st_op		=	'<strike>';
				$html_st_cl		=	'</strike>';
			} elseif	($prop['link-all'] ) {
				// カード全体をリンク（どこをクリックしても良いのが分かり易い）
				$is_unlink		=	false;
				$html_wrap_op	=	'<a class="lkc3-wrap lkc3-'.$linktype.'-wrap lkc3-link no_icon" href="'.esc_url($link_url ).'"'.$sw_target.$rel.'>';
				$html_wrap_cl	=	'</a>';
				$html_a_op		=	'';
				$html_a_cl		=	'';
				$html_st_op		=	'';
				$html_st_cl		=	'';
			} else {
				// タイトルとかURLとかを個別でリンク（タイトルや抜粋文などの文字を範囲指定をしてコピー等がし易い）
				$is_unlink		=	false;
				$html_wrap_op	=	'<div class="lkc3-wrap lkc3-'.$linktype.'-wrap">';
				$html_wrap_cl	=	'</div>';
				$html_a_op		=	'<a class="lkc3-link no_icon" href="'.esc_url($link_url ).'"'.$sw_target.$rel.'>';
				$html_a_cl		=	'</a>';
				$html_st_op		=	'';
				$html_st_cl		=	'';
			}

			// HTMLブロックの初期化
			$html_info		=	null;		// サイト情報
			$html_domain	=	null;		// サイト情報（ドメイン名）
			$html_sitename	=	null;		// サイト情報（サイト名）
			$html_siteicon	=	null;		// サイト情報（サイトアイコン）
			$html_cat_list	=	null;		// サイト情報（カテゴリー）
			$html_date_r	=	null;		// サイト情報（投稿日・更新日（右詰め））
			$html_added		=	null;		// サイト情報（付加情報（サイト区分））
			$html_thumbnail	=	null;		// サムネイル
			$html_heading	=	null;		// ヘッダー情報（見出し）
			$html_contents	=	null;		// 記事内容
			$html_title		=	null;		// 記事内容（タイトル）
			$html_excerpt	=	null;		// 記事内容（抜粋文）
			$html_url		=	null;		// 記事内容（URL）
			$html_sns		=	null;		// 記事内容（ソーシャル数）
			$html_date		=	null;		// 記事内容（投稿日・更新日）
			$html_more		=	null;		// 記事内容（続きを読むボタン）
			$html_card		=	null;		// リンクカード本体
			$html_tag		=	null;		// 返却するHTML
			$html_cat_list	=	null;		// カテゴリ一覧

			// 置き換えテーブル
			$rep_src		=	array('%TITLE%', '%EXCERPT%', '%SITE_NAME%', '%DOMAIN_URL%', '%DOMAIN%', '%URL%',             '%PLUGIN_NAME%',		'%PLUGIN_VERSION%',		'%WP_VERSION%', 	'%CURL_VERSION%',          '%PHP_VERSION%', '%MY_URL%',		'%USER_AGENT',		'%%', );
			$rep_desc		=	array( $title,    $excerpt,    $sitename,    $domain_url,    $domain,    rawurlencode($url ),  $this->plugin_name,	$this->plugin_version,  $this->wp_version,  curl_version()['version'], phpversion(),	$this->my_url,  $this->user_agent,  '%',  );

				// ドメイン名の準備
			if	($domain ) {
				if	(function_exists('idn_to_utf8' ) && $this->pz_StrStartsWith($domain, 'xn--' ) ) {	// 国際ドメイン対応（日本語ドメイン対応）
					$domain			=	idn_to_utf8($domain, 0, INTL_IDNA_VARIANT_UTS46 );
				}
				if	($domain ) {
					$html_domain	=	esc_html($domain );								// HTMLエスケープ
				}
			}

			// 表示用サイト名（サイト名が無い場合はドメイン名を使用）
			$disp_sitename_src	=	$sitename ? $sitename : $domain;
			$disp_sitename		=	esc_html($disp_sitename_src );						// サイト名（ドメイン名が無い場合は空欄）;
			if	($disp_sitename ) {
				$title_sitename	=	' title="'.esc_attr($disp_sitename_src ).'" ';
				$html_sitename	=	'<div class="lkc3-sitename"'.$title_sitename.'>'.$disp_sitename.'</div>';
			}

			// タイトルと抜粋文の設定
			if	(empty($regist_time ) ) {
				$title			=	__('Waiting...', 'pz-linkcard3' );
				$excerpt		=	'';
			} else {
				if	(intval($update_result ) !== 0 ) {
					// タイトル整形
					$temp			=	$title ?? '';										// タイトル
					$temp			=	wp_strip_all_tags($temp );							// HTMLタグ除去
					$temp			=	str_replace(array("\r", "\n"), '', $temp );			// 改行を除去
					$temp			=	esc_html($temp );									// エスケープ
					$title			=	$temp;

					// 抜粋文整形（抜粋文非表示の場合、空欄にする）
					$temp			=	$excerpt ?? '';										// 抜粋文
					$temp			=	wp_strip_all_tags($temp, '<br>' );					// HTMLタグ除去
					$temp			=	preg_replace('/<!--more-->.+/is', '', $temp );		// moreタグ以降削除
					$temp			=	preg_replace('/\[[^]]*\]/', '', $temp );			// ショートコードすべて除去
					$temp			=	str_replace(array("\r", "\n" ), '', $temp );		// 改行を除去
					$temp			=	esc_html($temp );									// エスケープ
					$temp			=	str_replace('&lt;br&gt;', '<span style="opacity: 0.5;">'.__('&#x23CE;&#xFE0F;', 'pz-linkcard3' ).'</span>', $temp );		// 改行マーク
					$excerpt		=	$temp;
				}
			}
			$html_title		=	$html_a_op.'<div class="lkc3-title">'.$title.'</div>'.$html_a_cl;
			$html_excerpt	=	'<div class="lkc3-excerpt">'.$excerpt.'</div>';

			// Twitterポスト取得
			// if	($is_external && !$title && !$excerpt && $domain === 'x.com') {
			// 	$twitter	=	$this->pz_GetTwitter(['url' => $url] );
			// }

			if	($url_redir ) {
				$url			=	$url_redir;
			}

			// リンク先URL
			$disp_url_src		=	$this->pz_DecodeURL($url, true );
			$disp_url			=	esc_html($disp_url_src );								// 表示用
			if	($disp_url ) {
				$html_url		=	'<div class="lkc3-url" title="'.esc_attr($disp_url_src ).'">'.$html_a_op.$html_st_op.$disp_url.$html_st_cl.$html_a_cl.'</div>';
			}

			// SNSカウントの表示
			if	($prop['debug-sns'] ) { $sns_tw = wp_rand(1, 100000 ); $sns_fb = wp_rand(1, 100000 ); $sns_hb = wp_rand(1, 100000 ); }	// SNSカウントをランダム値にする（デバッグ用）

			$sns			=	'';
			$url_noscheme	=	preg_replace('/(http|https):\/\/(.*)/', '$2', $url );		// スキームを外す
			$url_noscheme_enc	=	rawurlencode($url_noscheme );
			$title_enc			=	rawurlencode(wp_strip_all_tags($title ) );
			$sns_label			=	function($count, $single, $plural) {
				return	(intval($count ) === 1 ) ? $single : $plural;
			};
			if	($prop['sns-tw'] && ($sns_tw ?? 0 ) ) {
				if	($prop['sns-tw-old'] ) {
					$sns	.=	' <object><a class="lkc3-sns-tw no_icon" href="'.esc_url('https://x.com/search?q='.$url_noscheme_enc.'&text='.$title_enc ).'" target="_blank">'.	$sns_tw.' '.$sns_label($sns_tw, 'tweet', 'tweets' ).'</a></object>';
				} else {
					$sns	.=	' <object><a class="lkc3-sns-x  no_icon" href="'.esc_url('https://x.com/search?q='.$url_noscheme_enc.'&text='.$title_enc ).'" target="_blank">'.	$sns_tw.' '.$sns_label($sns_tw, 'post', 'posts' ).'</a></object>';
				}
			}
			if	($prop['sns-fb'] && ($sns_fb ?? 0 ) ) {
				$sns		.=	' <object><a class="lkc3-sns-fb no_icon" href="https://www.facebook.com/" target="_blank">'.														$sns_fb.' '.$sns_label($sns_fb, 'share', 'shares' ).'</a></object>';
			}
			if	($prop['sns-hb'] && ($sns_hb ?? 0 ) ) {
				$sns		.=	' <object><a class="lkc3-sns-hb no_icon" href="'.esc_url('https://b.hatena.ne.jp/entry/s/'.$url_noscheme ).'" target="_blank">'.					$sns_hb.' '.$sns_label($sns_hb, 'user', 'users' ).'</a></object>';
			}
			if	($sns ) {
				$html_sns	=	'<div class="lkc3-sns-list">'.$sns.'</div>';
			}

			// 投稿日＆更新日
			$icon1		=	$prop['post-date-icon1'];
			$icon2		=	$prop['post-date-icon2'];
			$temp_date	=	'';
			switch		($prop['post-date-style'] ) {
			case	2:			// 更新日
				if	($post_date) {
					if	($post_date < $post_modified ) {
						$temp_date	=	$icon2.$this->pz_date($prop['date-format'], $post_modified );
					} else {
						$temp_date	=	$icon1.$this->pz_date($prop['date-format'], $post_date );
					}
				}
				break;
			case	3:			// 投稿日＆更新日
				if	($post_date) {
					if	($post_date < $post_modified ) {
						$temp_date	=	$icon1.$this->pz_date($prop['date-format'], $post_date ).'&ensp;'.$icon2.$this->pz_date($prop['date-format'], $post_modified );
					} else {
						$temp_date	=	$icon1.$this->pz_date($prop['date-format'], $post_date );
					}
				}
				break;
			default:			// 投稿日
				if	($post_date ) {
					$temp_date		=	$icon1.$this->pz_date($prop['date-format'], $post_date );
				}
			}
			if		($temp_date ) {
				$html_date			=	'<div class="lkc3-date">'.  $temp_date.'</div>';
				$html_date_r		=	'<div class="lkc3-date-r">'.$temp_date.'</div>';
			}

			// カテゴリー
			if	(isset($post_cat )		&&	is_array($post_cat ) ) {
				foreach ($post_cat		as	$key => $value ) {
					// カテゴリURL
					$cat_style			=	'';

					$cat_color			=	get_term_meta($key, 'the_category_text_color', true );	// カテゴリ文字色（Cocoon）
					if	($cat_color ) {
						$cat_style		.=	'color: '.$cat_color.'; ';
					}

					$cat_bgcolor		=	get_term_meta($key, 'the_category_color', true );		// カテゴリ背景色（Cocoon）
					if	($cat_bgcolor ) {
						$cat_style		.=	'background-color: '.$cat_bgcolor.'; ';
					}

					if	(function_exists('get_field' ) ) {
						$cat_bgcolor	=	get_field('color', 'category_'.$key );					// カテゴリ背景色（ACF : Advanced Custom Fields）
						if	($cat_bgcolor ) {
							$cat_style	.=	'background-color: '.$cat_bgcolor.'; ';
						}
					}
					if	($cat_style ) {
						$cat_style		=	' style="'.esc_attr($cat_style ).'"';
					}

					$cat_item			=	'<object><a class="lkc3-cat no_icon" href="'.esc_url($value['link'] ).'" rel="noopener"'.$cat_style.'>'.esc_html($value['name'] ).'</a></object>';
					$html_cat_list		.=	$cat_item;
				}
			}
			if	($html_cat_list ) {
				$html_cat_list	=	'<div class="lkc3-cat-list">'.$html_cat_list.'</div>';
			}

			// 設定項目
			$sw_heading_text	=	esc_html($prop[$pref.'-heading-text']	??	''	);
			$sw_added_text		=	esc_html($prop[$pref.'-info-text']		??	''	);
			$sw_more_text		=	esc_html($prop[$pref.'-more-text']		??	''	);
			$sw_siteicon		=	esc_attr($prop[$pref.'-siteicon-get']	??	0	);
			$sw_siteicon_alt	=	esc_attr($prop[$pref.'-siteicon-alt']	??	''	);
			$sw_siteicon_size	=	esc_attr($prop['siteicon-size']			??	''	);
			$sw_thumbnail		=	esc_attr($prop[$pref.'-thumbnail-get']	??	0	);
			$sw_thumbnail_alt	=	esc_attr($prop[$pref.'-thumbnail-alt']	??	''	);
			$image_refresh_stamp	=	!empty($data['image_refresh_stamp'] ) ? absint($data['image_refresh_stamp'] ) : 0;

			// 代替テキスト（サムネイル）
			if	($sw_thumbnail_alt  &&  strstr($sw_thumbnail_alt, '%' ) ) {
				$temp			=	$sw_thumbnail_alt;
				$temp			=	str_replace($rep_src, $rep_desc, $temp );
				$sw_thumbnail_alt	=	esc_attr($temp );
			}

			// サムネイル取得
			$html_thumbnail			=	'';
			if	(!$prop['thumbnail-position'] || !$sw_thumbnail ) {
				$thumbnail_url		=	'';
			} else {
				if	($sw_thumbnail == 1 || $sw_thumbnail == 13 ) {						// 直接取得
					if	($thumbnail_url ) {
						if	($is_external ) {
							$thumbnail_url	=	$this->pz_GetImage($thumbnail_url, false, (bool) $image_refresh_stamp, true );		// 外部サイトのサムネイルをキャッシュ
						}
					}
				} else {
					$thumbnail_url			=	'';
				}
				if	(!$thumbnail_url ) {
					if	($sw_thumbnail		==	3 || $sw_thumbnail	==	13 ) {														// WebAPIを利用
						// サムネイル取得WebAPI
						if	($prop['thumbnail-api'] ) {
							$thumbnail_url					=	$prop['thumbnail-api'];
							if	($thumbnail_url  &&  strstr($thumbnail_url, '%' ) ) {
								$temp			=	$thumbnail_url;
								$temp			=	str_replace($rep_src, $rep_desc, $temp );
								$thumbnail_url	=	esc_url($temp );
							}
						}
					}
				}
			}
			if	($thumbnail_url ) {
				if	($image_refresh_stamp ) {
					$thumbnail_url	=	add_query_arg('pz_lkc3_refresh', $image_refresh_stamp, $thumbnail_url );
				}
				$html_thumbnail	=	'<figure class="lkc3-thumbnail"><img class="lkc3-thumbnail-img" src="'.esc_url($thumbnail_url ).'" width="'.esc_attr($prop['thumbnail-width'] ).'" height="'.esc_attr($prop['thumbnail-height'] ).'" alt="'.esc_attr($sw_thumbnail_alt ).'" loading="lazy" /></figure>';
			}

			// 代替テキスト（サイトアイコン）
			if	($sw_siteicon_alt  &&  strstr($sw_siteicon_alt, '%' ) ) {
				$temp			=	$sw_siteicon_alt;
				$temp			=	str_replace($rep_src, $rep_desc, $temp );
				$sw_siteicon_alt	=	esc_attr($temp );
			}

			// サイトアイコン取得
			$html_siteicon				=	'';
			if	(!$sw_siteicon ) {
				$siteicon_url			=	'';
			} else {
				if	($sw_siteicon == 1 || $sw_siteicon == 13 ) {							// 直接取得
					if	($siteicon_url ) {
						if	($is_internal ) {
							$siteicon_url	=	get_site_icon_url(32 );						// 自サイトのサイトアイコン
						} else {
							$siteicon_url	=	$this->pz_GetImage($siteicon_url, false, (bool) $image_refresh_stamp, true );
						}
					}
				} else {
					$siteicon_url			=	'';
				}
				if	(!$siteicon_url ) {													// WebAPIを利用
					if	($sw_siteicon		==	3 || $sw_siteicon		==	13 ) {
						// サイトアイコン取得WebAPI
						if	($prop['siteicon-api'] ) {
							$siteicon_url		=	$prop['siteicon-api'];
							if	($siteicon_url  &&  strstr($siteicon_url, '%' ) ) {				// URLに%があったら置換する
								$temp			=	$siteicon_url;
								$temp			=	str_replace($rep_src, $rep_desc, $temp );
								$siteicon_url	=	esc_url($temp );
							}
						}
					}
				}
			}
			if	($siteicon_url ) {
				if	($image_refresh_stamp ) {
					$siteicon_url	=	add_query_arg('pz_lkc3_refresh', $image_refresh_stamp, $siteicon_url );
				}
				$html_siteicon	=	'<div class="lkc3-siteicon"><img src="'.esc_url($siteicon_url ).'" alt="'.esc_attr($sw_siteicon_alt ).'" width="'.esc_attr($sw_siteicon_size ).'" height="'.esc_attr($sw_siteicon_size ).'" loading="lazy" /></div>';
			} else {
				$html_siteicon	=	'<div class="lkc3-siteicon"><img src="'.esc_url($this->base_url.'img/siteicon_dummy.png' ).'" width="'.esc_attr($sw_siteicon_size ).'" height="'.esc_attr($sw_siteicon_size ).'" alt=""></div>';
			}

			// 見出し情報
			if	($sw_heading_text ) {
				$html_heading	=	'<div class="lkc3-heading">'.$sw_heading_text.'</div>';
			}

			// 続きを読むボタン
			if	(($sw_more_text ) && (!$is_unlink ) ) {
				$html_more		=	$html_a_op.'<div class="lkc3-more">'.$sw_more_text.'</div>'.$html_a_cl;
			}

			// サイト区分
			if	($sw_added_text ) {
				$html_added		=	'<div class="lkc3-added">'.$sw_added_text.'</div>';
			}

			// Google AMP用 簡易タグ作成
			if	($this->flg_amp <> 2 ) {
				if	($this->flg_amp === 0 ) {
					$this->flg_amp				=	2;		// 仮に 2:通常（非AMP）とする
					// AMPプラグインの有無を確認
					if	((function_exists('ampforwp_is_amp_endpoint' ) && ampforwp_is_amp_endpoint() ) || (function_exists('is_amp_endpoint' ) && is_amp_endpoint() ) || (function_exists('is_amp' ) && is_amp() ) ) {
						$this->flg_amp			=	1;		// AMPプラグインがあり、AMPページである
					} else {
						if	($prop['flg-amp-url'] ) {
							if	((substr($this->url_now, 4 ) === '/amp' ) || (substr($this->url_now, 5 ) === '/amp/' ) || (substr($this->url_now, 6 ) === '?amp=1' ) || (substr($this->url_now, 8 ) === 'type=AMP' ) ) {
								$this->flg_amp	=	1;		// 1:AMP
							}
						}
					}
				}
				if	($this->flg_amp === 1 ) {
					$html_tag		=	'<div class="lkc3-external amp"><table border="1" cellspacing="0" cellpadding="4"><tr><td>'.esc_attr($excerpt ).'<br><a href="'.esc_url($url ).'"'.$sw_target.$rel.'>'.esc_attr($title ).'</a>&nbsp;-&nbsp;'.esc_html($sitename ).'</td></tr></table></div>';
					return	$html_tag;						// タグを出力して終了（CSS無し）
				}
			}

			// HTMLタグ（サイト情報）
			if	($prop['info-position'] ) {
				for	($i = 1; $i <= 5; $i++ ) {
					switch	($prop[$pref.'-info-type-'.$i] ) {
					case	'';
						break;
					case	'i':
						$html_info	.=	$html_siteicon;
						break;
					case	'n':
						$html_info	.=	$html_sitename;
						break;
					case	'd':
						$html_info	.=	$html_domain;
						break;
					case	'c':
						$html_info	.=	$html_cat_list;
						break;
					case	't':
						$html_info	.=	$html_title;
						break;
					case	'a':
						$html_info	.=	$html_added;
						break;
					case	'p':
						$html_info	.=	$html_date;
						break;
					case	'q':
						$html_info	.=	$html_date_r;
						break;
					case	's':
						$html_info	.=	$html_sns;
						break;
					default:
						$html_info	.=	'<!-- Unknown InfoType="'.esc_html($prop[$pref.'-info-type-'.$i] ).'" -->';
					}
				}
				if	($html_info ) {
					$html_info		=	'<div class="lkc3-info">'.$html_info.'</div>';
				}
			}

			// HTMLタグ（記事内容）
			for	($i = 1; $i <= 5; $i++ ) {
				switch	($prop[$pref.'-content-type-'.$i] ) {
				case	'':
					break;
				case	'n':
					$html_contents	.=	$html_sitename;
					break;
				case	't':
					$html_contents	.=	$html_title;
					break;
				case	'u':
					$html_contents	.=	$html_url;
					break;
				case	'e':
					$html_contents	.=	$html_excerpt;
					break;
				case	's':
					$html_contents	.=	'<div class="lkc3-line">'.$html_sns.'</div>';
					break;
				case	'p':
					$html_contents	.=	$html_date;
					break;
				case	"q":
					$html_contents	.=	$html_date_r;
					break;
				case	'c':
					$html_contents	.=	'<div class="lkc3-line">'.$html_cat_list.'</div>';
					break;
				case	'i':
					$html_contents	.=	$html_info;
					break;
				default:
					$html_contents	.=	'<!-- Unknown ContentType="'.esc_html($prop[$pref.'-content-type-'.$i] ).'" -->';
				}
			}
			$html_contents		=	'<div class="lkc3-contents">'.$html_contents.$html_more.'</div>';

			// 記事内容＋サムネイル
			$html_column		=	'<div class="lkc3-column">'.$html_contents.$html_thumbnail.'</div>';

			// サイト情報の位置
			switch	($prop['info-position'] ) {
			case	'u':	// 上
				$html_card		=	$html_info.$html_column;
				break;
			case	'd':	// 下
				$html_card		=	$html_info.$html_column;
				break;
			default:
				$html_card		=	$html_column;
			}
			$html_card			=	'<div class="lkc3-card">'.$html_card.'</div>'.$html_heading;

			// ラッピング
			$html_tag			=	$html_wrap_op.$html_card.$html_wrap_cl;

			// リンクカード領域のタグとクラス名
			$enclose_tag			=	$prop['enclose-tag']			?	strtolower($prop['enclose-tag'] )	:	'div';
			if	(!in_array($enclose_tag, array('div', 'blockquote', 'figure', 'article', 'section', 'nav', 'aside' ), true ) ) {
				$enclose_tag		=	'div';
			}
			$enclose_class_add		=	'';
			if			($is_mobile ) {
				$enclose_class_add	=	$prop['enclose-class-mobile']	?	' '.$prop['enclose-class-mobile']	:	'';
			} else {
				$enclose_class_add	=	$prop['enclose-class-pc']		?	' '.$prop['enclose-class-pc']		:	'';
			}
			$enclose_class		=	'linkcard3'.$enclose_class_add;

			// HTMLタグ
			$loaded				=	'';
			if	(empty($data['regist_time'] ) && $card_id ) {
				$lazy_page		=	$this->pz_GetLazyCardPageURL();
				$lazy_token		=	$this->pz_CreateLazyCardToken($card_id, $lazy_page );
				$loaded			=	' data-lazy="true" data-lkc3-token="'.esc_attr($lazy_token ).'" data-lkc3-page="'.esc_attr($lazy_page ).'"';
			}
			$enclose_tag		=	tag_escape($enclose_tag ) ?: 'div';
			$html_quickmenu_icon	=	'';
			if	(!empty($this->options['flg-quickmenu'] ) && is_user_logged_in() && current_user_can('manage_options' ) ) {
				$html_quickmenu_icon	=	'<span class="pz-lkc3-quickmenu-indicator dashicons dashicons-ellipsis" aria-hidden="true"></span>';
			}
			$html_tag			=	'<'.$enclose_tag.' class="'.esc_attr($enclose_class ).'" data-lkc3-id="'.esc_attr($card_id ).'"'.$loaded.'>'.$html_tag.$html_quickmenu_icon.'</'.$enclose_tag.'>';

			// リンクカードのHTMLを返却
			return	$html_tag;
		}

		// URLのエンコード（DB格納用のURL作成）
		private	function	pz_EncodeURL($url = null, $sanitize = false ) {
			// URLのサニタイズ
			if	($sanitize ) {
				$url	=	$this->pz_SanitizeURL($url );
			}

			// URL指定なし
			if	(!$url ) {
				return	'';
			}

			// 日本語がある
			if	(!preg_match("/^[\x20-\x7E]+$/", $url ) ) {

				// 国際ドメイン対応（日本語ドメイン対応）
				$url_info			=	$this->pz_GetURLInfo($url );
				if	(function_exists('idn_to_utf8' ) && !preg_match("/^[\x20-\x7E]+$/", $url_info['domain'] ) ) {
					$domain_before	=	(isset($url_info['scheme'] ) ? $url_info['scheme'] : '').'://'.(isset($url_info['domain'] ) ? $url_info['domain'] : '');
					$domain_after	=	(isset($url_info['scheme'] ) ? $url_info['scheme'] : '').'://'.(isset($url_info['domain'] ) ? idn_to_ascii($url_info['domain'], 0, INTL_IDNA_VARIANT_UTS46 ) : '');
					$url			=	$domain_after.mb_substr($url, mb_strlen($domain_before ) );		// URLのスキーム＋ドメイン部分だけ入れ替え
					}

				// 日本語がある
				if	(!preg_match("/^[\x20-\x7E]+$/", $url ) ) {
					$url	=	$this->pz_EncodeURI($url );			// エンティティ化
				}
			}
			
			// エンコードしたURLを返却		
			return		$url;
		}

		// URLのデコード（表示用URL作成）
		private	function	pz_DecodeURL($url = null, $sanitize = false ) {
			// URL指定なし
			$url	=	esc_url($url );
			if	(!$url ) {
				return	'';
			}

			// 国際ドメイン対応（日本語ドメイン対応）
			$url_m			=	wp_parse_url($url );															// URLパース（ドメイン名などを抽出）
			$scheme			=	isset($url_m['scheme'] )	?	$url_m['scheme'].':'				: '';		// スキーム
			$domain			=	isset($url_m['host'] )		?	$url_m['host']						: '';		// ドメイン名
			if	(function_exists('idn_to_utf8' ) && is_array($url_m ) && key_exists('host', $url_m ) && $this->pz_StrStartsWith($url_m['host'], 'xn--' ) ) {
				$domain_before	=	$scheme.'//'.$domain;
				$domain_after	=	$scheme.'//'.idn_to_utf8($domain, 0, INTL_IDNA_VARIANT_UTS46 );
				$url			=	$domain_after.mb_substr($url, mb_strlen($domain_before ) );				// URLのスキーム＋ドメイン部分だけ入れ替え
			}

			// エンティティ文字のデコード
			do {
				$url			=	rawurldecode($url );
			} while (mb_strpos($url, '%25' ) !== false );		// %25 = % が残っていたら、再度デコード

			// 半角空白があったらエンティティ化（エンコード）
			$url				=	str_replace(' ', '%20', $url );
			$url				=	str_replace("'", '%27', $url );

			// 変なのが残っていたらエスケープ
			$url				=	esc_url($url );

			// デコードしたURLを返却
			return		$url;
		}

		// URLのサニタイズ
		private	function	pz_SanitizeURL($url = null ) {
			// URLの指定なし
			$url	=	esc_url($url );
			if	(!isset($url ) ) {
				return	'';
			}

			// Aタグがあったら最初にあるAタグのhrefを持ってくる
			//if	(preg_match('/<a .*href\s*=\s*[\'"]?([^ \'"<>$]+)/sui', $url, $m ) ) {
			//	$url	=	$m[1];
			//}

			// 一部の記号を除去する
			$url		=	preg_replace('/^[\'"‘’“”″]+|[\'"‘’“”″]+$/', '', $url );		// 前後のクォート文字を除去する
			$url		=	str_replace(' ', '+', $url );

			// 最初にあるURLっぽいのを持ってくる
			if	(preg_match('/((http|https):\/\/[^\s<>]+)/sui',	$url, $m ) ) {
				$url	=	$m[1];
			}

			// エスケープ
			$url		=	str_replace(array(' ', "'" ), array('+', '%27' ), $url );
			$url		=	esc_url($url );

			// 最後のスラッシュの除去
			switch	($this->options['trail-slash'] ) {
			case	1:							// URLがドメイン名だけの場合、最後のスラッシュを除外する
				$url_info	=	$this->pz_GetURLInfo($url );
				if	(!isset($url_info['path'] ) || $url_info['path'] == '/' ) {
					$url	=	rtrim($url, '/' );
				}
				break;
			case	2:							// 常に最後のスラッシュを除外する
				$url		=	rtrim($url, '/' );
				break;
			}

			// エンティティ文字がある
			if	(mb_strpos($url, '%' ) !== false) {
				$url		=	$this->pz_DecodeURL($url ,false );
			}

			// 日本語がある
			if	(!preg_match('/^[\x20-\x7E]+$/', $url ) ) {
				$url		=	$this->pz_EncodeURL($url, false);
			}

			// サニタイズしたURLを返却する（エンティティ化済）
			return	$url;
		}

		// URLのパース（外部サイト・内部サイト・同ページの判定）
		private	function	pz_GetURLInfo($url ) {
			// URLの指定なし
			$url	=	esc_url($url );
			if	(!isset($url ) ) {
				return	'';
			}

			// 変数
			$is_external		=	false;		// 外部リンク
			$is_internal		=	false;		// 内部リンク
			$card_scheme		=	'';			// スキーム https
			$card_domain		=	'';			// ドメイン popozure.info
			$card_domain_url	=	'';			// ドメインURL https://popozure.info
			$is_error			=	false;		// エラー状態

			// ショートコードで指定されたURLをパース
			$card_url			=	$this->pz_DecodeURL($url );							// リンクカードのURL
			$card_url			.=	(mb_substr($card_url, -1, 1) <> '/' ? '/' : '' );	// URLの最後にスラッシュが無い場合は付ける
			$card_url_m			=	wp_parse_url($card_url );							// URLパース（ドメイン名などを抽出）
			$card_scheme		=	$card_url_m['scheme'] ?? '';						// スキーム
			$card_domain		=	$card_url_m['host'] ?? '';							// ドメイン名
			$card_domain_url	=	$card_scheme.'://'.$card_domain;					// ドメインURL
			$card_url_noscheme	=	mb_substr($card_url, mb_strlen($card_scheme ) );	// スキームを外したURL

			// 自サイトのトップURLをパース
			$top_url			=	$this->pz_DecodeURL(home_url() );
			$top_url			.=	(mb_substr($top_url, -1, 1) <> '/' ? '/' : '' );	// URLの最後にスラッシュが無い場合は付ける
			$top_url_m			=	wp_parse_url($top_url );							// URLパース（ドメイン名などを抽出）
			$top_scheme			=	$top_url_m['scheme'] ?? '';							// スキーム
			$top_url_noscheme	=	mb_substr($top_url, mb_strlen($top_scheme ) );		// スキームを外したURL

			// 表示中のページURLをパース
			$now_url			=	$this->pz_DecodeURL($this->url_now );						// 表示中のページのURL
			$now_url			.=	(mb_substr($now_url, -1, 1) <> '/' ? '/' : '' );	// URLの最後にスラッシュが無い場合は付ける

			// 外部リンク、内部リンクの判定
			if	(mb_substr($card_url_noscheme, 0, mb_strlen($top_url_noscheme ) ) === $top_url_noscheme ) {		// トップページURLと同じ
				$is_internal		=	true;				// 内部リンク
			} else {										// 外部サイト
				$is_external		=	true;				// 外部リンク
			}

			// サブディレクトリ型マルチサイト対応（内部リンク判定の場合のみ）
			if	($is_internal && function_exists('is_multisite' ) && is_multisite() && function_exists('is_subdomain_install' ) && !is_subdomain_install() && function_exists('is_main_site' ) && is_main_site() ) {
				$blog_myid		=	get_current_blog_id();
				$blog_id		=	0;
				for ($i = 1; $i <= 10; $i++ ) {
					$blog_url	=	get_site_url($i );
					if	(!$blog_url ) {
						break;
					}
					if	($i <> $blog_myid ) {
						// 自サイトのトップURLをパース
						$blog_url			=	$this->pz_DecodeURL($blog_url );
						$blog_url			.=	(mb_substr($blog_url, -1, 1) <> '/' ? '/' : '' );	// URLの最後にスラッシュが無い場合は付ける
						$blog_url_m			=	wp_parse_url($blog_url );							// URLパース（ドメイン名などを抽出）
						$blog_scheme		=	$blog_url_m['scheme'] ?? '';						// スキーム
						$blog_url_noscheme	=	mb_substr($blog_url, mb_strlen($blog_scheme ) );	// スキームを外したURL
						if	(mb_substr($card_url_noscheme, 0, mb_strlen($blog_url_noscheme ) ) === $blog_url_noscheme ) {		// サブサイトと同じ
							$is_external	=	true;		// 外部リンク
							$is_internal	=	false;
							break;
						}
					}
				}
			}

			if	($is_external === false && $is_internal === false ) {
				// 内部リンクでも外部リンクでも無い場合はエラーとする
				$is_error				=	true;
			}

			// 返り値
			$ret_arr['is_external']	=	$is_external;			// 外部リンク
			$ret_arr['is_internal']	=	$is_internal;			// 内部リンク
			$ret_arr['scheme']		=	$card_scheme;			// スキーム
			$ret_arr['domain']		=	$card_domain;			// ドメイン
			$ret_arr['domain_url']	=	$card_domain_url;		// ドメインURL
			// $ret_arr['port']		=	isset($card_url_m['port'] )			? $card_url_m['port']		: '';		// ポート
			// $ret_arr['user']		=	isset($card_url_m['user'] )			? $card_url_m['user']		: '';		// ユーザー名
			// $ret_arr['pass']		=	isset($card_url_m['pass'] )			? $card_url_m['pass']		: '';		// パスワード
			// $ret_arr['path']		=	isset($card_url_m['path'] )			? $card_url_m['path']		: '';		// パス（ドメイン名以降）
			// $ret_arr['query']		=	isset($card_url_m['query'] )	? $card_url_m['query']		: '';		// クエスチョンマーク ? 以降
			// $ret_arr['fragment']	=	isset($card_url_m['fragment'] )		? $card_url_m['fragment']	: '';		// ハッシュマーク # 以降
			$ret_arr['error']		=	$is_error;
			return		$ret_arr;
		}

		// 日本語URLをHTMLエンコードする
		private	function	pz_EncodeURI($url ) {
			$pattern	=
				array(
					// UnEscaped
					'%2D'=>'-', '%5F'=>'_', '%2E'=>'.', '%21'=>'!', '%25'=>'%', '%7E'=>'~', '%2A'=>'*', '%28'=>'(', '%29'=>')',
					// Reserved
					'%3B'=>';', '%2C'=>',', '%2F'=>'/', '%3F'=>'?', '%3A'=>':', '%40'=>'@', '%26'=>'&', '%3D'=>'=', '%2B'=>'+', '%24'=>'$',
					// Score
					'%23'=>'#'
				);
			$url		=	rawurlencode($url );
			$url		=	strtr($url, $pattern);
			return		$url;
		}

		private	function	pz_StrStartsWith($haystack, $needle ) {
			$haystack	=	(string) $haystack;
			$needle		=	(string) $needle;
			if	($needle === '' ) {
				return	true;
			}
			return	strncmp($haystack, $needle, strlen($needle ) ) === 0;
		}

		private	function	pz_RobotsPathMatches($pattern, $path ) {
			if	($pattern === '' ) {
				return	false;
			}
			$regex	=	preg_quote($pattern, '#');
			$regex	=	str_replace('\*', '.*', $regex );
			if	(substr($regex, -2 ) === '\$' ) {
				$regex	=	substr($regex, 0, -2 ).'$';
			} else {
				$regex	.=	'.*';
			}
			return	preg_match('#^'.$regex.'#', $path ) === 1;
		}

		private	function	pz_GetRobotsTxt($url, $user_agent ) {
			$scheme	=	wp_parse_url($url, PHP_URL_SCHEME );
			$host	=	wp_parse_url($url, PHP_URL_HOST );
			$port	=	wp_parse_url($url, PHP_URL_PORT );
			if	(!$scheme || !$host ) {
				return	null;
			}

			$robots_url		=	$scheme.'://'.$host.($port ? ':'.intval($port ) : '' ).'/robots.txt';
			$transient_key	=	'pz_lkc3_robots_'.md5($robots_url );
			$cached			=	get_transient($transient_key );
			if	(is_array($cached ) && array_key_exists('body', $cached ) ) {
				return	$cached['body'];
			}

			$response	=	wp_safe_remote_get($robots_url, array(
				'timeout'				=>	5,
				'redirection'			=>	3,
				'reject_unsafe_urls'	=>	true,
				'limit_response_size'	=>	1024 * 128,
				'user-agent'			=>	sanitize_text_field($user_agent ),
				'sslverify'				=>	$this->options['flg-sslverify'] ? true : false,
			) );
			if	(is_wp_error($response ) ) {
				set_transient($transient_key, array('body' => null ), HOUR_IN_SECONDS );
				return	null;
			}

			$code	=	wp_remote_retrieve_response_code($response );
			if	($code < 200 || $code >= 300 ) {
				set_transient($transient_key, array('body' => null ), HOUR_IN_SECONDS );
				return	null;
			}

			$body	=	wp_remote_retrieve_body($response );
			set_transient($transient_key, array('body' => $body ), DAY_IN_SECONDS );
			return	$body;
		}

		private	function	pz_IsRobotsAllowed($url, $user_agent ) {
			$robots	=	$this->pz_GetRobotsTxt($url, $user_agent );
			if	($robots === null || $robots === '' ) {
				return	true;
			}

			$ua			=	strtolower($user_agent );
			$path		=	wp_parse_url($url, PHP_URL_PATH );
			$query		=	wp_parse_url($url, PHP_URL_QUERY );
			$path		=	($path ? $path : '/' ).($query ? '?'.$query : '' );
			$groups		=	array();
			$agents		=	array();
			$rules		=	array();
			$has_rule	=	false;

			$flush_group = function() use (&$groups, &$agents, &$rules, &$has_rule) {
				if	($agents ) {
					$groups[]	=	array(
						'agents'	=>	$agents,
						'rules'		=>	$rules,
					);
				}
				$agents		=	array();
				$rules		=	array();
				$has_rule	=	false;
			};

			foreach	(preg_split('/\r\n|\r|\n/', $robots ) as $line ) {
				$line	=	preg_replace('/^\xEF\xBB\xBF/', '', $line );
				$line	=	trim(preg_replace('/#.*/', '', $line ) );
				if	($line === '' ) {
					$flush_group();
					continue;
				}
				if	(strpos($line, ':' ) === false ) {
					continue;
				}
				list($field, $value ) = array_map('trim', explode(':', $line, 2 ) );
				$field	=	strtolower($field );
				if	($field === 'user-agent' ) {
					if	($has_rule ) {
						$flush_group();
					}
					$agents[]	=	strtolower($value );
					continue;
				}
				if	(($field === 'allow' || $field === 'disallow' ) && $agents ) {
					$has_rule	=	true;
					$rules[]	=	array(
						'type'	=>	$field,
						'path'	=>	$value,
					);
				}
			}
			$flush_group();

			$best_agent_length	=	-1;
			$matched_rules		=	array();
			foreach	($groups as $group ) {
				$group_agent_length	=	-1;
				foreach	($group['agents'] as $agent ) {
					if	($agent === '*' || ($agent !== '' && strpos($ua, $agent ) !== false ) ) {
						$group_agent_length	=	max($group_agent_length, $agent === '*' ? 0 : strlen($agent ) );
					}
				}
				if	($group_agent_length < 0 ) {
					continue;
				}
				if	($group_agent_length > $best_agent_length ) {
					$best_agent_length	=	$group_agent_length;
					$matched_rules		=	$group['rules'];
				} elseif ($group_agent_length === $best_agent_length ) {
					$matched_rules		=	array_merge($matched_rules, $group['rules'] );
				}
			}

			$best_rule	=	null;
			foreach	($matched_rules as $rule ) {
				if	($rule['type'] === 'disallow' && $rule['path'] === '' ) {
					continue;
				}
				if	(!$this->pz_RobotsPathMatches($rule['path'], $path ) ) {
					continue;
				}
				$length	=	strlen($rule['path'] );
				if	(!$best_rule || $length > $best_rule['length'] || ($length === $best_rule['length'] && $rule['type'] === 'allow' ) ) {
					$best_rule	=	array(
						'type'		=>	$rule['type'],
						'length'	=>	$length,
					);
				}
			}

			return	!$best_rule || $best_rule['type'] !== 'disallow';
		}

		// ソーシャルカウント取得
		private	function	pz_RenewSNSCount($data ) {
			if	(!isset($data ) || !is_array($data ) ) {
				return	'';
			}

			$data	=	$this->pz_GetCache($data );
			if	(!isset($data ) || !is_array($data ) ) {
				return	'';
			}

			// ソーシャルカウント
			$sns_renew	= false;
			$update_cnt	= false;

			// タイムオーバー
			$rmtgt_opt			=	array(
				'timeout'				=>	5,
				'redirection'			=>	5,
				'reject_unsafe_urls'	=>	true,
				'user-agent'			=>	sanitize_text_field($this->options['user-agent'] ?? '' ),
			);

			// エンコードURL
			$url_raw	=	rawurlencode($data['url'] );

			// はてなブックマーク
			if	(isset($this->options['sns-hb'] ) && $this->options['sns-hb'] ) {
				$count_before	=	isset($data['sns_hatena'] ) ? $data['sns_hatena'] : '';
				$result			=	wp_safe_remote_get('https://api.b.st-hatena.com/entry.count?url=' .$url_raw, $rmtgt_opt );
				if	(isset($result ) && !is_wp_error($result ) && $result['response']['code'] == 200 ) {
					$count	=	intval($result['body'] );
					if	($count > $count_before ) {
						$data['sns_hatena']	=	$count;
						$update_cnt	=	true;
					}
				}
			}

			// 取得日
			$this->now				=	current_time('timestamp', false );							// 現在日時（ローカル時間）
			$data['sns_time']		=	$this->now;

			// 登録してから一週間までは1～2日、それ以降は4週～5週に1回更新（取得が固まらないようにランダム時間付与）
			if	($update_cnt || ($this->now - $data['regist_time'] < WEEK_IN_SECONDS ) ) {
				$data['sns_nexttime']	=	$this->now + DAY_IN_SECONDS	+ wp_rand(0, DAY_IN_SECONDS  );	// 1day   + 0-24hour
			} else {
				$data['sns_nexttime']	=	$this->now + 2419200		+ wp_rand(0, WEEK_IN_SECONDS );	// 28days + 0-7day
			}
			// MINUTE_IN_SECONDS	= 60
			// HOUR_IN_SECONDS		= 60	*	MINUTE_IN_SECONDS	= 3600
			// DAY_IN_SECONDS		= 24	*	HOUR_IN_SECONDS		= 86400
			// WEEK_IN_SECONDS		= 7		*	DAY_IN_SECONDS		= 604800
			// YEAR_IN_SECONDS		= 365	*	DAY_IN_SECONDS

			// DB更新
			global	$wpdb;
			if	(!empty($data['url'] ) ) {
				$data['url']		=	esc_url_raw($data['url'] );
				$data['url_key']	=	hash('sha256', $data['url'], false );
			}
			$result	=	$wpdb->update($this->db_card, $this->pz_NormalizeCardDateColumnsForStorage($data ), array('card_id' => $data['card_id'] ) );

			return	$data;
		}

		// キャッシュデータを取得
		private	function	pz_GetCache($data ) {
			return			require('lib/pz-lkc3-get-cache.php' );
		}

		// キャッシュデータを保存
		private	function	pz_SetCache($data ) {
			return			require('lib/pz-lkc3-set-cache.php' );
		}

		// // Twitter: Tweet参照
		// private function	pz_GetTwitter($data ) {
		// 	// X（旧Twitter）埋め込み処理（oEmbed）
		// 	if		($this->options['flg-oembed-x'] && preg_match('#https?://(x\.com|twitter\.com)/#', $url ) ) {	// X（旧Twitter）リンクの場合、投稿IDをセット
		// 		$flg_update		=	false;		// 更新フラグをセット
		// 		$tw_text		=	'';
		// 		$tw_user		=	'';
		// 		$tw_date		=	0;
		// 		$endpoint		=	'https://publish.twitter.com/oembed?url='.urlencode($url );
		// 		$rmtgt_opt			=	array(
		// 			'timeout'				=>	5,
		// 			'redirection'			=>	5,
		// 			'reject_unsafe_urls'	=>	true,
		// 			'user-agent'			=>	sanitize_text_field($this->options['user-agent'] ?? '' ),
		// 		);
		// 		$response		=	wp_safe_remote_get($endpoint, $rmtgt_opt );
		// 		if	($response !== false ) {
		// 			$oembed		=	json_decode(wp_remote_retrieve_body($response ), true );
		// 			$oe_html	=	$oembed['html'] ?? '';
		// 			if (preg_match('#<p[^>]*>(.*?)</p>#si', $oe_html, $m ) ) {
		// 				$p_content		=	$m[1];
		// 				$tw_text		=	trim(preg_replace('#<a\b[^>]*>.*?</a>#si', '', $p_content ) );	// ツイート
		// 				$tw_user		=	$oembed['author_name'];
		// 				if (preg_match('#<a[^>]*>([^<]*?)</a></blockquote>#si', $oe_html, $m ) ) {
		// 					$tw_date = strtotime($m[1] );		// strtotime が自動でパース
		// 				}
		// 			}
		// 			$tw_result				=	200;
		// 		} else {
		// 			$tw_user				=	__('Failed to retrieve X (formerly Twitter) link.', 'pz-linkcard3' );
		// 			$tw_text				=	'';
		// 			$tw_result				=	404;
		// 			$tw_date				=	0;
		// 		}
		// 		// タイトル・更新日を更新
		// 		if	(empty($data['title'] ) ) {
		// 			$data['title']			=	__('Posts from X (formerly Twitter) are not cached.', 'pz-linkcard3' );
		// 			$data['regist_title']	=	$data['title'];		// 登録時のタイトル
		// 		}
		// 		if	($data['update_result'] <> $tw_result ) {
		// 			$data['update_result']	=	$tw_result;
		// 		}
		// 		// 投稿内容をセット
		// 		$data['title']			=	$tw_user;		// タイトルにユーザー名をセット
		// 		$data['excerpt']		=	$tw_text;		// 抜粋文にXの投稿内容をセット
		// 		$data['post_date']		=	$tw_date;		// 投稿日時をセット
		// 		$data['update_result']	=	$tw_result;		// HTTP結果コードをセット
		// 		$data['regist_result']	=	$tw_result;		// 登録時のHTTP結果コードをセット
		// 		$data['regist_time']	=	$this->now;		// 登録日をセットして記事情報を再取得させなくする
		// 	}			
		// 	return	$data;
		// }

		// ブロックエディタのプレビュー
		function pz_linkcard_render_callback($attributes) {
			$url = esc_url_raw($attributes['url'] ?? '');
			if (!$url) {
				return '<div class="linkcard3"><div class="lkc3-wrap lkc3-external-wrap lkc3-unlink"><div class="lkc3-card"><div class="lkc3-info">' . $this->plugin_name . '</div><div class="lkc3-contents"><div class="lkc3-excerpt">' . __('No URL was specified.', 'pz-linkcard3') . '</div></div></div></div></div>';
			}
			$shortcode = preg_replace("/[^a-zA-Z0-9]/", "", $attributes['shortcode'] ?? '' );
			$available_shortcodes = array();
			foreach (array('code1', 'code2', 'code3', 'code4' ) as $key ) {
				$code = preg_replace("/[^a-zA-Z0-9]/", "", $this->options[$key] ?? '' );
				if ($code ) {
					$available_shortcodes[] = $code;
				}
			}
			if (!$available_shortcodes ) {
				$available_shortcodes[] = self::DEFAULTS['code1']['value'];
			}
			if (!$shortcode || !in_array($shortcode, $available_shortcodes, true ) ) {
				$shortcode = $available_shortcodes[0];
			}
			ob_start();
			try {
				$result = do_shortcode('['.$shortcode.' url="' . esc_attr($url) . '"]');
				$unexpected_output = ob_get_clean();
			} catch (\Throwable $e) {
				$unexpected_output = ob_get_clean();
				$result = '<div class="linkcard3"><div class="lkc3-wrap lkc3-external-wrap lkc3-unlink"><div class="lkc3-card"><div class="lkc3-info">' . esc_html($this->plugin_name) . '</div><div class="lkc3-contents"><div class="lkc3-excerpt">' . esc_html(__('Preview failed.', 'pz-linkcard3' ) ) . '<br>' . esc_html($e->getMessage()) . '</div></div></div></div></div>';
			}
			return $result;
		}

		// キャッシュデータを削除
		private	function	pz_DelCache($data ) {
			global	$wpdb;
			if	(!isset($data ) || !is_array($data ) ) {
				return	null;
			}
			if	(isset($data['card_id'] ) ) {
				$result		=	$wpdb->delete($this->db_card, array('card_id' => $data['card_id'] ), array('%d' ) );
				if	($result ) {
					return	true;
				}
			}
			if	(isset($data['url'] ) ) {
				$result		=	$wpdb->delete($this->db_card, array('url' => $data['url'] ), array('%s' ) );
				if	($result ) {
					return	true;
				}
			}
			return	null;
		}

		// 内部リンク・記事情報取得
		private	function	pz_GetPost($data ) {
			return			require('lib/pz-lkc3-get-post.php' );
		}

		// リダイレクト先URL取得
		private	function	pz_GetRedirURL($data ) {
			$url		=	esc_url_raw($data['url'] ?? '' );
			if	(!$url || !wp_http_validate_url($url ) ) {
				return	'';
			}
			if	(!empty($this->options['flg-local-check'] ) && !$this->pz_IsSiteURL($url ) && $this->pz_IsLocalAddress($url ) ) {
				return	'';
			}

			$rmtgt_opt			=	array(
				'timeout'				=>	30,
				'redirection'			=>	5,
				'reject_unsafe_urls'	=>	true,
				'user-agent'			=>	sanitize_text_field($this->options['user-agent'] ?? '' ),
			);
			$response	=	wp_safe_remote_head($url, $rmtgt_opt );
			$code		=	is_wp_error($response ) ? 0 : wp_remote_retrieve_response_code($response );

			if	(is_wp_error($response ) || in_array($code, array(405, 501 ), true ) ) {
				$rmtgt_opt['limit_response_size']	=	1024;
				$response				=	wp_safe_remote_get($url, $rmtgt_opt );
			}
			if	(is_wp_error($response ) ) {
				return	$url;
			}

			$http_response	=	$response['http_response'] ?? null;
			$response_obj	=	is_object($http_response ) && method_exists($http_response, 'get_response_object' ) ? $http_response->get_response_object() : null;
			$redir_url		=	is_object($response_obj ) && !empty($response_obj->url ) ? esc_url_raw($response_obj->url ) : '';

			return	$redir_url ?: $url;
		}

		// 外部リンク・記事情報取得
		private	function	pz_GetCURL($data, $timeout = 5 ) {
			return			require('lib/pz-lkc3-get-curl.php' );
		}
		
		// ローカルアドレスかどうかの判定
		private	function	pz_IsLocalAddress($url ) {
			$parts	=	wp_parse_url($url );
			if	(!is_array($parts ) || empty($parts['host'] ) ) {
				return	true;
			}

			$scheme	=	isset($parts['scheme'] )	?	strtolower($parts['scheme'] )	:	'';
			if	(!in_array($scheme, array('http', 'https' ), true ) ) {
				return	true;
			}

			if	(!empty($parts['user'] ) || !empty($parts['pass'] ) ) {
				return	true;
			}

			$port	=	isset($parts['port'] ) ? intval($parts['port'] ) : null;
			if	(isset($port ) && !in_array($port, array(80, 443 ), true ) ) {
				return	true;
			}

			$host	=	strtolower(trim($parts['host'], "[] \t\n\r\0\x0B." ) );
			if	(!$host ) {
				return	true;
			}

			if	(in_array($host, array('localhost', 'localhost.localdomain' ), true ) || preg_match('/\.(local|localhost)$/i', $host ) ) {
				return	true;
			}

			$is_public_ip = function($ip ) {
				return	filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) !== false;
			};

			if	(filter_var($host, FILTER_VALIDATE_IP ) !== false ) {
				return	!$is_public_ip($host );
			}

			$ips	=	array();
			if	(function_exists('dns_get_record' ) ) {
				$records	=	@dns_get_record($host, DNS_A + DNS_AAAA );
				if	(is_array($records ) ) {
					foreach	($records as $record ) {
						if	(!empty($record['ip'] ) ) {
							$ips[]	=	$record['ip'];
						}
						if	(!empty($record['ipv6'] ) ) {
							$ips[]	=	$record['ipv6'];
						}
					}
				}
			}
			if	(function_exists('gethostbynamel' ) ) {
				$ipv4s	=	@gethostbynamel($host );
				if	(is_array($ipv4s ) ) {
					$ips	=	array_merge($ips, $ipv4s );
				}
			}

			$ips	=	array_values(array_unique(array_filter($ips ) ) );
			if	(empty($ips ) ) {
				return	true;
			}

			foreach	($ips as $ip ) {
				if	(!$is_public_ip($ip ) ) {
					return	true;
				}
			}

			return false;
		}

		private	function	pz_IsLocalIPLink($url ) {
			$parts	=	wp_parse_url($url );
			if	(!is_array($parts ) || empty($parts['host'] ) ) {
				return	false;
			}

			if	($this->pz_IsSiteURL($url ) ) {
				return	false;
			}

			$scheme	=	isset($parts['scheme'] )	?	strtolower($parts['scheme'] )	:	'';
			if	(!in_array($scheme, array('http', 'https' ), true ) ) {
				return	false;
			}

			if	(!empty($parts['user'] ) || !empty($parts['pass'] ) ) {
				return	false;
			}

			$host	=	strtolower(trim($parts['host'], "[] \t\n\r\0\x0B." ) );
			if	(!$host ) {
				return	false;
			}

			if	(in_array($host, array('localhost', 'localhost.localdomain' ), true ) || preg_match('/\.(local|localhost)$/i', $host ) ) {
				return	true;
			}

			$is_public_ip = function($ip ) {
				return	filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) !== false;
			};

			if	(filter_var($host, FILTER_VALIDATE_IP ) !== false ) {
				return	!$is_public_ip($host );
			}

			$ips	=	array();
			if	(function_exists('dns_get_record' ) ) {
				$records	=	@dns_get_record($host, DNS_A + DNS_AAAA );
				if	(is_array($records ) ) {
					foreach	($records as $record ) {
						if	(!empty($record['ip'] ) ) {
							$ips[]	=	$record['ip'];
						}
						if	(!empty($record['ipv6'] ) ) {
							$ips[]	=	$record['ipv6'];
						}
					}
				}
			}
			if	(function_exists('gethostbynamel' ) ) {
				$ipv4s	=	@gethostbynamel($host );
				if	(is_array($ipv4s ) ) {
					$ips	=	array_merge($ips, $ipv4s );
				}
			}

			$ips	=	array_values(array_unique(array_filter($ips ) ) );
			foreach	($ips as $ip ) {
				if	(!$is_public_ip($ip ) ) {
					return	true;
				}
			}

			return	false;
		}

		// ローカルアドレス確認メッセージ
		private	function	pz_CheckLocalAddress($url ) {
			if	(empty($this->options['flg-local-check'] ) || $this->pz_IsSiteURL($url ) ) {
				return	false;
			}

			if	(!$this->pz_IsLocalAddress($url ) ) {
				return	false;
			}

			$parts	=	wp_parse_url($url );
			if	(!is_array($parts ) || empty($parts['host'] ) ) {
				return	__('Error: Unknown host.', 'pz-linkcard3' );
			}

			$scheme	=	isset($parts['scheme'] )	?	strtolower($parts['scheme'] )	:	'';
			if	(!in_array($scheme, array('http', 'https' ), true ) || !empty($parts['user'] ) || !empty($parts['pass'] ) ) {
				return	__('Error: Unknown host.', 'pz-linkcard3' );
			}

			$port	=	isset($parts['port'] ) ? intval($parts['port'] ) : null;
			if	(isset($port ) && !in_array($port, array(80, 443 ), true ) ) {
				return	__('Error: Private IP address range.', 'pz-linkcard3' );
			}

			$host	=	strtolower(trim($parts['host'], "[] \t\n\r\0\x0B." ) );
			if	(!$host ) {
				return	__('Error: Unknown host.', 'pz-linkcard3' );
			}
			$my_host	=	strtolower(trim($this->my_domain ?? '', "[] \t\n\r\0\x0B." ) );
			if	($my_host && $host === $my_host ) {
				return	false;
			}

			if	(in_array($host, array('localhost', 'localhost.localdomain' ), true ) || preg_match('/\.(local|localhost)$/i', $host ) ) {
				return	__('Error: Local domain.', 'pz-linkcard3' );
			}

			if	(filter_var($host, FILTER_VALIDATE_IP ) !== false ) {
				if	(in_array($host, array('127.0.0.1', '::1' ), true ) ) {
					return	__('Error: Loopback address.', 'pz-linkcard3' );
				}
				return	__('Error: Private IP address range.', 'pz-linkcard3' );
			}

			$ips	=	array();
			if	(function_exists('dns_get_record' ) ) {
				$records	=	@dns_get_record($host, DNS_A + DNS_AAAA );
				if	(is_array($records ) ) {
					foreach	($records as $record ) {
						if	(!empty($record['ip'] ) ) {
							$ips[]	=	$record['ip'];
						}
						if	(!empty($record['ipv6'] ) ) {
							$ips[]	=	$record['ipv6'];
						}
					}
				}
			}
			if	(function_exists('gethostbynamel' ) ) {
				$ipv4s	=	@gethostbynamel($host );
				if	(is_array($ipv4s ) ) {
					$ips	=	array_merge($ips, $ipv4s );
				}
			}
			$ips	=	array_values(array_unique(array_filter($ips ) ) );
			foreach	($ips as $ip ) {
				if	(in_array($ip, array('127.0.0.1', '::1' ), true ) ) {
					return	__('Error: Loopback address.', 'pz-linkcard3' );
				}
				if	(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) === false ) {
					return	__('Error: Private IP address range.', 'pz-linkcard3' );
				}
			}

			return	__('Error: Failed to resolve IP address.', 'pz-linkcard3' );
		}

		// TITLEとMETAタグを分解
		private	function	pz_GetMeta($html, $tags	=	null, $clear	=	false ) {
			if	($clear == true || !isset($tags ) ) {
				$tags	=	null;
				$tags	=	array('none' => 'none' );
			}

			// TITLEタグ
			if	(preg_match('/<\s*title\s*[^>]*>\s*([^<]*)\s*<\s*\/title\s*[^>]*>/si', $html, $m ) ) {
				$tags['title']	=	esc_html($m[1]);
			}

			// metaタグ パース
			$match	=	null;
			preg_match_all('/<\s*meta\s(?=[^>]*?\b(?:name|property)\s*=\s*(?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|([^"\'>]*?)(?=\s*\/?\s*>|\s\w+\s*=) ))[^>]*?\bcontent\s*=\s*(?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|([^"\'>]*?)(?=\s*\/?\s*>|\s\w+\s*=) )[^>]*>/is', $html, $match );
			if	(isset($match ) && is_array($match ) && count($match ) == 3 && count($match[1] ) > 0 ) {
				foreach($match[1] as &$m ) {
					$m	=	strtolower($m );
				}
				unset($m );
				foreach	(array_combine($match[1], $match[2] ) as $tag_key => $tag_value ) {
					if	(!isset($tags[$tag_key] ) || $tags[$tag_key] === '' ) {
						$tags[$tag_key]	=	$tag_value;
					}
				}
			}
			preg_match_all('/<\s*meta\b[^>]*>/is', $html, $meta_tags );
			if	(isset($meta_tags[0] ) && is_array($meta_tags[0] ) ) {
				foreach	($meta_tags[0] as $meta_tag ) {
					$attr	=	[];
					preg_match_all('/([a-zA-Z_:][-a-zA-Z0-9_:.]*)\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s"\'>\/=]+))/s', $meta_tag, $attr_matches, PREG_SET_ORDER );
					foreach	($attr_matches as $attr_match ) {
						$attr_value	=	($attr_match[2] ?? '' ) ?: (($attr_match[3] ?? '' ) ?: ($attr_match[4] ?? '' ) );
						$attr[strtolower($attr_match[1] )]	=	html_entity_decode($attr_value, ENT_QUOTES, get_bloginfo('charset' ) );
					}
					$meta_key	=	$attr['name'] ?? $attr['property'] ?? '';
					if	($meta_key && isset($attr['content'] ) ) {
						$meta_key	=	strtolower($meta_key );
						if	(!isset($tags[$meta_key] ) || $tags[$meta_key] === '' ) {
							$tags[$meta_key]	=	$attr['content'];
						}
					}
				}
			}

			// linkタグ パース
			$match	=	null;
			preg_match_all('/<\s*link\s(?=[^>]*?\brel\s*=\s*(?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|([^"\'>]*?)(?=\s*\/?\s*>|\s\w+\s*=) ))[^>]*?\bhref\s*=\s*(?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|([^"\'>]*?)(?=\s*\/?\s*>|\s\w+\s*=) )[^>]*>/is', $html, $match );
			if	(isset($match ) && is_array($match ) && count($match ) == 3 && count($match[1] ) > 0 ) {
				foreach($match[1] as &$m ) {
					$m	=	strtolower($m );
				}
				unset($m );
				$tags	+=	array_combine($match[1], $match[2] );
			}

			return	$tags;
		}

		// サムネイル取得（外部リンクOGP画像取得）
		private	function	pz_GetImage($image_url, $force = false, $stamp = false, $readonly = false ) {
			return			require('lib/pz-lkc3-get-image.php' );
		}

		// 設定を取得する
		private	function	pz_LoadOptions() {
			// パラメーターを取得
			$this->options			=	get_option($this->option_name, [] );	// オプション値を取得
			if	(!is_array($this->options ) ) {
				$this->options		=	[];
			}

			// パラメーターが無い
			if ($this->options === [] ) {
				foreach (self::DEFAULTS as $key => $value) {
					$this->options[$key] = $value['value'];
				}
				$this->options['saved-date']	=	$this->now;		// 保存日時をセット
				$result		=	update_option($this->option_name, $this->options );
			}
			foreach (self::DEFAULTS as $key => $value) {
				if (!array_key_exists($key, $this->options ) ) {
					$this->options[$key] = $value['value'];
				}
			}

			// 管理者モードの解除
			if	( ! $this->options['debug-mode'] ) {						// デバグモードがOFFの場合
				$this->options['initialize-exception']		=	0;			// 　初期化例外
				$this->options['log-mode']					=	0;			// 　ログモード
				$this->options['admin-mode']				=	0;			// 　管理者モード
			}
			if	( ! $this->options['admin-mode'] ) {						// 管理者モードがOFFの場合
				$this->options['multi-mode']				=	0;			// 　マルチサイトタブの表示
			}

			return	true;
		}

		// 設定を更新する
		private	function	pz_SaveOptions() {
			// 現在日時を更新
			$this->now		=	current_time('timestamp', false );		// 現在日時（ローカル時間）

			// 変更前
			$flg_change		=	false;
			$before			=	get_option($this->option_name, [] );
			if	(!is_array($before ) ) {
				$before		=	[];
			}

			// パラメーターに変更があるかどうか
			foreach	($this->options	as	$key => $value ) {
				if	(array_key_exists($key,		$before ) ) {
					if	($value		!=	$before[$key] ) {
						$flg_change	=	true;
					}
				} else {
					$flg_change		=	true;
				}
			}

			// 自動的に更新するパラメーターのセット
			$this->options['saved-date']	=	$this->now;		// 保存日時をセット

			// 設定の更新
			$result				=	update_option($this->option_name, $this->options );

			// 返却
			return	$flg_change;
		}

		// 設定を初期化する
		private	function	pz_InitializeOptions() {
			// 初期化
			$before				=	$this->options;
			foreach (self::DEFAULTS as $key => $value) {
				$this->options[$key] = $value['value'];
			}

			// 引き継ぐ設定値
			$takeover			=	array('saved-date', 'db-ver-card', 'db-ver-click', );
			if	(isset($before['initialize-exception'] ) && $before['initialize-exception'] ) {
				// 初期化例外が有効の時に引き継ぐ設定値
				array_push($takeover, 'initialize-exception', 'admin-mode', 'debug-mode', 'additional-mode', 'log-mode' );
			}

			// 設定を引き継ぐ
			foreach($takeover as $key ) {
				if	(array_key_exists($key, $before ) ) {
					$this->options[$key]		=	$before[$key];
				}
			}
			
			// ブログID
			$this->options['multi-myid']		=	get_current_blog_id();
			
			// プラグインのバージョン
			$this->options['plugin-version']	=	$this->plugin_version;
			
			// 設定を更新する
			$this->options['saved-date']	=	$this->now;		// 保存日時をセット
			update_option($this->option_name, $this->options );

			// 初期化処理
			$this->hook_activate();				// プラグインの再起動
			
			return	true;
		}

		// スタイルシート生成
		private	function	pz_SetStyle($filename = 'style' ) {
			return			require('lib/pz-lkc3-style-file.php' );
		}

		// スタイルシート（CSS）のテキストを生成する
		private function	pz_MakeCSSText($prop = null ) {
			if	(empty($prop ) ) {
				$prop	=	$this->options();
			}
			return			require('lib/pz-lkc3-style.php' );
		}

		// スタイルシート圧縮
		private	function	pz_CompressCSS($style ) {
			// 引用ここから
			$replaces	=	[];
			// $replaces['/@charset [^;]+;/' ] = '';
			// $replaces['/([\s:]url\()[\"\']([^\"\']+)[\"\'](\)[\s;}])/'] = '${1}${2}${3}';
			// $replaces['/(\/\*(?=[!]).*?\*\/|\"(?:(?!(?<!\\\)\").)*\"|\'(?:(?!(?<!\\\)\').)*\')|\s+/'] = '${1} ';
			// $replaces['/(\/\*(?=[!]).*?\*\/|\"(?:(?!(?<!\\\)\").)*\"|\'(?:(?!(?<!\\\)\').)*\')|\/\*.*?\*\/|\s+([:])\s+|\s+([)])|([(:])\s+/s'] = '${1}${2}${3}${4}';
			$replaces['/(\/\*!.*?\*\/|\"(?:(?!(?<!\\\)\").)*\"|\'(?:(?!(?<!\\\)\').)*\')|\/\*.*?\*\//s'] = '${1}'; // (2) コメントの除去
			$replaces['/(\/\*!.*?\*\/|\"(?:(?!(?<!\\\)\").)*\"|\'(?:(?!(?<!\\\)\').)*\')\s*|\s+/s'] = '${1} '; // (3) 1つ以上連続する空白文字列の置換
			$replaces['/(\/\*!.*?\*\/|\"(?:(?!(?<!\\\)\").)*\"|\'(?:(?!(?<!\\\)\').)*\')| ([!#$%&,.:;<=>?@^{|}~]) |([!#$&(,.:;<=>?@\[^{|}~]|\A) | ([$%&),;<=>?@\]^{|}~]|\z)/s' ] = '${1}${2}${3}${4}'; // (4) 一部の演算記号を除く記号前後の半角スペースの除去
			$replaces['/(\/\*!.*?\*\/|\"(?:(?!(?<!\\\)\").)*\"|\'(?:(?!(?<!\\\)\').)*\'|\([^;{}]+\))| ([+\-\/]) |([+\-\/]) | ([+\/])/s' ] = '${1}${2}${3}${4}'; // (5) 演算記号前後の半角スペースの除去
			$replaces['/\s*(\/\*(?=[!]).*?\*\/|\"(?:(?!(?<!\\\)\").)*\"|\'(?:(?!(?<!\\\)\').)*\'|[ :]calc\([^;}]+\)[ ;}]|[!$&+,\/;<=>?@^_{|}~]|\A|\z)\s*/s'] = '${1}'; // (2)～(5)で消えていなかった記号前後の半角スペース
			// $replaces['/#([0-9a-fA-F])\1([0-9a-fA-F])\2([0-9a-fA-F])\3/'] = '#${1}${2}${3}'; // カラーコード6桁→3桁
			$style		=	preg_replace(array_keys($replaces ), array_values($replaces ), $style );
			// 引用ここまで：https://shimotsuki.wwwxyz.jp/20200930-650
			do {
				$style	=	preg_replace('/(})[^{]*{}/', '$1', $style );		// 空の要素除去
			} while (preg_match('/;[^{]*{}/', $style ) );
			$style		=	trim($style );
			return		$style;
		}

		// スタイルシート圧縮
		private	function	pz_CompressJS($js ) {
			// 引用ここから
			$replaces = [];	// 置換用の配列を生成
			$replaces[ '/([(+=])\s*(\/(?:(?!(?<!\\\)\/).)+\/[dgimsuy]*)\s*([)+,.;])/s' ] = '${1}${2}${3}';		// (1) JSの正規表現前後の空白文字列の除去
			$replaces[ '/(\/\*[!@].*?\*\/|[(+=]\/(?:(?!(?<!\\\)\/).)+\/[dgimsuy]*[)+,.;]|\"(?:(?!(?<!\\\)\").)*\"|\'(?:(?!(?<!\\\)\').)*\'|\`(?:(?!(?<!\\\)\`).)*\`)|\/\*.*?\*\/|\/\/[^\r\n]+[\r\n]/s' ] = '${1}';		// (2) コメントの除去
			$replaces[ '/(\/\*[!@].*?\*\/|[(+=]\/(?:(?!(?<!\\\)\/).)+\/[dgimsuy]*[)+,.;]|\"(?:(?!(?<!\\\)\").)*\"|\'(?:(?!(?<!\\\)\').)*\'|\`(?:(?!(?<!\\\)\`).)*\`)\s*|\s+/s' ] = '${1} ';	// (3) 1つ以上連続する空白文字列の置換
			$replaces[ '/(\/\*[!@].*?\*\/|[(+=]\/(?:(?!(?<!\\\)\/).)+\/[dgimsuy]*[)+,.;]|\"(?:(?!(?<!\\\)\").)*\"|\'(?:(?!(?<!\\\)\').)*\'|\`(?:(?!(?<!\\\)\`).)*\`) | ([!#$%&)*+,\-.\/:;<=>?@\]^_|}~]) | ([!#$%&)*,.\/:;<=>?@\]^|}~]|\+(?!\+)|-(?!-)|\z)|([!#$%&()*+,\-.\/:;<=>?@\[\]^_{|}~]|\A) /s' ] = '${1}${2}${3}${4}';	// (4) 一部の演算記号を除く記号前後の半角スペースの除去
			$js = preg_replace( array_keys( $replaces ), array_values( $replaces ), $js );	// 一括置換
			// 引用ここまで：https://shimotsuki.wwwxyz.jp/20200930-650
		
			return		$js;
		}

		// デバグ用の文字列表示
		private	function	pz_HTTPMessage($result ) {
			$message			=	'';
			$http_message		=	require('lib/pz-lkc3-error-code.php' );

			if	($result ) {
				if	(array_key_exists($result, $http_message ) ) {
					$message		=	$http_message[$result];		// HTTPステータスコードに対応するメッセージを取得
				}
			} else {
				$message			=	'-';
			}

			return		$message;
		}

		// 日付・時刻の書式変換
		private	function	pz_Date($format, $value ) {
			if	(!$value ) {
				return	null;
			}
			$value	=	$this->pz_CardDatetimeToTimestamp($value );
			if	(!$value ) {
				return	null;
			}
			$temp	=	gmdate($format, $value );
			$format	=	preg_replace('/<br\s*\/?>/', '\<\b\r\>', $format );
			$temp	=	preg_replace('/<br\s*\/?>/', PHP_EOL, $temp );
			$temp	=	esc_html($temp );
			$temp	=	str_replace(PHP_EOL, '<br>', $temp );
// [DEBUG]

			return		$temp;
		}

		private	function	pz_GetCardDateColumns() {
			return	array('post_date', 'post_modified', 'alive_time', 'alive_nexttime', 'sns_time', 'sns_nexttime', 'regist_time', 'update_time');
		}

		private	function	pz_NormalizeCharsetName($charset ) {
			$charset	=	trim((string) $charset );
			if	($charset === '' ) {
				return	'';
			}
			$key	=	strtolower($charset );
			$key	=	preg_replace('/[^a-z0-9]+/', '', $key );
			$aliases	=	array(
				'utf8'			=>	'UTF-8',
				'utf'			=>	'UTF-8',
				'csutf8'		=>	'UTF-8',
				'usascii'		=>	'ASCII',
				'ascii'			=>	'ASCII',
				'ansi_x3.4-1968'	=>	'ASCII',
				'shiftjis'		=>	'SJIS-win',
				'shiftjisx0213'	=>	'SJIS-win',
				'sjis'			=>	'SJIS-win',
				'sjiswin'		=>	'SJIS-win',
				'windows31j'	=>	'SJIS-win',
				'windows932'	=>	'SJIS-win',
				'cp932'			=>	'SJIS-win',
				'mskanji'		=>	'SJIS-win',
				'csshiftjis'	=>	'SJIS-win',
				'eucjp'			=>	'eucJP-win',
				'eucjpwin'		=>	'eucJP-win',
				'eucjpms'		=>	'eucJP-win',
				'ujis'			=>	'eucJP-win',
				'xeucjp'		=>	'eucJP-win',
				'cseucpkdfmtjapanese'	=>	'eucJP-win',
				'jis'			=>	'JIS',
				'iso2022jp'		=>	'JIS',
				'csiso2022jp'	=>	'JIS',
			);
			if	(isset($aliases[$key] ) ) {
				return	$aliases[$key];
			}
			if	(preg_match('/utf.*8/', $key ) ) {
				return	'UTF-8';
			}
			if	(preg_match('/(?:shift|sjis|932|mskanji)/', $key ) ) {
				return	'SJIS-win';
			}
			if	(preg_match('/euc.*jp|ujis/', $key ) ) {
				return	'eucJP-win';
			}
			if	(preg_match('/iso2022.*jp|jis/', $key ) ) {
				return	'JIS';
			}
			return	$charset;
		}

		private	function	pz_CardTimestampToDatetime($value ) {
			if	($value === null || $value === '' || $value === 0 || $value === '0' ) {
				return	null;
			}
			if	(is_string($value ) ) {
				$value	=	trim($value );
				if	(preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $value ) ) {
					return	$value;
				}
				if	(preg_match('/^\d{4}-\d{2}-\d{2}$/', $value ) ) {
					return	$value.' 00:00:00';
				}
				if	(preg_match('/^\d{4}\/\d{1,2}\/\d{1,2}(?:\s+\d{1,2}:\d{1,2}(?::\d{1,2})?)?$/', $value ) ) {
					$timestamp	=	strtotime($value );
					return	$timestamp ? gmdate('Y-m-d H:i:s', $timestamp ) : null;
				}
			}
			if	(!is_numeric($value ) ) {
				$timestamp	=	strtotime((string) $value );
				return	$timestamp ? gmdate('Y-m-d H:i:s', $timestamp ) : null;
			}
			$value	=	(string) $value;
			$value	=	preg_replace('/[,.]\d+$/', '', $value );
			if	(preg_match('/^(\d{4})(\d{2})(\d{2})$/', $value, $matches ) && checkdate(intval($matches[2] ), intval($matches[3] ), intval($matches[1] ) ) ) {
				return	sprintf('%04d-%02d-%02d 00:00:00', intval($matches[1] ), intval($matches[2] ), intval($matches[3] ) );
			}
			if	(preg_match('/^(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})$/', $value, $matches ) && checkdate(intval($matches[2] ), intval($matches[3] ), intval($matches[1] ) ) && intval($matches[4] ) < 24 && intval($matches[5] ) < 60 && intval($matches[6] ) < 60 ) {
				return	sprintf('%04d-%02d-%02d %02d:%02d:%02d', intval($matches[1] ), intval($matches[2] ), intval($matches[3] ), intval($matches[4] ), intval($matches[5] ), intval($matches[6] ) );
			}
			$is_milliseconds	=	preg_match('/^\d{13,}$/', $value );
			$value	=	intval($value );
			if	($is_milliseconds ) {
				$value	=	intdiv($value, 1000 );
			}
			if	($value <= 0 ) {
				return	null;
			}
			if	($value >= 253402300799 || $value >= 99999999999 ) {
				return	'9999-12-31 23:59:59';
			}
			return	gmdate('Y-m-d H:i:s', $value );
		}

		private	function	pz_CardDatetimeToTimestamp($value ) {
			if	($value === null || $value === '' || $value === '0000-00-00 00:00:00' ) {
				return	0;
			}
			if	(is_numeric($value ) ) {
				$value	=	(string) $value;
				$value	=	preg_replace('/[,.]\d+$/', '', $value );
				$is_milliseconds	=	preg_match('/^\d{13,}$/', $value );
				$value	=	intval($value );
				return	$is_milliseconds ? intdiv($value, 1000 ) : $value;
			}
			try {
				$datetime	=	new DateTimeImmutable((string) $value, new DateTimeZone('UTC') );
				return	$datetime->getTimestamp();
			} catch (Exception $e ) {
				return	0;
			}
		}

		private	function	pz_NormalizeCardDateColumnsForStorage($data ) {
			if	(!is_array($data ) ) {
				return	$data;
			}
			foreach	($this->pz_GetCardDateColumns() as $column ) {
				if	(array_key_exists($column, $data ) ) {
					$data[$column]	=	$this->pz_CardTimestampToDatetime($data[$column] );
				}
			}
			return	$data;
		}

		private	function	pz_NormalizeCardDateColumnsForRuntime($data ) {
			if	(!is_array($data ) ) {
				return	$data;
			}
			foreach	($this->pz_GetCardDateColumns() as $column ) {
				if	(array_key_exists($column, $data ) ) {
					$data[$column]	=	$this->pz_CardDatetimeToTimestamp($data[$column] );
				}
			}
			return	$data;
		}

		// プラグインを有効化
		public	function	hook_activate() {
			return			require('lib/pz-lkc3-activate.php' );
		}

		// プラグインを無効化
		public	function	hook_deactivate() {
			wp_clear_scheduled_hook($this->cron_regist );			// WP-CRONスケジュール停止（記事内容取得）
			wp_clear_scheduled_hook($this->cron_alive );			// WP-CRONスケジュール停止（リンク先存在チェック）
			wp_clear_scheduled_hook($this->cron_sns );				// WP-CRONスケジュール停止（SNSカウント取得）
		}

		// 全てのプラグインロード後（プラガブル関数用）
		public	function	action_plugins_loaded() {
		}

		// プラグインの初期化
		public	function	action_init() {
			$code	=	preg_replace("/[^a-zA-Z0-9]/", "", $this->options['code1'] );								// ショートコード1
			if	($code ) {						
				add_shortcode($code, array($this, 'shortcode' ), 10 );
			}
			$code	=	preg_replace("/[^a-zA-Z0-9]/", "", $this->options['code2'] );								// ショートコード2
			if	($code ) {
				add_shortcode($code, array($this, 'shortcode' ), 10 );
			}
			$code	=	preg_replace("/[^a-zA-Z0-9]/", "", $this->options['code3'] );								// ショートコード3
			if	($code ) {
				add_shortcode($code, array($this, 'shortcode' ), 10 );
			}
			$code	=	preg_replace("/[^a-zA-Z0-9]/", "", $this->options['code4'] );								// ショートコード4
			if	($code ) {
				add_shortcode($code, array($this, 'shortcode' ), 10 );
			}
			//add_shortcode('pz-linkcard-block',							[$this, 'shortcode' ], 10 );				// ブロックエディターで設定したブロック

			// ブロックエディタの登録
			register_block_type('pz-series/pz-linkcard-block', [
				'editor_script' => 'pz-linkcard-block', // JS の登録ハンドル名
				'render_callback' => [$this, 'pz_linkcard_render_callback'],
				'attributes' => [
					'url' => [
						'type' => 'string',
						'default' => '',
					],
					'shortcode' => [
						'type' => 'string',
						'default' => '',
					],
				],
			]);
		}

		// WordPressの読み込み終了
		public	function	action_wp_loaded() {
		}

		// 管理画面の初期化 
		public	function	action_admin_init() {
			if ( user_can_richedit() ) {
				$this->is_richedit	=	true;
			} else {
				$this->is_richedit	=	false;
			}
		}

		// 管理画面のサブメニュー追加
		public	function	action_admin_menu() {
			$flg_count			=	false;
			$posted_properties	=	array();
			if	(
				isset($_POST['_wpnonce'], $_POST['properties'] )
				&& is_array($_POST['properties'] )
				&& wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'] ) ), 'pz-settings' )
			) {
				$posted_properties	=	map_deep(wp_unslash($_POST['properties'] ), 'sanitize_text_field' );
			}
			if		(isset($posted_properties['flg-alive-count'] ) ) {
				if	($posted_properties['flg-alive-count'] ) {
					$flg_count	=	true;
				}
			} else {
				if	($this->options['flg-alive-count'] ) {
					$flg_count	=	true;
				}
			}

			if	((function_exists('is_plugin_active' ) && is_plugin_active('pz-linkcard/pz-linkcard.php' ) ) || (function_exists('is_plugin_active_for_network' ) && is_plugin_active_for_network('pz-linkcard/pz-linkcard.php' ) ) ) {
				$menu_manager	=	__('[Pz] LinkCard3 Manager',	'pz-linkcard3' );	// LinkCard(2)と区別するために3を付与
				$menu_settings	=	__('[Pz] LinkCard3 Settings',	'pz-linkcard3' );	// LinkCard(2)と区別するために3を付与
			} else {
				$menu_manager	=	__('[Pz] LinkCard Manager',		'pz-linkcard3' );
				$menu_settings	=	__('[Pz] LinkCard Settings',	'pz-linkcard3' );
			}

			if		($flg_count ) {
				global	$wpdb;
				
				$result		=	$wpdb->get_row($wpdb->prepare('SELECT COUNT(*) AS count FROM %i WHERE update_result < 100 OR update_result >= 400', $this->db_card ) );
				if	(isset($result ) && isset($result->count ) && $result->count > 0 ) {
					$menu_manager	.=	'&nbsp;<span class="menu-counter lkc3-menu-count">'.$result->count.'</span>';
				}
			}
			add_management_page	('pz-linkcard3-manager',	$menu_manager,		'manage_options', 	$this->cacheman_page,	[$this, 'pz_page_cacheman' ] );
			add_options_page	('pz-linkcard3-settings',	$menu_settings,		'manage_options', 	$this->settings_page,	[$this, 'pz_page_settings' ] );
		}

		public	function	action_admin_menu_order() {
			$this->pz_MoveSubmenuBefore('tools.php', 'pz-linkcard-cacheman', $this->cacheman_page );
			$this->pz_MoveSubmenuBefore('options-general.php', 'pz-linkcard-settings', $this->settings_page );
		}

		private	function	pz_MoveSubmenuBefore($parent_slug, $before_slug, $target_slug ) {
			global	$submenu;

			if	(empty($submenu[$parent_slug] ) || !is_array($submenu[$parent_slug] ) ) {
				return;
			}

			$items			=	array_values($submenu[$parent_slug] );
			$before_index	=	null;
			$target_index	=	null;
			foreach	($items as $index => $item ) {
				if	(!isset($item[2] ) ) {
					continue;
				}
				if	($item[2] === $before_slug ) {
					$before_index	=	$index;
				}
				if	($item[2] === $target_slug ) {
					$target_index	=	$index;
				}
			}
			if	($before_index === null || $target_index === null || $before_index < $target_index ) {
				return;
			}

			$before_item	=	$items[$before_index];
			array_splice($items, $before_index, 1 );
			$target_index	=	null;
			foreach	($items as $index => $item ) {
				if	(isset($item[2] ) && $item[2] === $target_slug ) {
					$target_index	=	$index;
					break;
				}
			}
			if	($target_index === null ) {
				return;
			}
			array_splice($items, $target_index, 0, array($before_item ) );
			$submenu[$parent_slug]	=	$items;
		}

		// 管理画面・Pz カード管理
		public	function	pz_page_cacheman() {
			return			require('lib/pz-lkc3-cacheman.php' );
		}

		// 管理画面・Pz カード管理・エクスポートファイルのダウンロード
		public	function		action_admin_post_export_file() {
			if	(!current_user_can('manage_options') ) {
				wp_die(esc_html(__('You do not have permission to export LinkCard data.', 'pz-linkcard3' ) ), 403 );
			}

			require_once('lib/pz-lkc3-file-export.php' );
		}

		// 管理画面・Pz カード設定
		public	function	pz_page_settings() {
			return			require('lib/pz-lkc3-settings.php' );
		}

		// 管理画面の設定（スタイルシート、スクリプト設定）
		public	function	action_admin_enqueue_scripts($hook ) {
			$this->pz_DebugLog(__FUNCTION__ );

			switch	($hook ) {
				case	'settings_page_pz-linkcard3-settings':
 					wp_enqueue_script('pz-linkcard3-admin-common',		plugins_url('js/pz-lkc3-admin-common.js',	__FILE__ ),		[],		$this->pz_GetAssetVersion('js/pz-lkc3-admin-common.js'),	true );
 					wp_enqueue_script('pz-linkcard3-admin-tabs',		plugins_url('js/pz-lkc3-admin-tabs.js',		__FILE__ ),		[],		$this->pz_GetAssetVersion('js/pz-lkc3-admin-tabs.js'),		true );
 					wp_enqueue_script('pz-linkcard3-admin-search',		plugins_url('js/pz-lkc3-admin-search.js',	__FILE__ ),		[],		$this->pz_GetAssetVersion('js/pz-lkc3-admin-search.js'),		true );
 					wp_enqueue_script('pz-linkcard3-admin-settings',	plugins_url('js/pz-lkc3-admin-settings.js',	__FILE__ ),		['pz-linkcard3-admin-common'],		$this->pz_GetAssetVersion('js/pz-lkc3-admin-settings.js'),	true );
					wp_localize_script('pz-linkcard3-admin-settings',	'lkc3_ajax_preview', [
						'nonce'		=>	wp_create_nonce('lkc_nonce'),
						'ajaxurl'	=>	admin_url('admin-ajax.php'),
						'labels'	=>	[
							'discardChanges'	=>	__('Discard changes?', 'pz-linkcard3' ),
						],
 					] );
					wp_enqueue_script('pz-linkcard3-admin-preview-rules',	plugins_url('js/pz-lkc3-admin-preview-rules.js',	__FILE__ ),	[],		$this->pz_GetAssetVersion('js/pz-lkc3-admin-preview-rules.js'),	true );
					wp_enqueue_script('pz-linkcard3-admin-preview',			plugins_url('js/pz-lkc3-admin-preview.js',		__FILE__ ),	[],		$this->pz_GetAssetVersion('js/pz-lkc3-admin-preview.js'),	true );

 					wp_enqueue_style ('pz-linkcard3-admin',				plugins_url('css/pz-lkc3-admin.css',		__FILE__ ),		[],		$this->pz_GetAssetVersion('css/pz-lkc3-admin.css') );

 					wp_enqueue_script('pz-linkcard3-color-picker',		plugins_url('js/pz-lkc3-color-picker.js',	__FILE__ ),		[],		$this->pz_GetAssetVersion('js/pz-lkc3-color-picker.js'),	true );
					wp_localize_script('pz-linkcard3-color-picker',		'pz_lkc3_color_picker', [
						'labels'	=>	[
							'clear'			=>	__('Clear', 'pz-linkcard3' ),
							'selectColor'	=>	__('Select color', 'pz-linkcard3' ),
						],
					] );
 					wp_enqueue_style ('pz-linkcard3-color-picker',		plugins_url('css/pz-lkc3-color-picker.css',	__FILE__ ),		[],		$this->pz_GetAssetVersion('css/pz-lkc3-color-picker.css') );
					break;

				case	'tools_page_pz-linkcard3-cacheman':
					wp_enqueue_media();
					wp_enqueue_script('pz-linkcard3-admin-common',		plugins_url('js/pz-lkc3-admin-common.js',	__FILE__ ),		[],		$this->pz_GetAssetVersion('js/pz-lkc3-admin-common.js'),	true );
					wp_enqueue_script('pz-linkcard3-admin-cacheman',	plugins_url('js/pz-lkc3-admin-cacheman.js', __FILE__ ),		['pz-linkcard3-admin-common'],		$this->pz_GetAssetVersion('js/pz-lkc3-admin-cacheman.js'),	true );
					wp_localize_script('pz-linkcard3-admin-cacheman',	'pz_lkc3_cacheman_options', [
						'ajaxurl'	=>	admin_url('admin-ajax.php'),
						'nonce'		=>	wp_create_nonce('pz_lkc3_cacheman_columns'),
						'labels'	=>	[
							'discardChanges'	=>	__('Discard changes?', 'pz-linkcard3' ),
							'selectMedia'		=>	__('Select Media', 'pz-linkcard3' ),
							'useMedia'			=>	__('Use this media', 'pz-linkcard3' ),
						],
					] );
					wp_enqueue_script('pz-linkcard3-image-box',			plugins_url('includes/pz-image-box.js',		__FILE__ ),		[],		$this->pz_GetAssetVersion('includes/pz-image-box.js'),		true );
					wp_enqueue_style ('pz-linkcard3-admin',				plugins_url('css/pz-lkc3-admin.css',		__FILE__ ),		[],		$this->pz_GetAssetVersion('css/pz-lkc3-admin.css') );
			}
		}

		// 管理画面時の設定（スタイルシートの追加）
		public	function	action_admin_print_styles() {
			$this->pz_DebugLog(__FUNCTION__ );
		}

		// 管理画面時の設定（スクリプトの追加）
		public	function	action_admin_print_scripts() {
			$this->pz_DebugLog(__FUNCTION__ );
		}

		// 管理画面時の設定（ヘッダー）
		public	function	action_admin_head() {
			$this->pz_DebugLog(__FUNCTION__ );

			if ($this->is_settings ) {
				echo	'<style id="pz-lkc3-preview-css" type="text/css"></style>';
			}
		}

		// 管理画面時の注意書き設定
		public	function	action_admin_notices() {
			$this->pz_DebugLog(__FUNCTION__ );
		}

		// 管理画面時の設定（フッター出力）
		public	function	action_admin_print_footer_scripts() {
			$this->pz_DebugLog(__FUNCTION__ );

			if		(get_current_screen()->id == 'post' || get_current_screen()->id == 'page' ) {
				require('lib/pz-lkc3-input-url.php' );	// ビジュアル エディタ用の挿入ダイアログ

				// クラシック エディタ用のクイックタグ
				if		($this->options['flg-edit-qtag'] ) {
					if	(wp_script_is('quicktags' ) ) {
						wp_enqueue_script($this->plugin_slug.'-admin-qtag',	plugins_url('js/pz-lkc3-quicktags.js', __FILE__ ),			[],				$this->pz_GetAssetVersion('js/pz-lkc3-quicktags.js'), true );
					}
				}
			}

		//	if	($this->options['error-mode'] ) {
		//		if	(!$this->options['error-mode-hide'] ) {
		//			$debug_message	.= '<div class="notice notice-error is-dismissible"><p><strong>'.$this->plugin_name.': '.__('Invalid URL parameter in ', 'pz-linkcard3' ).'<a href="'.$this->options['error-url'].'#lkc3-error" target="_blank">'.$this->options['error-url'].'</a></strong><br>'.__('*', 'pz-linkcard3' ).' '.__('You can cancel this message from <a href=".'.$this->settings_url.'">the setting screen</a>.', 'pz-linkcard3' ).'</p></div>';
		//		}
		//	}
		}

		// 
		public function	action_current_screen($screen ) {
		}

		// 管理画面の設定（フッター）
		public	function	action_admin_footer() {
			if	($this->is_settings ) {
				require('includes/pz-color-picker.php' );	// カラーピッカー用のテンプレート
			}
		}

		public	function	action_admin_footer_plugins() {
			$local_storage_keys	=	[
				'pz-preview-checked',
				'pz-preview-width',
				'pz-preview-height',
				'pz-preview-left',
				'pz-preview-top',
				'pz-preview-mode',
				'pz-preview-docked-height',
				'pz-lkc3-variable-list-visible',
			];
			?>
			<script>
			(() => {
				const plugin = encodeURIComponent(<?php echo wp_json_encode($this->base_name ); ?>);
				const keys = <?php echo wp_json_encode($local_storage_keys ); ?>;
				const clearLocalStorage = () => {
					try {
						keys.forEach((key) => localStorage.removeItem(key));
					} catch (e) {
					}
				};
				document.addEventListener("click", (event) => {
					const link = event.target.closest("a");
					if (!link || !link.href.includes(plugin)) return;
					if (!link.href.includes("action=deactivate") && !link.href.includes("action=delete-selected")) return;
					clearLocalStorage();
				}, true);
			})();
			</script>
			<?php
		}

		// ブロックエディターの設定
		public	function	action_block_editor_assets() {
			$this->pz_DebugLog(__FUNCTION__ );

			$this->pz_EnqueueCardStyles('pz-lkc-block-editor' );

			wp_enqueue_script	('pz-linkcard-block',	plugins_url('js/pz-lkc3-admin-block.js', __FILE__ ),	[
				'wp-blocks', 'wp-block-editor', 'wp-editor', 'wp-components', 'wp-element', 'wp-i18n', 'wp-data', 'wp-hooks', 'wp-compose', 'wp-server-side-render'
			], $this->pz_GetAssetVersion('js/pz-lkc3-admin-block.js'), true );
			$shortcodes = array();
			foreach (array('code1', 'code2', 'code3', 'code4' ) as $key ) {
				$code = preg_replace("/[^a-zA-Z0-9]/", "", $this->options[$key] ?? '' );
				if ($code ) {
					$shortcodes[] = $code;
				}
			}
			if (!$shortcodes ) {
				$shortcodes[] = self::DEFAULTS['code1']['value'];
			}

			wp_localize_script	('pz-linkcard-block',	'pz_lkc_block_icon',		[
				'iconUrl'		=>	plugins_url('assets/pz-lkc3-block.svg', __FILE__),
				'shortcode'		=>	$shortcodes[0],
				'shortcodes'	=>	array_values(array_unique($shortcodes ) ),
				'title'			=>	'Pz-LinkCard3',
				'placeholder'	=>	__('Enter the URL and press Enter', 'pz-linkcard3' ),
				'description'	=>	__('Create a Pz-LinkCard3 shortcode.', 'pz-linkcard3' ),
			] );
		}

		public	function	action_block_assets() {
			if	(!is_admin() ) {
				return;
			}
			$this->pz_DebugLog(__FUNCTION__ );

			$this->pz_EnqueueCardStyles('pz-lkc-block-content' );
		}

		// オプションの更新
		public	function	action_admin_post_save_options() {
			$this->pz_DebugLog(__FUNCTION__ );
		}

		// 通常時のスタイルシート
		public	function	action_wp_enqueue_scripts() {
			$this->pz_DebugLog(__FUNCTION__ );

			// スタイルシート
			$this->flg_amp		=	null;
			$this->pz_EnqueueCardStyles('pz-lkc' );

			// 遅延読み込み
			wp_enqueue_script		('pz-lkc3-lazy',		plugins_url('js/pz-lkc3-lazy.js', __FILE__ ),		[],		$this->pz_GetAssetVersion('js/pz-lkc3-lazy.js'), true );
			wp_localize_script		('pz-lkc3-lazy',		'pz_lkc3_lazy',		[
				'rest_url'		=>	esc_url_raw(rest_url('pz-linkcard/v1/card/' ) ),
			] );

			if	(!empty($this->options['flg-quickmenu'] ) && is_user_logged_in() && current_user_can('manage_options' ) ) {
				wp_enqueue_style	('dashicons' );
				wp_enqueue_script	('pz-lkc3-quickmenu',	plugins_url('js/pz-lkc3-quickmenu.js', __FILE__ ),	[],		$this->pz_GetAssetVersion('js/pz-lkc3-quickmenu.js'), true );
				wp_localize_script	('pz-lkc3-quickmenu',	'pz_lkc3_quickmenu',	[
					'ajax_url'		=>	admin_url('admin-ajax.php' ),
					'edit_url'		=>	$this->cacheman_url,
					'settings_url'	=>	$this->settings_url,
					'logo_url'		=>	esc_url_raw($this->base_url.'assets/pz-linkcard3_logo.svg' ),
					'nonce'			=>	wp_create_nonce('pz_lkc3_refresh_card' ),
					'edit_nonce'	=>	wp_create_nonce('pz-cacheman' ),
					'labels'		=>	[
						'copyTitle'		=>	__('Copy title', 'pz-linkcard3' ),
						'copyExcerpt'	=>	__('Copy excerpt', 'pz-linkcard3' ),
						'copyLink'		=>	__('Copy link address', 'pz-linkcard3' ),
						'edit'			=>	__('Edit', 'pz-linkcard3' ),
						'refreshContent'	=>	__('Refresh post content', 'pz-linkcard3' ),
						'refreshThumbnail'	=>	__('Refresh thumbnail image', 'pz-linkcard3' ),
						'cacheManager'	=>	'Pzカード管理',
						'cardSettings'	=>	'Pzカード設定',
						'refreshing'	=>	__('Retrieving...', 'pz-linkcard3' ),
						'failed'		=>	__('Failed to retrieve the post content.', 'pz-linkcard3' ),
					],
				] );
			}

			// クリック回数
			if	($this->options['flg-click-count'] ) {
				wp_enqueue_script	('pz-lkc3-click',		plugins_url('js/pz-lkc3-count.js', __FILE__ ),		[],		$this->pz_GetAssetVersion('js/pz-lkc3-count.js'), true );
				wp_localize_script	('pz-lkc3-click',		'lkc3_ajax_count',	[
					'ajax_url'		=>	admin_url('admin-ajax.php' ),
					'nonce'			=>	wp_create_nonce('pz_lkc3_nonce' ),
				] );
			}
		}

		// ヘッダー
		public	function	action_wp_head() {
			$this->pz_DebugLog(__FUNCTION__ );

			//wp_enqueue_script	($this->plugin_slug.'-admin-settings',	plugins_url('js/pz-lkc3-admin-settings.js', __FILE__ ),					[],	$this->plugin_version, true );
		}

		// 管理バー用のインラインSVGアイコン
		private	function	pz_GetAdminBarIconSvg() {
			return	'<svg class="pz-lkc3-adminbar-icon" width="20" height="20" viewBox="0 0 920 720" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" style="width: 20px; height: 20px; margin-right: 4px; vertical-align: sub; color: currentColor;">'.
					'<path d="m 212.20737,246.02307 h 363.915" stroke="currentColor" stroke-width="46.9792" stroke-linecap="round" stroke-miterlimit="8" fill="none" fill-rule="evenodd" />'.
					'<path d="m 212.20737,379.02307 h 206.796" stroke="currentColor" stroke-width="46.9792" stroke-linecap="round" stroke-miterlimit="8" fill="none" fill-rule="evenodd" />'.
					'<path d="m 759.5039,252.24389 c -25.0068,0.18279 -50.0786,8.39512 -70.6035,25.02343 l -52.4609,42.5293 a 73.737587,75.212334 0 0 1 7.8105,-0.42383 73.737587,75.212334 0 0 1 47.5313,17.71094 l 28.791,-23.33984 c 13.2574,-10.74062 29.5717,-15.28849 45.3555,-13.95508 h 0.01 c 15.7837,1.33337 31.0307,8.55424 42.1504,21.35156 22.2351,25.59039 18.8419,63.01751 -7.6699,84.49609 L 658.4219,520.74193 c -26.511,21.47395 -65.2546,18.20002 -87.4941,-7.38281 -16.1178,-18.57162 -18.7691,-43.38191 -8.8829,-63.80273 l -53.1757,43.70312 c 1e-4,6.4e-4 -10e-5,0.001 0,0.002 3.2097,18.12781 11.2461,35.66986 24.2812,50.68945 v 0.008 c 39.3609,45.29988 110.0193,51.26928 156.9336,13.26172 L 832.0703,442.11303 v -0.008 C 878.9804,404.09382 885.1768,335.83074 845.8145,290.52904 826.1343,267.87912 798.6261,255.0598 770.1758,252.656 h 0.027 c -3.5563,-0.30047 -7.1269,-0.43823 -10.6993,-0.41211 z" fill="currentColor" stroke="currentColor" stroke-width="3.54463" fill-rule="evenodd" />'.
					'<path d="m 483.2719,669.21419 c 25.0068,-0.18279 50.0786,-8.39512 70.6035,-25.02343 l 52.4609,-42.5293 a 73.737587,75.212334 0 0 1 -7.8105,0.42383 73.737587,75.212334 0 0 1 -47.5313,-17.71094 l -28.791,23.33984 c -13.2574,10.74062 -29.5717,15.28849 -45.3555,13.95508 h -0.01 c -15.7837,-1.33337 -31.0307,-8.55424 -42.1504,-21.35156 -22.2351,-25.59039 -18.8419,-63.01751 7.6699,-84.49609 L 584.3539,400.71616 c 26.511,-21.47395 65.2546,-18.20002 87.4941,7.38281 16.1178,18.57162 18.7691,43.38191 8.8829,63.80273 l 53.1757,-43.70313 c -10e-5,-6.3e-4 10e-5,-9.9e-4 0,-0.002 -3.2097,-18.12781 -11.2461,-35.66986 -24.2812,-50.68945 v -0.008 C 670.2645,332.19925 599.6061,326.22985 552.6918,364.23741 L 410.7055,479.34485 v 0.008 c -46.9101,38.0114 -53.1065,106.27448 -13.7442,151.57618 19.6802,22.64992 47.1884,35.46924 75.6387,37.87304 h -0.027 c 3.5563,0.30047 7.1269,0.43823 10.6993,0.41211 z" fill="currentColor" stroke="currentColor" stroke-width="3.54463" fill-rule="evenodd" />'.
					'<path d="m 838.28841,490.2264 10e-6,69.2172 c 0,24.97305 -20.71999,45.07772 -46.45737,45.07772 l -89.85895,-10e-6" fill="none" stroke="currentColor" stroke-width="45.7624" stroke-linecap="round" stroke-linejoin="miter" stroke-miterlimit="50" />'.
					'<path d="m 336.05804,601.11245 h -208.8833 c -27.7,0 -50.000004,-22.3 -50.000004,-50 V 151.88977 c 0,-27.7 22.300004,-50 50.000004,-50 v 0 h 657.30431 c 27.7,0 50,22.3 50,50 l -10e-5,74.25782" fill="none" stroke="currentColor" stroke-width="50" stroke-linecap="round" stroke-linejoin="miter" stroke-miterlimit="50" />'.
					'</svg>';
		}

		// 管理バーのメニュー追加（今後：記述エラーやリンク切れなど実装予定？）
		public	function	action_admin_bar_menu($wp_admin_bar) {
			$this->pz_DebugLog(__FUNCTION__ );

			$flg_adminbar	=	!empty($this->options['flg-adminbar'] );
			if	(
				is_admin()
				&& isset($_POST['_wpnonce'], $_POST['properties'] )
				&& is_array($_POST['properties'] )
				&& wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'] ) ), 'pz-settings' )
			) {
				$posted_properties	=	map_deep(wp_unslash($_POST['properties'] ), 'sanitize_text_field' );
				if	(array_key_exists('flg-adminbar', $posted_properties ) ) {
					$flg_adminbar	=	!empty($posted_properties['flg-adminbar'] );
				}
			}

			if	($flg_adminbar ) {
				$wp_admin_bar->add_menu(array('id' => 'pz-lkc3',										'title' => $this->pz_GetAdminBarIconSvg().esc_html__('Pz-LinkCard', 'pz-linkcard3' ),	'href' => $this->cacheman_url ) );
//				$wp_admin_bar->add_menu(array('id' => 'pz-lkc3',										'title' => '<span style="vertical-align: sub;"><img src="'.$this->base_url.'assets/pz-linkcard3_icon.svg" height="24" width="24" alt="Pz-LinkCard3" style="height: 20px;" /></span>'.__('Pz-LinkCard',			'pz-linkcard3' ).'</span>',	'href' => '#' ) );
				$wp_admin_bar->add_menu(array('id' => 'pz-lkc3-cacheman',	'parent' => 'pz-lkc3',	'title' => __('Pz-LinkCard Manager',	'pz-linkcard3' ),	'href' => $this->cacheman_url,	'meta' => array('target' => '_parent' ) ) );
				$wp_admin_bar->add_menu(array('id' => 'pz-lkc3-settings',	'parent' => 'pz-lkc3',	'title' => __('Pz-LinkCard Settings',	'pz-linkcard3' ),	'href' => $this->settings_url,	'meta' => array('target' => '_parent' ) ) );
			}
		}

		// フッター
		public	function	action_wp_footer() {
			$this->pz_DebugLog(__FUNCTION__ );
		}

		// 更新完了
		public	function	action_upgrader_process_complete($upgrader_object, $options ) {
			// プラグインの通知以外は終了
			if	($options['type'] !== 'plugin' ) {
				return;
			}

			// 更新または自動更新の通知以外は終了
			if	(($options['action'] !== 'update' ) && ($options['action'] !== 'auto_update' ) ) {
				return;
			}

			// 一覧が配列でない場合は終了
			if		(!isset($options['plugins'] ) || !is_array($options['plugins'] ) ) {
				return;
			}

			// プラグイン更新時の処理
			foreach	($options['plugins']	as	$plugin_path ) {
				if	($plugin_path === $this->base_name ) {
					$this->pz_DebugLog(__FUNCTION__, 'Update Completed.' );

					// $this->pz_SetStyle();			// スタイルシートを生成
				}
			}
		}

		// クリックカウント
		public function action_ajax_lkc_click_count() {
			if	(empty($this->options['flg-click-count'] ) ) {
				wp_send_json_success('ignored');
				return;
			}

			check_ajax_referer('pz_lkc3_nonce', 'nonce');

			if	(!$this->pz_IsSameSiteAjaxRequest() ) {
				wp_send_json_success('ignored');
				return;
			}

			// 入力チェック
			$card_id = isset($_POST['card_id']) ? absint(wp_unslash($_POST['card_id'] ) ) : 0;
			if ($card_id <= 0) {
				$this->pz_DebugLog(__FUNCTION__, 'missing/invalid card_id' );
				wp_send_json_error('unknown Data ID.');
				return;
			}

			global $wpdb;

			$ip = isset($_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'] ) ) : '';
			$ua = isset($_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'] ) ) : '';
			$referer = isset($_SERVER['HTTP_REFERER'] ) ? esc_url_raw(wp_unslash($_SERVER['HTTP_REFERER'] ) ) : '';
			$now = current_time('mysql');

			// --- 1日以内の同一カード・同IPクリックがあるか確認 ---
			
			$exists = $wpdb->get_var(
				$wpdb->prepare(
					'SELECT COUNT(*) FROM %i WHERE card_id = %d AND ip = %s AND clicked_at > (NOW() - INTERVAL 1 DAY)',
					$this->db_click, $card_id, $ip
				)
			);

			if ($exists > 0) {
				wp_send_json_success('duplicate click ignored');
				return;
			}

			// --- ログテーブルにINSERT ---
			
			$wpdb->insert(
				$this->db_click,
				[
					'card_id' => $card_id,
					'ip' => $ip,
					'user_agent' => $ua,
					'referer' => $referer,
					'clicked_at' => $now,
				],
				['%d', '%s', '%s', '%s', '%s']
			);

			// --- cardテーブルのclick_countを更新 ---
			
			$updated = $wpdb->query($wpdb->prepare(
				'UPDATE %i SET click_count = click_count + 1 WHERE card_id = %d',
				$this->db_card, $card_id
			) );

			if ($updated !== false && $updated > 0) {
				wp_send_json_success('Click counted');
			} else {
				$err = $wpdb->last_error;
				$this->pz_DebugLog(__FUNCTION__, "DB update failed for card_id={$card_id} - updated={$updated} - last_error={$err}" );
				wp_send_json_error($err ?: 'DB update failed');
			}
		}

		// 設定画面・CSS生成コールバック
		public	function	action_ajax_lkc_refresh_card() {
			if	(!current_user_can('manage_options' ) ) {
				wp_send_json_error(__('You do not have permission to update LinkCard data.', 'pz-linkcard3' ), 403 );
				return;
			}
			check_ajax_referer('pz_lkc3_refresh_card', 'nonce');
			if	(!$this->pz_IsSameSiteAjaxRequest() ) {
				wp_send_json_error(__('Invalid request.', 'pz-linkcard3' ), 403 );
				return;
			}

			$card_id = isset($_POST['card_id'] ) ? absint(wp_unslash($_POST['card_id'] ) ) : 0;
			if	($card_id <= 0 ) {
				wp_send_json_error(__('Invalid card ID', 'pz-linkcard3' ), 400 );
				return;
			}

			$data = $this->pz_GetCache(array('card_id' => $card_id ) );
			if	(empty($data['card_id'] ) || empty($data['url'] ) ) {
				wp_send_json_error(__('Card not found', 'pz-linkcard3' ), 404 );
				return;
			}

			$url_info = $this->pz_GetURLInfo($data['url'] );
			if	(!empty($url_info['is_internal'] ) ) {
				$post_data = $this->pz_GetPost(array(
					'card_id'	=>	$card_id,
					'url'		=>	$data['url'],
					'post_id'	=>	$data['post_id'] ?? '',
				) );
				if	(is_array($post_data ) && !empty($post_data['update_result'] ) ) {
					$data = array_merge($data, $post_data );
				}
			} else {
				$fetched = $this->pz_GetCURL($data, 10 );
				if	(is_array($fetched ) && !array_key_exists('error', $fetched ) ) {
					$data = array_merge($data, $fetched );
				}
			}

			$data['update_time']		= current_time('timestamp', false );
			$data['regist_time']		= $data['regist_time'] ?? $data['update_time'];
			$data['regist_title']	= $data['regist_title'] ?? $data['title'] ?? '';
			$data['regist_excerpt']	= $data['regist_excerpt'] ?? $data['excerpt'] ?? '';
			$data['regist_result']	= $data['regist_result'] ?? $data['update_result'] ?? '';
			$data['alive_result']	= $data['update_result']	??	$data['alive_result']	??	'';
			$data['alive_time']		= $data['update_time']	??	'';

			$data = $this->pz_SetCache($data );
			if	(empty($data['card_id'] ) ) {
				wp_send_json_error(__('Card not found', 'pz-linkcard3' ), 404 );
				return;
			}

			$display_data	=	$this->pz_GetLinkCard(array(
				'card_id'	=>	$card_id,
				'url'		=>	$data['url'],
				'post_id'	=>	$data['post_id'] ?? '',
			) );
			if	(is_array($display_data ) && !empty($display_data['card_id'] ) ) {
				$data	=	$display_data;
			}

			wp_send_json_success(array(
				'card_id'	=>	$card_id,
				'html'		=>	$this->pz_GetHTML($data ),
			) );
		}

		public	function	action_ajax_lkc_refresh_thumbnail() {
			if	(!current_user_can('manage_options' ) ) {
				wp_send_json_error(__('You do not have permission to update LinkCard data.', 'pz-linkcard3' ), 403 );
				return;
			}
			check_ajax_referer('pz_lkc3_refresh_card', 'nonce');
			if	(!$this->pz_IsSameSiteAjaxRequest() ) {
				wp_send_json_error(__('Invalid request.', 'pz-linkcard3' ), 403 );
				return;
			}

			$card_id = isset($_POST['card_id'] ) ? absint(wp_unslash($_POST['card_id'] ) ) : 0;
			if	($card_id <= 0 ) {
				wp_send_json_error(__('Invalid card ID', 'pz-linkcard3' ), 400 );
				return;
			}

			$data = $this->pz_GetCache(array('card_id' => $card_id ) );
			if	(empty($data['card_id'] ) || empty($data['url'] ) ) {
				wp_send_json_error(__('Card not found', 'pz-linkcard3' ), 404 );
				return;
			}

			$url_info = $this->pz_GetURLInfo($data['url'] );
			if	(!empty($url_info['is_internal'] ) ) {
				$post_data = $this->pz_GetPost(array(
					'card_id'	=>	$card_id,
					'url'		=>	$data['url'],
					'post_id'	=>	$data['post_id'] ?? '',
				) );
				if	(is_array($post_data ) && !empty($post_data['update_result'] ) ) {
					$data = $this->pz_SetCache(array_merge($data, $post_data ) );
				}
			} elseif (!empty($data['thumbnail'] ) ) {
				$this->pz_GetImage($data['thumbnail'], true );
			}

			$data = $this->pz_GetCache(array('card_id' => $card_id ) );
			$display_data	=	$this->pz_GetLinkCard(array(
				'card_id'	=>	$card_id,
				'url'		=>	$data['url'] ?? '',
				'post_id'	=>	$data['post_id'] ?? '',
			) );
			if	(is_array($display_data ) && !empty($display_data['card_id'] ) ) {
				$data	=	$display_data;
			}
			$data['image_refresh_stamp']	=	current_time('timestamp', false );
			wp_send_json_success(array(
				'card_id'	=>	$card_id,
				'html'		=>	$this->pz_GetHTML($data ),
			) );
		}
		public	function	pz_generate_css_callback() {
			$this->pz_DebugLog(__FUNCTION__ );

			check_ajax_referer('lkc_nonce', '_ajax_nonce');
			if ( ! current_user_can('manage_options') ) {
					wp_die( -1, 403 );
			}

			// 入力値の受け取り
			// ここでCSS文字列を生成する（例: テンプレートベース）
			$prop	=	isset($_POST['properties'] ) && is_array($_POST['properties'] ) ? map_deep(wp_unslash($_POST['properties'] ), 'sanitize_text_field' ) : array();
			$css	=   $this->pz_MakeCSSText($prop );

			// CSSテキストとして返す
			header('Content-Type: text/css; charset=utf-8');
			echo		esc_html(wp_strip_all_tags($css ) );

			wp_die();			
		}

		// 設定画面・HTML生成コールバック
		public	function	pz_generate_html_callback() {
			$this->pz_DebugLog(__FUNCTION__ );

			check_ajax_referer('lkc_nonce', '_ajax_nonce');
			if ( ! current_user_can('manage_options') ) {
					wp_die( -1, 403 );
			}

			// プレビューエリア
			$prop	=	isset($_POST['properties'] ) && is_array($_POST['properties'] ) ? map_deep(wp_unslash($_POST['properties'] ), 'sanitize_text_field' ) : array();
			$prop['ex-thumbnail-get']	=	3;
			$tag			=	$this->pz_GetHTML($this->pz_GetPreviewCardData('external' ), $prop );
			$preview_1		=	'<div id="pz-preview-ex">'.$tag.'</div>';
			$tag			=	$this->pz_GetHTML($this->pz_GetPreviewCardData('internal' ), $prop );
			$preview_2		=	'<div id="pz-preview-in">'.$tag.'</div>';
			$html			=	$preview_1.$preview_2;

			// HTMLテキストとして返す
			header('Content-Type: text/html; charset=utf-8');
			echo		wp_kses_post($html );

			wp_die();			
		}

		// 管理画面・表示オプション保存
		public	function	pz_save_cacheman_columns_callback() {
			check_ajax_referer('pz_lkc3_cacheman_columns', 'nonce');

			if	(!current_user_can('manage_options') ) {
				wp_send_json_error('forbidden', 403);
			}

			$allowed_columns	=	$this->pz_GetCachemanColumnKeys();
			$columns			=	isset($_POST['columns'] ) && is_array($_POST['columns'] ) ? map_deep(wp_unslash($_POST['columns'] ), 'sanitize_text_field' ) : array();
			$save_columns		=	array();

			foreach	($allowed_columns as $column ) {
				$save_columns[$column]	=	isset($columns[$column] ) && ( '1' === (string) $columns[$column] || 1 === $columns[$column] );
			}

			update_user_meta(get_current_user_id(), 'pz_lkc3_cacheman_columns', $save_columns );

			$allowed_per_page	=	$this->pz_GetCachemanPerPageChoices();
			$per_page			=	isset($_POST['per_page'] ) ? absint(wp_unslash($_POST['per_page'] ) ) : 0;
			if	(in_array($per_page, $allowed_per_page, true ) ) {
				update_user_meta(get_current_user_id(), 'pz_lkc3_cacheman_per_page', $per_page );
			} else {
				$per_page	=	intval(get_user_meta(get_current_user_id(), 'pz_lkc3_cacheman_per_page', true ) );
				if	(!in_array($per_page, $allowed_per_page, true ) ) {
					$per_page	=	10;
				}
			}

			wp_send_json_success(array(
				'columns'	=>	$save_columns,
				'per_page'	=>	$per_page,
			) );
		}

		// 管理画面・編集画面レイアウト保存
		// 管理画面＞プラグイン＞一覧＞クイックメニュー
		public	function	filter_plugin_action_links($links ) {
			$links['manager']	=	'<a href="'.$this->cacheman_url.'">'.__('Manager' , 'pz-linkcard3' ).'</a>';
			$links['settings']	=	'<a href="'.$this->settings_url.'">'.__('Settings', 'pz-linkcard3' ).'</a>';
			return	$links;
		}

		// 管理画面時のスタイルシート、スクリプト設定
		public	function	filter_mce_external_plugins($plugins ) {
			if	($this->options['flg-edit-insert'] ) {
				$plugins[ "pz_linkcard_tinymce" ]	=	$this->base_url.'js/pz-lkc3-mce-button.js';
			}
			return	$plugins;
		}

		// 管理画面時のスタイルシート、スクリプト設定
		public	function	filter_mce_buttons($buttons ) {
			if	($this->options['flg-edit-insert'] ) {
				$buttons[]							=	'pz_linkcard_insert_shortcode';
			}
			return	$buttons;
		}

		// WP-CRONスケジュール（最初の読み込み）
		public	function	hook_regist() {
			$this->pz_DebugLog(__FUNCTION__, 'Start' );

			$log	=	null;
			require('lib/pz-lkc3-cron-regist.php' );

			$this->pz_DebugLog(__FUNCTION__, 'End' );
			return		$log;
		}

		// WP-CRONスケジュール（存在チェック）
		public	function	hook_check_alive() {
			$this->pz_DebugLog(__FUNCTION__, 'Start' );

			$log	=	null;
			require('lib/pz-lkc3-cron-alive.php' );
			$this->pz_DebugLog(__FUNCTION__, 'End' );
			return		$log;
		}

		// WP-CRONスケジュール（SNSカウント取得）
		public	function	hook_check_sns() {
			$this->pz_DebugLog(__FUNCTION__, 'Start' );

			$log	=	null;
			require('lib/pz-lkc3-cron-sns.php' );
			$this->pz_DebugLog(__FUNCTION__, 'End' );
			return		$log;
		}

		// デバグ用の文字列表示
		private	function	pz_IsLogMode() {
			return	!empty($this->options['debug-mode'] ) && !empty($this->options['log-mode'] );
		}

		private	function	pz_DebugLog($function, $user_message = '' ) {
		}

		private	function	pz_GetFilesystem() {
			global	$wp_filesystem;

			if	($wp_filesystem instanceof WP_Filesystem_Base ) {
				return	$wp_filesystem;
			}
			require_once ABSPATH.'wp-admin/includes/file.php';
			if	(!WP_Filesystem() ) {
				return	false;
			}
			return	$wp_filesystem;
		}

		private	function	pz_EnsureDirectory($dir, $wp_filesystem = null ) {
			if	(!$wp_filesystem ) {
				$wp_filesystem	=	$this->pz_GetFilesystem();
			}
			if	(!$wp_filesystem ) {
				return	false;
			}
			if	($wp_filesystem->is_dir($dir ) ) {
				return	true;
			}
			if	(function_exists('wp_mkdir_p' ) && wp_mkdir_p($dir ) ) {
				return	true;
			}
			return	$wp_filesystem->mkdir($dir, FS_CHMOD_DIR ) && $wp_filesystem->is_dir($dir );
		}

		private	function	pz_GetAssetVersion($relative_path ) {
			$wp_filesystem	=	$this->pz_GetFilesystem();
			$path			=	plugin_dir_path(__FILE__ ).ltrim($relative_path, '/\\' );
			return	($wp_filesystem && $wp_filesystem->exists($path ) ) ? $wp_filesystem->mtime($path ) : $this->plugin_version;
		}

		private	function	pz_GetStyleVersion($filename ) {
			$wp_filesystem	=	$this->pz_GetFilesystem();
			$path			=	$this->dir_style.ltrim($filename, '/\\' );
			return	($wp_filesystem && $wp_filesystem->exists($path ) ) ? $wp_filesystem->mtime($path ) : $this->plugin_version;
		}

		private	function	pz_EnqueueCardStyles($style_handle ) {
			$css_suffix		=	$this->options['flg-compress'] ? '.min.css' : '.css';
			$style_file		=	'style'.$css_suffix;

			wp_enqueue_style($style_handle, $this->url_style.$style_file, [], $this->pz_GetStyleVersion($style_file ) );
		}

		private	function	pz_GetPreviewCardData($type ) {
			$data	=	array(
				'card_id'			=>	1,
				'thumbnail'			=>	$this->base_url.'img/icon-pz-linkcard.png',
				'site_icon'			=>	$this->base_url.'img/icon_popozure.ico',
				'post_date'			=>	$this->now - 86400,
				'post_modified'		=>	$this->now,
				'post_cat'			=>	'',
				'sns_twitter'		=>	1234,
				'sns_facebook'		=>	1234,
				'sns_hatena'		=>	1234,
				'update_result'		=>	200,
				'regist_time'		=>	1,
			);

			if	('internal' === $type ) {
				return	array_merge($data, array(
					'url'				=>	$this->my_url.'/example/',
					'domain'			=>	$this->my_url,
					'site_name'			=>	$this->my_sitename,
					'title'				=>	$this->my_sitename,
					'excerpt'			=>	get_bloginfo('description' ),
				) );
			}

			return	array_merge($data, array(
				'url'				=>	'https://example.popozure.info/pz-linkcard3',
				'domain'			=>	'popozure.info',
				'site_name'			=>	__('Popozure.', 'pz-linkcard3' ),
				'title'				=>	__('Pz-LinkCard3 - WordPress plugin to display links in card format', 'pz-linkcard3' ),
				'excerpt'			=>	__('Pz-LinkCard3 is a WordPress plugin that displays links in card format. Say goodbye to plain text links!', 'pz-linkcard3' ),
			) );
		}

		private	function	pz_GetCachemanColumnKeys() {
			return	array(
				'id',
				'excerpt',
				'charset',
				'domain',
				'sns',
				'regist_time',
				'update_time',
				'sns_time',
				'alive_time',
				'post_id',
				'click_count',
				'result',
			);
		}

		private	function	pz_GetCachemanPerPageChoices() {
			return	array(10, 20, 50, 100);
		}

		private	function	pz_OutputLOG($function, $user_message = '' ) {
			if	(!$this->pz_IsLogMode() ) {
				return	false;
			}
			$wp_filesystem	=	$this->pz_GetFilesystem();
			if	(!$wp_filesystem ) {
				return	false;
			}
			if	(!$this->pz_EnsureDirectory($this->dir_debug, $wp_filesystem ) ) {
				return	false;
			}
			$filename		=	$this->dir_debug.$this->plugin_slug.'_'.gmdate('Ymd', current_time('timestamp', false ) ).'.log';
			if	(function_exists('microtime' ) ) {
				$timestamp	=	microtime(true );
				$dt			=	intval($timestamp );
				$ms			=	substr(intval($timestamp * 1000 ), -3, 3 );
				$timestamp	=	wp_date('Y-m-d H:i:s', $dt ).'.'.$ms;
			} else {
				$timestamp	=	gmdate('Y-m-d H:i:s', current_time('timestamp', false ) );
			}
			$count			=	sprintf('%03d', $this->logging_count );
			$message		=	($this->logging_count ? '' : PHP_EOL ).$timestamp.' '.$count.' ['.$function.'] '.$user_message.(mb_substr($user_message, -1, 1) == PHP_EOL ? '' : PHP_EOL );
			$this->logging_count++;
			$log_text		=	$wp_filesystem->exists($filename ) ? $wp_filesystem->get_contents($filename ) : '';
			$result			=	$wp_filesystem->put_contents($filename, $log_text.$message, FS_CHMOD_FILE );
			return			$result;
		}
	}
	
	$pz_linkcard3	=	new pz_linkcard3;
}

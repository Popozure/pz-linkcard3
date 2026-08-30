<?php
	
if (!defined( 'ABSPATH' ) ) { header( 'HTTP/1.1 403 Forbidden' ); exit; }
    // 内部リンク・記事情報取得

    // 初期化
    $url			=	$data['url']		??	'';
    $url_redir		=	$data['url_redir']	??	'';
    $post_id		=	$data['post_id']	??	'';
    $thumbnail		=	$data['thumbnail']	??	'';
    $siteicon		=	$data['site_icon']	??	'';

    // URL妥当性チェック
    if (!filter_var($url, FILTER_VALIDATE_URL ) ) {
        return	[];
    }

    $sitename      =   $this->my_sitename;         // サイト名
    $domain         =   $this->my_domain;           // ドメイン名
    $domain_url     =   $this->my_domain_url;       // ドメインURL
    $siteicon        =   function_exists('has_site_icon' ) && has_site_icon() ? get_site_icon_url(16, null, 0 ) : '' ;   // サイトアイコン

    // 記事IDとURL
    if		    ($url && !$post_id ) {
        $post_id	=	url_to_postid($url );		// URLから記事ID取得
    } elseif    (!$url && $post_id ) {
        $url		=	get_permalink($post_id );	// 記事IDからURL取得
    }

    // 変数の初期化
    $update_result          =   null;
    $post_cat				=	null;

    // 記事内容の格納（投稿・固定投稿）
    if	($post_id ) {
        // 記事IDが取得できた場合
        $update_result		=	200;									// 外部取得と同じコードをセット
        $post				=	get_post($post_id );					// 記事情報
        $title				=	$post->post_title;						// 記事タイトル
        $excerpt			=	$post->post_content;					// 記事内容から抜粋

        // 「抜粋」優先
        if	($this->options['in-content-get'] == 1 && $post->post_excerpt ) {	// 記事取得方法：「抜粋文」があった場合、優先する
            $excerpt		=	$post->post_excerpt;					// 抜粋文
        }

        // 「カスタムフィールド」優先
        if	($this->options['in-content-get'] == 3 ) {							// 記事取得方法：「カスタムフィールド」があった場合、優先する
            $meta_title		=	get_post_meta($post_id, $this->options['in-content-title'] );
            if	(array($meta_title ) && array_key_exists(0, $meta_title ) ) {
                $title		=	$meta_title[0];
            }
            $meta_excerpt	=	get_post_meta($post_id, $this->options['in-content-excerpt'] );
            if	(array($meta_excerpt ) && array_key_exists(0, $meta_excerpt ) ) {
                $excerpt	=	$meta_excerpt[0];
            }
        }

        // 投稿日取得
        $post_date			=   mysql2date('U', $post->post_date );		// 投稿日       
        $post_modified		=   mysql2date('U', $post->post_modified );	// 更新日

        // カテゴリ取得
        $post_cat_info		=	get_the_category($post_id );			// カテゴリ情報
        $post_cat			=	[];                                     // カテゴリ情報テーブル
        $temp_cat           =	[];	                                    // カテゴリ情報を格納する一時テーブル

        // 投稿のカテゴリ情報から取得＆ソートする
        foreach ($post_cat_info     as  $cat ) {
            if    ($cat->cat_ID  != 1 ) {
                $sort_key               =   str_pad($cat->category_parent, 5, '0', STR_PAD_LEFT ).','.str_pad($cat->cat_ID, 5, '0', STR_PAD_LEFT );
                $temp_cat[$sort_key]    =   array(
                    'taxonomy'          =>  $cat->taxonomy,
                    'cat_ID'            =>  $cat->cat_ID,
                    'category_parent'   =>  $cat->category_parent,
                    'cat_name'          =>  $cat->cat_name, 
                    'link'              =>  get_category_link($cat->cat_ID ) );
            }
        }
        if    ($temp_cat ) {
            ksort($temp_cat);
            foreach ($temp_cat          as  $key => $value ) {
                $idx                    =   $value['cat_ID'];
                $a                      =   $value['cat_name'];
                $post_cat[$idx]['name'] =   $a;
                $post_cat[$idx]['link']	=	$value['link'];
            }
        }

        // サムネイル取得
        $thumbnail_size		=	$this->options['in-thumbnail-size'] ? $this->options['in-thumbnail-size'] : 'thumbnail' ;
        $thumbnail			=	get_the_post_thumbnail_url($post_id, $thumbnail_size );
    } else {
        // 記事IDが取得出来なかった場合
        $update_result		=	404;
        $title				=	get_bloginfo('name' );
        $excerpt			=	get_bloginfo('description' );
        $post_date			=	0;
        $post_modified		=	0;
        $thumbnail			=	null;

        if	(rtrim($this->pz_DecodeURL($url ), '/' ) == rtrim($this->pz_DecodeURL(home_url() ), '/' ) ) {
            // トップページの場合
            $update_result		=	200;
        } else {
            // カテゴリ ページのディレクトリ名を取得
            $default_cat_id		=	get_option('default_category' );
            $default_cat_link	=	rtrim(get_category_link($default_cat_id ), '/' );
            $default_cat_slug	=	rtrim(get_category( $default_cat_id )->slug, '/' );

            // カテゴリーページかどうかチェック
            $url_decoded		=	$this->pz_DecodeURL($url ,false );
            $cat_base_url		=	rtrim(mb_substr($default_cat_link, 0, mb_strlen($default_cat_link ) - mb_strlen($default_cat_slug ) ), '/' );	// ベース部分だけ抽出
            if (mb_substr($url, 0, mb_strlen($cat_base_url ) ).'/' == $cat_base_url.'/' ) {
                $cat_slug		=	mb_substr($url_decoded, mb_strlen($cat_base_url ) + 1 );
                $cat_last_slug	=	basename($cat_slug );
                $cat_data		=	get_category_by_slug($cat_last_slug );
                if	($cat_data ) {
                    $cat_count		=	($cat_data->count - 0 );
                    $title			=	__('Category', 'pz-linkcard3' ).' '.__('‘', 'pz-linkcard3' ).$cat_data->name.__('’', 'pz-linkcard3' );
                    $excerpt		=	__('(', 'pz-linkcard3' ).__('Count', 'pz-linkcard3' ).':'.($cat_data->count - 0 ).__(')', 'pz-linkcard3' ).' '.$cat_data->description;
                    $update_result	=	200;
                } else {
                    $title			=	__('Category', 'pz-linkcard3' ).' '.__('‘', 'pz-linkcard3' ).$cat_slug.__('’', 'pz-linkcard3' );
                    $excerpt		=	__('Not Found', 'pz-linkcard3' );
                    $update_result	=	403;
                }
            } else {
                // タグ ページの処理
                $cat_dir			=	get_option('tag_base' );
                $cat_url			=	$domain_url.'/'.($cat_dir ? $cat_dir : 'tag' ).'/';
                $cat_len			=	mb_strlen($cat_url );
                if	(mb_substr($url, 0, $cat_len ) == $cat_url ) {
                    $cat_slug		=	mb_substr($url, $cat_len );
                    $cat_data		=	get_tags(array('slug' => $cat_slug ) );
                    if	($cat_data ) {
                        $title			=	__('Tag', 'pz-linkcard3' ).' '.__('‘', 'pz-linkcard3' ).$cat_data[0]->name.__('’', 'pz-linkcard3' );
                        $excerpt		=	__('(', 'pz-linkcard3' ).__('Count', 'pz-linkcard3' ).':'.($cat_data[0]->count - 0 ).__(')', 'pz-linkcard3' ).' '.$cat_data[0]->description;
                        $update_result	=	200;
                    } else {
                        $title			=	__('Tag', 'pz-linkcard3' ).' '.__('‘', 'pz-linkcard3' ).rawurldecode($cat_slug ).__('’', 'pz-linkcard3' );
                        $excerpt		=	__('Not Found', 'pz-linkcard3' );
                        $update_result	=	403;
                    }
                } else {
                    if	($this->options['in-content-redir'] ) {
                        $result			=	$this->Pz_GetCURL($data );		// 外部サイトとして読み込み
                        if	(isset($result ) && is_array($result ) && isset($result['url'] ) ) {
                            $data		=	$result;
                            $result		=	$this->pz_SetCache($data );
                        }
                        return			$result;
                    }
                }
            }
        }
    }

    // タイトル整形
    if				($str	=	$title ) {										// 代入しながら判定
        if			($str	=	wp_strip_all_tags($str ) ) {							// HTMLタグ除去
            if		($str	=	esc_html($str ) ) {								// HTMLエスケープ
                if	($str	=	str_replace(array("\r", "\n"), '', $str ) ) {	// 改行を除去
                    $str	=	mb_strimwidth($str, 0, 200, '...' );			// 200文字制限
                }
            }
        }
        $title			=	$str;
    }

    // 抜粋文整形
    if						($str	=	$excerpt ) {										// 代入しながら判定
        if					($str	=	wp_strip_all_tags($str ) ) {								// HTMLタグ除去
            if				($str	=	esc_html($str ) ) {									// HTMLエスケープ
                if			($str	=	str_replace(array("\r", "\n"), '', $str ) ) {		// 改行を除去
                    if		($str	=	preg_replace('/<!--more-->.+/is', '', $str ) ) {	// moreタグ以降削除
                        if	($str	=	preg_replace('/\[[^]]*\]/', '', $str ) ) {			// ショートコードすべて除去
                            $str	=	mb_strimwidth($str, 0, 500, '...' );				// 500文字制限
                        }
                    }
                }
            }
        }
        $excerpt		=	$str;
    }

    if  ($url_redir == $url ) {                     // リダイレクト先URLが元のURLと同じ場合
        $url_redir  =   '';                         // リダイレクト先URLを空にする
    }

    // データセット
    $url_info					=	$this->Pz_GetURLInfo($url );	// URL解析（自サイトチェック）
    $data['url']		    	=	$url;
    $data['url_redir']			=	$url_redir;
    $data['domain']				=	$this->my_domain;				// ドメイン名
    $data['site_name']			=	$this->my_sitename;				// サイト名
    $data['title']				=	$title;
    $data['excerpt']			=	$excerpt;
    $data['thumbnail']			=	$thumbnail;
    $data['site_icon']			=	$siteicon;
    $data['charset']			=	$this->my_charset;              // 文字コード
    $data['alive_result']		=	$update_result;
    $data['post_id']			=	$post_id;
    $data['post_date']			=	$post_date;
    $data['post_modified']		=	$post_modified;
    $data['post_cat']			=	$post_cat;
    $data['update_result']		=	$update_result;

    return	$data;

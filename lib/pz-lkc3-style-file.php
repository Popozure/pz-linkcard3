<?php
	
if (!defined( 'ABSPATH' ) ) { header( 'HTTP/1.1 403 Forbidden' ); exit; } ?>
<?php
	$wp_filesystem	=	$this->pz_GetFilesystem();
	if	(!$wp_filesystem ) {
		return	2;
	}

	// スタイルシート用のディレクトリが無かったら作成する
	if	(!$this->pz_EnsureDirectory($this->dir_style, $wp_filesystem ) ) {
		return	2;
	}
	$result				=	1;

	// 時間計測
	if	($this->options['debug-mode'] ) {
		$start_time		=	hrtime(true );
	}

	$prop				=	$this->options;
	$file_text			=	$this->pz_MakeCSSText($prop );

	if	($file_text === 2 || $file_text === false ) {
		return	9;
	}
	if	(!$file_text ) {
		return	9;
	}

	// コメント除去
	$file_text			=	esc_html(preg_replace('/\s*\/\*[^*]*\*+([^\/][^*]*\*+)*\//', '', $file_text ) );

	$charset			=	'@charset "'.$this->my_charset.'";';

	// CSS通常版
	$info_text			=	'/* '.$this->plugin_name.' ver.'.$this->plugin_version.' CSS #'.$this->now.' */';
	$out_text			=	$charset.PHP_EOL.$info_text.PHP_EOL.PHP_EOL.$file_text;
	$result				=	$wp_filesystem->put_contents($this->dir_style.$filename.'.css', $out_text, FS_CHMOD_FILE );
	if	(!$result ) {
		return	2;
	}

	// CSS通常版（圧縮）
	$info_text			=	'/*'.$this->plugin_code.$this->plugin_version.'#'.$this->now.'*/';
	$out_text			=	$charset.$this->pz_CompressCSS($file_text ).$info_text;
	$result				=	$wp_filesystem->put_contents($this->dir_style.$filename.'.min.css', $out_text, FS_CHMOD_FILE );
	if	(!$result ) {
		return	2;
	}

	// 時間計測
	if	($this->options['debug-mode'] ) {
		$end_time		=	hrtime(true );
		$elasped_time	=	$end_time - $start_time;
		$format_time	=	number_format($elasped_time / 1000 / 1000 / 1000, 8, '.', ',' );
	}

	return	1;

<?php
	
if (!defined('ABSPATH' ) ) {
	 exit;
}
?>
<?php
	$wp_filesystem = $this->pz_GetFilesystem();
	$render_path_input = function($value, $size = 120) {
		echo	'<p><input name="" type="text" title="'.esc_attr($value ).'" class="pz-click-all-select" value="'.esc_attr($value ).'" size="'.esc_attr($size ).'" readonly="readonly"></p>';
	};
	$render_help_icon = function($suffix) use ($help_icon) {
		return	wp_kses_post(sprintf($help_icon, esc_attr($suffix ) ) );
	};
	$format_size = function($size) {
		return	pz_GetSizeStringSi($size).' ('.pz_GetStringBytes($size ).')';
	};
	$get_dir_file_count = function($dir) use (&$get_dir_file_count, $wp_filesystem) {
		if	(!$wp_filesystem || !$dir || !$wp_filesystem->is_dir($dir ) ) {
			return	null;
		}
		$count	=	0;
		$files	=	$wp_filesystem->dirlist(trailingslashit($dir ) );
		if	(!is_array($files ) ) {
			return	null;
		}
		foreach	($files as $file => $info ) {
			$path	=	trailingslashit($dir ).$file;
			if	(($info['type'] ?? '') === 'd' ) {
				$count	+=	intval($get_dir_file_count($path ) );
			} else {
				$count++;
			}
		}
		return	$count;
	};
	$render_storage_usage = function($size, $action, $button_label, $empty_note, $file_count = null, $show_button = true) use ($format_size) {
		echo	'<p>'.esc_html__('Used', 'pz-linkcard3' ).esc_html__(': ', 'pz-linkcard3' );
		if	($size ) {
			echo	'<span class="pz-monospace">'.esc_html($format_size($size ) ).'</span>';
			if	($file_count !== null ) {
				echo	' / '.esc_html(__('Files:', 'pz-linkcard3' ) ).' <span class="pz-monospace">'.esc_html(number_format_i18n($file_count ) ).'</span>';
			}
			echo	'</p>';
			if	($show_button ) {
				echo	'<p><button type="submit" name="action" value="'.esc_attr($action ).'" class="pz-button" onclick="return confirm(\''.esc_js(esc_html__('Are you sure?', 'pz-linkcard3' ) ).'\');">'.esc_html($button_label ).'</button></p>';
			}
		} else {
			echo	'<span class="pz-monospace">- (- bytes)</span>';
			if	($file_count !== null ) {
				echo	' / '.esc_html(__('Files:', 'pz-linkcard3' ) ).' <span class="pz-monospace">'.esc_html(number_format_i18n($file_count ) ).'</span>';
			}
			echo	'</p>';
			if	($show_button ) {
				echo	'<p><button type="button" class="pz-button" style="cursor: not-allowed;">'.esc_html($button_label ).'</button>&emsp;'.esc_html($empty_note ).'</p>';
			}
		}
	};
	$get_db_table_stats = function($table_name) use ($wpdb) {
		if	(!$table_name ) {
			return	null;
		}
		$stats	=	$wpdb->get_row($wpdb->prepare(
			"SELECT COALESCE(DATA_LENGTH, 0) AS data_length, COALESCE(INDEX_LENGTH, 0) AS index_length, COALESCE(DATA_FREE, 0) AS data_free FROM information_schema.TABLES WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s LIMIT 1",
			DB_NAME,
			$table_name
		), ARRAY_A );
		if	(!$stats ) {
			return	null;
		}
		$row_count			=	$wpdb->get_var($wpdb->prepare('SELECT COUNT(*) FROM %i', $table_name ) );
		$data_length		=	intval($stats['data_length'] ?? 0 );
		$index_length		=	intval($stats['index_length'] ?? 0 );
		$data_free			=	intval($stats['data_free'] ?? 0 );
		return	array(
			'rows'			=>	intval($row_count ),
			'total_size'	=>	$data_length + $index_length,
			'data_length'	=>	$data_length,
			'index_length'	=>	$index_length,
			'data_free'		=>	$data_free,
		);
	};
	$render_db_table_stats_row = function($label, $table_name) use ($get_db_table_stats, $format_size) {
		$stats		=	$get_db_table_stats($table_name );
		$empty_size	=	'<span class="pz-monospace">- (- bytes)</span>';
		echo	'<tr>';
		echo	'<th scope="row" style="width: 260px;">'.esc_html($label ).'</th>';
		echo	'<td class="pz-additional-only"><input type="text" class="pz-click-all-select pz-monospace" value="'.esc_attr($table_name ).'" readonly="readonly"></td>';
		if	($stats !== null ) {
			echo	'<td class="pz-monospace">'.esc_html(number_format_i18n($stats['rows'] ) ).'</td>';
			echo	'<td class="pz-monospace">'.esc_html($format_size($stats['total_size'] ) ).'</td>';
			echo	'<td class="pz-monospace">'.esc_html($format_size($stats['data_length'] ) ).'</td>';
			echo	'<td class="pz-monospace">'.esc_html($format_size($stats['index_length'] ) ).'</td>';
		} else {
			echo	'<td class="pz-monospace">-</td>';
			echo	'<td>'.wp_kses_post($empty_size ).'</td>';
			echo	'<td>'.wp_kses_post($empty_size ).'</td>';
			echo	'<td>'.wp_kses_post($empty_size ).'</td>';
		}
		echo	'</tr>';
	};
	/* translators: %s: ドメイン名の置換例 */
	$siteicon_domain_example		=	esc_html(sprintf(__('(ex. %s)', 'pz-linkcard3' ), $pz_domain ) );
	/* translators: %s: ドメインURLの置換例 */
	$siteicon_domain_url_example	=	esc_html(sprintf(__('(ex. %s)', 'pz-linkcard3' ), $pz_domain_url ) );
	/* translators: %s: URLの置換例 */
	$siteicon_url_example		=	esc_html(sprintf(__('(ex. %s)', 'pz-linkcard3' ), $pz_url.$this->base_path ) );
	/* translators: %s: URLの置換例 */
	$thumbnail_url_example		=	esc_html(sprintf(__('(ex. %s)', 'pz-linkcard3' ), $pz_url.$this->base_path ) );
?>
<div class="<?php echo esc_attr($page_class('pz-etc' ) ); ?>" id="pz-etc">
	<div class="pz-submit-float"><?php submit_button(); ?></div>

	<h2><?php echo	wp_kses_post(esc_html__('Web API Settings', 'pz-linkcard3' ).$render_help_icon('-web-api' ) ); ?></h2>
	<table class="form-table">
		<tr>
			<th scope="row"><?php esc_html_e('Site Icon API', 'pz-linkcard3' ); ?></th>
			<td>
				<input name="properties[siteicon-api]" type="url" size="120" class="pz-click-all-select" value="<?php echo esc_attr($prop['siteicon-api'] ); ?>" />
				<p><?php echo	wp_kses_post(esc_html__('%DOMAIN% is replaced with the domain name.', 'pz-linkcard3' ).' '.$siteicon_domain_example.'<br>'.esc_html__('%DOMAIN_URL% is replaced with the domain URL.', 'pz-linkcard3' ).' '.$siteicon_domain_url_example.'<br>'.esc_html__('%URL% is replaced with the URL.', 'pz-linkcard3' ).' '.$siteicon_url_example ); ?>
				<p><?php esc_html_e('ex1.', 'pz-linkcard3' ); ?><input name="" type="text" size="70" class="pz-click-all-select" value="https://www.google.com/s2/favicons?domain=%DOMAIN%" readonly="readonly"></p>
				<p><?php esc_html_e('ex2.', 'pz-linkcard3' ); ?><input name="" type="text" size="70" class="pz-click-all-select" value="https://t0.gstatic.com/faviconV2?client=SOCIAL&url=%URL%" readonly="readonly"></p>
			</td>
		</tr>
		<tr>
			<th scope="row" rowspan="3"><?php esc_html_e('Thumbnail API', 'pz-linkcard3' ); ?></th>
			<td>
				<input name="properties[thumbnail-api]" type="url" size="120" class="pz-click-all-select" value="<?php echo	esc_attr($prop['thumbnail-api'] ); ?>" />
				<p><?php echo	wp_kses_post(esc_html__('%URL% is replaced with the URL.', 'pz-linkcard3' ).' '.$thumbnail_url_example ); ?></p>
				<p><?php esc_html_e('ex1.', 'pz-linkcard3' ); ?><input name="" type="text" size="70" class="pz-click-all-select" value="https://s.wordpress.com/mshots/v1/%URL%?w=200" readonly="readonly"></p>
				<p><?php esc_html_e('ex2.', 'pz-linkcard3' ); ?><input name="" type="text" size="70" class="pz-click-all-select" value="https://capture.heartrails.com/200x200?%URL%" readonly="readonly"></p>
			</td>
		</tr>
	</table>
	<?php submit_button(); ?>

	<div class="pz-debug-only">
		<h2><?php echo	wp_kses_post(esc_html__('Stylesheet Settings', 'pz-linkcard3' ).$render_help_icon('-css' ) ); ?></h2>
		<table class="form-table">
			<tr>
				<th scope="row"><?php echo esc_html__('CSS File URL', 'pz-linkcard3' ); ?></th>
				<td><input name=""	type="text" size="120" title="<?php echo esc_attr($this->dir_style.'style.css'     ); ?>" class="pz-click-all-select" value="<?php echo	esc_attr($this->dir_style.'style.css'     ); ?>" readonly="readonly" /></td>
			</tr>
			<tr>
				<th scope="row"><?php echo esc_html__('CSS File URL', 'pz-linkcard3' ).' '.esc_html__('(Compressed)', 'pz-linkcard3' ); ?></th>
				<td><input name="" 	type="text" size="120" title="<?php echo esc_attr($this->dir_style.'style.min.css' ); ?>" class="pz-click-all-select" value="<?php echo	esc_attr($this->dir_style.'style.min.css' ); ?>" readonly="readonly" /></td>
			</tr>
			<tr class="pz-additional-only">
				<th scope="row"><?php esc_html_e('Stylesheet Template File', 'pz-linkcard3' ); ?></th>
				<td><input name=""	type="text" size="120" title="<?php echo esc_attr($this->file_template ); ?>" class="pz-click-all-select" value="<?php echo esc_attr($this->file_template ); ?>" readonly="readonly" /></td>
			</tr>
		</table>
		<?php submit_button(); ?>
	</div>

	<h2><?php echo	wp_kses_post(esc_html__('Image Cache Information', 'pz-linkcard3' ).$render_help_icon('-image' ) ); ?></h2>
	<?php 
		if	($wp_filesystem && $wp_filesystem->exists($this->dir_cache ) ) {
			$dir	=	$this->dir_cache;
			$url	=	$this->url_cache;
			$size	=	pz_GetDirSize($this->dir_cache );
			$count	=	$get_dir_file_count($this->dir_cache );
		} else {
			$dir	=	null;
			$url	=	null;
			$size	=	0;
			$count	=	null;
		}
	?>
	<table class="form-table">
		<tr>
			<th scope="row"><?php esc_html_e('Image Cache URL', 'pz-linkcard3' ); ?></th>
			<td>
				<?php
					$render_path_input($url );
					echo	'<p>'.esc_html__('Schemes (http and https) are omitted.', 'pz-linkcard3' ).'</p>';
					$render_storage_usage($size, 'clear-image', __('Clear Image Cache', 'pz-linkcard3' ), __('* The directory is created when the image cache is output.', 'pz-linkcard3' ), $count, false );
				?>
			</td>
		</tr>
		<tr class="pz-additional-only">
			<th scope="row"><?php esc_html_e('Image Cache Directory', 'pz-linkcard3' ); ?></th>
			<td>
				<?php $render_path_input($this->dir_cache ); ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e('Image Size', 'pz-linkcard3' ); ?></th>
			<td>
				<select name="properties[image-size]">
					<?php
						foreach	(LIST_IMAGE_SIZE as $value => $description ) {
							echo	'<option value="'.esc_attr($value ).'"'.selected($this->options['image-size'], $value, false ).'>'.esc_html($description ).'</option>';
						}
					?>
				</select>
			</td>
		</tr>
	</table>

	<div class="pz-additional-only">
		<h2><?php echo	esc_html__('Log Settings', 'pz-linkcard3' ); ?></h2>
		<?php 
			$dir	=	$this->dir_debug;
			$url	=	$this->url_debug;
			if	($wp_filesystem && $wp_filesystem->exists($this->dir_debug ) ) {
				$size	=	pz_GetDirSize($this->dir_debug );
				$count	=	$get_dir_file_count($this->dir_debug );
			} else {
				$dir	=	__('Not Found.', 'pz-linkcard3' );
				$url	=	__('Not Found.', 'pz-linkcard3' );
				$size	=	null;
				$count	=	null;
			}
		?>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e('Log URL', 'pz-linkcard3' ); ?></th>
				<td>
					<?php
						$render_path_input($url );
						echo	'<p>'.esc_html__('Schemes (http and https) are omitted.', 'pz-linkcard3' ).'</p>';
						$render_storage_usage($size, 'clear-log', __('Clear Log Files', 'pz-linkcard3' ), __('* The directory is created when log files are output.', 'pz-linkcard3' ), $count );
					?>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e('Log Directory', 'pz-linkcard3' ); ?></th>
				<td>
					<?php $render_path_input($dir ); ?>
				</td>
			</tr>
		</table>
	</div>

	<h2><?php echo	wp_kses_post(esc_html__('Database Information', 'pz-linkcard3' ).$render_help_icon('-image' ) ); ?></h2>
	<table class="widefat striped" style="width: 100%;">
		<thead>
			<tr>
				<th scope="col" style="width: 260px;"><?php esc_html_e('Type', 'pz-linkcard3' ); ?></th>
				<th scope="col" class="pz-additional-only"><?php esc_html_e('Table Name', 'pz-linkcard3' ); ?></th>
				<th scope="col"><?php esc_html_e('Rows', 'pz-linkcard3' ); ?></th>
				<th scope="col"><?php esc_html_e('Total Size', 'pz-linkcard3' ); ?></th>
				<th scope="col"><?php esc_html_e('Data Size', 'pz-linkcard3' ); ?></th>
				<th scope="col"><?php esc_html_e('Index Size', 'pz-linkcard3' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
				$render_db_table_stats_row(__('DB Cache', 'pz-linkcard3' ), $this->db_card );
				$render_db_table_stats_row(__('Click Log', 'pz-linkcard3' ), $this->db_click );
			?>
		</tbody>
	</table>

	<div class="pz-additional-only">
		<h2><?php echo	wp_kses_post(esc_html__('DB Cache', 'pz-linkcard3' ).$render_help_icon('-initialize' ) ); ?></h2>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e('Initialize Cache', 'pz-linkcard3' ); ?></th>
				<td>
					<p style="margin-bottom: 8px; padding: 8px; background-color: #f84; color: #fff;">
						<label>
							<input type="checkbox" class="pz-db-clear-enabled">
							<?php echo esc_html__('I understand and consent to the deletion of all cache data from the link card.', 'pz-linkcard3' ); ?>
						</label>
					</p>
					<p>
						<button type="submit" name="action" value="init-cache" class="pz-button-sure pz-db-clear" disabled="disabled" onclick="return confirm('<?php echo esc_js(esc_html__('Are you sure?', 'pz-linkcard3' ) ); ?>');"><?php esc_html_e('Run Now', 'pz-linkcard3' ); ?></button>
						&ensp;<span><?php echo esc_html__('Clear all cache entries.', 'pz-linkcard3' ); ?></span>
					</p>
				</td>
			</tr>
		</table>
	</div>

</div>

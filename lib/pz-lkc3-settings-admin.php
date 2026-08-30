<?php
	
if (!defined( 'ABSPATH' ) ) { header( 'HTTP/1.1 403 Forbidden' ); exit; } ?>
<?php
	switch	($action) {
	case	'run-'.$this->cron_regist:
		wp_clear_scheduled_hook($this->cron_regist );
		$cron_log		=	'* execute: '.$this->cron_regist.PHP_EOL.PHP_EOL;
		$cron_log		.=	$this->hook_regist();
		break;
	case	'run-'.$this->cron_alive:
		if	(empty($this->options['alive-period'] ) ) {
			wp_clear_scheduled_hook($this->cron_alive );
		}
		$cron_log		=	'* execute: '.$this->cron_alive.PHP_EOL.PHP_EOL;
		$cron_log		.=	$this->hook_check_alive();
		break;
	case	'run-'.$this->cron_sns:
		if	(empty($this->options['sns-period'] ) ) {
			wp_clear_scheduled_hook($this->cron_sns );
		}
		$cron_log		=	'* execute: '.$this->cron_sns.PHP_EOL.PHP_EOL;
		$cron_log		.=	$this->hook_check_sns();
		break;
	}

	// WP-Cron の実行結果
	if (isset($cron_log ) ) {
		$cron_log			=	esc_attr(esc_html($cron_log ) );
		$cron_log			=	str_replace(PHP_EOL, '<br>', $cron_log );
	}

	$format_db_size = function($size) {
		return	pz_GetSizeStringSi($size).' ('.pz_GetStringBytes($size ).')';
	};
	$get_db_table_usage = function($table_name) use ($wpdb) {
		if	(!$table_name ) {
			return	null;
		}
		$stats	=	$wpdb->get_row($wpdb->prepare(
			"SELECT COALESCE(DATA_LENGTH, 0) AS data_length, COALESCE(INDEX_LENGTH, 0) AS index_length FROM information_schema.TABLES WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s LIMIT 1",
			DB_NAME,
			$table_name
		), ARRAY_A );
		if	(!$stats ) {
			return	null;
		}
		$row_count			=	$wpdb->get_var($wpdb->prepare('SELECT COUNT(*) FROM %i', $table_name ) );
		$data_length		=	intval($stats['data_length'] ?? 0 );
		$index_length		=	intval($stats['index_length'] ?? 0 );
		return	array(
			'rows'			=>	intval($row_count ),
			'total_size'	=>	$data_length + $index_length,
			'data_length'	=>	$data_length,
			'index_length'	=>	$index_length,
		);
	};
	$render_db_table_usage = function($table_name) use ($get_db_table_usage, $format_db_size) {
		$usage	=	$get_db_table_usage($table_name );
		if	($usage === null ) {
			echo	'<span class="pz-monospace">-</span>';
			return;
		}
		echo	'<span>'.esc_html(__('Rows', 'pz-linkcard3' ) ).': <span class="pz-monospace">'.esc_html(number_format_i18n($usage['rows'] ) ).'</span></span>';
		echo	' / <span>'.esc_html(__('Total Usage', 'pz-linkcard3' ) ).': <span class="pz-monospace">'.esc_html($format_db_size($usage['total_size'] ) ).'</span></span>';
		echo	' / <span>'.esc_html(__('Data Usage', 'pz-linkcard3' ) ).': <span class="pz-monospace">'.esc_html($format_db_size($usage['data_length'] ) ).'</span></span>';
		echo	' / <span>'.esc_html(__('Index Usage', 'pz-linkcard3' ) ).': <span class="pz-monospace">'.esc_html($format_db_size($usage['index_length'] ) ).'</span></span>';
	};

	// WP-Cronスケジュールを取得
	$cron_schedule	=	_get_cron_array();
	if	(!is_array($cron_schedule ) ) {
		$cron_schedule	=	array();
	}
	$schedules		=	wp_get_schedules();		// タイミングの定数（604800→週1回 など）
	$cron_list		=	array();
	foreach			($cron_schedule	as $timestamp	=> $cronhooks ) {
		foreach		($cronhooks		as $hook		=> $dings ) {
			foreach	($dings			as $signature	=> $data ) {
				if	(substr($hook, 0, 13 ) === 'pz_linkcard3_' ) {
					$myjob		=	true;
				} else {
					$myjob		=	false;
				}
				$schedule		=	isset($schedules[$data['schedule']]['display'] ) ? $schedules[$data['schedule']]['display'] : $data['schedule'] ;
				$interval		=	isset($data['interval'] ) ? $data['interval'].' '.__('Sec.', 'pz-linkcard3' ) : null ;
				$cron_list[]	=	array(
					'key'			=>	$hook,	// ソートキー
					'hook'			=>	$hook,
					'myjob'			=>	$myjob,
					'next_time'		=>	esc_html(get_date_from_gmt( gmdate( 'Y-m-d H:i:s', $timestamp ), $this->format_datetime ) ),
					'schedule'		=>	$schedule,
					'interval'		=>	$interval,
					'display_class'	=>	$myjob ? 'pz-cron-list-lkc' : 'pz-cron-list-other',
					);
			}
		}
	}
	asort($cron_list );

?>
<div class="<?php echo esc_attr($page_class('pz-admin', 'pz-page-admin' ) ); ?>" id="pz-admin">
	<div class="pz-admin-notice"><?php esc_html_e('Do not use this normally, as it may disable some features.', 'pz-linkcard3' ); ?></div>
	<div class="pz-submit-float"><?php submit_button(); ?></div>

	<h2><?php esc_html_e('Environment Information', 'pz-linkcard3' ); ?></h2>
	<table class="form-table">
		<tr>
			<th scope="row"><?php esc_html_e('WordPress Version', 'pz-linkcard3' ); ?></th>
			<td><?php pz_lkc3_readonly_text(get_bloginfo('version' ) ); ?></td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e('PHP Version', 'pz-linkcard3' ); ?></th>
			<td><?php pz_lkc3_readonly_text(phpversion() ); ?></td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e('cURL Version', 'pz-linkcard3' ); ?></th>
			<td><?php pz_lkc3_readonly_text(curl_version()['version'] ); ?></td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e('DBMS Version', 'pz-linkcard3' ); ?></th>
			<td><?php pz_lkc3_readonly_text($wpdb->db_version() ); ?></td>
		</tr>
	</table>
	<?php submit_button(); ?>

	<h2><?php esc_html_e('Plugin Information', 'pz-linkcard3' ); ?></h2>
	<table class="form-table pz-admin-info-table">
		<tr>
			<th scope="row" rowspan="2" style="width: 100px;"><?php esc_html_e('Plugin', 'pz-linkcard3' ); ?></th>
			<th scope="row" style="width: 100px;"><?php esc_html_e('Name', 'pz-linkcard3' ); ?></th>
			<td><?php pz_lkc3_readonly_text($this->plugin_name, 40 ); ?></td>
		</tr>
		<tr>
			<th scope="row" style="width: 100px;"><?php esc_html_e('Version', 'pz-linkcard3' ); ?></th>
			<td>
				<input type="text" name="properties[plugin-version]" value="<?php echo esc_attr($prop['plugin-version'] ); ?>" readonly="readonly" size="40" class="pz-monospace" />
			</td>
		</tr>
		<tr>
			<th scope="row" style="width: 100px;" colspan="2"><?php esc_html_e('Option Name', 'pz-linkcard3' ); ?></th>
			<td>
				<?php pz_lkc3_readonly_text($this->option_name, 40 ); ?>
			</td>
		</tr>
		<tr>
			<th scope="row" rowspan="3" style="width: 100px;"><?php esc_html_e('Link-Card', 'pz-linkcard3' ); ?></th>
			<th scope="row" style="width: 100px;"><?php esc_html_e('Table Name', 'pz-linkcard3' ); ?></th>
			<td>
				<?php pz_lkc3_readonly_text($this->db_card, 40 ); ?>
				<?php pz_lkc3_table_exists_badge($this->db_card ); ?>
			</td>
		</tr>
		<tr>
			<th scope="row" style="width: 100px;"><?php esc_html_e('Usage', 'pz-linkcard3' ); ?></th>
			<td><?php $render_db_table_usage($this->db_card ); ?></td>
		</tr>
		<tr>
			<th scope="row" style="width: 100px;"><?php esc_html_e('Version', 'pz-linkcard3' ); ?></th>
			<td>
				<input type="text" name="properties[db-ver-card]" value="<?php echo esc_attr($prop['db-ver-card'] ); ?>" readonly="readonly" size="40" class="pz-monospace" />
			</td>
		</tr>
		<tr>
			<th scope="row" rowspan="3" style="width: 100px;"><?php esc_html_e('Click-Log', 'pz-linkcard3' ); ?></th>
			<th scope="row" style="width: 100px;"><?php esc_html_e('Table Name', 'pz-linkcard3' ); ?></th>
			<td>
				<?php pz_lkc3_readonly_text($this->db_click, 40 ); ?>
				<?php pz_lkc3_table_exists_badge($this->db_click ); ?>
			</td>
		</tr>
		<tr>
			<th scope="row" style="width: 100px;"><?php esc_html_e('Usage', 'pz-linkcard3' ); ?></th>
			<td><?php $render_db_table_usage($this->db_click ); ?></td>
		</tr>
		<tr>
			<th scope="row" style="width: 100px;"><?php esc_html_e('Version', 'pz-linkcard3' ); ?></th>
			<td>
				<input type="text" name="properties[db-ver-click]" value="<?php echo esc_attr($prop['db-ver-click'] ); ?>" readonly="readonly" size="40" class="pz-monospace" />
			</td>
		</tr>
		<tr>
			<th scope="row" colspan="2" style="width: 200px;"><?php esc_html_e('Last Saved Settings', 'pz-linkcard3' ); ?></th>
			<td>
				<input name="properties[saved-date]" type="text" value="<?php echo esc_attr($this->options['saved-date'] ); ?>" readonly="readonly" class="pz-monospace" />
				<?php echo is_numeric($this->options['saved-date'] ) ? esc_html($this->pz_Date($this->format_datetime, $this->options['saved-date'] ) ) : esc_html($this->options['saved-date'] ); ?>
			</td>
		</tr>
	</table>
	<?php submit_button(); ?>

	<h2><?php esc_html_e('for Debug', 'pz-linkcard3' ); ?></h2>
	<table class="form-table">
		<tr>
			<th scope="row"><?php esc_html_e('Reboot This Plugin', 'pz-linkcard3' ); ?></th>
			<td>
				<button type="submit" name="action" value="init-plugin" class="pz-button-sure" onclick="return confirm('<?php echo esc_js(esc_html__('Are you sure?', 'pz-linkcard3' ) ); ?>');"><?php esc_html_e('Run', 'pz-linkcard3' ); ?></button>
				&ensp;<span><?php echo	esc_html__('Perform initial setup.', 'pz-linkcard3' ).'&nbsp;'.esc_html__('"Settings" will not be initialized.', 'pz-linkcard3' ); ?></span>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e('DB Update Mode', 'pz-linkcard3' ); ?></span></th>
			<td>
				<label>
					<input type="hidden"   name="properties[debug-nocache]" value="">
					<input type="checkbox" name="properties[debug-nocache]" value="1" <?php checked($prop['debug-nocache'] ); ?> class="pz-tab-show" />
					<?php esc_html_e('Forced access to links even if they are recorded in DB.', 'pz-linkcard3' ); ?>
				</label>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php echo wp_kses_post(__('Style Confirmation<br>(Setting Page)', 'pz-linkcard3' ) ); ?></span></th>
			<td>
				<label>
					<input type="hidden"   name="properties[debug-style-admin]" value="">
					<input type="checkbox" name="properties[debug-style-admin]" value="1" <?php checked($prop['debug-style-admin'] ); ?> />
					<?php esc_html_e('* Set a border around the element on the settings screen to confirm the style.', 'pz-linkcard3' ); ?>
				</label>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php echo wp_kses_post(__('Style Confirmation<br>(Link Card)', 'pz-linkcard3' ) ); ?></span></th>
			<td>
				<label>
					<input type="hidden"   name="properties[debug-style-card]" value="">
					<input type="checkbox" name="properties[debug-style-card]" value="1" <?php checked($prop['debug-style-card'] ); ?> />
					<?php esc_html_e('* Set a border around the link card element to check its style.', 'pz-linkcard3' ); ?>
				</label>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e('Social Count', 'pz-linkcard3' ); ?></span></th>
			<td>
				<label>
					<input type="hidden"   name="properties[debug-sns]" value="">
					<input type="checkbox" name="properties[debug-sns]" value="1" <?php checked($prop['debug-sns'] ); ?> />
					<?php esc_html_e('* Randomly display social counts.', 'pz-linkcard3' ); ?>
				</label>
			</td>
		</tr>
	</table>
	<?php submit_button(); ?>

	<h2><?php esc_html_e('Error Settings', 'pz-linkcard3' ); ?></h2>
	<table class="form-table">
		<tr>
			<th scope="row"><?php esc_html_e('Error Conditions', 'pz-linkcard3' ); ?></th>
			<td>
				<label>
					<input type="hidden"   name="properties[error-mode]" value="">
					<input type="checkbox" name="properties[error-mode]" value="1" <?php checked($prop['error-mode'] ); ?> />
					<?php esc_html_e('Check to enable error conditions.', 'pz-linkcard3' ); ?>
				</label>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e('Post ID', 'pz-linkcard3' ); ?></th>
			<td><input name="properties[error-postid]" type="text" size="5" value="<?php echo esc_attr($prop['error-postid'] ); ?>" /></td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e('Post URL', 'pz-linkcard3' ); ?></th>
			<td><input name="properties[error-url]" type="url" size="80" value="<?php echo esc_attr($prop['error-url'] ); ?>" /></td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e('Occurrence Time', 'pz-linkcard3' ); ?></th>
			<td>
				<input type="text" size="40" value="<?php echo is_numeric($prop['error-time'] ) ? esc_attr($this->pz_Date($this->format_datetime, $prop['error-time'] ) ) : esc_attr($prop['error-time'] ); ?>" readonly="readonly" />
				<input name="properties[error-time]" type="text" value="<?php echo esc_attr($prop['error-time'] ); ?>" class="pz-admin-only" />
			</td>
		</tr>
	</table>
	<?php submit_button(); ?>

	<?php if (isset($cron_log ) ) { ?>
		<h2><?php esc_html_e('Execution Result', 'pz-linkcard3' ); ?></h2>
		<div>
			<?php esc_html_e('Execution Result', 'pz-linkcard3' ); ?>
		</div>
		<div class="pz-cron-log pz-monospace">
			<?php echo wp_kses_post($cron_log ); ?>
		</div>
	<?php } ?>

	<h2><?php esc_html_e('WP-Cron Information', 'pz-linkcard3' ); ?></h2>
	<div class="pz-cron-margin">
		<label>
			<input type="checkbox" value="1" class="pz-cron-all">
			<?php esc_html_e('View all schedules.', 'pz-linkcard3' ); ?>
		</label>
	</div>
	<table class="pz-cron-list widefat striped">
		<thead>
			<tr style="display: table-row; height: 80px; text-align: center;">
				<th scope="col" class="pz-cron-head-run"><?php esc_html_e('Run', 'pz-linkcard3' ); ?></th>
				<th scope="col" class="pz-cron-head-hook"><?php esc_html_e('Hook', 'pz-linkcard3' ); ?><img src="<?php echo esc_url($this->base_url.'assets/sort_asc.svg' ); ?>" alt="" width="16" height="16" /></th>
				<th scope="col" class="pz-cron-head-next-time"><?php esc_html_e('Next Time', 'pz-linkcard3' ); ?></th>
				<th scope="col" class="pz-cron-head-schedule"><?php esc_html_e('Schedule', 'pz-linkcard3' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($cron_list as $key => $cron ) { ?>
				<tr class="<?php echo esc_attr($cron['display_class'] ); ?>">
					<td class="pz-cron-body-run">
						<?php if ($cron['myjob'] ) : ?>
							<button type="submit" name="action" class="pz-button-sure" value="<?php echo esc_attr('run-'.$cron['hook'] ); ?>" onclick="return confirm('<?php echo esc_js(esc_html__('Are you sure?', 'pz-linkcard3' ) ); ?>' );"><?php esc_html_e('Run Now', 'pz-linkcard3' ); ?></button>
						<?php else : ?>
							<button type="submit" name="action" class="pz-button-disabled" disabled="disabled"><?php esc_html_e('Run Now', 'pz-linkcard3' ); ?></button>
						<?php endif; ?>
					</td>
					<td class="pz-cron-body-hook"><?php echo esc_html($cron['hook'] ); ?></td>
					<td class="pz-cron-body-next-time"><?php echo esc_html($cron['next_time'] ); ?></td>
					<td class="pz-cron-body-schedule"><?php echo esc_html($cron['schedule'] ); ?></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
	<?php submit_button(); ?>
</div>

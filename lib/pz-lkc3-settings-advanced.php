<?php if (!defined( 'ABSPATH' ) ) { header( 'HTTP/1.1 403 Forbidden' ); exit; } ?>

<div class="<?php echo esc_attr($page_class('pz-advanced' ) ); ?>" id="pz-advanced">
	<div class="pz-submit-float"><?php submit_button(); ?></div>

	<h2><?php echo esc_html__('Advanced Settings', 'pz-linkcard3' ).wp_kses_post(sprintf($help_icon, esc_attr('-advanced' ) ) ); ?></h2>
	<table class="form-table">
		<tr>
			<th colspan="2">
				<?php esc_html_e('Trailing Slash', 'pz-linkcard3' ); ?>
			</th>
			<td>
				<?php
					echo_list('trail-slash', $prop['trail-slash'] ?? '',
						array(
				''			=>		__('Keep as is',					'pz-linkcard3' ),
				'1'			=> 		__('Remove only for domain-only URLs',	'pz-linkcard3' ),
				'2'			=>		__('Always remove',					'pz-linkcard3' ),
							), '', '', '', true, true );
				?>
			</td>
		</tr>
		<tr>
			<th colspan="2"><?php esc_html_e('Filter Priority', 'pz-linkcard3' ); ?></th>
			<td>
				<label>
					<input name="properties[mce-priority]" type="number" min="0" max="9999" size="80" value="<?php echo esc_attr($this->options['mce-priority'] ); ?>" /><?php esc_html_e('(Null or 0-9999)',  'pz-linkcard3' ); ?>
					<?php esc_html_e('Try a larger value if the insert button does not appear in the editor.', 'pz-linkcard3' ); ?>
				</label>
			</td>
		</tr>
		<tr>
			<th rowspan="3"><?php esc_html_e('Stylesheet', 'pz-linkcard3' ); ?></th>
			<th scope="row"><?php esc_html_e('Importance', 'pz-linkcard3' ); ?></th>
			<td>
				<?php pz_lkc3_property_checkbox($prop, 'flg-important', __('Apply “!important” to style sheets.', 'pz-linkcard3' ).__('(Recommended)', 'pz-linkcard3' ) ); ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e('Compress', 'pz-linkcard3' ); ?></th>
			<td>
				<?php pz_lkc3_property_checkbox($prop, 'flg-compress', __('Compress CSS and JavaScript to improve access speed.', 'pz-linkcard3' ) ); ?>
			</td>
		</tr>
		<tr>
			<th><?php esc_html_e('Text Selection', 'pz-linkcard3' ); ?></th>
			<td>
				<?php pz_lkc3_property_checkbox($prop, 'flg-unti-select', __('Prohibit the selection of text in the Link-Card.', 'pz-linkcard3' ) ); ?>
			</td>
		</tr>
		<tr>
			<th scope="row" colspan="2"><?php esc_html_e('Click Count', 'pz-linkcard3' ); ?></th>
			<td>
				<?php pz_lkc3_property_checkbox($prop, 'flg-click-count', __('Record the number of times the link card is clicked.', 'pz-linkcard3' ) ); ?>
			</td>
		</tr>
		<tr>
			<th colspan="2"><?php esc_html_e('Quick Menu', 'pz-linkcard3' ); ?></th>
			<td>
				<?php pz_lkc3_property_checkbox($prop, 'flg-quickmenu', __('This will enable the Quick Menu.', 'pz-linkcard3' ) ); ?>
			</td>
		</tr>
		<tr>
			<th colspan="2"><?php esc_html_e('Input Lock Time', 'pz-linkcard3' ); ?></th>
			<td>
				<?php pz_lkc3_property_checkbox($prop, 'flg-inhibit', __('After pressing a button, the screen goes dark to prevent accidental input.', 'pz-linkcard3' ) ); ?>
			</td>
		</tr>
		<tr>
			<th colspan="2"><?php esc_html_e('Google AMP Detection', 'pz-linkcard3' ); ?></th>
			<td>
				<p>
					<?php pz_lkc3_property_checkbox($prop, 'flg-amp-url', __('Use the simplified display when the URL ends with "/amp", "/amp/", or "/?amp=1".', 'pz-linkcard3' ).__('(Deprecated)', 'pz-linkcard3' ) ); ?>
				</p>
			</td>
		</tr>
		<tr>
			<th colspan="2"><?php esc_html_e('Hide URL Error', 'pz-linkcard3' ); ?></th>
			<td>
				<?php pz_lkc3_property_checkbox($prop, 'error-mode-hide', __('Do not display errors on the admin page.', 'pz-linkcard3' ).__('(Deprecated)', 'pz-linkcard3' ), 'pz-tab-show pz-sync' ); ?>
			</td>
		</tr>
	</table>
	<?php submit_button(); ?>

	<h2><?php echo	esc_html__('Not Recommended Settings', 'pz-linkcard3' ).wp_kses_post(sprintf($help_icon, esc_attr('-deprecation' ) ) ); ?></h2>
	<table class="form-table">
		<tr>
			<th rowspan="2"><?php esc_html_e('SNS Count', 'pz-linkcard3' );	?></th>
			<th><?php esc_html_e('Recurrence', 'pz-linkcard3' );	?></th>
			<td>
				<?php	echo_list('sns-period', $prop['sns-period'] ?? '', LIST_PERIOD, '', '', '', true, true ); ?>
			</td>
		</tr>
		<tr>
			<th><?php esc_html_e('Items per Run', 'pz-linkcard3' );	?></th>
			<td>
				<?php	echo_list('sns-period-num', $prop['sns-period-num'] ?? '', LIST_PERIOD_NUMBER, '', '', '', true, true ); ?>
			</td>
		</tr>
		<tr>
		<th rowspan="2"><?php esc_html_e('Link Availability Check', 'pz-linkcard3' );	?></th>
		<th><?php esc_html_e('Recurrence', 'pz-linkcard3' );	?></th>
			<td>
				<?php	echo_list('alive-period', $prop['alive-period'] ?? '', LIST_PERIOD, '', '', '', true, true ); ?>
			</td>
		</tr>
		<tr>
			<th><?php esc_html_e('Items per Run', 'pz-linkcard3' );	?></th>
			<td>
				<?php	echo_list('alive-period-num', $prop['alive-period-num'] ?? '', LIST_PERIOD_NUMBER, '', '', '', true, true ); ?>
			</td>
		</tr>
	</table>
	<?php submit_button(); ?>

	<h2><?php echo	esc_html__('Extension Settings', 'pz-linkcard3' ).wp_kses_post(sprintf($help_icon, esc_attr('-extension' ) ) ); ?></h2>
	<table class="form-table">
		<tr>
			<th colspan="2"><?php esc_html_e('Admin Bar', 'pz-linkcard3' ); ?></th>
			<td>
				<?php pz_lkc3_property_checkbox($prop, 'flg-adminbar', __('Add a menu to the admin bar. It will appear after the screen is refreshed.', 'pz-linkcard3' ) ); ?>
			</td>
		</tr>

		<tr>
			<th colspan="2" style="color: #06f !important; background-color: #ccccee !important;"><span ><?php esc_html_e('Debug Mode', 'pz-linkcard3' ); ?></span></th>
			<td	style="background-color: #f8f8ff !important;">
				<?php pz_lkc3_property_checkbox($prop, 'debug-mode', '<span style="color: #06f !important;">'.esc_html__('Enable debugging.', 'pz-linkcard3' ).__('(Deprecated)', 'pz-linkcard3' ).'</span>', 'pz-tab-show pz-sync' ); ?>
			</td>
		</tr>
		<tr class="pz-debug-only">
			<th colspan="2" style="color: #06f !important; background-color: #ccccee !important;"><span><?php esc_html_e('Additional Mode', 'pz-linkcard3' ); ?></span></th>
			<td	style="background-color: #f8f8ff !important;">
				<?php pz_lkc3_property_checkbox($prop, 'additional-mode', '<span style="color: #06f !important;">'.esc_html__('Show additional items.', 'pz-linkcard3' ).__('(Deprecated)', 'pz-linkcard3' ).'</span>', 'pz-tab-show pz-sync' ); ?>
			</td>
		</tr>
		<tr class="pz-debug-only">
			<th colspan="2" style="color: #06f !important; background-color: #ccccee !important;"><span ><?php esc_html_e('Log Mode', 'pz-linkcard3' ); ?></span></th>
			<td	style="background-color: #f8f8ff !important;">
				<?php pz_lkc3_property_checkbox(array('log-mode' => (!empty($this->options['debug-mode'] ) && !empty($this->options['log-mode'] ) ) ), 'log-mode', '<span style="color: #06f !important;">'.esc_html__('Collect logs. This may slow down operation.', 'pz-linkcard3' ).__('(Deprecated)', 'pz-linkcard3' ).'</span>', 'pz-tab-show pz-sync' ); ?>
			</td>
		</tr>
		<tr class="pz-debug-only">
			<th colspan="2" style="color: #f62 !important; background-color: #ddccbb !important;"><?php esc_html_e('MultiSite Mode', 'pz-linkcard3' ); ?></th>
			<td style="background-color: #fcf4f0 !important;">
				<?php pz_lkc3_property_checkbox($prop, 'multi-mode', '<span style="color: #f62 !important;">'.esc_html__('Display a menu for multisite.', 'pz-linkcard3' ).'</span>', 'pz-tab-show pz-sync', ($is_multisite ? ' readonly="readonly"' : '') ); ?>
			</td>
		</tr>
		<tr class="pz-additional-only">
			<th colspan="2" style="color: #c38 !important; background-color: #d8cce0 !important;"><?php esc_html_e('Administrator Mode', 'pz-linkcard3' ); ?></th>
			<td style="background-color: #fff8ff !important;">
				<?php pz_lkc3_property_checkbox($prop, 'admin-mode', '<span style="color: #c38 !important;">'.esc_html__('Display information that is not normally needed and enable special settings.', 'pz-linkcard3' ).__('(Deprecated)', 'pz-linkcard3' ).'</span>', 'pz-tab-show pz-sync', (!$this->options['admin-mode'] ? ' readonly="readonly" ondblclick="this.readOnly=false;"' : '') ); ?>
			</td>
		</tr>

		<tr class="pz-develop-only">
			<th colspan="2" style="color: #0a8 !important; background-color: #b0bbbb !important;"><?php esc_html_e('Development Mode', 'pz-linkcard3' ); ?></th>
			<td style="background-color: #fffff8 !important;">
				<?php pz_lkc3_property_checkbox(array('develop-mode' => $this->env_develop ), 'develop-mode', '<span style="color: #0a8 !important;">'.esc_html__('Currently working in a development environment.', 'pz-linkcard3' ).'</span>', 'pz-tab-show pz-sync', ' readonly="readonly"' ); ?>
			</td>
		</tr>
	</table>
	<?php submit_button(); ?>
</div>

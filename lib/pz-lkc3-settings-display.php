<?php
	
if (!defined('ABSPATH' ) ) {
	 exit;
}
?>
<div class="<?php echo esc_attr($page_class('pz-display' ) ); ?>" id="pz-display">
	<div class="pz-submit-float"><?php submit_button(); ?></div>
	<h2><?php echo	esc_html__('Display Settings', 'pz-linkcard3' ).wp_kses_post(sprintf($help_icon, esc_attr('-display' ) ) ); ?></h2>

	<table class="form-table" style="width: 100%;">
		<tr>
			<th rowspan="2">
				<?php esc_html_e('Display', 'pz-linkcard3' ); ?>
			</th>
			<th>
				<?php esc_html_e('Separator', 'pz-linkcard3' ); ?>
			</th>
			<td>
				<?php pz_lkc3_property_checkbox($prop, 'separator', __('Displays a line between site information and article content.', 'pz-linkcard3' ) ); ?>
			</td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e('Contents Frame', 'pz-linkcard3' ); ?>
			</th>
			<td>
				<?php pz_lkc3_property_checkbox($prop, 'content-inset', __('Frame the article information.', 'pz-linkcard3' ) ); ?>
			</td>
		</tr>
		<tr>
			<th colspan="2">
				<?php esc_html_e('Border color for broken links', 'pz-linkcard3' ); ?>
			</th>
			<td>
				<?php
					$item_name		=	'unlink-border-color';
					$item_notice	=	__('* If you do not wish to change the border color, press the “Clear” button to leave it blank.', 'pz-linkcard3' );
					echo	'<input name="properties['.esc_attr($item_name ).']" type="text" value="'.esc_attr($this->options[$item_name] ).'" class="pz-color pz-monospace pz-color-picker" />';
					echo esc_html($item_notice );
				?>
			</td>
		</tr>
		<tr>
			<th rowspan="3">
				<?php esc_html_e('Post Date & Modified Date', 'pz-linkcard3' ); ?>
			</th>
			<th>
				<?php esc_html_e('Contents', 'pz-linkcard3' ); ?>
			</th>
			<td>
				<?php	echo_list('post-date-style', $prop['post-date-style'] ?? '', LIST_DATE_STYLE, '', '', '', true, true ); ?>
			</td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e('Date Icon', 'pz-linkcard3' ); ?>
			</th>
			<td>
				<?php
					echo	esc_html__('Post Date:', 'pz-linkcard3' );
					echo_list('post-date-icon1', $prop['post-date-icon1'] ?? '', LIST_DATE_ICON, '', '', '', true, true );
					echo	'&emsp;'.esc_html__('Modified Date:', 'pz-linkcard3' );
					echo_list('post-date-icon2', $prop['post-date-icon2'] ?? '', LIST_DATE_ICON, '', '', '', true, true );
				?>
			</td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e('Format', 'pz-linkcard3' ); ?>
			</th>
			<td>
				<input name="properties[date-format]"			type="text" size="40" value="<?php echo	(isset($this->options['date-format'] ) ? esc_attr($this->options['date-format'] ) : '' ); ?>" list="date-format-default" />
			</td>
			<datalist id="date-format-default"><option value="Y.m.d">Y.m.d</option><option value="Y年m月d日">Y年n月j日</option></datalist>
		</tr>
		<tr>
			<th rowspan="3">
				<?php esc_html_e('Social Count', 'pz-linkcard3' ); ?>
			</th>
			<th><?php esc_html_e('Display', 'pz-linkcard3' ); ?></th>
			<td>
				<ul>
					<li>
						<?php pz_lkc3_property_checkbox($prop, 'sns-tw', __('X (Twitter)', 'pz-linkcard3' ).esc_html__('* number is not updated', 'pz-linkcard3' ) ); ?>
						<?php pz_lkc3_property_checkbox($prop, 'sns-tw-old', __('Change the unit of measure to "tweets".', 'pz-linkcard3' ) ); ?>
					</li>
					<li>
						<?php pz_lkc3_property_checkbox($prop, 'sns-fb', __('Facebook', 'pz-linkcard3' ).esc_html__('* number is not updated', 'pz-linkcard3' ) ); ?>
					</li>
					<li>
						<?php pz_lkc3_property_checkbox($prop, 'sns-hb', __('Hatena', 'pz-linkcard3' ) ); ?>
					</li>
				</ul>
			</td>
		</tr>
		<tr>
			<th><?php esc_html_e('Recurrence', 'pz-linkcard3' );	?></th>
			<td>
				<?php esc_html_e('The settings are located on the “Advanced” tab.',	'pz-linkcard3' ); ?>
			</td>
		</tr>
	</table>
	<?php submit_button(); ?>
</div>

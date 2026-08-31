<?php
	
if (!defined('ABSPATH' ) ) {
	 exit;
}
?>
<?php
	$shortcode_pattern_message = __('Enter at least two characters. The first character must be a half-width letter.', 'pz-linkcard3' );
	$render_shortcode_row = function($index, $row_class = '') use ($prop, $shortcode_pattern_message) {
		$code_key	=	'code'.$index;
		$labels		=	array(
			2	=>	__('Shortcode 2', 'pz-linkcard3' ),
			3	=>	__('Shortcode 3', 'pz-linkcard3' ),
			4	=>	__('Shortcode 4', 'pz-linkcard3' ),
		);
		echo	'<tr'.($row_class ? ' class="'.esc_attr($row_class ).'"' : '' ).'>';
		echo	'<th scope="row">'.esc_html($labels[$index] ?? '' ).'</th>';
		echo	'<td><span class="pz-monospace">[<input name="properties['.esc_attr($code_key ).']" type="text" class="pz-shortcode" value="'.esc_attr($prop[$code_key] ).'" pattern="[A-Za-z].[A-Za-z0-9]*" data-pz-pattern-message="'.esc_attr($shortcode_pattern_message ).'"> url="https://popozure.info" <span class="pz-shortcode-title"><span class="pz-shortcode-parameter">title</span>="xxxxxx"</span> <span class="pz-shortcode-content"><span class="pz-shortcode-parameter">content</span>="xxxxxx"</span>]</span><p>'.esc_html(__('Case-sensitive', 'pz-linkcard3' ) ).'</p></td>';
		echo	'</tr>';
	};
?>
<div class="<?php echo esc_attr($page_class('pz-editor' ) ); ?>" id="pz-editor">
	<div class="pz-submit-float"><?php submit_button(); ?></div>

	<h2><?php echo	esc_html__('Convert Settings', 'pz-linkcard3' ).wp_kses_post(sprintf($help_icon, esc_attr('-editor' ) ) ); ?></h2>
	<table class="form-table">
		<tr>
			<th scope="row"><?php esc_html_e('Convert from Text Link', 'pz-linkcard3' ); ?></th>
			<td>
				<?php pz_lkc3_property_checkbox($prop, 'auto-atag', __('Convert lines that contain only a text link into LinkCards.', 'pz-linkcard3' ), 'pz-auto-conv-enabled' ); ?>
				<div class="pz-autoconv-example-wrap">
					<?php esc_html_e('ex.', 'pz-linkcard3' ); ?>
					<div class="pz-autoconv-example"><p><?php esc_html_e('Lines like the following are converted.', 'pz-linkcard3' ); ?></p><p><a href="#" title="<?php echo esc_attr($this->plugin_url ); ?>">Pz-LinkCard</a></p><p><?php esc_html_e('Lines like the previous one are converted.', 'pz-linkcard3' ); ?></p></div>
				</div>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e('Convert from URL', 'pz-linkcard3' ); ?></th>
			<td>
				<?php pz_lkc3_property_checkbox($prop, 'auto-url', __('Convert lines that contain only a URL into LinkCards.', 'pz-linkcard3' ), 'pz-auto-conv-enabled' ); ?>
				<div class="pz-autoconv-example-wrap">
					<?php esc_html_e('ex.', 'pz-linkcard3' ); ?>
					<div class="pz-autoconv-example"><p><?php esc_html_e('Lines like the following are converted.', 'pz-linkcard3' ); ?></p><p><?php echo esc_url($this->plugin_url ); ?></p><p><?php esc_html_e('Lines like the previous one are converted.', 'pz-linkcard3' ); ?></p></div>
				</div>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e('Enable Shortcodes', 'pz-linkcard3' ); ?></th>
			<td>
				<?php pz_lkc3_property_checkbox($prop, 'flg-do-shortcode', __('Force shortcode rendering.', 'pz-linkcard3' ), 'pz-auto-conv' ); ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e('External Link Only', 'pz-linkcard3' ); ?></th>
			<td>
				<?php pz_lkc3_property_checkbox($prop, 'auto-external', __('Convert only external links.', 'pz-linkcard3' ).esc_html__('* The shortcode will be a link card regardless of this setting.', 'pz-linkcard3' ), 'pz-auto-conv' ); ?>
			</td>
		</tr>
	</table>
	<?php submit_button(); ?>

	<h2><?php echo	esc_html__('Editor Settings', 'pz-linkcard3' ).wp_kses_post(sprintf($help_icon, esc_attr('-editor' ) ) ); ?></h2>
	<table class="form-table">
		<tr>
			<th scope="row"><?php esc_html_e('Add Insert Button', 'pz-linkcard3' ); ?></th>
			<td>
				<?php pz_lkc3_property_checkbox($prop, 'flg-edit-insert', __('Add an insert button to the visual editor.', 'pz-linkcard3' ) ); ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e('Add Quick Tag', 'pz-linkcard3' ); ?></th>
			<td>
				<?php pz_lkc3_property_checkbox($prop, 'flg-edit-qtag', __('Add a Quick Tag button to the text editor.', 'pz-linkcard3' ) ); ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e('Clear Excerpt', 'pz-linkcard3' ); ?></th>
			<td>
				<?php pz_lkc3_property_checkbox($prop, 'flg-clear-excerpt', __('If the TITLE parameter is specified, EXCERPT is also cleared.', 'pz-linkcard3' ) ); ?>
			</td>
		</tr>
		</table>
	<?php submit_button(); ?>

	<h2><?php echo	esc_html__('Shortcode Settings', 'pz-linkcard3' ).wp_kses_post(sprintf($help_icon, esc_attr('-editor' ) ) ); ?></h2>
	<table class="form-table">
		<tr>
			<th scope="row"><?php esc_html_e('Shortcode 1', 'pz-linkcard3' ); ?></th>
				<td>
					<span class="pz-monospace">[<input name="properties[code1]" type="text" class="pz-shortcode pz-shortcode-1" value="<?php echo	esc_attr($prop['code1'] ); ?>" pattern="[A-Za-z].[A-Za-z0-9]*" data-pz-pattern-message="<?php echo esc_attr($shortcode_pattern_message ); ?>" /> url="https://popozure.info" 
					<span class="pz-shortcode-title"><span class="pz-shortcode-parameter">title</span>="xxxxxx"</span> 
					<span class="pz-shortcode-content"><span class="pz-shortcode-parameter">content</span>="xxxxxx"</span>]
					</span>
					<p><?php esc_html_e('Case-sensitive', 'pz-linkcard3' ); ?></p>
				</td>
			</tr>
		<tr>
			<th scope="row"><?php esc_html_e('Use Inline Text', 'pz-linkcard3' ); ?></th>
			<td>
				<span class="pz-monospace">[<span class="pz-shortcode-copy"><?php echo	esc_html($prop['code1'] ); ?></span> url="https://xxx"]
				<select name="properties[use-inline]" class="pz-shortcode-enabled">
					<option value=""	<?php selected($prop['use-inline'] == ''  ); ?>><?php esc_html_e('Do not use',		'pz-linkcard3' ); ?></option>
					<option value="1"	<?php selected($prop['use-inline'] == '1' ); ?>><?php esc_html_e('Use for excerpt',	'pz-linkcard3' ); ?></option>
					<option value="2"	<?php selected($prop['use-inline'] == '2' ); ?>><?php esc_html_e('Use for title',	'pz-linkcard3' ); ?></option>
				</select>
				<span class="pz-monospace">[/<span class="pz-shortcode-copy"><?php echo	esc_html($prop['code1'] ); ?></span>]</span>
				<p><?php esc_html_e('This setting applies only to Shortcode 1.', 'pz-linkcard3' ); ?></p></span></td>
			</td>
		</tr>
		<?php
			$render_shortcode_row(2);
			$render_shortcode_row(3);
			$render_shortcode_row(4, 'pz-admin-only');
		?>
		<tr>
			<th scope="row"><?php esc_html_e('Example Entry', 'pz-linkcard3' ); ?></th>
			<td>
				<p><?php echo esc_html__('ex1.', 'pz-linkcard3' ).'&ensp;'.esc_html__('Specify only URL parameters.', 'pz-linkcard3' ); ?><div class="pz-shortcode-example pz-click-all-select">[<span class="pz-shortcode-copy"><?php echo esc_html($prop['code1'] ); ?></span> url="https://xxx"]</div></p>
				<p><?php echo esc_html__('ex2.', 'pz-linkcard3' ).'&ensp;'.esc_html__('Specify URL and title parameters.', 'pz-linkcard3' ); ?><div class="pz-shortcode-example pz-click-all-select">[<span class="pz-shortcode-copy"><?php echo esc_html($prop['code1'] ); ?></span> url="https://xxx" <span class="pz-shortcode-title pz-monospace"><span class="pz-shortcode-parameter">title</span>="xxxxxx"</span>]</div></p>
				<p><?php echo esc_html__('ex3.', 'pz-linkcard3' ).'&ensp;'.esc_html__('Specify URL, title and content parameters.', 'pz-linkcard3' ); ?><div class="pz-shortcode-example pz-click-all-select">[<span class="pz-shortcode-copy"><?php echo esc_html($prop['code1'] ); ?></span> url="https://xxx" <span class="pz-shortcode-title pz-monospace"><span class="pz-shortcode-parameter">title</span>="xxxxxx"</span> <span class="pz-shortcode-content"><span class="pz-shortcode-parameter">content</span>="xxxxxx"</span>]</div></p>
				<p><?php esc_html_e('For any shortcode, you can change the title and excerpt using the `title` and `content` parameters.', 'pz-linkcard3' ); ?></p>
			</td>
		</tr>
	</table>
	<?php submit_button(); ?>
</div>

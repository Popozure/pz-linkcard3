<?php
	
if (!defined('ABSPATH' ) ) {
	 exit;
}
?>
<?php
	$letter_key_exists = function($key) {
		return array_key_exists($key, self::DEFAULTS );
	};
	$letter_color_cell = function($key) use ($prop, $letter_key_exists) {
		echo	'<td>';
		if	($letter_key_exists($key ) ) {
			echo	'<input type="text" name="properties['.esc_attr($key ).']" value="'.esc_attr($prop[$key] ).'" class="pz-color pz-monospace pz-color-picker">';
		} else {
			echo	'<input type="text" name="" value="" disabled="disabled" readonly="readonly" class="pz-color-dummy">';
		}
		echo	'</td>';
	};
	$letter_number_cell = function($key, $max, $unit = '') use ($prop, $letter_key_exists) {
		echo	'<td>';
		if	($letter_key_exists($key ) ) {
			$value	=	preg_replace('/[^0-9]/', '', ($prop[$key] ?? '' ) );
			echo	'<input type="number" name="properties['.esc_attr($key ).']" value="'.esc_attr($value ).'" class="pz-letter-box-r" min="0" max="'.esc_attr($max ).'">';
		} else {
			echo	'<input type="number" name="" value="" disabled="disabled" readonly="readonly" class="pz-letter-box-r" min="0" max="'.esc_attr($max ).'">';
		}
		echo	esc_html($unit );
		echo	'</td>';
	};
	$letter_checkbox_input = function($key) use ($prop, $letter_key_exists) {
		if	($letter_key_exists($key ) ) {
			$value	=	preg_replace('/[^0-9]/', '', ($prop[$key] ?? '' ) );
			echo	'<input type="hidden" name="properties['.esc_attr($key ).']" value="">';
			echo	'<input type="checkbox" name="properties['.esc_attr($key ).']" value="1" '.checked((bool) $value, true, false ).'>';
		} else {
			echo	'<input type="checkbox" name="" value="" disabled="disabled" readonly="readonly">';
		}
	};
?>
<div class="<?php echo esc_attr($page_class('pz-letter' ) ); ?>" id="pz-letter">
	<div class="pz-submit-float"><?php submit_button(); ?></div>
	<h2><?php echo	esc_html__('Letter Settings', 'pz-linkcard3' ).wp_kses_post(sprintf($help_icon, esc_attr('-letter' ) ) ); ?></h2>
	<table class="pz-letter-table form-table">
		<tr class="pz-letter-table-header">
			<th></th>
			<th><?php esc_html_e('Letter Color',		'pz-linkcard3' ); ?></th>
			<th><?php esc_html_e('Outline Color',		'pz-linkcard3' ); ?></th>
			<th><?php esc_html_e('Background Color',	'pz-linkcard3' ); ?></th>
			<th><?php esc_html_e('Size',				'pz-linkcard3' ); ?></th>
			<th><?php esc_html_e('Line Height',			'pz-linkcard3' ); ?></th>
			<th><?php esc_html_e('Line Limit',			'pz-linkcard3' ); ?></th>
			<th><?php esc_html_e('Bold',				'pz-linkcard3' ); ?></th>
			<th><?php esc_html_e('Italic',				'pz-linkcard3' ); ?></th>
			<th><?php echo wp_kses_post(__('Underline<br> (On Hover)',	'pz-linkcard3' ) ); ?></th>
		</tr>
		<?php
		$table	=	array(
			array( 'name' => 'title'	,	'title' => __('Title',				'pz-linkcard3' ) ),
			array( 'name' => 'excerpt'	,	'title' => __('Excerpt',			'pz-linkcard3' ) ),
			array( 'name' => 'url'		,	'title' => __('URL',				'pz-linkcard3' ) ),
			array( 'name' => 'date'		,	'title' => __('Date',				'pz-linkcard3' ) ),
			array( 'name' => 'heading'	,	'title' => __('Header Text',		'pz-linkcard3' ) ),
			array( 'name' => 'more'		,	'title' => __('More Button',		'pz-linkcard3' ) ),
			array( 'name' => 'info'		,	'title' => __('Site Information',	'pz-linkcard3' ) ),
			array( 'name' => 'added'	,	'title' => __('Added Text',			'pz-linkcard3' ) ),
			array( 'name' => 'cat'		,	'title' => __('Category',			'pz-linkcard3' ) ),
			array( 'name' => 'sns'		,	'title' => __('SNS Count',			'pz-linkcard3' ) ),
		);
		foreach ($table as $t) {
			echo	'<tr>';
			echo	'<th>'.esc_html($t['title'] ).'</th>';
			$letter_color_cell($t['name'].'-color' );
			$letter_color_cell($t['name'].'-outline-color' );
			$letter_color_cell($t['name'].'-bg-color' );
			$letter_number_cell($t['name'].'-size', 999, __('px', 'pz-linkcard3' ) );
			$letter_number_cell($t['name'].'-height', 999, __('px', 'pz-linkcard3' ) );
			$letter_number_cell($t['name'].'-maxline', 99 );

			echo	'<td>';
			$letter_checkbox_input($t['name'].'-bold' );
			echo	'</td>';

			echo	'<td>';
			$letter_checkbox_input($t['name'].'-italic' );
			echo	'</td>';

			echo	'<td>';
			$key		=		$t['name'].'-underline';
			if		($letter_key_exists($key ) ) {
				$letter_checkbox_input($key );
				$key	=		$t['name'].'-hover';
				echo	'&ensp;'.esc_html__('(', 'pz-linkcard3' ).'&ensp;';
				$letter_checkbox_input($key );
				echo	esc_html__(')', 'pz-linkcard3' );
			} else {
				echo	'<input type="checkbox" name="" value="" disabled="disabled" readonly="readonly">';
				echo	'&ensp;'.esc_html__('(', 'pz-linkcard3' ).'&ensp;';
				echo	'<input type="checkbox" name="" value="" disabled="disabled" readonly="readonly">';
				echo	esc_html__(')', 'pz-linkcard3' );
			}
			echo	'</td>';

			echo	'</tr>';
		};
	echo	'</table>';
	echo	'<span style="color: #f80; font-size: 16px;">'.esc_html__('* The "Outline Color" and "Background Color" will become transparent when you press CLEAR to erase the color code.', 'pz-linkcard3' ).'</span>';

	submit_button();
	echo	'</div>';

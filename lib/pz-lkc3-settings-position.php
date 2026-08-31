<?php
if (!defined('ABSPATH' ) ) {
	 exit;
}
$enclose_class_pattern_message = __('Use only half-width letters, numbers, spaces, hyphens, and underscores. The first character must be a letter.', 'pz-linkcard3' );
?>
<div class="<?php echo esc_attr($page_class('pz-position' ) ); ?>" id="pz-position">
	<div class="pz-submit-float"><?php submit_button(); ?></div>
	<h2><?php echo	esc_html__('Position Settings', 'pz-linkcard3' ).wp_kses_post(sprintf($help_icon, esc_attr('-position' ) ) ); ?></h2>

	<table class="form-table pz-settings-wide-label-table">
		<tr>
			<th scope="row"><?php esc_html_e('Link the Entire Card', 'pz-linkcard3' ); ?></th>
			<td>
				<?php pz_lkc3_property_checkbox($prop, 'link-all', __('Enclose the entire card at anchor.', 'pz-linkcard3' ) ); ?>
			</td>
		</tr>
		<tr><th scope="row"><?php esc_html_e('Resize', 'pz-linkcard3' ); ?></th>
			<td>
				<?php pz_lkc3_property_checkbox($prop, 'thumbnail-resize', __('Adjust thumbnail and letter size according to width.', 'pz-linkcard3' ) ); ?>
			</td>
		</tr>
	</table>

	<div class="pz-position-diagram">
		<div class="pz-position-heading">
			<div class="pz-position-field">
				<label><?php esc_html_e('Margin top', 'pz-linkcard3' ); ?></label>
				<?php echo_list('heading-margin-top', $prop['heading-margin-top'] ?? '', LIST_HEADING_MARGIN, '', '', '', true, true ); ?>
			</div>
			<div class="pz-position-heading-row">
				<div class="pz-position-field">
					<label><?php esc_html_e('Margin left', 'pz-linkcard3' ); ?></label>
					<?php echo_list('heading-margin-left', $prop['heading-margin-left'] ?? '', LIST_HEADING_MARGIN, '', '', '', true, true ); ?>
				</div>
				<div class="pz-position-heading-panel-wrap">
					<div class="pz-position-panel pz-position-panel-heading">
						<div class="pz-position-panel-title"><?php esc_html_e('Header', 'pz-linkcard3' ); ?></div>
						<div class="pz-position-field pz-position-field-inline">
							<label><?php esc_html_e('Horizontal Margin', 'pz-linkcard3' ); ?></label>
							<?php echo_list('heading-padding-h', $prop['heading-padding-h'] ?? '', LIST_MARGIN_0, '', '', '', true, true ); ?>
						</div>
						<div class="pz-position-field pz-position-field-inline">
							<label><?php esc_html_e('Vertical Margin', 'pz-linkcard3' ); ?></label>
							<?php echo_list('heading-padding-v', $prop['heading-padding-v'] ?? '', LIST_MARGIN_0, '', '', '', true, true ); ?>
						</div>
					</div>
					<div class="pz-note pz-position-heading-note"><?php esc_html_e('* Set the header text to display it.', 'pz-linkcard3' ); ?></div>
				</div>
			</div>
		</div>

		<div class="pz-position-field pz-position-outer-top">
			<label><?php esc_html_e('Margin top', 'pz-linkcard3' ); ?></label>
			<?php echo_list('margin-top', $prop['margin-top'] ?? '', LIST_MARGIN_N, '', '', '', true, true ); ?>
		</div>

		<div class="pz-position-field pz-position-outer-left">
			<label><?php esc_html_e('Margin left', 'pz-linkcard3' ); ?></label>
			<?php echo_list('margin-left', $prop['margin-left'] ?? '', LIST_MARGIN_A, '', '', '', true, true ); ?>
		</div>

		<div class="pz-position-field pz-position-outer-right">
			<label><?php esc_html_e('Margin right', 'pz-linkcard3' ); ?></label>
			<?php echo_list('margin-right', $prop['margin-right'] ?? '', LIST_MARGIN_A, '', '', '', true, true ); ?>
		</div>

		<div class="pz-position-field pz-position-outer-bottom">
			<label><?php esc_html_e('Margin bottom', 'pz-linkcard3' ); ?></label>
			<?php echo_list('margin-bottom', $prop['margin-bottom'] ?? '', LIST_MARGIN_N, '', '', '', true, true ); ?>
		</div>

		<div class="pz-position-card">
			<div class="pz-position-field pz-position-padding-top">
				<label><?php esc_html_e('Padding top', 'pz-linkcard3' ); ?></label>
				<?php echo_list('padding-top', $prop['padding-top'] ?? '', LIST_MARGIN, '', '', '', true, true ); ?>
			</div>


			<div class="pz-position-card-body">
				<div class="pz-position-field pz-position-padding-left">
					<label><?php esc_html_e('Padding left', 'pz-linkcard3' ); ?></label>
					<?php echo_list('padding-left', $prop['padding-left'] ?? '', LIST_MARGIN, '', '', '', true, true ); ?>
				</div>

				<div class="pz-position-card-content">
					<div class="pz-position-panel pz-position-siteinfo">
						<div class="pz-position-panel-title"><?php esc_html_e('Site Information', 'pz-linkcard3' ); ?></div>
						<div class="pz-position-field pz-position-field-inline">
							<label><?php esc_html_e('Position', 'pz-linkcard3' ); ?></label>
							<?php echo_list('info-position', $prop['info-position'] ?? '', LIST_INFO_POSITION, '', '', '', true, true ); ?>
						</div>
						<div class="pz-position-field pz-position-field-inline">
							<label><?php esc_html_e('Site Icon Size', 'pz-linkcard3' ); ?></label>
							<?php echo_list('siteicon-size', $prop['siteicon-size'] ?? '', LIST_ICON_SIZE, '', '', '', true, true ); ?>
						</div>
					</div>

					<div class="pz-position-field pz-position-content-margin">
						<label><?php esc_html_e('Block spacing', 'pz-linkcard3' ); ?></label>
						<?php echo_list('content-margin', $prop['content-margin'] ?? '', LIST_MARGIN, '', '', '', true, true ); ?>
					</div>

					<div class="pz-position-content-row">
						<div class="pz-position-panel pz-position-thumbnail">
							<div class="pz-position-panel-title"><?php esc_html_e('Thumbnail', 'pz-linkcard3' ); ?></div>
							<div class="pz-position-field pz-position-field-inline pz-position-thumbnail-position">
								<label><?php esc_html_e('Position', 'pz-linkcard3' ); ?></label>
								<?php echo_list('thumbnail-position', $prop['thumbnail-position'] ?? '', LIST_THUMBNAIL_POSITION, '', '', '', true, true ); ?>
							</div>
							<div class="pz-position-field pz-position-field-inline pz-position-thumbnail-width-field">
								<label><?php esc_html_e('Width', 'pz-linkcard3' ); ?></label>
								<span class="pz-position-input-unit">
									<input name="properties[thumbnail-width]" type="number" value="<?php echo intval($prop['thumbnail-width'] ); ?>" class="pz-position-number-small" />px
								</span>
							</div>
							<div class="pz-position-field pz-position-field-inline pz-position-thumbnail-height-field">
								<label><?php esc_html_e('Height', 'pz-linkcard3' ); ?></label>
								<span class="pz-position-input-unit">
									<input name="properties[thumbnail-height]" type="number" value="<?php echo intval($prop['thumbnail-height'] ); ?>" class="pz-position-number-small" />px
								</span>
							</div>
						</div>

						<div class="pz-position-size">
							<div class="pz-position-field pz-position-field-inline pz-position-size-width-field">
								<label><?php esc_html_e('Width', 'pz-linkcard3' ); ?></label>
								<span class="pz-position-input-unit">
									<input name="properties[width]" type="number" value="<?php echo esc_attr($prop['width'] ); ?>" max="9999" class="pz-position-number-medium" />
									<select name="properties[width-unit]" class="pz-position-width-unit">
										<option value="px" <?php selected($prop['width-unit'] == 'px' ); ?>><?php esc_html_e('px', 'pz-linkcard3' ); ?></option>
										<option value="%"  <?php selected($prop['width-unit'] == '%'  ); ?>><?php esc_html_e('%',  'pz-linkcard3' ); ?></option>
									</select>
								</span>
							</div>
							<div class="pz-position-field pz-position-field-inline pz-position-size-height-field">
								<label><?php esc_html_e('Height', 'pz-linkcard3' ); ?></label>
								<span class="pz-position-input-unit">
									<input name="properties[content-height]" type="number" value="<?php echo intval($prop['content-height'] ); ?>" max="9999" class="pz-position-number-medium" />px
								</span>
							</div>
						</div>
					</div>
				</div>

				<div class="pz-position-field pz-position-padding-right">
					<label><?php esc_html_e('Padding right', 'pz-linkcard3' ); ?></label>
					<?php echo_list('padding-right', $prop['padding-right'] ?? '', LIST_MARGIN, '', '', '', true, true ); ?>
				</div>
			</div>

			<div class="pz-position-field pz-position-padding-bottom">
				<label><?php esc_html_e('Padding bottom', 'pz-linkcard3' ); ?></label>
				<?php echo_list('padding-bottom', $prop['padding-bottom'] ?? '', LIST_MARGIN, '', '', '', true, true ); ?>
			</div>
		</div>

	</div>
	<?php
		echo	esc_html__('* Setting both left and right margins to auto will center the content.', 'pz-linkcard3' );
	?>

	<table class="form-table">
		<tr>
			<th rowspan="3">
				<?php esc_html_e('Wrapper Tag', 'pz-linkcard3' ); ?>
			</th>
			<th>
				<?php esc_html_e('Tag', 'pz-linkcard3' ); ?>
			</th>
			<td>
				<?php
					echo_list('enclose-tag', $prop['enclose-tag'] ?? '', LIST_ENCLOSE_TAG, '', '', '', true, true );
				?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e('Additional Class ID (Desktop)',	'pz-linkcard3' ); ?></th>
			<td><label class="pz-monospace">class="<input name="properties[enclose-class-pc]"			type="text" size="40" value="<?php echo	(isset($this->options['enclose-class-pc'] )		? esc_attr($this->options['enclose-class-pc'] ) 	: '' ); ?>" pattern="[A-Za-z]+[A-Za-z0-9\-_ ]*" data-pz-pattern-message="<?php echo esc_attr($enclose_class_pattern_message ); ?>" />"</label><br><?php echo esc_html__('* Use only half-width alphanumeric characters, \‘-\’, and \‘_\’.', 'pz-linkcard3' ); ?></td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e('Additional Class ID (Mobile)',	'pz-linkcard3' ); ?></th>
			<td><label class="pz-monospace">class="<input name="properties[enclose-class-mobile]"		type="text" size="40" value="<?php echo	(isset($this->options['enclose-class-mobile'] ) ? esc_attr($this->options['enclose-class-mobile'] ) : '' ); ?>" pattern="[A-Za-z]+[A-Za-z0-9\-_ ]*" data-pz-pattern-message="<?php echo esc_attr($enclose_class_pattern_message ); ?>" />"</label><br><?php echo esc_html__('* Use only half-width alphanumeric characters, \‘-\’, and \‘_\’.', 'pz-linkcard3' ); ?></td>
		</tr>
	</table>

	<?php submit_button(); ?>
</div>

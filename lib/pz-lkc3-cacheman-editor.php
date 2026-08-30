<?php
	
if (!defined( 'ABSPATH' ) ) { header( 'HTTP/1.1 403 Forbidden' ); exit; } ?>
<?php
	if	(!isset($cacheman_allowed_html ) || !is_array($cacheman_allowed_html ) ) {
		$cacheman_allowed_html	=	wp_kses_allowed_html('post' );
	}
	$cacheman_allowed_html['input']		=	array(
		'accesskey'		=>	true,
		'class'			=>	true,
		'disabled'		=>	true,
		'id'			=>	true,
		'max'			=>	true,
		'name'			=>	true,
		'ondblclick'	=>	true,
		'readonly'		=>	true,
		'size'			=>	true,
		'style'			=>	true,
		'type'			=>	true,
		'value'			=>	true,
	);
	$cacheman_allowed_html['button']	=	array(
		'aria-label'	=>	true,
		'class'			=>	true,
		'data-no-overlay'	=>	true,
		'data-pz-media-target'	=>	true,
		'formnovalidate'	=>	true,
		'name'			=>	true,
		'title'			=>	true,
		'type'			=>	true,
		'value'			=>	true,
	);
	$cacheman_allowed_html['textarea']	=	array(
		'class'			=>	true,
		'name'			=>	true,
		'ondblclick'	=>	true,
		'readonly'		=>	true,
		'rows'			=>	true,
		'wrap'			=>	true,
	);
	$cacheman_get_value = function($key, $default = '') use ($data) {
		return $data[$key] ?? $default;
	};
	$cacheman_admin_edit_attr = function() {
		return !empty($this->options['admin-mode'] ) ? ' ondblclick="this.readOnly=false;"' : '';
	};
	$cacheman_text_input = function($key, $args = array() ) use ($cacheman_get_value, $cacheman_admin_edit_attr) {
		$name		=	$args['name'] ?? 'data['.$key.']';
		$value		=	$args['value'] ?? $cacheman_get_value($key );
		$size		=	$args['size'] ?? 8;
		$class		=	$args['class'] ?? '';
		$title		=	isset($args['title'] ) ? ' title="'.esc_attr($args['title'] ).'"' : '';
		$style		=	isset($args['style'] ) ? ' style="'.esc_attr($args['style'] ).'"' : '';
		$readonly	=	!empty($args['readonly'] ) ? ' readonly="readonly"' : '';
		$admin_edit	=	!empty($args['editable'] ) ? ' ondblclick="this.readOnly=false;"' : (!empty($args['admin_edit'] ) ? $cacheman_admin_edit_attr() : '');
		$class_attr	=	$class ? ' class="'.esc_attr($class ).'"' : '';
		return '<input name="'.esc_attr($name ).'" type="text" value="'.esc_attr($value ).'" size="'.esc_attr($size ).'"'.$title.$class_attr.$readonly.$admin_edit.$style.'>';
	};
	$cacheman_action_buttons = function() {
		return '<button type="submit" name="action" value="update" class="button button-primary button-large">'.esc_html(__('Update', 'pz-linkcard3' ) ).'</button><button type="submit" name="action" value="cancel" class="button button-large" formnovalidate data-no-overlay="1">'.esc_html(__('Cancel', 'pz-linkcard3' ) ).'</button>';
	};
	$cacheman_field_row = function($label, $field, $class = '') {
		return '<tr'.($class ? ' class="'.esc_attr($class ).'"' : '' ).'><th scope="row">'.$label.'</th><td>'.$field.'</td></tr>';
	};
	$cacheman_postbox_open = function($title, $class = '', $header_action = '') {
		$box_class	=	'pz-man-cache-postbox'.($class ? ' '.esc_attr($class ) : '' );
		return '<div class="postbox '.$box_class.'"><div class="postbox-header"><h2>'.esc_html($title ).'</h2>'.$header_action.'</div><div class="inside">';
	};
	$cacheman_postbox_close = function() {
		return '</div></div>';
	};
	$cacheman_character_count = function($text) {
		$count	=	function_exists('mb_strlen' ) ? mb_strlen((string) $text ) : strlen((string) $text );
		/* translators: %s: 文字数 */
		return	sprintf(esc_html__('%s characters', 'pz-linkcard3' ), esc_html(number_format_i18n($count ) ) );
	};
	$cacheman_image_preview = function($key, $class = '') use ($cacheman_get_value) {
		$image_url		=	$cacheman_get_value($key );
		$image_cache_url	=	$image_url ? $this->pz_GetImage($image_url ) : '';
		$image_preview_url	=	$image_cache_url ?: $image_url;
		if	(!$image_cache_url ) {
			if	(!$image_preview_url ) {
				return '<div class="pz-man-cache-image-preview pz-man-cache-image-empty">-</div>';
			}
		}
		return '<div class="pz-man-cache-image-preview'.($class ? ' '.esc_attr($class ) : '' ).'"><a href="'.esc_url($image_preview_url ).'" target="_blank" rel="noopener noreferrer" class="pz-man-image-box-trigger"><div><img src="'.esc_url($image_preview_url ).'" alt="" loading="lazy" /></div></a></div>';
	};
	$cacheman_media_button = function($target_name) {
		return '<button type="button" class="button pz-additional-only pz-man-cache-media-button" data-pz-media-target="'.esc_attr($target_name ).'"><span class="dashicons dashicons-admin-media"></span>'.esc_html(__('Media', 'pz-linkcard3' ) ).'</button>';
	};
	$cacheman_category_checklist = function($post_id) use ($cacheman_get_value) {
		$hidden		=	'<input name="data[post_cat]" type="hidden" value="'.esc_attr($cacheman_get_value('post_cat' ) ).'">';
		$post_id	=	intval($post_id );
		if	($post_id <= 0 || !get_post($post_id ) ) {
			return	$hidden.'-';
		}
		if	(!function_exists('wp_terms_checklist' ) ) {
			require_once ABSPATH.'wp-admin/includes/template.php';
		}

		ob_start();
		wp_terms_checklist($post_id, array(
			'taxonomy'			=>	'category',
			'checked_ontop'		=>	false,
		) );
		$checklist	=	ob_get_clean();
		if	(trim($checklist ) === '' ) {
			return	$hidden.'-';
		}
		$checklist	=	preg_replace('/<input\b(?![^>]*\sdisabled=)/', '<input disabled="disabled"', $checklist );

		return	$hidden.
			'<div id="categorydiv" class="categorydiv pz-man-cache-categorydiv">'.
				'<ul class="category-tabs"><li class="tabs"><a href="#category-all">'.esc_html__('All Categories', 'pz-linkcard3' ).'</a></li></ul>'.
				'<div id="category-all" class="tabs-panel">'.
					'<ul id="categorychecklist" data-wp-lists="list:category" class="categorychecklist form-no-clear">'.$checklist.'</ul>'.
				'</div>'.
			'</div>';
	};
?>
	<button type="submit" name="action" value="update" class="pz-man-cache-default-submit" aria-hidden="true" tabindex="-1" style="position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden;padding:0;border:0;"></button>
<?php if (!empty($return_url ) ) : ?>
	<input type="hidden" name="return_url" value="<?php echo esc_url($return_url ); ?>" />
	<input type="hidden" name="return_scroll" value="<?php echo esc_attr($return_scroll ?? 0 ); ?>" />
<?php endif; ?>

	<div id="poststuff" class="pz-man-cache-dirty-check pz-man-cache-poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content" class="pz-man-cache-main">
				<div id="titlediv" class="pz-man-cache-titlediv">
					<div id="titlewrap">
						<label class="screen-reader-text" for="pz-man-cache-title"><?php esc_html_e('Title', 'pz-linkcard3' ); ?></label>
						<input id="pz-man-cache-title" name="data[title]" type="text" value="<?php echo esc_attr($data['title'] ); ?>" size="80" accesskey="2" placeholder="<?php esc_attr_e('Title', 'pz-linkcard3' ); ?>" />
					</div>
					<div class="inside pz-man-cache-permalink">
						<div class="pz-man-cache-permalink-row">
							<strong><?php esc_html_e('URL', 'pz-linkcard3' ) ?></strong>
							<?php echo wp_kses($cacheman_text_input('url', array('size' => 80, 'class' => 'pz-monospace', 'readonly' => true, 'title' => $this->pz_DecodeURL($cacheman_get_value('url' ) ) ) ), $cacheman_allowed_html ); ?>
						</div>
						<div class="pz-man-cache-permalink-row">
							<strong><?php esc_html_e('Redirect URL', 'pz-linkcard3' ) ?></strong>
							<?php echo wp_kses($cacheman_text_input('url_redir', array('size' => 80, 'class' => 'pz-monospace', 'readonly' => true ) ), $cacheman_allowed_html ); ?>
						</div>
					</div>
				</div>

				<?php
					/* translators: %s: 文字数 */
					$excerpt_count	=	'<span class="pz-man-cache-character-count" data-pz-character-count-for="pz-man-cache-excerpt" data-pz-character-count-template="'.esc_attr__('%s characters', 'pz-linkcard3' ).'">'.$cacheman_character_count($data['excerpt'] ).'</span>';
					echo wp_kses($cacheman_postbox_open(__('Excerpt', 'pz-linkcard3' ), 'pz-man-cache-editor-box', $excerpt_count ), $cacheman_allowed_html );
				?>
					<textarea id="pz-man-cache-excerpt" name="data[excerpt]" rows="6" wrap="soft" accesskey="3"><?php echo esc_textarea($data['excerpt'] ); ?></textarea>
				<?php echo wp_kses($cacheman_postbox_close(), $cacheman_allowed_html ); ?>

				<?php echo wp_kses($cacheman_postbox_open(__('Site Data', 'pz-linkcard3' ) ), $cacheman_allowed_html ); ?>
					<table class="form-table pz-man-cache-form-table">
						<?php echo wp_kses($cacheman_field_row(esc_html__('Site Name', 'pz-linkcard3' ), '<input name="data[site_name]" type="text" value="'.esc_attr($data['site_name'] ?? '' ).'" size="80" accesskey="1">' ), $cacheman_allowed_html ); ?>
						<?php echo wp_kses($cacheman_field_row(esc_html__('Domain', 'pz-linkcard3' ), $cacheman_text_input('domain', array('size' => 80, 'readonly' => true ) ) ), $cacheman_allowed_html ); ?>
					</table>
				<?php echo wp_kses($cacheman_postbox_close(), $cacheman_allowed_html ); ?>

				<?php echo wp_kses($cacheman_postbox_open(__('Internal Link Data', 'pz-linkcard3' ) ), $cacheman_allowed_html ); ?>
					<table class="form-table pz-man-cache-form-table">
						<?php
							$post_id_value	=	intval($data['post_id'] ?? 0 );
							$post_id_display	=	$post_id_value > 0 ? $post_id_value : '';
							echo wp_kses($cacheman_field_row(esc_html__('Post ID', 'pz-linkcard3' ), '<input name="data[post_id]" type="text" value="'.esc_attr($post_id_display ).'" size="8" readonly="readonly" class="pz-additional-only"> '.esc_html($post_id_display ) ), $cacheman_allowed_html );
						?>
						<?php echo wp_kses($cacheman_field_row(esc_html__('Post Date', 'pz-linkcard3' ), '<input name="data[post_date]" type="text" value="'.esc_attr($data['post_date'] ).'" size="8" readonly="readonly" class="pz-additional-only"> '.esc_html($this->pz_Date($this->format_datetime, $data['post_date'] ) ) ), $cacheman_allowed_html ); ?>
						<?php echo wp_kses($cacheman_field_row(esc_html__('Modified Date', 'pz-linkcard3' ), '<input name="data[post_modified]" type="text" value="'.esc_attr($data['post_modified'] ).'" size="8" readonly="readonly" class="pz-additional-only"> '.esc_html($this->pz_Date($this->format_datetime, $data['post_modified'] ) ) ), $cacheman_allowed_html ); ?>
						<?php echo wp_kses($cacheman_field_row(esc_html__('Category', 'pz-linkcard3' ), $cacheman_category_checklist($data['post_id'] ) ), $cacheman_allowed_html ); ?>
					</table>
				<?php echo wp_kses($cacheman_postbox_close(), $cacheman_allowed_html ); ?>

				<?php echo wp_kses($cacheman_postbox_open(__('Registration Data', 'pz-linkcard3' ), 'pz-additional-only' ), $cacheman_allowed_html ); ?>
					<table class="form-table pz-man-cache-form-table">
						<?php echo wp_kses($cacheman_field_row(esc_html__('Registration Title', 'pz-linkcard3' ), '<input name="data[regist_title]" type="text" value="'.esc_attr($data['regist_title'] ).'" size="80" readonly="readonly"'.$cacheman_admin_edit_attr().'>' ), $cacheman_allowed_html ); ?>
						<?php echo wp_kses($cacheman_field_row(esc_html__('Registration Excerpt', 'pz-linkcard3' ), '<textarea name="data[regist_excerpt]" rows="5" wrap="soft" readonly="readonly"'.$cacheman_admin_edit_attr().'>'.esc_textarea($data['regist_excerpt'] ).'</textarea>' ), $cacheman_allowed_html ); ?>
						<?php echo wp_kses($cacheman_field_row(esc_html__('Registration Character Set', 'pz-linkcard3' ), '<input name="data[regist_charset]" type="text" value="'.esc_attr($data['regist_charset'] ).'" size="8" readonly="readonly"'.$cacheman_admin_edit_attr().'>' ), $cacheman_allowed_html ); ?>
						<?php echo wp_kses($cacheman_field_row(esc_html__('Registration Date', 'pz-linkcard3' ), '<input name="data[regist_time]" type="text" value="'.esc_attr($data['regist_time'] ).'" size="8" readonly="readonly"'.$cacheman_admin_edit_attr().' class="pz-additional-only"> '.esc_html($this->pz_Date($this->format_datetime, $data['regist_time'] ) ) ), $cacheman_allowed_html ); ?>
						<?php
							$rs = esc_attr($data['regist_result'] );
							echo wp_kses($cacheman_field_row(esc_html__('Registration Result Code', 'pz-linkcard3' ), '<input name="data[regist_result]" type="text" value="'.esc_attr($data['regist_result'] ).'" size="2" readonly="readonly"'.$cacheman_admin_edit_attr().'>&ensp;'.esc_html($rs ).' '.esc_html($this->pz_HTTPMessage($rs ) ) ), $cacheman_allowed_html );
						?>
					</table>
				<?php echo wp_kses($cacheman_postbox_close(), $cacheman_allowed_html ); ?>
			</div>

			<div id="postbox-container-1" class="postbox-container pz-man-cache-side">
				<?php
					$cacheman_reload_button	=	'<button type="submit" name="single-edit" value="'.esc_attr($cacheman_get_value('card_id' ) ).'" class="pz-man-cache-reload-button" title="'.esc_attr__('Reload', 'pz-linkcard3' ).'" aria-label="'.esc_attr__('Reload', 'pz-linkcard3' ).'" formnovalidate><span class="dashicons dashicons-update"></span></button>';
					echo wp_kses($cacheman_postbox_open(__('Cache Editor', 'pz-linkcard3' ), 'pz-man-cache-submitbox', $cacheman_reload_button ), $cacheman_allowed_html );
				?>
					<div class="pz-man-cache-submit-meta">
						<input name="data[card_id]" type="hidden" value="<?php echo esc_attr($cacheman_get_value('card_id' ) ); ?>" />
						<input name="data[charset]" type="hidden" value="edit">
						<input name="data[click_count]" type="hidden" value="<?php echo esc_attr($data['click_count'] ); ?>" />
						<div class="pz-man-cache-submit-meta-row"><span><?php esc_html_e('ID', 'pz-linkcard3' ); ?>:</span> <span class="pz-man-cache-submit-meta-value"><?php echo esc_html($cacheman_get_value('card_id' ) ); ?></span></div>
						<div class="pz-man-cache-submit-meta-row"><span><?php esc_html_e('Character Set', 'pz-linkcard3' ); ?>:</span> <span class="pz-man-cache-submit-meta-value"><?php echo esc_html($data['regist_charset'] ).esc_html(__('->', 'pz-linkcard3' ) ).esc_html('edit' ); ?></span></div>
						<div class="pz-man-cache-submit-meta-row"><span><?php esc_html_e('Click Count', 'pz-linkcard3' ); ?>:</span> <span class="pz-man-cache-submit-meta-value"><?php echo esc_html(number_format($data['click_count'] ) ); ?></span></div>
						<div class="pz-man-cache-submit-meta-row"><span><?php esc_html_e('Registration Date', 'pz-linkcard3' ); ?>:</span> <span class="pz-man-cache-submit-meta-value"><?php echo esc_html($this->pz_Date($this->format_datetime, $data['regist_time'] ) ); ?></span></div>
						<div class="pz-man-cache-submit-meta-row"><span><?php esc_html_e('Update Date', 'pz-linkcard3' ); ?>:</span> <span class="pz-man-cache-submit-meta-value"><?php echo esc_html($this->pz_Date($this->format_datetime, $data['update_time'] ) ); ?></span></div>
					</div>
					<div id="major-publishing-actions">
						<div class="pz-man-cache-actions">
							<?php echo wp_kses($cacheman_action_buttons(), $cacheman_allowed_html ); ?>
						</div>
						<div class="clear"></div>
					</div>
				<?php echo wp_kses($cacheman_postbox_close(), $cacheman_allowed_html ); ?>

				<?php echo wp_kses($cacheman_postbox_open(__('Thumbnail Image', 'pz-linkcard3' ), 'pz-man-cache-image-box' ), $cacheman_allowed_html ); ?>
					<?php echo wp_kses($cacheman_image_preview('thumbnail', 'pz-man-cache-thumbnail-preview' ), $cacheman_allowed_html ); ?>
					<div class="pz-man-cache-image-url">
						<label><?php esc_html_e('URL', 'pz-linkcard3' ); ?></label>
						<div class="pz-man-cache-image-url-control">
							<input name="data[thumbnail]" type="text" value="<?php echo esc_attr($data['thumbnail'] ); ?>" size="80" class="pz-monospace" readonly="readonly" ondblclick="this.readOnly=false;" />
							<?php echo wp_kses($cacheman_media_button('data[thumbnail]' ), $cacheman_allowed_html ); ?>
						</div>
					</div>
				<?php echo wp_kses($cacheman_postbox_close(), $cacheman_allowed_html ); ?>

				<?php echo wp_kses($cacheman_postbox_open(__('Site Icon', 'pz-linkcard3' ), 'pz-man-cache-image-box' ), $cacheman_allowed_html ); ?>
					<?php echo wp_kses($cacheman_image_preview('site_icon', 'pz-man-cache-siteicon-preview' ), $cacheman_allowed_html ); ?>
					<div class="pz-man-cache-image-url">
						<label><?php esc_html_e('URL', 'pz-linkcard3' ); ?></label>
						<div class="pz-man-cache-image-url-control">
							<input name="data[site_icon]" type="text" value="<?php echo esc_attr($data['site_icon'] ?? '' ); ?>" size="80" class="pz-monospace" readonly="readonly" ondblclick="this.readOnly=false;" />
							<?php echo wp_kses($cacheman_media_button('data[site_icon]' ), $cacheman_allowed_html ); ?>
						</div>
					</div>
				<?php echo wp_kses($cacheman_postbox_close(), $cacheman_allowed_html ); ?>

				<?php echo wp_kses($cacheman_postbox_open(__('Status', 'pz-linkcard3' ) ), $cacheman_allowed_html ); ?>
					<table class="form-table pz-man-cache-side-table">
						<tr class="pz-man-cache-side-single-row">
							<td colspan="2"><label><input name="data[no_failure]" type="checkbox" value="1" <?php checked(!empty($data['no_failure'] ), true ); ?> accesskey="4" /><?php esc_html_e('Ignore the result code when it indicates an error.', 'pz-linkcard3' ); ?></label></td>
						</tr>
						<tr class="pz-man-cache-side-meta-row">
							<td colspan="2"><span><?php esc_html_e('Update Date', 'pz-linkcard3' ); ?>:</span> <?php echo esc_html($this->pz_Date($this->format_datetime, $data['update_time'] ) ); ?><br class="pz-additional-only"><input name="data[update_time]" type="text" value="<?php echo esc_attr($data['update_time'] ); ?>" class="pz-additional-only" size="9" readonly="readonly"<?php if (!empty($this->options['admin-mode'] ) ) : ?> ondblclick="this.readOnly=false;"<?php endif; ?> /></td>
						</tr>
						<?php
							$rs = esc_html($data['update_result'] );
							echo wp_kses('<tr class="pz-man-cache-side-meta-row"><td colspan="2"><span>'.esc_html__('Result Code of Update', 'pz-linkcard3' ).':</span> '.esc_html($rs ).' '.esc_html($this->pz_HTTPMessage($rs ) ).'<br class="pz-additional-only"><input name="data[update_result]" type="text" value="'.esc_attr($data['update_result'] ).'" class="pz-additional-only" size="5" readonly="readonly"'.$cacheman_admin_edit_attr().'></td></tr>', $cacheman_allowed_html );
						?>

						<tr class="pz-man-cache-side-meta-row">
							<td colspan="2"><span><?php esc_html_e('Last Alive Check', 'pz-linkcard3' ); ?>:</span> <?php echo esc_html($this->pz_Date($this->format_datetime, $data['alive_time'] ) ); ?><br class="pz-additional-only"><input name="data[alive_time]" type="text" value="<?php echo esc_attr($data['alive_time'] ); ?>" class="pz-additional-only" size="9" readonly="readonly"<?php if (!empty($this->options['admin-mode'] ) ) : ?> ondblclick="this.readOnly=false;"<?php endif; ?> /></td>
						</tr>
						<?php
							$rs = esc_attr($data['alive_result'] );
							echo wp_kses('<tr class="pz-man-cache-side-meta-row"><td colspan="2"><span>'.esc_html__('Result Code of Alive Check', 'pz-linkcard3' ).':</span> '.esc_html($rs ).' '.esc_html($this->pz_HTTPMessage($rs ) ).'<br class="pz-additional-only"><input name="data[alive_result]" type="text" value="'.esc_attr($data['alive_result'] ).'" class="pz-additional-only" size="2" readonly="readonly"'.$cacheman_admin_edit_attr().'></td></tr>', $cacheman_allowed_html );
						?>
						<tr class="pz-additional-only pz-man-cache-side-meta-row">
							<td colspan="2"><span><?php esc_html_e('Next Alive Check', 'pz-linkcard3' ); ?>:</span> <?php echo esc_html($this->pz_Date($this->format_datetime, $data['alive_nexttime'] ) ); ?><br><input name="data[alive_nexttime]" type="text" value="<?php echo esc_attr($data['alive_nexttime'] ); ?>" size="9" readonly="readonly"<?php if (!empty($this->options['admin-mode'] ) ) : ?> ondblclick="this.readOnly=false;"<?php endif; ?> /></td>
						</tr>

						<?php
							$post_id_fields = '';
							$post_id_values = array();
							for	($post_id_index = 1; $post_id_index <= 6; $post_id_index++ ) {
								$post_id_key	=	'use_post_id'.$post_id_index;
								$post_id_value	=	intval($cacheman_get_value($post_id_key ) );
								if	($post_id_value > 0 ) {
									$post_id_values[] = $post_id_value;
								}
								$post_id_fields	.=	$cacheman_text_input($post_id_key, array(
									'value'			=>	$post_id_value > 0 ? $post_id_value : '',
									'size'			=>	5,
									'class'			=>	'pz-man-cache-post-id-input pz-additional-only',
									'readonly'		=>	true,
									'editable'		=>	true,
								) );
							}
							echo wp_kses('<tr class="pz-man-cache-side-meta-row pz-man-cache-post-id-summary-row"><td colspan="2"><span>'.esc_html__('Post ID', 'pz-linkcard3' ).':</span> '.esc_html(implode(__(', ', 'pz-linkcard3' ), $post_id_values ) ).'</td></tr>', $cacheman_allowed_html );
							echo wp_kses('<tr class="pz-additional-only pz-man-cache-post-id-row"><td colspan="2">'.$post_id_fields.'</td></tr>', $cacheman_allowed_html );
						?>
					</table>
				<?php echo wp_kses($cacheman_postbox_close(), $cacheman_allowed_html ); ?>

				<?php echo wp_kses($cacheman_postbox_open(__('SNS', 'pz-linkcard3' ) ), $cacheman_allowed_html ); ?>
					<?php
						$sns_fields	=	array(
							'sns_twitter'	=>	__('Tw:', 'pz-linkcard3' ),
							'sns_facebook'	=>	__('fb:', 'pz-linkcard3' ),
							'sns_hatena'	=>	__('B!:', 'pz-linkcard3' ),
						);
					?>
					<div class="pz-man-cache-sns-summary">
						<?php
							foreach	($sns_fields as $sns_key => $sns_label ) {
								$sns_count	=	intval($cacheman_get_value($sns_key ) );
								echo wp_kses('<span class="pz-man-cache-sns-row"><span><strong class="pz-man-cache-sns-label">'.esc_html($sns_label ).'</strong> <span class="pz-man-cache-sns-count">'.($sns_count ? esc_html(number_format($sns_count ) ) : esc_html(__('(Not yet acquired)', 'pz-linkcard3' ) ) ).'</span></span>'.$cacheman_text_input($sns_key, array(
									'size'			=>	5,
									'class'			=>	'pz-additional-only',
									'readonly'		=>	true,
									'admin_edit'	=>	true,
								) ).'</span>', $cacheman_allowed_html );
							}
						?>
					</div>
					<table class="form-table pz-man-cache-side-table">
						<tr class="pz-man-cache-side-meta-row">
							<td colspan="2"><span><?php esc_html_e('Last SNS Check', 'pz-linkcard3' ); ?>:</span> <?php echo esc_html($this->pz_Date($this->format_datetime, $data['sns_time'] ) ); ?><br class="pz-additional-only"><input name="data[sns_time]" type="text" value="<?php echo esc_attr($data['sns_time'] ); ?>" class="pz-additional-only" size="9" readonly="readonly"<?php if (!empty($this->options['admin-mode'] ) ) : ?> ondblclick="this.readOnly=false;"<?php endif; ?> /></td>
						</tr>
						<tr class="pz-additional-only pz-man-cache-side-meta-row">
							<td colspan="2"><span><?php esc_html_e('Next SNS Check', 'pz-linkcard3' ); ?>:</span> <?php echo esc_html($this->pz_Date($this->format_datetime, $data['sns_nexttime'] ) ); ?><br><input name="data[sns_nexttime]" type="text" value="<?php echo esc_attr($data['sns_nexttime'] ); ?>" size="9" readonly="readonly"<?php if (!empty($this->options['admin-mode'] ) ) : ?> ondblclick="this.readOnly=false;"<?php endif; ?> /></td>
						</tr>
					</table>
				<?php echo wp_kses($cacheman_postbox_close(), $cacheman_allowed_html ); ?>
			</div>
		</div>
		<br class="clear" />
	</div>

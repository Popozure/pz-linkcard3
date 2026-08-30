<?php
	
if (!defined('ABSPATH' ) ) {
	 exit;
}
?>
<div class="<?php echo esc_attr($page_class('pz-basic' ) ); ?>" id="pz-basic">
	<div class="pz-submit-float"><?php submit_button(); ?></div>

	<div style="display: flex; align-items: center; margin: 16px 0;">
		<img src="<?php echo esc_url($this->base_url.'assets/pz-linkcard3_logo.svg' ); ?>" height="64" alt="Pz-LinkCard3" />
		<span style="margin: 0 0 0 -8px; padding: 4px 8px; background-color: #478; border-radius: 8px; font-size: 16px; font-weight: bold; color: #f0f0f1;">ver.<?php echo esc_html($this->plugin_version ); ?></span>
	</div>

	<h2><?php echo	esc_html__('Changelog', 'pz-linkcard3' ); ?></h2>
	<div class="pz-changelog">
		<?php echo	wp_kses_post($changelog ); ?>
	</div>

	<h2><?php echo	esc_html__('Related Information', 'pz-linkcard3' ); ?></h2>
	<table class="form-table">
		<tr>
			<th scope="row"><?php echo	esc_html__('How to', 'pz-linkcard3' ).' '.esc_html__('(Japanese Only)', 'pz-linkcard3' ); ?></th>
			<td>
				<?php
				$item_link	=	$this->plugin_url;
				$item_icon	=	$this->base_url.'assets/pz-linkcard3_icon_bk.svg';
				$item_name	=	$this->plugin_name;
				$item_desc	=	'Version '.$this->plugin_version;
				pz_lkc3_intro_card($item_link, $item_icon, $item_name, $item_desc, 'pz-introduction-pzlkc' );
				?>
			</td>
		</tr>
		<tr>
			<th scope="row" rowspan="3"><?php esc_html_e('When in Trouble', 'pz-linkcard3' ); ?></th>
			<td>
				<?php
				$item_link	=	'https://wordpress.org/support/plugin/pz-linkcard/';
				$item_icon	=	$this->base_url.'img/icon_WordPress.png';
				$item_name	=	__('Pz-LinkCard Forum', 'pz-linkcard3' );
				$item_desc	=	__('This is a forum for Pz-LinkCard by the official WordPress.org website.', 'pz-linkcard3' );
				pz_lkc3_intro_card($item_link, $item_icon, $item_name, $item_desc, 'pz-introduction-wporg', 'dashicons-wordpress' );
				?>
			</td>
		</tr>
		<tr>
			<td>
				<?php
				$item_link	=	$this->author_twitter_url;
				$item_icon	=	$this->base_url.'img/icon_x.svg';
				$item_name	=	__('Popozure.', 'pz-linkcard3' ).' ('.$this->author_twitter_name.')';
				$item_desc	=	__('If you find any problems, please let us know via direct message.', 'pz-linkcard3' );
				pz_lkc3_intro_card($item_link, $item_icon, $item_name, $item_desc, 'pz-introduction-twitter' );
				?>
			</td>
		</tr>
		<tr>
			<td>
				<?php
				$item_link	=	'https://x.com/popo68k';
				$item_icon	=	$this->base_url.'img/icon_x.svg';
				$item_name	=	__('Poporon@Popozure.', 'pz-linkcard3' ).' (@popo68k)';
				$item_desc	=	__('It\'s okay here too.', 'pz-linkcard3' );
				pz_lkc3_intro_card($item_link, $item_icon, $item_name, $item_desc, 'pz-introduction-twitter' );
				?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e('Author\'s Site', 'pz-linkcard3' ); ?></th>
			<td>
				<?php
				$item_link	=	$this->author_url;
				$item_icon	=	$this->base_url.'img/popozure.png';
				$item_name	=	__('Popozure.', 'pz-linkcard3' );
				$item_desc	=	__('Poporon\'s PC Daily Diary', 'pz-linkcard3' );
				pz_lkc3_intro_card($item_link, $item_icon, $item_name, $item_desc, 'pz-introduction-pzlkc' );
				?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e('Donation', 'pz-linkcard3' ); ?></th>
			<td>
				<?php
				$item_link	=	$this->author_donate_url;
				$item_icon	=	$this->base_url.'img/icon_amazon.png';
				$item_name	=	__('Amazon', 'pz-linkcard3' );
				$item_desc	=	__('You do not have to send me a gift, but if you make your own purchases through this link, I will receive a little extra money. That helps keep me motivated.', 'pz-linkcard3' );
				pz_lkc3_intro_card($item_link, $item_icon, $item_name, $item_desc, 'pz-introduction-amazon', 'dashicons-amazon' );
				?>
			</td>
		</tr>

		<tr class="pz-additional-only">
			<th scope="row"><?php esc_html_e('Donation', 'pz-linkcard3' ); ?></th>
			<td>
				<?php
				$item_link	=	$this->author_donate_url;
				$item_icon	=	$this->base_url.'img/icon_amazon.png';
				$item_name	=	__('Amazon', 'pz-linkcard3' );
				$item_desc	=	__('If you give me a gift, it will motivate me, but even if you don\'t give me a gift, I will respond with additional features and fixes.', 'pz-linkcard3' );
				pz_lkc3_intro_card($item_link, $item_icon, $item_name, $item_desc, 'pz-introduction-amazon', 'dashicons-amazon' );
				?>
			</td>
		</tr>

	</table>
	<?php submit_button(); ?>


	<h2><?php echo	esc_html__('Basic Settings', 'pz-linkcard3' ).wp_kses_post(sprintf($help_icon, esc_attr('-basic' ) ) ); ?></h2>
	<table class="form-table">
	<tr>
		<th scope="row"><?php esc_html_e('Last Saved Settings', 'pz-linkcard3' ); ?></th>
			<td>
				<?php echo is_numeric($this->options['saved-date'] ) ? esc_html($this->pz_Date($this->format_datetime, $this->options['saved-date'] ) ) : esc_html($this->options['saved-date'] ); ?>
			</td>
		</tr>
	</table>
	<?php submit_button(); ?>


</div>

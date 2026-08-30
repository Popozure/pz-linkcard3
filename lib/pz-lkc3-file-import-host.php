<?php
	
if (!defined('ABSPATH' ) ) {
	 exit;
}
?>
<?php

global $wpdb;

$pz_lkc3_source_table = $wpdb->prefix.'pz_linkcard';

$pz_lkc3_render_import_notice = function($type, $message) {
    echo '<div class="notice notice-'.esc_attr($type ).' is-dismissible"><p><strong>'.wp_kses_post($message ).'</strong></p></div>';
};
$pz_lkc3_render_db_error_notice = function() use ($wpdb, $pz_lkc3_render_import_notice) {
    $pz_lkc3_render_import_notice('error', esc_html__('DB Access Error.', 'pz-linkcard3' ).esc_html__('(', 'pz-linkcard3' ).esc_html($wpdb->last_error ).esc_html__(')', 'pz-linkcard3' ) );
};


if ($wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $pz_lkc3_source_table)) !== $pz_lkc3_source_table) {
    $pz_lkc3_render_import_notice('error', esc_html__('Import Failure.', 'pz-linkcard3' ).' '.esc_html__('DB Access Error.', 'pz-linkcard3' ) );
    return null;
}


$pz_lkc3_source_columns = $wpdb->get_col($wpdb->prepare('DESC %i', $pz_lkc3_source_table ), 0);
if (!$pz_lkc3_source_columns || $wpdb->last_error) {
    $pz_lkc3_render_db_error_notice();
    return null;
}


$pz_lkc3_target_columns = $wpdb->get_col($wpdb->prepare('DESC %i', $this->db_card ), 0);
if (!$pz_lkc3_target_columns || $wpdb->last_error) {
    $pz_lkc3_render_db_error_notice();
    return null;
}

$pz_lkc3_clear = false;
if (
    isset($_POST['_wpnonce'] ) &&
    wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'] ) ), 'pz-cacheman' )
) {
    $pz_lkc3_clear = isset($_POST['import_host_clear'] ) ? (bool) sanitize_text_field(wp_unslash($_POST['import_host_clear'] ) ) : false;
}

if ($pz_lkc3_clear) {
    
    $pz_lkc3_result = $wpdb->query($wpdb->prepare('DELETE FROM %i', $this->db_card ) );
    if ($wpdb->last_error) {
        $pz_lkc3_render_db_error_notice();
        return null;
    }

    
    $pz_lkc3_result = $wpdb->query($wpdb->prepare('ALTER TABLE %i AUTO_INCREMENT = 1', $this->db_card ) );
    if ($wpdb->last_error) {
        $pz_lkc3_render_db_error_notice();
        return null;
    }
}

$pz_lkc3_source_column_map = array_flip($pz_lkc3_source_columns);
$pz_lkc3_target_column_map = array_flip($pz_lkc3_target_columns);
$pz_lkc3_column_aliases = array(
    'url'             => array('url', 'link_url'),
    'url_redir'       => array('url_redir', 'redirect_url', 'redir_url'),
    'site_name'       => array('site_name', 'sitename', 'site', 'blogname'),
    'title'           => array('title'),
    'excerpt'         => array('excerpt', 'description', 'description_text'),
    'charset'         => array('charset'),
    'thumbnail'       => array('thumbnail', 'thumbnail_url', 'image', 'image_url', 'og_image'),
    'site_icon'       => array('site_icon', 'siteicon', 'siteicon_url', 'favicon', 'favicon_url'),
    'no_failure'      => array('no_failure'),
    'click_count'     => array('click_count', 'click', 'clicks', 'count_click'),
    'post_id'         => array('post_id'),
    'post_date'       => array('post_date'),
    'post_modified'   => array('post_modified'),
    'post_cat'        => array('post_cat', 'post_category'),
    'alive_result'    => array('alive_result', 'alive_code', 'http_code', 'status_code'),
    'alive_time'      => array('alive_time', 'alive_date'),
    'alive_nexttime'  => array('alive_nexttime'),
    'sns_twitter'     => array('sns_twitter', 'twitter_count', 'tweet_count'),
    'sns_facebook'    => array('sns_facebook', 'facebook_count', 'fb_count'),
    'sns_hatena'      => array('sns_hatena', 'hatena_count', 'hatebu_count'),
    'sns_time'        => array('sns_time'),
    'sns_nexttime'    => array('sns_nexttime'),
    'regist_title'    => array('regist_title', 'title'),
    'regist_excerpt'  => array('regist_excerpt', 'excerpt', 'description', 'description_text'),
    'regist_charset'  => array('regist_charset', 'charset'),
    'regist_result'   => array('regist_result', 'alive_result', 'http_code', 'status_code'),
    'regist_time'     => array('regist_time', 'regist_date', 'created_time', 'created_at'),
    'update_result'   => array('update_result', 'alive_result', 'http_code', 'status_code'),
    'update_time'     => array('update_time', 'updated_time', 'updated_at'),
);

$pz_lkc3_get_value = function ($row, $names) use ($pz_lkc3_source_column_map) {
    foreach ($names as $name) {
        if (isset($pz_lkc3_source_column_map[$name]) && array_key_exists($name, $row)) {
            return $row[$name];
        }
    }
    return null;
};

$pz_lkc3_normalize_timestamp = function ($pz_lkc3_value) {
    if ($pz_lkc3_value === null || $pz_lkc3_value === '') {
        return null;
    }
    if (is_numeric($pz_lkc3_value)) {
        return intval($pz_lkc3_value);
    }
    $timestamp = strtotime($pz_lkc3_value);
    return $timestamp ? $timestamp : null;
};

$pz_lkc3_upload_dir = wp_upload_dir();
$pz_lkc3_source_image_cache_dir = trailingslashit($pz_lkc3_upload_dir['basedir']).'pz-linkcard/cache/';
$pz_lkc3_target_image_cache_dir = $this->dir_cache;

$pz_lkc3_image_copy_count = 0;
$pz_lkc3_image_convert_count = 0;
$pz_lkc3_image_existing_count = 0;
$pz_lkc3_image_missing_count = 0;
$pz_lkc3_image_failure_count = 0;

$pz_lkc3_copy_import_image = function ($image_url) use (
    $pz_lkc3_source_image_cache_dir,
    $pz_lkc3_target_image_cache_dir,
    &$pz_lkc3_image_copy_count,
    &$pz_lkc3_image_convert_count,
    &$pz_lkc3_image_existing_count,
    &$pz_lkc3_image_missing_count,
    &$pz_lkc3_image_failure_count
) {
    $image_url = esc_url_raw((string) $image_url);
    if ($image_url === '') {
        return;
    }

    $wp_filesystem = $this->pz_GetFilesystem();
    if (!$wp_filesystem) {
        $pz_lkc3_image_failure_count++;
        return;
    }

    if (!$wp_filesystem->is_dir($pz_lkc3_source_image_cache_dir)) {
        $pz_lkc3_image_missing_count++;
        return;
    }
    if (!$this->pz_EnsureDirectory($pz_lkc3_target_image_cache_dir, $wp_filesystem)) {
        $pz_lkc3_image_failure_count++;
        return;
    }

    $file_name = bin2hex(hash('sha256', $image_url, true));
    $source_path = trailingslashit($pz_lkc3_source_image_cache_dir).$file_name;
    $target_path = trailingslashit($pz_lkc3_target_image_cache_dir).$file_name;
    $target_webp = $target_path.'.webp';

    if ($wp_filesystem->exists($target_webp) && $wp_filesystem->size($target_webp) >= 12) {
        $pz_lkc3_image_existing_count++;
        return;
    }

    $source_webp = $source_path.'.webp';
    if ($wp_filesystem->exists($source_webp) && $wp_filesystem->size($source_webp) >= 12) {
        if ($wp_filesystem->copy($source_webp, $target_webp, true, FS_CHMOD_FILE)) {
            $wp_filesystem->touch($target_webp, $wp_filesystem->mtime($source_webp));
            $pz_lkc3_image_copy_count++;
        } else {
            $pz_lkc3_image_failure_count++;
        }
        return;
    }

    $source_jpeg_candidates = array(
        $source_path.'.jpeg',
        $source_path.'.jpg',
        $source_path.'jpeg',
        $source_path,
    );

    foreach ($source_jpeg_candidates as $source_jpeg) {
        if (!$wp_filesystem->exists($source_jpeg) || $wp_filesystem->size($source_jpeg) < 12) {
            continue;
        }

        if (!function_exists('imagewebp')) {
            $pz_lkc3_image_failure_count++;
            return;
        }

        $image = @imagecreatefromjpeg($source_jpeg);
        if (!$image) {
            $image_body = $wp_filesystem->get_contents($source_jpeg);
            $image = $image_body !== false ? @imagecreatefromstring($image_body) : false;
        }
        if (!$image) {
            $pz_lkc3_image_failure_count++;
            return;
        }

        $pz_lkc3_result = imagewebp($image, $target_webp);
        imagedestroy($image);

        if ($pz_lkc3_result && $wp_filesystem->exists($target_webp) && $wp_filesystem->size($target_webp) >= 12) {
            $wp_filesystem->touch($target_webp, $wp_filesystem->mtime($source_jpeg));
            $pz_lkc3_image_convert_count++;
        } else {
            $pz_lkc3_image_failure_count++;
        }
        return;
    }

    $pz_lkc3_image_missing_count++;
};

$pz_lkc3_date_columns = array('post_date', 'post_modified', 'alive_time', 'alive_nexttime', 'sns_time', 'sns_nexttime', 'regist_time', 'update_time');
$pz_lkc3_order_column = '';
foreach (array('card_id', 'id', 'url') as $pz_lkc3_candidate) {
    if (isset($pz_lkc3_source_column_map[$pz_lkc3_candidate])) {
        $pz_lkc3_order_column = $pz_lkc3_candidate;
        break;
    }
}

$pz_lkc3_read_count = 0;
$pz_lkc3_skip_count = 0;
$pz_lkc3_success_count = 0;
$pz_lkc3_batch_size = 200;
$pz_lkc3_offset = 0;

do {
    if ($pz_lkc3_order_column) {
        
        $pz_lkc3_records = $wpdb->get_results(
            $wpdb->prepare('SELECT * FROM %i ORDER BY %i ASC LIMIT %d OFFSET %d', $pz_lkc3_source_table, $pz_lkc3_order_column, $pz_lkc3_batch_size, $pz_lkc3_offset),
            ARRAY_A
        );
    } else {
        
        $pz_lkc3_records = $wpdb->get_results(
            $wpdb->prepare('SELECT * FROM %i LIMIT %d OFFSET %d', $pz_lkc3_source_table, $pz_lkc3_batch_size, $pz_lkc3_offset),
            ARRAY_A
        );
    }
    if ($wpdb->last_error) {
        $pz_lkc3_render_db_error_notice();
        return null;
    }

    foreach ($pz_lkc3_records as $pz_lkc3_record) {
        $pz_lkc3_read_count++;
        $pz_lkc3_import = array();

        foreach ($pz_lkc3_target_columns as $pz_lkc3_target_column) {
            if ($pz_lkc3_target_column === 'card_id' || $pz_lkc3_target_column === 'url_key') {
                continue;
            }

            if (isset($pz_lkc3_source_column_map[$pz_lkc3_target_column]) && array_key_exists($pz_lkc3_target_column, $pz_lkc3_record)) {
                $pz_lkc3_import[$pz_lkc3_target_column] = $pz_lkc3_record[$pz_lkc3_target_column];
                continue;
            }

            if (isset($pz_lkc3_column_aliases[$pz_lkc3_target_column])) {
                $pz_lkc3_value = $pz_lkc3_get_value($pz_lkc3_record, $pz_lkc3_column_aliases[$pz_lkc3_target_column]);
                if ($pz_lkc3_value !== null) {
                    $pz_lkc3_import[$pz_lkc3_target_column] = $pz_lkc3_value;
                }
            }
        }

        foreach ($pz_lkc3_date_columns as $pz_lkc3_date_column) {
            if (array_key_exists($pz_lkc3_date_column, $pz_lkc3_import)) {
                $pz_lkc3_import[$pz_lkc3_date_column] = $pz_lkc3_normalize_timestamp($pz_lkc3_import[$pz_lkc3_date_column]);
            }
        }

        $pz_lkc3_import = array_intersect_key($pz_lkc3_import, $pz_lkc3_target_column_map);

        if (empty($pz_lkc3_import['url'])) {
            $pz_lkc3_skip_count++;
            continue;
        }

        $pz_lkc3_result = $this->pz_SetCache($pz_lkc3_import);
        if (is_array($pz_lkc3_result) && isset($pz_lkc3_result['url']) && $pz_lkc3_result['url'] === $pz_lkc3_import['url']) {
            $pz_lkc3_success_count++;
            if (!empty($pz_lkc3_import['site_icon'])) {
                $pz_lkc3_copy_import_image($pz_lkc3_import['site_icon']);
            }
            if (!empty($pz_lkc3_import['thumbnail'])) {
                $pz_lkc3_copy_import_image($pz_lkc3_import['thumbnail']);
            }
        } else {
            $pz_lkc3_skip_count++;
        }
    }

    $pz_lkc3_offset += $pz_lkc3_batch_size;
} while (count($pz_lkc3_records) === $pz_lkc3_batch_size);

if ($pz_lkc3_success_count) {
    $pz_lkc3_render_import_notice(
        'success',
        esc_html__('Import Successful.', 'pz-linkcard3' ).esc_html__('(', 'pz-linkcard3' ).
        esc_html__('Read:', 'pz-linkcard3' ).esc_html(number_format_i18n($pz_lkc3_read_count ) ).' '.
        esc_html__('Success:', 'pz-linkcard3' ).esc_html(number_format_i18n($pz_lkc3_success_count ) ).' '.
        esc_html__('Skip:', 'pz-linkcard3' ).esc_html(number_format_i18n($pz_lkc3_skip_count ) ).' '.
        esc_html__('Images:', 'pz-linkcard3' ).
        esc_html__('Copied:', 'pz-linkcard3' ).esc_html(number_format_i18n($pz_lkc3_image_copy_count ) ).' '.
        esc_html__('Converted:', 'pz-linkcard3' ).esc_html(number_format_i18n($pz_lkc3_image_convert_count ) ).' '.
        esc_html__('Existing:', 'pz-linkcard3' ).esc_html(number_format_i18n($pz_lkc3_image_existing_count ) ).' '.
        esc_html__('Missing:', 'pz-linkcard3' ).esc_html(number_format_i18n($pz_lkc3_image_missing_count ) ).' '.
        esc_html__('Failed:', 'pz-linkcard3' ).esc_html(number_format_i18n($pz_lkc3_image_failure_count ) ).
        esc_html__(')', 'pz-linkcard3' )
    );
} else {
    $pz_lkc3_render_import_notice(
        'error',
        esc_html__('Import Failure.', 'pz-linkcard3' ).esc_html__('(', 'pz-linkcard3' ).
        esc_html__('Read:', 'pz-linkcard3' ).esc_html(number_format_i18n($pz_lkc3_read_count ) ).' '.
        esc_html__('Skip:', 'pz-linkcard3' ).esc_html(number_format_i18n($pz_lkc3_skip_count ) ).' '.
        esc_html__('Images:', 'pz-linkcard3' ).
        esc_html__('Copied:', 'pz-linkcard3' ).esc_html(number_format_i18n($pz_lkc3_image_copy_count ) ).' '.
        esc_html__('Converted:', 'pz-linkcard3' ).esc_html(number_format_i18n($pz_lkc3_image_convert_count ) ).' '.
        esc_html__('Existing:', 'pz-linkcard3' ).esc_html(number_format_i18n($pz_lkc3_image_existing_count ) ).' '.
        esc_html__('Missing:', 'pz-linkcard3' ).esc_html(number_format_i18n($pz_lkc3_image_missing_count ) ).' '.
        esc_html__('Failed:', 'pz-linkcard3' ).esc_html(number_format_i18n($pz_lkc3_image_failure_count ) ).
        esc_html__(')', 'pz-linkcard3' )
    );
}

echo '<a href="'.esc_url($this->cacheman_url ).'" class="pz-man-file-button button button-primary">' . esc_html__('Return to Cache Manager', 'pz-linkcard3') . '</a>';

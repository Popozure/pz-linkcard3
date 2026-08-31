<?php
	
if (!defined('ABSPATH' ) ) {
	 exit;
}
?>
<?php

global $wpdb;



check_admin_referer('pz-cacheman' );


$target_columns = $wpdb->get_col($wpdb->prepare('DESC %i', $this->db_card ), 0);
if (!$target_columns || $wpdb->last_error) {
    echo '<div class="notice notice-error is-dismissible"><p><strong>'.esc_html__('DB Access Error.', 'pz-linkcard3' ).esc_html__('(', 'pz-linkcard3' ).esc_html($wpdb->last_error ).esc_html__(')', 'pz-linkcard3' ).'</strong></p></div>';
    return null;
}

$skip_columns = array('card_id', 'url_key', 'use_post_id1', 'use_post_id2', 'use_post_id3', 'use_post_id4', 'use_post_id5', 'use_post_id6');
$import_columns = array_values(array_diff($target_columns, $skip_columns));
$target_column_map = array_flip($target_columns);
$import_column_map = array_flip($import_columns);

$column_aliases = array(
    'url'             => array('url', 'link_url', ),
    'url_redir'       => array('url_redir', 'url_redirect', 'redirect_url', 'redir_url', ),
    'site_name'       => array('site_name', 'sitename', 'site', 'blogname', ),
    'title'           => array('title', ),
    'excerpt'         => array('excerpt', 'excerpt_text', 'description', 'description_text', ),
    'charset'         => array('charset', ),
    'thumbnail'       => array('thumbnail', 'thumbnail_url', 'image', 'image_url', 'og_image', ),
    'site_icon'       => array('site_icon', 'siteicon', 'siteicon_url', 'site_icon_url', 'favicon', 'favicon_url', ),
    'no_failure'      => array('no_failure', ),
    'click_count'     => array('click_count', 'click', 'clicks', 'count_click', ),
    'post_id'         => array('post_id', ),
    'post_date'       => array('post_date', ),
    'post_modified'   => array('post_modified', ),
    'post_cat'        => array('post_cat', 'post_category', ),
    'alive_result'    => array('alive_result', 'alive_code', ),
    'alive_time'      => array('alive_time', 'alive_date', ),
    'alive_nexttime'  => array('alive_nexttime', 'alive_nextdate', ),
    'sns_twitter'     => array('sns_twitter', 'sns_tw', 'twitter_count', 'tweet_count', 'sns_x', 'x_count', ),
    'sns_facebook'    => array('sns_facebook', 'sns_fb', 'facebook_count', 'fb_count'),
    'sns_hatena'      => array('sns_hatena', 'sns_hb', 'hatena_count', 'hatebu_count'),
    'sns_time'        => array('sns_time', 'sns_date', ),
    'sns_nexttime'    => array('sns_nexttime', 'sns_nextdate', ),
    'regist_title'    => array('regist_title', ),
    'regist_excerpt'  => array('regist_excerpt', ),
    'regist_charset'  => array('regist_charset', ),
    'regist_result'   => array('regist_result', 'http_code', 'status_code', ),
    'regist_time'     => array('regist_time', 'regist_date', ),
    'update_result'   => array('update_result', 'alive_result', ),
    'update_time'     => array('update_time', 'updated_time', 'update_date', 'updated_date', 'updated_at', ),
);

$normalize_column = function ($name) {
    $name = preg_replace('/^\xEF\xBB\xBF/', '', (string) $name);
    $name = trim($name);
    $name = strtolower($name);
    $name = str_replace(array('-', ' ', '.', ':'), '_', $name);
    $name = preg_replace('/_+/', '_', $name);
    return trim($name, '_');
};

$get_value = function ($record, $names) use ($normalize_column) {
    foreach ($names as $name) {
        $key = $normalize_column($name);
        if (array_key_exists($key, $record)) {
            return $record[$key];
        }
    }
    return null;
};

$normalize_timestamp = function ($value) {
    if ($value === null || $value === '') {
        return null;
    }
    if (is_numeric($value)) {
        return intval($value);
    }
    $timestamp = strtotime($value);
    return $timestamp ? $timestamp : null;
};

$upload_error = isset($_FILES['import_file']['error']) ? absint($_FILES['import_file']['error'] ) : UPLOAD_ERR_NO_FILE;

$temp_path = (isset($_FILES['import_file']['tmp_name']) && is_string($_FILES['import_file']['tmp_name'] ) ) ? sanitize_text_field(wp_unslash($_FILES['import_file']['tmp_name'] ) ) : '';
$clear = isset($_POST['import_clear'] ) ? (bool) sanitize_text_field(wp_unslash($_POST['import_clear'] ) ) : false;

if ($upload_error !== UPLOAD_ERR_OK || !$temp_path || !is_uploaded_file($temp_path)) {
    echo '<div class="notice notice-error is-dismissible"><p><strong>'.esc_html__('Import File Not Found.', 'pz-linkcard3' ).'</strong></p></div>';
    return null;
}

$wp_filesystem = $this->pz_GetFilesystem();
if (!$wp_filesystem) {
    echo '<div class="notice notice-error is-dismissible"><p><strong>'.esc_html__('Import File Open Error.', 'pz-linkcard3' ).'</strong></p></div>';
    return null;
}

$csv_text = $wp_filesystem->get_contents($temp_path);
if ($csv_text === false) {
    echo '<div class="notice notice-error is-dismissible"><p><strong>'.esc_html__('Import File Read Error.', 'pz-linkcard3' ).'</strong></p></div>';
    return null;
}

$csv_rows = str_getcsv($csv_text, "\n");
$csv_header_line = array_shift($csv_rows);
$csv_header = is_string($csv_header_line) ? str_getcsv($csv_header_line) : false;
if ($csv_header === false) {
    echo '<div class="notice notice-error is-dismissible"><p><strong>'.esc_html__('Import File Header Error.', 'pz-linkcard3' ).'</strong></p></div>';
    return null;
}

$csv_columns = array();
foreach ($csv_header as $index => $column_name) {
    $csv_columns[$index] = $normalize_column($column_name);
}

if ($clear) {
    
    $result = $wpdb->query($wpdb->prepare('DELETE FROM %i', $this->db_card ) );
    if ($wpdb->last_error) {
        echo '<div class="notice notice-error is-dismissible"><p><strong>'.esc_html__('DB Access Error.', 'pz-linkcard3' ).esc_html__('(', 'pz-linkcard3' ).esc_html($wpdb->last_error ).esc_html__(')', 'pz-linkcard3' ).'</strong></p></div>';
        return null;
    }

    
    $result = $wpdb->query($wpdb->prepare('ALTER TABLE %i AUTO_INCREMENT = 1', $this->db_card ) );
    if ($wpdb->last_error) {
        echo '<div class="notice notice-error is-dismissible"><p><strong>'.esc_html__('DB Access Error.', 'pz-linkcard3' ).esc_html__('(', 'pz-linkcard3' ).esc_html($wpdb->last_error ).esc_html__(')', 'pz-linkcard3' ).'</strong></p></div>';
        return null;
    }
}

$date_columns = array('post_date', 'post_modified', 'alive_time', 'alive_nexttime', 'sns_time', 'sns_nexttime', 'regist_time', 'update_time');
$read_count = 0;
$skip_count = 0;
$success_count = 0;

foreach ($csv_rows as $csv_row) {
    if ($csv_row === null || trim((string) $csv_row) === '') {
        continue;
    }
    $row = str_getcsv($csv_row);
    $read_count++;
    $record = array();
    foreach ($csv_columns as $index => $column_name) {
        if ($column_name === '') {
            continue;
        }
        $record[$column_name] = isset($row[$index]) ? $row[$index] : '';
    }

    $import = array();
    foreach ($import_columns as $target_column) {
        if (array_key_exists($target_column, $record)) {
            $import[$target_column] = $record[$target_column];
            continue;
        }

        if (isset($column_aliases[$target_column])) {
            $value = $get_value($record, $column_aliases[$target_column]);
            if ($value !== null) {
                $import[$target_column] = $value;
            }
        }
    }

    foreach ($date_columns as $date_column) {
        if (array_key_exists($date_column, $import)) {
            $import[$date_column] = $normalize_timestamp($import[$date_column]);
        }
    }

    $import = array_intersect_key($import, $target_column_map);
    $import = array_intersect_key($import, $import_column_map);

    if (empty($import['url'])) {
        $skip_count++;
        continue;
    }

    $result = $this->pz_SetCache($import);
    if (is_array($result) && isset($result['url']) && $result['url'] === $import['url']) {
        $success_count++;
    } else {
        $skip_count++;
    }
}

if ($success_count) {
    echo '<div class="notice notice-success is-dismissible"><p><strong>'.esc_html__('Import Successful.', 'pz-linkcard3' ).esc_html__('(', 'pz-linkcard3' ).esc_html__('Read:', 'pz-linkcard3' ).esc_html(number_format_i18n($read_count ) ).' '.esc_html__('Success:', 'pz-linkcard3' ).esc_html(number_format_i18n($success_count ) ).' '.esc_html__('Skip:', 'pz-linkcard3' ).esc_html(number_format_i18n($skip_count ) ).esc_html__(')', 'pz-linkcard3' ).'</strong></p></div>';
} else {
    echo '<div class="notice notice-error is-dismissible"><p><strong>'.esc_html__('Import Failure.', 'pz-linkcard3' ).esc_html__('(', 'pz-linkcard3' ).esc_html__('Read:', 'pz-linkcard3' ).esc_html(number_format_i18n($read_count ) ).' '.esc_html__('Skip:', 'pz-linkcard3' ).esc_html(number_format_i18n($skip_count ) ).esc_html__(')', 'pz-linkcard3' ).'</strong></p></div>';
}

echo '<a href="'.esc_url($this->cacheman_url ).'" class="pz-man-file-button button button-primary">' . esc_html__('Return to Cache Manager', 'pz-linkcard3') . '</a>';

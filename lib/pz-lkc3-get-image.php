<?php
	
if (!defined('ABSPATH' ) ) {
	exit;
}

$image_url = is_string($image_url ) ? trim($image_url ) : '';
if (empty($image_url ) ) {
	return null;
}

$is_site_image_url = function ($url) {
	$url_parts = wp_parse_url($url );
	if (!is_array($url_parts ) || empty($url_parts['host'] ) ) {
		return false;
	}

	$url_host = strtolower(trim($url_parts['host'], "[] \t\n\r\0\x0B." ) );
	$url_port = isset($url_parts['port'] ) ? intval($url_parts['port'] ) : null;
	$url_path = '/' . ltrim($url_parts['path'] ?? '/', '/' );

	foreach (array(home_url('/' ), site_url('/' ) ) as $site_url ) {
		$site_parts = wp_parse_url($site_url );
		if (!is_array($site_parts ) || empty($site_parts['host'] ) ) {
			continue;
		}

		$site_host = strtolower(trim($site_parts['host'], "[] \t\n\r\0\x0B." ) );
		$site_port = isset($site_parts['port'] ) ? intval($site_parts['port'] ) : null;
		$site_path = '/' . trim($site_parts['path'] ?? '', '/' );
		$site_path = '/' === $site_path ? '/' : trailingslashit($site_path );

		if ($url_host !== $site_host || $url_port !== $site_port ) {
			continue;
		}
		if (0 === strpos($url_path, $site_path ) ) {
			return true;
		}
	}

	return false;
};

if ($is_site_image_url($image_url ) ) {
	return esc_url($image_url );
}

if (!wp_http_validate_url($image_url ) ) {
	return null;
}

$is_cache_image_url = function ($url) {
	$url_path = wp_parse_url($url, PHP_URL_PATH );
	return (
		0 === strpos($url, $this->url_cache ) ||
		(is_string($url_path ) && 0 === strpos($url_path, '/wp-content/uploads/pz-linkcard3/cache' ) )
	);
};

if ($this->pz_IsLocalAddress($image_url ) && !$is_cache_image_url($image_url ) ) {
	return null;
}

$omit_url_list = array(
	'https://s0.wp.com/i/blank.jpg',
);
if (in_array($image_url, $omit_url_list, true ) ) {
	return null;
}

$wp_filesystem = $this->pz_GetFilesystem();
if (!$wp_filesystem ) {
	return null;
}
if (!$this->pz_EnsureDirectory($this->dir_cache, $wp_filesystem ) ) {
	return null;
}

$file_name = bin2hex(hash('sha256', esc_url_raw($image_url ), true ) );
$file_path = $this->dir_cache . $file_name;
$file_url  = $this->url_cache . $file_name;

if ($wp_filesystem->exists($file_path ) ) {
	$wp_filesystem->move($file_path, $file_path . '.jpeg', true );
}

if (!$force ) {
	if ($wp_filesystem->exists($file_path . '.webp' ) ) {
		if ($wp_filesystem->size($file_path . '.webp' ) < 12 ) {
			return null;
		}

		$file_url .= '.webp';
		if (true === $stamp ) {
			$file_url .= '?' . gmdate('Ymd-His', $wp_filesystem->mtime($file_path . '.webp' ) );
		}
		return esc_url($file_url );
	}

	if ($wp_filesystem->exists($file_path . '.jpeg' ) ) {
		if ($wp_filesystem->size($file_path . '.jpeg' ) < 12 ) {
			$wp_filesystem->put_contents($file_path . '.webp', '', FS_CHMOD_FILE );
			$wp_filesystem->delete($file_path . '.jpeg' );
			return null;
		}

		$file_time_jpeg = $wp_filesystem->mtime($file_path . '.jpeg' );
		$image_jpeg     = @imagecreatefromjpeg($file_path . '.jpeg' );
		if ($image_jpeg ) {
			$result = imagewebp($image_jpeg, $file_path . '.webp' );
			imagedestroy($image_jpeg );

			if ($result && $wp_filesystem->exists($file_path . '.webp' ) && $wp_filesystem->size($file_path . '.webp' ) >= 12 ) {
				$wp_filesystem->touch($file_path . '.webp', $file_time_jpeg );
				$wp_filesystem->delete($file_path . '.jpeg' );

				$file_url .= '.webp';
				if (true === $stamp ) {
					$file_url .= '?' . gmdate('Ymd-His', $wp_filesystem->mtime($file_path . '.webp' ) );
				}
				return esc_url($file_url );
			}
		}

		$file_url .= '.jpeg';
		if (true === $stamp ) {
			$file_url .= '?' . gmdate('Ymd-His', $wp_filesystem->mtime($file_path . '.jpeg' ) );
		}
		return esc_url($file_url );
	}
}

if ($readonly ) {
	return null;
}

$make_absolute_url = function ($location, $base_url) {
	if (class_exists('WP_Http' ) && method_exists('WP_Http', 'make_absolute_url' ) ) {
		return WP_Http::make_absolute_url($location, $base_url );
	}
	return $location;
};

$response = null;
for ($redirect_count = 0; $redirect_count < 5; $redirect_count++ ) {
	if ($this->pz_IsLocalAddress($image_url ) && !$is_cache_image_url($image_url ) ) {
		return null;
	}

	$response = wp_safe_remote_get(
		$image_url,
		array(
			'timeout'             => 10,
			'redirection'         => 0,
			'reject_unsafe_urls'  => true,
			'limit_response_size' => 1024 * 1024 * 5,
			'user-agent'          => sanitize_text_field($this->options['user-agent'] ?? '' ),
		)
	);
	if (is_wp_error($response ) ) {
		return null;
	}

	$status_code = wp_remote_retrieve_response_code($response );
	if ($status_code < 300 || $status_code >= 400 ) {
		break;
	}

	$location = wp_remote_retrieve_header($response, 'location' );
	if (is_array($location ) ) {
		$location = end($location );
	}
	$location = trim($location );
	if (!$location ) {
		return null;
	}

	$next_url = esc_url_raw($make_absolute_url($location, $image_url ) );
	if (!$next_url || (!$is_site_image_url($next_url ) && !wp_http_validate_url($next_url ) ) ) {
		return null;
	}
	if ($this->pz_IsLocalAddress($next_url ) && !$is_cache_image_url($next_url ) ) {
		return null;
	}

	$image_url = $next_url;
}
if (!$response ) {
	return null;
}

$status_code = wp_remote_retrieve_response_code($response );
if ($status_code >= 300 ) {
	$wp_filesystem->put_contents($file_path . '.webp', '', FS_CHMOD_FILE );
	return null;
}

$body = wp_remote_retrieve_body($response );
if (empty($body ) ) {
	$wp_filesystem->put_contents($file_path . '.webp', '', FS_CHMOD_FILE );
	return null;
}

$image = @imagecreatefromstring($body );
if (false === $image ) {
	$wp_filesystem->put_contents($file_path . '.webp', '', FS_CHMOD_FILE );
	return null;
}
if (function_exists('imagepalettetotruecolor' ) ) {
	imagepalettetotruecolor($image );
}
imagealphablending($image, false );
imagesavealpha($image, true );

$image_width  = @imagesx($image );
$image_height = @imagesy($image );
if (false === $image_width || false === $image_height || $image_width < 8 || $image_height < 8 ) {
	$wp_filesystem->put_contents($file_path . '.webp', '', FS_CHMOD_FILE );
	imagedestroy($image );
	return null;
}

$image_size = intval($this->options['image-size'] );
if ($image_size < 8 ) {
	$image_size = 32;
}

$new_width  = $image_size;
$new_height = $image_size;
if ($image_width > $image_height ) {
	$new_height = intval($image_height * ($new_width / $image_width ) );
} elseif ($image_width < $image_height ) {
	$new_width = intval($image_width * ($new_height / $image_height ) );
}

if ($new_width <= 1 || $new_height <= 1 ) {
	$wp_filesystem->put_contents($file_path . '.webp', '', FS_CHMOD_FILE );
	imagedestroy($image );
	return null;
}

if (!function_exists('imagewebp' ) ) {
	$wp_filesystem->put_contents($file_path . '.webp', '', FS_CHMOD_FILE );
	imagedestroy($image );
	return null;
}

$image_pallet = imagecreatetruecolor($new_width, $new_height );
if (!$image_pallet ) {
	$wp_filesystem->put_contents($file_path . '.webp', '', FS_CHMOD_FILE );
	imagedestroy($image );
	return null;
}

imagealphablending($image_pallet, false );
imagesavealpha($image_pallet, true );
$image_pallet_bg = imagecolorallocatealpha($image_pallet, 0, 0, 0, 127 );
imagefill($image_pallet, 0, 0, $image_pallet_bg );
imagecopyresampled($image_pallet, $image, 0, 0, 0, 0, $new_width, $new_height, $image_width, $image_height );

if (!imagewebp($image_pallet, $file_path . '.webp' ) ) {
	$wp_filesystem->put_contents($file_path . '.webp', '', FS_CHMOD_FILE );
	imagedestroy($image_pallet );
	imagedestroy($image );
	return null;
}
imagedestroy($image_pallet );
imagedestroy($image );

$file_url .= '.webp';
if (true === $stamp ) {
	$file_url .= '?' . gmdate('Ymd-His', $wp_filesystem->mtime($file_path . '.webp' ) );
}
return esc_url($file_url );

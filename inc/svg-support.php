<?php 
// Allow svg mimetype for uploads
add_filter('upload_mimes', 'cc_mime_types');
function cc_mime_types($mimes){
  	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
}

// Check Mime Types
add_filter('wp_check_filetype_and_ext', 'wl_svgs_upload_check', 10, 4);
function wl_svgs_upload_check($checked, $file, $filename, $mimes) {
	if (!$checked['type']) {
		$check_filetype		= wp_check_filetype($filename, $mimes);
		$ext				= $check_filetype['ext'];
		$type				= $check_filetype['type'];
		$proper_filename	= $filename;
		if ($type && 0 === strpos($type, 'image/') && $ext !== 'svg') {
			$ext = $type = false;
		}
		$checked = compact('ext','type','proper_filename');
	}
	return $checked;
}

// Fixes uploads for these 2 version of WordPress.
add_filter('wp_check_filetype_and_ext', 'wl_svgs_allow_svg_upload', 10, 4);
function wl_svgs_allow_svg_upload($data, $file, $filename, $mimes) {
	global $wp_version;
	if ( $wp_version !== '4.7.1' || $wp_version !== '4.7.2' ) {
		return $data;
	}
	$filetype = wp_check_filetype( $filename, $mimes );
	return [
		'ext'				=> $filetype['ext'],
		'type'				=> $filetype['type'],
		'proper_filename'	=> $data['proper_filename']
	];
}

// Show svg in uploads
add_action('admin_init', 'wl_svgs_display_thumbs');
function wl_svgs_display_thumbs() {
	function wl_svgs_thumbs_filter($content) {
		return apply_filters('final_output', $content);
	}
	ob_start('wl_svgs_thumbs_filter');
	add_filter('final_output', 'wl_svgs_final_output');
	function wl_svgs_final_output($content) {
		$content = str_replace(
			'<# } else if ( \'image\' === data.type && data.sizes && data.sizes.full ) { #>',
			'<# } else if ( \'svg+xml\' === data.subtype ) { #>
				<img class="details-image" src="{{ data.url }}" draggable="false" />
				<# } else if ( \'image\' === data.type && data.sizes && data.sizes.full ) { #>',
				$content
				);
		$content = str_replace(
			'<# } else if ( \'image\' === data.type && data.sizes ) { #>',
			'<# } else if ( \'svg+xml\' === data.subtype ) { #>
				<div class="centered">
					<img src="{{ data.url }}" class="thumbnail" draggable="false" />
				</div>
				<# } else if ( \'image\' === data.type && data.sizes ) { #>',
				$content);
		return $content;
	}
} ?>
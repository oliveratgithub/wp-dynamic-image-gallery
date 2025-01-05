<?php
/*
 * Plugin Name:			Dynamic Image List
 * Plugin URI:			https://github.com/oliveratgithub/wp-dynamic-image-gallery
 * Description:			This plugin adds a shortcode to list images from Media Library on Pages and Posts.
 * Version:				1.1.0
 * Requires at least:	6.7
 * Requires PHP:		8.1
 * Author:				Oliver
 * Author URI:			https://github.com/oliveratgithub
 *
 * Text Domain:			dynamic-image-list
 */

/** Exit when file is opened in webbrowser */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Shortcode */
function get_all_images_shortcode( $atts ) {
	// Convert shortcode attributes to parameters
	$params = shortcode_atts([
		'limit' => -1,
		'size' => 'medium',
		'link' => true,
		'random' => false
	], $atts);

	// Capture the output instead of printing directly
	ob_start();
	get_all_images(
		$params['limit'],
		$params['size'],
		$params['link'],
		filter_var($params['random'], FILTER_VALIDATE_BOOLEAN)
	);
	return ob_get_clean();
}
add_shortcode('show_images', 'get_all_images_shortcode');

function get_all_images( $limit=-1, $size='medium', $link=true, $random=false ) {
	$args = [
		'post_type' => 'attachment',
		'post_mime_type' => 'image',
		'posts_per_page' => esc_attr( $limit ),
		'post_status' => 'inherit',
		'orderby' => $random ? 'rand' : 'date',
		'order' => 'DESC'
	];

	$images = get_posts($args);

	if (empty($images)) {
		return '<p>No images found in the Media Library.</p>';
	}

	?>
	<figure class="wp-block-gallery has-nested-images columns-3 is-cropped wp-block-gallery-dynamic is-layout-flex wp-block-gallery-is-layout-flex">
		<?php foreach($images as $image) { ?>
		<figure class="wp-block-image size-<?php echo esc_attr($size); ?>">
			<?php
			if ($link) {
				echo wp_get_attachment_link(
					$image->ID,
					$size,	// Default: thumbnail
					true,	// Permalink
					false,	// Icon
					false,	// Text
					[ 		// Attributes
						'class' => 'wp-image-' . $image->ID,
						'data-id' => $image->ID,
						'loading' => 'lazy',
						'decoding' => 'async'
					]
				);
			} else {
				echo wp_get_attachment_image(
					$image->ID,
					$size,	// Default: thumbnail
					false,	// Icon
					[		// Attributes
						'class' => 'wp-image-' . $image->ID,
						'data-id' => $image->ID,
						'loading' => 'lazy',
						'decoding' => 'async'
					]
				);
			}
			?>
		</figure>
		<?php } ?>
	</figure>
<?php
}

/** Enqueue styles only when shortcode is used */
function dynamic_image_list_enqueue_styles() {
	wp_register_style(
		'dynamic-image-list-styles',
		plugins_url('style.css', __FILE__),
		[],
		'1.0.1'
	);
}
add_action('init', 'dynamic_image_list_enqueue_styles');

function dynamic_image_list_shortcode_styles() {
	wp_enqueue_style('dynamic-image-list-styles');
}
add_action('wp_enqueue_scripts', 'dynamic_image_list_shortcode_styles');

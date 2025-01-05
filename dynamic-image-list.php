<?php
/*
 * Plugin Name:			Dynamic Image Gallery
 * Plugin URI:			https://github.com/oliveratgithub/wp-dynamic-image-gallery
 * Description:			This plugin adds a shortcode to list images from Media Library on Pages and Posts.
 * Version:				1.0.0
 * Requires at least:	6.2
 * Requires PHP:		7.4
 * Author:				Oliver
 * Author URI:			https://github.com/oliveratgithub
 *
 * Text Domain:			dynamic-image-gallery
 */

/** Exit when file is opened in webbrowser */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Shortcode */
if ( !is_admin() ) // Disabled in Post/Page Editor
	{
	function get_all_images_shortcode( $atts ){
		// $atts = shortcode_atts([
		// 	'limit' => -1
		// 	,'size' => 'thumbnail'
		// ], $atts);
		get_all_images( $atts );
	}
	add_shortcode('show_images', 'get_all_images_shortcode');
}

function get_all_images( $limit=-1, $size='medium' ){
	$args = array(
		'post_type' => 'attachment',
		'numberposts' => esc_attr( $limit ),
		'post_status' => null,
		'post_parent' => null,
		'post_mime_type' => 'image'
		//'post_mime_type' => array('image/png', 'image/x-png', 'image/jpeg', 'image/jpg', 'video/mp4') // you can add to array 'image/gif' to display gif images, or 'video/mp4' or any mime type.
		//'offset' => 2
	);
	$images = get_posts($args);

	?>
	<figure class="wp-block-gallery alignwide has-nested-images columns-default is-cropped wp-block-gallery-1 is-layout-flex">
	<?php foreach($images as $image){ ?>
		<figure class="wp-block-image size-large"><a href="<?php echo get_attachment_link($image->ID); ?>">
			<?php echo wp_get_attachment_image($image->ID, esc_attr( $size ), false, [ 'loading' => 'lazy', 'decoding' => 'async', 'alt' => get_the_title($image->ID) ]); ?>
		</a></figure>
	<?php } ?>
	</figure>
<?php }

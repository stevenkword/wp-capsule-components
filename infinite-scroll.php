<?php
/*
Component Name: Capsule Infinite Scroll
Description: Makes Capsule Neverending
Author: Steven K Word
Version: 1.0
Author URI: http://stevenword.com/
*/

define('CAPSULE_INFINITE_SCROLL', true);

function capsule_has_infinite_scroll() {
	return (defined('CAPSULE_INFINITE_SCROLL') && CAPSULE_INFINITE_SCROLL);
}

class Capsule_Infinite_Scroll {

	/**
	 * Define and register singleton
	 */
	private static $instance = false;
	public static function instance() {
		if( !self::$instance )
			self::$instance = new Capsule_Infinite_Scroll;
		return self::$instance;
	}

	private function __clone() { }

	/**
	 * Register actions and filters
	 *
	 * @since version 1.7.1.0
	 * @uses add_action(), add_filter()
	 * @return null
	 */
	function __construct() {

		// Initialize
		add_action( 'after_setup_theme', array( $this, 'action_after_setup_theme' ) );

		// Include JS
		add_action( 'wp_footer', array( $this, 'action_wp_footer' ) );

		// Render new posts
		add_action( 'infinite_scroll_render', array( $this, 'action_infinite_scroll_render' ) );
	}

	/**
	 * Register basic support for Infinite Scroll
	 *
	 * For more options, see http://jetpack.me/support/infinite-scroll/
	 */
	function action_after_setup_theme() {
		add_theme_support( 'infinite-scroll', array(
			'container' => 'content',
			'footer'    => false
		) );
	}

	function action_wp_footer(){
		?>
		<!-- Infinite Scroll JS -->
		<script type="text/javascript">
			jQuery(document).ready(function($){

				/* one */
				jQuery(document).ready(function($) {
					$.each( $('.home-page .post-content, .archive .post-content'), function( key, value ) {
						var $href = $(this).find('.title-link').attr('href');
						var $h1 = $(this).find('h1').html();
						var $new_html = '<a href="' + $href + '">' + $h1 + '</a>';
						$(this).find('h1').html($new_html);
					});
				});

				/* two */
				$(document.body).on('post-load', function (a) {
					console.log(a);
					// Highlight
					Capsule.highlightCodeSyntax();
					// New posts have been added to the page.
					$.each( $('.infinite-wrap .post-content'), function( key, value ) {
						// Drop Menu
						console.log('key: ' + key);
						console.log( 'value' + value);
						var $article  = $(this).parent('article');
						Capsule.postExpandable($article);
						// Make links outs of titles
						var $href = $(this).find('.title-link').attr('href');
						var $h1 = $(this).find('h1').first().html();
						var $new_html = '<a href="' + $href + '">' + $h1 + '</a>';
						$(this).find('h1').first().html($new_html);
					});
				});
			});
		</script>
		<?php
	}

	/**
	 * Set the code to be rendered on for calling posts,
	 * hooked to template parts when possible.
	 *
	 * Note: must define a loop.
	 */
	function action_infinite_scroll_render(){
		while( have_posts() ) {
			the_post();
			include( get_template_directory() . '/ui/views/excerpt.php' );
		}
	}
}
Capsule_Infinite_Scroll::instance();
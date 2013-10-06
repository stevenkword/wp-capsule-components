<?php
/**
 * @package Capsule BuddyPress
 * @version 1.7.1.0
 */
/*
Plugin Name: CapsuleBuddy
Plugin URI: http://wordpress.org/extend/plugins/wp-capsule-buddy/
Description: Makes Capsule Social
Author: Steven K Word
Version: 1.0
Author URI: http://stevenword.com/
*/

define('CAPSULE_BUDDY', true);

function is_capsule_buddy() {
	return (defined('CAPSULE_BUDDY') && CAPSULE_BUDDY);
}

function remove(){

	remove_filter( 'bp_get_activity_action',                'wptexturize' );
	remove_filter( 'bp_get_activity_content_body',          'wptexturize' );
	remove_filter( 'bp_get_activity_content',               'wptexturize' );
	remove_filter( 'bp_get_activity_parent_content',        'wptexturize' );
	remove_filter( 'bp_get_activity_latest_update',         'wptexturize' );
	remove_filter( 'bp_get_activity_latest_update_excerpt', 'wptexturize' );

	remove_filter( 'bp_get_activity_action',                'convert_smilies' );
	remove_filter( 'bp_get_activity_content_body',          'convert_smilies' );
	remove_filter( 'bp_get_activity_content',               'convert_smilies' );
	remove_filter( 'bp_get_activity_parent_content',        'convert_smilies' );
	remove_filter( 'bp_get_activity_latest_update',         'convert_smilies' );
	remove_filter( 'bp_get_activity_latest_update_excerpt', 'convert_smilies' );

	remove_filter( 'bp_get_activity_action',                'convert_chars' );
	remove_filter( 'bp_get_activity_content_body',          'convert_chars' );
	remove_filter( 'bp_get_activity_content',               'convert_chars' );
	remove_filter( 'bp_get_activity_parent_content',        'convert_chars' );
	remove_filter( 'bp_get_activity_latest_update',         'convert_chars' );
	remove_filter( 'bp_get_activity_latest_update_excerpt', 'convert_chars' );

	remove_filter( 'bp_get_activity_action',                'wpautop' );
	remove_filter( 'bp_get_activity_content_body',          'wpautop' );
	remove_filter( 'bp_get_activity_content',               'wpautop' );
	remove_filter( 'bp_get_activity_feed_item_description', 'wpautop' );

remove_filter( 'bp_get_activity_content_body',          'make_clickable', 9 );
remove_filter( 'bp_get_activity_content',               'make_clickable', 9 );
remove_filter( 'bp_get_activity_content',               'stripslashes_deep' );
remove_filter( 'bp_get_activity_content_body',          'stripslashes_deep' );
remove_filter( 'bp_get_activity_content',               'bp_activity_make_nofollow_filter' );
remove_filter( 'bp_get_activity_content_body',          'bp_activity_make_nofollow_filter' );


}

function test( $blurb ) {

	global $wp_filter;

	//var_dump( $wp_filter[ 'bp_get_activity_content_body' ] );

	oomph_error_log( '[Activity]', $blurb );
	//var_dump( $blurb );
	return $blurb;
}

class CapsuleBuddy {

	/**
	 * Define and register singleton
	 */
	private static $instance = false;
	public static function instance() {
		if( !self::$instance )
			self::$instance = new CapsuleBuddy;
		return self::$instance;
	}

	/**
	 * Clone
	 */
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
		add_action( 'init', array( $this, 'action_init' ) );

		// Remove the editor warning
		//add_action( 'admin_init', 'action_admin_init' );

		// Filter the page title
		add_filter( 'capsule_page_title', array( $this, 'filter_capsule_page_title' ) );

		// Add nav items to the end of the main navigation
		add_action( 'capsule_main_nav_after', array( $this, 'action_capsule_main_nav_after' ) );

		// Add menu items to the beginning of the post menu
		add_action( 'capsule_post_menu_before', array( $this, 'action_capsule_post_menu_before' ) );

		// Apply the Capsule Markdown content filters to the BuddyPress Activity Stream
		add_filter( 'bp_get_activity_content_body', 'capsule_the_content_markdown' );
		add_filter( 'bp_get_activity_content_body', 'test', 99 );
		add_filter( 'the_content', 'test', 99 );

		// Show the user avatar
		add_filter( 'capsule_show_avatar', array($this, 'filter_capsule_show_avatar' ) );
	}

	/**
	 * Initialize
	 *
	 * @since version 1.7.1.0
	 * @param string $title
	 * @return null
	 */
	function action_init() {
		add_theme_support( 'custom-background' );
	}

	/**
	 * Remove the editor warning
	 *
	 * @since version 1.7.1.0
	 * @return null
	 */
	function action_admin_init() {
		global $wp_filter;
		//var_dump( $wp_filter );
		remove_action( 'edit_form_after_title', 'capsule_wp_editor_warning', 99 );
	}

	/**
	 * Filter the page title
	 *
	 * @since version 1.7.1.0
	 * @param string $title
	 * @return null
	 */
	function filter_capsule_page_title( $title ) {
		if( is_singular() ) {
			$post = get_queried_object();
			$title = sprintf(__( '%s', 'capsule' ), esc_html( $post->post_title ) );
		}
		if( 'Home' == $title )
			$title = get_bloginfo( 'name' );
		return $title;
	}

	/**
	 * Add nav items to the end of the main navigation
	 *
	 * @since version 1.7.1.0
	 * @return null
	 */
	function action_capsule_main_nav_after() {
		?>
		<li><a href="<?php echo esc_url( home_url( 'activity' ) ); ?>" class="icon">A</a></li>
		<li><a href="<?php echo esc_url( home_url( 'members' ) ); ?>" class="icon">M</a></li>
		<li><a href="<?php echo esc_url( home_url( 'members/admin/messages' ) ); ?>" class="icon">P</a></li>
		<?php
	}

	/**
	 * Add menu items to the beginning of the post menu
	 *
	 * @since version 1.7.1.0
	 * @return null
	 */
	function action_capsule_post_menu_before(){
		//echo 'test';
	}

	/**
	 * Show the user avatar to the right of the excerpt
	 *
	 * @since version 1.7.1.0
	 * @return null
	 */
	function filter_capsule_show_avatar( $show_avatar ) {
		return true;
	}

}
CapsuleBuddy::instance();
<?php
/*
Component Name: Capsule Theme Customizer
Description: Makes Capsule Pretty
Author: Steven K Word
Version: 1.7.1.0
Author URI: http://stevenword.com/
*/

class Capsule_Theme_Customizer {
	// Define and register singleton
	private static $instance = false;
	public static function instance() {
		if( !self::$instance )
			self::$instance = new Capsule_Theme_Customizer;
		return self::$instance;
	}

	private function __clone() { }

	/**
	 * Register actions
	 * @uses add_action, add_filter
	 * @return null
	 */
	function __construct() {

		// Adds the live preview JS on the customizer screen
		add_action( 'customize_preview_init', array( $this, 'action_customize_preview_init' ) );

		// Adds the Theme Customizer menu item to the appearance options
		add_action( 'admin_menu', array( $this, 'action_admin_menu' ), 99 );

		// Allows you define new Theme Customizer sections, settings, and controls
		//add_action( 'customize_register', array( $this, 'action_customize_register' ) );

		// Used for enqueuing custom Javascript on the Theme Customizer screen
		add_action( 'customize_preview_init', array( $this, 'action_customize_preview_init' ) );

		//Output custom CSS to live site
		//add_action( 'wp_head' , array( $this , 'action_wp_head' ) );

		// Add nav items to the end of the main navigation
		add_action( 'capsule_main_nav_after', array( $this, 'action_capsule_main_nav_after' ) );

	}

	/**
	 * Enqueue the live preview JS on the customizer screen
	 *
	 */
	function action_customize_preview_init() {
		$stylesheet_directory = get_stylesheet_directory_uri();
		wp_enqueue_script( 'theme-customizer', $stylesheet_directory . '/js/theme-customizer.js', array( 'jquery','customize-preview' ), 1.0, false );
	}

	/**
	 * Edits the Appearance Menu in the admin utility
	 *
	 */
	function action_admin_menu() {
		// Add new menu items
		$url = 'customizer.php?url=' . home_url();
		add_submenu_page( 'themes.php', 'Customize Theme', 'Customize Theme', 'manage_options', 'customize.php' );
	}

	/**
	 * Defines new Theme Customizer sections, settings, and controls
	 *
	 */
	public function action_customize_register( $wp_customize ) {

		// Remove unwanted default sections
		$wp_customize->remove_section( 'nav' );
		$wp_customize->remove_section( 'title_tagline' );
		$wp_customize->remove_section( 'static_front_page' );
		$wp_customize->remove_section( 'background_image' );
		$wp_customize->remove_section( 'header_image' );

		// Remove unwanted settings
		$wp_customize->remove_setting( 'blogname' );
		$wp_customize->remove_setting( 'blogdescription' );
		$wp_customize->remove_setting( 'header_textcolor' );
		$wp_customize->remove_setting( 'header_image' );
		$wp_customize->remove_setting( 'header_image_data' );
		$wp_customize->remove_setting( 'background_image' );
		$wp_customize->remove_setting( 'background_image_thumb' );
		$wp_customize->remove_setting( 'background_color' );
		$wp_customize->remove_setting( 'background_repeat' );
		$wp_customize->remove_setting( 'background_position_x' );
		$wp_customize->remove_setting( 'background_attachment' );
		$wp_customize->remove_setting( 'nav_menu_locations[primary]' );
		$wp_customize->remove_setting( 'show_on_front' );
		$wp_customize->remove_setting( 'page_on_front' );
		$wp_customize->remove_setting( 'page_for_posts' );

		// Remove unwanted controls
		$wp_customize->remove_control( 'blogname' );
		$wp_customize->remove_control( 'blogdescription' );
		$wp_customize->remove_control( 'header_textcolor' );
		$wp_customize->remove_control( 'header_image' );
		$wp_customize->remove_control( 'display_header_textcolor' );
		$wp_customize->remove_control( 'page_for_posts' );
		$wp_customize->remove_control( 'page_on_front' );
		$wp_customize->remove_control( 'show_on_front' );
		$wp_customize->remove_control( 'nav_menu_locations[primary]' );
		$wp_customize->remove_control( 'display_header_text' );
		$wp_customize->remove_control( 'background_image' );
		$wp_customize->remove_control( 'background_color' );
		$wp_customize->remove_control( 'background_repeat' );
		$wp_customize->remove_control( 'background_position_x' );
		$wp_customize->remove_control( 'background_attachment' );

		// Create new sections
		$wp_customize->add_section( 'logo_images', array(
				'title'		=> __( 'Logo Images', 'lin' ),
			'priority'	=> 70,
		) );

		$wp_customize->add_section( 'background_images', array(
				'title'		=> __( 'Background Images', 'lin' ),
			'priority'	=> 75,
		) );

		/* Alternate logo */
		$wp_customize->add_setting( 'logo_full' , array(
				'transport'	=> 'postMessage',
		) );

		$wp_customize->add_setting( 'logo_retracted' , array(
				'transport'	=> 'postMessage',
		) );

		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'logo_full', array(
			'label'		=> __( 'Logo Full (300x80)', 'lin' ),
			'section'	=> 'logo_images',
			'settings'	=> 'logo_full',
		) ) );

		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'logo_retracted', array(
			'label'		=> __( 'Header Retracted (180x40)', 'lin' ),
			'section'	=> 'logo_images',
			'settings'	=> 'logo_retracted',
		) ) );

		/*  Background colors */
		$wp_customize->add_setting( 'body_background_color', array(
				'default'	=> '#ffffff',
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'	=> 'postMessage',
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'body_background_color', array(
			'label'		=> __( 'Background Color', 'lin' ),
			'section'	=> 'colors',
			'settings'	=> 'body_background_color',
		) ) );

		/*  Content colors */
		$wp_customize->add_setting( 'content_background_color', array(
				'default'	=> '#ffffff',
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'	=> 'postMessage',
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'content_background_color', array(
			'label'		=> __( 'Content Background Color', 'lin' ),
			'section'	=> 'colors',
			'settings'	=> 'content_background_color',
		) ) );

		/* Background Images */
		$wp_customize->add_setting( 'background_repeater_image' , array(
				'transport'	=> 'postMessage',
		) );

		$wp_customize->add_setting( 'background_header_image' , array(
				'transport'	=> 'postMessage',
		) );

		$wp_customize->add_setting( 'background_wallpaper_image' , array(
				'transport'	=> 'postMessage',
		) );

		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'background_repeater_image', array(
			'label'		=> __( 'Background Repeater Image', 'lin' ),
			'section'	=> 'background_images',
			'settings'	=> 'background_repeater_image',
		) ) );

		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'background_header_image', array(
			'label'		=> __( 'Background Header Image', 'lin' ),
			'section'	=> 'background_images',
			'settings'	=> 'background_header_image',
		) ) );

		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'background_wallpaper_image', array(
			'label'        => __( 'Background Wallpaper Image', 'lin' ),
			'section'    => 'background_images',
			'settings'   => 'background_wallpaper_image',
		) ) );

		// We can also change built-in settings by modifying properties. For instance, let's make some stuff use live preview JS...
		$wp_customize->get_setting( 'body_background_color' )->transport = 'postMessage';
	}

	/**
	* This will output the custom WordPress settings to the live theme's WP head.
	*
	* Used by hook: 'wp_head'
	*
	* @see add_action('wp_head',$func)
	* @since version 1.7.1.0
	*/
	public function action_wp_head() {
		ob_start();

		$mods = get_theme_mods();
		$mods = apply_filters( 'theme_customizer_mods', $mods );

		/*
		body.custom-background {
			background-color: #ccc;
			background-repeat: no-repeat;
			background-position: top left;
			background-attachment: fixed;
			background-size: cover;
		}
		*/

		?>
		<!--Theme Customizer CSS-->
		<style type="text/css">
			<?php lin_generate_css('body', 'background', 'background_repeater_image', $mods['background_repeater_image'], 'transparent url("', '") fixed repeat-x 0 0' ); ?>
			<?php lin_generate_css('body','background-color', 'body_background_color', $mods['body_background_color'], ''); ?>
			<?php lin_generate_css('.background-header', 'background', 'background_header_image', $mods['background_header_image'], 'transparent url("', '") fixed no-repeat center top' ); ?>
			<?php lin_generate_css('.background-wallpaper', 'background', 'background_wallpaper_image', $mods['background_wallpaper_image'], 'transparent url("', '") fixed no-repeat center top' ); ?>
			<?php lin_generate_css('#main', 'background-color', 'content_background_color', $mods['content_background_color'], '' ); ?>
		</style>
		<!--/Theme Customizer CSS-->
		<?php
		$customizer_styles = ob_get_clean();
		echo $customizer_styles;
	}

	/**
	 * Add nav items to the end of the main navigation
	 *
	 * @since version 1.7.1.0
	 * @return null
	 */
	function action_capsule_main_nav_after() {
		?>
		<li><a href="<?php echo esc_url( admin_url( 'customize.php' ) ) . '?url=' . esc_url( home_url('/') ) ; ?>" class="icon">C</a></li>
		<?php
	}
}

Capsule_Theme_Customizer::instance();

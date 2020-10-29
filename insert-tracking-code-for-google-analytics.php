<?php

/*
 * Plugin Name: Insert Tracking Code for Google Analytics
 * Plugin URI: https://github.com/TravisNice/Insert-Google-Analytics-Tracking-Code
 * Description: Inserts the Google Analytics tracking code, requiring only the website's Tracking ID
 * Version: 1.0.9
 * Author: Travis Nice
 * Author URI: https://www.nice.id.au/design
 * License: GPL2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wordpress
 * Domain Path: /languages
 */



defined ( 'ABSPATH' ) or die ( 'Get out of my plugin!' );



if ( !defined( 'INSERT_GOOGLE_ANALYTICS_PLUGIN_VERSION' ) ) {

	define( 'INSERT_GOOGLE_ANALYTICS_PLUGIN_VERSION', '1.0.9' );

}



/**
 * The installation routines that are run when the plugin is activated
 *
 * @package Google Analytics
 * @since 1.0.0
 */



function insert_google_analytics_plugin_activation() {

	/**
        * @package Google Analytics
        * @since 1.0.9
        */

	$option = 'insert_google_analytics_plugin_version';
	if ( get_option( $option ) === false ) {

		update_option( $option, INSERT_GOOGLE_ANALYTICS_PLUGIN_VERSION );

	}

	/**
        * @package Google Analytics
        * @since 1.0.9
        */

	$option = 'insert_google_analytics_plugin_tracking_type';
	if ( get_option( $option ) === false ) {

		update_option( $option, 'global' );

	}

	/**
        * @package Google Analytics
        * @since 1.0.0
        */

	$option = 'insert_google_analytics_plugin_tracking_ID';
	if ( get_option( $option ) === false ) {

		update_option( $option, "" );

	}

	/**
        * @package Google Analytics
        * @since 1.0.0
        */

	$file = __FILE__;
	$callback = 'insert_google_analytics_plugin_uninstall_callback';
	register_uninstall_hook( $file, $callback );

	/**
        * @package Google Analytics
        * @since 1.0.0
        */

	$file = __FILE__;
	$callback = 'insert_google_analytics_plugin_deactivate_callback';
	register_deactivation_hook( $file, $callback );

}

register_activation_hook( __FILE__, 'insert_google_analytics_plugin_activation' );



/**
 * Check if the plugin was updated, and if so, run the activation function to
 * to make sure that the new options are available.
 *
 * @package Google Analytics
 * @since 1.0.9
 */



function insert_google_analytics_plugin_check_version() {

	$option = 'insert_google_analytics_plugin_version';
	if ( get_option( $option ) !== INSERT_GOOGLE_ANALYTICS_PLUGIN_VERSION ) {

		update_option( $option, INSERT_GOOGLE_ANALYTICS_PLUGIN_VERSION );

		insert_google_analytics_plugin_activation();

	}

}

add_action( 'plugins_loaded', 'insert_google_analytics_plugin_check_version' );



/**
 * The callback functions for register_uninstall_hook() and
 * register_deactivation_hook() above.
 *
 * @package Google Analytics
 * @since 1.0.0
 */



function insert_google_analytics_plugin_uninstall_callback () {

	 /** @since 1.0.9 */
	$option = 'insert_google_analytics_plugin_version';
	delete_option( $option );

	 /** @since 1.0.9 */
	$option = 'insert_google_analytics_plugin_tracking_type';
	delete_option( $option );

	 /** @since 1.0.0 */
	$option = 'insert_google_analytics_plugin_tracking_ID';
	delete_option( $option );

}

function insert_google_analytics_plugin_deactivate_callback () {

	 /** @since 1.0.9 */
	$option_group = 'insert_google_analytics_plugin_option_group';
	$option_name = 'insert_google_analytics_plugin_tracking_type';
	unregister_setting( $option_group, $option_name );

	 /** @since 1.0.0 */
	$option_group = 'insert_google_analytics_plugin_option_group';
	$option_name = 'insert_google_analytics_plugin_tracking_ID';
	unregister_setting( $option_group, $option_name );

}



/**
 * Add a settings link to the right of the activate and deactivate links on the
 * installed plugins page.
 *
 * @package Google Analytics
 * @since 1.0.0
 */



function insert_google_analytics_plugin_settings_link( $links ) {

	$settings_link = array(
		'<a href="' . admin_url( 'options-general.php?page=insert_google_analytics_plugin_options_menu' ) . '">Settings</a>',
	);

	$merged_links = array_merge(
		$settings_link,
		$links
	);

	return $merged_links;

}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'insert_google_analytics_plugin_settings_link' );



/**
 * Add a submenu to the WordPress Settings menu
 *
 * @package Google Analytics
 * @since 1.0.0
 */



function insert_google_analytics_plugin_menu() {

	$page_title = 'Google Analytics';
	$menu_title = 'Google Analytics';
	$capability = 'manage_options';
	$menu_slug  = 'insert_google_analytics_plugin_options_menu';
	$function   = 'insert_google_analytics_plugin_options_page';
	$position   = null;
	add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function, $position );

}

add_action( 'admin_menu', 'insert_google_analytics_plugin_menu' );



/**
 * This is the callback function from add_options_page() above
 *
 * @package Google Analytics
 * @since 1.0.0
 */



function insert_google_analytics_plugin_options_page() {

	/*
	 * Ensure that only people who are authorised to manage options are
	 * allowed to access the page
	 */

	if ( !current_user_can( 'manage_options' ) ) {

		wp_die( __( 'You do not have the required privileges to access this page.' ) );

	}

	echo '<div class="wrap">';
	echo '<h1>Google Analytics</h1>';
	echo '<form method="post" action="options.php">';

	$option_group = 'insert_google_analytics_plugin_option_group';
	settings_fields( $option_group );

	$page = 'insert_google_analytics_plugin_options_menu';
	do_settings_sections( $page );

	submit_button();

	echo '</form>';
	echo '</div>';

}



/**
 * Whitelist and setup our options
 *
 * @package Google Analytics
 * @since 1.0.0
 */



function insert_google_analytics_plugin_option_settings() {

	 /** @since 1.0.9 */
	$option_group = 'insert_google_analytics_plugin_option_group';
	$option_name = 'insert_google_analytics_plugin_tracking_type';
	$args = array(
		'type' => 'string',
		'description' => 'Provides a choice between Universal Analytics and Global Site Tag.',
		'sanitize_callback' => 'insert_google_analytics_plugin_sanitise_type',
		'show_in_rest' => FALSE,
		'default' => "global",
	);
	register_setting( $option_group, $option_name, $args );

	 /** @since 1.0.0 */
	$option_group = 'insert_google_analytics_plugin_option_group';
	$option_name = 'insert_google_analytics_plugin_tracking_ID';
	$args = array(
		'type' => 'string',
		'description' => 'Makes the tracking ID persistent.',
		'sanitize_callback' => 'insert_google_analytics_plugin_sanitise_text',
		'show_in_rest' => FALSE,
		'default' => "",
	);
	register_setting( $option_group, $option_name, $args );

	 /** @since 1.0.0 */
	$id = 'insert_google_analytics_plugin_tracking_ID_section';
	$title = 'Tracking ID';
	$callback = 'insert_google_analytics_plugin_render_tracking_ID_section';
	$page = 'insert_google_analytics_plugin_options_menu';
	add_settings_section( $id, $title, $callback, $page );

	 /** @since 1.0.9 */
	$id = 'insert_google_analytics_plugin_tracking_type_field';
	$title = 'Tracking Type';
	$callback = 'insert_google_analytics_plugin_render_tracking_type_field';
	$page = 'insert_google_analytics_plugin_options_menu';
	$section = 'insert_google_analytics_plugin_tracking_ID_section';
	add_settings_field( $id, $title, $callback, $page, $section );

	 /** @since 1.0.0 */
	$id = 'insert_google_analytics_plugin_tracking_ID_field';
	$title = 'Tracking ID';
	$callback = 'insert_google_analytics_plugin_render_tracking_ID_field';
	$page = 'insert_google_analytics_plugin_options_menu';
	$section = 'insert_google_analytics_plugin_tracking_ID_section';
	add_settings_field( $id, $title, $callback, $page, $section );

}

add_action( 'admin_init', 'insert_google_analytics_plugin_option_settings' );



/**
 * Callback to render the section and field above
 *
 * @package Google Analytics
 * @since 1.0.0
 */



 /** @since 1.0.0 */
function insert_google_analytics_plugin_render_tracking_ID_section() {

	echo "<p>You can find your Tracking ID from the Admin section of your Google Analytics Console. You may wish to follow <a href=\"https://support.google.com/analytics/answer/1032385\" rel=\"external\">Google's instructions</a> if you don't know where it is.</p>";

}

 /** @since 1.0.9 */
function insert_google_analytics_plugin_render_tracking_type_field() {

	$trackingType = get_option( 'insert_google_analytics_plugin_tracking_type' );

	switch ( $trackingType ) {

		case 'global':
			echo "<input type=\"radio\" id=\"global\" name=\"tracking_type\" value=\"global\"  checked>";
			echo "<label for=\"global\">Global Site Tag</label><br />";

			echo "<input type=\"radio\" id=\"universal\" name=\"tracking_type\" value=\"universal\">";
			echo "<label for=\"universal\">Universal Analytics</label><br />";

			break;


		case 'universal':
			echo "<input type=\"radio\" id=\"global\" name=\"tracking_type\" value=\"global\">";
			echo "<label for=\"global\">Global Site Tag</label><br />";

			echo "<input type=\"radio\" id=\"universal\" name=\"tracking_type\" value=\"universal\" checked>";
			echo "<label for=\"universal\">Universal Analytics</label><br />";

			break;

		default:
			echo "<input type=\"radio\" id=\"global\" name=\"tracking_type\" value=\"global\">";
			echo "<label for=\"global\">Global Site Tag</label><br />";

			echo "<input type=\"radio\" id=\"universal\" name=\"tracking_type\" value=\"universal\">";
			echo "<label for=\"universal\">Universal Analytics</label><br />";

			break;

	}

}

 /** @since 1.0.0 */
function insert_google_analytics_plugin_render_tracking_ID_field() {

	$trackingID = get_option( 'insert_google_analytics_plugin_tracking_ID' );

	echo "<input type=\"text\" id=\"insert_google_analytics_plugin_tracking_ID_field\" name=\"insert_google_analytics_plugin_tracking_ID\" value=\"" . $trackingID . "\">";

}



/**
 * Sanitise the input from the sanitize_callback above
 *
 * @package Google Analytics
 * @since 1.0.0
 */



function insert_google_analytics_plugin_sanitise_text( $input ) {

	$newValue = "";

	if ( isset( $input ) ) {
		$newValue = sanitize_text_field( $input );
	}

	return $newValue;

}



/**
 * Sanitise the input from the sanitize_callback above
 *
 * @package Google Analytics
 * @since 1.0.9
 */



function insert_google_analytics_plugin_sanitise_type( $input ) {

	return $_POST[ "tracking_type" ];

}



/**
 * Insert the code block into the site's footer.
 *
 * @package Google Analytics
 * @since 1.0.0
 */



function insert_google_analytics_plugin_render_footer() {

	$trackingID = get_option( 'insert_google_analytics_plugin_tracking_ID' );

	if ( isset ( $trackingID ) === true && $trackingID !== '' ) {

		/** @since 1.0.9 */
		$trackingType = get_option( 'insert_google_analytics_plugin_tracking_type' );

		switch ( $trackingType ) {

			case 'universal':
				echo "<!-- Google Analytics --><script>(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');ga('create', '" . $trackingID . "', 'auto');ga('send', 'pageview');</script><!-- End Google Analytics -->";
				break;

			case 'global':
				echo "<!-- Global site tag (gtag.js) - Google Analytics --><script async src=\"https://www.googletagmanager.com/gtag/js?id=" . $trackingID . "\"></script><script>window.dataLayer = window.dataLayer || [];function gtag(){dataLayer.push(arguments);}gtag('js', new Date());gtag('config', '". $trackingID ."');</script>";
				break;

		}

	}

}

add_action( 'wp_footer', 'insert_google_analytics_plugin_render_footer' );

?>

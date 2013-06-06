<?php
/*
Plugin Name: Date Set
Plugin URI: https://google.co.uk
Description: Add end date to posts.
Version: 1.0
Author: Test
Author URI: http://google.co.uk
License:

  Copyright 2013
  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as 
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
  
*/

class WordPress_jQuery_Date_Picker {
	
	/*--------------------------------------------*
	 * Constructor
	 *--------------------------------------------*/

	/**
	 * Initializes the plugin by setting localization, filters, and administration functions.
	 *
	 * @version		1.0
     * @since 		1.0
	 */
	 
	 public function __construct() {
		
		// Load plugin text domain
		add_action( 'init', array( $this, 'plugin_textdomain' ) );
		
		// Register admin Stylesheets
		add_action( 'admin_print_styles', array( $this, 'register_admin_styles' ) );
		
		// Register admin JavaScript
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );
		
		// Register the date meta box
		add_action( 'add_meta_boxes', array( $this, 'add_date_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_the_date' ) );
		
		// Display the date in the post
		add_action( 'the_content', array( $this, 'prepend_the_date' ) );
		 
	 } // end __construct

	/*---------------------------------------------*
	 * Localization, JavaScripts, Stylesheets, etc.
	 *---------------------------------------------*/
	
	/**
	 * Loads the plugin text domain for translation
	 *
	 * @version		1.0
     * @since 		1.0
	 */
	public function plugin_textdomain() {
		load_plugin_textdomain( 'wp-jquery-date-picker', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
	} // end plugin_textdomain
	
	/**
	 * Registers and enqueues admin-specific styles.
	 *
	 * @version		1.0
     * @since 		1.0
	 */
	public function register_admin_styles() {
	
		wp_enqueue_style( 'jquery-ui-datepicker', 'http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css' );
		wp_enqueue_style( 'wp-jquery-date-picker', plugins_url( 'WordPress-jQuery-Date-Picker/css/admin.css' ) );	
		wp_enqueue_style( 'wp-jquery-datetime-picker', plugins_url( 'WordPress-jQuery-Date-Picker/css/timepicker.css' ) );	
		
	} // end register_admin_styles

	/**
	 * Registers and enqueues admin-specific JavaScript.
	 *
	 * @version		1.0
	 * @since 		1.0
	 */	
	public function register_admin_scripts() {
	
		wp_enqueue_script( 'jquery-ui-datepicker' );
		//wp_enqueue_script( 'wp-jquery', 'http://code.jquery.com/jquery-1.9.1.min.js' );
		wp_enqueue_script( 'wp-jquery-migrate', 'http://code.jquery.com/jquery-migrate-1.2.1.js' );
		wp_enqueue_script( 'wp-jquery-ui', 'http://code.jquery.com/ui/1.10.3/jquery-ui.min.js'  );
		wp_enqueue_script( 'wp-jquery-date-picker', plugins_url( 'WordPress-jQuery-Date-Picker/js/admin.js' ) );
		wp_enqueue_script( 'wp-jquery-time-picker', plugins_url( 'WordPress-jQuery-Date-Picker/js/jquery-ui-timepicker.js' ) );
		
	} // end register_admin_scripts
	
	/*---------------------------------------------*
	 * Core Functions
	 *---------------------------------------------*/
	 
	 /**
	  * Registers the meta box for displaying the 'Date' option in the post editor.
	  *
	  * @version	1.0
	  * @since 		1.0
	  */
	 public function add_date_meta_box() {

		 add_meta_box(
		 	'the_date',
		 	__( 'Fixture End Date', 'wp-jquery-date-picker' ),
		 	array( $this, 'the_date_display' ),
		 	'post',
		 	'side',
		 	'high'
		 );

	 } // end add_date_meta_box
	 
	 /**
	  * Renders the user interface for completing the project in its associated meta box.
	  *
	  * @version	1.0
	  * @since 		1.0
	  */
	 public function the_date_display( $post ) {
	
		 wp_nonce_field( plugin_basename( __FILE__ ), 'wp-jquery-date-picker-nonce' );
	
		 echo '<input type="text" id="datepicker" name="the_date" value="' . get_post_meta( $post->ID, 'the_date', true ) . '" />';
	
	 } // end the_date_display
	 
	 /**
	  * Saves the project completion data for the incoming post ID.
	  *
	  * @param		int		The current Post ID.
	  * @version	1.0
	  * @since 		1.0
	  */
	 public function save_the_date( $post_id ) {
		 
		 // If the user has permission to save the meta data...
		 if( $this->user_can_save( $post_id, 'wp-jquery-date-picker-nonce' ) ) { 
		 
		 	// Delete any existing meta data for the owner
			if( get_post_meta( $post_id, 'the_date' ) ) {
				delete_post_meta( $post_id, 'the_date' );
			} // end if
			update_post_meta( $post_id, 'the_date', strip_tags( $_POST[ 'the_date' ] ) );			
			 
		 } // end if
		 
	 } // end save_the_date 
	 
	 /**
	  * Saves the project completion data for the incoming post ID.
	  *
	  * @param		int		The current Post ID.
	  * @version	1.0
	  * @since 		1.0
	  */ 
	  public function prepend_the_date( $content ) {
	
		// If the post meta isn't empty for `the_date`, then render it in the content
		if( 0 != ( $the_date = get_post_meta( get_the_ID(), 'the_date', true ) ) ) {
			$content = '<p>' . $the_date . '</p>' . $content;
		} // end if
		
		return $content;
	 
	} // end prepend_the_date
	 
 	/*---------------------------------------------*
	 * Helper Functions
	 *---------------------------------------------*/
	 
	 /**
	  * Determines whether or not the current user has the ability to save meta data associated with this post.
	  *
	  * @param		int		$post_id	The ID of the post being save
	  * @param		bool				Whether or not the user has the ability to save this post.
	  * @version	1.0
	  * @since		1.0
	  */
	 private function user_can_save( $post_id, $nonce ) {
		
	    $is_autosave = wp_is_post_autosave( $post_id );
	    $is_revision = wp_is_post_revision( $post_id );
	    $is_valid_nonce = ( isset( $_POST[ $nonce ] ) && wp_verify_nonce( $_POST[ $nonce ], plugin_basename( __FILE__ ) ) ) ? true : false;
	    
	    // Return true if the user is able to save; otherwise, false.
	    return ! ( $is_autosave || $is_revision) && $is_valid_nonce;

	 } // end user_can_save
	 
} // end class

function date_install(){
	$args = array(
			'post_type' => 'post',
			'meta_query' => array(array(
            	'key' => 'the_date',
            	'compare' => 'NOT EXISTS'
        	),),
    	);

	$posts = new WP_Query($args);	
	foreach($posts->get_posts() as $post){
		add_metadata( 'post', $post->ID, 'the_date', '' );
	}
}

// On activation set meta values
register_activation_hook (__FILE__, 'date_install');

add_action( 'admin_init', 'jquery_admin' );

function jquery_admin() {

    global $concatenate_scripts;

    $concatenate_scripts = false;
    wp_deregister_script( 'jquery' );
    wp_register_script('wp-jquery', 'http://code.jquery.com/jquery-1.9.1.min.js', false, '1.9.1');
  	wp_enqueue_script('wp-jquery');
	wp_enqueue_script( 'wp-jquery', 'http://code.jquery.com/jquery-1.9.1.min.js' );
	
	//wp_deregister_script('jquery');
  	//wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js', false, '1.7.2');
  	//wp_enqueue_script('jquery');
}

new WordPress_jQuery_Date_Picker();
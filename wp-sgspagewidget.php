<?php
/*
Plugin Name: WP-SGSPageWidget
Plugin URI: http://www.stegasoft.de/
Description: Create custom content for every single page that will be shown individually in the sidebar depending on which page is displayed (widget included)
Author: Stephan Gaertner
Version: 1.1
Author URI: http://www.stegasoft.de/
*/





//===== SOME CUSTOM VARS ======================================
$sgspage_vars = array("menu_orderby" => "post_title",              //menu_order, post_title etc., for more see WordPress reference
                      "menu_order" => "ASC"                        //ASC, DESC
                      );




//=============================================================
//===== NOTHING ELSE TO DO FROM HERE ==========================
//=============================================================

define('WPSGSPAGEWID_URLPATH', plugins_url()."/".plugin_basename( dirname(__FILE__)));





//==== FRONTEND HEADER CODE ======================================

function wp_sgspagewidget_head() {
  wp_enqueue_style("sgspagewidget_css", WPSGSPAGEWID_URLPATH.'/togtext.css.php' );
  wp_enqueue_script("sgspagewidget_js", WPSGSPAGEWID_URLPATH.'/togtext.js.php',array('jquery'));
}
add_action('wp_head', 'wp_sgspagewidget_head');


//===== clear all plugin data =====================================
register_deactivation_hook(__FILE__, 'wp_pagewidget_clear_options');
function wp_pagewidget_clear_options() {
  global $wpdb;

  $befehl = "DELETE FROM ".$wpdb->prefix."postmeta WHERE meta_key LIKE '_wp_pagewidget_meta_value_key%'";
  $result = $wpdb->get_results($befehl);
}




//======================================================================
//===== Add custom box in page editor ==================================
//======================================================================
/** Adds a box to the main column on the Post and Page edit screens. **/
function wp_pagewidget_add_meta_box() {

  //$screens = array( 'post', 'page' );
  $screens = array( 'page' );

  foreach ( $screens as $screen ) {
    add_meta_box(
                 'wp_pagewidget_sectionid',
                  __( 'SGSPage Widget', 'wp_pagewidget_textdomain' ),
                 'wp_pagewidget_meta_box_callback',
                 $screen
    );
  }
}
add_action( 'add_meta_boxes', 'wp_pagewidget_add_meta_box' );


/**
 * Prints the box content.
 * @param WP_Post $post The object for the current post/page.
 */
function wp_pagewidget_meta_box_callback( $post ) {

  // Add a nonce field so we can check for it later.
  wp_nonce_field( 'wp_pagewidget_save_meta_box_data', 'wp_pagewidget_meta_box_nonce' );


  //===== Including Page Text Widget =======================================
  echo '<div class="meta-box-sortables ui-sortable">'.
        '<div class="postbox closed">'.
         '<button class="handlediv button-link" aria-expanded="false" type="button">'.
          '<span class="screenreader-text"></span>'.
          '<span class="toggle-indicator" aria-hidden="true"></span>'.
         '</button>'.
         '<h2 class="hndl ui-sortable-handle" style="font-weight:bold;">Text-Widget</h2>'.
         '<div class="inside" style="border-top:solid 1px #dfdfdf; padding-top:10px;">';

         include "sgs-page-widget-text.php";

  echo   '</div>'.
        '</div>'.

       '</div>';


  //===== Including Page Subpage Widget =======================================
  echo '<div class="meta-box-sortables ui-sortable">'.
        '<div class="postbox closed">'.
         '<button class="handlediv button-link" aria-expanded="false" type="button">'.
          '<span class="screenreader-text"></span>'.
          '<span class="toggle-indicator" aria-hidden="true"></span>'.
         '</button>'.
         '<h2 class="hndl ui-sortable-handle" style="font-weight:bold;">Subpage Menu-Widget</h2>'.
         '<div class="inside" style="border-top:solid 1px #dfdfdf; padding-top:10px;">';

         include "sgs-page-widget-subpage-menu.php";

  echo   '</div>'.
        '</div>'.

       '</div>';



}


/**
 * When the post is saved, saves our custom data.
 * @param int $post_id The ID of the post being saved.
 */
function wp_pagewidget_save_meta_box_data( $post_id ) {

  /*
  * We need to verify this came from our screen and with proper authorization,
  * because the save_post action can be triggered at other times.
  */

  // Check if our nonce is set.
  if ( ! isset( $_POST['wp_pagewidget_meta_box_nonce'] ) ) {
    return;
  }

  // Verify that the nonce is valid.
  if ( ! wp_verify_nonce( $_POST['wp_pagewidget_meta_box_nonce'], 'wp_pagewidget_save_meta_box_data' ) ) {
    return;
  }

  // If this is an autosave, our form has not been submitted, so we don't want to do anything.
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
    return;
  }

  // Check the user's permissions.
  if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
    if ( ! current_user_can( 'edit_page', $post_id ) ) {
      return;
    }
  }
  else {
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
      return;
    }
  }

  /* OK, it's safe for us to save the data now. */

  /*
  // Make sure that it is set.
  if ( ! isset( $_POST['wp_pagewidget_new_field'] ) ) {
    return;
  }

  if ( ! isset( $_POST['wp_pagewidget_new_txtarea'] ) ) {
    return;
  }

  if ( ! isset( $_POST['wp_pagewidget_subpage_chk'] ) ) {
    return;
  }
  */


  // Sanitize user input and
  // Update the meta field in the database.
  //===== TEXT ===================
  $my_data = sanitize_text_field( $_POST['wp_pagewidget_new_field'] );
  update_post_meta( $post_id, '_wp_pagewidget_meta_value_key', $my_data );

  $my_data =  $_POST['wp_pagewidget_new_txtarea'] ;
  update_post_meta( $post_id, '_wp_pagewidget_meta_value_key_content', $my_data );

  $my_data =  $_POST['wp_pagewidget_text_disable_chk'] ;
  update_post_meta( $post_id, '_wp_pagewidget_meta_value_key_text_disable', $my_data );


  //===== MENU ===================
  $my_data =  $_POST['wp_pagewidget_subpage_title'] ;
  update_post_meta( $post_id, '_wp_pagewidget_meta_value_key_subpagetitle', $my_data );

  $my_data =  $_POST['wp_pagewidget_menu_disable_chk'] ;
  update_post_meta( $post_id, '_wp_pagewidget_meta_value_key_menu_disable', $my_data );

  $my_data =  $_POST['wp_pagewidget_cust_menu'] ;
  update_post_meta( $post_id, '_wp_pagewidget_meta_value_key_cust_menu', $my_data );

}
add_action( 'save_post', 'wp_pagewidget_save_meta_box_data' );






//======================================================================
//===== Create Widgets =================================================
//======================================================================

function sgs_pagewidget_init() {
  register_widget('WP_SGSPage_Widget');
  register_widget('WP_SGSPage_Widget_Subpages');
}
add_action('widgets_init', 'sgs_pagewidget_init');


include "sgs-widget-text.php";
include "sgs-widget-subpage-menu.php";






function sgspage_get_vars($key) {
  global $sgspage_vars;

  return $sgspage_vars[$key];
}


?>
<?php

  /*
  * Use get_post_meta() to retrieve an existing value
  * from the database and use the value for the form.
  */

  $title = get_post_meta( $post->ID, '_wp_pagewidget_meta_value_key', true );
  $content = get_post_meta( $post->ID, '_wp_pagewidget_meta_value_key_content', true );
  $disable_text = get_post_meta( $post->ID, '_wp_pagewidget_meta_value_key_text_disable', true );

  if($disable_text=="yes")
    $text_disable_check = " checked";
  else
    $text_disable_check = "";

  echo   '<div style="margin-bottom:20px;">'.
         '<label for="wp_pagewidget_text_disable_chk" style="font-weight:bold;">';
         _e( 'Disable:' );
  echo   '</label> '.
         '<input type="checkbox" id="wp_pagewidget_text_disable_chk" name="wp_pagewidget_text_disable_chk" value="yes"'.$text_disable_check.'>'.
         '</div>';

  echo   '<div style="margin-bottom:20px;">'.
         '<label for="wp_pagewidget_new_field" style="font-weight:bold;">';
         _e( 'Title:' );
  echo   '</label> '.
         '<input type="text" id="wp_pagewidget_new_field" name="wp_pagewidget_new_field" value="' . esc_attr( $title ) . '" size="25">'.

         '</div>';

         wp_editor( $content, "wp_pagewidget_new_txtarea" );


?>
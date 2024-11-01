<?php


  $subpage_title = get_post_meta( $post->ID, '_wp_pagewidget_meta_value_key_subpagetitle', true );
  $disable_menu = get_post_meta( $post->ID, '_wp_pagewidget_meta_value_key_menu_disable', true );
  $do_togg_cust_menu = get_post_meta( $post->ID, '_wp_pagewidget_meta_value_key_cust_menu', true );

  if($disable_menu=="yes")
    $menu_disable_check = " checked";
  else
    $menu_disable_check = "";

  $menus = wp_get_nav_menus( array( 'orderby' => 'name' ) );



  echo   '<div style="margin-bottom:20px;">'.
         '<label for="wp_pagewidget_menu_disable_chk" style="font-weight:bold;">';
         _e( 'Disable:' );
  echo   '</label> '.
         '<input type="checkbox" id="wp_pagewidget_menu_disable_chk" name="wp_pagewidget_menu_disable_chk" value="yes"'.$menu_disable_check.'>'.
         '</div>';


  echo   '<div style="margin-bottom:20px;">'.
         '<label for="wp_pagewidget_subpage_title" style="font-weight:bold;">';
         _e( 'Title:' );
  echo   '</label> '.
         '<input type="text" id="wp_pagewidget_subpage_title" name="wp_pagewidget_subpage_title" value="' . esc_attr( $subpage_title ) . '" size="25">'.

         '</div>';


  echo   '<p>'.
         '<label for="wp_pagewidget_cust_menu">';
          _e('Select Menu:');
  echo   '</label>'.
         '<select id="wp_pagewidget_cust_menu" name="wp_pagewidget_cust_menu">'.
         ' <option value="0">';
          _e( '&mdash; Select &mdash;' );
  echo   '</option>';


  foreach ( $menus as $menu ) {
    echo '<option value="' . $menu->term_id . '"'
         . selected( $do_togg_cust_menu, $menu->term_id, false )
         . '>'. esc_html( $menu->name ) . '</option>';
  }

  echo   '</select>'.
         '</p>';


?>




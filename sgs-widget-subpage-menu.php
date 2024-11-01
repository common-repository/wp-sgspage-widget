<?php


//===== WIDGET_CLASS =============================================
class WP_SGSPage_Widget_Subpages extends WP_Widget {


    function __construct() {
      // widget actual processes
      $widget_ops = array('classname' => 'widget_nav_menu sgspage_menu', 'description' => __('Display SGSPage Widget defined subpages of current page.'));
      $control_ops = array('width' => 400, 'height' => 350);
      parent::__construct('wpsgspage_nav_menu', __('WP-SGSPage Widget SubPages'), $widget_ops, $control_ops);


    }

    function form($instance) {

      // outputs the options form on admin
      $title = isset( $instance['title'] ) ? $instance['title'] : '';
      $wpsgspage_nav_menu = isset( $instance['wpsgspage_nav_menu'] ) ? $instance['wpsgspage_nav_menu'] : '';

      $title = sanitize_text_field( $instance['title'] );

      $do_togg_menu = isset( $instance['do_togg_menu'] ) ? $instance['do_togg_menu'] : 0;

      $menus = wp_get_nav_menus( array( 'orderby' => 'name' ) );


      ?>
      <p>
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title') ?> (<?php _e('default') ?>):</label>
        <input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" />
      </p>

      <p><input id="<?php echo $this->get_field_id('do_togg_menu'); ?>" name="<?php echo $this->get_field_name('do_togg_menu'); ?>" type="checkbox"<?php checked( $do_togg_menu ); ?> />&nbsp;<label for="<?php echo $this->get_field_id('do_togg_menu'); ?>"><?php _e('Enable toggle'); ?></label></p>


    <?php

    }

    function update($new_instance, $old_instance) {
      // processes widget options to be saved
      $instance = array();
      if ( ! empty( $new_instance['title'] ) ) {
        $instance['title'] = strip_tags( stripslashes($new_instance['title']) );
      }

      $instance['do_togg_menu'] = ! empty( $new_instance['do_togg_menu'] );

      return $instance;
    }

    function widget($args, $instance) {
      global $wp_query;


      // outputs the content of the widget

      $postid = $wp_query->post->ID;
      $post_guid = $wp_query->post->guid;

      $parent = get_post_ancestors($postid);

      $parent_id = "";
      if(is_array($parent))
        $parent_id = $parent[0];

      if($parent_id=="")
        $parent_id = $postid;


      if (get_post_meta( $postid, '_wp_pagewidget_meta_value_key_menu_disable', true )=="yes")
        return;

      $title = get_post_meta( $postid, '_wp_pagewidget_meta_value_key_subpagetitle', true );

      if($title=="")
        $title = get_post_meta( $parent_id, '_wp_pagewidget_meta_value_key_subpagetitle', true );

      if($title=="")
        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

      if($title=="")
        return;

      if(get_post_meta( $postid, '_wp_pagewidget_meta_value_key_cust_menu', true )!="" && get_post_meta( $postid, '_wp_pagewidget_meta_value_key_cust_menu', true )!="0")
        $do_togg_cust_menu = get_post_meta( $postid, '_wp_pagewidget_meta_value_key_cust_menu', true );
      else
        $do_togg_cust_menu = get_post_meta( $parent_id, '_wp_pagewidget_meta_value_key_cust_menu', true );

      $togg_cust_menu_code = "";
      if($do_togg_cust_menu!="" && $do_togg_cust_menu!="-1" && $do_togg_cust_menu!="0") {

                $nav_settings = array(
                                  'theme_location'  => '',
                                  'menu'            =>  $do_togg_cust_menu,
                                  'container'       => '',
                                  'container_class' => '',
                                  'container_id'    => '',
                                  'menu_class'      => '',
                                  'menu_id'         => 'menu-sidebar-'.$samw_menu_id,
                                  'echo'            => false,
                                  'fallback_cb'     => '',
                                  'before'          => '',
                                  'after'           => '',
                                  'link_before'     => '',
                                  'link_after'      => '',
                                  'items_wrap'      => '%3$s',
                                  'depth'           => 0,
                                  'walker'          => ''
                );


        $togg_cust_menu_code = wp_nav_menu($nav_settings);
      }


      //===== get subpages ===============================================
      $pg_args = array(
        'post_parent' => $parent_id,
        'post_type'   => 'page',
        'numberposts' => -1,
        'post_status' => 'publish',
        'orderby' => sgspage_get_vars("menu_orderby"),
        'order' => sgspage_get_vars("menu_order")
      );

      $pg_children = get_children( $pg_args );

      $add_classes = "";


      if(!empty( $instance['do_togg_menu'] ))
        $do_toggle_menu = true;
      else
        $do_toggle_menu = false;


      $std_title = $args['before_title'];

      if($do_toggle_menu) {
        $toggle_code = '<span class="sgspagewidget_togg_marker"></span>';
        $hide_class = " sgspage_hide";
        $std_title = str_replace("widget-title","widget-title sgspage-widget-title",$std_title);
      }
      else {
        $toggle_code = "";
        $hide_class = "";
      }


      $subpage_menu = '<div class="menu-sgssubpagemenu-container'.$hide_class.'">'.
                      '<ul class="menu" id="menu-sgssubpagemenu-'.$postid.'">';



      foreach($pg_children as $pg_child) {
        if($post_guid==$pg_child->guid)
          $add_classes = " current-menu-item current_page_item";
        else
          $add_classes = "";

        $child_url = get_post_permalink($pg_child->ID); //   $pg_child->guid ,

        $subpage_menu .= '<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-'.$pg_child->ID.$add_classes.'"><a href="'.get_permalink($pg_child->ID).'">'.$pg_child->post_title.'</a></li>';

      }

      $subpage_menu .= $togg_cust_menu_code;

      $subpage_menu .= '</ul>'.
                       '</div>';





      /**
      * Filter the content of the Text widget.
      *
      * @since 2.3.0
      * @since 4.4.0 Added the `$this` parameter.
      *
      * @param string         $widget_text The widget content.
      * @param array          $instance    Array of settings for the current widget.
      * @param WP_Widget_Text $this        Current Text widget instance.
      */


      echo $args['before_widget'];
      echo $std_title . $toggle_code . $title . $args['after_title'];

      echo $subpage_menu;


      echo $args['after_widget'];


    }

}

?>
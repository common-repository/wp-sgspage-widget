<?php



//===== WIDGET_CLASS =============================================
class WP_SGSPage_Widget extends WP_Widget {
    function __construct() {
      // widget actual processes


      $widget_ops = array('classname' => 'widget_text sgspage_text', 'description' => __('Display SGSPage-Widget data or arbitrary text or HTML (by default).'));
      $control_ops = array('width' => 400, 'height' => 350);
      parent::__construct('wpsgspage_text', __('WP-SGSPage Widget Text'), $widget_ops, $control_ops);


    }

    function form($instance) {

      // outputs the options form on admin
      $title = isset( $instance['title'] ) ? $instance['title'] : '';
      $wpsgspage_text = isset( $instance['wpsgspage_text'] ) ? $instance['wpsgspage_text'] : '';

      $filter = isset( $instance['filter'] ) ? $instance['filter'] : 0;
      $do_togg_text = isset( $instance['do_togg_text'] ) ? $instance['do_togg_text'] : 0;

      $title = sanitize_text_field( $instance['title'] );

      ?>
      <p>
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title') ?> (<?php _e('default') ?>):</label>
        <input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" />
      </p>
      <p>
        <label for="<?php echo $this->get_field_id('wpsgspage_text'); ?>"><?php _e('Content'); ?> (<?php _e('default') ?>):</label>
        <textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('wpsgspage_text'); ?>"  name="<?php echo $this->get_field_name('wpsgspage_text'); ?>"><?php echo esc_textarea( $instance['wpsgspage_text'] ); ?></textarea>
      </p>
      <p><input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox"<?php checked( $filter ); ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e('Automatically add paragraphs'); ?></label></p>

      <p><input id="<?php echo $this->get_field_id('do_togg_text'); ?>" name="<?php echo $this->get_field_name('do_togg_text'); ?>" type="checkbox"<?php checked( $do_togg_text ); ?> />&nbsp;<label for="<?php echo $this->get_field_id('do_togg_text'); ?>"><?php _e('Enable toggle'); ?></label></p>


    <?php

    }

    function update($new_instance, $old_instance) {
      // processes widget options to be saved
      $instance = array();
      if ( ! empty( $new_instance['title'] ) ) {
        $instance['title'] = strip_tags( stripslashes($new_instance['title']) );
      }
      if ( current_user_can('unfiltered_html') )
        $instance['wpsgspage_text'] =  $new_instance['wpsgspage_text'];
      else
        $instance['wpsgspage_text'] = wp_kses_post( stripslashes( $new_instance['wpsgspage_text'] ) );

      $instance['filter'] = ! empty( $new_instance['filter'] );

      $instance['do_togg_text'] = ! empty( $new_instance['do_togg_text'] );

      return $instance;
    }

    function widget($args, $instance) {
      global $wp_query, $do_toggle_text;

      // outputs the content of the widget


      $postid = $wp_query->post->ID;

      $title = get_post_meta( $postid, '_wp_pagewidget_meta_value_key', true );
      $widget_text = get_post_meta( $postid, '_wp_pagewidget_meta_value_key_content', true );


      if (get_post_meta( $postid, '_wp_pagewidget_meta_value_key_text_disable', true )=="yes")
        return;


      /** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
      if($title=="")
        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

      if($widget_text=="")
        $widget_text = ! empty( $instance['wpsgspage_text'] ) ? $instance['wpsgspage_text'] : '';


      if($title=="" && $widget_text=="")
        return;

      $std_title = $args['before_title'];

      if(!empty( $instance['do_togg_text'] ))
        $do_toggle_text = true;
      else
        $do_toggle_text = false;

      if($do_toggle_text) {
        $toggle_code = '<span class="sgspagewidget_togg_marker"></span>';
        $hide_class = " sgspage_hide";
        $std_title = str_replace("widget-title","widget-title sgspage-widget-title",$std_title);
      }
      else {
        $toggle_code = "";
        $hide_class = "";
      }


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
      $text = apply_filters( 'widget_text', $widget_text, $instance, $this );

      echo $args['before_widget'];



      echo $std_title . $toggle_code . $title . $args['after_title'];
      ?>
      <div class="textwidget sgspage_widget<?php echo $hide_class; ?>"><?php echo !empty( $instance['filter'] ) ? wpautop( $text ) : $text; ?></div>

      <?php
      echo $args['after_widget'];


    }

}






?>
<?php

$toggle_speed = "'60'";

?>


jQuery(document).ready(function ($) {

  $('.sgspagewidget_togg_marker').click(function(){
    $( this ).toggleClass( "sgspagewidget_togg_marker_up" );

  });

  $('.sgspage_text .sgspage-widget-title').click(function(){
    $(this).next().slideToggle(<?php echo $toggle_speed; ?>);

  });

  $('.sgspage_menu .sgspage-widget-title').click(function(){
    $(this).next().slideToggle(<?php echo $toggle_speed; ?>);

  });


});

<?php
/*
Plugin Name: ORCiD Widget
Plugin URI: 
Description: Displays an ORCiD profile info
Author: Henrique Diogo Silva
Version: 0.5
Author URI: https://hdiogosilva.xyz
*/


class orcidWidget extends WP_Widget
{
  function RandomPostWidget()
  {
    $widget_ops = array('classname' => 'orcidWidget', 'description' => 'Displays an ORCiD profile info' );
    $this->WP_Widget('orcidWidget', 'Random Post and Thumbnail', $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
    $title = $instance['title'];
?>
  <p><label for="<?php echo $this->get_field_id('title'); ?>">Título: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    return $instance;
  }
 
  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);
 
    echo $before_widget;
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
 
    if (!empty($title))
      echo $before_title . $title . $after_title;;
 
    // WIDGET CODE GOES HERE
    include "main.php";
    fetch($orcidID, "nome");
    fetch($orcidID, "bio");
    fetch($orcidID, "publica");
 
    echo $after_widget;
  }
 
}
add_action( 'widgets_init', create_function('', 'return register_widget("orcidWidget");') );?>

<?php

/*
Plugin Name: ORCiD Widget
Plugin URI: 
Description: Displays an ORCiD profile info
Author: Henrique Diogo Silva
Version: 0.9
Author URI: https://hdiogosilva.xyz
*/

/**
 * Adds Foo_Widget widget.
 */
class ORCiD_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'orcid_widget', // Base ID
			esc_html__( 'ORCiD Widget', 'ORCiD Widget' ), // Name
			array( 'description' => esc_html__( 'Displays an ORCiD profile info', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		
		extract( $args );
	   // these are the widget options
	   $title = apply_filters('widget_title', $instance['title']);
	   $orcidID = $instance['idOrcid'];
	   $pubLimit = $instance['numPub'];
	   echo $before_widget;
	   // Display the widget

	   if ( $title ) {
	      echo $before_title . $title . $after_title;
	   }

	   if ( !$pubLimit ) {
	   	$pubLimit = 3;
	   }

	   $elementos = [];
	   if( $instance['checkboxNome'] AND $instance['checkboxNome'] == '1' ) {
	     array_push($elementos, 'nome');
	   }
	   if( $instance['checkboxBio'] AND $instance['checkboxBio'] == '1' ) {
	     array_push($elementos, 'bio');

	   }
	   if( $instance['checkboxPublica'] AND $instance['checkboxPublica'] == '1' ) {
	     array_push($elementos, 'publica');
	   }

	   include "wp-orcid.php";
	   foreach ($elementos as $elemento) {
	   	fetch($orcidID, $elemento, $pubLimit);
	   }

		echo $after_widget;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		// widget form creation

			// Check values 
				if( $instance ) { 
				$title    = esc_attr( $instance['title'] );
				$idOrcid    = esc_attr( $instance['idOrcid'] );
				$checkboxNome = esc_attr( $instance['checkboxNome'] );
				$checkboxBio = esc_attr( $instance['checkboxBio'] );
				$checkboxPublica = esc_attr( $instance['checkboxPublica'] );
				$numPub    = esc_attr( $instance['numPub'] );
			} else { 
				$title    = '';
				$idOrcid = ''; 
				$checkboxNome = '';
				$checkboxBio = '';
				$checkboxPublica = '';
				$numPub = '';
			}
		?>

		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Título do Widget', 'text_domain' ); ?></label>
		</p>
		<p>
		<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'idOrcid' ) ); ?>"><strong><?php _e( 'ORCiD ID', 'text_domain' ); ?></strong></label>
		</p>
		<p>
		<input id="<?php echo esc_attr( $this->get_field_id( 'idOrcid' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'idOrcid' ) ); ?>" type="text" value="<?php echo esc_attr( $idOrcid ); ?>" />
		</p>

		<p><strong>Elementos a mostrar:</strong></p>
		<p>
		<input id="<?php echo esc_attr( $this->get_field_id( 'checkboxNome' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'checkboxNome' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $checkboxNome ); ?>> </input>
		<label for="<?php echo esc_attr( $this->get_field_id( 'checkboxNome' ) ); ?>"><?php _e( 'Nome', 'text_domain' ); ?></label>
		</p>
		<p>
		<input id="<?php echo esc_attr( $this->get_field_id( 'checkboxBio' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'checkboxBio' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $checkboxBio ); ?>> </input>
		<label for="<?php echo esc_attr( $this->get_field_id( 'checkboxBio' ) ); ?>"><?php _e( 'Biografia', 'text_domain' ); ?></label>
		</p>
		<p>
		<input id="<?php echo esc_attr( $this->get_field_id( 'checkboxPublica' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'checkboxPublica' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $checkboxPublica ); ?>> </input>
		<label for="<?php echo esc_attr( $this->get_field_id( 'checkboxPublica' ) ); ?>"><?php _e( 'Lista de Publicações', 'text_domain' ); ?></label>
		</p>

		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'numPub' ) ); ?>"><strong><?php _e( 'Nº de publicações a apresentar', 'text_domain' ); ?></strong></label>
		</p>
		<p>
		<input id="<?php echo esc_attr( $this->get_field_id( 'numPub' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'numPub' ) ); ?>" type="text" value="<?php echo esc_attr( $numPub ); ?>" />	
		</p>

		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();

		$instance['title'] = strip_tags($new_instance['title']);
		$instance['idOrcid'] = strip_tags($new_instance['idOrcid']);
      	$instance['checkboxNome'] = strip_tags($new_instance['checkboxNome']);
      	$instance['checkboxBio'] = strip_tags($new_instance['checkboxBio']);
      	$instance['checkboxPublica'] = strip_tags($new_instance['checkboxPublica']);
      	$instance['numPub'] = strip_tags($new_instance['numPub']);

		return $instance;
	}

} // class Foo_Widget

// register widget
function register_foo_widget() {
    register_widget( 'ORCiD_Widget' );
}
add_action( 'widgets_init', 'register_foo_widget' );
?>

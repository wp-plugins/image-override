<?php
/*
Plugin Name: Image Override
Plugin URI: http://www.billerickson.net/image-override-plugin
Description: Allows you to override WordPress' auto generated thumbnails. If you change your image sizes, deactivate and reactivate the plugin.
Version: 1.0
Author: Bill Erickson
Author URI: http://www.billerickson.net
License: GPLv2
*/



class BE_Image_Override {
	var $instance;
	
	public function __construct() {
		$this->instance =& $this;
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
		add_action( 'init', array( $this, 'init' ) );	
	}
	
	public function activate() {
		$all_image_sizes = $this->get_all_image_sizes();
		if ( isset( $all_image_sizes ) )
			add_option( 'be_image_override_image_sizes', $all_image_sizes );
	}
	
	public function deactivate() {
		delete_option( 'be_image_override_image_sizes' );
	}

	public function init() {
		add_filter( 'cmb_meta_boxes', array( $this, 'create_metaboxes' ) );
		add_action( 'init', array( $this, 'initialize_cmb_meta_boxes' ), 50 );
		add_action( 'image_override_display', array( $this, 'display' ), 10, 1 );
		add_action( 'return_image_override_display', array( $this, 'return_display' ), 10, 2 );
	}
	
	public function create_metaboxes( $meta_boxes ) {
		
		// Use 'image_override_post_types' filter to change what post types it applies to
		$post_types = apply_filters( 'image_override_post_types', array_keys( get_post_types( array('show_ui' => true ) ) ) );
		$all_image_sizes = get_option( 'be_image_override_image_sizes' );
		$sizes = array();
		foreach ($all_image_sizes as $size => $attr )
			$sizes[] = $size;
			
		// Use 'image_override_sizes' filter to change what sizes are used
		$sizes = apply_filters( 'image_override_sizes', $sizes );
		
		if ( !empty( $sizes ) ) {
			
			$prefix = 'image_override_';
			$fields = array();
			foreach( $sizes as $size ) {
			
				$fields[] = array(
					'name' => ucwords( $size ), 
					'desc' => 'This image size should be ' . $all_image_sizes[$size]['width'] . 'x' . $all_image_sizes[$size]['height'] . ( isset( $all_image_sizes[$size]['crop'] ) ? ' exactly' : '' ) . '.',
		            'id' => $prefix.$size,
		            'type' => 'file'			
				);
	
			}
		
			$meta_boxes[] = array(
		    	'id' => 'image-override',
			    'title' => 'Image Override',
			    'pages' => $post_types,
				'context' => 'normal',
				'priority' => 'high',
				'show_names' => true, 
				'fields' => $fields
			);
		}
		
		return $meta_boxes;
	}

	public function initialize_cmb_meta_boxes() {
		$sizes = apply_filters( 'image-override-sizes', array( 'thumbnail', 'medium', 'large' ) );
	    if ( !class_exists('cmb_Meta_Box') && !empty( $sizes ) ) {
	        require_once( dirname( __FILE__) . '/lib/metabox/init.php' );
	    }
	}
	
	public function display( $size = '' ) {
		global $post;
		echo $this->return_display( $post->ID, $size );
	}
	
	public function return_display( $id = '', $size = '' ) { 
		$override = get_post_meta( $id, 'image_override_'.$size, true );
		
		// If there's an override, use it!
		if ( !empty( $override ) ) return '<img src="' . esc_url( $override ) . '" class="attachment-'. esc_attr( $size ) . '" alt="' . get_the_title($id) . '" title="' . get_the_title($id) .'" />';
		
		// If you're running genesis, let's try genesis_get_image next
		if ( function_exists( 'genesis_get_image' ) )
			return genesis_get_image( array( 'size' => $size ) );
			
		// If all else fails, do the standard post image
		else return get_the_post_thumbnail( $id, $size );	
	}
	
	public function get_all_image_sizes() {
		global $_wp_additional_image_sizes;

		$builtin_sizes = array(
			'large'     => array(
	            'width' => get_option('large_size_w'),
	            'height' => get_option('large_size_h')
	        ),
	        'medium'    => array(
	            'width' => get_option('medium_size_w'),
	            'height' => get_option('medium_size_h')
	        ),
	        'thumbnail' => array(
	            'width' => get_option('thumbnail_size_w'),
	            'height' => get_option('thumbnail_size_h'),
	            'crop' => get_option('thumbnail_crop')
	        )
	    );
	    
	    $all_image_sizes = array();
	    if ( isset( $builtin_sizes ) && isset( $_wp_additional_image_sizes ) )
			$all_image_sizes = array_merge( $builtin_sizes, $_wp_additional_image_sizes );		
		else $all_image_sizes = $builtin_sizes;
		
		return $all_image_sizes;
	}
}

new BE_Image_Override;
?>
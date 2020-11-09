<?php
// Our custom post type function

wp_enqueue_script( 'aframe104', get_template_directory_uri() . '/js/aframe104.js', array(), true ); //this has to be loaded first - all the rest depands on it

function wpdocs_theme_name_scripts() {
  wp_enqueue_style( 'style', get_stylesheet_uri() );
  

  wp_enqueue_script( 'jquery');
  //wp_enqueue_script( 'aframe104', get_template_directory_uri() . '/js/aframe104.js', array(), true );
  wp_enqueue_script( 'lookAt', get_template_directory_uri() . '/js/lookAt.js', array(), true ); // modified to enable look-at component see line 1255  https://unpkg.com/browse/aframe-look-at-component@0.8.0/
  wp_enqueue_script( 'a-frame-to-html', get_template_directory_uri() . '/js/a-frame-to-html.js', array(), true );
  wp_enqueue_script( 'aframe-orbit-controls', get_template_directory_uri() . '/js/aframe-orbit-controls.js', array(), true ); //https://github.com/supermedium/superframe/tree/master/components/orbit-controls
  wp_enqueue_script( 'tsne', get_template_directory_uri() . '/js/tsne.js', array(), true );  //https://github.com/karpathy/tsnejs 
  wp_enqueue_script( 'aframe-randomizer-components', get_template_directory_uri() . '/js/aframe-randomizer-components.min.js', array(), true ); //https://github.com/supermedium/superframe/tree/master/components/randomizer

  wp_enqueue_script('cluster3d', get_template_directory_uri() . '/js/cluster3d2.js', array(), false, true); //import in footer instead of head

}

add_action( 'wp_enqueue_scripts', 'wpdocs_theme_name_scripts' );

add_theme_support( 'post-thumbnails' );

//register nav menu
function register_my_menus() {
  register_nav_menus(
    array(
      'header-menu' => __( 'Header Menu' ),
      'footer-menu' => __( 'Footer Menu' )
     )
   );
 }
 add_action( 'init', 'register_my_menus' );
 
 //add css class to li element:
 function add_additional_class_on_li($classes, $item, $args) {
    if(isset($args->add_li_class)) {
        $classes[] = $args->add_li_class;
    }
    return $classes;
}
add_filter('nav_menu_css_class', 'add_additional_class_on_li', 1, 3);
 

//set_post_thumbnail_size( 256, 256);
//add_image_size( 'single-post-thumbnail', 600, 600 );

//ADD EXTRA FIELD TO POST

// Add custom post meta
function WPTime_add_custom_post_meta() { // By Qassim Hassan - wp-time.com
    $screen = "post"; // will be display custom post meta box in post editor, to display it in page type, change "post" to "page"
    add_meta_box( 'bla-bla-bla', 'Custom Label', 'WPTime_custom_post_meta_callback', $screen, 'side', 'default', null );
}
add_action( 'add_meta_boxes', 'WPTime_add_custom_post_meta' );
 
// Custom post meta callback
function WPTime_custom_post_meta_callback($post){ // By Qassim Hassan - wp-time.com
    wp_nonce_field( 'custom_image_save_data', 'custom_image_nonce' ); 
    $value = get_post_meta( $post->ID, 'custom_image_name', true ); 
    echo '<input type="text" name="custom_image_name" value="' . esc_attr( $value ) . '" size="30" placeholder="Enter label">';
}
 
 
// Save custom post meta data = labels --------------------------------------------------------------------
function WPTime_custom_post_meta_save_data( $post_id ) { // By Qassim Hassan - wp-time.com
 
     if ( ! isset( $_POST['custom_image_nonce'] ) ) {
         return;
     }
 
     if ( ! wp_verify_nonce( $_POST['custom_image_nonce'], 'custom_image_save_data' ) ) {
         return;
     }

     if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
         return;
     }
  
     if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
         if ( ! current_user_can( 'edit_page', $post_id ) ) {
             return;
         }
     }
    else{
         if ( ! current_user_can( 'edit_post', $post_id ) ) {
             return;
         }
    }
 
    $my_data = sanitize_text_field( $_POST['custom_image_name'] ); 
    update_post_meta( $post_id, 'custom_image_name', $my_data );
}
add_action( 'save_post', 'WPTime_custom_post_meta_save_data');

//enable custom logo option in customizer-------------------------------------------
function config_custom_logo() {        
    //add_theme_support( 'custom-logo' );
    add_theme_support( 'custom-logo', array(
	'height'      => 100,
	'width'       => 400,
	'flex-height' => true,
	'flex-width'  => true,
	'header-text' => array( 'site-title', 'site-description' ),
) );

}
add_action( 'after_setup_theme' , 'config_custom_logo' );
//get customizer custom options - see customizer.php-----------------------------------
require_once get_template_directory() . '/customizer.php';

//include_once( 'theme_options.php' ); //add theme options to settings

/*
function create_posttype() {
 
    register_post_type( 'movies',
    // CPT Options
        array(
            'labels' => array(
                'name' => __( 'Movies' ),
                'singular_name' => __( 'Movie' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'movies'),
            'show_in_rest' => true,
 
        )
    );
}
// Hooking up our function to theme setup
add_action( 'init', 'create_posttype' );
*/
?>
<?php
    /*
    * Plugin Name:       Simple Event Calendar
    * Plugin URI:        https://jtaskila.fi
    * Description:       A plugin for displaying events in various forms on your site. The plugin uses only native wordpress functionalities and has full Polylang and WPML support.
    * Version:           1.2.0
    * Requires at least: 5.7
    * Requires PHP:      7.2
    * Author:            Juho Taskila
    * Author URI:        https://jtaskila.fi
    * License:           MIT
    * License URI:
    * Text Domain:       simple-event-calendar
    * Domain Path:       /languages
    */

    ////////////////////////////////////////////////////////////////
    // Author:      Juho Taskila
    // Date:        25.5.2021
    // Description: This file is the main file of sec-plugin
    ////////////////////////////////////////////////////////////////

    // CONTENTS
    ////////////////////////////////////////////////////////////////
    // 1(35). sec_load_plugin_textdomain
    // 2(49). sec_register_post_types
    // 3(78). sec_single_template
    // 4(86). sec_archive_template
    // 5(). sec_dependencies
    ////////////////////////////////////////////////////////////////
    include( plugin_dir_path( __FILE__ ) . 'include/sec-widget.php');
    include( plugin_dir_path( __FILE__ ) . 'include/sec-meta-boxes.php');
    include( plugin_dir_path( __FILE__ ) . 'include/sec-functions.php');

    //Load translation
    function sec_load_plugin_textdomain() {

    	$domain = 'simple-event-calendar';
    	$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

    	// wp-content/languages/plugin-name/plugin-name-de_DE.mo
    	load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
    	// wp-content/plugins/plugin-name/languages/plugin-name-de_DE.mo
    	load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );

    }
    add_action( 'init', 'sec_load_plugin_textdomain' );


    function sec_register_post_types(){
        $labels = array(
            'name'               => _x( 'Events', 'post type general name', 'simple-event-calendar' ),
            'singular_name'      => _x( 'Event', 'post type singular name', 'simple-event-calendar' ),
            'add_new'            => _x( 'Add New', 'event', 'simple-event-calendar' ),
            'add_new_item'       => __( 'Add New Event', 'simple-event-calendar' ),
            'edit_item'          => __( 'Edit Event', 'simple-event-calendar' ),
            'new_item'           => __( 'New Event', 'simple-event-calendar' ),
            'all_items'          => __( 'All Events', 'simple-event-calendar' ),
            'view_item'          => __( 'View Event', 'simple-event-calendar' ),
            'search_items'       => __( 'Search Events', 'simple-event-calendar' ),
            'not_found'          => __( 'No events found', 'simple-event-calendar' ),
            'not_found_in_trash' => __( 'No events found in the Trash', 'simple-event-calendar' ),
            'parent_item_colon'  => '’',
            'menu_name'          => __('Events', 'simple-event-calendar')
        );
        $args = array(
            'labels'        => $labels,
            'description'   => __( 'Upcoming events', 'simple-event-calendar' ),
            'public'        => true,
            'menu_position' => 5,
            'supports'      => array( 'title', 'editor', 'thumbnail' ),
            'has_archive'   => true,
            'menu_icon'      => 'dashicons-calendar-alt'
        );
        register_post_type( 'events', $args );
    }
    add_action('init', 'sec_register_post_types');

    function sec_single_template( $template ){
        if( is_singular( 'events' ) ){
            $template = plugin_dir_path( __FILE__ ).'templates/single-events.php';
        }
        return $template;
    }
    add_filter( 'single_template', 'sec_single_template', 50, 1);

    function sec_archive_template( $template ){
        if( is_post_type_archive( 'events' ) ){
            $template = plugin_dir_path( __FILE__ ).'templates/archive-events.php';
        }
        return $template;
    }

    add_filter( 'archive_template', 'sec_archive_template', 50, 1);


    function sec_dependencies(){
        wp_enqueue_style( 'simple-event-calendar-css', plugins_url( 'css/simple-event-calendar.css', __FILE__) );
    }

    add_action( 'wp_enqueue_scripts', 'sec_dependencies' );
?>

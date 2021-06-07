<?php
    ////////////////////////////////////////////////////////////////
    // Author:      Juho Taskila
    // Date:        26.5.2021
    // Description: This file contains the sec-widget
    ////////////////////////////////////////////////////////////////

    // CONTENTS
    ////////////////////////////////////////////////////////////////
    // 1. sec_widget class
    // 2. sec_widget_shortcode function
    ////////////////////////////////////////////////////////////////

    class sec_widget extends WP_Widget{
        function __construct(){
            parent::__construct(
                'sec_widget',
                __('Upcoming events', 'simple-event-calendar'),
                array(
                    'description' => __('Show upcoming events from event calendar', 'simple-event-calendar')
                )
            );
        }

        public function widget( $args, $instance ){
            // getting posts
            $query_args = array(
                'post_type' => 'events',
                'post_status' => 'publish',
                'posts_per_page' => $instance['number'],
                'meta_key' => 'sec_event_date_start',
                'orderby' => 'meta_value_num',
                'order' => 'ASC',
                'meta_query' => array(
                    'key' => 'sec_event_date_end',
                    'value' => time()-60*60*24, // shows also todays events
                    'compare' => '>='
                ),
            );
            $events = new WP_Query( $query_args );

            $title = apply_filters( 'widget_title', $instance['title'] );

            echo $args['before_widget'];
            if( !empty($title) ){
                echo $args['before_title'].$title.$args['after_title'];
            }


            if($events->have_posts()){
                echo '<ul class="nav flex-column sec-widget">';
                    while($events->have_posts()){
                        $events->the_post();
                        echo '<li class="nav-item">';
                        echo '<a href="'.get_the_permalink().'" class="nav-link">'.get_the_title();
                        if( $instance['show_date'] ){
                            echo '<br><echo span class="sec-widget-date">';
                            if( sec_has_start_date() ){
                                echo '<i class="far fa-calendar-alt"></i> '.sec_date();
                            }

                            if( sec_has_time() ){
                                echo ' <i class="far fa-clock"></i> '.sec_time();
                            }
                            echo '</span>';
                        }
                        echo '</a></li>';
                    }
                    echo '<li class="nav-item text-center"><a class="nav-link" href="'.get_post_type_archive_link('events').'">'.__('Show all events', 'simple-event-calendar').'</a></li>';
                echo '</ul>';
            }else{
                echo '<h3>'.__('No events found', 'simple-event-calendar').'</h3>';
            }

            echo $args['after_widget'];
        }

        public function form( $instance ){
            $title = isset( $instance['title'] ) ? $title = $instance['title'] : '';
            $show_date = isset( $instance['show_date'] ) ? $show_date = $instance['show_date'] : 0;
            $show_past = isset( $instance['show_past'] ) ? $show_past = $instance['show_past'] : 0;
            $number = isset( $instance['number'] ) ? $number = $instance['number'] : 5;
            echo '<p>';
                echo '<label for="'.$this->get_field_id( 'title' ).'">'.__('Title:').'</label>';
                echo '<input class="widefat" id="'.$this->get_field_id( 'title' ).'" name="'.$this->get_field_name( 'title' ).'" type="text" value="'.esc_attr($title).'">';
            echo '</p>';
            echo '<p>';
                echo '<input type="checkbox" class="checkbox" name="'.$this->get_field_name( 'show_date' ).'" id="'.$this->get_field_id( 'show_date' ).'" '.checked( $show_date, 'on', false).'>';
                echo '<label for="'.$this->get_field_id( 'show_date' ).'">'.__('Show dates', 'simple-event-calendar').'</label>';
            echo '</p>';

            echo '<p>';
                echo '<label for="'.$this->get_field_id( 'number' ).'">'.__('Events to show:').'</label>';
                echo '<input id="'.$this->get_field_id( 'number' ).'" class="tiny-text" name="'.$this->get_field_name( 'number' ).'" type="number" min="1" value="'.$number.'" size="3">';
            echo '</p>';
        }

        public function update( $new_instance, $old_instance ){
            $instance = array();
            $instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
            $instance['show_date'] = ( !empty( $new_instance['show_date'] ) ) ? strip_tags( $new_instance['show_date'] ) : 0;
            $instance['show_past'] = ( !empty( $new_instance['show_past'] ) ) ? strip_tags( $new_instance['show_past'] ) : 0;
            $instance['number'] = ( !empty( $new_instance['number'] ) ) ? strip_tags( $new_instance['number'] ) : 5;
            return $instance;
        }
    }

    function sec_register_widget(){
        register_widget( 'sec_widget' );
    }
    add_action( 'widgets_init', 'sec_register_widget' );

    // makes the widget available through shortcode in post content
    function sec_widget_shortcode( $atts = [], $content = null, $tag = '' ){
        $atts = array_change_key_case( (array)$atts, CASE_LOWER );
        $sec_atts = shortcode_atts(
            array(
                'show_date' => 1,
                'number' => 5,
            ), $atts, $tag
        );

        // getting posts
        $query_args = array(
            'post_type' => 'events',
            'post_status' => 'publish',
            'posts_per_page' => $sec_atts['number'],
            'meta_key' => 'sec_event_date_start',
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'meta_query' => array(
                'key' => 'sec_event_date_end',
                'value' => time()-60*60*24, // shows also todays events
                'compare' => '>='
            ),
        );
        $events = new WP_Query( $query_args );

        $r = array();
        if($events->have_posts()){
            $r[] = '<ul class="nav flex-column sec-widget">';
            while($events->have_posts()){
                $events->the_post();
                $r[] = '<li class="nav-item">';
                $r[] = '<a href="'.get_the_permalink().'" class="nav-link">'.get_the_title();
                if( $sec_atts['show_date'] ){
                    $r[] = '<br><echo span class="sec-widget-date">';
                    if( sec_has_start_date() ){
                        $r[] = '<i class="far fa-calendar-alt"></i> '.sec_date();
                    }

                    if( sec_has_time() ){
                        $r[] = ' <i class="far fa-clock"></i> '.sec_time();
                    }
                    $r[] = '</span>';
                }
                $r[] = '</a></li>';
            }
            $r[] = '<li class="nav-item text-center"><a class="nav-link" href="'.get_post_type_archive_link('events').'">'.__('Show all events', 'simple-event-calendar').'</a></li>';
            $r[] = '</ul>';

        }else{
            $r[] = '<h3>'.__('No events found', 'simple-event-calendar').'</h3>';
        }

        return implode('', $r);
    }
    add_shortcode('sec_widget', 'sec_widget_shortcode');
?>

<?php
    ////////////////////////////////////////////////////////////////
    // Author:      Juho Taskila
    // Date:        18.5.2021
    // Description: This file contains all the definitions for
    //              meta-boxes in events custom post type.
    ////////////////////////////////////////////////////////////////

    // CONTENTS
    ////////////////////////////////////////////////////////////////
    // 1(17). sec_add_meta_boxes
    // 2(34). sec_event_date_callback
    // 3(58). sec_event_venue_callback
    // 4(92). sec_save_events_meta
    ////////////////////////////////////////////////////////////////

    function sec_add_meta_boxes(){
        add_meta_box(
            'sec_event_date',
            __( 'Events date and time', 'simple-event-calendar'),
            'sec_event_date_callback',
            'events'
        );

        add_meta_box(
            'sec_event_venue',
            __( 'Events venue and address info', 'simple-event-calendar'),
            'sec_event_venue_callback',
            'events'
        );
    }
    add_action( 'add_meta_boxes', 'sec_add_meta_boxes');

    function sec_event_date_callback( $post ){
        wp_nonce_field( 'sec_event_update_meta', 'sec_event_date_nonce' );
        $event_start   = sec_format_date( get_post_meta( $post->ID, 'sec_event_date_start', true ) );
        $event_end     = sec_format_date( get_post_meta( $post->ID, 'sec_event_date_end', true ) );
        $event_time_start = sec_format_time( get_post_meta( $post->ID, 'sec_event_time_start', true ) );
        $event_time_end = sec_format_time( get_post_meta( $post->ID, 'sec_event_time_end', true ) );

        echo '<table>';
            echo '<tr>';
                echo '<td><label>'.__('Date', 'simple-event-calendar').'</label></td>';
                echo '<td><input type="text" placeholder="dd.mm.yyyy" id="sec_event_date_start" name="sec_event_date_start" value="'.esc_attr( $event_start ).'"></td>';
                echo '<td><label>-</label></td>';
                echo '<td><input type="text" placeholder="dd.mm.yyyy" id="sec_event_date_end" name="sec_event_date_end" value="'.esc_attr( $event_end ).'"></td>';
            echo '</tr>';
            echo '<tr>';
                echo '<td><label>'.__('Time', 'simple-event-calendar').'</label></td>';
                echo '<td><input type="text" placeholder="hh:mm" id="sec_event_time_start" name="sec_event_time_start" value="'.esc_attr( $event_time_start ).'"></td>';
                echo '<td><label>-</label></td>';
                echo '<td><input type="text" placeholder="hh:mm" id="sec_event_time_end" name="sec_event_time_end" value="'.esc_attr( $event_time_end ).'"></td>';
            echo '</tr>';
        echo '</table>';
        echo '<p>'.__( 'Leave the time blank if the event lasts a whole day.', 'simple-event-calendar' ).'</p>';
    }

    function sec_event_venue_callback( $post ){
        wp_nonce_field( 'sec_event_update_meta', 'sec_event_venue_nonce' );
        $event_venue        = get_post_meta( $post -> ID, 'sec_event_venue', true );
        $event_address      = get_post_meta( $post -> ID, 'sec_event_address', true );
        $event_postal_code  = get_post_meta( $post -> ID, 'sec_event_postalcode', true );
        $event_city         = get_post_meta( $post -> ID, 'sec_event_city', true );
        $event_maplink      = get_post_meta( $post -> ID, 'sec_event_maplink', true );

        echo '<table>';
            echo '<tr>';
                echo '<td><label>'.__( 'Venue name', 'simple-event-calendar' ).'</label></td>';
                echo '<td><input type="text" id="sec_event_venue" name="sec_event_venue" value="'.esc_attr( $event_venue ).'"></td>';
            echo '</tr>';
            echo '<tr>';
                echo '<td><label>'.__( 'Address', 'simple-event-calendar' ).'</label></td>';
                echo '<td><input type="text" id="sec_event_address" name="sec_event_address" value="'.esc_attr( $event_address ).'"></td>';
            echo '</tr>';
            echo '<tr>';
                echo '<td><label>'.__( 'Postal Code', 'simple-event-calendar' ).'</label></td>';
                echo '<td><input type="text" id="sec_event_postalcode" name="sec_event_postalcode" value="'.esc_attr( $event_postal_code ).'"></td>';
            echo '</tr>';
            echo '<tr>';
                echo '<td><label>'.__( 'City', 'simple-event-calendar' ).'</label></td>';
                echo '<td><input type="text" id="sec_event_city" name="sec_event_city" value="'.esc_attr( $event_city ).'"></td>';
            echo '</tr>';
            echo '<tr>';
                echo '<td><label>'.__( 'Map link', 'simple-event-calendar' ).'</label></td>';
                echo '<td><input type="text" id="sec_event_maplink" name="sec_event_maplink" value="'.esc_attr( $event_maplink ).'"></td>';
            echo '</tr>';
        echo '</table>';
        echo '<p>'.__( 'Specify the venues address info, all fields are optional.', 'simple-event-calendar' ).'</p>';
        echo '<p>'.__( 'Map link can be any shareable map link containing the address. Or a zoom or teams invite link.', 'simple-event-calendar' ).'</p>';
    }

    function sec_save_events_meta( $post ){
        $post_id = $post;

        if ( !current_user_can( 'edit_post', $post_id )){
            return $post_id;
        }
        if( !isset( $_POST['sec_event_date_nonce'] ) || !isset( $_POST['sec_event_venue_nonce'] ) ){
            return $post_id;
        }
        if( !wp_verify_nonce( $_POST['sec_event_date_nonce'], 'sec_event_update_meta') || !wp_verify_nonce( $_POST['sec_event_venue_nonce'], 'sec_event_update_meta') ){
            return $post_id;
        }


        // verifying that all fields are set
        if( !isset( $_POST['sec_event_date_start'] ) ||
            !isset( $_POST['sec_event_date_end']) ||
            !isset( $_POST['sec_event_time_start'] ) ||
            !isset( $_POST['sec_event_time_end'] ) ||
            !isset( $_POST['sec_event_venue'] ) ||
            !isset( $_POST['sec_event_address'] ) ||
            !isset( $_POST['sec_event_postalcode'] ) ||
            !isset( $_POST['sec_event_city'] ) ||
            !isset( $_POST['sec_event_maplink'] )
        ){
            return $post_id;
        }


        $event_meta['sec_event_date_start'] = sec_generate_timestamp( $_POST['sec_event_date_start'] );

        // if events end date is empty, set it to the same as start
        // when start and end days are the same, time timestamps must also be on the same day
        if( empty( $_POST['sec_event_date_end'] ) || empty( $_POST['sec_event_date_start'] ) ){
            $event_meta['sec_event_date_end']   = sec_generate_timestamp( $_POST['sec_event_date_start'] );
            $event_meta['sec_event_time_start'] = sec_generate_timestamp( $_POST['sec_event_date_start'], $_POST['sec_event_time_start'] );
            $event_meta['sec_event_time_end']   = sec_generate_timestamp(  $_POST['sec_event_date_start'], $_POST['sec_event_time_end'] );
        }else{
            $event_meta['sec_event_date_end']   = sec_generate_timestamp( $_POST['sec_event_date_end'] );
            $event_meta['sec_event_time_start'] = sec_generate_timestamp( $_POST['sec_event_date_start'], $_POST['sec_event_time_start'] );
            $event_meta['sec_event_time_end']   = sec_generate_timestamp( $_POST['sec_event_date_end'], $_POST['sec_event_time_end'] );
        }

        $event_meta['sec_event_venue']      = $_POST['sec_event_venue'];
        $event_meta['sec_event_address']    = $_POST['sec_event_address'];
        $event_meta['sec_event_postalcode'] = $_POST['sec_event_postalcode'];
        $event_meta['sec_event_city']       = $_POST['sec_event_city'];
        $event_meta['sec_event_maplink']    = $_POST['sec_event_maplink'];

        foreach($event_meta as $key => $value){
            if( 'revision' === $post->post_type ) {
                return;
            }

            if( get_post_meta( $post_id, $key, false ) ){
                update_post_meta( $post_id, $key, $value );
            }else{
                add_post_meta( $post_id, $key, $value );
            }

            if( !$value ){
                delete_post_meta( $post_id, $key );
            }
        }
    }
    add_action( 'save_post', 'sec_save_events_meta');
?>

<?php
    ////////////////////////////////////////////////////////////////
    // Author:      Juho Taskila
    // Date:        18.5.2021
    // Description: This file contains some helper
    //              functions for dates and times
    ////////////////////////////////////////////////////////////////

    // CONTENTS
    ////////////////////////////////////////////////////////////////
    // 1. sec_generate_timestamp
    // 2. sec_get_duration
    // 3. sec_format_date
    // 4. sec_format_time
    //
    //
    //
    //
    //
    //
    //
    //
    //
    //
    //
    //

    //////////////////////////////////////////////////////////
    // INTERNAL FUNCTIONS
    // Please use the loop functions instead if not
    // absolutely necessary to use these
    //////////////////////////////////////////////////////////

    //@return int (unix timestamp)
    function sec_generate_timestamp($date, $time = -1){

        // if date is empty, do not allow to generate timestamp for time
        if ( empty($date) || $date == 0 ){
            return 0;
        }

        if ( empty($time) ){
            return 0;
        }

        // if time is not set, eg. default value, make timestamp only from date
        if ( $time == -1 ){
            $timestamp = strtotime($date);
        }else{
            $timestamp = strtotime($date." ".$time);
        }

        // if timestamp creation failed, return 0
        if( $timestamp === false){
            return 0;
        }else{
            return $timestamp;
        }
    }

    //@return float
    function sec_get_duration( $timestamp_start, $timestamp_end ){
        $duration = $timestamp_end - $timestamp_start;
        $duration = $duration / 60 / 60;
        return $duration;
    }

    //@return string
    function sec_format_date( $timestamp ){
        if( !empty($timestamp) && $timestamp !== 0 && is_numeric($timestamp) ){
            return date("j.n.Y", $timestamp);
        }
        return "";
    }

    //@return string
    function sec_format_time( $timestamp ){
        if( !empty($timestamp) && $timestamp !== 0 && is_numeric($timestamp) ){
            return date("H:i", $timestamp);
        }
        return "";
    }


    //////////////////////////////////////////////////////////
    // LOOP FUNCTIONS
    // These functions only work inside the loop
    //////////////////////////////////////////////////////////

    //@return boolean
    function sec_is_multiday(){
        global $post;
        $post_id = $post -> ID;

        if(sec_has_start_date() && sec_has_end_date()){
            if ( get_post_meta( $post_id, 'sec_event_date_start', true ) !== get_post_meta( $post_id, 'sec_event_date_end', true) ){
                return true;
            }
        }
        return false;
    }

    //@return boolean
    function sec_has_start_date(){
        global $post;
        $post_id = $post -> ID;

        if( get_post_meta( $post_id, 'sec_event_date_start', true ) ){
            return true;
        }

        return false;
    }

    //@return string
    function sec_date(){
        global $post;
        $post_id = $post -> ID;

        if( sec_has_start_date() ){
            if( sec_is_multiday() ){
                return sec_format_date( get_post_meta( $post_id, 'sec_event_date_start', true) ).
                " - ".
                sec_format_date( get_post_meta( $post_id, 'sec_event_date_end', true) );
            }else{
                return sec_format_date( get_post_meta( $post_id, 'sec_event_date_start', true ) );
            }
        }
        return "";
    }

    //@return string
    function sec_time(){
        global $post;
        $post_id = $post -> ID;

        if( sec_has_time() ){
            return sec_format_time(  get_post_meta( $post_id, 'sec_event_time_start', true) ).
            " - ".
            sec_format_time(  get_post_meta( $post_id, 'sec_event_time_end', true) );
        }
        return "";
    }

    //@return boolean
    function sec_has_end_date(){
        global $post;
        $post_id = $post -> ID;

        if( get_post_meta( $post_id, 'sec_event_date_end', true ) ){
            return true;
        }

        return false;
    }

    //@return boolean
    function sec_has_start_time(){
        global $post;
        $post_id = $post -> ID;

        if( get_post_meta( $post_id, 'sec_event_time_start', true ) ){
            return true;
        }

        return false;
    }

    //@return boolean
    function sec_has_end_time(){
        global $post;
        $post_id = $post -> ID;

        if( get_post_meta( $post_id, 'sec_event_date_end', true ) ){
            return true;
        }

        return false;
    }

    //@return boolean
    function sec_has_time(){
        return sec_has_start_time() && sec_has_end_time();
    }

    //@return int (unix timestamp)
    function sec_start_timestamp(){
        global $post;
        $post_id = $post -> ID;

        if ( sec_has_start_time() ){
            return get_post_meta( $post_id, 'sec_event_time_start', true );
        }
        return 0;
    }

    //@return int (unix timestamp)
    function sec_end_timestamp(){
        global $post;
        $post_id = $post -> ID;

        if ( sec_has_end_time() ){
            return get_post_meta( $post_id, 'sec_event_time_end', true );
        }

        return 0;
    }

    //@return string
    function sec_duration(){
        if( sec_has_time() ){
            $duration = sec_end_timestamp() - sec_start_timestamp();
            $duration = round($duration / 3600, 0);

            if($duration >= 24){
                return __("multiple days", "simple-event-calendar");
            }else{
                return "~".$duration." h";
            }
        }
        return "";
    }

    //@return boolean
    function sec_has_maplink(){
        global $post;
        $post_id = $post -> ID;

        if( get_post_meta( $post_id, 'sec_event_maplink', true ) && !empty(get_post_meta( $post_id, 'sec_event_maplink', true )) ){
            return true;
        }

        return false;
    }

    //@return string
    function sec_maplink(){
        global $post;
        $post_id = $post -> ID;

        if( sec_has_maplink() ){
            return get_post_meta( $post_id, 'sec_event_maplink', true );
        }

        return "";
    }
?>

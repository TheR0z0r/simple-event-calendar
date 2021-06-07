<?php get_header(); ?>
<?php
    // upcoming events
    $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
    $args = array(
        'post_type' => 'events',
        'post_status' => 'publish',
        'paged' => $paged,
        'meta_key' => 'sec_event_date_start',
        'orderby' => 'meta_value_num',
        'order' => 'ASC',
        'meta_query' => array(
            'key' => 'sec_event_date_end',
            'value' => time()-60*60*24, // shows also todays events
            'compare' => '>='
        ),
    );

    // if showing past events, alter query
    if( isset( $_GET['action'] ) && $_GET['action'] == 'past' ){
        $args = array(
            'post_type' => 'events',
            'post_status' => 'publish',
            'paged' => $paged,
            'meta_key' => 'sec_event_date_start',
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'meta_query' => array(
                'key' => 'sec_event_date_end',
                'value' => time()-60*60*24,
                'compare' => '<='
            ),
        );
    }

    $events = new WP_Query( $args );
?>
<div class="container sec-events-container">

    <?php if( isset( $_GET['action'] ) && $_GET['action'] == 'past'): ?>
        <h1><?php _e('Past events', 'simple-event-calendar'); ?></h1>
    <?php else: ?>
        <h1><?php _e('Upcoming events', 'simple-event-calendar'); ?></h1>
    <?php endif; ?>

    <div class="row pt-4">
        <?php if($events->have_posts()) : while($events->have_posts()) : $events->the_post(); ?>
            <div class="col-lg-6 col-md-12 pb-4">
                <div class="card">
                    <?php if( has_post_thumbnail() ): ?>
                        <img class="card-img-top" src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>" alt="Card image cap">
                    <?php endif; ?>

                    <div class="card-body">
                        <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <p><?php echo wp_trim_words(get_the_content(), 16, '...'); ?></p>
                        <div class="row align-items-end">
                            <div class="col-7">
                                <p class="mb-0">
                                    <?php if ( sec_has_start_date() ): ?>
                                        <i class="far fa-calendar-alt"></i> <?php echo sec_date(); ?>
                                    <?php endif; ?>

                                    <?php if( sec_has_time() ): ?>
                                        <br><i class="far fa-clock"></i> <?php echo sec_time(); ?> (<?php echo sec_duration(); ?>)
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-5">
                                <p class="mb-0 text-right">
                                    <?php if( sec_has_maplink() ): ?>
                                        <a target="_blank" href="<?php echo sec_maplink(); ?>"><i class="fas fa-map-marker-alt"></i> <?php echo get_post_meta( get_the_ID(), 'sec_event_venue', true); ?></a>
                                    <?php else: ?>
                                        <i class="fas fa-map-marker-alt"></i> <?php echo get_post_meta( get_the_ID(), 'sec_event_venue', true); ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php endwhile; else: ?>
            <div class="col-12">
                <h3><?php _e('No events found', 'simple-event-calendar'); ?></h3>
            </div>
        <?php endif; ?>

        <div class="col-12 text-center sec-pagination">
            <?php
                echo paginate_links( array(
                    'base'         => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
                    'total'        => $events->max_num_pages,
                    'current'      => max( 1, get_query_var( 'paged' ) ),
                    'format'       => '?paged=%#%',
                    'show_all'     => false,
                    'type'         => 'plain',
                    'end_size'     => 2,
                    'mid_size'     => 1,
                    'prev_next'    => true,
                    'prev_text'    => sprintf( '%1$s', __( 'Previous events', 'simple-event-calendar' ) ),
                    'next_text'    => sprintf( '%1$s', __( 'Next events', 'simple-event-calendar' ) ),
                    'add_args'     => false,
                    'add_fragment' => '',
                ) );
            ?>
        </div>
    </div>
    <hr>

    <?php if( isset( $_GET['action'] ) && $_GET['action'] == 'past'): ?>
        <p class="text-center"><a href="?action=incoming"><?php _e('Show upcoming events', 'simple-event-calendar'); ?></a></p>
    <?php else: ?>
        <p class="text-center"><a href="?action=past"><?php _e('Show past events', 'simple-event-calendar'); ?></a></p>
    <?php endif; ?>

</div>
<?php get_footer(); ?>

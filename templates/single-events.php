<?php get_header(); ?>
<div class="container sec-events-container">
    <h2 class="entry-title"><?php the_title(); ?></h2>

    <div class="row pt-3">
        <?php if(have_posts()) : while(have_posts()) : the_post(); ?>
            <div class="col-lg-8 col-md-7 pb-4">
                <?php if( has_post_thumbnail() ): ?>
                    <img class="featured-image pb-3" src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>" alt="Featured image">
                <?php endif; ?>
                <?php the_content(); ?>

            </div>
            <div class="col-lg-4 col-md-5 pb-4">
                <div class="row">
                    <div class="col-md-12 col-sm-6">
                        <h2><?php _e('Date and time', 'simple-event-calendar'); ?></h2>
                        <p>
                            <?php if ( sec_has_start_date() ): ?>
                                <i class="far fa-calendar-alt"></i> <?php echo sec_date(); ?>
                            <?php endif; ?>

                            <?php if( sec_has_time() ): ?>
                                <br>
                                <i class="far fa-clock"></i> <?php echo sec_time(); ?> (<?php echo sec_duration(); ?>)
                            <?php endif; ?>

                        </p>
                    </div>
                    <div class="col-md-12 col-sm-6">
                        <h2><?php _e('Location', 'simple-event-calendar'); ?></h2>
                        <p>
                            <?php if( sec_has_maplink() ): ?>
                                <a target="_blank" href="<?php echo sec_maplink(); ?>"><i class="fas fa-map-marker-alt"></i> <?php echo get_post_meta( get_the_ID(), 'sec_event_venue', true); ?></a><br>
                            <?php else: ?>
                                <i class="fas fa-map-marker-alt"></i> <?php echo get_post_meta( get_the_ID(), 'sec_event_venue', true); ?><br>
                            <?php endif; ?>
                            <?php echo get_post_meta( get_the_ID(), 'sec_event_address', true ); ?><br>
                            <?php echo get_post_meta( get_the_ID(), 'sec_event_postalcode', true); ?>
                            <?php echo get_post_meta( get_the_ID(), 'sec_event_city', true) ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endwhile; else: ?>
            <div class="col-12">
                <h3><?php _e('No events found', 'simple-event-calendar'); ?></h3>
            </div>
        <?php endif; ?>
    </div>
    <hr>


</div>
<?php get_footer(); ?>

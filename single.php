<?php
/**
 * The Template for displaying all single posts.
 *
 * @package solo
 * @since solo 1.0
 */

get_header(); ?>

<div id="maincontentcontainer">

	<div id="primary" class="site-content row" role="main">

			<div class="col grid_12_of_12">

                            <div class="main-content">
                                
				<?php while ( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'content', get_post_format() ); ?>
                                
                                <?php endwhile; // end of the loop. ?>
                                
                                

                                <?php
                                // If comments are open or we have at least one comment, load up the comment template
                                if ( comments_open() || '0' != get_comments_number() ) {
                                        comments_template( '', true );
                                }
                                ?>
                                <?php get_sidebar(); ?>

                                <?php solo_content_nav( 'nav-below' ); ?>
\

                            </div> <!-- /.main-content -->

			</div> <!-- /.col.grid_12_of_12 -->
			

	</div> <!-- /#primary.site-content.row -->

</div> <!-- /#maincontentcontainer -->

<?php get_footer(); ?>

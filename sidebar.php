<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package solo
 * @since solo 1.0
 */
?>
<?php if(is_active_sidebar('sidebar-main')) { ?>
	<div class="col grid_12_of_12 sidebar">

		<div id="secondary"  role="complementary">
			<?php
			do_action( 'before_sidebar' );
                        
                        if (is_singular('download') || is_post_type_archive('download')) {
                            if (is_active_sidebar('sidebar-shop')) {
                                dynamic_sidebar('sidebar-shop');  
                            }
                        }

			if ( is_active_sidebar( 'sidebar-main' ) ) {
				dynamic_sidebar( 'sidebar-main' );
			}
			?>

		</div> <!-- /#secondary.widget-area -->

	</div> <!-- /.col.grid_4_of_12 -->
<?php } ?>

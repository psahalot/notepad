<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id #maincontentcontainer div and all content after.
 * There are also four footer widgets displayed. These will be displayed from
 * one to four columns, depending on how many widgets are active.
 *
 * @package solo
 * @since solo 1.0
 */
?>

<div id="footercontainer">

    <?php
    // Count how many footer sidebars are active so we can work out how many containers we need
    $footerSidebars = 0;
    for ($x = 1; $x <= 3; $x++) {
        if (is_active_sidebar('sidebar-footer' . $x)) {
            $footerSidebars++;
        }
    }

    // If there's one or more one active sidebars, create a row and add them
    if ($footerSidebars > 0) {
        ?>
        <footer class="site-footer row" role="contentinfo">
            <?php
            // Work out the container class name based on the number of active footer sidebars
            $containerClass = "grid_" . 12 / $footerSidebars . "_of_12";

            // Display the active footer sidebars
            for ($x = 1; $x <= 3; $x++) {
                if (is_active_sidebar('sidebar-footer' . $x)) {
                    ?>
                    <div id="<?php echo 'footer-widget' . $x; ?>" class="col <?php echo $containerClass ?>">
                        <div class="widget-area" role="complementary">
                    <?php dynamic_sidebar('sidebar-footer' . $x); ?>
                        </div>
                    </div> <!-- /.col.<?php echo $containerClass ?> -->
            <?php }
        }
        ?>
        </footer> <!-- /.site-footer.row -->
<?php } ?>

</div> <!-- /.footercontainer -->

    
    <div class="footer-wrap">
        <div class="footer-wrap-content">
            <div class="row smallprint left">
                <p>
                   <a href="<?php $notepad_theme = wp_get_theme(); echo $notepad_theme->get( 'AuthorURI' ); ?>">
                            <?php _e('Solo WordPress theme by IdeaBox','solo'); ?>
                    </a>
                </p>
            </div>
            <?php if (get_theme_mod('solo_footer_footer_text') != '') { ?>
                <div class="row smallprint right"><?php echo wpautop(get_theme_mod('solo_footer_footer_text')); ?></div>
           <?php } ?>
        </div>
    </div> <!-- /.footer-wrap -->

</div> <!-- /.#wrapper.hfeed.site -->

<?php wp_footer(); ?>
</body>

</html>

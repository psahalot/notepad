<?php
/**
 * The template for displaying posts in the Chat post format
 *
 * @package solo
 * @since solo 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="box-wrap">
	<header class="entry-header">
		<?php solo_posted_on(); ?>
	</header> <!-- /.entry-header -->
	<div class="entry-content">
		<?php the_content( wp_kses( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'solo' ), array( 
			'span' => array( 
				'class' => array() ) 
			) ) ); ?>
		<?php wp_link_pages( array(
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'solo' ),
			'after' => '</div>',
			'link_before' => '<span class="page-numbers">',
			'link_after' => '</span>'
		) ); ?>
	</div> <!-- /.entry-content -->
    </div>
   
    
        <footer class="entry-meta">
		<?php if ( is_singular() ) {
			// Only show the tags on the Single Post page
			solo_entry_meta();
		} ?>
		
	</footer> <!-- /.entry-meta -->
    
</article> <!-- /#post -->

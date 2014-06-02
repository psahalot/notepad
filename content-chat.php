<?php
/**
 * The template for displaying posts in the Chat post format
 *
 * @package stream
 * @since stream 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="box-wrap">
	<header class="entry-header">
		<?php stream_posted_on(); ?>
	</header> <!-- /.entry-header -->
	<div class="entry-content">
		<?php the_content( wp_kses( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'stream' ), array( 
			'span' => array( 
				'class' => array() ) 
			) ) ); ?>
		<?php wp_link_pages( array(
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'stream' ),
			'after' => '</div>',
			'link_before' => '<span class="page-numbers">',
			'link_after' => '</span>'
		) ); ?>
	</div> <!-- /.entry-content -->
    </div>
   
    
        <footer class="entry-meta">
		<?php if ( is_singular() ) {
			// Only show the tags on the Single Post page
			stream_entry_meta();
		} ?>
		
	</footer> <!-- /.entry-meta -->
    
</article> <!-- /#post -->

<?php
/**
 * The template for displaying posts in the Audio post format
 *
 * @package solo
 * @since solo 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <?php $audiourl = get_post_meta($post->ID, 'audiourl', true); 
	if ( $audiourl != '' ) : ?>
	
		<div class="featured-media">
		
			<audio controls="controls" id="audio-player">
			
				<source src="<?php echo $audiourl; ?>" />
				
			</audio>
		
		</div>
	
	<?php endif; ?>
    <div class="box-wrap">
	<header class="entry-header">
		<?php if ( is_single() ) { ?>
			<h1 class="entry-title"><?php the_title(); ?></h1>
		<?php }
		else { ?>
			<h1 class="entry-title">
				<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( esc_html__( 'Permalink to %s', 'solo' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
			</h1>
		<?php } // is_single() ?>
		<?php solo_posted_on(); ?>
	</header> <!-- /.entry-header -->
	<div class="entry-content">
		<div class="audio-content">
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
		</div><!-- /.audio-content -->
	</div> <!-- /.entry-content -->
    </div>
        
        <?php if(is_single()) { ?>
            <div class="tag-meta">
                 <?php if ( is_singular() ) {
                            // Only show the tags on the Single Post page
                            solo_entry_meta();
                    } ?>
            </div>
        <?php } ?>
    
        <footer class="entry-meta">
	
		<?php if ( is_singular() && get_the_author_meta( 'description' ) && is_multi_author() ) {
			// If a user has filled out their description and this is a multi-author blog, show their bio
			get_template_part( 'author-bio' );
		} ?>
	</footer> <!-- /.entry-meta -->
   
</article> <!-- /#post -->

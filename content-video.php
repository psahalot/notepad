<?php
/**
 * The template for displaying posts in the Video post format
 *
 * @package notepad
 * @since notepad 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <?php $videourl = get_post_meta($post->ID, 'videourl', true); if ( $videourl != '' ) : ?>

			<div class="featured-media">
			
				<?php if (strpos($videourl,'.mp4') !== true) : ?>
			
					<?php 
					
						$embed_code = wp_oembed_get($videourl); 
						
						echo $embed_code;
						
					?>
															
				<?php elseif (strpos($videourl,'.mp4') !== false) : ?>
					
					[video src="<?php echo $videourl; ?>"]
						
				<?php endif; ?>
				
			</div>
		
		<?php endif; ?>
    <div class="box-wrap">
	<header class="entry-header">
		<?php if ( is_single() ) { ?>
			<h1 class="entry-title"><?php the_title(); ?></h1>
		<?php }
		else { ?>
			<h1 class="entry-title">
				<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( esc_html__( 'Permalink to %s', 'notepad' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
			</h1>
		<?php } // is_single() ?>
		<?php notepad_posted_on(); ?>
	</header> <!-- /.entry-header -->
	<div class="entry-content">
		<?php the_content( wp_kses( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'notepad' ), array( 
			'span' => array( 
				'class' => array() )
			) ) ); ?>
		<?php wp_link_pages( array(
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'notepad' ),
			'after' => '</div>',
			'link_before' => '<span class="page-numbers">',
			'link_after' => '</span>'
		) ); ?>
	</div> <!-- /.entry-content -->

	<footer class="entry-meta">
		<?php if ( is_singular() ) {
			// Only show the tags on the Single Post page
			notepad_entry_meta();
		} ?>
		<?php edit_post_link( esc_html__( 'Edit', 'notepad' ) . ' <i class="fa fa-angle-right"></i>', '<div class="edit-link">', '</div>' ); ?>
		<?php if ( is_singular() && get_the_author_meta( 'description' ) && is_multi_author() ) {
			// If a user has filled out their description and this is a multi-author blog, show their bio
			get_template_part( 'author-bio' );
		} ?>
	</footer> <!-- /.entry-meta -->
    </div>
</article> <!-- /#post -->

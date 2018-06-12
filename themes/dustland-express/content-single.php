<?php
/**
 * @package Dustland Express
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    
	<header class="entry-header">
		<?php the_title( '<h3 class="entry-title">', '</h3>' ); ?>

		<div class="entry-meta">
			<?php dustlandexpress_posted_on(); ?>
		</div><!-- .entry-meta -->
	</header><!-- .entry-header -->
    
    <?php if( has_post_thumbnail() ): ?>
        <div class="entry-thumbnail"><?php the_post_thumbnail( 'full' ) ?></div>
    <?php endif; ?>

	<div class="entry-content">
		<?php the_content(); ?>
		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'dustland-express' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php dustlandexpress_entry_footer(); ?>
	</footer><!-- .entry-footer -->
    
</article><!-- #post-## -->

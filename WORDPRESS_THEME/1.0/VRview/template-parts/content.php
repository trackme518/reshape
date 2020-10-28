<?php
/**
 * The default template for displaying content
 *
 * @package Cambium
 */
?>

<div class="post-wrapper-hentry">
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="post-content-wrapper post-content-wrapper-archive">

			<div class="entry-header-wrapper">
				<header class="entry-header">
					<?php the_title( sprintf( '<h1 class="entry-title"><a href="%1$s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' ); ?>
				</header><!-- .entry-header -->

				<?php if ( 'post' === get_post_type() ) : // For Posts ?>
				<div class="entry-meta entry-meta-header-after">
					<?php
					cambium_posted_by();
					cambium_posted_on();
					cambium_sticky_post();
					?>
				</div><!-- .entry-meta -->
				<?php endif; ?>
			</div><!-- .entry-header-wrapper -->

			<?php cambium_post_thumbnail(); ?>

			<?php if ( cambium_has_excerpt() || cambium_has_read_more_label() ) : ?>
			<div class="entry-data-wrapper">
				<?php if ( cambium_has_excerpt() ) : ?>
				<div class="entry-summary">
					<?php the_excerpt(); ?>
				</div><!-- .entry-summary -->
				<?php endif; ?>

				<?php
				if ( cambium_has_read_more_label() ) {
					cambium_read_more_link();
				}
				?>
			</div><!-- .entry-data-wrapper -->
			<?php endif; ?>

		</div><!-- .post-content-wrapper -->
	</article><!-- #post-## -->
</div><!-- .post-wrapper-hentry -->

<?php
/**
 * The template part for displaying a message that posts cannot be found.
 *
 * @package Cambium
 */
?>

<div class="post-wrapper-hentry">
	<section class="no-results not-found">
		<div class="post-content-wrapper post-content-wrapper-single post-content-wrapper-single-notfound">

			<div class="page-content">
				<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

					<p><?php printf( wp_kses( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'cambium' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( admin_url( 'post-new.php' ) ) ); ?></p>

				<?php elseif ( is_search() ) : ?>

					<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'cambium' ); ?></p>
					<p><?php get_search_form(); ?></p>

				<?php else : ?>

					<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'cambium' ); ?></p>
					<p><?php get_search_form(); ?></p>

				<?php endif; ?>
			</div><!-- .page-content -->

		</div><!-- .post-content-wrapper -->
	</section>
</div><!-- .post-wrapper-hentry -->

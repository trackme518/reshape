<?php
/**
 * The template used for displaying page content in 404.php
 *
 * @package Cambium
 */
?>

<div class="post-wrapper-hentry">
	<section class="error-404 not-found">
		<div class="post-content-wrapper post-content-wrapper-single post-content-wrapper-single-404">

			<div class="page-content">
				<p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'cambium' ); ?></p>

				<?php
				// Search Widget
				the_widget(
					'WP_Widget_Search',
					array(
						'title' => esc_html__( 'Search', 'cambium' )
					),
					array(
						'before_title' => '<h2 class="widget-title">',
						'after_title' => '</h2>'
					)
				);
				?>

				<?php
				// Recent Posts Widget
				the_widget(
					'WP_Widget_Recent_Posts',
					array(
						'title' => esc_html__( 'Recent Posts', 'cambium' ),
					),
					array(
						'before_title' => '<h2 class="widget-title">',
						'after_title' => '</h2>'
					)
				);
				?>

				<?php
				// Archives Widget
				the_widget(
					'WP_Widget_Archives',
					array(
						'title'    => esc_html__( 'Archives', 'cambium' ),
						'dropdown' => 1,
					),
					array(
						'before_title' => '<h2 class="widget-title">',
						'after_title' => '</h2>'
					)
				);
				?>

				<?php
				// Tags Widget
				the_widget(
					'WP_Widget_Tag_Cloud',
					array(
						'title' => esc_html__( 'Tags', 'cambium' ),
					),
					array(
						'before_title' => '<h2 class="widget-title">',
						'after_title' => '</h2>'
					)
				);
				?>
			</div><!-- .page-content -->

		</div><!-- .post-content-wrapper -->
	</section><!-- .error-404 -->
</div><!-- .post-wrapper-hentry -->

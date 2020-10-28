<header id="masthead" class="site-header" role="banner">
	<div class="container">
		<div class="row">
			<div class="col">

				<div class="site-header-inside-wrapper">
					<div class="site-branding-wrapper">

						<div class="site-branding">
							<?php if ( is_front_page() && is_home() ) : ?>
							<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
							<?php else : ?>
								<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
							<?php endif; ?>

							<?php
							$description = get_bloginfo( 'description', 'display' );
							if ( $description || is_customize_preview() ) :
							?>
							<p class="site-description"><?php echo esc_html( $description ); /* WPCS: xss ok. */ ?></p>
							<?php endif; ?>
						</div>
					</div><!-- .site-branding-wrapper -->

				<nav id="site-navigation" class="main-navigation" role="navigation">
                <div class="main-navigation-inside">
                
                		<?php
                		wp_nav_menu(
                        array(
                          'theme_location' => 'header-menu',
                          'container_class' => 'site-header-menu',
                          'menu_class'      => 'header-menu sf-menu',
                		  'menu_id'         => 'menu-1',
                		  'depth'           => 3
                          )
                        );
                		?>
                
                </div><!-- .main-navigation-inside -->
                </nav><!-- .main-navigation -->

				</div><!-- .site-header-inside-wrapper -->

			</div><!-- .col -->
		</div><!-- .row -->
	</div><!-- .container -->
</header><!-- #masthead -->

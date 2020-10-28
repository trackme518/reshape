<?php
/**
 * The template for displaying site navigation
 * @package Cambium
 */
?>

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

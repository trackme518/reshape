</div><!-- #content -->

<footer id="sitefooter" class="site-footer">

<?php
echo do_shortcode('[logoshowcase]');
?> 

<div class="footer-container">

 <nav id="footer-navigation" class="footer-navigation" role="navigation">
                <div class="_row"> <!-- setting height to overcome the stupid logo slideshow not declared properly-->
                <div class="footer-navigation-inside">
                
                		<?php
                		wp_nav_menu(
                        array(
                          'theme_location' => 'footer-menu',
                          'container_class' => 'site-footer-menu',
                          'menu_class'      => 'sf-menu',
                		  'menu_id'         => 'menu-footer',
                		  'depth'           => 3,
                          'add_li_class'  => '_column'
                          )
                        );
                		?>
                </div><!-- .main-navigation-inside --> 
               
                </div> <!-- end row-->
               
            
                 
                </nav><!-- .main-navigation -->
               
</div><!-- end footer container-->

</footer>
                              
</div><!-- #page .site-wrapper -->
                                
<!-- START THEME INJECTED SCRIPTS AND CSS -->
<?php wp_footer(); ?>
<!-- END THEME INJECTED SCRIPTS AND CSS -->
</body>
</html>
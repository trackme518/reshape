<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Virual Exhibition - ReShape</title>
<meta name="author" content="Vojtech Leischner www.trackmeifyoucan.com All rights reserved">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<!-- START THEME INJECTED SCRIPTS AND CSS -->
<?php wp_head(); ?>
<!-- END THEME INJECTED SCRIPTS AND CSS -->

<style>

/*--------------------------------------------------------------------*/
*,
*::before,
*::after {
  box-sizing: border-box;
}

body{
padding: 0px;
margin: 0px;
font-family:'Lato';
font-size: 16px;
color: #223254;
background-color: <?php echo get_theme_mod( 'color-bars', '#000000' ); ?>;
}

.post-wrapper{ background-color: white; }

/* position: absolute; */
#aframecontainer{
width: 100%;
height: 90%;
}

#hide{
position: absolute;
width: 50%;
left: 50%;
height: 100%;
display: block;
background-color: red;
z-index: 10;
}

._column {
  float: left;
  padding-right: 1em;
}

._column-right{
  float: right;
  margin-right: 5px;
}

._row{
  margin-top: 1em;

}

._row:after {
  content: "";
  display: table;
  clear: both;
}

/* Responsive layout - makes the two columns stack on top of each other instead of next to each other */
@media screen and (max-width: 600px) {
  .column {
    width: 100%;
  }
}

/*
#gui {position: absolute; top: 0px; left: 0px; z-index: 50;  width: 100%; background-color: rgba(255, 255, 255, 0.75); padding: 0px; margin: 0px;}
*/
#gui {z-index: 50;  width: 100%; background-color: rgba(255, 255, 255, 1.0); padding: 0px; margin: 0px; padding-left: 1em;}

.wpls-logo-showcase, .wpls-logo-showcase-slider-wrp{max-height: 100px !important; margin-top: 1em;}

/*
.slider{ width: 145px;}
*/

.modal { position: absolute; z-index: -1; width: 100%; min-height: 100%; background: rgba(255, 255, 255, 0.7); opacity: 0; transition: opacity 0.5s ease; padding-top: 1em;}

/*
.modal { position: absolute; z-index: -1; width: 100%; height: 90%; overflow: auto; background: rgba(255, 255, 255, 0.7); opacity: 0; transition: opacity 0.5s ease; padding-top: 1em;}
*/

.modal.show {opacity: 1; z-index: 100;}
.modal.hide {opacity: 0; z-index: 100;}
.modal.down {z-index: -1;}

#innerhtml, .post-wrapper-hentry { max-width: 720px;  margin: auto; padding: 1em 0em 1em 0em;}
#innerhtml .wp-block-image {padding: 0px; margin: 0px;} 
#innerhtml .wp-block-image img{ max-height: 512px; max-width: 720px; width: auto; height: auto;}


#btn-close{
text-align: center; 
position: fixed; 
left: 50%; top: 90%; 
font-size: 1.5em; 
color: <?php echo get_theme_mod( 'color-hover', '#ffccff' ); ?>; 
background-color: rgba(255,255,255,1.0); 
border-radius: 25px; padding: 5px;
box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
}
#btn-close:hover {color: #000000;}

/*
.overlay {
  position: absolute;
  top: 0%;
  left: 100%;
}
*/
.a-loader-title {
   color: white;
 }
/*nice checkboxes----------------------------------------------------------------------------*/
.cboxB input:checked ~ label:before { content: "\2605"; }
.cboxB input { display: none; }

/* CUSTOM SQUARE */
.cboxB label:before {
  display: inline-block;
  content: "\00a0"; /* Blank space */
  width: 20px;
  margin-right: 5px;
  text-align: center;
  background: #f2f2f2;
}

/* OPTIONAL COSMETICS */
.cboxB {
  font-size: 16px;
  line-height: 20px;
  margin: 10px;
}
.cboxB label { cursor: pointer; }
.cboxB label:hover::before { background: #ddd; }


.sf-menu{
  list-style-type: none;
  display: inline;
  margin: 0;
  padding: 0;
}

li .menu-item{
display: inline;
}

.header-container, .footer-container{
padding-left: 1em;
padding-bottom: 1em;
}

#masthead, #sitefooter {
background-color: <?php echo get_theme_mod( 'color-bars', '#000000' ); ?>;
color: <?php echo get_theme_mod( 'color-text', '#000000' ); ?>; 
}

#masthead a, #sitefooter a{
text-decoration: none;
color: <?php echo get_theme_mod( 'color-text', '#000000' ); ?>;
}
             
#masthead a:hover, #sitefooter a:hover{
color: <?php echo get_theme_mod( 'color-hover', '#ffccff' ); ?>;
}

.site-title a{
text-decoration: none;
color: <?php echo get_theme_mod( 'color-text', '#000000' ); ?>;
}

.main-navigation-inside, .footer-navigation-inside{
font-weight: bold;
}

.site-wrapper{
margin: 0px;
padding: 0px;
}

/* PRETTY SELECT */
select {
  width: 150px;
  margin-top: 5px;
  padding: 0px;
  font-size: 16px;
  border: 1px solid #CCC;
  height: 30px;
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  background: "\f013"; /* url(https://stackoverflow.com/favicon.ico) 96% / 15% no-repeat #FFF; */
  
}

select:before {
  display: inline-block;
  content: "\2605";/
  width: 20px;
  margin-right: 5px;
  text-align: center;
  background: #f2f2f2;
}
/*
#autorotate{ width: 50px; height: 50px;}
.checkico{ width: 50px; height: 50px; }
*/
.ficon{ font-size: 25px; margin-top: 7px; cursor: pointer; color: black; width: 25px; height:25px;}
.ficon:hover {color: <?php echo get_theme_mod( 'color-hover', '#ffccff' ); ?>; }

svg {width: 25px; height:25px; fill: <?php echo get_theme_mod( 'color-text', '#000000' ); ?>; cursor: pointer; margin-top: 7px; }
svg:hover{ fill: <?php echo get_theme_mod( 'color-hover', '#ffccff' ); ?>; }

.custom-logo{ max-height: 100px; max-width: 400px; width: auto; height: auto;}
 </style>

</head>
<body>

<div id="page" class="site-wrapper site">

<header id="masthead" class="site-header" role="banner">
	<div class="header-container">
		
        <div class="_row">
        <div class="_column">
        
				<div class="site-header-inside-wrapper">
					<div class="site-branding-wrapper">

						<div class="site-branding">
							
              <?php
              if( function_exists( 'the_custom_logo' ) && has_custom_logo() ){ 
              echo '<div class="logo">' . get_custom_logo() . '</div>';
              }else{
              //echo '<div class="logo">' . get_custom_logo() . '</div>';
              echo '<h1 class="site-title"><a href="' . esc_url( home_url( '/' ) ) . '" title="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . '" rel="home">' . get_bloginfo( 'name' ) . '</a></h1>';
              } 
              ?>

              
              
							<?php
							$description = get_bloginfo( 'description', 'display' );
							if ( $description || is_customize_preview() ) :
							?>
							<p class="site-description"><?php echo esc_html( $description ); /* WPCS: xss ok. */ ?></p>
							<?php endif; ?>
						</div>
					</div><!-- .site-branding-wrapper -->
				</div><!-- .site-header-inside-wrapper -->
           
           <!-- 
            </div>
            <div class="_row">
            -->
            </div><!-- _column -->
      
            <nav id="site-navigation" class="main-navigation" role="navigation">
                <div class="main-navigation-inside">
                    <ul>
                		<?php
                		wp_nav_menu(
                        array(
                          'theme_location' => 'header-menu',
                          'container_class' => 'site-header-menu',
                          'menu_class'      => 'sf-menu',
                		  'menu_id'         => 'menu-header',
                		  //'depth'           => 0,
                          'add_li_class'  => '_column'
                          )
                        );
                		?>
                     </ul>
                </div><!-- .main-navigation-inside -->
                </nav><!-- .main-navigation -->

            
		</div><!-- .row -->
	</div><!-- .container -->
</header><!-- #masthead -->

<div id="content" class="site-content">
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
}

/* position: absolute; */
#aframecontainer{

width: 100%;
height: 100%;

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

.column {
  float: left;
  margin-left: 5px;
}

.column-right{
  float: right;
  margin-right: 5px;
}

.row{
padding-top: 5px;
padding-bottom: 5px;
}


.row:after {
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


#gui {z-index: 50;  width: 100%; background-color: rgba(255, 255, 255, 1.0); padding: 0px; margin: 0px; margin-left: 1em;}

.slider{ width: 145px;}

.modal { position: absolute; z-index: -1; width: 100%; min-height: 100%; background: #FFFFFF;opacity: 0; transition: opacity 0.5s ease; }
.modal.show {opacity: 1; z-index: 100;}
.modal.hide {opacity: 0; z-index: 100;}
.modal.down {z-index: -1;}
/*
#innerhtml { position: relative;  width: 600px;  top: 0px; left: 50%; transform: translate(-50%, 0%); }
#innerhtml img{ display: block; max-width: 600px; margin: 0px; padding: 0px;}         
*/

#btn-close{color: #fff; text-align: center; position: absolute; right: 0px; top: 0px; background-color: #bfbfbf; font-family: monospace; font-size: 2em; line-height: 0.8em; padding: 5px;}
#btn-close:hover {color: #000000; background-color: #ffffff;}

.overlay {
  position: absolute;
  top: 0%;
  left: 100%;
}

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
margin-left: 1em;
padding-bottom: 1em;
}

#masthead, #sitefooter {
background-color: black;
color: white;
}

#masthead a, #sitefooter a{
text-decoration: none;
color: white;
}

#masthead a:hover, #sitefooter a:hover{
color: #ffccff;
}

.site-title a{
text-decoration: none;
color: white;
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
.ficon{ font-size: 25px; margin-top: 7px; cursor: pointer; }




 </style>

</head>
<body>

<div id="page" class="site-wrapper site">

<header id="masthead" class="site-header" role="banner">
	<div class="header-container">
		
        <div class="row">
				<div class="site-header-inside-wrapper">
					<div class="site-branding-wrapper">

						<div class="site-branding">
							<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
							<?php
							$description = get_bloginfo( 'description', 'display' );
							if ( $description || is_customize_preview() ) :
							?>
							<p class="site-description"><?php echo esc_html( $description ); /* WPCS: xss ok. */ ?></p>
							<?php endif; ?>
						</div>
					</div><!-- .site-branding-wrapper -->
				</div><!-- .site-header-inside-wrapper -->
            </div><!-- .row -->
            
            <div class="row">
      
            <nav id="site-navigation" class="main-navigation" role="navigation">
                <div class="main-navigation-inside">
                
                		<?php
                		wp_nav_menu(
                        array(
                          'theme_location' => 'header-menu',
                          'container_class' => 'site-header-menu',
                          'menu_class'      => 'sf-menu',
                		  'menu_id'         => 'menu-header',
                		  'depth'           => 3,
                          'add_li_class'  => 'column'
                          )
                        );
                		?>
                
                </div><!-- .main-navigation-inside -->
                </nav><!-- .main-navigation -->

            
		</div><!-- .row -->
	</div><!-- .container -->
</header><!-- #masthead -->

<div id="content" class="site-content">
<?php get_header(); ?>

<!-- display the 2D html here in overlay -->
<div class="modal" id="modal"></div>

<!-- interactive sliders for tSNE -->
<!--
<div id="gui">   
  <div class="row">
     <div class="column"> <button onclick="updatetsne()">RUN AGAIN</button> </div>
     <div class="column"> 
          <span>Rotate: </span>
          <label class="toggle">
            <input id="autorotate" type="checkbox"/ onclick="checkAutoRotate()" checked>
            <span class="slidertoggle"></span>
          </label>
       </div>
     <div class="column"> <input id="iterations" class="slider" type="range" min="1" max="3000" value="1000"> <div>iterations: <span id="iterations_val">1000</span></div>  </div>
     <div class="column"> <input id="perplex" class="slider" type="range" min="1" max="100" value="5"> <div>perplexity: <span id="perplex_val">5</span></div> </div>
     <div class="column"> <input id="learnrate" class="slider" type="range" min="10" max="1000" value="100"> <div>learning rate: <span id="learnrate_val">100</span></div> </div>
     <div class="column">  <input type="checkbox" id="vehicle1" name="vehicle1" value="Bike"><label for="vehicle1"> TBA  filtering categories</label> </div>
  </div> 
</div>
-->
<?php

$labelsize = 10;
//$labelcolor = '#000000';
$usetags = false;

$option_classifier = get_theme_mod( 'classifier', 'categories' );  //second arg is default to fallback to
$option_spread = get_theme_mod( 'spread', '10' );
$option_offset =  get_theme_mod( 'random_offset', '2' );
$iterations = get_theme_mod( 'iterations', '500' );
$camdistance = get_theme_mod( 'camdistance', '15' );
$learnrate = get_theme_mod( 'learnrate', '100' );
$perplexity = get_theme_mod( 'perplexity', '5' );
$labelcolor = get_theme_mod( 'color-label', '#000000' );
$bcgcolor = get_theme_mod( 'color-background', '#FFFFFF' );

echo '<script>console.log("Theme options=> classifier:'.$option_classifier.';  spread:'.$option_spread.'; offset:'.$option_offset.'; iterations:'.$iterations.'; camdistance:'.$camdistance.'")</script>';

//sanitize and convert theme option to boolean
if( $option_classifier == 'tags' ){
$usetags = true;
}
if( $option_classifier == 'categories' ){
$usetags = false;
}

// the wp query to db - get all posts
$wpb_all_query = new WP_Query(array('post_type'=>'post', 'post_status'=>'publish', 'posts_per_page'=>-1)); 

$assetshtml = ""; //set all images as assets to load them beforehand
$entities = "";  //in this var we save all a-frame html entities

//some weird wordpress taxonomy - get rid of it and put the name of all tags in to simple array
$alltagsArray = array(); //this will hold just names of tags/categories- declare and init
$alltagsArrayParsed = array(); //this hold the same but discard values aftre ":" delimeter from tag names

//$printAlltagsArray = array();
//$stringAlltagsArray = '';

if($usetags){
    $alltagsArray = get_tags( array( 'fields' => 'names' ) ); //get all wp tags used of all times
}else{
    $alltagsArray = get_categories( array( 'fields' => 'names',  'hide_empty' => false ) ); //include even unused categories 
}

$usedtagsmenu = "";
$includetagsmenu = "";

$filteringmenu = '<div id="gui">
<div class="row">
<div class="column"> <button onclick="updatetsne()">UPDATE</button> </div>
<div class="column"> <span>Rotate: </span> <label class="toggle"> <input id="autorotate" type="checkbox"/ onclick="checkAutoRotate()" checked> <span class="slidertoggle"></span> </label> </div>
<div class="column"> <select name="filteroption" id="filteroption" onchange="changeFilteringMenu()">
    <option value="mustincludemenu">display only:</option>
    <option value="usedtagsmenu">used classifiers:</option>
  </select> </div>
       ';

//------------------------------------------------------------------------------
//create filtering menu for all used tags/categories AND parse tags for values---------------------------------------------------
for ($f = 0; $f < sizeof($alltagsArray); $f++) {
   $tagname = $alltagsArray[$f];
   $parsetagname = explode(":", $tagname);
     if(  sizeof($parsetagname) > 1 ){  //check if tag contains ":" delimeter - the value after delimeter is not tag name but value - get rid of it
        $tagname = $parsetagname[0];
     } 
   
   $printAlltagsArray .= $tagname . ',';
   array_push($alltagsArrayParsed, $tagname);
 
   
   $usedtagsmenu .= '<div class="column">  <div class="cboxB"> <input class="filterbytag animatedcheck" type="checkbox" id="filter-'.$f.'" name="'.$alltagsArrayParsed[$f].'" checked><label for="'.$alltagsArrayParsed[$f].'">'.$alltagsArrayParsed[$f].'</label> </div> </div>';
   //$usedtagsmenu .= '<div class="column"> <label for="'.$alltagsArrayParsed[$f].'">'.$alltagsArrayParsed[$f].'</label><input id="filter-'.$f.'" name="'.$alltagsArrayParsed[$f].'" type="checkbox"  class="imagetoggle visually-hidden filterbytag" checked><div class="control-me"></div>  </div>';
   //$usedtagsmenu .= '<div class="column"> <input class="filterbytag" type="checkbox" id="filter-'.$f.'" name="'.$alltagsArrayParsed[$f].'" checked><label for="'.$alltagsArrayParsed[$f].'">'.$alltagsArrayParsed[$f].'</label> </div>';  
  
   $includetagsmenu .= '<div class="column"> <div class="cboxB"> <input class="displaybytag animatedcheck" type="checkbox" id="display-'.$f.'" name="'.$alltagsArrayParsed[$f].'" checked><label for="'.$alltagsArrayParsed[$f].'">'.$alltagsArrayParsed[$f].'</label> </div> </div>';
  //$includetagsmenu .= '<div class="column"> <input class="displaybytag" type="checkbox" id="display-'.$f.'" name="'.$alltagsArrayParsed[$f].'" checked><label for="'.$alltagsArrayParsed[$f].'">'.$alltagsArrayParsed[$f].'</label> </div>'; 
}

$printAlltagsArray = substr($printAlltagsArray, 0, -1); //get rid of comma at the end
//---------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------------------
//$printAlltagsArray =  implode(',', $alltagsArray); //stzringify names of all tags
                                                                         //$includetagsmenu
$filteringmenu .= '<div id="mustincludemenu">' . $includetagsmenu . '</div>' . '<div id="usedtagsmenu" style="display: none;">' . $usedtagsmenu . '</div>' . '</div></div>';

echo $filteringmenu; //display filtering menu

$labels = array(); //this holds labels for clusters if they were specified by user using "label-" prefix in category or tag - add entities with label text value
$labelshtml = ''; //string with entities holding labels

$tagsToArray = array(); //this var will hold dense data for tSNE ie array populated by 1/0 present/absent tag/category
$printTagsToArray = ""; //this is the same array but converted to string for printing - passing to javascript

$rowCounter = 0; //keep track where we are in the table processing - used for assigning id to a-frame entities 

if ( $wpb_all_query->have_posts() ){ //there are posts to be displayed
  //parse all posts---------------------------------------------------------------------------------
  while ( $wpb_all_query->have_posts() ){
     $wpb_all_query->the_post();
     //CREATEE DENSE ARRAY FROM TAGS / CATEGORIES AND PUT THEM INTO ARRAY
     $currtags = array();
     if($usetags){
        $currtags = wp_get_post_tags( get_the_ID(), array( 'fields' => 'names' ) ); //this array with tag names - other options are ids , names , ...
     }else{
        $currtags = wp_get_post_categories( get_the_ID(), array( 'fields' => 'names' ) ); //this array with tag names - other options are ids , names , ...
     }
     
     $currTagArray = array(); //prepare tags of current entry to dense array

     $label = array(); //init array to hold id+label text if current tag/category is label  
     //compare to all used tags and create dense array--------------------------     
     for ($g = 0; $g < sizeof($alltagsArray); $g++) { 
         
         $tagsMatch = false;
         for ($currtagIndex = 0; $currtagIndex < sizeof($currtags); $currtagIndex++) {
            if( $currtags[$currtagIndex] == $alltagsArray[$g] ){  //get current tag name and compare to all tags                   
              $tagsMatch = true;
            }
         }
         
         if($tagsMatch){
          array_push($currTagArray, 1);
         }else{
          array_push($currTagArray, 0);
         }
               
    }//end compare to all tags--------------------------------------------------

    array_push($tagsToArray, $currTagArray); //push current row array to 2d array
    $printTagsToArray = $printTagsToArray . '[' . implode(',', $currTagArray) . '],'; //stringify the array for printing 
    //-----------------------------------------------------------------------------
    //PREPARE A-FRAME ENTITIES FOR 3D VISUALISATION:
     //crossorigin="anonymous" means we are loading resources from the same domain - see CORS pollicies for more on this
     $assetshtml .= '<img id="asset_'.$rowCounter.'" src="'.get_the_post_thumbnail_url().'" crossorigin="anonymous">';
     //check the custom post field if present use it for custom label
     $currmeta = strtoupper( get_post_meta(get_the_ID(),'custom_image_name',true) );
     $printcurrtags = implode(",", $currtags);
     
     if ( $currmeta != '') {   //data-tags="'.implode(',', $currTagArray).'"
        $entities .= '<a-entity id="'.$rowCounter.'" data-posttags="'.$printcurrtags.'" data-postid="' . get_the_ID() . '" visible="true" geometry="primitive: plane" material="src: #asset_'.$rowCounter.'" a-frame-to-html="id: '. get_the_ID() .'; fetchurl:' . get_home_url() . '; target: #modal;" look-at="#cam" class="clickable 3dpost">
          <a-entity text="value: ' . $currmeta . '; width: ' . $labelsize . '; anchor: center; align: center; color: ' . $labelcolor . ';" position="0 2 0" look-at="#cam"  ></a-entity>
        </a-entity>'; 
        }else{       
          $entities .= '<a-entity id="'.$rowCounter.'" data-posttags="'.$printcurrtags.'" data-postid="' . get_the_ID() . '" visible="true" geometry="primitive: plane" material="src: #asset_'.$rowCounter.'" a-frame-to-html="id: '. get_the_ID() .'; fetchurl:' . get_home_url() . '; target: #modal;" look-at="#cam" class="clickable 3dpost"></a-entity>';
        }    
    
    //------------------------------------------------------------------------------
    $rowCounter++;  
  }//end parse all posts---------------------------------------------------------------------------
  
$printTagsToArray = '['. substr($printTagsToArray, 0, strlen($printTagsToArray)-1 ) . ']';  
 //echo '<script>console.log("dense tag array: '.$printTagsToArray.'")</script>'; //debug each dense array

$assetshtml = '<a-assets>' . $assetshtml . '</a-assets>';
 
$aframe_header = '
  <a-scene id="aframecontainer" background="color: '.$bcgcolor.'" embedded>
  <!-- ASSETS START -->'. $assetshtml .'<!-- ASSETS END -->
  <a-entity id="3d_vis">
  <a-entity position="0 0 0" id="cam" visible="false" ></a-entity>
  ';

$aframe_footer = '
  </a-entity>
  <a-entity laser-controls="hand: right" raycaster="objects: .clickable;"></a-entity>                                         
  <a-entity id="mouseCursor" cursor="rayOrigin: mouse" raycaster="objects: .clickable"></a-entity>   
  <a-camera id="camera" look-controls="enabled: false" orbit-controls="target: 0 1.6 -0.5; minDistance: 0.5; maxDistance: 180; initialPosition: 0 5 '.$camdistance.'; autoRotate: true; autoRotateSpeed: 0.3;"></a-camera>
  </a-scene>
  ';
//camera look-controls orbit-controls="target: 0 1.6 -0.5; minDistance: 0.5; maxDistance: 180; initialPosition: 0 5 15"
//<a-camera id="cam" look-controls mouse-cursor wasd-controls="fly: true;" position="0 1.6 0"></a-camera>
echo '<!-- A-FRAME HEADER -->' . $aframe_header . '<!-- POST FEATURE IMAGES AS PLANES -->' . $entities . '<!-- A-FRAME FOOTER -->' . $aframe_footer; //inject A-frame web VR entities into DOM body

//prepare javascript code for tSNE and pass parsed dense tag data in appropriate format:  data-alltags="'.$printAlltagsArray.'"
echo '<span id="printTagsToArray" data-perplexity="'.$perplexity.'" data-learnrate="'.$learnrate.'" data-alltags="'.$printAlltagsArray.'" data-num-entries="'.sizeof($tagsToArray).'" data-num-tags="' . sizeof($alltagsArray) .'" data-tsne="'.$printTagsToArray.'" data-spread="'.$option_spread.'" data-offset="'.$option_offset.'" data-iterations="'.$iterations.'" ></span>';

//console.log("dense data size: " + inputData.length + " ");
//console.log("dense data: ' . $printTagsToArray . '");
//console.log("'.$printTagsToNxD.'");
}

?>
       
<script>
//display hide appropriate filtering menu based on select
function changeFilteringMenu(){
    var targetId = document.getElementById("filteroption").value;
    
    var mustincludemenu = document.getElementById('mustincludemenu');
    var usedtagsmenu = document.getElementById('usedtagsmenu');
    
    if( targetId == 'usedtagsmenu'){
        usedtagsmenu.style.display = 'inline';
       mustincludemenu.style.display = 'none';      
    }
    
    if( targetId == 'mustincludemenu'){
       usedtagsmenu.style.display = 'none';
       mustincludemenu.style.display = 'inline';    
    }
 //alert("select triggered "+targetId);   
}


//-----------------------
function hideme(currel){
console.log('clicked');
  var thisel = document.getElementById(currel);
  thisel.classList.remove("show");
  thisel.classList.add("hide");
  thisel.addEventListener("transitionend", putdown); 
  
  function putdown (){
    event.target.removeEventListener(event.type, putdown);
    thisel.classList.remove("hide");
    thisel.classList.add("down");
    
    thisel.innerHTML = ""; //reset last content to nothing - prevent old content to flicker
  }

}

function checkAutoRotate() {
  var checkbox = document.getElementById("autorotate");
  var rotcam = document.getElementById("camera");
  if (checkbox.checked) {
    rotcam.setAttribute("orbit-controls", "autoRotate: true;"); 
  } else {
    rotcam.setAttribute("orbit-controls", "autoRotate: false;"); 
  }
}

</script> 
       
<?php get_footer(); ?>
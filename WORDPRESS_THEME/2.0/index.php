<?php get_header(); ?>
<div class="3dview">
<!-- display the 2D html here in overlay -->
<div class="modal" id="modal"></div>

<?php

$labelsize = 10;
$usetags = false;

$autorot = get_theme_mod( 'camerarotate', 'false' );
$cameraSpeed =  get_theme_mod( 'cameraSpeed', '0.3' );
$option_classifier = get_theme_mod( 'classifier', 'categories' );  //second arg is default to fallback to
$option_spread = get_theme_mod( 'spread', '10' );
$option_offset =  get_theme_mod( 'random_offset', '2' );
$iterations = get_theme_mod( 'iterations', '500' );
$camdistance = get_theme_mod( 'camdistance', '15' );
$learnrate = get_theme_mod( 'learnrate', '100' );
$perplexity = get_theme_mod( 'perplexity', '5' );
$labelcolor = get_theme_mod( 'color-label', '#000000' );
$bcgcolor = get_theme_mod( 'color-background', '#FFFFFF' );
$rootpath = get_template_directory_uri(); 

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

$alltagsArray = array(); //this will hold just names of tags/categories- declare and init
$alltagsArrayParsed = array(); //this hold the same but discard values aftre ":" delimeter from tag names


if($usetags){
    $alltagsArray = get_tags( array( 'fields' => 'names' ) ); //get all wp tags used of all times
}else{
    $alltagsArray = get_categories( array( 'fields' => 'names',  'hide_empty' => false ) ); //include even unused categories 
}

$usedtagsmenu = "<!-- tags used for tSNE dense data array -->";
$includetagsmenu = "<!-- post that are visible must include one of these tags -->";

$playicon = "none";
$stopicon = "inline";
if($autorot){
$playicon = "inline";
$stopicon = "none";
}

$filteringmenu = '<div id="gui">
<div class="_row">
<div class="_column">  <span id="autorotate" onclick="checkAutoRotate(\'autorotate\')" ><span id="playicon" style="display: '.$playicon.';">' . file_get_contents($rootpath . '/icons/play-button.svg') . '</span><span id="stopicon" style="display: '.$stopicon.';">' . file_get_contents($rootpath . '/icons/stop.svg') . '</span> </span>  &nbsp; </div>
<div class="_column"> <select name="filteroption" id="filteroption" onchange="changeFilteringMenu()">
    <option value="mustincludemenu">display only:</option>
    <option value="usedtagsmenu">used classifiers:</option>
  </select> </div>
';

//------------------------------------------------------------------------------
//create filtering menu for all used tags/categories---------------------------------------------------
for ($f = 0; $f < sizeof($alltagsArray); $f++) {
   $tagname = $alltagsArray[$f];
   $printAlltagsArray .= $tagname . ',';
   array_push($alltagsArrayParsed, $tagname);
    
   $usedtagsmenu .= '<div class="_column">  <div class="cboxB"> <input class="filterbytag animatedcheck" type="checkbox" id="filter-'.$f.'" name="'.$alltagsArrayParsed[$f].'" checked><label for="'.$alltagsArrayParsed[$f].'" id="label-'.$f.'" onClick="checkboxLabel(\'filter-'.$f.'\')">'.$alltagsArrayParsed[$f].'</label> </div> </div>';
  
   $includetagsmenu .= '<div class="_column"> <div class="cboxB"> <input class="displaybytag animatedcheck" type="checkbox" id="display-'.$f.'" name="'.$alltagsArrayParsed[$f].'" checked><label for="'.$alltagsArrayParsed[$f].'" id="label-'.$f.'" onClick="checkboxLabel(\'display-'.$f.'\')" >'.$alltagsArrayParsed[$f].'</label> </div> </div>';
}

$printAlltagsArray = substr($printAlltagsArray, 0, -1); //get rid of comma at the end
$filteringmenu .= '<div id="mustincludemenu">' . $includetagsmenu . '<!-- display post with these tags END --></div>' . '<div id="usedtagsmenu" style="display: none;">' . $usedtagsmenu . 
'<!-- tags used for tSNE END--></div>' . '<div class="_column" onclick="showhelp()">'. file_get_contents($rootpath . '/icons/information.svg') . ' </div>' . '</div></div>';

echo $filteringmenu; //display filtering menu
//---------------------------------------------------------------------------------------------

$labels = array(); //this holds labels for clusters if they were specified by user using "label-" prefix in category or tag - add entities with label text value
$labelshtml = ''; //string with entities holding labels

$rowCounter = 0; //keep track where we are in the table processing - used for assigning id to a-frame entities 

if ( $wpb_all_query->have_posts() ){ //there are posts to be displayed
  //parse all posts---------------------------------------------------------------------------------
  while ( $wpb_all_query->have_posts() ){
     $wpb_all_query->the_post();

     $currtags = array();
     if($usetags){
        $currtags = wp_get_post_tags( get_the_ID(), array( 'fields' => 'names' ) ); //this array with tag names - other options are ids , names , ...
     }else{
        $currtags = wp_get_post_categories( get_the_ID(), array( 'fields' => 'names' ) ); //this array with tag names - other options are ids , names , ...
     }

     $label = array(); //init array to hold id+label text if current tag/category is label  
    //-----------------------------------------------------------------------------
    //PREPARE A-FRAME ENTITIES FOR 3D VISUALISATION:
     //crossorigin="anonymous" means we are loading resources from the same domain - see CORS pollicies for more on this
     $assetshtml .= '<img id="asset_'.$rowCounter.'" src="'.get_the_post_thumbnail_url().'" crossorigin="anonymous">';
     
     $currmeta = strtoupper( get_post_meta(get_the_ID(),'custom_image_name',true) );
     $printcurrtags = implode(",", $currtags);
     
     if ( $currmeta != '') {  //check the custom post field if present use it for custom label
        $entities .= '<a-entity id="'.$rowCounter.'" data-posttags="'.$printcurrtags.'" data-postid="' . get_the_ID() . '" visible="true" geometry="primitive: plane" material="src: #asset_'.$rowCounter.'" a-frame-to-html="id: '. get_the_ID() .'; fetchurl:' . get_home_url() . '; target: #modal;" look-at="#cam" class="clickable 3dpost">
          <a-entity text="value: ' . $currmeta . '; width: ' . $labelsize . '; anchor: center; align: center; color: ' . $labelcolor . ';" position="0 2 0" look-at="#cam"  ></a-entity>
        </a-entity>'; 
        }else{       
          $entities .= '<a-entity id="'.$rowCounter.'" data-posttags="'.$printcurrtags.'" data-postid="' . get_the_ID() . '" visible="true" geometry="primitive: plane" material="src: #asset_'.$rowCounter.'" a-frame-to-html="id: '. get_the_ID() .'; fetchurl:' . get_home_url() . '; target: #modal;" look-at="#cam" class="clickable 3dpost"></a-entity>';
        }    
    
    //------------------------------------------------------------------------------
    $rowCounter++;  
  }//end parse all posts---------------------------------------------------------------------------

$assetshtml = '<a-assets>' . $assetshtml . '</a-assets>';
 
$aframe_header = '
  <a-scene id="aframecontainer" background="color: '.$bcgcolor.'" loading-screen="dotsColor: '.get_theme_mod( 'color-text', '#000000' ).'; backgroundColor: '.get_theme_mod( 'color-bars', '#000000' ).';" vr-mode-ui="enabled: false" embedded>
  <!-- ASSETS START -->'. $assetshtml .'<!-- ASSETS END -->
  <a-entity id="3d_vis">
  <a-entity position="0 0 0" id="cam" visible="false" ></a-entity>
  ';

$aframe_footer = '
  </a-entity>
  <a-entity laser-controls="hand: right" raycaster="objects: .clickable;"></a-entity>                                         
  <a-entity id="mouseCursor" cursor="rayOrigin: mouse" raycaster="objects: .clickable"></a-entity>   
  <a-camera id="camera" look-controls="enabled: false" orbit-controls="target: 0 1.6 -0.5; minDistance: 0.5; maxDistance: 180; initialPosition: 0 5 '.$camdistance.'; autoRotate: ' .$autorot. '; autoRotateSpeed: '.$cameraSpeed.';"></a-camera>
  </a-scene>
  ';
//camera look-controls orbit-controls="target: 0 1.6 -0.5; minDistance: 0.5; maxDistance: 180; initialPosition: 0 5 15"
//<a-camera id="cam" look-controls mouse-cursor wasd-controls="fly: true;" position="0 1.6 0"></a-camera>
echo '<!-- A-FRAME HEADER -->' . $aframe_header . '<!-- POST FEATURE IMAGES AS PLANES -->' . $entities . '<!-- A-FRAME FOOTER -->' . $aframe_footer; //inject A-frame web VR entities into DOM body

//prepare javascript code for tSNE and pass parsed dense tag data in appropriate format:  data-alltags="'.$printAlltagsArray.'"
echo '<span id="printTagsToArray" data-perplexity="'.$perplexity.'" data-learnrate="'.$learnrate.'" data-spread="'.$option_spread.'" data-offset="'.$option_offset.'" data-iterations="'.$iterations.'" ></span>';


}

?>

</div>
       
<script>
//custom checkbox functionality
function checkboxLabel(currId){
  var targetId = document.getElementById(currId);
  if( targetId.checked ){
    targetId.checked = false;
  }else{
    targetId.checked = true;
  }
  updatetsne(); //immidietly update results
}

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

var root_uri = "<?php echo $rootpath; ?>";


var autorotate = Boolean(<?php echo $autorot; ?>);
var playicon = document.getElementById("playicon");
var stopicon = document.getElementById("stopicon");

function checkAutoRotate(currId) {
//<i class="far fa-stop-circle"></i>
//<i class="far fa-play-circle"></i>
  autorotate = !autorotate;
  var currEle = document.getElementById(currId);
  var rotcam = document.getElementById("camera");
  
  if (autorotate) {
    rotcam.setAttribute("orbit-controls", "autoRotate: true;");
    playicon.style.display = "inline";
    stopicon.style.display = "none";
  } else {
    rotcam.setAttribute("orbit-controls", "autoRotate: false;");
    stopicon.style.display = "inline";
    playicon.style.display = "none";
    //currEle.innerHTML = stopsvg;
    //currEle.innerHTML = '<img class="ficon" src=' + root_uri + '/icons/stop.svg">'; 
  }
}

function showhelp(){
alert('Rotate by left click and drag, pan with right click and drag. To zoom scroll or click scroll wheel and drag. Click play icon to toggle auto rotate. Click on images to reveal more information.');
}

</script> 
       
<?php get_footer(); ?>
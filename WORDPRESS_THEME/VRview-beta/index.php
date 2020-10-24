<?php get_header(); ?>

<!-- display the 2D html here in overlay -->
<div class="modal" id="modal"></div>
<!-- interactive sliders for tSNE -->
<div id="gui">   
  <div class="row">
     <div class="column"> <button onclick="updatetsne()">RUN AGAIN</button> </div> <!--  <br> <span>error: </span><span id="errorVal">0</span> -->
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
     <div class="column">  
        <input type="checkbox" id="vehicle1" name="vehicle1" value="Bike"><label for="vehicle1"> I have a bike</label>
     
     </div>
  </div> 
</div>

<?php

$labelsize = 10;
$usetags = false;
// the wp query to db - get all posts
$wpb_all_query = new WP_Query(array('post_type'=>'post', 'post_status'=>'publish', 'posts_per_page'=>-1)); 

$assetshtml = ""; //set all images as assets to load them beforehand
$entities = "";  //in this var we save all a-frame html entities

//some weird wordpress taxonomy - get rid of it and put the name of all tags in to simple array
$alltagsArray = array(); //this will hold just names of tags/categories- declare and init

if($usetags){
    $alltagsArray = get_tags( array( 'fields' => 'names' ) ); //get all wp tags used of all times
}else{
    $alltagsArray = get_categories( array( 'fields' => 'names',  'hide_empty' => false ) ); //include even unused categories 
}

$labels = array(); //this holds labels for clusters if they were specified by user using "label-" prefix in category or tag - add entities with label text value
$labelshtml = ''; //string with entities holding labels

$tagsToArray = array(); //this var will hold dense data for tSNE ie array populated by 1/0 present/absent tag/category
$printTagsToArray = ""; //this is the same array but converted to string for printing - passing to javascript

$rowCounter = 0; //keep track where we are in the table processing - used for assigning id to a-frame entities 

if ( $wpb_all_query->have_posts() ){ //there are posts to be displayed
  //parse all posts---------------------------------------------------------------------------------
  while ( $wpb_all_query->have_posts() ){
     $wpb_all_query->the_post();
     //a-frame-to-html="id: '. get_the_ID() .'; target: #modal;"   random-position           //get_home_url(   //admin_url('admin-ajax.php')
     //crossorigin="anonymous" means we are loading resources from the same domain - see CORS pollicies for more on this
     $assetshtml .= '<img id="asset_'.$rowCounter.'" src="'.get_the_post_thumbnail_url().'" crossorigin="anonymous">';
     //'#asset_'.$rowCounter
     //$entities .= '<a-entity id="'.$rowCounter.'" visible="true" geometry="primitive: plane" material="src: '.get_the_post_thumbnail_url().'" a-frame-to-html="id: '. get_the_ID() .'; fetchurl:' . get_home_url() . '; target: #modal;" look-at="#cam" class="clickable"></a-entity>';    
     $entities .= '<a-entity id="'.$rowCounter.'" visible="true" geometry="primitive: plane" material="src: #asset_'.$rowCounter.'" a-frame-to-html="id: '. get_the_ID() .'; fetchurl:' . get_home_url() . '; target: #modal;" look-at="#cam" class="clickable"></a-entity>';
     
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
                 //if the category or tag contains word "label-" display it as header for the cluster
                 if (strpos($currtags[$currtagIndex], 'label-') !== false) {
                    $label = [ $rowCounter , substr($currtags[$currtagIndex],6) ];
                    //echo '<script>console.log("label id: '.$label[0].' label text: '.$label[1].'")</script>'; //debug
                 }                    
              $tagsMatch = true;
            }
         }
         
         if($tagsMatch){
          array_push($currTagArray, 1);
         }else{
          array_push($currTagArray, 0);
         }
               
    }//end compare to all tags--------------------------------------------------

    if($label[1] != ''){
      //echo '<script>console.log("label FOUND")</script>';
      array_push($labels, $label);
    } 

    array_push($tagsToArray, $currTagArray); //push current row array to 2d array
    $printTagsToArray = $printTagsToArray . '[' . implode(',', $currTagArray) . '],'; //stringify the array for printing 
    
    $rowCounter++;  
  }//end parse all posts---------------------------------------------------------------------------
  
$printTagsToArray = '['. substr($printTagsToArray, 0, strlen($printTagsToArray)-1 ) . ']';  
 //echo '<script>console.log("dense tag array: '.$printTagsToArray.'")</script>'; //debug each dense array

for ($z = 0; $z < sizeof($labels); $z++) {
//class="3dlabel"  
  $labelshtml .= '<a-entity class="3dlabel" visible="true" look-at="#cam"  id="label-' . $labels[$z][0] . '" data-labelfor="' . $labels[$z][0] . '" text="value: '. $labels[$z][1] .'; anchor: center; width: ' . $labelsize . '; color: black; side: double;" ></a-entity>'; 
}

$assetshtml = '<a-assets>' . $assetshtml . '</a-assets>';
 
$aframe_header = '
  <a-scene id="aframecontainer" background="color: #ECECEC" embedded>
  <!-- ASSETS START -->'. $assetshtml .'<!-- ASSETS END -->
  <a-entity id="3d_vis">
  <a-entity position="0 0 0" id="cam" visible="false" ></a-entity>
  ';

$aframe_footer = '
  </a-entity>
  <a-entity laser-controls="hand: right" raycaster="objects: .clickable;"></a-entity>                                         
  <a-entity id="mouseCursor" cursor="rayOrigin: mouse" raycaster="objects: .clickable"></a-entity>   
  <a-camera id="camera" look-controls="enabled: false" orbit-controls="target: 0 1.6 -0.5; minDistance: 0.5; maxDistance: 180; initialPosition: 0 5 15; autoRotate: true; autoRotateSpeed: 0.3;"></a-camera>
  </a-scene>
  ';
//camera look-controls orbit-controls="target: 0 1.6 -0.5; minDistance: 0.5; maxDistance: 180; initialPosition: 0 5 15"
//<a-camera id="cam" look-controls mouse-cursor wasd-controls="fly: true;" position="0 1.6 0"></a-camera>
echo $aframe_header . '<!-- POST FEATURE IMAGES AS PLANES -->' . $entities . '<!-- LABELS -->' . $labelshtml . '<!-- END LABELS -->' . $aframe_footer; //inject A-frame web VR entities into DOM body

//prepare javascript code for tSNE and pass parsed dense tag data in appropriate format:
echo '<span id="printTagsToArray" data-num-entries="' . sizeof($tagsToArray) .'" data-num-tags="' . sizeof($alltagsArray) .'" data-tsne="' . $printTagsToArray . '"></span>';

//console.log("dense data size: " + inputData.length + " ");
//console.log("dense data: ' . $printTagsToArray . '");
//console.log("'.$printTagsToNxD.'");
}

?>
       
<script>
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
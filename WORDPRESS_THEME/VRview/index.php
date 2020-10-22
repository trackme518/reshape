<?php get_header(); ?>

<!-- display the 2D html here in overlay -->
<div class="modal" id="modal"></div>
<!-- interactive sliders for tSNE -->
<div id="gui">   
  <div class="row">
     <div class="column"> <button onclick="updatetsne()">RUN AGAIN</button> </div> <!--  <br> <span>error: </span><span id="errorVal">0</span> -->
     <div class="column"> <input id="iterations" class="slider" type="range" min="1" max="3000" value="1000"> <div>iterations: <span id="iterations_val">1000</span></div>  </div>
     <div class="column"> <input id="perplex" class="slider" type="range" min="1" max="100" value="5"> <div>perplexity: <span id="perplex_val">5</span></div> </div>
     <div class="column"> <input id="learnrate" class="slider" type="range" min="10" max="1000" value="100"> <div>learning rate: <span id="learnrate_val">100</span></div> </div>
  </div> 
</div>
    
<?php
// the wp query to db - get all posts
$wpb_all_query = new WP_Query(array('post_type'=>'post', 'post_status'=>'publish', 'posts_per_page'=>-1)); 

$entities = "";  //in this var we save all a-frame html entities

//some weird wordpress taxonomy - get rid of it and put the name of all tags in to simple array
$alltagsArray = array(); //this will hold just names of tags
$alltagsObject = get_tags(); //get all wp tags used of all times
foreach ( $alltagsObject as $tag ) {
    array_push($alltagsArray, $tag->name);
}

$tagsToArray = array(); //this var will hold dense data for tSNE
$printTagsToArray = ""; //this is the same array but converted to string for printing

$rowCounter = 0; //keep track where we are in the table processing - used for assigning id to a-frame entities 

if ( $wpb_all_query->have_posts() ){ //there are posts to be displayed
  //parse all posts---------------------------------------------------------------------------------
  while ( $wpb_all_query->have_posts() ){
     $wpb_all_query->the_post();
     //a-frame-to-html="id: '. get_the_ID() .'; target: #modal;"              //get_home_url(   //admin_url('admin-ajax.php')
     $entities .= '<a-entity id="'.$rowCounter.'" random-position geometry="primitive: plane" material="src: '.get_the_post_thumbnail_url().'" a-frame-to-html="id: '. get_the_ID() .'; fetchurl:' . get_home_url() . '; target: #modal;" look-at="#cam" class="clickable"></a-entity>';
     
     
     $currtags = wp_get_post_tags( get_the_ID(), array( 'fields' => 'names' ) ); //this array with tag names - other options are ids , names , ...
     
     $currTagArray = array(); //prepare tags of current entry to dense array

       
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
    /*
    $stringDenseArray = "";
    for ($g = 0; $g < sizeof($currTagArray); $g++) {
      $stringDenseArray .= $currTagArray[$g] . ',';
    }
    echo '<script>console.log("dense tag array: '.$stringDenseArray.'")</script>'; //debug each dense array
    */
    array_push($tagsToArray, $currTagArray); //push current row array to 2d array
    $printTagsToArray = $printTagsToArray . '[' . implode(',', $currTagArray) . '],'; //stringify the array for printing 
    
    $rowCounter++;  
  }//end parse all posts---------------------------------------------------------------------------
  
  $printTagsToArray = '['. substr($printTagsToArray, 0, strlen($printTagsToArray)-1 ) . ']';  
 //echo '<script>console.log("dense tag array: '.$printTagsToArray.'")</script>'; //debug each dense array
 $aframe_header = '
  <a-scene id="aframecontainer" background="color: #ECECEC" embedded>
  <a-entity id="3d_vis">
  <a-entity position="0 0 0" id="cam" visible="false" ></a-entity>
  ';

$aframe_footer = '
  </a-entity>
  <a-entity laser-controls="hand: right" raycaster="objects: .clickable;"></a-entity>                                         
  <a-entity id="mouseCursor" cursor="rayOrigin: mouse" raycaster="objects: .clickable"></a-entity>   
  <a-camera id="camera" look-controls="enabled: false" orbit-controls="target: 0 1.6 -0.5; minDistance: 0.5; maxDistance: 180; initialPosition: 0 5 15"></a-camera>
  </a-scene>
  ';

//camera look-controls orbit-controls="target: 0 1.6 -0.5; minDistance: 0.5; maxDistance: 180; initialPosition: 0 5 15"
//<a-camera id="cam" look-controls mouse-cursor wasd-controls="fly: true;" position="0 1.6 0"></a-camera>
echo $aframe_header . $entities . $aframe_footer; //inject A-frame web VR entities into DOM body
    
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
    //thisel.innerHTML = "";
  }

}

</script> 


<?php get_footer(); ?>
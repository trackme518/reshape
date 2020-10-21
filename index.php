<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <title>Virual Exhibition - ReShape</title>
  <meta name="author" content="Vojtech Leischner www.trackmeifyoucan.com All rights reserved">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <!-- main vr framwork -->
  <script src="scripts/aframe104.js"></script> 
  <!-- A-frame components: -->
  <script src="scripts/lookAt.js"></script> <!-- lookAt component - orient object  to face other object https://unpkg.com/browse/aframe-look-at-component@0.8.0/ -->
  <script src="scripts/show_on_click.js"></script>
  
  <!-- https://github.com/supermedium/superframe/tree/master/components/orbit-controls -->
  <script src="scripts/aframe-orbit-controls.js"></script> <!-- orbital controls instead of wasd fly controls modified to enable look-at component see line 1255 -->
  
  
  <script src="https://unpkg.com/aframe-randomizer-components@3.0.2/dist/aframe-randomizer-components.min.js"></script> <!-- https://github.com/supermedium/superframe/tree/master/components/randomizer/ -->
  
  <!-- main classifier framework - tSNE implemented in javascript -->
  <!-- https://github.com/scienceai/tsne-js -->
  
 <!-- <script src="scripts/tsne2.min.js"></script> --> <!-- this one has more options but it seems not getting good results with my data-->

 <!-- https://github.com/karpathy/tsnejs -->
  <script src="scripts/tsne.js"></script><!-- this one is more basic but seems to be doing good job in this use-case -->

  <!--<script src="https://unpkg.com/aframe-html-shader@0.2.0/dist/aframe-html-shader.min.js"></script>-->
  <!--<script src="backend/jquery-3.5.1.min.js"></script>-->
  <!-- https://github.com/vimeo/aframe-vimeo-component -->

 <link rel="stylesheet" href="style.css"> 
 <style>
 /* Create two equal columns that floats next to each other */
.column {
  float: left;
  width: 150px;
  margin-left: 5px;
}


.row{
margin-top: 5px;
margin-bottom: 5px;
}
/* Clear floats after the columns */
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

#gui {position: absolute; top: 0px; left: 0px; z-index: 50;  width: 100%; background-color: rgba(255, 255, 255, 0.75); padding: 0px; margin: 0px;}

.slider{ width: 145px;}
 
 </style>
</head>
<body>  
    
    <!-- display the 2D html here in overlay -->
    <div class="modal" id="modal"></div>
    <!-- interactive sliders for tSNE -->
    <div id="gui">   
      <div class="row">
        <div class="column"> <button onclick="updatetsne()">RUN AGAIN</button> <br> <span>error: </span><span id="errorVal">0</span></div>
        <div class="column"> <input id="iterations" class="slider" type="range" min="1" max="3000" value="1000"> <br> <span>iterations: </span><span id="iterations_val">1000</span>  </div>
        <div class="column"> <input id="perplexity" class="slider" type="range" min="1" max="100" value="10"> <br> <span>perplexity: </span><span id="perplexity_val">10</span> </div>
        <div class="column"> <input id="learnrate" class="slider" type="range" min="10" max="1000" value="100"> <br> <span>learning rate: </span><span id="learnrate_val">100</span> </div>
      </div> 
    </div>

<?php
//here we load the csv table with our previously saved data - our intention is 1) create A-frame entitiers and reference the images form upload folder
//2) prepare tags dense data for tSNE to work on

$entities = "";  //in this var we save all a-frame html entities

$fileHandle = fopen("backend/data.csv", "r"); //load our previsouly saved data from csv table


$alltags = file_get_contents('backend/tags.txt'); //load text file with all the tags used - we need this to prepare dense data for tSNE clustering
$alltagsArray = explode (",", $alltags); //load tags into an array 

$tagsToArray = array(); //this var will hold dense data for tSNE
$printTagsToArray = ""; //this is the same array but converted to string for printing
$printTagsToNxD = ""; //print as line break separeted values

$rowCounter = 0; //keep track where we are in the table processing 
//Loop through the CSV rows of the table
while (($row = fgetcsv($fileHandle, 0, ",")) !== FALSE) {
    //var_dump($row);
    if(sizeof($row) > 3 ){  //at least four params are needed - timestamp, file path, type of file, tags
        //image type entry - link uploaded image
        if( $row[2] == 'image'){
         $pathsrc =  'backend/uploads/' . $row[1]; //random-position
         $entities = $entities . '<a-entity id="'.$rowCounter.'" geometry="primitive: plane" material="src: '.$pathsrc.'" look-at="#cam"  class="clickable" a-frame-to-html="id: '.$row[0].'; path: '.$pathsrc.'; type: '.$row[2].'; target: #modal;"></a-entity>';
        }
        
        //video type entry - prepare youtube embed code and link associated thumb image 
        if( $row[2] == 'video'){ 
        $pattern = "/^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/";
        preg_match($pattern, $row[1], $match); // Outputs 1
          if( strlen($match[7])==11){
             //youtube is bullying me with CORS - so we rather download the damm thumbmnails in upload script instead...
            //$videothumb = 'http://img.youtube.com/vi/'.$match[7].'/0.jpg';
            $videothumb =  'backend/uploads/youtube_' . $match[7] . '.jpg';
            $entities = $entities . '<a-entity id="'.$rowCounter.'" geometry="primitive: plane" material="src: '.$videothumb.'" look-at="#cam" class="clickable" a-frame-to-html="id: '.$row[0].'; path: '.$row[1].'; type: '.$row[2].'; target: #modal;"></a-entity>';
          }//found video id of length 11
        
      }//end check video type
        
  
    //get tags for current entry as multi-dimensional data matrice
    $tags = explode (";", $row[3]); //load tags into an array  
    
    $currTagArray = array(); //prepare tags of current entry to dense array
    $javascript = "";
    $tagindex = 0;
    
    for ($g = 0; $g < sizeof($alltagsArray)-1; $g++) { //compare to all used tags   
         
         $tagsMatch = false;
         for ($currtagIndex = 0; $currtagIndex < sizeof($tags); $currtagIndex++) {
            if( $tags[$currtagIndex] == $alltagsArray[$g] ){       
              $tagsMatch = true;
            }
         }
         
         if($tagsMatch){
         array_push($currTagArray, 1);
         }else{
         array_push($currTagArray, 0);
         }       
    }

    array_push($tagsToArray, $currTagArray); //push current row array to 2d array
    $printTagsToNxD = $printTagsToNxD . implode(',', $currTagArray) . '/n';
    $printTagsToArray = $printTagsToArray . '[' . implode(',', $currTagArray) . '],'; //stringify the array for printing

  }//end check param size - at least four params are needed - timestamp, file path, type of file, tags
    
$rowCounter++;
}//end parse file

//html code for a-frame
///note the entity with id "cam" - it acts as placeholder for actual camera position because it is otherwise unaccesible because of orbital controls
//I use the position of this imagenary cam position for other entities look-at component target
//see modified orbital controls component - line 1255 
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

$printTagsToArray = '['. substr($printTagsToArray, 0, strlen($printTagsToArray)-1 ) . ']';
//echo $printTagsToArray;
//$tagsToArray[1][0]

//var inputData = [[0,1,1,0,0,0],[1,0,1,1,0,0],[1,0,0,0,0,0],[0,1,1,0,0,0],[0,0,1,1,0,0],[1,1,0,0,0,0],[1,1,0,0,0,0],[1,1,0,0,0,0],[1,1,0,0,0,0],[1,1,0,0,0,0],[1,1,0,0,0,0],[1,0,0,1,0,0],[0,0,1,1,0,0],[1,1,0,0,0,0],[0,1,1,0,0,0],[1,1,0,0,0,0],[0,0,0,0,1,0],[0,0,0,0,1,0],[0,0,0,0,1,0],[0,0,0,0,1,0],[0,0,0,0,1,0],[0,0,0,0,0,1],[0,0,0,0,0,1],[0,0,0,0,0,1],[0,0,0,0,0,1],[0,0,0,0,0,1],[0,0,0,0,0,1],[0,0,0,0,1,0]]

//prepare javascript code for tSNE and pass parsed dense tag data in appropriate format:
$tsneJs = '
<script>

var opt = {};
opt.epsilon = 10;
opt.perplexity = 5;
opt.dim = 3;
var tsne = new tsnejs.tSNE(opt);
var inputData = ' . $printTagsToArray . ';

tsne.initDataRaw(inputData);
for(var k = 0; k < 1000; k++) {
    tsne.step(); // every time you call this, solution gets better
}
   
var Y = tsne.getSolution(); // Y is an array of 2-D points that you can plot

var posMultiply = 10; //multiply normalized positions
var randomOffset = 2;
  
var getNormalized = normalizePositions();
console.log( "normalized solution: " + getNormalized );


function normalizePositions(){

  var normalized = new Array( Y.length );
  var extremes =  [[1000, -1000],[1000, -1000],[1000, -1000]]; //2D array to hold extremes
  
  //get min and max in all dimensions
  for (i = 0; i < Y.length; i++) {  
    //check for x----------------------------
    if( Y[i][0] < extremes[0][0] ){  //new min
        extremes[0][0] = Y[i][0];
    } 
    if( Y[i][0] > extremes[0][1] ){ //new max
        extremes[0][1] = Y[i][0];
    }
    //check for y----------------------------
        if( Y[i][1] < extremes[1][0] ){  //new min
        extremes[1][0] = Y[i][1];
    } 
    if( Y[i][1] > extremes[1][1] ){ //new max
        extremes[1][1] = Y[i][1];
    }
    //check for z----------------------------
            if( Y[i][2] < extremes[2][0] ){  //new min
        extremes[2][0] = Y[i][2];
    } 
    if( Y[i][2] > extremes[2][1] ){ //new max
        extremes[2][1] = Y[i][2];
    }
    //-----------------------------------
  }
  
  //go through the array again and normalize the values to 0-1 range
    for (i = 0; i < Y.length; i++) { 
        var currNorm = [ map_range( Y[i][0],  extremes[0][0], extremes[0][1], 0, 1) , map_range( Y[i][1],  extremes[1][0], extremes[1][1], 0, 1) , map_range( Y[i][2],  extremes[2][0], extremes[2][1], 0, 1) ] ; //map all values to 0-1 into array
        normalized[i] = currNorm;
        var currEl = document.getElementById(i);
        
        var currOffset = [ ( (Math.random()*2) -1.0   ) * randomOffset ,  ( (Math.random()*2) -1.0   ) * randomOffset, ( (Math.random()*2) -1.0   ) * randomOffset ];
        //var currOffset = [0,0,0];
        //console.log( currOffset[0] + ":"+currOffset[1]+":"+currOffset[2] );
        currEl.setAttribute("position", (normalized[i][0]*posMultiply + currOffset[0] ) + " " + ( normalized[i][1]*posMultiply + currOffset[1] ) + " " + ( normalized[i][2]*posMultiply + currOffset[2] ) ); //assign solved tSNE to VR elements
    }
    
    function map_range(value, low1, high1, low2, high2) {
        return low2 + (high2 - low2) * (value - low1) / (high1 - low1);
    }
    
    return normalized;
}

/*
  let model = new TSNE({
  dim: 3,
  perplexity: 10.0,
  earlyExaggeration: 4.0,
  learningRate: 100.0,
  nIter: 1000,
  metric: "euclidean"
});

//metric metric: "euclidean" dice

var inputData = ' . $printTagsToArray . ';

console.log("dense data size: " + inputData.length + " ");
//console.log("dense data: ' . $printTagsToArray . '");
//console.log("'.$printTagsToNxD.'");

model.init({
  data: inputData,
  type: "dense"
});

var posMultiply = 10;
var errorVal = document.getElementById("errorVal");
var runOnce = true;

function updatetsne(){
    
    let [error, iter] = [0,0];
    if(runOnce){
      [error, iter] = model.run();
    }else{
      [error, iter] = model.rerun();
    }

let output = model.getOutput();
let outputScaled = model.getOutputScaled();

  for (i = 0; i < outputScaled.length; i++) {
    var currEl = document.getElementById(i);
    //reduce to 2D only - useful for debug
    //currEl.setAttribute("position", outputScaled[i][0]*posMultiply + " " + outputScaled[i][1]*posMultiply + " " + 0 ); //assign solved tSNE to VR elements
    //reduce to 3D:
    currEl.setAttribute("position", outputScaled[i][0]*posMultiply + " " + outputScaled[i][1]*posMultiply + " " + outputScaled[i][2]*posMultiply ); //assign solved tSNE to VR elements
    //log solutions
    //console.log("solution: x " + outputScaled[i][0] + " y " + outputScaled[i][1] + " z " + outputScaled[i][2] );
  }

errorVal.innerHTML = error.toFixed(3); //display as rounded to three decimal places
console.log("error: " + error);
console.log("iter: " + iter);
}

updatetsne();

//change parameters interactively with sliders----------------------------------
var numiter = document.getElementById("iterations");
var numiterVal = document.getElementById("iterations_val");

var currperplexity = document.getElementById("perplexity");
var currperplexityVal = document.getElementById("perplexity_val");

var learnrate = document.getElementById("learnrate");
var learnrateVal = document.getElementById("learnrate_val");

//var output = document.getElementById("demo");
//output.innerHTML = slider.value; // Display the default slider value

// Update the current slider value (each time you drag the slider handle)
numiter.oninput = function() {
  model.nIter = this.value;
  //console.log( "number of iterations set to: " + model.nIter );
  numiterVal.innerHTML = this.value;
}
currperplexity.oninput = function() {
  model.perplexity = this.value;
  //console.log( "perplexity set to: " + model.perplexity );
  currperplexityVal.innerHTML = this.value;
} 
learnrate.oninput = function() {
  model.learningRate = this.value;
  //console.log( "learning rate set to: " + model.learningRate );
  learnrateVal.innerHTML = this.value;
}  
*/
</script>
';
/*
 <span>iterations</span>
    <input id="iterations" type="range" min="1" max="3000" value="1000">
    
    <span>perplexity</span>
    <input id="perplexity" type="range" min="1" max="100" value="10">
    
    <span>learning rate</span>
    <input id="learnrate" type="range" min="10" max="1000" value="100">
*/
echo $tsneJs;
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
</body>
</html>


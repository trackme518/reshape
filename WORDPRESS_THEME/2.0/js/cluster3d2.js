
console.log("javascript tsne script running");

var opt = {};

opt.dim = 3; //we want this in 3D
var tsne = new tsnejs.tSNE(opt);

opt.epsilon = document.getElementById("printTagsToArray").getAttribute("data-learnrate");
opt.perplexity = document.getElementById("printTagsToArray").getAttribute("data-perplexity");

var posMultiply =  document.getElementById("printTagsToArray").getAttribute("data-spread"); //get theme option rendered in html data element by php
var randomOffset = document.getElementById("printTagsToArray").getAttribute("data-offset");
var numIter = document.getElementById("printTagsToArray").getAttribute("data-iterations");

var posts = document.getElementsByClassName("3dpost"); //get all posts elements into array

console.log('learn rate: '+opt.epsilon+' perplexity: '+opt.perplexity+' data spread: '+posMultiply+' random offset: '+randomOffset+' iterations run: '+numIter+' number of posts: '+posts.length);  
//first run:
var getNormalized = runTsne();
//console.log( "normalized solution: " + getNormalized );

function runTsne(){
//FIRST PARSE DATA BASED ON USER SELECTED FILTERING OPTIONS:

    var filterbytag = document.getElementsByClassName("filterbytag"); //classify against these options
    var displaybytag = document.getElementsByClassName("displaybytag"); //display these options

    //var parsedData = Array.apply(null, Array(inputData.length)).map(function () {}); //init array with undefined values
    var parsedTags = new Array();
    var parsedData = new Array();//filtering what to use for classification
    var parsedVisible = new Array();//filtering which post to display - 1=display 0=inv
    
    //console.log('there is '+filterbytag.length+' classifiers');
    //check filtering menu-----------------------------------
    for(var b = 0; b < filterbytag.length; b++) {//for all tags (dense)           
           if( filterbytag[b].checked ){
              parsedTags.push( filterbytag[b].name );  
           }
           
           //check for visibility
           if( displaybytag[b].checked ){
               parsedVisible.push( displaybytag[b].name );
           }          
    }//end for all tags
    
     for(var a = 0; a < posts.length; a++) { //for every post
          var currPostTags =  posts[a].getAttribute("data-posttags").split(","); //get current post tags
          var currPostArray = new Array(); //init dense data array 
           
           for(var p = 0; p < parsedTags.length; p++) { //for all tags we are using for classification

                for(var tagIndex = 0; tagIndex < currPostTags.length; tagIndex++) { //compare to all tags of this post
                
                    var currValue = 0;    
                    var currentTag = currPostTags[tagIndex];

                     if( currentTag == parsedTags[p]){//we found match
                       currValue = 1;//assign value = tag exists
                    }//end match found------------------------
                    
                }//end compare to tags of this post---------------------------
             currPostArray.push(currValue); 
           }//end for all tags
        
        parsedData.push( currPostArray ); //put values into array
        
        //---------------------------------------------------------
         var visible = false;
         
        for(var v = 0; v < parsedVisible.length; v++) { //for all tags that should be visible
       
            for(var tagIndex = 0; tagIndex < currPostTags.length; tagIndex++) {

                if( currPostTags[tagIndex] == parsedVisible[v]){
                    visible = true;
                }
            }
        }
        
        if(visible){
            document.getElementById(a).setAttribute("visible", "true");//make the post visible 
        }else{
            document.getElementById(a).setAttribute("visible", "false");//make the post visible 
        }
        //--------------------------------------------------------  
     
     }//end for all posts---------------------------
     
     
     console.log( "parsed visible: " + JSON.stringify(parsedVisible) );
     console.log( "parsedTags: " + JSON.stringify(parsedTags) );
     console.log( "javascript dense data: " + JSON.stringify(parsedData) );

tsne.initDataRaw(parsedData); //init with only selected tags/categories
//tsne.initDataRaw(inputData);

console.log("dense data size: " + parsedData.length + " ");

  for(var k = 0; k < numIter; k++) {
      tsne.step(); // every time you call this, solution gets better
  }   
  var Y = tsne.getSolution(); // Y is an array of 2-D points that you can plot

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
  
        //calculate center of the visualization in x axis
        //console.log("max x: "+extremes[0][1]+" min x: "+extremes[0][0]);
        
    function getCentroid(minVal, maxVal){
        var centroid = 0;
        if( minVal > 0){
           centroid = maxVal - minVal;
        }else{
           centroid = maxVal + minVal;
        }
        centroid = map_range( centroid, minVal, maxVal, 0 ,1 ); 
        return centroid;
    }    
      
    var centerX = getCentroid(extremes[0][0], extremes[0][1])*posMultiply;
    var centerY = getCentroid(extremes[1][0], extremes[1][1])*posMultiply;
    var centerZ = getCentroid(extremes[2][0], extremes[2][1])*posMultiply;    
    console.log("center x: "+centerX+" center y: "+centerY+" center z: "+centerZ);
        
  //go through the array again and normalize the values to 0-1 range
    for (i = 0; i < Y.length; i++) { 
        
        var currNorm = [ map_range( Y[i][0],  extremes[0][0], extremes[0][1], 0, 1) , map_range( Y[i][1],  extremes[1][0], extremes[1][1], 0, 1) , map_range( Y[i][2],  extremes[2][0], extremes[2][1], 0, 1) ] ; //map all values to 0-1 into array
        normalized[i] = currNorm;
        var currEl = document.getElementById(i);

        var currOffset = [ ( (Math.random()*2) -1.0   ) * randomOffset ,  ( (Math.random()*2) -1.0   ) * randomOffset, ( (Math.random()*2) -1.0   ) * randomOffset ];
        //display centered
        currEl.setAttribute("position", (normalized[i][0]*posMultiply + currOffset[0] - centerX) + " " + ( normalized[i][1]*posMultiply + currOffset[1] -centerY) + " " + ( normalized[i][2]*posMultiply + currOffset[2] -centerZ ) ); //assign solved tSNE to VR elements
        //currEl.setAttribute("position", (normalized[i][0]*posMultiply + currOffset[0] ) + " " + ( normalized[i][1]*posMultiply + currOffset[1] ) + " " + ( normalized[i][2]*posMultiply + currOffset[2] ) ); //assign solved tSNE to VR elements
    }
    

    function map_range(value, srcRange0, srcRange1, dstRange0, dstRange1){
      // value is outside source range return
      if (value < srcRange0 || value > srcRange1){
        return NaN; 
      }
    
      var srcMax = srcRange1 - srcRange0,
          dstMax = dstRange1 - dstRange0,
          adjValue = value - srcRange0;
    
      return (adjValue * dstMax / srcMax) + dstRange0;
    
    }

    /*
    function map_range(value, low1, high1, low2, high2) {
        if(low1<0){
            return low2 + (high2 - low2) * (value + low1) / (high1 + low1);
        }
        return low2 + (high2 - low2) * (value - low1) / (high1 - low1);
    }
     */
    
    return normalized;
}

//change parameters interactively with sliders----------------------------------

function updatetsne(){
   runTsne();
}

/*
//THESE ARE TO ENABLE INTERACTIVE SLIDERS FOR TSNE PARAMS - now set in theme customizer as static settings
var sliderIter = document.getElementById("iterations");
var sliderIterVal = document.getElementById("iterations_val");

var perplex = document.getElementById("perplex");
console.log("slider val: "+perplex.value);
var perplexityVal = document.getElementById("perplex_val");

var learnrate = document.getElementById("learnrate");
var learnrateVal = document.getElementById("learnrate_val");

//set once on Load:--------------
perplex.value = opt.perplexity;
learnrate.value = opt.epsilon;
sliderIter.value = numIter;

sliderIter.oninput = function() {
  numIter = this.value;
  sliderIterVal.innerHTML = this.value;
}
perplex.oninput = function() {
  opt.perplexity = this.value;
  perplexityVal.innerHTML = this.value;
} 
learnrate.oninput = function() {
  opt.epsilon = this.value;
  learnrateVal.innerHTML = this.value;
}
*/
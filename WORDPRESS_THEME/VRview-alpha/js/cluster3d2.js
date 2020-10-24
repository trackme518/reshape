
console.log("tsne script running");

var numIter = 1000;

var opt = {};
opt.epsilon = 10;
opt.perplexity = 5;
opt.dim = 3;
var tsne = new tsnejs.tSNE(opt);
//var inputData = ' . $printTagsToArray . ';
var inputData = JSON.parse( document.getElementById("printTagsToArray").getAttribute("data-tsne") );  //fetch data outputted by php into html element as 2D array

//tsne.initDataRaw(inputData);


var posMultiply = 10; //multiply normalized positions
var randomOffset = 2;
  
var getNormalized = runTsne();
//console.log( "normalized solution: " + getNormalized );

function runTsne(){

tsne.initDataRaw(inputData);

console.log("dense data size: " + inputData.length + " ");

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

//change parameters interactively with sliders----------------------------------

function updatetsne(){
   runTsne();
}

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

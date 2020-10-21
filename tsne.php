<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <meta name="generator" content="PSPad editor, www.pspad.com">
  <title></title>
  <script src="scripts/tsne.js"></script>
  </head>
  <body>
   <p>just a test</p>
   
  <script>
var opt = {};
opt.epsilon = 10;
opt.perplexity = 5;
opt.dim = 3;
var tsne = new tsnejs.tSNE(opt);
   
  // initialize data. Here we have 3 points and some example pairwise dissimilarities
  var dists = [[0,0,0,0],[1,0,0,0],[1,0,0,0],[0,0,0,0],[0,0,0,0],[1,1,0,0],[1,1,0,0],[1,1,0,0],[1,1,0,0],[1,1,0,0],[1,1,0,0],[1,0,0,0],[0,0,0,0],[1,1,0,0],[0,0,0,0],[1,1,0,0]];
  
  //there has to be same number of elemtns as is size of each array?? wtf?
  //var dists = [[1, 0, 1, 0, 0], [0,0, 1, 0, 0], [0, 0, 1, 0, 0],[0,0, 1, 0, 0], [0, 0,1, 0, 0]];
  //tsne.initDataDist(dists);
  
  //var dists = [[1, 0, 1, 0], [0, 1, 0, 0], [0, 1, 0, 0],[0, 1, 0, 0], [ 0,1, 0, 0]];
  tsne.initDataRaw(dists);
   
  for(var k = 0; k < 500; k++) {
    tsne.step(); // every time you call this, solution gets better
  }
   
  var Y = tsne.getSolution(); // Y is an array of 2-D points that you can plot
  
for (i = 0; i < Y.length; i++) {
console.log("solution: x " + Y[i][0] + " y " + Y[i][1] + " z " + Y[i][2] );
} 


  </script>

  </body>
</html>


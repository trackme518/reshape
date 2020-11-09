//interact between  A-frame and rest of HTML DOM
AFRAME.registerComponent('a-frame-to-html', {
   schema: {
    id: {type: 'number', default: 0},
    fetchurl: {default: 'http://yourhomepage.com'},
    target: {type: 'selector'}
  },
  init: function () {
    var target = this.data.target;
    var postid = this.data.id;
    var fetchurl = this.data.fetchurl;
    //var currpath = this.data.path;
   // var type = this.data.type;
    var el = this.el;
    
    this.el.addEventListener('click', showcontent ); //add event listener for mouse click
    
    function showcontent(){
            
       target.classList.remove("down");
       target.classList.add("show");

      var fetchQuery = fetchurl+'/wp-json/wp/v2/posts/'+postid;
      console.log("fetch query: "+fetchQuery);
      //https://tricktheear.eu/wp-admin/customize.php?theme=VRview&return=https%3A%2F%2Ftricktheear.eu%2Fwp-admin%2Fthemes.php

       
       jQuery.get( fetchQuery, function( data ) {
         var htmltxt = '<div id="innerhtml">';
         
         htmltxt += '<h1>'+data.title.rendered+'</h1>';
         
         htmltxt += data.content.rendered; //append actual post from wp db
         //htmltxt += '<div class="overlay"><a id="btn-close" onclick="javascript:hideme(\''+target.id+'\')">×</a></div>'; //×
          htmltxt += '<div class="overlay"><a id="btn-close" onclick="javascript:hideme(\''+target.id+'\')">&nbsp;BACK <i class="fas fa-arrow-circle-left"></i></a></div>'; //×
         htmltxt += '</div>';
         target.innerHTML = htmltxt; //target modal overlay with ijected content
        });
        //<i class="fas fa-arrow-circle-left"></i>
        ///adjustCloseButton(); //call function that mimics the fixed position property but changes the top offset based on height of nav menu
    } 
 
  }
});
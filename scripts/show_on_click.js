//interact between  A-frame and rest of HTML DOM
AFRAME.registerComponent('a-frame-to-html', {
   schema: {
    id: {type: 'number', default: 0},
    path: {default: 'backend/uploads/placeholder.jpg'},
    target: {type: 'selector'},
    type: {default: 'image'}
  },
  init: function () {
    var target = this.data.target;
    var currpath = this.data.path;
    var type = this.data.type;
    var el = this.el;
    //let modal = document.querySelector('.modal')
    
    this.el.addEventListener('click', showcontent ); //add event listener for mouse click
    
    function showcontent(){
       var htmltxt = '<div id="innerhtml">';
       
       target.classList.remove("down");
       target.classList.add("show");
        
       if( type == 'image'){ //entry is of type image
         htmltxt += '<img src="'+currpath+'">';
      }
      
      if( type == 'video'){ //entry is of type image
         //htmltxt += '<img src="http://img.youtube.com/vi/'+youtube_parser(currpath)+'/0.jpg">';
         htmltxt += yt(currpath);
      }

      htmltxt += '<div class="overlay"><a id="btn-close" onclick="javascript:hideme(\''+target.id+'\')">×</a></div>'; //×
      htmltxt += '</div>';

      target.innerHTML = htmltxt;

    } 
  
 function youtube_parser(url){
    var regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/;
    var match = url.match(regExp);
    return (match&&match[7].length==11)? match[7] : false;
}
  
  function yt(url){
  
  console.log( youtube_parser(url) );
    var video_id = youtube_parser(url);
    var embedurl = 'http://www.youtube.com/embed/' + video_id + '/';
    return '<iframe width="560" height="315" src="'+embedurl+
    '" frameborder="0" allowfullscreen></iframe>'
    }  
 
  }
});
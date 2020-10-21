<?php
    /* Your password */
    $password = 'reshape568';

    if (empty($_COOKIE['password']) || $_COOKIE['password'] !== $password) {
        // Password not set or incorrect. Send to login.php.
        header('Location: login.php');
        exit;
    }else{
    //reshape568
    }
?>

<!DOCTYPE html>
<html lang="en">
	<head>
	<meta charset="utf-8">
	<title>ReShape Backend</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

	<!--------- Dropzone JS and CSS file ---------> 
	<script src="./dropzone-5.7.0/dist/min/dropzone.min.js"></script>
	<link rel="stylesheet" href="./dropzone-5.7.0/dist/min/dropzone.min.css">

	<!----------- Custom CSS ------->
	<style>

		body{
			background-color: #bababa;
		}

		.dropzone {
			border: 2px dashed rgb(65, 65, 149);
			max-height: 256px;	
			padding: 0px 20px !important;    
		}

		span {
			font-size: 25px;		
		}
		
		#icon {
			max-width: 150px;
		}

        
        #myDropzone{
        //width: 256px;
        height:150px;
        }
        
        .button {
          font: bold 11px Arial;
          text-decoration: none;
          background-color: #EEEEEE;
          color: #333333;
          padding: 2px 6px 2px 6px;
          border-top: 1px solid #CCCCCC;
          border-right: 1px solid #333333;
          border-bottom: 1px solid #333333;
          border-left: 1px solid #CCCCCC;
        }

	</style>

</head>
<body>

<div class="container pt-5">
	<div class="row">
		<div class="col-md-8 m-auto">

            <form action="upload.php" enctype="multipart/form-data" method="POST" id="metadata">
           
              <div id="myDropzone" class="dropzone"></div>
              <br>
              <!-- load tags from text file with javascript-->

<div id="mytags">              
<?php
//fetch tags from text file and display them as part of the form:

$whichTags = file_get_contents("tags.txt");
$tagsArray = explode(",", $whichTags);
$htmlout = "";
for ($i = 0; $i < sizeof($tagsArray); $i++) {
    if( $tagsArray[$i] != ""){
      $htmlout = $htmlout . '<input type="checkbox" id="' . $tagsArray[$i] . '" name="tag' . $tagsArray[$i] . '" value="' . $tagsArray[$i] . '">';
      $htmlout = $htmlout . '<label for="' . $tagsArray[$i] . '" id="label-' . $tagsArray[$i] .'" >&nbsp;'. strtoupper($tagsArray[$i]) .'&nbsp;</label>';
    }
}
echo $htmlout;

?>            
</div>

              <br>
              <input type="text" id="video" name="video" placeholder="video link">
              <br>
  
              <br><input type="submit" id="submit-all" value="SUBMIT">
            </form>
            <br>
            
            
             <form action="" method="POST" id="addtag">
               <input type="text" id="taginput" name="tag" placeholder="your-tag">
               <button type="button" onClick="javascript:addtag('add')">ADD</button> 
               <button type="button" onClick="javascript:addtag('delete')">DELETE</button> 
             </form>

	</div>
</div>

<script src="./jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

<!----------- Custom JS --------------->
<script>
//TODO:
//clean data after submit!

//ADD/Delete TAG FORM-----------------------------------------------
function addtag(dowhat){
  var tagtext = document.getElementById("taginput").value;
  tagtext = tagtext.toLowerCase();
  console.log(dowhat+" : "+tagtext);
      if(tagtext != ""){
          $.post("addTag.php", { "whattag" : tagtext, "dowhat" : dowhat, "password" :  getCookie("password") } );
          
          document.getElementById("addtag").reset(); //clear all data
           //---------------------------
        var tagParent = document.getElementById("mytags");
        var newTags = "";
        
           if(dowhat == "add"){
             newTags += '<input type="checkbox" id="'+tagtext+'" name="'+tagtext+'" value="'+tagtext+'">';
             newTags += '<label for="'+tagtext+'" id="label-'+tagtext+'">&nbsp;'+tagtext.toUpperCase()+'&nbsp;</label>';
             tagParent.innerHTML = tagParent.innerHTML + newTags;
           }

           if(dowhat == "delete"){
            var deletetag = document.getElementById(tagtext);
            var deletelabel = document.getElementById('label-'+tagtext);
            deletetag.parentNode.removeChild(deletetag);
            deletelabel.parentNode.removeChild(deletelabel);
           }
     
      }     
}
//DROPZONE FORM----------------------------------------
  var oldFile = "";
  var oldId = "";
  var oldExt = "";

Dropzone.options.myDropzone= {
    url: 'upload.php',
    autoProcessQueue: false,
    uploadMultiple: false,
    maxFiles: 1,
    maxFilesize: 1,
    acceptedFiles: 'image/*',
    addRemoveLinks: true,
    init: function() {
    
        dzClosure = this; // Makes sure that 'this' is understood inside the functions below.

        // for Dropzone to process the queue (instead of default form behavior):
        document.getElementById("submit-all").addEventListener("click", function(e) {
            // Make sure that the form isn't actually being sent.
            e.preventDefault();
            e.stopPropagation();
            dzClosure.processQueue();
            
             if (dzClosure.files.length) {
                  dzClosure.processQueue(); // upload files and submit the form
              } else {
                  //$('#my-dropzone').submit(); // just submit the form
                  console.log("sending wihtout image");
                  sendDataOnly();
              }
            //dzClosure.emit("sending");           
        });

        //replace previsouly added image with the new one:
        this.on("addedfile", function() {
          if (this.files[1]!=null){
             console.log('replacing: '+this.files[0].name);
             //$.post("delete.php", { "path" : oldFile, "id" : oldId } );
            this.removeFile(this.files[0]);
          }
        });

        //SEND ALL DATA - image int the dropzone MUST be present!
         this.on("sending", function(data, xhr, formData) {
         console.log("sending image and data...");
            //------------------------------------------------
            var stringTags = "";
            var formdata = $('#metadata').serializeArray().reduce(function(obj, item) {
            obj[item.name] = item.value;
            if(item.name !== 'video' && item.name !== 'password'){
                 if(item.value !== 'undefined'){
                   if(stringTags){
                      stringTags = stringTags + ";" + item.value;
                   }else{
                      stringTags = item.value;
                   }    
                }    
            }      
            return obj;
            }, {});
            
            formData.append("tags", stringTags);
            formData.append("video", formdata.video);
            formData.append("password", getCookie("password") );
                
            console.log("curr password: "+getCookie("password"));
            console.log('tags: '+stringTags);
            console.log("video link: "+ formdata.video );
            
            document.getElementById("metadata").reset(); //clear all data
        });
        
        function sendDataOnly(){
          //------------------------------------------------
            var stringTags = "";
            var formdata = $('#metadata').serializeArray().reduce(function(obj, item) {
            obj[item.name] = item.value;
            if(item.name !== 'video' ){ //&& item.name !== 'password'
                 if(item.value !== 'undefined'){
                   if(stringTags){
                      stringTags = stringTags + ";" + item.value;
                   }else{
                      stringTags = item.value;
                   }    
                }    
            }      
            return obj;
            }, {});
           
            console.log('video link: '+formdata.video);
            console.log('tags: '+stringTags);
            console.log('your password: '+getCookie("password") );
            
           $.post("upload.php", { "video" : formdata.video, "extension" : "video", "tags" :  stringTags, "password" :  getCookie("password") } ); 
           
           document.getElementById("metadata").reset(); //clear all data         
            
        }
 
        
        this.on("success", function(file, serverResponse) {
        console.log("server msg: " + serverResponse );
         
        this.removeFile(this.files[0]); //remove old file
         
         /* 
        var serverData = serverResponse.split(";");
        
          if(serverData.length == 3){
             oldFile = serverData[0];
             oldId = serverData[1];
             oldExt = serverData[2]; 
          }
        console.log("last file path: "+oldFile);
        console.log("last time stamp: "+oldId);
        */
        });
    
    
    }
}
//---------------------------------

    function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) == ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return "";
  }
//------------------------------- 

</script>

</body>
</html>
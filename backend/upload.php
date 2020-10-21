<?php
	// ------------- Check if file is not empty ------------
	
$pass = $_POST['password'];
if ($pass == "reshape568") { 

         $tags  = $_POST['tags'];
         $video = $_POST['video'];
         
         if( !empty($_FILES) && $video == "" ) { //there are files for upload and video link field is empty
          	$fileName =	$_FILES['file']['name'];
    		$file =	$_FILES['file']['tmp_name'];
            $fileType = 'none';
            //$source_path =	$_FILES['file']['tmp_name'];
    		//$fileExtension	=	pathinfo($fileName, PATHINFO_EXTENSION);

            $timeStamp =  time();
            $targetFile = $timeStamp."-".strtolower(str_replace(" ","-",$fileName));
        	$target_path = "./uploads/".$targetFile;
                   
            $source_properties = getimagesize($file);
            $image_type = $source_properties[2]; 
            //--------------RESIZE IMAGE-----------------------------------------------------------
            $target_width = 600;
            
              if( $image_type == IMAGETYPE_JPEG ) {   
                if( $source_properties[0] > $target_width){ //only resize if necessary
                  $image_resource_id = imagecreatefromjpeg($file);  
                  $target_layer = fn_resize($image_resource_id,$source_properties[0],$source_properties[1],$target_width);
                  imagejpeg( $target_layer, $target_path ); //this will save image
                }else{
                    move_uploaded_file ( $file, $target_path );
                }
                $fileType = 'image';
              }else if( $image_type == IMAGETYPE_GIF )  {
                 if( $source_properties[0] > $target_width){ //only resize if necessary
                   $image_resource_id = imagecreatefromgif($file);
                   $target_layer = fn_resize($image_resource_id,$source_properties[0],$source_properties[1],$target_width);
                   imagegif( $target_layer, $target_path );
                }else{
                    move_uploaded_file ( $file, $target_path );
                    //file_put_contents( $target_path , $targetFile ); 
                }
                $fileType = 'image';
              }else if ( $image_type == IMAGETYPE_PNG ) {
                if( $source_properties[0] > $target_width){ //only resize if necessary
                  $image_resource_id = imagecreatefrompng($file); 
                  $target_layer = fn_resize($image_resource_id,$source_properties[0],$source_properties[1]);
                  imagepng(  $target_layer, $target_path  );
                }else{
                   move_uploaded_file ( $file, $target_path ); 
                }
                $fileType = 'image';
             }
             
             //save info to databse
             if($fileType == 'image'){
               $timeStamp =  time();
               $txt = array($timeStamp, $targetFile, $fileType, $tags); //instead of path to image put the link to video     
               $handle = fopen("data.csv", "a");
               fputcsv($handle, $txt);
               fclose($handle);                  
               echo $targetFile .";" . $timeStamp . ";" . $fileType;
             }
        }//end there are files to be uploaded 
           
            
        if( $video != "") { //no files to be uploaded - can be just link to video 
           $timeStamp =  time();
           $fileType = 'video';
           
           //downloadYouTubeThubnailImage($video,'LOW','testyoutube.jpg','./uploads/')
            //download thumb image from youtube video - to avoid CORS problems later
            $pattern = "/^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/";
            preg_match($pattern, $video, $match); // Outputs 1
            if( strlen($match[7])==11){
              $videoid = $match[7];
              //echo 'video id: '.$match[7];
              $videothumb = 'http://img.youtube.com/vi/'.$match[7].'/0.jpg';
              file_put_contents('./uploads/youtube_'.$match[7].'.jpg', file_get_contents($videothumb) );
              
             $txt = array( $timeStamp, $video, $fileType, $tags ); //instead of path to image put the link to video   
             $handle = fopen("data.csv", "a");
             fputcsv($handle, $txt);
             fclose($handle);              
              
            echo $video .";" . $timeStamp . ";" . $fileType;
            } 
           
           
          
        }
}//end check password 

//utility functins--------------------------        		
function fn_resize($image_resource_id,$width,$height, $target_width) {
   $ratio = $target_width / $width; // get aspect ratio
   $target_height = $height * $ratio;
   $target_layer=imagecreatetruecolor($target_width,$target_height);
   imagecopyresampled($target_layer,$image_resource_id,0,0,0,0,$target_width,$target_height, $width,$height);
   return $target_layer;
}
?>
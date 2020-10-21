<?php

    $pass = $_POST['password'];
    $whattag = $_POST['whattag'];
    $dowhat = $_POST['dowhat'];
    
    if ($pass == "reshape568") {
        if( $whattag != ""){
        
        $txt =  strtolower($whattag) . ",";
        
          if($dowhat == "add"){  
            file_put_contents("tags.txt", $txt, FILE_APPEND);
          }
          
          if($dowhat == "delete"){
            $oldtags = file_get_contents("tags.txt");
            $newtags = str_replace($txt,"",$oldtags);
            file_put_contents("tags.txt", $newtags);
          }
        }
    } 
?>

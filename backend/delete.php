<?php
$pass = $_POST['password'];

if ($pass == "reshape568") { 
  $fileToDelete = $_POST['path'];
  $lineIndex = $_POST['id'];
  unlink( $fileToDelete );  //delete actual image
  //now delete the entry in data .csv database
  $table = fopen('data.csv','r');
  $temp_table = fopen('data_temp.csv','w');
  
  $id = $lineIndex; // the name of the column you're looking for
  
  while (($data = fgetcsv($table, 1000)) !== FALSE){
      if(reset($data) == $id){ // this is if you need the first column in a row
          continue;
      }
      fputcsv($temp_table,$data);
  }
  fclose($table);
  fclose($temp_table);
  rename('data_temp.csv','data.csv');
}
?>
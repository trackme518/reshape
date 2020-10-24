<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Virual Exhibition - ReShape</title>
<meta name="author" content="Vojtech Leischner www.trackmeifyoucan.com All rights reserved">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<!-- START THEME INJECTED SCRIPTS AND CSS -->
<?php wp_head(); ?>
<!-- END THEME INJECTED SCRIPTS AND CSS -->
 <style>

body{
padding: 0px;
margin: 0px;
}

#aframecontainer{
position: absolute;
width: 100%;
height: 100%;

}

#test{
width: 100%;
height: 100%;
}

#hide{
position: absolute;
width: 50%;
left: 50%;
height: 100%;
display: block;
background-color: red;
z-index: 10;
}

iframe{
        border: 0;
      }

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

.modal { position: absolute; z-index: -1; width: 100%; min-height: 100%; background: #FFFFFF;opacity: 0; transition: opacity 0.5s ease; }
.modal.show {opacity: 1; z-index: 100;}
.modal.hide {opacity: 0; z-index: 100;}
.modal.down {z-index: -1;}
/*
#innerhtml { position: relative;  width: 600px;  top: 0px; left: 50%; transform: translate(-50%, 0%); }
#innerhtml img{ display: block; max-width: 600px; margin: 0px; padding: 0px;}         
*/

#btn-close{color: #fff; text-align: center; position: absolute; right: 0px; top: 0px; background-color: #bfbfbf; font-family: monospace; font-size: 2em; line-height: 0.8em; padding: 5px;}
#btn-close:hover {color: #000000; background-color: #ffffff;}

.overlay {
  position: absolute;
  top: 0%;
  left: 100%;
}
/*   TOGGLE SWITCH   --------------- */
 /* The switch - the box around the slider */
/* Hide default input */
.toggle input {
  display: none;
}

/* The container and background */
.toggle {
  position: relative;
  display: inline-block;
  width: 68px;
  height: 30px;
}
.slidertoggle {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  border: 1px solid #aaa;
  border-radius: 30px;
  transition: all 0.4s;
}

/* The sliding button */
.slidertoggle:before {
  position: absolute;
  content: "";
  width: 24px;
  height: 24px;
  left: 2px;
  top: 2px;
  background-color: #eee;
  border-radius: 24px;
  transition: all 0.4s;
}

/* On checked */
input:checked + .slidertoggle {
  background-color: #2196F3;
}
input:checked + .slidertoggle:before {
  transform: translateX(37px);
}

.slidertoggle:after {
  position: absolute;
  content: "OFF";
  top: 0px;
  right: 5px;
  color: #fff;
  font-size: 0.9em;
}

input:checked + .slidertoggle:after {
  content: "ON";
  left: 10px;
}
 </style>
</head>
<body>


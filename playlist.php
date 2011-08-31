<?php
 //Grab the files from a directory
 $dir = 'trailers';
 if(is_dir($dir)){
     if($dd = opendir($dir)){
         while (($f = readdir($dd)) !== false)
             if($f != '.' && $f != '..'){
                 $files[] = $f;
             }
     closedir($dd);
     }
 }
//Create JS's header
header("Content-type: text/javascript");
$file = array();
 
foreach($files as $key => $val){
    //inserts the val into the array
     $file[$key] = $val;
}
 
//creates json obj with the array
echo 'var obj = '  .  json_encode($file)  .  ";\n";
?>
 
// listener function changes src
function myNewSrc() {
 
      var myVideo = document.getElementsByTagName('video')[0];
      myVideo.src = obj[(++i)%obj.length];
      myVideo.load();
      myVideo.play();
 
}
 
// function adds listener function to ended event
function myAddListener(){
     var myVideo = document.getElementsByTagName('video')[0];
     myVideo.addEventListener('ended', myNewSrc, false);
 
}
 
$(document).ready(function(){
 
    for(i in obj){
        var text = document.createTextNode(obj[i]);
        var list = document.getElementById('list');
        var bullet = document.createElement('li');
        var output = document.getElementById('output');
 
        var path = "vids/" + text.nodeValue;
 
        bullet.innerHTML= "<a href='" + path + "' onclick='return false;'>" + text.nodeValue + "</a>";
        list.appendChild(bullet);
        output.appendChild(list);
 
    }
 
    $('#list > li a').live('click', function(e){
        var t = e.target;
        var $this = $(this);
        var path = $this.attr('href');
        var myVideo = document.getElementsByTagName('video')[0];
 
        myVideo.src = path;
        myVideo.load();
        myVideo.play();
 
    });
	});

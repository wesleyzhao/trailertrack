<!DOCTYPE html>
<html lang="en">
<head><link rel="stylesheet" href="main.css" type="text/css"/>
<meta charset=utf-8>
</head>
<title>trailertrack</title>
<body>
<div class="wrapper">
<p align="center">
<!--<h1>trailertrack</h1>-->
<!-- "Video For Everybody" v0.4.2 by Kroc Camen of Camen Design -->
<?php

define('FACEBOOK_APP_ID', '152050941511848');
define('FACEBOOK_SECRET', '5290ad67b00e033d49953ddcc163914b');

function get_facebook_cookie($app_id, $application_secret) {
  $args = array();
  parse_str(trim($_COOKIE['fbs_' . $app_id], '\\"'), $args);
  ksort($args);
  $payload = '';
  foreach ($args as $key => $value) {
    if ($key != 'sig') {
      $payload .= $key . '=' . $value;
    }
  }
  if (md5($payload . $application_secret) != $args['sig']) {
    return null;
  }
  return $args;
}

$cookie = get_facebook_cookie(FACEBOOK_APP_ID, FACEBOOK_SECRET);


	function getIMDB(){
	$con=mysql_connect("host","username","password");
	mysql_select_db("trailtrackmov",$con);
		$curTrailer=$_COOKIE['TrailerSrc'];
	$result=mysql_query("SELECT IMDBrefer FROM trailerInfo WHERE TrailerSrc='".$curTrailer."'");
	$row=mysql_fetch_array($result);
	$IMDBrefer=$row['IMDBrefer'];
	mysql_close($con);
	return $IMDBrefer;
	}
	
	function getCurrentMovie(){
	$con=mysql_connect("trailtrackmov.db.7195872.hostedresource.com","trailtrackmov","pwn463PWN");
	mysql_select_db("trailtrackmov",$con);
		$curTrailer=$_COOKIE['TrailerSrc'];
	echo "curTrailer=".$curTrailer;
	$result=mysql_query("SELECT movieTitle FROM movieInfo WHERE TrailerSrc='".$curTrailer."'");
	$row=mysql_fetch_array($result);
	$movieTitle=$row['movieTitle'];
	mysql_close($con);
	return $movieTitle;
	}
	
	function getClipType(){
	$con=mysql_connect("trailtrackmov.db.7195872.hostedresource.com","trailtrackmov","pwn463PWN");
	mysql_select_db("trailtrackmov",$con);
		$curTrailer=$_COOKIE['TrailerSrc'];
	$result=mysql_query("SELECT ClipType FROM trailerInfo WHERE TrailerSrc='".$curTrailer."'");
	$row=mysql_fetch_array($result);
	$ClipType=$row['ClipType'];
	mysql_close($con);
	return $ClipType;
	}
?>
<div id="PLAYER">
<video controls="controls" autoplay="autoplay" poster="images/loading.png" width="640" height="360" id="videoPlayer">
	<source src="" type="video/mp4" />
	<object type="application/x-shockwave-flash" data="http://releases.flowplayer.org/swf/flowplayer-3.2.1.swf" width="640" height="360">
		<param name="movie" value="http://releases.flowplayer.org/swf/flowplayer-3.2.1.swf" />
		<param name="allowFullScreen" value="true" />
		<param name="wmode" value="transparent" />
		<param name="flashVars" value="config={'playlist':['http%3A%2F%2Fsandbox.thewikies.com%2Fvfe-generator%2Fimages%2Fbig-buck-bunny_poster.jpg',{'url':'trailers%2FHangOverTrailer.mov','autoPlay':true}]}" />
	</object>
</video>

</div>
<div id="movieTitle"><!--MOVIE TITLE--></div>
<div id="ClipType"><!--clip type--></div>
<div ALIGN=CENTER id="buttons">
<a href="javascript:likeClick();" id="likeButton"><img id="likeIMG" src="images/thumbs-up.png"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href="javascript:updateMovies(list);"><img src="images/next-arrow.png"></a>
<font size=2 face="Century Gothic" color="black" align="center">
</div>
<p align="center">
trailertrack.me coming soon...<br>
A site to watch trailers that <i>you</i> like.
</p>

<div id="recentLikes" class="recentLikes"></div>

<?php if ($cookie) { 
	$con=mysql_connect("trailtrackmov.db.7195872.hostedresource.com","trailtrackmov","pwn463PWN");
	$FBid=$cookie['uid'];
	if (!$con)
		{die('Could not connect: ' . mysql_error());}
	else{
		mysql_select_db("trailtrackmov",$con);
		$result=mysql_query("SELECT first_name,last_name,picture FROM Users WHERE FBid='".$FBid."'");
		$exists=mysql_num_rows($result);
		if (!$exists)
			{
			$user=json_decode(file_get_contents(
			'https://graph.facebook.com/'.$FBid.'?access_token='.
			$cookie['access_token']));
			$first_name=$user->first_name;
			$last_name=$user->last_name;
			$pic="http://graph.facebook.com/".$FBid."/picture";
			$test=mysql_query("INSERT INTO Users (FBid,first_name,last_name,picture,gender,email)
			VALUES ('" .$FBid. "','" .$first_name. "','" .$last_name. "','" .$pic. "','" .$user->gender. "','" .$user->email. "')");
			}
		else{
		$row=mysql_fetch_array($result);
		$first_name=$row['first_name'];
		$last_name=$row['last_name'];
		$pic=$row['picture'];
		}
		mysql_close($con);
	}
	 
	 } else { ?>
<p><font size=5 weight=bold>log in to save your favorites</font></p>
<p><fb:login-button></fb:login-button></p>
<?php } ?>
<div id="scripts"></div>
    <!--<p><fb:like></fb:like></p> THIS is the like button-->
    <div id="fb-root"></div>
	<script src="http://connect.facebook.net/en_US/all.js"></script>
	<div class="push"></div>
	</div> <!--End of wrapper -->
	<div id="footer-container">
	<div class="footer"><font color=#6699FF>trailertrack</font></div>
		<div id="welcome"><?php if($cookie) {echo "Welcome, " .$first_name." ".$last_name; 
		echo "<img src='" .$pic. "' height=30 width=30>";}?></div>
	</div>
	</body>
	
<script type="text/javascript">
function loadFiles(){
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

$file = array();
 $count=0;
foreach($files as $key => $val){
    //inserts the val into the array
     $file[$key] = $val;
	 $count=$count+1;
}
//creates json playlistArr with the array
echo 'var playlistArr = '  .  json_encode($file)  .  ";\n";

?>
return playlistArr
}
<?php echo 'var FBid = ' .json_encode($FBid) . ";\n"; 
?>
	playlistArr =loadFiles();	
	var list = new Array();
	updateMovies(list);
	
	var count=0;
	var TrailerSrc="";
	//var count=Math.floor(Math.random()*(list.length));
	//var nextVideo=
	var videoPlayer = document.getElementById('videoPlayer');
	//myHandler(true);
	videoPlayer.addEventListener('ended',myHandler,false);
	<?php
		if ($cookie) {
			echo "getRecentLikes(3);\n";
		}
	?>
	function myHandler(e){
		document.getElementById("likeButton").href="javascript:likeClick();";
		document.getElementById("likeIMG").src="images/thumbs-up.png";
		if(e){
		e=false;
		count=Math.floor(Math.random()*list.length);
		TrailerSrc = list[count];
		changeVidSrc(TrailerSrc);
		//updateTitles(TrailerSrc);
		//videoPlayer.src="trailers/"+TrailerSrc;
		//updateTitles();
		list.splice(count,1);
		if (list.length==0) loadFiles();
		}
	}
	function likeClick(){
		document.getElementById("likeButton").removeAttribute('href');
		document.getElementById("likeIMG").src="images/thumbs-up-clicked-color.png";
		if (window.XMLHttpRequest)
		{
			xmlhttpLike=new XMLHttpRequest();
		}
		else{
			xmlhttpLike=new ActiveXOBject("Microsoft.XMLHTTP");
		}
		xmlhttpLike.onreadystatechange=function()
		{
			if (xmlhttpLike.readyState==4 && xmlhttpLike.status==200)
				{
				getRecentLikes(3);
				}
		}
		xmlhttpLike.open("GET","likeClick.php?TrailerSrc="+TrailerSrc+"&FBid="+FBid,true);
		xmlhttpLike.send();
		
	}
	
	function posterClick(posterURL){
		if (window.XMLHttpRequest)
		{
			xmlhttp=new XMLHttpRequest();
		}
		else{
			xmlhttp=new ActiveXOBject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function()
		{
			if (xmlhttp.readyState==4 && xmlhttp.status==200)
				{
				document.getElementById("scripts").innerHTML=xmlhttp.responseText;
				}
		}
		xmlhttp.open("GET","posterClick.php?PosterSrc="+posterURL,true);
		xmlhttp.send();
	}
	
	function changeVidSrc(videoSrc){
		document.getElementById("likeButton").href="javascript:likeClick();";
		document.getElementById("likeIMG").src="images/thumbs-up.png";
		videoPlayer.src="trailers/"+videoSrc;
		updateTitles(videoSrc);
	}
	
	/**videoPlayer.onended=function(e){
		videoPlayer.src=nextVideo;
		if (count<2) count=count+1;
		else count=0;
		}**/
	  //start of FB gen code
      FB.init({appId: '<?= FACEBOOK_APP_ID ?>', status: true,
               cookie: true, xfbml: true});
      FB.Event.subscribe('auth.login', function(response) {
        window.location.reload();
      });
    //end of FB gen code
	
	function updateMovies(currentList){
		if (window.XMLHttpRequest)
		{
			xmlhttpUpdate=new XMLHttpRequest();
		}
		else{
			xmlhttpUpdate=new ActiveXOBject("Microsoft.XMLHTTP");
		}
		xmlhttpUpdate.onreadystatechange=function()
		{
			if (xmlhttpUpdate.readyState==4 && xmlhttpUpdate.status==200)
				{
				//document.getElementById("scripts").innerHTML=xmlhttp.responseText;
				//document.getElementById("scripts").innerHTML="lalalalalala";
				if (currentList.length == 0)
				list = eval(xmlhttpUpdate.responseText);
				myHandler(true);
				}
		}
		xmlhttpUpdate.open("GET","updateMovies.php?TrailerSrc="+TrailerSrc+"&FBid="+FBid+"&arrCount="+currentList.length,true);
		xmlhttpUpdate.send();
		
	
	}
	
	function updateTitles(string){
	var arr = string.split("_");
	var title = arr[0];
	var trailer = (arr[1].split("."))[0];
	document.getElementById('movieTitle').innerHTML=title;
	document.getElementById('ClipType').innerHTML=trailer;
	}
	function getRecentLikes(integer){
		if (window.XMLHttpRequest)
		{
			xmlhttp=new XMLHttpRequest();
		}
		else{
			xmlhttp=new ActiveXOBject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function()
		{
			if (xmlhttp.readyState==4 && xmlhttp.status==200)
				{
				document.getElementById("recentLikes").innerHTML=xmlhttp.responseText;
				}
		}
		xmlhttp.open("GET","getLikes.php?FBid="+FBid+"&num="+integer,true);
		xmlhttp.send();
	}
	
</script>
</html>

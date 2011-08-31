<?php
$PosterSrc = $_GET["PosterSrc"];
	$con=mysql_connect("host","username","password");
	mysql_select_db("trailtrackmov",$con);
	$curLikes=mysql_query("SELECT TrailerSrc FROM movieInfo WHERE PosterSrc='".$PosterSrc."'");
	$resultRow=mysql_fetch_array($curLikes);
	$trailerArr = split(',',$resultRow['TrailerSrc']);
	$TrailerSrc = $trailerArr[0];
	echo "<script type='text/javascript'>changeVidSrc('".$TrailerSrc."');</script>";
?>
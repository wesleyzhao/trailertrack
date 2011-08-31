<?php
$num = $_GET["num"];
$FBid = $_GET["FBid"];
	$con=mysql_connect("host","username","password");
	mysql_select_db("trailtrackmov",$con);
	$curLikes=mysql_query("SELECT likes FROM Users WHERE FBid='".$FBid."'");
	$resultRow=mysql_fetch_array($curLikes);
	if (strlen($resultRow['likes'])>1){
	$likeArr = split(',',$resultRow['likes']);
	if (count($likeArr)<$num) 
		{
		$len = count($likeArr);
		}
	else 
		{
		$len = $num;
		}
	echo "Trailers You Like";
	echo "<table border='1'>";
	for ($i =0 ; $i<$len ; $i++){
		$result = mysql_query("SELECT PosterSrc FROM movieInfo WHERE IMDBrefer='".$likeArr[count($likeArr)-1-$i]."'");
		$row = mysql_fetch_array($result);
		
		$TrailerResult=mysql_query("SELECT TrailerSrc FROM movieInfo WHERE PosterSrc='".$row['PosterSrc']."'");
		$tResult=mysql_fetch_array($TrailerResult);
		$trailerArr = split(',',$tResult['TrailerSrc']);
		$TrailerSrc = $trailerArr[0];
		
		$posterString = '"'.$row['PosterSrc'].'"';
		$trailerString = '"'.$TrailerSrc.'"';
		#echo "<tr><a href='javascript:posterClick(".$posterString.");'><img src=".$posterString." width = '125'></a></tr>";
		echo "<tr><a href='javascript:changeVidSrc(".$trailerString.");'><img src=".$posterString." width = '125'></a></tr>";
	}
	
	}
?>

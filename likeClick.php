<?php
$curTrailer = $_GET["TrailerSrc"];
$FBid = $_GET["FBid"];
	$con=mysql_connect("host","username","password");
	mysql_select_db("trailtrackmov",$con);
	$result=mysql_query("SELECT IMDBrefer FROM trailerInfo WHERE TrailerSrc='".$curTrailer."'");
	$row=mysql_fetch_array($result);
	$IMDBrefer=$row['IMDBrefer'];
	$curLikes=mysql_query("SELECT likes FROM Users WHERE FBid='".$FBid."'");
	$row2=mysql_fetch_array($curLikes);
	if (strlen($row2['likes'])>1){
		$arr=split(',',$row2['likes']);		//split likes by comma
		if (!in_array($IMDBrefer,$arr))
		{
			$res=mysql_query("UPDATE Users SET likes='" .$row2['likes']. "," .$IMDBrefer. "' WHERE FBid='" .$FBid. "'");
		}
	}
	else mysql_query("UPDATE Users SET likes='" .$IMDBrefer. "' WHERE FBid='" .$FBid. "'");
	mysql_close($con);
	echo $result;
?>

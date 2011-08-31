<?php
$curTrailer = $_GET["TrailerSrc"];
$FBid = $_GET["FBid"];
$arrCount = (int) ($_GET["arrCount"]);

	$con=mysql_connect("host","username","password");
	mysql_select_db("trailtrackmov",$con);
	$result=mysql_query("SELECT IMDBrefer FROM trailerInfo WHERE TrailerSrc='".$curTrailer."'");
	$row=mysql_fetch_array($result);
	$IMDBrefer=$row['IMDBrefer'];
	$curWatched=mysql_query("SELECT watched FROM Users WHERE FBid='".$FBid."'");
	$row2=mysql_fetch_array($curWatched);
	$arr = array();
	if (strlen($row2['watched'])>1){
		$arr=split(',',$row2['watched']);		//split watched by comma
		if (!in_array($IMDBrefer,$arr))
		{
			$res=mysql_query("UPDATE Users SET watched='" .$row2['watched']. "," .$IMDBrefer. "' WHERE FBid='" .$FBid. "'");
		}
	}
	else mysql_query("UPDATE Users SET watched='" .$IMDBrefer. "' WHERE FBid='" .$FBid. "'");
	
	getNewList($arr,5,$arrCount,$FBid);
	//mysql_close($con);
	function getNewList($watchedArr,$listSize,$currentListSize,$FBid){
		$con2=mysql_connect("trailtrackmov.db.7195872.hostedresource.com","trailtrackmov","pwn463PWN");
		mysql_select_db("trailtrackmov",$con2);
		$updatedList = array();
		if ($currentListSize==0)		//if the current playlist is empty
		{
			$trailerQuery = mysql_query("SELECT TrailerSrc,IMDBrefer FROM trailerInfo");
			//$trailerRow = mysql_fetch_array($trailerQuery);
			$trailerArr = array();
			$imdbArr = array();
			
			while ($trailerRow = mysql_fetch_array($trailerQuery)){
				$trailerArr[] = $trailerRow['TrailerSrc'];
				$imdbArr[] = $trailerRow['IMDBrefer'];
			}
			
			$dictArr = array();
			$dictArr = array_combine($imdbArr,$trailerArr);
			
			$diff = count($imdbArr)-count($watchedArr);		//finds the difference between imdbRef's in all, and amount watched
			if ($diff <= 0)		//if the user has watched ALL trailers in database, empty, reload
			{
				$rere=mysql_query("UPDATE Users SET watched='' WHERE FBid='".$FBid."'");
				//echo json_encode($rere).':'.json_encode($FBid);
				for ($i = 0 ; $i<$listSize ; $i++){
					$random = rand(0,count($trailerArr)-1);
					$updatedList[]=$trailerArr[$random];
					unset($trailerArr[$random]);
					$trailerArr = array_values($trailerArr);
				}
				
			}
			else{
				$watchedTrailerArr=array();
				$unwatchedTrailerArr = array();
				if ($diff <$listSize)		//if there are less than $listSize left unwatched
				{
					$listSize = $diff;
				}
				/*
				foreach ($watchedArr as $watchedIMDB){		//creates array of TrailerSrc's from IMDBrefer's in watchedArr
					$qry = mysql_query("SELECT TrailerSrc FROM TrailerInfo WHERE IMDBrefer='".$watchedIMDB."'")
					$qryRow = mysql_fetch_array($qry);
					$watchedTrailerArr[]=$qryRow['TrailerSrc'];
				}
				*/
				
				foreach ($imdbArr as $imdbRef){
				/*Fills unwatchedTrailerArr with TrailerSrc from the dictionary imdbRef=>TrailerSrc
				if the imdbRef is NOT in the watchedArr of IMDBrefer's
				*/
					if (!in_array($imdbRef,$watchedArr))
					{
						$unwatchedTrailerArr[]=$dictArr[$imdbRef];
					}
				}
				
				for ($i = 0; $i <$listSize ; $i ++){
					$random = rand(0,count($unwatchedTrailerArr)-1);
					$updatedList[]=$unwatchedTrailerArr[$random];
					//array_splice($unwatchedTrailerArr,$random);
					unset($unwatchedTrailerArr[$random]);
					$unwatchedTrailerArr = array_values($unwatchedTrailerArr);
				}
				
			}
			
			//echo '<script type="text/javascript">list = '  .  json_encode($updatedList)  .  ';\n</script>';
			/*
			$file = array();
			$count=0;
			foreach($updatedList as $key => $val){
			//inserts the val into the array
			$file[$key] = $val;
			$count=$count+1;
			}
			echo "<?php json_encode(".$file."); ?>";
			*/
			echo json_encode($updatedList);
		}
		else{
			/*if the playlist is still populated (count >0)*/
			//echo "at ELSE statement";
		}
		
		mysql_close($con2);
		
	}
	
	
	
?>
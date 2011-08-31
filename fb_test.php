<html>
<head></head>
<title>FB Test</title>


<body>
<?php
error_reporting(E_ALL);
echo 'here';
echo 'here5';
try{
require('src/facebook.php');
}
catch (Exception $e){
	echo 'error: '.$e;
}
echo 'here4';
$facebook = new Facebook(array( 
	'appId' => '152050941511848',
	'secret' => '5290ad67b00e033d49953ddcc163914b',
	'cookie' => true,
	
	));
echo 'here3';
function isLogged(){
global $facebook;
$session = $facebook->getSession();
if ($session){
	try{
		$uid = $facebook->getUser();
		$person = $facebook->api("/$uid");
		return $uid;
	}
	catch (FacebookApiException $e){
		return false;
	}
}
	else{
		return false;
	}
}
echo 'hereLogged';
if (isLogged()) {
	$ref = $facebook->getLogoutUrl();
	$log = "log-out";
	echo 'is logged in here';
}	
else{
	$ref = $facebook->getLoginUrl(array(
		'req_perms'=>'user_location,friends_location'
	));
	$log = 'log-in';
	echo 'is logged out here';
}

echo "<a href='$ref'>$log</a>";
echo 'here2';
if (isLogged()){
	$uid = isLogged();
	
	echo "<br>uid: $uid";
	$token = $facebook ->getAccessToken();
	echo "<br>access token:$token";
	
	$googleApi = "http://maps.google.com/maps/api/staticmap?size=480x480&sensor=false";
	
	$user = $facebook->api("/$uid?$token");
	$loc = $user['location']['name'];
	$homeLoc = makeStringLocation($loc);
		$googleApi = $googleApi.makeMarker($homeLoc,"yellow","U","normal");
	
	$person = $facebook->api("/$uid/friends");
	$friends =$person['data'];
	
	$count =strval(count($friends));
	
	echo "<br>friends count: $count";
	$num = 0;
	
	for ($i = 0;$i<50;$i++){
	//foreach ($friends as $fr){
	$fr = $friends[$i];
		echo "<br><b>name</b>: {$fr['name']}, <b>id</b>: {$fr['id']}";
		
$friend = $facebook->api("/{$fr['id']}?access_token=$token");
		
		try{
		$frLoc = $friend['location']['name'];
		if ($frLoc){
			echo '<ul>';
			echo "location:".makeStringLocation($frLoc);
			echo '</ul>';
			$googleApi = $googleApi.getPath($frLoc);

		}
		else{
			echo '<br><i>location info is really private</i>';
		}
		}
		catch (Exception $e){
			echo "caught.";
		}
		
	}
	
	/*
	foreach ($friends as $fr){
		echo "<br><b>name</b>: {$fr['name']}, <b>id</b>: {$fr['id']}";
	}
	*/
	
}
else{
	$person = $facebook->api("/prestonmui");
	$gender = $person['gender'];
	$name = $person['name'];
	echo "<br>name: {$person['name']}, gender: {$person['gender']}";
}


echo "<img src='$googleApi' />";
$marker = "&markers=size:mid%7Ccolor:green%7Clabel:1%7CShanghai,China%5C";
$path = "&path=Philadelphia,PA|Seattle,WA&path=Philadelphia,PA|Shanghai,China";

function makeMarker($location,$color,$label,$size = "mid"){
	$marker = "&markers=size:".$size."|color:".$color."|label:".$label."|".$location."%5C";
	return $marker;
}

function addPlus($arr){
//must have 2 indexs 0,1
	$arr[0] = str_replace(' ','+',$arr[0]);
	$arr[1] = str_replace(' ','+',$arr[1]);
	return $arr;
}

function makeLocation($arr){
	$string = implode(",",$arr);
	return $string;
}

function makeGoogleString($arr){
	$arr = addPlus($arr);
	return makeLocation($arr);
}

function makeStringLocation($string){
//inputs facebook string, converts to google url api string
	$arr1 = explode(', ',$string);
	$string = makeGoogleString($arr1);
	return $string;
}
function getPath($location1,$location2="Philadelphia,PA"){
	global $homeLoc;
	if (strlen($homeLoc)>0){
	$string2 = makeStringLocation($location2);
	}
	else {$string2 =  $homeLoc;}
	$string1 = makeStringLocation($location1);
	
	$url = "&path=".$string1."|".$string2;
	return $url.makeMarker($string1,"green","1");
}
?>


</body>
</html>
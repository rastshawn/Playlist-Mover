<?php

require_once 'credentials.php';
require_once 'vendor/autoload.php';
require_once 'settings.php';

session_start();
echo "<br>";

if (isset($_GET['cmd']) && $_GET['cmd'] == "logout") {
	unset($_SESSION['spotifyToken']);
	exit();
}

// if the access code has been granted, get the authentication token
if (isset($_GET['code']) && $_GET['code'] != ""){
	$code = $_GET['code'];
	
	$data = array(

		'grant_type'=>'authorization_code',
		'code'=>$code,
		'redirect_uri'=>'http://preznix.shawnrast.com/playlists/spotify/spotify.php',
		'client_id'=>$creds['id'],
		'client_secret'=>$creds['secret']
	);
	$response = \Httpful\Request::post("https://accounts.spotify.com/api/token")
		->body(http_build_query($data))
	//	->addHeader('Authorization', 'Basic '.base64_encode($creds['id'].':'.$creds['secret']))
		->addHeader('Content-Type','application/x-www-form-urlencoded')
		->expectsJson()
		->send();
	$token=$response->body->access_token;
	if ($token!="") {
		$_SESSION['spotifyToken'] = $token;

		// the redirect clears the token from the url
		header("Location: spotify.php");
	} else {
		print "Error getting token";
		exit();
	}
}

// check to see if we have a validated token in the session.
if (!isset($_SESSION['spotifyToken']) || $_SESSION['spotifyToken'] = '') {
	$url = 'https://accounts.spotify.com/authorize/?';
	$url .= "client_id=".$creds['id']."&response_type=code&";
	$url .= "redirect_uri=http://preznix.shawnrast.com/playlists/spotify/spotify.php";
	$url .= '&scope=playlist-read-private';
	header("Location: $url");
} 
?>

<html>


  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">

    <title>rastsc</title>

	</head>
    <!-- Bootstrap core CSS -->
 <!-- Latest compiled and minified CSS -->
 <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

 <!-- jQuery library -->
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

 <!-- Latest compiled JavaScript -->
 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> 

<script>
var token="<?echo $token;?>";
var clientID="<?echo $clientID;?>";
</script>
<body>
if you see this oAuth worked
</body>
</html>

<?php

// Handles oAuth flow for spotify. 


// Log out of spotify by removing the oAuth token from the session. 
if (isset($_GET['cmd']) && $_GET['cmd'] == "logout") {
	unset($_SESSION['spotifyToken']);
	exit();
}

// If there is an authorization code in the request, 
// this must be the second step of the oAuth flow. 
// Get the auth token with the code and reload the page. 
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
// If there isn't, start the oAuth flow by requesting an authorization code.
if (!isset($_SESSION['spotifyToken']) || $_SESSION['spotifyToken'] == '') {
	$url = 'https://accounts.spotify.com/authorize/?';
	$url .= "client_id=".$creds['id']."&response_type=code&";
	$url .= "redirect_uri=http://preznix.shawnrast.com/playlists/spotify/spotify.php";
	$url .= '&scope=playlist-read-private';
	header("Location: $url");
} 

?>


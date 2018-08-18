<?php

require_once 'credentials.php';
require_once 'vendor/autoload.php';

session_start();


// Authenticate the user with oAuth. 
require_once 'oAuth.php';
// if the user is at this line, they should have a token. 
$token = $_SESSION['spotifyToken'];


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
var token='<?php echo $token;?>';
</script>
<script>
$.ajax({
	url: 'https://api.spotify.com/v1/me/playlists',
	method: 'GET',
	headers: {
		'Authorization' : 'Bearer ' + token
	},
	success: function(response){
		console.log(response);
	},
	error: function(error){
		console.log(error);
	}
});
</script>
<body>
if you see this oAuth worked
</body>
</html>

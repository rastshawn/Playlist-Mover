<?php

require_once 'credentials.php';
require_once 'vendor/autoload.php';

session_start();

// Authenticate the user with oAuth. 
require_once 'oAuth.php';
// if the user is at this line, they should have a token. 
$token = $_SESSION['spotifyToken'];

?>

<html lang="en">
  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">

    <title>Playlists</title>

</head>


<!-- Bootstrap and JQuery -->    
<!-- Bootstrap core CSS -->
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> 


<!-- Add the spotify token to javascript so it can be used for API calls. -->
<script>
var token='<?php echo $token;?>';
</script>


<script>

var GET_PLAYLIST_ENDPOINT = "https://api.spotify.com/v1/me/playlists";


// Spotify's API returns paged results.
// For users with more than 20 playlists, or playlists with more than 
// 20 songs, this will return all of them at once. 
function getAll(endpoint) {
	return new Promise(function(resolve, reject) {
		$.ajax({
			url: endpoint, 
			method: 'GET',
			headers: {
				'Authorization' : 'Bearer ' + token
			},
			success: function(response){
				//console.log(response);
				if (response.next){
					var currentResponse = response.items;
					getAll(response.next).then(function(response) {
						resolve(currentResponse.concat(response));
					}, function(error) {
						reject(error);
					});
					
				} else {
					resolve(response.items);
				}
			},
			error: function(error){
				reject(error);
			}
		});
	});
}

function getTracks(playlistID) {
	var uri = "https://api.spotify.com/v1/playlists/" + playlistID + "/tracks";
	return getAll(uri);
}
function getPlaylists() {
	var uri = "https://api.spotify.com/v1/me/playlists";
	return getAll(uri);
}


///// OBJECTS
function Song(title, artists, album, source) {
	this.title = title;
	this.artists = artists;
	this.album = album;
	this.source = source;
}
function Playlist(name, songs) {
	this.name = name;
	this.songs = songs;
}
function Source(service, id) {
	this.service = service;
	this.id = id;
}


function playlistClick(e) {
	var playlistID = e.target.attributes.spotifyID.value;
	var tracks = [];
	getTracks(playlistID).then(function(resolve) {
		resolve.forEach(function(track) {
			var source = new Source("spotify", track.track.id);		
			var trackToAdd = new Song(
				track.track.name, 
				track.track.artists,
				track.track.album.name,
				source
			);
			tracks.push(trackToAdd);
		});

		var newPlaylist = new Playlist("output", tracks);
		console.log(newPlaylist);
	});
}

getPlaylists()
	.then(function(response) {
		/* make the list */
		$("#playlists").append("<ul>");
		response.forEach(function(playlist) {
			$("#playlists").append(
				"<li class='playlist' " + 
				"spotifyID='" + playlist.id + "'>"+ 
				playlist.name + "</li>"		
			);
		});
		$("#playlists").append("</ul>");
		
		/* add click listener */
		$(".playlist").click(playlistClick);
	}, function(error) {
		console.log(error);
	}
);

</script>
<body>
	<div id="playlists"></div>
</body>
</html>

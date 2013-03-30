var update_counter = 0;
$(document).ready(function() {
    $("#playlist").playlist(
        { 
            playerurl: "lib/drplayer/swf/drplayer.swf"
        }
    );
});

function getSongName(path) {
	var nameParts = path.split("/");
	var name = nameParts[nameParts.length-1];
	return name;
}

function updatePlaylist(playlist) {
	$.ajax({
    	url: URL_PROXY,
        type: "post",
		data: {store_playlist: playlist},
		success: function (response, textStatus, jqXHR){
		}
	});
}

function updateSong(song, target) {
	var timestamp = Math.round(new Date().getTime() / 1000);
	if(timestamp > song.expires) {
		$.ajax({
	    	url: URL_PROXY,
	        type: "post",
			data: {get_media: song.path},
			success: function (response, textStatus, jqXHR){
				response = JSON.parse(response);
				$("#song_url_"+target).attr("href", response.url);
				$(".spinner_"+target).remove();
				song.url = response.url;
				song.expires = response.expires;
			}
		});
	}
	else {
		$("#song_url_"+target).attr("href", song.url);
		$(".spinner_"+target).remove();
	}
	return song;
}

function updatePlaylist(path) {
	var request = $.ajax({
    	url: URL_PROXY,
        type: "post",
		data: {get_list: path},
		beforeSend: function() {
			$("#playlist_container").html("<img src='img/spinner.gif' />");
		},
		success: function (response, textStatus, jqXHR){
			response = eval(response);
			var counter = 1;
			var string = "<div id='playlist'>";
			for(var song in response) {
				string += "<div id='song_url_"+counter+"' href='' style='width: 400px;' class='item'>"
					+"<div>"
					+"<div class='fr duration'></div>"
					+"<div class='btn play'></div>"
					+"<div class='title'>"+getSongName(response[song].path)+"</div>"
					+"<div class='spinner_"+counter+"'><img src='img/spinner.gif'/></div>"
					+"</div>"
					+"<div class='player inactive'></div>"
					+"</div>";
				counter++;
			}
			string += "</div>";
			$("#playlist_container").html(string);
			
			counter = 1;
			for(var song in response) {
				response[song] = updateSong(response[song], counter);
				counter++;
			}
			// update playlist in the server
			var arrayToSend = {};
			arrayToSend[path] = encodeURIComponent(JSON.stringify(response));
//			updatePlaylist(arrayToSend);
			$.ajax({
		    	url: URL_PROXY,
		        type: "post",
				data: {store_playlist: arrayToSend},
				success: function (response, textStatus, jqXHR){
				}
			});
		}
	});
	request.done(function (response, textStatus, jqXHR) {
		$("#playlist").playlist(
            { 
                playerurl: "lib/drplayer/swf/drplayer.swf"
            }
    	);
	});
}
	
function updateFolder(path) {
	var request = $.ajax({
    	url: URL_PROXY,
        type: "post",
		data: {path: path, navigate: 1},
		beforeSend: function() {
			$("#folder_list_loading").html("<img src='img/spinner.gif' />");
		},
		success: function (response, textStatus, jqXHR){
			$("#folder_list_loading").html("");
			$("#folder_list").html(response);
		}
	});
	request.done(function (response, textStatus, jqXHR) {
	});
}
	
function fadetoblack() {
	$("body").animate({ backgroundColor: "#555555"}, 1500);
	$(".aceitunes").animate({backgroundColor: "#555555"}, 1500);
}
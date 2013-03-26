var URL_PROXY = "http://127.0.0.1/projects/ace.itun.es/proxy.php";
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

function updateSong(path, target) {
	$.ajax({
    	url: URL_PROXY,
        type: "post",
		data: {get_media: path},
		success: function (response, textStatus, jqXHR){
			response = JSON.parse(response);
			$("#song_url_"+target).attr("href", response.url);
			$(".spinner_"+target).remove();
		}
	});
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
				updateSong(response[song].path, counter);
				counter++;
			}
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
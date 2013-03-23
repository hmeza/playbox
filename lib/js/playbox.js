var URL_PROXY = "http://127.0.0.1/projects/ace.itun.es/proxy.php";
$(document).ready(function() {
    $("#playlist").playlist(
        { 
            playerurl: "lib/drplayer/swf/drplayer.swf"
        }
    );
});

function updatePlaylist(path) {
	var request = $.ajax({
    	url: URL_PROXY,
        type: "post",
		data: {path: path, play: 1},
		beforeSend: function() {
			$("#playlist_container").html("<img src='img/spinner.gif' />");
		},
		success: function (response, textStatus, jqXHR){
			$("#playlist_container").html(response);
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
<?php
require_once('conf/conf.php');
require_once('lib/bootstrap.php');
require_once('lib/dropbox.php');

define('DEFAULT_LINES_SEPARATOR', '<br><br>');

function drawMusicList(dropbox $o_dropbox, $s_lastPathParameter) {
	$s_content = 'My playlists:<br>';
	$st_list = $o_dropbox->getMusicList();
	foreach($st_list as $s_key => $s_entry) {
		$s_content .= '<a href="index.php?path='.$s_key.$s_lastPathParameter.'&play=true">Play</a> ';
		$s_content .= '<a href="'.$s_key.'">'.$s_key.'</a><br>';
	}
	return $s_content;
}

function drawPlaylist($st_playlist) {
	$s_content = '<div id="playlist">';
	foreach($st_playlist as $st_entry) {
		$s_content .= '<div href="'.$st_entry['url'].'" style="width: 400px;" class="item">
			<div>
			<div class="fr duration"></div>
			<div class="btn play"></div>
			<div class="title">'.$st_entry['path'].'</div>
			</div>
			<div class="player inactive"></div>
			</div>';
	}
	$s_content .= '
	</div>	
	<a href="javascript:void(0);" onClick="$(\'#playlist\').playlist(\'prev\');">Prev</a>
	<a href="javascript:void(0);" onClick="$(\'#playlist\').playlist(\'next\');">Next</a>
	<a href="javascript:void(0);" onClick="$(\'#playlist\').playlist(\'pause\');"><img src="lib/drplayer/i/pause.gif" alt="Pause" title="Pause"></a>
	<a href="javascript:void(0);" onClick="$(\'#playlist\').playlist(\'play\');"><img src="lib/drplayer/i/play.gif" alt="Play" title="Play"></a>';
	return $s_content;
}

function drawFolderList($metaData, $s_path, $s_lastPathParameter, $s_lastPath) {

	$s_content = 'Current path: '.$s_path.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	$s_content .= '<a href="index.php?path='.$s_path.$s_lastPathParameter.'&store=true">Store this path as playlist</a><br>';
	$s_content .= '<a href="index.php?path='.$s_lastPath.'">Go back</a>'.DEFAULT_LINES_SEPARATOR;
	foreach($metaData['body']->contents as $o_item) {
		if($o_item->is_dir == 1) {
			$s_content .= '<a href="index.php?path='.$o_item->path.$s_lastPathParameter.'">'.$o_item->path.'</a><br>';
		}
		else {
			$s_content .= '<a href="https://dl-web.dropbox.com/get'.$o_item->path.'" target="blank">'.$o_item->path.'</a><br>';
		}
	}
	return $s_content;
}

global $dropbox;
$o_dropbox = new dropbox($dropbox);
if(isset($_GET['path'])) $s_path = urldecode($_GET['path']);
else $s_path = "/";
if(isset($_GET['last_path'])) $s_lastPath = urldecode($_GET['last_path']);
else $s_lastPath = '/';
$s_message =  '';
$s_playlist = '';
if(isset($_GET['store']) && $_GET['store'] == true) {
	$st_list = $o_dropbox->getMusicList();
	$o_dropbox->addMusicPath($s_path);
	$o_dropbox->storeMusicList();
	$s_message = "Folder stored successfully";
}
if(isset($_GET['play']) && $_GET['play'] == true) {
        $s_playlist = drawPlaylist($o_dropbox->getSharedPlaylist($_GET['path']));
	$s_message = "Now playing ".dropbox::getNameFromPath($_GET['path']);
}


$s_lastPathParameter = '&last_path='.$s_path;
// Get html content to show folders and files
$s_content = drawFolderList($dropbox->metaData($s_path), $s_path, $s_lastPathParameter, $s_lastPath);
// Get playlists
$s_playlists = drawMusicList($o_dropbox, $s_lastPathParameter);

?>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link rel="Stylesheet" href="lib/drplayer/drplayer.css" type="text/css" />

    <script src="lib/js/jquery.js" type="text/javascript"></script>

    <script src="lib/drplayer/drplayer.js" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $("#playlist").playlist(
                {
                    playerurl: "lib/drplayer/swf/drplayer.swf"
                }
            );
        });
    </script>
<body>
<table width="100%">
<td colspan=2>
</td>
<?php echo $s_message.DEFAULT_LINES_SEPARATOR; ?>
<td width="50%">
<?php
echo $s_content;
?>
</td>
<td>
<?php
echo $s_playlists.DEFAULT_LINES_SEPARATOR;
echo $s_playlist.DEFAULT_LINES_SEPARATOR;
?>
</td>
</table>
<!-- Work in progress -->
Thanks to http://devreactor.com/ for the player
</body>
</html>

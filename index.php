<?php
require_once('conf/conf.php');
require_once('lib/bootstrap.php');
require_once('lib/dropbox.php');

include 'conf/lang/esp.php';

define('DEFAULT_LINES_SEPARATOR', '<br><br>');

function drawMusicList(dropbox $o_dropbox, $s_lastPathParameter) {
	$s_content = LANG_MY_PLAYLIST.'<br>';
	$st_list = $o_dropbox->getMusicList();
	foreach($st_list as $s_key => $s_entry) {
		$s_content .= '<a href="index.php?path='.$s_key.$s_lastPathParameter.'&remove=true">'.LANG_REMOVE.'</a> ';
		$s_content .= '<a href="index.php?path='.$s_key.$s_lastPathParameter.'&play=true">'.LANG_PLAY.'</a> ';
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
	<a href="javascript:void(0);" onClick="$(\'#playlist\').playlist(\'prev\');">'.LANG_PREV.'</a>
	<a href="javascript:void(0);" onClick="$(\'#playlist\').playlist(\'next\');">'.LANG_NEXT.'</a>
	<a href="javascript:void(0);" onClick="$(\'#playlist\').playlist(\'pause\');"><img src="lib/drplayer/i/pause.gif" alt="'.LANG_PAUSE.'" title="'.LANG_PAUSE.'"></a>
	<a href="javascript:void(0);" onClick="$(\'#playlist\').playlist(\'play\');"><img src="lib/drplayer/i/play.gif" alt="'.LANG_PLAY.'" title="'.LANG_PLAY.'"></a>';
	return $s_content;
}

function drawFolderList($metaData, $s_path, $s_lastPathParameter, $s_lastPath) {
	$s_content = LANG_CURRENT_PATH.$s_path.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	$s_content .= '<a href="index.php?path='.$s_path.$s_lastPathParameter.'&store=true">'.LANG_STORE_THIS_PATH.'</a><br>';
	$s_content .= '<a href="index.php?path='.$s_lastPath.'">'.LANG_GO_BACK.'</a>'.DEFAULT_LINES_SEPARATOR;
	foreach($metaData['body']->contents as $o_item) {
		if($o_item->is_dir == 1) {
			$s_content .= '<a href="index.php?path='.$o_item->path.$s_lastPathParameter.'">'.$o_item->path.'</a><br>';
		}
		else {
			$s_content .= $o_item->path.'<br>';
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
	$s_message = LANG_STORE_SUCCESSFULLY;
}
if(isset($_GET['play']) && $_GET['play'] == true) {
	$s_playlist = drawPlaylist($o_dropbox->getSharedPlaylist($_GET['path']));
	$s_message = LANG_NOW_PLAYING.dropbox::getNameFromPath($_GET['path']);
}
if(isset($_GET['remove']) && $_GET['remove'] == true) {
	$o_dropbox->removeMusicPath($_GET['path']);
	$o_dropbox->storeMusicList();
	$s_path = "/";
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
<td width="50%" valign="top">
<?php
echo $s_content;
?>
</td>
<td valign="top">
<?php
echo $s_playlists.DEFAULT_LINES_SEPARATOR;
echo $s_playlist.DEFAULT_LINES_SEPARATOR;
?>
</td>
</table>
</body>
</html>

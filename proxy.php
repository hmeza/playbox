<?php
require_once('conf/conf.php');
require_once('lib/bootstrap.php');
require_once('lib/dropbox.php');
require_once('lib/view.php');

$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
include 'conf/lang/'.$lang.'.php';

/**
 * Sort paths by name starting with folders.
 * @param Object $a
 * @param Object $b
 */
function sortPaths($a, $b) {
	if(!$a->is_dir && !$b->is_dir) {
		return (strcmp(strtoupper(dropbox::getNameFromPath($a->path)), strtoupper(dropbox::getNameFromPath($b->path))) > 0) ? 1 : 0;
	}
    else if($a->is_dir == $b->is_dir || $a->is_dir && !$b->is_dir) {
    	return 0;
    }
    return (!$a->is_dir && $b->is_dir) ? 1 : 0;
}

global $dropbox;
$s_bodyEnd = '';
$o_dropbox = new dropbox($dropbox);
if(isset($_POST['path']) && !empty($_POST['path'])) $s_path = urldecode($_POST['path']);
else $s_path = "/";
$s_message =  '';
$s_response = '';
// Save the current music path to the list of paths
if(isset($_POST['store'])) {
	$st_list = $o_dropbox->getMusicList();
	$o_dropbox->addMusicPath($s_path);
	$o_dropbox->storeMusicList();
	$s_message = LANG_STORE_SUCCESSFULLY;
}
// Play a playlist/folder
if(isset($_POST['play'])) {
	$s_response = \view::drawPlaylist($o_dropbox->getSharedPlaylist($s_path));
	$s_message = LANG_NOW_PLAYING.dropbox::getNameFromPath($_POST['path']);
}
// Remove a playlist/folder from the playlists
if(isset($_POST['remove'])) {
	$o_dropbox->removeMusicPath($_GET['path']);
	$o_dropbox->storeMusicList();
	$s_path = "/";
}
// Retrieve a folder
if(isset($_POST['navigate'])) {
	$s_response = \view::drawFolderList($dropbox->metaData($s_path), $s_path);
}
if(isset($_POST['get_list'])) {
	$s_response = $o_dropbox->getSharedPlaylist($_POST['get_list']);
	$s_response = json_encode($s_response);
}
if(isset($_POST['get_media'])) {
	$s_response = $o_dropbox->shareSong($_POST['get_media']);
	$s_response = json_encode($s_response);	
}
if(isset($_POST['store_playlist'])) {
	error_log("received: ".print_r($_POST['store_playlist'],true));
	$st_list = $o_dropbox->getMusicList();
	$o_dropbox->storePlaylist(json_decode($_POST['store_playlist']))
				->storeMusicList();
	$s_response = LANG_STORE_SUCCESSFULLY;
}
echo $s_response;
?>
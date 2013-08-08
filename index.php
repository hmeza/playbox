<?php
require_once('conf/conf.php');
require_once('lib/bootstrap.php');
require_once('lib/dropbox.php');
require_once('lib/view.php');

$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
include 'conf/lang/'.$lang.'.php';

define('DEFAULT_LINES_SEPARATOR', '<br><br>');

/**
 * Sort paths starting with folders.
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
if(isset($_GET['path']) && !empty($_GET['path'])) $s_path = urldecode($_GET['path']);
else $s_path = "/";
$s_message =  '';
$s_playlist = \view::drawPlaylist(array());
if(isset($_GET['store'])) {
	$st_list = $o_dropbox->getMusicList();
	$o_dropbox->addMusicPath($s_path);
	$o_dropbox->storeMusicList();
	$s_message = LANG_STORE_SUCCESSFULLY;
}
if(isset($_GET['remove'])) {
	$o_dropbox->removeMusicPath($_GET['path']);
	$o_dropbox->storeMusicList();
	$s_path = "/";
}

\view::$s_playlist = $s_playlist;
\view::main($o_dropbox, $dropbox, $s_message, $s_path, $s_bodyEnd);
?>
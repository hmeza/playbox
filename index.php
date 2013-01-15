<?php
require_once('conf/conf.php');
require_once('lib/bootstrap.php');
require_once('lib/dropbox.php');
require_once('lib/view.php');

include 'conf/lang/esp.php';

define('DEFAULT_LINES_SEPARATOR', '<br><br>');

/**
 * Sort paths starting with folders.
 * @param
 */
function sortPaths($a, $b) {
    if($a->is_dir == $b->is_dir || ($a->is_dir && !$b->is_dir)) return 0;
    return (!$a->is_dir && $b->is_dir) ? 1 : 0;
}

global $dropbox;
$o_dropbox = new dropbox($dropbox);
if(isset($_GET['path']) && !empty($_GET['path'])) $s_path = urldecode($_GET['path']);
else $s_path = "/";
$s_message =  '';
$s_playlist = '';
if(isset($_GET['store'])) {
	$st_list = $o_dropbox->getMusicList();
	$o_dropbox->addMusicPath($s_path);
	$o_dropbox->storeMusicList();
	$s_message = LANG_STORE_SUCCESSFULLY;
}
if(isset($_GET['play'])) {
	$s_playlist = \view::drawPlaylist($o_dropbox->getSharedPlaylist($_GET['path']));
	$s_message = LANG_NOW_PLAYING.dropbox::getNameFromPath($_GET['path']);
}
if(isset($_GET['remove'])) {
	$o_dropbox->removeMusicPath($_GET['path']);
	$o_dropbox->storeMusicList();
	$s_path = "/";
}

?>
<!-- Dirty, but in progress -->
<html>
<?php
echo \view::head();
?>
<body>
<?php
echo \view::facebookLikeScript();
?>
<table width="100%">
<td>
<?php echo $s_message.DEFAULT_LINES_SEPARATOR; ?>
</td>
<td>
<?php
echo \view::facebookLike();
echo \view::googlePlusOne();
?>
</td>
<tr>
<td width="50%" valign="top">
<?php
echo \view::drawFolderList($dropbox->metaData($s_path), $s_path, \dropbox::getParentPath($s_path));
?>
</td>
<td valign="top">
<?php
echo \view::drawMusicList($o_dropbox).DEFAULT_LINES_SEPARATOR;
echo $s_playlist.DEFAULT_LINES_SEPARATOR;
?>
</td>
</table>
</body>
</html>

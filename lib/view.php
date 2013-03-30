<?php

class view {
	static public $s_playlist = "";
	
	/**
	 * Draws the header of the view.
	 * @return string
	 */
	static public function head() {
		$s_return = '
<head>
	<title>'.SITE_NAME.'</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link rel="Stylesheet" href="aceitunes.css" type="text/css" />
    <link rel="Stylesheet" href="lib/drplayer/drplayer.css" type="text/css" />
	<script src="lib/js/jquery.js" type="text/javascript"></script>
	<script type="text/javascript" src="http://bit.ly/owj6Fv"> </script>
	<script type="text/javascript" src="http://bit.ly/oJARsF"> </script>
    <script src="lib/drplayer/drplayer.js" type="text/javascript"></script>
	<script src="lib/js/config.js" type="text/javascript"></script>
    <script src="lib/js/playbox.js" type="text/javascript"></script>
</head>';
		return $s_return;
	}
	
	/**
	 * Script to make Facebook Like button work.
	 * @return string
	 */
	static public function facebookLikeScript() {
		$s_return = '<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/es_LA/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, \'script\', \'facebook-jssdk\'));</script>';
		return $s_return;
	}
	
	/**
	 * Draws the Like button from Facebook.
	 * @return string
	 */
	static public function facebookLike() {
		$s_return = '<div class="fb-like" data-href="'.URL.'" data-send="true" data-layout="button_count" data-width="450" data-show-faces="true" data-font="arial"></div>';
		return $s_return;
	}
	
	/**
	 * Draws the google +1 button.
	 * @return string
	 */
	static public function googlePlusOne() {
		$s_return = '
<div class="g-plusone" data-size="medium" data-href="<?php echo URL; ?>"></div>
<script type="text/javascript">
  window.___gcfg = {lang: \'es\'};

  (function() {
    var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;
    po.src = \'https://apis.google.com/js/plusone.js\';
    var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>';
		return $s_return;
	}
	
	/**
	 * Draws the music list (list of playlists).
	 * @param dropbox $o_dropbox
	 * @return string
	 */
	static function drawMusicList(dropbox $o_dropbox) {
		$s_content = LANG_MY_PLAYLIST.'<br>';
		$st_list = $o_dropbox->getMusicList();
		foreach($st_list as $s_key => $s_entry) {
			$s_content .= '<a href="index.php?path='.$s_key.'&remove=true">'.LANG_REMOVE.'</a> ';
			$s_content .= '<span onclick="updatePlaylist(\''.$s_key.'\')" style="cursor:hand; cursor:pointer;">'.LANG_PLAY.'</span> ';
			$s_content .= '<a href="index.php?path='.$s_key.'">'.\dropbox::getNameFromPath($s_key).'</a><br>';
		}
		return $s_content;
	}

	static private function drawPlayButtons() {
		return '
		<a href="javascript:void(0);" onClick="$(\'#playlist\').playlist(\'prev\');">'.LANG_PREV.'</a>
		<a href="javascript:void(0);" onClick="$(\'#playlist\').playlist(\'next\');">'.LANG_NEXT.'</a>
		<a href="javascript:void(0);" onClick="$(\'#playlist\').playlist(\'pause\');">'.LANG_PAUSE.'</a>
		<a href="javascript:void(0);" onClick="$(\'#playlist\').playlist(\'play\');">'.LANG_PLAY.'</a>';
	}

	/**
	 * Draws the javascript code to be executed once the DOM is fully loaded.
	 * @return string
	 */
	static function bodyReady() {
		return '
			<script>
			$(document).ready(function () {
				$(\'#playlist\').playlist(\'play\');
			});
			</script>
				';
	}
	
	/**
	 * Shows the folder list.
	 * @param array $metaData
	 * @param string $s_path
	 * @param string $s_lastPath
	 * @return string
	 */
	static function drawFolderList($metaData, $s_path) {
		$s_content = LANG_CURRENT_PATH.$s_path.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		$s_content .= '<a href="index.php?path='.$s_path.'&store=true">'.LANG_STORE_THIS_PATH.'</a><br>';
		$s_content .= '<span class="aceitunes" onclick="updateFolder(\''.\dropbox::getParentPath($s_path).'\')">'.LANG_GO_BACK.'</span>'.DEFAULT_LINES_SEPARATOR;
		$s_content .= '<ul class="aceitunes">';
		usort($metaData['body']->contents, "sortPaths");
		foreach($metaData['body']->contents as $o_item) {
			if($o_item->is_dir == 1) {
				$s_content .= '<li class="aceitunes">
						<span class="aceitunes" onclick="updateFolder(\''.$o_item->path.'\')" class="aceitunes">'.\dropbox::getNameFromPath($o_item->path).'</span>
								</li>';
			}
			else {
				$s_content .= '<li class="aceitunes">
						<a href="" class="aceitunesDisabled">'.\dropbox::getNameFromPath($o_item->path).'</a>
								</li>';
			}
		}
		$s_content .= '</ul>';
		return $s_content;
	}
	
	/**
	 * Draws the index page.
	 * @param \dropbox $o_dropbox
	 * @param \Dropbox\API $dropbox
	 * @param string $s_message
	 * @param string $s_path
	 * @param string $s_bodyEnd
	 */
	static public function main($o_dropbox, $dropbox, $s_message = '', $s_path = '', $s_bodyEnd) {
		echo '
<!-- Dirty, but in progress -->
<html>
'.self::head().'
<body>
'.\view::facebookLikeScript().'
<table width="100%" class="aceitunes">
<tr>
<td class="aceitunes"><p class="aceitunes">'.SITE_NAME.'</p></td>
<td class="aceitunes">
'.self::facebookLike().'
'.self::googlePlusOne().'
</td>
</tr>
<tr>
<td colspan=2 class="aceitunes">
'.$s_message.DEFAULT_LINES_SEPARATOR.'
</td>
</tr>
<tr>
<td width="50%" valign="top" class="aceitunes">
<div id="folder_list_loading" style="height:16px;" class="aceitunes"></div>
<div id="folder_list" class="aceitunes">
'.\view::drawFolderList($dropbox->metaData($s_path), $s_path).'
</div>
</td>
<td valign="top" class="aceitunes">
<span class="aceitunes" onclick="fadetoblack();">Fade to black</span><br>
'.self::drawMusicList($o_dropbox).DEFAULT_LINES_SEPARATOR.'
<div id="playlist_container">
'.self::$s_playlist.DEFAULT_LINES_SEPARATOR.'
</div>
</td>
</table>
'.((isset($s_bodyEnd)) ? $s_bodyEnd : '').'
</body>
</html>';
	}
}
?>
<?php

class view {
	static public function head() {
		$s_return = '
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
</head>';
		return $s_return;
	}
	
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
	
	static public function facebookLike() {
		$s_return = '<div class="fb-like" data-href="'.URL.'" data-send="true" data-layout="button_count" data-width="450" data-show-faces="true" data-font="arial"></div>';
		return $s_return;
	}
	
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
	
	static function drawMusicList(dropbox $o_dropbox) {
		$s_content = LANG_MY_PLAYLIST.'<br>';
		$st_list = $o_dropbox->getMusicList();
		foreach($st_list as $s_key => $s_entry) {
			$s_content .= '<a href="index.php?path='.$s_key.'&remove=true">'.LANG_REMOVE.'</a> ';
			$s_content .= '<a href="index.php?path='.$s_key.'&play=true">'.LANG_PLAY.'</a> ';
			$s_content .= '<a href="index.php?path='.$s_key.'">'.\dropbox::getNameFromPath($s_key).'</a><br>';
		}
		return $s_content;
	}
	
	static function drawPlaylist($st_playlist) {
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
	
	static function drawFolderList($metaData, $s_path, $s_lastPath) {
		$s_content = LANG_CURRENT_PATH.$s_path.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		$s_content .= '<a href="index.php?path='.$s_path.'&store=true">'.LANG_STORE_THIS_PATH.'</a><br>';
		$s_content .= '<a href="index.php?path='.$s_lastPath.'">'.LANG_GO_BACK.'</a>'.DEFAULT_LINES_SEPARATOR;
	        usort($metaData['body']->contents, "sortPaths");
		foreach($metaData['body']->contents as $o_item) {
			if($o_item->is_dir == 1) {
				$s_content .= '<a href="index.php?path='.$o_item->path.'">'.$o_item->path.'</a><br>';
			}
			else {
				$s_content .= $o_item->path.'<br>';
			}
		}
		return $s_content;
	}
	
	static public function main() {
	}
}
?>
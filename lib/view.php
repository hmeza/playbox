<?php

class view {
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
	
	
	
	static public function main() {
		
	}
}
?>
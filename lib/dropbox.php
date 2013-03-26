<?php
include_once "lib/dropbox/Dropbox/API.php";
class dropbox {
	const MUSIC_LIST_FILE_NAME = "dropbox_player.txt";
	const TMP_FILE_FOLDER = '/tmp';
	const TMP_FILE_NAME = 'dropbox.txt';
	
	/**
	 * @var array contains a list of folders with music to be played.
	 */
	private $st_musicList = array();
	
	/**
	 * @var \Dropbox\API 
	 */
	private $o_dropboxHandler = null;

	/**
	 * Given a full pathname, returns the name of the file or last folder name.
	 * @param string $s_path
	 * @return string
	 */
	static public function getNameFromPath($s_path) {
		$st_parts = explode("/", $s_path);
		return end($st_parts);
	}
	
	static public function getParentPath($s_path) {
		$st_lastPathParts = explode("/", $s_path);
		array_pop($st_lastPathParts);
		return implode("/", $st_lastPathParts);
	}
	
	/**
	 * Creates a temporary file to be uploaded containing the list of folders.
	 */
	private function createMusicList() {
		$tmp = tempnam(self::TMP_FILE_FOLDER, self::TMP_FILE_NAME);
		file_put_contents($tmp, json_encode($this->st_musicList));
		// Upload the file with an alternative filename
		$put = $this->o_dropboxHandler->putFile($tmp, self::MUSIC_LIST_FILE_NAME);
		return $tmp;
	}
	
	public function __construct(\Dropbox\API $dropbox) {
		$this->o_dropboxHandler = $dropbox;
		$this->getMusicList();
	}
	
	/**
	 * Stores the music list into Dropbox.
	 */
	public function storeMusicList() {
		try {
			$tmp_filename = $this->createMusicList();
			unlink($tmp_filename);
		}
		catch(\Exception $e) {
		}
	}
	
	/**
	 * Loads the music list from Dropbox.
	 * @throws \Exception
	 * @return array:
	 */
	public function getMusicList() {
		if(empty($this->st_musicList)) {
			try {
				$st_data = $this->o_dropboxHandler->getFile("/".self::MUSIC_LIST_FILE_NAME, self::TMP_FILE_FOLDER."/".self::TMP_FILE_NAME);
				$tmp = tempnam(self::TMP_FILE_FOLDER, self::TMP_FILE_NAME);
				$s_data = file_get_contents($st_data['name']);
				$this->st_musicList = json_decode($s_data,true);
				if(isset($this->st_musicList['error']) || !is_array($this->st_musicList))
						throw new \Exception("Error reading music list file");
			}
			catch(\Exception $e) {
				$this->st_musicList = array();
				$this->storeMusicList();
			}
		}
		return $this->st_musicList;
	}
	
	/**
	 * Adds a new music folder into the music list, if it does not exist.
	 * @param string $s_path
	 */
	public function addMusicPath($s_path) {
		if(!in_array($s_path, $this->st_musicList))
			$this->st_musicList[$s_path] = array();
	}
	
	/**
	 * Removes a music folder if it exists.
	 * @param string $s_path
	 */
	public function removeMusicPath($s_path) {
		if(array_key_exists($s_path, $this->st_musicList))
			unset($this->st_musicList[$s_path]);
	}
	
	/**
	 * Request sharing for a certain folder to play it.
	 * @param string $s_path
	 * @return array
	 */
	public function share($s_path) {
		$metaData = $this->o_dropboxHandler->metaData($s_path);
		$st_shares = array();
		foreach($metaData['body']->contents as $o_item) {
			if($o_item->is_dir != 1 && strstr($o_item->path, ".mp3")) {
				$st_sharedItem = $this->o_dropboxHandler->media($o_item->path, false);
				$st_shares[] = array(
					'path' => $o_item->path,
					'url' => $st_sharedItem['body']->url,
					'expires' => $st_sharedItem['body']->expires
				);
			}
		}
		return $st_shares;
	}

	/**
	 * Retrieve playlist contents.
	 * @param string $s_path
	 * @return array
	 */
	public function getSharedPlaylist($s_path) {
		$st_list = $this->getMusicList();
		$st_music = array();
		if(array_key_exists($s_path, $st_list) && !empty($st_list[$s_path])) {
			$st_music = $st_list[$s_path];
		}
		else {
			$st_music = $this->share($s_path);
			$this->st_musicList[$s_path] = $st_music;
			$this->storeMusicList();
		}
		return $st_music;
	}
}

<?php

/**
 * OAuth storage handler built using the cache.
* @author Hugo Meza <hugo.meza.macias@gmail.com>
* @link https://github.com/benthedesigner/dropbox
* @package Dropbox\Oauth
* @subpackage Storage
*/
namespace Dropbox\OAuth\Storage;

class Cache extends Session
{
	/**
	 * Authenticated user ID
	 * @var int
	 */
	private $userID = null;

	/**
	 * Construct the parent object and
	 * set the authenticated user ID
	 * @param \Dropbox\OAuth\Storage\Encrypter $encrypter
	 * @param int $userID
	 * @throws \Dropbox\Exception
	 */
	public function __construct(Encrypter $encrypter = null, $userID)
	{
		session_start();
		// Construct the parent object so we can access the SESSION
		// instead of reading the file on every request
		parent::__construct($encrypter);

		// Set the authenticated user ID
		$this->userID = $userID;
	}

	/**
	 * Get an OAuth token from the file or session (see below)
	 * Request tokens are stored in the session, access tokens in the file
	 * Once a token is retrieved it will be stored in the user's session
	 * for subsequent requests to reduce overheads
	 * @param string $type Token type to retrieve
	 * @return array|bool
	 */
	public function get($type)
	{
		if ($type != 'request_token' && $type != 'access_token') {
			throw new \Dropbox\Exception("Expected a type of either 'request_token' or 'access_token', got '$type'");
		} elseif ($type == 'request_token') {
			return parent::get($type);
		} elseif ($token = parent::get($type)) {
			return $token;
		} else {
			$_SESSION[$this->namespace][$type] = $token;
			return $this->decrypt($token);
		}
	}

	/**
	 * Set an OAuth token in the file or session (see below)
	 * Request tokens are stored in the session, access tokens in the file
	 * @param \stdClass Token object to set
	 * @param string $type Token type
	 * @return void
	 */
	public function set($token, $type)
	{
		if ($type != 'request_token' && $type != 'access_token') {
			throw new \Dropbox\Exception("Expected a type of either 'request_token' or 'access_token', got '$type'");
		} elseif ($type == 'request_token') {
			parent::set($token, $type);
		} else {
			$token = $this->encrypt($token);
			$_SESSION[$this->namespace][$type] = $token;
		}
	}

	/**
	 * Delete the access token stored on disk for the current user ID
	 * @return bool
	 */
	public function delete()
	{
		parent::delete();
		$file = $this->getTokenFilePath();
		return file_exists($file) && @unlink($file);
	}
}

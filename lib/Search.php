<?php

/**
 * Handles initializing and adding/deleting documents in the search index.
 * Adding a document with an existing ID will update its values.
 */
class Search {
	/**
	 * The search backend.
	 */
	public static $backend;

	/**
	 * The search client object.
	 */
	public static $client;

	/**
	 * The search index object.
	 */
	public static $index;

	/**
	 * The error message if a document add fails.
	 */
	public static $error;

	/**
	 * Initialize the backend. Call this before `add()` with
	 * the `$appconf` array.
	 */
	public static function init ($appconf) {
		self::$backend = $appconf['Search']['backend'];
		switch (self::$backend) {
			case 'indextank':
				require_once ('apps/search/lib/indextank_client.php');
				self::$client = new ApiClient ($appconf['IndexTank']['private_api_url']);
				self::$index = self::$client->get_index ($appconf['IndexTank']['index_name']);
				break;
			case 'elasticsearch':
				require_once ('apps/search/lib/elastica_autoloader.php');

				$servers = array ();
				foreach ($appconf['ElasticSearch'] as $server) {
					if (! is_array ($server)) {
						continue;
					}
					if (strpos ($server['host'], '@') !== false) {
						$components = parse_url ($server['host']);
						$server['host'] = $components['host'];
						$server['headers'] = array (
							'Authorization' => 'Basic ' . base64_encode ($components['user'] . ':' . $components['pass']) . '=='
						);
						if ($components['scheme'] === 'https') {
							$server['transport'] => 'https';
						}
					}
					$servers[] = $server;
				}
			
				$config = array ('servers' => $servers);

				if ($appconf['ElasticSearch']['index_name'] === 'domain') {
					if (preg_match ('/^[a-zA-Z0-9\.-]+$/', $_SERVER['HTTP_HOST'])) {
						$index = preg_replace ('/^www\./', '', $_SERVER['HTTP_HOST']);
					} else {
						$index = 'webpages';
					}
				} else {
					$index = $appconf['ElasticSearch']['index_name'];
				}

				self::$client = new Elastica_Client ($config);
				self::$index = self::$client->getIndex ($index);
				break;
		}
	}

	/**
	 * Gets the description from an HTML body.
	 */
	public static function description ($body, $max = 160) {
		$body = trim (strip_tags ($body));
		$exploded = explode ('.', $body);
		$description = array_shift ($exploded);
		if (strlen ($description) > $max) {
			$description = substr ($description, 0, $max - 3) . '...';
		}
		return $description;
	}

	/**
	 * Add a document to the search index. Accepts a page ID
	 * and an array of the following fields: title, description,
	 * text, and url. Description and url are optional. Description
	 * will be taken from the first sentence of the text field, and
	 * the url will be set to the page ID with a leading slash.
	 */
	public static function add ($page, $doc) {
		if (! isset ($doc['description'])) {
			$doc['description'] = self::description ($doc['text']);
		}
		if (! isset ($doc['url'])) {
			$doc['url'] = '/' . $doc['page'];
		}
		$doc['text'] = $page . ' ' . $doc['title'] . ' ' . strip_tags ($doc['text']);
		
		switch (self::$backend) {
			case 'indextank':
				$res = self::$index->add_document ($page, $doc);
				if ($res === 200) {
					return true;
				}
				self::$error = 'Error adding document.';
				return false;
				break;
			case 'elasticsearch':
				$doc['id'] = $page;
				$type = self::$index->getType ('webpage');
				$doc = new Elastica_Document ($page, $doc);
				try {
					$res = $type->addDocument ($doc);
				} catch (Elastica_Exception_Client $e) {
					self::$error = $e->getMessage ();
					return false;
				}
				if ($res->isOk ()) {
					self::$index->refresh ();
					return true;
				}
				self::$error = $res->getError ();
				return false;
				break;
		}
	}

	/**
	 * Delete a document from the search index based on its ID.
	 */
	public static function delete ($page) {
		switch (self::$backend) {
			case 'indextank':
				$res = self::$index->delete_document ($page);
				if ($res === 200) {
					return true;
				}
				self::$error = 'Error deleting document.';
				return false;
				break;
			case 'elasticsearch':
				try {
					$res = self::$client->deleteIds (array ($page), 'webpages', 'webpage');
				} catch (Elastica_Exception_Client $e) {
					self::$error = $e->getMessage ();
					return false;
				}
				if ($res->isOk ()) {
					self::$index->refresh ();
					return true;
				}
				self::$error = $res->getError ();
				return false;
				break;
		}
	}
}

?>
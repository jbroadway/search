<?php

if (! $this->internal) {
	die ('Cannot add document from a browser.');
}

if (! isset ($this->data['page']) || empty ($this->data['page'])) {
	die ('Missing required field: page');
}

switch ($appconf['Search']['backend']) {
	case 'indextank':
		require_once ('apps/search/lib/indextank_client.php');
		
		$client = new ApiClient ($appconf['IndexTank']['private_api_url']);
		$index = $client->get_index ($appconf['IndexTank']['index_name']);
		
		// hopefully 200!
		return $index->delete_document ($this->data['page']);

	case 'elasticsearch':
		require_once ('apps/search/lib/elastica_autoloader.php');

		$servers = array ();
		foreach ($appconf['ElasticSearch'] as $server) {
			$servers[] = $server;
		}

		$client = new Elastica_Client (array ('servers' => $servers));

		$res = $client->deleteIds (array ($this->data['page']), 'webpages', 'webpage');
		if ($res->isOk ()) {
			return 200;
		}
		error_log ($res->getError ());
		return 500;
		break;

	default:
		error_log ('Unknown search backend: ' . $appconf['Search']['backend']);
		return false;
}

?>
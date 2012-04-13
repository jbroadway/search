<?php

if (! $this->internal) {
	die ('Cannot add document from a browser.');
}

$f = new Form;
$failed = $f->verify_values ($this->data, 'apps/search/forms/add.php');
if (count ($failed) > 0) {
	error_log ('Validation error on fields: ' . join (', ', $failed));
	return;
}
		
$body = trim (strip_tags ($this->data['body']));
$exploded = explode ('.', $body);
$description = array_shift ($exploded);
if (strlen ($description) > 160) {
	$description = substr ($description, 0, 157) . '...';
}
$body = $this->data['page'] . ' ' . $this->data['title'] . $body;
$url = '/' . $this->data['page'];

switch ($appconf['Search']['backend']) {
	case 'indextank':
		require_once ('apps/search/lib/indextank_client.php');
		
		$client = new ApiClient ($appconf['IndexTank']['private_api_url']);
		$index = $client->get_index ($appconf['IndexTank']['index_name']);
		
		// hopefully 200!
		return $index->add_document ($this->data['page'], array (
			'title' => $this->data['title'],
			'description' => $description,
			'text' => $body,
			'url' => $url
		));

	case 'elasticsearch':
		require_once ('apps/search/lib/elastica_autoloader.php');

		$servers = array ();
		foreach ($appconf['ElasticSearch'] as $server) {
			$servers[] = $server;
		}

		$client = new Elastica_Client (array ('servers' => $servers));
		$index = $client->getIndex ('webpages');
		$type = $index->getType ('webpage');

		$doc = new Elastica_Document ($this->data['page'], array (
			'id' => $this->data['page'],
			'title' => $this->data['title'],
			'description' => $description,
			'text' => $body,
			'url' => $url
		));

		$res = $type->addDocument ($doc);
		if ($res->isOk ()) {
			$index->refresh ();
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
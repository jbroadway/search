<?php

/**
 * This is the public-facing search engine page.
 */

switch ($appconf['Search']['backend']) {
	case 'indextank':
		if (! $this->internal) {
			$page->title = i18n_get ('Search');
		}

		if ($_GET['query']) {
			require_once ('apps/search/lib/indextank_client.php');

			$client = new ApiClient ($appconf['IndexTank']['private_api_url']);
			$index = $client->get_index ($appconf['IndexTank']['index_name']);

			$res = $index->search ($_GET['query'], null, null, null, 'description', 'title,url');

			$results = array ();
			foreach ($res->results as $row) {
				$results[] = (object) array (
					'url' => $row->url,
					'title' => html_entity_decode ($row->title, ENT_QUOTES, 'UTF-8'),
					'description' => html_entity_decode ($row->description, ENT_QUOTES, 'UTF-8')
				);
			}

			$total = $res->matches;

			echo $tpl->render ('search/results', array (
				'results' => $results,
				'total' => $total,
				'query' => $_GET['query']
			));
		} else {
			echo $tpl->render ('search/form');
		}
		return;

	case 'elasticsearch':
		if (! $this->internal) {
			$page->title = i18n_get ('Search');
		}

		if ($_GET['query']) {
			require_once ('apps/search/lib/elastica_autoloader.php');
	
			$servers = array ();
			foreach ($appconf['ElasticSearch'] as $server) {
				$servers[] = $server;
			}
	
			$client = new Elastica_Client (array ('servers' => $servers));
			$index = $client->getIndex ('webpages');
			$type = $index->getType ('webpage');

			$query = new Elastica_Query_QueryString ($_GET['query']);
			$res = $type->search ($query);

			$results = array ();
			foreach ($res as $row) {
				$results[] = (object) array (
					'url' => $row->url,
					'title' => $row->title,
					'description' => $row->description
				);
			}

			$total = $res->count ();

			echo $tpl->render ('search/results', array (
				'results' => $results,
				'total' => $total,
				'query' => $_GET['query']
			));
		} else {
			echo $tpl->render ('search/form');
		}
		return;

}

?>
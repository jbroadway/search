<?php

/**
 * This is the public-facing search engine page.
 */

if ($this->cli) {
	echo $this->run ('search/cli');
	return;
}

if (! $this->internal) {
	$page->title = i18n_get ('Search');
	if ($appconf['Search']['layout'] !== 'default') {
		$page->layout = $appconf['Search']['layout'];
	}
}

if (isset ($_GET['query']) && ! empty ($_GET['query'])) {
	Search::init ($appconf);
	if (Search::$backend === 'indextank') {
		$res = Search::$index->search ($_GET['query'], null, null, null, 'description', 'title,description,url');

		$results = array ();
		foreach ($res->results as $row) {
			$results[] = (object) array (
				'url' => $row->url,
				'title' => html_entity_decode ($row->title, ENT_QUOTES, 'UTF-8'),
				'description' => html_entity_decode ($row->description, ENT_QUOTES, 'UTF-8')
			);
		}

		$total = $res->matches;
	} else {
		$query = array (
			'query' => array (
				'query_string' => array (
					'query' => $_GET['query']
				)
			)
		);
		
		$type = Search::$index->getType ('webpage');
		$path = Search::$index->getName () . '/' . $type->getName () . '/_search';
		$response = Search::$client->request ($path, \Elastica\Request::GET, $query);
		$res = $response->getData ();

		$results = array ();
		foreach ($res as $row) {
			$results[] = (object) array (
				'url' => $row->url,
				'title' => $row->title,
				'description' => $row->description
			);
		}

		$total = count ($res);
	}

	echo $tpl->render ('search/results', array (
		'results' => $results,
		'total' => $total,
		'query' => $_GET['query']
	));
} else {
	echo $tpl->render ('search/form');
}

?>
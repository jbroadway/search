<?php

switch ($appconf['Search']['backend']) {
	case 'indextank':
		$page->add_script ('<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/jquery-ui.min.js"></script>
		<script type="text/javascript" src="/apps/search/js/indextank/jquery.indextank.ize.js"></script>
		<script type="text/javascript" src="/apps/search/js/indextank/jquery.indextank.autocomplete.js"></script>
		<script type="text/javascript" src="/apps/search/js/indextank/jquery.indextank.ajaxsearch.js"></script>
		<script type="text/javascript" src="/apps/search/js/indextank/jquery.indextank.renderer.js"></script>
		<script type="text/javascript" src="/apps/search/js/indextank/jquery.indextank.instantsearch.js"></script>
		<script type="text/javascript" src="/apps/search/js/indextank/jquery.indextank.basic.js"></script>
		<link type="text/css" rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/themes/' . $appconf['jQuery']['ui_theme'] . '/jquery-ui.css" media="all" />
		<style type="text/css">
		.result {
			padding-bottom: 10px;
		}
		.result a {
			font-weight: bold;
			display: block;
		}
		</style>
		<script>
		$(document).ready(function(){
			$("#search-form").indextank_Ize(\'' . $appconf['IndexTank']['public_api_url'] . '\', \'' . $appconf['IndexTank']['index_name'] . '\');
			var renderer =  $("#search-results").indextank_Renderer();
			$("#search-query").indextank_Autocomplete().indextank_AjaxSearch( {listeners: renderer}).indextank_InstantSearch();
		});
		</script>');
		
		echo $tpl->render ('search/index');
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
			echo $tpl->render ('search/elastic');
		}
		return;

}

?>
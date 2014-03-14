<?php

/**
 * Adds a page to the search index. Called by any hooks that specify
 * it in the global conf/config.php.
 */

if (! $this->internal) {
	die ('Cannot add document from a browser.');
}

$f = new Form;
$failed = $f->verify_values ($this->data, 'apps/search/forms/add.php');
if (count ($failed) > 0) {
	error_log ('Validation error on fields: ' . join (', ', $failed));
	return;
}

Search::init ($appconf);

return Search::add (
	$this->data['page'],
	array (
		'title' => $this->data['title'],
		'text' => $this->data['body'],
		'url' => '/' . $this->data['page']
	)
);

?>

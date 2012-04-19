<?php

/**
 * Deletes a page from the search index. Called by any hooks that specify
 * it in the global conf/config.php.
 */

if (! $this->internal) {
	die ('Cannot add document from a browser.');
}

if (! isset ($this->data['page']) || empty ($this->data['page'])) {
	error_log ('Missing required field: page');
	return;
}

Search::init ($appconf);
return Search::delete ($this->data['page']);

?>
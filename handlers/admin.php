<?php

/**
 * Admin handler simply does a re-index of the site,
 * either for initial search population or for major
 * updates to the site.
 */

$this->require_admin ();

$page->layout = 'admin';

$page->title = i18n_get ('Search');

if (isset ($_GET['index'])) {
	// Do the re-indexing
	set_time_limit (0);

	Search::init ($appconf);

	$conf_list = glob ('apps/*/conf/config.php');
	foreach ($conf_list as $conf) {
		$ini = parse_ini_file ($conf, true);
		if (isset ($ini['Admin']['search'])) {
			list ($res, $count) = call_user_func ($ini['Admin']['search']);
			if (! $res) {
				return $this->error (500, __ ('An error occurred'), Search::$error);
			}
		}
	}

	$this->add_notification (i18n_get ('Indexing completed.'));
	$this->redirect ('/search/admin');
}

echo $tpl->render ('search/admin');

?>
<?php

if (! $this->cli) die ('Must be run from the CLI');

$page->layout = false;

set_time_limit (0);

Search::init ($appconf);

$conf_list = glob ('apps/*/conf/config.php');
$total = 0;
foreach ($conf_list as $conf) {
	$ini = parse_ini_file ($conf, true);
	if (isset ($ini['Admin']['search'])) {
		list ($res, $count) = call_user_func ($ini['Admin']['search']);
		$total += $count;
		if (! $res) {
			Cli::out ('Error: ' . Search::$error, 'error');
			Cli::out ($total . ' documents indexed.');
			return;
		}
	}
}

Cli::out ($total . ' documents indexed.');

?>
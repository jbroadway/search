<?php

function __autoload_elastica ($class) {
	$path = str_replace ('\\', '/', trim ($class, '\\'));

    if (file_exists ('apps/search/lib/Elastica/lib/' . $path . '.php')) {
        require_once ('apps/search/lib/Elastica/lib/' . $path . '.php');
    }
}

spl_autoload_register ('__autoload_elastica', true, true);

?>
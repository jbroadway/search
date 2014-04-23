<?php

//if you're not using autoloading you can include this file

require_once 'apps/search/lib/Indextank/Exception.php';
require_once 'apps/search/lib/Indextank/Exception/IndexAlreadyExists.php';
require_once 'apps/search/lib/Indextank/Exception/IndexDoesNotExist.php';
require_once 'apps/search/lib/Indextank/Exception/InvalidDefinition.php';
require_once 'apps/search/lib/Indextank/Exception/InvalidQuery.php';
require_once 'apps/search/lib/Indextank/Exception/InvalidResponseFromServer.php';
require_once 'apps/search/lib/Indextank/Exception/InvalidUrl.php';
require_once 'apps/search/lib/Indextank/Exception/TooManyIndexes.php';
require_once 'apps/search/lib/Indextank/Exception/Unauthorized.php';
require_once 'apps/search/lib/Indextank/Exception/HttpException.php';
require_once 'apps/search/lib/Indextank/Response.php';
require_once 'apps/search/lib/Indextank/Api.php';
require_once 'apps/search/lib/Indextank/Index.php';


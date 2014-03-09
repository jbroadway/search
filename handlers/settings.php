<?php

// keep unauthorized users out
$this->require_admin ();

require_once ('apps/admin/lib/Functions.php');

// set the layout and page title
$page->layout = 'admin';
$page->title = __ ('Search Settings');

// create the form
$form = new Form ('post', $this);

// set the form data from the app settings
$server1 = Appconf::search ('ElasticSearch', 'server1');
$form->data = array (
	'layout' => Appconf::search ('Search', 'layout'),
	'backend' => Appconf::search ('Search', 'backend'),
	'server1_host' => $server1['host'],
	'server1_port' => $server1['port'],
	'public_api_url' => Appconf::search ('IndexTank', 'public_api_url'),
	'private_api_url' => Appconf::search ('IndexTank', 'private_api_url'),
	'index_name' => Appconf::search ('IndexTank', 'index_name'),
	'layouts' => admin_get_layouts (),
	'backends' => array ('elasticsearch' => 'ElasticSearch', 'indextank' => 'IndexTank')
);

echo $form->handle (function ($form) {
	// merge the new values into the settings
	$merged = Appconf::merge ('search', array (
		'Search' => array (
			'layout' => $_POST['layout'],
			'backend' => $_POST['backend']
		),
		'ElasticSearch' => array (
			'server1' => array (
				'host' => $_POST['server1_host'],
				'port' => $_POST['server1_port']
			)
		),
		'IndexTank' => array (
			'public_api_url' => $_POST['public_api_url'],
			'private_api_url' => $_POST['private_api_url'],
			'index_name' => $_POST['index_name']
		)
	));

	// save the settings to disk
	if (! Ini::write ($merged, 'conf/app.search.' . ELEFANT_ENV . '.php')) {
		printf (
			'<p>%s</p>',
			__ ('Unable to save changes. Check your permissions and try again.')
		);
		return;
	}

	// redirect to the main admin page with a notification
	$form->controller->add_notification (__ ('Settings saved.'));
	$form->controller->redirect ('/search/admin');
});

?>
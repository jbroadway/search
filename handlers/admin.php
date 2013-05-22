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

	// Check for pages
	$pages = Webpage::query ()
		->where ('access', 'public')
		->fetch_orig ();

	foreach ($pages as $page) {
		if (! Search::add (
			$page->id,
			array (
				'title' => $page->title,
				'text' => $page->body,
				'url' => '/' . $page->id
			)
		)) {
			return $this->error (500, 'An error occurred', Search::$error);
		}
	}

	// Check for blog posts
	$posts = blog\Post::query ()
		->where ('published', 'yes')
		->fetch_orig ();

	foreach ($posts as $post) {
		if (! Search::add (
			'blog/post/' . $post->id . '/' . URLify::filter ($post->title),
			array (
				'title' => $post->title,
				'text' => $post->body,
				'url' => '/blog/post/' . $post->id . '/' . URLify::filter ($post->title)
			)
		)) {
			return $this->error (500, 'An error occurred', Search::$error);
		}
	}

	// Check for events
	if (file_exists ('apps/events')) {
		$events = Event::query ()
			->fetch_orig ();
	
		foreach ($events as $event) {
			if (! Search::add (
				'events/' . $event->id . '/' . URLify::filter ($event->title),
				array (
					'title' => $event->title,
					'text' => $event->details,
					'url' => '/events/' . $event->id . '/' . URLify::filter ($event->title)
				)
			)) {
			return $this->error (500, 'An error occurred', Search::$error);
		}
		}
	}

	// Check for wiki
	if (file_exists ('apps/wiki')) {
		require_once ('apps/wiki/lib/markdown.php');
		require_once ('apps/wiki/lib/Functions.php');

		$pages = Wiki::query ()
			->fetch_orig ();
	
		foreach ($pages as $page) {
			if (! Search::add (
				'wiki/' . $page->id,
				array (
					'title' => str_replace ('-', ' ', $page->id),
					'text' => wiki_parse_body ($page->body),
					'url' => '/wiki/' . $page->id
				)
			)) {
			return $this->error (500, 'An error occurred', Search::$error);
		}
		}
	}

	$this->add_notification (i18n_get ('Indexing completed.'));
	$this->redirect ('/search/admin');
}

echo $tpl->render ('search/admin');

?>
<?php

use lithium\core\Libraries;
use lithium\action\Response;
use lithium\net\http\Router;
use li3_docs\extensions\route\Locale;

$config = Libraries::get('li3_docs');
$base = isset($config['url']) ? $config['url'] : '/docs';

/**
 * Handles broken URL parsers by matching method URLs with no closing ) and redirecting.
 */
Router::connect("{$base}/{:args}\(", array(), function($request) {
	return new Response(array('location' => "{$request->url})"));
});

Router::connect($base, array('controller' => 'li3_docs.ApiBrowser', 'action' => 'index'));

Router::connect("{$base}/{:lib}/{:args}", array(
	'controller' => 'li3_docs.ApiBrowser', 'action' => 'view'
));

Router::connect('/li3_docs/{:path:js|css}/{:file}.{:type}', array(), function($request) {
	$req = $request->params;
	$file = dirname(__DIR__) . "/webroot/{$req['path']}/{$req['file']}.{$req['type']}";

	if (!file_exists($file)) {
		return;
	}

	return new Response(array(
		'body' => file_get_contents($file),
		'headers' => array('Content-type' => str_replace(
			array('css', 'js'), array('text/css', 'text/javascript'), $req['type']
		))
	));
});

Router::connect(new Locale(array(
	'template' => $base,
	'params' => array('controller' => 'li3_docs.ApiBrowser')
)));

Router::connect(new Locale(array(
	'template' => "{$base}/{:lib}/{:args}",
	'params' => array('controller' => 'li3_docs.ApiBrowser', 'action' => 'view')
)));

?>
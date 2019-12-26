<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use DI\Container;

$container = new Container();
$container->set('renderer', function () {
	return new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');
});
AppFactory::setContainer($container);

$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);

$app->get('/', function ($request, $response) {
     return $response->write('Hello, world!');
});

$app->get('/users/{id}', function ($request, $response, $args) {
	$params = ['id' => $args['id'], 'nickname' => 'user-' . $args['id']];

	return $this->get('renderer')->render($response, 'users/show.phtml', $params);
});

$users = ['mike', 'mishel', 'adel', 'keks', 'kamila'];

$app->get('/users', function ($request, $response) use ($users) {
	$term = $request->getQueryParam('term');
	$searchedUsers = array_filter($users, function ($user) use ($term) {
		return strpos($user, $term) !== false;
	});

	$params = [
		'term' => $term,
		'users' => $searchedUsers
	];

	return $this->get('renderer')->render($response, 'users/users.phtml', $params);
});

$app->run();


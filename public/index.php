<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use DI\Container;

use function Stringy\create as s;

$container = new Container();
$container->set('renderer', function () {
	return new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');
});
AppFactory::setContainer($container);

$app = AppFactory::create();
$router = $app->getRouteCollector()->getRouteParser();
$app->addErrorMiddleware(true, true, true);

$app->get('/', function ($request, $response) use ($router) {
    return $response->withHeader('Location', $router->urlFor('users'))
    ->withStatus(302);
});

$repo = new Slime\Repository();

$app->get('/users', function ($request, $response) use ($repo) {
	$term = $request->getQueryParam('term');
    $users = $repo->get();

    if (!empty($term)) {
        $searchedUsers = collect($users)->filter(function ($user) use ($term) {
            return s(strtolower($user['nickname']))->contains(strtolower($term));
        });
    } else {
        $searchedUsers = $users;
    }

	$params = [
		'term' => $term,
		'users' => $searchedUsers
	];

	return $this->get('renderer')->render($response, 'users/users.phtml', $params);
})->setName('users');

$app->get('/users/new', function ($request, $response) {
	$params = [
        'user' => ['nickname' => '', 'email' => '']
    ];

	return $this->get('renderer')->render($response, 'users/new.phtml', $params);
});

$app->post('/users', function ($request, $response) use ($repo) {
    $validator = new Slime\Validator();
    $user = $request->getParsedBodyParam('user');
    $errors = $validator->validate($user);
    if (count($errors) === 0) {
        $repo->save($user);
        return $response->withHeader('Location', '/users')
          ->withStatus(302);
	}
	
    $params = [
        'user' => $user,
		'errors' => $errors
    ];
    return $this->get('renderer')->render($response, "users/new.phtml", $params);
});

$app->run();


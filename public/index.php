<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Validator;
use Slim\Factory\AppFactory;
use DI\Container;
use Slim\Middleware\MethodOverrideMiddleware;
use function GetData\getData;
use function GetData\putData;

// session_start();
$container = new Container();
$container->set('renderer', function () {
    return new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');
});
$container->set('flash', function () {
    return new \Slim\Flash\Messages();
});

AppFactory::setContainer($container);

$app = AppFactory::create();
$app->add(MethodOverrideMiddleware::class);
$app->addErrorMiddleware(true, true, true);

$router = $app->getRouteCollector()->getRouteParser();
session_start();
$app->get('/', function ($request, $response) {
    return $this->get('renderer')->render($response, 'index.phtml');
});

$app->get("/users", function ($request, $response) {
    $users = getData();
    $flash = $this->get('flash')->getMessages();
    $find = $request->getQueryParam('find');
    if ($find) {
        $result = array_filter($users, function ($user) use ($find) {
            return strpos($user["name"], $find) !== false;
        });
    }

    if (!$find) {
        $result = $users;
    }

    $params = [
        'users' => $result,
        'flash' => $flash
    ];
    return $this->get('renderer')->render($response, "CRUD/users.phtml", $params);
})->setName('users');

$app->get('/users/new', function ($request, $response) {
    $params = [
        'user' => ['id' => '', 'name' => '', 'email' => '', 'password' => '', 'passwordConfirmation' => '', 'city' => '']
    ];
    return $this->get('renderer')->render($response, 'CRUD/new.phtml', $params);
})->setName('new');

$app->post("/users", function ($request, $response) use ($router) {
    $user = $request->getParsedBodyParam('user');
    $maxId =  collect(getData())->max('id');
    $user['id'] = $maxId + 1;
    $validate = new \App\Validator();
    $errors = $validate->validate($user);
    if (count($errors) === 0) {
        putData($user);
        $flash = $this->get('flash')->addMessage('success', 'User has been added');
        $url = $router->urlFor('users');
        return $response->withRedirect($url);
    }

    $params = [
        'user' => $user,
        'errors' => $errors
    ];

    return $this->get('renderer')->render($response, 'CRUD/new.phtml', $params);
});

$app->get("/users/{id}", function ($request, $response, $args) {
    $id = $args['id'];
    $users = getData();
    foreach ($users as $user) {
        if ($user['id'] == $id) {
            $result[] = $user;
        }
    }
    $params = [
        'users' => $result
    ];
    return $this->get('renderer')->render($response, "CRUD/user.phtml", $params);
})->setName('user');

$app->get("/users/{id}/edit", function ($request, $response, $args) {
    $id = $args['id'];
    $users = getData();
    $user = $users[$id];
    
    $params = [
        'user' => $user,
        'errors' => []
    ];
    return $this->get('renderer')->render($response, 'CRUD/edit.phtml', $params);
});

$app->patch("/users/{id}", function ($request, $response, $args) use ($router) {
    $id = $args['id'];
    $users = getData();
    $user = $users[$id];
    unset($users[$id]);
    $changeTo = $request->getParsedBodyParam('user');
    foreach ($user as $key => $value) {
        if (array_key_exists($key, $changeTo)) {
            $user[$key] = $changeTo[$key];
        }
    }

    $validator = new Validator();
    $errors = $validator->validate($user);

    if (count($errors) === 0) {
        $users[$id] = $user;
        file_put_contents("/home/evg/hexlet-slim-example/datebase/users.json", json_encode($users));
        $flash = $this->get('flash')->addMessage('success', 'Data update');
        $url = $router->urlFor('users');
        return $response->withRedirect($url);
    }
    $params = [
        'test' => $user,
        'errors' => $errors
    ];

    return $this->get('renderer')->render($response, "CRUD/edit.phtml", $params);
});

$app->delete("/users/{id}", function ($request, $response, $args) use ($router) {
    $users = getData();
    $id = $args['id'];
    unset($users[$id]);
    if (empty($users)) {
        unlink("datebase/users.json");
        return $response->withRedirect($router->urlFor('users'));
    }
    file_put_contents("/home/evg/hexlet-slim-example/datebase/users.json", json_encode($users));
    $this->get('flash')->addMessage('success', "User was remove");
    $url = $router->urlFor('users');
    return $response->withRedirect($url);
});

$app->run();

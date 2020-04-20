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

// $app->get('/users/{id}', function ($request, $response, array $args) {
//     $params = ['id' => $args['id'], 'nickname' => 'user-' . $args['id']];
//     // var_dump($params);
//     return $this->get('renderer')->render($response, 'users/show.phtml', $params);
// });


// SEARCH FORM
// $users = ['mike', 'mishel', 'adel', 'keks', 'kvas', 'kamila', 'boris'];
// $app->get("/users", function ($request, $response) use ($users) {
//     $term = $request->getQueryParam('term');
//     // var_dump($term);
//     $result = array_filter($users, function ($user) use ($term) {
//         // var_dump($user);
//         // var_dump(strpos($term, $user));
//         return strpos($user, $term) !== false;
//     });
//     $params = [
//         'users' => $users,
//         'filtered' => $result
//     ];
//     var_dump($params);
//     return $this->get('renderer')->render($response, "search/show.phtml", $params);
// });


// Modicified form

$app->get('/users/new', function ($request, $response) {
    $params = [
        'user' => ['name' => '', 'email' => '', 'password' => '', 'passwordConfirmation' => '', 'city' => '']
    ];
    return $this->get('renderer')->render($response, 'modicified/index.phtml', $params);
});

$app->post("/users", function ($request, $response) {
    $user = json_encode($request->getParsedBodyParam('user'));
    file_put_contents("datebase/users.json", $user . PHP_EOL, FILE_APPEND);
    echo "all okey";
    $params = [
        'user' => $user
    ];
    return $this->get('renderer')->render($response, 'modicified/index.phtml', $params);
});


$app->get("/users/search", function ($request, $response) {
    $term = $request->getQueryParam('term');
    $arrayFromDataBase = explode(PHP_EOL, file_get_contents("datebase/users.json"));
    foreach ($arrayFromDataBase as $user) {
        if (!empty($user)) {
            $users[] = json_decode($user, true);
        }
    }
    $filtered = array_filter($users, function ($user) use ($term) {
        return strpos($user['name'], $term) !== false;
    });
    var_dump($filtered);
    if (empty($filtered)) {
        return $response->withStatus(404);
    }

    $params = [
        'users' => $users,
        'filtered' => $filtered
        ];
    return $this->get('renderer')->render($response, "search/show.phtml", $params);
});

$app->run();

<?php
require_once 'vendor/autoload.php';

use App\Controllers\UsersController;
use App\Validation\Errors;
use App\View;
use App\Redirect;
use Twig\TwigFunction;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/users', [UsersController::class, 'index']);
    $r->addRoute('GET', '/users/{id:\d+}', [UsersController::class, 'show']);

});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        var_dump('404 Not Found');
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        var_dump('405 Method Not Allowed');
        break;
    case FastRoute\Dispatcher::FOUND:
        $controller = $routeInfo[1][0];
        $method = $routeInfo[1][1];
        $vars = $routeInfo[2];

        /** @var View $response */
        $response = (new $controller)->$method($vars);

        $loader = new FilesystemLoader('app/Views');
        $twig = new Environment($loader);
//        $twig->addExtension(new CssInlinerExtension());
//        $twig->addGlobal('session', $_SESSION);
        $twig->addFunction(
            new TwigFunction('errors', function(string $url) { return Errors::getAll(); })
        );


        if($response instanceof View)
        {
            echo $twig->render($response->getPath() . '.html', $response->getVariables());
        }

        if($response instanceof Redirect)
        {
            header('Location: ' . $response->getLocation());
            exit;
        }

        break;
}
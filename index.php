<?php
//echo '<pre>';
require_once 'vendor/autoload.php';

session_start();

use App\Controllers\ApartmentsController;
use App\Controllers\ReservationsController;
use App\Controllers\UsersController;
use App\Validation\Errors;
use App\View;
use App\Redirect;
use Twig\Extra\CssInliner\CssInlinerExtension;
use Twig\TwigFunction;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
//---USERS
    $r->addRoute('GET', '/', [UsersController::class, 'main']);

    $r->addRoute('GET', '/users', [UsersController::class, 'index']);
    $r->addRoute('GET', '/users/{id:\d+}', [UsersController::class, 'show']);

    $r->addRoute('POST', '/users/register', [UsersController::class, 'register']);

    $r->addRoute('POST', '/users/login', [UsersController::class, 'login']);
    $r->addRoute('POST', '/', [UsersController::class, 'logout']);


//---APARTMENTS
    $r->addRoute('GET', '/apartments', [ApartmentsController::class, 'index']);
    $r->addRoute('GET', '/apartments/{id:\d+}', [ApartmentsController::class, 'show']);

    $r->addRoute('POST', '/apartments/{id:\d+}/delete', [ApartmentsController::class, 'delete']);

    $r->addRoute('GET', '/apartments/{id:\d+}/edit', [ApartmentsController::class, 'editForm']);
    $r->addRoute('POST', '/apartments/{id:\d+}', [ApartmentsController::class, 'edit']);

    $r->addRoute('GET', '/apartments/create', [ApartmentsController::class, 'createForm']);
    $r->addRoute('POST', '/apartments', [ApartmentsController::class, 'create']);

    $r->addRoute('POST', '/apartments/{id:\d+}/review', [ApartmentsController::class, 'review']);

//---RESERVATIONS
    $r->addRoute('GET', '/apartments/{id:\d+}/reservations', [ReservationsController::class, 'index']);

    $r->addRoute('POST', '/apartments/{id:\d+}/reserve', [ReservationsController::class, 'reserve']);
    $r->addRoute('POST', '/apartments/{id:\d+}/cancel/{reservationid:\d+}', [ReservationsController::class, 'cancel']);


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
        $twig->addExtension(new CssInlinerExtension());
        $twig->addGlobal('session', $_SESSION);
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

if(isset($_SESSION['errors'])){
    unset($_SESSION['errors']);
}

if(isset($_SESSION['inputs'])){
    unset($_SESSION['inputs']);
}

if(isset($_SESSION['status'])){
    unset($_SESSION['status']);
}
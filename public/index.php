<?php 
if( !session_id() ) @session_start();

require '../vendor/autoload.php';
use Aura\SqlQuery\QueryFactory;
use Delight\Auth\Auth;
use League\Plates\Engine;


/* 
0. Отладка - компонент отладки kint-php/kint
1. Маршрутизация - компонент nikic/fast-route
2. PHP DI - компонент php-di/php-di 
3. QueryBuilder -компонент aura/sqlquery
4. Контроллеры - register; login; users; create_user; profile; edit; media; 	
	status; security; 
5. Компонент Views - league/plates
6. Компонент флэш сообщений - tamtamchik/simple-flash 
7. Компонент заполнения страницы - fzaninotto/faker
 */

// PHP DI
$builder = new DI\ContainerBuilder();
$builder->addDefinitions([
		PDO::class => function(){
            $driver = 'mysql';
            $host = 'localhost';
            $dbname = 'MarlinDb';
            $username = 'root';
            $password = 'root';

            return new PDO("$driver:host=$host;dbname=$dbname", $username, $password);
        },

        QueryFactory::class => function(){
            return new QueryFactory('mysql');
        },

        Engine::class => function(){
            return new Engine('verstka/');
        },

        Auth::class => function($container){
            return new Auth($container->get('PDO'));
        }
	]);
$container = $builder->build();

// Router
$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {

    $r->addRoute('GET', '/page_register', ['App\controllers\HomeController', 'page_register']);
    $r->addRoute('POST', '/registration', ['App\controllers\Register', 'register']);
    $r->addRoute('GET', '/page_login', ['App\controllers\HomeController', 'page_login']);
    $r->addRoute('POST', '/login', ['App\controllers\User', 'login']);
    $r->addRoute('GET', '/users', ['App\controllers\HomeController', 'get_users']);
    $r->addRoute('GET', '/create_user', ['App\controllers\HomeController', 'create_user']);
    $r->addRoute('POST', '/createUserAsAdmin', ['App\controllers\Register', 'createUserAsAdmin']);
    $r->addRoute('GET', '/edit/{id:\d+}', ['App\controllers\HomeController', 'edit_profile']);
    $r->addRoute('POST', '/edit_user_info/{id:\d+}', ['App\controllers\User', 'edit_user_info']);
    $r->addRoute('GET', '/page_profile/{id:\d+}', ['App\controllers\HomeController', 'page_profile']);
    $r->addRoute('GET', '/media/{id:\d+}', ['App\controllers\HomeController', 'media']);
    $r->addRoute('POST', '/set_media/{id:\d+}', ['App\controllers\User', 'set_media']);
    $r->addRoute('GET', '/page_security/{id:\d+}', ['App\controllers\HomeController', 'page_security']);
    $r->addRoute('POST', '/edit_security/{id:\d+}', ['App\controllers\User', 'edit_security']);
    $r->addRoute('GET', '/status/{id:\d+}', ['App\controllers\HomeController', 'status']);
    $r->addRoute('POST', '/set_status/{id:\d+}', ['App\controllers\User', 'set_status']);
    $r->addRoute('GET', '/delete_user/{id:\d+}', ['App\controllers\User', 'delete_user']);
    $r->addRoute('GET', '/logout', ['App\controllers\User', 'logout']);
    $r->addRoute('GET', '/createFakerUserAsAdmin', ['App\controllers\Register', 'createFakerUserAsAdmin']);



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
        // ... 404 Not Found
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        // ... call $handler with $vars

        $container->call($routeInfo[1], $routeInfo[2]);
        break;
}

















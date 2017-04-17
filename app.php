<?php

use DyndnsUpdater\Application\Application;
use DyndnsUpdater\Controller\UpdateController;
use DyndnsUpdater\ServiceProvider\UpdaterServiceProvider;
use FrostieDE\Silex\EnvironmentServiceProvider;
use Igorw\Silex\ConfigServiceProvider;
use Igorw\Silex\YamlConfigDriver;
use Monolog\Logger;
use Silex\Provider\MonologServiceProvider;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

$loader = include_once __DIR__ . '/vendor/autoload.php';

ErrorHandler::register();

$app = new Application();

/**
 * SERVICE REGISTRATION
 */
$app->register(new MonologServiceProvider(), [
    'monolog.logfile' => __DIR__ . '/var/logs/app.log',
    'monolog.use_error_handler' => true,
    'monolog.level' => Logger::INFO
]);

$app->register(new EnvironmentServiceProvider());

ExceptionHandler::register($app['env'] === 'dev');

$app->register(new ConfigServiceProvider(__DIR__ . '/app/config.yml', [ ], new YamlConfigDriver(), 'config'));

$envConfigFile = __DIR__ . '/app/config.' . $app['env'] . '.yml';
if(file_exists($envConfigFile)) {
    $app->register(new ConfigServiceProvider($envConfigFile, [ ], null, 'config'));
}

$app->register(new UpdaterServiceProvider());

/**
 * ROUTES
 */

$app->get('/update', function(Request $request) use ($app) {
    return (new UpdateController($app))->update($request);
});

/**
 * ERRORS
 */
$app->error(function(Exception $exception) {
    $code = 500;

    if($exception instanceof BadRequestHttpException) {
        $code = 403;
    } else if($exception instanceof NotFoundHttpException) {
        $code = 404;
    }

    $response = new Response($exception->getMessage(), $code);

    return $response;
});

return $app;
<?php
use Slim\App;

return function (App $app) {
    $container = $app->getContainer();

    // view renderer
    $container['renderer'] = function ($c) {
        $settings = $c->get('settings')['renderer'];
        return new \Slim\Views\PhpRenderer($settings['template_path']);
    };

    // monolog
    $container['logger'] = function ($c) {
        $settings = $c->get('settings')['logger'];
        $logger = new \Monolog\Logger($settings['name']);
        $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
        $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
        return $logger;
    };

    $container['environment'] = function () {
        // Fix the Slim 3 subdirectory issue (#1529)
        // This fix makes it possible to run the app from localhost/slim3-app
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $_SERVER['REAL_SCRIPT_NAME'] = $scriptName;
        $_SERVER['SCRIPT_NAME'] = dirname(dirname($scriptName)) . '/' . basename($scriptName);
        return new \Slim\Http\Environment($_SERVER);
    };

    // Database connection
    $container['db'] = function ($c) {
		$pdo = new PDO("mysql:host=localhost;dbname=almacen;charset=utf8",'root','');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    };
};

<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
use app\Router;

require 'vendor/autoload.php';
session_start();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$path = (isset($_GET['r'])) ? $_GET['r'] : 'home';
$router = new Router();
$router->globalVars();
$router->renderController($path);
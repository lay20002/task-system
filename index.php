<?php
require "core/Controller.php";
require "core/Model.php";

$c = $_GET['c'] ?? 'auth';
$a = $_GET['a'] ?? 'login';

require "models/User.php";
require "models/Task.php";
require "controllers/".ucfirst($c)."Controller.php";

$controller = ucfirst($c)."Controller";
(new $controller)->$a();

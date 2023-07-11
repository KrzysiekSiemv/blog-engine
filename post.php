<?php
    require "_config.php";
    require "vendor/blog-engine/php/Authentication/AuthController.php";
    require "vendor/blog-engine/php/Posts/PostController.php";

    use BlogEngine\Posts\PostController;
    use BlogEngine\Authentication\AuthController;

    session_start();

    $auth = new AuthController();
    $conn = $auth->Connection();
<?php
include_once 'functions.php';
sec_session_start();
 
// Unset de toutes les variables sessions
$_SESSION = array();
 
// get des parametres session
$params = session_get_cookie_params();
 
// Suppression des cookies 
setcookie(session_name(),
        '', time() - 42000, 
        $params["path"], 
        $params["domain"], 
        $params["secure"], 
        $params["httponly"]);
 
// destruction de session
session_destroy();
header('Location: ../index.php');
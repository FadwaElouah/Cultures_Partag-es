<?php
session_start();
require_once 'User.php';

$user = new User();

// Check if the user is logged in
if (!$user->isLoggedIn()) {
    header("Location: login.php");
    exit();
}
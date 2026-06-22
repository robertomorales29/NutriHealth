<?php
define('APP_NAME', 'NutriHealth');
define('APP_ROOT', dirname(__DIR__));

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('America/Mexico_City');

<?php
require_once __DIR__ . '/includes/functions.php';
require_login();
$user = current_user();

match ($user['rol']) {
    'paciente' => require __DIR__ . '/pages/paciente/dashboard.php',
    'nutriologo' => require __DIR__ . '/pages/nutriologo/dashboard.php',
    'entrenador' => require __DIR__ . '/pages/entrenador/dashboard.php',
    default => redirect('index.php'),
};

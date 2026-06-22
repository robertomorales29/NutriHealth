<?php
require_once __DIR__ . '/../includes/functions.php';
require_role(['paciente']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('pages/paciente/progreso.php');
}

$user = current_user();
$pacienteId = (int) $user['id'];
$returnTo = $_POST['return_to'] ?? 'pages/paciente/progreso.php';
$peso = clean_decimal_or_null($_POST['peso'] ?? null);

if ($peso === null || $peso <= 0) {
    flash('error', 'El peso actual es obligatorio y debe ser mayor que cero.');
    redirect($returnTo);
}

$actividad = $_POST['nivel_actividad'] ?? 'Moderado';
if (!in_array($actividad, ['Bajo', 'Moderado', 'Alto'], true)) {
    $actividad = 'Moderado';
}

$objetivo = trim($_POST['objetivo_salud'] ?? '');
if (function_exists('mb_strlen') && mb_strlen($objetivo) > 150) {
    $objetivo = mb_substr($objetivo, 0, 150);
} elseif (!function_exists('mb_strlen') && strlen($objetivo) > 150) {
    $objetivo = substr($objetivo, 0, 150);
}

$stmt = db()->prepare('INSERT INTO datos_salud_actuales
    (paciente_id, peso, porcentaje_grasa, masa_muscular, cintura, cadera, objetivo_salud, nivel_actividad)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE
        peso = VALUES(peso),
        porcentaje_grasa = VALUES(porcentaje_grasa),
        masa_muscular = VALUES(masa_muscular),
        cintura = VALUES(cintura),
        cadera = VALUES(cadera),
        objetivo_salud = VALUES(objetivo_salud),
        nivel_actividad = VALUES(nivel_actividad)');
$stmt->execute([
    $pacienteId,
    $peso,
    clean_decimal_or_null($_POST['porcentaje_grasa'] ?? null),
    clean_decimal_or_null($_POST['masa_muscular'] ?? null),
    clean_decimal_or_null($_POST['cintura'] ?? null),
    clean_decimal_or_null($_POST['cadera'] ?? null),
    $objetivo ?: null,
    $actividad,
]);

flash('success', 'Tus datos actuales se actualizaron correctamente.');
redirect($returnTo);

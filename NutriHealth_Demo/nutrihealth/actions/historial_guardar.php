<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('dashboard.php');
}

$user = current_user();
$returnTo = $_POST['return_to'] ?? 'dashboard.php';
$pacienteId = (int) ($_POST['paciente_id'] ?? 0);

if ($user['rol'] === 'paciente') {
    $pacienteId = (int) $user['id'];
}

$patient = get_person($pacienteId);
if (!$patient || $patient['rol'] !== 'paciente') {
    flash('error', 'Paciente no válido.');
    redirect($returnTo);
}

if (empty($_POST['peso'])) {
    flash('error', 'El peso es obligatorio para registrar avance.');
    redirect($returnTo);
}

$stmt = db()->prepare('INSERT INTO historial_salud
    (paciente_id, peso, porcentaje_grasa, masa_muscular, cintura, cadera, observaciones)
    VALUES (?, ?, ?, ?, ?, ?, ?)');
$stmt->execute([
    $pacienteId,
    $_POST['peso'] ?: null,
    $_POST['porcentaje_grasa'] ?: null,
    $_POST['masa_muscular'] ?: null,
    $_POST['cintura'] ?: null,
    $_POST['cadera'] ?: null,
    trim($_POST['observaciones'] ?? '') ?: null,
]);

flash('success', 'Avance registrado correctamente.');
redirect($returnTo);

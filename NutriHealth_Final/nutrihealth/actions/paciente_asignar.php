<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('dashboard.php');
}

$user = current_user();
$returnTo = $_POST['return_to'] ?? 'dashboard.php';
if (!in_array($user['rol'], ['nutriologo', 'entrenador'], true)) {
    flash('error', 'No tienes permiso para asignar pacientes.');
    redirect($returnTo);
}

$pacienteId = (int) ($_POST['paciente_id'] ?? 0);
$patient = get_person($pacienteId);
if (!$patient || $patient['rol'] !== 'paciente') {
    flash('error', 'Paciente no válido.');
    redirect($returnTo);
}

ensure_assignment($pacienteId, (int) $user['id'], $user['rol']);
flash('success', 'Paciente asignado correctamente.');
redirect($returnTo);

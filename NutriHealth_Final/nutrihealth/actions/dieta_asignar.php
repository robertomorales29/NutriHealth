<?php
require_once __DIR__ . '/../includes/functions.php';
require_role(['nutriologo']);

$returnTo = $_POST['return_to'] ?? 'pages/nutriologo/dietas.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('pages/nutriologo/dietas.php');
}

$user = current_user();
$dietaId = (int) ($_POST['dieta_id'] ?? 0);
$pacienteId = (int) ($_POST['paciente_id'] ?? 0);
$fechaInicio = $_POST['fecha_inicio'] ?: date('Y-m-d');
$fechaFin = $_POST['fecha_fin'] ?: null;

$stmt = db()->prepare('SELECT id FROM dietas WHERE id = ? AND nutriologo_id = ?');
$stmt->execute([$dietaId, $user['id']]);
if (!$stmt->fetch()) {
    flash('error', 'Dieta no válida.');
    redirect($returnTo);
}

$patient = get_person($pacienteId);
if (!$patient || $patient['rol'] !== 'paciente') {
    flash('error', 'Paciente no válido.');
    redirect($returnTo);
}

$cancel = db()->prepare("UPDATE dietas_pacientes SET estado = 'finalizada' WHERE paciente_id = ? AND estado = 'activa'");
$cancel->execute([$pacienteId]);

$insert = db()->prepare('INSERT INTO dietas_pacientes (paciente_id, dieta_id, fecha_inicio, fecha_fin, estado) VALUES (?, ?, ?, ?, ?)');
$insert->execute([$pacienteId, $dietaId, $fechaInicio, $fechaFin, 'activa']);
ensure_assignment($pacienteId, (int) $user['id'], 'nutriologo');
flash('success', 'Dieta asignada correctamente.');
redirect($returnTo);
